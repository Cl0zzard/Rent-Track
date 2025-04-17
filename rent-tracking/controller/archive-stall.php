<?php
include '../connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET') {


    if (isset($_GET['stall_slots_id']) && !empty($_GET['stall_slots_id'])) {

        $stall_slots_id = $_GET['stall_slots_id'];
        $type = $_GET['type'];

        if ($type == 2) {
            $url = "../stall-slots";
        }else{
            $url = "../archived-history";
        }
        $alert = "<script>window.location.href = '".$url."';</script>";

        $sqlUpdate = "UPDATE stall_slots SET status = :type WHERE stall_slots_id = :stall_slots_id";
        $updateQuery = $conn->prepare($sqlUpdate);
        $updateQuery->bindParam(':type', $type);
        $updateQuery->bindParam(':stall_slots_id', $stall_slots_id);

        if ($updateQuery->execute()) {
            if ($type == 2) {
                echo "<script>alert('Stall Archived Successfully!');</script>";
            }else{
                echo "<script>alert('Stall Unarchived Successfully!');</script>";
            }
            echo $alert;
            exit();
        } else {
            echo "<script>alert('Stall Archived Failed!');</script>";
            echo $alert;
            exit();
        }

    }
}
?>
