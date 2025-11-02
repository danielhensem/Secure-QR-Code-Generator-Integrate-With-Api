<?php
session_start();
include 'componet/conn.php'; // Ensure $con is defined

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

header('Content-Type: application/json');

// ✅ Check if user is logged in
if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['id'];

// ✅ Read raw JSON data
$rawData = file_get_contents("php://input");
$data = json_decode($rawData, true);

// ✅ Validate input
if (!isset($data['email'], $data['qr_id'])) {
    echo json_encode(["error" => "Invalid email or QR ID."]);
    exit;
}

$email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
$qr_id = intval($data['qr_id']);

if (!$email || !$qr_id) {
    echo json_encode(["error" => "Invalid email or QR ID."]);
    exit;
}

// ✅ Insert into DB
$stmt = $con->prepare("INSERT INTO code (email, qr_code_id, status) VALUES (?, ?, 1)");
if (!$stmt) {
    echo json_encode(["error" => "Database prepare failed: " . $con->error]);
    exit;
}
$stmt->bind_param("si", $email, $qr_id);
$stmt->execute();
$stmt->close();

// ✅ Prepare PHPMailer
$mail = new PHPMailer(true);

try {
    // SMTP settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;
    $mail->Username   = 'danielhkl118@gmail.com';        // Replace with your Gmail
    $mail->Password   = 'vntn mfwl enzn mcnj';           // Replace with your Gmail App Password
    $mail->SMTPSecure = 'tls';                         // Use 'ssl' if needed
    $mail->Port       = 587;                           // 465 for 'ssl'

    // Debugging (optional)
    // $mail->SMTPDebug = 2; 
    // $mail->Debugoutput = 'html';

    // From and to
    $mail->setFrom('your_email@gmail.com', 'SQ Tech Solver Coop');
    $mail->addAddress($email);

    // Email content
    $mail->isHTML(true);
    $mail->Subject = 'QR Code Verification';
    $mail->Body    = "
        <p>Hello,</p>
        <p>Thank you for using our service. You can access the QR code by clicking the one-time passcode (you’ll receive it after verifying).</p>
        <p><strong>SQ Tech Solver Coop</strong><br>
        Our service is to ensure your security first before anything.<br>
        From Admin</p>
    ";

    $mail->send();
    echo json_encode(["success" => true, "message" => "Email sent to $email"]);
} catch (Exception $e) {
    echo json_encode(["error" => "Email failed to send: {$mail->ErrorInfo}"]);
}
