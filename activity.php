<?php
include_once("componet/conn.php");
session_start();
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
$getUserQuery = "SELECT id FROM users WHERE name = '$name' LIMIT 1";
$userResult = mysqli_query($con, $getUserQuery);
$userRow = mysqli_fetch_assoc($userResult);
$userId = $userRow['id'];
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

        .activity-card {
            border: 1px solid #ccc;
            border-radius: 40px;
            padding: 15px;
            background: linear-gradient(90deg, rgba(255, 255, 255, 1) 35%, rgba(194, 192, 192, 1) 100%);
            box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.4);
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border:1px solid black;
        }

        .activity-message {
            font-size: 16px;
            color: #333;
            flex: 1;
        }

        .activity-time {
            font-size: 14px;
            color: #777;
            white-space: nowrap;
            margin-left: 15px;
        }

        .filter-container {
            text-align: right;
            margin-bottom: 10px;
        }

        .filter-container select {
            padding: 5px;
        }

        .load-more-btn {
            background: #007bff;
            color: white;
            padding: 8px 14px;
            border: none;
            border-radius: 30px;
            cursor: pointer;
            margin-top: 10px;
        }
    </style>
</head>

<body>
              <div id="stars"></div>
          <div id="stars2"></div>
          <div id="stars3"></div>
    <?php include("componet/navbar.php"); ?>
<h2 class="case-title"
                        style="display: flex; margin-top:15px;margin-bottom: 0px; font-family: 'Segoe UI', sans-serif; font-size:26px; justify-content:center; align-items:center; font-weight:bold;">
                        Activity</h2>

    <div class="main-body">
        <section>
            <div class="bigcontainer">
                <div class="cat-products" style="margin:20px 20px; ">
                    <!-- <h2 class="case-title"
                        style="display: flex; font-family: 'Segoe UI', sans-serif; font-size:26px; justify-content:center; align-items:center; font-weight:bold;">
                        Activity</h2> -->

                    <div class="filter-container">
                        <select id="filterRange"
                            style="border-radius: 30px; background:white; color:black;border: 1px solid black;">
                            <option value="7">Last 7 Days</option>
                            <option value="30">Last 30 Days</option>
                            <option value="all">All Time</option>
                        </select>
                    </div>

                    <div id="activityList"></div>
                    <div style="text-align:center; margin-top:15px;">
                        <button id="loadMoreBtn" class="load-more-btn"
                            style="background:#007bff; color:white; padding:8px 14px; border:none; border-radius:40px; cursor:pointer; font-size:14px;">
                            Load More
                        </button>
                    </div>

                    <div class="footer-note">&copy; 2025 SQ-Tech Solver. All rights reserved.</div>
                </div>
            </div>
        </section>
    </div>

    <script>
        let offset = 0;
        let limit = 7;
        let currentFilter = "7";
        let allLoaded = false;

        function loadActivities(reset = false) {
            if (reset) {
                // Reset everything
                offset = 0;
                allLoaded = false;
                document.getElementById('activityList').innerHTML = "";
                document.getElementById('loadMoreBtn').style.display = "inline-block"; // show again
            }

            if (allLoaded) return;

            // Add timestamp to prevent cached responses
            fetch(`load_activity.php?offset=${offset}&limit=${limit}&filter=${currentFilter}&_=${Date.now()}`)
                .then(res => res.text())
                .then(data => {
                    // If no data returned, hide Load More
                    if (data.trim() === "" || data.includes("No activity")) {
                        document.getElementById('loadMoreBtn').style.display = "none";
                        allLoaded = true;
                    } else {
                        document.getElementById('activityList').insertAdjacentHTML('beforeend', data);
                        offset += limit;
                    }
                })
                .catch(err => console.error("Error loading activities:", err));
        }

        // Handle button click
        document.getElementById('loadMoreBtn').addEventListener('click', () => {
            loadActivities();
        });

        // Handle filter change
        document.getElementById('filterRange').addEventListener('change', function () {
            currentFilter = this.value;
            loadActivities(true); // full reset + new data
        });

        // Initial load
        loadActivities();
    </script>

    <!-- javascript sw -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
    <script src="index.js"></script>
           <script src="stars.js"></script>
    <link rel="stylesheet" href="live-stars.css">
</body>

</html>