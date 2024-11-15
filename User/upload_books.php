<?php
session_start();
include 'db_connection.php'; // Adjust the path as necessary

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userId = $_SESSION['user_id']; // Assuming you have user ID stored in session
    $file = $_FILES['book_file'];

    // Validate file type
    $allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
    if (in_array($file['type'], $allowedTypes)) {
        $targetDir = "uploads/books/";
        $filePath = $targetDir . basename($file['name']);

        // Move the uploaded file to the target directory
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            // Insert into database
            $stmt = $conn->prepare("INSERT INTO user_books (user_id, file_path) VALUES (?, ?)");
            $stmt->bind_param("is", $userId, $filePath);
            $stmt->execute();

            echo "File uploaded successfully!";
        } else {
            echo "Failed to upload file.";
        }
    } else {
        echo "Invalid file type. Only PDF and Word files are allowed.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Books</title>
</head>
<body>
    <h1>Upload Book</h1>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="file" name="book_file" accept=".pdf, .doc, .docx" required>
        <button type="submit">Upload</button>
    </form>
</body>
</html>
