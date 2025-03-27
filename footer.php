<?php
// No specific PHP logic needed here unless you add dynamic content
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
            --primary: #1e3a8a;   /* Deep blue */
            --secondary: #3b82f6; /* Bright blue */
            --accent: #ef4444;    /* Red */
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
        /* Placeholder for main content styling (assuming it exists above) */
        .main-content {
            min-height: calc(100vh - 200px); /* Adjust based on footer height */
            padding: 20px;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        /* Footer Styling */
        footer {
            background: var(--gradient);
            color: white;
            padding: 40px 20px;
            margin-top: 40px;
            text-align: center;
            box-shadow: 0 -4px 15px rgba(0, 0, 0, 0.1);
            animation: fadeInUp 1s ease-in-out;
        }
        footer .container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }
        footer p {
            font-size: 1rem;
            margin: 0;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.2);
        }
        footer .links {
            display: flex;
            justify-content: center;
            gap: 30px;
            flex-wrap: wrap;
        }
        footer a {
            color: var(--accent);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s, transform 0.3s;
        }
        footer a:hover {
            color: #d1d5db; /* Light gray hover */
            transform: translateY(-2px);
        }
        footer .contact-info {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        footer .social-icons {
            margin-top: 15px;
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
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @media (max-width: 768px) {
            footer .links {
                gap: 20px;
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
    <!-- Main content placeholder (assuming it exists above </div>) -->
    <div class="main-content">
        <!-- Your existing content goes here -->
        <div class="container">
            <p>This is a placeholder for the main content that precedes the footer.</p>
        </div>
    </div>
    <!-- Main content ends here -->

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
</body>
</html>