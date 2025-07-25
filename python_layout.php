<?php
// layout.php
session_start();
include 'db_connection.php';
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Sanix - Subject Layout</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/user_styles.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        body { font-family: Arial, sans-serif; }
        .sidebar { min-height: 100vh; background-color: #f8f9fa; padding: 20px; }
        .sidebar a { display: block; padding: 10px 0; text-decoration: none; color: #333; }
        .sidebar a:hover { color: #007bff; }
        .content-area { padding: 20px; }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <?php include 'navbar.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 sidebar">
                <?php
                if (isset($_GET['category_id'])) {
                    $category_id = intval($_GET['category_id']);
                    $sql = "SELECT s.subcategory_id, s.name FROM subcategories s WHERE s.category_id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $category_id);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    echo '<h5>Topics</h5>';
                    while ($row = $result->fetch_assoc()) {
                        echo '<a href="?category_id=' . $category_id . '&subcategory_id=' . $row['subcategory_id'] . '">' . htmlspecialchars($row['name']) . '</a>';
                    }
                } else {
                    echo '<p>Select a category from the top navigation bar.</p>';
                }
                ?>
            </div>

            <!-- Content Area -->
            <div class="col-md-9 content-area">
                <?php
                if (isset($_GET['subcategory_id'])) {
                    $subcategory_id = intval($_GET['subcategory_id']);
                    $sql = "SELECT title, description FROM posts WHERE subcategory_id = ? AND status = 'approved' ORDER BY createdat DESC";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $subcategory_id);
                    $stmt->execute();
                    $posts = $stmt->get_result();

                    if ($posts->num_rows > 0) {
                        while ($post = $posts->fetch_assoc()) {
                            echo '<div class="mb-4">';
                            echo '<h4>' . htmlspecialchars($post['title']) . '</h4>';
                            echo '<p>' . nl2br(htmlspecialchars($post['description'])) . '</p>';
                            echo '</div>';
                        }
                    } else {
                        echo '<p>No posts found in this subtopic.</p>';
                    }
                } else {
                    echo '<p>Select a topic from the left to view posts.</p>';
                }
                ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
