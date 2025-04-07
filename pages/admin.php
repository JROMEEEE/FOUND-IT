<?php 
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include('../includes/header.php'); 
require_once('../config/db.php');
?>

<h2>Admin Dashboard</h2>
<section class="section">
    <div class="container">
        <h3>Manage Lost Items</h3>
        <?php
        // Fetch all lost items
        $sql = "SELECT * FROM Lost_Item ORDER BY date_lost DESC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0): ?>
            <div class="table-container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Location</th>
                            <th>Date Lost</th>
                            <th>Status</th>
                            <th>Reporter Name</th>
                            <th>Reporter Email</th>
                            <th>Reporter Phone</th>
                            <th>Image</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['category']); ?></td>
                                <td><?php echo htmlspecialchars($row['description']); ?></td>
                                <td><?php echo htmlspecialchars($row['location']); ?></td>
                                <td><?php echo htmlspecialchars($row['date_lost']); ?></td>
                                <td id="status-<?php echo $row['lost_id']; ?>"><?php echo htmlspecialchars($row['status']); ?></td>
                                <td><?php echo htmlspecialchars($row['reporter_name']); ?></td>
                                <td><?php echo htmlspecialchars($row['reporter_email']); ?></td>
                                <td><?php echo htmlspecialchars($row['reporter_phone']); ?></td>
                                <td>
                                    <?php if (!empty($row['image'])): ?>
                                        <img src="../uploads/<?php echo htmlspecialchars($row['image']); ?>" alt="Item Image" class="item-image">
                                    <?php else: ?>
                                        No Image
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button onclick="updateStatus('approve', <?php echo $row['lost_id']; ?>)" class="btn btn-success">Approve</button>
                                        <button onclick="updateStatus('reject', <?php echo $row['lost_id']; ?>)" class="btn btn-danger">Reject</button>
                                        <button onclick="updateStatus('pending', <?php echo $row['lost_id']; ?>)" class="btn btn-warning">Mark as Pending</button>
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
        xhr.open("GET", `process_item.php?action=${action}&lost_id=${lostId}`, true);
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

<style>
    body {
        font-family: Arial, sans-serif;
        background-color: #f9f9f9;
        color: #333;
        margin: 0;
        padding: 0;
    }

    h2 {
        text-align: center;
        margin-top: 20px;
        color: #2e7d32;
    }

    .container {
        width: 95%; /* Adjusted to occupy 95% of the background */
        margin: 0 auto;
    }

    .table-container {
        overflow-x: auto;
        margin-top: 20px;
    }

    .table {
        width: 100%; /* Table occupies the full width of the container */
        border-collapse: collapse;
        background-color: #fff;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .table th, .table td {
        padding: 20px; /* Increased padding for more spacious cells */
        text-align: left;
        border: 1px solid #ddd;
        font-size: 16px; /* Increased font size for better readability */
    }

    .table th {
        background-color: #2e7d32;
        color: white;
        font-size: 18px;
    }

    .table tr:nth-child(even) {
        background-color: #f9f9f9;
    }

    .table tr:hover {
        background-color: #f1f1f1;
    }

    .item-image {
        width: 150px; /* Increased image size for better visibility */
        height: auto;
        border-radius: 5px;
    }

    .btn {
        padding: 12px 20px; /* Increased button size */
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        font-weight: bold;
        transition: background-color 0.3s ease;
    }

    .btn-success {
        background-color: #28a745;
        color: white;
    }

    .btn-success:hover {
        background-color: #1e7e34;
    }

    .btn-danger {
        background-color: #dc3545;
        color: white;
    }

    .btn-danger:hover {
        background-color: #a71d2a;
    }

    .btn-warning {
        background-color: #ffc107;
        color: white;
    }

    .btn-warning:hover {
        background-color: #e0a800;
    }

    .btn-secondary {
        background-color: #6c757d;
        color: white;
        text-decoration: none;
        padding: 12px 25px;
        border-radius: 5px;
        display: inline-block;
        margin-top: 20px;
    }

    .btn-secondary:hover {
        background-color: #5a6268;
    }

    /* New styles for horizontal buttons */
    .action-buttons {
        display: flex;
        gap: 10px; /* Add spacing between buttons */
        justify-content: flex-start; /* Align buttons to the left */
    }
</style>

<?php include('../includes/footer.php'); ?>