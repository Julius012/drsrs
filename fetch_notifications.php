<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'attachee') {
    // Only attachee users should fetch notifications
    exit();
}

require_once 'db2.php';

$user_id = $_SESSION['user_id'];

$sql = "SELECT id, report_id, status, message, created_at FROM notifications WHERE user_id = ? ORDER BY created_at DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$output = '';
if ($result->num_rows > 0) {
    // Build a list of notifications
    while ($row = $result->fetch_assoc()) {
        // Use a green check for approved and red cross for rejected.
        if ($row['status'] == 1) {
            $icon = '<span style="color:green;">&#10004;</span>';  // green checkmark
        } else {
            $icon = '<span style="color:red;">&#10008;</span>';      // red cross
        }
        $output .= '<div class="notification-item" style="padding:10px; margin-bottom:5px; border:1px solid #ddd; border-radius:4px;">';
        $output .= $icon . ' ' . htmlspecialchars($row['message']);
        // Add a download button/link for the report
        $output .= ' <a href="download.php?id=' . $row['report_id'] . '" target="_blank" style="padding:3px 6px; background:#2196F3; color:white; text-decoration:none; border-radius:3px; margin-left:10px;">Download Report</a>';
        $output .= '<br><small>' . $row['created_at'] . '</small>';
        $output .= '</div>';
    }
} else {
    $output = '<p>No notifications yet.</p>';
}

echo $output;

$stmt->close();
$conn->close();
?>