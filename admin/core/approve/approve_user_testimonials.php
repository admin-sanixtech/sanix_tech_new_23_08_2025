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

// Handle approval and denial actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $action = $_GET['action'];
    $testimonial_id = (int)$_GET['id'];
    
    if ($action === 'approve') {
        // Update testimonial to approved
        $update_sql = "UPDATE testimonials SET approved = 1, approved_by = ?, approved_at = NOW() WHERE testimonial_id = ?";
        $stmt = $conn->prepare($update_sql);
        if ($stmt) {
            $stmt->bind_param("ii", $admin_id, $testimonial_id);
            if ($stmt->execute()) {
                $success_message = "Testimonial approved successfully!";
            } else {
                $error_message = "Error approving testimonial: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error_message = "Error preparing statement: " . $conn->error;
        }
    } 
    elseif ($action === 'deny') {
        // Delete testimonial or mark as denied (depending on your preference)
        // Option 1: Delete the testimonial
        $delete_sql = "DELETE FROM testimonials WHERE testimonial_id = ?";
        $stmt = $conn->prepare($delete_sql);
        
        // Option 2: Mark as denied (uncomment if you prefer to keep denied records)
        /*
        $update_sql = "UPDATE testimonials SET approved = -1, denied_by = ?, denied_at = NOW() WHERE testimonial_id = ?";
        $stmt = $conn->prepare($update_sql);
        */
        
        if ($stmt) {
            $stmt->bind_param("i", $testimonial_id);
            // For option 2, use: $stmt->bind_param("ii", $admin_id, $testimonial_id);
            
            if ($stmt->execute()) {
                $success_message = "Testimonial denied and removed successfully!";
            } else {
                $error_message = "Error denying testimonial: " . $stmt->error;
            }
            $stmt->close();
        } else {
            $error_message = "Error preparing statement: " . $conn->error;
        }
    }
    
    // Redirect to prevent resubmission on refresh
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Query to retrieve all pending testimonials
$sql = "SELECT t.testimonial_id, u.name, t.comment, t.created_at 
        FROM testimonials t 
        JOIN users u ON t.user_id = u.user_id 
        WHERE t.approved = 0
        ORDER BY t.created_at DESC";
$result = $conn->query($sql);

if (!$result) {
    die("SQL Query Error: " . $conn->error);
}

// Fetch all results into an array to avoid pointer issues
$testimonials = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $testimonials[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Testimonials - Admin Panel</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="css/admin_styleone.css" />
    <style>
        .testimonial-comment {
            max-width: 300px;
            word-wrap: break-word;
        }
        .action-buttons {
            white-space: nowrap;
        }
        .alert {
            margin: 20px 0;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <aside id="sidebar" class="js-sidebar">
        <?php include 'admin_menu.php'; ?>
    </aside>
    <div class="main">
        <?php include 'admin_navbar.php'; ?>
        
        <div class="container-fluid px-4">
            <h2 class="mt-4 mb-4">Manage Pending Testimonials</h2>
            
            <!-- Success/Error Messages -->
            <?php if (isset($success_message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($success_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($error_message); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- Statistics -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5 class="card-title">Pending Testimonials</h5>
                            <h2><?php echo count($testimonials); ?></h2>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php if (count($testimonials) > 0): ?>
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Pending Testimonials (<?php echo count($testimonials); ?>)</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-striped mb-0">
                                <thead class="table-dark">
                                    <tr>
                                        <th>S.No</th>
                                        <th>User</th>
                                        <th>Comment</th>
                                        <th>Submitted</th>
                                        <th class="text-center">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php 
                                    $sno = 1;
                                    foreach ($testimonials as $row): ?>
                                        <tr>
                                            <td><?php echo $sno++; ?></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($row['name'] ?? 'Unknown User'); ?></strong>
                                            </td>
                                            <td class="testimonial-comment">
                                                <?php echo htmlspecialchars($row['comment'] ?? 'No comment'); ?>
                                            </td>
                                            <td>
                                                <?php 
                                                if (isset($row['created_at']) && $row['created_at']) {
                                                    $date = new DateTime($row['created_at']);
                                                    echo $date->format('M d, Y H:i'); 
                                                } else {
                                                    echo 'Unknown date';
                                                }
                                                ?>
                                            </td>
                                            <td class="text-center action-buttons">
                                                <a href="?action=approve&id=<?php echo $row['testimonial_id']; ?>" 
                                                   class="btn btn-success btn-sm me-2"
                                                   onclick="return confirm('Are you sure you want to approve this testimonial?')">
                                                    <i class="fas fa-check"></i> Approve
                                                </a>
                                                <a href="?action=deny&id=<?php echo $row['testimonial_id']; ?>" 
                                                   class="btn btn-danger btn-sm"
                                                   onclick="return confirm('Are you sure you want to deny and remove this testimonial? This action cannot be undone.')">
                                                    <i class="fas fa-times"></i> Deny
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            <?php else: ?>
                <div class="card">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <h4 class="text-muted">No Pending Testimonials</h4>
                        <p class="text-muted">All testimonials have been reviewed. Check back later for new submissions.</p>
                        <a href="view_approved_testimonials.php" class="btn btn-primary">
                            View Approved Testimonials
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js"></script>
<!-- Font Awesome for icons (optional) -->
<script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>

<script>
// Auto-dismiss alerts after 5 seconds
setTimeout(function() {
    var alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        var bsAlert = new bootstrap.Alert(alert);
        bsAlert.close();
    });
}, 5000);
</script>

</body>
</html>