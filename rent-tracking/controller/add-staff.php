<?php
include '../connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $email = $_POST['email'];
    $phonenumber = $_POST['phonenumber'];
    $address = $_POST['address'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // ✅ Security best practice

    $url = "../management?account_status=1";
    $alert = "<script>window.location.href = '" . $url . "';</script>";

    // Prepare duplicate check
    if (isset($_POST['admin_id']) && !empty($_POST['admin_id'])) {
        $admin_id = $_POST['admin_id'];

        $duplicateCheck = $conn->prepare("SELECT * FROM admin_account WHERE (username = :username OR email = :email) AND admin_id != :admin_id");
        $duplicateCheck->bindParam(':username', $username);
        $duplicateCheck->bindParam(':email', $email);
        $duplicateCheck->bindParam(':admin_id', $admin_id);
    } else {
        $duplicateCheck = $conn->prepare("SELECT * FROM admin_account WHERE username = :username OR email = :email");
        $duplicateCheck->bindParam(':username', $username);
        $duplicateCheck->bindParam(':email', $email);
    }

    $duplicateCheck->execute();

    if ($duplicateCheck->rowCount() > 0) {
        echo "<script>alert('Username or Email already exists!');</script>";
        echo $alert;
        exit();
    }

    // If updating
    if (isset($_POST['admin_id']) && !empty($_POST['admin_id'])) {
        $sqlUpdate = "UPDATE admin_account SET name = :name, username = :username, email = :email, phonenumber = :phonenumber, address = :address";

        if (!empty($_POST['password'])) {
            $sqlUpdate .= ", password = :password";
        }

        $sqlUpdate .= " WHERE admin_id = :admin_id";

        $updateQuery = $conn->prepare($sqlUpdate);
        $updateQuery->bindParam(':name', $name);
        $updateQuery->bindParam(':username', $username);
        $updateQuery->bindParam(':email', $email);
        $updateQuery->bindParam(':phonenumber', $phonenumber);
        $updateQuery->bindParam(':address', $address);
        $updateQuery->bindParam(':admin_id', $admin_id);

        if (!empty($_POST['password'])) {
            $updateQuery->bindParam(':password', $password);
        }

        if ($updateQuery->execute()) {
            echo "<script>alert('Updated Staff Successfully!');</script>";
        } else {
            echo "<script>alert('Updating Staff Failed!');</script>";
        }
        echo $alert;
        exit();
    } else {
        // If inserting
        $insert = "INSERT INTO admin_account (name, username, email, phonenumber, address, password) VALUES (:name, :username, :email, :phonenumber, :address, :password)";
        $stmt = $conn->prepare($insert);
        $stmt->bindParam(":name", $name);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":phonenumber", $phonenumber);
        $stmt->bindParam(":address", $address);
        $stmt->bindParam(":password", $password);

        if ($stmt->execute()) {
            echo "<script>alert('Adding Staff Successfully!');</script>";
        } else {
            echo "<script>alert('Adding Staff Failed!');</script>";
        }
        echo $alert;
        exit();
    }
}
?>