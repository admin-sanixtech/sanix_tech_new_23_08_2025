<?php
// Start session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    echo "Access denied. Please log in.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Sanix Technology</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <div class="header-left">
            <h1>Sanix Technology</h1>
        </div>
        <div class="header-right">
            <p>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</p>
            <a href="logout.php" class="logout-btn">Logout</a>
        </div>
    </header>
    <hr>
</body>
</html>
