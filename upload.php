<?php
session_start();

// Redirect to login if the user isn't authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

require_once 'db2.php'; // This file creates the $conn variable

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ensure a file was uploaded without errors
    if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['file']['tmp_name'];
        $fileName    = $_FILES['file']['name'];
        $fileType    = $_FILES['file']['type'];

        // Only allow PDF and DOCX files
        $allowedExtensions = ['pdf', 'docx'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (!in_array($fileExtension, $allowedExtensions)) {
            die("Error: Only PDF and DOCX files are allowed.");
        }

        // Read the file content
        $fileData = file_get_contents($fileTmpPath);

        // Map form report type to ENUM values in your DB
        $reportTypeInput = $_POST['uploadType'];
        $report_type = ($reportTypeInput === 'weekly_report') ? 'weekly' : 'final';

        // Prepare the INSERT query. We bind the file content directly.
        $sql = "INSERT INTO reports (user_id, report_type, report_data, file_name, file_type, approved)
                VALUES (?, ?, ?, ?, ?, 0)";

        if ($stmt = $conn->prepare($sql)) {
            // Directly bind $fileData. This works reliably if the file size is moderate.
            $stmt->bind_param("issss", $user_id, $report_type, $fileData, $fileName, $fileType);
            if ($stmt->execute()) {
                // Output a success message and redirect after 3 seconds
                echo "<!DOCTYPE html>
                <html>
                <head>
                    <meta http-equiv='refresh' content='3;url=attacheedashboard.php'>
                    <title>Upload Successful</title>
                </head>
                <body>
                    <p>File uploaded successfully. Redirecting to dashboard in 3 seconds...</p>
                </body>
                </html>";
            } else {
                echo "Error executing query: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error preparing statement: " . $conn->error;
        }
    } else {
        echo "Error: " . $_FILES['file']['error'];
    }
} else {
    echo "Invalid request.";
}
?>
