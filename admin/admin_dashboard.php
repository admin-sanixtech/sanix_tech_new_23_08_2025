<?php
error_reporting(E_ALL); // Enable error reporting
ini_set('display_errors', 1); // Display errors

session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

include 'db_connection.php'; 

// Fetch total users
$totalUsersQuery = "SELECT COUNT(*) as total_users FROM users";
$totalUsersResult = mysqli_query($conn, $totalUsersQuery);
$totalUsers = mysqli_fetch_assoc($totalUsersResult)['total_users'];

// Fetch active users count
$activeUsersQuery = "SELECT COUNT(*) as active_users FROM users WHERE last_login >= NOW() - INTERVAL 30 DAY";
$activeUsersResult = mysqli_query($conn, $activeUsersQuery);
$activeUsers = mysqli_fetch_assoc($activeUsersResult)['active_users'];

// Fetch today's added users
$todaysDate = date('Y-m-d');
$todaysUsersQuery = "SELECT COUNT(*) as todays_users FROM users WHERE DATE(created_at) = '$todaysDate'";
$todaysUsersResult = mysqli_query($conn, $todaysUsersQuery);
$todaysUsers = mysqli_fetch_assoc($todaysUsersResult)['todays_users'];

// Fetch total questions added
$totalQuestionsAddedQuery = "SELECT COUNT(*) as total_questions_added FROM quiz_questions";
$totalQuestionsAddedResult = mysqli_query($conn, $totalQuestionsAddedQuery);
$totalQuestionsAdded = mysqli_fetch_assoc($totalQuestionsAddedResult)['total_questions_added'];

// Fetch total questions
$totalQuestionsQuery = "SELECT COUNT(*) as total_questions FROM quiz_questions";
$totalQuestionsResult = mysqli_query($conn, $totalQuestionsQuery);
$totalQuestions = mysqli_fetch_assoc($totalQuestionsResult)['total_questions'];

// Fetch today's added questions
$todaysQuestionsQuery = "SELECT COUNT(*) as todays_questions FROM quiz_questions WHERE DATE(created_at) = '$todaysDate'";
$todaysQuestionsResult = mysqli_query($conn, $todaysQuestionsQuery);
$todaysQuestions = mysqli_fetch_assoc($todaysQuestionsResult)['todays_questions'];

// Fetch questions attended by users
$attendedQuestionsQuery = "
    SELECT u.name as user_name, q.question_text 
    FROM quiz_results r
    JOIN users u ON r.user_id = u.user_id
    JOIN quiz_questions q ON r.question_id = q.question_id
";
$attendedQuestionsResult = mysqli_query($conn, $attendedQuestionsQuery);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css"> <!-- Link to your CSS file -->
    <style>
        /* Styles for the header */
        .header {
            background-color: #007bff; /* Blue background */
            color: white; /* White text color */
            padding: 10px 20px; /* Padding for header */
            display: flex; /* Flexbox layout */
            justify-content: space-between; /* Space between elements */
            align-items: center; /* Center items vertically */
        }

        .header h1 {
            margin: 0; /* Remove default margin */
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

        /* Dashboard styles */
        .dashboard {
            display: flex;
            justify-content: space-around;
            flex-wrap: wrap;
            margin: 20px;
        }

        .dashboard-item {
            background-color: #f8f9fa;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
            padding: 20px;
            margin: 20px;
            flex-basis: 22%; /* Allows four items per row */
        }

        .dashboard-item h1 {
            font-size: 48px; /* Bigger font size for numbers */
            color: #007bff;
        }

        .dashboard-item p {
            font-size: 24px; /* Label font size */
            color: #6c757d;
        }

        .main-content {
            margin-left: 220px; /* To make room for the sidebar */
        }

        /* Table styles */
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
        }

        /* Media queries for responsiveness */
        @media (max-width: 768px) {
            .dashboard-item {
                flex-basis: 100%; /* Full width on small screens */
            }
            .main-content {
                margin-left: 0; /* Remove sidebar margin */
            }
            .sidebar {
                display: none; /* Hide sidebar on small screens */
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Sanix Technology</h1>
        <div class="user-menu">
            <span>
                <?php echo isset($_SESSION['name']) ? $_SESSION['name'] : 'User'; ?> 
                <i class="fa fa-user"></i>
            </span>
            <div class="user-dropdown">
                <a href="#">Change Settings</a>
                <a href="#">Change Password</a>
                <a href="../logout.php">Logout</a>
            </div>
        </div>
    </div>
    
    <div class="sidebar">
        <img src="path/to/profile/photo.jpg" alt="Profile Photo"> <!-- Update with actual path -->
        <ul class="sidebar-menu">
            <li><a href="admin_dashboard.php">Dashboard</a></li>
            <li><a href="quiz_management.php">Quiz Management</a></li>
            <li><a href="quiz_page.php">Quiz Page</a></li>
            <li><a href="users.php">Users</a></li>
            <li><a href="questions.php">Questions</a></li>
            <li><a href="add_question.php">Add Questions</a></li>
            <li><a href="category_management.php">Categories</a></li>
            <li><a href="subcategory_management.php">Subcategories</a></li>
            <li><a href="#">Reports</a></li>
            <li><a href="#">Settings</a></li>
            <li><a href="../logout.php">Logout</a></li>
        </ul>
    </div>
    
    <div class="main-content">
        <div class="dashboard">
            <div class="dashboard-item">
                <h1><?php echo $totalUsers; ?></h1>
                <p>Total Users</p>
            </div>
            <div class="dashboard-item">
                <h1><?php echo $activeUsers; ?></h1>
                <p>Active Users (Last 30 Days)</p>
            </div>
            <div class="dashboard-item">
                <h1><?php echo $todaysUsers; ?></h1>
                <p>Today's Added Users</p>
            </div>
            <div class="dashboard-item">
                <h1><?php echo $totalQuestionsAdded; ?></h1>
                <p>Total Questions Added</p>
            </div>
            <div class="dashboard-item">
                <h1><?php echo $totalQuestions; ?></h1>
                <p>Total Questions</p>
            </div>
            <div class="dashboard-item">
                <h1><?php echo $todaysQuestions; ?></h1>
                <p>Today's Added Questions</p>
            </div>
        </div>
        
        <h2>Questions Attended by Users</h2>
        <table>
            <thead>
                <tr>
                    <th>User Name</th>
                    <th>Question</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($attendedQuestionsResult)): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['user_name']); ?></td>
                        <td><?php echo htmlspecialchars($row['question_text']); ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
