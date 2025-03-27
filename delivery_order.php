<?php
session_start();
include 'db_connect.php';

// Ensure delivery person is logged in
if (!isset($_SESSION['delivery_logged_in']) || !isset($_SESSION['delivery_id'])) {
    header("Location: delivery_login.php");
    exit();
}

$delivery_id = $_SESSION['delivery_id'];

// Fetch orders assigned to this delivery person
$stmt = $conn->prepare("SELECT * FROM user_orders WHERE delivery_id = ? AND status IN ('Pending', 'Confirmed')");
$stmt->bind_param("i", $delivery_id);
$stmt->execute();
$orders = $stmt->get_result();

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Orders - Photography Services</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <style>
        :root {
            --primary: #1e3a8a;
            --secondary: #3b82f6;
            --accent: #ef4444;
            --bg-light: #f3f4f6;
            --text-light: #111827;
            --text-dark: #d1d5db;
            --card-bg-light: white;
            --shadow: 0 8px 25px rgba(0,0,0,0.15);
            --gradient: linear-gradient(135deg, var(--primary), var(--secondary));
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Roboto', sans-serif;
            background: var(--bg-light);
            color: var(--text-light);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .navbar {
            background: var(--gradient);
            padding: 1rem;
            box-shadow: var(--shadow);
            position: sticky;
            top: 0;
            z-index: 1000;
            animation: slideDown 0.5s ease-out;
        }
        .navbar .container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        .navbar-brand {
            font-size: 2rem;
            font-weight: 700;
            color: white;
            text-decoration: none;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
            transition: color 0.3s;
        }
        .navbar-brand:hover {
            color: #d1d5db;
        }
        .nav-links {
            display: flex;
            gap: 2rem;
            align-items: center;
        }
        .nav-links a {
            color: white;
            text-decoration: none;
            font-size: 1.1rem;
            font-weight: 600;
            transition: color 0.3s, transform 0.3s;
        }
        .nav-links a:hover {
            color: var(--accent);
            transform: translateY(-2px);
        }
        main {
            flex: 1;
        }
        .hero {
            background: var(--gradient);
            color: white;
            padding: 80px 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
            animation: fadeIn 1.5s ease-in-out;
        }
        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.2), transparent);
            animation: rotateGlow 15s infinite linear;
            z-index: 0;
        }
        .hero h1, .hero p {
            position: relative;
            z-index: 1;
        }
        .hero h1 {
            font-size: 3rem;
            font-weight: 700;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
            margin-bottom: 10px;
        }
        .hero p {
            font-size: 1.5rem;
            max-width: 900px;
            margin: 0 auto;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 30px 20px;
            animation: fadeInUp 1s ease-in-out;
        }
        .section-title {
            font-size: 2.5rem;
            color: var(--primary);
            text-align: center;
            margin-bottom: 40px;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1);
            animation: fadeInDown 1s ease-in-out;
        }
        .order-card {
            background: var(--card-bg-light);
            border: 2px solid var(--secondary);
            padding: 20px;
            border-radius: 15px;
            box-shadow: var(--shadow);
            margin-bottom: 20px;
            transition: transform 0.3s, box-shadow 0.3s;
            animation: zoomIn 0.8s ease-in-out;
        }
        .order-card:hover {
            transform: scale(1.03);
            box-shadow: 0 12px 35px rgba(0,0,0,0.2);
        }
        .order-card strong {
            font-size: 1.4rem;
            color: var(--primary);
            display: block;
            margin-bottom: 10px;
        }
        .order-card p {
            font-size: 1rem;
            color: var(--text-light);
            margin-bottom: 10px;
        }
        .btn-confirm, .btn-delivered, .btn-cancel {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: background 0.3s, transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
            margin-right: 10px;
        }
        .btn-confirm {
            background: var(--secondary);
            color: white;
        }
        .btn-confirm:hover {
            background: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(30, 58, 138, 0.4);
        }
        .btn-delivered {
            background: #4CAF50;
            color: white;
        }
        .btn-delivered:hover {
            background: #388E3C;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(56, 142, 60, 0.4);
        }
        .btn-cancel {
            background: var(--accent);
            color: white;
        }
        .btn-cancel:hover {
            background: #dc2626;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4);
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            z-index: 1000;
            animation: fadeIn 0.5s ease-in-out;
        }
        .modal.show {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            position: relative;
            padding: 25px;
            width: 90%;
            max-width: 480px;
            background: var(--card-bg-light);
            border-radius: 15px;
            box-shadow: var(--shadow);
            animation: slideInModal 0.5s ease-out;
        }
        .modal-content h5 {
            font-size: 1.8rem;
            color: var(--primary);
            margin-bottom: 15px;
            text-align: center;
        }
        .modal-content textarea {
            width: 100%;
            padding: 10px;
            border: 2px solid var(--secondary);
            border-radius: 8px;
            font-size: 0.95rem;
            min-height: 100px;
            resize: vertical;
            transition: border-color 0.3s, box-shadow 0.3s;
            margin-bottom: 15px;
        }
        .modal-content textarea:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 5px rgba(30, 58, 138, 0.5);
        }
        .modal-content button {
            background: var(--accent);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: background 0.3s, transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
            width: 100%;
        }
        .modal-content button:hover {
            background: #dc2626;
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4);
        }
        footer {
            background: var(--gradient);
            color: white;
            padding: 20px;
            text-align: center;
            box-shadow: 0 -4px 15px rgba(0, 0, 0, 0.1);
            animation: fadeInUp 1s ease-in-out;
        }
        footer .container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 10px;
        }
        footer p {
            font-size: 1rem;
            margin: 0;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.2);
        }
        footer .links {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }
        footer a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s, transform 0.3s;
        }
        footer a:hover {
            color: #d1d5db;
            transform: translateY(-2px);
        }
        footer .contact-info {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        footer .social-icons {
            margin-top: 10px;
        }
        footer .social-icons a {
            font-size: 1.5rem;
            margin: 0 10px;
            color: white;
            transition: color 0.3s, transform 0.3s;
        }
        footer .social-icons a:hover {
            color: var(--secondary);
            transform: scale(1.2);
        }
        @keyframes slideDown {
            from { transform: translateY(-100%); }
            to { transform: translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes zoomIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
        }
        @keyframes slideInModal {
            from { transform: translateY(-100px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        @keyframes rotateGlow {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        @media (max-width: 768px) {
            .navbar .container {
                flex-direction: column;
                gap: 1rem;
            }
            .nav-links {
                gap: 1rem;
                flex-wrap: wrap;
                justify-content: center;
            }
            .navbar-brand {
                font-size: 1.5rem;
            }
            .nav-links a {
                font-size: 1rem;
            }
            .hero h1 {
                font-size: 2.5rem;
            }
            .hero p {
                font-size: 1.2rem;
            }
            .section-title {
                font-size: 2rem;
            }
            .order-card {
                padding: 15px;
            }
            .modal-content {
                width: 95%;
                padding: 20px;
            }
            footer .links {
                gap: 15px;
            }
            footer p {
                font-size: 0.9rem;
            }
            footer .social-icons a {
                font-size: 1.3rem;
                margin: 0 8px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="container">
            <a href="/" class="navbar-brand">Photoshoot Booking</a>
            <div class="nav-links">
                <a href="home.html">Home</a>
                <a href="about.html">About</a>
                <a href="contact.php">Contact</a>
                <a href="logout.php">Logout</a>
            </div>
        </div>
    </nav>

    <main>
        <!-- Hero Section -->
        <section class="hero">
            <h1>Assinged Orders</h1>
            <p>Manage your assigned orders.</p>
        </section>

        <!-- Orders Section -->
        <div class="container">
            <h2 class="section-title">Assigned Orders</h2>
            <?php 
            if ($orders->num_rows > 0) {
                while ($order = $orders->fetch_assoc()) { 
            ?>
                <div class="order-card">
                    <strong>Order ID: <?php echo htmlspecialchars($order['id']); ?></strong>
                    <p>Folder: <?php echo htmlspecialchars($order['folder_name']); ?></p>
                    <p>Address: <?php echo htmlspecialchars($order['address']); ?></p>
                    <p>Status: <strong><?php echo htmlspecialchars($order['status']); ?></strong></p>
                    <?php if ($order['status'] === 'Pending') { ?>
                        <button class="btn-confirm update-status" data-status="Confirmed" data-id="<?php echo htmlspecialchars($order['id']); ?>">Confirm Order</button>
                    <?php } ?>
                    <button class="btn-delivered update-status" data-status="Delivered" data-id="<?php echo htmlspecialchars($order['id']); ?>">Task Confirmed</button>
                    <button class="btn-cancel cancel-order" data-id="<?php echo htmlspecialchars($order['id']); ?>">Cancel</button>
                </div>
            <?php 
                }
            } else {
                echo "<p class='text-center text-gray'>No assigned orders.</p>";
            }
            ?>
        </div>
    </main>

    <!-- Cancel Order Modal -->
    <div class="modal" id="cancelModal">
        <div class="modal-content">
            <h5>Cancel Order</h5>
            <form id="cancelForm">
                <input type="hidden" id="cancel_order_id" name="order_id">
                <label for="cancel_reason">Reason for Cancellation:</label>
                <textarea id="cancel_reason" name="reason" placeholder="Enter reason for cancellation" required></textarea>
                <button type="submit">Submit</button>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>© <?php echo date('Y'); ?> Photoshoot Booking System. All rights reserved.</p>
            <div class="links">
                <a href="/privacy.php">Privacy Policy</a>
                <a href="/terms.php">Terms of Service</a>
                <a href="/contact.php">Contact Us</a>
            </div>
            <p class="contact-info">Email: <a href="mailto:support@photoshootbooking.com">support@photoshootbooking.com</a> | Phone: (123) 456-7890</p>
            <div class="social-icons">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
    </footer>

    <script>
        $(document).ready(function() {
            $(".update-status").click(function() {
                let status = $(this).data("status");
                let orderId = $(this).data("id");

                $.post("update_order_status.php", { order_id: orderId, status: status }, function(response) {
                    alert(response);
                    location.reload();
                }).fail(function() {
                    alert("Error updating status. Please try again.");
                });
            });

            $(".cancel-order").click(function() {
                $("#cancel_order_id").val($(this).data("id"));
                $("#cancelModal").addClass("show");
            });

            $("#cancelForm").submit(function(e) {
                e.preventDefault();
                let orderId = $("#cancel_order_id").val();
                let reason = $("#cancel_reason").val();

                $.post("update_order_status.php", { order_id: orderId, status: "Cancelled", reason: reason }, function(response) {
                    alert(response);
                    location.reload();
                }).fail(function() {
                    alert("Error cancelling order. Please try again.");
                });
            });

            $("#cancelModal").click(function(e) {
                if (e.target === this) {
                    $(this).removeClass("show");
                }
            });
        });
    </script>
</body>
</html>