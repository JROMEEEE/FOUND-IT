<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include('../includes/header.php'); 
require_once('../config/db.php');

// Fetch found items that are unclaimed
$sql = "SELECT * FROM Found_Item WHERE status = 'unclaimed'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Claim Found Items</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to bottom, #f9f9f9, #e0e0e0);
            color: #333;
        }

        h2 {
            text-align: center;
            margin-top: 20px;
            color: #2e7d32;
            font-size: 28px;
        }

        .back-btn {
            display: inline-block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #ff6f00;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            text-align: center;
            transition: background-color 0.3s ease;
        }

        .back-btn:hover {
            background-color: #c43e00;
        }

        .items-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }

        .item-card {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .item-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 10px rgba(0, 0, 0, 0.15);
        }

        .item-card h3 {
            font-size: 22px;
            color: #2e7d32;
            margin-bottom: 10px;
        }

        .item-card p {
            font-size: 16px;
            color: #555;
            margin: 5px 0;
        }

        .item-card button {
            margin-top: 15px;
            padding: 10px 20px;
            background-color: #2e7d32;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .item-card button:hover {
            background-color: #005005;
            transform: translateY(-2px);
        }

        .no-items {
            text-align: center;
            font-size: 18px;
            color: #777;
            margin-top: 50px;
        }
    </style>
</head>
<body>

<!-- Back to Dashboard Button -->
<a href="dashboard.php" class="back-btn">Back to Dashboard</a>

<h2>Claim Found Items</h2>

<div class="items-list">
    <?php if ($result->num_rows > 0): ?>
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="item-card">
                <h3><?php echo htmlspecialchars($row['category']); ?></h3>
                <p><strong>Description:</strong> <?php echo htmlspecialchars($row['description']); ?></p>
                <p><strong>Found at:</strong> <?php echo htmlspecialchars($row['location']); ?></p>
                <p><strong>Date Found:</strong> <?php echo htmlspecialchars($row['date_found']); ?></p>
                <form action="claim.php" method="POST">
                    <input type="hidden" name="found_id" value="<?php echo htmlspecialchars($row['found_id']); ?>">
                    <button type="submit" name="claim">Claim This Item</button>
                </form>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="no-items">No unclaimed items found.</p>
    <?php endif; ?>
</div>

<?php
if (isset($_POST['claim'])) {
    $found_id = $_POST['found_id'];
    $user_id = $_SESSION['user_id'];

    // Insert claim request
    $sql = "INSERT INTO Claim (found_id, user_id, status) VALUES (?, ?, 'pending')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $found_id, $user_id);

    if ($stmt->execute()) {
        // Update the status of the item in the Found_Item table
        $update_sql = "UPDATE Found_Item SET status = 'claimed' WHERE found_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("i", $found_id);

        if ($update_stmt->execute()) {
            echo "<script>alert('Claim request submitted and item status updated!');</script>";
        } else {
            echo "<script>alert('Error updating item status: " . $conn->error . "');</script>";
        }
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}
?>

<?php include('../includes/footer.php'); ?>
</body>
</html>