<?php require_once('config/db.php'); // Include the database connection ?>

<?php
$sql = "SELECT * FROM lost_item WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

// Fetch recent lost/found items (limit 5)
$sql_lost = "SELECT * FROM Lost_Item ORDER BY date_lost DESC LIMIT 5";
$lost_items = $conn->query($sql_lost);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="styles.css">
    <title>Found-IT!</title>
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

                <li nav-item col-6 col-lg-auto>
                    <a class="navbar-brand d-flex align-items-center" href="pages/aboutus.php">About Us</a>
                </li>

                <li nav-item col-6 col-lg-auto>
                    <a class="navbar-brand d-flex align-items-center" href="pages/faqs.php">FAQs</a>
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
                <a href="pages/search.php" class="btn btn-primary">Search for an Item </a>
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

            

            <?php if ($lost_items && $lost_items->num_rows > 0): ?>
                <div class="row">
                    <?php while ($item = $lost_items->fetch_assoc()): ?>
                        <div class=" col-md-3 mb-3">
                        <div class="card shadow">

                        <?php if (!empty($item['image'])): ?>
                                    <img src="uploads/<?php echo basename($item['image']); ?>" alt="Lost Item Image" class="img-fluid"style="width: 100%; height: 200px; object-fit: cover; object-position: center;">
                                    
                                <?php else: ?>
                                    <img src="uploads/no_image.jfif" alt="Lost Item Image" class="img-fluid" style="width: 100%; height: 200px; object-fit: cover; object-position: center;">
                                <?php endif; ?>
                                <div class="card-body">
                                    <h4 class="card-title"><?php echo htmlspecialchars($item['category']); ?></h4>
                                    <p class="card-text" style="margin-bottom:-5px;"><?php echo htmlspecialchars($item['description']); ?></p>
                                    <small class="text-muted">Lost on: <?php echo htmlspecialchars($item['date_lost']); ?></small>
                                    <div class="mt-3 text-center">
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p class="text-center">No lost items reported recently.</p>
            <?php endif; ?>
        </div>

</body>
</html>