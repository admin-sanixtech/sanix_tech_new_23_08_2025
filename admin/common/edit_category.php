<?php
//edit_category.php
// Only start session if one isn't already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define possible paths for db_connection.php
$possiblePaths = [
    __DIR__ . '/../config/db_connection.php',   // ../config/
    __DIR__ . '/config/db_connection.php',     // current/config/
    __DIR__ . '/../../config/db_connection.php', // ../../config/
    __DIR__ . '/db_connection.php',            // same folder
    __DIR__ . '/home2/sanixazs/public_html/admin/config/db_connection.php',
    __DIR__ . '/admin/config/db_connection.php'
];

// Flag for tracking inclusion
$fileIncluded = false;

foreach ($possiblePaths as $path) {
    if (file_exists($path)) {
        include $path;
        $fileIncluded = true;
        break;
    }
}

if (!$fileIncluded) {
    die("Error: db_connection.php not found in any expected locations.");
}

// Enable error reporting to help with debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: http://sanixtech.in/login.php');
    exit();
}

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if category ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header('Location: add_category.php?error=Category ID is required');
    exit();
}

$category_id = intval($_GET['id']);

// Use the full table name with database prefix
$query = $conn->query("SELECT * FROM sanixazs_main_db.categories WHERE category_id = $category_id");

if (!$query || $query->num_rows === 0) {
    header('Location: add_category.php?error=Category not found');
    exit();
}

$row = $query->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $conn->real_escape_string($_POST['category_name']);
    $image_path = $row['category_image']; // Keep existing image by default

    // Handle image update if a new image is uploaded
    if (!empty($_FILES['category_image']['name']) && $_FILES['category_image']['error'] === UPLOAD_ERR_OK) {
        $target_dir = "uploads/";
        
        // Create directory if it doesn't exist
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        
        // Generate unique filename
        $img_ext = strtolower(pathinfo($_FILES['category_image']['name'], PATHINFO_EXTENSION));
        $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
        
        if (in_array($img_ext, $allowed_ext)) {
            $new_img_name = uniqid('cat_', true) . '.' . $img_ext;
            $image_path = $target_dir . $new_img_name;
            
            if (!move_uploaded_file($_FILES["category_image"]["tmp_name"], $image_path)) {
                $error_message = "Error uploading file.";
            }
        } else {
            $error_message = "Only JPG, PNG, and GIF files are allowed.";
        }
    }

    // Update query with database prefix
    if (!isset($error_message)) {
        $stmt = $conn->prepare("UPDATE sanixazs_main_db.categories SET category_name = ?, category_image = ? WHERE category_id = ?");
        $stmt->bind_param("ssi", $name, $image_path, $category_id);
        
        if ($stmt->execute()) {
            header("Location: add_category.php?success=Category updated successfully");
            exit();
        } else {
            $error_message = "Error updating category: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <title>Edit Category</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet">
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
                        <h2>Edit Category</h2>
                        
                        <?php if (isset($error_message)): ?>
                            <div class="alert alert-danger"><?= htmlspecialchars($error_message) ?></div>
                        <?php endif; ?>
                        
                        <form method="post" enctype="multipart/form-data">
                            <div class="mb-3">
                                <label for="category_name" class="form-label">Category Name</label>
                                <input type="text" id="category_name" name="category_name" class="form-control" value="<?= htmlspecialchars($row['category_name']) ?>" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Current Image:</label><br>
                                <?php if (!empty($row['category_image']) && file_exists($row['category_image'])): ?>
                                    <img src="<?= htmlspecialchars($row['category_image']) ?>" width="100" height="100" class="img-thumbnail mb-2" alt="Current Category Image"><br>
                                <?php else: ?>
                                    <p class="text-muted">No image available</p><br>
                                <?php endif; ?>
                                <label for="category_image" class="form-label">New Image (optional):</label>
                                <input type="file" id="category_image" name="category_image" class="form-control" accept="image/*">
                            </div>
                            <button type="submit" class="btn btn-success">Update Category</button>
                            <a href="add_category.php" class="btn btn-secondary">Cancel</a>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>