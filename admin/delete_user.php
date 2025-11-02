<?php
include "../componet/conn.php";
session_start();

// Force JSON response
header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['terminateAccount'])){
    $user_id = $_POST['user_id'] ?? 0;

    if($user_id){
        // Wrap queries in try/catch style
        try {
            $con->query("DELETE FROM `accessrecord` WHERE qr_id IN (SELECT id FROM qr_security WHERE user_id='$user_id')");
            $con->query("DELETE FROM `qr_security` WHERE user_id='$user_id'");
            $con->query("DELETE FROM `friends` WHERE user_id='$user_id' OR request_id='$user_id'");
            $con->query("DELETE FROM `users` WHERE id='$user_id'");

            echo json_encode(['success' => true]);
        } catch(Exception $e){
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['success' => false, 'error' => 'User ID not provided']);
    }
    exit();
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request']);
    exit();
}
?>
