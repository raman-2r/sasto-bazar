<?php
// backend/config.php

$host = 'localhost';
$dbname = 'myfinalproject';
$username = 'root'; // default XAMPP username
$password = '';     // default XAMPP password

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    // Set the PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // Set default fetch mode to associative array
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // For production, log the error rather than echoing it
    die("Connection failed: " . $e->getMessage());
}
?>
