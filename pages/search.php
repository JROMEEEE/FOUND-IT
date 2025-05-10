<?php  
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Include database configuration
include('../config/db.php'); // Ensure this file contains the $conn variable

$search_query = "";
$results = [];

if (isset($_GET['q'])) {
    $search_query = $_GET['q'];
    $sql = "
        SELECT category, description, location, date_lost, 'Lost' AS type, status 
        FROM Lost_Item 
        WHERE description LIKE ? OR category LIKE ?
        UNION 
        SELECT category, description, location, date_found, 'Found' AS type, status 
        FROM Found_Item 
        WHERE description LIKE ? OR category LIKE ?
    ";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $search_param = "%$search_query%";
        $stmt->bind_param("ssss", $search_param, $search_param, $search_param, $search_param);
        $stmt->execute();
        $result = $stmt->get_result();
        $results = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        die("Error preparing the SQL statement: " . $conn->error);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Lost & Found Items</title>
    <?php
    include('../config/bootstrap.php');
    ?>
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css">
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

    <h2 class="display-3 text-center mt-5">Search Lost & Found Items</h2>  

    <div class="container mb-5">
        <div class="row justify-content-center" style="margin-top: 50px;">
            <div class="col-md-6">
                <form id="search-form" method="GET" action="search.php" class="input-group">
                    <input type="text" id="search-input" name="q" class="form-control" placeholder="Search by item name or category..." value="<?php echo htmlspecialchars($search_query); ?>">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-primary">🔍 Search</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="search-results p-5">
        <?php if (!empty($results)): ?>
            <div class="table-responsive">
                <table id="resultsTable" class="table table-striped table-hover table-bordered">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Location</th>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($results as $item): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($item['category']); ?></td>
                                <td><?php echo htmlspecialchars($item['description']); ?></td>
                                <td><?php echo htmlspecialchars($item['location']); ?></td>
                                <td><?php echo htmlspecialchars($item['date_lost'] ?? $item['date_found']); ?></td>
                                <td><?php echo htmlspecialchars($item['type']); ?></td>
                                <td><?php echo htmlspecialchars($item['status']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php elseif ($search_query): ?>
            <div class="alert alert-warning" role="alert">
                No results found for "<?php echo htmlspecialchars($search_query); ?>".
            </div>
        <?php endif; ?>
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384 -MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
    <script src="https://cdn.datatables.net/2.1