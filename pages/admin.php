<?php 
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include('../includes/header.php'); 
require_once('../config/db.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
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
        <div class="tab-navigation">
            <button class="tab-button active" data-tab="lost-items" onclick="showTab('lost-items', event)">Lost Items</button>
            <button class="tab-button" data-tab="found-items" onclick="showTab('found-items', event)">Found Items</button>
            <button class="tab-button" data-tab="claim-requests" onclick="showTab('claim-requests', event)">Claim Requests</button>
        </div>

        <!-- Lost Items Tab -->
        <div id="lost-items" class="tab-content active">
            <h3>Manage Lost Items</h3>
            <?php
            $sql = "SELECT * FROM Lost_Item ORDER BY date_lost DESC";
            $result = $conn->query($sql);

            if ($result->num_rows > 0): ?>
                <div class="table-container">
                    <table class="table lost-items-table">
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
                                    <td><?= htmlspecialchars($row['category']) ?></td>
                                    <td><?= htmlspecialchars($row['description']) ?></td>
                                    <td><?= htmlspecialchars($row['location']) ?></td>
                                    <td><?= htmlspecialchars($row['date_lost']) ?></td>
                                    <td id="status-lost-<?= $row['lost_id'] ?>"><?= htmlspecialchars($row['status']) ?></td>
                                    <td><?= htmlspecialchars($row['reporter_name']) ?></td>
                                    <td><?= htmlspecialchars($row['reporter_email']) ?></td>
                                    <td><?= htmlspecialchars($row['reporter_phone']) ?></td>
                                    <td>
                                        <?php if (!empty($row['image'])): ?>
                                            <img src="../uploads/<?= htmlspecialchars($row['image']) ?>" alt="Item Image" class="item-image">
                                        <?php else: ?>
                                            No Image
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button onclick="updateLostItemStatus('approve', <?= $row['lost_id'] ?>)" class="btn btn-success">Approve</button>
                                            <button onclick="updateLostItemStatus('reject', <?= $row['lost_id'] ?>)" class="btn btn-danger">Reject</button>
                                            <button onclick="updateLostItemStatus('pending', <?= $row['lost_id'] ?>)" class="btn btn-warning">Mark as Pending</button>
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
        </div>

        <!-- Found Items Tab -->
        <div id="found-items" class="tab-content">
            <h3>Manage Found Items</h3>
            <?php
            $sql = "SELECT * FROM Found_Item ORDER BY date_found DESC";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0): ?>
                <div class="table-container">
                    <table class="table found-items-table">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Description</th>
                                <th>Location Found</th>
                                <th>Date Found</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['category'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($row['description'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($row['location'] ?? '') ?></td>
                                    <td><?= htmlspecialchars($row['date_found'] ?? '') ?></td>
                                    <td id="status-found-<?= $row['found_id'] ?>"><?= htmlspecialchars($row['status'] ?? 'pending') ?></td>
                                    <td>
                                        <div class="action-buttons">
                                            <button onclick="updateFoundItemStatus('approve', <?= $row['found_id'] ?>)" class="btn btn-success">Approve</button>
                                            <button onclick="updateFoundItemStatus('reject', <?= $row['found_id'] ?>)" class="btn btn-danger">Reject</button>
                                            <button onclick="updateFoundItemStatus('pending', <?= $row['found_id'] ?>)" class="btn btn-warning">Mark as Pending</button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>No found items to manage.</p>
            <?php endif; ?>
        </div>

        <!-- Claim Requests Tab -->
        <div id="claim-requests" class="tab-content">
            <h3>Manage Claim Requests</h3>
            <?php
            $sql = "SELECT c.*, f.category, f.description, f.location, f.date_found 
                    FROM Claim_Request c 
                    JOIN Found_Item f ON c.found_id = f.found_id 
                    ORDER BY c.date_submitted DESC";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0): ?>
                <div class="table-container">
                    <table class="table claim-requests-table">
                        <thead>
                            <tr>
                                <th>Item Details</th>
                                <th>Claimant Info</th>
                                <th>Proof Submitted</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <strong>Category:</strong> <?= htmlspecialchars($row['category'] ?? '') ?><br>
                                        <strong>Description:</strong> <?= htmlspecialchars($row['description'] ?? '') ?><br>
                                        <strong>Location:</strong> <?= htmlspecialchars($row['location'] ?? '') ?><br>
                                        <strong>Date Found:</strong> <?= htmlspecialchars($row['date_found'] ?? '') ?>
                                    </td>
                                    <td>
                                        <strong>Name:</strong> <?= htmlspecialchars($row['claimant_name'] ?? '') ?><br>
                                        <strong>Email:</strong> <?= htmlspecialchars($row['claimant_email'] ?? '') ?><br>
                                        <strong>Phone:</strong> <?= htmlspecialchars($row['claimant_phone'] ?? '') ?><br>
                                        <strong>Unique Features:</strong> <?= htmlspecialchars($row['unique_features'] ?? '') ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($row['proof_image'])): ?>
                                            <button class="btn btn-info" onclick="viewProofImage('<?= htmlspecialchars($row['proof_image']) ?>')">View Image</button>
                                        <?php else: ?>
                                            No Image
                                        <?php endif; ?>
                                    </td>
                                    <td id="status-claim-<?= $row['claim_id'] ?>">
                                        <span class="badge bg-<?= ($row['status'] == 'approved') ? 'success' : (($row['status'] == 'rejected') ? 'danger' : 'warning') ?>">
                                            <?= ucfirst(htmlspecialchars($row['status'] ?? 'pending')) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button onclick="updateClaimStatus('approve', <?= $row['claim_id'] ?>)" class="btn btn-success">Approve</button>
                                            <button onclick="updateClaimStatus('reject', <?= $row['claim_id'] ?>)" class="btn btn-danger">Reject</button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>No claim requests to manage.</p>
            <?php endif; ?>
        </div>

        <a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
    </div>
</section>

<!-- Modal for viewing proof images -->
<div class="modal fade" id="proofImageModal" tabindex="-1" aria-labelledby="proofImageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="proofImageModalLabel">Proof Image</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="proofImageDisplay" src="/placeholder.svg" alt="Proof Image" style="max-width: 100%;">
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="admin.js"></script>

</body>
</html>