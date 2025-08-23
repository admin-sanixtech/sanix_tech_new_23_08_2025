<?php
// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Include your DB connection file
include 'db_connection.php';

// Run a simple query to test connection
$sql = "SELECT NOW() as current_time";
$result = $conn->query($sql);

if ($result && $row = $result->fetch_assoc()) {
    echo "✅ Database connected successfully!<br>";
    echo "Current server time: " . $row['current_time'];
} else {
    echo "❌ Query failed: " . $conn->error;
}
?>
