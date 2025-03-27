<?php
session_start();
require_once 'confi.php';

// Add new time slot
if (isset($_POST['add_slot'])) {
    $date_time = $_POST['date_time'];
    $stmt = $conn->prepare("INSERT INTO availability (date_time) VALUES (?)");
    $stmt->execute([$date_time]);
    $_SESSION['success'] = "Time slot added successfully!";
    header("Location: " . $_SERVER['PHP_SELF']); // Redirect to avoid resubmission
    exit;
}

// Delete time slot
if (isset($_GET['delete'])) {
    $slot_id = $_GET['delete'];
    
    // Start transaction
    $conn->beginTransaction();
    
    try {
        // First delete the appointment record
        $stmt = $conn->prepare("DELETE FROM photography_appointments WHERE slot_id = ?");
        $stmt->execute([$slot_id]);
        
        // Then delete the availability record
        $stmt = $conn->prepare("DELETE FROM availability WHERE slot_id = ?");
        $stmt->execute([$slot_id]);
        
        // Commit transaction
        $conn->commit();
        $_SESSION['success'] = "Time slot deleted successfully!";
    } catch (Exception $e) {
        // Rollback on error
        $conn->rollBack();
        $_SESSION['error'] = "Could not delete the slot: " . $e->getMessage();
    }
    header("Location: " . $_SERVER['PHP_SELF']); // Redirect to avoid resubmission
    exit;
}

// Fetch all slots and appointments
$query = "SELECT a.*, p.client_name, p.email, p.phone, p.event_type 
          FROM availability a 
          LEFT JOIN photography_appointments p ON a.slot_id = p.slot_id 
          ORDER BY a.date_time";
$slots = $conn->query($query)->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Time Slots</title>
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

        .navbar {
            background-color: var(--primary-dark);
            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.2);
            padding: 15px 0;
        }

        .navbar-brand, .nav-link {
            color: white !important;
            font-weight: 600;
            transition: color 0.3s ease, transform 0.3s ease;
        }

        .nav-link:hover {
            color: var(--primary-light) !important;
            transform: scale(1.05);
        }

        .container {
            max-width: 1400px;
            padding: 40px 20px;
            animation: fadeIn 1s ease;
        }

        h2 {
            color: var(--primary-dark);
            font-weight: 700;
            text-align: center;
            margin-bottom: 40px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            animation: fadeInDown 1s ease;
        }

        .form-container, .table {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 20px;
        }

        .form-container {
            max-width: 600px;
            margin: 0 auto 40px;
            animation: slideUp 0.5s ease;
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
            transform: scale(1.02);
        }

        .btn {
            border-radius: 8px;
            padding: 10px 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-add {
            background-color: var(--primary-light);
            border: none;
        }

        .btn-add:hover {
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

        .table thead {
            background-color: var(--primary-dark);
            color: white;
        }

        .table tbody tr {
            transition: all 0.3s ease;
        }

        .table tbody tr:hover {
            background-color: #f1f5f9;
            transform: scale(1.01);
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
            transition: color 0.3s ease;
        }

        footer a:hover {
            color: white;
        }

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
                    <li class="nav-item"><a class="nav-link" href="admin_dash.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin_book.php">Bookings</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin_shoot.php">Shoot Requests</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <h2>Manage Time Slots</h2>

        <!-- Success/Error Messages -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success text-center">
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger text-center">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <!-- Add Time Slot Form -->
        <div class="form-container">
            <h3 class="text-center mb-4">Add New Time Slot</h3>
            <form method="POST">
                <div class="mb-3">
                    <label for="date_time" class="form-label">Select Date & Time</label>
                    <input type="datetime-local" name="date_time" id="date_time" class="form-control" required>
                </div>
                <button type="submit" name="add_slot" class="btn btn-add text-white w-100"><i class="fas fa-plus me-2"></i>Add Slot</button>
            </form>
        </div>

        <!-- Time Slots Table -->
        <?php if (empty($slots)): ?>
            <div class="alert alert-info text-center">No time slots available at this time.</div>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Date & Time</th>
                        <th>Status</th>
                        <th>Client Details</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($slots as $slot): ?>
                        <tr class="slot-row">
                            <td><?= date('F j, Y - H:i', strtotime($slot['date_time'])) ?></td>
                            <td class="<?= $slot['status'] === 'booked' ? 'text-success' : 'text-warning' ?>">
                                <?= htmlspecialchars($slot['status']) ?>
                            </td>
                            <td>
                                <?php if ($slot['client_name']): ?>
                                    <strong>Name:</strong> <?= htmlspecialchars($slot['client_name']) ?><br>
                                    <strong>Email:</strong> <a href="mailto:<?= htmlspecialchars($slot['email']) ?>"><?= htmlspecialchars($slot['email']) ?></a><br>
                                    <strong>Phone:</strong> <?= htmlspecialchars($slot['phone']) ?><br>
                                    <strong>Event:</strong> <?= htmlspecialchars($slot['event_type']) ?>
                                <?php else: ?>
                                    <span class="text-muted">No booking yet</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="?delete=<?= $slot['slot_id'] ?>" 
                                   class="btn btn-delete text-white btn-sm" 
                                   onclick="return confirm('Are you sure you want to delete this time slot? This will also delete any associated booking.')">
                                    <i class="fas fa-trash me-2"></i>Delete
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
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
        // Staggered animation for slot rows
        document.addEventListener('DOMContentLoaded', () => {
            const rows = document.querySelectorAll('.slot-row');
            rows.forEach((row, index) => {
                row.style.opacity = '0';
                row.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    row.style.transition = 'all 0.5s ease';
                    row.style.opacity = '1';
                    row.style.transform = 'translateY(0)';
                }, index * 100); // Staggered effect
            });
        });
    </script>
</body>
</html>