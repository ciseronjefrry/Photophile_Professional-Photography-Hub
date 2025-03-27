<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $conn->real_escape_string($_POST['username']);
    $password = $_POST['password']; // In production, use password_hash and password_verify

    // Query the delivery_users table
    $stmt = $conn->prepare("SELECT id, username, password FROM delivery_users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        // For simplicity, using plain text password comparison; in production, use password_verify
        if ($password === $user['password']) { // Replace with password_verify($password, $user['password']) if hashed
            $_SESSION['delivery_logged_in'] = true;
            $_SESSION['delivery_id'] = $user['id'];
            $_SESSION['delivery_username'] = $user['username'];
            header("Location: delivery_dash.php");
            exit();
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "Invalid username!";
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Delivery Login - Photography Services</title>
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
            justify-content: center;
            align-items: center;
        }
        .container {
            max-width: 400px;
            padding: 30px;
            background: var(--card-bg-light);
            border-radius: 15px;
            box-shadow: var(--shadow);
            animation: fadeInUp 1s ease-in-out;
        }
        h2 {
            font-size: 2rem;
            color: var(--primary);
            text-align: center;
            margin-bottom: 20px;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1);
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        input {
            width: 100%;
            padding: 10px;
            border: 2px solid var(--secondary);
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        input:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 5px rgba(30, 58, 138, 0.5);
        }
        button {
            background: var(--secondary);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            transition: background 0.3s, transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }
        button:hover {
            background: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(30, 58, 138, 0.4);
        }
        .error {
            color: var(--accent);
            text-align: center;
            margin-bottom: 15px;
            font-weight: 600;
        }
        footer {
            background: var(--gradient);
            color: white;
            padding: 20px;
            text-align: center;
            box-shadow: 0 -4px 15px rgba(0, 0, 0, 0.1);
            animation: fadeInUp 1s ease-in-out;
            position: absolute;
            bottom: 0;
            width: 100%;
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
            color:rgb(79, 140, 232);
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
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @media (max-width: 768px) {
            .container {
                width: 95%;
                padding: 20px;
            }
            h2 {
                font-size: 1.8rem;
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
    <div class="container">
        <h2>Photographer Login</h2>
        <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
        <form method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Login</button>
        </form>
    </div>

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