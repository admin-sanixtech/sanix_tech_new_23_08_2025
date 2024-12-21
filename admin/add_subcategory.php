<?php
session_start();
include 'db_connection.php';

// Check if the user is logged in and has admin role
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. You must be an admin to view this page.");
}

// Fetch categories for the dropdown
$categories = $conn->query("SELECT * FROM sanixazs_main_db.categories");

// Fetch all subcategories (initially to display all)
$subcategories_result = $conn->query("SELECT sc.subcategory_name, c.category_name 
                                      FROM sanixazs_main_db.subcategories sc 
                                      JOIN sanixazs_main_db.categories c 
                                      ON sc.category_id = c.category_id");

?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Manage Subcategories</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" />
    <script src="https://kit.fontawesome.com/ae360af17e.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/admin_styleone.css" />
    <script src="https://d3js.org/d3.v7.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .tooltip { position: absolute; background: rgba(0,0,0,0.7); color: white; padding: 5px; pointer-events: none; }
        .hover-lift { transition: transform 0.3s ease-in-out; }
        .hover-lift:hover { transform: translateY(-5px); }
    </style>
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
                        <!-- Add Subcategory Form -->
                        <h2>Add Subcategory</h2>
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

                        <!-- Display Existing Subcategories (based on selected category) -->
                        <h2>Existing Subcategories</h2>
                        <table id="subcategories_table">
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
                </div>
            </div>
        </main>
    </div>
</div>

<?php include 'admin_footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/script.js"></script>

<!-- jQuery for AJAX -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function() {
        // When category is selected, fetch subcategories
        $('#category_id').on('change', function() {
            var category_id = $(this).val();
            if (category_id) {
                // Send an AJAX request to fetch subcategories for the selected category
                $.ajax({
                    url: 'fetch_subcategories.php',
                    method: 'POST',
                    data: { category_id: category_id },
                    success: function(response) {
                        // Update the table with the subcategories for this category
                        $('#subcategories_table tbody').html(response);
                    }
                });
            } else {
                // If no category is selected, reset the table to show all subcategories
                $.ajax({
                    url: 'fetch_subcategories.php',
                    method: 'POST',
                    data: { category_id: '' },  // Empty category_id to get all subcategories
                    success: function(response) {
                        // Update the table to show all subcategories
                        $('#subcategories_table tbody').html(response);
                    }
                });
            }
        });
    });
</script>

</body>
</html>
