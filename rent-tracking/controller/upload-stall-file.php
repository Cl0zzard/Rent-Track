<?php
include '../connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
  

    if (!empty($_FILES['stall_file']['tmp_name'][0])) {

        $is_true = false;
        foreach ($_FILES['stall_file']['tmp_name'] as $index => $tmp_name) {
            $file_name = $_FILES['stall_file']['name'][$index];
            $file_type = mime_content_type($tmp_name);

            // Optional: Add unique prefix to avoid duplicate filenames
            $unique_file_name = uniqid() . '_' . basename($file_name);
            $file_destination = '../upload/' . $unique_file_name;

            // Move file to destination
            if (move_uploaded_file($tmp_name, $file_destination)) {
                $insert = "INSERT INTO stall_slots_file (stall_file, stall_slots_id) VALUES (:stall_file, :stall_slots_id)";
                $stmt = $conn->prepare($insert);
                $stmt->bindParam(":stall_file", $unique_file_name);
                $stmt->bindParam(":stall_slots_id", $_POST['stall_slots_id']);
                if ($stmt->execute()) {
                    $is_true = true;
                }
                 
            }
        }

        $url = "../stall-slots";
        $alert = "<script>window.location.href = '".$url."';</script>";

        if ($is_true === true) {
           echo "<script>alert('File Uploaded Successfully!');</script>";
           echo $alert;
           exit;
        }else{
           echo "<script>alert('File Uploaded Failed!');</script>";
           echo $alert;
           exit;
        }

    }

}
?>
