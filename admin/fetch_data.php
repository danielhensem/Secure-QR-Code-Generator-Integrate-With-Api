<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>

<?php
error_reporting(0);  // disable warnings/notices
ini_set('display_errors', 0); // don’t output errors to browser
ob_start(); // sta
session_start();
include "../componet/conn.php"; 

header('Content-Type: application/json');

// Get user_id from POST
$user_id = $_POST['user_id'] ?? 0;
$range = $_POST['range'] ?? 'all'; // '7', '30', 'all'

if (!$user_id) {
    echo json_encode(['error' => 'Missing user_id']);
    exit;
}

// --- Fetch user details ---
$stmtUser = $con->prepare("SELECT id, name, email FROM users WHERE id = ?");
$stmtUser->bind_param("i", $user_id);
$stmtUser->execute();
$resultUser = $stmtUser->get_result();
$user = $resultUser->fetch_assoc();
$stmtUser->close();

// --- Helper function for counts ---
function countFromQuery($con, $query, $paramTypes, $params) {
    $stmt = mysqli_prepare($con, $query);
    if (!$stmt) return 0;

    if ($paramTypes && $params) {
        mysqli_stmt_bind_param($stmt, $paramTypes, ...$params);
    }

    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $count);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    return $count ?: 0;
}

// --- Date filter ---
$dateFilter = "";
$dateParam = [];
if ($range === '7' || $range === '30') {
    $dateFilter = "AND timestamp >= DATE_SUB(CURDATE(), INTERVAL ? DAY)";
    $dateParam = [intval($range)];
}

// --- Account Activity Counts ---
$generated = countFromQuery(
    $con,
    "SELECT COUNT(*) FROM qr_security WHERE user_id = ? " . ($dateFilter ? $dateFilter : ""),
    $dateFilter ? "ii" : "i",
    $dateFilter ? [$user_id, ...$dateParam] : [$user_id]
);

$shared = countFromQuery(
    $con,
    "SELECT COUNT(*) FROM qr_shares WHERE sender_id = ? " . ($dateFilter ? $dateFilter : ""),
    $dateFilter ? "ii" : "i",
    $dateFilter ? [$user_id, ...$dateParam] : [$user_id]
);

$received = countFromQuery(
    $con,
    "SELECT COUNT(*) FROM qr_shares WHERE receiver_id = ? " . ($dateFilter ? $dateFilter : ""),
    $dateFilter ? "ii" : "i",
    $dateFilter ? [$user_id, ...$dateParam] : [$user_id]
);

$friends = countFromQuery(
    $con,
    "SELECT COUNT(*) FROM friends WHERE user_id = ? OR request_id = ?",
    "ii",
    [$user_id, $user_id]
);

// --- Accessed QR codes ---
$sqlAccess = "
    SELECT DATE(ar.timestamp) AS access_date, COUNT(*) AS access_count
    FROM accessrecord ar
    INNER JOIN qr_security qs ON qs.id = ar.qr_id
    WHERE qs.user_id = ?
";

if ($range === '7') {
    $sqlAccess .= " AND ar.timestamp >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
} elseif ($range === '30') {
    $sqlAccess .= " AND ar.timestamp >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
}

$sqlAccess .= " GROUP BY DATE(ar.timestamp) ORDER BY DATE(ar.timestamp)";

$stmt = $con->prepare($sqlAccess);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$accessed = [];
while ($row = $result->fetch_assoc()) {
    $accessed[] = [
        'date' => $row['access_date'],
        'count' => (int)$row['access_count']
    ];
}
$stmt->close();

// --- Combine all data ---
$response = [
    'user'      => $user,          // id, name, email
    'generated' => $generated,
    'shared'    => $shared,
    'received'  => $received,
    'friends'   => $friends,
    'accessed'  => $accessed
];
ob_clean(); 
echo json_encode($response);
exit;