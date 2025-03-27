<?php
include 'database.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    $stmt = $conn->prepare("SELECT id, email, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    $stmt->bind_result($id, $email, $hashed_password);
    $stmt->fetch();

    if ($stmt->num_rows > 0 && password_verify($password, $hashed_password)) {
        $_SESSION['user_id'] = $id;
        $_SESSION['username'] = $username;
        $_SESSION['user_email'] = $email;
        header("Location: user_home.php");
        exit();
    } else {
        echo "<script>alert('Invalid Username or Password');</script>";
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Photography Services</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <style>
        :root {
            --primary: #1e3a8a;   /* Deep blue */
            --secondary: #3b82f6; /* Bright blue */
            --accent: #ef4444;    /* Red */
            --text-light: #fff;
            --shadow: 0 6px 20px rgba(0,0,0,0.15);
        }
        body {
            background: url('log.jpg') no-repeat center center/cover;
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            overflow: hidden;
            margin: 0;
            font-family: 'Poppins', sans-serif;
        }
        .navbar {
            width: 100%;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            padding: 15px 20px;
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
        .navbar-brand, .nav-link {
            color: var(--text-light);
            font-weight: bold;
            transition: color 0.3s;
        }
        .navbar-brand:hover, .nav-link:hover {
            color: #d1d5db; /* Light gray hover */
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
        }
        @keyframes zoomIn {
            from {
                opacity: 0;
                transform: scale(0.8);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }
        .login-container::before {
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
        .login-container h2 {
            color: var(--primary);
            margin-bottom: 20px;
            font-size: 2rem;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1);
            animation: fadeInText 1s ease-in;
        }
        @keyframes fadeInText {
            from { opacity: 0; transform: translateY(-10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .form-control {
            border: 2px solid var(--secondary);
            border-radius: 8px;
            padding: 12px;
            transition: border-color 0.3s, box-shadow 0.3s;
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
            border-radius: 8px;
            transition: background 0.3s, transform 0.3s;
            width: 100%;
        }
        .btn-custom:hover {
            background: var(--primary);
            transform: translateY(-2px);
        }
        .btn-outline-light {
            border-color: var(--text-light);
            color: var(--text-light);
            transition: background 0.3s, color 0.3s;
        }
        .btn-outline-light:hover {
            background: var(--secondary);
            color: var(--text-light);
            border-color: var(--secondary);
        }
        .text-danger {
            color: var(--accent) !important;
            font-weight: bold;
            transition: color 0.3s;
        }
        .text-danger:hover {
            color: #dc2626; /* Darker red hover */
        }
        .input-group {
            margin-bottom: 20px;
            position: relative;
        }
        .input-group i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--primary);
            font-size: 1.2rem;
        }
        .input-group input {
            padding-left: 40px;
        }
        .mt-3 {
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
            <a class="navbar-brand">Photography Services</a>
            <div>
                <a href="home.html" class="btn btn-outline-light me-2">Home</a>
                <button onclick="goBack()" class="btn btn-outline-light">Back</button>
            </div>
        </div>
    </nav>

    <div class="login-container mt-5">
        <h2 class="text-center">Login</h2>
        <form method="POST">
            <div class="input-group mb-3">
                <i class="fas fa-user"></i>
                <input type="text" name="username" class="form-control" placeholder="Enter Username" required>
            </div>
            <div class="input-group mb-3">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" class="form-control" placeholder="Enter Password" required>
            </div>
            <button type="submit" class="btn btn-custom">Login</button>
        </form>
        <p class="text-center mt-3">Don't have an account? <a href="register.php" class="text-danger">Register</a></p>
    </div>
</body>
</html>