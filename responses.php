<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'database.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_email'])) {
    header("Location: login.php"); // Redirect if not logged in
    exit();
}

$user_email = $_SESSION['user_email']; // Get logged-in user's email

// Fetch only messages belonging to the logged-in user
$sql = "SELECT * FROM contact_messages WHERE email = ? AND reply IS NOT NULL";
$stmt = $conn->prepare($sql);

if (!$stmt) {
    die("SQL Error: " . $conn->error);
}

$stmt->bind_param("s", $user_email);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Messages & Replies</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .container { max-width: 800px; margin-top: 50px; }
        .card { border-radius: 10px; box-shadow: 0px 4px 8px rgba(0, 0, 0, 0.1); }
        .card-body { background: white; border-radius: 10px; padding: 20px; }
        .footer { text-align: center; padding: 10px; margin-top: 20px; background: green; color: white; }
    </style>
</head>
<body>

    <div class="container">
        <h2 class="text-center">Your Messages & Replies</h2>

        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <div class="card mt-3">
                    <div class="card-body">
                        <h5 class="card-title">Message:</h5>
                        <p><?php echo htmlspecialchars($row['message']); ?></p>
                        <hr>
                        <h5 class="text-success">Admin Reply:</h5>
                        <p><?php echo htmlspecialchars($row['reply']); ?></p>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="alert alert-info text-center mt-4">No replies yet. Please check back later.</div>
        <?php endif; ?>
        
        <div class="text-center mt-3">
            <a href="contact.php" class="btn btn-success">Go Back</a>
        </div>
    </div>

    <footer class="footer">
        <p>&copy; 2025 Photography Services. All Rights Reserved.</p>
    </footer>

</body>
</html>
