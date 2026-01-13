<?php
session_start();
include 'componet/conn.php';
date_default_timezone_set('Asia/Kuala_Lumpur');


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

$accessError = '';
$feedbackSuccess = '';

// Handle feedback submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['submit_feedback']) && !empty($_POST['feedback_text'])) {
        $feedbackText = trim($_POST['feedback_text']);
        $userId = $_SESSION['id'];
        $timestamp = date('Y-m-d H:i:s');

        $stmt = $con->prepare("INSERT INTO feedback (feedback_text, timestamp) VALUES (?,?)");
        $stmt->bind_param("ss", $feedbackText, $timestamp);

        if ($stmt->execute()) {
            $feedbackSuccess = "Feedback submitted successfully.";
        } else {
            $accessError = "Failed to submit feedback.";
        }
    } elseif (isset($_POST['clear_feedback'])) {
        $_POST['feedback_text'] = '';
    }
}
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


        <title>SQ-TECH SOLVER - Secure QR Code Generator</title>body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        .main-container {
            max-width: 1000px;
            margin: 40px auto;
            background: linear-gradient(90deg, rgba(255, 255, 255, 1) 35%, rgba(194, 192, 192, 1) 100%);
            border-radius: 40px;
            box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
            padding: 30px;
            border:1px solid black;
        }

        .row {
            display: flex;
            gap: 20px;
            margin-bottom: 30px;
        }

        .card-box {
            flex: 1;
            padding: 20px;
            border-radius: 30px;
            background-color: #b4b4b4ff;
            box-shadow: 0 0 4px rgba(0, 0, 0, 0.05);
            border:1px solid black;
            color: black;

        }

        .feedback-section {
            padding: 20px;
            background-color: white;
            border-top: 1px solid #ddd;
            border-radius: 30px;
            border:1px solid black;
        }

        textarea,
        button {
            width: 100%;
            padding: 10px;
            margin-top: 10px;
            font-size: 15px;
            border-radius: 6px;
            border: 1px solid #ccc;
        }

        button {
            background-color: #0c0c0cff;
            color: white;
            border: none;
            cursor: pointer;
        }

        button:hover {
            background-color: #1e40af;
        }

        .error {
            color: red;
        }

        .success {
            color: green;
        }
    </style>
</head>

<body>
              <div id="stars"></div>
          <div id="stars2"></div>
          <div id="stars3"></div>
    <?php include("componet/navbar.php"); ?>
    <h1 class="case-title-h1" style="color: white;">Feedback</h1>

    <div class="main-container">
        <div class="row">
            <div class="card-box">
                <h3 style="margin: 20px;">System Developer Comment</h3>
                <p style="margin: 20px;"> This secure QR Code Generator system provides the highest level of security
                    and reliability for generating QR codes.<br> Our platform ensures your confidentiality while
                    offering the best and most user-friendly experience. Lastly, enjoy the features! 😊 </p>
            </div>
            <div class="card-box">
                <h3 style="margin: 20px;">Developer Details</h3>
                <p style="margin: 20px;">Developed by Daniel Haikal<br>Email: danielhkl118@gmail.com<br>Version: 1.0.0
                </p>
            </div>
        </div>
        <div class="feedback-section">
            <h3 style="font-size:16px;color:black;">Submit Your Feedback</h3><br>
            <?php if (!empty($accessError))
                echo "<div class='error'>$accessError</div>"; ?>
            <?php if (!empty($feedbackSuccess))
                echo "<div class='success'>$feedbackSuccess</div>"; ?>

            <form method="post">
                <textarea name="feedback_text" placeholder="Enter your feedback here..." rows="8"
                    style=" color:black;align-items:center; justify-content:center; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size:15px; border:1px solid black;padding:10px; border-radius: 20px;"
                    required></textarea>
                <br>
                <br>
                <dev style=" display:flex;justify-content: center; align-items: center;">
                    <button type="submit" name="submit_feedback"
                        style="margin-right:20px; background:black; color: white; font-weight: bold;border-radius:40px; width:300px; align-items:center; justify-content:center; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size:16px;">Submit</button>
                    <button type="submit" name="clear_feedback"
                        style="background:black; color: white; font-weight: bold;border-radius:40px; width:300px; align-items:center; justify-content:center; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size:16px;">Clear</button>
                </dev>
            </form>
        </div>
    </div>
    <div class="footer-note">&copy; 2025 SQ‑Tech Solver. All rights reserved.</div>
    <!-- javascript sw -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
    <script src="index.js"></script>
          <script src="stars.js"></script>
    <link rel="stylesheet" href="live-stars.css">
</body>

</html>