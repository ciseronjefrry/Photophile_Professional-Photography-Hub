<?php
include 'db_connect.php';

session_start();

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all places
$places_sql = "SELECT * FROM places";
$places_result = $conn->query($places_sql);
if (!$places_result) {
    die("Error fetching places: " . $conn->error);
}

// Handle booking submission
if (isset($_POST['book'])) {
    $place_id = $_POST['place_id'];
    $user_email = filter_var($_POST['user_email'], FILTER_SANITIZE_EMAIL);

    if (filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
        $insert_sql = "INSERT INTO bookings (user_email, place_id) VALUES (?, ?)";
        $stmt = $conn->prepare($insert_sql);
        if ($stmt) {
            $stmt->bind_param("si", $user_email, $place_id);
            if ($stmt->execute()) {
                echo "<p class='success-message text-center mt-4'>Your booking is pending confirmation.</p>";
            } else {
                echo "<p class='error-message text-center mt-4'>Booking failed: " . $stmt->error . "</p>";
            }
            $stmt->close();
        } else {
            echo "<p class='error-message text-center mt-4'>Prepare failed: " . $conn->error . "</p>";
        }
    } else {
        echo "<p class='error-message text-center mt-4'>Invalid email address.</p>";
    }
}

// Handle checking booking status
if (isset($_POST['check_bookings'])) {
    $user_email = filter_var($_POST['user_email'], FILTER_SANITIZE_EMAIL);

    if (filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
        $bookings_sql = "SELECT b.booking_id, p.place_name, b.booking_date, b.confirmation_status 
                         FROM bookings b 
                         JOIN places p ON b.place_id = p.place_id 
                         WHERE b.user_email = ?";
        $stmt = $conn->prepare($bookings_sql);
        if ($stmt) {
            $stmt->bind_param("s", $user_email);
            $stmt->execute();
            $bookings_result = $stmt->get_result();

            echo "<section class='bookings-section'><div class='container'><h2 class='section-title text-center mb-6'>Your Bookings</h2>";
            if ($bookings_result->num_rows > 0) {
                echo "<div class='grid'>";
                while ($booking = $bookings_result->fetch_assoc()) {
                    echo "<div class='card'>";
                    echo "<h3 class='text-xl font-semibold mb-2 text-primary'>Booking ID: " . htmlspecialchars($booking['booking_id']) . "</h3>";
                    echo "<p class='text-gray mb-2'>Place: " . htmlspecialchars($booking['place_name']) . "</p>";
                    echo "<p class='text-gray mb-2'>Date: " . htmlspecialchars($booking['booking_date']) . "</p>";
                    echo "<p class='text-gray mb-2'>Status: <span class='font-bold " . ($booking['confirmation_status'] === 'Confirmed' ? 'text-secondary' : 'text-accent') . "'>" . htmlspecialchars($booking['confirmation_status']) . "</span></p>";
                    echo "</div>";
                }
                echo "</div>";
            } else {
                echo "<p class='text-center text-gray mt-4'>No bookings found for this email.</p>";
            }
            echo "</div></section>";
            $stmt->close();
        } else {
            echo "<p class='error-message text-center mt-4'>Query preparation failed: " . $conn->error . "</p>";
        }
    } else {
        echo "<p class='error-message text-center mt-4'>Invalid email address.</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Photoshoot Places - Photography Services</title>
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
        }
        .section-title {
            font-size: 2.5rem;
            color: var(--primary);
            text-align: center;
            margin-bottom: 40px;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1);
            animation: fadeInDown 1s ease-in-out;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            justify-items: center;
        }
        .card {
            background: var(--card-bg-light);
            border-radius: 15px;
            box-shadow: var(--shadow);
            padding: 20px;
            transition: transform 0.3s, box-shadow 0.3s;
            animation: zoomIn 0.8s ease-in-out;
            text-align: center;
            max-width: 340px;
            width: 100%;
        }
        .card:hover {
            transform: scale(1.05);
            box-shadow: 0 12px 35px rgba(0,0,0,0.2);
        }
        .card h2 {
            font-size: 1.6rem;
            color: var(--primary);
            margin-bottom: 12px;
        }
        .card h3 {
            font-size: 1.4rem;
            color: var(--primary);
            margin-bottom: 8px;
        }
        .card p {
            font-size: 0.95rem;
            color: var(--text-light);
            margin-bottom: 10px;
        }
        .card img {
            width: 100%;
            max-height: 180px;
            object-fit: cover;
            border-radius: 10px;
            margin-bottom: 15px;
            transition: transform 0.3s;
        }
        .card:hover img {
            transform: scale(1.1);
        }
        .text-gray {
            color: #6b7280;
        }
        .text-primary {
            color: var(--primary);
        }
        .text-secondary {
            color: var(--secondary);
        }
        .text-accent {
            color: var(--accent);
        }
        .btn-custom {
            background: var(--secondary);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: background 0.3s, transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
            display: inline-block;
        }
        .btn-custom:hover {
            background: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(30, 58, 138, 0.4);
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
        .modal-content h2 {
            font-size: 1.8rem;
            color: var(--primary);
            margin-bottom: 15px;
            text-align: center;
        }
        .modal-content label {
            font-weight: 600;
            color: var(--primary);
            display: block;
            margin-bottom: 6px;
        }
        .modal-content input {
            border: 2px solid var(--secondary);
            border-radius: 8px;
            padding: 10px;
            width: 100%;
            transition: border-color 0.3s, box-shadow 0.3s;
            font-size: 0.95rem;
        }
        .modal-content input:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 5px rgba(30, 58, 138, 0.5);
        }
        .modal-buttons {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-top: 15px;
        }
        .check-form {
            background: var(--card-bg-light);
            padding: 25px;
            border-radius: 15px;
            box-shadow: var(--shadow);
            max-width: 480px;
            margin: 30px auto;
            animation: fadeInUp 1s ease-in-out;
        }
        .check-form .mb-4 {
            margin-bottom: 15px;
        }
        .success-message, .error-message {
            font-size: 1rem;
            padding: 8px;
            border-radius: 5px;
            margin: 15px auto;
            max-width: 500px;
            text-align: center;
        }
        .success-message {
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid var(--secondary);
            color: var(--secondary);
            font-weight: 600;
        }
        .error-message {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid var(--accent);
            color: var(--accent);
            font-weight: 600;
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
        /* Ensure content fills page */
        main {
            flex: 1; /* Fills available space */
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
            .grid {
                grid-template-columns: 1fr;
            }
            .section-title {
                font-size: 2rem;
            }
            .hero h1 {
                font-size: 2.5rem;
            }
            .hero p {
                font-size: 1.2rem;
            }
            .card {
                max-width: 100%;
            }
            .modal-content {
                width: 95%;
                padding: 20px;
            }
            .hero {
                padding: 60px 15px;
            }
            .container {
                padding: 20px 15px;
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
                <a href="user_home.php">Home</a>
              
                <a href="#check">Check Bookings</a>
                <a href="about.html">About</a>
                <a href="/contact.php">Contact</a>
            </div>
        </div>
    </nav>

    <main>
        <!-- Hero Section -->
        <section class="hero">
            <h1>Explore Photoshoot Places</h1>
            <p>Discover stunning locations for your next photoshoot and book with ease.</p>
        </section>

        <!-- Places Section -->
        <section class="container">
            <h2 class="section-title">Available Photoshoot Places</h2>
            <div class="grid">
                <?php
                while ($place = $places_result->fetch_assoc()) {
                    $image_path = "images/" . htmlspecialchars($place['image']);
                    $image_src = file_exists($image_path) ? $image_path : "images/placeholder.jpg";
                    echo "<div class='card'>";
                    echo "<img src='" . $image_src . "' alt='" . htmlspecialchars($place['place_name']) . "'>";
                    echo "<h2>" . htmlspecialchars($place['place_name']) . "</h2>";
                    echo "<p class='text-gray'>" . htmlspecialchars($place['description']) . "</p>";
                    echo "<p class='text-gray'>Price: $" . htmlspecialchars($place['price']) . "</p>";
                    echo "<button class='btn-custom book-button' data-place_id='" . htmlspecialchars($place['place_id']) . "'>Book Now</button>";
                    echo "</div>";
                }
                ?>
            </div>
        </section>

        <!-- Check Bookings Section -->
        <section id="check">
        <section class="container">
            <h2 class="section-title">Check Your Bookings</h2>
            <form method="post" action="" class="check-form">
                <div class="mb-4">
                    <label for="user_email_check" class="block text-primary font-bold mb-2">Enter Your Email</label>
                    <input type="email" id="user_email_check" name="user_email" required class="w-full px-3 py-2 border rounded">
                </div>
                <button type="submit" name="check_bookings" class="btn-custom w-full">Check Bookings</button>
            </form>
        </section>
    </main>

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

    <!-- Booking Modal -->
    <div class="modal" id="book-modal">
        <div class="modal-content">
            <form method="post" action="">
                <input type="hidden" name="place_id" id="place_id">
                <h2>Confirm Booking</h2>
                <div class="mb-4">
                    <label for="user_email" class="block text-primary font-bold mb-2">Your Email</label>
                    <input type="email" id="user_email" name="user_email" required class="w-full px-3 py-2 border rounded">
                </div>
                <div class="modal-buttons">
                    <button type="submit" name="book" class="btn-custom">Confirm</button>
                    <button type="button" class="btn-custom bg-accent hover:bg-red-600 close-modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Modal Handling
        document.addEventListener('DOMContentLoaded', () => {
            const bookButtons = document.querySelectorAll('.book-button');
            const bookModal = document.getElementById('book-modal');
            const placeIdInput = document.getElementById('place_id');
            const closeModalButtons = document.querySelectorAll('.close-modal');

            bookButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const placeId = button.getAttribute('data-place_id');
                    placeIdInput.value = placeId;
                    bookModal.classList.add('show');
                });
            });

            closeModalButtons.forEach(button => {
                button.addEventListener('click', () => {
                    bookModal.classList.remove('show');
                });
            });

            bookModal.addEventListener('click', e => {
                if (e.target === bookModal) {
                    bookModal.classList.remove('show');
                }
            });
        });
    </script>
</body>
</html>