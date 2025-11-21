<?php
// session_start();
// session_unset();
// session_destroy();
session_start();


// If not logged in -> show 404 and stop processing
if (!isset($_SESSION["login"])) {
    header("HTTP/1.1 404 Not Found");
    // If you have a custom 404 page, include it. Otherwise you can echo a minimal message.
    if (file_exists(__DIR__ . '/404.php')) {
        include __DIR__ . '/404.php';
    } else {
        echo '<h1>404 Not Found</h1><p>The requested page was not found.</p>';
    }
    exit;
}

// user is logged in
$name = $_SESSION["username"];
// header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
// header("Cache-Control: post-check=0, pre-check=0", false);
// header("Pragma: no-cache");

include 'componet/conn.php';
include 'custom_crypto.php';

date_default_timezone_set('Asia/Kuala_Lumpur');
$qrToken = '';
$qr = null;
$qrSecond = null;
$qrContent = '';
$showQrContent = false;
$accessError = '';
$scanMessage = '';   // notification message
$otpSent = false;
$otpError = '';
$qrUploaded = false;

function mime_content_type_from_string($binary)
{
    $f = finfo_open();
    $mime = finfo_buffer($f, $binary, FILEINFO_MIME_TYPE);
    finfo_close($f);
    return $mime ?: 'application/octet-stream';
}

if (isset($_GET['reset']) == '1') {
    header("Location: scan.php"); // redirect to fresh scan page
    unset($_SESSION['qr_uploaded_base64'], $_SESSION['qr_uploaded_mime']);
    unset($_SESSION['qr_token'], $_SESSION['otp_code'], $_SESSION['otp_created_at'], $_SESSION['qr_id'], $_SESSION['qr_filename']);
    $_SESSION['qr_token'] = null;
    $qr = null;
    $qrSecond = null;
    exit();
}

// Upload file : read token in qr code
// Upload file : read token in qr code
if (isset($_FILES['qr_upload']) && $_FILES['qr_upload']['error'] === UPLOAD_ERR_OK) {
    $tempPath = $_FILES['qr_upload']['tmp_name'];
    $mime = mime_content_type($tempPath);
    $base64 = base64_encode(file_get_contents($tempPath));

    $_SESSION['qr_uploaded_base64'] = $base64;
    $_SESSION['qr_uploaded_mime'] = $mime;
    $qrUploaded = true;
}

if (isset($_FILES['qr_upload']) && $_FILES['qr_upload']['error'] === UPLOAD_ERR_OK) {
    $uploadedFile = $_FILES['qr_upload']['tmp_name'];
    $zbarPath = "C:\\Users\\Irfan\\Desktop\\XAMPP\\htdocs\\SqTechSolver-Secure_Qr_Code_Generator_System\\ZBar\\bin\\zbarimg.exe";
    $command = "\"$zbarPath\" -q --raw " . escapeshellarg($uploadedFile);
    $output = trim(shell_exec($command));

    file_put_contents("zbar_debug.txt", "Command: $command\nOutput: $output\n");

    if ($output) {
        // If output is a full URL, extract token from it
        $parsedUrl = parse_url($output);
        parse_str($parsedUrl['query'] ?? '', $queryParams);
        $qrToken = $queryParams['token'] ?? $output; // fallback if no query param

        $_SESSION['qr_token'] = $qrToken;
    } else {
        $accessError = "Failed to decode QR code.";
    }
} elseif (!empty($_POST['qr_token'])) {
    $qrToken = $_POST['qr_token'];
    $_SESSION['qr_token'] = $qrToken;
} elseif (!empty($_SESSION['qr_token'])) {
    $qrToken = $_SESSION['qr_token'];
}


/* ==============================
   Malware Scan with Session Cache
   ============================== */
if ($qrToken) {
    $apiKey = "1131364b275b749ce59649385236b3ec"; // MetaDefender API key
    $malicious = false;

    // ✅ Check if THIS token already scanned
    if (isset($_SESSION['scanned_tokens'][$qrToken])) {
        // Load result from session
        $scanMessage = $_SESSION['scanned_tokens'][$qrToken]['message'];
        $malicious = $_SESSION['scanned_tokens'][$qrToken]['malicious'];

        if ($malicious) {
            $accessError = "Security Warning: This QR code may contain malware.";
            $_SESSION['qr_token'] = null;
            $qr = null;
            $qrSecond = null;
        }
    } else {
        // Not scanned yet → do API scan
        $tempFile = tempnam(sys_get_temp_dir(), "qr_") . ".txt";
        file_put_contents($tempFile, $qrToken);

        $submitUrl = "https://api.metadefender.com/v4/file";
        $ch = curl_init($submitUrl);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            "apikey: $apiKey"
        ]);
        curl_setopt($ch, CURLOPT_POSTFIELDS, [
            "file" => new CURLFile($tempFile, "text/plain", basename($tempFile))
        ]);
        $submitResponse = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        unlink($tempFile);

        $submitResult = json_decode($submitResponse, true);

        if ($httpCode == 200 && !empty($submitResult['data_id'])) {
            $dataId = $submitResult['data_id'];
            sleep(3);

            $reportUrl = "https://api.metadefender.com/v4/file/" . $dataId;
            $ch = curl_init($reportUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, ["apikey: $apiKey"]);
            $reportResponse = curl_exec($ch);
            curl_close($ch);

            $reportResult = json_decode($reportResponse, true);

            if (isset($reportResult['scan_results']['scan_all_result_i'])) {
                if ($reportResult['scan_results']['scan_all_result_i'] === 0) {
                    $scanMessage = "✅ QR content is safe (file scan).";
                } else {
                    $malicious = true;
                    $scanMessage = "⚠️ Dangerous QR code detected (file scan). Access blocked.";
                }
            } else {
                $scanMessage = "⚠️ Scan report not ready. Try again.";
            }
        } else {
            $scanMessage = "⚠️ Failed to connect to MetaDefender API. (HTTP $httpCode)";
        }

        // ✅ Save result for THIS token
        $_SESSION['scanned_tokens'][$qrToken] = [
            'malicious' => $malicious,
            'message' => $scanMessage
        ];

        if ($malicious) {
            $accessError = "Security Warning: This QR code may contain malware.";
            $_SESSION['qr_token'] = null;
            $qr = null;
            $qrSecond = null;
        }
    }
}

/* ==============================
   Continue Normal Token → DB Flow
   ============================== */
if ($qrToken && !$accessError) {
    $stmt = $con->prepare("SELECT * FROM qr_secondlayer WHERE token = ?");
    $stmt->bind_param("s", $qrToken);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $qrSecond = $res->fetch_assoc();
        $foreignId = $qrSecond['id']; // This will be matched with qr_security.id

        // Check if this foreign key exists in qr_security.id
        $stmt2 = $con->prepare("SELECT * FROM qr_security WHERE id = ?");
        $stmt2->bind_param("i", $foreignId);
        $stmt2->execute();
        $res2 = $stmt2->get_result();

        if ($res2->num_rows > 0) {
            $qr = $res2->fetch_assoc();
            $_SESSION['qr_security_id'] = $qr['id'];
            $_SESSION['qr_id'] = $qr['id'];
            $_SESSION['qr_filename'] = $qr['qr_filename'];
        } else {
            $accessError = "No security record found for this QR token.";
            $_SESSION['qr_token'] = null;
            $qr = null;
            $qrSecond = null;
        }
    } else {
        $accessError = "Invalid QR token.";
        $_SESSION['qr_token'] = null;
        $qr = null;
        $qrSecond = null;
    }
}

/* ==============================
   Show JS Window Notification
   ============================== */
if ($scanMessage) {
    echo "<script>alert(" . json_encode($scanMessage) . ");</script>";
}
if ($accessError) {
    echo "<script>alert(" . json_encode($accessError) . ");</script>";
}
// Step 3: Handle OTP request
if (isset($_POST['request_otp']) && isset($_POST['otp_email']) && isset($_SESSION['qr_token'])) {
    $email = trim($_POST['otp_email']);
    $qrToken = $_SESSION['qr_token'];

    if (!empty($email)) {
        // Fetch qr_id from token
        $stmt = $con->prepare("SELECT id FROM qr_secondlayer WHERE token = ?");
        $stmt->bind_param("s", $qrToken);
        $stmt->execute();
        $res = $stmt->get_result();
        $stmt->close();

        if ($res->num_rows > 0) {
            $row = $res->fetch_assoc();
            $qrId = $row['id'];

            $payload = json_encode([
                "email" => $email,
                "qr_id" => $qrId,
                "token" => $qrToken
            ]);

            $ch = curl_init('http://172.20.10.6:8080/E-Commerce%20system/sendotp.php');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Content-Type: application/json',
                'Content-Length: ' . strlen($payload),
            ]);
            $response = curl_exec($ch);
            curl_close($ch);

            $result = json_decode($response, true);
            if (isset($result['error'])) {
                $otpError = $result['error'];
            } else {
                $otpSent = true;
            }
        } else {
            $otpError = "QR code reference not found.";
        }

    } else {
        $otpError = "Please enter a valid email.";
    }
}



if (isset($_POST['submit_access']) && isset($_SESSION['qr_token'])) {
    $qrToken = $_SESSION['qr_token'];
    $passwordOk = false;
    $password = $_POST['password'] ?? '';
    $otp = $_POST['otp'] ?? '';

    // Re-fetch QR secondlayer
    $stmt = $con->prepare("SELECT * FROM qr_secondlayer WHERE token = ?");
    $stmt->bind_param("s", $qrToken);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows > 0) {
        $qrSecond = $res->fetch_assoc();
        $foreignId = $qrSecond['id'];
        $scanStatus = (int) $qrSecond['scan_status']; // NEW: check scan_status

        // Re-fetch QR security
        $stmt2 = $con->prepare("SELECT * FROM qr_security WHERE id = ?");
        $stmt2->bind_param("i", $foreignId);
        $stmt2->execute();
        $res2 = $stmt2->get_result();

        if ($res2->num_rows > 0) {
            $qr = $res2->fetch_assoc();

            $_SESSION['qr_id'] = $qr['id'];
            $_SESSION['qr_filename'] = $qr['qr_filename'];

            // 🔹 Case 1: scan_status = 0 → Open QR (no password needed)
            if ($scanStatus === 0) {
                $passwordOk = true;
            }
            // 🔹 Case 2: scan_status = 1 → Require password or OTP
            else {
                // First: Check OTP
                if (!empty($otp) && isset($_SESSION['otp_code'], $_SESSION['otp_created_at'])) {
                    if ($otp == $_SESSION['otp_code'] && (time() - $_SESSION['otp_created_at']) <= 50) {
                        $passwordOk = true;
                    } else {
                        $accessError = "Invalid or expired OTP.";
                        unset($_SESSION['otp_code'], $_SESSION['otp_created_at']);
                    }
                }

                // Then: Check password
                if (!$passwordOk && !empty($password)) {
                    if (password_verify($password, $qr['password_hash'])) {
                        $passwordOk = true;
                    } else {
                        $accessError = "Incorrect password.";
                    }
                }

                if (!$passwordOk && empty($accessError)) {
                    $accessError = "Please enter a valid password or OTP.";
                }
            }

            // If authenticated
            if ($passwordOk) {
                $stmt3 = $con->prepare("SELECT * FROM qr_secondlayer WHERE id = ?");
                $stmt3->bind_param("i", $qrSecond['id']);
                $stmt3->execute();
                $res3 = $stmt3->get_result();

                if ($res3->num_rows > 0) {
                    $row = $res3->fetch_assoc();

                    $encodedEncrypted = $row['encrypted_content'];
                    $encodedEncryptedKey = $row['encrypted_key']; // NEW
                    $encodedSalt = $row['salt'];
                    $encodedNonceContent = $row['nonce'];
                    $encodedNonceKey = $row['nonce_key']; // NEW
                    $timestamp = $row['time'];

                    // Step 1: Decode base64-encoded fields
                    $encryptedContent = base64_decode($encodedEncrypted);
                    $encryptedKey = base64_decode($encodedEncryptedKey);
                    $salt = base64_decode($encodedSalt);
                    $nonceContent = base64_decode($encodedNonceContent);
                    $nonceKey = base64_decode($encodedNonceKey);

                    if (!$encryptedContent || !$encryptedKey || !$salt || !$nonceContent || !$nonceKey) {
                        echo "Decoding error. QR data is incomplete or corrupted.";
                        exit();
                    }

                    // Step 2: Use qr_secondlayer.id to get user_id from qr_security
                    $ipkey = $row['id'];

                    $stmtUserId = $con->prepare("SELECT user_id FROM qr_security WHERE id = ?");
                    $stmtUserId->bind_param("i", $ipkey);
                    $stmtUserId->execute();
                    $resUserId = $stmtUserId->get_result();
                    $userRow = $resUserId->fetch_assoc();

                    if (!$userRow || empty($userRow['user_id'])) {
                        echo "Could not find user linked to QR.";
                        exit();
                    }

                    $qrOwnerId = $userRow['user_id'];

                    // Step 3: Fetch email and phrase using user_id
                    $stmtUser = $con->prepare("SELECT email, phrase FROM users WHERE id = ?");
                    $stmtUser->bind_param("i", $qrOwnerId);
                    $stmtUser->execute();
                    $resUser = $stmtUser->get_result();
                    $userData = $resUser->fetch_assoc();

                    if (!$userData || empty($userData['email']) || empty($userData['phrase'])) {
                        echo "QR owner's data incomplete. Cannot derive decryption key.";
                        exit();
                    }

                    $email = $userData['email'];
                    $phrase = $userData['phrase'];



                    // Step 3: Build derived input using SHA-256
                    $key_material = $timestamp . $email . $phrase;
                    $derived_input = hash('sha256', $key_material, true); // binary output

                    // Step 4: Derive user key using PBKDF (Argon2id via libsodium)
                    $userKey = sodium_crypto_pwhash(
                        SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_IETF_KEYBYTES,
                        $derived_input,
                        $salt,
                        SODIUM_CRYPTO_PWHASH_OPSLIMIT_MODERATE,
                        SODIUM_CRYPTO_PWHASH_MEMLIMIT_MODERATE,
                        SODIUM_CRYPTO_PWHASH_ALG_DEFAULT
                    );

                    // Step 5: Decrypt the per-QR key
                    $aad = "";
                    $perQrKey = sodium_crypto_aead_chacha20poly1305_ietf_decrypt(
                        $encryptedKey,
                        $aad,
                        $nonceKey,
                        $userKey
                    );

                    if ($perQrKey === false) {
                        echo "Failed to decrypt per-QR key. Key mismatch or tampering.";
                        exit();
                    }

                    // Step 6: Decrypt the QR content
                    $qrContent = sodium_crypto_aead_chacha20poly1305_ietf_decrypt(
                        $encryptedContent,
                        $aad,
                        $nonceContent,
                        $perQrKey
                    );

                    if ($qrContent === false) {
                        echo "Decryption failed. QR content may be tampered with or incorrect key.";
                        exit();
                    }

                    // === Step 7.5: Insert access log ===
                    $accessType = '';
                    if (!empty($_POST['otp']) && isset($_SESSION['otp_code'], $_SESSION['otp_created_at'])) {
                        $userEmail = trim($_POST['otp_email']);
                        $accessType = 'Access through OTP';
                    } elseif (!empty($_POST['password'])) {
                        $userEmail = $email;
                        $accessType = 'Access through Password';
                    } else {
                        $userEmail = $email;
                        $accessType = 'No Password';
                    }

                    // Get user IP address
                    $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'Unknown IP';

                    // Get qr_id from session or fetched row
                    $qr_id = $_SESSION['qr_id'] ?? ($row['id'] ?? null);

                    if (!empty($email) && !empty($qr_id)) {
                        $stmtLog = mysqli_prepare($con, "INSERT INTO accessrecord (email, typeaccess, qr_id, ip_address, timestamp) VALUES (?, ?, ?, ?, NOW())");
                        mysqli_stmt_bind_param($stmtLog, "ssis", $userEmail, $accessType, $qr_id, $ipAddress);
                        mysqli_stmt_execute($stmtLog);
                    }

                    if (!empty($qr_id)) {
                        $activityMessage = "Accessed QR Code #" . $qr_id;
                        $activityStmt = $con->prepare("INSERT INTO activity (message, timestamp, user_id) VALUES (?, NOW(),?)");
                        if ($activityStmt) {
                            $activityStmt->bind_param("si", $activityMessage, $qrOwnerId);
                            $activityStmt->execute();
                            $activityStmt->close();
                        } else {
                            error_log("Failed to prepare activity insert: " . $con->error);
                        }
                    }
                    // Step 7: Handle decrypted content based on type
                    $fileType = $row['file_type'] ?? '';
                    $fileName = $row['file_name'] ?? 'qr_file';

                    if ($fileType === 'pdf') {
                        header('Content-Type: application/pdf');
                        header('Content-Disposition: inline; filename="' . $fileName . '"');
                        echo $qrContent;
                        $_SESSION['scan_done'] = false;
                        exit;
                    }

                    if ($fileType === 'image') {
                        $rawContent = $qrContent;

                        // Try decoding safely
                        $decoded = base64_decode($qrContent, true);
                        if ($decoded !== false && strlen($decoded) > 0) {
                            $rawContent = $decoded; // it was base64
                        }

                        // Detect MIME type
                        $finfo = new finfo(FILEINFO_MIME_TYPE);
                        $mime = $finfo->buffer($rawContent) ?: "image/png";

                        // Clean output buffer
                        if (ob_get_level()) {
                            ob_end_clean();
                        }

                        // Send headers
                        header("Content-Type: $mime");
                        header("Content-Length: " . strlen($rawContent));
                        echo $rawContent;
                        exit;
                    }



                    if (filter_var($qrContent, FILTER_VALIDATE_URL)) {
                        header("Location: $qrContent");
                        $_SESSION['scan_done'] = false;
                        exit;
                    }

                    $showQrContent = true;
                    $_SESSION['scan_done'] = false;

                    // Step 8: Clear QR session variables
                    unset(
                        $_SESSION['qr_uploaded_base64'],
                        $_SESSION['qr_uploaded_mime'],
                        $_SESSION['qr_token'],
                        $_SESSION['otp_code'],
                        $_SESSION['otp_created_at'],
                        $_SESSION['qr_id'],
                        $_SESSION['qr_filename'],
                        $_SESSION['scan_done']
                    );
                } else {
                    $accessError = "Failed to retrieve QR content.";
                    unset($_SESSION['scan_done']);
                    unset($_SESSION['qr_token'], $_SESSION['otp_code'], $_SESSION['otp_created_at'], $_SESSION['qr_id'], $_SESSION['qr_filename']);
                }

            }

        } else {
            $accessError = "Security info not found.";
            unset($_SESSION['scan_done']);
        }

    } else {
        $accessError = "Invalid or expired QR token.";
        unset($_SESSION['scan_done']);
    }
}


?>

<!-- HTML Part -->
<!DOCTYPE html>
<html lang="en">
<input type="hidden" id="qr_token" style="display:none;" value="<?= htmlspecialchars($_SESSION['qr_token'] ?? '') ?>">
<script>
    (function () {
        function addFavicon() {
            var href = 'img/log.svg';
            // remove any existing icon links to avoid duplicates
            var existing = document.querySelectorAll('link[rel~="icon"], link[rel="shortcut icon"]');
            existing.forEach(function (el) { el.parentNode.removeChild(el); });
            var link = document.createElement('link');
            link.rel = 'icon';
            link.type = 'image/png';
            link.href = href;
            // append to head (wait if head isn't parsed yet)
            if (document.head) {
                document.head.appendChild(link);
            } else {
                var h = document.getElementsByTagName('head')[0] || document.createElement('head');
                h.appendChild(link);
                if (!document.head) document.documentElement.insertBefore(h, document.documentElement.firstChild);
            }
        }
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', addFavicon);
        } else addFavicon();
    })();
</script>

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SQ-TECH SOLVER - Secure QR Code Generator</title>
    <link rel="icon" type="image/png" href="img/log.svg">
    <!-- External Styles -->
    <link rel="stylesheet" href="style-index.css">
    <link rel="stylesheet" href="style-res.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom Animated Background -->
    <style>
        @keyframes bgSlide {
            0% {
                background-position: 0% 50%;
            }

            50% {
                background-position: 100% 50%;
            }

            100% {
                background-position: 0% 50%;
            }

        }

        .animated-bg {
            min-height: 100vh;
            background: #424141ff;
            background: linear-gradient(90deg, rgba(255, 255, 255, 1) 35%, rgba(194, 192, 192, 1) 100%);
            background-size: 1000% 1000%;
            animation: bgSlide 15s ease infinite;
            position: relative;
            overflow-x: hidden;
            /* allow vertical scroll but hide horizontal overflow */
        }

        /* Make the floating layer cover the entire page */
        /* .floating-layer {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  pointer-events: none; /* Allow user to interact with page behind */
        /* z-index: 1; Above background, below content if needed */
        /* overflow: hidden; */
        /* } */

        /* Animate icons */
        /* .floating-icon {
  position: absolute;
  font-size: 30px;
  opacity: 0.5;
  color: white;
  animation: floatUp 20s linear infinite;
} */

        /* Keyframes for floating */
        @keyframes floatUp {
            0% {
                transform: translateY(100vh) scale(1);
                opacity: 0;
            }

            30% {
                opacity: 0.8;
            }

            100% {
                transform: translateY(-200px) scale(1.5);
                opacity: 0;
            }
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0f2f5;
            padding: 0;
            font-size: 15px;
        }

        .container {
            max-width: 1000px;
            min-width: 300px;
            margin: 40px auto;
            background: linear-gradient(90deg, rgba(255, 255, 255, 1) 35%, rgba(194, 192, 192, 1) 100%);
            border-radius: 40px;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
            display: flex;
            flex-wrap: wrap;
            /* ✅ allow wrapping */
            overflow: hidden;
            border: 1px solid black;
        }

        /* Default layout: 50% width each */
        .left,
        .right {
            width: 50%;
            padding: 30px;
            box-sizing: border-box;
            /* ✅ ensures padding doesn't break layout */
        }

        .left {
            background: linear-gradient(90deg, rgba(255, 255, 255, 1) 35%, rgba(194, 192, 192, 1) 100%);

            text-align: center;
        }

        .right {
            background: transparent;
            border-radius: 40px;
        }

        /* ✅ Responsive behavior: Stack on smaller screens */
        @media (max-width: 768px) {

            .left,
            .right {
                width: 100%;
                margin: 10px 20px;
                /* Stack on top of each other */
            }
        }

        input,
        textarea,
        button {
            width: 100%;
            margin: 10px 0;
            padding: 10px;
            font-size: 15px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        button {
            background-color: #2563eb;
            color: black;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #1e40af;
        }

        .error {
            color: red;
            font-size: 14px;
        }

        .success {
            color: green;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }
    </style>
</head>

<body class="animated-bg">
    <?php include("componet/navbar.php"); ?>
    <h1 class="case-title-h1" style="margin-left: 10px; margin-right: 10px;">QR Scan & Access</h1>
    <div class="container">
        <div class="left">
            <h3 style="font-weight: bold; font-size:17px;">Upload QR Code</h3>
            <br>
            <hr class="dashed-line">
            <!-- <div style="margin-top: 20px;">
                <button id="selectQrCode"
                    style="padding: 6px 12px; background-color: #29f800ff; color: black; border: none; border-radius: 5px; width: 100%;">
                    Click Here for Choose QR Code !
                </button>

                <div id="friendShareForm" style="display:none; margin-top: 10px;">
                    <select id="friendListDropdown" style="padding:6px; width:100%;"></select>
                    < <button id="submitFriendShare"
                        style="padding:6px 10px; margin-top: 8px; background-color:#e17055; color:white; border:none; border-radius:5px; width: auto;">
                        Share
                    </button> -->
            <!-- </div>
            </div> -->
            <!-- <br>
                        <h3>Or</h3> -->
            <form method="post" enctype="multipart/form-data">
                <input type="file" name="qr_upload" style="border-radius: 40px;" required>
                <button type="submit" style="border-radius: 40px; color:white; font-weight: bold;">Upload &
                    Decode</button>
            </form>

            <?php
            $qrImageTag = "";

            if (!empty($_SESSION['qr_uploaded_base64']) && !empty($_SESSION['qr_uploaded_mime'])) {
                // $mime = $_SESSION['qr_uploaded_mime'];
                // $base64 = $_SESSION['qr_uploaded_base64'];
                // $qrImageTag = "<img style='margin-top:20px; max-width:300px;' src='data:$mime;base64,$base64'>";
                // echo "<div style='margin-top:10px; font-weight:bold; text-align:left;'>QR Code Id : {$_SESSION['qr_id']}</div>";
                // echo "<div style='text-align:left; font-weight:bold;'>Filename : {$_SESSION['qr_filename']}</div>";
                // echo $qrImageTag;
                //...
                // Escape values to prevent XSS. base64 payload is not escaped (binary), but mime and displayed strings are.
                $mime = htmlspecialchars($_SESSION['qr_uploaded_mime'] ?? 'application/octet-stream', ENT_QUOTES, 'UTF-8');
                $base64 = $_SESSION['qr_uploaded_base64'] ?? '';
                $qrIdOut = htmlspecialchars((string) ($_SESSION['qr_id'] ?? ''), ENT_QUOTES, 'UTF-8');
                $qrFilenameOut = htmlspecialchars((string) ($_SESSION['qr_filename'] ?? ''), ENT_QUOTES, 'UTF-8');
                $qrImageTag = "<img style='margin-top:20px; max-width:300px;' src='data:" . $mime . ";base64," . $base64 . "' alt='QR Code Image' />";
                echo "<div style='margin-top:10px; font-weight:bold; text-align:left;'>QR Code Id : {$qrIdOut}</div>";
                echo "<div style='text-align:left; font-weight:bold;'>Filename : {$qrFilenameOut}</div>";
                echo $qrImageTag;
                // Enable OTP button again if present
                echo "<script>if (document.getElementById('sendOtpBtn')) document.getElementById('sendOtpBtn').disabled = false;</script>";
            }
            ?>
        </div>

        <div class="right">
            <h3 style="font-weight: bold; font-size:17px;">Secure Access</h3>
            <?php if (!empty($accessError))
                echo "<div class='error'>$accessError</div>"; ?>
            <?php if (!empty($otpError))
                echo "<div class='error'>$otpError</div>"; ?>
            <?php if ($otpSent)
                echo "<div class='success'>OTP has been sent to your email.</div>"; ?>

            <?php if ($showQrContent): ?>
                <label>QR Content:</label>
                <textarea readonly><?= htmlspecialchars($qrContent) ?></textarea>
                <button style="border-radius: 40px; color:white; font-weight: bold;" type="button"
                    onclick="window.location.href='scan.php?reset=true'">Reset</button>

            <?php elseif ($qr && $qrSecond): ?>

                <?php if ((int) $qrSecond['scan_status'] === 0): ?>
                    <!-- Open QR: No password/OTP needed -->
                    <form method="POST" id="accessForm">
                        <button type="submit" name="submit_access"
                            style="border-radius: 40px; color:white; font-weight: bold;">Access QR</button>
                    </form>
                    <button type="button" onclick="window.location.href='scan.php?reset=true'"
                        style="border-radius: 40px; color:white; font-weight: bold;">Reset</button>

                <?php else: ?>
                    <!-- Secured QR: Show OTP + Password fields -->
                    <form method="POST" id="accessForm">
                        <input type="email" name="otp_email" id="otp_email" placeholder="Enter your email"
                            style="border-radius: 40px;">
                        <button type="button" id="sendOtpBtn" style="border-radius: 40px; color:white; font-weight: bold;">Send
                            OTP</button>
                        <br><br>

                        <input type="text" name="otp" id="otp_input" placeholder="Enter OTP" style="border-radius: 40px;">
                        <div id="otp-timer" style="color: red; margin-top: 5px;"></div>

                        <br><br>
                        <input type="password" name="password" placeholder="Enter Password (optional)"
                            style="border-radius: 40px;">
                        <button type="submit" name="submit_access"
                            style="border-radius: 40px; color:white; font-weight: bold;">Access</button>
                    </form>

                    <button type="button" onclick="window.location.href='scan.php?reset=true'"
                        style=" border-radius: 40px; color:white; font-weight: bold;">Reset</button>
                <?php endif; ?>

            <?php else: ?>
                <p style="white-space:normal; word-break: break-word;"> Please upload and scan a QR code to begin.<br><br>

                    <strong>Additional Information:</strong><br><br>

                    1. If the QR code was created without a password, users can open the content immediately.<br>

                    2. If the QR code was created with a password, users must enter the password or request a one-time
                    passcode (OTP).<br>

                    3. To request verification, users must share their email to the QR code owner. After the owner verifies
                    them, the user can request an OTP.
                    If the owner chooses to share the password directly, it becomes the responsibility of both the owner and
                    the receiver.<br>

                </p>

            <?php endif; ?>
        </div>


    </div>
    <div class="footer-note">&copy; 2025 SQ‑Tech Solver. All rights reserved.</div>


    <!-- javascript sw -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
    <script src="index.js"></script>
</body>

</html>
<!-- <script>

    window.onpageshow = function (event) {
        const sessionFlag = sessionStorage.getItem("hasActiveSession");

        if (sessionFlag && event.persisted) {
            const continueSession = confirm("Do you want to continue your previous session?");

            if (!continueSession) {
                sessionStorage.removeItem("hasActiveSession");
                window.location.href = "scan.php?reset=1";
            }
        }
    };



    document.addEventListener("DOMContentLoaded", () => {
        // Whenever QR is uploaded, mark session active
        if (<?= isset($qr) && isset($qrSecond) ? 'true' : 'false' ?>) {
            sessionStorage.setItem("hasActiveSession", "true");
        }
    });

    let countdownInterval;
    let qrUploaded = false;

    function startOTPTimer(seconds) {
        let timerDisplay = document.getElementById("otp-timer");
        clearInterval(countdownInterval);

        let remaining = seconds;
        timerDisplay.textContent = `OTP expires in ${remaining} seconds`;

        countdownInterval = setInterval(() => {
            remaining--;
            if (remaining <= 0) {
                clearInterval(countdownInterval);
                timerDisplay.textContent = "OTP expired. Please request again.";
            } else {
                timerDisplay.textContent = `OTP expires in ${remaining} seconds`;
            }
        }, 1000);
    }

    document.addEventListener("DOMContentLoaded", () => {
        const sendOtpBtn = document.getElementById("sendOtpBtn");
        const accessBtn = document.querySelector("button[name='submit_access']");
        const accessForm = document.getElementById("accessForm");

        // ✅ Assign qrUploaded from PHP
        qrUploaded = <?= isset($qr) && isset($qrSecond) ? 'true' : 'false' ?>;

        // Disable buttons initially
        if (!qrUploaded) {
            if (sendOtpBtn) sendOtpBtn.disabled = true;
            if (accessBtn) accessBtn.disabled = true;
        } else {
            if (sendOtpBtn) sendOtpBtn.disabled = false;
            if (accessBtn) accessBtn.disabled = false;
        }

        if (sendOtpBtn) {
            sendOtpBtn.addEventListener("click", function () {
                const email = document.getElementById("otp_email").value.trim();
                const token = document.getElementById("qr_token").value.trim();
                if (!qrUploaded) {
                    alert("Please upload and decode a QR code first.");
                    return;
                }
                if (!email) {
                    alert("Please enter your email.");
                    return;
                }

                const payload = JSON.stringify({ email: email, token: token });

                fetch("sendotp.php", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json"
                    },
                    body: payload
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            startOTPTimer(50);
                            alert("OTP sent successfully to " + email);
                        } else {
                            alert(data.error || "Failed to send OTP.");
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert("Something went wrong.");
                    });
            });
        }

        if (accessForm) {
            accessForm.addEventListener("submit", function (e) {
                const email = document.getElementById("otp_email").value.trim();
                const password = document.querySelector("input[name='password']").value.trim();
                const otp = document.getElementById("otp_input").value.trim();

                if (!qrUploaded) {
                    alert("Please upload and decode a QR code first.");
                    e.preventDefault();
                    return;
                }

                if (!otp && !password) {
                    alert("Please enter either OTP or Password.");
                    e.preventDefault();
                    return;
                }

                if (otp && !email) {
                    alert("Please enter your email to verify OTP.");
                    e.preventDefault();
                    return;
                }

                // ✅ Do NOT preventDefault if validation passes
            });
        }
    });

    document.getElementById("resetBtn").addEventListener("click", function () {
        window.location.href = "scan.php?reset=true";
    });

    if (window.history.replaceState) {
        window.history.replaceState(null, null, window.location.href);
    }
</script> -->