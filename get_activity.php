<?php
include_once("componet/conn.php");
session_start();

if (!isset($_SESSION["login"])) {
    exit();
}

$name = $_SESSION["username"];
$getUserQuery = "SELECT id FROM users WHERE name = '$name' LIMIT 1";
$userResult = mysqli_query($con, $getUserQuery);
$userRow = mysqli_fetch_assoc($userResult);
$userId = $userRow['id'];

$qr_id = isset($_POST['qr_id']) ? intval($_POST['qr_id']) : null;

$query = "SELECT id, email, typeaccess, timestamp, qr_id 
          FROM accessrecord 
          WHERE qr_id = '$qr_id'
          ORDER BY timestamp DESC"; 
$res = mysqli_query($con, $query);

if ($res && mysqli_num_rows($res) > 0) {
    while ($row = mysqli_fetch_assoc($res)) {
        echo "<tr>";
        echo "<td style='padding:6px; border-bottom:1px solid #ddd;text-align:center;'>" . htmlspecialchars($row['timestamp']) . "</td>";
        echo "<td style='padding:6px; border-bottom:1px solid #ddd;text-align:center;'>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td style='padding:6px; border-bottom:1px solid #ddd;text-align:center;'>" . htmlspecialchars($row['typeaccess']) . "</td>";
        echo "<td style='padding:6px; border-bottom:1px solid #ddd;text-align:center;'>" . htmlspecialchars($row['qr_id']) . "</td>";
        echo "<td style='padding:6px; border-bottom:1px solid #ddd;text-align:center;'>" . htmlspecialchars($row['id']) . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='5' style='padding:8px; text-align:center;'>No access records found</td></tr>";
}
?>