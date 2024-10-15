<?php
// Database configuration
$servername = "localhost"; // Replace with your server name or IP address
$username = "sanixazs"; // Replace with your database username
$password = "Kri1Lin2@#$%"; // Replace with your database password
$dbname = "sanixazs_main_db"; // Your database name

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
