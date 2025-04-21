<?php
include '../connect.php';

header('Content-Type: application/json');

$email = $_GET['email'] ?? '';
$response = ['confirmed' => false];

if ($email) {
    $stmt = $conn->prepare("SELECT confirmed FROM stall_slots WHERE email = :email LIMIT 1");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($row && $row['confirmed'] == 1) {
        $response['confirmed'] = true;
    }
}

echo json_encode($response);
?>