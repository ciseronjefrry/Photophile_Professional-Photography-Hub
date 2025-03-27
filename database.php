<?php
$servername = "localhost"; // Change if using a remote database
$username = "root"; // Change to your MySQL username
$password = ""; // Change if your MySQL has a password
$database = "photography_services"; // New project database name

// Create connection
$conn = new mysqli($servername, $username, $password, $database);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
