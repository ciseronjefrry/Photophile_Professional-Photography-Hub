<?php
session_start();
include 'db_connect.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get the folder name from the URL
if (!isset($_GET['folder'])) {
    echo "Invalid request.";
    exit();
}

$folderName = basename($_GET['folder']); // Ensure security by sanitizing input
$folderPath = "uploads/" . $folderName;

if (!is_dir($folderPath)) {
    echo "Folder not found.";
    exit();
}

// Fetch description from database
$stmt = $conn->prepare("SELECT description FROM uploaded_folders WHERE folder_name = ?");
$stmt->bind_param("s", $folderName);
$stmt->execute();
$result = $stmt->get_result();
$folderData = $result->fetch_assoc();
$description = $folderData ? htmlspecialchars($folderData['description']) : "No description available.";

// Get list of files
$files = array_diff(scandir($folderPath), array('.', '..'));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View & Download - <?php echo htmlspecialchars($folderName); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body>

<div class="container mt-4">
    <h2>Folder: <?php echo htmlspecialchars($folderName); ?></h2>
    <p><strong>Description:</strong> <?php echo $description; ?></p>

    <?php if (empty($files)) { ?>
        <p>No files available in this folder.</p>
    <?php } else { ?>
        <ul class="list-group">
            <?php foreach ($files as $file) { ?>
                <li class="list-group-item">
                    <?php echo htmlspecialchars($file); ?>
                    <a href="<?php echo htmlspecialchars($folderPath . '/' . $file); ?>" download class="btn btn-success btn-sm float-end">Download</a>
                </li>
            <?php } ?>
        </ul>
    <?php } ?>

    <a href="user_order.php" class="btn btn-secondary mt-3">Back</a>
</div>

</body>
</html>
