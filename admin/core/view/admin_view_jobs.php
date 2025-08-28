<?php
session_start();
include '../db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo "Access denied.";
    exit();
}

$message = '';

// Handle approval/rejection actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $job_post_id = $_POST['job_post_id'];
    $action = $_POST['action']; // 'approve' or 'reject'
    $comments = $_POST['comments'] ?? '';
    $admin_id = $_SESSION['user_id'];
    
    try {
        if ($action === 'approve') {
            // Update job post status to approved (using job_id as primary key)
            $stmt = $conn->prepare("UPDATE job_post SET status = 'approved', is_approved = 1, approved_by = ?, approved_at = NOW() WHERE job_id = ?");
            $stmt->bind_param("ii", $admin_id, $job_post_id);
            $stmt->execute();
            
            $message = "<div class='alert alert-success'>Job post approved successfully!</div>";
            
        } elseif ($action === 'reject') {
            // Update job post status to rejected (using job_id as primary key)
            $stmt = $conn->prepare("UPDATE job_post SET status = 'rejected', is_approved = 0, approved_by = ?, approved_at = NOW(), rejection_reason = ? WHERE job_id = ?");
            $stmt->bind_param("isi", $admin_id, $comments, $job_post_id);
            $stmt->execute();
            
            $message = "<div class='alert alert-warning'>Job post rejected.</div>";
        }
        
        // Add to approval history
        $history_stmt = $conn->prepare("INSERT INTO job_approval_history (job_post_id, admin_id, action, comments) VALUES (?, ?, ?, ?)");
        $action_db = $action === 'approve' ? 'approved' : 'rejected';
        $history_stmt->bind_param("iiss", $job_post_id, $admin_id, $action_db, $comments);
        $history_stmt->execute();
        
        // Get job details for notification (using job_id as primary key)
        $job_stmt = $conn->prepare("SELECT title, created_by FROM job_post WHERE job_id = ?");
        $job_stmt->bind_param("i", $job_post_id);
        $job_stmt->execute();
        $job_result = $job_stmt->get_result();
        $job = $job_result->fetch_assoc();
        
        // Notify the job creator
        $notify_message = $action === 'approve' ? 
            "Your job post \"{$job['title']}\" has been approved and is now live!" : 
            "Your job post \"{$job['title']}\" was rejected. Reason: " . $comments;
            
        $notify_stmt = $conn->prepare("INSERT INTO admin_notifications (admin_id, job_post_id, message) VALUES (?, ?, ?)");
        $notify_stmt->bind_param("iis", $job['created_by'], $job_post_id, $notify_message);
        $notify_stmt->execute();
        
    } catch (Exception $e) {
        $message = "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }
}

// Fetch pending job posts (using job_id as primary key)
$pending_query = "
    SELECT jp.*, u.username as creator_name, u.email as creator_email 
    FROM job_post jp 
    JOIN users u ON jp.created_by = u.id 
    WHERE jp.status = 'pending' OR (jp.is_approved = 0 AND jp.status IS NULL)
    ORDER BY jp.created_at DESC
";
$pending_result = $conn->query($pending_query);

// Fetch recently approved/rejected jobs for reference (using job_id as primary key)
$recent_query = "
    SELECT jp.*, u.username as creator_name, a.username as approver_name 
    FROM job_post jp 
    JOIN users u ON jp.created_by = u.id 
    LEFT JOIN users a ON jp.approved_by = a.id 
    WHERE jp.status IN ('approved', 'rejected') OR jp.is_approved = 1
    ORDER BY jp.approved_at DESC, jp.updated_at DESC
    LIMIT 10
";
$recent_result = $conn->query($recent_query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Job Approvals - Admin</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <style>
        .job-card {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            margin-bottom: 20px;
            transition: box-shadow 0.3s;
        }
        .job-card:hover {
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .job-images img {
            max-width: 80px;
            max-height: 60px;
            margin: 5px;
            border-radius: 4px;
        }
        .company-logo {
            max-width: 60px;
            max-height: 60px;
            border-radius: 4px;
        }
        .status-badge {
            font-size: 0.875rem;
        }
    </style>
</head>
<body class="bg-light">
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="fas fa-clipboard-check"></i> Job Approvals</h2>
            <div>
                <a href="admin_job_post.php" class="btn btn-success me-2">
                    <i class="fas fa-plus"></i> New Job Post
                </a>
                <a href="dashboard.php" class="btn btn-secondary">
                    <i class="fas fa-home"></i> Dashboard
                </a>
            </div>
        </div>

        <?php echo $message; ?>

        <!-- Pending Approvals Section -->
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
                <h5 class="mb-0"><i class="fas fa-clock"></i> Pending Approvals (<?php echo $pending_result->num_rows; ?>)</h5>
            </div>
            <div class="card-body">
                <?php if ($pending_result->num_rows > 0): ?>
                    <?php while ($job = $pending_result->fetch_assoc()): ?>
                        <div class="job-card p-3 bg-white">
                            <div class="row">
                                <div class="col-md-8">
                                    <div class="d-flex align-items-center mb-2">
                                        <?php if ($job['company_logo']): ?>
                                            <img src="../<?php echo $job['company_logo']; ?>" class="company-logo me-3" alt="Logo">
                                        <?php endif; ?>
                                        <div>
                                            <h6 class="mb-1"><?php echo htmlspecialchars($job['title']); ?></h6>
                                            <small class="text-muted">
                                                <i class="fas fa-user"></i> <?php echo htmlspecialchars($job['creator_name']); ?> | 
                                                <i class="fas fa-calendar"></i> <?php echo date('M d, Y', strtotime($job['created_at'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <strong>Role:</strong> <?php echo htmlspecialchars($job['role']); ?><br>
                                        <strong>Location:</strong> <?php echo htmlspecialchars($job['location']); ?><br>
                                        <?php if ($job['salary_range']): ?>
                                            <strong>Salary:</strong> <?php echo htmlspecialchars($job['salary_range']); ?><br>
                                        <?php endif; ?>
                                        <strong>Type:</strong> <?php echo ucfirst($job['employment_type']); ?>
                                    </div>
                                    
                                    <div class="mb-2">
                                        <strong>Description:</strong>
                                        <p class="mb-1"><?php echo nl2br(htmlspecialchars(substr($job['description'], 0, 200))); ?>
                                        <?php if (strlen($job['description']) > 200) echo '...'; ?></p>
                                    </div>
                                    
                                    <?php if ($job['job_images']): ?>
                                        <?php $images = json_decode($job['job_images'], true); ?>
                                        <?php if ($images): ?>
                                            <div class="job-images mb-2">
                                                <strong>Images:</strong><br>
                                                <?php foreach ($images as $image): ?>
                                                    <img src="../<?php echo $image; ?>" alt="Job Image">
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                
                                <div class="col-md-4">
                                    <form method="POST" class="mb-2">
                                        <input type="hidden" name="job_post_id" value="<?php echo $job['job_id']; ?>">
                                        <div class="mb-2">
                                            <textarea class="form-control" name="comments" placeholder="Comments (optional for approval, required for rejection)" rows="3"></textarea>
                                        </div>
                                        <div class="d-grid gap-2">
                                            <button type="submit" name="action" value="approve" class="btn btn-success">
                                                <i class="fas fa-check"></i> Approve
                                            </button>
                                            <button type="submit" name="action" value="reject" class="btn btn-danger" 
                                                    onclick="return confirm('Are you sure you want to reject this job post?')">
                                                <i class="fas fa-times"></i> Reject
                                            </button>
                                        </div>
                                    </form>
                                    <button class="btn btn-info btn-sm w-100" type="button" data-bs-toggle="collapse" data-bs-target="#details-<?php echo $job['job_id']; ?>">
                                        <i class="fas fa-eye"></i> View Full Details
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Collapsible full details -->
                            <div class="collapse mt-3" id="details-<?php echo $job['job_id']; ?>">
                                <div class="border-top pt-3">
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>Email:</strong> <?php echo htmlspecialchars($job['email_to']); ?><br>
                                            <strong>Company:</strong> <?php echo htmlspecialchars($job['company_name']); ?><br>
                                            <strong>Experience:</strong> <?php echo htmlspecialchars($job['experience_level']); ?><br>
                                        </div>
                                        <div class="col-md-6">
                                            <?php if ($job['application_deadline']): ?>
                                                <strong>Deadline:</strong> <?php echo date('M d, Y', strtotime($job['application_deadline'])); ?><br>
                                            <?php endif; ?>
                                            <strong>Skills:</strong> <?php echo htmlspecialchars($job['skills_required']); ?>
                                        </div>
                                    </div>
                                    <div class="mt-2">
                                        <strong>Full Description:</strong>
                                        <p><?php echo nl2br(htmlspecialchars($job['description'])); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <h5>No pending approvals!</h5>
                        <p class="text-muted">All job posts are up to date.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Actions Section -->
        <div class="card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-history"></i> Recent Actions</h5>
            </div>
            <div class="card-body">
                <?php if ($recent_result->num_rows > 0): ?>
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Job Title</th>
                                    <th>Created By</th>
                                    <th>Status</th>
                                    <th>Approved/Rejected By</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($recent = $recent_result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($recent['title']); ?></td>
                                        <td><?php echo htmlspecialchars($recent['creator_name']); ?></td>
                                        <td>
                                            <?php if ($recent['status'] === 'approved'): ?>
                                                <span class="badge bg-success status-badge">
                                                    <i class="fas fa-check"></i> Approved
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-danger status-badge">
                                                    <i class="fas fa-times"></i> Rejected
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo htmlspecialchars($recent['approver_name']); ?></td>
                                        <td><?php echo date('M d, Y H:i', strtotime($recent['approved_at'])); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-center text-muted">No recent actions found.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>