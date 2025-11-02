<?php
include_once("componet/conn.php");
session_start();
date_default_timezone_set('Asia/Kuala_Lumpur');

if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit();
}

$name = $_SESSION["username"];

// Get current user ID
$getUserQuery = "SELECT id FROM users WHERE name = '$name' LIMIT 1";
$userResult = mysqli_query($con, $getUserQuery);
$userRow = mysqli_fetch_assoc($userResult);
$userId = $userRow['id'];

// Handle Accept, Block, Remove
if (isset($_GET['action']) && isset($_GET['id'])) {
    $actionId = intval($_GET['id']);
    $action = $_GET['action'];

    // Friend row may store user_id or request_id as current user
    $friendQuery = "SELECT * FROM friends WHERE 
        (user_id = '$userId' AND request_id = '$actionId') OR 
        (user_id = '$actionId' AND request_id = '$userId')
        LIMIT 1";
    $friendRow = mysqli_fetch_assoc(mysqli_query($con, $friendQuery));
    $friendId = $friendRow ? $friendRow['id'] : null;

    if ($friendId) {
        if ($action === 'accept') {
            mysqli_query($con, "UPDATE friends SET status = 1 WHERE id = '$friendId'");
            $activityNotification = "User Id #" . $userId . "  has accept your friend request";
            $stmt = $con->prepare("INSERT INTO notification (message,receiver_id,status, timestamp) VALUES (?,?,1, NOW())");
            $stmt->bind_param("si", $activityNotification, $actionId);
            $stmt->execute();
            $stmt->close();
        } elseif ($action === 'block') {
            mysqli_query($con, "UPDATE friends SET status = 2 WHERE id = '$friendId'");
        } elseif ($action === 'unblocked') {
            mysqli_query($con, "UPDATE friends SET status = 1 WHERE id = '$friendId'");
        } elseif ($action === 'remove') {
            mysqli_query($con, "DELETE FROM friends WHERE id = '$friendId'");
        }
    }

    header("Location: friends.php");
    exit();
}

// Handle Add Friend by Email
$addFriendMsg = "";
if (isset($_POST['add_friend_email'])) {
    $friendEmail = trim($_POST['add_friend_email']);

    if (!empty($friendEmail)) {
        $findEmail = "SELECT id FROM users WHERE email = '$friendEmail' LIMIT 1";
        $emailResult = mysqli_query($con, $findEmail);

        if (mysqli_num_rows($emailResult) > 0) {
            $friendData = mysqli_fetch_assoc($emailResult);
            $requestId = $friendData['id'];

            if ($requestId == $userId) {
                $addFriendMsg = "You cannot add yourself.";
            } else {
                $checkFriend = "SELECT * FROM friends WHERE 
                    (user_id = '$userId' AND request_id = '$requestId') OR
                    (user_id = '$requestId' AND request_id = '$userId')";
                $exists = mysqli_query($con, $checkFriend);

                if (mysqli_num_rows($exists) == 0) {
                    $timestamp = date("Y-m-d H:i:s");
                    $insertFriend = "INSERT INTO friends (user_id, request_id, status, timestamp)
                                     VALUES ('$userId', '$requestId', 0, '$timestamp')";
                    mysqli_query($con, $insertFriend);
                    $addFriendMsg = "Friend request sent!";
                    $activityNotification = "Let's be friend from #" . $userId;
                    $stmt = $con->prepare("INSERT INTO notification (message,receiver_id,status, timestamp) VALUES (?,?,1, NOW())");
                    $stmt->bind_param("si", $activityNotification, $requestId);
                    $stmt->execute();
                    $stmt->close();
                } else {
                    $addFriendMsg = "Friend request already exists.";
                }
            }
        } else {
            $addFriendMsg = "Email not found in users.";
        }
    }
}
?>
<!-- HTML section remains unchanged -->
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SQ-TECH SOLVER - Secure QR Code Generator</title>
    <link rel="icon" type="image/png" href="img/log.png">
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

        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0f2f5;
            margin: 0;
            padding: 0;
        }

        .case-section {
            padding: 40px 20px;
            background-color: #ffffff;
        }

        .case-title {
            text-align: center;
            font-size: 26px;
            color: #222;
            margin-bottom: 30px;
            font-weight: bold;
        }

        .friend-card {
            border: 1px solid #ccc;
            border-radius: 40px;
            padding: 15px;
            background: linear-gradient(135deg, #8ee3ef, #ffd4bf);
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0px 30px 80px rgba(0, 0, 0, 0.4);
        }

        .friend-name {
            font-size: 18px;
            color: #333;
        }

        .friend-actions button {
            padding: 6px 12px;
            border: none;
            border-radius: 20px;
            font-size: 14px;
            cursor: pointer;
            margin-left: 10px;
        }

        .accept-btn {
            background-color: #28a745;
            color: #fff;
        }

        .block-btn {
            background-color: #dc3545;
            color: #fff;
        }

        .remove-btn {
            background-color: gray;
            color: #fff;
        }

        .status-text {
            color: green;
            font-weight: bold;
        }

        .blocked-text {
            color: red;
            font-weight: bold;
        }

        .add-friend-form {
            text-align: center;
            margin-bottom: 30px;
        }

        .add-friend-form input[type="email"] {
            padding: 8px;
            width: 280px;
            border: 1px solid #ccc;
            border-radius: 20px;
        }

        .add-friend-form button {
            padding: 8px 14px;
            background-color: #0676f7ff;
            border: none;
            border-radius: 20px;
            color: white;
            cursor: pointer;
        }

        .add-friend-message {
            text-align: center;
            color: #444;
            margin-top: 10px;
        }

        .footer-note {
            text-align: center;
            margin-top: 40px;
            font-size: 13px;
            color: #888;
        }
    </style>
</head>

<body class="animated-bg">
    <?php include("componet/navbar.php"); ?>

    <div class="main-body">
        <section>
            <div class="bigcontainer">
                <div class="cat-products" style="margin:20px 20px;">
                    <h2 class="case-title" style="font-size:26px;">Friends</h2>

                    <div class="add-friend-form">
                        <form method="POST" action="friends.php">
                            <input type="email" name="add_friend_email" placeholder="Enter friend's email"
                                style="width:300px; text-align: left; font-size: 13px;" required>
                            <button type="submit" style="font-size: 13px;">Add</button>
                        </form>
                        <?php if ($addFriendMsg): ?>
                            <div class="add-friend-message"><?= htmlspecialchars($addFriendMsg) ?></div>
                        <?php endif; ?>
                    </div>

                    <?php
                    $friendQuery = "
                        SELECT 
                            f.id AS friend_row_id,
                            f.user_id,
                            f.request_id,
                            f.status,
                            f.timestamp,
                            IF(f.user_id = '$userId', f.request_id, f.user_id) AS other_user_id,
                            u.name AS friend_name,
                            u.email AS friend_email
                        FROM friends f
                        JOIN users u ON u.id = IF(f.user_id = '$userId', f.request_id, f.user_id)
                        WHERE f.user_id = '$userId' OR f.request_id = '$userId'
                        ORDER BY f.timestamp DESC
                    ";

                    $result = mysqli_query($con, $friendQuery);

                    if (mysqli_num_rows($result) > 0) {
                        while ($row = mysqli_fetch_assoc($result)) {
                            $friendId = $row['other_user_id'];
                            $friendName = htmlspecialchars($row['friend_name']);
                            $friendEmail = htmlspecialchars($row['friend_email']);
                            $status = $row['status'];
                            $isSender = ($row['user_id'] == $userId);

                            echo '<div class="friend-card">';
                            echo '<div class="friend-name"><i class="fa-solid fa-user" style=margin-right:8px;></i> ' . $friendName . ' </div>';
                            echo '<div class="friend-actions">';

                            if ($status == 0) {
                                if ($isSender) {
                                    echo '<span class="status-text" style="color:#888;">Pending Request</span>';
                                    echo '<a href="?action=remove&id=' . $friendId . '"><button class="remove-btn">Remove</button></a>';
                                } else {
                                    echo '<a href="?action=accept&id=' . $friendId . '"><button class="accept-btn">Accept</button></a>';
                                    echo '<a href="?action=remove&id=' . $friendId . '"><button class="remove-btn">Remove</button></a>';
                                }
                            } elseif ($status == 1) {
                                echo '<span class="status-text">My Friend</span>';
                                echo '<a href="?action=block&id=' . $friendId . '"><button class="block-btn">Block</button></a>';
                                echo '<a href="?action=remove&id=' . $friendId . '"><button class="remove-btn">Remove</button></a>';
                            } elseif ($status == 2) {
                                echo '<span class="blocked-text">Blocked</span>';
                                echo '<a href="?action=unblocked&id=' . $friendId . '"><button class="remove-btn">Unblocked</button></a>';
                            }

                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo "<p style='text-align:center; color:#666;'>You have no friend activity yet.</p>";
                    }
                    ?>

                    <div class="footer-note">&copy; 2025 SQ‑Tech Solver. All rights reserved.</div>
                </div>
            </div>
        </section>
    </div>
        <!-- javascript sw -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/swiper@8/swiper-bundle.min.js"></script>
    <script src="index.js"></script>
</body>

</html>