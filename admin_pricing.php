<?php
// Database connection
$conn = new mysqli('localhost', 'root', '', 'photography_services');
if ($conn->connect_error) die('Connection Failed: ' . $conn->connect_error);

// Handle Add, Edit, Delete, Confirm, and Delete Booking actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add') {
        $package_name = $conn->real_escape_string($_POST['package_name']);
        $description = $conn->real_escape_string($_POST['description']);
        $price = floatval($_POST['price']);
        $included_services = $conn->real_escape_string($_POST['included_services']);
        
        $stmt = $conn->prepare("INSERT INTO pricing_packages (package_name, description, price, included_services) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("ssds", $package_name, $description, $price, $included_services);
        $stmt->execute();
        $stmt->close();
    } 
    
    elseif ($_POST['action'] === 'edit' && isset($_POST['package_id'])) {
        $package_id = intval($_POST['package_id']);
        $package_name = $conn->real_escape_string($_POST['package_name']);
        $description = $conn->real_escape_string($_POST['description']);
        $price = floatval($_POST['price']);
        $included_services = $conn->real_escape_string($_POST['included_services']);
        
        $stmt = $conn->prepare("UPDATE pricing_packages SET package_name=?, description=?, price=?, included_services=? WHERE id=?");
        $stmt->bind_param("ssdsi", $package_name, $description, $price, $included_services, $package_id);
        $stmt->execute();
        $stmt->close();
    }
    
    elseif ($_POST['action'] === 'delete' && isset($_POST['package_id'])) {
        $package_id = intval($_POST['package_id']);
        $stmt = $conn->prepare("DELETE FROM pricing_packages WHERE id=?");
        $stmt->bind_param("i", $package_id);
        $stmt->execute();
        $stmt->close();
    }
    
    elseif ($_POST['action'] === 'confirm_booking' && isset($_POST['booking_id'])) {
        $booking_id = intval($_POST['booking_id']);
        $stmt = $conn->prepare("UPDATE package_bookings SET status='Confirmed' WHERE id=?");
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();
        $stmt->close();
    }
    
    elseif ($_POST['action'] === 'delete_booking' && isset($_POST['booking_id'])) {
        $booking_id = intval($_POST['booking_id']);
        $stmt = $conn->prepare("DELETE FROM package_bookings WHERE id=?");
        $stmt->bind_param("i", $booking_id);
        $stmt->execute();
        $stmt->close();
    }
}

// Fetch packages and bookings
$packages = $conn->query("SELECT * FROM pricing_packages");
$bookings = $conn->query("
    SELECT b.id, b.user_name, b.email, b.phone, p.package_name, b.status
    FROM package_bookings b
    JOIN pricing_packages p ON b.package_id = p.id
");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Pricing & Packages Management</title>
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

        /* Main Content */
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

        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 12px;
            transition: all 0.3s ease;
        }

        .form-control:focus, .form-select:focus {
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

        .status-pending { color: #f59e0b; }
        .status-confirmed { color: #10b981; }

        /* Modal */
        .modal-content {
            border-radius: 12px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
            animation: slideUp 0.5s ease;
        }

        .modal-header {
            background-color: var(--primary-dark);
            color: white;
            border-bottom: none;
            border-radius: 12px 12px 0 0;
        }

        .btn-close {
            color: white;
            opacity: 0.8;
        }

        .btn-close:hover {
            opacity: 1;
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
            transition: color 0.3s ease;
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
            <a class="navbar-brand" href="#">Photography Admin</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="admin_dash.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="#exist">Existing Packages</a></li>
                    <li class="nav-item"><a class="nav-link" href="#books">Booking Requests</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin_reviews.php">Reviews</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <h2>Manage Photography Packages</h2>

        <!-- Add Package Form -->
        <div class="form-container">
            <h3 class="text-center mb-4">Add New Package</h3>
            <form method="POST">
                <input type="hidden" name="action" value="add">
                <div class="mb-3">
                    <input type="text" name="package_name" placeholder="Package Name" required class="form-control">
                </div>
                <div class="mb-3">
                    <textarea name="description" placeholder="Description" required class="form-control" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <input type="number" name="price" placeholder="Price (₹)" required step="0.01" class="form-control">
                </div>
                <div class="mb-3">
                    <textarea name="included_services" placeholder="Included Services (e.g., 2-hour shoot, 10 edited photos)" required class="form-control" rows="3"></textarea>
                </div>
                <button type="submit" class="btn btn-add text-white w-100"><i class="fas fa-plus me-2"></i>Add Package</button>
            </form>
        </div>

        <!-- Existing Packages -->
        <section id="exist">
        <h2>Existing Packages</h2>
        <?php if ($packages->num_rows === 0): ?>
            <div class="alert alert-info text-center">No packages available at this time.</div>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Package Name</th>
                        <th>Description</th>
                        <th>Price</th>
                        <th>Included Services</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $packages->fetch_assoc()): ?>
                        <tr class="package-row">
                            <td><?= htmlspecialchars($row['package_name']) ?></td>
                            <td><?= htmlspecialchars($row['description']) ?></td>
                            <td>₹<?= number_format($row['price'], 2) ?></td>
                            <td><?= nl2br(htmlspecialchars($row['included_services'])) ?></td>
                            <td>
                                <button class="btn btn-edit text-white btn-sm me-2" 
                                        onclick="openEditModal(<?= $row['id'] ?>, '<?= htmlspecialchars($row['package_name']) ?>', '<?= htmlspecialchars($row['description']) ?>', '<?= $row['price'] ?>', '<?= htmlspecialchars($row['included_services']) ?>')">
                                    <i class="fas fa-edit me-2"></i>Edit
                                </button>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="delete">
                                    <input type="hidden" name="package_id" value="<?= $row['id'] ?>">
                                    <button type="submit" class="btn btn-delete text-white btn-sm" 
                                            onclick="return confirm('Are you sure you want to delete this package?')">
                                        <i class="fas fa-trash me-2"></i>Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <!-- Booking Requests -->
        <section id="books">
        <h2 class="mt-5">Booking Requests</h2>
        <?php if ($bookings->num_rows === 0): ?>
            <div class="alert alert-info text-center">No booking requests at this time.</div>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Package</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $bookings->fetch_assoc()): ?>
                        <tr class="booking-row">
                            <td><?= htmlspecialchars($row['user_name']) ?></td>
                            <td><?= htmlspecialchars($row['email']) ?></td>
                            <td><?= htmlspecialchars($row['phone']) ?></td>
                            <td><?= htmlspecialchars($row['package_name']) ?></td>
                            <td class="status-<?= strtolower($row['status']) ?>">
                                <?= htmlspecialchars($row['status']) ?>
                            </td>
                            <td>
                                <div class="d-flex gap-2">
                                    <?php if ($row['status'] === 'Pending'): ?>
                                        <form method="POST" style="display:inline;">
                                            <input type="hidden" name="action" value="confirm_booking">
                                            <input type="hidden" name="booking_id" value="<?= $row['id'] ?>">
                                            <button type="submit" class="btn btn-confirm text-white btn-sm">
                                                <i class="fas fa-check me-2"></i>Confirm
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-success"><i class="fas fa-check-circle"></i> Confirmed</span>
                                    <?php endif; ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="action" value="delete_booking">
                                        <input type="hidden" name="booking_id" value="<?= $row['id'] ?>">
                                        <button type="submit" class="btn btn-delete text-white btn-sm" 
                                                onclick="return confirm('Are you sure you want to delete this booking request?')">
                                            <i class="fas fa-trash me-2"></i>Delete
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Edit Package Modal -->
    <div class="modal fade" id="editPackageModal" tabindex="-1" aria-labelledby="editPackageModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editPackageModalLabel">Edit Package</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit">
                        <input type="hidden" name="package_id" id="edit_package_id">
                        <div class="mb-3">
                            <label for="edit_package_name" class="form-label">Package Name</label>
                            <input type="text" name="package_name" id="edit_package_name" required class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea name="description" id="edit_description" required class="form-control" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit_price" class="form-label">Price (₹)</label>
                            <input type="number" name="price" id="edit_price" required step="0.01" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="edit_included_services" class="form-label">Included Services</label>
                            <textarea name="included_services" id="edit_included_services" required class="form-control" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
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
        // Staggered animation for table rows
        document.addEventListener('DOMContentLoaded', () => {
            const packageRows = document.querySelectorAll('.package-row');
            const bookingRows = document.querySelectorAll('.booking-row');
            [...packageRows, ...bookingRows].forEach((row, index) => {
                row.style.opacity = '0';
                row.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    row.style.transition = 'all 0.5s ease';
                    row.style.opacity = '1';
                    row.style.transform = 'translateY(0)';
                }, index * 100); // Staggered effect
            });
        });

        // Open Edit Modal
        function openEditModal(id, name, description, price, services) {
            document.getElementById('edit_package_id').value = id;
            document.getElementById('edit_package_name').value = name;
            document.getElementById('edit_description').value = description;
            document.getElementById('edit_price').value = price;
            document.getElementById('edit_included_services').value = services;
            new bootstrap.Modal(document.getElementById('editPackageModal')).show();
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>