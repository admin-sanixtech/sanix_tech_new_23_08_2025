<?php
session_start();
$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'db_connection.php';

    if (isset($_POST['email']) && !empty($_POST['email'])) {
        $email = mysqli_real_escape_string($conn, $_POST['email']);

        // Check if user exists
        $checkQuery = "SELECT email FROM users WHERE email = '$email' AND status = 'active'";
        $result = mysqli_query($conn, $checkQuery);

        if ($result && mysqli_num_rows($result) > 0) {
            $token = bin2hex(random_bytes(50));

            // Insert token into password_resets
            $insertQuery = "INSERT INTO password_resets (email, token) VALUES ('$email', '$token')";
            if (mysqli_query($conn, $insertQuery)) {
                $resetLink = "https://sanixtech.in/reset_password.php?token=$token";
                $subject = "Password Reset Request - Sanix Technology";
                $messageBody = "Click the link below to reset your password:\n\n$resetLink\n\nIf you didn't request this, please ignore this email.";
                $headers = "From: no-reply@sanixtech.in";

                if (mail($email, $subject, $messageBody, $headers)) {
                    $message = "<div class='success'>A reset link has been sent to your email.</div>";
                } else {
                    $message = "<div class='error'>Failed to send email. Please try again.</div>";
                }
            } else {
                $message = "<div class='error'>Could not insert token. Try again later.</div>";
            }
        } else {
            $message = "<div class='error'>This email does not exist in our system.</div>";
        }
    } else {
        $message = "<div class='error'>Please enter a valid email.</div>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password - Sanix Technology</title>
    <link rel="stylesheet" href="css/user_styles.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body { font-family: Arial; background-color: #f4f4f4; }
        .container { max-width: 500px; margin: 50px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; }
        .form-group { margin-bottom: 15px; }
        .form-control { height: 40px; }
        .btn { width: 100%; }
        .success { color: green; text-align: center; margin-top: 10px; }
        .error { color: red; text-align: center; margin-top: 10px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>Reset Password</h2>
        <?= $message ?>
        <form method="POST" action="">
            <div class="form-group">
                <label for="email">Enter your registered email address:</label>
                <input type="email" name="email" class="form-control" required />
            </div>
            <button type="submit" class="btn btn-primary">Send Reset Link</button>
        </form>
    </div>
</body>
</html>
