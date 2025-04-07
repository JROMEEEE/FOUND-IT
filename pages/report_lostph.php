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

<h2>Report Lost Item</h2>
<section class="section">
    <div class="container">
        
        <div style="margin-bottom: 20px;">
            <a href="../login.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Home
            </a>
        </div>
        
        <form method="POST">
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
                <label>Lost Location</label>
                <input type="text" name="location" class="form-control" placeholder="Where did you lose it?" required>
            </div>
            <div class="form-group">
                <label>Date Lost</label>
                <input type="date" name="date_lost" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Contact Information</label>
                <input type="text" name="reporter_name" class="form-control" placeholder="Your Name" required>
                <input type="email" name="reporter_email" class="form-control" placeholder="Your Email" required>
                <input type="tel" name="reporter_phone" class="form-control" placeholder="Your Phone Number" required>
            </div>
            <div class="form-check">
                <input type="checkbox" name="terms" class="form-check-input" required>
                <label>I agree to the terms and conditions</label>
            </div>
            <button type="submit" name="submit_lost" class="btn btn-primary">Submit Report</button>
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