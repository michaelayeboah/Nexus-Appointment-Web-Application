<?php
// backend/config.php

$host = 'localhost';
$dbname = 'nexus_appointments';
$username = 'root'; // Default XAMPP username
$password = '';     // Default XAMPP password is empty

try {
    // A PDO instance to connect to the database
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    
    // Set PDO to throw exceptions if there is a database error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Stop the script and show an error if connection fails
    die("Database connection failed: " . $e->getMessage());
}
?>