<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include '../db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$message = '';

// Debug: Check database connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Handle AJAX requests
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    header('Content-Type: application/json');
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_action'])) {
        $response = ['success' => false, 'message' => '', 'data' => []];
        
        try {
            $job_post_id = intval($_POST['job_post_id']);
            $action = $_POST['action'] ?? null;
            $comments = $_POST['comments'] ?? '';
            $admin_id = $_SESSION['user_id'];
            
            if ($action === 'approve') {
                $sql = "UPDATE job_post SET status = 'approved', is_approved = 1, approved_by = ?, approved_at = NOW() WHERE job_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("ii", $admin_id, $job_post_id);
                
                if ($stmt->execute()) {
                    $response['success'] = true;
                    $response['message'] = 'Job post approved successfully!';
                    $response['action'] = 'approved';
                } else {
                    $response['message'] = 'Failed to approve job post.';
                }
                $stmt->close();
                
            } elseif ($action === 'reject') {
                $sql = "UPDATE job_post SET status = 'rejected', is_approved = 0, approved_by = ?, approved_at = NOW(), rejection_reason = ? WHERE job_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("isi", $admin_id, $comments, $job_post_id);
                
                if ($stmt->execute()) {
                    $response['success'] = true;
                    $response['message'] = 'Job post rejected successfully!';
                    $response['action'] = 'rejected';
                } else {
                    $response['message'] = 'Failed to reject job post.';
                }
                $stmt->close();
            }
            
            // Add to approval history if successful
            if ($response['success']) {
                $history_stmt = $conn->prepare("INSERT INTO job_approval_history (job_post_id, admin_id, action, comments, created_at) VALUES (?, ?, ?, ?, NOW())");
                if ($history_stmt) {
                    $action_db = $action === 'approve' ? 'approved' : 'rejected';
                    $history_stmt->bind_param("iiss", $job_post_id, $admin_id, $action_db, $comments);
                    $history_stmt->execute();
                    $history_stmt->close();
                }
                
                // Get job details for notification
                $job_stmt = $conn->prepare("SELECT title, created_by FROM job_post WHERE job_id = ?");
                if ($job_stmt) {
                    $job_stmt->bind_param("i", $job_post_id);
                    $job_stmt->execute();
                    $job_result = $job_stmt->get_result();
                    $job = $job_result->fetch_assoc();
                    $job_stmt->close();
                    
                    if ($job) {
                        $response['data']['job_title'] = $job['title'];
                        
                        // Notify the job creator (only if notification table exists)
                        $notification_check = $conn->query("SHOW TABLES LIKE 'admin_notifications'");
                        if ($notification_check && $notification_check->num_rows > 0) {
                            $notify_message = $action === 'approve' ? 
                                "Your job post \"{$job['title']}\" has been approved and is now live!" : 
                                "Your job post \"{$job['title']}\" was rejected. Reason: " . $comments;
                                
                            $notify_stmt = $conn->prepare("INSERT INTO admin_notifications (admin_id, job_post_id, message, created_at) VALUES (?, ?, ?, NOW())");
                            if ($notify_stmt) {
                                $notify_stmt->bind_param("iis", $job['created_by'], $job_post_id, $notify_message);
                                $notify_stmt->execute();
                                $notify_stmt->close();
                            }
                        }
                    }
                }
            }
            
        } catch (Exception $e) {
            $response['message'] = 'Error: ' . $e->getMessage();
        }
        
        echo json_encode($response);
        exit();
    }
    
    // Check for new pending jobs
    if (isset($_GET['check_new'])) {
        $count_query = "SELECT COUNT(*) as count FROM job_post WHERE (status = 'pending' OR (is_approved = 0 AND (status IS NULL OR status = '' OR status NOT IN ('approved', 'rejected')))) AND is_approved != 1 AND (status IS NULL OR status != 'approved')";
        $count_result = $conn->query($count_query);
        $count = $count_result ? $count_result->fetch_assoc()['count'] : 0;
        
        echo json_encode(['new_count' => $count]);
        exit();
    }
}

// Handle regular form submissions (fallback)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['ajax_action'])) {
    $job_post_id = intval($_POST['job_post_id']);
    $action = $_POST['action'] ?? null;
    $comments = $_POST['comments'] ?? '';
    $admin_id = $_SESSION['user_id'];
    
    try {
        if ($action === 'approve') {
            $sql = "UPDATE job_post SET status = 'approved', is_approved = 1, approved_by = ?, approved_at = NOW() WHERE job_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ii", $admin_id, $job_post_id);
            $stmt->execute();
            $message = "<div class='alert alert-success'><i class='fas fa-check-circle'></i> Job post approved successfully!</div>";
            $stmt->close();
            
        } elseif ($action === 'reject') {
            $sql = "UPDATE job_post SET status = 'rejected', is_approved = 0, approved_by = ?, approved_at = NOW(), rejection_reason = ? WHERE job_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("isi", $admin_id, $comments, $job_post_id);
            $stmt->execute();
            $message = "<div class='alert alert-warning'><i class='fas fa-exclamation-triangle'></i> Job post rejected.</div>";
            $stmt->close();
        }
        
        // Add to approval history
        if ($action) {
            $history_stmt = $conn->prepare("INSERT INTO job_approval_history (job_post_id, admin_id, action, comments, created_at) VALUES (?, ?, ?, ?, NOW())");
            if ($history_stmt) {
                $action_db = $action === 'approve' ? 'approved' : 'rejected';
                $history_stmt->bind_param("iiss", $job_post_id, $admin_id, $action_db, $comments);
                $history_stmt->execute();
                $history_stmt->close();
            }
        }
        
    } catch (Exception $e) {
        $message = "<div class='alert alert-danger'><i class='fas fa-exclamation-circle'></i> Error: " . $e->getMessage() . "</div>";
    }
}

// Fetch pending job posts
$pending_query = "
    SELECT 
        jp.job_id,
        jp.title,
        jp.role, 
        jp.location,
        jp.description,
        jp.created_by,
        jp.salary_range,
        jp.employment_type,
        jp.company_logo,
        jp.job_images,
        jp.email_to,
        jp.company_name,
        jp.experience_level,
        jp.application_deadline,
        jp.skills_required,
        jp.created_at,
        u.email as creator_name,
        u.email as creator_email
    FROM job_post jp 
    LEFT JOIN users u ON jp.created_by = u.user_id
    WHERE (
        (jp.status = 'pending') 
        OR 
        (jp.is_approved = 0 AND (jp.status IS NULL OR jp.status = '' OR jp.status NOT IN ('approved', 'rejected')))
    )
    AND jp.is_approved != 1
    AND (jp.status IS NULL OR jp.status != 'approved')
    ORDER BY jp.created_at DESC
";

$pending_result = $conn->query($pending_query);
if (!$pending_result) {
    die("Query failed: " . $conn->error);
}

// Fetch recently approved/rejected jobs
$recent_query = "
    SELECT 
        jp.job_id,
        jp.title,
        jp.status,
        jp.approved_at,
        jp.approved_by,
        jp.created_by,
        u.email as creator_name,
        a.email as approver_name
    FROM job_post jp 
    LEFT JOIN users u ON jp.created_by = u.user_id
    LEFT JOIN users a ON jp.approved_by = a.user_id
    WHERE jp.status IN ('approved', 'rejected') 
    ORDER BY jp.approved_at DESC
    LIMIT 10
";

$recent_result = $conn->query($recent_query);
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Approvals - Admin Dashboard</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/admin_styleone.css">
    
    <style>
        .job-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .job-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
            border-color: rgba(13, 110, 253, 0.5);
        }
        
        .job-images img {
            max-width: 80px;
            max-height: 60px;
            margin: 5px;
            border-radius: 8px;
            border: 2px solid rgba(255, 255, 255, 0.1);
            transition: transform 0.2s;
        }
        
        .job-images img:hover {
            transform: scale(1.1);
        }
        
        .company-logo {
            max-width: 60px;
            max-height: 60px;
            border-radius: 8px;
            border: 2px solid rgba(255, 255, 255, 0.1);
        }
        
        .status-badge {
            font-size: 0.875rem;
            padding: 0.5rem 0.75rem;
        }
        
        .page-header {
            background: linear-gradient(135deg, rgba(13, 110, 253, 0.1), rgba(102, 16, 242, 0.1));
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .approval-card {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
        }
        
        .approval-card .card-header {
            background: linear-gradient(135deg, rgba(255, 193, 7, 0.2), rgba(255, 193, 7, 0.1));
            border-bottom: 1px solid rgba(255, 193, 7, 0.3);
            border-radius: 12px 12px 0 0 !important;
        }
        
        .recent-card .card-header {
            background: linear-gradient(135deg, rgba(13, 202, 240, 0.2), rgba(13, 202, 240, 0.1));
            border-bottom: 1px solid rgba(13, 202, 240, 0.3);
        }
        
        .btn-approve {
            background: linear-gradient(135deg, #198754, #20c997);
            border: none;
            transition: all 0.3s ease;
        }
        
        .btn-approve:hover {
            background: linear-gradient(135deg, #157347, #1aa179);
            transform: translateY(-1px);
        }
        
        .btn-reject {
            background: linear-gradient(135deg, #dc3545, #fd7e14);
            border: none;
            transition: all 0.3s ease;
        }
        
        .btn-reject:hover {
            background: linear-gradient(135deg, #b02a37, #e8681b);
            transform: translateY(-1px);
        }
        
        .table-dark {
            background: rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }

        .job-card.processing {
            opacity: 0.7;
            pointer-events: none;
        }

        .job-card.fade-out {
            animation: fadeSlideOut 0.6s ease-out forwards;
        }

        @keyframes fadeSlideOut {
            0% {
                opacity: 1;
                transform: translateY(0) scale(1);
                max-height: 500px;
            }
            50% {
                opacity: 0.5;
                transform: translateY(-10px) scale(0.98);
            }
            100% {
                opacity: 0;
                transform: translateY(-20px) scale(0.95);
                max-height: 0;
                margin: 0;
                padding: 0;
                border: none;
            }
        }

        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1060;
        }
    </style>
</head>

<body>
<div class="wrapper">
    <!-- Sidebar -->
    <aside id="sidebar" class="js-sidebar">
        <?php include 'admin_menu.php'; ?>
    </aside>
    
    <!-- Main Content -->
    <div class="main">
        <!-- Top Navigation -->
        <?php include 'admin_navbar.php'; ?>
        
        <main class="content px-3 py-4">
            <div class="container-fluid">
                <!-- Page Header -->
                <div class="page-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1">
                                <i class="fas fa-clipboard-check me-2"></i>Job Approvals
                            </h2>
                            <p class="text-muted mb-0">Review and manage job post approvals</p>
                        </div>
                        <div>
                            <a href="admin_post_job.php" class="btn btn-success me-2">
                                <i class="fas fa-plus me-2"></i>New Job Post
                            </a>
                            <a href="manage_jobs.php" class="btn btn-outline-info me-2">
                                <i class="fas fa-briefcase me-2"></i>Manage Jobs
                            </a>
                            <a href="dashboard.php" class="btn btn-outline-secondary">
                                <i class="fas fa-home me-2"></i>Dashboard
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Messages -->
                <?php if (!empty($message)) echo $message; ?>

                <!-- Toast Container for notifications -->
                <div class="toast-container"></div>

                <!-- Pending Approvals Section -->
                <div class="approval-card card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0 text-warning">
                            <i class="fas fa-clock me-2"></i>
                            Pending Approvals 
                            <span class="badge bg-warning text-dark ms-2" id="pending-count"><?php echo $pending_result->num_rows; ?></span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="pending-jobs-container">
                            <?php if ($pending_result->num_rows > 0): ?>
                                <?php while ($job = $pending_result->fetch_assoc()): ?>
                                    <div class="job-card p-4" data-job-id="<?php echo $job['job_id']; ?>">
                                        <div class="row">
                                            <div class="col-lg-8">
                                                <div class="d-flex align-items-start mb-3">
                                                    <?php if (isset($job['company_logo']) && $job['company_logo']): ?>
                                                        <img src="../<?php echo htmlspecialchars($job['company_logo']); ?>" 
                                                             class="company-logo me-3" alt="Company Logo">
                                                    <?php endif; ?>
                                                    <div class="flex-grow-1">
                                                        <h5 class="mb-2 text-white job-title">
                                                            <?php echo htmlspecialchars($job['title']); ?>
                                                        </h5>
                                                        <div class="text-muted mb-2">
                                                            <i class="fas fa-user me-1"></i>
                                                            <?php echo htmlspecialchars($job['creator_name'] ?? 'Unknown'); ?> |
                                                            <i class="fas fa-calendar ms-3 me-1"></i>
                                                            <?php echo isset($job['created_at']) ? date('M d, Y', strtotime($job['created_at'])) : 'Unknown'; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                
                                                <div class="row mb-3">
                                                    <div class="col-md-6">
                                                        <div class="mb-2">
                                                            <strong class="text-info">Role:</strong> 
                                                            <span class="text-light"><?php echo htmlspecialchars($job['role']); ?></span>
                                                        </div>
                                                        <div class="mb-2">
                                                            <strong class="text-info">Location:</strong> 
                                                            <span class="text-light"><?php echo htmlspecialchars($job['location']); ?></span>
                                                        </div>
                                                        <?php if (isset($job['salary_range']) && $job['salary_range']): ?>
                                                            <div class="mb-2">
                                                                <strong class="text-info">Salary:</strong> 
                                                                <span class="text-light"><?php echo htmlspecialchars($job['salary_range']); ?></span>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <?php if (isset($job['employment_type'])): ?>
                                                            <div class="mb-2">
                                                                <strong class="text-info">Type:</strong> 
                                                                <span class="badge bg-secondary"><?php echo ucfirst($job['employment_type']); ?></span>
                                                            </div>
                                                        <?php endif; ?>
                                                        <?php if (isset($job['experience_level']) && $job['experience_level']): ?>
                                                            <div class="mb-2">
                                                                <strong class="text-info">Experience:</strong> 
                                                                <span class="text-light"><?php echo htmlspecialchars($job['experience_level']); ?></span>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                
                                                <div class="mb-3">
                                                    <strong class="text-info">Description:</strong>
                                                    <p class="text-light mt-1">
                                                        <?php echo nl2br(htmlspecialchars(substr($job['description'], 0, 300))); ?>
                                                        <?php if (strlen($job['description']) > 300) echo '<span class="text-muted">...</span>'; ?>
                                                    </p>
                                                </div>
                                                
                                                <?php if (isset($job['job_images']) && $job['job_images']): ?>
                                                    <?php $images = json_decode($job['job_images'], true); ?>
                                                    <?php if ($images && is_array($images)): ?>
                                                        <div class="job-images mb-3">
                                                            <strong class="text-info d-block mb-2">Images:</strong>
                                                            <?php foreach ($images as $image): ?>
                                                                <img src="../<?php echo htmlspecialchars($image); ?>" alt="Job Image" class="me-2 mb-2">
                                                            <?php endforeach; ?>
                                                        </div>
                                                    <?php endif; ?>
                                                <?php endif; ?>
                                            </div>
                                            
                                            <div class="col-lg-4">
                                                <form method="POST" class="mb-3 approval-form">
                                                    <input type="hidden" name="job_post_id" value="<?php echo $job['job_id']; ?>">
                                                    <input type="hidden" name="ajax_action" value="1">
                                                    <div class="mb-3">
                                                        <label class="form-label text-info">Comments</label>
                                                        <textarea class="form-control comments-field" name="comments" 
                                                                  placeholder="Add comments (required for rejection)" 
                                                                  rows="3"></textarea>
                                                    </div>
                                                    <div class="d-grid gap-2">
                                                        <button type="submit" name="action" value="approve" 
                                                                class="btn btn-approve approve-btn">
                                                            <i class="fas fa-check me-2"></i>Approve Job
                                                        </button>
                                                        <button type="submit" name="action" value="reject" 
                                                                class="btn btn-reject reject-btn">
                                                            <i class="fas fa-times me-2"></i>Reject Job
                                                        </button>
                                                    </div>
                                                </form>
                                                
                                                <button class="btn btn-outline-info w-100" type="button" 
                                                        data-bs-toggle="collapse" 
                                                        data-bs-target="#details-<?php echo $job['job_id']; ?>">
                                                    <i class="fas fa-eye me-2"></i>View Full Details
                                                </button>
                                            </div>
                                        </div>
                                        
                                        <!-- Collapsible full details -->
                                        <div class="collapse mt-4" id="details-<?php echo $job['job_id']; ?>">
                                            <div class="border-top border-secondary pt-4">
                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <?php if (isset($job['email_to'])): ?>
                                                            <div class="mb-2">
                                                                <strong class="text-info">Contact Email:</strong><br>
                                                                <span class="text-light"><?php echo htmlspecialchars($job['email_to']); ?></span>
                                                            </div>
                                                        <?php endif; ?>
                                                        <?php if (isset($job['company_name']) && $job['company_name']): ?>
                                                            <div class="mb-2">
                                                                <strong class="text-info">Company:</strong><br>
                                                                <span class="text-light"><?php echo htmlspecialchars($job['company_name']); ?></span>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <?php if (isset($job['application_deadline']) && $job['application_deadline']): ?>
                                                            <div class="mb-2">
                                                                <strong class="text-info">Application Deadline:</strong><br>
                                                                <span class="text-light"><?php echo date('M d, Y', strtotime($job['application_deadline'])); ?></span>
                                                            </div>
                                                        <?php endif; ?>
                                                        <?php if (isset($job['skills_required']) && $job['skills_required']): ?>
                                                            <div class="mb-2">
                                                                <strong class="text-info">Required Skills:</strong><br>
                                                                <span class="text-light"><?php echo nl2br(htmlspecialchars($job['skills_required'])); ?></span>
                                                            </div>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                                <div class="mt-3">
                                                    <strong class="text-info">Complete Description:</strong>
                                                    <div class="mt-2 p-3 rounded" style="background: rgba(255, 255, 255, 0.02);">
                                                        <?php echo nl2br(htmlspecialchars($job['description'])); ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="empty-state">
                                    <i class="fas fa-check-circle text-success"></i>
                                    <h4 class="text-success">All Caught Up!</h4>
                                    <p class="text-muted">No job posts are waiting for approval.</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <!-- Recent Actions Section -->
                <?php if ($recent_result && $recent_result->num_rows > 0): ?>
                <div class="recent-card card">
                    <div class="card-header">
                        <h5 class="mb-0 text-info">
                            <i class="fas fa-history me-2"></i>Recent Actions
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-dark table-hover">
                                <thead>
                                    <tr>
                                        <th><i class="fas fa-briefcase me-2"></i>Job Title</th>
                                        <th><i class="fas fa-user me-2"></i>Created By</th>
                                        <th><i class="fas fa-flag me-2"></i>Status</th>
                                        <th><i class="fas fa-user-check me-2"></i>Reviewed By</th>
                                        <th><i class="fas fa-calendar me-2"></i>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($recent = $recent_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($recent['title']); ?></td>
                                            <td><?php echo htmlspecialchars($recent['creator_name'] ?? 'Unknown'); ?></td>
                                            <td>
                                                <?php if ($recent['status'] === 'approved'): ?>
                                                    <span class="badge bg-success status-badge">
                                                        <i class="fas fa-check me-1"></i>Approved
                                                    </span>
                                                <?php else: ?>
                                                    <span class="badge bg-danger status-badge">
                                                        <i class="fas fa-times me-1"></i>Rejected
                                                    </span>
                                                <?php endif; ?>
                                            </td>
                                            <td><?php echo htmlspecialchars($recent['approver_name'] ?? 'System'); ?></td>
                                            <td>
                                                <?php 
                                                $date_field = $recent['approved_at'];
                                                echo $date_field ? date('M d, Y H:i', strtotime($date_field)) : 'Unknown';
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Enhanced AJAX form submission with real-time job removal
document.querySelectorAll('.approval-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent default form submission
        
        const submitBtn = e.submitter;
        const jobCard = form.closest('.job-card');
        const jobId = form.querySelector('input[name="job_post_id"]').value;
        const action = submitBtn.value;
        const comments = form.querySelector('textarea[name="comments"]').value.trim();
        const jobTitle = jobCard.querySelector('.job-title').textContent.trim();
        
        // Validation for rejection
        if (action === 'reject' && !comments) {
            showToast('error', 'Please provide a reason for rejecting this job post.');
            form.querySelector('textarea[name="comments"]').focus();
            return;
        }
        
        // Confirmation for rejection
        if (action === 'reject' && !confirm('Are you sure you want to reject this job post?\n\nReason: ' + comments)) {
            return;
        }
        
        // Set processing state
        jobCard.classList.add('processing');
        
        // Update button state
        const originalText = submitBtn.innerHTML;
        submitBtn.disabled = true;
        
        if (action === 'approve') {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Approving...';
        } else {
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Rejecting...';
        }
        
        // Disable all form elements
        form.querySelectorAll('input, textarea, button').forEach(el => el.disabled = true);
        
        // Prepare form data
        const formData = new FormData();
        formData.append('ajax_action', '1');
        formData.append('job_post_id', jobId);
        formData.append('action', action);
        formData.append('comments', comments);
        
        // Send AJAX request
        fetch(window.location.href, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                const actionText = action === 'approve' ? 'approved' : 'rejected';
                const actionColor = action === 'approve' ? 'success' : 'warning';
                showToast(actionColor, `Job "${jobTitle}" has been ${actionText} successfully!`);
                
                // Animate job card removal
                setTimeout(() => {
                    fadeOutJobCard(jobCard);
                    updatePendingCount(-1);
                }, 500);
                
            } else {
                // Show error message
                showToast('error', data.message || 'An error occurred while processing the request.');
                
                // Reset form state
                jobCard.classList.remove('processing');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                form.querySelectorAll('input, textarea, button').forEach(el => el.disabled = false);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('error', 'Network error occurred. Please try again.');
            
            // Reset form state
            jobCard.classList.remove('processing');
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
            form.querySelectorAll('input, textarea, button').forEach(el => el.disabled = false);
        });
    });
});

// Function to show toast notifications
function showToast(type, message) {
    const toastContainer = document.querySelector('.toast-container');
    
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `toast align-items-center text-white border-0 mb-2`;
    
    // Set background color based on type
    let bgColor = 'bg-primary';
    let icon = 'fas fa-info-circle';
    
    switch(type) {
        case 'success':
            bgColor = 'bg-success';
            icon = 'fas fa-check-circle';
            break;
        case 'error':
            bgColor = 'bg-danger';
            icon = 'fas fa-exclamation-circle';
            break;
        case 'warning':
            bgColor = 'bg-warning';
            icon = 'fas fa-exclamation-triangle';
            break;
    }
    
    toast.classList.add(bgColor);
    
    toast.innerHTML = `
        <div class="d-flex">
            <div class="toast-body">
                <i class="${icon} me-2"></i>${message}
            </div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    `;
    
    toastContainer.appendChild(toast);
    
    // Initialize and show toast
    const bsToast = new bootstrap.Toast(toast, { 
        delay: type === 'error' ? 8000 : 5000 
    });
    bsToast.show();
    
    // Remove from DOM when hidden
    toast.addEventListener('hidden.bs.toast', () => {
        toastContainer.removeChild(toast);
    });
}

// Function to fade out job card after approval/rejection
function fadeOutJobCard(jobCard) {
    jobCard.classList.add('fade-out');
    setTimeout(() => {
        if (jobCard.parentNode) {
            jobCard.remove();
            checkIfEmpty();
        }
    }, 600);
}

// Update pending count
function updatePendingCount(change) {
    const countElement = document.getElementById('pending-count');
    if (countElement) {
        const currentCount = parseInt(countElement.textContent);
        const newCount = Math.max(0, currentCount + change);
        countElement.textContent = newCount;
        
        // Update page title
        document.title = `Job Approvals (${newCount}) - Admin Dashboard`;
    }
}

// Check if no pending jobs remain and show empty state
function checkIfEmpty() {
    const container = document.getElementById('pending-jobs-container');
    const jobCards = container.querySelectorAll('.job-card');
    
    if (jobCards.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-check-circle text-success"></i>
                <h4 class="text-success">All Caught Up!</h4>
                <p class="text-muted">No job posts are waiting for approval.</p>
                <button class="btn btn-outline-info mt-3" onclick="location.reload()">
                    <i class="fas fa-refresh me-2"></i>Check for New Jobs
                </button>
            </div>
        `;
    }
}

// Auto-expand textareas as user types
document.querySelectorAll('textarea').forEach(textarea => {
    textarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });
});

// Image preview on click
document.querySelectorAll('.job-images img, .company-logo').forEach(img => {
    img.addEventListener('click', function() {
        // Create modal to show full-size image
        const modal = document.createElement('div');
        modal.className = 'modal fade';
        modal.innerHTML = `
            <div class="modal-dialog modal-lg">
                <div class="modal-content bg-dark">
                    <div class="modal-header border-secondary">
                        <h5 class="modal-title text-white">Image Preview</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body text-center">
                        <img src="${this.src}" class="img-fluid rounded" alt="Image Preview">
                    </div>
                </div>
            </div>
        `;
        
        document.body.appendChild(modal);
        const bsModal = new bootstrap.Modal(modal);
        bsModal.show();
        
        // Remove modal from DOM when closed
        modal.addEventListener('hidden.bs.modal', function() {
            document.body.removeChild(modal);
        });
    });
    
    // Add cursor pointer
    img.style.cursor = 'pointer';
});

// Search functionality
function addSearchBox() {
    const pageHeader = document.querySelector('.page-header');
    const searchBox = document.createElement('div');
    searchBox.className = 'mt-3';
    searchBox.innerHTML = `
        <div class="row">
            <div class="col-md-6">
                <div class="input-group">
                    <span class="input-group-text bg-dark border-secondary">
                        <i class="fas fa-search text-muted"></i>
                    </span>
                    <input type="text" class="form-control bg-dark border-secondary text-light" 
                           placeholder="Search pending jobs..." id="job-search">
                    <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    pageHeader.appendChild(searchBox);
    
    // Add event listener
    document.getElementById('job-search').addEventListener('input', function() {
        filterJobs(this.value);
    });
}

function filterJobs(searchTerm) {
    const jobCards = document.querySelectorAll('.job-card');
    searchTerm = searchTerm.toLowerCase();
    let visibleCount = 0;
    
    jobCards.forEach(card => {
        const title = card.querySelector('.job-title').textContent.toLowerCase();
        const role = card.querySelector('.text-light').textContent.toLowerCase();
        const description = card.querySelector('p').textContent.toLowerCase();
        
        if (title.includes(searchTerm) || role.includes(searchTerm) || description.includes(searchTerm)) {
            card.style.display = 'block';
            visibleCount++;
        } else {
            card.style.display = 'none';
        }
    });
    
    // Update visible count if searching
    if (searchTerm) {
        const countElement = document.getElementById('pending-count');
        if (countElement) {
            countElement.textContent = visibleCount;
        }
    }
}

function clearSearch() {
    const searchInput = document.getElementById('job-search');
    searchInput.value = '';
    
    // Show all job cards
    document.querySelectorAll('.job-card').forEach(card => {
        card.style.display = 'block';
    });
    
    // Reset count
    const originalCount = document.querySelectorAll('.job-card').length;
    document.getElementById('pending-count').textContent = originalCount;
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Escape to clear search
    if (e.key === 'Escape') {
        const searchInput = document.getElementById('job-search');
        if (searchInput && document.activeElement === searchInput) {
            clearSearch();
        }
    }
    
    // Ctrl/Cmd + R to refresh
    if ((e.ctrlKey || e.metaKey) && e.key === 'r') {
        e.preventDefault();
        location.reload();
    }
});

// Auto-refresh functionality to check for new jobs
function checkForNewJobs() {
    fetch(window.location.href + '?check_new=1', { 
        method: 'GET',
        headers: { 
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        const currentCount = parseInt(document.getElementById('pending-count').textContent);
        if (data.new_count > currentCount) {
            showNewJobsNotification(data.new_count - currentCount);
        }
    })
    .catch(error => console.log('Auto-refresh check failed:', error));
}

function showNewJobsNotification(count) {
    showToast('info', `${count} new job${count > 1 ? 's' : ''} waiting for approval! Click to refresh.`);
    
    // Add click listener to refresh
    setTimeout(() => {
        const refreshBtn = document.createElement('button');
        refreshBtn.className = 'btn btn-sm btn-primary mt-2';
        refreshBtn.innerHTML = '<i class="fas fa-refresh me-1"></i>Refresh Page';
        refreshBtn.onclick = () => location.reload();
        
        const lastToast = document.querySelector('.toast-container .toast:last-child .toast-body');
        if (lastToast) {
            lastToast.appendChild(refreshBtn);
        }
    }, 100);
}

// Page load animations and initialization
window.addEventListener('DOMContentLoaded', function() {
    // Animate job cards on load
    const jobCards = document.querySelectorAll('.job-card');
    jobCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
    
    // Add search box if there are many jobs
    if (jobCards.length > 3) {
        addSearchBox();
    }
    
    // Set up auto-refresh every 60 seconds
    setInterval(checkForNewJobs, 60000);
    
    // Update page title with pending count
    const pendingCount = document.getElementById('pending-count').textContent;
    document.title = `Job Approvals (${pendingCount}) - Admin Dashboard`;
});

// Handle page visibility change to refresh when tab becomes active
document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
        // Page became visible, check for new jobs
        setTimeout(checkForNewJobs, 1000);
    }
});

// Handle browser back button
window.addEventListener('popstate', function(event) {
    location.reload();
});

// Prevent accidental page navigation
window.addEventListener('beforeunload', function(e) {
    const processingCards = document.querySelectorAll('.job-card.processing');
    if (processingCards.length > 0) {
        e.preventDefault();
        e.returnValue = 'You have pending approval actions. Are you sure you want to leave?';
    }
});

// Add smooth scroll to top functionality
function addScrollToTop() {
    const scrollBtn = document.createElement('button');
    scrollBtn.className = 'btn btn-primary position-fixed';
    scrollBtn.style.cssText = 'bottom: 20px; right: 20px; z-index: 1000; border-radius: 50%; width: 50px; height: 50px; display: none;';
    scrollBtn.innerHTML = '<i class="fas fa-arrow-up"></i>';
    scrollBtn.onclick = () => window.scrollTo({ top: 0, behavior: 'smooth' });
    
    document.body.appendChild(scrollBtn);
    
    // Show/hide scroll button
    window.addEventListener('scroll', function() {
        if (window.scrollY > 300) {
            scrollBtn.style.display = 'block';
        } else {
            scrollBtn.style.display = 'none';
        }
    });
}

// Initialize scroll to top
addScrollToTop();
</script>

</body>
</html>