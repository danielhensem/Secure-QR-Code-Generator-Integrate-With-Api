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

// ✅ Correct query: fetch the *other* user in the friendship
$sql = "
    SELECT u.id, u.email
    FROM friends f
    JOIN users u 
        ON ( (f.user_id = ? AND u.id = f.request_id) 
          OR (f.request_id = ? AND u.id = f.user_id) )
    WHERE f.status = 1
";

$stmt = $con->prepare($sql);
$stmt->bind_param("ii", $user_id, $user_id);
$stmt->execute();
$result = $stmt->get_result();

$friends = [];
while ($row = $result->fetch_assoc()) {
    $friends[] = $row;
}

echo json_encode($friends);
