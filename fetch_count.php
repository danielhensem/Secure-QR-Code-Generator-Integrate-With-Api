<?php
session_start();
include 'componet/conn.php'; // Make sure $con is defined

if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo "User not logged in";
    exit;
}

$user_id = $_SESSION['id'];

$query = "SELECT COUNT(*) AS total FROM qr_security WHERE user_id = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $total);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

echo $total;
?>
