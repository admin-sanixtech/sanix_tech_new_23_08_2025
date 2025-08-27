<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'db_connection.php';

// Get Python category ID (assuming Python has category_id = 2, adjust as needed)
// You can also fetch this dynamically
$category_query = "SELECT category_id FROM categories WHERE category_name = 'Python'";
$category_result = $conn->query($category_query);
$category = $category_result->fetch_assoc();
$python_category_id = $category ? $category['category_id'] : 2; // fallback to 2

// Fetch subcategories for Python
$subcategory_query = "SELECT subcategory_id, subcategory_name FROM subcategories WHERE category_id = ? ORDER BY subcategory_name";
$stmt = $conn->prepare($subcategory_query);
$stmt->bind_param("i", $python_category_id);
$stmt->execute();
$subcategory_result = $stmt->get_result();

// Get selected subcategory (if any)
$selected_subcategory_id = isset($_GET['subcategory_id']) ? intval($_GET['subcategory_id']) : 0;

// If no subcategory selected, get the first one
if ($selected_subcategory_id === 0 && $subcategory_result->num_rows > 0) {
    $subcategory_result->data_seek(0);
    $first_subcategory = $subcategory_result->fetch_assoc();
    $selected_subcategory_id = $first_subcategory['subcategory_id'];
    $subcategory_result->data_seek(0);
}

// Fetch posts for selected subcategory
$posts_query = "SELECT p.*, s.subcategory_name 
                FROM posts p 
                LEFT JOIN subcategories s ON p.subcategory_id = s.subcategory_id 
                WHERE p.category_id = ? AND p.subcategory_id = ? 
                ORDER BY p.created_at DESC";
$stmt = $conn->prepare($posts_query);
$stmt->bind_param("ii", $python_category_id, $selected_subcategory_id);
$stmt->execute();
$posts_result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Python Course - Sanix Technologies</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/user_styles.css?v=<?php echo time(); ?>">
    <style>
        .python-header {
            background: linear-gradient(135deg, #3776ab 0%, #ffd43b 100%);
            color: white;
            padding: 2rem 0;
            margin-bottom: 2rem;
        }
        .sidebar {
            background-color: #f8f9fa;
            min-height: 500px;
            border-right: 2px solid #dee2e6;
        }
        .topic-link {
            padding: 12px 15px;
            cursor: pointer;
            border-bottom: 1px solid #dee2e6;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
        }
        .topic-link:hover {
            background-color: #e9ecef;
            padding-left: 20px;
        }
        .topic-link.active-topic {
            background-color: #3776ab;
            color: white;
            border-left: 4px solid #ffd43b;
        }
        .content-area {
            background-color: #85bdc6ff;
            min-height: 500px;
        }
        .post-item {
            margin-bottom: 15px;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            border-left: 4px solid #3776ab;
            transition: transform 0.2s ease;
        }
        .post-item:hover {
            transform: translateX(5px);
        }
        .post-title {
            font-size: 1.3rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 10px;
        }
        .post-meta {
            font-size: 0.9rem;
            color: #666;
            margin-bottom: 10px;
        }
        .post-description {
            color: #555;
            line-height: 1.6;
        }
        .python-badge {
            background: #3776ab;
            color: white;
            font-size: 0.8rem;
        }
    </style>
</head>
<body>
    <?php include('header.php'); ?>
    <?php include('navbar.php'); ?>

    <!-- Python Header -->
    <div class="python-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="mb-2">
                        <i class="fab fa-python me-3"></i>Python Programming Course
                    </h1>
                    <p class="mb-0 fs-5">Master Python from basics to advanced concepts</p>
                </div>
                <div class="col-md-4 text-end">
                    <img src="uploads/python_logo.png" alt="Python" style="height: 80px;" class="img-fluid">
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid my-5 px-0">
        <div class="row gx-0">
            <!-- Left Sidebar - Python Topics -->
            <aside class="col-md-3 sidebar">
                <div class="p-3">
                    <h4 class="mb-3">
                        <i class="fas fa-list me-2"></i>Python Topics
                    </h4>
                    <p class="text-muted small">Select a topic to explore</p>
                </div>
                
                <div class="subcategory-list">
                    <?php if ($subcategory_result && $subcategory_result->num_rows > 0): ?>
                        <?php while($subcategory = $subcategory_result->fetch_assoc()): ?>
                            <div class="topic-link <?php echo ($subcategory['subcategory_id'] == $selected_subcategory_id) ? 'active-topic' : ''; ?>" 
                                 data-subcategory-id="<?php echo $subcategory['subcategory_id']; ?>"
                                 data-subcategory-name="<?php echo htmlspecialchars($subcategory['subcategory_name']); ?>">
                                <i class="fas fa-code me-2"></i>
                                <span><?php echo htmlspecialchars($subcategory['subcategory_name']); ?></span>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="topic-link">
                            <i class="fas fa-info-circle me-2"></i>
                            <span>No topics found</span>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Progress Tracker -->
                <div class="mt-4 p-3 bg-light">
                    <h6 class="mb-2">
                        <i class="fas fa-chart-line me-2"></i>Course Progress
                    </h6>
                    <div class="progress mb-2" style="height: 6px;">
                        <div class="progress-bar bg-warning" role="progressbar" style="width: 30%"></div>
                    </div>
                    <small class="text-muted">30% Complete</small>
                </div>
            </aside>

            <!-- Main Content Area -->
            <div class="col-md-7 content-area">
                <div id="content-display" class="p-4">
                    <?php if ($posts_result && $posts_result->num_rows > 0): ?>
                        <?php 
                        // Get subcategory name
                        $subcategory_name = '';
                        $subcategory_result->data_seek(0);
                        while($sub = $subcategory_result->fetch_assoc()) {
                            if ($sub['subcategory_id'] == $selected_subcategory_id) {
                                $subcategory_name = $sub['subcategory_name'];
                                break;
                            }
                        }
                        ?>
                        
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h3 class="heading">
                                <i class="fas fa-play-circle me-2"></i>
                                <?php echo htmlspecialchars($subcategory_name); ?>
                            </h3>
                            <span class="python-badge badge px-3 py-2">
                                <?php echo $posts_result->num_rows; ?> Lesson(s)
                            </span>
                        </div>
                        
                        <?php while($post = $posts_result->fetch_assoc()): ?>
                            <div class="post-item">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <div class="post-title">
                                            <i class="fas fa-file-code me-2 text-primary"></i>
                                            <?php echo htmlspecialchars($post['title']); ?>
                                        </div>
                                        <div class="post-meta">
                                            <i class="fas fa-calendar me-2"></i>
                                            <?php echo date('M d, Y', strtotime($post['created_at'])); ?>
                                            
                                            <?php if ($post['views'] > 0): ?>
                                                <i class="fas fa-eye ms-3 me-2"></i>
                                                <?php echo $post['views']; ?> views
                                            <?php endif; ?>
                                            
                                            <?php if ($post['reading_time'] > 0): ?>
                                                <i class="fas fa-clock ms-3 me-2"></i>
                                                <?php echo $post['reading_time']; ?> min
                                            <?php endif; ?>
                                        </div>
                                        <div class="post-description">
                                            <?php 
                                            $description = strip_tags($post['description']);
                                            echo htmlspecialchars(substr($description, 0, 150));
                                            if (strlen($description) > 150) echo '...';
                                            ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-3 d-flex justify-content-between align-items-center">
                                    <a href="post_detail.php?post_id=<?php echo $post['post_id']; ?>" 
                                       class="btn btn-primary">
                                        <i class="fas fa-play me-2"></i>Start Learning
                                    </a>
                                    <div class="text-muted small">
                                        <?php if ($post['likes'] > 0): ?>
                                            <i class="fas fa-heart me-1"></i>
                                            <?php echo $post['likes']; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fab fa-python fa-4x text-muted mb-3"></i>
                            <h3 class="heading">Welcome to Python Course</h3>
                            <p class="text-muted">Select a topic from the left sidebar to start learning Python programming.</p>
                            <div class="mt-4">
                                <div class="alert alert-info">
                                    <strong>💡 Getting Started:</strong> Choose any topic from the sidebar to begin your Python journey!
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Right Sidebar -->
            <?php include('right_sidebar.php'); ?>
        </div>
    </div>

    <?php include('footer.php'); ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const topicLinks = document.querySelectorAll(".topic-link");
            const contentDisplay = document.getElementById("content-display");

            topicLinks.forEach(function(link) {
                link.addEventListener("click", function() {
                    if (!this.hasAttribute('data-subcategory-id')) {
                        return;
                    }

                    const subcategoryId = this.getAttribute("data-subcategory-id");
                    const categoryId = <?php echo $python_category_id; ?>;

                    // Show loading
                    contentDisplay.innerHTML = `
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-3 text-muted">Loading Python content...</p>
                        </div>
                    `;

                    // Update URL without refreshing page
                    const newUrl = `python_course.php?subcategory_id=${subcategoryId}`;
                    window.history.pushState({}, '', newUrl);

                    // Fetch content for selected subcategory
                    fetch(`fetch_posts.php?category_id=${categoryId}&subcategory_id=${subcategoryId}`)
                        .then(response => response.text())
                        .then(data => {
                            contentDisplay.innerHTML = data;
                            
                            // Update active state
                            topicLinks.forEach(tl => tl.classList.remove("active-topic"));
                            this.classList.add("active-topic");
                        })
                        .catch(error => {
                            console.error("Error:", error);
                            contentDisplay.innerHTML = `
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-triangle me-2"></i>
                                    Error loading content. Please try again.
                                </div>
                            `;
                        });
                });
            });

            // Auto-load first topic if none selected
            if (<?php echo $selected_subcategory_id; ?> === 0 && topicLinks.length > 0) {
                topicLinks[0].click();
            }
        });
    </script>
</body>
</html>