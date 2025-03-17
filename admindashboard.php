<?php
session_start();
// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] != 'admin') {
    // If a logged-in user is not admin, redirect them appropriately
    if (isset($_SESSION['user_type'])) {
        if ($_SESSION['user_type'] == 'attachee') {
            header("Location: attacheedashboard.php");
        } elseif ($_SESSION['user_type'] == 'mentor' || $_SESSION['user_type'] == 'supervisor') {
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

// Fetch all users from the database
$sql = "SELECT id, full_name, email, role FROM users ORDER BY id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="style.css">
  <style>
    /* Modal styling */
    .modal {
      display: none; 
      position: fixed; 
      z-index: 1000; 
      left: 0;
      top: 0;
      width: 100%; 
      height: 100%; 
      overflow: auto; 
      background-color: rgba(0,0,0,0.4); 
    }
    .modal-content {
      background-color: #fefefe;
      margin: 10% auto;
      padding: 20px;
      border: 1px solid #888;
      width: 40%;
    }
    .close {
      color: #aaa;
      float: right;
      font-size: 28px;
      font-weight: bold;
      cursor: pointer;
    }
    .close:hover,
    .close:focus {
      color: black;
      text-decoration: none;
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    table th, table td {
      border: 1px solid #ddd;
      padding: 8px;
    }
    table th {
      background-color: #f2f2f2;
    }
    button {
      cursor: pointer;
    }
  </style>
  <script>
    // Opens the edit modal and pre-fills it with user data
    function openEditModal(userId, fullName, email, role) {
      document.getElementById('editUserId').value = userId;
      document.getElementById('editFullName').value = fullName;
      document.getElementById('editEmail').value = email;
      document.getElementById('editRole').value = role;
      document.getElementById('editModal').style.display = 'block';
    }
    
    // Closes the modal
    function closeModal() {
      document.getElementById('editModal').style.display = 'none';
    }
    
    // Confirms deletion and redirects if confirmed
    function confirmDelete(userId) {
      if (confirm("Are you sure you want to delete this user?")) {
        window.location.href = "delete_user.php?id=" + userId;
      }
    }
    
    // Close modal when clicking outside of it
    window.onclick = function(event) {
      var modal = document.getElementById('editModal');
      if (event.target == modal) {
        modal.style.display = "none";
      }
    }
  </script>
</head>
<body>
  <div class="sidebar">
    <h2>Admin Panel</h2>
    <ul>
      <li><a href="#manage-users">Manage Users</a></li>
      <li><a href="#generate-reports">Generate Reports</a></li>
      <li><a href="#settings">Settings</a></li>
      <li><a href="logout.php">Logout</a></li>
    </ul>
  </div>

  <div class="main-content">
    <header>
      <h1>Welcome, Admin</h1>
    </header>

    <section id="manage-users">
      <h2>Manage User Accounts</h2>
      <table>
        <thead>
          <tr>
            <th>User ID</th>
            <th>Name</th>
            <th>Email</th>
            <th>Role</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ($result && $result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                  // Use addslashes to escape quotes in full name if needed.
                  $escapedName = addslashes($row['full_name']);
                  echo "<tr>
                          <td>{$row['id']}</td>
                          <td>{$row['full_name']}</td>
                          <td>{$row['email']}</td>
                          <td>{$row['role']}</td>
                          <td>
                              <button onclick=\"openEditModal('{$row['id']}', '{$escapedName}', '{$row['email']}', '{$row['role']}')\">Edit</button>
                              <br><br>
                              <button onclick=\"confirmDelete({$row['id']})\">Delete</button>
                          </td>
                        </tr>";
              }
          } else {
              echo "<tr><td colspan='5'>No users found.</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </section>

    <section id="generate-reports">
      <h2>Generate Report Summary</h2>
      <button onclick="alert('Report is not ready yet!');">Download Report</button>
    </section>
  </div>

  <!-- Modal for editing a user -->
  <div id="editModal" class="modal">
    <div class="modal-content">
      <span class="close" onclick="closeModal()">&times;</span>
      <h2>Edit User</h2>
      <form action="update_user.php" method="post">
        <input type="hidden" id="editUserId" name="userId">
        <div class="form-group">
          <label for="editFullName">Full Name:</label>
          <input type="text" id="editFullName" name="fullName" required>
        </div>
        <div class="form-group">
          <label for="editEmail">Email:</label>
          <input type="email" id="editEmail" name="email" required>
        </div>
        <div class="form-group">
          <label for="editRole">Role:</label>
          <select id="editRole" name="role" required>
            <option value="Admin">Admin</option>
            <option value="Attachee">Attachee</option>
            <option value="Mentor">Mentor</option>
          </select>
        </div>
        <button type="submit">Save Changes</button>
      </form>
    </div>
  </div>
</body>
</html>

<?php
$conn->close();
?>
