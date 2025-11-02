<?php include_once("componet/conn.php"); ?>

<?php
$error = false;
if (isset($_REQUEST["loginbtn"])) {
    $email = $_REQUEST["email"];
    $pwd = $_REQUEST["pwd"];

    $sq = "select * from users where email='{$email}';";
    $exc = $con->query($sq);

    if (mysqli_num_rows($exc) == 1) {
        $data = mysqli_fetch_assoc($exc);
        if ($data['status'] == 0) {
            if (password_verify($pwd, $data["password"])) {
                session_start();
                $_SESSION["login"] = true;
                $_SESSION["username"] = $data["name"];
                $_SESSION["id"] = $data["id"];
                header("location:home.php");
            } else {
                $error = true;
            }
        }
    } else {
        $error = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SQ-TECH SOLVER - Secure QR Code Generator</title>
    <link rel="icon" type="image/png" href="img/log.png">
    <link rel="stylesheet" href="loginsignup.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-Zenh87qX5JnK2Jl0vWa8Ck2rdkQ2Bzep5IDxbcnCeuOxjzrPF/et3URy9Bv1WTRi" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js">
    <title>EZSOP - login</title>
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
        <strong>⚠️ Alert!</strong> Email or Password are incorrect.
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
                        <h1>SQ-TECH SOLVER</h1>
                        <h5>Welcome Back! </h5>
                        <!-- <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut
                            labore et dolore magna aliqua.</p>  -->
                        <!-- <img src="img/loginbg.png" width="300"> -->
                    </div>
                </div>
                <div class="right-side" style="width : 600px;">
                    <form action="login.php" method="post">
                        <div class="forms">
                            <h1 class="forms-heading">Login</h1>
                            <!-- <div class="form-inputs"> <i class="fa fa-user"></i> <input type="text" placeholder="User name"> 
                            </div> -->
                            <p>Email *</p>
                            <div class="form-inputs">
                                <input type="email" autocomplete='chrome-off' name="email" style="border-radius:30px;"
                                    required>
                                <i class="fa fa-envelope"></i>
                            </div>
                            <p>Password *</p>
                            <div class="form-inputs">
                                <input class="password-input" type="password" placeholder="Password" name="pwd"
                                    maxlength="40" style="border-radius:30px;" required>
                                <i class="fa fa-eye" id="password_eye"></i>
                            </div>

                            <div class="submit-button">
                                <button type="submit" name="loginbtn">Login</button>
                            </div>

                            <div>
                                <div class="form-acc">
                                    <p>Don't have account?</p><a href="signup.php">Create Now</a>
                                </div>
                                <div class="form-acc" style="allign-items:center">
                                    <p>Forget Password</p><a href="forgot.php">Click Here</a>
                                </div>
                                <div class="form-acc" style="allign-items:center">
                                    <p>Back To</p><a href="index.php">Home Page</a>
                                </div>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-OERcA2EqjJCMA+/3y+gxIOqMEjwtxJY7qPCqsdltbNJuaOe923+mo//f6V8Qbsw3"
        crossorigin="anonymous"></script>
</body>

</html>