<?php
session_start();
include 'db_connection.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is admin
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "Access denied. You must be an admin to view this page.";
    exit;
}

// Admin ID for denial record
$admin_id = $_SESSION['user_id']; // Assuming admin's ID is stored in session as user_id

// Handle approval and denial actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $testimonial_id = $_GET['id'];

    if ($_GET['action'] === 'approve') {
        $sql = "UPDATE testimonials SET approved = 1 WHERE testimonial_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $testimonial_id);
        if ($stmt->execute()) {
            echo "Testimonial approved.";
        } else {
            echo "Error: " . $conn->error;
        }
    } elseif ($_GET['action'] === 'deny') {
        // Step 1: Insert the denied testimonial into the denied_testimonials table
        $sql_insert = "INSERT INTO denied_testimonials (testimonial_id, user_id, name, comment, denied_by) 
                       SELECT t.testimonial_id, t.user_id, u.name, t.comment, ? 
                       FROM testimonials t 
                       JOIN users u ON t.user_id = u.user_id 
                       WHERE t.testimonial_id = ?";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("ii", $admin_id, $testimonial_id);

        if ($stmt_insert->execute()) {
            // Step 2: Delete the testimonial from the testimonials table
            $sql_delete = "DELETE FROM testimonials WHERE testimonial_id = ?";
            $stmt_delete = $conn->prepare($sql_delete);
            $stmt_delete->bind_param("i", $testimonial_id);

            if ($stmt_delete->execute()) {
                echo "Testimonial denied and moved to denied_testimonials.";
            } else {
                echo "Error deleting testimonial: " . $conn->error;
            }
        } else {
            echo "Error inserting into denied_testimonials: " . $conn->error;
        }
    }
}

// Query to retrieve all testimonials where approved = 0
$sql = "SELECT t.testimonial_id, u.name, t.comment, t.created_at 
        FROM testimonials t 
        JOIN users u ON t.user_id = u.user_id 
        WHERE t.approved = 0";
$result = $conn->query($sql);

// Check for SQL errors
if (!$result) {
    die("Error in SQL query: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard - Pending Testimonials</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" />
    <script src="https://kit.fontawesome.com/ae360af17e.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/admin_styleone.css" />
</head>
<body>

<div class="container mt-5">
    <h2>Pending Testimonials (Approved = 0)</h2>
    <?php if ($result->num_rows > 0): ?>
        <table class="table table-bordered">
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
