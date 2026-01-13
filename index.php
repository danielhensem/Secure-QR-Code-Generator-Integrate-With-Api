<?php
// --- Original PHP Code (Unchanged) ---
include_once("componet/conn.php");
session_start();
if (isset($_SESSION["login"])) {
    $name = $_SESSION["username"];
}
//8.1.6
//7.2.31
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SQ-TECH SOLVER - Secure QR Code Generator</title>
    <link rel="icon" type="image/png" href="img/log.png">
    <link rel="stylesheet" href="footer.css">
    <link rel="stylesheet" href="index.css">
    <link rel="stylesheet" href="hero-canvas.css">


    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:wght@700&family=Quicksand:wght@400;500;700&display=swap"
        rel="stylesheet">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
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

        /* --- Global Styles & Variables --- */
        :root {
            --primary-color: #007bff;
            --secondary-color: white;
            --background-color: #f8f9fa;
            --text-color: #333;
            --light-text-color: #fff;
            --card-bg: #ffffff;
            --border-radius: 8px;
            --box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Quicksand', sans-serif;
            background-color: var(--background-color);
            color: var(--text-color);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        h1,
        h2,
        h3 {
            font-family: 'Ubuntu', sans-serif;
            color: var(--secondary-color);
        }

        section {
            padding: 80px 0;
        }

        .section-header {
            text-align: center;
            margin-bottom: 25px;
        }

        .section-header h1 {
            margin-top: 30px;
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .section-header hr {
            width: 80px;
            height: 4px;
            background-color: var(--primary-color);
            border: none;
            margin: 0 auto;
        }

        /* --- Header / Navigation Bar --- */
        .main-header {
            display: flex;
            border-radius: 50px;
              background: rgba(255, 255, 255, 0.12);

  backdrop-filter: blur(22px) saturate(140%);
  -webkit-backdrop-filter: blur(22px) saturate(140%);
   border: 1px solid rgba(255, 255, 255, 0.35);

  box-shadow:
    0 12px 30px rgba(0, 0, 0, 0.12),
    inset 0 1px 1px rgba(255, 255, 255, 0.45);

  transition: backdrop-filter 0.3s ease, box-shadow 0.3s ease;
            padding: 5px 20px;
            justify-content: center;
            position: fixed;
            align-items: center;
            text-align: center;
            width: 90%;
            top: 30px;
            left: 50%;
            transform: translateX(-50%);
            /* width: 100%; */
            z-index: 1000;

        }

        /* .main-header {
  position: fixed;
  top: 10px;
  left: 50%;
  transform: translateX(-50%);

  border-radius: 50px;
  background-color: var(--card-bg);
  padding: 5px 20px;

  display: flex;
  justify-content: center;
  align-items: center;

  z-index: 1000;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
} */


        .main-header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-family: 'Ubuntu', sans-serif;
            font-size: 1.8rem;
            color: var(--secondary-color);
            text-decoration: none;
            font-weight: 700;
        }

        .logo i {
            color: var(--primary-color);
        }

        .main-nav a {
            color: white;
            text-decoration: none;
            margin-left: 25px;
            font-weight: 700;
            transition: color 0.3s ease;
        }

        .main-nav a:hover,
        .main-nav a.btn:hover {
            color: var(--primary-color);
        }

        .btn {
            background-color: var(--primary-color);
            color: var(--light-text-color);
            padding: 10px 20px;
            border-radius: 30px;
            text-decoration: none;
            transition: background-color 0.3s, transform 0.2s;
        }

        .btn:hover {
            background-color: #0056b3;
            transform: translateY(-2px);
            color: var(--light-text-color);
        }

        /* --- Hero Section (Swiper) --- */
        .hero-section {
            position: relative;
            height: 100vh;
            color: var(--light-text-color);
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
        }

        .hero-swiper {
            position: absolute;
            width: 100%;
            height: 100%;
            top: 0;
            left: 0;
            z-index: 1;
        }

        .hero-swiper .swiper-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            filter: brightness(0.5);
            /* Darken image */
        }

        .hero-text {
            position: relative;
            z-index: 2;
            max-width: 800px;
        }

        .hero-text span {
            display: block;
            font-size: 1.2rem;
            font-weight: 700;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .hero-text h1 {
            font-size: 4rem;
            margin: 10px 0 20px;
            color: var(--light-text-color);
        }

        .hero-text p {
            font-size: 1.2rem;
            margin-bottom: 30px;
        }

        /* --- Products / Features Section --- */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 30px;
            margin-bottom: 50px;
        }

        .product-card {
            background-color: var(--card-bg);
            border-radius: 30px;
            box-shadow: var(--box-shadow);
            overflow: hidden;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .product-card .card-img img {
            width: 100%;
            height: 200px;
            object-fit: cover;
        }

        .product-card .card-text {
            padding: 30px;
        }

        .product-card h3 {
            font-size: 1.5rem;
            margin-bottom: 0;
        }

        /* --- "Why Us" & "About Us" Section --- */
        .content-split {
            display: flex;
            align-items: center;
            gap: 50px;
        }

        .content-split .text-content,
        .content-split .image-content {
            flex: 1;
        }

        .content-split .image-content img {
            width: 100%;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
        }

        .content-split .text-content h2 {
            font-size: 2.2rem;
            margin-bottom: 20px;
        }

        .content-split .text-content span {
            display: block;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .content-split .text-content p {
            margin-bottom: 15px;
        }

        .review-wrapper {
            overflow: hidden;
            width: 100%;
            position: relative;
            padding: 20px 0;
        }

        /* Static grid (no animation here) */
        .review-grid {
            display: flex;
            gap: 30px;
            overflow-x: hidden;
            scroll-behavior: smooth;
        }

        /* Review cards that will move */
        .review-card {
            background: var(--card-bg);
            border-left: 5px solid var(--primary-color);
            padding: 25px;
            border-radius: 30px;
            box-shadow: var(--box-shadow);
            transition: transform 0.3s ease;
            width: 320px;
            flex: 0 0 auto;
        }

        .review-card:hover {
            transform: scale(1.05);
        }

        .review-card h2 {
            font-size: 1.4rem;
            margin-bottom: 10px;
            color: #222;
        }

        .review-card span {
            font-size: 1rem;
            color: #666;
            line-height: 1.6;
        }

        /* --- Auto Scroll Animation --- */
        @keyframes autoScroll {
            0% {
                transform: translateX(0);
            }

            100% {
                transform: translateX(-50%);
            }
        }

        /* --- Responsive Design --- */
        @media (max-width: 768px) {
            .main-nav {
                display: none;
                /* Simple hiding for demo. A real site would use a hamburger menu. */
            }

            .hero-text h1 {
                font-size: 2.5rem;
            }

            .content-split {
                flex-direction: column;
            }

            section {
                padding: 50px 0;
            }
        }

        <style>

        /* logo image + text */
        .logo {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: inherit;
        }

        .logo-img {
            width: 36px;
            height: 36px;
            object-fit: contain;
            display: inline-block;
            vertical-align: middle;
            border-radius: 6px;
            /* optional */
        }

        .logo-text {
            font-family: 'Ubuntu', sans-serif;
            font-weight: 700;
            font-size: 17px;
            color: var(--secondary-color);
            line-height: 1;
        }

        /* hide text on small screens if needed */
        @media (max-width: 600px) {
            .logo-text {
                display: none;
            }

            .logo-img {
                display: flex;
                align-items: center;
                text-align: center;
                justify-content: center;
                width: 30px;
                height: 30px;
            }
        }
    </style>
    </style>
</head>

<body class="animated-bg">

    <header class="main-header">
        <div class="container">
            <a href="#" class="logo">
                <img src="img/log.svg" alt="SQ-Tech Solver" class="logo-img" style="width: 55px; height: auto;">
                <span class="logo-text"
                    style="font-size: 20px; font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;">SQ-Tech
                    Solver</span>
            </a>
            <nav class="main-nav">
                <a href="#products">Our Product</a>
                <a href="#why-us">Why Us</a>
                <a href="#reviews">Reviews</a>
                <a href="#about">About Us</a>
                <?php if (isset($_SESSION["login"])): ?>
                    <a href="#"><i class="fa-solid fa-user"></i> Welcome, <?php echo htmlspecialchars($name); ?></a>
                <?php else: ?>
                    <a href="login.php" class="btn">Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main>

        <div id="app">
            <canvas id="canvas"></canvas>

            <section class="hero-section">
                <div class="hero-text" style="text-align: center;">
                    <span>SQ-TECH SOLVER</span>
                    <h1>Secure QR Code Generator System</h1>
                    <p>Your trusted partner for creating safe and reliable QR codes on online platform.</p>
                    <a href="login.php" class="btn" style="text-align:center;align-items:center;justify-content:center;">Get Started</a>
                </div>
            </section>
        </div>


        <section id="products" class="container">
            <div class="section-header">
                <h1 style="color: white;">Our Product</h1>

            </div>
            <div class="product-grid">
                <div class="product-card" style="background: rgba(255, 255, 255, 0.12);backdrop-filter: blur(22px) saturate(140%);-webkit-backdrop-filter: blur(22px) saturate(140%); margin:20px;border:1px solid; border-radius:40px;  border: 1px solid rgba(255, 255, 255, 0.35);

  box-shadow:
    0 12px 30px rgba(0, 0, 0, 0.12),
    inset 0 1px 1px rgba(255, 255, 255, 0.45);

  transition: backdrop-filter 0.3s ease, box-shadow 0.3s ease;">
                    <div class="card-img">
                        <img src="img/abas.svg" alt="Generate QR Code">
                    </div>
                    <div class="card-text">
                        <h3 style="color: white;">Generate Secure QR Code</h3>
                        <small style="color: white;">Enjoy highest protection of qr code content in this system integrated with strong
                            encryption algorithm and industry logic.</small>
                    </div>
                </div>
                <div class="product-card" style="background: rgba(255, 255, 255, 0.12);backdrop-filter: blur(22px) saturate(140%);-webkit-backdrop-filter: blur(22px) saturate(140%); margin:20px;border:1px solid; border-radius:40px;  border: 1px solid rgba(255, 255, 255, 0.35);

  box-shadow:
    0 12px 30px rgba(0, 0, 0, 0.12),
    inset 0 1px 1px rgba(255, 255, 255, 0.45);

  transition: backdrop-filter 0.3s ease, box-shadow 0.3s ease;">
                    <div class="card-img">
                        <img src="img/abas2.svg" alt="Scan QR Code">
                    </div>
                    <div class="card-text">
                        <h3 style="color: white;">Scan and Verify QR Code</h3>
                        <small style="color: white;">Enjoy scanning QR Code which has been assured with content verification process through
                            malware scanner API. </small>

                    </div>
                </div>
                <div class="product-card" style="background: rgba(255, 255, 255, 0.12);backdrop-filter: blur(22px) saturate(140%);-webkit-backdrop-filter: blur(22px) saturate(140%); margin:20px;border:1px solid; border-radius:40px;  border: 1px solid rgba(255, 255, 255, 0.35);

  box-shadow:
    0 12px 30px rgba(0, 0, 0, 0.12),
    inset 0 1px 1px rgba(255, 255, 255, 0.45);

  transition: backdrop-filter 0.3s ease, box-shadow 0.3s ease;">
                    <div class="card-img">
                        <img src="img/abas3.svg" alt="Share QR Code">
                    </div>
                    <div class="card-text">
                        <h3 style="color: white;">Share With Friends Secretly, or Public</h3>
                        <small style="color: white;">Enjoy sharing QR Code with your friend within the system or sharing through email.
                        </small>
                    </div>
                </div>

                <div class="product-card" style="background: rgba(255, 255, 255, 0.12);backdrop-filter: blur(22px) saturate(140%);-webkit-backdrop-filter: blur(22px) saturate(140%); margin:20px;border:1px solid; border-radius:40px;  border: 1px solid rgba(255, 255, 255, 0.35);

  box-shadow:
    0 12px 30px rgba(0, 0, 0, 0.12),
    inset 0 1px 1px rgba(255, 255, 255, 0.45);

  transition: backdrop-filter 0.3s ease, box-shadow 0.3s ease;">
                    <div class="card-img">
                        <img src="img/4.svg" alt="Share QR Code">
                    </div>
                    <div class="card-text">
                        <h3 style="color: white;">Manage QR Code Systematically</h3>
                        <small style="color: white;">Enjoy managing your own secure qr code with multiple features includes view, download,
                            share and others.
                        </small>
                    </div>
                </div>

                <div class="product-card" style="background: rgba(255, 255, 255, 0.12);backdrop-filter: blur(22px) saturate(140%);-webkit-backdrop-filter: blur(22px) saturate(140%); margin:20px;border:1px solid; border-radius:40px;  border: 1px solid rgba(255, 255, 255, 0.35);

  box-shadow:
    0 12px 30px rgba(0, 0, 0, 0.12),
    inset 0 1px 1px rgba(255, 255, 255, 0.45);

  transition: backdrop-filter 0.3s ease, box-shadow 0.3s ease;">
                    <div class="card-img">
                        <img src="img/5.svg" alt="Share QR Code">
                    </div>
                    <div class="card-text">
                        <h3 style="color: white;">Analyze the trends in real time and make better desicion</h3>
                        <small style="color: white;">Enjoy analyzing how many people has scanned your qr code and change your desicion
                            following the trends.
                        </small>
                    </div>
                </div>

                <div class="product-card" style="background: rgba(255, 255, 255, 0.12);backdrop-filter: blur(22px) saturate(140%);-webkit-backdrop-filter: blur(22px) saturate(140%); margin:20px;border:1px solid; border-radius:40px;  border: 1px solid rgba(255, 255, 255, 0.35);

  box-shadow:
    0 12px 30px rgba(0, 0, 0, 0.12),
    inset 0 1px 1px rgba(255, 255, 255, 0.45);

  transition: backdrop-filter 0.3s ease, box-shadow 0.3s ease;">
                    <div class="card-img">
                        <img src="img/6.svg" alt="Share QR Code">
                    </div>
                    <div class="card-text" >
                        <h3 style="color: white;">Educate yourself with knowledge and awareness</h3>
                        <small style="color: white;">Enjoy watching the video and article related to qr code fraud cases which can help you to
                            identify which one is scam or truth.
                        </small>
                    </div>
                </div>
            </div>
        </section>

        <section id="why-us" style="background: rgba(255, 255, 255, 0.12);backdrop-filter: blur(22px) saturate(140%);-webkit-backdrop-filter: blur(22px) saturate(140%); margin:20px;border:1px solid; border-radius:40px;  border: 1px solid rgba(255, 255, 255, 0.35);

  box-shadow:
    0 12px 30px rgba(0, 0, 0, 0.12),
    inset 0 1px 1px rgba(255, 255, 255, 0.45);

  transition: backdrop-filter 0.3s ease, box-shadow 0.3s ease;">
            <div class="container">
                <div class="content-split">
                    <div class="text-content">
                        <h2 style="color: white;">Why you need our system for your QR Code?</h2>
                        <p style="color: white; text-align:justify;">Your data deserves more than just a QR code, it deserves true
                            protection.
                            Our Secure QR Code Generator isn’t just another QR creator, it’s a security fortress built
                            with next-generation encryption and malware scanning to keep your information safe at every
                            step.<br><br>
                            💡 Here’s what makes us different:<br>
                            Every file you upload is automatically scanned for hidden threats using the OPSWAT
                            Metadefender malware engine,
                            ensuring your content is safe before generating a QR code. Your data is protected with
                            advanced ChaCha20-Poly1305
                            encryption, giving each QR its own unique security. Before anyone can view your QR content,
                            the system verifies
                            their identity through a password or OTP for strong access control. You can also share
                            securely with trusted
                            contacts or send through email with customizable permissions. Your privacy and security are
                            always our top priority.
                        </p>
                        <p><strong style="color: white;">✨ Choose Us for Uncompromising Security, Because every scan should be
                                trusted.</strong></p>
                    </div>
                    <div class="image-content" style="display: flex; border-radius:30px;  width:100%; height:auto;">
                        <img src="img/abas8.svg" alt="Cybersecurity Shield" style="border-radius:30px; ">
                    </div>
                </div>
            </div>
        </section>

        <section id="reviews" class="container">
            <div class="section-header">
                <h1 style="color: white;">Customer Reviews</h1>
            </div>

            <div class="review-wrapper">
                <div class="review-grid" id="reviewGrid">
                    <!-- Review 1 -->
                    <div class="review-card">
                        <h2>Aisyah R. — Small Business Owner</h2>
                        <span>
                            “Finally, a QR generator that puts <strong>security first!</strong> Every product QR I make
                            goes through
                            <strong>malware scanning</strong> and <strong>encryption</strong> automatically. I can
                            confidently share my store
                            links knowing they’re 100% safe.”
                        </span>
                    </div>

                    <div class="review-card">
                        <h2>Adam T. — Final Year Student</h2>
                        <span>
                            “I used it for my university project files. The <strong>OTP</strong> and <strong>password
                                protection</strong> made
                            it feel professional and secure. Even my lecturer was impressed by the safety features!”
                        </span>
                    </div>

                    <div class="review-card">
                        <h2>Farah N. — Cybersecurity Enthusiast</h2>
                        <span>
                            “This isn’t just a QR generator — it’s a <strong>digital vault!</strong> The
                            <strong>ChaCha20 encryption</strong> and
                            <strong>API scan</strong> make it feel like enterprise-level protection for everyday users.”
                        </span>
                    </div>

                    <div class="review-card">
                        <h2>Rayyan M. — Web Developer</h2>
                        <span>
                            “I integrated the secure QR system into my client’s site, and it worked flawlessly. The
                            security layers are
                            impressive and easy to deploy.”
                        </span>
                    </div>

                    <div class="review-card">
                        <h2>Hana A. — Educator</h2>
                        <span>
                            “I use this system to share lesson materials with students safely. It ensures no malware or
                            tampered files reach
                            them. So convenient and safe!”
                        </span>
                    </div>

                    <div class="review-card">
                        <h2>Azlan K. — IT Support Specialist</h2>
                        <span>
                            “This QR system shows what secure design really means. The <strong>API verification</strong>
                            step is genius — it
                            gives true confidence before sharing files.”
                        </span>
                    </div>

                    <div class="review-card">
                        <h2>Emily C. — Freelancer</h2>
                        <span>
                            “I often send project files to clients using this QR system. The automatic scan and
                            encryption give my clients
                            peace of mind every time.”
                        </span>
                    </div>

                    <div class="review-card">
                        <h2>Muhammad D. — Entrepreneur</h2>
                        <span>
                            “I’ve tried many QR generators, but none match this one’s security. Double encryption and
                            OTP access — it’s
                            brilliant for my business needs.”
                        </span>
                    </div>

                    <div class="review-card">
                        <h2>Siti N. — Graphic Designer</h2>
                        <span>
                            “The design and security combination are amazing. I love how I can share my portfolio files
                            safely with encrypted
                            QR codes.”
                        </span>
                    </div>

                    <div class="review-card">
                        <h2>Daniel L. — University Lecturer</h2>
                        <span>
                            “I recommend this QR generator to my students for project submissions. It promotes
                            cybersecurity awareness while
                            being user-friendly.”
                        </span>
                    </div>

                    <div class="review-card">
                        <h2>Nurul H. — Startup Founder</h2>
                        <span>
                            “Security and innovation combined! The <strong>tri-layer verification</strong> truly sets
                            this system apart from
                            others.”
                        </span>
                    </div>

                    <div class="review-card">
                        <h2>Amir Z. — Data Analyst</h2>
                        <span>
                            “The system ensures full data integrity from upload to QR generation. It’s fast, efficient,
                            and most importantly —
                            safe.”
                        </span>
                    </div>

                    <div class="review-card">
                        <h2>Lina P. — Online Seller</h2>
                        <span>
                            “My customers love scanning my QR codes to get product details. I feel secure knowing each
                            QR is verified and
                            malware-free.”
                        </span>
                    </div>

                    <div class="review-card">
                        <h2>Jonathan W. — Software Engineer</h2>
                        <span>
                            “The architecture behind this system is solid. I admire how <strong>encryption</strong> and
                            <strong>API
                                validation</strong> are combined smoothly.”
                        </span>
                    </div>

                    <div class="review-card">
                        <h2>Khairul F. — Student</h2>
                        <span>
                            “I used this for my final-year project documentation. It’s simple, clean, and ensures no one
                            can access my files
                            without permission.”
                        </span>
                    </div>

                    <div class="review-card">
                        <h2>Sabrina M. — Digital Marketer</h2>
                        <span>
                            “This QR generator lets me create campaign links safely. Clients appreciate that it’s
                            verified and encrypted. It
                            adds real value to my marketing work.”
                        </span>
                    </div>

                    <div class="review-card">
                        <h2>Ali R. — Security Researcher</h2>
                        <span>
                            “I analyzed its encryption implementation — impressive! Using
                            <strong>ChaCha20-Poly1305</strong> with unique salts
                            and nonces is exactly how modern systems should protect data.”
                        </span>
                    </div>

                    <div class="review-card">
                        <h2>Melissa T. — Project Manager</h2>
                        <span>
                            “We use it in our team to distribute internal reports. The <strong>OTP access
                                control</strong> keeps sensitive
                            documents secure.”
                        </span>
                    </div>

                    <div class="review-card">
                        <h2>Rizwan A. — System Administrator</h2>
                        <span>
                            “The tri-layer verification system is impressive. It proves that even a QR generator can
                            meet enterprise-grade
                            security standards.”
                        </span>
                    </div>

                    <div class="review-card">
                        <h2>Chloe S. — Content Creator</h2>
                        <span>
                            “I share my digital art using this secure QR platform. My fans can access the content
                            safely, and I know it’s
                            protected from tampering.”
                        </span>
                    </div>

                </div>
            </div>
            <script>
                const grid = document.getElementById("reviewGrid");
                let scrollSpeed = 1;

                function autoScroll() {
                    grid.scrollLeft += scrollSpeed;
                    if (grid.scrollLeft >= grid.scrollWidth - grid.clientWidth) {
                        grid.scrollLeft = 0; // restart smoothly
                    }
                }

                // run every 30ms
                let scrollInterval = setInterval(autoScroll, 30);

                // Pause when hovering a single review card
                const cards = document.querySelectorAll(".review-card");
                cards.forEach(card => {
                    card.addEventListener("mouseenter", () => clearInterval(scrollInterval));
                    card.addEventListener("mouseleave", () => {
                        scrollInterval = setInterval(autoScroll, 30);
                    });
                });
            </script>


        </section>

        <section id="about" style="background: rgba(255, 255, 255, 0.12);backdrop-filter: blur(22px) saturate(140%);-webkit-backdrop-filter: blur(22px) saturate(140%); margin:20px;border:1px solid; border-radius:40px;  border: 1px solid rgba(255, 255, 255, 0.35);

  box-shadow:
    0 12px 30px rgba(0, 0, 0, 0.12),
    inset 0 1px 1px rgba(255, 255, 255, 0.45);

  transition: backdrop-filter 0.3s ease, box-shadow 0.3s ease;">
            <div class="container">
                <div class="content-split">
                    <div class="image-content">
                        <img src="img/abas9.svg" alt="SQ Tech Solver Team" style="border-radius:30px;">
                    </div>
                    <div class="text-content">
                        <h2>About Us</h2>

                        <p style="text-align:justify;color:white;">SQ Tech Solver is dedicated to creating secure and innovative
                            digital solutions that make
                            technology safer and more reliable for everyone. Specializing in web development and
                            cybersecurity, our team focuses on delivering real protection through intelligent design.
                            One of our key achievements is the Secure QR Code Generator System, a platform that supports
                            text, PDF, and image inputs while maintaining the highest level of data protection. The
                            system
                            uses advanced encryption, input validation and hashing to ensure data
                            integrity. It also integrates with a malware scanner API to verify
                            that
                            all content is safe before generating each QR code. This process provides users with a
                            reliable
                            and secure experience for document sharing, authentication, and online payments.
                            Founded by Daniel Haikal, a final year Computer Science student with Honors majoring in
                            Cybersecurity branding with name SQ Tech Solver, it was built on passion and a strong belief
                            in secure digital
                            innovation. Daniel is deeply committed to developing one of the most secure QR code
                            generators
                            and strives to build a safer digital environment where users can share information with
                            confidence. <br>
                            <strong>At SQ Tech Solver, we believe security is not an option but it is the foundation of
                                trust.</strong>
                        </p>
                    </div>
                </div>
            </div>
        </section>

    </main>

    <?php include "componet/footer.php"; ?>

    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>

    <script>
        // Initialize Hero Swiper
        const heroSwiper = new Swiper('.hero-swiper', {
            loop: true,
            effect: 'fade',
            autoplay: {
                delay: 4000,
                disableOnInteraction: false,
            },
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
        });
    </script>
    <script type="module" src="hero-canvas.js"></script>

</body>

</html>