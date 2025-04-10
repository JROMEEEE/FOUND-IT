<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
} 
require_once('../config/db.php');

// Get item details from URL parameters
$item_id = isset($_GET['id']) ? $_GET['id'] : null;
$item_type = isset($_GET['type']) ? $_GET['type'] : null; // 'lost' or 'found'

if (!$item_id || !$item_type) {
    header("Location: dashboard.php");
    exit();
}

// Fetch item details based on type
if ($item_type === 'lost') {
    $sql = "SELECT * FROM Lost_Item WHERE lost_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $item_id);
} else {
    $sql = "SELECT * FROM Found_Item WHERE found_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $item_id);
}

$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();

if (!$item) {
    header("Location: dashboard.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category = $conn->real_escape_string($item['category']);
    $date = date('Y-m-d');
    $description = $conn->real_escape_string($item['description']);
    $unique_features = $conn->real_escape_string($_POST['claim_reason']);
    $full_name = $conn->real_escape_string($_POST['contact_name']);
    $email_address = $conn->real_escape_string($_POST['contact_email']);
    $contact_number = $conn->real_escape_string($_POST['contact_number']);
    
    // Handle image upload
    $supporting_image = null;
    if (isset($_FILES['claim_image']) && $_FILES['claim_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/claims/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        
        $file_extension = strtolower(pathinfo($_FILES['claim_image']['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($file_extension, $allowed_extensions)) {
            $unique_filename = uniqid() . '.' . $file_extension;
            $target_path = $upload_dir . $unique_filename;
            
            if (move_uploaded_file($_FILES['claim_image']['tmp_name'], $target_path)) {
                $supporting_image = 'uploads/claims/' . $unique_filename;
            }
        }
    }
    
    // Insert claim into database
    $sql = "INSERT INTO claimsubmissions (category, date, description, unique_features, supporting_image, full_name, email_address, contact_number) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssss", $category, $date, $description, $unique_features, $supporting_image, $full_name, $email_address, $contact_number);
    
    if ($stmt->execute()) {
        $_SESSION['success_message'] = "Your claim has been submitted successfully!";
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "Error submitting claim. Please try again.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="claims.css"> <!-- Link to the external CSS -->
    <title>Submit Claim - FOUND-IT!</title>
</head>

<body>
<nav class="shadow navbar custom-navbar sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="../index.php">
            <img src="../assets/logo.png" width="45" height="45" class="d-inline-block align-middle me-2">
            FOUND-IT!
        </a>
        <ul class="navbar-nav flex-row flex-wrap bd-navbar-nav">
            <li class="nav-item col-6 col-lg-auto">
                <a class="navbar-brand d-flex align-items-center" href="logout.php">Logout</a>
            </li>
            <li class="nav-item col-6 col-lg-auto">
                <a class="navbar-brand d-flex align-items-center" href="dashboard.php">Dashboard</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">Submit Claim</h2>
                    
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    
                    <div class="item-details mb-4">
                        <div class="detail-box">
                            <label>Category</label>
                            <div class="detail-value">
                                <?php echo htmlspecialchars($item['category']); ?>
                            </div>
                        </div>

                        <div class="detail-box">
                            <label>Date</label>
                            <div class="detail-value">
                                <?php 
                                    echo $item_type === 'lost' 
                                        ? htmlspecialchars($item['date_lost']) 
                                        : htmlspecialchars($item['date_found']); 
                                ?>
                            </div>
                        </div>
                    </div>

                    <form method="POST" action="" enctype="multipart/form-data">
                        <div class="mb-3">
                            <label for="claim_reason" class="form-label"><strong>Description</strong></label>
                            <textarea class="form-control" id="claim_reason" name="claim_reason" rows="4" required></textarea>
                            <div class="form-text">Please specify the unique features of the item that would help us identify that it is yours.</div>
                        </div>

                        <div class="mb-3">
                            <label for="claim_image" class="form-label"><strong>Upload Supporting Image</strong></label>
                            <input type="file" class="form-control" id="claim_image" name="claim_image" accept="image/*">
                            <div class="form-text">Upload an image that helps prove your claim (optional). Supported formats: JPG, JPEG, PNG, GIF</div>
                        </div>

                        <div class="mb-3">
                            <label for="contact_info" class="form-label">Contact Information</label>
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label for="contact_name" class="form-label">Full Name</label>
                                    <input type="text" class="form-control" id="contact_name" name="contact_name" required>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="contact_email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="contact_email" name="contact_email" required>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label for="contact_number" class="form-label">Contact Number</label>
                                    <input type="tel" class="form-control" id="contact_number" name="contact_number" required>
                                </div>
                            </div>
                            <div class="form-text">Please provide your contact details so we can reach you about your claim.</div>
                        </div>

                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-success">Submit Claim</button>
                            <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Include logout modal -->
<?php include('../includes/logout_modal.php'); ?>

</body>
</html>