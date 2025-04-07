<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Content-Type: application/json");
    echo json_encode(["success" => false, "message" => "Unauthorized access."]);
    exit();
}

require_once('../config/db.php');

if (isset($_GET['action']) && isset($_GET['lost_id'])) {
    $action = $_GET['action'];
    $lost_id = intval($_GET['lost_id']);

    // Determine the new status based on the action
    $new_status = '';
    if ($action == 'approve') {
        $new_status = 'approved';
    } elseif ($action == 'reject') {
        $new_status = 'rejected';
    } elseif ($action == 'pending') {
        $new_status = 'pending';
    } else {
        header("Content-Type: application/json");
        echo json_encode(["success" => false, "message" => "Invalid action."]);
        exit();
    }

    // Update the status in the database
    $sql = "UPDATE Lost_Item SET status = ? WHERE lost_id = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("si", $new_status, $lost_id);
        if ($stmt->execute()) {
            header("Content-Type: application/json");
            echo json_encode(["success" => true, "new_status" => $new_status]);
        } else {
            header("Content-Type: application/json");
            echo json_encode(["success" => false, "message" => "Failed to update status."]);
        }
    } else {
        header("Content-Type: application/json");
        echo json_encode(["success" => false, "message" => "SQL error: " . $conn->error]);
    }
} else {
    header("Content-Type: application/json");
    echo json_encode(["success" => false, "message" => "Invalid request."]);
}
?>