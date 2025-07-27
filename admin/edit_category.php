<?php
session_start();
include 'db_connection.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die('Access denied. Admins only.');
}

if (!isset($_GET['id'])) {
    die('Category ID is required.');
}

$category_id = intval($_GET['id']);
$query = $conn->query("SELECT * FROM categories WHERE category_id = $category_id");

if (!$query || $query->num_rows === 0) {
    die('Category not found.');
}

$row = $query->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['category_name'];
    $image_path = $row['category_image'];

    // Handle image update if a new image is uploaded
    if (!empty($_FILES['category_image']['name'])) {
        $target_dir = "uploads/";
        $image_path = $target_dir . basename($_FILES["category_image"]["name"]);
        move_uploaded_file($_FILES["category_image"]["tmp_name"], $image_path);
    }

    // Update query
    $stmt = $conn->prepare("UPDATE categories SET category_name = ?, category_image = ? WHERE category_id = ?");
    $stmt->bind_param("ssi", $name, $image_path, $category_id);
    $stmt->execute();

    header("Location: add_category.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <title>Edit Category</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Edit Category</h2>
    <form method="post" enctype="multipart/form-data">
        <div class="mb-3">
            <label>Category Name</label>
            <input type="text" name="category_name" class="form-control" value="<?= htmlspecialchars($row['category_name']) ?>" required>
        </div>
        <div class="mb-3">
            <label>Current Image:</label><br>
            <img src="<?= htmlspecialchars($row['category_image']) ?>" width="100"><br><br>
            <label>New Image (optional):</label>
            <input type="file" name="category_image" class="form-control">
        </div>
        <button type="submit" class="btn btn-success">Update Category</button>
        <a href="add_category.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>
</body>
</html>
