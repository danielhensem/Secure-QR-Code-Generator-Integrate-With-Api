<?php

include_once "componet/conn.php";
if (isset($_SESSION["login"])) {
    $name = $_SESSION["username"];
}

$notif_count = 0;

if (isset($_SESSION['id']) && is_numeric($_SESSION['id'])) {
    $current_id = (int) $_SESSION['id'];

    $sql = "SELECT COUNT(*) AS total FROM notification WHERE receiver_id = ? AND status = 1";
    $stmt = $con->prepare($sql);

    if (!$stmt) {
        die("SQL error: " . $con->error); // show the MySQL error
    }

    $stmt->bind_param("i", $current_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $notif_count = $row['total'] ?? 0;
} else {
    $current_id = 0; // no user logged in
}

// ✅ If AJAX request for notifications
if (isset($_POST['load_notifications'])) {
    if (!isset($_SESSION['id'])) {
        echo "<div class='notification-item'>Please log in to view notifications.</div>";
        exit;
    }

    $user_id = intval($_SESSION['id']);

    // Fetch unread notifications
    $sql = "SELECT message FROM notification 
            WHERE receiver_id = ? AND status = 1
            ORDER BY id DESC";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div class='notification-item'>" . htmlspecialchars($row['message']) . "</div>";
        }

        // Mark them as read
        $update_sql = "UPDATE notification SET status = 2 WHERE receiver_id = ? AND status = 1";
        $update_stmt = $con->prepare($update_sql);
        $update_stmt->bind_param("i", $user_id);
        $update_stmt->execute();
    } else {
        echo "<div class='notification-item'>No new notifications.</div>";
    }
    exit; // Stop page render for AJAX calls
}

?>

<nav>
    <style>
        .websitelogo {
            margin: 0;
        }

        .logo-link {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
            color: inherit;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .logo-img {
            width: 36px;
            height: 36px;
            object-fit: contain;
            border-radius: 6px;
            display: inline-block;
        }

        .logo-text {
            font-weight: bold;
            font-size: 20px;
            line-height: 1;
        }

        @media (max-width: 600px) {
            .logo-text {
                display: none;
            }

            .logo-img {
                width: 30px;
                height: 30px;
            }
        }
    </style>
    <section class="full-navbar">
        <div class="navbar">
            <div class="navbar-left">
                <h1 class="websitelogo">
                    <a href="index.php" class="logo-link" title="SQ-Tech Solver">
                        <img src="img/log.svg" alt="SQ-Tech Solver" class="logo-img" />
                        <span class="logo-text">SQ-Tech Solver</span>
                    </a>
                </h1>
            </div>
            <div class="bs-nav">
                <ul class="nav-links">


                    <?php
                    if (isset($_SESSION["login"])) {
                        if ($_SESSION["login"] == true) {
                            ?>
                            <li class="nav-link nav-loggd">
                                <a href="profile.php">
                                    <i class="far fa-user"></i>
                                    <span class="nav-link-text"><?php echo $name ?></span>
                                </a>



                            </li>
                            <?php
                        } else {
                            echo '  
                                <li class="nav-link">
                                    <a href="login.php">
                                        <i class="far fa-user"></i>
                                            <span class="nav-link-text">Log In</span> 
                                    </a>
                                </li> ';
                        }
                    } else {
                        echo '  
                                <li class="nav-link">
                                    <a href="login.php">
                                        <i class="far fa-user"></i>
                                            <span class="nav-link-text">Log In</span> 
                                    </a>
                                </li> ';
                    }
                    ?>



                    <li class="nav-link">
                        <a href="#" id="notificationBtn">
                            <i class="far fa-bell"></i>
                            <span class="nav-link-text">Notification(<?= $notif_count ?>)</span>
                        </a>
                    </li>

                    <!-- ✅ Notification Modal -->
                    <div id="notificationModal" class="notification-modal">
                        <div class="notification-content" id="notificationList">
                            <!-- Notifications will be loaded here -->
                        </div>
                    </div>

                    <!-- ✅ Styles -->
                    <style>
                        .notification-modal {
                            display: none;
                            position: absolute;
                            top: 70px;
                            /* Adjust according to your navbar height */
                            right: 20px;
                            width: 300px;
                            background: white;
                            border: 1px solid #ddd;
                            border-radius: 8px;
                            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
                            z-index: 9999;
                        }

                        .notification-content {
                            max-height: 300px;
                            overflow-y: auto;
                            padding: 10px;
                        }

                        .notification-item {
                            padding: 8px;
                            border-bottom: 1px solid #eee;
                            font-size: 14px;
                        }

                        .notification-item:last-child {
                            border-bottom: none;
                        }
                    </style>
                    <!-- ✅ Script -->
                    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                    <script>
                        $(document).ready(function () {
                            $("#notificationBtn").on("click", function (e) {
                                e.preventDefault();
                                $("#notificationModal").toggle();

                                $.ajax({
                                    url: "", // same file
                                    method: "POST",
                                    data: { load_notifications: 1 },
                                    success: function (data) {
                                        $("#notificationList").html(data);
                                    }
                                });
                            });

                            $(document).on("click", function (e) {
                                if (!$(e.target).closest("#notificationModal, #notificationBtn").length) {
                                    $("#notificationModal").hide();
                                }
                            });
                        });

                        function updateNotificationCount() {
                            fetch('get_notification_count.php')
                                .then(res => res.json())
                                .then(data => {
                                    document.querySelector('.nav-link-text').textContent = `Notification(${data.count})`;
                                });
                        }

                        // Update every 10 seconds
                        setInterval(updateNotificationCount, 10000);

                        // Run on page load
                        updateNotificationCount();

                    </script>
                    <!-- <li class="nav-link">
                            <a href="my-cart.php">
                                <img src="img/shopping-cart.png" alt="">
                                <?php
                                if (isset($_SESSION['id'])) {
                                    $getcartnum = "select * from mycart where userid={$_SESSION['id']}";
                                    $exccartnum = $con->query($getcartnum);
                                    $itemnum = mysqli_num_rows($exccartnum);
                                    ?> 
                                    <span class="nav-link-text">(<?php echo $itemnum; ?>)</span> <?php
                                } else {
                                    ?> <span class="nav-link-text"> 0 </span>
                                <?php }
                                ?>
 
                            </a>
                        </li> -->
                </ul>
            </div>
            <div class="ss-nav">

                <ul class="res-nav-links">

                    <li class="nav-link" id="nav-menu-bar">
                        <button id="nav-menu" class="nav-open">
                            <i class="fa-solid fa-bars bars"></i>
                        </button>
                        <button id="nav-menu" class="nav-close">
                            <i class="fa-solid fa-xmark"></i>
                        </button>
                    </li>
                </ul>
            </div>
        </div>
        <div class="res-nav">

            <ul class="nav-links-menu">

                <?php
                if (isset($_SESSION["login"])) {
                    ?>
                    <li class="nav-link-menu">
                        <a href="profile.php">
                            <i class="far fa-user"></i>
                            <span class="nav-link-text"><?php echo $name; ?></span>
                        </a>

                    </li>
                    <?php
                } else {
                    echo
                        ' <li class="nav-link-menu">
                                    <a href="login.php">
                                        <i class="far fa-user"></i>
                                        <span class="nav-link-text">Log In</span>
                                    </a>
                                </li>';

                }

                ?>
                <li class="nav-link-menu">
                    <a href="#" id="notificationBtn">
                        <i class="far fa-bell"></i>
                        <span class="nav-link-text">Notification(<?= $notif_count ?>)</span>
                    </a>

                </li>
            </ul>
            <div class="sub-nav">
                <ul class="sub-nav-links" justifycontent="center">
                    <li class="sub-nav-link">
                        <a href="home.php">
                            <p>Home</p>
                        </a>
                    </li>
                    <?php
                    $scq = "SELECT * FROM `product-catagory` WHERE status=0";
                    $excscq = $con->query($scq);

                    while ($row = mysqli_fetch_array($excscq)) {
                        // If cname is 'scan', redirect to generateqr.php instead
                        if (strtolower($row["cname"]) == 'scan') {
                            ?>
                            <li class="sub-nav-link">
                                <a href="scan.php">
                                    <p><?php echo $row["cname"]; ?></p>
                                </a>
                            </li>
                            <?php
                            continue; // Skip the rest of the loop
                        }

                        if (strtolower($row["cname"]) == 'dashboard') {
                            ?>
                            <li class="sub-nav-link">
                                <a href="dashboard.php">
                                    <p><?php echo $row["cname"]; ?></p>
                                </a>
                            </li>
                            <?php
                            continue; // Skip the rest of the loop
                        }

                        if (strtolower($row["cname"]) == 'friend') {
                            ?>
                            <li class="sub-nav-link">
                                <a href="friends.php">
                                    <p><?php echo $row["cname"]; ?></p>
                                </a>
                            </li>
                            <?php
                            continue; // Skip the rest of the loop
                        }

                        if (strtolower($row["cname"]) == 'activity') {
                            ?>
                            <li class="sub-nav-link">
                                <a href="activity.php">
                                    <p><?php echo $row["cname"]; ?></p>
                                </a>
                            </li>
                            <?php
                            continue; // Skip the rest of the loop
                        }

                        if (strtolower($row["cname"]) == 'feedback') {
                            ?>
                            <li class="sub-nav-link">
                                <a href="feedback.php">
                                    <p><?php echo $row["cname"]; ?></p>
                                </a>
                            </li>
                            <?php
                            continue; // Skip the rest of the loop
                        }

                        if ($row["issubset"] == 1) {
                            $subcq = "SELECT * FROM `sub-catagory` WHERE cid={$row['cid']} AND status=0";
                            $excsubcat = $con->query($subcq);
                            ?>
                            <li class="sub-nav-link sub-sub-nav">
                                <a href="products.php?category=<?php echo $row['cid'] ?>">
                                    <p><?php echo $row['cname']; ?></p>
                                </a>
                                <div class="sub-nav-dropbox">
                                    <?php while ($subcat = mysqli_fetch_assoc($excsubcat)) { ?>
                                        <ul class="sn-dropbox-list">
                                            <li class="sn-dropbox-link">
                                                <a href="products.php?subcategory=<?php echo $subcat['subid'] ?>">
                                                    <?php echo $subcat['subname'] ?>
                                                </a>
                                            </li>
                                        </ul>
                                    <?php } ?>
                                </div>
                            </li>
                            <?php
                        } else {
                            ?>
                            <li class="sub-nav-link">
                                <a href="products.php?category=<?php echo $row['cid'] ?>">
                                    <p><?php echo $row["cname"]; ?></p>
                                </a>
                            </li>
                            <?php
                        }
                    }
                    ?>


                    <?php

                    ?>



                </ul>
            </div>
        </div>
        
    </section>
</nav>