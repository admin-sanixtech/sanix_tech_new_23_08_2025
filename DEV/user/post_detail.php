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

    // Fetch the full post details and username
    $sql = "SELECT posts.title, posts.description, posts.createdat, users.name 
            FROM posts 
            INNER JOIN users ON posts.user_id = users.user_id 
            WHERE posts.post_id = ? AND posts.status = 'approved'";
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
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title><?php echo htmlspecialchars($post['title']); ?></title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" />
    <style>
        .question-container { display: flex; }
        .question-box { flex: 3; padding: 15px; }
        .answer-box { margin-top: 10px; background-color: #f9f9f9; padding: 15px; border-radius: 5px; }
        .question-nav { flex: 1; padding: 15px; }
        .question-nav button { display: block; margin: 5px 0; }
        .actions { margin-top: 20px; }
    </style>
    <link rel="stylesheet" href="css/user_styleone.css" />
</head>
<body>
    <div class="wrapper">
      <aside id="sidebar" class="js-sidebar">
        <?php include 'user_menu.php'; ?>
      </aside>
      <div class="main">
        <?php include 'user_navbar.php'; ?>
        <main class="content px-3 py-2">
          <div class="container-fluid">
            <div class="mb-3">
<div class="container mt-5">
    <h1><?php echo htmlspecialchars($post['title']); ?></h1>
    <p><?php echo nl2br(htmlspecialchars($post['description'])); ?></p>
    <small>
        Posted on: <?php echo htmlspecialchars($post['createdat']); ?> 
        by: <?php echo htmlspecialchars($post['name']); ?>
    </small>
</div>
</body>
</html>
