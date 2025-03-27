<?php
session_start();
include 'db_connect.php';

// Ensure only admin can access
if (!isset($_SESSION['admin_logged_in'])) {
    echo "Unauthorized access!";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id']) && isset($_POST['delivery_id'])) {
    $order_id = intval($_POST['order_id']);
    $delivery_id = intval($_POST['delivery_id']);

    // Update the order status to "Confirmed" and assign delivery ID
    $stmt = $conn->prepare("UPDATE user_orders SET status = 'Confirmed', delivery_id = ? WHERE id = ? AND status = 'Pending'");
    $stmt->bind_param("ii", $delivery_id, $order_id);

    if ($stmt->execute()) {
        if ($stmt->affected_rows > 0) {
            echo "Order confirmed and assigned successfully!";
        } else {
            echo "No pending order found with this ID.";
        }
    } else {
        echo "Error assigning delivery: " . $conn->error;
    }

    $stmt->close();
} else {
    echo "Invalid request!";
}

$conn->close();
?>