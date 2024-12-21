<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'db_connection.php';

    $token = $_POST['token'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_BCRYPT);

    // Validate the token
    $tokenQuery = "SELECT email FROM password_resets WHERE token = '$token' AND created_at >= NOW() - INTERVAL 1 HOUR";
    $result = mysqli_query($conn, $tokenQuery);
    
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $email = $row['email'];

        // Update the password in the users table
        $updatePasswordQuery = "UPDATE users SET password = '$new_password' WHERE email = '$email'";
        mysqli_query($conn, $updatePasswordQuery);

        // Delete the token so it can't be reused
        $deleteTokenQuery = "DELETE FROM password_resets WHERE token = '$token'";
        mysqli_query($conn, $deleteTokenQuery);

        echo "Your password has been updated.";
    } else {
        echo "Invalid or expired token.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
</head>
<body>
    <h2>Enter New Password</h2>
    <form method="POST" action="reset_password.php">
        <input type="hidden" name="token" value="<?php echo $_GET['token']; ?>">
        <label for="new_password">New Password:</label>
        <input type="password" name="new_password" required>
        <button type="submit">Reset Password</button>
    </form>
</body>
</html>
