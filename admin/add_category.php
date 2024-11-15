<?php
session_start();
include 'db_connection.php';

// Check if the user is logged in and has admin role
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. You must be an admin to view this page.");
}

// Initialize an empty array for messages
$messages = [];

// Check if the form is submitted to add a new category
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_category'])) {
    $category_name = $conn->real_escape_string($_POST['category_name']);
    
    // Check for duplicate category
    $check_sql = "SELECT * FROM sanixazs_main_db.categories WHERE category_name = '$category_name'";
    $check_result = $conn->query($check_sql);
    
    if ($check_result->num_rows > 0) {
        $messages[] = "Category '$category_name' already exists!";
    } else {
        $sql = "INSERT INTO sanixazs_main_db.categories (category_name) VALUES ('$category_name')";
        if ($conn->query($sql) === TRUE) {
            $messages[] = "New category '$category_name' added successfully!";
        } else {
            $messages[] = "Error: " . $conn->error;
        }
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
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add Category</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" />
    <script src="https://kit.fontawesome.com/ae360af17e.js" crossorigin="anonymous"></script>
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
                        <!-- Add Category Form -->
                        <h2>Add New Category</h2>
                        <form action="" method="POST" class="mb-3">
                            <label for="category_name" class="form-label">Category Name:</label>
                            <input type="text" id="category_name" name="category_name" class="form-control mb-2" required>
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
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($categories)) : ?>
                                    <?php foreach ($categories as $index => $category) : ?>
                                        <tr>
                                            <td><?php echo $index + 1; ?></td>
                                            <td><?php echo htmlspecialchars($category['category_name']); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else : ?>
                                    <tr><td colspan="2">No categories found.</td></tr>
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
<script src="js/script.js"></script>
</body>
</html>
