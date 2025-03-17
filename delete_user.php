<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: login.html");
    exit();
}

require_once 'db2.php';

if (isset($_GET['id'])) {
    $userId = intval($_GET['id']);

    $sql = "DELETE FROM users WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);

    if ($stmt->execute()) {
        header("Location: admindashboard.php");
        exit();
    } else {
        echo "Error deleting user: " . $conn->error;
    }
    $stmt->close();
    $conn->close();
} else {
    header("Location: admindashboard.php");
    exit();
}
?>
