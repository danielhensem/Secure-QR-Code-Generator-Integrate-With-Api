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

$query = "
    SELECT 
        qs.id, 
        qs.qr_filename, 
        qs.otp_enabled, 
        f.shared_at, 
        qs.qr_image 
    FROM 
        qr_shares AS f
    INNER JOIN 
        qr_security AS qs 
        ON f.qr_id = qs.id
    WHERE 
        f.receiver_id = ? 
        AND qs.user_id = f.sender_id
    ORDER BY f.shared_at DESC";


$stmt = mysqli_prepare($con, $query);
if (!$stmt) {
    echo "<tr><td colspan='4'>Query error: " . mysqli_error($con) . "</td></tr>";
    exit;
}

mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($result) === 0) {
    echo "<tr><td colspan='4'>No shared QR codes found.</td></tr>";
    exit;
}

while ($row = mysqli_fetch_assoc($result)) {
    $otpStatus = $row['otp_enabled'] == 1 ? 'Enabled' : 'Disabled';
    $qrBase64 = base64_encode($row['qr_image']);

    $data = [
        'id' => $row['id'],
        'qr_filename' => $row['qr_filename'],
        'otp_enabled' => $row['otp_enabled'],
        'shared_at' => $row['shared_at'],
        'qr_image_base64' => $qrBase64
    ];

    $details = htmlspecialchars(json_encode($data), ENT_QUOTES, 'UTF-8');

    echo "<tr style='border-bottom: 1px solid #000000ff;'>";
    echo "<tr class='qr-row'  data-details='{$details}' style='cursor:pointer;'>";
    echo "  <td style='text-align:left;  width:70px;'>{$data['id']}</td>";
    echo "  <td>{$data['qr_filename']}</td>";
    echo "</tr>";

}
?>