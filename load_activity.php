<?php
include_once("componet/conn.php");
session_start();
date_default_timezone_set('Asia/Kuala_Lumpur');

if (!isset($_SESSION["login"])) {
    exit();
}

$name = $_SESSION["username"];
$getUserQuery = "SELECT id FROM users WHERE name = '$name' LIMIT 1";
$userResult = mysqli_query($con, $getUserQuery);
$userRow = mysqli_fetch_assoc($userResult);
$userId = $userRow['id'];

// Get pagination and filter parameters safely
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 7;
$filter = $_GET['filter'] ?? "7";

// Build WHERE clause
$whereClause = "WHERE user_id = '$userId'";
if ($filter !== "all") {
    $days = intval($filter);
    $whereClause .= " AND timestamp >= NOW() - INTERVAL $days DAY";
}

// Query activity logs
$query = "SELECT message, timestamp FROM activity 
          $whereClause
          ORDER BY timestamp DESC
          LIMIT $offset, $limit";
$result = mysqli_query($con, $query);

// If no results, handle it gracefully
if (mysqli_num_rows($result) == 0 && $offset == 0) {
    echo "<div style='text-align:center; color:#777; font-style:italic; margin-top:20px;'>
            No activity found for this period.
          </div>";
    exit();
}

// Output each activity card
while ($row = mysqli_fetch_assoc($result)) {
    $message = htmlspecialchars($row['message']);
    $time = date("d M Y, h:i A", strtotime($row['timestamp']));
    echo "<div class='activity-card'>
            <div class='activity-message'>
                <i class='fa-solid fa-bell' style='margin-right:8px; color:#007bff;'></i>
                $message
            </div>
            <div class='activity-time'>$time</div>
          </div>";
}
?>
