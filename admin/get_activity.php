<?php
include "../componet/conn.php";

$query = "SELECT timestamp, user_id, message 
          FROM activity 
          ORDER BY timestamp DESC"; 
$res = mysqli_query($con, $query);

if ($res && mysqli_num_rows($res) > 0) {
    while ($row = mysqli_fetch_assoc($res)) {
        echo "<tr>";
        echo "<td style='padding:6px; border-bottom:1px solid #ddd;text-align:center;'>" . htmlspecialchars($row['timestamp']) . "</td>";
        echo "<td style='padding:6px; border-bottom:1px solid #ddd;text-align:center;'>" . htmlspecialchars($row['user_id']) . "</td>";
        echo "<td style='padding:6px; border-bottom:1px solid #ddd;text-align:left;'>" . htmlspecialchars($row['message']) . "</td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='3' style='padding:8px; text-align:center;'>No activity found</td></tr>";
}
?>
