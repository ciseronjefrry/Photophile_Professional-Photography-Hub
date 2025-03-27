<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'photography_services');
if ($conn->connect_error) die('Connection Failed');

// Fetch available packages
$packages = $conn->query("SELECT * FROM pricing_packages");

// Handle package booking
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['book_package'])) {
    if (isset($_POST['name'], $_POST['email'], $_POST['phone'], $_POST['package_id'])) {
        $user_name = $conn->real_escape_string($_POST['name']);  
        $email = $conn->real_escape_string($_POST['email']);
        $phone = $conn->real_escape_string($_POST['phone']);
        $package_id = intval($_POST['package_id']);

        // Insert booking into database
        $stmt = $conn->prepare("INSERT INTO package_bookings (user_name, email, phone, package_id, booking_date, status) VALUES (?, ?, ?, ?, NOW(), 'Pending')");
        $stmt->bind_param("sssi", $user_name, $email, $phone, $package_id);
        
        if ($stmt->execute()) {
            echo '<script>alert("Booking Request Submitted Successfully!");</script>';
        } else {
            echo '<script>alert("Error submitting booking. Try again!");</script>';
        }
    } else {
        echo '<script>alert("Please fill in all required fields.");</script>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Photography Packages - Photography Services</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1e3a8a;   /* Deep blue */
            --secondary: #3b82f6; /* Bright blue */
            --accent: #ef4444;    /* Red */
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
        /* Navbar Styling */
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
        /* Main Content */
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
        .package {
            background: var(--card-bg-light);
            border: 2px solid var(--secondary);
            padding: 20px;
            margin: 15px 0;
            border-radius: 15px;
            box-shadow: var(--shadow);
            transition: transform 0.3s, box-shadow 0.3s;
            animation: zoomIn 0.8s ease-in-out;
        }
        .package:hover {
            transform: scale(1.03);
            box-shadow: 0 12px 35px rgba(0,0,0,0.2);
        }
        .package h3 {
            font-size: 1.8rem;
            color: var(--primary);
            margin-bottom: 12px;
        }
        .package p {
            font-size: 1rem;
            color: var(--text-light);
            margin-bottom: 10px;
        }
        .package strong {
            font-size: 1.2rem;
            color: var(--secondary);
            display: block;
            margin-bottom: 10px;
        }
        .package button {
            background: var(--secondary);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: background 0.3s, transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }
        .package button:hover {
            background: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(30, 58, 138, 0.4);
        }
        .booking-form {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: var(--card-bg-light);
            padding: 25px;
            border-radius: 15px;
            box-shadow: var(--shadow);
            width: 90%;
            max-width: 480px;
            z-index: 1001;
            animation: slideInModal 0.5s ease-out;
        }
        .booking-form.show {
            display: block;
        }
        .booking-form h3 {
            font-size: 1.8rem;
            color: var(--primary);
            text-align: center;
            margin-bottom: 20px;
        }
        .booking-form form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        .booking-form label {
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 5px;
        }
        .booking-form input {
            width: 100%;
            padding: 10px;
            border: 2px solid var(--secondary);
            border-radius: 8px;
            font-size: 0.95rem;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        .booking-form input:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 5px rgba(30, 58, 138, 0.5);
        }
        .form-buttons {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-top: 15px;
        }
        .form-buttons button {
            background: var(--secondary);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: background 0.3s, transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
            flex: 1;
        }
        .form-buttons button:hover {
            background: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(30, 58, 138, 0.4);
        }
        .form-buttons button.cancel {
            background: var(--accent);
        }
        .form-buttons button.cancel:hover {
            background: #dc2626;
        }
        /* Footer Styling */
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
            .package h3 {
                font-size: 1.6rem;
            }
            .package {
                padding: 15px;
            }
            .booking-form {
                width: 95%;
                padding: 20px;
            }
            .form-buttons {
                flex-direction: column;
                gap: 10px;
            }
            .form-buttons button {
                width: 100%;
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
                <a href="user_book.php">Book a Shoot</a>
                <a href="about.html">About</a>
                <a href="contact.php">Contact</a>
            </div>
        </div>
    </nav>

    <main>
        <!-- Hero Section -->
        <section class="hero">
            <h1>Photography Packages</h1>
            <p>Choose the perfect package for your photoshoot needs.</p>
        </section>

        <!-- Packages Section -->
        <div class="container">
            <h2 class="section-title">Available Packages</h2>
            <?php while ($row = $packages->fetch_assoc()): ?>
                <div class="package">
                    <h3><?php echo htmlspecialchars($row['package_name']); ?></h3>
                    <p><?php echo htmlspecialchars($row['description']); ?></p>
                    <strong>Price: ₹<?php echo number_format($row['price'], 2); ?></strong>
                    <p><?php echo nl2br(htmlspecialchars($row['included_services'])); ?></p>
                    <button onclick="openBookingForm(<?php echo $row['id']; ?>)">Book Now</button>
                </div>
            <?php endwhile; ?>
        </div>
    </main>

    <!-- Booking Form (Modal) -->
    <div id="bookingForm" class="booking-form">
        <h3>Book Photography Package</h3>
        <form method="POST">
            <input type="hidden" name="package_id" id="package_id">
            <input type="hidden" name="book_package" value="1">
            <label for="name">Your Name</label>
            <input type="text" name="name" id="name" placeholder="Enter your name" required>
            <label for="email">Your Email</label>
            <input type="email" name="email" id="email" placeholder="Enter your email" required>
            <label for="phone">Your Phone</label>
            <input type="text" name="phone" id="phone" placeholder="Enter your phone number" required>
            <div class="form-buttons">
                <button type="submit">Submit Booking</button>
                <button type="button" class="cancel" onclick="closeBookingForm()">Cancel</button>
            </div>
        </form>
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
        function openBookingForm(id) {
            document.getElementById('package_id').value = id;
            document.getElementById('bookingForm').classList.add('show');
        }
        function closeBookingForm() {
            document.getElementById('bookingForm').classList.remove('show');
        }
    </script>
</body>
</html>