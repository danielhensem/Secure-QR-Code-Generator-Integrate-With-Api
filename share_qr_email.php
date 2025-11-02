<?php
session_start();
include 'componet/conn.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

header('Content-Type: application/json');

// ✅ Check session
if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$sender_id = $_SESSION['id'];
$data = json_decode(file_get_contents("php://input"), true);

// ✅ Validate input
if (!isset($data['email'], $data['qr_id'])) {
    echo json_encode(["error" => "Invalid email or QR ID."]);
    exit;
}

$email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
$qr_id = intval($data['qr_id']);
 
if (!$email || !$qr_id) {
    echo json_encode(["error" => "Invalid input values."]);
    exit;
}

// ✅ Retrieve QR image (as BLOB) from qr_security table
$stmt = $con->prepare("SELECT qr_image FROM qr_security WHERE id = ?");
$stmt->bind_param("i", $qr_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["error" => "QR code not found."]);
    exit;
}

$row = $result->fetch_assoc();
$qrBlob = $row['qr_image'];

if (empty($qrBlob)) {
    echo json_encode(["error" => "QR image data is empty."]);
    exit;
}

// ✅ Try to get receiver_id if email exists
$stmt = $con->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows > 0) {
    $receiver = $res->fetch_assoc();
    $receiver_id = $receiver['id'];
} else {
    // Allow sending to non-registered emails
    $receiver_id = null; // or 0, depending on your database design
}


// ✅ Save image temporarily (no base64_decode here!)
$temp_image_path = tempnam(sys_get_temp_dir(), 'qr_') . '.png';
file_put_contents($temp_image_path, $qrBlob);

// ✅ Insert into qr_shares table
$stmt = $con->prepare("INSERT INTO qr_shares (qr_id, sender_id, receiver_id) VALUES (?, ?, ?)");
$stmt->bind_param("iii", $qr_id, $sender_id, $receiver_id);
$stmt->execute();
$stmt->close();

$activityMessage = "Send QR Code #" . $qr_id. " to ". $email;
$stmt = $con->prepare("INSERT INTO activity (message, timestamp, user_id) VALUES (?, NOW(), ?)");
$stmt->bind_param("si", $activityMessage,$sender_id);
$stmt->execute();
$stmt->close();

// ✅ Send Email
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'danielhkl118@gmail.com'; // your Gmail
    $mail->Password   = 'vntn mfwl enzn mcnj';     // your app password
    $mail->SMTPSecure = 'tls';
    $mail->Port       = 587;

    $mail->setFrom('your_email@gmail.com', 'SQ Tech Solver Coop');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = 'You received a QR Code from SQ Tech Solver Coop';
    $mail->Body = "
        <p>Hello,</p>
        <p><strong>User ID #$sender_id</strong> has shared a secure QR Code with you.</p>
        <p>The attached QR code may require a one-time verification upon scan for privacy and security.</p>
        <br>
        <p>Regards,<br><strong>SQ Tech Solver Coop</strong></p>
    ";

    $mail->addAttachment($temp_image_path, 'qr_code.png');
    $mail->send();

    // ✅ Cleanup temp file
    unlink($temp_image_path);

    echo json_encode(["success" => true, "message" => "QR code shared with $email"]);
} catch (Exception $e) {
    echo json_encode(["error" => "Email failed: {$mail->ErrorInfo}"]);
}
