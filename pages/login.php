<?php
require_once('../config/db.php');

if (isset($_POST['login'])) {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM User WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if ($user['password'] === $password || password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            header("Location: dashboard.php");
            exit();
        }
    }
    echo "<script>alert('Invalid email or password');</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    include('../config/bootstrap.php');
    ?>
    <link rel="stylesheet" href="login.css">
    <title>Document</title>
</head>
<body>

     <nav class="shadow navbar custom-navbar sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="../index.php">
                <img src="../assets/logo.png" width="45" height="45" class="d-inline-block align-middle me-2">
                FOUND-IT!
            </a>
            <ul class="navbar-nav flex-row flex-wrap bd-navbar-nav">
                <li nav-item col-6 col-lg-auto>
                    <a class="navbar-brand d-flex align-items-center" href="../index.php">Home</a>
                </li>
                <li nav-item col-6 col-lg-auto>
                    <a class="navbar-brand d-flex align-items-center" href="dashboard.php">Dashboard</a>
                </li>

                <li nav-item col-6 col-lg-auto>
                    <a class="navbar-brand d-flex align-items-center" href="aboutus.php">About Us</a>
                </li>

                <li nav-item col-6 col-lg-auto>
                    <a class="navbar-brand d-flex align-items-center" href="faqs.php">FAQs</a>
                </li>
        </div>
    </nav>

    <div class="container d-flex justify-content-center align-items-center min-vh-100=">
        <div class="col-md-6">
            <div class="center-text">

                <div class="login-container">
                    <div class="login-form">

                        <img src="../assets/logo.png" alt="logo" style="width:150px" ; margin-bottom:20px;>
                        <form method="POST">
                            <input type="email" name="email" placeholder="Email" required>
                            <input type="password" name="password" placeholder="Password" required>
                            <button type="submit" name="login">Login</button>
                        </form>
                        <p>Don't have an account? <a href="register.php" class="a-link">Register here</a></p>

                    </div>

                </div>

            </div>
        </div>
    </div>
    
</body>
</html>