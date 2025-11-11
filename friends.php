<?php
include_once("componet/conn.php");
session_start();
date_default_timezone_set('Asia/Kuala_Lumpur');

if (!isset($_SESSION["login"])) {
    header("Location: login.php");
    exit();
}

$name = $_SESSION["username"];

// Get current user ID (prefer session id if available, otherwise prepared lookup by name)
if (isset($_SESSION['id']) && is_numeric($_SESSION['id'])) {
    $userId = (int) $_SESSION['id'];
} else {
    $stmt = $con->prepare("SELECT id FROM users WHERE name = ? LIMIT 1");
    $stmt->bind_param('s', $name);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($row = $res->fetch_assoc()) {
        $userId = (int) $row['id'];
    } else {
        // cannot determine user id
        header("Location: login.php");
        exit();
    }
    $stmt->close();
}

// Handle Accept, Block, Remove with prepared statements
if (isset($_GET['action']) && isset($_GET['id'])) {
    $actionId = intval($_GET['id']);
    $action = $_GET['action'];

    // only allow specific actions
    $allowed = ['accept', 'block', 'unblocked', 'remove'];
    if (!in_array($action, $allowed, true)) {
        header("Location: friends.php");
        exit();
    }

    // prepared: find friend row where either direction matches
    $stmt = $con->prepare("
        SELECT id, user_id, request_id 
        FROM friends 
        WHERE (user_id = ? AND request_id = ?) OR (user_id = ? AND request_id = ?)
        LIMIT 1
    ");
    $stmt->bind_param('iiii', $userId, $actionId, $actionId, $userId);
    $stmt->execute();
    $res = $stmt->get_result();
    $friendRow = $res->fetch_assoc();
    $stmt->close();

    $friendId = $friendRow ? (int) $friendRow['id'] : null;

    if ($friendId) {
        if ($action === 'accept') {
            $up = $con->prepare("UPDATE friends SET status = 1 WHERE id = ?");
            $up->bind_param('i', $friendId);
            $up->execute();
            $up->close();

            $activityNotification = "User Id #{$userId} has accepted your friend request";
            $stmt = $con->prepare("INSERT INTO notification (message, receiver_id, status, timestamp) VALUES (?, ?, 1, NOW())");
            $stmt->bind_param("si", $activityNotification, $actionId);
            $stmt->execute();
            $stmt->close();
        } elseif ($action === 'block') {
            $up = $con->prepare("UPDATE friends SET status = 2 WHERE id = ?");
            $up->bind_param('i', $friendId);
            $up->execute();
            $up->close();
        } elseif ($action === 'unblocked') {
            $up = $con->prepare("UPDATE friends SET status = 1 WHERE id = ?");
            $up->bind_param('i', $friendId);
            $up->execute();
            $up->close();
        } elseif ($action === 'remove') {
            $del = $con->prepare("DELETE FROM friends WHERE id = ?");
            $del->bind_param('i', $friendId);
            $del->execute();
            $del->close();
        }
    }

    header("Location: friends.php");
    exit();
}


// 


// Handle Add Friend by Email
$addFriendMsg = "";
if (isset($_POST['add_friend_email'])) {
    $friendEmail = trim($_POST['add_friend_email']);

    if (!empty($friendEmail) && filter_var($friendEmail, FILTER_VALIDATE_EMAIL)) {
        // prepared: find user id by email
        $stmt = $con->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
        $stmt->bind_param('s', $friendEmail);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res && $res->num_rows > 0) {
            $friendData = $res->fetch_assoc();
            $requestId = (int) $friendData['id'];

            if ($requestId == $userId) {
                $addFriendMsg = "You cannot add yourself.";
            } else {
                // prepared: check existing relationship
                $stmt = $con->prepare("
                    SELECT COUNT(*) AS cnt FROM friends 
                    WHERE (user_id = ? AND request_id = ?) OR (user_id = ? AND request_id = ?)
                ");
                $stmt->bind_param('iiii', $userId, $requestId, $requestId, $userId);
                $stmt->execute();
                $r2 = $stmt->get_result();
                $existsRow = $r2->fetch_assoc();
                $stmt->close();

                if ((int) $existsRow['cnt'] === 0) {
                    $timestamp = date("Y-m-d H:i:s");
                    $stmt = $con->prepare("INSERT INTO friends (user_id, request_id, status, timestamp) VALUES (?, ?, 0, ?)");
                    $stmt->bind_param('iis', $userId, $requestId, $timestamp);
                    $stmt->execute();
                    $stmt->close();

                    $addFriendMsg = "Friend request sent!";

                    $activityNotification = "Let's be friend from #{$userId}";
                    $stmt = $con->prepare("INSERT INTO notification (message, receiver_id, status, timestamp) VALUES (?, ?, 1, NOW())");
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
    } else {
        $addFriendMsg = "Please provide a valid email address.";
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
                    $listStmt = $con->prepare("
    SELECT 
        f.id AS friend_row_id,
        f.user_id,
        f.request_id,
        f.status,
        f.timestamp,
        IF(f.user_id = ?, f.request_id, f.user_id) AS other_user_id,
        u.name AS friend_name,
        u.email AS friend_email
    FROM friends f
    JOIN users u ON u.id = IF(f.user_id = ?, f.request_id, f.user_id)
    WHERE f.user_id = ? OR f.request_id = ?
    ORDER BY f.timestamp DESC
");
                    $listStmt->bind_param('iiii', $userId, $userId, $userId, $userId);
                    $listStmt->execute();
                    $result = $listStmt->get_result();

                    if ($result && $result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            $friendId = (int) $row['other_user_id'];
                            $friendName = htmlspecialchars($row['friend_name']);
                            $friendEmail = htmlspecialchars($row['friend_email']);
                            $status = (int) $row['status'];
                            $isSender = ((int) $row['user_id'] === $userId);

                            echo '<div class="friend-card">';
                            echo '<div class="friend-name"><i class="fa-solid fa-user" style="margin-right:8px;"></i> ' . $friendName . ' </div>';
                            echo '<div class="friend-actions">';

                            if ($status == 0) {
                                if ($isSender) {
                                    echo '<span class="status-text" style="color:#888;">Pending Request</span>';
                                    echo '<a href="?action=remove&amp;id=' . $friendId . '"><button class="remove-btn">Remove</button></a>';
                                } else {
                                    echo '<a href="?action=accept&amp;id=' . $friendId . '"><button class="accept-btn">Accept</button></a>';
                                    echo '<a href="?action=remove&amp;id=' . $friendId . '"><button class="remove-btn">Remove</button></a>';
                                }
                            } elseif ($status == 1) {
                                echo '<span class="status-text">My Friend</span>';
                                echo '<a href="?action=block&amp;id=' . $friendId . '"><button class="block-btn">Block</button></a>';
                                echo '<a href="?action=remove&amp;id=' . $friendId . '"><button class="remove-btn">Remove</button></a>';
                            } elseif ($status == 2) {
                                echo '<span class="blocked-text">Blocked</span>';
                                echo '<a href="?action=unblocked&amp;id=' . $friendId . '"><button class="remove-btn">Unblocked</button></a>';
                            }

                            echo '</div>';
                            echo '</div>';
                        }
                    } else {
                        echo "<p style='text-align:center; color:#666;'>You have no friend activity yet.</p>";
                    }

                    $listStmt->close();

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