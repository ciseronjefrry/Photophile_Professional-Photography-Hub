<?php
$servername = "localhost";
$username = "root";  // Change if different
$password = "";      // Change if different
$database = "photography_services";  // Your database name

$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}
?>
