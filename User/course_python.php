<?php
// No session check here: this page is public
session_start();

include 'db_connection.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Define the category ID for Python
$category_id = 1; // Update if your Python category ID is different

// Fetch posts for the Python category with status = 'approved'
$sql = "SELECT post_id, title, LEFT(description, 300) AS short_description, createdat 
        FROM posts 
        WHERE category_id = ? AND status = 'approved'
        ORDER BY createdat DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $category_id);
$stmt->execute();
$result = $stmt->get_result();

// Store all posts into an array
$posts = [];
while ($row = $result->fetch_assoc()) {
    $posts[] = $row;
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Python Posts</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" />
    <script src="https://kit.fontawesome.com/ae360af17e.js" crossorigin="anonymous"></script>
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
                <div class="card border-0">
                    <div class="card-body">
                        <div class="container mt-5">
                            <h2>Python Posts</h2>
                            <p><strong>Total posts found:</strong> <?php echo count($posts); ?></p>

                            <div class="row">
                                <?php if (count($posts) > 0): ?>
                                    <?php foreach ($posts as $post): ?>
                                        <div class="col-md-12 mb-4">
                                            <div class="card post-card h-100">
                                                <div class="card-body">
                                                    <h5 class="card-title">
                                                        <?php echo htmlspecialchars($post['title']); ?>
                                                    </h5>
                                                    <p class="card-text">
                                                        <?php echo nl2br(htmlspecialchars($post['short_description'])); ?>...
                                                    </p>
                                                    <a href="post_detail.php?id=<?php echo $post['post_id']; ?>" class="btn btn-primary">
                                                        Read More
                                                    </a>
                                                </div>
                                                <div class="card-footer text-muted">
                                                    Posted on: <?php echo htmlspecialchars($post['createdat']); ?>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="col-12">
                                        <p>No posts found for this category.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
