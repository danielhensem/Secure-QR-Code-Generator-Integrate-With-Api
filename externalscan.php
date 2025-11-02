<?php
session_start();
include 'componet/conn.php';
// include 'custom_crypto.php'; // Assuming this file exists and is correct
date_default_timezone_set('Asia/Kuala_Lumpur');

// --- Your existing PHP logic remains largely the same ---
// I've kept your backend logic as it was, since the request was for design improvements.
// Minor adjustments might be needed if session variable names change, but I've kept them consistent.
$qrToken = $_GET['token'] ?? ($_SESSION['qr_token'] ?? '');
$qr = null;
$qrSecond = null;
$qrContent = '';
$showQrContent = false;
$accessError = '';
$otpSent = false;
$otpError = '';
$scanMessage = '';  // notification message

/* ==============================
   Step 0: Malware Scan (as temp file, only once per session)
   ============================== */
if ($qrToken) {
    if (!isset($_SESSION['scanned_tokens'][$qrToken])) {
        $apiKey = "1131364b275b749ce59649385236b3ec"; // MetaDefender API key
        $malicious = false;

        // 1. Save token into temporary text file
        $tmpFile = tempnam(sys_get_temp_dir(), "qrscan_");
        file_put_contents($tmpFile, $qrToken);

        // 2. Upload to MetaDefender as file
        $ch = curl_init("https://api.metadefender.com/v4/file");
        $cfile = new CURLFile($tmpFile, "text/plain", "token.txt");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,
            CURLOPT_HTTPHEADER => ["apikey: $apiKey"],
            CURLOPT_POSTFIELDS => ["file" => $cfile]
        ]);
        $submitResponse = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        unlink($tmpFile); // remove temp file
        $submitResult = json_decode($submitResponse, true);

        if ($httpCode === 200 && !empty($submitResult['data_id'])) {
            $dataId = $submitResult['data_id'];
            sleep(3); // wait a bit for scanning

            // 3. Get report
            $ch = curl_init("https://api.metadefender.com/v4/file/$dataId");
            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => ["apikey: $apiKey"]
            ]);
            $reportResponse = curl_exec($ch);
            curl_close($ch);

            $reportResult = json_decode($reportResponse, true);

            if (isset($reportResult['scan_results']['scan_all_result_i'])) {
                if ($reportResult['scan_results']['scan_all_result_i'] === 0) {
                    $scanMessage = "✅ QR token file is safe (MetaDefender).";
                } else {
                    $malicious = true;
                    $scanMessage = "⚠️ Dangerous QR token detected in file scan!";
                }
            } else {
                $scanMessage = "⚠️ Scan report not ready, try again later.";
            }
        } else {
            // fallback regex check if API fails
            $patterns = [
                '/javascript:/i',
                '/<script.*?>/i',
                '/onerror=/i',
                '/onload=/i',
                '/base64,/i',
                '/(evil|phishing|malware|attack)/i'
            ];
            foreach ($patterns as $p) {
                if (preg_match($p, $qrToken)) {
                    $malicious = true;
                    break;
                }
            }
            $scanMessage = $malicious
                ? "⚠️ Suspicious QR token detected (fallback)."
                : "✅ QR token passed fallback security check.";
        }

        // Save result in session
        $_SESSION['scanned_tokens'][$qrToken] = [
            'malicious' => $malicious,
            'message' => $scanMessage
        ];
    }

    // Load from session
    $scanResult = $_SESSION['scanned_tokens'][$qrToken];
    $malicious = $scanResult['malicious'];
    $scanMessage = $scanResult['message'];

    if ($malicious) {
        $accessError = "Security Warning: QR token blocked due to malware suspicion.";
        $_SESSION['qr_token'] = null;
        $qrToken = '';
    }
}

/* ==============================
   Step 1: Process token if safe
   ============================== */
if ($qrToken && !$accessError) {
    $_SESSION['qr_token'] = $qrToken;
    $stmt = $con->prepare("SELECT * FROM qr_secondlayer WHERE token = ?");
    $stmt->bind_param("s", $qrToken);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows) {
        $qrSecond = $res->fetch_assoc();
        $fid = $qrSecond['id'];
        $_SESSION['scan_status'] = (int)$qrSecond['scan_status'];


        $stmt2 = $con->prepare("SELECT * FROM qr_security WHERE id = ?");
        $stmt2->bind_param("i", $fid);
        $stmt2->execute();
        $res2 = $stmt2->get_result();

        if ($res2->num_rows) {
            $qr = $res2->fetch_assoc();
            $_SESSION['qr_id'] = $qr['id'];

            if (!isset($_SESSION['qr_uploaded_base64'])) {
                $_SESSION['qr_uploaded_base64'] =
                    'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mNkYAAAAAYAAjCB0C8AAAAASUVORK5CYII=';
                $_SESSION['qr_uploaded_mime'] = 'image/png';
            }
        } else {
            $accessError = "No security record found for this QR.";
        }
    } else {
        $accessError = "Invalid or expired QR token.";
    }
}

/* ==============================
   Step 2: Notifications
   ============================== */
if ($scanMessage) {
    echo "<script>alert(" . json_encode($scanMessage) . ");</script>";
}
if ($accessError) {
    echo "<script>alert(" . json_encode($accessError) . ");</script>";
}
// Step 3: OTP/Password access
if (isset($_POST['submit_access'], $_SESSION['qr_token'])) {
    $token = $_SESSION['qr_token'];
    $otp = trim($_POST['otp'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $passwordOk = false;

    $stmt = $con->prepare("SELECT * FROM qr_secondlayer WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $rs = $stmt->get_result();

    if ($rs->num_rows) {
        $qrSecond = $rs->fetch_assoc();
        $qid = $qrSecond['id'];
        $scanStatus = (int)$qrSecond['scan_status']; // NEW: check scan_status

        $stmt2 = $con->prepare("SELECT * FROM qr_security WHERE id = ?");
        $stmt2->bind_param("i", $qid);
        $stmt2->execute();
        $rs2 = $stmt2->get_result();

        if ($rs2->num_rows) {
            $qr = $rs2->fetch_assoc();

            // 🔹 Case 1: scan_status = 0 → open QR, auto-grant access
            if ($scanStatus === 0) {
                $passwordOk = true;
            } 
            // 🔹 Case 2: scan_status = 1 → require OTP or password
            else {
                // OTP validation
                if ($otp && isset($_SESSION['otp_code'], $_SESSION['otp_created_at'])) {
                    if (
                        $otp === (string) $_SESSION['otp_code'] &&
                        (time() - $_SESSION['otp_created_at']) <= 60
                    ) { // OTP valid for 60 seconds
                        $passwordOk = true;
                    } else {
                        $accessError = "Invalid or expired OTP. Please try again.";
                    }
                }

                // Password validation (if OTP didn't work or wasn't provided)
                if (!$passwordOk && $password) {
                    if (password_verify($password, $qr['password_hash'])) {
                        $passwordOk = true;
                    } else {
                        $accessError = "Incorrect password.";
                    }
                }

                // If still not valid and no error set
                if (!$passwordOk && empty($accessError)) {
                    $accessError = "Please enter a valid password or OTP.";
                }
            }
            if ($passwordOk) {
                // --- Decryption Logic ---
                // This complex logic is assumed correct and is kept from your original code.
                $encContent = base64_decode($qrSecond['encrypted_content']);
                $encKey = base64_decode($qrSecond['encrypted_key']);
                $salt = base64_decode($qrSecond['salt']);
                $nonceC = base64_decode($qrSecond['nonce']);
                $nonceK = base64_decode($qrSecond['nonce_key']);
                $ts = $qrSecond['time'];
                $stmtUid = $con->prepare("SELECT user_id FROM qr_security WHERE id = ?");
                $stmtUid->bind_param("i", $qid);
                $stmtUid->execute();
                $ru = $stmtUid->get_result()->fetch_assoc();
                $uid = $ru['user_id'];
                $stmtU = $con->prepare("SELECT email, phrase FROM users WHERE id = ?");
                $stmtU->bind_param("i", $uid);
                $stmtU->execute();
                $ud = $stmtU->get_result()->fetch_assoc();
                $derived = hash('sha256', $ts . $ud['email'] . $ud['phrase'], true);
                $userKey = sodium_crypto_pwhash(SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_IETF_KEYBYTES, $derived, $salt, SODIUM_CRYPTO_PWHASH_OPSLIMIT_MODERATE, SODIUM_CRYPTO_PWHASH_MEMLIMIT_MODERATE, SODIUM_CRYPTO_PWHASH_ALG_DEFAULT);
                $perKey = sodium_crypto_aead_chacha20poly1305_ietf_decrypt($encKey, '', $nonceK, $userKey);
                $content = sodium_crypto_aead_chacha20poly1305_ietf_decrypt($encContent, '', $nonceC, $perKey);

                // === Step 7.5: Insert access log ===
// === Step 7.5: Insert access log ===
                $accessType = '';
                $userEmail = '';

                if (!empty($_POST['otp']) && isset($_SESSION['otp_code'], $_SESSION['otp_created_at'])) {
                    // User submitted OTP, use the OTP email (from input)
                    $userEmail = trim($_POST['otp_email'] ?? '');
                    $accessType = 'Access through OTP';
                } elseif (!empty($_POST['password']) && isset($ud['email'])) {
                    // User submitted password, use the main email (from DB)
                    $userEmail = $ud['email'];
                    $accessType = 'Access through Password';
                } else {
                    $accessType = 'Unknown';
                }

                // Get user IP address
                $ipAddress = $_SERVER['REMOTE_ADDR'] ?? 'Unknown IP';

                // Get qr_id from session or fetched row
                $qr_id = $_SESSION['qr_id'] ?? ($row['id'] ?? null);

                // Log access only if all necessary fields are available
                if (!empty($userEmail) && !empty($qr_id)) {
                    $stmtLog = mysqli_prepare($con, "INSERT INTO accessrecord (email, typeaccess, qr_id, ip_address, timestamp) VALUES (?, ?, ?, ?, NOW())");
                    if ($stmtLog) {
                        mysqli_stmt_bind_param($stmtLog, "ssis", $userEmail, $accessType, $qr_id, $ipAddress);
                        mysqli_stmt_execute($stmtLog);
                        mysqli_stmt_close($stmtLog);
                    }
                }

                // --- Content Handling ---
                $ft = $qrSecond['file_type'] ?? '';
                $fn = $qrSecond['file_name'] ?? 'file';
                if ($ft === 'pdf') {
                    header('Content-Type: application/pdf');
                    header('Content-Disposition: inline; filename="' . $fn . '"');
                    echo $content;
                    exit;
                }
                if ($ft === 'image') {
    $rawContent = $content;

    // Try decoding safely (check if it's base64)
    $decoded = base64_decode($content, true);
    if ($decoded !== false && strlen($decoded) > 0) {
        $rawContent = $decoded; // It was valid base64
    }

    // Detect MIME type
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->buffer($rawContent) ?: "image/png";

    // Clean output buffer
    if (ob_get_level()) {
        ob_end_clean();
    }

    // Send headers + image
    header("Content-Type: $mime");
    header("Content-Length: " . strlen($rawContent));
    echo $rawContent;
    exit;
}

                if (filter_var($content, FILTER_VALIDATE_URL)) {
                    header("Location: $content");
                    exit;
                }
                $qrContent = $content;
                $showQrContent = true;
                unset($_SESSION['otp_code'], $_SESSION['otp_created_at']);


            }
        } else {
            $accessError = "Security data missing.";
        }
    } else {
        $accessError = "Invalid token.";
    }
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure QR Access</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --accent-color:linear-gradient(135deg, #d7e8f7, #ffe5d9);
            /* A vibrant mint/teal for accents */
            --background-start: #1f2027;
            /* Dark slate */
            --background-end: #2c2d3e;
            /* Dark purple/blue */
            --text-color: #f0f0f0;
            --card-background: rgba(255, 255, 255, 0.05);
            --card-border: rgba(255, 255, 255, 0.1);
            --input-bg: rgba(0, 0, 0, 0.2);
            --error-color: #ff6b6b;
            --success-color: #4dff94;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, var(--background-start), var(--background-end));
            color: var(--text-color);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }

        .card {
            width: 100%;
            max-width: 480px;
            background: var(--card-background);
            backdrop-filter: blur(15px);
            /* The "glass" effect */
            -webkit-backdrop-filter: blur(15px);
            border: 1px solid var(--card-border);
            border-radius: 20px;
            padding: 35px 40px;
            text-align: center;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.37);
        }

        h1 {
            font-weight: 600;
            font-size: 2.2rem;
            margin-bottom: 20px;
            background: linear-gradient(90deg, var(--accent-color), #fff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .qr-display {
            margin-bottom: 25px;
        }

        .qr-display p {
            font-size: 0.9rem;
            margin-bottom: 10px;
            opacity: 0.8;
        }

        img.qr-thumb {
            display: inline-block;
            max-width: 180px;
            border-radius: 12px;
            background: white;
            padding: 8px;
            border: 1px solid var(--card-border);
        }

        .form-group {
            margin-bottom: 18px;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 300;
            font-size: 0.9rem;
        }

        input,
        textarea,
        button {
            width: 100%;
            padding: 15px;
            font-size: 1rem;
            font-family: 'Poppins', sans-serif;
            border-radius: 10px;
            border: 1px solid transparent;
            transition: all 0.3s ease;
        }

        input,
        textarea {
            background: var(--input-bg);
            color: var(--text-color);
            border: 1px solid var(--card-border);
        }

        input:focus,
        textarea:focus {
            outline: none;
            border-color: var(--accent-color);
            box-shadow: 0 0 15px rgba(0, 245, 198, 0.2);
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        button {
            background: var(--accent-color);
            color: #111;
            font-weight: 600;
            cursor: pointer;
            border: none;
            margin-top: 10px;
        }

        button:hover,
        button:focus {
            background: #fff;
            box-shadow: 0 0 20px rgba(0, 245, 198, 0.4);
            transform: translateY(-2px);
        }

        .otp-group {
            display: flex;
            gap: 10px;
            align-items: stretch;
        }

        .otp-group input {
            flex-grow: 1;
        }

        .otp-group button {
            flex-shrink: 0;
            width: auto;
            padding: 10px 20px;
            margin-top: 0;
            /* Align with input */
        }

        .divider {
            margin: 25px 0;
            font-weight: 300;
            opacity: 0.7;
            text-transform: uppercase;
            font-size: 0.8rem;
            position: relative;
        }

        .divider::before,
        .divider::after {
            content: '';
            position: absolute;
            top: 50%;
            width: 35%;
            height: 1px;
            background: var(--card-border);
        }

        .divider::before {
            left: 0;
        }

        .divider::after {
            right: 0;
        }

        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }

        .alert.error {
            background: rgba(255, 107, 107, 0.15);
            color: var(--error-color);
            border: 1px solid var(--error-color);
        }

        .alert.success {
            background: rgba(77, 255, 148, 0.15);
            color: var(--success-color);
            border: 1px solid var(--success-color);
        }

        .otp-timer {
            color: var(--accent-color);
            margin-top: 10px;
            font-size: 0.9rem;
            height: 1.2em;
            /* Reserve space to prevent layout shift */
        }

        /* Responsive Design */
        @media (max-width: 600px) {
            .card {
                padding: 25px 20px;
                border-radius: 15px;
            }

            h1 {
                font-size: 1.8rem;
            }

            input,
            button,
            textarea {
                padding: 12px;
            }
        }
    </style>
</head>

<body>

    <div class="card">
        <h1>Secure Access</h1>

        <?php if ($accessError): ?>
            <div class="alert error"><?= htmlspecialchars($accessError) ?></div><?php endif; ?>
        <?php if ($otpError): ?>
            <div class="alert error"><?= htmlspecialchars($otpError) ?></div><?php endif; ?>
        <?php if ($otpSent): ?>
            <div class="alert success">OTP sent to <?= htmlspecialchars($_SESSION['otp_email']) ?>. It will expire in 60
                seconds.</div><?php endif; ?>

        <?php if ($qrToken && isset($_SESSION['qr_uploaded_base64'])): ?>
            <div class="qr-display">
                <p style="font-size:30px;">SQ-TECH SOLVER</p>
                <p>Secure . Reliable . Convenience</p><br><br>
                <p>Content locked by this QR Code</p>
                <img class="qr-thumb"
                    src="data:<?= htmlspecialchars($_SESSION['qr_uploaded_mime']) ?>;base64,<?= htmlspecialchars($_SESSION['qr_uploaded_base64']) ?>"
                    alt="Scanned QR Code">
            </div>
        <?php endif; ?>

        <?php if ($showQrContent): ?>
    <div class="form-group">
        <label for="decryptedContent">Decrypted Content</label>
        <textarea id="decryptedContent" readonly><?= htmlspecialchars($qrContent) ?></textarea>
    </div>
    <button onclick="location.href='externalscan.php'">Scan Another QR</button>

<?php elseif ($qr): ?>
<?php if (isset($_SESSION['scan_status']) && $_SESSION['scan_status'] == 0): ?>
    <!-- ✅ Direct access, only show Unlock button -->
    <form method="post" id="accessForm">
        <input type="hidden" name="qr_token" id="qr_token" value="<?= htmlspecialchars($qrToken) ?>">

        <button type="submit" name="submit_access">Unlock Content</button>
    </form>

<?php elseif (isset($_SESSION['scan_status']) && $_SESSION['scan_status'] == 1): ?>
    <!-- 🔑 Password / OTP required -->
    <form method="post" id="accessForm">
        <input type="hidden" name="qr_token" id="qr_token" value="<?= htmlspecialchars($qrToken) ?>">


        <div class="form-group">
            <label for="otp_email">1. Enter Email to Receive OTP</label>
            <div class="otp-group">
                <input type="email" name="otp_email" id="otp_email" placeholder="your.email@example.com">
                <button type="button" id="sendOtpBtn">Send</button>
            </div>
            <div class="otp-timer" id="otp-timer"></div>
        </div>

        <div class="form-group">
            <label for="otp_input">2. Enter OTP</label>
            <input type="text" name="otp" id="otp_input" placeholder="6-digit code">
        </div>

        <div class="divider">OR</div>

        <div class="form-group">
            <label for="password_input">Enter Password</label>
            <input type="password" name="password" id="password_input" placeholder="••••••••">
        </div>

        <button type="submit" name="submit_access">Unlock Content</button>
    </form>

<?php else: ?>
    <!-- 🚫 Invalid or missing scan_status -->
    <div class="alert alert-warning text-center mt-3">
        Scan status not available. Please try scanning again.
    </div>
<?php endif; ?>


<?php else: ?>
    <p>No valid QR code found. Please return to the scanner and try again.</p>
    <button onclick="location.href='externalscan.php'">Back to Scanner</button>
<?php endif; ?>
</div>

    <script>
        // --- Your existing JavaScript logic remains the same ---
        // It's already well-structured for functionality. The new CSS classes will apply automatically.
        function startOTPTimer(seconds) {
            const timerDisplay = document.getElementById("otp-timer");
            const sendOtpBtn = document.getElementById("sendOtpBtn");
            timerDisplay.style.display = "block";
            sendOtpBtn.disabled = true;
            clearInterval(window.countdownInterval);

            let remaining = seconds;
            timerDisplay.textContent = `Resend available in ${remaining}s`;

            window.countdownInterval = setInterval(() => {
                remaining--;
                if (remaining <= 0) {
                    clearInterval(window.countdownInterval);
                    timerDisplay.textContent = "";
                    sendOtpBtn.disabled = false;
                } else {
                    timerDisplay.textContent = `Resend available in ${remaining}s`;
                }
            }, 1000);
        }

        document.addEventListener("DOMContentLoaded", () => {
            const sendOtpBtn = document.getElementById("sendOtpBtn");
            const accessForm = document.getElementById("accessForm");
            const otpEmailInput = document.getElementById("otp_email");
            const qrTokenInput = document.getElementById("qr_token");

            if (sendOtpBtn) {
                sendOtpBtn.addEventListener("click", () => {
                    const email = otpEmailInput.value.trim();
                    const token = qrTokenInput.value.trim();

                    if (!email || !/^\S+@\S+\.\S+$/.test(email)) {
                        alert("Please enter a valid email address.");
                        return;
                    }

                    sendOtpBtn.disabled = true; // Disable button immediately
                    sendOtpBtn.textContent = "Sending...";

                    fetch("sendotp.php", {
                        method: "POST",
                        headers: { "Content-Type": "application/json" },
                        body: JSON.stringify({ email, token })
                    })
                        .then(res => res.json())
                        .then(data => {
                            if (data.success) {
                                alert("OTP sent to " + email);
                                startOTPTimer(60); // Start 60-second timer
                            } else {
                                alert(data.error || "Failed to send OTP. Please check the email and try again.");
                                sendOtpBtn.disabled = false;
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            alert("An error occurred. Please try again later.");
                            sendOtpBtn.disabled = false;
                        })
                        .finally(() => {
                            sendOtpBtn.textContent = "Send";
                        });
                });
            }

            if (accessForm) {
                accessForm.addEventListener("submit", (e) => {
                    const password = document.querySelector("input[name='password']").value.trim();
                    const otp = document.getElementById("otp_input").value.trim();

                    if (!otp && !password) {
                        alert("Please enter either the OTP or your Password to unlock the content.");
                        e.preventDefault();
                    }
                });
            }
        });

        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>

</body>

</html>