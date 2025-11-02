
<?php
include_once("componet/conn.php"); // $con mysqli
    use PHPMailer\PHPMailer\PHPMailer;
    use PHPMailer\PHPMailer\Exception;

    require 'PHPMailer/src/PHPMailer.php';
    require 'PHPMailer/src/SMTP.php';
    require 'PHPMailer/src/Exception.php';
date_default_timezone_set('Asia/Kuala_Lumpur');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($_POST['email'])) {
    header('Location: forgot.php');
    exit;
}

$email = trim($_POST['email']);

// Always show same message to avoid account enumeration
$notice = "If that email exists, you will receive instructions to reset your password.";

$stmt = $con->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows === 1) {
    $stmt->bind_result($userId);
    $stmt->fetch();

    // create a secure token
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', time() + 3600); // 1 hour

    // store token & expiry
    $up = $con->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE id = ?");
    $up->bind_param('ssi', $token, $expires, $userId);
    $up->execute();

    // build reset link
    $proto = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $baseUrl = 'http://172.20.10.6:8080/SqTechSolver-Secure_Qr_Code_Generator_System';
    $link = $baseUrl . '/reset.php?token=' . rawurlencode($token);


    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->SMTPDebug = 0;
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;

        // Replace with your SMTP credentials
        $mail->Username   = 'danielhkl118@gmail.com';
        $mail->Password   = 'vntn mfwl enzn mcnj';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // For local testing only — remove/adjust in production
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
        $mail->Subject = 'Password reset instructions - SQ Tech Solver';
        $mail->Body = "
            <p>Hello,</p>
            <p>We received a request to reset your password. Click the link below to set a new password. This link is valid for 1 hour.</p>
            <p><a href=\"" . htmlspecialchars($link, ENT_QUOTES, 'UTF-8') . "\">Reset your password</a></p>
            <p>If you did not request this, you can ignore this email.</p>
            <p>Regards,<br><strong>SQ Tech Solver</strong></p>
        ";

        $mail->AltBody = "Open this link to reset your password (valid 1 hour):\n\n" . $link;

        $mail->send();
    } catch (Exception $e) {
        // Log or ignore — still show generic notice to user
        error_log("Reset email failed: " . $mail->ErrorInfo);
    }
}

$stmt->close();
?>
<!doctype html>
<html>
<head><meta charset="utf-8"><title>Reset Sent</title></head>
<body>
  <p><?= htmlspecialchars($notice) ?></p>
  <p><a href="login.php">Back to login</a></p>
</body>
</html>