<?php
session_start();
include 'database.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch messages only for the logged-in user
$sql = "SELECT * FROM contact_messages WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us 50.0 - Photography Services</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1e3a8a;
            --secondary: #3b82f6;
            --accent: #ef4444;
            --bg-light: #f3f4f6;
            --bg-dark: #1f2937;
            --text-light: #111827;
            --text-dark: #d1d5db;
            --card-bg-light: white;
            --card-bg-dark: #374151;
            --shadow: 0 6px 20px rgba(0,0,0,0.15);
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
            transition: background 0.3s, color 0.3s;
            overflow: hidden;
            height: 100vh;
            display: flex;
        }
        body.dark-mode {
            background: var(--bg-dark);
            color: var(--text-dark);
        }
        .container {
            display: flex;
            width: 100%;
            height: 100vh;
            overflow: hidden;
        }
        .sidebar {
            width: 280px;
            background: var(--primary);
            color: white;
            padding: 20px;
            transition: width 0.3s ease;
            position: fixed;
            top: 0;
            left: 0;
            height: 100%;
            z-index: 20;
            animation: slideInLeft 0.5s ease-out;
        }
        .sidebar.collapsed {
            width: 70px;
        }
        .sidebar h2 {
            font-size: 26px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        .sidebar.collapsed h2 span {
            display: none;
        }
        .sidebar ul {
            list-style: none;
        }
        .sidebar ul li {
            padding: 12px;
            display: flex;
            align-items: center;
            cursor: pointer;
            transition: background 0.3s, transform 0.2s;
        }
        .sidebar ul li:hover {
            background: var(--secondary);
            transform: translateX(5px);
        }
        .sidebar ul li i {
            margin-right: 10px;
        }
        .sidebar.collapsed ul li span {
            display: none;
        }
        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 20px;
            overflow-y: auto;
            scrollbar-width: none;
            background: var(--bg-light);
            transition: background 0.3s;
        }
        body.dark-mode .main-content {
            background: var(--bg-dark);
        }
        .header {
            background: var(--gradient);
            color: white;
            padding: 15px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 10;
            box-shadow: var(--shadow);
            animation: slideDown 0.5s ease-out;
        }
        .header input {
            padding: 8px;
            border: none;
            border-radius: 20px;
            width: 200px;
            outline: none;
        }
        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .card {
            background: var(--card-bg-light);
            padding: 20px;
            border-radius: 16px;
            box-shadow: var(--shadow);
            transition: transform 0.3s, opacity 0.3s;
            opacity: 0;
            animation: slideIn 0.5s forwards;
        }
        body.dark-mode .card {
            background: var(--card-bg-dark);
        }
        .card:nth-child(odd) {
            animation-delay: 0.1s;
        }
        .card:nth-child(even) {
            animation-delay: 0.2s;
        }
        .card:hover {
            transform: scale(1.02);
        }
        .card h2 {
            font-size: 22px;
            margin-bottom: 15px;
            color: var(--primary);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .card ul {
            list-style: none;
            padding: 0;
        }
        .card ul li {
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        body.dark-mode .card ul li {
            border-bottom: 1px solid #4b5563;
        }
        .card ul li:last-child {
            border-bottom: none;
        }
        .form-control {
            border: 2px solid var(--secondary);
            border-radius: 8px;
            padding: 10px;
            transition: border-color 0.3s;
        }
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 5px rgba(30, 58, 138, 0.5);
            outline: none;
        }
        .btn-custom {
            background: var(--secondary);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 10px;
            transition: background 0.3s, transform 0.3s;
        }
        .btn-custom:hover {
            background: var(--primary);
            transform: translateY(-2px);
        }
        .toggle-btn, .theme-toggle {
            cursor: pointer;
            font-size: 20px;
            padding: 5px;
        }
        .search-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideInLeft {
            from { transform: translateX(-100%); }
            to { transform: translateX(0); }
        }
        @keyframes slideDown {
            from { transform: translateY(-100%); }
            to { transform: translateY(0); }
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
            }
            .sidebar h2 span {
                display: none;
            }
            .main-content {
                margin-left: 70px;
            }
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            .header input {
                width: 150px;
            }
        }
        footer {
            background: var(--gradient);
            color: white;
            padding: 15px;
            text-align: center;
            position: fixed;
            bottom: 0;
            width: calc(100% - 280px);
            margin-left: 280px;
            z-index: 9;
            transition: width 0.3s, margin-left 0.3s;
        }
        .sidebar.collapsed ~ .main-content footer {
            width: calc(100% - 70px);
            margin-left: 70px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <h2><i class="fas fa-camera"></i> <span>Photography Services</span></h2>
            <ul>
                <li onclick="loadSection('hero')"><i class="fas fa-home"></i> <span>Home</span></li>
                <li onclick="loadSection('contact')"><i class="fas fa-envelope"></i> <span>Contact</span></li>
                <li onclick="loadSection('features')"><i class="fas fa-tools"></i> <span>Why Choose Us</span></li>
                <li><a href="responses.php"><i class="fas fa-reply"></i> <span>View Responses</span></a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1>Contact Us 50.0</h1>
                <div class="search-container">
                    <input type="text" id="search-bar" placeholder="Search..." onkeyup="searchDashboard()">
                    <span class="toggle-btn" onclick="toggleSidebar()">☰</span>
                    <span class="theme-toggle" onclick="toggleTheme()">🌙</span>
                </div>
            </div>
            <div class="dashboard-grid" id="dashboard-grid">
                <!-- Hero Section -->
                <div class="card" data-section="hero">
                    <h2>Get in Touch <i class="fas fa-phone-alt"></i></h2>
                    <p>We'd love to hear from you. Send us a message and we'll respond as soon as possible.</p><br>
                    <a href="#contact" class="btn-custom">Contact Us</a>
                </div>

                <!-- Contact Form -->
                <div class="card" data-section="contact">
                    <h2>Contact Form <i class="fas fa-envelope"></i></h2>
                    <form action="process_contact.php" method="POST">
                        <div class="mb-3">
                            <label for="name" class="form-label">Full Name    :</label>
                            <input type="text" class="form-control" id="name" name="name" required><br>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address :</label>
                            <input type="email" class="form-control" id="email" name="email" required><br>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number :</label>
                            <input type="text" class="form-control" id="phone" name="phone" required><br>
                        </div>
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject :</label>
                            <input type="text" class="form-control" id="subject" name="subject" required><br>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Your Message :</label>
                            <textarea class="form-control" id="message" name="message" rows="3" required></textarea>
                        </div>
                        <button type="submit" class="btn-custom">Submit</button>
                    </form>
                </div>

                <!-- Features Section -->
                <div class="card" data-section="features">
                    <h2>Why Choose Us? <i class="fas fa-star"></i></h2>
                    <ul>
                        <li><i class="fas fa-camera"></i> Professional Photography: High-quality services for all occasions.</li>
                        <li><i class="fas fa-palette"></i> Creative Editing: Beautifully edited, unique photos.</li>
                        <li><i class="fas fa-users"></i> Customer Satisfaction: Priority on happiness and service.</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>© 2025 Photography Services. All Rights Reserved.</p>
        <p>Follow us on
            <a href="#" class="text-white"><i class="fab fa-facebook"></i></a>
            <a href="#" class="text-white"><i class="fab fa-twitter"></i></a>
            <a href="#" class="text-white"><i class="fab fa-instagram"></i></a>
        </p>
    </footer>

    <script>
        // Sidebar Toggle
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('collapsed');
        }

        // Theme Toggle
        function toggleTheme() {
            document.body.classList.toggle('dark-mode');
            localStorage.setItem('theme', document.body.classList.contains('dark-mode') ? 'dark' : 'light');
        }

        // Load Section
        function loadSection(section) {
            const cards = document.querySelectorAll('.card');
            cards.forEach(card => {
                card.style.display = card.dataset.section === section ? 'block' : 'none';
            });
        }

        // Search Functionality
        function searchDashboard() {
            const query = document.getElementById('search-bar').value.toLowerCase();
            const cards = document.querySelectorAll('.card');
            cards.forEach(card => {
                const text = card.textContent.toLowerCase();
                card.style.display = text.includes(query) ? 'block' : 'none';
            });
        }

        // Initialization
        document.addEventListener('DOMContentLoaded', () => {
            console.log("Contact Us 50.0 loaded successfully!");
            if (localStorage.getItem('theme') === 'dark') {
                document.body.classList.add('dark-mode');
            }
        });
    </script>
</body>
</html>