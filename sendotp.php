<?php
session_start();
header('Content-Type: application/json');

require_once 'componet/conn.php';
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';
require_once 'PHPMailer/src/Exception.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(["error" => "Invalid request method."]);
    exit;
}

// Decode JSON input
$input = json_decode(file_get_contents("php://input"), true);
$email = isset($input['email']) ? trim($input['email']) : '';
$qrToken = isset($input['token']) ? trim($input['token']) : '';

// Validate inputs
if (empty($email) || empty($qrToken)) {
    echo json_encode(["error" => "Email and QR token are required."]);
    exit;
}

// Get the QR Code ID from qr_secondlayer using token
$stmt = $con->prepare("SELECT id FROM qr_secondlayer WHERE token = ?");
$stmt->bind_param("s", $qrToken);
$stmt->execute();
$qrResult = $stmt->get_result();
$stmt->close();

if ($qrResult->num_rows === 0) {
    echo json_encode(["error" => "Invalid QR token."]);
    exit;
}

$qrRow = $qrResult->fetch_assoc();
$qrCodeId = $qrRow['id'];

// Check if email is verified for this QR code
$stmt = $con->prepare("SELECT * FROM code WHERE email = ? AND status = 1 AND qr_code_id = ?");
$stmt->bind_param("si", $email, $qrCodeId);
$stmt->execute();
$codeResult = $stmt->get_result();
$stmt->close();

if ($codeResult->num_rows === 0) {
    echo json_encode(["error" => "Email is not verified or doesn't match this QR code."]);
    exit;
}

// Generate OTP
$otp = random_int(100000, 999999);
$_SESSION['otp_code'] = $otp;
$_SESSION['otp_created_at'] = time();

// Send OTP via email
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'danielhkl118@gmail.com';
    $mail->Password = 'vntn mfwl enzn mcnj'; // App password
    $mail->SMTPSecure = 'tls';
    $mail->Port = 587;

    $mail->setFrom('danielhkl118@gmail.com', 'SQ Tech Solver Coop');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = 'Your One-Time Passcode (OTP)';
    $mail->Body = "
        <p>Hello,</p>
        <p>Your OTP is: <strong>$otp</strong></p>
        <p>This code is valid for <strong>50 seconds</strong>. Please use it immediately.</p>
        <p><strong>SQ Tech Solver Coop</strong></p>
    ";

    $mail->send();
    echo json_encode(["success" => true]);
} catch (Exception $e) {
    echo json_encode(["error" => "Email failed to send: " . $mail->ErrorInfo]);
}
?>