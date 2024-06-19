<?php
include 'db2.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_type = $_POST['userType'];
    $full_name = $_POST['fullName'];
    $institution = $_POST['institution'];
    $academicYear = $_POST['academicYear'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);  // Hash the password for security
    $role = $_POST['role'];

    $sql = "INSERT INTO users (user_type, full_name, institution, academic_year, email, password, role) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssss",$user_type,$full_name, $institution,$academicYear, $email, $password, $role);

    if ($stmt->execute()) {
        echo "Registered successfully!";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>
