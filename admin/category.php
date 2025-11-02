<?php include "../componet/conn.php" ?>
<?php
session_start();
if (!isset($_SESSION["adminlogin"])) {
    header("location:admin-login.php");
}
?>
<?php
$sq1 = "SELECT * FROM users";
$sq1exc = mysqli_query($con, $sq1);
$totaluser = mysqli_num_rows($sq1exc);


$sq2 = "SELECT * FROM products";
$sq2exc = mysqli_query($con, $sq2);
$totalitems = mysqli_num_rows($sq2exc);
?>
<html>

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/boxicons@latest/css/boxicons.min.css">
    <link rel="stylesheet" href="admin-style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        table {
            align-items: center;
            margin-top: 50px;
            margin-bottom: 30px;
            /* background-color: #333; */
            color: black;
            font-size: 20px;
            /* box-shadow: 15px 15px 4px yellowgreen; */
        }

        .t_hading {
            height: 40px;
            text-align: center;
        }

        .t_hading th {
            text-align: center;
            border-bottom: 1px rgb(217, 220, 219) solid;
            font-size: 19px;
        }

        .t_body tr td {
            padding: 10px 30px;
            text-align: center;
            border-bottom: 0.1px rgb(217, 220, 219) solid;
            font-size: 18px;
        }

        .t_body tr td i {
            padding: 10px 15px;
            color: blue;
        }
    </style>
</head>

<body>

    <body id="body-pd">
        <nav>
            <header class="header" id="header">
                <div class="header_toggle"> <i class='bx bx-menu' id="header-toggle"></i> </div>
                <div class="header-title">
                    <h3>About</h3>
                </div>
                <div>Welcome <?php echo $_SESSION["name"] ?></div>
            </header>
            <div class="l-navbar" id="nav-bar">
                <nav class="nav">
                    <div>
                        <a href="admin-dashboard.php" class="nav_logo"> <i class='bx bx-layer nav_logo-icon'></i> <span
                                class="nav_logo-name">SQ-Tech SOLVER</span> </a>
                        <div class="nav_list">
                            <a href="admin-dashboard.php" class="nav_link "> <i class='bx bx-grid-alt nav_icon'></i>
                                <span class="nav_name">Dashboard</span> </a>
                            <a href="user.php" class="nav_link"> <i class='bx bx-user nav_icon'></i> <span
                                    class="nav_name">Users</span> </a>
                            <a href="category.php" class="nav_link active"> <i class='bx bx-bookmark nav_icon'></i>
                                <span class="nav_name">Info</span> </a>
                        </div>
                    </div>
                    <a href="admin-componet/logout.php" class="nav_link"> <i class='bx bx-log-out nav_icon'></i> <span
                            class="nav_name">SignOut</span> </a>
                </nav>
            </div>
        </nav>
        <!-- Container Main Start -->
<div class="container py-5">
    <div class="row">
        <!-- Left Dashboard (70%) -->
        <div class="col-lg-8 mb-4">
            <div class="p-4 shadow-sm rounded-4" 
                 style="background: black; color: white; min-height: 600px; border-radius:40px;">
                <h3 class="mb-3 text-center">About the System</h3>
                <ul class="list-unstyled fs-5">
                    <li>✔ This system securely manages QR codes for multiple use cases.</li>
                    <li>✔ Users can generate, share, and access QR codes with layered security.</li>
                    <li>✔ Supports OTP, password, and encryption for maximum protection.</li>
                    <li>✔ Friend-sharing and email transfer options are built-in.</li>
                    <li>✔ Real-time analytics and charts for QR access tracking.</li>
                    <li>✔ Fully responsive dashboard with easy navigation.</li>
                </ul>
            </div>
        </div>

        <!-- Right Dashboard (30%) -->
        <div class="col-lg-4">
            <div class="p-4 shadow-sm rounded-4" 
                 style="background: darkgrey; min-height: 600px; border-radius:40px;">
                <h4 class="mb-3 text-center">User Feedback</h4>
                <div
                        style="display:flex; font-family: 'Gill Sans', 'Gill Sans MT', Calibri, 'Trebuchet MS', sans-serif; font-weight: bold; font-size: 25px;">
                        <p>
                            <?php
                            $result = $con->query("SELECT COUNT(*) as total FROM feedback");
                            $row = $result->fetch_assoc();
                            echo "Total Feedback: " . $row['total'];
                            ?>
                        </p>
                    </div>
                <div class="table-responsive" style="color:white;max-height: 600px; border:collapse; overflow-y:auto;">
                    <table class="table  table-hover align-middle text-center">
                        <thead class="table-dark">
                            <tr>
                                <th style="font-size:16px; color:white;">ID</th>
                                <th style="font-size:16px; color:white;">Feedback</th>
                                <th style="font-size:16px; color:white;">Timestamp</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $query = "SELECT id, feedback_text, timestamp FROM feedback ORDER BY timestamp ASC";
                            $result = mysqli_query($con, $query);
                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr>
                                            <td style='font-size:15px; color:white;'>{$row['id']}</td>
                                            <td style='text-align:left;font-size:15px; color:white;'>{$row['feedback_text']}</td>
                                            <td style='font-size:15px; color:white;'>{$row['timestamp']}</td>
                                          </tr>";
                                }
                            } else {
                                echo "<tr><td colspan='3'>No feedback yet</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


        <script>
            document.addEventListener("DOMContentLoaded", function (event) {

                const showNavbar = (toggleId, navId, bodyId, headerId) => {
                    const toggle = document.getElementById(toggleId),
                        nav = document.getElementById(navId),
                        bodypd = document.getElementById(bodyId),
                        headerpd = document.getElementById(headerId)

                    // Validate that all variables exist
                    if (toggle && nav && bodypd && headerpd) {
                        toggle.addEventListener('click', () => {
                            // show navbar
                            nav.classList.toggle('show')
                            // change icon
                            toggle.classList.toggle('bx-x')
                            // add padding to body
                            bodypd.classList.toggle('body-pd')
                            // add padding to header
                            headerpd.classList.toggle('body-pd')
                        })
                    }
                }

                showNavbar('header-toggle', 'nav-bar', 'body-pd', 'header')

                /*===== LINK ACTIVE =====*/
                const linkColor = document.querySelectorAll('.nav_link')

                function colorLink() {
                    if (linkColor) {
                        linkColor.forEach(l => l.classList.remove('active'))
                        this.classList.add('active')
                    }
                }
                linkColor.forEach(l => l.addEventListener('click', colorLink))

            });

            function checkdel(name) {
                if (confirm('Are you sure you want to delete this category?')) {
                    return true
                } else {
                    return false
                }

            }
        </script>
    </body>

</html>