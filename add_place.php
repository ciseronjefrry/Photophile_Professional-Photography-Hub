<?php
include 'db_connect.php';

// Check database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if (isset($_POST['add_place'])) {
    $place_name = filter_var($_POST['place_name'], FILTER_SANITIZE_STRING);
    $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
    $price = filter_var($_POST['price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

    // Validate inputs
    if (empty($place_name) || empty($description) || $price <= 0) {
        $error = "Please fill in all fields with valid data.";
    } else {
        // Handle file upload
        if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $image_name = $_FILES['image']['name'];
            $image_tmp = $_FILES['image']['tmp_name'];
            $target_dir = "images/";
            $target_file = $target_dir . basename($image_name);

            // Validate file type and size
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];
            $file_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
            $file_size = $_FILES['image']['size'];

            if (!in_array($file_ext, $allowed_types)) {
                $error = "Only JPG, JPEG, PNG, and GIF files are allowed.";
            } elseif ($file_size > 5 * 1024 * 1024) { // 5MB limit
                $error = "File size must not exceed 5MB.";
            } else {
                // Move file and insert into database
                if (move_uploaded_file($image_tmp, $target_file)) {
                    $insert_sql = "INSERT INTO places (place_name, description, price, image) VALUES (?, ?, ?, ?)";
                    $stmt = $conn->prepare($insert_sql);
                    if ($stmt) {
                        $stmt->bind_param("ssds", $place_name, $description, $price, $image_name);
                        if ($stmt->execute()) {
                            header("Location: admin_dash.php"); // Redirect to admin dashboard
                            exit;
                        } else {
                            $error = "Database error: " . $stmt->error;
                        }
                        $stmt->close();
                    } else {
                        $error = "Prepare failed: " . $conn->error;
                    }
                } else {
                    $error = "Failed to upload image. Check folder permissions.";
                }
            }
        } else {
            $error = "Image upload failed: " . $_FILES['image']['error'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Add New Photoshoot Place</title>
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

        .form-container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            animation: slideUp 0.5s ease;
        }

        .form-label {
            color: var(--primary-dark);
            font-weight: 500;
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

        .btn-submit {
            background-color: var(--primary-light);
            border: none;
            padding: 12px 25px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-submit:hover {
            background-color: var(--primary-dark);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
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
                    <li class="nav-item">
                        <a class="nav-link" href="admin_dash.php">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Places</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Bookings</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        <h1>Add New Photoshoot Place</h1>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger text-center"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        <div class="form-container">
            <form method="post" action="" enctype="multipart/form-data">
                <div class="mb-4">
                    <label for="place_name" class="form-label">Place Name</label>
                    <input type="text" id="place_name" name="place_name" required class="form-control" placeholder="Enter place name">
                </div>
                <div class="mb-4">
                    <label for="description" class="form-label">Description</label>
                    <textarea id="description" name="description" required class="form-control" rows="4" placeholder="Describe the photoshoot location"></textarea>
                </div>
                <div class="mb-4">
                    <label for="price" class="form-label">Price ($)</label>
                    <input type="number" id="price" name="price" step="0.01" min="0" required class="form-control" placeholder="e.g., 150.00">
                </div>
                <div class="mb-4">
                    <label for="image" class="form-label">Upload Image</label>
                    <input type="file" id="image" name="image" accept="image/*" required class="form-control">
                    <p class="text-sm text-gray-500 mt-1">Accepted formats: JPG, PNG, GIF (max 5MB)</p>
                </div>
                <button type="submit" name="add_place" class="btn btn-submit text-white"><i class="fas fa-plus me-2"></i>Add Place</button>
            </form>
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
</body>
</html>

<?php $conn->close(); ?>