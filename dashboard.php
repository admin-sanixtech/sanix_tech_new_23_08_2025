<?php
session_start();

// Check if the user is logged in; if not, redirect to the login page
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

echo "<h1>Welcome, " . $_SESSION['username'] . "!</h1>";
echo "<p>This is your dashboard page.</p>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sanix Technology</title>
</head>
<body>
    <h2>Dashboard</h2>
    <!-- Add your dashboard content here -->
    <a href="logout.php">Logout</a>
</body>
</html>
