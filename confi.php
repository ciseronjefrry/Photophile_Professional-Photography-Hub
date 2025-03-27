<?php
$host = 'localhost';
$dbname = 'photography_services';
$username = 'root';  // Default XAMPP/WAMP username
$password = '';      // Default XAMPP/WAMP empty password

try {
    $conn = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    exit;
}