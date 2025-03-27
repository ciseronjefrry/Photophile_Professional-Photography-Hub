<?php
include 'database.php'; // Assuming this connects to 'localhost', 'root', '', 'photography_services'
session_start();

// Check if user is logged in and is an admin
$is_admin = isset($_SESSION['role']) && $_SESSION['role'] === 'admin';

// Handle delete action
if ($is_admin && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_message'])) {
    $message_id = intval($_POST['message_id']);
    $stmt = $conn->prepare("DELETE FROM contact_messages WHERE id = ?");
    $stmt->bind_param("i", $message_id);
    $stmt->execute();
    $_SESSION['success'] = "Message deleted successfully!";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

$sql = "SELECT * FROM contact_messages WHERE reply IS NOT NULL";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Queries & Replies - Photography Services</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-dark: #1e3a8a;   /* Deep blue */
            --primary-light: #3b82f6;  /* Bright blue */
            --accent: #ef4444;         /* Red */
            --text-light: #fff;
            --shadow: 0 6px 20px rgba(0,0,0,0.15);
            --gradient: linear-gradient(135deg, var(--primary-dark), var(--primary-light));
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: #333;
            margin: 0;
            padding: 0;
            overflow-x: hidden;
        }

        /* Navbar */
        .navbar {
            background: var(--gradient);
            box-shadow: var(--shadow);
            padding: 15px 0;
            position: fixed;
            width: 100%;
            top: 0;
            z-index: 1000;
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

        .nav-link:hover {
            color: #d1d5db !important;
            transform: scale(1.1);
        }

        /* Hero Section */
        .hero-section {
            background: var(--gradient);
            color: var(--text-light);
            text-align: center;
            padding: 100px 20px;
            position: relative;
            overflow: hidden;
            animation: fadeInHero 1.5s ease-in-out;
        }

        @keyframes fadeInHero {
            from { opacity: 0; transform: translateY(50px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .hero-section h1 {
            font-size: 3rem;
            font-weight: 700;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.3);
            animation: bounceIn 1.2s ease-in-out;
        }

        @keyframes bounceIn {
            0% { opacity: 0; transform: scale(0.3); }
            50% { opacity: 0.5; transform: scale(1.05); }
            100% { opacity: 1; transform: scale(1); }
        }

        .hero-section p {
            font-size: 1.2rem;
            animation: fadeInText 2s ease-in-out;
        }

        @keyframes fadeInText {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Replies Section */
        .reply-section {
            background: #fff;
            padding: 40px;
            border-radius: 15px;
            box-shadow: var(--shadow);
            margin-top: -50px;
            position: relative;
            z-index: 1;
            animation: slideUpSection 1s ease-in-out;
        }

        @keyframes slideUpSection {
            from { opacity: 0; transform: translateY(100px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .reply-section h2 {
            color: var(--primary-dark);
            font-weight: 700;
            text-align: center;
            margin-bottom: 30px;
            animation: fadeInText 1.5s ease-in-out;
        }

        .message-card {
            background: #f9fafb;
            border: none;
            border-radius: 10px;
            margin-bottom: 20px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            animation: cardPopIn 0.8s ease-in-out;
        }

        @keyframes cardPopIn {
            from { opacity: 0; transform: scale(0.95) translateY(20px); }
            to { opacity: 1; transform: scale(1) translateY(0); }
        }

        .message-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
        }

        .message-card h5 {
            color: var(--primary-dark);
            font-weight: 600;
            margin-bottom: 15px;
        }

        .message-card p {
            margin-bottom: 10px;
        }

        .message-card .text-success {
            color: #10b981 !important;
            font-weight: 500;
        }

        .btn-delete {
            background-color: var(--accent);
            border: none;
            color: var(--text-light);
            transition: background-color 0.3s ease, transform 0.3s ease;
        }

        .btn-delete:hover {
            background-color: #dc2626;
            transform: scale(1.05);
        }

        /* Footer */
        .footer {
            background: var(--gradient);
            color: var(--text-light);
            padding: 30px 0;
            text-align: center;
            margin-top: 60px;
            position: relative;
            overflow: hidden;
        }

        .footer::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1), transparent);
            animation: pulse 8s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(0.8); opacity: 0.5; }
            50% { transform: scale(1); opacity: 0.3; }
            100% { transform: scale(0.8); opacity: 0.5; }
        }

        .footer a {
            color: var(--text-light);
            margin: 0 10px;
            font-size: 1.5rem;
            transition: transform 0.3s ease, color 0.3s ease;
        }

        .footer a:hover {
            color: #d1d5db;
            transform: scale(1.2);
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Photography Services</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="home.html">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Log out</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section">
        <h1>User Queries & Replies</h1>
        <p>Explore responses from our dedicated admin team tailored to your inquiries.</p>
    </div>

    <!-- Replies Section -->
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-10">
                <div class="reply-section">
                    <h2>Admin Responses</h2>
                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success text-center"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
                    <?php endif; ?>
                    <?php if ($result->num_rows === 0): ?>
                        <div class="alert alert-info text-center">No replies available yet.</div>
                    <?php else: ?>
                        <?php $index = 0; while ($row = $result->fetch_assoc()): $index++; ?>
                            <div class="message-card" style="animation-delay: <?php echo $index * 0.2; ?>s;">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5><i class="fas fa-user me-2"></i><?php echo htmlspecialchars($row['name']); ?></h5>
                                    <?php if ($is_admin): ?>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="message_id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" name="delete_message" class="btn btn-delete btn-sm" onclick="return confirm('Are you sure you want to delete this message?')">
                                                <i class="fas fa-trash me-2"></i>Delete
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                                <p><strong>Message:</strong> <?php echo htmlspecialchars($row['message']); ?></p>
                                <hr>
                                <p><strong>Admin Reply:</strong> <span class="text-success"><?php echo htmlspecialchars($row['reply']); ?></span></p>
                            </div>
                        <?php endwhile; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <p>© <?php echo date('Y'); ?> Photography Services. All Rights Reserved.</p>
        <p>Follow us on
            <a href="#" class="text-white"><i class="fab fa-facebook"></i></a>
            <a href="#" class="text-white"><i class="fab fa-twitter"></i></a>
            <a href="#" class="text-white"><i class="fab fa-instagram"></i></a>
        </p>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const cards = document.querySelectorAll('.message-card');
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.animation = 'cardPopIn 0.8s ease-in-out forwards';
                    }
                });
            }, { threshold: 0.1 });

            cards.forEach(card => observer.observe(card));
        });
    </script>
</body>
</html>