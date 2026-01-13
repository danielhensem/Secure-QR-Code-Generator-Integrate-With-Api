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
    <link rel="icon" type="image/png" href="img/log.svg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Custom Animated Background -->
    <style>
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
            color: #ffffffff;
            font-weight: bold;
        }
    </style>
</head>

<body>
    <div id="stars"></div>
    <div id="stars2"></div>
    <div id="stars3"></div>
    <div class="main-container">
        <div class="generator-header">
            <h1 style="font-weight: bold; color:white;">Design & Secure</h1>
            <div class="step-wrapper">
                <div class="step active">Design</div>
                <div class="arrow">→</div>
                <div class="step active">Securing</div>
                <div class="arrow">→</div>
                <div class="step">Complete</div>
            </div>
        </div>
        <div class="flex-row">


            <!-- Left Column: QR design + save + password -->
            <div class="left-col"
                style=" border:1px solid black; background: linear-gradient(90deg, rgba(255, 255, 255, 1) 35%, rgba(194, 192, 192, 1) 100%);">
                <strong style="color:black;">Custom QR Code Design (Optional)</strong>
                <p style="color: gray;">Customize your QR code with different colors before securing it.</p>
                <hr>
                <div class="qr-design-tool">
                    <div class="qr-controls">
                        <p style=" font-size:13px; color: black;">Alert: The front color of the QR code should be darker
                            than the
                            background so it can be scanned quickly.</p>
                        <p style=" font-size:13px; color:red; font-weight:bold;">Avoid:<br> - Light-colored dots on a
                            dark background (for example, yellow
                            dots on black).<br> - Bright color blends that make the code hard to see.<br>
                            - See-through or busy backgrounds that hide the edges of the QR code.</p>
                        <label for="fgColor">1.Foreground Color:</label>
                        <input type="color" id="fgColor" value="#000000">

                        <label for="bgColor">2.Background Color:</label>
                        <input type="color" id="bgColor" value="#ffffff">

                        <!-- <label for="logoUpload">Upload Logo (optional):</label>
                    <input type="file" id="logoUpload" accept="image/*"> -->

                        <!-- Save Design Button -->

                        <p style="color: gray; font-weight: bold;">Click Save to keep your QR code changes.</p>
                        <button id="saveDesign" type="button" class="btn-submit"
                            style="display:flex ; border-radius:20px; align-items:center; justify-content:center; width:30%; height:30px;margin-bottom:15px;">
                            Save
                        </button>

                    </div>
                    <br>
                    <!-- Password Form -->
                    <form id="qrForm" action="finalreview.php" method="POST">
                        <div class="form-group">
                            <label for="header_text" style="font-weight:bold;color: black;">3. Title
                                (Required)</label><br>
                            <input type="text" id="header_text" name="header_text" style="color:black;"
                                placeholder="Enter header text" required><br>
                            <label for="description" style="font-weight: bold; color: black;">4. Description
                                (Optional)</label><br>
                            <textarea id="description" name="description" rows="4" maxlength="800"
                                placeholder="Add a short description or notes related to this QR code"
                                style="color:black;width:100%;padding:8px;border-radius:6px;margin-top:6px;"></textarea>

                            <strong style="color: black;">5. Password (Optional)</strong><br>

                            <input type="password" id="password" name="password" placeholder="Enter password (optional)"
                                style="color: black; border-radius: 30px; margin: 0;"><br>

                            <div id="passwordStrength"
                                style="color:black; font-size:13px; font-weight:bold; margin-top:4px;">
                                Password strength: <span id="strengthText" style="color:red;">Weak</span>
                            </div>

                            <div id="passwordHint" style="font-size:12px; color:#444; margin-top:2px;">
                                Use uppercase, lowercase, number, symbol, and at least 8 characters.
                            </div>
                            <script>
                                const passwordInput = document.getElementById("password");
                                const strengthText = document.getElementById("strengthText");

                                passwordInput.addEventListener("input", function () {
                                    const val = passwordInput.value;
                                    let score = 0;

                                    if (val.length >= 8) score++;
                                    if (/[A-Z]/.test(val)) score++;
                                    if (/[a-z]/.test(val)) score++;
                                    if (/[0-9]/.test(val)) score++;
                                    if (/[^A-Za-z0-9]/.test(val)) score++;

                                    if (val.length === 0) {
                                        strengthText.textContent = "Optional";
                                        strengthText.style.color = "gray";
                                        return;
                                    }

                                    if (score <= 2) {
                                        strengthText.textContent = "Weak";
                                        strengthText.style.color = "red";
                                    } else if (score === 3 || score === 4) {
                                        strengthText.textContent = "Medium";
                                        strengthText.style.color = "orange";
                                    } else {
                                        strengthText.textContent = "Strong";
                                        strengthText.style.color = "green";
                                    }
                                });
                            </script>

                        </div>
                        <br>
                        <div class="form-group">
                            <strong style="color:black;">6. Access Permissions (Choose one of them)</strong><br><br>

                            <div style="
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    justify-content: flex-start;
    gap: 8px; /* Space between the two rows */
">

                                <label style="
        color: black;
        display: flex;
        align-items: center; /* Changed from flex-start to center */
        gap: 5px; /* Control the exact distance here */
        cursor: pointer;
    ">
                                    <input type="radio" name="permission" value="1" checked style="margin: 0;">
                                    Allow user view the QR Code's content only (simple website and single page).
                                </label>

                                <label style="
        color: black;
        display: flex;
        align-items: center; /* Changed from flex-start to center */
        gap: 5px; /* Control the exact distance here */
        cursor: pointer;
    ">
                                    <input type="radio" name="permission" value="2" style="margin: 0;">
                                    Allow user to manage QR Code's content (Recommended for complex website).
                                </label>

                            </div>
                        </div>


                        <br>
                        <button type="submit"
                            style="display: flex; border-radius:20px; align-items: center; justify-content: center; width: 50%; min-width: 70%; min-height: auto;height: 30px;"
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

                            const header = document.getElementById("header_text").value.trim();
                            const desc = document.getElementById("description").value.trim();
                            if (header.length > 120) {
                                alert("Header is too long (max 120 chars).");
                                event.preventDefault();
                                return;
                            }
                            if (desc.length > 800) {
                                alert("Description is too long (max 800 chars).");
                                event.preventDefault();
                                return;
                            }
                        });
                    </script>

                </div>
            </div>

            <!-- Right Column: Live QR preview -->
            <div class="right-col"
                style="border:1px solid black; background: linear-gradient(90deg, rgba(255, 255, 255, 1) 35%, rgba(194, 192, 192, 1) 100%); justify-content:center; align-items: center;">
                <strong style="color:black;">Live QR Preview</strong>
                <br><br><br>
                <div class="qr-preview-container"
                    style=" display:flex;top:20px; align-items:center; justify-content: center;">
                    <canvas id="qrCanvas" display="flex" width="300" min-width="auto" min-height="auto" height="300"
                        align-items="center" justify-content="center"></canvas>
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
    <script src="stars.js"></script>
    <link rel="stylesheet" href="live-stars.css">
</body>

</html>