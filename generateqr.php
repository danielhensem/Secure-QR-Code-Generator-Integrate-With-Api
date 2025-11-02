<?php include_once("componet/conn.php"); ?>
<?php session_start();
if (isset($_SESSION["login"])) {
    $name = $_SESSION["username"];
}
$qrPath = 'images/qr1.png';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="design.css">
    <title>SQ-TECH SOLVER Secure QR Code Generator</title>
        <link rel="icon" type="image/png" href="img/log.png">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom Animated Background -->

</head>

<body class="animated-bg">

    <div class="main-container">
        <div class="generator-header">
            <h1 style="font-weight: bold;">Design & Secure</h1>
            <div class="step-wrapper">
                <div class="step">Upload</div>
                <div class="arrow">→</div>
                <div class="step">Generate</div>
                <div class="arrow">→</div>
                <div class="step active">Design</div>
                <div class="arrow">→</div>
                <div class="step active">Securing</div>
                <div class="arrow">→</div>
                <div class="step">Complete</div>
            </div>
        </div>
        <div class="flex-row">


            <!-- Left Column: QR design + save + password -->
            <div class="left-col" style="background: linear-gradient(135deg, #8ee3ef, #ffd4bf);">
                <h2>Custom QR Code Design</h2>
                <p style="color: gray;">Customize your QR code with colors and a logo before securing it.</p>
                <hr>
                <div class="qr-design-tool">
                    <div class="qr-controls">
                        <p style="color: black;">Alert: The foreground must be darker than the background to easily scan
                            reading.</p>
                        <p style="color:red;"> Avoid: <br>
                            - Light-colored QR dots on dark background (e.g., yellow on black).<br>
                            - Bright gradients that reduce contrast.<br>
                            - Transparent or textured backgrounds that hide the code edges.</p><br>
                        <label for="fgColor">1.Foreground Color:</label>
                        <input type="color" id="fgColor" value="#000000">

                        <label for="bgColor">2.Background Color:</label>
                        <input type="color" id="bgColor" value="#ffffff">

                        <!-- <label for="logoUpload">Upload Logo (optional):</label>
                    <input type="file" id="logoUpload" accept="image/*"> -->

                        <!-- Save Design Button -->
                        
                        <p style="color: gray;">Click Save to keep your QR code changes.</p>
                        <button id="saveDesign" type="button" class="btn-submit"
                            style="display:flex ; border-radius:20px; align-items:center; justify-content:center; width:30%; height:30px;margin-bottom:15px;">
                            Save
                        </button>

                    </div>
                    <br>
                    <!-- Password Form -->
                    <form id="qrForm" action="finalreview.php" method="POST">
                        <div class="form-group">
                            
                            <strong for="password">3. Password</strong><br>
                            <p style="color: red;">
                            Password is optional. However, if you choose to enter one, it must follow the password rules
                            (minimum 8 characters, include uppercase letter, number, and special character).
                            Please ensure your QR design is saved before proceeding.
                        </p><br>
                            <input type="password" id="password" name="password"
                                placeholder="Enter password (optional)">
                        </div>

                        <button type="submit"
                            style="display: flex; border-radius:20px; align-items: center; justify-content: center; width: 30%; height: 30px;"
                            class="btn-submit">
                            Secure QR
                        </button>
                        <br>
                        
                    </form>

                    <script>
                        document.getElementById("qrForm").addEventListener("submit", function (event) {
                            const password = document.getElementById("password").value.trim();

                            // Only validate if user entered something
                            if (password !== "") {
                                if (password.length < 8) {
                                    alert("Password must be at least 8 characters long.");
                                    event.preventDefault();
                                    return;
                                }

                                if (!/[A-Z]/.test(password)) {
                                    alert("Password must contain at least one uppercase letter.");
                                    event.preventDefault();
                                    return;
                                }

                                if (!/[0-9]/.test(password)) {
                                    alert("Password must contain at least one number.");
                                    event.preventDefault();
                                    return;
                                }

                                if (!/[!@#$%^&*(),.?\":{}|<>]/.test(password)) {
                                    alert("Password must contain at least one special character.");
                                    event.preventDefault();
                                    return;
                                }
                            }
                        });
                    </script>

                </div>
            </div>

            <!-- Right Column: Live QR preview -->
            <div class="right-col"
                style="background: linear-gradient(135deg, #8ee3ef, #ffd4bf); justify-content:center; align-items: center;">
                <h2>Live QR Preview</h2>
                <div class="qr-preview-container"
                    style=" disply:flex;top:20px; align-items:center; justify-content: center;">
                    <canvas id="qrCanvas" display="flex" width="420" height="420" align-items="center"
                        justify-content="center"></canvas>
                </div>
            </div>

        </div>
    </div>
    <div class="footer-note" style="display:flex;align-items:center;justify-content:center;">&copy; 2025 SQ‑Tech Solver.
        All rights reserved.</div>

    <!-- Spinner Overlay -->
    <div id="spinnerOverlay"
        style="position:fixed;top:0;left:0;width:100%;height:100%;background-color:white;display:none;align-items:center;justify-content:center;flex-direction:column;z-index:9999;">
        <div style="text-align:center; margin-bottom: 20px;">
            <div id="rotatingEmoji" style="font-size:80px; user-select:none;">🔲</div>
            <div id="statusText" style="font-size:22px; color:#333; font-family:Arial,sans-serif; margin-top: 15px;">
                Generating QR Code...</div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const fgColorPicker = document.getElementById('fgColor');
            const bgColorPicker = document.getElementById('bgColor');
            // const logoUpload = document.getElementById('logoUpload');
            const canvas = document.getElementById('qrCanvas');
            const ctx = canvas.getContext('2d');

            let logoImage = null;
            let baseQR = new Image();
            baseQR.crossOrigin = "anonymous";
            baseQR.src = '<?php echo $qrPath; ?>';
            baseQR.onload = () => drawQR();

            function drawQR() {
                ctx.drawImage(baseQR, 0, 0, canvas.width, canvas.height);
                let imgData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                let pixels = imgData.data;

                let fg = hexToRgb(fgColorPicker.value);
                let bg = hexToRgb(bgColorPicker.value);

                for (let i = 0; i < pixels.length; i += 4) {
                    let r = pixels[i], g = pixels[i + 1], b = pixels[i + 2];
                    if (r < 128 && g < 128 && b < 128) {
                        pixels[i] = fg.r; pixels[i + 1] = fg.g; pixels[i + 2] = fg.b;
                    } else {
                        pixels[i] = bg.r; pixels[i + 1] = bg.g; pixels[i + 2] = bg.b;
                    }
                }

                ctx.putImageData(imgData, 0, 0);

                // if (logoImage) {
                //     const logoSize = canvas.width * 0.25;
                //     ctx.drawImage(logoImage, (canvas.width - logoSize) / 2, (canvas.height - logoSize) / 2, logoSize, logoSize);
                // }
            }

            fgColorPicker.addEventListener('input', drawQR);
            bgColorPicker.addEventListener('input', drawQR);

            // logoUpload.addEventListener('change', function(e) {
            //     const file = e.target.files[0];
            //     if (!file) return;
            //     const reader = new FileReader();
            //     reader.onload = function(evt) {
            //         logoImage = new Image();
            //         logoImage.onload = drawQR;
            //         logoImage.src = evt.target.result;
            //     };
            //     reader.readAsDataURL(file);
            // });

            // Save Design Functionality
            document.getElementById('saveDesign').addEventListener('click', function () {
                const dataURL = canvas.toDataURL('image/png');
                fetch('save_qr_design.php', {
                    method: 'POST',
                    body: JSON.stringify({ image: dataURL }),
                    headers: { 'Content-Type': 'application/json' }
                }).then(res => res.json()).then(data => {
                    if (data.success) {
                        Swal.fire('Success', 'QR design saved successfully!', 'success');
                    } else {
                        Swal.fire('Error', 'Failed to save QR design.', 'error');
                    }
                });
            });

            function hexToRgb(hex) {
                let bigint = parseInt(hex.replace('#', ''), 16);
                return { r: (bigint >> 16) & 255, g: (bigint >> 8) & 255, b: bigint & 255 };
            }

            document.addEventListener("DOMContentLoaded", function () {
                const form = document.getElementById("qrForm");
                const overlay = document.getElementById("spinnerOverlay");
                const statusEl = document.getElementById("statusText");
                const emoji = document.getElementById("rotatingEmoji");

                form.addEventListener("submit", function () {
                    // Show overlay
                    overlay.style.display = "flex";

                    let rotation = 0;
                    let percent = 0;

                    // Rotate emoji continuously (~60fps)
                    const rotateInterval = setInterval(() => {
                        rotation = (rotation + 6) % 360;
                        emoji.style.transform = "rotate(" + rotation + "deg)";
                    }, 16);

                    // Status update steps
                    const statusSteps = [
                        { max: 25, text: "Generating QR Code..." },
                        { max: 75, text: "Securing QR Code..." },
                        { max: 100, text: "Storing into Database..." }
                    ];

                    // Simulate quick progress (~1.5 seconds total)
                    const percentInterval = setInterval(() => {
                        percent++;

                        for (let step of statusSteps) {
                            if (percent <= step.max) {
                                statusEl.innerHTML = step.text;
                                break;
                            }
                        }

                        if (percent >= 100) {
                            clearInterval(percentInterval);
                            clearInterval(rotateInterval);
                            // No redirect here — form will naturally go to finalpreview.php
                        }
                    }, 15);
                });
            });
        });
    </script>

</body>

</html>