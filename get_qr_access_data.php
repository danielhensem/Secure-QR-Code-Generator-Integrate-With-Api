<?php
session_start();
include 'componet/conn.php';
header('Content-Type: application/json');

$qr_id = intval($_POST['qr_id'] ?? 0);
$user_id = $_SESSION['id'] ?? 0;
$range = $_POST['range'] ?? 'all'; // '7', '30', or 'all'

if (!$qr_id || !$user_id) {
    echo json_encode(['error' => 'Missing qr_id or user_id']);
    exit;
}

$params = [$qr_id, $user_id];
$types = "ii";

$dateFilter = "";
if ($range === '7' || $range === '30') {
    $dateFilter = "AND ar.timestamp >= DATE_SUB(CURDATE(), INTERVAL ? DAY)";
    $params[] = intval($range);
    $types .= "i";
}

$sql = "
    SELECT DATE(ar.timestamp) AS access_date, COUNT(*) AS access_count
    FROM accessrecord ar
    INNER JOIN qr_security qs ON qs.id = ar.qr_id
    WHERE ar.qr_id = ? AND qs.user_id = ?
    $dateFilter
    GROUP BY DATE(ar.timestamp)
    ORDER BY DATE(ar.timestamp)
";

$stmt = $con->prepare($sql);
if (!$stmt) {
    echo json_encode(['error' => 'Failed to prepare statement']);
    exit;
}

$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$labels = [];
$data = [];

while ($row = $result->fetch_assoc()) {
    $labels[] = $row['access_date'];
    $data[] = (int)$row['access_count'];
}

echo json_encode(['labels' => $labels, 'data' => $data]);
exit;
