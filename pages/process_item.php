<?php
session_start();
require_once('../config/db.php');
require_once('../send_email.php');

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && isset($_GET['claim_id'])) {
    $action = $_GET['action'];
    $claim_id = (int)$_GET['claim_id'];
    
    // Validate action
    if (!in_array($action, ['approve', 'reject'])) {
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        exit();
    }
    
    // Set the new status based on action
    $new_status = ($action === 'approve') ? 'Approved' : 'Rejected';
    
    // Update the status in the database
    $sql = "UPDATE claimsubmissions SET status = ? WHERE claim_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_status, $claim_id);
    
    if ($stmt->execute()) {
        // Fetch details from claimsubmissions
        $sql2 = "SELECT full_name, email_address, category, description, unique_features, date, supporting_image FROM claimsubmissions WHERE claim_id = ?";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bind_param("i", $claim_id);
        $stmt2->execute();
        $stmt2->bind_result($full_name, $email_address, $category, $description, $unique_features, $date, $supporting_image);
        $stmt2->fetch();
        $stmt2->close();

        // Fetch found_id from claimsubmissions
        $sql = "SELECT found_id FROM claimsubmissions WHERE claim_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $claim_id);
        $stmt->execute();
        $stmt->bind_result($found_id);
        $stmt->fetch();
        $stmt->close();

        // Fetch founder's email from found_item
        $sql = "SELECT reporter_email FROM found_item WHERE found_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $found_id);
        $stmt->execute();
        $stmt->bind_result($founder_email);
        $stmt->fetch();
        $stmt->close();

        $subject = $action === 'approve' ? 'Your Claim Has Been Approved' : 'Your Claim Has Been Rejected';
        $messageBody = "<h2>Claim Update</h2>";
        $messageBody .= "<p>Dear " . htmlspecialchars($full_name) . ",</p>";
        $messageBody .= "<p>Your claim has been <strong>" . ($action === 'approve' ? 'Approved' : 'Rejected') . "</strong>.</p>";
        $messageBody .= "<p><strong>Claim Details:</strong></p>";
        $messageBody .= "<ul>";
        $messageBody .= "<li>Category: " . htmlspecialchars($category) . "</li>";
        $messageBody .= "<li>Description: " . htmlspecialchars($description) . "</li>";
        $messageBody .= "<li>Unique Features: " . htmlspecialchars($unique_features) . "</li>";
        $messageBody .= "<li>Date: " . htmlspecialchars($date) . "</li>";
        $messageBody .= "</ul>";
        
        if (!empty($supporting_image)) {
            $messageBody .= "<p><strong>Supporting Image:</strong></p>";
            $messageBody .= "<img src='cid:supporting_image' alt='Supporting Image' style='max-width: 100%; height: auto;'>";
        }
        
        $messageBody .= "<p>You may contact the founder of the item at: <a href='mailto:$founder_email'>$founder_email</a></p>";
        
        if ($action === 'approve') {
            $messageBody .= "<p>Your claim has been APPROVED. Please check your email for further instructions.</p>";
        } else {
            $messageBody .= "<p>Your claim has been REJECTED. If you believe this is an error, please contact the admin for clarification.</p>";
        }
        $messageBody .= "<p>Thank you for using FOUND-IT!</p>";

        // Send the email
        sendClaimNotificationEmail($email_address, $full_name, $subject, $messageBody, $supporting_image);

        echo json_encode([
            'success' => true,
            'new_status' => $new_status,
            'message' => 'Status updated and email sent successfully'
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Failed to update status: ' . $conn->error
        ]);
    }
    
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request'
    ]);
}

$conn->close();
?> 