<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('../includes/header.php'); 
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

<h2>Report Found Item</h2>
<section class="section">
    <div class="container">
        <!-- Back to Dashboard Button -->
        <div style="margin-bottom: 20px;">
            <a href="dashboard.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Category</label>
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
                <label>Specify Category</label>
                <input type="text" name="custom_category" id="custom_category" class="form-control" placeholder="Enter custom category">
            </div>
            
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control" placeholder="Description (e.g., Blue wallet)" required></textarea>
            </div>
            <div class="form-group">
                <label>Found Location</label>
                <input type="text" name="location" class="form-control" placeholder="Where did you find it?" required>
            </div>
            <div class="form-group">
                <label>Date Found</label>
                <input type="date" name="date_found" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Upload Image (Optional)</label>
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>
            <div class="form-group">
                <label>Contact Information</label>
                <input type="text" name="reporter_name" class="form-control" placeholder="Your Name" required>
                <input type="email" name="reporter_email" class="form-control" placeholder="Your Email" required>
                <input type="tel" name="reporter_phone" class="form-control" placeholder="Your Phone Number" required>
            </div>
            <div class="form-group">
                <label>Current Status</label>
                <select name="status" class="form-select" required>
                    <option value="in_possession">I have the item</option>
                    <option value="turned_in">Turned in to lost and found</option>
                    <option value="left_in_place">Left in place where found</option>
                </select>
            </div>
            <div class="form-check">
                <input type="checkbox" name="terms" class="form-check-input" required>
                <label>I agree to the terms and conditions</label>
            </div>
            <button type="submit" name="submit_found" class="btn btn-primary">Submit Report</button>
        </form>
    </div>
</section>

<script>
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
</script>

<?php include('../includes/footer.php'); ?>