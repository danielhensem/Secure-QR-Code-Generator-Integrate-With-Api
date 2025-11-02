<?php
session_start();
include 'componet/conn.php';

header('Content-Type: application/json');

$user_id = $_SESSION["id"] ?? 0;
$range = $_POST['range'] ?? 'all'; // '7', '30', or 'all'

// helper function
function countFromQuery($con, $query, $paramTypes, $params) {
    $stmt = mysqli_prepare($con, $query);
    if (!$stmt) return 0;

    if ($paramTypes && $params) {
        $refs = [];
        foreach ($params as $k => $v) {
            $refs[$k] = &$params[$k];
        }
        array_unshift($refs, $paramTypes);
        call_user_func_array([$stmt, 'bind_param'], $refs);
    }

    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $count);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    return $count ?: 0;
}

// build date filter if needed (use CURDATE() for calendar days)
$dateFilter = "";
$dateParam = [];
if ($range === '7') {
    $dateFilter = "AND DATE(`timestamp`) >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
} elseif ($range === '30') {
    $dateFilter = "AND DATE(`timestamp`) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
}

// Generated
$generated = countFromQuery(
    $con,
    "SELECT COUNT(*) FROM qr_security WHERE user_id = ? " . ($dateFilter ? " $dateFilter" : ""),
    "i",
    [$user_id]
);

// Shared
$shared = countFromQuery(
    $con,
    "SELECT COUNT(*) FROM qr_shares WHERE sender_id = ? " . ($dateFilter ? " $dateFilter" : ""),
    "i",
    [$user_id]
);

// Received
$received = countFromQuery(
    $con,
    "SELECT COUNT(*) FROM qr_shares WHERE receiver_id = ? " . ($dateFilter ? " $dateFilter" : ""),
    "i",
    [$user_id]
);

// Friends (no date filter)
$friends = countFromQuery(
    $con,
    "SELECT COUNT(*) FROM friends WHERE user_id = ? OR request_id = ?",
    "ii",
    [$user_id, $user_id]
);

echo json_encode([
    "Generated" => $generated,
    "Shared"    => $shared,
    "Received"  => $received,
    "Friends"   => $friends
]);
?>
