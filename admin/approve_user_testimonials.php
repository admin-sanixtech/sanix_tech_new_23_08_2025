<?php
session_start();
include 'db_connection.php';

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "Access denied. You must be an admin to view this page.";
    exit;
}

// Admin ID for denial record
$admin_id = $_SESSION['user_id'];

// Handle approval and denial actions (same as before)

// Query to retrieve all pending testimonials
$sql = "SELECT t.testimonial_id, u.name, t.comment, t.created_at 
        FROM testimonials t 
        JOIN users u ON t.user_id = u.user_id 
        WHERE t.approved = 0";
$result = $conn->query($sql);

if (!$result) {
    die("SQL Query Error: " . $conn->error);
}

echo "Number of rows: " . $result->num_rows; // Debugging output
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approve User Testimonials</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2>Pending Testimonials</h2>
    <?php if ($result->num_rows > 0): ?>
        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>Sno</th>
                    <th>User</th>
                    <th>Comment</th>
                    <th>Date of Submission</th>
                    <th>Approve</th>
                    <th>Deny</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $sno = 1;
                while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $sno++; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['comment']); ?></td>
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                        <td>
                            <a href="?action=approve&id=<?php echo $row['testimonial_id']; ?>" class="btn btn-success btn-sm">Approve</a>
                        </td>
                        <td>
                            <a href="?action=deny&id=<?php echo $row['testimonial_id']; ?>" class="btn btn-danger btn-sm">Deny</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No pending testimonials found.</p>
    <?php endif; ?>
</div>
</body>
</html>
