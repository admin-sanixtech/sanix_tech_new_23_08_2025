<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
        .register-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-group label {
            font-weight: bold;
        }
        .btn-primary {
            display: block;
            width: 100%;
            margin-top: 20px;
            padding: 10px;
        }
    </style>
</head>
<body>
<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>

<div class="register-container">
    <h2>Register</h2>
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
            <input type="file" id="photo" name="photo" class="form-control" accept="image/*">
        </div>
        <button type="submit" name="submit" class="btn btn-primary">Register</button>
    </form>

    <?php
    include 'db_connection.php';

    if (isset($_POST['submit'])) {
        $username = trim($_POST['username']);
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $phone_number = trim($_POST['phone_number']);
        $photo = $_FILES['photo']['name'];

        if ($password !== $confirm_password) {
            echo "<p class='text-danger'>Passwords do not match. Please try again.</p>";
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            $targetDir = "uploads/";
            $targetFile = $targetDir . basename($photo);
            $photoPath = null;

            if (!empty($photo) && move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
                $photoPath = $targetFile;
            }

            $role = 'user';
            try {
                $stmt = $pdo->prepare("INSERT INTO users (name, email, password, phone_number, photo, role, status, created_at)
                                       VALUES (?, ?, ?, ?, ?, ?, 'active', NOW())");
                $stmt->execute([$username, $email, $hashed_password, $phone_number, $photoPath, $role]);

                echo "<p class='text-success'>Registration successful! Redirecting to login page...</p>";
                echo "<script>setTimeout(function(){ window.location.href='login.php'; }, 2000);</script>";
            } catch (PDOException $e) {
                if (str_contains($e->getMessage(), 'Duplicate entry')) {
                    echo "<p class='text-danger'>Email already registered. Try logging in.</p>";
                } else {
                    echo "<p class='text-danger'>Error: " . $e->getMessage() . "</p>";
                }
            }
        }
    }
    ?>
</div>

</body>
</html>
