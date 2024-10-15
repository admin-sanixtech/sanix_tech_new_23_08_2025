<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Include database connection
include 'db_connection.php';

// Fetch user information
$userId = $_SESSION['user_id'];
$userQuery = "SELECT * FROM sanixazs_main_db.users WHERE user_id = ?";
$stmt = $conn->prepare($userQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$userResult = $stmt->get_result();
$user = $userResult->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h1>Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h1>
    <p>Your role: <?php echo htmlspecialchars($user['role']); ?></p>
    <h2>Your Profile</h2>
    <p>Email: <?php echo htmlspecialchars($user['email']); ?></p>
    <p>Status: <?php echo htmlspecialchars($user['status']); ?></p>
    <p>Account Created: <?php echo htmlspecialchars($user['created_at']); ?></p>
    <p><a href="user_profile.php">Edit Profile</a></p>
    <p><a href="user_quiz.php">Take Quiz</a></p>
    <p><a href="logout.php">Logout</a></p>
</body>
</html>
