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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Categories and Add Questions</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
        }
        .header {
            background-color: #007bff; /* Top ribbon color */
            color: white;
            padding: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .sidebar {
            width: 200px;
            height: 100vh;
            background-color: #f4f4f4;
            float: left;
            padding: 15px;
        }
        .content {
            margin-left: 220px; /* Leave space for sidebar */
            padding: 15px;
        }
        form {
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>

    <div class="header">
        <h1>Sanix Technology</h1>
        <div><?php echo htmlspecialchars($_SESSION['email']); ?></div>
    </div>

    <div class="sidebar">
    <img src="path/to/profile_photo.jpg" alt="Profile Photo">
    <ul class="sidebar-menu">
        <li><a href="#" onclick="loadContent('dashboard')">Dashboard</a></li>
        <li><a href="users.php">Users</a></li>
        <li><a href="questions.php">Questions</a></li>
        <li><a href="add_question.php">Add Questions</a></li>
        <li><a href="category_management.php">Categories</a></li>
        <li><a href="subcategory_management.php">Subcategories</a></li>
        <li><a href="#">Reports</a></li>
        <li><a href="#">Settings</a></li>
        <li><a href="../logout.php">Logout</a></li>
    </ul>
</div>

    <div class="content">
        <!-- Add Category Form -->
        <h2>Add New Category</h2>
        <form action="" method="POST">
            <label for="category_name">Category Name:</label>
            <input type="text" id="category_name" name="category_name" required>
            <button type="submit" name="add_category">Add Category</button>
        </form>

        <!-- Display Messages -->
        <?php if (!empty($messages)) {
            foreach ($messages as $message) {
                echo "<p>$message</p>";
            }
        } ?>

        <!-- Display Existing Categories -->
        <h2>Existing Categories</h2>
        <table>
            <thead>
                <tr>
                    <th>Category Name</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($categories)) {
                    foreach ($categories as $category) {
                        echo "<tr><td>" . htmlspecialchars($category['category_name']) . "</td></tr>";
                    }
                } else {
                    echo "<tr><td>No categories found.</td></tr>";
                } ?>
            </tbody>
        </table>

        <hr>

        <!-- Add New Question Form -->
        <h2>Add New Question</h2>
        <form action="add_question.php" method="POST">
            <label for="category">Category:</label>
            <select id="category" name="category_id">
                <?php
                if (!empty($categories)) {
                    foreach ($categories as $category) {
                        echo "<option value='" . $category['id'] . "'>" . htmlspecialchars($category['category_name']) . "</option>";
                    }
                } else {
                    echo "<option disabled>No categories available</option>";
                }
                ?>
            </select>

            <br><br>

            <label for="question">Question:</label>
            <input type="text" id="question" name="question">

            <br><br>

            <label for="correct_answer">Correct Answer:</label>
            <input type="text" id="correct_answer" name="correct_answer">

            <br><br>

            <label for="description">Description:</label>
            <textarea id="description" name="description"></textarea>

            <br><br>

            <button type="submit" name="add_question">Add Question</button>
        </form>
    </div>
</body>
</html>
