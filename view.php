<?php
session_start();
include 'componet/conn.php';
date_default_timezone_set('Asia/Kuala_Lumpur');

// ✅ Ensure access is granted and code is provided
if (!isset($_SESSION['access_granted']) || !isset($_GET['code'])) {
    header("Location: scan.php");
    exit;
}

$qrFilename = $_GET['code'];

// ✅ Fetch QR from database
$stmt = $con->prepare("SELECT * FROM qr_security WHERE qr_filename = ?");
$stmt->bind_param("s", $qrFilename);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("❌ QR code not found.");
}

$qr = $result->fetch_assoc();

// ✅ Extract image from blob and write to temp file
$qrContent = "❌ QR content could not be read.";
$tempFile = tempnam(sys_get_temp_dir(), 'qr_') . '.png';

if (!empty($qr['qr_image'])) {
    file_put_contents($tempFile, $qr['qr_image']);

    // ✅ Decode using zbarimg
    $command = escapeshellcmd("zbarimg --raw " . escapeshellarg($tempFile) . " 2>&1");
    $output = shell_exec($command);
    $decoded = trim($output);

    if (!empty($decoded)) {
        $qrContent = $decoded;
    }

    unlink($tempFile); // ✅ Cleanup temp file
}

// ✅ Clear access flag (optional)
unset($_SESSION['access_granted']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>QR Code Content</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f0f4f8;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            margin: 0;
        }

        .box {
            background: white;
            padding: 30px 40px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            max-width: 650px;
            width: 100%;
        }

        h2 {
            color: #2563eb;
            margin-bottom: 20px;
        }

        textarea {
            width: 100%;
            padding: 14px;
            font-size: 16px;
            border-radius: 8px;
            border: 1px solid #ccc;
            resize: vertical;
            min-height: 150px;
            margin-bottom: 20px;
        }

        .preview {
            margin-bottom: 20px;
        }

        .preview img {
            max-width: 200px;
            border: 1px solid #ccc;
            border-radius: 8px;
        }

        a {
            display: inline-block;
            margin-top: 10px;
            color: #2563eb;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="box">
    <h2>🔓 QR Code Content</h2>

    <!-- ✅ Preview image -->
    <div class="preview">
        <strong>QR Preview:</strong><br>
        <img src="data:image/png;base64,<?= base64_encode($qr['qr_image']) ?>" alt="QR Image">
    </div>

    <!-- ✅ Decoded content -->
    <label><strong>Decoded Content:</strong></label>
    <textarea readonly><?= htmlspecialchars($qrContent) ?></textarea>

    <a href="scan.php">← Back to Scanner</a>
</div>
</body>
</html>
