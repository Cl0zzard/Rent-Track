<?php include 'plugins-header.php'; ?>
<?php
// Include your database connection here
include 'connect.php';

use PHPMailer\PHPMailer\PHPMailer;

require_once "phpmailer/PHPMailer.php";
require_once "phpmailer/SMTP.php";
require_once "phpmailer/Exception.php";

$message = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = $_GET['email']; 
    $token = $_GET['token']; 
    $password = $_POST['password'];
    $cpassword = $_POST['cpassword'];


    // Validate input
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Check if email exists in the database
        $stmt = $conn->prepare("SELECT * FROM admin_account WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user) {

            // Check if token exists in the database
            $stmt = $conn->prepare("SELECT * FROM admin_account WHERE resetToken = ?");
            $stmt->execute([$token]);
            $user2 = $stmt->fetch();

            if ($user2) {
               
               if ($password === $cpassword) {
                    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

                    $stmt = $conn->prepare("UPDATE admin_account SET password = ? WHERE resetToken = ? AND email = ?");

                    if ($stmt->execute([$hashedPassword, $token, $email])) {
                        echo "<script>alert('Password Reset Successfully!');</script>";
                        echo "<script>window.location.href = 'login';</script>";
                        exit;

                    }
               }else{
                    echo "<script>alert('Password and Confirmpassword Not Same!');</script>";
               }
            }else {
                echo "<script>alert('Invalid Link!');</script>";
            }
        } else {
            echo "<script>alert('Invalid Link!');</script>";
        }
    }

}

?>
<style>
  .login-main-div {
      background: url('images/bg-banner.png') no-repeat center center fixed;
      background-size: cover;
      height: 100vh;
  }

  .rgba-black {
      background: rgba(0, 0, 0, 0.5); /* Dark overlay for better readability */
  }
</style>
<div class="login-main-div" style="height: 100vh;">
    <div class="rgba-black landing-page-wrapper container-fluid d-flex align-items-center justify-content-center h-100">
        <div class="row mx-0 w-100 justify-content-center">
            <div class="col-12 col-lg-7 row mx-0 justify-content-center">
                <div class="col d-none d-xxl-flex flex-column align-items-center justify-content-center text-start text-white">
                    <div>
                        <img src="images/logo.png" width="160px" height="auto">
                    </div>
                    <h1>RentTrack</h1>
                    <strong>Your one-stop solution for managing rental properties</strong>
                </div>
                <div class="col-11 col-sm-9 col-lg-8 col-xxl-5 py-5 px-4 landing-form bg-white shadow border border-red rounded-1">
                    <form method="post" class="d-flex flex-column row-gap-5">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="fw-bold">Reset password?</h5>
                            <small>
                                <a href="login" class="text-decoration-none text-primary ">Back to login</a>
                            </small>
                        </div>
                        <div class="d-flex flex-column row-gap-3">
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" id="password" name="password" placeholder="password" required>
                                <label for="password">Password</label>
                            </div>
                            <div class="form-floating mb-3">
                                <input type="password" class="form-control" id="cpassword" name="cpassword" placeholder="cpassword" required>
                                <label for="cpassword">Confirm Password</label>
                            </div>
                            <div class="d-flex flex-column text-center">
                                <button type="submit" class="w-100 fw-bold btn btn-primary">Reset</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'plugins-footer.php'; ?>
