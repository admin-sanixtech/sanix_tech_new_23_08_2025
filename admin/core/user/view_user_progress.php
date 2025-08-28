<?php
// Start session before any HTML output
session_start();
include '../db_connection.php';

// Enable error reporting for debugging (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Query to get user progress with user names
$query = "SELECT up.*, u.name 
          FROM user_progress up
          JOIN users u ON up.user_id = u.user_id
          ORDER BY up.date_of_work DESC";

// Fetch existing categories
$progress_result = $conn->query($query);
$user_progress = [];
if ($progress_result->num_rows > 0) {
    while ($row = $progress_result->fetch_assoc()) {
        $user_progress[] = $row;
    }
}
?>


<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8" />
    <title>User Progress Report</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="css/admin_styleone.css" />
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
                    <div class="content">
                        
                        <!-- Display Messages -->
                        <?php if (!empty($messages)) : ?>
                            <div class="alert alert-info">
                                <?php foreach ($messages as $message) : ?>
                                    <p><?php echo $message; ?></p>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Display Existing Categories -->
                        <h2>User Progress Report</h2>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>User Name</th>
                                    <th>Topic</th>
                                    <th>Duration (min)</th>
                                    <th>Progress (%)</th>
                                    <th>Work Description</th>
                                    <th>Date of Work</th>
                                    <th>Status</th>
                                    <th>Remarks</th>
                                </tr>
                            </thead>
                            <tbody>
    <?php if (!empty($user_progress)) : ?>
        <?php $i = 1; foreach($user_progress as $row): ?>
            <tr>
                <td><?= $i++ ?></td>
                <td><?= htmlspecialchars($row['name']) ?></td>
                <td><?= htmlspecialchars($row['topic']) ?></td>
                <td><?= htmlspecialchars($row['duration_minutes']) ?></td>
                <td><?= htmlspecialchars($row['progress_percent']) ?>%</td>
                <td><?= htmlspecialchars($row['work_description']) ?></td>
                <td><?= htmlspecialchars($row['date_of_work']) ?></td>
                <td><?= htmlspecialchars($row['status']) ?></td>
                <td><?= htmlspecialchars($row['remarks']) ?></td>
            </tr>
        <?php endforeach; ?>
    <?php else : ?>
        <tr><td colspan="9">No categories found.</td></tr>
    <?php endif; ?>
</tbody>

                        </table>

                        <hr>
                        <?php include 'admin_footer.php'; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
