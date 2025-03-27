<?php
session_start();

// Redirect if admin is not logged in
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: admin_login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard 50.0 - Photography Services</title>
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
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }
        .card {
            background: var(--card-bg-light);
            padding: 20px;
            border-radius: 16px;
            box-shadow: var(--shadow);
            transition: transform 0.3s, opacity 0.3s, background 0.3s;
            opacity: 0;
            animation: zoomIn 0.5s forwards;
            text-align: center;
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
            transform: scale(1.05);
            background: #e8f0fe; /* Light blue hover in light mode */
        }
        body.dark-mode .card:hover {
            background: #4b5563; /* Dark gray hover in dark mode */
        }
        .card h4 {
            font-size: 20px;
            margin-bottom: 10px;
            color: var(--primary);
        }
        .card p {
            font-size: 14px;
            color: var(--text-light);
            margin-bottom: 15px;
        }
        body.dark-mode .card p {
            color: var(--text-dark);
        }
        .btn-feature {
            display: block;
            background: var(--secondary);
            color: white;
            padding: 12px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            transition: background 0.3s, transform 0.3s;
        }
        .btn-feature:hover {
            background: var(--primary);
            transform: translateY(-2px);
            color: white;
        }
        .welcome-card {
            grid-column: 1 / -1;
            background: var(--gradient);
            color: white;
            padding: 30px;
        }
        .welcome-card h2 {
            font-size: 28px;
            margin: 0;
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
        @keyframes slideInLeft {
            from { transform: translateX(-100%); }
            to { transform: translateX(0); }
        }
        @keyframes slideDown {
            from { transform: translateY(-100%); }
            to { transform: translateY(0); }
        }
        @keyframes zoomIn {
            from { opacity: 0; transform: scale(0.9); }
            to { opacity: 1; transform: scale(1); }
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
            <h2><i class="fas fa-user-shield"></i> <span>Admin Dashboard</span></h2>
            <ul>
                <li onclick="loadSection('users')"><i class="fas fa-users"></i> <span>Manage Users</span></li>
                <li onclick="loadSection('messages')"><i class="fas fa-envelope"></i> <span>View Messages</span></li>
                <li onclick="loadSection('portfolio')"><i class="fas fa-images"></i> <span>Admin Portfolio</span></li>
                <li onclick="loadSection('bookings')"><i class="fas fa-calendar-check"></i> <span>Manage Bookings</span></li>
                <li onclick="loadSection('payments')"><i class="fas fa-wallet"></i> <span>Payments & Invoices</span></li>
                <li onclick="loadSection('reviews')"><i class="fas fa-star"></i> <span>Customer Reviews</span></li>
                <li onclick="loadSection('shoot')"><i class="fas fa-camera-retro"></i> <span>Custom Shoot Requests</span></li>
                <li onclick="loadSection('feedback')"><i class="fas fa-comment"></i> <span>Feedback & Reviews</span></li>
                <li onclick="loadSection('pricing')"><i class="fas fa-tags"></i> <span>Pricing & Packages</span></li>
                <li onclick="loadSection('availability')"><i class="fas fa-clock"></i> <span>Availability</span></li>
                <li onclick="loadSection('orders')"><i class="fas fa-box"></i> <span>Orders</span></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1>Admin Dashboard 50.0</h1>
                <div class="search-container">
                    <input type="text" id="search-bar" placeholder="Search Controls..." onkeyup="searchDashboard()">
                    <span class="toggle-btn" onclick="toggleSidebar()">☰</span>
                    <span class="theme-toggle" onclick="toggleTheme()">🌙</span>
                </div>
            </div>
            <div class="dashboard-grid" id="dashboard-grid">
                <!-- Welcome Card -->
                <div class="card welcome-card">
                    <h2>Welcome, Admin!</h2>
                    <p>Manage all Photography Services operations from this powerful dashboard.</p>
                </div>

                <!-- Manage Users -->
                <div class="card" data-section="users">
                    <h4>Manage Users</h4>
                    <p>View, edit, and remove users.</p>
                    <a href="manage_user.php" class="btn-feature">Go</a>
                </div>

                <!-- View Messages -->
                <div class="card" data-section="messages">
                    <h4>View Messages</h4>
                    <p>Check and reply to user messages.</p>
                    <a href="replies.php" class="btn-feature">Go</a>
                </div>

                <!-- Admin Portfolio -->
                <div class="card" data-section="portfolio">
                    <h4>Admin Portfolio</h4>
                    <p>Add, edit, or remove photography services.</p>
                    <a href="admin_portfolio.php" class="btn-feature">Go</a>
                </div>

                <!-- Manage Bookings -->
                <div class="card" data-section="bookings">
                    <h4>Manage Bookings</h4>
                    <p>View and update user bookings.</p>
                    <a href="admin_book.php" class="btn-feature">Go</a>
                </div>

                <!-- Payments & Invoices -->
                <div class="card" data-section="payments">
                    <h4>Payments & Invoices</h4>
                    <p>Track payments and generate invoices.</p>
                    <a href="admin_payments_invoices.php" class="btn-feature">Go</a>
                </div>

                <!-- Customer Reviews -->
                <div class="card" data-section="reviews">
                    <h4>All messages</h4>
                    <p>Monitor and manage user feedback.</p>
                    <a href="re.php" class="btn-feature">Go</a>
                </div>

                <!-- Custom Shoot Requests -->
                <div class="card" data-section="shoot">
                    <h4>Custom Shoot Requests</h4>
                    <p>Review and approve custom shoot requests.</p>
                    <a href="admin_shoot.php" class="btn-feature">Go</a>
                </div>

                <!-- Feedback & Reviews -->
                <div class="card" data-section="feedback">
                    <h4>Feedback & Reviews</h4>
                    <p>Manage customer feedback and reviews.</p>
                    <a href="admin_reviews.php" class="btn-feature">Go</a>
                </div>

                <!-- Pricing & Packages -->
                <div class="card" data-section="pricing">
                    <h4>Pricing & Packages</h4>
                    <p>Update pricing and promotional packages.</p>
                    <a href="admin_pricing.php" class="btn-feature">Go</a>
                </div>

                <!-- Availability -->
                <div class="card" data-section="availability">
                    <h4>Availability</h4>
                    <p>Manage photographer availability schedules.</p>
                    <a href="admin_available.php" class="btn-feature">Go</a>
                </div>

                <!-- Orders -->
                <div class="card" data-section="orders">
                    <h4>Orders</h4>
                    <p>Track and fulfill customer orders.</p>
                    <a href="order.php" class="btn-feature">Go</a>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>© 2025 Photography Services | Admin Panel</p>
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
                if (card.classList.contains('welcome-card')) return;
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
            console.log("Admin Dashboard 50.0 loaded successfully!");
            if (localStorage.getItem('theme') === 'dark') {
                document.body.classList.add('dark-mode');
            }
        });
    </script>
</body>
</html>