<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'db_connection.php';

// Get post ID from URL
$post_id = isset($_GET['post_id']) ? intval($_GET['post_id']) : 0;

if ($post_id === 0) {
    die("Invalid post ID");
}

// Fetch post details with category and subcategory info
$post_query = "SELECT p.*, c.category_name, s.subcategory_name 
               FROM posts p 
               LEFT JOIN categories c ON p.category_id = c.category_id 
               LEFT JOIN subcategories s ON p.subcategory_id = s.subcategory_id 
               WHERE p.post_id = ?";

$stmt = $conn->prepare($post_query);
$stmt->bind_param("i", $post_id);
$stmt->execute();
$result = $stmt->get_result();
$post = $result->fetch_assoc();

if (!$post) {
    die("Post not found");
}

// Update view count
$update_views = "UPDATE posts SET views = views + 1 WHERE post_id = ?";
$stmt = $conn->prepare($update_views);
$stmt->bind_param("i", $post_id);
$stmt->execute();

// Fetch related posts from same subcategory
$related_query = "SELECT post_id, title, description, created_at, views 
                  FROM posts 
                  WHERE subcategory_id = ? AND post_id != ? 
                  ORDER BY created_at DESC 
                  LIMIT 5";
$stmt = $conn->prepare($related_query);
$stmt->bind_param("ii", $post['subcategory_id'], $post_id);
$stmt->execute();
$related_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($post['title']); ?> - Sanix Technologies</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/user_styles.css?v=<?php echo time(); ?>">
    
    <!-- Meta tags for SEO -->
    <?php if (!empty($post['meta_title'])): ?>
        <meta name="title" content="<?php echo htmlspecialchars($post['meta_title']); ?>">
    <?php endif; ?>
    
    <?php if (!empty($post['meta_description'])): ?>
        <meta name="description" content="<?php echo htmlspecialchars($post['meta_description']); ?>">
    <?php endif; ?>
    
    <?php if (!empty($post['seo_keywords'])): ?>
        <meta name="keywords" content="<?php echo htmlspecialchars($post['seo_keywords']); ?>">
    <?php endif; ?>
    
    <style>
        .breadcrumb-item a {
            text-decoration: none;
            color: #007bff;
        }
        .post-header {
            border-bottom: 2px solid #dee2e6;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
        }
        .post-meta {
            font-size: 0.9rem;
            color: #6c757d;
        }
        .post-content {
            line-height: 1.8;
            font-size: 1.1rem;
        }
        .post-content img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin: 1rem 0;
        }
        .related-posts {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 1.5rem;
        }
        .related-post-item {
            border-bottom: 1px solid #dee2e6;
            padding: 1rem 0;
        }
        .related-post-item:last-child {
            border-bottom: none;
        }
        .back-button {
            margin-bottom: 1rem;
        }
        .featured-image {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            margin-bottom: 1.5rem;
        }
    </style>
</head>
<body>
    <?php include('header.php'); ?>
    <?php include('navbar.php'); ?>

    <div class="container my-5">
        <!-- Back Button -->
        <div class="back-button">
            <a href="course_detail.php?category_id=<?php echo $post['category_id']; ?>&subcategory_id=<?php echo $post['subcategory_id']; ?>" 
               class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i>Back to <?php echo htmlspecialchars($post['subcategory_name']); ?>
            </a>
        </div>

        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <!-- Breadcrumb -->
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="category_cards_show.php">
                                <i class="fas fa-home me-1"></i>Courses
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="course_detail.php?category_id=<?php echo $post['category_id']; ?>">
                                <?php echo htmlspecialchars($post['category_name']); ?>
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="course_detail.php?category_id=<?php echo $post['category_id']; ?>&subcategory_id=<?php echo $post['subcategory_id']; ?>">
                                <?php echo htmlspecialchars($post['subcategory_name']); ?>
                            </a>
                        </li>
                        <li class="breadcrumb-item active" aria-current="page">
                            <?php echo htmlspecialchars(substr($post['title'], 0, 30)) . (strlen($post['title']) > 30 ? '...' : ''); ?>
                        </li>
                    </ol>
                </nav>

                <!-- Post Header -->
                <div class="post-header">
                    <h1 class="mb-3"><?php echo htmlspecialchars($post['title']); ?></h1>
                    
                    <div class="post-meta mb-3">
                        <span class="me-3">
                            <i class="fas fa-calendar me-1"></i>
                            <?php echo date('F d, Y', strtotime($post['created_at'])); ?>
                        </span>
                        
                        <?php if ($post['reading_time'] > 0): ?>
                            <span class="me-3">
                                <i class="fas fa-clock me-1"></i>
                                <?php echo $post['reading_time']; ?> min read
                            </span>
                        <?php endif; ?>
                        
                        <span class="me-3">
                            <i class="fas fa-eye me-1"></i>
                            <?php echo $post['views']; ?> views
                        </span>
                        
                        <?php if ($post['likes'] > 0): ?>
                            <span>
                                <i class="fas fa-heart me-1"></i>
                                <?php echo $post['likes']; ?> likes
                            </span>
                        <?php endif; ?>
                    </div>

                    <div class="mb-3">
                        <span class="badge bg-primary me-2"><?php echo htmlspecialchars($post['category_name']); ?></span>
                        <span class="badge bg-secondary"><?php echo htmlspecialchars($post['subcategory_name']); ?></span>
                    </div>
                </div>

                <!-- Featured Image -->
                <?php if (!empty($post['featured_image'])): ?>
                    <img src="<?php echo htmlspecialchars($post['featured_image']); ?>" 
                         alt="<?php echo htmlspecialchars($post['title']); ?>" 
                         class="featured-image">
                <?php endif; ?>

                <!-- Post Description -->
                <?php if (!empty($post['description'])): ?>
                    <div class="alert alert-info">
                        <strong>Overview:</strong> <?php echo htmlspecialchars($post['description']); ?>
                    </div>
                <?php endif; ?>

                <!-- Post Content -->
                <div class="post-content">
                    <?php echo $post['content']; ?>
                </div>

                <!-- Post Actions -->
                <div class="mt-4 p-3 bg-light rounded">
                    <div class="row">
                        <div class="col-md-6">
                            <button class="btn btn-outline-primary me-2" onclick="likePost(<?php echo $post_id; ?>)">
                                <i class="fas fa-heart me-1"></i>Like
                            </button>
                            <button class="btn btn-outline-success me-2" onclick="sharePost()">
                                <i class="fas fa-share me-1"></i>Share
                            </button>
                        </div>
                        <div class="col-md-6 text-end">
                            <small class="text-muted">
                                Last updated: <?php echo date('F d, Y', strtotime($post['updated_at'])); ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Related Posts -->
                <div class="related-posts">
                    <h4 class="mb-3">
                        <i class="fas fa-list me-2"></i>Related Topics
                    </h4>
                    
                    <?php if ($related_result && $related_result->num_rows > 0): ?>
                        <?php while ($related = $related_result->fetch_assoc()): ?>
                            <div class="related-post-item">
                                <h6>
                                    <a href="post_detail.php?post_id=<?php echo $related['post_id']; ?>" 
                                       class="text-decoration-none">
                                        <?php echo htmlspecialchars($related['title']); ?>
                                    </a>
                                </h6>
                                <small class="text-muted">
                                    <i class="fas fa-calendar me-1"></i>
                                    <?php echo date('M d, Y', strtotime($related['created_at'])); ?>
                                    <i class="fas fa-eye ms-2 me-1"></i>
                                    <?php echo $related['views']; ?>
                                </small>
                                <p class="mt-1 mb-0">
                                    <?php 
                                    $desc = strip_tags($related['description']);
                                    echo htmlspecialchars(substr($desc, 0, 80));
                                    if (strlen($desc) > 80) echo '...';
                                    ?>
                                </p>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-muted">No related posts found.</p>
                    <?php endif; ?>
                    
                    <div class="mt-3">
                        <a href="course_detail.php?category_id=<?php echo $post['category_id']; ?>&subcategory_id=<?php echo $post['subcategory_id']; ?>" 
                           class="btn btn-sm btn-outline-primary w-100">
                            <i class="fas fa-arrow-left me-1"></i>
                            View All <?php echo htmlspecialchars($post['subcategory_name']); ?> Posts
                        </a>
                    </div>
                </div>

                <!-- Category Navigation -->
                <div class="mt-4 p-3 bg-info bg-opacity-10 rounded">
                    <h5 class="mb-3">
                        <i class="fas fa-navigation me-2"></i>Quick Navigation
                    </h5>
                    <div class="d-grid gap-2">
                        <a href="category_cards_show.php" 
                           class="btn btn-sm btn-outline-info">
                            <i class="fas fa-home me-1"></i>All Courses
                        </a>
                        <a href="course_detail.php?category_id=<?php echo $post['category_id']; ?>" 
                           class="btn btn-sm btn-outline-info">
                            <i class="fas fa-book me-1"></i>
                            <?php echo htmlspecialchars($post['category_name']); ?> Course
                        </a>
                    </div>
                </div>

                <!-- Course Progress (Optional) -->
                <div class="mt-4 p-3 bg-success bg-opacity-10 rounded">
                    <h6 class="mb-2">
                        <i class="fas fa-chart-line me-2"></i>Your Progress
                    </h6>
                    <div class="progress mb-2" style="height: 8px;">
                        <div class="progress-bar bg-success" role="progressbar" style="width: 25%"></div>
                    </div>
                    <small class="text-muted">25% Complete</small>
                </div>
            </div>
        </div>
    </div>

    <?php include('footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function likePost(postId) {
            fetch('like_post.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({post_id: postId})
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update like count in UI
                    location.reload(); // Simple reload for now
                } else {
                    alert('Error liking post');
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        function sharePost() {
            if (navigator.share) {
                navigator.share({
                    title: '<?php echo addslashes($post['title']); ?>',
                    text: '<?php echo addslashes(substr(strip_tags($post['description']), 0, 100)); ?>',
                    url: window.location.href
                });
            } else {
                // Fallback - copy to clipboard
                navigator.clipboard.writeText(window.location.href).then(() => {
                    alert('Link copied to clipboard!');
                });
            }
        }

        // Auto-scroll to top on page load
        window.addEventListener('load', function() {
            window.scrollTo(0, 0);
        });
    </script>
</body>
</html>