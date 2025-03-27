<?php
include 'db_connect.php';

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all places
$places_sql = "SELECT * FROM places";
$places_result = $conn->query($places_sql);
if (!$places_result) {
    die("Error fetching places: " . $conn->error);
}

// Handle booking confirmation
if (isset($_POST['confirm_booking'])) {
    $booking_id = $_POST['booking_id'];
    $update_sql = "UPDATE bookings SET confirmation_status = 'Confirmed' WHERE booking_id = ?";
    $stmt = $conn->prepare($update_sql);
    if ($stmt) {
        $stmt->bind_param("i", $booking_id);
        if ($stmt->execute()) {
            echo "<div class='alert alert-success text-center mt-4'>Booking confirmed successfully.</div>";
        } else {
            echo "<div class='alert alert-danger text-center mt-4'>Failed to confirm booking: " . $stmt->error . "</div>";
        }
        $stmt->close();
    } else {
        echo "<div class='alert alert-danger text-center mt-4'>Prepare failed: " . $conn->error . "</div>";
    }
}

// Fetch all bookings
$bookings_sql = "SELECT b.booking_id, b.user_email, p.place_name, b.booking_date, b.confirmation_status 
                 FROM bookings b 
                 JOIN places p ON b.place_id = p.place_id";
$bookings_result = $conn->query($bookings_sql);
if (!$bookings_result) {
    die("Error fetching bookings: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Photoshoot Places & Bookings</title>
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
            overflow-x: hidden;
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

        /* Main Content */
        .container {
            max-width: 1400px;
            padding: 40px 20px;
        }

        h1 {
            color: var(--primary-dark);
            font-weight: 700;
            text-align: center;
            margin-bottom: 40px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            animation: fadeInDown 1s ease;
        }

        .card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            animation: slideUp 0.5s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .card-title {
            color: var(--primary-dark);
            font-weight: 600;
            font-size: 1.25rem;
        }

        .card img {
            border-radius: 8px;
            transition: transform 0.3s ease;
        }

        .card:hover img {
            transform: scale(1.05);
        }

        .btn {
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-add, .btn-confirm {
            background-color: #10b981;
            border: none;
        }

        .btn-add:hover, .btn-confirm:hover {
            background-color: #059669;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-edit {
            background-color: var(--primary-light);
            border: none;
        }

        .btn-edit:hover {
            background-color: var(--primary-dark);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-delete {
            background-color: var(--accent);
            border: none;
        }

        .btn-delete:hover {
            background-color: #dc2626;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border: none;
            border-radius: 8px;
            animation: slideIn 0.5s ease;
        }

        .alert-danger {
            background-color: #fee2e2;
            color: var(--accent);
            border: none;
            border-radius: 8px;
            animation: slideIn 0.5s ease;
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
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(50px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">Photography Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="admin_dash.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Places</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#Manage Bookings">Bookings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <h1>Manage Photoshoot Places</h1>
        <a href="add_place.php" class="btn btn-add text-white mb-4"><i class="fas fa-plus me-2"></i>Add New Place</a>
        <div class="row">
            <?php while ($place = $places_result->fetch_assoc()): ?>
                <?php
                $image_path = "images/" . htmlspecialchars($place['image']);
                $image_src = file_exists($image_path) ? $image_path : "images/placeholder.jpg";
                ?>
                <div class="col-lg-4 col-md-6 mb-4">
                    <div class="card">
                        <img src="<?= $image_src ?>" alt="<?= htmlspecialchars($place['place_name']) ?>" class="w-full max-h-48 object-contain mb-2">
                        <div class="p-4">
                            <h2 class="card-title"><?= htmlspecialchars($place['place_name']) ?></h2>
                            <p class="text-gray-600 mb-2"><?= htmlspecialchars($place['description']) ?></p>
                            <p class="text-gray-600 mb-2"><b>Price:</b> ₹<?= number_format($place['price'], 2) ?></p>
                            <div class="d-flex gap-2">
                                <a href="edit_place.php?place_id=<?= htmlspecialchars($place['place_id']) ?>" class="btn btn-edit text-white"><i class="fas fa-edit me-2"></i>Edit</a>
                                <a href="delete_place.php?place_id=<?= htmlspecialchars($place['place_id']) ?>" class="btn btn-delete text-white" onclick="return confirm('Are you sure you want to delete this place?')"><i class="fas fa-trash me-2"></i>Delete</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        <section id="Manage Bookings">
        <h1 class="Manage Bookings">Manage Bookings</h1>
        <?php if ($bookings_result->num_rows > 0): ?>
            <div class="row">
                <?php while ($booking = $bookings_result->fetch_assoc()): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card">
                            <div class="p-4">
                                <h2 class="card-title">Booking ID: <?= htmlspecialchars($booking['booking_id']) ?></h2>
                                <p class="text-gray-600 mb-2"><b>User:</b> <?= htmlspecialchars($booking['user_email']) ?></p>
                                <p class="text-gray-600 mb-2"><b>Place:</b> <?= htmlspecialchars($booking['place_name']) ?></p>
                                <p class="text-gray-600 mb-2"><b>Date:</b> <?= date('F j, Y', strtotime($booking['booking_date'])) ?></p>
                                <p class="text-gray-600 mb-2"><b>Status:</b> 
                                    <span class="<?= $booking['confirmation_status'] === 'Confirmed' ? 'text-success' : 'text-warning' ?>">
                                        <?= htmlspecialchars($booking['confirmation_status']) ?>
                                    </span>
                                </p>
                                <?php if ($booking['confirmation_status'] !== 'Confirmed'): ?>
                                    <form method="post" action="">
                                        <input type="hidden" name="booking_id" value="<?= htmlspecialchars($booking['booking_id']) ?>">
                                        <button type="submit" name="confirm_booking" class="btn btn-confirm text-white"><i class="fas fa-check me-2"></i>Confirm</button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">No bookings found.</div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>© <?= date('Y') ?> Photography Admin Dashboard. All Rights Reserved.</p>
            <p><a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a></p>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script>
        // Staggered animation for cards
        document.addEventListener('DOMContentLoaded', () => {
            const cards = document.querySelectorAll('.card');
            cards.forEach((card, index) => {
                card.style.opacity = '0';
                card.style.transform = 'translateY(50px)';
                setTimeout(() => {
                    card.style.transition = 'all 0.5s ease';
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, index * 100); // Staggered effect
            });
        });
    </script>
</body>
</html>

<?php $conn->close(); ?>