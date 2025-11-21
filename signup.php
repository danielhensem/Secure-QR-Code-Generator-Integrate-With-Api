<?php include_once("componet/conn.php"); ?>

<?php
include_once("componet/conn.php");
session_start();
date_default_timezone_set('Asia/Kuala_Lumpur');

$message = ""; // for displaying errors on form
$error = false;

if (isset($_POST["signsubmit"])) {

    // require the verification code from email
    $enteredCode = trim($_POST['signup_code'] ?? '');

    if ($enteredCode === '') {
        $message = "Please enter the verification code sent to your email.";
        $error = true;
    } else {
        // check session stored code and expiry
        if (!isset($_SESSION['signup_code'], $_SESSION['signup_code_expires']) || time() > intval($_SESSION['signup_code_expires'])) {
            $message = "Verification code expired. Request a new code.";
            $error = true;
        } elseif (!hash_equals($_SESSION['signup_code'], $enteredCode)) {
            $message = "Invalid verification code.";
            $error = true;
        }
    }

    // only continue registration when code is valid
    if (!$error) {
        $name = trim($_POST["shortname"]);
        $email = trim($_POST["email"]);
        $pwd = trim($_POST["pwd"]);

        if (empty($name) || empty($email) || empty($pwd)) {
            $message = "Please fill in all required fields.";
            $error = true;
        } else {
            // Check if email exists
            $q1 = "SELECT * FROM `users` WHERE email=?";
            $stmt = $con->prepare($q1);
            $stmt->bind_param('s', $email);
            $stmt->execute();
            $checkemail = $stmt->get_result();

            if ($checkemail && $checkemail->num_rows > 0) {
                $message = "Email already registered!";
                $error = true;
            } else {
                // Generate secure random phrase
                function generateRandomPhrase($length = 40)
                {
                    $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_=+[]{}<>?';
                    $phrase = '';
                    for ($i = 0; $i < $length; $i++) {
                        $phrase .= $characters[random_int(0, strlen($characters) - 1)];
                    }
                    return $phrase;
                }

                $timestamp1 = date("Y-m-d H:i:s");
                $phrase = generateRandomPhrase(40);
                $safepwd = password_hash($pwd, PASSWORD_DEFAULT);

                $insq = "INSERT INTO `users`(`name`, `password`, `email`, `phrase`, `timestamp`) VALUES (?,?,?,?,?)";
                $stmtIns = $con->prepare($insq);
                $stmtIns->bind_param('sssss', $name, $safepwd, $email, $phrase, $timestamp1);

                if ($stmtIns->execute() === TRUE) {
                    $last_id = $con->insert_id;

                    // Auto-friend setup
                    $request_id = 61;
                    $status = 1;
                    $timestamp = date("Y-m-d H:i:s");

                    $friendInsert = "INSERT INTO `friends`(`user_id`, `request_id`, `status`, `timestamp`) VALUES (?,?,?,?)";
                    $stmtF = $con->prepare($friendInsert);
                    $stmtF->bind_param('iiis', $last_id, $request_id, $status, $timestamp);
                    $stmtF->execute();

                    // registration successful — clear used code
                    unset($_SESSION['signup_code'], $_SESSION['signup_code_expires']);

                    header("Location: login.php");
                    exit();
                } else {
                    $message = "Error creating user: " . $con->error;
                    $error = true;
                }
            }
            if (isset($stmt))
                $stmt->close();
        }
    }
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <title>SQ-TECH SOLVER - Secure QR Code Generator</title>
    <link rel="icon" type="image/png" href="img/log.svg">
</head>

<body>
    <?php
    if ($error) {

        echo '
<style>
    .alertbox {
        position: fixed;
        top: 20px;
        left: 20px;
        right: 20px;
        z-index: 1055; /* Higher than modal (1050) and navbar (1030) */
        max-width: 500px;
        margin: auto;
    }

    .alert {
        border-radius: 10px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        border-left: 5px solid #ffc107;
        background-color: #fff3cd;
        color: #856404;
        font-family: "Segoe UI", sans-serif;
    }

    .btn-close {
        padding: 0.5rem 1rem;
    }
</style>

<div class="alertbox">
    <div class="alert alert-warning alert-dismissible fade show" role="alert">
        <strong>⚠️ Alert!</strong> Email already exists.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
</div>';
    }
    ?>
    <div class="section">
        <div class="container" style="width: 1000;">
            <div class="form">
                <div class="left-side" style="width: 300px;">
                    <div class="content" style="width: 250px;">
                        <img src="img/log.svg" srcset="img/log@2x.png 2x" alt="SQ-Tech Solver" class="logo-img"
                            style="width: 100px; height: 100px;">
                            <br><br>
                        <h1 style="color:black;">SQ-TECH SOLVER</h1>
                        <h5 style="color:black;">Bring Quality Over Standard</h5>
                        <!-- <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut
                            labore et dolore magna aliqua.</p>  -->
                        <!-- <img src="img/loginbg.png" width="300"> -->
                    </div>

                </div>
                <div class="right-side" style="width:600px;">
                    <form id="signupForm" method="post" action="">
                        <div class="forms">
                            <h1 class="forms-heading">Register Account</h1>

                            <?php if ($message): ?>
                                <p style="color:red;text-align:center;"><?php echo $message; ?></p>
                            <?php endif; ?>

                            <p>Short name *</p>
                            <div class="form-inputs">
                                <input type="text" id="shortname" placeholder="Short name" name="shortname" maxlength="20" required
                                    style="border-radius:30px;">
                                <i class="fa fa-user"></i>
                            </div>

                            <p>Email *</p>
                            <div class="form-inputs">
                                <input type="email" id="email" name="email" placeholder="Email" autocomplete="off" required
                                    style="border-radius:30px;">
                                <i class="fa fa-envelope"></i>
                            </div>

                            <!-- Verification code request -->
                            <div style="display:flex;gap:8px;align-items:center;margin-top:8px;">
                                <input type="text" id="signup_code" name="signup_code"
                                    placeholder="Enter code from email" style="flex:1;border-radius:30px;padding:10px;"
                                    required>
                                <button type="button" id="requestCodeBtn"
                                    style="padding: 2px 10px;border-radius:20px;cursor:pointer;">
                                    Request Code
                                </button>
                            </div>
                            <div id="codeMessage" style="color:green;margin-top:6px;display:none;"></div>


                            <p>Password *</p>
                            <div class="form-inputs">
                                <input type="password" id="password" name="pwd" placeholder="Password" autocomplete="off" required
                                    style="border-radius:30px;">
                                <i class="fa fa-eye" id="password_eye"></i>
                            </div>

                            <p>Confirm Password *</p>
                            <div class="form-inputs">
                                <input type="password" id="confirmPassword" name="confirmpwd" placeholder="Password" autocomplete="off"
                                    required style="border-radius:30px;">
                                <i class="fa fa-eye" id="confirm_password_eye"></i>
                            </div>

                            <div class="submit-button">
                                <button type="submit" name="signsubmit" style="border-radius:30px;">Submit</button>
                            </div>

                            <div class="form-acc" style="text-align:center;">
                                <p>Already have account?</p><a href="login.php">Login</a>
                            </div>

                            <div class="form-acc" style="text-align:center;">
                                <p>Back To</p><a href="index.php">Home Page</a>
                            </div>
                        </div>
                    </form>
                </div>

                <script>
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

                        // === Validate password strength ===
                        if (!isStrongPassword(password.value)) {
                            alert("Password must be at least 8 characters and include uppercase, lowercase, number, and special character.");
                            e.preventDefault();
                            return;
                        }

                        // === Validate confirm password ===
                        if (password.value !== confirmPassword.value) {
                            alert("Passwords do not match. Please re-enter.");
                            e.preventDefault();
                            return;
                        }
                    });


    (function () {
        const btn = document.getElementById('requestCodeBtn');
        const emailInput = document.getElementById('email') || document.querySelector('input[name="email"]');
        const msg = document.getElementById('codeMessage');
        if (!btn) return;

        btn.addEventListener('click', async function () {
            try {
                const email = (emailInput && emailInput.value || '').trim();
                if (!email) {
                    alert('Please enter your email first.');
                    return;
                }
                btn.disabled = true;
                btn.textContent = 'Sending...';

                const res = await fetch('send_signup_code.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ email: email })
                });

                let j;
                try {
                    j = await res.json();
                } catch {
                    throw new Error('Non-JSON response');
                }

                if (j.success) {
                    msg.style.display = 'block';
                    msg.style.color = 'green';
                    msg.textContent = 'Verification code sent. It is valid for 1 minute.';
                    let remaining = 60;
                    const orig = 'Request Code';
                    btn.disabled = true;
                    btn.textContent = 'Wait ' + remaining + 's';
                    const t = setInterval(() => {
                        remaining--;
                        btn.textContent = 'Wait ' + remaining + 's';
                        if (remaining <= 0) {
                            clearInterval(t);
                            btn.disabled = false;
                            btn.textContent = orig;
                        }
                    }, 1000);
                } else {
                    msg.style.display = 'block';
                    msg.style.color = 'red';
                    msg.textContent = j.error || 'Failed to send code.';
                    btn.disabled = false;
                    btn.textContent = 'Request Code';
                }
            } catch (err) {
                console.error('Request code error:', err);
                alert('Error sending code. Check console/network.');
                btn.disabled = false;
                btn.textContent = 'Request Code';
            }
        });
    })();

                     </script>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3"
        crossorigin="anonymous"></script>
</body>

</html>