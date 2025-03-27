<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    die("Error: User not logged in.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['folder_name']) || !isset($_POST['address'])) {
        die("Error: Missing required fields.");
    }

    $user_id = $_SESSION['user_id'];  // Ensure user_id is available
    $username = $_SESSION['username'];
    $folder_name = trim($_POST['folder_name']);
    $address = trim($_POST['address']);

    if (empty($folder_name) || empty($address)) {
        die("Error: Folder name and address cannot be empty.");
    }

    // Corrected SQL to insert user_id
    $stmt = $conn->prepare("INSERT INTO user_orders (user_id, user_name, folder_name, address, status) VALUES (?, ?, ?, ?, 'Pending')");
    if ($stmt === false) {
        die("Error: Database query failed. " . $conn->error);
    }

    $stmt->bind_param("isss", $user_id, $username, $folder_name, $address);

    if ($stmt->execute()) {
        echo "Order placed successfully!";
    } else {
        echo "Error placing order: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}

$conn->close();
?>
