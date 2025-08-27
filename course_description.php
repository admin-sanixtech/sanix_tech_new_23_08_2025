

<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'db_connection.php';

if (!isset($_GET['title'])) {
    die("No title provided.");
}

$title = $_GET['title'];

// Fetch description for given title
$stmt = $conn->prepare("SELECT description FROM posts WHERE title = ?");
$stmt->bind_param("s", $title);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    echo "<h3>" . htmlspecialchars($title) . "</h3>";
    echo "<p>" . nl2br(htmlspecialchars($row['description'])) . "</p>";
} else {
    echo "No description found for this topic.";
} 
