<?php
include 'db_connection.php';

if (isset($_POST['category_id'])) {
    $category_id = intval($_POST['category_id']);
    
    // Fetch subcategories for the selected category
    $result = $conn->query("SELECT * FROM sanixazs_main_db.subcategories WHERE category_id = $category_id");

    if ($result->num_rows > 0) {
        // Create options for the subcategory dropdown
        while ($row = $result->fetch_assoc()) {
            echo "<option value='" . $row['subcategory_id'] . "'>" . htmlspecialchars($row['subcategory_name']) . "</option>";
        }
    } else {
        echo "<option value=''>No subcategories found</option>";
    }
}
?>
