<?php
// get_subcategories_json.php

// Set content type to JSON
header('Content-Type: application/json');

require_once(__DIR__ . '/../../config/db_connection.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if category_id is passed in the request
if (isset($_GET['category_id']) && !empty($_GET['category_id'])) {
    $category_id = intval($_GET['category_id']); // Use intval to ensure it's an integer

    // Prepare and execute the query to fetch subcategories for the selected category
    $query = "SELECT subcategory_id, subcategory_name FROM subcategories WHERE category_id = ? ORDER BY subcategory_name ASC";
    if ($stmt = $conn->prepare($query)) {
        // Bind the category_id parameter (single parameter)
        $stmt->bind_param("i", $category_id); // "i" means integer for category_id

        // Execute the statement
        if ($stmt->execute()) {
            // Get the result
            $result = $stmt->get_result();
            $subcategories = [];

            // Check if subcategories are found
            if ($result->num_rows > 0) {
                // Collect subcategories in an array
                while ($row = $result->fetch_assoc()) {
                    $subcategories[] = [
                        'subcategory_id' => $row['subcategory_id'],
                        'subcategory_name' => $row['subcategory_name']
                    ];
                }
                // Return subcategories as JSON
                echo json_encode($subcategories);
            } else {
                // Return empty array if no subcategories found
                echo json_encode([]);
            }
        } else {
            // Return error as JSON
            echo json_encode(['error' => 'Error executing query: ' . $stmt->error]);
        }
        $stmt->close();
    } else {
        // Return error as JSON
        echo json_encode(['error' => 'Error preparing query: ' . $conn->error]);
    }
} else {
    // Return error as JSON
    echo json_encode(['error' => 'No category selected or invalid category ID']);
}

$conn->close();
?>