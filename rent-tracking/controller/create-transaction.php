<?php
include '../connect.php';

use PHPMailer\PHPMailer\PHPMailer;

require_once "../phpmailer/PHPMailer.php";
require_once "../phpmailer/SMTP.php";
require_once "../phpmailer/Exception.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $stall_slots_id = $_POST['stall_slots_id'];
    $url = "../transaction?stall_slots_id=" . $stall_slots_id;
    $redirectScript = "<script>window.location.href = '" . $url . "';</script>";

    session_start();  // Start the session
    $edited_by = $_SESSION['admin']['admin_id'];

    if (isset($_POST['transaction_type']) && $_POST['transaction_type'] == '1') {
        // CREATE or UPDATE TRANSACTION HISTORY
        $balance = floatval($_POST['balance']);
        $penalty = floatval($_POST['penalty']);
        $duedate = $_POST['duedate'];
        $transaction_history_id = $_POST['transaction_history_id'] ?? null;

        $status = 2; // default to Active/Unpaid
        $total_balance = $balance + $penalty;

        if (!empty($transaction_history_id)) {
            // UPDATE EXISTING TRANSACTION
            $stmt = $conn->prepare("SELECT * FROM transaction_history WHERE transaction_history_id = :transaction_history_id");
            $stmt->bindParam(':transaction_history_id', $transaction_history_id);
            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                $existing = $stmt->fetch(PDO::FETCH_ASSOC);
                $existingBalance = floatval($existing['balance']);
                $existingPenalty = floatval($existing['penalty']); // Get existing penalty
                $existingDueDate = $existing['duedate'];

                $today = date('Y-m-d');
                $updatedDueDate = $duedate; // from form

                // Adjust balance based on penalty difference
                $newBalance = $balance + $penalty - $existingPenalty;

                if ($newBalance <= 0) {
                    $status = 1; // Paid
                } elseif (strtotime($today) > strtotime($updatedDueDate)) {
                    $status = 3; // Overdue
                } else {
                    $status = 2; // Unpaid
                }

                // Update the transaction record
                $sql = "UPDATE transaction_history 
                        SET stall_slots_id = :stall_slots_id, 
                            balance = :balance,
                            penalty = :penalty,
                            status = :status,
                            duedate = :duedate, 
                            transaction_edited_by = :edited_by,    
                            transaction_date_edited = NOW()         
                        WHERE transaction_history_id = :transaction_history_id";

                $update = $conn->prepare($sql);
                $update->bindParam(':stall_slots_id', $stall_slots_id);
                $update->bindParam(':balance', $newBalance);  // Use the adjusted balance
                $update->bindParam(':penalty', $penalty);
                $update->bindParam(':status', $status);
                $update->bindParam(':duedate', $duedate);
                $update->bindParam(':edited_by', $edited_by);
                $update->bindParam(':transaction_history_id', $transaction_history_id);

                if ($update->execute()) {
                    echo "<script>alert('Transaction Updated Successfully!');</script>";
                    echo $redirectScript;
                    exit();
                } else {
                    echo "<script>alert('Updating Transaction Failed!');</script>";
                    echo $redirectScript;
                    exit();
                }
            }
        } else {
            // INSERT NEW TRANSACTION
            $status = 2; // Default to Unpaid

            // Check if the transaction is overdue
            if (strtotime(date("Y-m-d")) > strtotime($duedate)) {
                $status = 3; // Overdue
            } elseif ($total_balance <= 0) {
                $status = 1; // Paid
            }

            $sql = "INSERT INTO transaction_history (stall_slots_id, balance, penalty, status, duedate)
                    VALUES (:stall_slots_id, :balance, :penalty, :status, :duedate)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':stall_slots_id', $stall_slots_id);
            $stmt->bindParam(':balance', $total_balance);
            $stmt->bindParam(':penalty', $penalty);
            $stmt->bindParam(':status', $status);
            $stmt->bindParam(':duedate', $duedate);

            if ($stmt->execute()) {
                echo "<script>alert('Transaction Created Successfully!');</script>";
                echo $redirectScript;
                exit();
            } else {
                echo "<script>alert('Creating Transaction Failed!');</script>";
                echo $redirectScript;
                exit();
            }
        }

    } else {
        // PROCESS PAYMENT

        $transaction_history_id = $_POST['transaction_history_id'];
        $amount_paid = floatval($_POST['amount_paid']);
        $dateToday = date('F j, Y');

        $stmt = $conn->prepare("SELECT * FROM transaction_history th 
                                INNER JOIN stall_slots ss ON ss.stall_slots_id = th.stall_slots_id
                                WHERE th.transaction_history_id = :transaction_history_id");
        $stmt->bindParam(':transaction_history_id', $transaction_history_id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {

            if ($amount_paid > $row['balance']) {
                $updated_balance = 0;
                $updated_amount_paid = $row['balance'];
            } else {
                $updated_balance = round($row['balance'] - $amount_paid, 2);
                $updated_amount_paid = round($row['amount_paid'] + $amount_paid, 2);
            }

            $today = date('Y-m-d');
            $dueDate = $row['duedate'];

            if ($updated_balance <= 0) {
                $status = 1; // Paid
            } elseif (strtotime($today) > strtotime($dueDate)) {
                $status = 3; // Overdue
            } else {
                $status = 2; // Unpaid
            }

            $completed_date = $status === 1 ? date('Y-m-d') : null;

            $sqlUpdate = "UPDATE transaction_history 
                          SET balance = :balance,
                              amount_paid = :amount_paid,
                              status = :status,
                              completed_date = :completed_date,
                              transaction_edited_by = :edited_by,    
                              transaction_date_edited = NOW()           
                          WHERE transaction_history_id = :transaction_history_id";
            $updateQuery = $conn->prepare($sqlUpdate);
            $updateQuery->bindParam(':balance', $updated_balance);
            $updateQuery->bindParam(':amount_paid', $updated_amount_paid);
            $updateQuery->bindParam(':status', $status);
            $updateQuery->bindParam(':completed_date', $completed_date);
            $updateQuery->bindParam(':edited_by', $edited_by);
            $updateQuery->bindParam(':transaction_history_id', $transaction_history_id);

            if ($updateQuery->execute()) {
                $formatdate = date('F j, Y', strtotime($row['duedate']));
                $emailStatus = sendReminderEmail(
                    $row['email'],
                    $row['tenantname'],
                    $dateToday,
                    $formatdate,
                    $updated_balance,
                    $updated_amount_paid,
                    $row['penalty']
                ) ? 'success' : 'failed';
            } else {
                $emailStatus = 'not_sent';
            }

            echo json_encode([
                [
                    'status_sent' => $emailStatus,
                    'tenant' => $row['tenantname']
                ]
            ]);
        }
    }
}

// 📨 Email sending function
function sendReminderEmail($email, $tenantName, $dateToday, $formatdate, $balance, $updated_amount_paid, $penalty)
{
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
        $mail->Subject = "Payment Receipt - " . $dateToday;
        $mail->Body = "
            <p>Hello, <strong>$tenantName</strong>,</p>
            <p>This is a reminder regarding your payment details:</p>
            <table border='1' cellpadding='8' cellspacing='0' style='border-collapse: collapse; width: 100%;'>
                <tr>
                    <th style='background-color: #f2f2f2;'>Due Date</th>
                    <th style='background-color: #f2f2f2;'>Balance</th>
                    <th style='background-color: #f2f2f2;'>Penalty</th>
                    <th style='background-color: #f2f2f2;'>Total Paid</th>
                    <th style='background-color: #f2f2f2;'>Status</th>
                </tr>
                <tr>
                    <td style='text-align: center;'>$formatdate</td>
                    <td style='text-align: center;'>₱" . number_format($balance, 2) . "</td>
                    <td style='text-align: center;'>₱" . number_format($penalty, 2) . "</td>
                    <td style='text-align: center;'>₱" . number_format($updated_amount_paid, 2) . "</td>
                    <td style='text-align: center; font-weight: bold; color: " . ($balance <= 0 ? 'green' : 'red') . ";'>
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