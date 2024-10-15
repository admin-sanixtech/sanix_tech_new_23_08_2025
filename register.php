<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Sanix Technology</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS file -->
    <style>
        /* Center the form on the page */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f4f4f4;
            flex-direction: column;
        }

        header {
            text-align: center;
            padding: 10px;
            background-color: #333;
            color: #fff;
            width: 100%;
            position: fixed;
            top: 0;
        }

        .nav-menu {
            list-style: none;
            padding: 0;
            display: flex;
            justify-content: center;
            margin: 0;
            background-color: #444;
        }

        .nav-menu li {
            margin: 0 15px;
        }

        .nav-menu li a {
            text-decoration: none;
            color: #fff;
            padding: 10px;
            display: block;
        }

        .nav-menu li a:hover {
            background-color: #666;
        }

        .register-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            margin-top: 80px; /* Adjust to avoid overlap with fixed header */
        }

        h2 {
            text-align: center;
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            color: #333;
        }

        .form-group input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .btn-primary {
            background-color: #5cb85c;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
        }

        .btn-primary:hover {
            background-color: #4cae4c;
        }

        /* Responsive styling */
        @media (max-width: 600px) {
            .register-container {
                width: 90%;
            }
        }
    </style>
</head>
<body>
    <header>
        <h1>Sanix Technology</h1>
        <ul class="nav-menu">
            <li><a href="#">Services</a></li>
            <li><a href="#">Courses</a></li>
            <li><a href="#">Projects</a></li>
            <li><a href="careers.php">Careers</a></li>
            <li><a href="#">About Us</a></li>
            <li><a href="contact.php">Contact Us</a></li>
        </ul>
    </header>

    <div class="register-container">
        <h2>Register</h2>
        <form action="register.php" method="POST">
            <div class="form-group">
                <label for="username">Username:</label>
                <input type="text" id="username" name="username" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Password:</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm Password:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <div class="form-group">
                <label for="phone_number">Phone Number (Optional):</label>
                <input type="text" id="phone_number" name="phone_number">
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Register</button>
        </form>

        <?php
        include 'db_connection.php'; // Include the database connection file

        if (isset($_POST['submit'])) {
            // Retrieve user inputs
            $username = $_POST['username'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];
            $phone_number = $_POST['phone_number']; // Optional field

            // Check if passwords match
            if ($password !== $confirm_password) {
                echo "<p>Passwords do not match. Please try again.</p>";
            } else {
                // Hash the password for security
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);

                // Set the default role to 'user'
                $role = 'user';

                // Use prepared statement to insert the user data into the 'users' table
                $stmt = $conn->prepare("INSERT INTO users (name, email, password, phone_number, role, status, created_at) VALUES (?, ?, ?, ?, ?, 'active', NOW())");
                $stmt->bind_param("sssss", $username, $email, $hashed_password, $phone_number, $role);

                if ($stmt->execute()) {
                    echo "<p>Registration successful! Welcome, $username.</p>";
                } else {
                    echo "Error: " . $stmt->error;
                }

                $stmt->close();
            }
        }

        $conn->close(); // Close the connection
        ?>
    </div>
</body>
</html>
