<?php
include 'connect.php';

if (isset($_GET['token'])) {
    $token = $_GET['token'];

    $sql = "SELECT * FROM stall_slots WHERE confirmation_token = :token LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':token', $token);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $stall_slots_id = $row['stall_slots_id'];

        // Update the confirmed column to 1
        $update = "UPDATE stall_slots SET confirmed = 1 WHERE stall_slots_id = :stall_slots_id";
        $updateStmt = $conn->prepare($update);
        $updateStmt->bindParam(':stall_slots_id', $stall_slots_id);

        if ($updateStmt->execute()) {
            echo "<script>alert('Email confirmed successfully!');</script>";
        } else {
            echo "<script>alert('Confirmation failed!');</script>";
        }
    } else {
        echo "<script>alert('Invalid token!');</script>";
    }
}
?>