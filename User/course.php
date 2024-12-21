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

// Get category_id and validate it
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
if ($category_id <= 0) {
    die("Invalid category.");
}

// Fetch category details
$sql_category = "SELECT category_name FROM categories WHERE category_id = ?";
$stmt_category = $conn->prepare($sql_category);
$stmt_category->bind_param("i", $category_id);
$stmt_category->execute();
$result_category = $stmt_category->get_result();

if ($result_category->num_rows === 0) {
    die("Category not found.");
}

$category = $result_category->fetch_assoc();
$course_title = $category['category_name'];

// Fetch posts for the category with status = 'approved'
$sql = "SELECT post_id, title, LEFT(description, 300) AS short_description, createdat 
        FROM posts 
        WHERE category_id = ? AND status = 'approved'
        ORDER BY createdat DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $category_id);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($course_title); ?></title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css">
    <style>
        .post-card {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <h2><?php echo htmlspecialchars($course_title); ?> Posts</h2>
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
