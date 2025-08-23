<?php
require_once 'db_connection.php';

$category_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch category name (optional)
$category = $conn->query("SELECT name FROM categories WHERE id = $category_id")->fetch_assoc();

// Fetch posts or projects related to this category
$posts = $conn->query("SELECT * FROM posts WHERE category_id = $category_id");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?php echo $category['name']; ?> - Posts</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <h2 class="mb-4 text-center">Posts in <?php echo $category['category_name']; ?></h2>
    <div class="row">
        <?php while ($row = $posts->fetch_assoc()): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo $row['title']; ?></h5>
                        <p class="card-text"><?php echo substr($row['description'], 0, 100); ?>...</p>
                        <a href="post_detail.php?id=<?php echo $row['post_id']; ?>" class="btn btn-outline-primary">Read More</a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>
</body>
</html>
