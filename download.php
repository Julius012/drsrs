<?php
session_start();

// Redirect to login if the user isn't authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'db2.php'; // Assumes this file creates $conn

if (isset($_GET['id'])) {
    $reportId = intval($_GET['id']);
    $user_id = $_SESSION['user_id'];
    $userType = $_SESSION['user_type'];

    // If the user is an attachee, restrict file download to their own files.
    // Supervisors (and admins) are allowed to download any file.
    if ($userType === 'attachee') {
        $sql = "SELECT file_name, file_type, report_data FROM reports WHERE id = ? AND user_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $reportId, $user_id);
    } else {
        $sql = "SELECT file_name, file_type, report_data FROM reports WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $reportId);
    }

    if ($stmt) {
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows === 1) {
            $stmt->bind_result($fileName, $fileType, $fileData);
            $stmt->fetch();

            // Set headers to force download or display inline
            header("Content-Type: " . $fileType);
            header("Content-Disposition: attachment; filename=\"" . $fileName . "\"");
            header("Content-Length: " . strlen($fileData));

            echo $fileData;
            exit;
        } else {
            echo "File not found or access denied.";
        }
        $stmt->close();
    } else {
        echo "Error preparing statement: " . $conn->error;
    }
} else {
    echo "No file ID specified.";
}
?>
