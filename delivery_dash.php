<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['delivery_logged_in']) || !isset($_SESSION['delivery_username'])) {
    header("Location: delivery_login.php");
    exit();
}
$username = $_SESSION['delivery_username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Photogarpher Dashboard - Photography Services</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1e3a8a;
            --secondary: #3b82f6;
            --bg-light: #f3f4f6;
            --bg-dark: #1f2937;
            --text-light: #111827;
            --text-dark: #d1d5db;
            --card-bg-light: white;
            --card-bg-dark: #374151;
            --shadow: 0 6px 20px rgba(0,0,0,0.15);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Roboto', sans-serif;
            background: var(--bg-light);
            color: var(--text-light);
            display: flex;
        }
        body.dark-mode {
            background: var(--bg-dark);
            color: var(--text-dark);
        }
        .sidebar {
    width: 280px;
    background: var(--primary);
    color: white;
    padding: 20px;
    position: fixed;
    height: 100vh;
    font-family: 'Roboto', sans-serif; /* Ensures a modern font */
}

.sidebar ul {
    list-style: none;
    padding: 0;
}

.sidebar ul li {
    padding: 12px;
    display: flex;
    align-items: center;
    cursor: pointer;
    transition: background 0.3s;
    font-size: 16px; /* Adjusts text size */
    font-weight: bold; /* Makes text clearer */
    text-decoration: none; /* Removes underline */
}

.sidebar ul li a {
    color: white; /* Ensures links are also white */
    text-decoration: none; /* Removes underline from links */
    width: 100%; /* Makes the link fill the entire li */
    display: block;
}

.sidebar ul li:hover {
    background: var(--secondary);
}

        .main-content {
            flex: 1;
            margin-left: 280px;
            padding: 20px;
        }
        .header {
            background: var(--secondary);
            color: white;
            padding: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
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
            border-radius: 10px;
            box-shadow: var(--shadow);
            text-align: center;
        }
        .card a {
            display: block;
            margin-top: 10px;
            text-decoration: none;
            font-weight: bold;
            color: var(--primary);
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
        body.dark-mode .card {
            background: var(--card-bg-dark);
        }
        .logout-btn {
            background: #ef4444;
            padding: 10px 15px;
            color: white;
            border: none;
            cursor: pointer;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Delivery Dashboard</h2>
        <ul>
            <li><i class="fas fa-truck"></i> <a href="delivery_order.php">Assigned Orders</a></li>
            <li><i class="fas fa-sign-out-alt"></i> <a href="logout.php" >Logout</a></li>
        </ul>
    </div>

    <div class="main-content">
        <div class="header">
            <h1>Welcome, <?php echo htmlspecialchars($username); ?>!</h1>
        </div>
        <div class="dashboard-grid">
            <div class="card">
                <h2>Assigned Orders</h2>
                <a href="delivery_order.php" class="btn-custom">View Orders</a>
            </div>
            </div>
        </div>
    </div>
</body>
</html>
