<?php
// Include database connection
include 'db_connection.php';

// Get the POST data
$id = intval($_POST['id']);
$display = intval($_POST['display']);

// Update the display status
$sql = "UPDATE sanixtec_main_db.questions SET display_on_dashboard = ? WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('ii', $display, $id);
$result = $stmt->execute();

if ($result) {
    echo "Update successful";
} else {
    echo "Error: " . $conn->error;
}

$stmt->close();
$conn->close();
?>
