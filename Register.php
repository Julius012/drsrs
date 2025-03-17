<?php
include 'db2.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the selected values from the form using the name attributes.
    $user_type    = $_POST['userType'];    // Will be 'admin', 'mentor', or 'attachee'
    $full_name    = $_POST['fullName'];
    $institution  = $_POST['institution'];
    $academicYear = $_POST['academicYear'];
    $email        = $_POST['email'];
    $password     = password_hash($_POST['password'], PASSWORD_BCRYPT); // Hash the password
    $role         = $_POST['userRole'];    // Will be 'Admin', 'Mentor', or 'Attachee'

    $sql = "INSERT INTO users (user_type, full_name, institution, academic_year, email, password, role) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss", $user_type, $full_name, $institution, $academicYear, $email, $password, $role);

    if ($stmt->execute()) {
        // Redirect after registration (no output before header!)
        header("Location: login.html");
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
        sleep(3);
        header("Location: register.html");
        exit();
    }

    $stmt->close();
    $conn->close();
}
?>
