<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    header("Location: login.html");
    exit();
}

require_once 'db2.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId   = $_POST['userId'];
    $fullName = $_POST['fullName'];
    $email    = $_POST['email'];
    $role     = $_POST['role'];

    $sql = "UPDATE users SET full_name = ?, email = ?, role = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $fullName, $email, $role, $userId);

    if ($stmt->execute()) {
        header("Location: admindashboard.php");
        exit();
    } else {
        echo "Error updating user: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>
