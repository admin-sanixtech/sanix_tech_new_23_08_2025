<?php
session_start();
include 'db_connection.php';

// Adjusted role check using the actual session structure
if (isset($_SESSION['role']) && trim($_SESSION['role']) === 'admin') {
    // Debugging message to ensure the admin role is recognized
    echo "Admin access granted!"; 
} else {
    die("Access denied. You must be an admin to view this page.");
}

// Check if subcategory form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_subcategory'])) {
    $subcategory_name = $conn->real_escape_string($_POST['subcategory_name']);
    $category_id = intval($_POST['category_id']);

    $sql = "INSERT INTO sanixazs_main_db.subcategories (subcategory_name, category_id) VALUES ('$subcategory_name', '$category_id')";
    
    if ($conn->query($sql) === TRUE) {
        echo "<p style='color: green;'>New subcategory added successfully!</p>";
    } else {
        echo "<p style='color: red;'>Error: " . $conn->error . "</p>";
    }
}

// Fetch categories for the dropdown
$categories = $conn->query("SELECT * FROM sanixazs_main_db.categories");

// Fetch existing subcategories for display
$subcategories_result = $conn->query("SELECT sc.subcategory_name, c.category_name 
                                       FROM sanixazs_main_db.subcategories sc 
                                       JOIN sanixazs_main_db.categories c 
                                       ON sc.category_id = c.category_id");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subcategory Management</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        header {
            background-color: #f8f9fa;
            padding: 10px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }
        nav {
            float: left;
            width: 200px;
            background: #f8f9fa;
            border-right: 1px solid #ddd;
            height: 100%;
            padding: 10px;
        }
        nav a {
            display: block;
            margin: 5px 0;
            padding: 8px;
            color: #333;
            text-decoration: none;
        }
        nav a:hover {
            background-color: #ddd;
        }
        main {
            margin-left: 220px;
            padding: 10px;
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

<header>
    <h1>Sanix Technologies</h1>
    <p>Logged in as: <?php echo htmlspecialchars($_SESSION['email']); ?></p>
</header>

<nav>
    <h3>Admin Menu</h3>
            <img src="path/to/profile_photo.jpg" alt="Profile Photo">
            <ul class="sidebar-menu">
                <li><a href="#" onclick="loadContent('dashboard')">Dashboard</a></li>
                <li><a href="#" onclick="loadContent('users')">Users</a></li>
                <li><a href="#" onclick="loadContent('questions')">Questions</a></li>
                <li><a href="add_question.php">Add Questions</a></li>
                <li><a href="category_management.php">Categories</a></li>
                <li><a href="subcategory_management.php">Subcategories</a></li>
                <li><a href="#">Reports</a></li>
                <li><a href="#">Settings</a></li>
                <li><a href="../logout.php">Logout</a></li>
           
    </ul>
</nav>

<main>
    <h2>Add New Subcategory</h2>
    <form action="" method="POST">
        <label for="category_id">Select Category:</label>
        <select id="category_id" name="category_id" required>
            <option value="">Select a category</option>
            <?php
            if ($categories->num_rows > 0) {
                while ($cat = $categories->fetch_assoc()) {
                    echo "<option value='" . $cat['category_id'] . "'>" . htmlspecialchars($cat['category_name']) . "</option>";
                }
            }
            ?>
        </select>

        <label for="subcategory_name">Subcategory Name:</label>
        <input type="text" id="subcategory_name" name="subcategory_name" required>

        <button type="submit" name="add_subcategory">Add Subcategory</button>
    </form>

    <h2>Existing Subcategories</h2>
    <table>
        <thead>
            <tr>
                <th>S.No</th>
                <th>Category Name</th>
                <th>Subcategory Name</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($subcategories_result->num_rows > 0) {
                $index = 1;
                while ($row = $subcategories_result->fetch_assoc()) {
                    echo "<tr>
                            <td>" . $index++ . "</td>
                            <td>" . htmlspecialchars($row['category_name']) . "</td>
                            <td>" . htmlspecialchars($row['subcategory_name']) . "</td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='3'>No subcategories found.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</main>

</body>
</html>
