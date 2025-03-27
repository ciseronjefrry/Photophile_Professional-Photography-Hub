<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include 'database.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['id'], $_POST['email'], $_POST['reply'])) {
        $id = intval($_POST['id']);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $reply = trim($_POST['reply']);

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            die("Invalid email format.");
        }

        $stmt = $conn->prepare("UPDATE contact_messages SET reply = ? WHERE id = ?");
        $stmt->bind_param("si", $reply, $id);

        if ($stmt->execute()) {
            $subject = "Reply to your query";
            $headers = "From: admin@yourwebsite.com\r\n";
            $headers .= "Reply-To: admin@yourwebsite.com\r\n";
            $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";

            if (mail($email, $subject, $reply, $headers)) {
                echo "Reply sent successfully!";
            } else {
                echo "Failed to send email.";
            }
        } else {
            echo "Error: " . $stmt->error;
        }
        
        $stmt->close();
        $conn->close();

        header("Location: replies.php");
        exit();
    }
}
?>
