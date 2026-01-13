<style>
.qr-row {
    cursor: pointer;
    transition: background-color 0.2s ease;
}

/* Hover for desktop only */
@media (hover: hover) {
    .qr-row:hover {
        background-color:#011f72ff;
    }
}

/* Persistent selected state */
.qr-row.selected {
    background-color: #011f72ff;
}
</style>

<?php
session_start();
include 'componet/conn.php';

if (!isset($_SESSION['id'])) {
    echo "<tr><td colspan='4'>User not logged in</td></tr>";
    exit;
}

$user_id = $_SESSION['id'];

// Handle Delete Request
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
    $delete_id = intval($_POST['delete_id']);

    // Begin transaction
    mysqli_begin_transaction($con);

    try {

        // 4. Delete from qr_secondlayer (parent table)
        $stmt = mysqli_prepare($con, "DELETE FROM qr_secondlayer WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $delete_id);
        mysqli_stmt_execute($stmt);
        $deletedRows = mysqli_stmt_affected_rows($stmt);
        mysqli_stmt_close($stmt);

        // // 1. Delete from accessrecord (child table)
        // $stmt = mysqli_prepare($con, "DELETE FROM accessrecord WHERE qr_id = ?");
        // mysqli_stmt_bind_param($stmt, "i", $delete_id);
        // mysqli_stmt_execute($stmt);
        // mysqli_stmt_close($stmt);

        // 2. Delete from code (child table)
        $stmt = mysqli_prepare($con, "DELETE FROM code WHERE qr_code_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $delete_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);

        // 3. Delete from qr_security (child table)
        $stmt = mysqli_prepare($con, "DELETE FROM qr_security WHERE id = ?");
        mysqli_stmt_bind_param($stmt, "i", $delete_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_close($stmt);



        if ($deletedRows > 0) {
            mysqli_commit($con);
            echo "success";
        } else {
            mysqli_rollback($con);
            echo "failed: QR not found or doesn't belong to user.";
        }

    } catch (Exception $e) {
        mysqli_rollback($con);
        error_log("Delete failed: " . $e->getMessage());
        echo "failed: exception occurred.";
    }

    exit;
}


// Otherwise: Generate the HTML table
// Updated query to join qr_security and qr_secondlayer
$query = "SELECT 
            qs.id, 
            qs.qr_filename, 
            qs.otp_enabled, 
            qs.created_at, 
            qs.qr_image,
            qsl.header_text,
            qsl.description,
            qsl.file_type
          FROM qr_security qs
          LEFT JOIN qr_secondlayer qsl ON qs.id = qsl.id
          WHERE qs.user_id = ?
          ORDER BY qs.created_at DESC";

$stmt = mysqli_prepare($con, $query);

if (!$stmt) {
    echo "<tr><td colspan='5'>Query error: " . mysqli_error($con) . "</td></tr>";
    exit;
}

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    echo "<tr><td colspan='5'>No QR data found.</td></tr>";
    exit;
}

while ($row = mysqli_fetch_assoc($result)) {
    $otpStatus = $row['otp_enabled'] == 1 ? 'Enabled' : 'Disabled';
    $qrBase64 = base64_encode($row['qr_image']);

    $data = [
        'id' => $row['id'],
        'qr_filename' => $row['qr_filename'],
        'otp_enabled' => $row['otp_enabled'],
        'created_at' => $row['created_at'],
        'qr_type' => $row['file_type'] ?? 'N/A',
        'header_text' => $row['header_text'] ?? 'No Header',
        'description' => $row['description'] ?? 'No Description',
        'qr_image_base64' => $qrBase64
    ];

    $details = htmlspecialchars(json_encode($data), ENT_QUOTES, 'UTF-8');


    echo "<tr style='border-bottom: 1px solid #000000ff;'>";
    echo "<tr class='qr-row'  data-details='{$details}' style='cursor:pointer;'>";
    echo "  <td style='text-align:left; width:70px; '>{$data['id']}</td>";
    echo "  <td>{$data['header_text']}</td>";
    echo "</tr>";

}
