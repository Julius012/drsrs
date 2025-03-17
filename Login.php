<?php
include 'db2.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_PO ST['loginEmail'];
    $password = $_POST['loginPassword'];

    // Updated query to also retrieve the user_type column.
    $sql = "SELECT id, password, user_type FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($id, $hashed_password, $user_type);
        $stmt->fetch();

        if (password_verify($password, $hashed_password)) {
            session_start();
            $_SESSION['user_id'] = $id;
            $_SESSION['user_type'] = $user_type;

            // Redirect based on user type.
            if ($user_type == 'admin') {
                header("Location: admindashboard.php");
            } elseif ($user_type == 'attachee') {
                header("Location: attacheedashboard.php");
            } elseif ($user_type == 'supervisor') {
                header("Location: supervisordashboard.php");
            } else {
                // Fallback: redirect to login if user_type is unrecognized.
                header("Location: login.html");
            }
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No user found with that email.";
    }

    $stmt->close();
    $conn->close();
}
?>
