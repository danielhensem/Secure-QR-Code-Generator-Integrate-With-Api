<?php
include "../componet/conn.php";
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $receiver_id = $_POST['user_id'] ?? '';
    $message     = $_POST['message'] ?? '';
    $sender_id   = $_SESSION['id'] ?? 0; // current logged-in user

    if (!$sender_id || !$message) {
        echo "error";
        exit();
    }

    // If receiver is "all"
    if (strtolower($receiver_id) === "all") {
        $users = $con->query("SELECT id FROM users WHERE id != '$sender_id'");
        while ($row = $users->fetch_assoc()) {
            $friend_id = $row['id'];
            $activityNotification = "Hi admin here, thank you for using our service,we right to notice #{$sender_id} that {$message}";

            $stmt = $con->prepare("INSERT INTO notification (message, receiver_id, status, timestamp) VALUES (?,?,1, NOW())");
            $stmt->bind_param("si", $activityNotification, $friend_id);
            $stmt->execute();
            $stmt->close();
        }
        echo "success_all";
    } else {
        // Normal single send
        if ($receiver_id) {
            $activityNotification = "Hi admin here, thank you for using our service,we right to notice #{$sender_id} that {$message}";
            $stmt = $con->prepare("INSERT INTO notification (message, receiver_id, status, timestamp) VALUES (?,?,1, NOW())");
            $stmt->bind_param("si", $activityNotification, $receiver_id);
            $stmt->execute();
            $stmt->close();
            echo "success_single";
        } else {
            echo "error";
        }
    }
}
?>
