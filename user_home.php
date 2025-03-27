<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Home 50.0 - Photography Services</title>
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
    font-family: 'Roboto', sans-serif; /* Modern font */
}

.sidebar.collapsed {
    width: 70px;
}

/* Sidebar Heading */
.sidebar h2 {
    font-size: 26px;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    font-weight: bold;
    white-space: nowrap; /* Prevents text from wrapping */
}

.sidebar.collapsed h2 span {
    display: none;
}

/* Sidebar Menu */
.sidebar ul {
    list-style: none;
    padding: 0;
}

.sidebar ul li {
    padding: 12px;
    display: flex;
    align-items: center;
    cursor: pointer;
    transition: background 0.3s, transform 0.2s;
    font-size: 16px;
    font-weight: 500; /* Medium weight for readability */
    text-decoration: none; /* Removes underline */
}

/* Icons inside Sidebar */
.sidebar ul li i {
    margin-right: 10px;
    font-size: 20px; /* Adjust icon size */
}

/* Hide text when collapsed */
.sidebar.collapsed ul li span {
    display: none;
}

/* Sidebar Hover Effect */
.sidebar ul li:hover {
    background: var(--secondary);
    transform: translateX(5px);
}

/* Ensures Links are Styled Properly */
.sidebar ul li a {
    color: white;
    text-decoration: none; /* No underline */
    width: 100%;
    display: flex;
    align-items: center;
}

.sidebar ul li a:hover {
    text-decoration: none;
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
            text-align: center;
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
        .btn-custom {
            display: block;
            background: var(--secondary);
            color: white;
            padding: 15px;
            margin: 10px auto;
            border-radius: 10px;
            text-decoration: none;
            font-weight: bold;
            transition: all 0.3s ease-in-out;
            max-width: 300px;
        }
        .btn-custom:hover {
            background: var(--primary);
            transform: scale(1.1);
            color: white;
        }
        .welcome-card {
            grid-column: 1 / -1;
            text-align: center;
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
            text-align: center;
            padding: 15px;
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
            <h2><i class="fas fa-camera"></i> <span>Menu</span></h2>
            <ul>
                <li onclick="loadSection('home')"><i class="fas fa-home"></i> <span>Home</span></li>
                <li onclick="loadSection('services')"><i class="fas fa-concierge-bell"></i> <span>Services</span></li>
                <li onclick="loadSection('gallery')"><i class="fas fa-images"></i> <span>Gallery</span></li>
                <li>><a href="contact.php"><i class="fas fa-envelope"></i> <span>Contact</span></a></li>
                <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a></li>
            </ul>
        </div>

        <!-- Main Content -->
        <div class="main-content">
            <div class="header">
                <h1>Photography Services 50.0</h1>
                <div class="search-container">
                    <input type="text" id="search-bar" placeholder="Search Services..." onkeyup="searchDashboard()">
                    <span class="toggle-btn" onclick="toggleSidebar()">☰</span>
                    <span class="theme-toggle" onclick="toggleTheme()">🌙</span>
                </div>
            </div>
            <div class="dashboard-grid" id="dashboard-grid">
                <!-- Welcome Card -->
                <div class="card welcome-card" data-section="home">
                    <h2>Welcome, <b><?php echo htmlspecialchars($username); ?></b>! <i class="fas fa-user"></i></h2>
                    <p>Capture Your Special Moments with Us</p>
                </div>

                <!-- Services Card -->
                <div class="card" data-section="services">
                    <h2>Our Services <i class="fas fa-camera-retro"></i></h2>
                    <a href="contact.php" class="btn-custom">Contact with the service</a>
                    <a href="user_book.php" class="btn-custom">Book a Photoshoot</a>
                    <a href="user_shoot.php" class="btn-custom">Custom Shoot Request</a>
                </div>

                <!-- Gallery Card -->
                <div class="card" data-section="gallery">
                    <h2>Gallery & Portfolio <i class="fas fa-photo-video"></i></h2>
                    <a href="user_portfolio.php" class="btn-custom">Photographer Portfolio & Gallery</a>
                </div>

                <!-- Booking & Payment Card -->
                <div class="card" data-section="services">
                    <h2>Booking & Payments <i class="fas fa-wallet"></i></h2>
                    <a href="pricing.php" class="btn-custom">Pricing & Packages</a>
                    <a href="user_payments_invoices.php" class="btn-custom">Online Payment & Invoice</a>
                    <a href="user_order.php" class="btn-custom">Photo Delivery & Download</a>
                </div>
                <!-- Reviews Card -->
                <div class="card" data-section="gallery">
                    <h2>Community <i class="fas fa-users"></i></h2>
                    <a href="user_reviews.php" class="btn-custom">Customer Reviews & Ratings</a>
                </div>

                <!-- Offers Card -->
                <div class="card" data-section="services">
                    <h2>Special Offers <i class="fas fa-gift"></i></h2>
                    <a href="#offers" class="btn-custom">Special Offers & Discounts</a>

                </div>

                <!-- Social Media Card -->
                <div class="card" data-section="gallery">
                    <h2>Connect <i class="fas fa-share-alt"></i></h2>
                    <a href="#social" class="btn-custom">Social Media Integration</a>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>© 2025 Photography Services | Capture your special moments with us | Contact: support@photographyservices.com</p>
    </footer>

    <script>
        // Simulated Dynamic Data (Replace with PHP backend fetch)
        const userData = {
            username: "<?php echo htmlspecialchars($username); ?>",
            services: ["Live Chat", "Book a Photoshoot", "Custom Shoot Request"],
            gallery: ["Portfolio & Gallery"],
            booking: ["Pricing", "Payment", "Delivery"],
            availability: ["Check Availability"],
            reviews: ["Reviews & Ratings"],
            offers: ["Offers", "Refer & Earn", "Gift a Shoot"],
            support: ["FAQs & Help"],
            social: ["Social Media"]
        };

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
            console.log("Photography Services 50.0 loaded successfully!");

            // Load saved theme
            if (localStorage.getItem('theme') === 'dark') {
                document.body.classList.add('dark-mode');
            }

            // Add click handlers for links
            document.querySelectorAll('.btn-custom').forEach(link => {
                link.addEventListener('click', e => {
                    if (link.getAttribute('href').startsWith('#')) {
                        e.preventDefault();
                        alert(`Feature coming soon: ${link.textContent}`);
                    }
                });
            });
        });
    </script>
</body>
</html>