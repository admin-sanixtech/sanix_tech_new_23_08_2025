<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'db_connection.php';

    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    // Check if the email exists in the users table
    $checkEmailQuery = "SELECT email FROM users WHERE email = '$email'";
    $result = mysqli_query($conn, $checkEmailQuery);

    if (mysqli_num_rows($result) > 0) {
        // Generate a unique token
        $token = bin2hex(random_bytes(50)); // Generate a 50-character token

        // Insert the reset token into the password_resets table
        $insertTokenQuery = "INSERT INTO password_resets (email, token) VALUES ('$email', '$token')";
        mysqli_query($conn, $insertTokenQuery);

        // Send the password reset email
        $resetLink = "https://yourwebsite.com/reset_password.php?token=$token";
        $subject = "Password Reset Request";
        $message = "Click the following link to reset your password: $resetLink";
        $headers = "From: no-reply@yourwebsite.com";
        
        if (mail($email, $subject, $message, $headers)) {
            echo "An email has been sent to your email address.";
        } else {
            echo "Failed to send email.";
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
    <title>Password Reset Request</title>
</head>
<body>
    <h2>Reset Password</h2>
    <form method="POST" action="password_reset_request.php">
        <label for="email">Enter your email address:</label>
        <input type="email" name="email" required>
        <button type="submit">Send Reset Link</button>
    </form>
</body>
</html>
