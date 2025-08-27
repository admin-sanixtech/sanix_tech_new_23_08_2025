<?php
// get_subcategories.php
include 'db_connection.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if category_id is passed in the request
if (isset($_GET['category_id']) && !empty($_GET['category_id'])) {
    $category_id = intval($_GET['category_id']); // Use intval to ensure it's an integer

    // Prepare and execute the query to fetch subcategories for the selected category
    $query = "SELECT subcategory_id, subcategory_name FROM subcategories WHERE category_id = ?";
    if ($stmt = $conn->prepare($query)) {
        // Bind the category_id parameter (single parameter)
        $stmt->bind_param("i", $category_id); // "i" means integer for category_id

        // Execute the statement
        if ($stmt->execute()) {
            // Get the result
            $result = $stmt->get_result();

            // Check if subcategories are found
            if ($result->num_rows > 0) {
                // Output subcategory options
                while ($row = $result->fetch_assoc()) {
                    echo "<option value='" . htmlspecialchars($row['subcategory_id'], ENT_QUOTES, 'UTF-8') . "'>" . 
                         htmlspecialchars($row['subcategory_name'], ENT_QUOTES, 'UTF-8') . "</option>";
                }
            } else {
                echo "<option value=''>No subcategories available</option>";
            }
        } else {
            echo "Error executing query: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error preparing query: " . $conn->error;
    }
} else {
    echo "No category selected or invalid category ID.";
}

$conn->close();
?>


