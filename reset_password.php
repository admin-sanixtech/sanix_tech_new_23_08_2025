<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $token = $_POST['token'] ?? '';
    $new_password = $_POST['new_password'] ?? '';

    if (!empty($token) && !empty($new_password)) {
        $safe_token = mysqli_real_escape_string($conn, $token);
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

        // Check if the token is valid and not expired
        $sql = "SELECT email FROM password_resets WHERE token = '$safe_token' AND created_at >= NOW() - INTERVAL 1 HOUR";
        $result = mysqli_query($conn, $sql);

        if ($result && mysqli_num_rows($result) > 0) {
            $row = mysqli_fetch_assoc($result);
            $email = mysqli_real_escape_string($conn, $row['email']);

            // Update user password
            $update = "UPDATE users SET password = '$hashed_password' WHERE email = '$email'";
            if (mysqli_query($conn, $update)) {
                // Delete token after successful reset
                $delete = "DELETE FROM password_resets WHERE token = '$safe_token'";
                mysqli_query($conn, $delete);
                echo "✅ Your password has been successfully updated.";
            } else {
                echo "❌ Error updating password. Try again.";
            }
        } else {
            echo "❌ Invalid or expired reset link.";
        }
    } else {
        echo "❌ All fields are required.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
</head>
<body>
    <h2>Enter New Password</h2>
    <form method="POST" action="">
        <input type="hidden" name="token" value="<?php echo htmlspecialchars($_GET['token'] ?? '', ENT_QUOTES); ?>">
        <label for="new_password">New Password:</label>
        <input type="password" name="new_password" required>
        <button type="submit">Reset Password</button>
    </form>
</body>
</html>
