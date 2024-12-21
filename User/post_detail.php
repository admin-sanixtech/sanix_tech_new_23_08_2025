<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: https://sanixtech.in");
    exit;
  }


  
include 'db_connection.php';

// Get the post ID from the URL
if (isset($_GET['id'])) {
    $post_id = $_GET['id'];

    // Fetch the full post details
    $sql = "SELECT title, description, createdat 
            FROM posts 
            WHERE post_id = ? AND status = 'approved'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $post_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $post = $result->fetch_assoc();
    } else {
        die("Post not found or not approved.");
    }
} else {
    die("Invalid post ID.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1><?php echo htmlspecialchars($post['title']); ?></h1>
    <p><?php echo nl2br(htmlspecialchars($post['description'])); ?></p>
    <small>Posted on: <?php echo htmlspecialchars($post['createdat']); ?></small>
</div>
</body>
</html>
