<?php
include 'db_connect.php';

// Handle Delete Request
if (isset($_GET['delete_user'])) {
   // Check if the user has related orders before deletion
$user_id = intval($_GET['delete_user']);
$check_orders = "SELECT COUNT(*) FROM user_orders WHERE user_id = ?";
$stmt = $conn->prepare($check_orders);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($order_count);
$stmt->fetch();
$stmt->close();

if ($order_count > 0) {
    echo "<script>alert('Cannot delete user. Orders exist for this user.'); window.location.href='manage_user.php';</script>";
    exit();
}

// If no orders exist, proceed with deletion
$delete_sql = "DELETE FROM users WHERE id = ?";
$stmt = $conn->prepare($delete_sql);
$stmt->bind_param("i", $user_id);
if ($stmt->execute()) {
    echo "<script>alert('User deleted successfully.'); window.location.href='manage_user.php';</script>";
} else {
    echo "<script>alert('Error deleting user.'); window.location.href='manage_user.php';</script>";
}

}

// Handle Edit Request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_user'])) {
    $user_id = intval($_POST['user_id']);
    $username = filter_var($_POST['username'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_BCRYPT) : null;

    if ($password) {
        $update_sql = "UPDATE users SET username=?, email=?, password=? WHERE id=?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("sssi", $username, $email, $password, $user_id);
    } else {
        $update_sql = "UPDATE users SET username=?, email=? WHERE id=?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("ssi", $username, $email, $user_id);
    }

    if ($stmt->execute()) {
        echo "<script>alert('User updated successfully.'); window.location.href='manage_user.php';</script>";
    } else {
        echo "<div class='alert alert-danger text-center'>Error updating user.</div>";
    }
}

// Fetch Users
$result = $conn->query("SELECT id, username, email FROM users");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Users - Admin Dashboard</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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

        /* Main Content */
        .container {
            max-width: 1200px;
            padding: 40px 20px;
            animation: fadeIn 1s ease;
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

        .table {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
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

        .btn-edit, .btn-delete {
            padding: 8px 20px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
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
            color: white;
            text-decoration: none;
        }

        .btn-delete:hover {
            background-color: #dc2626;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

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

        .btn-save {
            background-color: #10b981;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
        }

        .btn-save:hover {
            background-color: #059669;
        }

        .btn-cancel {
            background-color: #6b7280;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
        }

        .btn-cancel:hover {
            background-color: #4b5563;
        }

        /* Footer */
        footer {
            background-color: var(--primary-dark);
            color: white;
            padding: 20px 0;
            text-align: center;
            position: relative;
            bottom: 0;
            width: 100%;
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
            <a class="navbar-brand" href="#">Admin Dashboard</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ml-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="admin_dash.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="manage_user.php">Manage Users</a>
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
        <h1>Manage Users</h1>
        <table class="table">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['username']) ?></td>
                        <td><?= htmlspecialchars($row['email']) ?></td>
                        <td>
                            <button onclick="editUser('<?= $row['id'] ?>', '<?= htmlspecialchars($row['username']) ?>', '<?= htmlspecialchars($row['email']) ?>')" class="btn-edit text-white">Edit</button>
                            <a href="manage_user.php?delete_user=<?= $row['id'] ?>" onclick="return confirm('Are you sure you want to delete this user?')" class="btn-delete">Delete</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- Edit User Modal -->
    <div id="editUserModal" class="hidden fixed inset-0 bg-gray-600 bg-opacity-50 flex justify-center items-center">
        <div class="modal-content bg-white p-6 w-96">
            <div class="modal-header">
                <h2 class="text-xl font-bold">Edit User</h2>
            </div>
            <form method="post" action="manage_user.php">
                <input type="hidden" id="edit_user_id" name="user_id">
                <div class="mt-4">
                    <label class="block">Username</label>
                    <input type="text" id="edit_username" name="username" required class="form-control">
                </div>
                <div class="mt-4">
                    <label class="block">Email</label>
                    <input type="email" id="edit_email" name="email" required class="form-control">
                </div>
                <div class="mt-4">
                    <label class="block">New Password (Optional)</label>
                    <input type="password" name="password" class="form-control" placeholder="Leave blank to keep current password">
                </div>
                <div class="flex justify-end mt-6">
                    <button type="button" onclick="closeModal()" class="btn-cancel text-white mr-2">Cancel</button>
                    <button type="submit" name="edit_user" class="btn-save text-white">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <div class="container">
            <p>&copy; <?= date('Y') ?> Photography Admin Dashboard. All rights reserved.</p>
            <p><a href="#">Privacy Policy</a> | <a href="#">Terms of Service</a></p>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function editUser(id, username, email) {
            document.getElementById("edit_user_id").value = id;
            document.getElementById("edit_username").value = username;
            document.getElementById("edit_email").value = email;
            document.getElementById("editUserModal").classList.remove("hidden");
        }

        function closeModal() {
            document.getElementById("editUserModal").classList.add("hidden");
        }
    </script>
</body>
</html>

<?php $conn->close(); ?>