<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'photography_services');
if ($conn->connect_error) die('Connection Failed: ' . $conn->connect_error);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Handle payment submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['make_payment'])) {
    $invoice_id = intval($_POST['invoice_id']);
    $amount = floatval($_POST['amount']);
    $payment_method = $conn->real_escape_string($_POST['payment_method']);
    $user_message = $conn->real_escape_string($_POST['user_message'] ?? '');
    $screenshot = null;

    if ($payment_method === 'GPay' && isset($_FILES['screenshot']) && $_FILES['screenshot']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        $screenshot_name = uniqid() . "_" . basename($_FILES['screenshot']['name']);
        $target_file = $target_dir . $screenshot_name;
        $allowed_types = ['jpg', 'jpeg', 'png'];

        $file_ext = strtolower(pathinfo($screenshot_name, PATHINFO_EXTENSION));
        if (!in_array($file_ext, $allowed_types)) {
            $_SESSION['error'] = "Only JPG, JPEG, and PNG files are allowed.";
        } elseif ($_FILES['screenshot']['size'] > 5 * 1024 * 1024) {
            $_SESSION['error'] = "File size must not exceed 5MB.";
        } elseif (move_uploaded_file($_FILES['screenshot']['tmp_name'], $target_file)) {
            $screenshot = $screenshot_name;
            $_SESSION['gpay_instruction'] = "Please send ₹" . number_format($amount, 2) . " via GPay to 6374768127. Your screenshot has been uploaded and is pending verification.";
        } else {
            $_SESSION['error'] = "Failed to upload screenshot.";
        }
    } elseif ($payment_method === 'GPay') {
        $_SESSION['gpay_instruction'] = "Please send ₹" . number_format($amount, 2) . " via GPay to 6374768127 and upload a screenshot.";
    }

    if (!isset($_SESSION['error'])) {
        $stmt = $conn->prepare("INSERT INTO payments (invoice_id, user_id, amount, payment_method, screenshot, user_message) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("iidsss", $invoice_id, $_SESSION['user_id'], $amount, $payment_method, $screenshot, $user_message);
        $stmt->execute();

        if ($payment_method !== 'GPay') {
            $stmt = $conn->prepare("UPDATE invoices SET status='Paid' WHERE invoice_id=? AND user_id=?");
            $stmt->bind_param("ii", $invoice_id, $_SESSION['user_id']);
            $stmt->execute();
            $_SESSION['success'] = "Payment processed successfully!";
        } else {
            $_SESSION['success'] = "GPay payment request recorded. " . ($screenshot ? "Screenshot uploaded, awaiting verification." : "Please upload a screenshot.");
        }
    }
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Handle delete actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_invoice'])) {
    $invoice_id = intval($_POST['invoice_id']);
    $stmt = $conn->prepare("DELETE FROM invoices WHERE invoice_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $invoice_id, $_SESSION['user_id']);
    $stmt->execute();
    $_SESSION['success'] = "Invoice deleted successfully!";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_payment'])) {
    $payment_id = intval($_POST['payment_id']);
    $stmt = $conn->prepare("DELETE FROM payments WHERE payment_id = ? AND user_id = ?");
    $stmt->bind_param("ii", $payment_id, $_SESSION['user_id']);
    $stmt->execute();
    $_SESSION['success'] = "Payment deleted successfully!";
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Fetch user's invoices and payments
$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT * FROM invoices WHERE user_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$invoices = $stmt->get_result();

$stmt = $conn->prepare("SELECT p.*, i.client_name FROM payments p JOIN invoices i ON p.invoice_id = i.invoice_id WHERE p.user_id = ? ORDER BY payment_date DESC");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$payments = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User - Payments & Invoices</title>
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
        .table { background: white; border-radius: 12px; box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1); padding: 20px; }
        .table thead { background-color: var(--primary-dark); color: white; }
        .table tbody tr { transition: all 0.3s ease; }
        .table tbody tr:hover { background-color: #f1f5f9; transform: scale(1.01); }
        .btn { border-radius: 8px; padding: 10px 20px; font-weight: 600; transition: all 0.3s ease; }
        .btn-pay { background-color: var(--primary-light); border: none; }
        .btn-pay:hover { background-color: var(--primary-dark); transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2); }
        .btn-delete { background-color: var(--accent); border: none; }
        .btn-delete:hover { background-color: #dc2626; transform: translateY(-3px); box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2); }
        .alert-success { background-color: #d1fae5; color: #065f46; border: none; border-radius: 8px; animation: slideIn 0.5s ease; }
        .alert-info { background-color: #e0f2fe; color: #1e40af; border: none; border-radius: 8px; animation: slideIn 0.5s ease; }
        .alert-danger { background-color: #fee2e2; color: var(--accent); border: none; border-radius: 8px; animation: slideIn 0.5s ease; }
        footer { background-color: var(--primary-dark); color: white; padding: 20px 0; text-align: center; margin-top: 40px; }
        footer a { color: var(--primary-light); text-decoration: none; transition: color 0.3s ease; }
        footer a:hover { color: white; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes fadeInDown { from { opacity: 0; transform: translateY(-20px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes slideIn { from { opacity: 0; transform: translateX(-20px); } to { opacity: 1; transform: translateX(0); } }
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
                    <li class="nav-item"><a class="nav-link" href="user_book.php">Book a Shoot</a></li>
                    <li class="nav-item"><a class="nav-link" href="#">Payments & Invoices</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <h2>Your Payments & Invoices (Welcome, <?= htmlspecialchars($_SESSION['username']) ?>)</h2>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success text-center"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['gpay_instruction'])): ?>
            <div class="alert alert-info text-center"><?= $_SESSION['gpay_instruction']; unset($_SESSION['gpay_instruction']); ?></div>
        <?php endif; ?>
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger text-center"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <!-- Invoices Table -->
        <h2>Invoices</h2>
        <?php if ($invoices->num_rows === 0): ?>
            <div class="alert alert-info text-center">No invoices available.</div>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Amount</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th>Due Date</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $invoices->fetch_assoc()): ?>
                        <tr class="invoice-row">
                            <td><?= $row['invoice_id'] ?></td>
                            <td>₹<?= number_format($row['amount'], 2) ?></td>
                            <td><?= htmlspecialchars($row['description']) ?></td>
                            <td class="<?= $row['status'] === 'Paid' ? 'text-success' : ($row['status'] === 'Overdue' ? 'text-danger' : 'text-warning') ?>">
                                <?= $row['status'] ?>
                            </td>
                            <td><?= date('F j, Y', strtotime($row['due_date'])) ?></td>
                            <td>
                                <?php if ($row['status'] !== 'Paid'): ?>
                                    <form method="POST" enctype="multipart/form-data">
                                        <input type="hidden" name="invoice_id" value="<?= $row['invoice_id'] ?>">
                                        <input type="hidden" name="amount" value="<?= $row['amount'] ?>">
                                        <select name="payment_method" class="form-control mb-2" required>
                                            <option value="Credit Card">Credit Card</option>
                                            <option value="PayPal">PayPal</option>
                                            <option value="Bank Transfer">Bank Transfer</option>
                                            <option value="GPay">GPay</option>
                                        </select>
                                        <div id="screenshot_field_<?= $row['invoice_id'] ?>" style="display: none;" class="mb-2">
                                            <label for="screenshot_<?= $row['invoice_id'] ?>">Upload GPay Screenshot:</label>
                                            <input type="file" name="screenshot" id="screenshot_<?= $row['invoice_id'] ?>" class="form-control" accept="image/*">
                                        </div>
                                        <div class="mb-2">
                                            <label for="user_message_<?= $row['invoice_id'] ?>">Message to Admin:</label>
                                            <textarea name="user_message" id="user_message_<?= $row['invoice_id'] ?>" class="form-control" rows="2" placeholder="Optional message"></textarea>
                                        </div>
                                        <button type="submit" name="make_payment" class="btn btn-pay text-white btn-sm me-2">
                                            <i class="fas fa-money-bill-wave me-2"></i>Pay Now
                                        </button>
                                    </form>
                                <?php endif; ?>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="delete_invoice">
                                    <input type="hidden" name="invoice_id" value="<?= $row['invoice_id'] ?>">
                                    <button type="submit" class="btn btn-delete text-white btn-sm" onclick="return confirm('Are you sure you want to delete this invoice?')">
                                        <i class="fas fa-trash me-2"></i>Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                        <script>
                            document.querySelector('select[name="payment_method"]').addEventListener('change', function() {
                                document.getElementById('screenshot_field_<?= $row['invoice_id'] ?>').style.display = this.value === 'GPay' ? 'block' : 'none';
                            });
                        </script>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <!-- Payments Table -->
        <h2 class="mt-5">Payment History</h2>
        <?php if ($payments->num_rows === 0): ?>
            <div class="alert alert-info text-center">No payments recorded.</div>
        <?php else: ?>
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Amount</th>
                        <th>Method</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $payments->fetch_assoc()): ?>
                        <tr class="payment-row">
                            <td><?= $row['payment_id'] ?></td>
                            <td>₹<?= number_format($row['amount'], 2) ?></td>
                            <td><?= $row['payment_method'] ?></td>
                            <td><?= date('F j, Y - H:i', strtotime($row['payment_date'])) ?></td>
                            <td class="<?= $row['status'] === 'Completed' ? 'text-success' : ($row['status'] === 'Failed' ? 'text-danger' : 'text-warning') ?>">
                                <?= $row['status'] ?>
                            </td>
                            <td>
                                <form method="POST" style="display:inline;">
                                    <input type="hidden" name="action" value="delete_payment">
                                    <input type="hidden" name="payment_id" value="<?= $row['payment_id'] ?>">
                                    <button type="submit" class="btn btn-delete text-white btn-sm" onclick="return confirm('Are you sure you want to delete this payment?')">
                                        <i class="fas fa-trash me-2"></i>Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>© <?= date('Y') ?> Photography Services. All Rights Reserved.</p>
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

            document.querySelectorAll('select[name="payment_method"]').forEach(select => {
                select.addEventListener('change', function() {
                    const invoiceId = this.closest('form').querySelector('input[name="invoice_id"]').value;
                    document.getElementById('screenshot_field_' + invoiceId).style.display = this.value === 'GPay' ? 'block' : 'none';
                });
            });
        });
    </script>
</body>
</html>

<?php $conn->close(); ?>