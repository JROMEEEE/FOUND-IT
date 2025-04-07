<?php require_once('config/db.php'); // Include the database connection ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <title>Document</title>
</head>
<body>

<nav class="shadow navbar custom-navbar sticky-top">
      <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="index.php">
          <img src="assets/logo.png" width="45" height="45" class="d-inline-block align-middle me-2">
          FOUND-IT!
        </a>
        <ul class="navbar-nav flex-row flex-wrap bd-navbar-nav">
          <li nav-item col-6 col-lg-auto>
            <a class="navbar-brand d-flex align-items-center" href="index.php">Home</a>
          </li>
          <li nav-item col-6 col-lg-auto>
            <a class="navbar-brand d-flex align-items-center" href="pages/dashboard.php">Dashboard</a>
          </li>
      </div>
</nav>

<!-- HERO SECTION -->
<div class="card text-center mt-5 mb-5 w-75 mx-auto shadow-sm">
  <div class="card-body">
    <img src="assets/logo.png" width="320" height="320" class="" alt="Logo">
    <h5 class="card-title display-4 mb-3">Lost Something? Found Something?</h5>
    <p class="card-text">Our platform helps connect people who have lost items with those who have found them.</p>
    <div class="mb-3 mt-3">
        <a href="pages/report_lost.php" class="btn btn-primary">Report Lost Item</a>
        <a href="pages/report_found.php" class="btn btn-primary">Report Found Item</a>
    </div>
  </div>
</div>

<?php
// Fetch recently lost items
$sql_lost = "SELECT * FROM lost_item ORDER BY date_lost DESC LIMIT 4";
$result_lost = $conn->query($sql_lost);

if (!$result_lost) {
    die("Error fetching lost items: " . $conn->error);
}

// Fetch recently found items
$sql_found = "SELECT * FROM found_item ORDER BY date_found DESC LIMIT 4";
$result_found = $conn->query($sql_found);

if (!$result_found) {
    die("Error fetching found items: " . $conn->error);
}
?>

<section class="section">
    <div class="container my-4">
        <h2 class="section-title text-center mb-4">Recently Lost Items</h2>
        
        <div class="dashboard-search mb-4 text-center">
            <!-- <form action="pages/search.php" method="GET" class="form-inline justify-content-center">
                <input type="hidden" name="type" value="lost">
                <input type="text" name="query" class="form-control mr-2" placeholder="Search lost items..." required>
                <button type="submit" class="btn btn-primary mt-3">Search</button>
            </form> -->
            <a href="pages/search.php" class="btn btn-primary">Search for an Item</a>
        </div>
        
        <div class="row">
            <?php if ($result_lost->num_rows > 0): ?>
                <?php while ($row = $result_lost->fetch_assoc()): ?>
                    <div class="col-md-4 mb-4">
                        <div class="card shadow-sm">
                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($row['category']); ?></h5>
                                <p class="card-text"><?php echo htmlspecialchars($row['description']); ?></p>
                                <small class="text-muted">Lost on: <?php echo htmlspecialchars($row['date_lost']); ?></small>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12">
                    <div class="alert alert-warning text-center" role="alert">
                        No lost items found.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>

    
</body>
</html>