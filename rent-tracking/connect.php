<?php

$host = "localhost";
$username = "root";
$password = "";
$database = "rent_tracking";

try {
    // Establish a connection to the database using PDO
    $conn = new PDO("mysql:host=$host;dbname=$database", $username, $password);
    
    // Set PDO to throw exceptions for error handling
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Optionally, set character encoding to UTF-8
    $conn->exec("SET NAMES utf8");
} catch(PDOException $e) {
    // Handle any potential errors during the connection process
    echo "Database connection failed. Error: " . $e->getMessage();
    exit;
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>
