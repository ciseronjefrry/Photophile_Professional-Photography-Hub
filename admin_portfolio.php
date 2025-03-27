<?php
include 'db_connect.php'; // Database connection
include 'header.php'; // Admin panel header

// Fetch photographer details
$result = $conn->query("SELECT * FROM photographers LIMIT 1");
$photographer = $result->fetch_assoc();

// If no photographer data exists, set default values
if (!$photographer) {
    $photographer = [
        'name' => '',
        'experience' => '',
        'specialization' => '',
        'bio' => '',
        'profile_image' => 'default.jpg'
    ];
}

// Handle Profile Update
if (isset($_POST['update_profile'])) {
    $name = $_POST['name'];
    $experience = $_POST['experience'];
    $specialization = $_POST['specialization'];
    $bio = $_POST['bio'];
    $profile_image = $photographer['profile_image'];

    if (!empty($_FILES['profile_image']['name'])) {
        $profile_image = time() . "_" . $_FILES['profile_image']['name'];
        move_uploaded_file($_FILES['profile_image']['tmp_name'], "images/$profile_image");
    }

    if ($result->num_rows > 0) {
        $conn->query("UPDATE photographers SET name='$name', experience='$experience', specialization='$specialization', bio='$bio', profile_image='$profile_image'");
    } else {
        $conn->query("INSERT INTO photographers (name, experience, specialization, bio, profile_image) VALUES ('$name', '$experience', '$specialization', '$bio', '$profile_image')");
    }

    header("Location: admin_portfolio.php"); // Refresh page
}

// Handle Portfolio Upload
if (isset($_POST['add_portfolio'])) {
    $category = $_POST['category'];
    $image = time() . "_" . $_FILES['image']['name'];
    move_uploaded_file($_FILES['image']['tmp_name'], "images/$image");

    $conn->query("INSERT INTO portfolio (category, image) VALUES ('$category', '$image')");
    header("Location: admin_portfolio.php");
}

// Handle Portfolio Deletion
if (isset($_GET['delete_portfolio'])) {
    $id = $_GET['delete_portfolio'];
    $conn->query("DELETE FROM portfolio WHERE id=$id");
    header("Location: admin_portfolio.php");
}

// Handle Testimonials
if (isset($_POST['add_testimonial'])) {
    $client_name = $_POST['client_name'];
    $review = $_POST['review'];
    $rating = $_POST['rating'];

    $conn->query("INSERT INTO testimonials (client_name, review, rating) VALUES ('$client_name', '$review', '$rating')");
    header("Location: admin_portfolio.php");
}

// Handle Testimonial Deletion
if (isset($_GET['delete_testimonial'])) {
    $id = $_GET['delete_testimonial'];
    $conn->query("DELETE FROM testimonials WHERE id=$id");
    header("Location: admin_portfolio.php");
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Photographer Portfolio</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .container { max-width: 800px; margin-top: 20px; }
        .card { margin-top: 20px; padding: 20px; border-radius: 10px; box-shadow: 2px 2px 10px rgba(0,0,0,0.1); }
        .profile-img { width: 150px; height: 150px; border-radius: 50%; object-fit: cover; }
        .gallery-img { width: 100%; height: 200px; object-fit: cover; }
    </style>
</head>
<body>
<div class="container text-center">
    <h2>Welcome to My Photography Portfolio</h2>
    <p>Explore the beauty of moments captured through my lens. From stunning landscapes to heartfelt portraits, every photo tells a story.</p>

    <h3>My Specializations</h3>
    <ul style="list-style: none; padding: 0;">
        <li> Wedding & Event Photography</li>
        <li> Landscape & Nature Photography</li>
        <li> Portrait & Fashion Photography</li>
        <li> Product & Commercial Shoots</li>
    </ul>

    <h3>Why Choose Me?</h3>
    <p>With a passion for storytelling, I bring creativity and professionalism to every photoshoot. My goal is to create timeless memories that you will cherish forever.</p>

    <h3>Let's Work Together!</h3>
    <p>Looking for a professional photographer? <a href="contact.php" style="color: #1e3a8a; text-decoration: underline;">Contact me</a> to discuss your project!</p>
</div>

<div class="container">
    <h1 class="text-center">Manage Photographer Portfolio</h1>

    <!-- Photographer Profile Form -->
    <div class="card">
        <h2>Photographer Details</h2>
        <form method="post" enctype="multipart/form-data">
            <div class="text-center">
                <img src="images/<?= $photographer['profile_image'] ?>" class="profile-img" id="profilePreview">
            </div>
            <input type="file" name="profile_image" class="form-control mt-2" accept="image/*" onchange="previewProfile(event)">
            <input type="text" name="name" value="<?= htmlspecialchars($photographer['name']) ?>" class="form-control mt-2" placeholder="Photographer Name" required>
            <input type="text" name="experience" value="<?= htmlspecialchars($photographer['experience']) ?>" class="form-control mt-2" placeholder="Experience" required>
            <input type="text" name="specialization" value="<?= htmlspecialchars($photographer['specialization']) ?>" class="form-control mt-2" placeholder="Specialization" required>
            <textarea name="bio" class="form-control mt-2" placeholder="Short Bio" required><?= htmlspecialchars($photographer['bio']) ?></textarea>
            <button type="submit" name="update_profile" class="btn btn-primary mt-3">Update Profile</button>
        </form>
    </div>

    <!-- Portfolio Management -->
    <div class="card">
        <h2>Manage Portfolio</h2>
        <form method="post" enctype="multipart/form-data">
            <input type="text" name="category" class="form-control mt-2" placeholder="Category (Wedding, Portrait, etc.)" required>
            <input type="file" name="image" class="form-control mt-2" accept="image/*" required>
            <button type="submit" name="add_portfolio" class="btn btn-success mt-3">Add Portfolio</button>
        </form>
        
        <div class="row mt-3">
            <?php
            $portfolios = $conn->query("SELECT * FROM portfolio");
            while ($row = $portfolios->fetch_assoc()) {
                echo "<div class='col-md-4 mt-2'>
                        <img src='images/{$row['image']}' class='gallery-img'>
                        <p>{$row['category']} <a href='?delete_portfolio={$row['id']}' class='btn btn-danger btn-sm'>Delete</a></p>
                      </div>";
            }
            ?>
        </div>
    </div>

    <!-- Testimonials -->
    <div class="card">
        <h2>Client Testimonials</h2>
        <form method="post">
            <input type="text" name="client_name" class="form-control mt-2" placeholder="Client Name" required>
            <textarea name="review" class="form-control mt-2" placeholder="Review" required></textarea>
            <input type="number" name="rating" class="form-control mt-2" min="1" max="5" placeholder="Rating (1-5)" required>
            <button type="submit" name="add_testimonial" class="btn btn-warning mt-3">Add Testimonial</button>
        </form>

        <ul class="list-group mt-3">
            <?php
            $testimonials = $conn->query("SELECT * FROM testimonials");
            while ($row = $testimonials->fetch_assoc()) {
                echo "<li class='list-group-item'>{$row['client_name']} - {$row['review']} (Rating: {$row['rating']}/5) <a href='?delete_testimonial={$row['id']}' class='btn btn-sm btn-danger float-end'>Delete</a></li>";
            }
            ?>
        </ul>
    </div>
</div>

<script>
    function previewProfile(event) {
        document.getElementById('profilePreview').src = URL.createObjectURL(event.target.files[0]);
    }
</script>

</body>
</html>
