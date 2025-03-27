<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Database connection details
$servername = "localhost";
$username = "root"; // Change if necessary
$password = ""; // Change if necessary
$database = "photography_services";

// Create connection
$conn = mysqli_connect($servername, $username, $password, $database);

// Check connection
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Set character encoding to avoid issues with special characters
mysqli_set_charset($conn, "utf8");

?>
