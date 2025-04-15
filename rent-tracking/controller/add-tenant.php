<?php
include '../connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $tenantname = $_POST['tenantname'];
    $monthly = $_POST['monthly'];
    $email = $_POST['email'];
    $phonenumber = $_POST['phonenumber'];
    $location = $_POST['location'];
    $manager_name = $_POST['manager_name'];
    $url = "../stall-slots";
    $alert = "<script>window.location.href = '".$url."';</script>";



    if (isset($_POST['stall_slots_id']) && !empty($_POST['stall_slots_id'])) {

        $stall_slots_id = $_POST['stall_slots_id'];

        
            $sqlUpdate = "UPDATE stall_slots SET tenantname = :tenantname, monthly = :monthly, email = :email, phonenumber = :phonenumber, location = :location, manager_name = :manager_name WHERE stall_slots_id = :stall_slots_id";
        
        $updateQuery = $conn->prepare($sqlUpdate);
        $updateQuery->bindParam(':tenantname', $tenantname);
        $updateQuery->bindParam(':monthly', $monthly);
        $updateQuery->bindParam(':email', $email);
        $updateQuery->bindParam(':phonenumber', $phonenumber);
        $updateQuery->bindParam(':location', $location);
        $updateQuery->bindParam(':manager_name', $manager_name);
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

    }else{

        $sqlSelect = "SELECT 1 FROM stall_slots WHERE tenantname = :tenantname LIMIT 1";
        $slctQuery = $conn->prepare($sqlSelect);
        $slctQuery->bindParam(':tenantname', $tenantname);
        $slctQuery->execute();

        if ($slctQuery->rowCount() > 0) {
            echo "<script>alert('Stall Name Already Exists!');</script>";
            echo $alert;
            exit();
        }

        $insert = "INSERT INTO stall_slots (tenantname, monthly, email, phonenumber, location, manager_name) VALUES (:tenantname, :monthly, :email, :phonenumber, :location, :manager_name)";
        $stmt = $conn->prepare($insert);
        $stmt->bindParam(":tenantname", $tenantname);
        $stmt->bindParam(":monthly", $monthly);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":phonenumber", $phonenumber);
        $stmt->bindParam(":location", $location);
        $stmt->bindParam(":manager_name", $manager_name);

        if ($stmt->execute()) {
            echo "<script>alert('Adding Tenant Successfully!');</script>";
            echo $alert;
            exit();
        } else {
            echo "<script>alert('Adding Tenant Failed!');</script>";
            echo $alert;
            exit();
        }
    }
}
?>
