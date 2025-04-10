<?php
include('../config/db.php');

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['username']); // Use 'name' instead of 'username'
    $email = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT); // Hash the password
    $role = 'user'; // Default role for new users

    // Check if email already exists
    $check_email = "SELECT * FROM user WHERE email = ?";
    $stmt = $conn->prepare($check_email);

    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Email is already registered!";
        } else {
            // Insert new user
            $sql = "INSERT INTO user (name, email, password, role) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("ssss", $name, $email, $password, $role);

                if ($stmt->execute()) {
                    // Redirect to dashboard after successful registration
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $error = "Something went wrong. Please try again.";
                }
            } else {
                $error = "Error preparing the SQL statement: " . $conn->error;
            }
        }
    } else {
        $error = "Error preparing the SQL statement: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="register.css">
    <title>Register</title>
</head>

<body>


    <?php include '../includes/navbar.php'; ?>

    <div class="container d-flex justify-content-center align-items-center min-vh-100=">
        <div class="col-md-6">
            <div class="center-text">

                <div class="register-container">
                    <div class="register-form">

    
                        <form action="register.php" method="POST">
                        <img src="../assets/logo.png" alt="logo" style="width:150px" ; margin-bottom:20px;>                            <?php if ($error): ?>
                                <div class="message error"><?= $error; ?></div>
                            <?php endif; ?>

                            <?php if ($success): ?>
                                <div class="message success"><?= $success; ?></div>
                            <?php endif; ?>

                            <input type="text" name="username" placeholder="Full Name" required>
                            <input type="email" name="email" placeholder="Email" required>
                            <input type="password" name="password" placeholder="Password" required>
                            <button type="submit">Register</button>
                            <p>Already have an account? <a href="login.php">Login here</a></p>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>

</html>