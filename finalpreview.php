<?php include_once("componet/conn.php"); ?>
<?php session_start();
if (isset($_SESSION["login"])) {
    $name = $_SESSION["username"];
}
?>
<?php
$cid = "";
if (isset($_REQUEST['category'])) {
    $cid = $_REQUEST['category'];
    $getcat = "SELECT * FROM `product-catagory` where cid={$cid};";
    $catdata = $con->query($getcat);
    $fdata = mysqli_fetch_assoc($catdata);
    $cname = $fdata["cname"];
}
if (isset($_REQUEST['subcategory'])) {
    $cid = $_REQUEST['subcategory'];
    $getcat = "SELECT * FROM `sub-catagory` where subid={$cid};";
    $catdata = $con->query($getcat);
    $fdata = mysqli_fetch_assoc($catdata);
    $cname = $fdata["subname"];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- links -->
         <title>SQ-TECH SOLVER Secure QR Code Generator</title>
        <link rel="icon" type="image/png" href="img/log.png">
    <link rel="stylesheet" href="style-index.css">
    <link rel="stylesheet" href="style-res.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <!-- SweetAlert2 CDN -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- javascript sw-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />

    <!-- links -->
    <!-- fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Acme&family=Barlow:wght@500&family=Quicksand:wght@500&family=Raleway:wght@300&family=Ubuntu:wght@700&display=swap"
        rel="stylesheet">
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
            background: linear-gradient(135deg, #d7e8f7, #ffe5d9);
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
    <!-- fonts -->
    <title>SQ-TECH SOLVER Secure QR Code Generator</title>

         /* Step Indicator */
        .step-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 5px;
            margin-bottom: 2px;
        }

        .step {
            padding: 6px 12px;
            border-radius: 20px;
            background-color: #e9ecef;
            color: #6c757d;
            font-size: 15px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .step.active {
            background-color: #007bff;
            color: white;
            font-weight: 700;
        }

        .arrow {
            color: #000000ff;
        }
        </style>
</head>

<body class="animated-bg">



    <div class="main-body">
        <!-- page navigator -->
        <!-- <div class="page-navigator">
            <div class="bigcontainer">
                <div class="page-names">
                    <a href="index.php">Home</a>
                    <span>></span>
                    <p><?php //echo $cname ?></p>
                </div>
            </div>
        </div> -->
        <!-- page navigator -->

        <!--shopall items -->
        <section>
            <div class="bigcontainer">

                <div class="hading" style="margin-top: 50px;">
                    <h1>Final Preview</h1>
                    <div class="step-wrapper">
                    <div class="step">Upload</div>
                    <div class="arrow">→</div>
                    <div class="step">Generate</div>
                    <div class="arrow">→</div>
                    <div class="step">Design</div>
                    <div class="arrow">→</div>
                    <div class="step">Securing</div>
                    <div class="arrow">→</div>
                    <div class="step active">Complete</div>
                </div>
                </div>




            </div>
        </section>


        <section style="; padding: 60px 20px;">
            <div class="bigcontainer"
                style="max-width: 600px; margin: auto; background: linear-gradient(135deg, #8ee3ef, #ffd4bf); border-radius: 40px; padding: 40px 20px; box-shadow: 0 70px 110px rgba(2, 0, 134, 0.2); text-align: center;">

                <!-- QR Code Preview -->
                <!-- <h2 style="color:white; margin-bottom: 20px;">Final QR Code Preview</h2> -->
                <div
                    style="width: 500px; height: 500px; margin: 0 auto 30px auto; border: 5px dashed #006e37ff; border-radius: 10px; display: flex; align-items: center; justify-content: center; background: #f3f4f6;">
                    <?php
                    if (isset($_SESSION['secured_qr_filename'])) {
                        $qrFilename = $_SESSION['secured_qr_filename'];

                        // Get the QR image blob from the database
                        $stmt = $con->prepare("SELECT qr_image FROM qr_security WHERE qr_filename = ?");
                        $stmt->bind_param("s", $qrFilename);
                        $stmt->execute();
                        $result = $stmt->get_result();

                        if ($row = $result->fetch_assoc()) {
                            $qrBlob = $row['qr_image'];

                            // Output as JPEG base64 image
                            $base64Image = base64_encode($qrBlob);
                            echo '<img src="data:image/jpeg;base64,' . $base64Image . '" alt="QR Code" style="width: 100%; height: 100%;">';
                        } else {
                            echo '<span style="color: red;">QR image not found in database.</span>';
                        }

                        $stmt->close();
                    } else {
                        echo '<span style="color: red;">No secured QR session found.</span>';
                    }
                    ?>

                </div>
                    <!-- Spinner Overlay -->
                <div id="spinnerOverlay" style="position:fixed;top:0;left:0;width:100%;height:100%;background-color:white;display:none;align-items:center;justify-content:center;flex-direction:column;z-index:9999;">
                    <div style="text-align:center; margin-bottom: 20px;">
                        <div id="rotatingEmoji" style="font-size:80px; user-select:none;">🔲</div>
                        <div id="statusText" style="font-size:22px; color:#333; font-family:Arial,sans-serif; margin-top: 15px;">Finishing up...</div>
                    </div>
                </div>

                <!-- Buttons -->
                <div style="margin-top: 30px;">

                    <button id="finishBtn" type="button"
                        style="font-size: 15px; font-weight: bold; width:350px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 12px 24px; margin: 10px; background-color: #021d91ff; color: white; border: none; border-radius: 40px; cursor: pointer;">
                        Finish
                    </button>
                </div>
            </div>
        </section>
        <div class="footer-note">&copy; 2025 SQ‑Tech Solver. All rights reserved.</div>


    </div>




                    <script>
document.getElementById("finishBtn").addEventListener("click", function () {
    var overlay = document.getElementById("spinnerOverlay");
    overlay.style.display = "flex";

    var statusEl = document.getElementById("statusText");
    var emoji = document.getElementById("rotatingEmoji");

    var percent = 0;
    var rotation = 0;

    // Rotate emoji continuously (~60fps)
    var rotateInterval = setInterval(function () {
        rotation = (rotation + 6) % 360;
        emoji.style.transform = "rotate(" + rotation + "deg)";
    }, 16);

    // Update status messages quickly
    var statusIntervals = [
        { max: 25, text: "Saving your QR Code..." },
        { max: 75, text: "Finalizing..." },
        { max: 100, text: "Redirecting to homepage..." }
    ];

    var percentInterval = setInterval(function () {
        percent++;
        for (var i = 0; i < statusIntervals.length; i++) {
            if (percent <= statusIntervals[i].max) {
                statusEl.innerHTML = statusIntervals[i].text;
                break;
            }
        }

        if (percent >= 100) {
            clearInterval(percentInterval);
            clearInterval(rotateInterval);
            setTimeout(function () {
                window.location.href = "home.php";
            }, 100);
        }
    }, 20); // 1.5s total
});
</script>



    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
    <script src="index.js"></script>
</body>

</html>