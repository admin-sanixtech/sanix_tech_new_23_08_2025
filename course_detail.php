<?php 
// Debug version with comprehensive error handling
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Start output buffering to catch any errors
ob_start();

try {
    // Check if db_connection.php exists
    if (!file_exists('db_connection.php')) {
        throw new Exception("Database connection file not found");
    }
    
    require_once 'db_connection.php';
    
    // Check database connection
    if (!isset($conn) || !$conn) {
        throw new Exception("Database connection failed");
    }
    
    // Get and validate category ID
    $category_id = isset($_GET['category_id']) ? filter_var($_GET['category_id'], FILTER_VALIDATE_INT) : 0;
    
    if ($category_id <= 0) {
        throw new Exception("Invalid category ID provided");
    }
    
    // Test database connection with a simple query
    $testQuery = "SELECT 1";
    $testResult = $conn->query($testQuery);
    if (!$testResult) {
        throw new Exception("Database query test failed: " . $conn->error);
    }
    
    // Check if categories table exists
    $tableCheck = $conn->query("SHOW TABLES LIKE 'categories'");
    if (!$tableCheck || $tableCheck->num_rows == 0) {
        throw new Exception("Categories table does not exist");
    }
    
    // Fetch category details with error handling - FIXED: removed category_description
    $categoryQuery = "SELECT category_id, category_name, category_image FROM categories WHERE category_id = ? LIMIT 1";
    $categoryStmt = $conn->prepare($categoryQuery);
    
    if (!$categoryStmt) {
        throw new Exception("Failed to prepare category query: " . $conn->error);
    }
    
    $categoryStmt->bind_param("i", $category_id);
    
    if (!$categoryStmt->execute()) {
        throw new Exception("Failed to execute category query: " . $categoryStmt->error);
    }
    
    $categoryResult = $categoryStmt->get_result();
    $category = $categoryResult->fetch_assoc();
    
    if (!$category) {
        throw new Exception("Category with ID {$category_id} not found");
    }
    
    // Check for subcategories table and fetch subcategories
    $subCategoriesExist = false;
    $subcategories = [];
    $subTableCheck = $conn->query("SHOW TABLES LIKE 'subcategories'");
    
    if ($subTableCheck && $subTableCheck->num_rows > 0) {
        $subCategoriesExist = true;
        // Fetch subcategories - using only existing columns
        $subQuery = "SELECT subcategory_id, subcategory_name, category_id 
                     FROM subcategories 
                     WHERE category_id = ? 
                     ORDER BY subcategory_name";
        $subStmt = $conn->prepare($subQuery);
        
        if ($subStmt) {
            $subStmt->bind_param("i", $category_id);
            if ($subStmt->execute()) {
                $subResult = $subStmt->get_result();
                while ($row = $subResult->fetch_assoc()) {
                    $subcategories[] = $row;
                }
            }
            $subStmt->close();
        }
    }
    
    // Check for posts table and fetch posts
    $postsExist = false;
    $posts = [];
    $postTableCheck = $conn->query("SHOW TABLES LIKE 'posts'");
    
    if ($postTableCheck && $postTableCheck->num_rows > 0) {
        $postsExist = true;
        // Get selected subcategory ID
        $selected_subcategory_id = isset($_GET['subcategory_id']) ? intval($_GET['subcategory_id']) : 0;
        
        // If no subcategory selected and we have subcategories, select first one
        if ($selected_subcategory_id == 0 && !empty($subcategories)) {
            $selected_subcategory_id = $subcategories[0]['subcategory_id'];
        }
        
        // Fetch posts for selected subcategory or all posts for category
        if ($selected_subcategory_id > 0) {
            $postQuery = "SELECT post_id, title, description, content, featured_image, views, likes, reading_time, created_at 
                          FROM posts 
                          WHERE category_id = ? AND subcategory_id = ? 
                          ORDER BY created_at DESC";
            $postStmt = $conn->prepare($postQuery);
            $postStmt->bind_param("ii", $category_id, $selected_subcategory_id);
        } else {
            $postQuery = "SELECT post_id, title, description, content, featured_image, views, likes, reading_time, created_at 
                          FROM posts 
                          WHERE category_id = ? 
                          ORDER BY created_at DESC";
            $postStmt = $conn->prepare($postQuery);
            $postStmt->bind_param("i", $category_id);
        }
        
        if ($postStmt) {
            if ($postStmt->execute()) {
                $postResult = $postStmt->get_result();
                while ($row = $postResult->fetch_assoc()) {
                    $posts[] = $row;
                }
            }
            $postStmt->close();
        }
    }
    
    $categoryStmt->close();
    
} catch (Exception $e) {
    $error_message = $e->getMessage();
    $debug_info = [
        'error' => $error_message,
        'category_id' => isset($category_id) ? $category_id : 'not set',
        'db_connection' => isset($conn) ? 'exists' : 'missing',
        'php_version' => PHP_VERSION,
        'timestamp' => date('Y-m-d H:i:s')
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($category) ? htmlspecialchars($category['category_name']) . ' - ' : ''; ?>Sanix Technologies</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/user_styles.css?v=<?php echo time(); ?>">
    <style>
        .debug-info {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 1.5rem;
            margin: 1rem 0;
            font-family: 'Courier New', monospace;
            font-size: 14px;
        }
        .error-container {
            background: linear-gradient(135deg, #dc3545, #c82333);
            color: white;
            padding: 2rem;
            border-radius: 10px;
            margin: 2rem 0;
        }
        .success-container {
            background: linear-gradient(135deg, #28a745, #218838);
            color: white;
            padding: 2rem;
            border-radius: 10px;
            margin: 2rem 0;
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
            background-color: #007bff;
            color: white;
            border-left: 4px solid #ffc107;
        }
        .content-area {
            background-color: #85bdc6ff;
            min-height: 500px;
        }
        .post-item {
            margin-bottom: 20px;
            padding: 20px;
            background: white;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            border-left: 4px solid #007bff;
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
            margin-bottom: 15px;
        }
        .post-description {
            color: #555;
            line-height: 1.6;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <?php 
    try {
        if (file_exists('header.php')) include('header.php');
        if (file_exists('navbar.php')) include('navbar.php');
    } catch (Exception $e) {
        echo '<nav class="navbar navbar-expand-lg navbar-dark bg-primary"><div class="container"><a class="navbar-brand" href="index.php">Sanix Technologies</a></div></nav>';
    }
    ?>
    
    <?php if (isset($error_message)): ?>
        <!-- Error Display -->
        <div class="container my-5">
            <div class="error-container">
                <div class="text-center">
                    <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                    <h2>Page Error</h2>
                    <p class="lead"><?php echo htmlspecialchars($error_message); ?></p>
                    <a href="category_cards_show.php" class="btn btn-light btn-lg">
                        <i class="fas fa-arrow-left me-2"></i>Back to Categories
                    </a>
                </div>
            </div>
            
            <div class="debug-info">
                <h5><i class="fas fa-bug me-2"></i>Debug Information:</h5>
                <pre><?php echo htmlspecialchars(json_encode($debug_info, JSON_PRETTY_PRINT)); ?></pre>
                
                <h6 class="mt-3">Common Solutions:</h6>
                <ul>
                    <li>Check if <code>db_connection.php</code> exists and contains valid database credentials</li>
                    <li>Verify database connection and that the server is running</li>
                    <li>Ensure the <code>categories</code> table exists in your database</li>
                    <li>Check that category ID <?php echo isset($category_id) ? $category_id : 'N/A'; ?> exists in the database</li>
                    <li>Verify file permissions on the server</li>
                </ul>
                
                <h6 class="mt-3">Test Database Connection:</h6>
                <p>Try accessing your database directly or check phpMyAdmin to verify the connection.</p>
            </div>
        </div>
        
    <?php else: ?>
        <!-- Success - Show Content -->
        <div class="container-fluid my-5 px-0">
            <div class="row gx-0">
                <!-- Left Sidebar - Subcategories -->
                <aside class="col-md-3 sidebar">
                    <div class="p-3">
                        <h4 class="mb-3"><?php echo htmlspecialchars($category['category_name']); ?></h4>
                        <h6 class="text-muted mb-3">
                            <?php if (!empty($subcategories)): ?>
                                Topics (<?php echo count($subcategories); ?>)
                            <?php else: ?>
                                Course Content
                            <?php endif; ?>
                        </h6>
                    </div>
                    
                    <div class="subcategory-list">
                        <?php if (!empty($subcategories)): ?>
                            <?php foreach($subcategories as $index => $subcategory): ?>
                                <?php 
                                $selected_subcategory_id = isset($_GET['subcategory_id']) ? intval($_GET['subcategory_id']) : 0;
                                if ($selected_subcategory_id == 0 && $index == 0) {
                                    $selected_subcategory_id = $subcategory['subcategory_id'];
                                }
                                $isActive = ($subcategory['subcategory_id'] == $selected_subcategory_id);
                                ?>
                                <div class="topic-link <?php echo $isActive ? 'active-topic' : ''; ?>" 
                                     data-subcategory-id="<?php echo $subcategory['subcategory_id']; ?>"
                                     data-subcategory-name="<?php echo htmlspecialchars($subcategory['subcategory_name']); ?>">
                                    <i class="fas fa-play-circle me-2"></i>
                                    <span><?php echo htmlspecialchars($subcategory['subcategory_name']); ?></span>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="topic-link">
                                <i class="fas fa-info-circle me-2"></i>
                                <span>No topics available</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Progress Tracker -->
                    <div class="mt-4 p-3 bg-light">
                        <h6 class="mb-2">
                            <i class="fas fa-chart-line me-2"></i>Course Progress
                        </h6>
                        <div class="progress mb-2" style="height: 6px;">
                            <div class="progress-bar bg-success" role="progressbar" style="width: 25%"></div>
                        </div>
                        <small class="text-muted">25% Complete</small>
                    </div>
                </aside>

                <!-- Main Content Area -->
                <div class="col-md-7 content-area">
                    <div id="content-display" class="p-4">
                        <?php if (!empty($posts)): ?>
                            <?php 
                            // Get current subcategory name
                            $current_subcategory_name = '';
                            $selected_subcategory_id = isset($_GET['subcategory_id']) ? intval($_GET['subcategory_id']) : 0;
                            if ($selected_subcategory_id == 0 && !empty($subcategories)) {
                                $selected_subcategory_id = $subcategories[0]['subcategory_id'];
                            }
                            
                            foreach($subcategories as $sub) {
                                if ($sub['subcategory_id'] == $selected_subcategory_id) {
                                    $current_subcategory_name = $sub['subcategory_name'];
                                    break;
                                }
                            }
                            
                            if (empty($current_subcategory_name)) {
                                $current_subcategory_name = $category['category_name'] . ' Content';
                            }
                            ?>
                            
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h3 class="heading">
                                    <i class="fas fa-list-ul me-2"></i>
                                    <?php echo htmlspecialchars($current_subcategory_name); ?>
                                </h3>
                                <span class="badge bg-primary px-3 py-2">
                                    <?php echo count($posts); ?> Post(s)
                                </span>
                            </div>
                            
                            <?php foreach($posts as $post): ?>
                                <div class="post-item">
                                    <div class="post-title">
                                        <i class="fas fa-file-text me-2 text-primary"></i>
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
                                        echo htmlspecialchars(substr($description, 0, 200));
                                        if (strlen($description) > 200) echo '...';
                                        ?>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <a href="post_detail.php?post_id=<?php echo $post['post_id']; ?>" 
                                           class="btn btn-primary">
                                            <i class="fas fa-arrow-right me-2"></i>Read More
                                        </a>
                                        <?php if ($post['likes'] > 0): ?>
                                            <div class="text-muted small">
                                                <i class="fas fa-heart me-1"></i>
                                                <?php echo $post['likes']; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            
                        <?php else: ?>
                            <div class="text-center py-5">
                                <i class="fas fa-folder-open fa-4x text-muted mb-3"></i>
                                <h3 class="heading">Welcome to <?php echo htmlspecialchars($category['category_name']); ?></h3>
                                <p class="text-muted">
                                    <?php if (!empty($subcategories)): ?>
                                        Select a topic from the left sidebar to view content.
                                    <?php else: ?>
                                        No content available for this category yet.
                                    <?php endif; ?>
                                </p>
                                <div class="mt-4">
                                    <a href="category_cards_show.php" class="btn btn-outline-primary">
                                        <i class="fas fa-arrow-left me-2"></i>Back to Categories
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Right Sidebar -->
                <?php 
                try {
                    if (file_exists('right_sidebar.php')) include('right_sidebar.php');
                } catch (Exception $e) {
                    echo '<div class="col-md-2 bg-light p-3"><h6>Related Links</h6><p>Sidebar content unavailable</p></div>';
                }
                ?>
            </div>
        </div>
        
        <!-- Debug Info for Successful Load -->
        <div class="container">
            <div class="debug-info mt-4">
                <h6>Debug Info (Remove in Production):</h6>
                <p><strong>Category ID:</strong> <?php echo $category_id; ?></p>
                <p><strong>Category Name:</strong> <?php echo htmlspecialchars($category['category_name']); ?></p>
                <p><strong>Subcategories Found:</strong> <?php echo count($subcategories); ?></p>
                <p><strong>Posts Found:</strong> <?php echo count($posts); ?></p>
                <p><strong>Subcategories Table Exists:</strong> <?php echo $subCategoriesExist ? 'Yes' : 'No'; ?></p>
                <p><strong>Posts Table Exists:</strong> <?php echo $postsExist ? 'Yes' : 'No'; ?></p>
            </div>
        </div>
    <?php endif; ?>
    
    <?php 
    try {
        if (file_exists('footer.php')) include('footer.php');
    } catch (Exception $e) {
        echo '<footer class="bg-dark text-white text-center py-3"><p>&copy; 2025 Sanix Technologies. All rights reserved.</p></footer>';
    }
    ?>
    
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
                    const categoryId = <?php echo isset($category_id) ? $category_id : 0; ?>;

                    // Show loading
                    contentDisplay.innerHTML = `
                        <div class="text-center py-5">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Loading...</span>
                            </div>
                            <p class="mt-3 text-muted">Loading content...</p>
                        </div>
                    `;

                    // Update URL without refreshing page
                    const newUrl = `course_detail.php?category_id=${categoryId}&subcategory_id=${subcategoryId}`;
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
        });
        
        console.log('Course detail page loaded successfully');
        console.log('Category ID: <?php echo isset($category_id) ? $category_id : "N/A"; ?>');
    </script>
</body>
</html>

<?php
// Clean up
if (isset($conn)) {
    $conn->close();
}

// Flush output buffer
ob_end_flush();
?>