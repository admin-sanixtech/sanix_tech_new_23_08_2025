<?php
include 'db_connection.php'; // Include the database connection file

session_start(); // Start the session

// Handle form submission
if (isset($_POST['submit'])) {
    // Retrieve user inputs
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Prepare and execute the SQL query to check for the user
    $stmt = $conn->prepare("SELECT user_id, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        $stmt->bind_result($user_id, $hashed_password, $role);
        $stmt->fetch();

        // Verify the entered password with the hashed password stored in the database
        if (password_verify($password, $hashed_password)) {
            // Password is correct; set session variables
            $_SESSION['user_id'] = $user_id;
            $_SESSION['email'] = $email;
            $_SESSION['role'] = $role;

            // Redirect based on user role
            if ($role === 'admin') {
                header("Location: admin/admin_dashboard.php");
            } else {
                header("Location: user_dashboard.php");
            }
            exit;
        } else {
            $error_message = "Invalid password. Please try again.";
        }
    } else {
        $error_message = "Email not found. Please try again.";
    }

    $stmt->close();
}
$conn->close(); // Close the connection
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Sanix Technology</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
    <style>
        /* Basic styles to center form and header/menu */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }

        .header {
            background-color: #333;
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 24px;
        }

        .nav-menu {
            text-align: center;
            padding: 10px;
            background-color: #444;
        }

        .nav-menu ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
        }

        .nav-menu ul li {
            display: inline;
            margin: 0 15px;
        }

        .nav-menu ul li a {
            color: white;
            text-decoration: none;
            font-size: 18px;
        }

        .login-container {
            width: 400px;
            margin: 50px auto;
            background-color: white;
            padding: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
        }

        h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
        }

        .form-group input[type="text"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .btn-primary {
            width: 100%;
            padding: 10px;
            background-color: #333;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-primary:hover {
            background-color: #555;
        }

        .error {
            color: red;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        Sanix Technology
    </div>

    <div class="nav-menu">
        <ul>
            <li><a href="#">Services</a></li>
            <li><a href="#">Courses</a></li>
            <li><a href="#">Projects</a></li>
            <li><a href="careers.php">Careers</a></li>
            <li><a href="#">About Us</a></li>
            <li><a href="contact.php">Contact Us</a></li>
        </ul>
    </div>

    <div class="login-container">
        <h2>Login</h2>
        <form action="login.php" method="POST">
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="text" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" name="submit" class="btn-primary">Login</button>
        </form>

        <!-- Display error message if any -->
        <?php
        if (isset($error_message)) {
            echo "<p class='error'>$error_message</p>";
        }
        ?>

        <!-- Add a Forgot Password link -->
        <p><a href="password_reset_request.php">Forgot Password?</a></p>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>
</body>
</html>
