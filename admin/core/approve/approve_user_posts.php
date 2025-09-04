<?php
//approve_user_posts.php
session_start();
require_once(__DIR__ . '/../../config/db_connection.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: https://sanixtech.in/login.php');
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
            $post_id = intval($_POST['post_id']);
            $action = $_POST['action'] ?? null;
            $comments = $_POST['comments'] ?? '';
            $admin_id = $_SESSION['user_id'];
            
            if ($action === 'approve') {
                // Update post status
                $sql = "UPDATE posts SET status = 'approved' WHERE post_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $post_id);
                
                if ($stmt->execute()) {
                    // Get post creator for coin reward
                    $creator_sql = "SELECT createdby, title FROM posts WHERE post_id = ?";
                    $creator_stmt = $conn->prepare($creator_sql);
                    $creator_stmt->bind_param("i", $post_id);
                    $creator_stmt->execute();
                    $creator_result = $creator_stmt->get_result();
                    $post_data = $creator_result->fetch_assoc();
                    $creator_stmt->close();
                    
                    if ($post_data) {
                        $user_id = $post_data['createdby'];
                        $post_title = $post_data['title'];
                        
                        // Award coin to user
                        $coin_sql = "INSERT INTO user_coins (user_id, coins) VALUES (?, 1)
                                    ON DUPLICATE KEY UPDATE coins = coins + 1";
                        $coin_stmt = $conn->prepare($coin_sql);
                        $coin_stmt->bind_param("i", $user_id);
                        $coin_stmt->execute();
                        $coin_stmt->close();
                        
                        $response['success'] = true;
                        $response['message'] = 'Post approved successfully! 1 Sanix coin awarded to user.';
                        $response['action'] = 'approved';
                        $response['data']['post_title'] = $post_title;
                    } else {
                        $response['message'] = 'Post approved, but could not award coins.';
                    }
                } else {
                    $response['message'] = 'Failed to approve post.';
                }
                $stmt->close();
                
            } elseif ($action === 'reject') {
                $sql = "UPDATE posts SET status = 'rejected' WHERE post_id = ?";
                $stmt = $conn->prepare($sql);
                $stmt->bind_param("i", $post_id);
                
                if ($stmt->execute()) {
                    $response['success'] = true;
                    $response['message'] = 'Post rejected successfully.';
                    $response['action'] = 'rejected';
                } else {
                    $response['message'] = 'Failed to reject post.';
                }
                $stmt->close();
            }
            
            // Add to approval history if successful
            if ($response['success']) {
                $history_stmt = $conn->prepare("INSERT INTO post_approval_history (post_id, admin_id, action, comments, created_at) VALUES (?, ?, ?, ?, NOW())");
                if ($history_stmt) {
                    $history_stmt->bind_param("iiss", $post_id, $admin_id, $action, $comments);
                    $history_stmt->execute();
                    $history_stmt->close();
                }
            }
            
        } catch (Exception $e) {
            $response['message'] = 'Error: ' . $e->getMessage();
        }
        
        echo json_encode($response);
        exit();
    }
    
    // Check for new pending posts
    if (isset($_GET['check_new'])) {
        $count_query = "SELECT COUNT(*) as count FROM posts WHERE status = 'pending'";
        $count_result = $conn->query($count_query);
        $count = $count_result ? $count_result->fetch_assoc()['count'] : 0;
        
        echo json_encode(['new_count' => $count]);
        exit();
    }
}

// Handle regular form submissions (fallback)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['ajax_action'])) {
    $post_id = intval($_POST['post_id']);
    $action = $_POST['action'] ?? null;
    $comments = $_POST['comments'] ?? '';
    
    try {
        if ($action === 'approve') {
            $sql = "UPDATE posts SET status = 'approved' WHERE post_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $post_id);
            $stmt->execute();
            
            // Award coin to user
            $creator_sql = "SELECT createdby FROM posts WHERE post_id = ?";
            $creator_stmt = $conn->prepare($creator_sql);
            $creator_stmt->bind_param("i", $post_id);
            $creator_stmt->execute();
            $creator_result = $creator_stmt->get_result();
            $creator_data = $creator_result->fetch_assoc();
            $creator_stmt->close();
            
            if ($creator_data) {
                $coin_sql = "INSERT INTO user_coins (user_id, coins) VALUES (?, 1)
                            ON DUPLICATE KEY UPDATE coins = coins + 1";
                $coin_stmt = $conn->prepare($coin_sql);
                $coin_stmt->bind_param("i", $creator_data['createdby']);
                $coin_stmt->execute();
                $coin_stmt->close();
            }
            
            $message = "<div class='alert alert-success'><i class='fas fa-check-circle'></i> Post approved successfully! 1 Sanix coin awarded.</div>";
            $stmt->close();
            
        } elseif ($action === 'reject') {
            $sql = "UPDATE posts SET status = 'rejected' WHERE post_id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("i", $post_id);
            $stmt->execute();
            $message = "<div class='alert alert-warning'><i class='fas fa-exclamation-triangle'></i> Post rejected.</div>";
            $stmt->close();
        }
        
    } catch (Exception $e) {
        $message = "<div class='alert alert-danger'><i class='fas fa-exclamation-circle'></i> Error: " . $e->getMessage() . "</div>";
    }
}

// Fetch pending posts
$pending_query = "
    SELECT 
        p.post_id,
        p.title,
        p.description,
        p.createdby,
        p.createdat,
        p.category_id,
        p.subcategory_id,
        COALESCE(c.category_name, 'Unknown Category') as category_name,
        COALESCE(s.subcategory_name, 'Unknown Subcategory') as subcategory_name,
        COALESCE(u.username, CONCAT('User #', p.createdby)) as creator_name,
        u.email as creator_email
    FROM posts p
    LEFT JOIN categories c ON p.category_id = c.category_id
    LEFT JOIN subcategories s ON p.subcategory_id = s.subcategory_id
    LEFT JOIN users u ON p.createdby = u.user_id
    WHERE p.status = 'pending'
    ORDER BY p.createdat DESC
";

$pending_result = $conn->query($pending_query);
if (!$pending_result) {
    die("Query failed: " . $conn->error);
}

// Fetch recently approved/rejected posts
$recent_query = "
    SELECT 
        p.post_id,
        p.title,
        p.status,
        p.createdat,
        COALESCE(u.username, CONCAT('User #', p.createdby)) as creator_name,
        COALESCE(ah.created_at, p.createdat) as action_date,
        COALESCE(admin_u.username, 'System') as approver_name
    FROM posts p 
    LEFT JOIN users u ON p.createdby = u.user_id
    LEFT JOIN post_approval_history ah ON p.post_id = ah.post_id
    LEFT JOIN users admin_u ON ah.admin_id = admin_u.user_id
    WHERE p.status IN ('approved', 'rejected') 
    ORDER BY COALESCE(ah.created_at, p.createdat) DESC
    LIMIT 10
";

$recent_result = $conn->query($recent_query);
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Approvals - Admin Dashboard</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../css/admin_styleone.css">
    
    <style>
        .post-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .post-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
            border-color: rgba(13, 110, 253, 0.5);
        }
        
        .category-badge {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 0.5rem 0.75rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
        }
        
        .subcategory-badge {
            background: rgba(118, 75, 162, 0.2);
            color: #bb86fc;
            padding: 0.25rem 0.5rem;
            border-radius: 15px;
            font-size: 0.75rem;
            margin-left: 0.5rem;
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

        .post-card.processing {
            opacity: 0.7;
            pointer-events: none;
        }

        .post-card.fade-out {
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
        
        .user-info {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            padding: 1rem;
            margin-top: 1rem;
        }
        
        .description-text {
            max-height: 150px;
            overflow-y: auto;
            line-height: 1.6;
            background: rgba(255, 255, 255, 0.02);
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }
        
        .status-badge {
            font-size: 0.875rem;
            padding: 0.5rem 0.75rem;
        }
    </style>
</head>

<body>
<div class="wrapper">
    <!-- Sidebar -->
    <aside id="sidebar" class="js-sidebar">
        <?php include '../../admin_menu.php'; ?>
    </aside>
    
    <!-- Main Content -->
    <div class="main">
        <!-- Top Navigation -->
        <?php include '../../admin_navbar.php'; ?>
        
        <main class="content px-3 py-4">
            <div class="container-fluid">
                <!-- Page Header -->
                <div class="page-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1">
                                <i class="fas fa-clipboard-check me-2"></i>Post Approvals
                            </h2>
                            <p class="text-muted mb-0">Review and manage user post submissions</p>
                        </div>
                        <div>
                            <a href="manage_posts.php" class="btn btn-outline-info me-2">
                                <i class="fas fa-file-alt me-2"></i>Manage Posts
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
                            Pending Post Approvals 
                            <span class="badge bg-warning text-dark ms-2" id="pending-count"><?php echo $pending_result->num_rows; ?></span>
                        </h5>
                    </div>
                    <div class="card-body">
                        <div id="pending-posts-container">
                            <?php if ($pending_result->num_rows > 0): ?>
                                <?php while ($post = $pending_result->fetch_assoc()): ?>
                                    <div class="post-card p-4" data-post-id="<?php echo $post['post_id']; ?>">
                                        <div class="row">
                                            <div class="col-lg-8">
                                                <div class="d-flex align-items-start justify-content-between mb-3">
                                                    <div class="flex-grow-1">
                                                        <h5 class="mb-2 text-white post-title">
                                                            <?php echo htmlspecialchars($post['title']); ?>
                                                        </h5>
                                                        <div class="mb-2">
                                                            <span class="category-badge"><?php echo htmlspecialchars($post['category_name']); ?></span>
                                                            <span class="subcategory-badge"><?php echo htmlspecialchars($post['subcategory_name']); ?></span>
                                                        </div>
                                                    </div>
                                                    <div class="text-muted">
                                                        <i class="fas fa-calendar me-1"></i>
                                                        <?php echo isset($post['createdat']) ? date('M d, Y H:i', strtotime($post['createdat'])) : 'Unknown'; ?>
                                                    </div>
                                                </div>
                                                
                                                <div class="description-text">
                                                    <strong class="text-info d-block mb-2">Description:</strong>
                                                    <?php echo nl2br(htmlspecialchars($post['description'])); ?>
                                                </div>
                                                
                                                <div class="user-info">
                                                    <div class="d-flex align-items-center">
                                                        <i class="fas fa-user-circle fa-2x text-info me-3"></i>
                                                        <div>
                                                            <strong class="text-white">Created by: <?php echo htmlspecialchars($post['creator_name']); ?></strong>
                                                            <?php if (!empty($post['creator_email'])): ?>
                                                                <br><small class="text-muted"><?php echo htmlspecialchars($post['creator_email']); ?></small>
                                                            <?php endif; ?>
                                                            <br><small class="text-muted">User ID: <?php echo $post['createdby']; ?></small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="col-lg-4">
                                                <form method="POST" class="mb-3 approval-form">
                                                    <input type="hidden" name="post_id" value="<?php echo $post['post_id']; ?>">
                                                    <input type="hidden" name="ajax_action" value="1">
                                                    <div class="mb-3">
                                                        <label class="form-label text-info">Admin Comments</label>
                                                        <textarea class="form-control comments-field" name="comments" 
                                                                  placeholder="Optional comments about the approval/rejection" 
                                                                  rows="3"></textarea>
                                                    </div>
                                                    <div class="d-grid gap-2">
                                                        <button type="submit" name="action" value="approve" 
                                                                class="btn btn-approve approve-btn">
                                                            <i class="fas fa-check me-2"></i>Approve & Award Coin
                                                        </button>
                                                        <button type="submit" name="action" value="reject" 
                                                                class="btn btn-reject reject-btn">
                                                            <i class="fas fa-times me-2"></i>Reject Post
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <div class="empty-state">
                                    <i class="fas fa-check-circle text-success"></i>
                                    <h4 class="text-success">All Caught Up!</h4>
                                    <p class="text-muted">No posts are waiting for approval.</p>
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
                                        <th><i class="fas fa-file-alt me-2"></i>Post Title</th>
                                        <th><i class="fas fa-user me-2"></i>Created By</th>
                                        <th><i class="fas fa-flag me-2"></i>Status</th>
                                        <th><i class="fas fa-user-check me-2"></i>Reviewed By</th>
                                        <th><i class="fas fa-calendar me-2"></i>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($recent = $recent_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars(substr($recent['title'], 0, 50)) . (strlen($recent['title']) > 50 ? '...' : ''); ?></td>
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
                                                $date_field = $recent['action_date'];
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
// Enhanced AJAX form submission with real-time post removal
document.querySelectorAll('.approval-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault(); // Prevent default form submission
        
        const submitBtn = e.submitter;
        const postCard = form.closest('.post-card');
        const postId = form.querySelector('input[name="post_id"]').value;
        const action = submitBtn.value;
        const comments = form.querySelector('textarea[name="comments"]').value.trim();
        const postTitle = postCard.querySelector('.post-title').textContent.trim();
        
        // Confirmation for rejection
        if (action === 'reject' && !confirm('Are you sure you want to reject this post?\n\nThis action cannot be undone.')) {
            return;
        }
        
        // Set processing state
        postCard.classList.add('processing');
        
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
        formData.append('post_id', postId);
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
                const actionText = action === 'approve' ? 'approved and coin awarded' : 'rejected';
                const actionColor = action === 'approve' ? 'success' : 'warning';
                showToast(actionColor, `Post "${postTitle}" has been ${actionText} successfully!`);
                
                // Animate post card removal
                setTimeout(() => {
                    fadeOutPostCard(postCard);
                    updatePendingCount(-1);
                }, 500);
                
            } else {
                // Show error message
                showToast('error', data.message || 'An error occurred while processing the request.');
                
                // Reset form state
                postCard.classList.remove('processing');
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
                form.querySelectorAll('input, textarea, button').forEach(el => el.disabled = false);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('error', 'Network error occurred. Please try again.');
            
            // Reset form state
            postCard.classList.remove('processing');
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

// Function to fade out post card after approval/rejection
function fadeOutPostCard(postCard) {
    postCard.classList.add('fade-out');
    setTimeout(() => {
        if (postCard.parentNode) {
            postCard.remove();
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
        document.title = `Post Approvals (${newCount}) - Admin Dashboard`;
    }
}

// Check if no pending posts remain and show empty state
function checkIfEmpty() {
    const container = document.getElementById('pending-posts-container');
    const postCards = container.querySelectorAll('.post-card');
    
    if (postCards.length === 0) {
        container.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-check-circle text-success"></i>
                <h4 class="text-success">All Caught Up!</h4>
                <p class="text-muted">No posts are waiting for approval.</p>
                <button class="btn btn-outline-info mt-3" onclick="location.reload()">
                    <i class="fas fa-refresh me-2"></i>Check for New Posts
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
                           placeholder="Search pending posts..." id="post-search">
                    <button class="btn btn-outline-secondary" type="button" onclick="clearSearch()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
    pageHeader.appendChild(searchBox);
    
    // Add event listener
    document.getElementById('post-search').addEventListener('input', function() {
        filterPosts(this.value);
    });
}

function filterPosts(searchTerm) {
    const postCards = document.querySelectorAll('.post-card');
    searchTerm = searchTerm.toLowerCase();
    let visibleCount = 0;
    
    postCards.forEach(card => {
        const title = card.querySelector('.post-title').textContent.toLowerCase();
        const description = card.querySelector('.description-text').textContent.toLowerCase();
        const creator = card.querySelector('.user-info').textContent.toLowerCase();
        
        if (title.includes(searchTerm) || description.includes(searchTerm) || creator.includes(searchTerm)) {
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
    const searchInput = document.getElementById('post-search');
    searchInput.value = '';
    
    // Show all post cards
    document.querySelectorAll('.post-card').forEach(card => {
        card.style.display = 'block';
    });
    
    // Reset count
    const originalCount = document.querySelectorAll('.post-card').length;
    document.getElementById('pending-count').textContent = originalCount;
}

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Escape to clear search
    if (e.key === 'Escape') {
        const searchInput = document.getElementById('post-search');
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

// Auto-refresh functionality to check for new posts
function checkForNewPosts() {
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
            showNewPostsNotification(data.new_count - currentCount);
        }
    })
    .catch(error => console.log('Auto-refresh check failed:', error));
}

function showNewPostsNotification(count) {
    showToast('info', `${count} new post${count > 1 ? 's' : ''} waiting for approval! Click to refresh.`);
    
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
    // Animate post cards on load
    const postCards = document.querySelectorAll('.post-card');
    postCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
    
    // Add search box if there are many posts
    if (postCards.length > 3) {
        addSearchBox();
    }
    
    // Set up auto-refresh every 60 seconds
    setInterval(checkForNewPosts, 60000);
    
    // Update page title with pending count
    const pendingCount = document.getElementById('pending-count').textContent;
    document.title = `Post Approvals (${pendingCount}) - Admin Dashboard`;
});

// Handle page visibility change to refresh when tab becomes active
document.addEventListener('visibilitychange', function() {
    if (!document.hidden) {
        // Page became visible, check for new posts
        setTimeout(checkForNewPosts, 1000);
    }
});

// Handle browser back button
window.addEventListener('popstate', function(event) {
    location.reload();
});

// Prevent accidental page navigation
window.addEventListener('beforeunload', function(e) {
    const processingCards = document.querySelectorAll('.post-card.processing');
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

// Database table creation helper (for development)
function createApprovalHistoryTable() {
    // This would be handled on the backend, but leaving as reference
    const createTableSQL = `
        CREATE TABLE IF NOT EXISTS post_approval_history (
            id INT AUTO_INCREMENT PRIMARY KEY,
            post_id INT NOT NULL,
            admin_id INT NOT NULL,
            action ENUM('approved', 'rejected') NOT NULL,
            comments TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (post_id) REFERENCES posts(post_id),
            FOREIGN KEY (admin_id) REFERENCES users(user_id)
        );
    `;
    console.log('Execute this SQL to create approval history table:', createTableSQL);
}

// Call on first load if needed
if (document.getElementById('pending-count').textContent === '0' && document.querySelector('.empty-state')) {
    console.log('Consider creating approval history table for better tracking');
}
</script>

</body>
</html>