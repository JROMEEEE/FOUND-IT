<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
} 
require_once('../config/db.php');

// Fetch recent lost/found items (limit 5)
$sql_lost = "SELECT * FROM Lost_Item ORDER BY date_lost DESC LIMIT 5";
$lost_items = $conn->query($sql_lost);

$sql_found = "SELECT * FROM Found_Item ORDER BY date_found DESC LIMIT 5";
$found_items = $conn->query($sql_found);

// Handle search functionality
$search_results = [];
if (isset($_POST['search'])) {
    $search_query = $conn->real_escape_string($_POST['search_query']);
    $sql_search = "SELECT * FROM Found_Item WHERE category LIKE '%$search_query%' OR description LIKE '%$search_query%' 
                   UNION 
                   SELECT * FROM Lost_Item WHERE category LIKE '%$search_query%' OR description LIKE '%$search_query%'";
    $search_results = $conn->query($sql_search);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- BOOTSTRAP IMPORT -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <!-- CSS -->
    <link rel="stylesheet" type="text/css" href="../styles.css">
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
                <a class="navbar-brand d-flex align-items-center" href="logout.php">Logout</a>
            </li>
            <li nav-item col-6 col-lg-auto>
                <a class="navbar-brand d-flex align-items-center" href="../">Go Back</a>
            </li>
      </div>
</nav>

    <h2 class="display-5 text-center mt-5">Welcome, <?php echo $_SESSION['name']; ?>!</h2> 

    <div class="container my-4">
        <div class="header-actions text-center">
            <a href="report_lost.php" class="btn btn-primary mx-2">➕ Report Lost Item</a>
            <a href="report_found.php" class="btn btn-info mx-2">🔍 Report Found Item</a>
            <a href="search.php" class="btn btn-success mx-2">📋 Browse All Items</a>
            <?php if ($_SESSION['role'] == 'admin'): ?>
                <a href="admin.php" class="btn btn-warning mx-2">👑 Admin Panel</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="container my-4">
        <div class="search-bar text-center">
            <form method="POST" action="" class="form-inline justify-content-center">
                <input type="text" name="search_query" class="form-control mr-2" placeholder="Search for items..." required>
                <button type="submit" name="search" class="btn btn-primary mt-3">Search</button>
            </form>
        </div>
    </div>

    <?php if (isset($_POST['search'])): ?>
        <div class="dashboard-section">
            <h3>🔍 Search Results</h3>
            <?php if ($search_results->num_rows > 0): ?>
                <div class="items-grid">
                    <?php while ($item = $search_results->fetch_assoc()): ?>
                        <div class="item-card">
                            <h4><?php echo $item['category']; ?></h4>
                            <p><?php echo $item['description']; ?></p>
                            <small><?php echo isset($item['date_found']) ? "Found on: " . $item['date_found'] : "Lost on: " . $item['date_lost']; ?></small>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p>No items match your search query.</p>
            <?php endif; ?>
        </div>
    <?php endif; ?> 

    <div class="container my-4 bg-secondary shadow-lg">
        <div class="dashboard-section">
            <br>
            <h3 class="text-center mb-4 text-white">⏳ Recently Lost Items</h3>
            <?php if ($lost_items->num_rows > 0): ?>
                <div class="row">
                    <?php while ($item = $lost_items->fetch_assoc()): ?>
                        <div class="col-md-4 mb-4">
                            <div class="card">
                                <div class="card-body">
                                    <h4 class="card-title"><?php echo htmlspecialchars($item['category']); ?></h4>
                                    <p class="card-text"><?php echo htmlspecialchars($item['description']); ?></p>
                                    <small class="text-muted">Lost on: <?php echo htmlspecialchars($item['date_lost']); ?></small>
                                    <?php if ($item['user_id'] == $_SESSION['user_id']): ?>
                                        <form action="claim.php" method="POST" class="mt-3">
                                            <input type="hidden" name="lost_id" value="<?php echo htmlspecialchars($item['lost_id']); ?>">
                                            <button type="submit" name="mark_found" class="btn btn-success">Mark as Found</button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p class="text-center">No lost items reported recently.</p>
            <?php endif; ?>
        </div>
    </div>

<div class="container my-4 bg-secondary shadow-lg">
    <div class="dashboard-section">
        <br>
        <h3 class="text-center mb-4 text-white">✨ Recently Found Items</h3>
        <?php if ($found_items->num_rows > 0): ?>
            <div class="row">
                <?php while ($item = $found_items->fetch_assoc()): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card">
                            <div class="card-body">
                                <h4 class="card-title"><?php echo htmlspecialchars($item['category']); ?></h4>
                                <p class="card-text"><?php echo htmlspecialchars($item['description']); ?></p>
                                <small class="text-muted">Found on: <?php echo htmlspecialchars($item['date_found']); ?></small>
                                <?php if ($item['user_id'] == $_SESSION['user_id']): ?>
                                    <form action="claim.php" method="POST" class="mt-3">
                                        <input type="hidden" name="found_id" value="<?php echo htmlspecialchars($item['found_id']); ?>">
                                        <button type="submit" name="claim" class="btn btn-success">Claim</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p class="text-center">No found items reported recently.</p>
        <?php endif; ?>
    </div>
</div>

</body>
</html>