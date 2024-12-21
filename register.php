<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Sanix Technology</title>
    <link rel="stylesheet" href="css/user_styles.css">
    <link rel="stylesheet" href="css/register_styles.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css"/>
    <style>
        /* Center the form on the page */
        .register-container {
            max-width: 500px; /* Width of the form */
            margin: 50px auto; /* Center it horizontally and add top margin */
            padding: 20px;
            background-color: #f9f9f9; /* Optional background color */
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        /* Style for the form elements */
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
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];
            $phone_number = $_POST['phone_number'];
            $photo = $_FILES['photo']['name'];

            if ($password !== $confirm_password) {
                echo "<p>Passwords do not match. Please try again.</p>";
            } else {
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                $targetDir = "uploads/";
                $targetFile = $targetDir . basename($_FILES['photo']['name']);
                if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
                    $photoPath = $targetFile;
                } else {
                    $photoPath = null;
                }

                $role = 'user';

                $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone_number, photo, role, status, created_at) VALUES (?, ?, ?, ?, ?, ?, 'active', NOW())");
                $stmt->bind_param("ssssss", $username, $email, $hashed_password, $phone_number, $photoPath, $role);

                if ($stmt->execute()) {
                    echo "<p>Registration successful! Welcome, $username.</p>";
                    header("Location: login.php");
                    exit();
                } else {
                    echo "Error: " . $stmt->error;
                }

                $stmt->close();
            }
        }

        $conn->close();
        ?>
    </div>
</body>
</html>
