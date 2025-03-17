<?php
session_start();
// Ensure the user is logged in and is a supervisor
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'supervisor') {
    header("Location: login.html");
    exit();
}

require_once 'db2.php';

if (isset($_GET['id']) && isset($_GET['status'])) {
    $reportId = intval($_GET['id']);
    $statusParam = $_GET['status'];

    // Set the approved value: 1 for approve, 0 for reject.
    if ($statusParam === 'approve') {
        $newStatus = 1;
        $actionText = "approved";
    } elseif ($statusParam === 'reject') {
        $newStatus = 0;
        $actionText = "rejected";
    } else {
        header("Location: supervisordashboard.php");
        exit();
    }

    // Update the report status
    $sql = "UPDATE reports SET approved = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ii", $newStatus, $reportId);

    if ($stmt->execute()) {
        // Fetch the attachee (user_id) for the report so we can send a notification.
        $stmt->close();
        $sql = "SELECT user_id FROM reports WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $reportId);
        $stmt->execute();
        $stmt->bind_result($attacheeId);
        $stmt->fetch();
        $stmt->close();

        // Create a notification message. You can customize this text.
        $message = "Your report (ID: $reportId) was $actionText by the supervisor.";

        // Insert the notification into the notifications table.
        $sql = "INSERT INTO notifications (user_id, report_id, status, message) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iiis", $attacheeId, $reportId, $newStatus, $message);
        $stmt->execute();
        $stmt->close();

        header("Location: supervisordashboard.php");
        exit();
    } else {
        echo "Error updating report status: " . $conn->error;
    }
    $conn->close();
} else {
    header("Location: supervisordashboard.php");
    exit();
}
?>
