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

<?php include('../includes/navbar.php') ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
    
<h2 class="display-3 text-center mt-5 mb-5">Claim Found Items</h2>
    <div class="container mb-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="items-list">
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <div class="card mb-4">
                                <div class="card-body">
                                    <h5 class="card-title"><?php echo htmlspecialchars($row['category']); ?></h5>
                                    <p class="card-text"><strong>Description:</strong> <?php echo htmlspecialchars($row['description']); ?></p>
                                    <p class="card-text"><strong>Found at:</strong> <?php echo htmlspecialchars($row['location']); ?></p>
                                    <p class="card-text"><strong>Date Found:</strong> <?php echo htmlspecialchars($row['date_found']); ?></p>
                                    <form action="claim.php" method="POST">
                                        <input type="hidden" name="found_id" value="<?php echo htmlspecialchars($row['found_id']); ?>">
                                        <button type="submit" name="claim" class="btn btn-primary">Claim This Item</button>
                                    </form>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="no-items text-center">No unclaimed items found.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
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

</body>
</html>