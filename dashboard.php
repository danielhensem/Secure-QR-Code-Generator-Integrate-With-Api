<?php include_once("componet/conn.php"); ?>
<?php
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
            background: #f5f7fa;
            margin: 0;
            padding: 0;
        }

        /* ==== Slider Section ==== */
        .slider-container {
            display: flex;
            align-items: stretch;
            max-width: 1000px;
            margin: 40px auto;
            background: #fff;
            border-radius: 40px;
            box-shadow: 0 50px 90px rgba(16, 130, 165, 0.15);
            overflow: hidden;
        }

        /* Left description area */
        .slider-description {
            flex: 1;
            padding: 30px;
            background: linear-gradient(145deg, #0469b6ff, #ffffff);
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .slider-description h3 {
            margin: 0 0 15px;
            font-size: 22px;
            color: #1a1a1a;
        }

        .slider-description p {
            font-size: 15px;
            color: #444;
            line-height: 1.6;
        }

        /* Right video area */
        .slider-video {
            flex: 1;
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .slider-slide {
            display: none;
            width: 100%;
        }

        .slider-slide.active {
            display: block;
        }

        .slider-slide iframe {
            width: 100%;
            height: 300px;
            border: none;
            border-radius: 0;
        }

        /* Arrows */
        .slider-arrow {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            font-size: 30px;
            background-color: rgba(0, 0, 0, 0.4);
            color: white;
            border: none;
            cursor: pointer;
            padding: 8px 14px;
            border-radius: 50%;
            z-index: 10;
        }

        .slider-arrow.left {
            left: -45px;
        }

        .slider-arrow.right {
            right: -45px;
        }

        /* ==== Card Section ==== */
        .case-section {
            padding: 10px;
        }

        .case-title {
            text-align: center;
            font-size: 26px;
            color: white;
            margin-top: 20px;
            margin-bottom: 35px;
            font-weight: 700;
            letter-spacing: 0.5px;
        }

        .case-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 22px;
            max-width: 1400px;
            margin: auto;
        }

        .case-card {
             background: #424141ff;
            background: linear-gradient(90deg, rgba(255, 255, 255, 1) 35%, rgba(194, 192, 192, 1) 100%);
            border-radius: 40px;
            box-shadow: 0 70px 100px rgba(0, 0, 0, 0.08);
            padding: 18px;
            text-decoration: none;
            color: inherit;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            transition: all 0.3s ease;
            min-height: 260px;
            border:1px solid black;
        }

        .case-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        }

        .case-icon {
            width: 70px;
            height: 70px;
            background: black;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin-bottom: 12px;
        }

        .case-icon i {
            font-size: 34px;
            color: white;
        }

        .case-link-text {
            font-size: 15px;
            font-weight: 600;
            color: #222;
            margin-bottom: 8px;
        }

        .case-description {
            font-size: 13px;
            color: #444;
            line-height: 1.4;
        }

        .footer-note {
            text-align: center;
            margin-top: 40px;
            font-size: 13px;
            color: #777;
        }

        @media (max-width: 768px) {
            .slider-container {
                flex-direction: column;
            }

            .slider-arrow.left {
                left: 10px;
            }

            .slider-arrow.right {
                right: 10px;
            }
        }

        .slider-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }

        .slider-container {
            display: flex;
            align-items: center;
            background: #ffffff;
            border-radius: 40px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 900px;
            padding: 20px;
            flex-wrap: wrap;
            gap: 20px;
            position: relative;
            border: 1px solid black;
        }

        .slider-arrow {
            background: transparent;
            border: none;
            color: #333;
            font-size: 28px;
            padding: 10px 16px;
            border-radius: 50%;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            z-index: 10;
        }

        .slider-arrow:hover {
            background: transparent;
            transform: scale(1.1);
        }

        .slider-arrow.left {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
        }

        .slider-arrow.right {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
        }

        .slider-description {
            flex: 1 1 100%;
            background: white;
            padding: 20px;
            border-radius: 20px;
        }

        .slider-description h3 {
            margin-top: 0;
            font-size: 16px;
        }

        .slider-description p {
            font-size: 14px;
        }

        .slider-video {
            flex: 1 1 100%;
            display: flex;
            justify-content: center;
            position: relative;
            margin-left: 50px;
            margin-right: 50px;
        }

        .slider-slide {
            display: none;
            width: 100%;
        }

        .slider-slide.active {
            display: block;
        }

        .slider-video iframe {
            width: 100%;
            height: 300px;
            border: none;
            border-radius: 40px;
        }

        @media (min-width: 768px) {

            .slider-description,
            .slider-video {
                flex: 1 1 45%;
            }
        }
    </style>
</head>

<body>
        <div id="stars"></div>
    <div id="stars2"></div>
    <div id="stars3"></div>
    <?php include("componet/navbar.php"); ?>

    <h2 class="case-title" style="margin-left:10px; margin-right:10px;">QR Code Scam, Fraud & Tampering Cases</h2>

    <div style=" padding:10px; margin-left:30px;  margin-right: 10px;">
        <small
            style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;  margin-top:20px; font-size: 16px; color:white; white-space: normal; word-break: break-word; justify-content: left; align-items: left; text-align: justify;">
            <strong>Stay Smart. Scan Safe.</strong>
            QR codes make life easier from payments to event check-ins but they can also be a hacker’s hidden doorway.
            In recent years, cybercriminals have turned simple QR scans into powerful tools for data theft, phishing,
            and financial fraud. Many victims never realized they were scanning fake codes until their money or personal
            information was gone.
            You can educate yourself by watching the video about how you can fallen victim in fraud cases.
            The video also bring the awareness to the society and securing them from any cyber attack.
        </small>
    </div>

    <!-- ==== Video Slider with Descriptions ==== -->
    <div class="slider-wrapper">
        <div class="slider-container">
            <!-- Arrow Buttons -->
            <button class="slider-arrow left" id="prevBtn">&#10094;</button>

            <!-- Description -->
            <div class="slider-description" id="sliderDescription">
                <h3>QR Code Scam #1</h3>
                <p>This video discusses 10 shocking QR code scams. Stay alert and informed to avoid being tricked by
                    cybercriminals.</p>
            </div>

            <!-- Video Area -->
            <div class="slider-video">
                <div class="slider-slide active">
                    <iframe src="https://www.youtube.com/embed/2KfnlZWrAMw?enablejsapi=1" allowfullscreen></iframe>
                </div>
                <div class="slider-slide">
                    <iframe src="https://www.youtube.com/embed/PbqsOYSpyMw?enablejsapi=1" allowfullscreen></iframe>
                </div>
                <div class="slider-slide">
                    <iframe src="https://www.youtube.com/embed/2m5aqoZpLRg?enablejsapi=1" allowfullscreen></iframe>
                </div>
            </div>

            <!-- Arrow Buttons -->
            <button class="slider-arrow right" id="nextBtn">&#10095;</button>
        </div>
    </div>

    <!-- ==== Scam Cases Grid ==== -->
    <section class="case-section">
        <div style="margin-left:30px;  margin-right: 10px; margin-bottom:35px;">
            <small
                style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-weight: normal; margin-top:20px; font-size: 16px; color:white; white-space: normal; word-break: break-word; align-items: left; text-align: justify;">Stay
                aware about Real Scams. Real Lessons.
                Click here to uncover shocking QR fraud stories and learn how to stay one step ahead of scammers.You can
                choose and view articles related to Qr Code Fraud Cases below ; </small>
        </div>
        <div class="case-grid" style="margin-left:50px; margin-right: 50px ;">
            <?php
            $cases = [
                ["url" => "https://timesofindia.indiatimes.com/city/kochi/bail-pleas-rejected-in-qr-code-scam-case/articleshow/122924682.cms", "title" => "Kochi ₹69 lakh QR scam", "icon" => "fa-qrcode", "desc" => "Fraudsters tricked shop owners by scanning fake QR codes to siphon ₹69 lakh from multiple merchants."],
                ["url" => "https://timesofindia.indiatimes.com/city/mumbai/print-paste-con-puts-his-qr-at-shops-diverts-payments-to-self/articleshow/122769942.cms", "title" => "Mumbai QR swap fraud", "icon" => "fa-shop", "desc" => "Scammer replaced shop QR codes with his own, diverting payments into his bank account without detection."],
                ["url" => "https://www.thebureauinvestigates.com/stories/2025-06-27/quishing-new-qr-code-scam-sweeps-uk-car-parks", "title" => "UK QR scam in car parks", "icon" => "fa-car", "desc" => "Fake parking QR codes placed on machines steal driver payment details and lead to phishing websites."],
                ["url" => "https://www.theguardian.com/money/2025/may/25/qr-code-scam-what-is-quishing-drivers-app-phone-parking-payment", "title" => "QR parking fraud £13k", "icon" => "fa-money-bill-wave", "desc" => "Parking scam across the UK resulted in £13,000 stolen from unsuspecting motorists who scanned fake codes."],
                ["url" => "https://www.wired.com/story/russia-signal-qr-code-phishing-attack", "title" => "Signal QR phishing (Russia)", "icon" => "fa-shield-halved", "desc" => "Hackers used fake Signal QR login pages to hijack user accounts and intercept messages."],
                ["url" => "https://www.pymnts.com/cybersecurity/2025/quishing-scams-grow-in-tandem-with-use-of-qr-codes/", "title" => "Quishing scam surge", "icon" => "fa-virus", "desc" => "Massive rise in phishing attacks using QR codes to capture sensitive personal and banking information."],
                ["url" => "https://www.thescottishsun.co.uk/motors/14902971/urgent-warning-scots-drivers-fake-parking-scam-steals-cash/", "title" => "Scotland QR meter scam", "icon" => "fa-triangle-exclamation", "desc" => "Fake QR stickers on parking meters in Scotland trick drivers into entering card details online."],
                ["url" => "https://www.abc15.com/news/crime/scammers-replacing-qr-codes-on-restaurants-to-steal-credit-card-info", "title" => "Arizona: restaurant QR theft", "icon" => "fa-utensils", "desc" => "QR menu stickers replaced in restaurants to capture diners’ credit card information when ordering."],
                ["url" => "https://www.news18.com/crime/scammer-dupes-man-of-rs-99000-after-sending-qr-code-on-whatsapp-9020835.html", "title" => "India WhatsApp QR scam ₹99k", "icon" => "fa-mobile-screen", "desc" => "Victim scanned a QR sent via WhatsApp, resulting in an instant loss of ₹99,000 from his account."],
                ["url" => "https://www.nbcnews.com/news/us-news/police-warn-qr-code-scams-fake-payment-links-rcna76372", "title" => "US: fake QR in public places", "icon" => "fa-city", "desc" => "Public QR codes replaced in US cities redirect users to fraudulent payment sites or phishing portals."]
            ];

            foreach ($cases as $case) {
                echo '<a href="' . htmlspecialchars($case['url']) . '" class="case-card" target="_blank">';
                echo '<div class="case-icon"><i class="fas ' . htmlspecialchars($case['icon']) . '"></i></div>';
                echo '<div class="case-link-text">' . htmlspecialchars($case['title']) . '</div>';
                echo '<div class="case-description">' . htmlspecialchars($case['desc']) . '</div>';
                echo '</a>';
            }
            ?>
        </div>
        <div class="footer-note">&copy; 2025 SQ-Tech Solver. All rights reserved.</div>
    </section>

    <script>
        const descriptions = [
            {
                title: "How to spot a fake QR code (and stop getting scammed)",
                text: "QR codes are everywhere and we often scan them without thinking. But that little square barcode could be hiding something much more than a restaurant menu. Here are 8 tips for spotting and avoiding malicious QR codes."
            },
            {
                title: "Recent Global QR Scam Trends",
                text: "A deep dive into recent QR code scam cases worldwide, revealing the techniques used and the impact on victims from different industries."
            },
            {
                title: "Protect Yourself from Quishing",
                text: "Learn how to identify and avoid QR code phishing attacks ('quishing') and protect your financial and personal information."
            }
        ];

        let currentIndex = 0;
        const slides = document.querySelectorAll('.slider-slide');
        const descriptionBox = document.getElementById('sliderDescription');

        function showSlide(index) {
            slides[currentIndex].classList.remove('active');
            currentIndex = (index + slides.length) % slides.length;
            slides[currentIndex].classList.add('active');
            descriptionBox.querySelector('h3').textContent = descriptions[currentIndex].title;
            descriptionBox.querySelector('p').textContent = descriptions[currentIndex].text;
        }

        document.getElementById('prevBtn').addEventListener('click', () => showSlide(currentIndex - 1));
        document.getElementById('nextBtn').addEventListener('click', () => showSlide(currentIndex + 1));

        showSlide(0); // Initialize
    </script>

    <!-- javascript sw -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
    <script src="index.js"></script>
    <script src="stars.js"></script>
    <link rel="stylesheet" href="live-stars.css">
</body>

</html>