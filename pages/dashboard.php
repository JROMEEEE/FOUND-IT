<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
require_once('../config/db.php');

$sql = "SELECT * FROM lost_item WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();


// Fetch recent lost/found items (limit 5) excluding only approved items
$sql_lost = "SELECT * FROM lost_Item WHERE status != 'approved' ORDER BY date_lost DESC LIMIT 5";
$lost_items = $conn->query($sql_lost);

$sql_found = "SELECT * FROM Found_Item WHERE status != 'approved' ORDER BY date_found DESC LIMIT 5";
$found_items = $conn->query($sql_found);

// Handle search functionality
$search_results = [];
if (isset($_POST['search'])) {
    $search_query = $conn->real_escape_string($_POST['search_query']);
    $sql_search = "SELECT * FROM Found_Item WHERE (category LIKE '%$search_query%' OR description LIKE '%$search_query%') 
                   AND status != 'approved'
                   UNION 
                   SELECT * FROM lost_Item WHERE (category LIKE '%$search_query%' OR description LIKE '%$search_query%')
                   AND status != 'approved'";
    $search_results = $conn->query($sql_search);
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>

    <!-- BOOTSTRAP -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <!-- CUSTOM CSS -->
    <link rel="stylesheet" href="dashboard.css">
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark shadow sticky-top" style="background-color:#d90429;">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="../index.php" >
                <img src="../assets/logo.png" width="45" height="45" class="me-2">
                FOUND-IT!
            </a>
            <div class="d-flex">
                <a class="nav-link text-white me-3" href="logout.php">Logout</a>
                <a class="nav-link text-white" href="../">Go Back</a>
            </div>
        </div>
    </nav>

    <h2 class="display-5 text-center mt-5">Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h2>

    <div class="container my-4 text-center">
        <a href="report_lost.php" class="btn btn-danger mx-2"><i class="fas fa-plus-circle"></i> Report Lost Item</a>
        <a href="report_found.php" class="btn btn-danger mx-2"><i class="fas fa-search"></i> Report Found Item</a>
        <a href="search.php" class="btn btn-danger mx-2"><i class="fas fa-list-alt"></i> Browse All Items</a>
        <?php if ($_SESSION['role'] == 'admin'): ?>
            <a href="admin.php" class="btn btn-danger mx-2"><i class="fas fa-user-shield"></i> Admin Panel</a>
        <?php endif; ?>
    </div>

    <div class="container my-4">
        <div class="search-bar d-flex justify-content-center justify-content-md-end">
            <form method="POST" action="" class="d-flex flex-column flex-md-row align-items-stretch gap-0">
                <input type="text" name="search_query" class="form-control rounded-start-2 rounded-end-0" placeholder="Search for items..." required>
                <button type="submit" name="search" class="btn btn-primary rounded-end-2 rounded-start-0"><i class="fas fa-search"></i> Search</button>
            </form>
        </div>
    </div>

    <?php if (isset($_POST['search'])): ?>
        <div class="container my-4">
            <h3>Search Results</h3>
            <?php if ($search_results && $search_results->num_rows > 0): ?>
                <div class="row">
                    <?php while ($item = $search_results->fetch_assoc()): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card shadow">
                                <div class="card-body">
                                    <h4 class="card-title"><?php echo htmlspecialchars($item['category']); ?></h4>
                                    <p class="card-text"><?php echo htmlspecialchars($item['description']); ?></p>
                                    <small class="text-muted">
                                        <?php echo isset($item['date_found']) ? "Found on: " . htmlspecialchars($item['date_found']) : "Lost on: " . htmlspecialchars($item['date_lost']); ?>
                                    </small>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p>No items match your search query.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <div class="container my-4 bg-secondary shadow-lg text-white p-4 rounded">
        <h3 class="text-center mb-4"><i class="fas fa-box-open"></i> Recently Lost Items</h3>
        <?php if ($lost_items && $lost_items->num_rows > 0): ?>
            <div class="row">
                <?php while ($item = $lost_items->fetch_assoc()): ?>
                    <div class=" col-md-4 mb-4">
                        <div class="card">

                            <?php if (!empty($item['image'])): ?>
                                <img src="../uploads/<?php echo basename($item['image']); ?>" alt="Lost Item Image" class="img-fluid  ">
                            <?php else: ?>
                                <img src="../uploads/no_image.jfif"alt="Lost Item Image" class="img-fluid  ">
                            <?php endif; ?>
                            <div class="card-body">
                                <h4 class="card-title"><?php echo htmlspecialchars($item['category']); ?></h4>
                                <p class="card-text"><?php echo htmlspecialchars($item['description']); ?></p>
                                <small class="text-muted">Lost on: <?php echo htmlspecialchars($item['date_lost']); ?></small>
                                <div class="mt-3 text-center">
                                    <a href="report_found.php?lost_id=<?php echo $item['lost_id']; ?>" class="btn btn-success">Mark as found</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="text-center" style="color: black;">No lost items reported recently.</p>
        <?php endif; ?>
    </div>


    <div class="container my-4 bg-secondary shadow-lg text-white p-4 rounded">
        <h3 class="text-center mb-4"><i class="fas fa-search-location"></i> Recently Found Items</h3>
        <?php if ($found_items && $found_items->num_rows > 0): ?>
            <div class="row">
                <?php while ($item = $found_items->fetch_assoc()): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">

                        <?php if(!empty($item['image'])):?>
                            <img src="../uploads/<?php echo basename($item['image']);?>" alt="Found Item Image" class ="img-fluid">
                            <?php else:?>
                                <img src="../uploads/no_image.jfif" alt="Found Item Image" class="img-fluid">
                                <?php endif;?>
                            
                            <div class="card-body">
                                <h4 class="card-title"><?php echo htmlspecialchars($item['category']); ?></h4>
                                <p class="card-text"><?php echo htmlspecialchars($item['description']); ?></p>
                                <small class="text-muted">Found on: <?php echo htmlspecialchars($item['date_found']); ?></small>
                                <div class="mt-3 text-center">
                                    <?php if ($item['user_id'] != $_SESSION['user_id'] || $_SESSION['role'] == 'admin'): ?>
                                        <a href="claims.php?id=<?php echo $item['found_id']; ?>&type=found" class="btn btn-warning">Claim Item</a>
                                    <?php else: ?>
                                        <span class="text-muted">You reported this item</span>
                                    <?php endif;?>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="text-center" style="color:black;">No found items reported recently.</p>
        <?php endif; ?>
    </div>

    <!-- jQuery and Bootstrap JS -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Include logout modal -->
    <?php include('../includes/logout_modal.php'); ?>

</body>

</html>