<?php
include '../connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {


    if (isset($_GET['admin_id']) && !empty($_GET['admin_id'])) {

        $admin_id = $_GET['admin_id'];
        $type = $_GET['type'];

        if ($type == 2) {
            $url = "../management?account_status=2";
        } else {
            $url = "../management?account_status=1";
        }
        $alert = "<script>window.location.href = '" . $url . "';</script>";

        $sqlUpdate = "UPDATE admin_account SET status_archived = :type, date_archived = CURDATE()  WHERE admin_id = :admin_id";
        $updateQuery = $conn->prepare($sqlUpdate);
        $updateQuery->bindParam(':type', $type);
        $updateQuery->bindParam(':admin_id', $admin_id);

        if ($updateQuery->execute()) {
            if ($type == 2) {
                echo "<script>alert('Staff Archived Successfully!');</script>";
            } else {
                echo "<script>alert('Staff Unarchived Successfully!');</script>";
            }
            echo $alert;
            exit();
        } else {
            echo "<script>alert('Staff Archived Failed!');</script>";
            echo $alert;
            exit();
        }

    }
}
?>