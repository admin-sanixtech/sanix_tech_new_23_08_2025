<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Include the database connection
    include 'db_connection.php';

    // Get the email from the form and sanitize it
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    // Check if the email exists in the users table
    $checkEmailQuery = "SELECT email FROM users WHERE email = '$email' AND status = 'active'";
    $result = mysqli_query($conn, $checkEmailQuery);

    if (mysqli_num_rows($result) > 0) {
        // Generate a unique token
        $token = bin2hex(random_bytes(50)); // 50-character token

        // Insert the token into the password_resets table
        $insertTokenQuery = "INSERT INTO password_resets (email, token) VALUES ('$email', '$token')";
        if (mysqli_query($conn, $insertTokenQuery)) {
            // Send the password reset email
            $resetLink = "https://sanixtech.in/reset_password.php?token=$token";
            $subject = "Password Reset Request";
            $message = "Click the link to reset your password: $resetLink";
            $headers = "From: no-reply@sanixtech.in";

            if (mail($email, $subject, $message, $headers)) {
                echo "An email has been sent to your email address.";
            } else {
                echo "Failed to send email.";
            }
        } else {
            echo "Failed to insert reset token. Please try again.";
        }
    } else {
        echo "This email does not exist in our system.";
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Sanix Technology</title>
    <link rel="stylesheet" href="css/user_styles.css"> <!-- Link to your CSS file -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css"/>
    <style>
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

        .contact-container {
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
        .form-group input[type="email"],
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .form-group textarea {
            resize: vertical;
            height: 100px;
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

        .success {
            color: green;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        Sanix Technology
    </div>

    <?php include 'navbar.php'; ?>
    <h2>Reset Password</h2>
    <form method="POST" action="password_reset_request.php">
        <label for="email">Enter your email address:</label>
        <input type="email" name="email" required>
        <button type="submit">Send Reset Link</button>
    </form>
</body>
</html>
