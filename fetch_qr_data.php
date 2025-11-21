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

    echo "<tr style='border-bottom: 1px solid #ddd;'>";
    echo "    <td style='font-size: 13px;padding: 12px 15px;'>{$data['created_at']}</td>";
    echo "    <td style='font-size: 13px;padding: 12px 15px; white-space: normal; word-break: break-word; max-width: 200px;'>{$data['qr_filename']}</td>";
    echo "    <td style='font-size: 13px;padding: 12px 15px; text-align:center;'>{$data['id']}</td>";
    echo "    <td style='font-size: 13px;padding: 12px 15px; text-align:center;'>{$data['header_text']}</td>";
    echo "    <td style='font-size: 13px;padding: 12px 15px; text-align:center;'>{$data['qr_type']}</td>"; // ✅ New QR Type column
    echo "    <td style='padding: 12px 15px;'>";

    // View Button
echo "        <button class='view-btn' data-details='" . json_encode($data) . "' 
                style='padding: 2px 2px; background-color: #3498db; color: white; border: none; border-radius: 5px; cursor: pointer; margin-right: 2px; font-size: 12px;'>
                <i class='fas fa-eye'></i>
            </button>";

// Analyze Button
echo "        <button class='analyze-btn' data-id='{$data['id']}' 
                style='padding: 2px 2px; background-color: #e67e22; color: white; border: none; border-radius: 5px; cursor: pointer; margin-right: 2px; font-size: 12px;'>
                <i class='fas fa-chart-line'></i>
            </button>";

// Download Button
echo "        <button class='download-btn' 
                data-filename='{$data['qr_filename']}'
                data-image='data:image/jpeg;base64,{$data['qr_image_base64']}'
                style='padding: 2px 2px; background-color: #27ae60; color: white; border: none; border-radius: 5px; cursor: pointer; margin-right: 2px; font-size: 12px;'>
                <i class='fas fa-download'></i>
            </button>";

// Delete Button
echo "        <button class='delete-btn' data-id='{$data['id']}' 
                style='padding: 2px 2px; background-color: #e74c3c; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 12px;'>
                <i class='fas fa-trash'></i>
            </button>";

    echo "    </td>";
    echo "</tr>";
}
