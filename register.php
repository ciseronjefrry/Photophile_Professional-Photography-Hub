<?php
include 'database.php'; // Assumes connection to 'localhost', 'root', '', 'photography_services'
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match!');</script>";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')");
        $stmt->bind_param("sss", $username, $email, $hashed_password);

        if ($stmt->execute()) {
            echo "<script>alert('Registration successful! Please log in.'); window.location='login.php';</script>";
        } else {
            echo "<script>alert('Error: Username or Email already exists!');</script>";
        }

        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Photography Services</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-dark: #1e3a8a;
            --primary-light: #3b82f6;
            --accent: #ef4444;
            --text-light: #fff;
            --shadow: 0 6px 20px rgba(0,0,0,0.15);
            --gradient: linear-gradient(135deg, var(--primary-dark), var(--primary-light));
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: url('reg.jpg') no-repeat center center/cover;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            overflow: hidden;
            margin: 0;
        }

        .navbar {
            width: 100%;
            background: var(--gradient);
            padding: 15px 20px;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            box-shadow: var(--shadow);
            animation: slideDown 0.8s ease-out;
        }

        @keyframes slideDown {
            from { transform: translateY(-100%); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        .navbar-brand, .nav-link {
            color: var(--text-light) !important;
            font-weight: 600;
            transition: color 0.3s ease, transform 0.3s ease;
        }

        .nav-link:hover, .navbar-brand:hover {
            color: #d1d5db !important;
            transform: scale(1.05);
        }

        .register-container {
            background: rgba(255, 255, 255, 0.95);
            padding: 40px;
            border-radius: 15px;
            box-shadow: var(--shadow);
            max-width: 450px;
            width: 100%;
            animation: zoomIn 0.8s ease-in-out;
            position: relative;
            overflow: hidden;
            z-index: 1;
        }

        @keyframes zoomIn {
            from { opacity: 0; transform: scale(0.8); }
            to { opacity: 1; transform: scale(1); }
        }

        .register-container::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.2), transparent);
            animation: rotateGlow 10s infinite linear;
            z-index: -1;
        }

        @keyframes rotateGlow {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .register-container h2 {
            color: var(--primary-dark);
            margin-bottom: 25px;
            font-size: 2rem;
            font-weight: 700;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1);
            animation: fadeInText 1s ease-in;
            text-align: center;
        }

        @keyframes fadeInText {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .form-control {
            border: 2px solid var(--primary-light);
            border-radius: 8px;
            padding: 12px 12px 12px 40px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease, transform 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-dark);
            box-shadow: 0 0 8px rgba(30, 58, 138, 0.5);
            transform: scale(1.02);
            outline: none;
        }

        .input-group {
            position: relative;
            margin-bottom: 20px;
            animation: slideUpInput 0.5s ease-in-out;
        }

        @keyframes slideUpInput {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .input-group i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary-dark);
            font-size: 1.2rem;
            z-index: 2;
        }

        .btn-custom {
            background: var(--primary-light);
            border: none;
            color: var(--text-light);
            padding: 12px;
            font-size: 1.1rem;
            font-weight: 600;
            border-radius: 8px;
            transition: background 0.3s ease, transform 0.3s ease, box-shadow 0.3s ease;
            width: 100%;
        }

        .btn-custom:hover {
            background: var(--primary-dark);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .text-accent {
            color: var(--accent) !important;
            font-weight: bold;
            transition: color 0.3s ease;
        }

        .text-accent:hover {
            color: #dc2626 !important;
        }

        .mt-3 {
            text-align: center;
            animation: fadeInText 1.2s ease-in;
        }
    </style>
    <script>
        function goBack() {
            window.history.back();
        }
    </script>
</head>
<body>
    <nav class="navbar navbar-expand-lg">
        <div class="container-fluid d-flex justify-content-between">
            <a class="navbar-brand" href="#">Photography Services</a>
            <div>
                <a href="home.html" class="btn btn-outline-light me-2">Home</a>
                <button onclick="goBack()" class="btn btn-outline-light">Back</button>
            </div>
        </div>
    </nav>

    <div class="register-container mt-5">
        <h2>Register</h2>
        <form method="POST">
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="username" class="form-control" placeholder="Enter Username" required>
            </div>
            <div class="input-group">
                <i class="fas fa-envelope"></i>
                <input type="email" name="email" class="form-control" placeholder="Enter Email" required>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" class="form-control" placeholder="Enter Password" required>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="confirm_password" class="form-control" placeholder="Confirm Password" required>
            </div>
            <button type="submit" class="btn btn-custom">Register</button>
        </form>
        <p class="mt-3">Already have an account? <a href="login.php" class="text-accent">Login</a></p>
    </div>
</body>
</html>