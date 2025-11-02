<?php
session_start();
include 'componet/conn.php'; // Ensure $con is defined

if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo "User not logged in";
    exit;
}

$user_id = $_SESSION['id'];

$query = "SELECT COUNT(*) AS total 
          FROM friends 
          WHERE (user_id = ? OR request_id = ?) AND status = 1";
$stmt = mysqli_prepare($con, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, "ii", $user_id, $user_id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $total);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);

    echo $total;
} else {
    http_response_code(500);
    echo "Database error: " . mysqli_error($con);
}
