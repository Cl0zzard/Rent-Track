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
    $email = $_POST['email']; // Email or Phone Number

    // Validate input
    if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Check if email exists in the database
        $stmt = $conn->prepare("SELECT * FROM admin_account WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
    }

    if ($user) {
        // Generate OTP
        $resetToken = bin2hex(random_bytes(50)); // 100-character hexadecimal token

        $stmt = $conn->prepare("UPDATE admin_account SET resetToken = ? WHERE email = ?");
        $stmt->execute([$resetToken, $email]);

        if (sendOTPEmail($email, $resetToken)) {
            echo "<script>alert('Please Check your email!');</script>";
        } else {
            echo "<script>alert('Instruction failed to sent!');</script>";
        }
    } else {
        echo "<script>alert('Email not found!');</script>";
    }
}

// Function to send OTP email using PHPMailer
function sendOTPEmail($email, $resetToken) {
    $mail = new PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPAuth = true;
        $mail->Username = "kkit8588@gmail.com"; // Your email address
        $mail->Password = 'aiorrgpinpteusih'; // Your email password
        $mail->SMTPSecure = "tls";
        $mail->Port = 587;

        $mail->setFrom('kkit8588@gmail.com', "Rent Track");
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = "Request to Change Password";
        $mail->Body = "<p>Hello,</p>
               <p>We received a request to change the password for your Rent Track account.</p>
               <p>If you did not make this request, you can ignore this email.</p>
               <p>To reset your password, click the link below:</p>
               <p><a href='http://localhost/rent-tracking/reset-password?token=$resetToken&email=$email' style='color: #1d72b8; text-decoration: none;'>Reset Password Here</a></p>
               <br>
               <p>Thank you for using Rent Track!</p>
               <br>
               <p>Best regards,</p>
               <p><strong>The Rent Track Team</strong></p>";

        $mail->send();
        return true;
    } catch (Exception $e) {
        // Optionally log the error message: error_log($e->getMessage());
        return false;
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
                            <h5 class="fw-bold">Forgot password?</h5>
                            <small>
                                <a href="login" class="text-decoration-none text-primary ">Back to login</a>
                            </small>
                        </div>
                        <div class="d-flex flex-column row-gap-3">
                            <small>Please enter your working email for the Instruction</small>
                            <div class="form-floating mb-3">
                                <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                                <label for="email">Email</label>
                            </div>
                            <div class="d-flex flex-column text-center">
                                <button type="submit" class="w-100 fw-bold btn btn-primary">Continue</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'plugins-footer.php'; ?>
