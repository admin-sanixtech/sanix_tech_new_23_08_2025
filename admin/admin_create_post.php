<?php
session_start();
include 'db_connection.php'; // Database connection

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "Access denied. You must be an admin to view this page.";
    exit;
}


$message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $category_id = $_POST['category_id'];
    $subcategory_id = $_POST['subcategory_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];

    // Insert the post into the database
    $sql = "INSERT INTO posts (category_id, subcategory_id, title, description, createdby) 
            VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissi", $category_id, $subcategory_id, $title, $description, $user_id);

    if ($stmt->execute()) {
        $message = "<div class='alert alert-success'>Post created successfully!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}

// Fetch categories for the dropdown
$categories_query = "SELECT category_id, category_name FROM categories";
$categories_result = $conn->query($categories_query);

if (!$categories_result) {
    die("Error fetching categories: " . $conn->error); // Debugging step
}


// Check if category_id is passed in the request
if (isset($_GET['category_id'])) {
    $category_id = $_GET['category_id'];

    // Fetch subcategories based on the selected category_id
    $query = "SELECT subcategory_id, subcategory_name FROM subcategories WHERE category_id = ?";
    $stmt = $conn->prepare($query);

    // Bind the category_id parameter (single parameter)
    $stmt->bind_param("i", $category_id); // "i" means integer for category_id

    // Execute the statement
    $stmt->execute();
    
    // Get the result
    $result = $stmt->get_result();

    // Check if subcategories are found
    if ($result->num_rows > 0) {
        // Output subcategory options
        while ($row = $result->fetch_assoc()) {
            echo "<option value='" . $row['subcategory_id'] . "'>" . htmlspecialchars($row['subcategory_name']) . "</option>";
        }
    } else {
        echo "<option value=''>No subcategories available</option>";
    }
}

?>



<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Approve User Questions</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" />
    <script src="https://kit.fontawesome.com/ae360af17e.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/admin_styleone.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css">

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
                    <div class="card-body">

<div class="container mt-5">
    <h2>Create a New Post</h2>

    <?php if (!empty($message)) echo $message; ?>

    <form action="admin_create_post.php" method="POST">
        <div class="mb-3">
            <label for="category_id" class="form-label">Category</label>
            <select name="category_id" id="category_id" class="form-select" required>
                <option value="">Select Category</option>
                <?php while ($row = $categories_result->fetch_assoc()): ?>
                    <option value="<?php echo $row['category_id']; ?>">
                        <?php echo htmlspecialchars($row['category_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="subcategory_id" class="form-label">Subcategory</label>
            <select name="subcategory_id" id="subcategory_id" class="form-select" required>
                <option value="">Select Subcategory</option>
                <!-- Subcategories will be populated dynamically based on selected category -->
            </select>
        </div>

        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" name="title" id="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" class="form-control" rows="5" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Create Post</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Function to load subcategories when a category is selected
document.getElementById('category_id').addEventListener('change', function() {
    var category_id = this.value;

    if (category_id) {
        // Send an AJAX request to fetch subcategories for the selected category
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'get_subcategories.php?category_id=' + category_id, true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                document.getElementById('subcategory_id').innerHTML = xhr.responseText;
            }
        };
        xhr.send();
    } else {
        // Clear subcategory dropdown if no category is selected
        document.getElementById('subcategory_id').innerHTML = '<option value="">Select Subcategory</option>';
    }
});
</script>

</body>
</html>
