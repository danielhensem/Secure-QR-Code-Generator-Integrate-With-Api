<?php
session_start();
include 'componet/conn.php';

header('Content-Type: application/json');

// ✅ Check if logged in
if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['id'];

// ✅ Query: get all QR security IDs for this user
$sql = "SELECT id FROM qr_security WHERE user_id = ?";
$stmt = $con->prepare($sql);

if (!$stmt) {
    http_response_code(500);
    echo json_encode(["error" => "Database error: " . $con->error]);
    exit;
}

$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$qr_ids = [];
while ($row = $result->fetch_assoc()) {
    $qr_ids[] = $row;
}

echo json_encode($qr_ids);
