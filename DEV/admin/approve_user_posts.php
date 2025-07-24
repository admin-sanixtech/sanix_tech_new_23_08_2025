<?php
session_start();
include 'db_connection.php'; // Database connection

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "Access denied. You must be an admin to view this page.";
    exit;
}

// Handle Approve and Deny actions
if (isset($_GET['action']) && isset($_GET['post_id'])) {
    $action = $_GET['action'];
    $post_id = $_GET['post_id'];
    $status = ($action === 'approve') ? 'approved' : 'rejected';

    $sql_update_post = "UPDATE posts SET status = ? WHERE post_id = ?";
    $stmt_post = $conn->prepare($sql_update_post);
    $stmt_post->bind_param("si", $status, $post_id);

    if ($stmt_post->execute()) {
        if ($status === 'approved') {
            // Fetch the user who created the post
            $sql_user = "SELECT createdby FROM posts WHERE post_id = ?";
            $stmt_user = $conn->prepare($sql_user);
            $stmt_user->bind_param("i", $post_id);
            $stmt_user->execute();
            $stmt_user->bind_result($user_id);
            $stmt_user->fetch();
            $stmt_user->close();

            // Increment the user's coins
            $sql_coins = "INSERT INTO user_coins (user_id, coins) VALUES (?, 1)
                          ON DUPLICATE KEY UPDATE coins = coins + 1";
            $stmt_coins = $conn->prepare($sql_coins);
            $stmt_coins->bind_param("i", $user_id);

            if ($stmt_coins->execute()) {
                $message = "<div class='alert alert-success'>Post approved, and 1 Sanix coin awarded to user.</div>";
            } else {
                $message = "<div class='alert alert-warning'>Post approved, but there was an error awarding coins: " . $conn->error . "</div>";
            }
        } else {
            $message = "<div class='alert alert-success'>Post has been " . htmlspecialchars($status) . " successfully.</div>";
        }
    } else {
        $message = "<div class='alert alert-danger'>Error updating post status: " . $conn->error . "</div>";
    }
}

// Fetch all pending posts
$sql = "SELECT p.post_id, c.category_name, s.subcategory_name, p.title, p.description, p.createdby, p.created_at 
        FROM posts p
        JOIN categories c ON p.category_id = c.category_id
        JOIN subcategories s ON p.subcategory_id = s.subcategory_id
        WHERE p.status = 'pending'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Approve User Posts</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" />
</head>
<body>
<div class="wrapper">
    <div class="container mt-5">
        <h2>Pending User Posts</h2>
        <?php if (!empty($message)) echo $message; ?>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>S. No</th>
                    <th>Category</th>
                    <th>Subcategory</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Created By</th>
                    <th>Created At</th>
                    <th>Approve</th>
                    <th>Deny</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result->num_rows > 0): ?>
                    <?php $sno = 1; ?>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $sno++; ?></td>
                            <td><?php echo htmlspecialchars($row['category_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['subcategory_name']); ?></td>
                            <td><?php echo htmlspecialchars($row['title']); ?></td>
                            <td><?php echo htmlspecialchars($row['description']); ?></td>
                            <td><?php echo htmlspecialchars($row['createdby']); ?></td>
                            <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                            <td>
                                <a href="approve_user_posts.php?action=approve&post_id=<?php echo $row['post_id']; ?>" class="btn btn-success btn-sm">Approve</a>
                            </td>
                            <td>
                                <a href="approve_user_posts.php?action=deny&post_id=<?php echo $row['post_id']; ?>" class="btn btn-danger btn-sm">Deny</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center">No pending posts found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
