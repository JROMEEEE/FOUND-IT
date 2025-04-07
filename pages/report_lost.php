<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('../includes/header.php'); 
require_once('../config/db.php');

// Handle form submission
if (isset($_POST['submit_lost'])) {
    $category = trim($_POST['category']);
    // If "Other" was selected, use the custom category value
    if ($category === "Other" && !empty($_POST['custom_category'])) {
        $category = trim($_POST['custom_category']);
    }
    $description = trim($_POST['description']);
    $location = trim($_POST['location']);
    $date_lost = $_POST['date_lost'];
    $reporter_name = trim($_POST['reporter_name']);
    $reporter_email = trim($_POST['reporter_email']);
    $reporter_phone = trim($_POST['reporter_phone']);
    $status = "pending"; // Default status for lost items
    $user_id = $_SESSION['user_id'];

    // Insert into the database
    $sql = "INSERT INTO lost_item (user_id, category, description, location, date_lost, status, reporter_name, reporter_email, reporter_phone) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("SQL error: " . $conn->error);
    }
    $stmt->bind_param("issssssss", $user_id, $category, $description, $location, $date_lost, $status, $reporter_name, $reporter_email, $reporter_phone);

    if ($stmt->execute()) {
        echo "<script>alert('Lost item reported successfully!'); window.location='dashboard.php';</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}
?>

<?php include('../includes/navbar.php'); // NAVBAR ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    <div class='container bg-secondary text-white'>
    <form method="POST" class="container my-4">
        <br>
    <h2 class="display-4 text-center mt-3">Report Lost Item</h2>
    <br>
        <div class="form-group mb-3">
            <label for="category">Category</label>
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
        
        <div class="form-group" id="customCategoryGroup" style="display: none;">
            <label for="custom_category">Specify Category</label>
            <input type="text" name="custom_category" id="custom_category" class="form-control" placeholder="Enter custom category">
        </div>
        
        <div class="form-group mb-3">
            <label for="description">Description</label>
            <textarea name="description" class="form-control" id="description" placeholder="Description (e.g., Blue wallet)" required></textarea>
        </div>
        
        <div class="form-group mb-3">
            <label for="location">Lost Location</label>
            <input type="text" name="location" class="form-control" id="location" placeholder="Where did you lose it?" required>
        </div>
        
        <div class="form-group mb-3">
            <label for="date_lost">Date Lost</label>
            <input type="date" name="date_lost" class="form-control" id="date_lost" required>
        </div>
        
        <div class="form-group">
            <label for="reporter_name">Contact Information</label>
            <input type="text" name="reporter_name" class="form-control mb-3" id="reporter_name" placeholder="Your Name" required>
            <input type="email" name="reporter_email" class="form-control mb-3" id="reporter_email" placeholder="Your Email" required>
            <input type="tel" name="reporter_phone" class="form-control mb-3" id="reporter_phone" placeholder="Your Phone Number" required>
        </div>
        
        <div class="form-check">
            <input type="checkbox" name="terms" class="form-check-input" id="terms" required>
            <label for="terms" class="form-check-label mb-3">I agree to the terms and conditions</label>
        </div>
        
        <button type="submit" name="submit_lost" class="btn btn-primary mb-3">Submit Report</button>
    </form>
    </div>

    <!-- <script>
    function toggleCustomCategory() {
        const categorySelect = document.getElementById('category');
        const customCategoryGroup = document.getElementById('customCategoryGroup');
        
        if (categorySelect.value === 'Other') {
            customCategoryGroup.style.display = 'block';
            document.getElementById('custom_category').required = true;
        } else {
            customCategoryGroup.style.display = 'none';
            document.getElementById('custom_category').required = false;
        }
    }
    </script> -->

</body>
</html>