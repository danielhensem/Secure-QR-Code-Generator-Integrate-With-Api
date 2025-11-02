<?php
include_once("componet/conn.php");
date_default_timezone_set('Asia/Kuala_Lumpur');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['token']) || empty($_POST['pwd']) || empty($_POST['pwd2'])) {
    header('Location: login.php');
    exit;
}

$token = $_POST['token'];
$pwd = $_POST['pwd'];
$pwd2 = $_POST['pwd2'];

if ($pwd !== $pwd2) {
    die('Passwords do not match. <a href="javascript:history.back()">Go back</a>');
}
if (strlen($pwd) < 6) {
    die('Password too short. <a href="javascript:history.back()">Go back</a>');
}

// find user by token and ensure not expired
$stmt = $con->prepare("SELECT id, reset_expires FROM users WHERE reset_token = ? LIMIT 1");
$stmt->bind_param('s', $token);
$stmt->execute();
$stmt->bind_result($id, $expires);
if (!$stmt->fetch()) {
    $stmt->close();
    die('Invalid token.');
}
$stmt->close();

if (!$expires || strtotime($expires) < time()) {
    die('Token expired. <a href="forgot.php">Request a new reset</a>');
}

// update password (hash) and clear token
$hash = password_hash($pwd, PASSWORD_DEFAULT);
$up = $con->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
$up->bind_param('si', $hash, $id);
if ($up->execute()) {
    echo 'Password updated successfully. <a href="login.php">Login</a>';
} else {
    echo 'Database error. Please try again later.';
}
$up->close();
?>