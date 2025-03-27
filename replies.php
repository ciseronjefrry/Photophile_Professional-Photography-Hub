<?php
include 'database.php';

$sql = "SELECT * FROM contact_messages WHERE reply IS NULL";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage User Queries</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-dark: #1e3a8a;
            --primary-light: #3b82f6;
            --accent: #ef4444;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            color: #333;
            margin: 0;
            padding: 0;
        }

        /* Navbar */
        .navbar {
            background-color: var(--primary-dark);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand, .nav-link {
            color: white !important;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .nav-link:hover {
            color: var(--primary-light) !important;
        }

        /* Hero Section */
        .hero-section {
            background: linear-gradient(to right, var(--primary-dark), var(--primary-light));
            color: white;
            text-align: center;
            padding: 60px 20px;
            animation: fadeIn 1.5s ease-in-out;
        }

        h1 {
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1.5px;
        }

        /* Main Content */
        .container {
            max-width: 1200px;
            padding: 40px 20px;
        }

        h2 {
            color: var(--primary-dark);
            font-weight: 700;
            margin-bottom: 30px;
            text-transform: uppercase;
            letter-spacing: 1px;
            animation: fadeInDown 1s ease;
        }

        .query-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            animation: slideUp 0.5s ease;
        }

        .query-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .card-title {
            color: var(--primary-dark);
            font-weight: 600;
            font-size: 1.25rem;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 12px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 8px rgba(59, 130, 246, 0.5);
        }

        .btn-success {
            background-color: var(--primary-light);
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-success:hover {
            background-color: var(--primary-dark);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        /* Footer */
        footer {
            background-color: var(--primary-dark);
            color: white;
            padding: 20px 0;
            text-align: center;
            margin-top: 40px;
        }

        footer a {
            color: var(--primary-light);
            text-decoration: none;
        }

        footer a:hover {
            color: white;
        }

        /* Animations */
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(50px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Admin Panel</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="admin_dash.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_user.php">Users</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Queries</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <div class="hero-section">
        <h1>Admin Dashboard</h1>
        <p>Manage User Queries Efficiently</p>
    </div>

    <!-- Main Content -->
    <div class="container mt-5">
        <h2>User Queries</h2>
        <?php if ($result->num_rows === 0): ?>
            <div class="alert alert-info text-center">No pending queries at this time.</div>
        <?php else: ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="card query-card mt-3">
                    <div class="card-body">
                        <h5 class="card-title">
                            <i class="fas fa-user me-2"></i> <?= htmlspecialchars($row['name']) ?>
                        </h5>
                        <p><b>Email:</b> <a href="mailto:<?= htmlspecialchars($row['email']) ?>"><?= htmlspecialchars($row['email']) ?></a></p>
                        <p><b>Phone:</b> <?= htmlspecialchars($row['phone']) ?></p>
                        <p><b>Subject:</b> <?= htmlspecialchars($row['subject']) ?></p>
                        <p class="border p-3 rounded bg-light"><?= htmlspecialchars($row['message']) ?></p>
                        <form action="process_reply.php" method="POST">
                            <input type="hidden" name="id" value="<?= $row['id'] ?>">
                            <input type="hidden" name="email" value="<?= $row['email'] ?>">
                            <textarea name="reply" class="form-control mb-2" rows="4" placeholder="Write your reply here..." required></textarea>
                            <button type="submit" class="btn btn-success"><i class="fas fa-paper-plane me-2"></i>Send Reply</button>
                        </form>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>© <?= date('Y') ?> Photography Admin Panel. All Rights Reserved.</p>
            <p><a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a></p>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php $conn->close(); ?>