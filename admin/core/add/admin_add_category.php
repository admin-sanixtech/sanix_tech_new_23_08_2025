<?php
//admin_add_category.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once(__DIR__ . '/../../config/db_connection.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Secure admin check
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: http://sanixtech.in/login.php');
    exit();
}

$messages = [];

// Define base URL for uploads
$base_url = 'http://sanixtech.in/';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    $category_name = $conn->real_escape_string($_POST['category_name']);

    // Handle image upload
    if (isset($_FILES['category_image']) && $_FILES['category_image']['error'] === UPLOAD_ERR_OK) {
        $img_name = $_FILES['category_image']['name'];
        $img_tmp = $_FILES['category_image']['tmp_name'];
        $img_ext = strtolower(pathinfo($img_name, PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($img_ext, $allowed_ext)) {
            $new_img_name = uniqid('cat_', true) . '.' . $img_ext;
            
            // Define upload paths
            $upload_dir = $_SERVER['DOCUMENT_ROOT'] . '/uploads/';
            $upload_path = $upload_dir . $new_img_name;
            $db_path = 'uploads/' . $new_img_name; // Store relative path in DB

            // Create uploads directory if it doesn't exist
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }

            if (move_uploaded_file($img_tmp, $upload_path)) {
                // Check for duplicate category
                $check_sql = "SELECT * FROM sanixazs_main_db.categories WHERE category_name = '$category_name'";
                $check_result = $conn->query($check_sql);

                if ($check_result->num_rows > 0) {
                    $messages[] = "Category '$category_name' already exists!";
                } else {
                    // Insert with status as 'pending' for approval
                    $sql = "INSERT INTO sanixazs_main_db.categories (category_name, category_image, status)
                            VALUES ('$category_name', '$db_path', 'pending')";
                    if ($conn->query($sql) === TRUE) {
                        $messages[] = "New category '$category_name' submitted for approval!";
                    } else {
                        $messages[] = "Error: " . $conn->error;
                    }
                }
            } else {
                $messages[] = "Failed to upload image.";
            }
        } else {
            $messages[] = "Only JPG, PNG, and GIF files are allowed.";
        }
    } else {
        $messages[] = "Category image is required.";
    }
}

// Function to get full image URL
function getImageUrl($imagePath, $baseUrl) {
    if (empty($imagePath)) {
        return '';
    }
    
    // If it's already a full URL, return as is
    if (strpos($imagePath, 'http') === 0) {
        return $imagePath;
    }
    
    // Check multiple possible locations
    $possiblePaths = [
        $baseUrl . $imagePath,
        $baseUrl . 'uploads/' . basename($imagePath),
        $baseUrl . 'admin/uploads/' . basename($imagePath),
        $baseUrl . 'admin/category/uploads/' . basename($imagePath)
    ];
    
    foreach ($possiblePaths as $path) {
        $headers = @get_headers($path);
        if ($headers && strpos($headers[0], '200') !== false) {
            return $path;
        }
    }
    
    // If no valid path found, return the first possibility
    return $possiblePaths[0];
}

// Get status badge class
function getStatusBadge($status) {
    switch ($status) {
        case 'approved':
            return '<span class="badge bg-success">Approved</span>';
        case 'rejected':
            return '<span class="badge bg-danger">Rejected</span>';
        case 'pending':
        default:
            return '<span class="badge bg-warning">Pending</span>';
    }
}

// Fetch existing categories with all statuses
$category_result = $conn->query("SELECT * FROM sanixazs_main_db.categories ORDER BY created_at DESC");
$categories = [];
if ($category_result->num_rows > 0) {
    while ($row = $category_result->fetch_assoc()) {
        $categories[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8" />
    <title>Add Category</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../../css/admin_styleone.css">
</head>
<body>
<div class="wrapper">
    <aside id="sidebar" class="js-sidebar">
        <?php include '../../admin_menu.php'; ?>
    </aside>
    <div class="main">
        <?php include '../../admin_navbar.php'; ?>
        <main class="content px-3 py-2">
            <div class="container-fluid">
                <div class="card border-0">
                    <div class="content">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h2>Add New Category</h2>
                            <a href="admin_approve_category.php" class="btn btn-info">
                                <i class="fas fa-check-circle"></i> Approve Categories
                            </a>
                        </div>
                        
                        <form action="" method="POST" enctype="multipart/form-data" class="mb-3">
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="category_name" class="form-label">Category Name:</label>
                                    <input type="text" id="category_name" name="category_name" class="form-control mb-2" required>
                                </div>
                                <div class="col-md-6">
                                    <label for="category_image" class="form-label">Category Image:</label>
                                    <input type="file" id="category_image" name="category_image" class="form-control mb-2" accept="image/*" required>
                                </div>
                            </div>
                            <button type="submit" name="add_category" class="btn btn-primary">Submit Category for Approval</button>
                        </form>

                        <!-- Display Messages -->
                        <?php if (!empty($messages)) : ?>
                            <div class="alert alert-info">
                                <?php foreach ($messages as $message) : ?>
                                    <p><?php echo $message; ?></p>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Display Existing Categories -->
                        <h2>All Categories</h2>
                        <div class="table-responsive">
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>SNO</th>
                                        <th>Category Name</th>
                                        <th>Category ID</th>
                                        <th>Image</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($categories)) : ?>
                                        <?php foreach ($categories as $index => $category) : ?>
                                            <tr>
                                                <td><?php echo $index + 1; ?></td>
                                                <td><?php echo htmlspecialchars($category['category_name']); ?></td>
                                                <td><?php echo htmlspecialchars($category['category_id']); ?></td>
                                                <td>
                                                    <?php if (!empty($category['category_image'])): ?>
                                                        <?php $imageUrl = getImageUrl($category['category_image'], $base_url); ?>
                                                        <img src="<?php echo htmlspecialchars($imageUrl); ?>" 
                                                             width="60" height="60" alt="Category Image"
                                                             style="object-fit: cover; border-radius: 5px;"
                                                             onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iODAiIGhlaWdodD0iODAiIHZpZXdCb3g9IjAgMCA4MCA4MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjgwIiBoZWlnaHQ9IjgwIiBmaWxsPSIjZjNmNGY2Ii8+CjxwYXRoIGQ9Ik00MCA0MEMzNy43OTA5IDQwIDM2IDM4LjIwOTEgMzYgMzZDMzYgMzMuNzkwOSAzNy43OTA5IDMyIDQwIDMyQzQyLjIwOTEgMzIgNDQgMzMuNzkwOSA0NCAzNkM0NCA0MS43OTA5IDQyLjIwOTEgNDAgNDAgNDBaIiBmaWxsPSIjOWNhM2FmIi8+CjxwYXRoIGQ9Ik0yOCA1Nkw0MCA0NEw1MiA1NkgyOFoiIGZpbGw9IiM5Y2EzYWYiLz4KPC9zdmc+'; this.alt='Image not found';">
                                                    <?php else: ?>
                                                        <span class="text-muted">No image</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo getStatusBadge($category['status']); ?></td>
                                                <td><?php echo date('M d, Y', strtotime($category['created_at'])); ?></td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <?php if ($category['status'] === 'pending'): ?>
                                                            <a href="admin_approve_category.php?approve=<?php echo $category['category_id']; ?>" 
                                                               class="btn btn-success btn-sm" 
                                                               onclick="return confirm('Approve this category?');">
                                                                <i class="fas fa-check"></i> Approve
                                                            </a>
                                                            <a href="admin_approve_category.php?reject=<?php echo $category['category_id']; ?>" 
                                                               class="btn btn-danger btn-sm" 
                                                               onclick="return confirm('Reject this category?');">
                                                                <i class="fas fa-times"></i> Reject
                                                            </a>
                                                        <?php endif; ?>
                                                        
                                                        <?php if ($category['status'] === 'approved'): ?>
                                                            <a href="../../edit_category.php?id=<?php echo $category['category_id']; ?>" 
                                                               class="btn btn-warning btn-sm">
                                                                <i class="fas fa-edit"></i> Edit
                                                            </a>
                                                        <?php endif; ?>
                                                        
                                                        <a href="../../delete_category.php?id=<?php echo $category['category_id']; ?>" 
                                                           class="btn btn-danger btn-sm" 
                                                           onclick="return confirm('Are you sure you want to delete this category?');">
                                                            <i class="fas fa-trash"></i> Delete
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <tr><td colspan="7" class="text-center">No categories found.</td></tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <hr>
                        <?php include '../../admin_footer.php'; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>
</html>