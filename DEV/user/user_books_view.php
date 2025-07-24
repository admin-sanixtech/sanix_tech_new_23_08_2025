<?php
// db_connection.php (Include your existing database connection code)
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


include 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: https://sanixtech.in");
    exit;
  }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Books</title>
</head>
<body>
    <h1>Available Books</h1>

    <?php
    // Fetch books with a PDF format
    $sql = "SELECT id, title, author, genre, description, cover_image, price, format FROM books WHERE format='eBook'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "<div style='border: 1px solid #ccc; padding: 10px; margin: 10px;'>";
            echo "<h2>" . htmlspecialchars($row['title']) . "</h2>";
            echo "<p><strong>Author:</strong> " . htmlspecialchars($row['author']) . "</p>";
            echo "<p><strong>Genre:</strong> " . htmlspecialchars($row['genre']) . "</p>";
            echo "<p><strong>Description:</strong> " . htmlspecialchars($row['description']) . "</p>";
            echo "<p><strong>Price:</strong> $" . number_format($row['price'], 2) . "</p>";

            if (!empty($row['cover_image'])) {
                echo "<img src='" . htmlspecialchars($row['cover_image']) . "' alt='Cover Image' style='max-width: 150px;'><br>";
            }

            echo "<a href='view_pdf.php?id=" . $row['id'] . "' target='_blank'>Read Book</a>";
            echo "</div>";
        }
    } else {
        echo "<p>No books available.</p>";
    }

    $conn->close();
    ?>
</body>
</html>
