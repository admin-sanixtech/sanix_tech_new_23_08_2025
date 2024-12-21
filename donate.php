<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Include database connection
include 'db_connection.php';

// Fetch user information (optional, if you want to display the username)
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
    <title>Donate</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        .header {
            background-color: #003366; /* Dark blue */
            color: white;
            padding: 15px;
            text-align: center;
        }
        .nav-menu {
            background-color: lightblue; /* Light blue */
            width: 20%;
            padding: 10px;
            float: left;
            height: 100vh;
        }
        .nav-menu a {
            color: black;
            text-decoration: none;
            display: block;
            padding: 10px;
            margin: 5px 0;
            border-bottom: 1px solid white; /* White divider line */
        }
        .nav-menu a:hover {
            background-color: #b3d9ff; /* Lighter blue on hover */
        }
        .content {
            margin-left: 22%;
            padding: 20px;
        }
        .content img {
            max-width: 100%;
            height: auto;
            margin-top: 20px;
        }
        /* Sidebar styles */
        .sidebar {
            width: 200px;
            float: left; /* Align to the left */
            margin-right: 20px; /* Spacing between sidebar and main content */
        }

        .sidebar img {
            width: 100%;
            border-radius: 50%; /* Circular profile photo */
        }

        .sidebar-menu {
            list-style-type: none;
            padding: 0;
        }

        .sidebar-menu li {
            margin: 10px 0;
        }

        .sidebar-menu li a {
            text-decoration: none;
            color: #007bff;
        }
        .content img {
            max-width: 100%; /* Scale image to fit the container */
            height: auto; /* Maintain aspect ratio */
            margin-top: 20px; /* Spacing from top */
        }


    </style>
</head>
<body>
    <div class="header">
        <h1>Sanix Technology</h1>
        <h2>Welcome, <?php echo htmlspecialchars($user['name']); ?>!</h2>
    </div>

    <div class="sidebar">
        <img src="path/to/profile/photo.jpg" alt="Profile Photo"> <!-- Update with actual path -->
        <ul class="sidebar-menu">
            <li><a href="user_dashboard.php">Dashboard</a></li>
            <li><a href="user_quiz.php">Quiz Page</a></li>
            <li><a href="Courses.php">Courses</a></li>
            <li><a href="questions.php">Questions</a></li>
            <li><a href="books.php">Books</a></li>
            <li><a href="subscription.php">Subscription</a></li>
            <li><a href="donate.php">Donate</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </div>

    <div class="content">
        <h2>Donate</h2>
        <p>Your support helps us grow and improve our services.</p>
        <img src="images/Donate1.jpg" alt="QR Code for Donations">
    </div>
</body>
</html>
