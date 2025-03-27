<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];
    $reason = isset($_POST['reason']) ? $_POST['reason'] : null;

    if ($status == "Cancelled") {
        $stmt = $conn->prepare("UPDATE user_orders SET status = ?, cancel_reason = ? WHERE id = ?");
        $stmt->bind_param("ssi", $status, $reason, $order_id);
    } else {
        $stmt = $conn->prepare("UPDATE user_orders SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $order_id);
    }
    $stmt->execute();

    echo "Order updated successfully!";
}
?>
