<?php
session_start();
require_once 'confi.php';

if(isset($_POST['book_slot'])) {
    $slot_id = $_POST['slot_id'];
    $client_name = $_POST['client_name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $event_type = $_POST['event_type'];
    $special_requests = $_POST['special_requests'];

    // Begin transaction
    $conn->beginTransaction();
    
    try {
        // Update availability status
        $stmt = $conn->prepare("UPDATE availability SET status = 'booked' WHERE slot_id = ?");
        $stmt->execute([$slot_id]);

        // Create appointment
        $stmt = $conn->prepare("INSERT INTO photography_appointments (slot_id, client_name, email, phone, event_type, special_requests) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$slot_id, $client_name, $email, $phone, $event_type, $special_requests]);

        $conn->commit();
        $_SESSION['success'] = "Booking confirmed! We will contact you shortly.";
    } catch(Exception $e) {
        $conn->rollBack();
        $_SESSION['error'] = "Booking failed. Please try again.";
    }
}

// Fetch available slots
$query = "SELECT * FROM availability WHERE status = 'available' AND date_time > NOW() ORDER BY date_time";
$available_slots = $conn->query($query)->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Book Your Photography Session</title>
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
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .card-body {
            text-align: center;
            padding: 25px;
        }

        .card-title {
            color: var(--primary-dark);
            font-size: 1.25rem;
            font-weight: 600;
        }

        .btn-primary {
            background-color: var(--primary-light);
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: var(--primary-dark);
        }

        .modal-content {
            border-radius: 12px;
            border: none;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.2);
        }

        .modal-header {
            background-color: var(--primary-dark);
            color: white;
            border-bottom: none;
            border-radius: 12px 12px 0 0;
        }

        .modal-title {
            font-weight: 600;
        }

        .close {
            color: white;
            opacity: 0.8;
        }

        .close:hover {
            opacity: 1;
        }

        .form-group label {
            color: var(--primary-dark);
            font-weight: 500;
        }

        .form-control {
            border-radius: 8px;
            border: 1px solid #ddd;
            padding: 12px;
            transition: border-color 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--primary-light);
            box-shadow: 0 0 5px rgba(59, 130, 246, 0.5);
        }

        .btn-secondary {
            background-color: #6b7280;
            border: none;
            border-radius: 8px;
        }

        .btn-secondary:hover {
            background-color: #4b5563;
        }

        .alert-success {
            background-color: #d1fae5;
            color: #065f46;
            border: none;
            border-radius: 8px;
        }

        .alert-danger {
            background-color: #fee2e2;
            color: var(--accent);
            border: none;
            border-radius: 8px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Book Your Photography Session</h2>

        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <div class="row">
            <?php foreach($available_slots as $slot): ?>
            <div class="col-md-4 mb-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title"><?= date('F j, Y - H:i', strtotime($slot['date_time'])) ?></h5>
                        <button class="btn btn-primary" data-toggle="modal" data-target="#bookingModal<?= $slot['slot_id'] ?>">Book Now</button>
                    </div>
                </div>

                <!-- Booking Modal -->
                <div class="modal fade" id="bookingModal<?= $slot['slot_id'] ?>" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">Schedule Your Session</h5>
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <form method="POST">
                                <div class="modal-body">
                                    <input type="hidden" name="slot_id" value="<?= $slot['slot_id'] ?>">
                                    
                                    <div class="form-group">
                                        <label for="client_name">Full Name</label>
                                        <input type="text" name="client_name" id="client_name" class="form-control" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="email">Email Address</label>
                                        <input type="email" name="email" id="email" class="form-control" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="phone">Phone Number</label>
                                        <input type="tel" name="phone" id="phone" class="form-control" required>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="event_type">Event Type</label>
                                        <select name="event_type" id="event_type" class="form-control" required>
                                            <option value="Wedding">Wedding</option>
                                            <option value="Portrait">Portrait</option>
                                            <option value="Family">Family</option>
                                            <option value="Commercial">Commercial</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group">
                                        <label for="special_requests">Special Requests</label>
                                        <textarea name="special_requests" id="special_requests" class="form-control" rows="3" placeholder="Any specific requirements?"></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                    <button type="submit" name="book_slot" class="btn btn-primary">Confirm Booking</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>