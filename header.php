<?php
session_start(); // Start session if needed for login functionality
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Photoshoot Booking System</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1e3a8a;
            --secondary: #3b82f6;
            --accent: #ef4444;
            --bg-light: #f3f4f6;
            --text-light: #111827;
            --text-dark: #d1d5db;
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
        }
        .navbar {
            background: var(--gradient);
            padding: 1.5rem 1rem;
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
        .main-content {
    min-height: calc(100vh - 100px);
    padding: 20px;
}

        .container {
    max-width: 80%;
    margin: auto;
    padding: 5px 0;
}

        @keyframes slideDown {
            from { transform: translateY(-100%); }
            to { transform: translateY(0); }
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
        }
        img {
    display: block;
    margin: auto;
    max-width: 100%;
    height: auto;
}
.gallery-img {
    display: block;
    margin: 0 auto;
    max-width: 100%;
    height: auto;
    padding: 0;
}


body {
    margin: 0;
    padding: 0;
}

.container {
    max-width: 80%;
    margin: auto;
    padding: 20px 0;
}

.section {
    padding: 20px 0;
}

    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar">
        <div class="container">
            <a href="user_home.php" class="navbar-brand">Photoshoot Booking</a> <!-- Changed "/" to "user_home.php" -->
            <div class="nav-links">
                <a href="home.html">Home</a>
                <a href="about.html">About</a>
                <a href="contact.php">Contact</a>
            </div>
        </div>
    </nav>

    <!-- Main content -->
    <div class="main-content">
    <div class="container">
    <img src="images/photo.jpg" alt="Photographer" class="gallery-img">

    </div>
</div>

</body>
</html>
