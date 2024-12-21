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
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Approve User Questions</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" />
    <script src="https://kit.fontawesome.com/ae360af17e.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/admin_styleone.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css">

</head>
<body>
<div class="wrapper">
    <aside id="sidebar" class="js-sidebar">
        <?php include 'admin_menu.php'; ?>
    </aside>
    <div class="main">
        <?php include 'admin_navbar.php'; ?>
        <main class="content px-3 py-2">
            <div class="container-fluid">

                <div class="card border-0">
                    <div class="card-body">
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
