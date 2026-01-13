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
include_once("componet/conn.php");
date_default_timezone_set('Asia/Kuala_Lumpur');

// ✅ Check if user is logged in
if (!isset($_SESSION['id'])) {
    http_response_code(401);
    echo json_encode(["error" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['id'];

$message = "";
$error = false;

// // Fetch user data
// $q1 = "SELECT * FROM `users` WHERE id='$user_id'";
// $result = mysqli_query($con, $q1);
// if (!$result || mysqli_num_rows($result) == 0) {
//     echo "User not found!";
//     exit();
// }
// $user = mysqli_fetch_assoc($result);

// Fetch user data (use prepared statement)
$stmt = $con->prepare("SELECT * FROM `users` WHERE id = ? LIMIT 1");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
if (!$result || $result->num_rows == 0) {
    echo "User not found!";
    $stmt->close();
    exit();
}
$user = $result->fetch_assoc();
$stmt->close();

// Handle profile update (only name + password)
// if (isset($_POST["updateProfile"])) {
//     $name = $_POST["name"];
//     $pwd = $_POST["pwd"];
//     $confirmPwd = $_POST["confirmpwd"];

//     if ($name == "") {
//         $message = "Name cannot be empty.";
//     } elseif ($pwd !== "" && $pwd !== $confirmPwd) {
//         $message = "Passwords do not match.";
//     } else {
//         $updateFields = [];
//         $updateFields[] = "name='" . mysqli_real_escape_string($con, $name) . "'";

//         // Only update password if provided
//         if ($pwd !== "") {
//             $safepwd = password_hash($pwd, PASSWORD_DEFAULT);
//             $updateFields[] = "password='$safepwd'";
//         }

//         $updateQuery = "UPDATE `users` SET " . implode(", ", $updateFields) . " WHERE id='$user_id'";

//         if ($con->query($updateQuery) === TRUE) {
//             $message = "Profile updated successfully!";
//             // Refresh user data
//             $q1 = "SELECT * FROM `users` WHERE id='$user_id'";
//             $result = mysqli_query($con, $q1);
//             $user = mysqli_fetch_assoc($result);
//         } else {
//             $message = "Error updating profile: " . $con->error;
//         }
//     }
// }

// Handle profile update (only name + password)
if (isset($_POST["updateProfile"])) {
    $name = trim($_POST["name"] ?? '');
    $pwd = $_POST["pwd"] ?? '';
    $confirmPwd = $_POST["confirmpwd"] ?? '';

    if ($name === "") {
        $message = "Name cannot be empty.";
    } elseif ($pwd !== "" && $pwd !== $confirmPwd) {
        $message = "Passwords do not match.";
    } else {
        if ($pwd !== "") {
            // update name and password
            $safepwd = password_hash($pwd, PASSWORD_DEFAULT);
            $stmt = $con->prepare("UPDATE `users` SET name = ?, password = ? WHERE id = ?");
            $stmt->bind_param('ssi', $name, $safepwd, $user_id);
        } else {
            // update name only
            $stmt = $con->prepare("UPDATE `users` SET name = ? WHERE id = ?");
            $stmt->bind_param('si', $name, $user_id);
        }

        if ($stmt->execute()) {
            $message = "Profile updated successfully!";
            $stmt->close();
            // Refresh user data securely
            $stmt2 = $con->prepare("SELECT * FROM `users` WHERE id = ? LIMIT 1");
            $stmt2->bind_param('i', $user_id);
            $stmt2->execute();
            $res2 = $stmt2->get_result();
            $user = $res2->fetch_assoc();
            $stmt2->close();
        } else {
            $message = "Error updating profile: " . $con->error;
            $stmt->close();
        }
    }
}

// Handle account termination (use prepared statements)
if (isset($_POST["terminateAccount"])) {
    // delete access records for user's QR codes
    $stmt = $con->prepare("DELETE FROM `accessrecord` WHERE qr_id IN (SELECT id FROM qr_security WHERE user_id = ?)");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stmt->close();

    // delete qr_security rows
    $stmt = $con->prepare("DELETE FROM `qr_security` WHERE user_id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stmt->close();

    // delete friends rows where user is either side
    $stmt = $con->prepare("DELETE FROM `friends` WHERE user_id = ? OR request_id = ?");
    $stmt->bind_param('ii', $user_id, $user_id);
    $stmt->execute();
    $stmt->close();

    // delete user
    $stmt = $con->prepare("DELETE FROM `users` WHERE id = ?");
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stmt->close();

    // Clear session and redirect
    session_destroy();
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="loginsignup.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>SQ-TECH SOLVER - Secure QR Code Generator</title>
    <link rel="icon" type="image/png" href="img/log.png">
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
    </style>
    <script>
        function confirmTermination() {
            return confirm("Are you sure you want to permanently delete your account? This action cannot be undone.");
        }
    </script>
</head>

<body >
    <div class="section">
        <div class="container" style="width: 1000; ">
            <div class="form">
                <div class="left-side" style="width: 300px;">
                    <div class="content" style="width: 250px; ">
                        <div style="display:flex; justify-content: center; align-items: center;">
                            <img src="img/log.svg" alt="SQ-Tech Solver" class="logo-img"
                                style="width: 100px; height: auto;">
                        </div>
                        <br><br>
                        <h1 style="font-size: 25px;font-weight: bold; color:black;">SQ-TECH SOLVER</h1><br>
                        <h5 style="color:black;">Bring Quality Over Standard</h5>
                    </div>
                </div>
                <div class="right-side" style="width : 700px; margin-left: 15px;">
                    <form id="signupForm" action="profile.php" method="post">
                        <div class="forms">
                            <h1 class="forms-heading">My Profile</h1>
                            <?php if ($message): ?>
                                <p style="color:red;text-align:center;"><?php echo $message; ?></p>
                            <?php endif; ?>

                            <p>Short name *</p>
                            <div class="form-inputs">
                                <input type="text" id="shortname" maxlength="20" name="name"
                                    value="<?= htmlspecialchars($user['name']) ?>" style="border-radius:30px;" required>
                                <i class="fa fa-user"></i>
                            </div>

                            <p>Email (Cannot change)</p>
                            <div class="form-inputs">
                                <input type="email" value="<?= htmlspecialchars($user['email']) ?>"
                                    style="border-radius:30px;" disabled>
                                <i class="fa fa-envelope"></i>
                            </div>

                            <!-- <p>Phrase (cannot change)</p> 
                        <div class="form-inputs"> 
                            <input type="text" value="<?= htmlspecialchars($user['phrase']) ?>" disabled>
                            <i class="fa fa-lock"></i> 
                        </div> -->

                            <p style="margin: 0 0 4px 0;">New Password (Leave blank if not changing)</p>
                            <div class="form-inputs">
                                <input type="password" id="password" name="pwd" autocomplete="off"
                                    style="border-radius: 30px; margin: 0; ">
                                <i class="fa fa-eye" id="password_eye"></i>
                                <span style="font-size: 10px; color: red; display: block; margin: 2px 0 0 0;">
                                    Password must contain uppercase, lowercase, number, special symbol, and be at least
                                    8 characters.
                                </span>
                            </div>

                            <p style="margin: 0 0 4px 0;">Confirm New Password</p>
                            <div class="form-inputs">
                                <input type="password" id="confirmPassword" name="confirmpwd" autocomplete="off"
                                    style="border-radius: 30px; margin: 0; ">
                                <i class="fa fa-eye" id="confirm_password_eye"></i>
                                <span style="font-size: 10px; color: red; display: block; margin: 2px 0 0 0;">
                                    Password must contain uppercase, lowercase, number, special symbol, and be at least
                                    8 characters.
                                </span>
                            </div>

                            <div class="submit-button" style="width:100%; max-width:100%;">
                                <button type="submit" name="updateProfile" style="border-radius:30px;">Update
                                    Profile</button>
                            </div>

                            <hr>
                            <!-- <div class="submit-button" style="width:100%; max-width:100%;" >
                            <form method="post" onsubmit="return confirmTermination();">
                                <input type="submit" name="terminateAccount" value="Terminate Account" class="btn btn-danger">
                            </form>
                        </div> -->
                    </form>

                    <form method="post" onsubmit="return confirmTermination();">
                        <div class="submit-button" style="width:100%; max-width:100%; ">
                            <button type="submit" name="terminateAccount" class="btn btn-danger"
                                style="border-radius:30px; justify-content: center; align-items: center; background-color: red;">Delete
                                Account</button>
                        </div>
                    </form>
                    <div class="form-acc" style="display: flex; justify-content: center; align-items: center;">
                        <a href="home.php">Back to Home Page</a>
                    </div>
                    <div class="form-acc" style="display: flex; justify-content: center; align-items: center;">
                        <a href="componet/userlogout.php">
                            <i class="fa-solid fa-arrow-right-from-bracket"></i>
                            <span>Log out</span>
                        </a>
                    </div>
                </div>

                <script>
                    // const form = document.getElementById('signupForm');
                    // const shortname = document.getElementById('shortname');
                    // const password = document.getElementById('password');
                    // const confirmPassword = document.getElementById('confirmPassword');

                    // // Password strength check function
                    // function isStrongPassword(pwd) {
                    //     return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/.test(pwd);
                    // }

                    // // Real-time shortname check (optional alert)
                    // shortname.addEventListener('input', () => {
                    //     if (shortname.value.length > 20) {
                    //         alert("Maximum 20 characters allowed for Short Name.");
                    //         shortname.value = shortname.value.slice(0, 20);
                    //     }
                    // });

                    // // Form validation before submit
                    // form.addEventListener('submit', (e) => {
                    //     // === Validate short name ===
                    //     if (shortname.value.trim().length === 0) {
                    //         alert("Short name cannot be empty.");
                    //         e.preventDefault();
                    //         return;
                    //     }

                    //     // === Validate password strength ===
                    //     if (!isStrongPassword(password.value)) {
                    //         alert("Password must be at least 8 characters and include uppercase, lowercase, number, and special character.");
                    //         e.preventDefault();
                    //         return;
                    //     }

                    //     // // === Validate confirm password ===
                    //     // if (password.value !== confirmPassword.value) {
                    //     //     alert("Passwords do not match. Please re-enter.");
                    //     //     e.preventDefault();
                    //     //     return;
                    //     // }
                    // });
                    const form = document.getElementById('signupForm');
                    const shortname = document.getElementById('shortname');
                    const password = document.getElementById('password');
                    const confirmPassword = document.getElementById('confirmPassword');

                    // Password strength check function
                    function isStrongPassword(pwd) {
                        return /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/.test(pwd);
                    }

                    // Real-time shortname check (optional alert)
                    shortname.addEventListener('input', () => {
                        if (shortname.value.length > 20) {
                            alert("Maximum 20 characters allowed for Short Name.");
                            shortname.value = shortname.value.slice(0, 20);
                        }
                    });

                    // Form validation before submit
                    form.addEventListener('submit', (e) => {
                        // === Validate short name ===
                        if (shortname.value.trim().length === 0) {
                            alert("Short name cannot be empty.");
                            e.preventDefault();
                            return;
                        }

                        // If password is provided, validate strength and matching confirm
                        const pwdVal = (password && password.value || '').trim();
                        const confVal = (confirmPassword && confirmPassword.value || '').trim();

                        if (pwdVal !== '') {
                            if (!isStrongPassword(pwdVal)) {
                                alert("Password must be at least 8 characters and include uppercase, lowercase, number, and special character.");
                                e.preventDefault();
                                return;
                            }
                            if (pwdVal !== confVal) {
                                alert("Passwords do not match. Please re-enter.");
                                e.preventDefault();
                                return;
                            }
                        }
                        // if pwdVal is empty -> no password checks, proceed to update name only
                    });
                </script>

            </div>
        </div>
    </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>