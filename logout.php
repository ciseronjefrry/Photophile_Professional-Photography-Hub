<?php
// Start and immediately destroy the session
session_start();
session_destroy();

// Prevent caching to ensure the user cannot navigate back to a logged-in state
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>Logout - Placement Management System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg,rgb(249, 248, 249), #2575fc); /* Original vibrant theme */
            color: #ffffff;
            overflow: hidden;
        }

        .container {
            text-align: center;
            padding: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
            animation: fadeIn 0.8s ease-in-out;
        }

        h1 {
            font-size: 2.5em;
            font-weight: 600;
            margin-bottom: 15px;
        }

        p {
            font-size: 1.1em;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        .logout-btn {
            padding: 12px 35px;
            font-size: 1.1em;
            font-weight: 500;
            color: #ffffff;
            background: #ff4b1f; /* Original button color */
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        .logout-btn:hover {
            background: #ff9068; /* Original hover color */
            transform: translateY(-2px);
        }

        .countdown {
            margin-top: 20px;
            font-size: 0.95em;
            opacity: 0.9;
        }

        /* Background animation */
        .background {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            overflow: hidden;
        }

        .circle {
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            animation: float 10s infinite ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes float {
            0% { transform: translateY(0); }
            50% { transform: translateY(-20vh) translateX(15vw); }
            100% { transform: translateY(0); }
        }
    </style>
</head>
<body>
<div class="background">
        <div class="circle" style="width: 150px; height: 150px; top: 10%; left: 20%;"></div>
        <div class="circle" style="width: 200px; height: 200px; bottom: 15%; right: 25%;"></div>
        <div class="circle" style="width: 100px; height: 100px; top: 50%; left: 50%;"></div>
    </div>

    <div class="container">
        <h1>Logged Out</h1>
        <p>You have successfully logged out of the Placement Management System. Thank you for using our services. We look forward to assisting you again.</p>
        <button class="logout-btn" onclick="redirectToLogin()">Return to Login</button>
        <div class="countdown">Redirecting in <span id="timer">5</span> seconds...</div>
    </div>

    <script>
        let countdown = 5;
        const timerElement = document.getElementById("timer");

        // Countdown timer
        const interval = setInterval(() => {
            countdown--;
            timerElement.textContent = countdown;
            if (countdown <= 0) {
                clearInterval(interval);
                redirectToLogin();
            }
        }, 1000);

        function redirectToLogin() {
            // Clear client-side storage
            sessionStorage.clear();
            localStorage.clear();

            // Redirect to login page
            window.location.href = "login.php";
        }

        // Prevent back navigation
        history.pushState(null, null, location.href);
        window.onpopstate = function () {
            history.pushState(null, null, location.href);
        };
    </script>
</body>
</html>