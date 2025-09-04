<?php
// Include database connection
include 'db_connection.php';

// Fetch category and subcategory from POST request
$category_id = isset($_POST['category_id']) ? $_POST['category_id'] : '';
$subcategory_id = isset($_POST['subcategory_id']) ? $_POST['subcategory_id'] : '';

// Build the SQL query based on selected filters
$sql = "SELECT question_id, question_text FROM sanixazs_main_db.quiz_questions WHERE 1=1";

if ($category_id) {
    $sql .= " AND category_id = '$category_id'";
}

if ($subcategory_id) {
    $sql .= " AND subcategory_id = '$subcategory_id'";
}

$result = $conn->query($sql);

// Output the questions as HTML options for the select box
while ($row = $result->fetch_assoc()) {
    echo "<option value='" . $row['question_id'] . "'>" . $row['question_text'] . "</option>";
}
?>
