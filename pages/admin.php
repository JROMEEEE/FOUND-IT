<?php 
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

require_once('../config/db.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Items - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="admin_panel.css">
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark shadow sticky-top">
    <div class="container-fluid">
        <a class="navbar-brand d-flex align-items-center" href="../index.php">
            <img src="../assets/logo.png" width="45" height="45" class="me-2">
            FOUND-IT!
        </a>
        <div class="d-flex">
            <a class="nav-link text-white me-3" href="dashboard.php">Dashboard</a>
            <a class="nav-link text-white me-3" href="logout.php">Logout</a>
        </div>
    </div>
</nav>

<h2>Admin Dashboard</h2>
<section class="section">
    <div class="container">
        <h3>Manage Lost Items</h3>
        <?php
        // Fetch all lost items
        $sql = "SELECT * FROM claimsubmissions ORDER BY claim_id DESC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0): ?>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Date</th>
                            <th>Description</th>
                            <th>Unique Features</th>
                            <th>Reporter Name</th>
                            <th>Reporter Email</th>
                            <th>Reporter Phone</th>
                            <th>Image</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['category']); ?></td>
                                <td><?php echo htmlspecialchars($row['date']); ?></td>
                                <td><?php echo htmlspecialchars($row['description']); ?></td>
                                <td><?php echo htmlspecialchars($row['unique_features']); ?></td>
                                <td><?php echo htmlspecialchars($row['full_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['email_address']); ?></td>
                                <td><?php echo htmlspecialchars($row['contact_number']); ?></td>
                                <td>
                                    <?php if (!empty($row['supporting_image'])): ?>
                                        <img src="../<?php echo htmlspecialchars((string)$row['supporting_image'] ?? ''); ?>" alt="Supporting Image" class="item-image">
                                    <?php else: ?>
                                        No Image
                                    <?php endif; ?>
                                </td>
                                <td id="status-<?php echo $row['claim_id']; ?>"><?php echo ucfirst(htmlspecialchars($row['status'])); ?></td>
                                <td>
                                    <div class="action-buttons">
                                        <button onclick="updateStatus('approve', <?php echo $row['claim_id']; ?>)" class="btn btn-success">Approve</button>
                                        <button onclick="updateStatus('reject', <?php echo $row['claim_id']; ?>)" class="btn btn-danger">Reject</button>
                                    </div>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p>No lost items to manage.</p>
        <?php endif; ?>

        <!-- Back to Dashboard Button -->
        <a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
    </div>
</section>

<script>
    // Function to update the status via AJAX
    function updateStatus(action, lostId) {
        const xhr = new XMLHttpRequest();
        xhr.open("GET", `process_item.php?action=${action}&claim_id=${lostId}`, true);
        xhr.onload = function () {
            if (xhr.status === 200) {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                    // Update the status in the table
                    document.getElementById(`status-${lostId}`).innerText = response.new_status;
                } else {
                    alert(response.message);
                }
            } else {
                alert("An error occurred while processing the request.");
            }
        };
        xhr.onerror = function () {
            alert("An error occurred while connecting to the server.");
        };
        xhr.send();
    }
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="admin.js"></script>

</body>
</html>