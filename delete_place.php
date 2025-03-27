<?php
include 'db_connect.php';

if (!isset($_GET['place_id']) || empty($_GET['place_id'])) {
    die("Invalid Place ID");
}

$place_id = $_GET['place_id'];

// First, delete all related bookings
$delete_bookings_sql = "DELETE FROM bookings WHERE place_id = ?";
$stmt = $conn->prepare($delete_bookings_sql);
$stmt->bind_param("i", $place_id);
$stmt->execute();
$stmt->close();

// Now, delete the place
$delete_place_sql = "DELETE FROM places WHERE place_id = ?";
$stmt = $conn->prepare($delete_place_sql);
$stmt->bind_param("i", $place_id);
$stmt->execute();
$stmt->close();

// Redirect back to admin page
header("Location: admin_book.php");
exit;
?>
