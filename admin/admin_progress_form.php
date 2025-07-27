<?php
// Include DB connection
include 'db_connection.php';

// Assume session is active and user_id is stored
session_start();
$user_id = $_SESSION['user_id']; // Change this if you're storing user info differently
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>User Progress Submission</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://kit.fontawesome.com/ae360af17e.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/admin_styleone.css" />
</head>
<body>
<div class="wrapper">
    <aside id="sidebar" class="js-sidebar">
        <?php include 'admin_menu.php'; ?>
    </aside>

    <div class="main">
        <?php include 'admin_navbar.php'; ?>

        <main class="content px-4 py-4">
            <div class="container bg-dark rounded shadow p-4">
                <h3 class="mb-4 text-info">Submit Work Progress</h3>
                <form method="post" action="submit_admin_progress.php">
                    <input type="hidden" name="user_id" value="<?= $user_id ?>">

                    <div class="mb-3">
                        <label class="form-label">Topic</label>
                        <input type="text" class="form-control" name="topic" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Duration (in minutes)</label>
                        <input type="number" class="form-control" name="duration_minutes" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Progress (%)</label>
                        <input type="number" class="form-control" name="progress_percent" min="0" max="100" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description of Work Done</label>
                        <textarea class="form-control" name="work_description" rows="4" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Date of Work</label>
                        <input type="date" class="form-control" name="date_of_work" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-select" name="status">
                            <option value="In Progress">In Progress</option>
                            <option value="Completed">Completed</option>
                            <option value="Blocked">Blocked</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Remarks (optional)</label>
                        <textarea class="form-control" name="remarks" rows="2"></textarea>
                    </div>

                    <div class="d-grid">
                        <button type="submit" class="btn btn-success btn-lg">Submit Progress</button>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
