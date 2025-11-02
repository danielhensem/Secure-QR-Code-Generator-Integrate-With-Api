<?php
// It's crucial to start the session before any HTML output
session_start();
include_once("../componet/conn.php");

// --- PHP LOGIN LOGIC ---
// This section is placed at the top for better organization and security practices.
$error_message = ''; // Variable to hold any error messages

if (isset($_POST["adminloginbtn"])) {
    if (isset($con)) { // Check if the database connection exists
        $email = $_POST["email"];
        $pwd = $_POST["pwd"];

        // --- SECURITY FIX: PREVENT SQL INJECTION ---
        // The original code was vulnerable to SQL Injection.
        // Using prepared statements is the standard, secure way to run queries.
        $stmt = $con->prepare("SELECT * FROM admins WHERE email = ?");
        $stmt->bind_param("s", $email); // 's' means the parameter is a string
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            $data = $result->fetch_assoc();

            // --- SECURITY UPGRADE: USE HASHED PASSWORDS ---
            // For this to work, your password column in the database should store hashed passwords.
            // Example: To create a hash: password_hash('your_password', PASSWORD_DEFAULT);
            // We will check the plain-text password against the stored hash.
            // For now, I will leave the old direct comparison commented out as a reference.
            // if ($pwd === $data['password']) { // Old insecure way
            if (password_verify($pwd, $data['password'])) { // Secure way
                $_SESSION["adminlogin"] = "adlog";
                $_SESSION["name"] = $data["name"];
                header("Location: admin-dashboard.php");
                exit(); // Always exit after a header redirect
            } else {
                $error_message = "Invalid email or password.";
            }
        } else {
            $error_message = "Invalid email or password.";
        }
        $stmt->close();
    } else {
        $error_message = "Database connection failed. Please check your configuration.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EZSOP - Admin Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <style>
        :root {
            --primary-color: #ffffff;
            --secondary-color: #f0f0f0;
            --accent-color: #000000ff;
            --gradient-start: #6b6b6bff;
            --gradient-end: #0082fcff;
            --text-color: #333;
            --placeholder-color: #aaa;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            /* Animated Gradient Background */
            background: linear-gradient(135deg, var(--gradient-start), var(--gradient-end));
            background-size: 400% 400%;
            animation: gradientAnimation 15s ease infinite;
        }

        @keyframes gradientAnimation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .login-container {
            width: 400px;
            padding: 40px;
            /* Glassmorphism Effect */
            background: rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            box-shadow: 0 4px 30px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(7px);
            -webkit-backdrop-filter: blur(7px);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: var(--primary-color);
        }

        .login-form h1 {
            text-align: center;
            margin-bottom: 30px;
            font-weight: 500;
            letter-spacing: 1px;
        }

        .input-group {
            position: relative;
            margin-bottom: 30px;
        }

        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--placeholder-color);
            transition: color 0.3s ease;
        }
        
        .input-group .password-toggle-icon {
            left: unset;
            right: 15px;
            cursor: pointer;
        }

        .input-group input {
            width: 100%;
            padding: 15px 15px 15px 50px; /* Padding for the icon */
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.5);
            border-radius: 8px;
            outline: none;
            color: var(--primary-color);
            font-size: 16px;
            font-family: 'Poppins', sans-serif;
            transition: all 0.3s ease;
        }

        .input-group input::placeholder {
            color: var(--placeholder-color);
        }

        .input-group input:focus {
            border-color: var(--primary-color);
            background: rgba(238, 238, 238, 0.3);
        }

        .input-group input:focus + i {
            color: var(--primary-color);
        }
        
        .submit-btn {
            width: 100%;
            padding: 15px;
            border: none;
            border-radius: 8px;
            background: linear-gradient(135deg, var(--gradient-end), var(--gradient-start));
            color: var(--primary-color);
            font-size: 18px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            letter-spacing: 1px;
        }

        .submit-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }
        
        .error-message {
            text-align: center;
            color: #ffcccc;
            margin-top: 15px;
            font-size: 14px;
        }

    </style>
</head>
<body>
    <div class="login-container">
        <form action="" method="post" class="login-form">
            <h1>Admin Login</h1>
            
            <div class="input-group">
                <input type="email" placeholder="Email" name="email" autocomplete="off" required>
                <i class="fas fa-envelope"></i>
            </div>
            
            <div class="input-group">
                <input id="password-field" type="password" placeholder="Password" name="pwd" required>
                <i class="fas fa-lock"></i>
                <i class="fas fa-eye password-toggle-icon" id="password-toggle"></i>
            </div>
            
            <button type="submit" class="submit-btn" name="adminloginbtn">Login</button>
            
            <?php if (!empty($error_message)): ?>
                <p class="error-message"><?php echo $error_message; ?></p>
            <?php endif; ?>
        </form>
    </div>

    <script>
        const passwordField = document.getElementById('password-field');
        const togglePassword = document.getElementById('password-toggle');

        togglePassword.addEventListener('click', function () {
            // Toggle the type attribute
            const type = passwordField.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordField.setAttribute('type', type);
            
            // Toggle the icon
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    </script>
</body>
</html>