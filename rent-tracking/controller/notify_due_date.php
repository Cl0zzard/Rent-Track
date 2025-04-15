<?php
include '../connect.php';

use PHPMailer\PHPMailer\PHPMailer;

require_once "../phpmailer/PHPMailer.php";
require_once "../phpmailer/SMTP.php";
require_once "../phpmailer/Exception.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $today = date('Y-m-d');
    $fiveDaysAgo = date('Y-m-d', strtotime('5 days'));

    $sql = "SELECT th.*, ss.tenantname, ss.email
            FROM transaction_history th
            INNER JOIN stall_slots ss ON ss.stall_slots_id = th.stall_slots_id
            WHERE th.duedate <= :fiveDaysAgo
            GROUP BY th.stall_slots_id";

    $stmt = $conn->prepare($sql);
    $stmt->execute(['fiveDaysAgo' => $fiveDaysAgo]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $response = [];
    
    if (count($rows) === 0) {
        echo json_encode(['status' => 'no_overdue']);
    } else {
        foreach ($rows as $row) {
            $formatdate = date('F j, Y', strtotime($row['duedate']));
            $status = sendReminderEmail($row['email'], $row['tenantname'], $formatdate, $row['balance']) ? 'success' : 'failed';

            $response[] = [
                'status' => $status,
                'tenant' => $row['tenantname'],
                'email' => $row['email']
            ];
        }
        echo json_encode($response);
    }
}

function sendReminderEmail($email, $tenantName, $formatdate, $balance) {
    $mail = new PHPMailer(true);
    
    try {
        $mail->isSMTP();
        $mail->Host = "smtp.gmail.com";
        $mail->SMTPAuth = true;
        $mail->Username = "renttrackusa@gmail.com"; 
        $mail->Password = 'jkonlajueioaocgj'; 
        $mail->SMTPSecure = "tls";
        $mail->Port = 587;

        $mail->setFrom('renttrackusa@gmail.com', "Rent Track");
        $mail->addAddress($email);
        $mail->isHTML(true);
        $mail->Subject = "Payment Due Date Reminder";
        $mail->Body = "
            <p>Hello, <strong>$tenantName</strong>,</p>
            <p>This is a reminder that your payment was due on <strong>$formatdate</strong>.</p>
            <p>Your remaining balance is <strong>₱" . number_format($balance, 2) . "</strong>.</p>
            <p>Please ensure timely payment to avoid penalties.</p>
            <br>
            <p>Thank you.</p>
            <p><strong>Rent Track</strong></p>
        ";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email to $email failed: " . $e->getMessage());
        return false;
    }
}
?>
