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

        .dashboard-wrapper {
            padding: 30px;
        }

        .card-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 20px;
        }

        .feature-card {
            flex: 1 1 22%;
            background-color: #fff;
            padding: 25px;
            border-radius: 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
            min-width: 230px;
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
            color: #555;
            line-height: 1.5;
        }

        @media (max-width: 768px) {
            .feature-card {
                flex: 1 1 45%;
            }
        }

        @media (max-width: 480px) {
            .feature-card {
                flex: 1 1 100%;
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
            color: #1f2937;
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
        }

        /* The individual card for each chart */
        .chart-card {
            flex: 1 1 350px;
            /* Allows cards to grow and shrink */
            min-width: 300px;
            /* Minimum width before wrapping */
            max-width: 100%;
            background: linear-gradient(135deg, #8ee3ef, #ffd4bf);
            border: 1px solid #e5e7eb;
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
            color: #374151;
        }

        /* Style for the filter dropdown */
        .chart-filter {
            padding: 6px 10px;
            border-radius: 8px;
            border: 1px solid #d1d5db;
            background-color: #f9fafb;
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
            border-radius: 20px;
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
        }

        table#qrTable thead {
            position: sticky;
            background: linear-gradient(135deg, #8ee3ef, #ffd4bf);
            display: table;
            width: 100%;
            table-layout: fixed;

        }

        #qrTableScroll {
            display: block;
            background-color: lightcyan;
            max-height: 330px;
            /* About 8 rows height */
            /* overflow-y: auto; */
        }

        #qrTableScroll tbody {

            display: table;
            background-color: lightcyan;
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
        <link rel="icon" type="image/png" href="img/log.png">
</head>

<body class="animated-bg">


    <?php include("componet/navbar.php"); ?>
    <div class="main-body">
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
            <div class="bigcontainer">
                <div class="cat-products">
                    <div class="products-cards">
                        <div class="container">
                            <h1
                                style="font-family: 'Inter', sans-serif; font-weight:bold; color: black; font-size:40px;text-align: left;margin-top:20px; margin-bottom:20px;">
                                Welcome to the Secured & Trusted QR Code Innovation Center!<br>
                            </h1>
                            <small
                                style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;font-weight: bold; font-size:16px;display: block; margin-top: 10px; ">
                                Developed by SQ-TechSolver@2025
                            </small>
                            <small
                                style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;font-weight: normal; font-size:16px;display: block;  margin-bottom:10px;">
                                These features have been made available for you:
                            </small>

                            <div class="dashboard-wrapper">
                                <div class="card-grid">

                                    <!-- Generate QR Code Card -->
                                    <div class="feature-card">
                                        <i class="fas fa-qrcode feature-icon"></i>
                                        <div class="feature-title">Generate QR Code</div>
                                        <div class="feature-description">
                                            User can generate secure QR code with strong encryption algorithm and manage
                                            it in the home page.
                                        </div>
                                        <br>
                                        <div class="form-acc"
                                            style="display: flex; justify-content: center; align-items: center; margin: 20px;">
                                            <a href="products.php"
                                                style="color:blue; font-size:13px; font-weight:bold; text-align: center;">Click Here
                                                For Generate Secure QR Code.</a>
                                        </div>
                                    </div>

                                    <!-- Scan QR Code Card -->
                                    <div class="feature-card">
                                        <i class="fas fa-barcode feature-icon"></i>
                                        <div class="feature-title">Scan QR Code</div>
                                        <div class="feature-description">
                                            Users can scan QR codes and view their content. They can either scan the QR
                                            code from their phone or directly using the system.
                                        </div>
                                        <br>
                                        <div class="form-acc"
                                            style="display: flex; justify-content: center; align-items: center; margin-left:20px; margin-right:20px; margin-bottom: 23px;">
                                            <a href="scan.php"
                                                style="color:blue; font-size:13px; font-weight:bold; text-align: center;">Click Here
                                                For Scan QR Code.</a>
                                        </div>
                                    </div>

                                    <!-- Share QR Code Card -->
                                    <div class="feature-card">
                                        <i class="fas fa-share-alt feature-icon"></i>
                                        <div class="feature-title">Share QR Code</div>
                                        <div class="feature-description">
                                            User can share QR code through email, share with friends within the system,
                                            or
                                            download and share externally.
                                        </div>
                                        <br>
                                        <div class="form-acc"
                                            style="display: flex; justify-content: center; align-items: center; margin: 20px;">
                                            <a href="#generate-section"
                                                style="color:blue; font-size:13px; font-weight:bold; text-align: center;">
                                                Click Here For Share QR Code.
                                            </a>

                                        </div>
                                    </div>

                                    <!-- Manage & Analyze QR Code Card -->
                                    <div class="feature-card">
                                        <i class="fas fa-chart-line feature-icon"></i>
                                        <div class="feature-title">Manage & Analyze</div>
                                        <div class="feature-description">
                                            User can manage generated QR codes and analyze how many people scan them via
                                            analytical
                                            insights on the homepage.
                                        </div>
                                        <br>
                                        <div class="form-acc"
                                            style="display: flex; justify-content: center; align-items: center; margin: 20px;">
                                            <a href="#generate-section"
                                                style="color:blue; font-size:13px; font-weight:bold; text-align: center;">
                                                Click Here For Manage QR Code.
                                            </a>
                                        </div>
                                    </div>

                                </div>
                            </div>
                            <hr style="border: none; height: 2px; background-color: #858484ff; margin-top: 40px; margin-bottom: 40px; border-radius: 5px;">

                            <h2 style="text-align:left; font-family: 'Segoe UI', sans-serif;  font-size: 1.75rem;
                            font-weight: 600;margin-bottom: 20px;">
                                Analytic Dashboard<br>
                            </h2>


                            <small
                                style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;font-weight: normal; font-size:16px;display: block; margin-top: 10px; margin-bottom:80px;">
                                Your trusted platform for generating secure and reliable QR codes. <br>
                                Experience a seamless, systematic QR code management solution designed to attract users
                                with confidence and convenience. <br>
                                We don't just generate, we educate. Learn how to protect yourself from QR code fraud and
                                stay ahead with smart, secure scanning.
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
                                        <h4 class="card-title">Generated / Shared / Received</h4>
                                        <select id="accessFilter1" class="chart-filter">
                                            <option value="all">All Time</option>
                                            <option value="7">Last 7 Days</option>
                                            <option value="30">Last 30 Days</option>
                                        </select>
                                    </div>
                                    <div class="chart-body">
                                        <canvas id="barChart"></canvas>
                                    </div>
                                </div>

                                <div class="chart-card">
                                    <div class="card-header">
                                        <h4 class="card-title">Stats Overview (Line)</h4>
                                        <select id="accessFilter2" class="chart-filter">
                                            <option value="all">All Time</option>
                                            <option value="7">Last 7 Days</option>
                                            <option value="30">Last 30 Days</option>
                                        </select>
                                    </div>
                                    <div class="chart-body">
                                        <canvas id="lineChart"></canvas>
                                    </div>
                                </div>

                                <div class="chart-card">
                                    <div class="card-header">
                                        <h4 class="card-title">Daily Scans</h4>
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
                                                    backgroundColor: ['#3b82f6', '#10b981', '#f59e0b'],
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
                                                    borderColor: '#6366f1',
                                                    backgroundColor: 'rgba(99, 102, 241, 0.1)',
                                                    fill: true,
                                                    tension: 0.3,
                                                    pointBackgroundColor: '#6366f1'
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
                                                    borderColor: '#ef4444',
                                                    backgroundColor: 'rgba(239, 68, 68, 0.1)',
                                                    fill: true,
                                                    tension: 0.3,
                                                    pointBackgroundColor: '#ef4444'
                                                }]
                                            },
                                            options: commonChartOptions
                                        });
                                    });
                            }

                            // Individual filter handlers
                            document.getElementById('accessFilter1').addEventListener('change', function () {
                                loadBarChart(this.value);
                            });

                            document.getElementById('accessFilter2').addEventListener('change', function () {
                                loadLineChart(this.value);
                            });

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
                                style=" background: linear-gradient(135deg, #8ee3ef, #ffd4bf); width:90%; max-width:800px; max-height:fit-content; overflow-y:auto; margin:5% auto; padding:20px; border-radius:50px; position:relative; box-shadow:0 4px 20px rgba(0,0,0,0.3);">

                                <span id="closeModal"
                                    style="position:absolute; top:10px; right:20px; cursor:pointer; font-size:24px;">&times;</span>

                                <h3 style="text-align:center; font-size: 17px; font-weight: bold;">QR Code Details</h3>

                                <div id="modalContent" style="margin-top:20px;"></div>
                            </div>
                        </div>

                        <!-- Analyze Modal -->
                        <!-- Analyze Modal -->
                        <div id="analyzeModal"
                            style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.6); justify-content:center; align-items:center; z-index:1000;">
                            <div class="chart-container"
                                style="background: linear-gradient(135deg, #8ee3ef, #ffd4bf); padding:20px; border-radius:30px; max-width:800px; width:90%; max-height:90vh; overflow-y:auto;">
                                <h2 style="text-align:center; font-size:17px; font-weight: bold;">QR Code Access
                                    Analysis</h2>

                                <!-- Filter Range -->
                                <div style="margin-top:10px; text-align:center; font-size:14px;">
                                    <label for="filterRange">Select Range: </label>
                                    <select id="filterRange"
                                        style="padding:5px 10px; margin:5px;  font-size:14px; border-radius:20px;">
                                        <option value="all" selected>All Time</option>
                                        <option value="7">Last 7 Days</option>
                                        <option value="30">Last 30 Days</option>
                                    </select>
                                </div>

                                <!-- Canvas will be added dynamically -->
                                <canvas id="accessedChart" style="width:100%; height:300px;"></canvas>

                                <div id="insights" style="margin-top:20px; font-size:15px;"></div>

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
                        <small
                            style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;font-weight: normal; font-size:16px;display: block; text-align: left; margin-left: 10px; margin-right:10px; margin-bottom:10px; white-space: normal; word-break: break-word;">
                            See how many QR codes you’ve created, shared, and received and how many people have scans
                            your QR codes every day.
                            Every line and curve on the chart tells your story of how actively you’re connecting through
                            this QR Code System.
                        </small>
                        


                        <!-- Load Chart.js -->
                        <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>



                        <!-- QR Stats Table -->
                        <!-- QR Stats Table -->
             
                        <div class="qr-stats-table"
                            style="width: 100%; margin-left: auto; margin-right: auto; font-family: 'Quicksand', sans-serif;">

                            <div class="analytics-dashboard">
                                <h2 style="text-align:left; font-family: 'Segoe UI', sans-serif;margin-bottom: 20px;">
                                    Manage QR Code<br>
                                </h2>
                                <small
                                    style="font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-weight: normal; text-align:left; font-size: 16px;">You
                                    can manage your QR code here. You are allowed to view, scan, download,
                                    share, analyze and delete your qr code.</small>
                            </div>

                            <!-- Search Filter -->
                            <div style="margin-bottom: 10px; text-align: right; border-radius:40px;">
                                <input type="text" id="qrSearchInput" placeholder="Search table..."
                                    style="padding: 6px; font-family: 'Quicksand', sans-serif; width: 250px; border-radius:40px;">
                            </div>

                            <!-- Styles -->

                            <!-- Scrollable Table -->
                            <div class="scroll-table-container">
                                <table id="qrTable">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>QR Filename</th>
                                            <th>QR Code Id</th>
                                            <th>QR Code Type</th>
                                            <th>Actions</th>
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
                                                <td>qr_59_1754237535_38f17d03.png</td>
                                                <td>Disabled</td>
                                                <td>
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
                                            const viewBtn = e.target.closest('.view-btn');
                                            const analyzeBtn = e.target.closest('.analyze-btn');
                                            const downloadBtn = e.target.closest('.download-btn');
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
                                                            <p style="font-size:14px;"><strong>Created At:</strong> ${data.created_at}</p>
                                                            <br>
                                                            <p style="color:red; font-size:14px;"><strong>Enter email for verification process. Verification is compulsory for allowing user to scan and access qr code through one time passcode (otp). It not reuirement for user who access the Qr code through shared password.</strong></p><br>
                                                            <div>
                                                                <input type="email" id="emailField" placeholder="Enter email..." 
                                                                    style="padding: 6px; width: 100%; border-radius:30px;" />
                                                                <button id="emailSendBtn" title="Send to Email"
                                                                    style="padding: 6px 10px; margin-top: 10px; background-color: #6c5ce7; color: white; border: none; border-radius:30px; cursor: pointer;">
                                                                    Verify Email
                                                                </button>
                                                            </div>
                                                            <br>
                                                            <p style="color:Blue; font-size:14px;"><strong>User can choose and click button to send Qr Code to specific email or share the qr code through system.</strong></p>
                                                            
                                                            <div style="margin-top: 20px;">
                                                                <button id="transferEmailBtn"
                                                                    style="padding: 6px 12px; background-color: #0984e3; color: white; border: none; border-radius:30px; width: 100%;">
                                                                    📧 Share To Email
                                                                </button>

                                                                <div id="emailTransferForm" style="display:none; margin-top:10px;">
                                                                    <input type="email" id="transferEmailInput" placeholder="Enter recipient email" 
                                                                        style="padding:6px; width:100%; border-radius:30px;" />
                                                                    <button id="submitEmailTransfer"
                                                                        style="padding:6px 10px; margin-top: 8px; background-color:#00b894; color:white; border:none; border-radius:30px; ">
                                                                        Send
                                                                    </button>
                                                                </div>
                                                            </div>

                                                            <div style="margin-top: 20px;">
                                                                <button id="shareWithinAccountBtn"
                                                                    style="padding: 6px 12px; background-color: #fdcb6e; color: white; border: none; border-radius:30px; width: 100%;">
                                                                    👥 Share To Your Friends
                                                                </button>

                                                                <div id="friendShareForm" style="display:none; margin-top: 10px;">
                                                                    <select id="friendListDropdown" style="padding:6px; width:100%;"></select>
                                                                    <button id="submitFriendShare"
                                                                        style="padding:6px 10px; margin-top: 8px; background-color:#e17055; color:white; border:none; border-radius:30px;width: auto;">
                                                                        Share
                                                                    </button>
                                                                </div>
                                                            </div>
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
                                                            
                                                            <button id="downloadBtn"
                                                                style="padding: 6px 12px; background-color: #27ae60; color: white; border: none; border-radius: 30px; cursor: pointer; margin-bottom: 10px;">
                                                                Download QR Code
                                                            </button>

                                                            <button id="scanBtn"
                                                                style="padding: 6px 12px; background-color:#dee200a8; color: white; border: none; border-radius: 30px; cursor: pointer;">
                                                                Scan QR Code
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
                                                        // Transfer through Email
                                                        document.getElementById('transferEmailBtn').onclick = () => {
                                                            document.getElementById('emailTransferForm').style.display = 'block';
                                                        };

                                                        document.getElementById('submitEmailTransfer').onclick = () => {
                                                            const recipientEmail = document.getElementById('transferEmailInput').value.trim();
                                                            if (!recipientEmail) {
                                                                alert("Please enter a valid email address.");
                                                                return;
                                                            }

                                                            fetch('share_qr_email.php', {
                                                                method: 'POST',
                                                                headers: { 'Content-Type': 'application/json' },
                                                                body: JSON.stringify({
                                                                    email: recipientEmail,
                                                                    qr_id: data.id
                                                                })
                                                            })
                                                                .then(res => res.json())
                                                                .then(response => {
                                                                    alert(response.message || "QR Code transferred via email.");
                                                                    document.getElementById('transferEmailInput').value = ''; // Clear email input
                                                                    document.getElementById('emailTransferForm').style.display = 'none'; // Hide form
                                                                })
                                                                .catch(err => {
                                                                    console.error('Email transfer failed:', err);
                                                                    alert("Failed to send email.");
                                                                });
                                                        };

                                                        // Share within Account
                                                        document.getElementById('shareWithinAccountBtn').onclick = () => {
                                                            document.getElementById('friendShareForm').style.display = 'block';

                                                            fetch('get_friends_list.php')
                                                                .then(res => res.json())
                                                                .then(friends => {
                                                                    const dropdown = document.getElementById('friendListDropdown');
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
                                                                })
                                                                .catch(err => {
                                                                    console.error("Failed to load friends list:", err);
                                                                });
                                                        };

                                                        document.getElementById('submitFriendShare').onclick = () => {
                                                            const friendId = document.getElementById('friendListDropdown').value;
                                                            if (!friendId) {
                                                                alert("Please select a friend.");
                                                                return;
                                                            }

                                                            fetch('share_qr_friends.php', {
                                                                method: 'POST',
                                                                headers: { 'Content-Type': 'application/json' },
                                                                body: JSON.stringify({
                                                                    friend_id: friendId,
                                                                    qr_id: data.id
                                                                })
                                                            })
                                                                .then(res => res.json())
                                                                .then(response => {
                                                                    alert(response.message || "QR Code shared successfully.");
                                                                    document.getElementById('friendShareForm').style.display = 'none'; // Hide form
                                                                })
                                                                .catch(err => {
                                                                    console.error('QR share failed:', err);
                                                                    alert("Failed to share QR code.");
                                                                });
                                                        };


                                                        // Email Send Button
                                                        const emailBtn = document.getElementById('emailSendBtn');
                                                        if (emailBtn) {
                                                            emailBtn.addEventListener('click', () => {
                                                                const email = document.getElementById('emailField').value.trim();
                                                                if (!email) {
                                                                    alert("Please enter a valid email address.");
                                                                    return;
                                                                }

                                                                fetch('send_qr_email.php', {
                                                                    method: 'POST',
                                                                    headers: { 'Content-Type': 'application/json' },
                                                                    body: JSON.stringify({
                                                                        email: email,
                                                                        qr_id: data.id
                                                                    })
                                                                })
                                                                    .then(res => res.text())
                                                                    .then(response => {
                                                                        alert(response);
                                                                        document.getElementById('emailField').value = '';
                                                                    })
                                                                    .catch(err => {
                                                                        console.error('Email send failed:', err);
                                                                        alert("Failed to send email.");
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
                                    border-radius: 20px;
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
                                            <th>Receive At</th>
                                            <th>QR Filename</th>
                                            <th>QR Code Id</th>
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


</body>

</html>