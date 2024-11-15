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
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/admin_styles.css"> 
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
    <script src="https://d3js.org/d3.v7.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .tooltip { position: absolute; background: rgba(0,0,0,0.7); color: white; padding: 5px; pointer-events: none; }
        .hover-lift { transition: transform 0.3s ease-in-out; }
        .hover-lift:hover { transform: translateY(-5px); }
    </style>
</head>
<body>
<?php
include 'header.php';
include 'sidebar.php';
?>


<div class="main-content">
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
</div>

</body>
</html>
