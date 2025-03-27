<?php
session_start();
if (!isset($_session['user_id'])) {
    header("Location: login.php");
    exit();
}
include 'db.php';
// Rest of the code using $conn for queries
$conn = mysqli_connect("localhost", "root", "", "photography_services");
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}
?>