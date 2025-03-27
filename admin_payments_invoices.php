<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'photography_services');
if ($conn->connect_error) die('Connection Failed: ' . $conn->connect_error);

// Temporarily bypass session check for testing
// if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
//     header("Location: login.php");
//     exit;
// }

// Handle actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'add_invoice') {
        $client_name = $conn->real_escape_string($_POST['client_name']);
        $client_email = $conn->real_escape_string($_POST['client_email']);
        $amount = floatval($_POST['amount']);
        $description = $conn->real_escape_string($_POST['description']);
        $due_date = $_POST['due_date'];
        $user_id = 1; // Placeholder for testing
        $stmt = $conn->prepare("INSERT INTO invoices (user_id, client_name, client_email, amount, description, due_date) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("issdss", $user_id, $client_name, $client_email, $amount, $description, $due_date);
        $stmt->execute();
        $_SESSION['success'] = "Invoice added successfully!";
    } elseif ($_POST['action'] === 'edit_invoice') {
        $invoice_id = intval($_POST['invoice_id']);
        $client_name = $conn->real_escape_string($_POST['client_name']);
        $client_email = $conn->real_escape_string($_POST['client_email']);
        $amount = floatval($_POST['amount']);
        $description = $conn->real_escape_string($_POST['description']);
        $due_date = $_POST['due_date'];
        $stmt = $conn->prepare("UPDATE invoices SET client_name=?, client_email=?, amount=?, description=?, due_date=? WHERE invoice_id=?");
        $stmt->bind_param("ssdssi", $client_name, $client_email, $amount, $description, $due_date, $invoice_id);
        $stmt->execute();
        $_SESSION['success'] = "Invoice updated successfully!";
    } elseif ($_POST['action'] === 'delete_invoice') {
        $invoice_id = intval($_POST['invoice_id']);
        $stmt = $conn->prepare("DELETE FROM invoices WHERE invoice_id=?");
        $stmt->bind_param("i", $invoice_id);
        $stmt->execute();
        $_SESSION['success'] = "Invoice deleted successfully!";
    } elseif ($_POST['action'] === 'verify_payment') {
        $payment_id = intval($_POST['payment_id']);
        $invoice_id = intval($_POST['invoice_id']);
        $stmt = $conn->prepare("UPDATE payments SET status='Completed' WHERE payment_id=?");
        $stmt->bind_param("i", $payment_id);
        $stmt->execute();
        $stmt = $conn->prepare("UPDATE invoices SET status='Paid' WHERE invoice_id=?");
        $stmt->bind_param("i", $invoice_id);
        $stmt->execute();
        $_SESSION['success'] = "Payment verified successfully!";
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Fetch invoices and payments
$invoices = $conn->query("SELECT * FROM invoices ORDER BY created_at DESC");
$payments = $conn->query("SELECT p.*, i.client_name, i.invoice_id FROM payments p JOIN invoices i ON p.invoice_id = i.invoice_id ORDER BY payment_date DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Payments & Invoices</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root { --primary-dark: #1e3a8a; --primary-light: #3b82f6; --accent: #ef4444; }
        body { font-family: 'Segoe UI', sans-serif; background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%); color: #333; margin: 0; padding: 0; overflow-x: hidden; }
        .navbar { background-color: var(--primary-dark); box-shadow: 0 2px 15px rgba(0, 0, 0, 0.2); padding: 15px 0; }
        .navbar-brand, .nav-link { color: white !important; font-weight: 600; transition: color 0.3s ease, transform 0.3s ease; }
        .nav-link:hover { color: var(--primary-light) !important; transform: scale(1.05); }
        .container { max-width: 1400px; padding: 40px 20px; animation: fadeIn 1s ease; }
        h2 { color: var(--primary-dark); font-weight: 700; text-align: center; margin-bottom: 40px; text-transform: uppercase; letter-spacing: 1.5px; animation: fadeInDown 1s ease; }
        .form-container, .table { background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); padding: 20px; }
        .form-container { max-width: 600px; margin: 0 auto 40px; animation: slideUp 0.5s ease; }
        .form-control { border-radius: 8px; border: 1px solid #ddd; padding: 12px; transition: all 0.3s ease; }
        .form-control:focus { border-color: var(--primary-light); box-shadow: 0 0 8px rgba(59, 130, 246, 0.5); transform: scale(1.02); }
        .btn { border-radius: 8px; padding: 10px 20px; font-weight: 600; transition: all 0.3s ease; }
        .btn-add { background-color: var(--primary-light); border: none; }
        .btn-add:hover { background-color: var(--primary-dark); transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2); }
        .btn-edit { background-color: var(--primary-light); border: none; }
        .btn-edit:hover { background-color: var(--primary-dark); transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2); }
        .btn-delete { background-color: var(--accent); border: none; }
        .btn-delete:hover { background-color: #dc2626; transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2); }
        .btn-verify { background-color: #10b981; border: none; }
        .btn-verify:hover { background-color: #059669; transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2); }
        .table thead { background-color: var(--primary-dark); color: white; }
        .table tbody tr { transition: all 0.3s ease; }
        .table tbody tr:hover { background-color: #f1f5f9; transform: scale(1.01); }
        .alert-success { background-color: #d1fae5; color: #065f46; border: none; border-radius: 8px; animation: slideIn 0.5s ease; }
        .modal-content { border-radius: 12px; box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2); animation: slideUp 0.5s ease; }
        .modal-header { background-color: var(--primary-dark); color: white; border-bottom: none; border-radius: 12px 12px 0 0; }
        .btn-close { color: white; opacity: 0.8; }
        .btn-close:hover { opacity: 1; }
        footer { background-color: var(--primary-dark); color: white; padding: 20px 0; text-align: center; margin-top: 40px; }
        footer a { color: var(--primary-light); text-decoration: none; transition: color 0.3s ease; }
        footer a:hover { color: white; }
        .screenshot-img { max-width: 100px; cursor: pointer; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes fadeInDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes slideUp { from { opacity: 0; transform: translateY(50px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes slideIn { from { opacity: 0; transform: translateX(-20px); } to { opacity: 1; transform: translateX(0); } }
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
                    <li class="nav-item"><a class="nav-link" href="re.php">Reviews</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin_shoot.php">Shoot Requests</a></li>
                    <li class="nav-item"><a class="nav-link" href="admin_pricing.php">Pricing & Packages</a></li>
                    <li class="nav-item"><a class="nav-link" href="#features">Payment & Inoices</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <h2>Manage Payments & Invoices</h2>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success text-center"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <!-- Add Invoice Form -->
        <div class="form-container">
            <h3 class="text-center mb-4">Add New Invoice</h3>
            <form method="POST">
                <input type="hidden" name="action" value="add_invoice">
                <div class="mb-3">
                    <input type="text" name="client_name" placeholder="Client Name" required class="form-control">
                </div>
                <div class="mb-3">
                    <input type="email" name="client_email" placeholder="Client Email" required class="form-control">
                </div>
                <div class="mb-3">
                    <input type="number" name="amount" placeholder="Amount (₹)" step="0.01" required class="form-control">
                </div>
                <div class="mb-3">
                    <textarea name="description" placeholder="Description" class="form-control" rows="3"></textarea>
                </div>
                <div class="mb-3">
                    <input type="date" name="due_date" required class="form-control">
                </div>
                <button type="submit" class="btn btn-add text-white w-100"><i class="fas fa-plus me-2"></i>Add Invoice</button>
            </form>
        </div>

        <!-- Invoices Table -->
        <section id="features">
        <h2>Invoices</h2>
        <div class="features">
        <?php if ($invoices->num_rows === 0): ?>
            <div class="alert alert-info text-center">No invoices available.</div>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Client</th>
                        <th>Email</th>
                        <th>Amount</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Due Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $invoices->fetch_assoc()): ?>
                        <tr class="invoice-row">
                            <td><?= $row['invoice_id'] ?></td>
                            <td><?= htmlspecialchars($row['client_name']) ?></td>
                            <td><?= htmlspecialchars($row['client_email']) ?></td>
                            <td>₹<?= number_format($row['amount'], 2) ?></td>
                            <td><?= htmlspecialchars($row['description']) ?></td>
                            <td class="<?= $row['status'] === 'Paid' ? 'text-success' : ($row['status'] === 'Overdue' ? 'text-danger' : 'text-warning') ?>">
                                <?= $row['status'] ?>
                            </td>
                            <td><?= date('F j, Y', strtotime($row['due_date'])) ?></td>
                            <td>
                                <button class="btn btn-edit text-white btn-sm me-2" onclick="openEditModal(<?= $row['invoice_id'] ?>, '<?= htmlspecialchars($row['client_name']) ?>', '<?= htmlspecialchars($row['client_email']) ?>', '<?= $row['amount'] ?>', '<?= htmlspecialchars($row['description']) ?>', '<?= $row['due_date'] ?>')">
                                    <i class="fas fa-edit me-2"></i>Edit
                                </button>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="delete_invoice">
                                    <input type="hidden" name="invoice_id" value="<?= $row['invoice_id'] ?>">
                                    <button type="submit" class="btn btn-delete text-white btn-sm" onclick="return confirm('Are you sure you want to delete this invoice?')">
                                        <i class="fas fa-trash me-2"></i>Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <!-- Payments Table -->
        <h2 class="mt-5">Payments</h2>
        <?php if ($payments->num_rows === 0): ?>
            <div class="alert alert-info text-center">No payments recorded.</div>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Client</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Screenshot</th>
                        <th>User Message</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $payments->fetch_assoc()): ?>
                        <tr class="payment-row">
                            <td><?= $row['payment_id'] ?></td>
                            <td><?= htmlspecialchars($row['client_name']) ?></td>
                            <td>₹<?= number_format($row['amount'], 2) ?></td>
                            <td><?= $row['payment_method'] ?></td>
                            <td>
                                <?php if ($row['screenshot']): ?>
                                    <a href="uploads/<?= htmlspecialchars($row['screenshot']) ?>" target="_blank">
                                        <img src="uploads/<?= htmlspecialchars($row['screenshot']) ?>" class="screenshot-img" alt="Screenshot">
                                    </a>
                                <?php else: ?>
                                    <span class="text-muted">N/A</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($row['user_message'] ?? 'N/A') ?></td>
                            <td><?= date('F j, Y - H:i', strtotime($row['payment_date'])) ?></td>
                            <td class="<?= $row['status'] === 'Completed' ? 'text-success' : ($row['status'] === 'Failed' ? 'text-danger' : 'text-warning') ?>">
                                <?= $row['status'] ?>
                            </td>
                            <td>
                                <?php if ($row['payment_method'] === 'GPay' && $row['status'] === 'Pending' && $row['screenshot']): ?>
                                    <form method="POST" style="display:inline;">
                                        <input type="hidden" name="action" value="verify_payment">
                                        <input type="hidden" name="payment_id" value="<?= $row['payment_id'] ?>">
                                        <input type="hidden" name="invoice_id" value="<?= $row['invoice_id'] ?>">
                                        <button type="submit" class="btn btn-verify text-white btn-sm" onclick="return confirm('Verify this GPay payment?')">
                                            <i class="fas fa-check me-2"></i>Verify
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Edit Invoice Modal -->
    <div class="modal fade" id="editInvoiceModal" tabindex="-1" aria-labelledby="editInvoiceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editInvoiceModalLabel">Edit Invoice</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <input type="hidden" name="action" value="edit_invoice">
                        <input type="hidden" name="invoice_id" id="edit_invoice_id">
                        <div class="mb-3">
                            <label for="edit_client_name" class="form-label">Client Name</label>
                            <input type="text" name="client_name" id="edit_client_name" required class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="edit_client_email" class="form-label">Client Email</label>
                            <input type="email" name="client_email" id="edit_client_email" required class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="edit_amount" class="form-label">Amount (₹)</label>
                            <input type="number" name="amount" id="edit_amount" step="0.01" required class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="edit_description" class="form-label">Description</label>
                            <textarea name="description" id="edit_description" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="edit_due_date" class="form-label">Due Date</label>
                            <input type="date" name="due_date" id="edit_due_date" required class="form-control">
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
        document.addEventListener('DOMContentLoaded', () => {
            const invoiceRows = document.querySelectorAll('.invoice-row');
            const paymentRows = document.querySelectorAll('.payment-row');
            [...invoiceRows, ...paymentRows].forEach((row, index) => {
                row.style.opacity = '0';
                row.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    row.style.transition = 'all 0.5s ease';
                    row.style.opacity = '1';
                    row.style.transform = 'translateY(0)';
                }, index * 100);
            });
        });

        function openEditModal(id, name, email, amount, description, due_date) {
            document.getElementById('edit_invoice_id').value = id;
            document.getElementById('edit_client_name').value = name;
            document.getElementById('edit_client_email').value = email;
            document.getElementById('edit_amount').value = amount;
            document.getElementById('edit_description').value = description;
            document.getElementById('edit_due_date').value = due_date;
            new bootstrap.Modal(document.getElementById('editInvoiceModal')).show();
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>