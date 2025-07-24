<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: https://sanixtech.in");
    exit;
  }
include 'db_connection.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Define the category ID for Python
$category_id = 1; // Replace with the actual ID of the Python category in your database

// Fetch posts for the Python category with status = 'approved'
$sql = "SELECT post_id, title, LEFT(description, 300) AS short_description, createdat 
        FROM posts 
        WHERE category_id = ? AND status = 'approved'
        ORDER BY createdat DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $category_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result) {
    die("Error fetching posts: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Python Course</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css">
    <style>
        .post-card {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2>Python Posts</h2>
    <div class="row">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($post = $result->fetch_assoc()): ?>
                <div class="col-12">
                    <div class="card post-card">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($post['title']); ?></h5>
                            <p class="card-text">
                                <?php echo nl2br(htmlspecialchars($post['short_description'])); ?>...
                            </p>
                            <a href="post_detail.php?id=<?php echo $post['post_id']; ?>" class="btn btn-primary">Read More</a>
                        </div>
                        <div class="card-footer">
                            <small>Posted on: <?php echo htmlspecialchars($post['createdat']); ?></small>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>No posts found for this category.</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>
