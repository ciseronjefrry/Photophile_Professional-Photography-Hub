<?php
session_start();
include 'db_connect.php';

// Ensure only admin can access
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}

// Fetch orders
$orders = $conn->query("SELECT * FROM user_orders WHERE status='Pending'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Orders - Photography Services</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<div class="container mt-4">
    <h2 class="text-center">Pending Orders</h2>
    <?php while ($order = $orders->fetch_assoc()) { ?>
        <div class="order-card">
            <strong>Order ID: <?php echo $order['id']; ?></strong>
            <p>User: <?php echo $order['user_name']; ?></p>
            <p>Folder: <?php echo $order['folder_name']; ?></p>
            <p>Address: <?php echo $order['address']; ?></p>
        </div>
    <?php } ?>
</div>

</body>
</html>
