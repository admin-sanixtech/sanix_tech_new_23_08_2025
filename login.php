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
            } 
            else if ($role === 'user') {
                header("Location: User/user_dashboard.php");
            } 
            else {
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
    <link rel="stylesheet" href="css/user_styles.css"> <!-- Link to your CSS file -->
    <link rel="stylesheet" href="css/user_login_styles.css"> <!-- Link to your CSS file -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css"/>

</head>
<body>
    <header>
        <h1>Sanix Technology</h1>
    </header>

    <?php include 'navbar.php'; ?>

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
