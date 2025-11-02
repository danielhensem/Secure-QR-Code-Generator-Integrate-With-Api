<?php
session_start();
header('Content-Type: application/json');
include_once("componet/conn.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
require 'PHPMailer/src/Exception.php';

$input = json_decode(file_get_contents('php://input'), true);
$email = trim($input['email'] ?? '');

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['error' => 'Invalid email address.']);
    exit;
}

// generate 6-digit numeric code
$code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
$expires = time() + 60; // 60 seconds

// store in session
$_SESSION['signup_code'] = $code;
$_SESSION['signup_code_expires'] = $expires;

// send email with PHPMailer
$mail = new PHPMailer(true);
try {
    $mail->isSMTP();
    $mail->SMTPDebug = 0;
    $mail->Host       = 'smtp.gmail.com';
    $mail->SMTPAuth   = true;

    // TODO: replace with your SMTP credentials
    $mail->Username   = 'danielhkl118@gmail.com';
    $mail->Password   = 'vntn mfwl enzn mcnj';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // optional: allow local testing
    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true
        ]
    ];

    $mail->setFrom($mail->Username, 'SQ Tech Solver');
    $mail->addAddress($email);

    $mail->isHTML(true);
    $mail->Subject = 'Your SQ-Tech signup verification code';
    $mail->Body    = "<p>Your verification code is <strong>{$code}</strong>. It will expire in 1 minute.</p>";
    $mail->AltBody = "Your verification code is {$code}. It will expire in 1 minute.";

    $mail->send();

    echo json_encode(['success' => true]);
} catch (Exception $e) {
    error_log("Signup code mail error: " . $mail->ErrorInfo);
    echo json_encode(['error' => 'Failed to send email.']);
}