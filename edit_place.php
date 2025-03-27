<?php
include 'db_connect.php';
include 'header.php';

// Check if place_id is provided in the URL
if (!isset($_GET['place_id']) || empty($_GET['place_id'])) {
    die("Invalid Place ID");
}

$place_id = $_GET['place_id'];

// Fetch place details
$place_sql = "SELECT * FROM places WHERE place_id = ?";
$stmt = $conn->prepare($place_sql);
$stmt->bind_param("i", $place_id);
$stmt->execute();
$place = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['edit_place'])) {
    $place_name = $_POST['place_name'];
    $description = $_POST['description'];
    $price = $_POST['price'];
    $image = $place['image']; // Keep existing image if no new image is uploaded

    // Handle image upload
    if (!empty($_FILES['image']['name'])) {
        $image = basename($_FILES['image']['name']);
        $image_tmp = $_FILES['image']['tmp_name'];
        move_uploaded_file($image_tmp, "images/$image");
    }

    // Update query
    $update_sql = "UPDATE places SET place_name=?, description=?, price=?, image=? WHERE place_id=?";
    $stmt = $conn->prepare($update_sql);
    if ($stmt) {
        $stmt->bind_param("ssdsi", $place_name, $description, $price, $image, $place_id);
        if ($stmt->execute()) {
            header("Location: admin_book.php");
            exit;
        } else {
            echo "<p class='text-red-500 text-center'>Error updating place: " . $stmt->error . "</p>";
        }
        $stmt->close();
    } else {
        echo "<p class='text-red-500 text-center'>Prepare statement failed: " . $conn->error . "</p>";
    }
}

?>

<div class='container mx-auto p-4'>
    <h1 class='text-3xl font-bold mb-4 text-center'>Edit Photoshoot Place</h1>
    <form method='post' action='' class='max-w-md mx-auto bg-white p-6 rounded shadow' enctype='multipart/form-data'>
        <div class='mb-4'>
            <label for='place_name' class='block text-gray-700 font-bold mb-2'>Place Name</label>
            <input type='text' id='place_name' name='place_name' value='<?= htmlspecialchars($place['place_name']) ?>' required class='w-full px-3 py-2 border rounded'>
        </div>
        <div class='mb-4'>
            <label for='description' class='block text-gray-700 font-bold mb-2'>Description</label>
            <textarea id='description' name='description' required class='w-full px-3 py-2 border rounded'><?= htmlspecialchars($place['description']) ?></textarea>
        </div>
        <div class='mb-4'>
            <label for='price' class='block text-gray-700 font-bold mb-2'>Price ($)</label>
            <input type='number' id='price' name='price' value='<?= htmlspecialchars($place['price']) ?>' step='0.01' required class='w-full px-3 py-2 border rounded'>
        </div>
        <div class='mb-4'>
            <label for='image' class='block text-gray-700 font-bold mb-2'>Upload New Image (optional)</label>
            <input type='file' id='image' name='image' class='w-full px-3 py-2 border rounded'>
            <p class='text-gray-600 text-sm mt-2'>Current Image: <strong><?= htmlspecialchars($place['image']) ?></strong></p>
            <img src="images/<?= htmlspecialchars($place['image']) ?>" alt="Current Image" class="w-32 h-32 mt-2 object-cover">
        </div>
        <button type='submit' name='edit_place' class='bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600 transition duration-300'>Update Place</button>
    </form>
</div>

<?php include 'footer.php'; ?>
