<?php
session_start();
// Check if the user is logged in and is an attachee
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'attachee') {
    if (isset($_SESSION['user_type'])) {
        if ($_SESSION['user_type'] == 'admin') {
            header("Location: admindashboard.php");
        } elseif ($_SESSION['user_type'] == 'supervisor') {
            header("Location: supervisordashboard.php");
        } else {
            header("Location: login.html");
        }
    } else {
        header("Location: login.html");
    }
    exit();
}

require_once 'db2.php'; // Assumes this file creates $conn

$user_id = $_SESSION['user_id'];
?>
<!DOCTYPE html>
<html>

<head>
    <title>Attachee Dashboard</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* Basic styling for the notifications panel */
        #notifications-panel {
            background: rgba(255, 255, 255, 0.9);
            padding: 10px;
            margin: 20px auto;
            width: 80%;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        #notifications-panel h2 {
            margin-top: 0;
        }
    </style>
</head>

<body style="background-image: url(DRSRS.jpg);">
    <h1 style="text-align: center; color: white;">Welcome to DRSRS Attachees Repository System</h1>
    <p style="text-align: center; color: white;">Submit all your weekly reports on time.</p>

    <!-- Logout Button -->
    <div style="text-align: center; margin: 10px;">
        <form action="logout.php" method="post">
            <input type="submit" value="Logout">
        </form>
    </div>



    <div class="container">
        <h2>Upload Your Final Project</h2>
        <form action="upload.php" method="post" enctype="multipart/form-data">
            <label for="uploadType">Select Report Type:</label>
            <select id="uploadType" name="uploadType" required>
                <option value="weekly_report">Weekly Report</option>
                <option value="final_report">Final Report</option>
            </select>

            <label for="file">Choose a file:</label>
            <input type="file" id="file" name="file" accept=".pdf,.docx" required>

            <input type="submit" value="Upload">
        </form>
    </div>

    <div class="container">
        <h2>Your Uploaded Reports</h2>
        <div class="project-list">
            <?php
            // Fetch reports uploaded by the logged-in user
            $sql = "SELECT id, file_name FROM reports WHERE user_id = ? ORDER BY uploaded_at DESC";
            if ($stmt = $conn->prepare($sql)) {
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $stmt->bind_result($id, $fileName);

                $found = false;
                while ($stmt->fetch()) {
                    $found = true;
                    echo '<div class="project-item">';
                    // Display report id in square brackets followed by the filename
                    echo '<a href="download.php?id=' . $id . '" download>[' . $id . '] ' . htmlspecialchars($fileName) . '</a>';
                    echo '</div>';
                }
                $stmt->close();

                if (!$found) {
                    echo "<p>You haven't uploaded any reports yet.</p>";
                }
            } else {
                echo "Error preparing statement: " . $conn->error;
            }
            ?>
        </div>
    </div>

    <!-- Notifications Panel -->
    <div id="notifications-panel">
        <h2>Notifications</h2>
        <div id="notifications">
            <!-- Notifications will be loaded here via AJAX -->
            <p>Loading notifications...</p>
        </div>
    </div>


    <footer>
        <h3>About DRSRS</h3>
        <p>PO Address: 47146-00100</p>
        <p>Physical Address: Popo Road off Mombasa Road South C</p>
        <p>Contact Number: 0700000123</p>
    </footer>

    <!-- JavaScript for AJAX polling -->
    <script>
        function fetchNotifications() {
            fetch('fetch_notifications.php')
                .then(response => response.text())
                .then(data => {
                    document.getElementById('notifications').innerHTML = data;
                })
                .catch(error => console.error('Error fetching notifications:', error));
        }
        // Poll every 5 seconds
        setInterval(fetchNotifications, 5000);
        // Initial fetch when page loads
        window.onload = fetchNotifications;
    </script>
</body>

</html>
<?php
$conn->close();
?>