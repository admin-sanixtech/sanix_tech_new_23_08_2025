<?php
session_start();
include 'db_connection.php';

$message = "";

if (isset($_POST['submit'])) {
    $username         = trim($_POST['username']);
    $email            = trim($_POST['email']);
    $password         = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone_number     = trim($_POST['phone_number']);
    $photo            = $_FILES['photo']['name'];

    if ($password !== $confirm_password) {
        $message = "❌ Passwords do not match!";
    } else {
        // Check if email already exists
        $check = $conn->prepare("SELECT * FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check_result = $check->get_result();

        if ($check_result->num_rows > 0) {
            $message = "❌ Email already registered!";
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            $targetDir = "uploads/";
            $uniqueFileName = uniqid() . "_" . basename($photo);
            $targetFile = $targetDir . $uniqueFileName;

            if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
                $photoPath = $targetFile;
            } else {
                $photoPath = null;
            }

            $role = 'user';
            $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone_number, photo, role, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'active', NOW())");
            $stmt->bind_param("ssssss", $username, $email, $hashed_password, $phone_number, $photoPath, $role);

            if ($stmt->execute()) {
                $_SESSION['success_message'] = "✅ Registration successful! Please login.";
                header("Location: login.php");
                exit();
            } else {
                $message = "❌ Error: " . $stmt->error;
            }

            $stmt->close();
        }
        $check->close();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - Sanix Technology</title>
    <link rel="stylesheet" href="css/user_styles.css">
    <link rel="stylesheet" href="css/register_styles.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .register-container {
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            background-color: #f9f9f9;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .register-container h2 { text-align: center; margin-bottom: 20px; }
        .form-group label { font-weight: bold; }
        .btn-primary { width: 100%; margin-top: 20px; padding: 10px; }
        .alert { margin-top: 15px; }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>
    <?php include 'navbar.php'; ?>

    <div class="register-container">
        <h2>Register</h2>

        <?php if (!empty($message)): ?>
            <div class="alert alert-danger"><?= $message ?></div>
        <?php endif; ?>

        <form action="register.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="username">Username:*</label>
                <input type="text" id="username" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="email">Email:*</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="password">Password:*</label>
                <input type="password" id="password" name="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password:*</label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="phone_number">Phone Number:*</label>
                <input type="text" id="phone_number" name="phone_number" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="photo">Upload Photo:*</label>
                <input type="file" id="photo" name="photo" class="form-control" accept="image/*" required>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Register</button>
        </form>
    </div>
    this is testing
    this is next level testing
</body>
</html>


