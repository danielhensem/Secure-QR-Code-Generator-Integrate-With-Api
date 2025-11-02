<?php
session_start();
include 'componet/conn.php';

if (!isset($_SESSION['id'])) {
    die("User not logged in.");
}

$userId = $_SESSION['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = trim($_POST['password']);
    $useOtp = isset($_POST['otp']) ? 1 : 0;
    $email = isset($_POST['otp_email']) ? trim($_POST['otp_email']) : null;

    date_default_timezone_set('Asia/Kuala_Lumpur');
    $createdAt = date('Y-m-d H:i:s');

    // Check password
    if (!empty($password)) {
        // User entered password
        $hashedPassword = password_hash($password, PASSWORD_BCRYPT);
        $scanStatus = 1; // password-protected
    } else {
        // User left blank → generate random password internally
        $generatedPassword = bin2hex(random_bytes(6)); // random string
        $hashedPassword = password_hash($generatedPassword, PASSWORD_BCRYPT);
        $scanStatus = 0; // open access
    }

    // Load QR code from existing file
    $qrPath = 'images/qr1.png';
    if (!file_exists($qrPath)) {
        die("QR file not found at: $qrPath");
    }

    $pngData = file_get_contents($qrPath);
    if ($pngData === false) {
        die("Failed to read QR PNG data.");
    }

    $qrFilename = 'qr_' . $userId . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.png';
    $null = null;

    // Insert into qr_security
    $stmt = $con->prepare("INSERT INTO qr_security (
        qr_filename, password_hash, otp_enabled, otp_email, qr_image, created_at, user_id
    ) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssisbsi", $qrFilename, $hashedPassword, $useOtp, $email, $null, $createdAt, $userId);
    $stmt->send_long_data(4, $pngData);

    if ($stmt->execute()) {
        $_SESSION['secured_qr_filename'] = $qrFilename;

        if (isset($_SESSION['last_qr_token'])) {
            $token = $_SESSION['last_qr_token'];

            $getIdStmt = $con->prepare("SELECT id FROM qr_security WHERE qr_filename = ? AND user_id = ? ORDER BY created_at DESC LIMIT 1");
            $getIdStmt->bind_param("si", $qrFilename, $userId);
            $getIdStmt->execute();
            $result = $getIdStmt->get_result();

            if ($row = $result->fetch_assoc()) {
                $qrId = $row['id'];
                $desc = "Authorized";

                // Update qr_secondlayer with scan_status based on password entry
                $updateStmt = $con->prepare("UPDATE qr_secondlayer SET id = ?, id_description = ?, scan_status = ? WHERE token = ?");
                $updateStmt->bind_param("isis", $qrId, $desc, $scanStatus, $token);
                $updateStmt->execute();
                $updateStmt->close();

                // Log activity
                $activityMessage = "Generated QR Code #" . $qrId;
                $activityStmt = $con->prepare("INSERT INTO activity (message, timestamp, user_id) VALUES (?, NOW(), ?)");
                $activityStmt->bind_param("si", $activityMessage, $userId);
                $activityStmt->execute();
                $activityStmt->close();
            }

            $getIdStmt->close();
            unset($_SESSION['last_qr_token']);

            // Insert into code table for OTP use
            $getEmailStmt = $con->prepare("SELECT email FROM users WHERE id = ?");
            $getEmailStmt->bind_param("i", $userId);
            $getEmailStmt->execute();
            $emailResult = $getEmailStmt->get_result();

            if ($emailRow = $emailResult->fetch_assoc()) {
                $userEmail = $emailRow['email'];
                $insertCodeStmt = $con->prepare("INSERT INTO code (email, qr_code_id, status) VALUES (?, ?, 1)");
                $insertCodeStmt->bind_param("si", $userEmail, $qrId);
                $insertCodeStmt->execute();
                $insertCodeStmt->close();
            }
            $getEmailStmt->close();
        }

        // Handle OTP if enabled
        if ($useOtp && !empty($email)) {
            $otp = random_int(100000, 999999);
            $_SESSION['otp_code'] = $otp;
            $_SESSION['otp_email'] = $email;

            $subject = "Your One-Time Passcode (OTP)";
            $message = "Your OTP is: $otp\nIt is valid for 5 minutes.\nDo not share it with anyone.";
            $headers = "From: noreply@yourdomain.com";

            mail($email, $subject, $message, $headers);
        }

        // Transition page (unchanged)
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
        <meta charset="UTF-8">
        <title>Redirecting...</title>
        <link rel="icon" type="image/png" href="img/log.png">
        <style>
            body {
                margin: 0;
                background: #6da9c0;
                display: flex;
                justify-content: center;
                align-items: center;
                height: 100vh;
                overflow: hidden;
            }
            .transition-message {
                font-family: Arial, sans-serif;
                font-size: 26px;
                color: white;
                opacity: 0;
                transform: translateY(20px);
                animation: fadeSlide 1.2s forwards;
            }
            @keyframes fadeSlide {
                0% { opacity: 0; transform: translateY(20px); }
                100% { opacity: 1; transform: translateY(0); }
            }
            .fade-out {
                animation: fadeOut 1s forwards;
            }
            @keyframes fadeOut {
                0% { opacity: 1; }
                100% { opacity: 0; }
            }
        </style>
        </head>
        <body>
            <div class="transition-message" id="msg">Preparing your preview...</div>
            <script>
                setTimeout(() => {
                    document.getElementById('msg').classList.add('fade-out');
                }, 1500);
                setTimeout(() => {
                    window.location.href = "finalpreview.php";
                }, 3000);
            </script>
        </body>
        </html>
        <?php
        exit();
    } else {
        echo "Failed to store QR: " . $stmt->error;
    }

    $stmt->close();
}
?>
