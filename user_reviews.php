<?php
session_start();

// Debugging: Check if session is properly set
if (!isset($_SESSION['user_id'])) {
    echo "Session not set. Redirecting to login.";
    header("refresh:2;url=login.php"); // Redirect after 2 seconds
    exit();
}

include 'dbdb.php'; // Ensure this is the correct database connection file

// Handle review submission
if (isset($_POST['submit_review'])) {
    $client_name = mysqli_real_escape_string($conn, $_POST['client_name']);
    $rating = (int) $_POST['rating'];
    $review_text = mysqli_real_escape_string($conn, $_POST['review_text']);
    
    $query = "INSERT INTO reviews (client_name, rating, review_text, created_at) 
              VALUES ('$client_name', '$rating', '$review_text', NOW())";

    if (mysqli_query($conn, $query)) {
        $_SESSION['success'] = "Review submitted successfully!";
    } else {
        $_SESSION['error'] = "Error: " . mysqli_error($conn); // Debugging error
    }
    
    header("Location: user_reviews.php");
    exit();
}

// Fetch all reviews
$reviews = mysqli_query($conn, "SELECT * FROM reviews ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Reviews - Share Your Experience</title>
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
            overflow-x: hidden;
        }

        .container {
            max-width: 1200px;
            padding: 40px 20px;
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

        .review-form {
            max-width: 600px;
            margin: 0 auto 40px;
            padding: 30px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            animation: slideUp 1s ease;
        }

        .form-group label {
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

        .btn-primary {
            background-color: var(--primary-light);
            border: none;
            padding: 12px;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .reviews-table {
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            animation: fadeIn 1.5s ease;
        }

        .table {
            border-radius: 8px;
            overflow: hidden;
        }

        .thead-light {
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

        /* Animations */
        @keyframes fadeInDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(50px); }
            to { opacity: 1; transform: translateY(0); }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @keyframes slideIn {
            from { opacity: 0; transform: translateX(-20px); }
            to { opacity: 1; transform: translateX(0); }
        }
    </style>
</head>
<body>
    <div class="container">
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success mt-3">
                <?= $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger mt-3">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <div class="review-form">
            <h2>Share Your Experience</h2>
            <form method="POST">
                <div class="form-group">
                    <label for="client_name">Your Name</label>
                    <input type="text" name="client_name" id="client_name" class="form-control" placeholder="Enter your name" required>
                </div>

                <div class="form-group">
                    <label for="rating">Rating</label>
                    <select name="rating" id="rating" class="form-control" required>
                        <option value="5">⭐⭐⭐⭐⭐ (5 Stars)</option>
                        <option value="4">⭐⭐⭐⭐ (4 Stars)</option>
                        <option value="3">⭐⭐⭐ (3 Stars)</option>
                        <option value="2">⭐⭐ (2 Stars)</option>
                        <option value="1">⭐ (1 Star)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="review_text">Your Review</label>
                    <textarea name="review_text" id="review_text" class="form-control" rows="4" placeholder="Tell us about your experience..." required></textarea>
                </div>

                <button type="submit" name="submit_review" class="btn btn-primary btn-block">Submit Review</button>
            </form>
        </div>

        <div class="reviews-table">
            <h2>Customer Reviews</h2>
            <table class="table table-bordered">
                <thead class="thead-light">
                    <tr>
                        <th>Name</th>
                        <th>Rating</th>
                        <th>Review</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = mysqli_fetch_assoc($reviews)): ?>
                        <tr class="review-row">
                            <td><?= htmlspecialchars($row['client_name']) ?></td>
                            <td><?= str_repeat('⭐', $row['rating']) ?></td>
                            <td><?= htmlspecialchars($row['review_text']) ?></td>
                            <td><?= date('F j, Y - H:i', strtotime($row['created_at'])) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add animation to review rows on page load
        document.addEventListener('DOMContentLoaded', () => {
            const rows = document.querySelectorAll('.review-row');
            rows.forEach((row, index) => {
                row.style.opacity = '0';
                row.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    row.style.transition = 'all 0.5s ease';
                    row.style.opacity = '1';
                    row.style.transform = 'translateY(0)';
                }, index * 100); // Staggered animation
            });
        });
    </script>
</body>
</html>