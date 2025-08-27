<?php
require 'db_connection.php';

// Get parameters
$category_id = isset($_GET['category_id']) ? intval($_GET['category_id']) : 0;
$subcategory_id = isset($_GET['subcategory_id']) ? intval($_GET['subcategory_id']) : 0;

if ($category_id === 0 || $subcategory_id === 0) {
    echo '<div class="alert alert-warning">Invalid parameters</div>';
    exit;
}

// Fetch posts for the selected subcategory
$posts_query = "SELECT p.*, s.subcategory_name 
                FROM posts p 
                LEFT JOIN subcategories s ON p.subcategory_id = s.subcategory_id 
                WHERE p.category_id = ? AND p.subcategory_id = ? 
                ORDER BY p.created_at DESC";

$stmt = $conn->prepare($posts_query);
$stmt->bind_param("ii", $category_id, $subcategory_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    // Get subcategory name from first post
    $first_post = $result->fetch_assoc();
    $subcategory_name = $first_post['subcategory_name'];
    
    // Reset pointer to beginning
    $result->data_seek(0);
    
    echo '<h3 class="heading mb-4">' . htmlspecialchars($subcategory_name) . '</h3>';
    
    while ($post = $result->fetch_assoc()) {
        ?>
        <div class="post-item">
            <div class="post-title">
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
                    <?php echo $post['reading_time']; ?> min read
                <?php endif; ?>
            </div>
            <div class="post-description">
                <?php 
                $description = strip_tags($post['description']);
                echo htmlspecialchars(substr($description, 0, 200));
                if (strlen($description) > 200) echo '...';
                ?>
            </div>
            <div class="mt-2">
                <a href="post_detail.php?post_id=<?php echo $post['post_id']; ?>" 
                   class="btn btn-sm btn-outline-primary">
                    Read More <i class="fas fa-arrow-right ms-1"></i>
                </a>
            </div>
        </div>
        <?php
    }
} else {
    // Get subcategory name even if no posts
    $subcategory_query = "SELECT subcategory_name FROM subcategories WHERE subcategory_id = ?";
    $stmt = $conn->prepare($subcategory_query);
    $stmt->bind_param("i", $subcategory_id);
    $stmt->execute();
    $subcategory_result = $stmt->get_result();
    $subcategory = $subcategory_result->fetch_assoc();
    
    $subcategory_name = $subcategory ? $subcategory['subcategory_name'] : 'Unknown Topic';
    
    echo '<h3 class="heading mb-4">' . htmlspecialchars($subcategory_name) . '</h3>';
    echo '<div class="alert alert-info">';
    echo '<i class="fas fa-info-circle me-2"></i>';
    echo 'No posts found for this topic yet. Check back later for updates!';
    echo '</div>';
}
?>