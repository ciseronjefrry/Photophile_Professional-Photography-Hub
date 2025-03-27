<?php
session_start();
include 'db_connect.php';

// Ensure only admin can access
if (!isset($_SESSION['admin_logged_in'])) {
    echo "Unauthorized access!";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['folder_name'])) {
    $folder_name = $conn->real_escape_string($_POST['folder_name']);
    $folder_path = "uploads/" . $folder_name . "/";

    // Delete folder and its contents from filesystem
    if (is_dir($folder_path)) {
        array_map('unlink', glob("$folder_path/*.*")); // Delete all files inside
        rmdir($folder_path); // Delete the folder
    }

    // Delete folder from database
    $stmt = $conn->prepare("DELETE FROM admin_uploads WHERE folder_name = ?");
    $stmt->bind_param("s", $folder_name);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "Folder deleted successfully!";
        } else {
            echo "Folder not found in database.";
        }
    } else {
        echo "Error deleting folder from database: " . $conn->error;
    }

    $stmt->close();
} else {
    echo "Invalid request!";
}

$conn->close();
?>