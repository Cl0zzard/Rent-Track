<?php
session_start();
include '../connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    $id = $_POST['id'];
    $tablename = $_POST['tablename'];
    $id_type = $_POST['id_type'];

    $response = [];

    $deleteSQL = "DELETE FROM $tablename WHERE $id_type = :id";
    $deleteStmt = $conn->prepare($deleteSQL);
    $deleteStmt->bindParam(':id', $id, PDO::PARAM_INT);
    if ($deleteStmt->execute()) {
        $response['status'] = 'success';
    } else {
        $response['status'] = 'error';
    }
    echo json_encode($response);
}
?>
