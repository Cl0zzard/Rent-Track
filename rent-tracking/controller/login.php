<?php
session_start();
include '../connect.php'; // Adjust the path as necessary

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Check if the user exists
    $sqlCheck = "SELECT * FROM admin_account WHERE username = :username";
    $checkQuery= $conn->prepare($sqlCheck);
    $checkQuery->bindParam(':username', $username);
    $checkQuery->execute();
    $user = $checkQuery->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo "<script>alert('User not registered!');</script>";
        echo "<script>window.location.href = '../login';</script>";
        exit;
    } else {
        if ($user['status_archived'] == 1) {

            // Verify the password
            if (password_verify($password, $user['password'])) {
                
                session_regenerate_id(true);
                $_SESSION['admin'] = $user; // Store user info in session

                echo "<script>alert('Success Login!');</script>";
                // Redirect to a protected page or dashboard
                echo "<script>window.location.href = '../dashboard';</script>";
                exit;
            } else {
                echo "<script>alert('Your password is incorrect!');</script>";
                echo "<script>window.location.href = '../login';</script>";
                exit;
            }
        }else{
            echo "<script>alert('Sorry your account is not active!');</script>";
            echo "<script>window.location.href = '../login';</script>";
            exit;
        }
    }
}
?>
