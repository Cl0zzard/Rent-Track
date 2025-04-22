<?php
include '../connect.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once "../phpmailer/PHPMailer.php";
require_once "../phpmailer/SMTP.php";
require_once "../phpmailer/Exception.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $tenantname = $_POST['tenantname'];
    $monthly = $_POST['monthly'];
    $email = $_POST['email'];
    $phonenumber = $_POST['phonenumber'];
    $location = $_POST['location'];
    $manager_name = $_POST['manager_name'];
    $url = "../stall-slots";
    $alert = "<script>window.history.back();</script>";  // Go back to the previous page when alert is closed

    $confirmation_token = bin2hex(random_bytes(16)); // Generate a unique token

    // If there's a stall_slots_id, update the record
    if (isset($_POST['stall_slots_id']) && !empty($_POST['stall_slots_id'])) {

        $stall_slots_id = $_POST['stall_slots_id'];

        $sqlUpdate = "UPDATE stall_slots SET tenantname = :tenantname, monthly = :monthly, email = :email, phonenumber = :phonenumber, location = :location, manager_name = :manager_name, confirmation_token = :confirmation_token WHERE stall_slots_id = :stall_slots_id";

        $updateQuery = $conn->prepare($sqlUpdate);
        $updateQuery->bindParam(':tenantname', $tenantname);
        $updateQuery->bindParam(':monthly', $monthly);
        $updateQuery->bindParam(':email', $email);
        $updateQuery->bindParam(':phonenumber', $phonenumber);
        $updateQuery->bindParam(':location', $location);
        $updateQuery->bindParam(':manager_name', $manager_name);
        $updateQuery->bindParam(':confirmation_token', $confirmation_token);
        $updateQuery->bindParam(':stall_slots_id', $stall_slots_id);

        if ($updateQuery->execute()) {
            echo "<script>alert('Updated Tenant Successfully!');</script>";
            echo $alert;
            exit();
        } else {
            echo "<script>alert('Updating Tenant Failed!');</script>";
            echo $alert;
            exit();
        }

    } else {

        // Check if the tenant name already exists
        $sqlSelect = "SELECT 1 FROM stall_slots WHERE tenantname = :tenantname LIMIT 1";
        $slctQuery = $conn->prepare($sqlSelect);
        $slctQuery->bindParam(':tenantname', $tenantname);
        $slctQuery->execute();

        if ($slctQuery->rowCount() > 0) {
            echo "<script>alert('Stall Name Already Exists!');</script>";
            echo $alert;
            exit();
        }

        // Insert new tenant and send confirmation email
        $insert = "INSERT INTO stall_slots (tenantname, monthly, email, phonenumber, location, manager_name, confirmation_token) 
                   VALUES (:tenantname, :monthly, :email, :phonenumber, :location, :manager_name, :confirmation_token)";
        $stmt = $conn->prepare($insert);
        $stmt->bindParam(":tenantname", $tenantname);
        $stmt->bindParam(":monthly", $monthly);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":phonenumber", $phonenumber);
        $stmt->bindParam(":location", $location);
        $stmt->bindParam(":manager_name", $manager_name);
        $stmt->bindParam(":confirmation_token", $confirmation_token);

        if ($stmt->execute()) {
            // Send confirmation email with the token
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = "smtp.gmail.com";
                $mail->SMTPAuth = true;
                $mail->Username = "renttrackusa@gmail.com";
                $mail->Password = 'jkonlajueioaocgj';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                // Recipients
                $mail->setFrom('renttrackusa@gmail.com', "Rent Track");
                $mail->addAddress($email);

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Confirm Your Email';
                $mail->Body = 'Please confirm your email by clicking the following link: 
                                  <a href="http://localhost/Rent-Track/rent-tracking/confirm-email.php?token=' . $confirmation_token . '">Confirm Email</a>';

                $mail->send();
                echo "<script>alert('Tenant added. Have the tenant confirm their email!');</script>";
                echo $alert;
            } catch (Exception $e) {
                echo "<script>alert('Message could not be sent. Mailer Error: {$mail->ErrorInfo}');</script>";
                echo $alert;
            }
            exit();
        } else {
            echo "<script>alert('Adding Tenant Failed!');</script>";
            echo $alert;
            exit();
        }
    }
}
?>