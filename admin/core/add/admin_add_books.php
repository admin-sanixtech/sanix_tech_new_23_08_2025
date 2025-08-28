<?php


// Start the session if it's not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_connection.php'; // Include the database connection

// Enable error reporting to help with debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Book Upload</title>
</head>
<body>
    <h1>Upload a Book</h1>
    <form method="POST" enctype="multipart/form-data" action="upload_book.php">
        <label>Title:</label>
        <input type="text" name="title" required><br>

        <label>Author:</label>
        <input type="text" name="author" required><br>

        <label>Genre:</label>
        <input type="text" name="genre" required><br>

        <label>Description:</label>
        <textarea name="description"></textarea><br>

        <label>ISBN:</label>
        <input type="text" name="isbn" required><br>

        <label>Publisher:</label>
        <input type="text" name="publisher"><br>

        <label>Publication Date:</label>
        <input type="date" name="publication_date"><br>

        <label>Language:</label>
        <input type="text" name="language"><br>

        <label>Pages:</label>
        <input type="number" name="pages"><br>

        <label>Price:</label>
        <input type="number" step="0.01" name="price" required><br>

        <label>Stock:</label>
        <input type="number" name="stock"><br>

        <label>Cover Image:</label>
        <input type="file" name="cover_image"><br>

        <label>Discount:</label>
        <input type="number" step="0.01" name="discount"><br>

        <label>Format:</label>
        <select name="format">
            <option value="Hardcover">Hardcover</option>
            <option value="Paperback" selected>Paperback</option>
            <option value="eBook">eBook</option>
        </select><br>

        <label>Bestseller:</label>
        <input type="checkbox" name="bestseller" value="1"><br>

        <button type="submit">Upload Book</button>
    </form>
</body>
</html>

<?php
// upload_book.php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include 'db_connection.php'; // Include database connection script

    $title = $_POST['title'];
    $author = $_POST['author'];
    $genre = $_POST['genre'];
    $description = $_POST['description'] ?? null;
    $isbn = $_POST['isbn'];
    $publisher = $_POST['publisher'] ?? null;
    $publication_date = $_POST['publication_date'] ?? null;
    $language = $_POST['language'] ?? null;
    $pages = $_POST['pages'] ?? null;
    $price = $_POST['price'];
    $stock = $_POST['stock'] ?? 0;
    $discount = $_POST['discount'] ?? null;
    $format = $_POST['format'] ?? 'Paperback';
    $bestseller = isset($_POST['bestseller']) ? 1 : 0;
    $uploaded_by = 1; // Example admin ID

    // Handle file upload
    $cover_image = null;
    if (!empty($_FILES['cover_image']['name'])) {
        $target_dir = "uploads/";
        $cover_image = $target_dir . basename($_FILES['cover_image']['name']);
        if (!move_uploaded_file($_FILES['cover_image']['tmp_name'], $cover_image)) {
            echo "Error uploading file.";
            exit;
        }
    }

    // Insert into database
    $stmt = $conn->prepare(
        "INSERT INTO books (title, author, genre, description, isbn, publisher, publication_date, language, pages, price, stock, cover_image, discount, format, bestseller, uploaded_by) 
         VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
    );
    $stmt->bind_param(
        "ssssssssiddssdii",
        $title, $author, $genre, $description, $isbn, $publisher, $publication_date, $language, $pages, $price, $stock, $cover_image, $discount, $format, $bestseller, $uploaded_by
    );

    if ($stmt->execute()) {
        echo "Book uploaded successfully!";
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
