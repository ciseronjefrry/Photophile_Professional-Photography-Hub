<?php
session_start(); // Start session to store user data
$conn = new mysqli('localhost', 'root', '', 'photography_services');
if ($conn->connect_error) die('Connection Failed');

// Ensure email is defined (use user_email to match login.php consistency)
$email = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : '';

// Redirect to login if not authenticated
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email']; // Capture email from form
    $_SESSION['user_email'] = $email; // Store email in session (optional, but kept as per your code)
    $phone = $_POST['phone'];
    $shoot_type = $_POST['shoot_type'];
    $location = $_POST['location'];
    $date_time = $_POST['date_time'];
    $additional_requests = $_POST['additional_requests'];
    
    // Handle file upload
    $uploaded_files = [];
    foreach ($_FILES['reference_images']['tmp_name'] as $key => $tmp_name) {
        if ($_FILES['reference_images']['size'][$key] > 0) {
            $file_name = time() . '_' . basename($_FILES['reference_images']['name'][$key]); // Unique filename
            move_uploaded_file($tmp_name, "uploads/$file_name");
            $uploaded_files[] = $file_name;
        }
    }
    $images_json = json_encode($uploaded_files);

    // Insert shoot request using user_id directly from session (not email lookup)
    $stmt = $conn->prepare("INSERT INTO shoot_requests (user_id, shoot_type, location, date_time, additional_requests, reference_images, status) 
                            VALUES (?, ?, ?, ?, ?, ?, 'Pending')");
    $stmt->bind_param("isssss", $_SESSION['user_id'], $shoot_type, $location, $date_time, $additional_requests, $images_json);
    $stmt->execute();

    echo '<script>alert("Shoot Request Submitted Successfully!");</script>';
}

// Fetch user shoot requests using user_id (not email) for isolation
$stmt = $conn->prepare("SELECT * FROM shoot_requests WHERE user_id = ?");
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$user_requests = $stmt->get_result();

// Generate unique color based on user_id for UI
$user_id = $_SESSION['user_id'];
$unique_hue = ($user_id * 137) % 360; // Simple hash-like function for unique hue
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Custom Shoot Request - Photography Services</title>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.15.4/css/all.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: hsl(<?php echo $unique_hue; ?>, 50%, 30%); /* Unique primary color */
            --secondary: hsl(<?php echo $unique_hue; ?>, 70%, 50%); /* Unique secondary color */
            --accent: #ef4444;    /* Red */
            --bg-light: #f3f4f6;
            --text-light: #111827;
            --text-dark: #d1d5db;
            --card-bg-light: white;
            --shadow: 0 8px 25px rgba(0,0,0,0.15);
            --gradient: linear-gradient(135deg, var(--primary), var(--secondary));
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Roboto', sans-serif;
            background: var(--bg-light);
            color: var(--text-light);
            line-height: 1.6;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .hero {
            background: var(--gradient);
            color: white;
            padding: 80px 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
            animation: fadeIn 1.5s ease-in-out;
        }
        .hero::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(59, 130, 246, 0.2), transparent);
            animation: rotateGlow 15s infinite linear;
            z-index: 0;
        }
        .hero h1, .hero p {
            position: relative;
            z-index: 1;
        }
        .hero h1 {
            font-size: 3rem;
            font-weight: 700;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.3);
            margin-bottom: 10px;
        }
        .hero p {
            font-size: 1.5rem;
            max-width: 900px;
            margin: 0 auto;
        }
        .container {
            max-width: 900px; /* Narrower for focus */
            margin: 0 auto;
            padding: 30px 20px;
            background: var(--card-bg-light);
            border-radius: 15px;
            box-shadow: var(--shadow);
            animation: fadeInUp 1s ease-in-out;
            margin-top: -40px; /* Overlap hero slightly */
        }
        .form-section {
            padding: 20px;
        }
        h2 {
            font-size: 2rem;
            color: var(--primary);
            text-align: center;
            margin-bottom: 30px;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.1);
        }
        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }
        input, select, textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid var(--secondary);
            border-radius: 8px;
            font-size: 1rem;
            transition: border-color 0.3s, box-shadow 0.3s;
        }
        input:focus, select:focus, textarea:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 5px rgba(30, 58, 138, 0.5);
        }
        textarea {
            min-height: 100px;
            resize: vertical;
        }
        label {
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 5px;
            display: block;
        }
        button[type="submit"] {
            background: var(--secondary);
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            transition: background 0.3s, transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }
        button[type="submit"]:hover {
            background: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(30, 58, 138, 0.4);
        }
        .requests-section {
            margin-top: 40px;
            padding: 20px;
            background: var(--card-bg-light);
            border-radius: 15px;
            box-shadow: var(--shadow);
            animation: fadeInUp 1s ease-in-out;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 12px;
            text-align: center;
            border-bottom: 1px solid #e0e0e0;
        }
        th {
            background: var(--primary);
            color: white;
            font-weight: 700;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.2);
        }
        td img {
            width: 50px;
            height: auto;
            margin: 0 5px;
            border-radius: 5px;
            transition: transform 0.3s;
        }
        td img:hover {
            transform: scale(1.5);
        }
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.7);
            z-index: 1000;
            animation: fadeIn 0.5s ease-in-out;
        }
        .modal.show {
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            position: relative;
            padding: 25px;
            width: 90%;
            max-width: 480px;
            background: var(--card-bg-light);
            border-radius: 15px;
            box-shadow: var(--shadow);
            animation: slideInModal 0.5s ease-out;
        }
        .modal-content h2 {
            font-size: 1.8rem;
            color: var(--primary);
            margin-bottom: 15px;
            text-align: center;
        }
        .modal-content label {
            font-weight: 600;
            color: var(--primary);
            display: block;
            margin-bottom: 6px;
        }
        .modal-content input {
            border: 2px solid var(--secondary);
            border-radius: 8px;
            padding: 10px;
            width: 100%;
            transition: border-color 0.3s, box-shadow 0.3s;
            font-size: 0.95rem;
        }
        .modal-content input:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 5px rgba(30, 58, 138, 0.5);
        }
        .modal-buttons {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            margin-top: 15px;
        }
        .btn-custom {
            background: var(--secondary);
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 0.95rem;
            transition: background 0.3s, transform 0.3s, box-shadow 0.3s;
            cursor: pointer;
        }
        .btn-custom:hover {
            background: var(--primary);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px rgba(30, 58, 138, 0.4);
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes slideInModal {
            from { transform: translateY(-100px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        @keyframes rotateGlow {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
        @media (max-width: 768px) {
            .container {
                width: 95%;
                padding: 20px 15px;
                margin-top: -20px;
            }
            .hero h1 {
                font-size: 2.5rem;
            }
            .hero p {
                font-size: 1.2rem;
            }
            h2 {
                font-size: 1.8rem;
            }
            table {
                font-size: 0.9rem;
            }
            td img {
                width: 40px;
            }
            .modal-content {
                width: 95%;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <!-- Hero Section with Personalized Greeting -->
    <section class="hero">
        <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
        <p>Create a personalized photoshoot tailored to your vision.</p>
    </section>

    <!-- Main Content -->
    <div class="container">
        <div class="form-section">
            <h2>Request a Custom Shoot</h2>
            <form method="POST" enctype="multipart/form-data">
                <label for="name">Your Name</label>
                <input type="text" name="name" id="name" placeholder="Enter your name" value="<?php echo htmlspecialchars($_SESSION['username']); ?>" required>

                <label for="email">Your Email</label>
                <input type="email" name="email" id="email" placeholder="Enter your email" value="<?php echo htmlspecialchars($email); ?>" required>

                <label for="phone">Your Phone</label>
                <input type="text" name="phone" id="phone" placeholder="Enter your phone number" required>

                <label for="shoot_type">Shoot Type</label>
                <select name="shoot_type" id="shoot_type">
                    <option value="Wedding">Wedding</option>
                    <option value="Family Tour">Family Tour</option>
                    <option value="Event">Event</option>
                    <option value="Birthday">Birthday</option>
                    <option value="Others">Others</option>
                </select>

                <label for="location">Shoot Location</label>
                <input type="text" name="location" id="location" placeholder="Enter shoot location" required>

                <label for="date_time">Date & Time</label>
                <input type="datetime-local" name="date_time" id="date_time" required>

                <label for="additional_requests">Additional Requests</label>
                <textarea name="additional_requests" id="additional_requests" placeholder="Any special requests"></textarea>

                <label for="reference_images">Reference Images</label>
                <input type="file" name="reference_images[]" id="reference_images" multiple>

                <button type="submit">Submit Request</button>
            </form>
        </div>

        <div class="requests-section">
            <h2>Your Requests</h2>
            <?php if ($user_requests && $user_requests->num_rows > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Shoot Type</th>
                            <th>Status</th>
                            <th>Images</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $user_requests->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['shoot_type']); ?></td>
                                <td><?php echo htmlspecialchars($row['status']); ?></td>
                                <td>
                                    <?php 
                                    $images = json_decode($row['reference_images'], true);
                                    if ($images) {
                                        foreach ($images as $img) {
                                            echo "<img src='uploads/" . htmlspecialchars($img) . "' alt='Reference Image'>";
                                        }
                                    } else {
                                        echo "No images";
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="text-center text-gray">No requests found.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Booking Modal -->
    <div class="modal" id="book-modal">
        <div class="modal-content">
            <form method="post" action="">
                <input type="hidden" name="place_id" id="place_id">
                <h2>Confirm Booking</h2>
                <div class="mb-4">
                    <label for="user_email" class="block text-primary font-bold mb-2">Your Email</label>
                    <input type="email" id="user_email" name="user_email" value="<?php echo htmlspecialchars($email); ?>" required class="w-full px-3 py-2 border rounded">
                </div>
                <div class="modal-buttons">
                    <button type="submit" name="book" class="btn-custom">Confirm</button>
                    <button type="button" class="btn-custom bg-accent hover:bg-red-600 close-modal">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Modal Handling (if needed, though not used here)
        document.addEventListener('DOMContentLoaded', () => {
            const bookButtons = document.querySelectorAll('.book-button');
            const bookModal = document.getElementById('book-modal');
            const placeIdInput = document.getElementById('place_id');
            const closeModalButtons = document.querySelectorAll('.close-modal');

            bookButtons.forEach(button => {
                button.addEventListener('click', () => {
                    const placeId = button.getAttribute('data-place_id');
                    placeIdInput.value = placeId;
                    bookModal.classList.add('show');
                });
            });

            closeModalButtons.forEach(button => {
                button.addEventListener('click', () => {
                    bookModal.classList.remove('show');
                });
            });

            bookModal.addEventListener('click', e => {
                if (e.target === bookModal) {
                    bookModal.classList.remove('show');
                }
            });
        });
    </script>
</body>
</html>