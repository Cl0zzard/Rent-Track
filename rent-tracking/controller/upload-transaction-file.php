<?php
include '../connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {


    if (!empty($_FILES['transaction_file']['tmp_name'][0])) {

        $is_true = false;
        // Get the stall name
        $stall_id = $_POST['stall_slots_id'];
        $query = "SELECT tenantname FROM stall_slots WHERE stall_slots_id = :stall_id LIMIT 1";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(":stall_id", $stall_id);
        $stmt->execute();
        $stall = $stmt->fetch(PDO::FETCH_ASSOC);

        $stall_name = $stall ? preg_replace('/[^a-zA-Z0-9_-]/', '_', $stall['tenantname']) : 'stall';
        foreach ($_FILES['transaction_file']['tmp_name'] as $index => $tmp_name) {
            $file_name = $_FILES['transaction_file']['name'][$index];
            $file_type = mime_content_type($tmp_name);

            // Optional: Add unique prefix to avoid duplicate filenames
            $unique_file_name = $stall_name . '_receipt_' . basename($file_name);
            $file_destination = '../upload/' . $unique_file_name;

            // Move file to destination
            if (move_uploaded_file($tmp_name, $file_destination)) {
                $insert = "INSERT INTO transaction_file (transactions_file, transaction_history_id) VALUES (:transactions_file, :transaction_history_id)";
                $stmt = $conn->prepare($insert);
                $stmt->bindParam(":transactions_file", $unique_file_name);
                $stmt->bindParam(":transaction_history_id", $_POST['transaction_history_id']);
                if ($stmt->execute()) {
                    $is_true = true;
                }

            }
        }

        $url = "../transaction?stall_slots_id=" . $_POST['stall_slots_id'];
        $alert = "<script>window.location.href = '" . $url . "';</script>";

        if ($is_true === true) {
            echo "<script>alert('File Uploaded Successfully!');</script>";
            echo $alert;
            exit;
        } else {
            echo "<script>alert('File Uploaded Failed!');</script>";
            echo $alert;
            exit;
        }

    }

}
?>