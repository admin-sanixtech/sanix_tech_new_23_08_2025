<?php
session_start();
require_once 'db_connection.php';

// Ensure admin access only
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    echo "Access denied. Admin only.";
    exit;
}

// Approve or Reject Logic
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['job_id'], $_POST['action'])) {
    $job_id = $_POST['job_id'];
    $action = $_POST['action'] === 'approve' ? 1 : 0;

    $update = $conn->prepare("UPDATE job_post SET is_approved = ? WHERE job_id = ?");
    $update->bind_param("ii", $action, $job_id);
    $update->execute();
}

$query = "SELECT job_post.*, users.name AS posted_by_name FROM job_post 
          LEFT JOIN users ON job_post.created_by = users.user_id 
          ORDER BY job_post.created_at DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>All Job Posts (Admin)</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container py-4">
    <h2>All Job Posts</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Title</th>
                <th>Email To</th>
                <th>Description</th>
                <th>Location</th>
                <th>Posted By</th>
                <th>Approved?</th>
                <th>Posted On</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= htmlspecialchars($row['email_to']) ?></td>
                <td><?= htmlspecialchars($row['description']) ?></td>
                <td><?= htmlspecialchars($row['location']) ?></td>
                <td><?= htmlspecialchars($row['posted_by_name']) ?></td>
                <td><?= $row['is_approved'] ? 'Yes' : 'Pending' ?></td>
                <td><?= $row['created_at'] ?></td>
                <td>
                    <?php if (!$row['is_approved']): ?>
                        <form method="post" style="display:inline;">
                            <input type="hidden" name="job_id" value="<?= $row['job_id'] ?>">
                            <button type="submit" name="action" value="approve" class="btn btn-success btn-sm">Approve</button>
                            <button type="submit" name="action" value="reject" class="btn btn-danger btn-sm">Reject</button>
                        </form>
                    <?php else: ?>
                        —
                    <?php endif; ?>
                </td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
