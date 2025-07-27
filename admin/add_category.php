<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die('Access denied. Admins only.');
}

$messages = [];

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
            $upload_path = 'uploads/' . $new_img_name;

            if (!is_dir('uploads')) {
                mkdir('uploads', 0777, true);
            }

            if (move_uploaded_file($img_tmp, $upload_path)) {
                // Check for duplicate category
                $check_sql = "SELECT * FROM sanixazs_main_db.categories WHERE category_name = '$category_name'";
                $check_result = $conn->query($check_sql);

                if ($check_result->num_rows > 0) {
                    $messages[] = "Category '$category_name' already exists!";
                } else {
                    $sql = "INSERT INTO sanixazs_main_db.categories (category_name, category_image)
                            VALUES ('$category_name', '$upload_path')";
                    if ($conn->query($sql) === TRUE) {
                        $messages[] = "New category '$category_name' added successfully!";
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

// Fetch existing categories
$category_result = $conn->query("SELECT * FROM sanixazs_main_db.categories");
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
    <link rel="stylesheet" href="css/admin_styleone.css" />
</head>
<body>
<div class="wrapper">
    <aside id="sidebar" class="js-sidebar">
        <?php include 'admin_menu.php'; ?>
    </aside>
    <div class="main">
        <?php include 'admin_navbar.php'; ?>
        <main class="content px-3 py-2">
            <div class="container-fluid">
                <div class="card border-0">
                    <div class="content">
                        <h2>Add New Category</h2>
                        <form action="" method="POST" enctype="multipart/form-data" class="mb-3">
                            <label for="category_name" class="form-label">Category Name:</label>
                            <input type="text" id="category_name" name="category_name" class="form-control mb-2" required>

                            <label for="category_image" class="form-label">Category Image:</label>
                            <input type="file" id="category_image" name="category_image" class="form-control mb-2" accept="image/*" required>

                            <button type="submit" name="add_category" class="btn btn-primary">Add Category</button>
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
                        <h2>Existing Categories</h2>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>SNO</th>
                                    <th>Category Name</th>
                                    <th>Category ID</th>
                                    <th>Image</th>
                                    <th>Edit</th>
                                    <th>Delete</th>
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
                                                    <img src="<?php echo htmlspecialchars($category['category_image']); ?>" width="80" height="80" alt="Category Image">
                                                <?php else: ?>
                                                    No image
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="edit_category.php?id=<?php echo $category['category_id']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                            </td>
                                            <td>
                                                <a href="delete_category.php?id=<?php echo $category['category_id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr><td colspan="6">No categories found.</td></tr>
                                <?php endif; ?>
                            </tbody>
                        </table>

                        <hr>
                        <?php include 'admin_footer.php'; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
