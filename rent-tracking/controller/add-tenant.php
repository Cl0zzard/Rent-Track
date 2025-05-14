<?php
include '../connect.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once "../phpmailer/PHPMailer.php";
require_once "../phpmailer/SMTP.php";
require_once "../phpmailer/Exception.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $tenantid = $_POST['tenant_account_id'];
    $tenantname = $_POST['tenantname'];
    $monthly = $_POST['monthly'];
    $email = $_POST['email'];
    $phonenumber = $_POST['phonenumber'];
    $location = $_POST['location'];
    $manager_name = $_POST['manager_name'];
    $manager_username = $_POST['manager_username'];
    $manager_password = password_hash($_POST['manager_password'], PASSWORD_DEFAULT);
    $url = "../stall-slots";
    $alert = "<script>window.location.href = '$url';</script>";  // Go back to the previous page when alert is closed

    $confirmation_token = bin2hex(random_bytes(16)); // Generate a unique token


    session_start();  // Start the session
    $edited_by = $_SESSION['admin']['admin_id'];

    // If there's a stall_slots_id, update the record
    if (isset($_POST['stall_slots_id']) && !empty($_POST['stall_slots_id'])) {

        $stall_slots_id = $_POST['stall_slots_id'];

        try {
            $conn->beginTransaction();

            // UPDATE stall_slots
            $sqlUpdate = "UPDATE stall_slots 
                SET tenantname = :tenantname, 
                    monthly = :monthly, 
                    email = :email, 
                    phonenumber = :phonenumber, 
                    location = :location, 
                    manager_name = :manager_name, 
                    confirmation_token = :confirmation_token,
                    edited_by = :edited_by,    
                    date_edited = NOW()         
                WHERE stall_slots_id = :stall_slots_id";

            $updateQuery = $conn->prepare($sqlUpdate);
            $updateQuery->bindParam(':tenantname', $tenantname);
            $updateQuery->bindParam(':monthly', $monthly);
            $updateQuery->bindParam(':email', $email);
            $updateQuery->bindParam(':phonenumber', $phonenumber);
            $updateQuery->bindParam(':location', $location);
            $updateQuery->bindParam(':manager_name', $manager_name);
            $updateQuery->bindParam(':confirmation_token', $confirmation_token);
            $updateQuery->bindParam(':edited_by', $edited_by);
            $updateQuery->bindParam(':stall_slots_id', $stall_slots_id);

            if ($updateQuery->execute()) {
                // UPDATE into admin_account
                $sqlUpdate = "UPDATE admin_account 
                    SET phonenumber = :phonenumber, 
                        name = :name, 
                        email = :email, 
                        address = :tenantname
                    WHERE  admin_id = :tenant_account_id";

                // Prepare the query
                $updateAdminQuery = $conn->prepare($sqlUpdate);
                $updateAdminQuery->bindParam(':phonenumber', $phonenumber);
                $updateAdminQuery->bindParam(':name', $manager_name);
                $updateAdminQuery->bindParam(':email', $email);
                $updateAdminQuery->bindParam(':tenantname', $tenantname);
                $updateAdminQuery->bindParam(':tenant_account_id', $tenantid);

                // Execute the query
                $updateAdminQuery->execute();

                // Commit transaction
                $conn->commit();
                echo "<script>alert('Updated Tenant Successfully!'); window.location.href = '$url';</script>";
                exit();
            } else {
                // Rollback transaction on failure
                $conn->rollBack();
                echo "<script>alert('Updating Tenant Failed!'); window.location.href = '$url';</script>";
                exit();
            }

        } catch (PDOException $e) {
            $conn->rollBack();
            echo "<script>alert(" . json_encode("Error: " . $e->getMessage()) . ");</script>";
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

        // Check if the manager already manages another account
        $sqlCheckManager = "SELECT 1 FROM admin_account WHERE email = :email OR username = :username LIMIT 1";
        $checkQuery = $conn->prepare($sqlCheckManager);
        $checkQuery->bindParam(':email', $email);
        $checkQuery->bindParam(':username', $manager_username);
        $checkQuery->execute();

        if ($checkQuery->rowCount() > 0) {
            echo "<script>alert('User already manages other accounts');</script>";
            echo $alert;
            exit();
        }

        // Then insert into admin_account
        $insertAdmin = "INSERT INTO admin_account (username, password, name, email, address, phonenumber, role) 
        VALUES (:username, :password, :name, :email, :tenantname, :phonenumber, 3)";
        $stmtAdmin = $conn->prepare($insertAdmin);
        $stmtAdmin->bindParam(":username", $manager_username); // you need to define this earlier
        $stmtAdmin->bindParam(":password", $manager_password); // should be hashed
        $stmtAdmin->bindParam(":name", $manager_name);
        $stmtAdmin->bindParam(":email", $email);
        $stmtAdmin->bindParam(":tenantname", $tenantname); // maps to `address`
        $stmtAdmin->bindParam(":phonenumber", $phonenumber);
        $stmtAdmin->execute();

        // Get the new tenant's account ID
        $tenantAccountId = $conn->lastInsertId();

        // Insert new tenant and send confirmation email
        $insert = "INSERT INTO stall_slots (tenantname, monthly, email, phonenumber, location, manager_name, confirmation_token, tenant_account_id) 
                   VALUES (:tenantname, :monthly, :email, :phonenumber, :location, :manager_name, :confirmation_token, :tenant_account_id)";
        $stmt = $conn->prepare($insert);
        $stmt->bindParam(":tenantname", $tenantname);
        $stmt->bindParam(":monthly", $monthly);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":phonenumber", $phonenumber);
        $stmt->bindParam(":location", $location);
        $stmt->bindParam(":manager_name", $manager_name);
        $stmt->bindParam(":confirmation_token", $confirmation_token);
        $stmt->bindParam(':tenant_account_id', $tenantAccountId); // Bind the FK

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