<?php
session_start();
include '../db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "Access denied.";
    exit();
}

if (isset($_GET['approve'])) {
    $job_id = intval($_GET['approve']);
    $conn->query("UPDATE job_post SET is_approved = 1 WHERE job_id = $job_id");
}

$result = $conn->query("SELECT * FROM job_post WHERE is_approved = 0");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Approve Jobs</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
</head>
<body class="p-4">
    <h3>Pending Job Approvals</h3>
    <?php while ($row = $result->fetch_assoc()): ?>
        <div class="border p-3 mb-3">
            <h5><?= htmlspecialchars($row['title']) ?> (<?= $row['role'] ?>)</h5>
            <p><?= nl2br(htmlspecialchars($row['description'])) ?></p>
            <p><b>Email:</b> <?= htmlspecialchars($row['email_to']) ?> | <b>Location:</b> <?= htmlspecialchars($row['location']) ?></p>
            <a href="?approve=<?= $row['job_id'] ?>" class="btn btn-success btn-sm">Approve</a>
        </div>
    <?php endwhile; ?>
</body>
</html>
