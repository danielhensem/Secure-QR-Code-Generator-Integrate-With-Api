<?php
session_start();
include 'componet/conn.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['id'];
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['friend_id'], $data['qr_id'])) {
    echo json_encode(["error" => "Missing data"]);
    exit;
}

$friend_id = intval($data['friend_id']);
$qr_id = intval($data['qr_id']);

$stmt = $con->prepare("
    INSERT INTO qr_shares (qr_id, sender_id, receiver_id)
    VALUES (?, ?, ?)
");

if (!$stmt) {
    echo json_encode(["error" => "Prepare failed: " . $con->error]);
    exit;
}

$stmt->bind_param("iii", $qr_id, $user_id, $friend_id);
$stmt->execute();
$stmt->close();

$activityMessage = "Send QR Code #" . $qr_id. " to #". $friend_id;
$stmt = $con->prepare("INSERT INTO activity (message, timestamp, user_id) VALUES (?, NOW(),?)");
$stmt->bind_param("si", $activityMessage,$user_id);
$stmt->execute();
$stmt->close();

$activityNotification = "User id #".$user_id. "  has shared QR Code with you #". $qr_id;
$stmt = $con->prepare("INSERT INTO notification (message,receiver_id,status, timestamp) VALUES (?,?,1, NOW())");
$stmt->bind_param("si", $activityNotification,$friend_id);
$stmt->execute();
$stmt->close();


echo json_encode(["success" => true, "message" => "QR shared with friend successfully."]);
