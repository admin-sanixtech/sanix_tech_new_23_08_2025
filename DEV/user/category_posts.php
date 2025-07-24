<?php
include 'db_connection.php'; // Include your database connection file
$category_id = $_GET['category_id']; // Get the category_id from the URL

$limit = 10; // Number of posts per page
$page = isset($_GET['page']) ? $_GET['page'] : 1; // Get the current page number
$offset = ($page - 1) * $limit; // Calculate the offset for SQL

// Fetch posts for the current page
$sql_posts = "SELECT * FROM posts WHERE category_id = ? LIMIT ?, ?";
$stmt = $conn->prepare($sql_posts);
$stmt->bind_param("iii", $category_id, $offset, $limit);
$stmt->execute();
$result = $stmt->get_result();

// Count total posts for pagination
$sql_count = "SELECT COUNT(*) as total FROM posts WHERE category_id = ?";
$stmt_count = $conn->prepare($sql_count);
$stmt_count->bind_param("i", $category_id);
$stmt_count->execute();
$count_result = $stmt_count->get_result()->fetch_assoc();
$total_posts = $count_result['total'];
$total_pages = ceil($total_posts / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category Posts</title>
    <link rel="stylesheet" href="path-to-your-css.css"> <!-- Add your CSS file path -->
</head>
<body>
    <?php include 'header.php'; ?> <!-- Include navigation/header -->

    <div class="container">
        <h1>Posts in Category: <?php echo htmlspecialchars($category_id); ?></h1>
        <div class="posts">
            <?php while ($post = $result->fetch_assoc()): ?>
                <div class="post">
                    <h2><?php echo htmlspecialchars($post['title']); ?></h2>
                    <p><?php echo htmlspecialchars($post['description']); ?></p>
                </div>
            <?php endwhile; ?>
        </div>

        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="category_posts.php?category_id=<?php echo $category_id; ?>&page=<?php echo $i; ?>">
                    <?php echo $i; ?>
                </a>
            <?php endfor; ?>
        </div>
    </div>

    <?php include 'footer.php'; ?> <!-- Include footer -->
</body>
</html>
