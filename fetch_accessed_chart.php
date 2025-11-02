<?php
session_start();
include 'componet/conn.php';

header('Content-Type: application/json');
$user_id = $_SESSION['id'] ?? 0;
$range = $_POST['range'] ?? 'all'; // match frontend POST

if (!$user_id) {
    echo json_encode(['error' => 'Missing user_id']);
    exit;
}

$sql = "
    SELECT DATE(ar.timestamp) AS access_date, COUNT(*) AS access_count
    FROM accessrecord ar
    INNER JOIN qr_security qs ON qs.id = ar.qr_id
    WHERE qs.user_id = ?
";

// Optional filter
if ($range === '7') {
    $sql .= " AND ar.timestamp >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)";
} elseif ($range === '30') {
    $sql .= " AND ar.timestamp >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
}

$sql .= " GROUP BY DATE(ar.timestamp) ORDER BY DATE(ar.timestamp)";

$stmt = $con->prepare($sql);
if (!$stmt) {
    echo json_encode(['error' => 'Prepare failed: ' . $con->error]);
    exit;
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = [
        'date' => $row['access_date'],
        'count' => (int)$row['access_count']
    ];
}

echo json_encode($data);
exit;
