<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($username === "jefrry" && $password === "tomjerry2425") {
        $_SESSION['admin_logged_in'] = true;
        header("Location: admin_dash.php");
        exit();
    } else {
        $error = "Invalid Username or Password!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Photography Services</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #1e3a8a;   /* Deep blue */
            --secondary: #3b82f6; /* Bright blue */
            --accent: #ef4444;    /* Red */
            --bg-light: #f3f4f6;
            --text-light: #fff;
            --shadow: 0 8px 25px rgba(0,0,0,0.2);
            --gradient: linear-gradient(135deg, var(--primary), var(--secondary));
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            background: url('ad.jpg') no-repeat center center/cover;
            height: 100vh;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            font-family: 'Roboto', sans-serif;
            overflow: hidden;
        }
        .navbar {
            background: var(--gradient);
            padding: 15px 0;
            width: 100%;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            box-shadow: var(--shadow);
            animation: slideDown 0.5s ease-out;
        }
        @keyframes slideDown {
            from { transform: translateY(-100%); }
            to { transform: translateY(0); }
        }
        .navbar-brand {
            font-size: 1.8rem;
            font-weight: bold;
            color: var(--text-light);
            text-align: center;
            width: 100%;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.2);
        }
        .nav-link {
            color: var(--text-light) !important;
            font-weight: 500;
            transition: color 0.3s;
        }
        .nav-link:hover {
            color: #d1d5db !important; /* Light gray hover */
        }
        .main-container {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
            padding: 20px;
        }
        .login-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 15px;
            box-shadow: var(--shadow);
            max-width: 450px;
            width: 100%;
            animation: zoomIn 0.8s ease-in-out;
            position: relative;
            overflow: hidden;
            border: 1px solid rgba(59, 130, 246, 0.2);
        }
        @keyframes zoomIn {
            from { opacity: 0; transform: scale(0.85); }
            to { opacity: 1; transform: scale(1); }
        }
        .login-container::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.15), transparent);
            animation: rotateGlow 12s infinite linear;
            z-index: -1;
        }
        @keyframes rotateGlow {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        .login-container h2 {
            color: var(--primary);
            font-size: 2rem;
            margin-bottom: 25px;
            text-align: center;
            font-weight: 700;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1);
            animation: fadeInText 1s ease-in;
        }
        @keyframes fadeInText {
            from { opacity: 0; transform: translateY(-15px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .alert-danger {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid var(--accent);
            color: var(--accent);
            font-weight: 500;
            animation: shake 0.5s ease;
        }
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        .input-group {
            position: relative;
            margin-bottom: 20px;
        }
        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary);
            font-size: 1.2rem;
            z-index: 1;
        }
        .form-control {
            border: 2px solid var(--secondary);
            border-radius: 10px;
            padding: 12px 12px 12px 40px;
            transition: border-color 0.3s, box-shadow 0.3s;
            background: rgba(255, 255, 255, 0.8);
        }
        .form-control:focus {
            border-color: var(--primary);
            box-shadow: 0 0 8px rgba(30, 58, 138, 0.5);
            outline: none;
        }
        .btn-custom {
            background: var(--secondary);
            border: none;
            color: var(--text-light);
            padding: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 10px;
            transition: background 0.3s, transform 0.3s, box-shadow 0.3s;
            width: 100%;
        }
        .btn-custom:hover {
            background: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(30, 58, 138, 0.4);
        }
        .footer {
            background: var(--gradient);
            color: var(--text-light);
            text-align: center;
            padding: 15px;
            width: 100%;
            position: fixed;
            bottom: 0;
            z-index: 1000;
            font-size: 0.9rem;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
            animation: fadeInUp 0.8s ease-in;
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @media (max-width: 768px) {
            .login-container {
                max-width: 350px;
                padding: 30px;
            }
            .navbar-brand {
                font-size: 1.4rem;
            }
            .form-control {
                padding: 10px 10px 10px 35px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg">
        <div class="container">
            <a class="navbar-brand mx-auto">Admin Login - Photography Services</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="home.html">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="about.html">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="login.php">Services</a></li>
                    <li class="nav-item"><a class="nav-link" href="home.html#contact">Contact</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Login Form -->
    <div class="main-container">
        <div class="login-container">
            <h2>Admin Login</h2>
            <?php if (isset($error)) echo "<div class='alert alert-danger text-center'>$error</div>"; ?>
            <form method="POST">
                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input type="text" name="username" class="form-control" placeholder="Enter Username" required>
                </div>
                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input type="password" name="password" class="form-control" placeholder="Enter Password" required>
                </div>
                <button type="submit" class="btn btn-custom">Login</button>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>© 2025 Photography Services. All Rights Reserved.</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>