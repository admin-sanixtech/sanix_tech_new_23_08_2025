<?php
session_start();
include 'db_connection.php'; // Adjust the path as necessary

// Check if the user is admin
if ($_SESSION['role'] !== 'admin') {
    header("Location: index.php"); // Redirect if not admin
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $approved = $_POST['approve'];

    // Update approval status in the database
    $stmt = $conn->prepare("UPDATE user_books SET approved = ? WHERE id = ?");
    $stmt->bind_param("ii", $approved, $id);
    $stmt->execute();
}

$books = $conn->query("SELECT * FROM user_books WHERE approved = 0");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Approval for Books</title>
</head>
<body>
    <h1>Admin Approval for Books</h1>

    <h2>Pending Books</h2>
    <table>
        <tr>
            <th>Book ID</th>
            <th>User ID</th>
            <th>File</th>
            <th>Action</th>
        </tr>
        <?php while ($book = $books->fetch_assoc()): ?>
        <tr>
            <td><?= $book['id'] ?></td>
            <td><?= $book['user_id'] ?></td>
            <td><a href="<?= $book['file_path'] ?>">View</a></td>
            <td>
                <form method="post">
                    <input type="hidden" name="id" value="<?= $book['id'] ?>">
                    <button type="submit" name="approve" value="1">Approve</button>
                    <button type="submit" name="approve" value="0">Reject</button>
                </form>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
