<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

include 'db_connection.php'; // Make sure this path is correct

// Fetch users from the database
$usersQuery = "SELECT * FROM sanixazs_main_db.users"; // Adjust the table name if needed
$usersResult = $conn->query($usersQuery);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User List</title>
    <link rel="stylesheet" href="css/styles.css"> <!-- Link to your CSS file -->
    <style>
        /* Styling for layout */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
        }
        /* Top Ribbon */
        .top-ribbon {
            background-color: #333;
            color: #fff;
            padding: 15px;
            text-align: center;
            font-size: 18px;
        }
        /* Content Layout */
        .content {
            display: flex;
            height: calc(100vh - 60px); /* Adjust for top ribbon height */
        }
        /* Left side menu */
        .left-menu {
            flex: 1;
            background-color: #f8f8f8;
            padding: 20px;
            border-right: 2px solid #ccc;
        }
        /* Right side with user list */
        .right-side {
            flex: 3;
            padding: 20px;
            overflow-y: auto;
        }
        /* Table Styling */
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ccc;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f4f4f4;
        }
        /* Menu Styling */
        .menu {
            list-style: none;
            padding: 0;
        }
        .menu li {
            margin-bottom: 15px;
        }
        .menu li a {
            text-decoration: none;
            color: #333;
            font-weight: bold;
        }
        .menu li a:hover {
            color: #007bff;
        }
    </style>
</head>
<body>

    <!-- Top Ribbon -->
    <div class="top-ribbon">
        Admin Panel - User Management
    </div>

    <!-- Main Content -->
    <div class="content">
        
        <!-- Left side: Menu -->
        <div class="left-menu">
            <h4>Admin Menu</h4>
            <ul class="menu">
                <li><a href="admin_dashboard.php">Dashboard</a></li>
                <li><a href="add_question.php">Add Question</a></li>
                <li><a href="questions.php">View Questions</a></li>
                <li><a href="users.php">View Users</a></li>
                <li><a href="reports.php">Reports</a></li>
                <li><a href="settings.php">Settings</a></li>
                <li><a href="profile.php">Profile</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>
        
        <!-- Right side: User list -->
        <div class="right-side">
            <h3>User List</h3>
            <table>
                <tr>
                    <th>User ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th>Updated At</th>
                </tr>
                <?php
                if ($usersResult && $usersResult->num_rows > 0) {
                    while ($user = $usersResult->fetch_assoc()) {
                        echo "<tr>
                                <td>" . htmlspecialchars($user['user_id']) . "</td>
                                <td>" . htmlspecialchars($user['name']) . "</td>
                                <td>" . htmlspecialchars($user['email']) . "</td>
                                <td>" . htmlspecialchars($user['role']) . "</td>
                                <td>" . htmlspecialchars($user['status']) . "</td>
                                <td>" . htmlspecialchars($user['created_at']) . "</td>
                                <td>" . htmlspecialchars($user['updated_at']) . "</td>
                            </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7'>No users found.</td></tr>";
                }
                ?>
            </table>
        </div>
    </div>

</body>
</html>
