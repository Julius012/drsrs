<?php
session_start();
// Check if the user is logged in and is a supervisor
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'supervisor') {
    // If a logged-in user is not a supervisor, redirect them appropriately
    if (isset($_SESSION['user_type'])) {
        if ($_SESSION['user_type'] == 'admin') {
            header("Location: admindashboard.php");
        } elseif ($_SESSION['user_type'] == 'attachee') {
            header("Location: attacheedashboard.php");
        } else {
            header("Location: login.html");
        }
    } else {
        header("Location: login.html");
    }
    exit();
}

require_once 'db2.php'; // Assumes this file creates $conn

// Query to fetch pending, approved, and rejected reports.
// Here, pending reports are assumed to have approved IS NULL.
$pending_sql  = "SELECT r.id, u.full_name, r.report_type, r.uploaded_at 
                 FROM reports r JOIN users u ON r.user_id = u.id 
                 WHERE r.approved IS NULL 
                 ORDER BY r.uploaded_at DESC";
$approved_sql = "SELECT r.id, u.full_name, r.report_type, r.uploaded_at 
                 FROM reports r JOIN users u ON r.user_id = u.id 
                 WHERE r.approved = 1 
                 ORDER BY r.uploaded_at DESC";
$rejected_sql = "SELECT r.id, u.full_name, r.report_type, r.uploaded_at 
                 FROM reports r JOIN users u ON r.user_id = u.id 
                 WHERE r.approved = 0 
                 ORDER BY r.uploaded_at DESC";

$pending_result  = $conn->query($pending_sql);
$approved_result = $conn->query($approved_sql);
$rejected_result = $conn->query($rejected_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Supervisor Dashboard</title>
  <link rel="stylesheet" href="style.css">
  <style>
    /* Table styling similar to the admin dashboard */
    table {
      width: 100%;
      border-collapse: collapse;
      margin-bottom: 20px;
    }
    table th, table td {
      border: 1px solid #ddd;
      padding: 8px;
    }
    table th {
      background-color: #f2f2f2;
      text-align: left;
    }
    button {
      cursor: pointer;
      padding: 5px 10px;
      margin: 2px;
    }
    .approve {
      background-color: #4CAF50;
      color: white;
      border: none;
    }
    .reject {
      background-color: #f44336;
      color: white;
      border: none;
    }
    a.download-link {
      text-decoration: none;
      color: #2196F3;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <div class="sidebar">
    <h2>Supervisor Panel</h2>
    <ul>
      <li><a href="#review-reports">Review Reports</a></li>
      <li><a href="#approved-reports">Approved Reports</a></li>
      <li><a href="#rejected-reports">Rejected Reports</a></li>
      <li><a href="logout.php">Logout</a></li>
    </ul>
  </div>

  <div class="main-content">
    <header>
      <h1>Welcome, Supervisor</h1>
    </header>

    <!-- Pending Reports Section -->
    <section id="review-reports">
      <h2>Reports Pending Review</h2>
      <table>
        <thead>
          <tr>
            <th>Report ID</th>
            <th>Submitted By</th>
            <th>Report Type</th>
            <th>Date Submitted</th>
            <th>Download</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ($pending_result && $pending_result->num_rows > 0) {
              while ($row = $pending_result->fetch_assoc()) {
                  echo "<tr>
                          <td>{$row['id']}</td>
                          <td>{$row['full_name']}</td>
                          <td>{$row['report_type']}</td>
                          <td>{$row['uploaded_at']}</td>
                          <td><a class='download-link' href='download.php?id={$row['id']}' target='_blank'>Download</a></td>
                          <td>
                            <a href='update_report_status.php?id={$row['id']}&status=approve'>
                              <button class='approve'>Approve</button>
                            </a>
                            <a href='update_report_status.php?id={$row['id']}&status=reject'>
                              <button class='reject'>Reject</button>
                            </a>
                          </td>
                        </tr>";
              }
          } else {
              echo "<tr><td colspan='6'>No reports pending review.</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </section>

    <!-- Approved Reports Section -->
    <section id="approved-reports">
      <h2>Approved Reports</h2>
      <table>
        <thead>
          <tr>
            <th>Report ID</th>
            <th>Submitted By</th>
            <th>Report Type</th>
            <th>Date Submitted</th>
            <th>Download</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ($approved_result && $approved_result->num_rows > 0) {
              while ($row = $approved_result->fetch_assoc()) {
                  echo "<tr>
                          <td>{$row['id']}</td>
                          <td>{$row['full_name']}</td>
                          <td>{$row['report_type']}</td>
                          <td>{$row['uploaded_at']}</td>
                          <td><a class='download-link' href='download.php?id={$row['id']}' target='_blank'>Download</a></td>
                          <td>
                            <a href='update_report_status.php?id={$row['id']}&status=reject'>
                              <button class='reject'>Reject</button>
                            </a>
                          </td>
                        </tr>";
              }
          } else {
              echo "<tr><td colspan='6'>No approved reports yet.</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </section>

    <!-- Rejected Reports Section -->
    <section id="rejected-reports">
      <h2>Rejected Reports</h2>
      <table>
        <thead>
          <tr>
            <th>Report ID</th>
            <th>Submitted By</th>
            <th>Report Type</th>
            <th>Date Submitted</th>
            <th>Download</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ($rejected_result && $rejected_result->num_rows > 0) {
              while ($row = $rejected_result->fetch_assoc()) {
                  echo "<tr>
                          <td>{$row['id']}</td>
                          <td>{$row['full_name']}</td>
                          <td>{$row['report_type']}</td>
                          <td>{$row['uploaded_at']}</td>
                          <td><a class='download-link' href='download.php?id={$row['id']}' target='_blank'>Download</a></td>
                          <td>
                            <a href='update_report_status.php?id={$row['id']}&status=approve'>
                              <button class='approve'>Approve</button>
                            </a>
                          </td>
                        </tr>";
              }
          } else {
              echo "<tr><td colspan='6'>No rejected reports yet.</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </section>
  </div>
</body>
</html>

<?php
$conn->close();
?>
