<?php
include "../componet/conn.php";
session_start();

// Check admin login
if (!isset($_SESSION["adminlogin"]) || $_SESSION["adminlogin"] == "adlogout") {
    header("location:admin-login.php");
    exit;
}

// --- Queries with proper error handling ---
function getTotalRows($con, $table)
{
    $query = "SELECT * FROM `$table`";
    $result = mysqli_query($con, $query);
    if (!$result) {
        die("Query failed for table `$table`: " . mysqli_error($con));
    }
    return mysqli_num_rows($result);
}

// Total counts
$totaluser = getTotalRows($con, "users");
$totalitems = getTotalRows($con, "products");
$totalcat = getTotalRows($con, "product-catagory");
$totalsubcat = getTotalRows($con, "sub-catagory");
// $totalorder = getTotalRows($con, "orders");
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <link rel="stylesheet" href="admin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<style>
    p {
        color: white;
    }

    .button {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        flex-wrap: nowrap;
        /* keep in one line */
        border-radius: 40px;
    }

    .button.btn {
        width: 20%;
        max-width: 20%;
        border: 2px #000;
        /* corrected border */
        border-radius: 40px;
        cursor: pointer;
        text-align: center;
        align-items: center;
        justify-content: center;
        padding: 10px 0;
    }

    .button.btn.b {
        background-color: black;
        color: black;
    }

    .button.btn.w {
        background-color: black;
        color: white;
    }

    .button.btn.m {
        background-color: black;
        color: black;
    }
</style>
<div id="body-pd">

    <!-- Header -->
    <header class="header" id="header">
        <div class="header_toggle">
            <i class='bx bx-menu' id="header-toggle"></i>
        </div>

        <div class="header-title">
            <h3>Analytic Dashboard</h3>
        </div>
        <div>Welcome <?php echo htmlspecialchars($_SESSION["name"]); ?></div>
    </header>

    <!-- Sidebar -->
    <div class="l-navbar" id="nav-bar">
        <nav class="nav">
            <div>
                <a href="admin-dashboard.php" class="nav_logo">
                    <i class='bx bx-layer nav_logo-icon'></i>
                    <span class="nav_logo-name">SQ-Tech SOLVER</span>
                </a>
                <div class="nav_list">
                    <a href="admin-dashboard.php" class="nav_link active">
                        <i class='bx bx-grid-alt nav_icon'></i>
                        <span class="nav_name">Dashboard</span>
                    </a>
                    <a href="user.php" class="nav_link">
                        <i class='bx bx-user nav_icon'></i>
                        <span class="nav_name">Users</span>
                    </a>
                    <a href="category.php" class="nav_link">
                        <i class='bx bx-bookmark nav_icon'></i>
                        <span class="nav_name">Info</span>
                    </a>

                </div>
            </div>
            <a href="admin-componet/logout.php" class="nav_link">
                <i class='bx bx-log-out nav_icon'></i>
                <span class="nav_name">SignOut</span>
            </a>
        </nav>
    </div>
    <br>
    <!-- Buttons -->
    <div class="button">
        <button type="button" class="button btn b" data-range="weekly">
            <p>Week</p>
        </button>
        <button type="button" class="button btn w" data-range="monthly">
            <p>Month</p>
        </button>
        <button type="button" class="button btn m" data-range="all">
            <p>Current Month</p>
        </button>
    </div>

    <!-- Dashboard Cards -->
    <div class="dashbord py-5">
        <div class="dash-card">
            <div class="dash-icons"><i class="fa-solid fa-user"></i></div>
            <div class="dash-desc">
                <ul>
                    <li class="dash-label" style="text-align:center;">Total Users</li>
                    <li class="dash-data" id="card-users">0</li>
                </ul>
            </div>
        </div>

        <div class="dash-card">
            <div class="dash-icons"><i class="fa-solid fa-qrcode"></i></div>
            <div class="dash-desc">
                <ul>
                    <li class="dash-label">Generated QR</li>
                    <li class="dash-data" id="card-generated">0</li>
                </ul>
            </div>
        </div>

        <div class="dash-card">
            <div class="dash-icons"><i class="fa-solid fa-lock"></i></div>
            <div class="dash-desc">
                <ul>
                    <li class="dash-label" style="display:flex;text-align:center;">Encrypted</li>
                    <li class="dash-data" id="card-encrypted">0</li>
                </ul>
            </div>
        </div>

        <div class="dash-card">
            <div class="dash-icons"><i class="fa-solid fa-share"></i></div>
            <div class="dash-desc">
                <ul>
                    <li class="dash-label" style="justify-content:center; align-items:center;">Shared</li>
                    <li class="dash-data" id="card-shared">0</li>
                </ul>
            </div>
        </div>

        <div class="dash-card">
            <div class="dash-icons"><i class="fa-solid fa-eye"></i></div>
            <div class="dash-desc">
                <ul>
                    <li class="dash-label">Accessed</li>
                    <li class="dash-data" id="card-accessed">0</li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Graph + Pie -->
    <div class="dashboard">
        <div class="dashbord dash-graph">
            <canvas id="lineChart"></canvas>
        </div>

        <div class="dashboard-container">
            <div class="dashboard dash-pie" style="background-color: darkgray;">
                <canvas id="donutChart"></canvas>
            </div>
            <br>
            <div class="dashboard dash-pie-desc" style="background-color: darkgray;">

                <div style="flex-grow:1;text-align:center;">
                    <ul style="list-style:none;margin:0;padding:0;">
                        <li style="font-size:22px;font-weight:600;color:white;">Top Category</li>
                        <li id="top-metric" style="font-size:22px;font-weight:700;color:black;">-</li>
                    </ul>
                </div>
            </div>


        </div>
    </div>

    <!-- Add Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>

    <script>
        let lineChart, donutChart;

        /// Fetch & Render Function
        function loadDashboard(range = 'all') {
            fetch('admin-fetchdata.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'range=' + range
            })
                .then(res => res.json())
                .then(data => {
                    // Flatten totals
                    let totals = {
                        Users: sumTotals(data.Users),
                        Generated: sumTotals(data.Generated),
                        Encrypted: sumTotals(data.Encrypted),
                        Shared: sumTotals(data.Shared),
                        Accessed: sumTotals(data.Accessed),
                    };

                    // ✅ Update dash-card numbers
                    document.getElementById('card-users').innerText = totals.Users;
                    document.getElementById('card-generated').innerText = totals.Generated;
                    document.getElementById('card-encrypted').innerText = totals.Encrypted;
                    document.getElementById('card-shared').innerText = totals.Shared;
                    document.getElementById('card-accessed').innerText = totals.Accessed;

                    // Build unique map of sort -> display label (period)
                    let periodMap = new Map();
                    for (let key in data) {
                        (data[key] || []).forEach(r => {
                            // if same sort appears from different metrics, keep the display label (first is fine)
                            if (!periodMap.has(r.sort)) periodMap.set(r.sort, r.period);
                        });
                    }

                    // Sort the keys (strings like '2025-07' / '202527' / '2025-09-03' sort correctly lexicographically)
                    let sortedKeys = Array.from(periodMap.keys()).sort();

                    // Build labels from sorted keys (chronological)
                    let labels = sortedKeys.map(k => periodMap.get(k));

                    // New extractData that keys by r.sort
                    function extractData(arr, sortedKeys) {
                        let map = {};
                        (arr || []).forEach(r => { map[r.sort] = parseInt(r.total) || 0; });
                        return sortedKeys.map(k => map[k] || 0);
                    }

                    // Example dataset construction (use tension for smoothing)
                    let datasets = [
                        { label: "Users", data: extractData(data.Users, sortedKeys), borderColor: "blue", fill: false, tension: 0.4, pointRadius: 3 },
                        { label: "Generated", data: extractData(data.Generated, sortedKeys), borderColor: "green", fill: false, tension: 0.4, pointRadius: 3 },
                        { label: "Encrypted", data: extractData(data.Encrypted, sortedKeys), borderColor: "purple", fill: false, tension: 0.4, pointRadius: 3 },
                        { label: "Shared", data: extractData(data.Shared, sortedKeys), borderColor: "orange", fill: false, tension: 0.4, pointRadius: 3 },
                        { label: "Accessed", data: extractData(data.Accessed, sortedKeys), borderColor: "red", fill: false, tension: 0.4, pointRadius: 3 },
                    ];

                    // then create Chart.js using `labels` and `datasets` as before


                    // ✅ Line Chart
                    if (lineChart) lineChart.destroy();
                    lineChart = new Chart(document.getElementById("lineChart"), {
                        type: "line",
                        data: { labels, datasets },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            plugins: {
                                legend: { position: "top" }
                            },
                            scales: {
                                x: { title: { display: true, text: (range === "weekly" ? "Week" : "Month") } },
                                y: { beginAtZero: true, title: { display: true, text: "Count" } }
                            }
                        }
                    });

                    // ✅ Donut Chart
                    let donutLabels = Object.keys(totals);
                    let donutData = Object.values(totals);

                    if (donutChart) donutChart.destroy();
                    donutChart = new Chart(document.getElementById("donutChart"), {
                        type: "doughnut",
                        data: {
                            labels: donutLabels,
                            datasets: [{
                                data: donutData,
                                backgroundColor: ["blue", "green", "purple", "orange", "red"]
                            }]
                        },
                        options: {
                            responsive: true,
                            plugins: {
                                legend: { position: "bottom" },
                                datalabels: {
                                    color: "#fff",
                                    font: {
                                        weight: "bold",
                                        size: 12
                                    },
                                    formatter: function (value, context) {
                                        // Show percentage instead of raw value
                                        let total = context.chart.data.datasets[0].data.reduce((a, b) => a + b, 0);
                                        let percentage = (value / total * 100).toFixed(1) + "%";
                                        return percentage;
                                    }
                                }
                            }
                        },
                        plugins: [ChartDataLabels] // register plugin
                    });


                    // ✅ Biggest %
                    let maxVal = Math.max(...donutData);
                    let maxIdx = donutData.indexOf(maxVal);
                    document.getElementById("top-metric").innerText =
                        donutLabels[maxIdx] + " (" + ((maxVal / donutData.reduce((a, b) => a + b, 0)) * 100).toFixed(1) + "%)";
                });
        }

        // Helpers
        function sumTotals(arr) {
            return arr.reduce((sum, r) => sum + parseInt(r.total), 0);
        }
        function extractData(arr, labels) {
            let map = {};
            arr.forEach(r => map[r.period] = parseInt(r.total));
            return labels.map(l => map[l] || 0);
        }

        // Button Events
        document.querySelectorAll(".button button").forEach(btn => {
            btn.addEventListener("click", () => {
                let range = btn.getAttribute("data-range");
                loadDashboard(range);
            });
        });



        // Initial load
        loadDashboard("all");
    </script>



    <div class="dashboard">
        <div class="dashboard-container">
            <!--  -->
            <br>

            <!-- Dash Pie Desc = Top tracked IP -->
            <div class="dashboard dash-desc" id="dashPieDesc"
                style="background-color: darkgray; margin-right:0px;height:200px; min-height:490px; justify-content: center; align-items:center;">
                <div
                    style="border-radius:40px; padding:20px; text-align:center; width:260px; margin:10px; background:#f9f9f9; box-shadow:0 4px 8px rgba(0,0,0,0.1);">
                    <p style="margin:0; font-weight:bold; font-size:18px; color:#182848;">Total Activity</p>
                    <!-- <hr style="width:60%; margin:10px auto; border:1px solid #000000ff; justify-content:center; align-items:center;"> -->
                    <p style="margin:0; font-size:26px; font-weight:bold; color:#4b6cb7;">
                        <?php
                        $result = $con->query("SELECT COUNT(*) as total FROM activity");
                        $row = $result->fetch_assoc();
                        echo $row['total'];
                        ?>
                    </p>
                    <br>
                    <p style="margin:0; font-weight:bold; font-size:18px; color:#182848;">Total Email Request (Verification)</p>
                    <!-- <hr style="width:60%; margin:10px auto; border:1px solid #000000ff; justify-content:center; align-items:center;"> -->
                    <p style="margin:0; font-size:26px; font-weight:bold; color:#4b6cb7;">
                        <?php
                        $result = $con->query("SELECT COUNT(*) as total FROM code");
                        $row = $result->fetch_assoc();
                        echo $row['total'];
                        ?>
                    </p>
                    <br>
                    <p style="margin:0; font-weight:bold; font-size:18px; color:#182848;">Total Notification</p>
                    <!-- <hr style="width:60%; margin:10px auto; border:1px solid #000000ff; justify-content:center; align-items:center;"> -->
                    <p style="margin:0; font-size:26px; font-weight:bold; color:#4b6cb7;">
                        <?php
                        $result = $con->query("SELECT COUNT(*) as total FROM notification");
                        $row = $result->fetch_assoc();
                        echo $row['total'];
                        ?>
                    </p>
                    <br>
                    <p style="margin:0; font-weight:bold; font-size:18px; color:#182848;">Total Activity</p>
                    <!-- <hr style="width:60%; margin:10px auto; border:1px solid #000000ff; justify-content:center; align-items:center;"> -->
                    <p style="margin:0; font-size:26px; font-weight:bold; color:#4b6cb7;">
                        <?php
                        $result = $con->query("SELECT COUNT(*) as total FROM feedback");
                        $row = $result->fetch_assoc();
                        echo $row['total'];
                        ?>
                    </p>

                </div>

            </div>

        </div>

        <!-- Dash Graph = Map -->
        <div class="dashbord dash-graph" id="dashGraph" style="background-color: dark-grey; margin-right: 20px;">
            <!-- Last Updated -->
            <!-- Last Updated -->
            <!-- Container -->
            <div style="display:block; width:100%;">

                <style>
                    /* Make all tables borderless */
                    table {
                        border-collapse: collapse;
                        /* optional but cleaner */
                        border: none;
                    }

                    /* Remove borders from all table headers and cells */
                    table th,
                    table td {
                        border: none;
                        padding: 10px;
                        /* optional: keeps spacing inside cells */
                    }
                </style>

                <!-- Last Updated -->
                <div id="lastUpdated" style="display:block; color:black; font-size:14px; text-align:center; 
                margin:10px 0 8px 10px; font-weight:bold; width:100%;">
                    Last updated: <?php echo date("Y-m-d H:i:s"); ?>
                </div>

                <div style="display:block; max-height:400px; overflow-y:auto; background: dark-grey; 
     padding:12px; margin:0 10px 15px 10px; 
     box-shadow:0 4px 8px rgba(255, 255, 255, 0.2); width:calc(100% - 20px);">

                    <table style="width:100%; text-align:left; font-size:15px;">
                        <thead>
                            <tr
                                style="background:linear-gradient(90deg,#4b6cb7,#182848); color:white;  top:0; z-index:1;">
                                <th style="text-align:center;">Timestamp</th>
                                <th style="text-align:center;">User ID</th>
                                <th style="text-align:center;">Message</th>
                            </tr>
                        </thead>
                        <tbody id="activityBody">
                            <!-- Data will load here -->
                        </tbody>
                    </table>
                </div>


            </div>

        </div>

        <script>
            function loadActivity() {
                var xhr = new XMLHttpRequest();
                xhr.open("GET", "get_activity.php", true);
                xhr.onreadystatechange = function () {
                    if (xhr.readyState === 4 && xhr.status === 200) {
                        document.getElementById("activityBody").innerHTML = xhr.responseText;
                        document.getElementById("lastUpdated").innerText =
                            "Last updated: " + new Date().toLocaleString();
                    }
                };
                xhr.send();
            }

            // Load immediately, then every 5 sec
            loadActivity();
            setInterval(loadActivity, 5000);
        </script>


    </div>




    <!-- Navbar Toggle Script -->
    <script>
        document.addEventListener("DOMContentLoaded", function (event) {
            const showNavbar = (toggleId, navId, bodyId, headerId) => {
                const toggle = document.getElementById(toggleId),
                    nav = document.getElementById(navId),
                    bodypd = document.getElementById(bodyId),
                    headerpd = document.getElementById(headerId);

                if (toggle && nav && bodypd && headerpd) {
                    toggle.addEventListener('click', () => {
                        nav.classList.toggle('show');
                        toggle.classList.toggle('bx-x');
                        bodypd.classList.toggle('body-pd');
                        headerpd.classList.toggle('body-pd');
                    });
                }
            }

            showNavbar('header-toggle', 'nav-bar', 'body-pd', 'header');

            const linkColor = document.querySelectorAll('.nav_link');
            function colorLink() {
                linkColor.forEach(l => l.classList.remove('active'));
                this.classList.add('active');
            }
            linkColor.forEach(l => l.addEventListener('click', colorLink));
        });
    </script>
    </body>

</html>