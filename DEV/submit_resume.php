<?php
session_start();
include 'db_connection.php'; // Include your database connection file
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Resume - Sanix Technology</title>
    <link rel="stylesheet" href="styles.css"> <!-- Add your CSS for styling -->
    <style>
        /* Basic styles to center form and header/menu */
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

        .resume-container {
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
        .form-group input[type="file"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .btn-submit {
            width: 100%;
            padding: 10px;
            background-color: #333;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-submit:hover {
            background-color: #555;
        }
    </style>
</head>
<body>

    <div class="header">
        Sanix Technology
    </div>

    <div class="nav-menu">
        <ul>
            <li><a href="#">Services</a></li>
            <li><a href="#">Courses</a></li>
            <li><a href="#">Projects</a></li>
            <li><a href="careers.php">Careers</a></li>
            <li><a href="#">About Us</a></li>
            <li><a href="#">Contact Us</a></li>
        </ul>
    </div>

    <div class="resume-container">
        <h2>Submit Your Resume</h2>
        <form action="submit_resume.php" method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label for="name">Name:</label>
                <input type="text" id="name" name="name" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="resume">Upload Resume (PDF only):</label>
                <input type="file" id="resume" name="resume" accept="application/pdf" required>
            </div>
            <button type="submit" class="btn-submit">Submit Resume</button>
        </form>
    </div>

    <?php
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Form fields
        $name = $_POST['name'];
        $email = $_POST['email'];
        $resume = $_FILES['resume'];

        // Validate file type (only PDF)
        $allowedTypes = ['application/pdf'];
        if (!in_array($resume['type'], $allowedTypes)) {
            die("Only PDF files are allowed.");
        }

        // Move the uploaded file to the server's uploads directory
        $uploadDir = 'uploads/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        $uploadFile = $uploadDir . basename($resume['name']);
        if (move_uploaded_file($resume['tmp_name'], $uploadFile)) {
            // Send the email
            $to = 'hr@sanixtech.in';
            $subject = "New Resume Submission from $name";
            $message = "Name: $name\nEmail: $email\n\nPlease find the attached resume.";
            
            // Headers for email with attachment
            $boundary = md5(time()); // Unique boundary for email sections
            $headers = "From: $email\r\n";
            $headers .= "MIME-Version: 1.0\r\n";
            $headers .= "Content-Type: multipart/mixed; boundary=\"$boundary\"\r\n";

            // Plain text message part
            $body = "--$boundary\r\n";
            $body .= "Content-Type: text/plain; charset=UTF-8\r\n";
            $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
            $body .= chunk_split(base64_encode($message));

            // Attachment part
            $fileContent = chunk_split(base64_encode(file_get_contents($uploadFile)));
            $body .= "--$boundary\r\n";
            $body .= "Content-Type: application/pdf; name=\"" . basename($resume['name']) . "\"\r\n";
            $body .= "Content-Disposition: attachment; filename=\"" . basename($resume['name']) . "\"\r\n";
            $body .= "Content-Transfer-Encoding: base64\r\n\r\n";
            $body .= $fileContent . "\r\n";
            $body .= "--$boundary--"; // End of message

            // Send email
            if (mail($to, $subject, $body, $headers)) {
                // Insert data into the database
                $upload_time = date('Y-m-d H:i:s');
                $stmt = $conn->prepare("INSERT INTO resumes (sender_name, sender_email, resume_file, upload_time) VALUES (?, ?, ?, ?)");
                $stmt->bind_param("ssss", $name, $email, $resume['name'], $upload_time);

                // Execute statement
                if ($stmt->execute()) {
                    // Redirect to the careers page with success message
                    header("Location: careers.php?success=true");
                    exit;
                } else {
                    echo "Error saving to database: " . $stmt->error;
                }

                // Close statement
                $stmt->close();
            } else {
                echo "Failed to send email.";
            }
        } else {
            echo "Failed to upload resume.";
        }

        // Close database connection
        $conn->close();
    }
    ?>
</body>
</html>
