<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once('../config/db.php');

// Handle form submission
if (isset($_POST['submit_found'])) {
    $category = trim($_POST['category']);
    // If "Other" was selected, use the custom category value
    if ($category === "Other" && !empty($_POST['custom_category'])) {
        $category = trim($_POST['custom_category']);
    }
    $description = trim($_POST['description']);
    $location = trim($_POST['location']);
    $date_found = $_POST['date_found'];
    $reporter_name = trim($_POST['reporter_name']);
    $reporter_email = trim($_POST['reporter_email']);
    $reporter_phone = trim($_POST['reporter_phone']);
    $status = trim($_POST['status']);
    $user_id = $_SESSION['user_id'];

    // Handle optional image upload
    $image_path = null;
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "../uploads/";
        $image_path = $target_dir . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image_path);
    }

    // Insert into the database
    $sql = "INSERT INTO found_item (user_id, category, description, location, date_found, status, reporter_name, reporter_email, reporter_phone, image) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("SQL error: " . $conn->error);
    }
    $stmt->bind_param("isssssssss", $user_id, $category, $description, $location, $date_found, $status, $reporter_name, $reporter_email, $reporter_phone, $image_path);

    if ($stmt->execute()) {
        echo "<script>alert('Found item reported successfully!'); window.location='dashboard.php';</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
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
                    <a class="navbar-brand d-flex align-items-center" href="pages/dashboard.php">Dashboard</a>
                </li>

                <li nav-item col-6 col-lg-auto>
                    <a class="navbar-brand d-flex align-items-center" href="pages/aboutus.php">About Us</a>
                </li>

                <li nav-item col-6 col-lg-auto>
                    <a class="navbar-brand d-flex align-items-center" href="pages/faqs.php">FAQs</a>
                </li>
        </div>
    </nav>

    <div class="container mt-5 p-5 shadow-sm">
        <form method="POST">
            <h2 class="display-4 mb-5 text-center">Report Found Item</h2>

            <div class="form-group mb-3">
                <label for="category" class="mb-2">Category</label>
                <select name="category" id="category" class="form-select" required onchange="toggleCustomCategory()">
                    <option value="">Select Category</option>
                    <option value="Electronics">Electronics</option>
                    <option value="Books">Books</option>
                    <option value="ID Cards">ID Cards</option>
                    <option value="Bags">Bags</option>
                    <option value="Clothing">Clothing</option>
                    <option value="Jewelry">Jewelry</option>
                    <option value="Other">Other (Please specify)</option>
                </select>
            </div>
            
            <div class="form-group mb-3" id="customCategoryGroup" style="display: none;">
                <label for="custom_category" class="mb-2">Specify Category</label>
                <input type="text" name="custom_category" id="custom_category" class="form-control" placeholder="Enter custom category">
            </div>
                
            <div class="form-group mb-3">
                <label for="description" class="mb-2">Description</label>
                <textarea name="description" class="form-control" id="description" placeholder="Description (e.g., Blue wallet)" required></textarea>
            </div>
            
            <div class="form-group mb-3">
                <label for="location" class="mb-2">Found Location</label>
                <input type="text" name="location" class="form-control" id="location" placeholder="Where did you find it?" required>
            </div>
            
            <div class="form-group mb-3">
                <label for="date_found" class="mb-2">Date Found</label>
                <input type="date" name="date_found" class="form-control" id="date_found" required>
            </div>
            
            <div class="form-group mb-3">
                <label for="image" class="mb-2">Upload Image (Optional)</label>
                <input type="file" name="image" class="form-control" id="image" accept="image/*">
            </div>
            
            <div class="form-group mb-3">
                <label for="reporter_name" class="mb-2">Contact Information</label>
                <input type="text" name="reporter_name" class="form-control mb-3" id="reporter_name" placeholder="Your Name" required>
                <input type="email" name="reporter_email" class="form-control mb-3" id="reporter_email" placeholder="Your Email" required>
                <input type="tel" name="reporter_phone" class="form-control mb-3" id="reporter_phone" placeholder="Your Phone Number" required>
            </div>
            
            <div class="form-group mb-3">
                <label for="status" class="mb-2">Current Status</label>
                <select name="status" class="form-select" id="status" required>
                    <option value="in_possession">I have the item</option>
                    <option value="turned_in">Turned in to lost and found</option>
                    <option value="left_in_place">Left in place where found</option>
                </select>
            </div>
            
            <div class="form-check mb-4">
                <input type="checkbox" name="terms" class="form-check-input" id="terms" required>
                <label for="terms" class="form-check-label">I agree to the terms and conditions</label>
            </div>
            
            <button type="submit" name="submit_found" class="btn btn-primary">Submit Report</button>
        </form>
    </div>


</body>
</html>