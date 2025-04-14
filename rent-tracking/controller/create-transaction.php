<?php
include '../connect.php';

use PHPMailer\PHPMailer\PHPMailer;

require_once "../phpmailer/PHPMailer.php";
require_once "../phpmailer/SMTP.php";
require_once "../phpmailer/Exception.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $stall_slots_id = $_POST['stall_slots_id'];
    $url = "../transaction?stall_slots_id=".$stall_slots_id;
    $alert = "<script>window.location.href = '".$url."';</script>";

    if (isset($_POST['transaction_type']) && $_POST['transaction_type'] == '1') {

        $balance = $_POST['balance'];
        $penalty = $_POST['penalty'];
        $status = 2;
        $duedate = $_POST['duedate'];

        if (isset($_POST['transaction_history_id']) && !empty($_POST['transaction_history_id'])) {

            $transaction_history_id = $_POST['transaction_history_id'];

            $sqlSelect = "SELECT * FROM transaction_history WHERE transaction_history_id = :transaction_history_id";
            $slctQuery = $conn->prepare($sqlSelect);
            $slctQuery->bindParam(':transaction_history_id', $transaction_history_id);
            $slctQuery->execute();

            if ($slctQuery->rowCount() > 0) {
                $selected = $slctQuery->fetch(PDO::FETCH_ASSOC); // ayusin mo FETCH_ASSOC

                // Example condition: check if status is "pending"
                if ($selected['balance'] <= 0) {
                    $status = 1;
                }else{
                    $status = 2;
                }

                if ($selected['balance'] <= 0 || $selected['balance'] == $balance) {
                    $total_balance = $balance;
                }else{
                    $total_balance = $balance + $penalty;
                }
            }


            $sqlUpdate = "UPDATE transaction_history 
                        SET stall_slots_id = :stall_slots_id, 
                            balance = :balance,
                            penalty = :penalty,
                            status = :status,
                            duedate = :duedate 
                        WHERE transaction_history_id = :transaction_history_id";

            $updateQuery = $conn->prepare($sqlUpdate);
            $updateQuery->bindParam(':stall_slots_id', $stall_slots_id);
            $updateQuery->bindParam(':balance', $total_balance);
            $updateQuery->bindParam(':penalty', $penalty);
            $updateQuery->bindParam(':status', $status);
            $updateQuery->bindParam(':duedate', $duedate);
            $updateQuery->bindParam(':transaction_history_id', $transaction_history_id);

            if ($updateQuery->execute()) {
                echo "<script>alert('Transaction Updated Successfully!');</script>";
                echo $alert;
                exit();
            } else {
                echo "<script>alert('Updating Transaction Failed!');</script>";
                echo $alert;
                exit();
            }

        }else{
            $total_balance = $balance + $penalty;

            $insert = "INSERT INTO transaction_history (stall_slots_id, balance, penalty, status, duedate) VALUES (:stall_slots_id, :balance, :penalty, :status, :duedate)";
            $stmt = $conn->prepare($insert);
            $stmt->bindParam(":stall_slots_id", $stall_slots_id);
            $stmt->bindParam(":balance", $total_balance);
            $stmt->bindParam(":penalty", $penalty);
            $stmt->bindParam(":status", $status);
            $stmt->bindParam(":duedate", $duedate);

            if ($stmt->execute()) {
                echo "<script>alert('Creating Transaction Successfully!');</script>";
                echo $alert;
                exit();
            } else {
                echo "<script>alert('Creating Transaction Failed!');</script>";
                echo $alert;
                exit();
            }
        }
    }else{
        
        $response = [];
        $dateToday = date('F j, Y');
        $transaction_history_id = $_POST['transaction_history_id'];
        $amount_paid = $_POST['amount_paid'];

        $sql = "SELECT *
                  FROM transaction_history th 
                  INNER JOIN stall_slots ss ON ss.stall_slots_id = th.stall_slots_id
                  WHERE th.transaction_history_id = :transaction_history_id";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":transaction_history_id", $transaction_history_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $updated_balance = round($row['balance'] - $amount_paid, 2);
            $updated_amount_paid = round($row['amount_paid'] + $amount_paid, 2);


            if ($updated_balance <= 0) {
                $status = 1;
                $currentDateTime = date('Y-m-d');
            }else{
                $status = 2;
                $currentDateTime = NULL;
            }
            $sqlUpdate = "UPDATE transaction_history 
                            SET balance = :balance,
                            amount_paid = :amount_paid,
                            status = :status,
                            completed_date = :completed_date
                            WHERE transaction_history_id = :transaction_history_id";

            $updateQuery = $conn->prepare($sqlUpdate);
            $updateQuery->bindParam(':balance', $updated_balance);
            $updateQuery->bindParam(':amount_paid', $updated_amount_paid);
            $updateQuery->bindParam(':status', $status);
            $updateQuery->bindParam(':completed_date', $currentDateTime);
            $updateQuery->bindParam(':transaction_history_id', $transaction_history_id);

            if ($updateQuery->execute()) {
                $formatdate = date('F j, Y', strtotime($row['duedate']));
                $status_sent = sendReminderEmail($row['email'], $row['tenantname'], $dateToday, $formatdate, $updated_balance, $updated_amount_paid, $row['penalty']) ? 'success' : 'failed';
            } else {
                $status_sent = 'not_sent';
            }

            $response[] = [
                    'status_sent' => $status_sent,
                    'tenant' => $row['tenantname']
                ];
        }
        echo json_encode($response);
    }
}

function sendReminderEmail($email, $tenantName, $dateToday, $formatdate, $balance, $updated_amount_paid, $penalty) {
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
                $mail->Subject = "Payment Receipt - ".$dateToday;
                $mail->Body = "
                        <p>Hello, <strong>$tenantName</strong>,</p>
                        <p>This is a reminder regarding your payment details:</p>

                        <table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse; width: 100%;'>
                            <tr>
                                <th style='background-color: #f2f2f2;'>Due Date</th>
                                <th style='background-color: #f2f2f2;'>Balance</th>
                                <th style='background-color: #f2f2f2;'>Penalty</th>
                                <th style='background-color: #f2f2f2;'>Total Amount</th>
                                <th style='background-color: #f2f2f2;'>Status</th>
                            </tr>
                            <tr>
                                <td style='text-align: center;'>" . $formatdate . "</td>
                                <td style='text-align: center;'>₱" . number_format(($balance ?? 0), 2) . "</td>
                                <td style='text-align: center;'>₱" . number_format(($penalty ?? 0), 2) . "</td>
                                <td style='text-align: center;'>₱" . number_format(($updated_amount_paid ?? 0), 2) . "</td>
                                <td style='text-align: center; font-weight: bold; color: " . ($balance == 0 ? 'green' : 'red') . ";'>
                                    " . ($balance <= 0 ? 'Complete' : 'Incomplete') . "
                                </td>
                            </tr>
                        </table>

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
