<?php include_once("componet/conn.php"); ?>
<?php session_start();

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

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

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
            animation: bgSlide 20s ease infinite;
            position: relative;
            overflow-x: hidden;
            /* allow vertical scroll but hide horizontal overflow */
        }

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

        .dashboard-wrapper {
            padding: 30px;
            margin-bottom: 50px;
        }

        /* ===== CARD GRID ===== */
        .card-grid {
            display: flex;
            flex-wrap: wrap;
            /* allow wrapping on smaller screens */
            justify-content: space-between;
            gap: 20px;
        }

        /* ===== FEATURE CARD ===== */
        .feature-card {
            flex: 1 1 calc(16.66% - 16px);
            /* 6 cards per row on desktop minus gap */
            min-width: 150px;
            background-color: rgba(0, 0, 0, 0.7);
            border: 1px solid #fff;
            padding: 15px 10px 5px;
            border-radius: 50px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
            transition: transform 0.2s ease-in-out;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-icon {
            font-size: 36px;
            color: #007bff;
            margin-bottom: 15px;
        }

        .feature-title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .feature-description {
            font-size: 14px;
            color: #ccc;
            line-height: 1.3;
        }

        /* ===== TABLET VIEW (2 rows) ===== */
        @media (max-width: 900px) {
            .feature-card {
                flex: 1 1 calc(50% - 10px);
                /* 2 cards per row → total 2 rows for 4–6 cards */
                min-width: 120px;
                padding: 10px 5px;
            }

            .feature-icon {
                font-size: 28px;
                margin-bottom: 10px;
            }

            .feature-title {
                font-size: 16px;
            }

            .feature-description {
                font-size: 12px;
            }
        }

        /* ===== PHONE VIEW (2 rows) ===== */
        @media (max-width: 480px) {
            .feature-card {
                flex: 1 1 calc(50% - 10px);
                /* 2 cards per row on phone */
                min-width: 100px;
                padding: 8px 5px;
                display: flex;
                /* make card a flex container */
                flex-direction: column;
                /* stack items vertically */
                justify-content: center;
                /* vertical centering */
                align-items: center;
                /* horizontal centering */
                text-align: center;

                .feature-description {
                    display: none;
                    /* hide description on small screens */
                }
            }

            .feature-icon {
                margin-top: 15px;
                font-size: 30px;

            }

            .feature-title {
                margin: 10px;
                font-size: 15px;
            }


        }


        /* Main container for all analytics content */
        .analytics-dashboard {
            font-family: 'Segoe UI', sans-serif;
            padding: 10px;
        }

        .analytics-dashboard h2 {
            font-size: 1.75rem;
            font-weight: 600;
            color: white;
            margin-bottom: 25px;
            text-align: center;
        }

        /* Flex container for the chart cards */
        .charts-container {
            display: flex;
            flex-wrap: wrap;
            gap: 25px;
            /* Provides space between cards */
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        /* The individual card for each chart */
        .chart-card {
            flex: 1 1 350px;
            /* Allows cards to grow and shrink */
            min-width: 300px;
            /* Minimum width before wrapping */
            max-width: 100%;
            background: white;
            border: 1px solid #000000ff;
            border-radius: 30px;
            box-shadow: 0 60px 100px rgba(4, 0, 255, 0.05);
            padding: 20px;
            display: flex;
            flex-direction: column;
            transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;

        }

        .chart-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.08);
        }

        .header-section {
            font-family: 'Trebuchet MS', sans-serif;
            font-weight: bold;
            color: white;
            font-size: 28px;
            margin: 0;
        }

        @media (max-width: 480px) {
            .header-section {
                font-size: 14px;
            }
        }

        /* Header section within each card */
        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #f3f4f6;
        }

        .card-title {
            margin: 0;
            font-size: 1.1rem;
            font-weight: 600;
            color: white;
        }

        /* Style for the filter dropdown */
        .chart-filter {
            padding: 6px 10px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            background-color: rgba(0, 0, 0, 0.5);
            font-size: 0.875rem;
            cursor: pointer;
        }

        .chart-filter:focus {
            outline: 2px solid #6366f1;
            border-color: transparent;
        }

        /* Canvas container to ensure it fills the card */
        .chart-body {
            flex-grow: 1;
            position: relative;
        }

        .scroll-table-container {
            overflow-x: auto;
            width: 100%;
            margin-bottom: 60px;
            box-shadow: 0 40px 40px rgba(12, 32, 206, 0.1);
        }

        table#qrTable,
        table#qrTableScroll {
            width: 100%;
            border-collapse: collapse;
            font-family: 'Quicksand', sans-serif;
            table-layout: fixed;
        }

        table#qrTable th,
        table#qrTableScroll td {
            padding: 12px;
            text-align: center;
            justify-content: center;
            color: black;
        }

        table#qrTable thead {
            position: sticky;
            background: gray;
            display: table;
            width: 100%;
            table-layout: fixed;

        }

        #qrTableScroll {
            display: block;
            background-color: rgba(0, 0, 0, 0.3);
            max-height: 330px;
            /* About 8 rows height */
            /* overflow-y: auto; */
        }

        #qrTableScroll tbody {

            display: table;
            background-color: rgba(0, 0, 0, 0.5);
            border: 1px solid #000000ff;
            width: 100%;
            table-layout: fixed;
        }

        .action-btn {
            padding: 4px 8px;
            margin: 2px;
            border: none;
            border-radius: 4px;
            color: #fff;
            cursor: pointer;
        }

        .btn-view {
            background-color: #3498db;
        }

        .btn-delete {
            background-color: #e74c3c;
        }

        .btn-analyze {
            background-color: #e67e22;
        }

        html {
            scroll-behavior: smooth;
        }

        /* Small popup style */
        .qrPopup {
            position: absolute;
            /* position near the button */
            min-width: 250px;
            background-color: rgba(0, 0, 0, 0.8);
            border: 1px solid #fff;
            border-radius: 12px;
            padding: 15px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
            z-index: 1000;
            display: none;
            /* hidden by default */
        }


        @keyframes spin {
            from {
                transform: rotate(0deg);
            }

            to {
                transform: rotate(360deg);
            }
        }
    </style>

    <!-- links -->
    <link rel="stylesheet" href="style-index.css">
    <link rel="stylesheet" href="style-res.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

    <!-- javascript sw-->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.css" />
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">



    <!-- links -->
    <!-- fonts -->
    <!-- <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Acme&family=Barlow:wght@500&family=Quicksand:wght@500&family=Raleway:wght@300&family=Ubuntu:wght@700&display=swap"
        rel="stylesheet"> -->
    <!-- fonts -->
    <title>SQ-TECH SOLVER - Secure QR Code Generator</title>
    <link rel="icon" type="image/png" href="img/log.svg">
</head>

<body>
    <div id="stars"></div>
    <div id="stars2"></div>
    <div id="stars3"></div>

    <?php include("componet/navbar.php"); ?>
    <div class="main-body" style="padding: 0px;">
        <!-- page navigator -->
        <!-- <div class="page-navigator">
            <div class="bigcontainer">
                <div class="page-names">
                    <a href="index.php">Home</a>
                    <span>></span>
                    <p>Shop All</p>
                </div>
            </div>
        </div> -->
        <!-- page navigator -->

        <!--shopall items -->

        <section>
            <div class="bigcontainer" style="margin-top:0px;">
                <div class="cat-products">
                    <div class="products-cards">
                        <div class="container">
                            <div
                                style="display: flex; align-items: center; justify-content: space-between; margin: 10px;">
                                <h1 class="header-section"
                                    style="font-family: 'Trebuchet MS', sans-serif; font-weight: bold; color: white; font-size: 28px; margin: 0px;">
                                    Welcome <strong><?php echo htmlspecialchars($name); ?></strong> to the<br>
                                    Secure QR Code Generator System
                                </h1>
                                <img src="img/pic2.svg" alt="Logo"
                                    style=" width:120px;height: 120px; margin-top:20px; margin-left: 20px; margin-right: 20px;">
                            </div>

                            <small
                                style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;font-weight: bold; text-align: left; font-size:16px;display: block; margin-top: 10px; margin-left: 10px;">
                                Inspired by SQ-TechSolver@2025
                            </small>
                            <small
                                style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;font-weight: normal; text-align: left; font-size:16px;display: block;  margin-bottom:10px; margin-left: 10px;">
                                These features have been made available for you
                            </small>

                            <div class="dashboard-wrapper">
                                <div class="card-grid">

                                    <!-- Generate QR Code Card -->
                                    <div class="feature-card" data-href="products.php">
                                        <i class="fas fa-qrcode feature-icon"></i>
                                        <div class="feature-title">Generate QR Code</div>
                                        <div class="feature-description">
                                            User can generate secure QR code with strong encryption algorithm and manage
                                            it in the home page.
                                        </div>
                                        <br>
                                        <div class="form-acc"
                                            style="display: flex; justify-content: center; align-items: center; margin-left:20px; margin-right:20px; margin-bottom: 23px; ">
                                            <a href="products.php"
                                                style="color:blue; font-size:13px; font-weight:bold; text-align: center;">Click
                                                Here
                                                For Generate Secure QR Code.</a>
                                        </div>
                                    </div>

                                    <!-- Scan QR Code Card -->
                                    <div class="feature-card" data-href="scan.php">
                                        <i class="fas fa-camera feature-icon"></i>
                                        <div class="feature-title">Scan QR Code</div>
                                        <div class="feature-description">
                                            Users can scan QR codes and view their content. They can either scan the QR
                                            code from their phone or directly using the system.
                                        </div>
                                        <br>
                                        <div class="form-acc"
                                            style="display: flex; justify-content: center; align-items: center; margin-left:20px; margin-right:20px; margin-bottom: 23px;">
                                            <a href="scan.php"
                                                style="color:blue; font-size:13px; font-weight:bold; text-align: center;">Click
                                                Here
                                                For Scan QR Code.</a>
                                        </div>
                                    </div>

                                    <!-- Share QR Code Card -->
                                    <div class="feature-card" data-href="#generate-section">
                                        <i class="fas fa-share-alt feature-icon"></i>
                                        <div class="feature-title">Share QR Code</div>
                                        <div class="feature-description">
                                            User can share QR code through email, share with friends within the system,
                                            or
                                            download and share externally.
                                        </div>
                                        <br>
                                        <div class="form-acc"
                                            style="display: flex; justify-content: center; align-items: center; margin-left:20px; margin-right:20px; margin-bottom: 20px;">
                                            <a href="#generate-section"
                                                style="color:blue; font-size:13px; font-weight:bold; text-align: center;">
                                                Click Here For Share QR Code.
                                            </a>

                                        </div>
                                    </div>

                                    <!-- Manage & Analyze QR Code Card -->
                                    <div class="feature-card" data-href="#generate-section">
                                        <i class="fas fa-chart-line feature-icon"></i>
                                        <div class="feature-title">Manage & Analyze</div>
                                        <div class="feature-description">
                                            User can manage generated QR codes and analyze how many people scan them via
                                            analytical
                                            insights on the homepage.
                                        </div>
                                        <br>
                                        <div class="form-acc"
                                            style="display: flex; justify-content: center; align-items: center; margin-bottom: 20px; margin-left: 20px; margin-right: 20px;">
                                            <a href="#generate-section"
                                                style="color:blue; font-size:13px; font-weight:bold; text-align: center;">
                                                Click Here For Manage QR Code.
                                            </a>
                                        </div>
                                    </div>

                                    <div class="feature-card" data-href="#generate-section">
                                        <i class="fas fa-eye feature-icon"></i>
                                        <div class="feature-title">Verify QR Code</div>
                                        <div class="feature-description">
                                            Recipients can verify and access QR code content by entering their
                                            registered email address.
                                            Only authorized recipients are allowed to proceed.
                                        </div>
                                        <br>
                                        <div class="form-acc"
                                            style="display: flex; justify-content: center; align-items: center; margin-bottom: 20px; margin-left: 20px; margin-right: 20px;">
                                            <a href="#generate-section"
                                                style="color: blue; font-size: 13px; font-weight: bold; text-align: center;">
                                                Click Here To Verify QR Code
                                            </a>
                                        </div>
                                    </div>


                                </div>
                            </div>

                            <script>
                                document.querySelectorAll('.feature-card').forEach(card => {
                                    card.addEventListener('click', () => {
                                        const link = card.getAttribute('data-href');
                                        if (link) {
                                            window.location.href = link;
                                        }
                                    });
                                });
                            </script>
                            <h2 style="text-align:left; font-family: 'Segoe UI', sans-serif;  font-size: 1.75rem;
                            font-weight: 600;margin-bottom: 20px;">
                                Analytic Dashboard<br>
                            </h2>


                            <!-- <strong
                                style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-weight: bold; font-size:16px; display:block; margin-top:10px; margin-bottom:10px; white-space:normal; word-break:break-word;">
                                Your trusted platform for generating secure and reliable QR codes.
                                Experience a seamless and organized QR code management system designed to give users
                                confidence and convenience.
                                We don't just generate but we also educate. Learn how to protect yourself from QR code
                                fraud
                                and stay ahead with smart, secure scanning.
                            </strong> -->

                            <small
                                style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-weight: normal; font-size:16px; display:block; text-align:left; margin-right:10px; margin-bottom:30px; white-space:normal; word-break:break-word;">
                                See how many QR codes you’ve created, shared, and received, as well as how many people
                                scan your QR codes each day.
                                Every line and curve on the chart tells the story of how actively you’re connecting
                                through this QR Code System.
                            </small>

                            <div class="row features-inner">
                                <div class="col-lg-3 col-md-6 col-sm-6">
                                    <div class="single-features">
                                        <div class="f-icon">
                                            <span class="h7" id="generatedCount">0</span>
                                        </div>
                                        <h6>Generate QR Code</h6>
                                        <!-- <p>Free Shipping on all order</p> -->
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-6">
                                    <div class="single-features">
                                        <div class="f-icon">
                                            <span class="h7" id="share_email">0</span>
                                        </div>
                                        <h6>Share QR Code</h6>
                                        <!-- <p>Free Shipping on all order</p> -->
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-6">
                                    <div class="single-features">
                                        <div class="f-icon">
                                            <span class="h7" id="fetchreceiveqr">0</span>
                                        </div>
                                        <h6>Receive QR Code</h6>
                                        <!-- <p>Free Shipping on all order</p> -->
                                    </div>
                                </div>
                                <div class="col-lg-3 col-md-6 col-sm-6">
                                    <div class="single-features">
                                        <div class="f-icon">
                                            <span class="h7" id="generatedCountFriends">0</span>
                                        </div>
                                        <h6>Friends</h6>
                                        <!-- <p>Free Shipping on all order</p> -->
                                    </div>
                                </div>
                            </div>
                        </div>



                        <!-- script for count -->
                        <script>
                            document.addEventListener("DOMContentLoaded", function () {
                                fetch('fetch_count.php')
                                    .then(res => res.text())
                                    .then(count => {
                                        document.getElementById('generatedCount').innerText = count;
                                    })
                                    .catch(err => {
                                        console.error("Failed to fetch generated QR count:", err);
                                    });
                            });
                            document.addEventListener("DOMContentLoaded", function () {
                                fetch('fetch_receive_qr.php')
                                    .then(res => res.text())
                                    .then(count => {
                                        document.getElementById('fetchreceiveqr').innerText = count;
                                    })
                                    .catch(err => {
                                        console.error("Failed to fetch generated QR count:", err);
                                    });
                            });
                            document.addEventListener("DOMContentLoaded", function () {
                                fetch('fetch_friends.php')
                                    .then(res => res.text())
                                    .then(count => {
                                        document.getElementById('generatedCountFriends').innerText = count;
                                    })
                                    .catch(err => {
                                        console.error("Failed to fetch number of friends:", err);
                                    });
                            });
                            document.addEventListener("DOMContentLoaded", function () {
                                fetch('fetch_email.php')
                                    .then(res => res.text())
                                    .then(count => {
                                        document.getElementById('share_email').innerText = count;
                                    })
                                    .catch(err => {
                                        console.error("Failed to fetch number of friends:", err);
                                    });
                            });

                        </script>

                        <!-- Container for 2 fixed charts
                        <div style="display: flex; flex-wrap: wrap; justify-content: center; gap: 20px; padding: 20px;">
                            <div style="flex: 1; max-width: 600px;">
                                <canvas id="qrActivityChart" width="400" height="200"></canvas>
                            </div>
                            <div style="flex: 1; max-width: 600px;">
                                <canvas id="friendActivityChart" width="400" height="200"></canvas>
                            </div>
                        </div> -->


                        <div class="analytics-dashboard">
                            <!-- <h2 style="text-align: left; margin-left: 30px;">QR Code Analytics Overview</h2> -->

                            <div class="charts-container">

                                <div class="chart-card">
                                    <div class="card-header">
                                        <h4 class="card-title" style="color: black;">Generated / Shared / Received</h4>
                                        <!-- <select id="accessFilter1" class="chart-filter">
                                            <option value="all">All Time</option>
                                            <option value="7">Last 7 Days</option>
                                            <option value="30">Last 30 Days</option>
                                        </select> -->
                                    </div>
                                    <div class="chart-body">
                                        <canvas id="barChart"></canvas>
                                    </div>
                                </div>

                                <div class="chart-card">
                                    <div class="card-header">
                                        <h4 class="card-title" style="color: black;">Stats Overview (Line)</h4>
                                        <!-- <select id="accessFilter2" class="chart-filter">
                                            <option value="all">All Time</option>
                                            <option value="7">Last 7 Days</option>
                                            <option value="30">Last 30 Days</option>
                                        </select> -->
                                    </div>
                                    <div class="chart-body">
                                        <canvas id="lineChart"></canvas>
                                    </div>

                                </div>

                                <div class="chart-card">
                                    <div class="card-header">
                                        <h4 class="card-title" style="color: black;">Daily Scans</h4>
                                        <select id="accessFilter" class="chart-filter">
                                            <option value="all">All Time</option>
                                            <option value="7">Last 7 Days</option>
                                            <option value="30">Last 30 Days</option>
                                        </select>
                                    </div>
                                    <div class="chart-body">
                                        <canvas id="accessChart"></canvas>
                                    </div>
                                </div>

                            </div>
                        </div>

                        <script>
                            const barCtx = document.getElementById('barChart').getContext('2d');
                            const lineCtx = document.getElementById('lineChart').getContext('2d');
                            const accessCtx = document.getElementById('accessChart').getContext('2d');
                            let barChart, lineChart, accessChart;

                            // A common set of options for chart appearance
                            const commonChartOptions = {
                                responsive: true,
                                maintainAspectRatio: false,
                                plugins: {
                                    legend: {
                                        display: false
                                    }
                                },
                                scales: {
                                    y: {
                                        beginAtZero: true,
                                        grid: {
                                            color: '#e5e7eb',
                                            borderDash: [2, 4],
                                        },
                                        ticks: {
                                            color: '#6b7280'
                                        }
                                    },
                                    x: {
                                        grid: {
                                            display: false
                                        },
                                        ticks: {
                                            color: '#6b7280'
                                        }
                                    }
                                }
                            };

                            // Load Bar Chart (Generated/Shared/Received)
                            function loadBarChart(filterRange = 'all') {
                                fetch('fetch_graph_data.php', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                    body: `range=${filterRange}`
                                })
                                    .then(res => res.json())
                                    .then(data => {
                                        delete data['accessed'];

                                        const labels = Object.keys(data);
                                        const values = Object.values(data);

                                        if (barChart) barChart.destroy();
                                        barChart = new Chart(barCtx, {
                                            type: 'bar',
                                            data: {
                                                labels: labels,
                                                datasets: [{
                                                    label: 'Count',
                                                    data: values,
                                                    backgroundColor: ['#05b8ffff', '#00054bff'],
                                                    borderRadius: 4,
                                                }]
                                            },
                                            options: commonChartOptions
                                        });
                                    });
                            }

                            // Load Line Chart (Stats Overview)
                            function loadLineChart(filterRange = 'all') {
                                fetch('fetch_graph_data.php', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                    body: `range=${filterRange}`
                                })
                                    .then(res => res.json())
                                    .then(data => {
                                        delete data['accessed'];

                                        const labels = Object.keys(data);
                                        const values = Object.values(data);

                                        if (lineChart) lineChart.destroy();
                                        lineChart = new Chart(lineCtx, {
                                            type: 'line',
                                            data: {
                                                labels: labels,
                                                datasets: [{
                                                    label: 'Count',
                                                    data: values,
                                                    borderColor: '#3955b1ff',
                                                    backgroundColor: 'rgba(0, 108, 170, 0.1)',
                                                    fill: true,
                                                    tension: 0.3,
                                                    pointBackgroundColor: '#000146ff'
                                                }]
                                            },
                                            options: commonChartOptions
                                        });
                                    });
                            }

                            function loadAccessedChart(filterDays = '7') {
                                fetch('fetch_accessed_chart.php', {
                                    method: 'POST',
                                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                    body: `range=${filterDays}`
                                })
                                    .then(res => res.json())
                                    .then(data => {
                                        const labels = data.map(entry => entry.date);
                                        const counts = data.map(entry => entry.count);

                                        if (accessChart) accessChart.destroy();
                                        accessChart = new Chart(accessCtx, {
                                            type: 'line',
                                            data: {
                                                labels: labels,
                                                datasets: [{
                                                    label: 'Scans',
                                                    data: counts,
                                                    borderColor: '#140069ff',
                                                    backgroundColor: 'rgba(144, 124, 255, 0.1)',
                                                    fill: true,
                                                    tension: 0.3,
                                                    pointBackgroundColor: '#00aeffff'
                                                }]
                                            },
                                            options: commonChartOptions
                                        });
                                    });
                            }

                            // Individual filter handlers
                            // document.getElementById('accessFilter1').addEventListener('change', function () {
                            //     loadBarChart(this.value);
                            // });

                            // document.getElementById('accessFilter2').addEventListener('change', function () {
                            //     loadLineChart(this.value);
                            // });

                            document.getElementById('accessFilter').addEventListener('change', function () {
                                loadAccessedChart(this.value);
                                loadGeneralCharts(selectedRange);
                                loadAccessedChart(selectedRange);
                            });

                            // Initial Load
                            loadBarChart('all');   // Default bar chart
                            loadLineChart('all');  // Default line chart
                            loadAccessedChart('all'); // Default daily scans (7 days)
                        </script>


                        <!-- <section> -->
                        <!-- Aesthetic Table Section -->

                        <!-- FontAwesome for icons -->
                        <link rel="stylesheet"
                            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

                        <!-- Modal -->
                        <div id="qrModal"
                            style="display:none; position:fixed; z-index:1000; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); overflow-y: auto;">

                            <div
                                style="border: 3px solid black; background: linear-gradient(90deg, rgba(255, 255, 255, 1) 35%, rgba(194, 192, 192, 1) 100%); width:90%; max-width:800px; max-height:fit-content; overflow-y:auto; margin:5% auto; padding:20px; border-radius:50px; position:relative; box-shadow:0 4px 20px rgba(0,0,0,0.3);">

                                <span id="closeModal"
                                    style="position:absolute; top:10px; right:20px; cursor:pointer; font-size:24px;">&times;</span>

                                <h3 style="text-align:center; font-size: 17px; color: black; font-weight: bold;">QR Code
                                    Details</h3>

                                <div id="modalContent" style="color: black; margin-top:20px;"></div>
                            </div>
                        </div>

                        <!-- Analyze Modal -->
                        <!-- Analyze Modal -->
                        <div id="analyzeModal"
                            style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); justify-content:center; align-items:center; z-index:1000;">
                            <div class="chart-container"
                                style="border: 3px solid black;background: linear-gradient(90deg, rgba(255, 255, 255, 1) 35%, rgba(194, 192, 192, 1) 100%); padding:20px; border-radius:30px; max-width:800px; width:90%; max-height:90vh; overflow-y:auto;">
                                <h2 style="text-align:center;   color: black; font-size:17px; font-weight: bold;">QR
                                    Code Access
                                    Analysis</h2>

                                <!-- Filter Range -->
                                <div style="margin-top:10px; text-align:center; font-size:14px;color:black;">
                                    <label for="filterRange">Select Range: </label>
                                    <select id="filterRange"
                                        style="padding:5px 10px; color: black;margin:5px;  font-size:14px; border-radius:20px;">
                                        <option value="all" selected>All Time</option>
                                        <option value="7">Last 7 Days</option>
                                        <option value="30">Last 30 Days</option>
                                    </select>
                                </div>

                                <!-- Canvas will be added dynamically -->
                                <canvas id="accessedChart" style="width:100%; height:300px;"></canvas>

                                <div id="insights" style="margin-top:20px; color: black;font-size:15px;"></div>

                                <div style="margin-top:20px; text-align:center;">
                                    <button id="downloadReportBtn"
                                        style="margin-right:10px; padding:6px 12px; background-color:#0984e3; color:white; border:none; border-radius:30px;">Download
                                        Report</button>
                                    <button id="closeAnalyzeModal"
                                        style="padding:6px 12px; background-color:#d63031; color:white; border:none; border-radius:30px;">Close</button>
                                </div>
                            </div>
                        </div>
                        <div id="generate-section"></div>



                        <!-- Load Chart.js -->
                        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>



                        <!-- QR Stats Table -->
                        <!-- QR Stats Table -->

                        <div class="qr-stats-table"
                            style="width: 100%; margin-left: auto; margin-right: auto; font-family: 'Quicksand', sans-serif;">

                            <div class="analytics-dashboard">
                                <h2 style="text-align:left; font-family: 'Segoe UI', sans-serif;margin-bottom: 20px; ">
                                    Manage QR Code<br>
                                </h2>
                                <small
                                    style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-weight: normal; text-align:left; font-size: 16px;">You
                                    can manage your QR code here. You are allowed to view, scan, download,
                                    share, analyze and delete your qr code. Password and QR Code's content are not
                                    allowed to edit for security purpose.</small>
                            </div>

                            <!-- Search Filter -->
                            <div style="margin-bottom: 10px; text-align: right; border-radius:40px;">
                                <input type="text" id="qrSearchInput" placeholder="Search table..."
                                    style="padding: 6px; color:black; font-family: 'Quicksand', sans-serif; width: 250px; border-radius:40px;">
                            </div>

                            <!-- Styles -->

                            <!-- Scrollable Table -->
                            <div class="scroll-table-container">
                                <table id="qrTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 150px;">Date</th>
                                            <!-- <th>QR Filename</th> -->
                                            <th style="width: 100px;">QR Code Id</th>
                                            <th>References</th>
                                            <th>Click Here For Actions</th>
                                        </tr>
                                    </thead>
                                </table>

                                <!-- Scrollable body -->
                                <div id="qrTableScroll">
                                    <table>
                                        <tbody id="qrTableBody">
                                            <!-- Example rows (to be replaced by JS) -->
                                            <tr>
                                                <td>2025-08-04 00:12:15</td>

                                                <td>Disabled</td>
                                                <td>
                                                    <button class="action-btn btn-share">Share to your friend</button>
                                                    <button class="action-btn btn-email">Share to email</button>
                                                    <button class="action-btn btn-view">View</button>
                                                    <button class="action-btn btn-delete">Delete</button>
                                                    <button class="action-btn btn-analyze">Analyze</button>
                                                </td>
                                            </tr>
                                            <!-- Add more rows dynamically -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Filter Script -->
                            <script>
                                document.getElementById('qrSearchInput').addEventListener('keyup', function () {
                                    const filter = this.value.toLowerCase();
                                    const rows = document.querySelectorAll('#qrTableBody tr');

                                    rows.forEach(row => {
                                        const rowText = row.textContent.toLowerCase();
                                        row.style.display = rowText.includes(filter) ? '' : 'none';
                                    });
                                });
                            </script>
                        </div>


                        <script>

                            document.getElementById('qrSearchInput').addEventListener('keyup', function () {
                                const filter = this.value.toLowerCase();
                                const rows = document.querySelectorAll('#qrTable tbody tr');

                                rows.forEach(row => {
                                    const rowText = row.textContent.toLowerCase();
                                    row.style.display = rowText.includes(filter) ? '' : 'none';
                                });
                            });

                            document.addEventListener("DOMContentLoaded", function () {
                                fetch('fetch_qr_data.php')
                                    .then(res => res.text())
                                    .then(html => {
                                        const tbody = document.getElementById('qrTableBody');
                                        tbody.innerHTML = html;

                                        // Event delegation for both View and Delete buttons
                                        tbody.addEventListener('click', function (e) {

                                            const systemBtn = e.target.closest('.share-system-btn');
                                            const emailBtn = e.target.closest('.share-email-btn');
                                            const viewBtn = e.target.closest('.view-btn');
                                            const analyzeBtn = e.target.closest('.analyze-btn');
                                            const downloadBtn = e.target.closest('.download-btn');
                                            const deleteBtn = e.target.closest('.delete-btn');

                                            
                                            // Utility function to close popup when clicking outside
                                            function setupOutsideClick(popup) {
                                                const handleClickOutside = (e) => {
                                                    if (!popup.contains(e.target)) {
                                                        popup.remove();
                                                        document.removeEventListener('click', handleClickOutside);
                                                    }
                                                };
                                                // Delay adding listener to avoid immediate removal on click
                                                setTimeout(() => {
                                                    document.addEventListener('click', handleClickOutside);
                                                }, 0);
                                            }
                                            // ======= View Button for Friends =======
                                            if (systemBtn) {
                                                systemBtn.addEventListener('click', (e) => {
                                                    try {
                                                        const data = JSON.parse(systemBtn.dataset.details);

                                                        // Remove existing popup
                                                        let existingPopup = document.querySelector('.qrPopup');
                                                        if (existingPopup) existingPopup.remove();

                                                        const popup = document.createElement('div');
                                                        popup.className = 'qrPopup';

                                                        popup.innerHTML = `
                <label for="friendListDropdown" style="font-weight:bold; color: white;">👥 Share To Your Friends</label>
                <div style="display:flex; align-items:center; gap:8px;  font-size:14px; color: white;">
    <span style="font-size:16px; font-weight:bold;">${data.header_text}</span>
    <span>-</span>
    <span><strong>ID:</strong> ${data.id}</span>
</div>

                <div id="friendShareForm" style="margin-top:10px;">
                    <select id="friendListDropdown" style="padding:6px; width:100%; border:1px solid white; color: white; background-color: rgba(0,0,0,0.5);"></select>
                    <button id="submitFriendShare"
                        style="padding:6px 10px; margin-top:8px; background-color:#e17055; color:white; border-radius:30px;width:100%;">
                        Share
                    </button>
                </div>
            `;

                                                        // Append first to measure
                                                        document.body.appendChild(popup);
                                                        popup.style.display = 'block';
                                                        popup.style.position = 'fixed'; // Use fixed for viewport positioning

                                                        // Position below the button
                                                        const rect = systemBtn.getBoundingClientRect();
                                                        const popupWidth = popup.offsetWidth;
                                                        const popupHeight = popup.offsetHeight;

                                                        popup.style.left = `${rect.left + rect.width / 2 - popupWidth / 2}px`;
                                                        popup.style.top = `${rect.bottom + 5}px`;

                                                        // Load friends list
                                                        fetch('get_friends_list.php')
                                                            .then(res => res.json())
                                                            .then(friends => {
                                                                const dropdown = popup.querySelector('#friendListDropdown');
                                                                dropdown.innerHTML = '';
                                                                if (friends.length === 0) {
                                                                    dropdown.innerHTML = '<option disabled>No friends found</option>';
                                                                } else {
                                                                    friends.forEach(friend => {
                                                                        const option = document.createElement('option');
                                                                        option.value = friend.id;
                                                                        option.textContent = friend.email;
                                                                        dropdown.appendChild(option);
                                                                    });
                                                                }
                                                            });

                                                        // Share QR
                                                        popup.querySelector('#submitFriendShare').onclick = () => {
                                                            const friendId = popup.querySelector('#friendListDropdown').value;
                                                            if (!friendId) {
                                                                alert("Please select a friend.");
                                                                return;
                                                            }

                                                            fetch('share_qr_friends.php', {
                                                                method: 'POST',
                                                                headers: { 'Content-Type': 'application/json' },
                                                                body: JSON.stringify({ friend_id: friendId, qr_id: data.id })
                                                            })
                                                                .then(res => res.json())
                                                                .then(response => {
                                                                    alert(response.message || "QR Code shared successfully.");
                                                                    popup.remove();
                                                                })
                                                                .catch(err => {
                                                                    console.error('QR share failed:', err);
                                                                    alert("Failed to share QR code.");
                                                                });
                                                        };

                                                        // Close when clicking outside
                                                        setupOutsideClick(popup);

                                                    } catch (err) {
                                                        console.error("Failed to parse QR data:", err);
                                                    }
                                                });
                                            }
                                            // ======= View Button for Email =======
                                            if (emailBtn) {
                                                emailBtn.addEventListener('click', (e) => {
                                                    try {
                                                        const data = JSON.parse(emailBtn.dataset.details);

                                                        // Remove existing popup
                                                        let existingPopup = document.querySelector('.qrPopup');
                                                        if (existingPopup) existingPopup.remove();

                                                        const popup = document.createElement('div');
                                                        popup.className = 'qrPopup';

                                                        popup.innerHTML = `
                <p style="color: white;">Enter your friend’s email to send the QR code:</p>
<div style="display:flex; align-items:center; gap:8px; font-size:14px; color: white;">
    <span style="font-size:16px; font-weight:bold;">${data.header_text}</span>
    <span>-</span>
    <span><strong>ID:</strong> ${data.id}</span>
</div>

                <input type="email" id="autoEmailInput" placeholder="Enter recipient email"
                    style="padding:6px; width:100%; border:1px solid white; color: white; background-color: rgba(0,0,0,0.5);" />
                <button id="sendEmailBtn"
                    style="padding:6px 12px; margin-top:8px; background-color:#00b894; color:white; border:none; border-radius:30px; width:100%;">
                    Send QR Code
                </button>
                <div id="emailLoading" style="display:none; margin-top:10px; color: white;">
                    Sending QR code...
                </div>
            `;

                                                        // Append first to measure size
                                                        document.body.appendChild(popup);
                                                        popup.style.display = 'block';
                                                        popup.style.position = 'fixed'; // Use fixed for viewport positioning

                                                        // Position below the button
                                                        const rect = emailBtn.getBoundingClientRect();
                                                        const popupWidth = popup.offsetWidth;
                                                        const popupHeight = popup.offsetHeight;

                                                        popup.style.left = `${rect.left + rect.width / 2 - popupWidth / 2}px`;
                                                        popup.style.top = `${rect.bottom + 5}px`;

                                                        // Send QR code function
                                                        const emailInput = popup.querySelector('#autoEmailInput');
                                                        const sendBtn = popup.querySelector('#sendEmailBtn');
                                                        const loadingDiv = popup.querySelector('#emailLoading');

                                                        const sendQRCode = () => {
                                                            const recipientEmail = emailInput.value.trim();
                                                            if (!recipientEmail) {
                                                                alert("Please enter a valid email address.");
                                                                return;
                                                            }
                                                            loadingDiv.style.display = 'block';

                                                            fetch('share_qr_email.php', {
                                                                method: 'POST',
                                                                headers: { 'Content-Type': 'application/json' },
                                                                body: JSON.stringify({ email: recipientEmail, qr_id: data.id })
                                                            })
                                                                .then(res => res.json())
                                                                .then(response => {
                                                                    alert(response.message || `QR Code sent to ${recipientEmail}.`);
                                                                    popup.remove();
                                                                })
                                                                .catch(err => {
                                                                    console.error('Email transfer failed:', err);
                                                                    alert("Failed to send QR code.");
                                                                })
                                                                .finally(() => {
                                                                    loadingDiv.style.display = 'none';
                                                                });
                                                        };

                                                        sendBtn.addEventListener('click', sendQRCode);
                                                        emailInput.addEventListener('keypress', (e) => { if (e.key === 'Enter') sendQRCode(); });

                                                        // Close when clicking outside
                                                        setupOutsideClick(popup);

                                                    } catch (err) {
                                                        console.error("Failed to parse QR data:", err);
                                                    }
                                                });
                                            }

                                            // ======= View Button =======
                                            if (viewBtn) {
                                                try {
                                                    const data = JSON.parse(viewBtn.dataset.details);
                                                    const content = `
                                                    <div style="display: flex; flex-wrap: wrap; gap: 30px; justify-content: center; align-items: flex-start;">

                                                        <!-- Left Column -->
                                                        <div style="flex: 1; min-width: 250px;">
                                                            <p style="font-size:16px; font-weight:bold;"><strong></strong> ${data.header_text}</p><br>
                                                            <p style="font-size:14px;"><strong>ID:</strong> ${data.id}</p>
                                                            <p style="font-size:14px;"><strong>QR Filename:</strong> ${data.qr_filename}</p>
                                                            <p style="font-size:14px;"><strong>Created At:</strong> ${data.created_at}</p>
                                                            <p style="font-size:14px;"><strong>Description:</strong> ${data.description}</p>
                                                            <br>
                                                           <p style="color:black; font-size:14px;"> <strong>To protect your QR code, recipients must be verified by you before requesting a one-time passcode (OTP). Only verified users will be able to unlock your QR content.</strong> </p><br>  <div>
                                                                <input type="email" id="emailField" placeholder="Enter email for verification" 
                                                                    style="padding: 6px; width: 100%; border: 1px solid black; border-radius:30px;" />
                                                                <br><br>
                                                                <div style="color: black; font-size: 14px;">
                                                                    <strong>Verification Type (Required):</strong><br>
                                                                    <label>
                                                                        <input type="radio" name="accessType" value="1" required> One-time only
                                                                    </label><br>
                                                                    <label>
                                                                        <input type="radio" name="accessType" value="2" required> Life-Time access
                                                                    </label>
                                                                </div>
                                                                <button id="emailSendBtn" title="Send to Email"
                                                                    style="padding: 4px 8px; margin-top: 10px; background-color: #6c5ce7; color: white; font-size:14px;border: none; border-radius:20px; cursor: pointer;">
                                                                    Verify Email
                                                                </button>
                                                                <div id="emailVerifySpinner" style="
                                                                        display:none;
                                                                        margin-top:10px;
                                                                        text-align:left;
                                                                    ">
                                                                        <div style="
                                                                            width:20px;
                                                                            height:20px;
                                                                            border:4px solid #dfe6e9;
                                                                            border-top:4px solid #6c5ce7;
                                                                            border-radius:50%;
                                                                            animation: spin 1s linear infinite;
                                                                            
                                                                        "></div>
                                                                        
                                                                    </div>
                                                                

                                                            </div>
                                                            <br>
                                                            

                                                           
                                                        </div>

                                                        <!-- Right Column -->
                                                            <div style="
                                                            flex: 1; 
                                                            min-width: 250px; 
                                                            display: flex; 
                                                            flex-direction: column; 
                                                            justify-content: center; 
                                                            align-items: center; 
                                                            text-align: center;
                                                        ">
                                                            
                                                            <p style="color:black; font-size:16px;"><strong>Preview</strong></p><br>
                                                                                                                    
                                                            <img id="qrImagePreview"
                                                                src="data:image/jpeg;base64,${data.qr_image_base64}"
                                                                alt="QR Code"
                                                                style="width: 250px; height: 250px; border: 1px solid #ccc; border-radius:30px; margin-bottom: 20px;">
                                                                                                                                
                                                                    <div style="display: flex; gap: 5px; align-items: center;">
                                                                        <button id="downloadBtn"
                                                                            style="padding: 4px 8px; background-color: #27ae60; color: white; border: none; border-radius: 20px; cursor: pointer; font-size: 14px;">
                                                                            Download
                                                                        </button>

                                                                        <button id="scanBtn"
                                                                            style="padding: 4px 8px; background-color: #4f5002a8; color: white; border: none; border-radius: 20px; cursor: pointer; font-size: 14px;">
                                                                            Scan
                                                                        </button>
                                                                    </div>

                                                        </div>
                                                        
                                                        <!-- ================= Activity Table Section ================= -->

                                                        <div style="width:100%; margin-top:30px;">

    <!-- Last Updated -->
    <p style="text-align:center; font-size:16px; font-weight:bold; color:black;">📋 Access Activity Log</p>
    <div id="lastUpdated"
        style="
            color:black;
            font-size:14px;
            text-align:center;
            margin:10px 0;
            font-weight:bold;
            width:100%;
        ">
        Last updated: --
    </div>

    <!-- Table Container -->
    <div style="
        max-height:400px;
        overflow-y:auto;
        padding:12px;
        margin:0 10px 15px 10px;
        box-shadow:0 4px 8px rgba(255,255,255,0.2);
        width:calc(100% - 20px);
    ">

        <table style="width:100%; text-align:left; font-size:15px; border-collapse:collapse;">
            <thead>
                <tr style="
                    background:linear-gradient(90deg,#4b6cb7,#182848);
                    color:white;
                    position:sticky;
                    top:0;
                    z-index:1;
                ">
                    <th style="text-align:center; padding:10px;">Timestamp</th>
                    <th style="text-align:center; padding:10px;">Email</th>
                    <th style="text-align:center; padding:10px;">Type Access</th>
                    <th style="text-align:center; padding:10px;">QR ID</th>
                    <th style="text-align:center; padding:10px;">Access ID</th>
                </tr>
            </thead>
            <tbody id="activityBody">
                <!-- Data loads here -->
            </tbody>
        </table>

    </div>
</div>

<!-- Refresh Button -->
<div style="text-align: center; margin-top: 10px;">
    <button id="refreshActivityBtn" style="padding: 8px 16px; background-color: #007bff; color: white; border: none; border-radius: 20px; cursor: pointer; font-size: 14px;">
        Refresh Activity
    </button>
</div>


                                                    </div>
                                                    `;

                                                    //code for chart



                                                    document.getElementById('modalContent').innerHTML = content;
                                                    document.getElementById('qrModal').style.display = 'flex';

                                                    // Download Button
                                                    setTimeout(() => {
                                                        const downloadBtn = document.getElementById('downloadBtn');
                                                        const img = document.getElementById('qrImagePreview');
                                                        const scanBtn = document.getElementById('scanBtn');


                                                        if (downloadBtn && img) {
                                                            downloadBtn.addEventListener('click', () => {
                                                                const link = document.createElement('a');
                                                                link.href = img.src;
                                                                link.download = data.qr_filename || 'qr_code.jpg';
                                                                document.body.appendChild(link);
                                                                link.click();
                                                                document.body.removeChild(link);
                                                            });
                                                        }

                                                        function loadActivity(qrId) {
                                                            const xhr = new XMLHttpRequest();
                                                            xhr.open("POST", "get_activity.php", true);
                                                            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

                                                            xhr.onreadystatechange = function () {
                                                                if (xhr.readyState === 4 && xhr.status === 200) {
                                                                    const newData = xhr.responseText;
                                                                    const body = document.getElementById("activityBody");
                                                                    const updated = document.getElementById("lastUpdated");

                                                                    if (body && updated && newData !== body.innerHTML) {
                                                                        body.innerHTML = newData;
                                                                        updated.innerText = "Last updated: " + new Date().toLocaleString();
                                                                    }
                                                                }
                                                            };

                                                            xhr.send("qr_id=" + encodeURIComponent(qrId));
                                                        }

                                                        // Load immediately, then every 5 seconds
                                                        loadActivity(data.id);
                                                        // setInterval(() => loadActivity(data.id), 5000);


                                                        if (scanBtn && img) {
                                                            // Scan button
                                                            scanBtn.addEventListener('click', () => {
                                                                fetch(img.src)
                                                                    .then(res => res.blob())
                                                                    .then(blob => {
                                                                        const file = new File([blob], data.qr_filename || 'qr_code.jpg', { type: blob.type });

                                                                        // Create a form dynamically
                                                                        const form = document.createElement('form');
                                                                        form.method = 'POST';
                                                                        form.enctype = 'multipart/form-data';
                                                                        form.action = 'scan.php';

                                                                        // Create file input
                                                                        const fileInput = document.createElement('input');
                                                                        fileInput.type = 'file';
                                                                        fileInput.name = 'qr_upload';

                                                                        // Put the file inside DataTransfer (to simulate user file upload)
                                                                        const dt = new DataTransfer();
                                                                        dt.items.add(file);
                                                                        fileInput.files = dt.files;

                                                                        form.appendChild(fileInput);
                                                                        document.body.appendChild(form);

                                                                        // Submit form → move to scan.php with the file
                                                                        form.submit();
                                                                    });
                                                            });
                                                        }

                                                        // Refresh Button
                                                        const refreshBtn = document.getElementById('refreshActivityBtn');
                                                        if (refreshBtn) {
                                                            refreshBtn.addEventListener('click', () => {
                                                                loadActivity(data.id);
                                                            });
                                                        }

                                                        // Email Send Button
                                                        // Email Send Button with Spinner
                                                        const emailverificationBtn = document.getElementById('emailSendBtn');
                                                        const spinner = document.getElementById('emailVerifySpinner');
                                                        // const trackAccessBtn = document.getElementById('emailTrackAccess');
                                                        const emailField = document.getElementById('emailField');
                                                        if (emailverificationBtn && spinner && emailField) {
                                                            emailverificationBtn.addEventListener('click', () => {
                                                                const email = emailField.value.trim();
                                                                const accessType = document.querySelector('input[name="accessType"]:checked');

                                                                if (!email) {
                                                                    alert("Please enter a valid email address.");
                                                                    return;
                                                                }

                                                                if (!accessType) {
                                                                    alert("Please select an access type.");
                                                                    return;
                                                                }

                                                                // Lock UI
                                                                emailverificationBtn.disabled = true;
                                                                spinner.style.display = 'block';

                                                                fetch('send_qr_email.php', {
                                                                    method: 'POST',
                                                                    headers: { 'Content-Type': 'application/json' },
                                                                    body: JSON.stringify({
                                                                        email: email,
                                                                        qr_id: data.id,
                                                                        access_type: accessType.value
                                                                    })
                                                                })
                                                                    .then(res => res.text())
                                                                    .then(response => {
                                                                        alert(response);
                                                                        emailField.value = '';
                                                                    })
                                                                    .catch(err => {
                                                                        console.error('Email send failed:', err);
                                                                        alert("Failed to send email.");
                                                                    })
                                                                    .finally(() => {
                                                                        spinner.style.display = 'none';
                                                                        emailverificationBtn.disabled = false;
                                                                    });
                                                            });
                                                        }
                                                    }, 100);

                                                } catch (err) {
                                                    console.error("Failed to parse QR data:", err);
                                                }
                                            }


                                            let chartInstance = null;

                                            if (analyzeBtn) {
                                                analyzeBtn.addEventListener("click", function () {
                                                    const qrId = this.dataset.id;
                                                    const modal = document.getElementById('analyzeModal');
                                                    const insightsDiv = document.getElementById('insights');
                                                    const filterDropdown = document.getElementById('filterRange');

                                                    // Remove old canvas if it exists
                                                    const oldCanvas = document.getElementById('accessedChart');
                                                    if (oldCanvas) {
                                                        oldCanvas.remove();
                                                    }

                                                    // Create new canvas
                                                    const newCanvas = document.createElement('canvas');
                                                    newCanvas.id = 'accessedChart';
                                                    newCanvas.width = 600;
                                                    newCanvas.height = 400;
                                                    insightsDiv.before(newCanvas);
                                                    const ctx = newCanvas.getContext('2d');

                                                    // Destroy previous chart if it exists
                                                    if (chartInstance) {
                                                        chartInstance.destroy();
                                                        chartInstance = null;
                                                    }

                                                    function loadChart(range) {
                                                        fetch('get_qr_access_data.php', {
                                                            method: 'POST',
                                                            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                                                            body: `qr_id=${qrId}&range=${range}`
                                                        })
                                                            .then(res => res.json())
                                                            .then(response => {
                                                                if (response.error) throw new Error(response.error);

                                                                const labels = response.labels;
                                                                const data = response.data;
                                                                const total = data.reduce((a, b) => a + b, 0);
                                                                const max = Math.max(...data);
                                                                const min = Math.min(...data);
                                                                const avg = (total / data.length).toFixed(2);
                                                                const labelRangeText = range === 'all' ? 'All Time' : `Last ${range} Days`;

                                                                // Destroy chart if exists (just in case)
                                                                if (chartInstance) {
                                                                    chartInstance.destroy();
                                                                }

                                                                chartInstance = new Chart(ctx, {
                                                                    type: 'line',
                                                                    data: {
                                                                        labels: labels,
                                                                        datasets: [{
                                                                            label: `Access Count (${labelRangeText})`,
                                                                            data: data,
                                                                            borderWidth: 2,
                                                                            borderColor: '#00a8ff',
                                                                            backgroundColor: 'rgba(0,168,255,0.1)',
                                                                            tension: 0.3,
                                                                            fill: true
                                                                        }]
                                                                    },
                                                                    options: {
                                                                        responsive: true,
                                                                        maintainAspectRatio: false
                                                                    }
                                                                });

                                                                insightsDiv.innerHTML = `
                    <h4>📊 Stats:</h4>
                    <ul>
                        <li><strong>Total:</strong> ${total}</li>
                        <li><strong>Max/Day:</strong> ${max}</li>
                        <li><strong>Min/Day:</strong> ${min}</li>
                        <li><strong>Average:</strong> ${avg}</li>
                    </ul>
                `;
                                                            })
                                                            .catch(err => {
                                                                insightsDiv.innerHTML = `<p style="color:red;">Error: ${err.message}</p>`;
                                                            });
                                                    }

                                                    // Show modal
                                                    modal.style.display = 'flex';
                                                    loadChart(filterDropdown.value);

                                                    // Replace old dropdown listener to avoid duplicates
                                                    const newDropdown = filterDropdown.cloneNode(true);
                                                    filterDropdown.parentNode.replaceChild(newDropdown, filterDropdown);
                                                    newDropdown.addEventListener('change', () => {
                                                        loadChart(newDropdown.value);
                                                    });

                                                    // Handle modal close
                                                    document.getElementById('closeAnalyzeModal').onclick = () => {
                                                        modal.style.display = 'none';

                                                        if (chartInstance) {
                                                            chartInstance.destroy();
                                                            chartInstance = null;
                                                        }

                                                        const currentCanvas = document.getElementById('accessedChart');
                                                        if (currentCanvas) {
                                                            currentCanvas.remove();
                                                        }

                                                        insightsDiv.innerHTML = '';
                                                    };

                                                    // Download report
                                                    document.getElementById('downloadReportBtn').onclick = () => {
                                                        fetch(`generate_access_report.php?qr_id=${qrId}&range=${newDropdown.value}`)
                                                            .then(res => res.blob())
                                                            .then(blob => {
                                                                const link = document.createElement('a');
                                                                link.href = window.URL.createObjectURL(blob);
                                                                link.download = `QR_Access_Report_${qrId}.pdf`;
                                                                link.click();
                                                            });
                                                    };
                                                });
                                            }

                                            // ======= Download Button =======
                                            if (downloadBtn) {
                                                const imageSrc = downloadBtn.dataset.image;
                                                const filename = downloadBtn.dataset.filename || 'qr_code.jpg';

                                                if (!imageSrc) {
                                                    alert("QR image not available.");
                                                    return;
                                                }

                                                const link = document.createElement('a');
                                                link.href = imageSrc;
                                                link.download = filename;
                                                document.body.appendChild(link);
                                                link.click();
                                                document.body.removeChild(link);
                                                return; // Stop further processing
                                            }


                                            // ======= Delete Button =======
                                            if (deleteBtn) {
                                                const qrId = deleteBtn.dataset.id;
                                                alert("Attempting to delete QR ID: " + qrId);
                                                if (confirm("Are you sure you want to delete this QR code?")) {
                                                    fetch('fetch_qr_data.php', {
                                                        method: 'POST',
                                                        headers: {
                                                            'Content-Type': 'application/x-www-form-urlencoded'
                                                        },
                                                        body: 'delete_id=' + encodeURIComponent(qrId)
                                                    })


                                                        .then(res => res.text())
                                                        .then(result => {
                                                            if (result.trim() === "success") {
                                                                alert("QR Code deleted successfully.");
                                                                location.reload();
                                                            } else {
                                                                alert("Failed to delete QR Code.");
                                                                console.error("Error deleting QR code:", err);
                                                            }
                                                        })
                                                        .catch(err => {
                                                            console.error("Error deleting QR code:", err);
                                                        });
                                                }
                                            }
                                        });

                                        // Close modal handler
                                        document.getElementById('closeModal').onclick = () => {
                                            document.getElementById('qrModal').style.display = 'none';
                                        };
                                    })
                                    .catch(err => {
                                        console.error("Error fetching QR table:", err);
                                    });
                            });
                        </script>




                        <!-- Aesthetic Table Section -->

                        <!-- FontAwesome for icons -->
                        <link rel="stylesheet"
                            href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

                        <!-- Modal -->
                        <div id="qrModal"
                            style="display:none; position:fixed; z-index:1000; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); overflow-y: auto;">

                            <div
                                style="background:white; width:90%; max-width:800px; max-height:fit-content; overflow-y:auto; margin:5% auto; padding:20px; border-radius:30px; position:relative; box-shadow:0 4px 20px rgba(0,0,0,0.3);">

                                <span id="closeModal"
                                    style="position:absolute; top:10px; right:20px; cursor:pointer; font-size:24px;">&times;</span>

                                <h3 style="text-align:center;">QR Code Details</h3>

                                <div id="modalContent" style="margin-top:20px;"></div>
                            </div>
                        </div>


                        <!-- QR Stats Table -->
                        <div class="qr-stats-table"
                            style="margin-top: 20px; margin-bottom:auto; width: 100%; margin-left: auto; margin-right: auto; font-family: 'Quicksand', sans-serif;">

                            <div class="analytics-dashboard">
                                <h2 style="text-align:left; font-family: 'Segoe UI', sans-serif; margin-bottom: 20px;">
                                    Receive QR Code<br>
                                </h2>
                                <small
                                    style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-weight: normal; text-align:left; font-size: 16px;">This
                                    table display the Qr code which has been shared with you through the system. You are
                                    allowed to view, scan and download qr code.</small>

                            </div>
                            <!-- Optional: search filter -->
                            <div style="margin-bottom: 10px; text-align: right; border-radius:40px;">
                                <input type="text" id="receiveSearchInput" placeholder="Search table..."
                                    style="padding: 6px; font-family: 'Quicksand', sans-serif; width: 250px;border-radius:40px;">
                            </div>

                            <!-- Styles -->
                            <style>
                                .receive-table-container {
                                    overflow-x: auto;
                                    width: 100%;
                                    margin-bottom: 60px;
                                    box-shadow: 0 40px 40px rgba(114, 7, 7, 0.1);
                                }

                                table#qrTable,
                                table#qrTableScroll {
                                    width: 100%;
                                    border-collapse: collapse;
                                    font-family: 'Quicksand', sans-serif;
                                    table-layout: fixed;
                                }

                                table#qrTable th,
                                table#qrTableScroll td {
                                    padding: 12px;
                                    text-align: center;
                                    justify-content: center;
                                }

                                table#qrTable thead {
                                    background-color: #f7f7f7;
                                    display: table;
                                    width: 100%;
                                    table-layout: fixed;
                                }

                                #qrTableScroll {
                                    display: block;
                                    max-height: 330px;
                                }

                                #qrTableScroll tbody {
                                    display: table;
                                    width: 100%;
                                    table-layout: fixed;
                                }

                                .action-btn {
                                    padding: 4px 8px;
                                    margin: 2px;
                                    border: none;
                                    border-radius: 4px;
                                    color: #fff;
                                    cursor: pointer;
                                }

                                .btn-view {
                                    background-color: #3498db;
                                }

                                .btn-delete {
                                    background-color: #e74c3c;
                                }

                                .btn-analyze {
                                    background-color: #e67e22;
                                }
                            </style>

                            <!-- Table Display -->
                            <div class="receive-table-container">
                                <table id="qrTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 150px;">Receive At</th>
                                            <th style="width: 100px;">QR Code Id</th>
                                            <th>QR Filename</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                </table>

                                <!-- Scrollable tbody -->
                                <div id="qrTableScroll">
                                    <table>
                                        <tbody id="qrTableBody2">
                                            <!-- Example row, replace with JS -->
                                            <tr>
                                                <td>2025-08-06 11:15:00</td>
                                                <td>qr_received_245324.png</td>
                                                <td>Enabled</td>
                                                <td>
                                                    <button class="action-btn btn-view">View</button>
                                                    <button class="action-btn btn-delete">Delete</button>
                                                    <button class="action-btn btn-analyze">Analyze</button>
                                                </td>
                                            </tr>
                                            <!-- More rows loaded by JS -->
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <!-- Filter Script -->
                            <script>
                                document.getElementById('receiveSearchInput').addEventListener('keyup', function () {
                                    const filter = this.value.toLowerCase();
                                    const rows = document.querySelectorAll('#qrTableBody2 tr');

                                    rows.forEach(row => {
                                        const rowText = row.textContent.toLowerCase();
                                        row.style.display = rowText.includes(filter) ? '' : 'none';
                                    });
                                });
                            </script>
                        </div>


                        <script>
                            document.addEventListener("DOMContentLoaded", function () {
                                fetch('fetch_shared_data.php')
                                    .then(res => res.text())
                                    .then(html => {
                                        const tbody = document.getElementById('qrTableBody2');
                                        tbody.innerHTML = html;

                                        // Event delegation for both View and Delete buttons
                                        tbody.addEventListener('click', function (e) {
                                            const viewBtn = e.target.closest('.view-btn');
                                            const deleteBtn = e.target.closest('.delete-btn');

                                            // ======= View Button =======
                                            if (viewBtn) {
                                                try {
                                                    const data = JSON.parse(viewBtn.dataset.details);
                                                    const content = `
                                                    <div style="display: flex; flex-wrap: wrap; gap: 30px; justify-content: center; align-items: flex-start;">

                                                        <!-- Left Column -->
                                                        <div style="flex: 1; min-width: 250px;">
                                                            <p style="font-size:14px;"><strong>ID:</strong> ${data.id}</p>
                                                            <p style="font-size:14px;"><strong>QR Filename:</strong> ${data.qr_filename}</p>

                                                            <p style="font-size:14px;"><strong>Receive At:</strong> ${data.shared_at}</p>
                                                            <br></br>
                                                             <div style="
                                                            flex: 1; 
                                                            min-width: 250px; 
                                                            display: flex; 
                                                            flex-direction: column; 
                                                            justify-content: center; 
                                                            align-items: center; 
                                                            text-align: center;
                                                        ">

                                                                                                                    <p style="color:black; font-size:16px;"><strong>Preview</strong></p><br>
                                                            <img id="qrImagePreview"
                                                                src="data:image/jpeg;base64,${data.qr_image_base64}"
                                                                alt="QR Code"
                                                                style="width: 250px; height: 250px; border: 1px solid #ccc; border-radius:30px; margin-bottom: 20px;">
                                                            
                                                            <button id="downloadBtn"
                                                                style="padding: 6px 12px; background-color: #27ae60; color: white; border: none; border-radius: 30px; cursor: pointer; margin-bottom: 10px;">
                                                                Download QR Code
                                                            </button>

                                                            <button id="scanBtn"
                                                                style="padding: 6px 12px; background-color: #dee200a8; color: white; border: none; border-radius: 30px; cursor: pointer;">
                                                                Scan QR Code
                                                            </button>
                                                        </div>

                                                        </div>
                                                    </div>
                                                    `;



                                                    document.getElementById('modalContent').innerHTML = content;
                                                    document.getElementById('qrModal').style.display = 'flex';

                                                    // Download Button
                                                    setTimeout(() => {
                                                        const downloadBtn = document.getElementById('downloadBtn');
                                                        const img = document.getElementById('qrImagePreview');
                                                        const scanBtn = document.getElementById('scanBtn'); // Make sure you add a button with this ID

                                                        if (downloadBtn && img) {
                                                            // Download button
                                                            downloadBtn.addEventListener('click', () => {
                                                                const link = document.createElement('a');
                                                                link.href = img.src;
                                                                link.download = data.qr_filename || 'qr_code.jpg';
                                                                document.body.appendChild(link);
                                                                link.click();
                                                                document.body.removeChild(link);
                                                            });
                                                        }

                                                        if (scanBtn && img) {
                                                            // Scan button
                                                            scanBtn.addEventListener('click', () => {
                                                                fetch(img.src)
                                                                    .then(res => res.blob())
                                                                    .then(blob => {
                                                                        const file = new File([blob], data.qr_filename || 'qr_code.jpg', { type: blob.type });

                                                                        // Create a form dynamically
                                                                        const form = document.createElement('form');
                                                                        form.method = 'POST';
                                                                        form.enctype = 'multipart/form-data';
                                                                        form.action = 'scan.php';

                                                                        // Create file input
                                                                        const fileInput = document.createElement('input');
                                                                        fileInput.type = 'file';
                                                                        fileInput.name = 'qr_upload';

                                                                        // Put the file inside DataTransfer (to simulate user file upload)
                                                                        const dt = new DataTransfer();
                                                                        dt.items.add(file);
                                                                        fileInput.files = dt.files;

                                                                        form.appendChild(fileInput);
                                                                        document.body.appendChild(form);

                                                                        // Submit form → move to scan.php with the file
                                                                        form.submit();
                                                                    });
                                                            });
                                                        }
                                                    }, 100);


                                                } catch (err) {
                                                    console.error("Failed to parse QR data:", err);
                                                }
                                            }


                                            // ======= Delete Button =======
                                            if (deleteBtn) {
                                                const qrId = deleteBtn.dataset.id;

                                                if (confirm("Are you sure you want to delete this QR code?")) {
                                                    fetch('fetch_qr_data.php', {
                                                        method: 'POST',
                                                        headers: {
                                                            'Content-Type': 'application/x-www-form-urlencoded'
                                                        },
                                                        body: 'delete_id=' + encodeURIComponent(qrId)
                                                    })
                                                        .then(res => res.text())
                                                        .then(result => {
                                                            if (result.trim() === "success") {
                                                                alert("QR Code deleted successfully.");
                                                                location.reload();
                                                            } else {
                                                                alert("Failed to delete QR Code.");
                                                            }
                                                        })
                                                        .catch(err => {
                                                            console.error("Error deleting QR code:", err);
                                                        });
                                                }
                                            }
                                        });

                                        // Close modal handler
                                        document.getElementById('closeModal').onclick = () => {
                                            document.getElementById('qrModal').style.display = 'none';
                                        };
                                    })
                                    .catch(err => {
                                        console.error("Error fetching QR table:", err);
                                    });
                            });
                        </script>

                        <div class="footer-note">&copy; 2025 SQ‑Tech Solver. All rights reserved.</div>

                    </div>

                    <!-- </section> -->
                </div>

            </div>
    </div>
    </section>
    </div>
    <!-- footer  -->





    <!-- javascript sw -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
    <script src="index.js"></script>
    <script src="stars.js"></script>
    <link rel="stylesheet" href="live-stars.css">
    <!-- Chatbase Embed -->
    <script>
        (function () {
            if (!window.chatbase || window.chatbase("getState") !== "initialized") {
                window.chatbase = (...arguments) => {
                    if (!window.chatbase.q) { window.chatbase.q = [] }
                    window.chatbase.q.push(arguments)
                };
                window.chatbase = new Proxy(window.chatbase, {
                    get(target, prop) {
                        if (prop === "q") { return target.q }
                        return (...args) => target(prop, ...args)
                    }
                })
            }

            const onLoad = function () {
                const script = document.createElement("script");
                script.src = "https://www.chatbase.co/embed.min.js";
                script.id = "fUxbSN5eUMhP3OoW4cHe_";
                script.domain = "www.chatbase.co";
                document.body.appendChild(script);
            };

            if (document.readyState === "complete") {
                onLoad();
            } else {
                window.addEventListener("load", onLoad);
            }
        })();
    </script>
    <script>
        async function identifyChatbaseUser() {
            const response = await fetch('/api/chatbase-token');
            const data = await response.json();

            if (data.token) {
                window.chatbase('identify', { token: data.token });
            }
        }

        window.addEventListener('load', identifyChatbaseUser);
    </script>


</body>

</html>