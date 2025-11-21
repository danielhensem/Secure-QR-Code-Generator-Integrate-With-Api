<?php
include_once("componet/conn.php");
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
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SQ-TECH SOLVER - Secure QR Code Generator</title>
    <link rel="icon" type="image/png" href="img/log.svg">

    <link rel="stylesheet" href="style-index.css">
    <link rel="stylesheet" href="style-res.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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

        /* Main Body & Layout */
        body {
            background-color: #f4f7f6;
            /* A slightly softer background */
        }

        .main-body {
            display: flex;
            flex-wrap: wrap;
            /* allows wrapping on small screens */
            justify-content: center;
            /* center horizontally */
            align-items: flex-start;
            /* align from top */
            width: 100%;
            max-width: 100%;

            gap: 20px;
            /* space between containers */
        }

        /* Generator Container (fixed width on desktop) */
        .generator-container {
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            flex: 0 0 900px;
            /* fixed width = 750px */
            width: 900px;
            background: #424141ff;
            background: linear-gradient(90deg, rgba(255, 255, 255, 1) 35%, rgba(194, 192, 192, 1) 100%);
            border-radius: 40px;
            border: 1px solid black;
            box-shadow: 0 60px 110px rgba(0, 0, 0, 0.08);
            padding: 30px 40px;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }

        /* Information Container (takes remaining space) */
        .information-container {
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: stretch;
            flex: 1;
            /* fills the remaining space */
            background: linear-gradient(135deg, #8ee3ef, #ffd4bf);
            border-radius: 40px;
            box-shadow: 0 60px 110px rgba(0, 0, 0, 0.08);
            padding: 30px 40px;
            box-sizing: border-box;
            transition: all 0.3s ease;
        }

        /* Responsive layout for small screens */
        @media (max-width: 992px) {
            .main-body {
                flex-direction: column;
                /* stack vertically */
                align-items: center;
            }

            .generator-container,
            .information-container {
                width: 100%;
                max-width: 100%;
                flex: none;
                /* remove fixed behavior */
            }
        }


        .generator-header h1 {
            margin-bottom: 30px;
            font-family: 'Segoe UI', sans-serif;
            text-align: center;
            font-size: 2.5rem;
            color: #333;

        }

        /* Step Indicator */
        .step-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 5px;
            margin-bottom: 60px;
        }

        .step {
            padding: 6px 12px;
            border-radius: 20px;
            background-color: #e9ecef;
            color: #6c757d;
            font-size: 13px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .step.active {
            background-color: #007bff;
            color: white;
            font-weight: 700;
        }

        .arrow {
            color: #111111ff;
            font-weight: bold;
        }

        /* Tabbed Interface */
        .tab-buttons {
            display: flex;
            border-bottom: 2px solid #dee2e6;
            margin-bottom: 25px;
        }

        .tab-button {
            padding: 12px 25px;
            cursor: pointer;
            background: transparent;
            border: none;
            font-size: 16px;
            font-weight: 600;
            color: #6c757d;
            position: relative;
            bottom: -2px;
            border-bottom: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .tab-button:hover {
            color: #0056b3;
        }

        .tab-button.active {
            color: #007bff;
            border-bottom-color: #007bff;
        }

        .tab-pane {
            display: none;
            /* Hidden by default */
        }

        .tab-pane.active {
            display: block;
            /* Shown when active */
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Form Elements Styling */
        textarea#user_text {
            width: 100%;
            height: 150px;
            padding: 12px;
            font-size: 16px;
            border-radius: 8px;
            border: 1px solid #ced4da;
            resize: vertical;
            box-sizing: border-box;
        }

        .file-upload-wrapper {
            border: 2px dashed #007bff;
            border-radius: 8px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            background-color: #f8f9fa;
            transition: background-color 0.3s ease;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 8px;
            /* Optional: Adds a small, consistent space between items */
        }

        .file-upload-wrapper:hover {
            background-color: #e9ecef;
        }

        .file-upload-wrapper .fa-upload {
            font-size: 2.5rem;
            color: #007bff;
            margin-bottom: 15px;
        }

        .file-upload-wrapper p {
            font-size: 16px;
            font-weight: 600;
            color: #495057;
            margin: 0;
        }

        .file-name-display {
            font-size: 14px;
            color: #28a745;
            margin-top: 10px;
            font-weight: 500;
        }

        input[type="file"] {
            display: none;
        }

        /* Button Group */
        .button-group-centered {
            display: flex;
            flex-direction: column;
            flex-wrap: wrap;
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }

        .btn {
            padding: 12px 25px;
            font-size: 16px;
            font-weight: 600;
            border: none;
            background-color: #007bff;
            color: white;
            cursor: pointer;
            border-radius: 8px;
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
            margin: 0 5px;
        }

        .btn:hover {
            background-color: #0056b3;
            box-shadow: 0 4px 15px rgba(0, 123, 255, 0.3);
        }

        .btn-danger {
            background-color: #dc3545;
        }

        .btn-danger:hover {
            background-color: #a71d2a;
            box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);
        }

        .btn .fa-qrcode,
        .btn .fa-times {
            margin-right: 8px;
        }

        /* Spinner Overlay */
        #spinnerOverlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.9);
            z-index: 9999;
            align-items: center;
            justify-content: center;
            flex-direction: column;
        }

        #rotatingEmoji {
            font-size: 80px;
            user-select: none;
        }

        #statusText {
            font-size: 22px;
            color: #333;
            font-family: Arial, sans-serif;
            margin-top: 20px;
            font-weight: 500;
        }

        /* Footer */
        .footer-credit {
            text-align: center;
            margin-top: 40px;
            color: #6c757d;
            font-size: 14px;
        }
    </style>
</head>

<body class="animated-bg">
    <?php include("componet/navbar.php"); ?>
    <h1 class="case-title-h1" style="margin-left: 10px; margin-right: 10px;">Generate Secure QR Code</h1>
    <div class="row align-items-stretch main-body">
        <div class="col-lg-10 col-md-12" style="margin:10px;">
            <div class="generator-container">
                <div class="generator-header">

                    <div class="step-wrapper">
                        <div class="step active">Upload</div>
                        <div class="arrow">→</div>
                        <div class="step">Generate</div>
                        <div class="arrow">→</div>
                        <div class="step">Design</div>
                        <div class="arrow">→</div>
                        <div class="step">Securing</div>
                        <div class="arrow">→</div>
                        <div class="step">Complete</div>
                    </div>
                </div>
                <div style="display: flex; flex-wrap:wrap;align-items: center; justify-content: space-between; margin: 10px;">
                    <img src="img/s/3.svg" alt="Logo"
                        style=" width:120px;height: 120px; margin-top:20px; margin-left: auto; margin-right: auto;">
                    <img src="img/s/4.svg" alt="Logo"
                        style=" width:120px;height: 120px; margin-top:20px; margin-left: auto; margin-right: auto;">
                    <img src="img/s/5.svg" alt="Logo"
                        style=" width:120px;height: 120px; margin-top:20px; margin-left: auto; margin-right: auto;">
                    <img src="img/s/6.svg" alt="Logo"
                        style=" width:120px;height: 120px; margin-top:20px; margin-left: auto; margin-right: auto;">
                    <img src="img/s/7.svg" alt="Logo"
                        style=" width:120px;height: 120px; margin-top:20px; margin-left: auto; margin-right: auto;">

                </div>

              
                <form method="post" id="qrForm" enctype="multipart/form-data">
                    <div class="tab-buttons" style="text-align: center;justify-content:center; align-items: center;">
                        <button type="button" class="tab-button active" data-tab="textTab"><i class="fa fa-font"></i>
                            Text/URL</button>
                        <button type="button" class="tab-button" data-tab="pdfTab"><i class="fa fa-file-pdf"></i> PDF
                            File</button>
                        <button type="button" class="tab-button" data-tab="imageTab"><i class="fa fa-file-image"></i>
                            Image File</button>
                    </div>

                    <div class="tab-content">
                        <!-- TEXT TAB -->
                        <div id="textTab" class="tab-pane active">
                            <p style="font-size:16px;">Enter any text or URL you want to embed in the QR code.</p><br>
                            <textarea name="item_id" id="user_text" style="border-radius: 30px;"
                                placeholder="e.g., https://example.com or your secret message. The text cannot exceed 200 character."><?php echo isset($_POST['item_id']) ? htmlspecialchars($_POST['item_id']) : ''; ?></textarea>
                        </div>

                        <!-- PDF TAB -->
                        <div id="pdfTab" class="tab-pane">
                            <label for="pdfUpload" class="file-upload-wrapper"><br>
                                <i class="fa fa-upload"></i><br>
                                <p>Click to Upload PDF</p>
                                <span id="pdfFileName" class="file-name-display">No file selected. File cannot exceed
                                    40mb</span>
                            </label>
                            <input type="file" id="pdfUpload" name="pdf_file" accept="application/pdf">
                            <div id="pdfPreview" style="margin-top:10px; display:none;">
                                <embed id="pdfEmbed" type="application/pdf" width="100%" height="200px" />
                            </div>
                        </div>

                        <!-- IMAGE TAB -->
                        <div id="imageTab" class="tab-pane">
                            <label for="imageUpload" class="file-upload-wrapper">
                                <i class="fa fa-upload"></i>
                                <p>Click to Upload Image</p>
                                <span id="imageFileName" class="file-name-display">No image selected. Image cannot
                                    exceed 40mb</span>
                            </label>
                            <input type="file" id="imageUpload" name="image_file" accept="image/*">
                            <div id="imagePreview" style="margin-top:10px; display:none;">
                                <img id="previewImg" src="" style="max-width:100%; border-radius:10px;" />
                            </div>
                        </div>
                    </div>

                    <div class="button-group-centered" style="gap:15px;">
                        <button type="submit" name="generate_qr" id="generateBtn" class="btn"
                            style="border-radius: 40px;">
                            <i class="fa fa-qrcode"></i> Generate QR Code
                        </button>
                        <button type="button" id="cancelBtn" class="btn btn-danger" style="border-radius: 40px;">
                            <i class="fa fa-times"></i> Cancel
                        </button>
                    </div>
                </form>

                <div id="spinnerOverlay">
                    <div id="rotatingEmoji">🔲</div>
                    <div id="statusText">Generating QR Code...</div>
                </div>

                <script>
                    document.addEventListener("DOMContentLoaded", function () {
                        const tabs = document.querySelectorAll('.tab-button');
                        const panes = document.querySelectorAll('.tab-pane');
                        const pdfUpload = document.getElementById("pdfUpload");
                        const pdfFileName = document.getElementById("pdfFileName");
                        const pdfPreview = document.getElementById("pdfPreview");
                        const pdfEmbed = document.getElementById("pdfEmbed");
                        const imageUpload = document.getElementById("imageUpload");
                        const imageFileName = document.getElementById("imageFileName");
                        const imagePreview = document.getElementById("imagePreview");
                        const previewImg = document.getElementById("previewImg");

                        // Tab switching
                        tabs.forEach(tab => {
                            tab.addEventListener('click', () => {
                                tabs.forEach(t => t.classList.remove('active'));
                                panes.forEach(p => p.classList.remove('active'));
                                tab.classList.add('active');
                                document.getElementById(tab.dataset.tab).classList.add('active');
                            });
                        });

                        // PDF file preview
                        pdfUpload.addEventListener("change", function () {
                            const file = this.files[0];
                            if (file) {
                                pdfFileName.textContent = file.name;
                                if (file.size > 80 * 1024 * 1024) {
                                    alert("PDF file size cannot exceed 80 MB.");
                                    pdfUpload.value = "";
                                    pdfFileName.textContent = "No file selected";
                                    pdfPreview.style.display = "none";
                                    return;
                                }
                                const fileURL = URL.createObjectURL(file);
                                pdfEmbed.src = fileURL;
                                pdfPreview.style.display = "block";
                            } else {
                                pdfFileName.textContent = "No file selected";
                                pdfPreview.style.display = "none";
                            }
                        });

                        // Image preview
                        imageUpload.addEventListener("change", function () {
                            const file = this.files[0];
                            if (file) {
                                imageFileName.textContent = file.name;
                                if (file.size > 80 * 1024 * 1024) {
                                    alert("Image file size cannot exceed 80 MB.");
                                    imageUpload.value = "";
                                    imageFileName.textContent = "No image selected";
                                    imagePreview.style.display = "none";
                                    return;
                                }
                                const reader = new FileReader();
                                reader.onload = e => {
                                    previewImg.src = e.target.result;
                                    imagePreview.style.display = "block";
                                };
                                reader.readAsDataURL(file);
                            } else {
                                imageFileName.textContent = "No image selected";
                                imagePreview.style.display = "none";
                            }
                        });

                        // Cancel button
                        document.getElementById("cancelBtn").addEventListener("click", function () {
                            document.getElementById("user_text").value = "";
                            pdfUpload.value = "";
                            imageUpload.value = "";
                            pdfFileName.textContent = "No file selected";
                            imageFileName.textContent = "No image selected";
                            pdfPreview.style.display = "none";
                            imagePreview.style.display = "none";
                            tabs[0].click();
                        });

                        // Validate before submitting
                        document.getElementById("qrForm").addEventListener("submit", function (event) {
                            const text = document.getElementById("user_text").value.trim();
                            const activeTab = document.querySelector(".tab-pane.active").id;

                            if (activeTab === "textTab") {
                                const wordCount = text.split(/\s+/).filter(Boolean).length;
                                if (wordCount > 200) {
                                    alert("Text input cannot exceed 200 words.");
                                    event.preventDefault();
                                    return;
                                }
                            }

                            if (activeTab === "pdfTab" && pdfUpload.files[0]) {
                                if (pdfUpload.files[0].size > 30 * 1024 * 1024) {
                                    alert("PDF file size cannot exceed 40 MB.");
                                    event.preventDefault();
                                    return;
                                }
                            }

                            if (activeTab === "imageTab" && imageUpload.files[0]) {
                                if (imageUpload.files[0].size > 40 * 1024 * 1024) {
                                    alert("Image file size cannot exceed 40 MB.");
                                    event.preventDefault();
                                    return;
                                }
                            }

                            // Only show spinner if validation passed
                            document.getElementById("spinnerOverlay").style.display = "flex";
                        });
                    });
                </script>


                <?php
                // --- BACKEND PHP PROCESSING LOGIC 
                if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["generate_qr"])) {
                    include 'phpqrcode/qrlib.php';
                    include 'componet/conn.php';
                    include 'custom_crypto.php';

                    if (!isset($_SESSION['id'])) {
                        die("User not logged in.");
                    }

                    $userId = $_SESSION['id'];
                    $item = '';
                    $fileType = '';
                    $originalFileName = '';

                    // ==============================
                    // Handle Input (Code 2)
                    // ==============================
                    if (!empty($_POST["item_id"])) {
                        $item = trim($_POST["item_id"]);
                        $fileType = 'text';
                    } elseif (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] === UPLOAD_ERR_OK) {
                        $originalFileName = basename($_FILES['pdf_file']['name']);
                        $fileData = file_get_contents($_FILES['pdf_file']['tmp_name']);
                        $item = $fileData;
                        $fileType = 'pdf';
                    } elseif (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
                        $originalFileName = basename($_FILES['image_file']['name']);
                        $fileData = file_get_contents($_FILES['image_file']['tmp_name']);
                        $item = $fileData; // keep raw binary, no base64
                        $fileType = 'image';
                    }

                    // ==============================
                    // Malware Scan with Session Cache (Code 1 + extended)
                    // ==============================
                    if (!empty($item)) {
                        $apiKey = "1131364b275b749ce59649385236b3ec"; // MetaDefender API key
                        $malicious = false;
                        $qrToken = hash("sha256", $item); // use hash as token key
                
                        if (isset($_SESSION['scanned_tokens'][$qrToken])) {
                            // Load cached result
                            $scanMessage = $_SESSION['scanned_tokens'][$qrToken]['message'];
                            $malicious = $_SESSION['scanned_tokens'][$qrToken]['malicious'];

                            if ($malicious) {
                                $_SESSION = []; // clear all
                                header("Location: product.php");
                                exit;
                            }
                        } else {
                            // Scan required
                            if ($fileType === 'text') {
                                // Save as .txt for scanning
                                $tempFile = tempnam(sys_get_temp_dir(), "qr_") . ".txt";
                                file_put_contents($tempFile, $item);
                                $scanPayload = new CURLFile($tempFile, "text/plain", basename($tempFile));
                            } elseif ($fileType === 'pdf') {
                                $tempFile = tempnam(sys_get_temp_dir(), "qr_") . ".pdf";
                                file_put_contents($tempFile, $item);
                                $scanPayload = new CURLFile($tempFile, "application/pdf", basename($tempFile));
                            } elseif ($fileType === 'image') {
                                // Safe decode if base64
                                $decoded = base64_decode($item, true);
                                $imageData = ($decoded !== false && strlen($decoded) > 0) ? $decoded : $item;

                                // Detect MIME
                                $finfo = new finfo(FILEINFO_MIME_TYPE);
                                $mime = $finfo->buffer($imageData);

                                // Map MIME → correct extension
                                $ext = "bin"; // default fallback
                                switch ($mime) {
                                    case "image/jpeg":
                                        $ext = "jpg";
                                        break;
                                    case "image/png":
                                        $ext = "png";
                                        break;
                                    case "image/gif":
                                        $ext = "gif";
                                        break;
                                    case "image/webp":
                                        $ext = "webp";
                                        break;
                                }

                                // Create temp file with right extension
                                $tempFile = tempnam(sys_get_temp_dir(), "qr_") . "." . $ext;
                                file_put_contents($tempFile, $imageData);

                                // Send correct MIME type to API
                                $scanPayload = new CURLFile($tempFile, $mime, basename($tempFile));
                            } elseif (filter_var($item, FILTER_VALIDATE_URL)) {
                                // Scan URL directly
                                $submitUrl = "https://api.metadefender.com/v4/url";
                                $ch = curl_init($submitUrl);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                curl_setopt($ch, CURLOPT_POST, true);
                                curl_setopt($ch, CURLOPT_HTTPHEADER, ["apikey: $apiKey"]);
                                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(["url" => $item]));
                                $submitResponse = curl_exec($ch);
                                $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                                curl_close($ch);

                                $submitResult = json_decode($submitResponse, true);

                                if ($httpCode == 200 && !empty($submitResult['data_id'])) {
                                    $dataId = $submitResult['data_id'];
                                    sleep(3);

                                    $reportUrl = "https://api.metadefender.com/v4/url/" . $dataId;
                                    $ch = curl_init($reportUrl);
                                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                    curl_setopt($ch, CURLOPT_HTTPHEADER, ["apikey: $apiKey"]);
                                    $reportResponse = curl_exec($ch);
                                    curl_close($ch);

                                    $reportResult = json_decode($reportResponse, true);

                                    if (isset($reportResult['scan_results']['scan_all_result_i'])) {
                                        if ($reportResult['scan_results']['scan_all_result_i'] === 0) {
                                            $scanMessage = "✅ URL is safe.";
                                        } else {
                                            $malicious = true;
                                            $scanMessage = "⚠️ Dangerous URL detected. Access blocked.";
                                        }
                                    } else {
                                        $scanMessage = "⚠️ Scan report not ready. Try again.";
                                    }
                                } else {
                                    $scanMessage = "⚠️ Failed to connect to MetaDefender API. (HTTP $httpCode)";
                                }

                                // Save result in session
                                $_SESSION['scanned_tokens'][$qrToken] = [
                                    'malicious' => $malicious,
                                    'message' => $scanMessage
                                ];

                                if ($malicious) {
                                    $_SESSION = [];
                                    header("Location: product.php");
                                    exit;
                                }
                                // stop further file-scan flow
                                return;
                            }

                            // ---- File scan branch (PDF, Image, Text) ----
                            $submitUrl = "https://api.metadefender.com/v4/file";
                            $ch = curl_init($submitUrl);
                            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                            curl_setopt($ch, CURLOPT_POST, true);
                            curl_setopt($ch, CURLOPT_HTTPHEADER, ["apikey: $apiKey"]);
                            curl_setopt($ch, CURLOPT_POSTFIELDS, ["file" => $scanPayload]);
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
                                        $scanMessage = "✅ File is safe.";
                                    } else {
                                        $malicious = true;
                                        $scanMessage = "⚠️ Dangerous file detected. Access blocked.";
                                    }
                                } else {
                                    $scanMessage = "⚠️ Scan report not ready. Try again.";
                                }
                            } else {
                                $scanMessage = "⚠️ Failed to connect to MetaDefender API. (HTTP $httpCode)";
                            }

                            // ✅ Save result in session
                            $_SESSION['scanned_tokens'][$qrToken] = [
                                'malicious' => $malicious,
                                'message' => $scanMessage
                            ];

                            if ($malicious) {
                                $_SESSION = [];
                                header("Location: product.php");
                                exit;
                            }
                        }
                    }


                    if (empty($item)) {
                        // Optional: Handle case where nothing was submitted
                        echo '<script>alert("Please provide text, a PDF, or an image to generate a QR code."); document.getElementById("spinnerOverlay").style.display = "none";</script>';
                        return;
                    }

                    $stmt = mysqli_prepare($con, "SELECT email, phrase FROM users WHERE id = ? LIMIT 1");
                    mysqli_stmt_bind_param($stmt, "i", $userId);
                    mysqli_stmt_execute($stmt);
                    $result = mysqli_stmt_get_result($stmt);

                    if (!$result || mysqli_num_rows($result) === 0) {
                        http_response_code(404);
                        echo json_encode(['error' => 'User not found']);
                        exit();
                    }

                    $user = mysqli_fetch_assoc($result);
                    $email = $user['email'];
                    $phrase = $user['phrase'];

                    $timestamp = time();
                    $key_material = $timestamp . $email . $phrase;
                    $derived_input = hash('sha256', $key_material, true);
                    $salt = random_bytes(16);

                    if (!empty($item)) {
                        $userKey = sodium_crypto_pwhash(
                            SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_IETF_KEYBYTES,
                            $derived_input,
                            $salt,
                            SODIUM_CRYPTO_PWHASH_OPSLIMIT_MODERATE,
                            SODIUM_CRYPTO_PWHASH_MEMLIMIT_MODERATE
                        );

                        $perQrKey = random_bytes(SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_IETF_KEYBYTES);

                        $nonceContent = random_bytes(SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_IETF_NPUBBYTES);
                        $encryptedContent = sodium_crypto_aead_chacha20poly1305_ietf_encrypt($item, '', $nonceContent, $perQrKey);

                        $nonceKey = random_bytes(SODIUM_CRYPTO_AEAD_CHACHA20POLY1305_IETF_NPUBBYTES);
                        $encryptedKey = sodium_crypto_aead_chacha20poly1305_ietf_encrypt($perQrKey, '', $nonceKey, $userKey);

                        $digest = hash('sha256', $encryptedContent);
                        $fileNameForDB = $originalFileName ?: '';
                        $token = 'SQ-TechSolverV1.25' . $userId . '-' . bin2hex(random_bytes(5));
                        $_SESSION['last_qr_token'] = $token;

                        $encodedEncryptedContent = base64_encode($encryptedContent);
                        $encodedEncryptedKey = base64_encode($encryptedKey);
                        $encodedSalt = base64_encode($salt);
                        $encodedNonceContent = base64_encode($nonceContent);
                        $encodedNonceKey = base64_encode($nonceKey);

                        // Prepare the insert statement
                        $stmt = mysqli_prepare($con, "INSERT INTO qr_secondlayer (
                encrypted_content, encrypted_key, digest, token,
                salt, nonce, nonce_key, tag, time, file_type, file_name
            ) VALUES (?, ?, ?, ?, ?, ?, ?, '', ?, ?, ?)");

                        // Create placeholder for the BLOB field
                        $nullBlob = NULL; // For encrypted_content
                
                        // Bind parameters: 'b' for BLOB, 's' for strings, 'i' for integer
                        mysqli_stmt_bind_param(
                            $stmt,
                            "bssssssiss",
                            $nullBlob,               // 0 → encrypted_content (BLOB)
                            $encodedEncryptedKey,    // 1 → encrypted_key
                            $digest,                 // 2 → digest
                            $token,                  // 3 → token
                            $encodedSalt,            // 4 → salt
                            $encodedNonceContent,    // 5 → nonce
                            $encodedNonceKey,        // 6 → nonce_key
                            $timestamp,              // 7 → time (int)
                            $fileType,               // 8 → file_type
                            $fileNameForDB           // 9 → file_name
                        );

                        // Send actual BLOB content separately
                        mysqli_stmt_send_long_data($stmt, 0, $encodedEncryptedContent); // 0 = first param
                
                        // Execute and handle result
                        if (!mysqli_stmt_execute($stmt)) {
                            error_log("Insert failed: " . mysqli_stmt_error($stmt));
                            echo "<script>alert('Insert failed: " . mysqli_stmt_error($stmt) . "');</script>";
                        } else {
                            mysqli_stmt_close($stmt); // Clean up
                        }


                        $file = "images/qr1.png";
                        $baseUrl = "http://172.20.10.6:8080/SqTechSolver-Secure_Qr_Code_Generator_System/externalscan.php"; // Change to your actual server URL Hotspot Daniel
                        // $baseUrl = "http://192.168.100.17:8080/E-Commerce%20system/externalscan.php"; // Change to your actual server URL Wifi Rumah
                        $qrUrl = $baseUrl . "?token=" . urlencode($token);
                        QRcode::png($qrUrl, $file, 'H', 10, 5);

                        // Spinner Animation and Redirect Script
                        echo '
            <script>
                document.addEventListener("DOMContentLoaded", function () {
                    var overlay = document.getElementById("spinnerOverlay");
                    overlay.style.display = "flex"; // Ensure it is visible

                    var statusEl = document.getElementById("statusText");
                    var emoji = document.getElementById("rotatingEmoji");
                    
                    var percent = 0;
                    var rotation = 0;

                    var rotateInterval = setInterval(function () {
                        rotation = (rotation + 6) % 360;
                        emoji.style.transform = "rotate(" + rotation + "deg)";
                    }, 16);

                    var statusMessages = [
                        { max: 25, text: "Generating QR Code..." },
                        { max: 75, text: "Securing QR Code..." },
                        { max: 100, text: "Storing securely..." }
                    ];

                    var percentInterval = setInterval(function () {
                        percent++;
                        for (var i = 0; i < statusMessages.length; i++) {
                            if (percent <= statusMessages[i].max) {
                                statusEl.innerHTML = statusMessages[i].text;
                                break;
                            }
                        }

                        if (percent >= 100) {
                            clearInterval(percentInterval);
                            clearInterval(rotateInterval);
                            emoji.innerHTML = "✔️"; // Change to checkmark on completion
                            emoji.style.transform = "rotate(0deg)";
                            statusEl.innerHTML = "Done!";
                            setTimeout(function () {
                                window.location.href = "generateqr.php?scroll=true";
                            }, 500); // A brief pause before redirecting
                        }
                    }, 20); // Total time ~2 seconds
                });
            </script>';
                    }
                }
                ?>
                <!-- javascript sw -->
                <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
                <script src="index.js"></script>
</body>

</html>