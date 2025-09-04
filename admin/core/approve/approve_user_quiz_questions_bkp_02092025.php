<?php
// approve_user_quiz_questions.php
session_start();
require_once(__DIR__ . '/../../config/db_connection.php');

// Enable error reporting for development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$message = '';
$items_per_page = 12;
$current_page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$offset = ($current_page - 1) * $items_per_page;

/**
 * Function to approve a question - copies it to main quiz_questions table
 */
function approveQuestion($conn, $pending_id, $admin_id, $notes = '') {
    try {
        // Start transaction
        $conn->begin_transaction();
        
        // Get the pending question
        $get_sql = "SELECT * FROM quiz_questions_pending WHERE pending_id = ? AND status = 'pending'";
        $get_stmt = $conn->prepare($get_sql);
        $get_stmt->bind_param("i", $pending_id);
        $get_stmt->execute();
        $question_data = $get_stmt->get_result()->fetch_assoc();
        $get_stmt->close();
        
        if (!$question_data) {
            throw new Exception("Question not found or already processed");
        }
        
        // Insert into main quiz_questions table
        $insert_sql = "INSERT INTO quiz_questions (
            question_text, question_content, question_type, correct_answer,
            option_a, option_a_content, option_b, option_b_content,
            option_c, option_c_content, option_d, option_d_content,
            code_snippet, category_id, subcategory_id, difficulty_level,
            description, answer_content, created_by
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $insert_stmt = $conn->prepare($insert_sql);
        $insert_stmt->bind_param("sssssssssssssiiissi",
            $question_data['question_text'],
            $question_data['question_content'],
            $question_data['question_type'],
            $question_data['correct_answer'],
            $question_data['option_a'],
            $question_data['option_a_content'],
            $question_data['option_b'],
            $question_data['option_b_content'],
            $question_data['option_c'],
            $question_data['option_c_content'],
            $question_data['option_d'],
            $question_data['option_d_content'],
            $question_data['code_snippet'],
            $question_data['category_id'],
            $question_data['subcategory_id'],
            $question_data['difficulty_level'],
            $question_data['description'],
            $question_data['answer_content'],
            $question_data['created_by']
        );
        
        if (!$insert_stmt->execute()) {
            throw new Exception("Failed to insert into quiz_questions table");
        }
        
        $new_question_id = $conn->insert_id;
        $insert_stmt->close();
        
        // Update pending status
        $update_sql = "UPDATE quiz_questions_pending SET 
            status = 'approved', 
            reviewed_at = CURRENT_TIMESTAMP, 
            reviewed_by = ?, 
            review_notes = ? 
            WHERE pending_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("isi", $admin_id, $notes, $pending_id);
        
        if (!$update_stmt->execute()) {
            throw new Exception("Failed to update pending question status");
        }
        $update_stmt->close();
        
        // Log the approval
        $log_sql = "INSERT INTO quiz_question_approvals (question_id, admin_id, action, notes) VALUES (?, ?, 'approved', ?)";
        $log_stmt = $conn->prepare($log_sql);
        $log_stmt->bind_param("iis", $new_question_id, $admin_id, $notes);
        $log_stmt->execute();
        $log_stmt->close();
        
        // Commit transaction
        $conn->commit();
        return true;
        
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        error_log("Approve question error: " . $e->getMessage());
        throw $e;
    }
}

/**
 * Function to reject a question
 */
function rejectQuestion($conn, $pending_id, $admin_id, $notes = '') {
    try {
        // Update pending status
        $update_sql = "UPDATE quiz_questions_pending SET 
            status = 'rejected', 
            reviewed_at = CURRENT_TIMESTAMP, 
            reviewed_by = ?, 
            review_notes = ? 
            WHERE pending_id = ? AND status = 'pending'";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("isi", $admin_id, $notes, $pending_id);
        
        if (!$update_stmt->execute() || $update_stmt->affected_rows === 0) {
            throw new Exception("Failed to reject question or question not found");
        }
        $update_stmt->close();
        
        // Log the rejection (using pending_id as question_id for rejected questions)
        $log_sql = "INSERT INTO quiz_question_approvals (question_id, admin_id, action, notes) VALUES (?, ?, 'rejected', ?)";
        $log_stmt = $conn->prepare($log_sql);
        $log_stmt->bind_param("iis", $pending_id, $admin_id, $notes);
        $log_stmt->execute();
        $log_stmt->close();
        
        return true;
        
    } catch (Exception $e) {
        error_log("Reject question error: " . $e->getMessage());
        throw $e;
    }
}

/**
 * Function to get full question details for review modal
 */
function getQuestionDetails($conn, $pending_id) {
    $sql = "SELECT 
        qp.*,
        COALESCE(c.category_name, 'Uncategorized') as category_name,
        COALESCE(sc.subcategory_name, '') as subcategory_name,
        COALESCE(u.username, u.username, 'Unknown User') as creator_name,
        u.email as creator_email
    FROM quiz_questions_pending qp
    LEFT JOIN categories c ON qp.category_id = c.category_id
    LEFT JOIN subcategories sc ON qp.subcategory_id = sc.subcategory_id
    LEFT JOIN users u ON qp.created_by = u.user_id
    WHERE qp.pending_id = ?";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $pending_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $question = $result->fetch_assoc();
    $stmt->close();
    
    return $question;
}

// Handle AJAX request for question details
if (isset($_GET['ajax']) && $_GET['ajax'] === 'get_question' && isset($_GET['id'])) {
    $question_id = intval($_GET['id']);
    $question = getQuestionDetails($conn, $question_id);
    
    if ($question) {
        header('Content-Type: application/json');
        echo json_encode($question);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Question not found']);
    }
    exit();
}

// Handle bulk actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['bulk_action'])) {
    $action = $_POST['bulk_action'];
    $question_ids = isset($_POST['selected_questions']) ? $_POST['selected_questions'] : [];
    
    if (!empty($question_ids) && in_array($action, ['approve', 'reject'])) {
        $success_count = 0;
        $error_count = 0;
        
        foreach ($question_ids as $question_id) {
            $question_id = intval($question_id);
            
            try {
                if ($action === 'approve') {
                    approveQuestion($conn, $question_id, $_SESSION['user_id']);
                } else {
                    rejectQuestion($conn, $question_id, $_SESSION['user_id']);
                }
                $success_count++;
            } catch (Exception $e) {
                $error_count++;
                error_log("Bulk action error for question $question_id: " . $e->getMessage());
            }
        }
        
        $action_word = ($action === 'approve') ? 'approved' : 'rejected';
        if ($success_count > 0) {
            $message = "success|Successfully $action_word $success_count question(s).";
        }
        if ($error_count > 0) {
            $message .= ($message ? " " : "") . "error|Failed to process $error_count question(s).";
        }
        
        header("Location: " . $_SERVER['PHP_SELF'] . "?msg=" . urlencode($message) . "&page=" . $current_page);
        exit();
    }
}

// Handle individual approve/reject actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['bulk_action'])) {
    $question_id = intval($_POST['question_id']);
    $action = $_POST['action'];
    $notes = isset($_POST['notes']) ? trim($_POST['notes']) : '';

    try {
        if ($action === 'approve') {
            approveQuestion($conn, $question_id, $_SESSION['user_id'], $notes);
            $message = "success|Question #$question_id approved successfully and added to main quiz database!";
        } elseif ($action === 'reject') {
            rejectQuestion($conn, $question_id, $_SESSION['user_id'], $notes);
            $message = "warning|Question #$question_id rejected successfully!";
        } else {
            throw new Exception("Invalid action specified");
        }
        
    } catch (Exception $e) {
        $message = "error|Error: " . $e->getMessage();
        error_log("Individual action error: " . $e->getMessage());
    }

    // Redirect to prevent form resubmission
    header("Location: " . $_SERVER['PHP_SELF'] . "?msg=" . urlencode($message) . "&page=" . $current_page);
    exit();
}

// Display message from redirect
if (isset($_GET['msg'])) {
    $msg_parts = explode('|', $_GET['msg'], 2);
    $msg_type = isset($msg_parts[0]) ? $msg_parts[0] : 'info';
    $msg_text = isset($msg_parts[1]) ? $msg_parts[1] : $_GET['msg'];
    
    $alert_class = match($msg_type) {
        'success' => 'alert-success',
        'warning' => 'alert-warning',
        'error' => 'alert-danger',
        default => 'alert-info'
    };
    
    $icon = match($msg_type) {
        'success' => 'fas fa-check-circle',
        'warning' => 'fas fa-exclamation-triangle',
        'error' => 'fas fa-exclamation-circle',
        default => 'fas fa-info-circle'
    };
    
    $message = "<div class='alert $alert_class alert-dismissible fade show'><i class='$icon me-2'></i>" . htmlspecialchars($msg_text) . "<button type='button' class='btn-close' data-bs-dismiss='alert'></button></div>";
}

// Get statistics
$stats_sql = "SELECT 
    COUNT(*) as total_questions,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count,
    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved_count,
    SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected_count
FROM quiz_questions_pending";
$stats_result = $conn->query($stats_sql);
$stats = $stats_result->fetch_assoc();

// Main query for pending questions with better error handling
$sql = "SELECT 
    qp.pending_id,
    qp.question_text,
    qp.question_content,
    qp.question_type,
    qp.correct_answer,
    qp.option_a,
    qp.option_a_content,
    qp.option_b,
    qp.option_b_content,
    qp.option_c,
    qp.option_c_content,
    qp.option_d,
    qp.option_d_content,
    qp.code_snippet,
    qp.difficulty_level,
    qp.description,
    qp.answer_content,
    qp.created_by,
    qp.created_at,
    qp.status,
    qp.category_id,
    qp.subcategory_id,
    COALESCE(c.category_name, 'Uncategorized') as category_name,
    COALESCE(sc.subcategory_name, '') as subcategory_name,
    COALESCE(u.username, u.username, 'Unknown User') as creator_name
FROM quiz_questions_pending qp
LEFT JOIN categories c ON qp.category_id = c.category_id
LEFT JOIN subcategories sc ON qp.subcategory_id = sc.subcategory_id
LEFT JOIN users u ON qp.created_by = u.user_id
WHERE qp.status = 'pending'
ORDER BY qp.created_at DESC
LIMIT ? OFFSET ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("ii", $items_per_page, $offset);
if (!$stmt->execute()) {
    die("Execute failed: " . $stmt->error);
}

$result = $stmt->get_result();

// Calculate total pages
$total_pages = ceil($stats['pending_count'] / $items_per_page);
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Questions - Admin Dashboard</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Admin Base CSS -->
    <link rel="stylesheet" href="../../css/admin_styleone.css">
    <!-- Custom CSS for this page -->
    <link rel="stylesheet" href="../../css/approve_user_quiz_questions_styles.css">
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
                                <i class="fas fa-question-circle me-2"></i>Pending Questions
                            </h2>
                            <p class="text-muted mb-0">Review and approve user-submitted quiz questions</p>
                        </div>
                        <div>
                            <span class="badge bg-warning text-dark fs-6">
                                <i class="fas fa-clock me-1"></i>
                                <?php echo $stats['pending_count']; ?> Pending
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="stats-card">
                            <i class="fas fa-database text-primary fs-2 mb-2"></i>
                            <h4 class="mb-1"><?php echo $stats['total_questions']; ?></h4>
                            <small class="text-muted">Total Questions</small>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stats-card">
                            <i class="fas fa-clock text-warning fs-2 mb-2"></i>
                            <h4 class="mb-1"><?php echo $stats['pending_count']; ?></h4>
                            <small class="text-muted">Pending Review</small>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stats-card">
                            <i class="fas fa-check-circle text-success fs-2 mb-2"></i>
                            <h4 class="mb-1"><?php echo $stats['approved_count']; ?></h4>
                            <small class="text-muted">Approved</small>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="stats-card">
                            <i class="fas fa-times-circle text-danger fs-2 mb-2"></i>
                            <h4 class="mb-1"><?php echo $stats['rejected_count']; ?></h4>
                            <small class="text-muted">Rejected</small>
                        </div>
                    </div>
                </div>

                <!-- Messages -->
                <?php if (!empty($message)) echo $message; ?>

                <?php if ($result->num_rows > 0): ?>
                <!-- Bulk Actions -->
                <form id="bulkForm" method="POST">
                    <div class="bulk-actions">
                        <div class="row align-items-center">
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <input type="checkbox" id="selectAll" class="form-check-input me-2">
                                    <label for="selectAll" class="form-check-label me-3">Select All</label>
                                    <span id="selectedCount" class="badge bg-info">0 selected</span>
                                </div>
                            </div>
                            <div class="col-md-6 text-md-end mt-2 mt-md-0">
                                <div class="btn-group" role="group">
                                    <button type="submit" name="bulk_action" value="approve" 
                                            class="btn btn-success btn-sm" id="bulkApprove" disabled>
                                        <i class="fas fa-check me-1"></i>Approve Selected
                                    </button>
                                    <button type="submit" name="bulk_action" value="reject" 
                                            class="btn btn-danger btn-sm" id="bulkReject" disabled>
                                        <i class="fas fa-times me-1"></i>Reject Selected
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Questions List -->
                    <div class="row">
                        <?php while ($row = $result->fetch_assoc()): 
                            // Safely get values with defaults
                            $question_id = intval($row['pending_id']);
                            $question_text = $row['question_text'] ?? 'No question text';
                            $question_content = $row['question_content'] ?? '';
                            $question_type = $row['question_type'] ?? 'unknown';
                            $difficulty_level = $row['difficulty_level'] ?? 'Unknown';
                            $category_name = $row['category_name'] ?? 'Uncategorized';
                            $subcategory_name = $row['subcategory_name'] ?? '';
                            $creator_name = $row['creator_name'] ?? 'Unknown';
                            $created_at = $row['created_at'] ?? date('Y-m-d H:i:s');
                            $code_snippet = $row['code_snippet'] ?? '';
                            
                            // Get options for multiple choice questions
                            $options = [];
                            if ($question_type === 'multiple_choice') {
                                $options = [
                                    'A' => ['text' => $row['option_a'] ?? '', 'content' => $row['option_a_content'] ?? ''],
                                    'B' => ['text' => $row['option_b'] ?? '', 'content' => $row['option_b_content'] ?? ''],
                                    'C' => ['text' => $row['option_c'] ?? '', 'content' => $row['option_c_content'] ?? ''],
                                    'D' => ['text' => $row['option_d'] ?? '', 'content' => $row['option_d_content'] ?? '']
                                ];
                            }
                        ?>
                            <div class="col-lg-6 col-xl-4 mb-4">
                                <div class="question-card card h-100" data-question-id="<?php echo $question_id; ?>">
                                    <!-- Selection Checkbox -->
                                    <div class="question-checkbox">
                                        <input type="checkbox" name="selected_questions[]" 
                                               value="<?php echo $question_id; ?>" 
                                               class="form-check-input question-select">
                                    </div>
                                    
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <div>
                                            <small class="text-muted">ID: <?php echo $question_id; ?></small>
                                            <span class="difficulty-badge badge bg-<?php 
                                                echo match($difficulty_level) {
                                                    'Beginner' => 'success',
                                                    'Intermediate' => 'warning', 
                                                    'Advanced' => 'danger',
                                                    'Expert' => 'dark',
                                                    default => 'secondary'
                                                }; 
                                            ?> ms-2">
                                                <?php echo htmlspecialchars($difficulty_level); ?>
                                            </span>
                                        </div>
                                        <span class="badge bg-<?php 
                                            echo match($question_type) {
                                                'multiple_choice' => 'primary',
                                                'true_false' => 'info',
                                                'short_answer' => 'success',
                                                'code' => 'warning',
                                                'paragraph' => 'secondary',
                                                default => 'secondary'
                                            }; 
                                        ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $question_type)); ?>
                                        </span>
                                    </div>
                                    
                                    <div class="card-body">
                                        <!-- Category Information -->
                                        <div class="mb-3">
                                            <small class="text-info">
                                                <i class="fas fa-folder me-1"></i>
                                                <?php echo htmlspecialchars($category_name); ?>
                                                <?php if (!empty($subcategory_name)): ?>
                                                    <i class="fas fa-angle-right mx-1"></i>
                                                    <?php echo htmlspecialchars($subcategory_name); ?>
                                                <?php endif; ?>
                                            </small>
                                        </div>
                                        
                                        <!-- Question Preview -->
                                        <div class="question-preview position-relative">
                                            <h6 class="card-title text-white mb-3">
                                                <?php 
                                                $preview_text = substr($question_text, 0, 100);
                                                echo nl2br(htmlspecialchars($preview_text)); 
                                                if (strlen($question_text) > 100): ?>
                                                    <span class="text-muted">...</span>
                                                <?php endif; ?>
                                            </h6>
                                            
                                            <!-- Show options for multiple choice -->
                                            <?php if ($question_type === 'multiple_choice' && !empty($options)): ?>
                                                <div class="options-preview mb-2">
                                                    <?php foreach ($options as $key => $option): ?>
                                                        <?php if (!empty($option['text']) || !empty($option['content'])): ?>
                                                            <div class="option-item small <?php echo $row['correct_answer'] === $key ? 'text-success fw-bold' : 'text-muted'; ?>">
                                                                <strong><?php echo $key; ?>:</strong> 
                                                                <?php 
                                                                $option_text = !empty($option['text']) ? $option['text'] : $option['content'];
                                                                echo htmlspecialchars(substr($option_text, 0, 50));
                                                                if (strlen($option_text) > 50) echo '...';
                                                                ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                    <small class="text-success"><i class="fas fa-check-circle me-1"></i>Correct: <?php echo $row['correct_answer']; ?></small>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <!-- Code snippet indicator -->
                                            <?php if (!empty($code_snippet)): ?>
                                                <div class="mb-2">
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="fas fa-code me-1"></i>Contains Code
                                                    </span>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                        <!-- Creator and Date -->
                                        <div class="text-muted small mb-3">
                                            <i class="fas fa-user me-1"></i>
                                            <?php echo htmlspecialchars($creator_name); ?>
                                            <br>
                                            <i class="fas fa-calendar me-1"></i>
                                            <?php echo date('M d, Y H:i', strtotime($created_at)); ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Action Buttons -->
                                    <div class="card-footer bg-transparent border-top-0 pt-0">
                                        <div class="d-grid gap-2">
                                            <button type="button" class="btn btn-outline-info btn-sm" 
                                                    onclick="showReviewModal(<?php echo $question_id; ?>)">
                                                <i class="fas fa-eye me-1"></i>Review Question
                                            </button>
                                            <div class="d-flex gap-2">
                                                <button type="button" class="btn btn-success btn-sm flex-fill" 
                                                        onclick="showApprovalModal(<?php echo $question_id; ?>, 'approve')">
                                                    <i class="fas fa-check me-1"></i>Approve
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm flex-fill" 
                                                        onclick="showApprovalModal(<?php echo $question_id; ?>, 'reject')">
                                                    <i class="fas fa-times me-1"></i>Reject
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    </div>
                </form>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <nav aria-label="Questions pagination" class="mt-4">
                    <ul class="pagination justify-content-center">
                        <li class="page-item <?php echo $current_page <= 1 ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo max(1, $current_page - 1); ?>">
                                <i class="fas fa-chevron-left"></i> Previous
                            </a>
                        </li>
                        
                        <?php 
                        $start_page = max(1, $current_page - 2);
                        $end_page = min($total_pages, $current_page + 2);
                        
                        if ($start_page > 1): ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=1">1</a>
                            </li>
                            <?php if ($start_page > 2): ?>
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>
                        
                        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                            <li class="page-item <?php echo $i === $current_page ? 'active' : ''; ?>">
                                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                        <?php endfor; ?>
                        
                        <?php if ($end_page < $total_pages): ?>
                            <?php if ($end_page < $total_pages - 1): ?>
                                <li class="page-item disabled">
                                    <span class="page-link">...</span>
                                </li>
                            <?php endif; ?>
                            <li class="page-item">
                                <a class="page-link" href="?page=<?php echo $total_pages; ?>"><?php echo $total_pages; ?></a>
                            </li>
                        <?php endif; ?>
                        
                        <li class="page-item <?php echo $current_page >= $total_pages ? 'disabled' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo min($total_pages, $current_page + 1); ?>">
                                Next <i class="fas fa-chevron-right"></i>
                            </a>
                        </li>
                    </ul>
                    
                    <div class="text-center mt-3">
                        <small class="text-muted">
                            Showing page <?php echo $current_page; ?> of <?php echo $total_pages; ?> 
                            (<?php echo $stats['pending_count']; ?> total questions)
                        </small>
                    </div>
                </nav>
                <?php endif; ?>

                <?php else: ?>
                    <!-- Empty State -->
                    <div class="empty-state text-center py-5">
                        <i class="fas fa-inbox text-muted mb-3" style="font-size: 4rem;"></i>
                        <h4 class="text-muted">No Pending Questions</h4>
                        <p class="text-muted">
                            <?php if ($stats['total_questions'] > 0): ?>
                                All questions have been reviewed. Great job!
                            <?php else: ?>
                                No questions have been submitted yet.
                            <?php endif; ?>
                        </p>
                        
                        <?php if ($stats['total_questions'] > 0): ?>
                            <div class="row justify-content-center mt-4">
                                <div class="col-md-8">
                                    <div class="row text-center">
                                        <div class="col-4">
                                            <div class="text-muted">
                                                <i class="fas fa-database fs-1 mb-2"></i>
                                                <div><?php echo $stats['total_questions']; ?></div>
                                                <small>Total</small>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="text-success">
                                                <i class="fas fa-check-circle fs-1 mb-2"></i>
                                                <div><?php echo $stats['approved_count']; ?></div>
                                                <small>Approved</small>
                                            </div>
                                        </div>
                                        <div class="col-4">
                                            <div class="text-danger">
                                                <i class="fas fa-times-circle fs-1 mb-2"></i>
                                                <div><?php echo $stats['rejected_count']; ?></div>
                                                <small>Rejected</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <a href="dashboard.php" class="btn btn-outline-primary mt-4">
                            <i class="fas fa-home me-2"></i>Back to Dashboard
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<!-- Review Modal -->
<div class="modal fade review-modal" id="reviewModal" tabindex="-1" aria-labelledby="reviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="reviewModalLabel">
                    <i class="fas fa-eye me-2"></i>Review Question
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="reviewContent">
                    <div class="text-center p-4">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-3 text-muted">Loading question details...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="fas fa-times me-1"></i>Close
                </button>
                <button type="button" class="btn btn-danger" id="reviewReject">
                    <i class="fas fa-times me-1"></i>Reject
                </button>
                <button type="button" class="btn btn-success" id="reviewApprove">
                    <i class="fas fa-check me-1"></i>Approve
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Approval/Rejection Modal -->
<div class="modal fade notes-modal" id="approvalModal" tabindex="-1" aria-labelledby="approvalModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">Approve Question</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="approvalForm" method="POST">
                <div class="modal-body">
                    <input type="hidden" id="modalQuestionId" name="question_id">
                    <input type="hidden" id="modalAction" name="action">
                    
                    <p id="modalMessage">Are you sure you want to approve this question?</p>
                    
                    <div class="mb-3">
                        <label for="modalNotes" class="form-label">
                            <i class="fas fa-sticky-note me-1"></i>Notes (Optional)
                        </label>
                        <textarea class="form-control" id="modalNotes" name="notes" rows="3" 
                                placeholder="Add any notes about your decision..."></textarea>
                        <div class="form-text">These notes will be saved with your review decision.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-success" id="modalConfirmBtn">
                        <i class="fas fa-check me-1"></i>Approve Question
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// JavaScript for handling the approval interface
document.addEventListener('DOMContentLoaded', function() {
    // Handle select all functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    const questionCheckboxes = document.querySelectorAll('.question-select');
    const selectedCountBadge = document.getElementById('selectedCount');
    const bulkApproveBtn = document.getElementById('bulkApprove');
    const bulkRejectBtn = document.getElementById('bulkReject');
    
    function updateSelectedCount() {
        const checkedBoxes = document.querySelectorAll('.question-select:checked');
        const count = checkedBoxes.length;
        
        selectedCountBadge.textContent = count + ' selected';
        bulkApproveBtn.disabled = count === 0;
        bulkRejectBtn.disabled = count === 0;
        
        // Update select all checkbox state
        selectAllCheckbox.indeterminate = count > 0 && count < questionCheckboxes.length;
        selectAllCheckbox.checked = count === questionCheckboxes.length && count > 0;
    }
    
    selectAllCheckbox.addEventListener('change', function() {
        questionCheckboxes.forEach(cb => cb.checked = this.checked);
        updateSelectedCount();
    });
    
    questionCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateSelectedCount);
    });
    
    // Handle bulk form submission
    document.getElementById('bulkForm').addEventListener('submit', function(e) {
        const checkedBoxes = document.querySelectorAll('.question-select:checked');
        if (checkedBoxes.length === 0) {
            e.preventDefault();
            alert('Please select at least one question.');
            return;
        }
        
        const action = e.submitter.value;
        const actionText = action === 'approve' ? 'approve' : 'reject';
        
        if (!confirm(`Are you sure you want to ${actionText} ${checkedBoxes.length} question(s)?`)) {
            e.preventDefault();
        }
    });
});

// Global variable to store current question ID for modal actions
let currentQuestionId = null;

// Function to show review modal
function showReviewModal(questionId) {
    currentQuestionId = questionId;
    const modal = new bootstrap.Modal(document.getElementById('reviewModal'));
    const reviewContent = document.getElementById('reviewContent');
    
    // Show loading state
    reviewContent.innerHTML = `
        <div class="text-center p-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-3 text-muted">Loading question details...</p>
        </div>
    `;
    
    modal.show();
    
    // Load question details via AJAX
    fetch(`?ajax=get_question&id=${questionId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                reviewContent.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Error: ${data.error}
                    </div>
                `;
                return;
            }
            
            // Build the question display
            let optionsHtml = '';
            if (data.question_type === 'multiple_choice') {
                const options = [
                    { key: 'A', text: data.option_a, content: data.option_a_content },
                    { key: 'B', text: data.option_b, content: data.option_b_content },
                    { key: 'C', text: data.option_c, content: data.option_c_content },
                    { key: 'D', text: data.option_d, content: data.option_d_content }
                ];
                
                optionsHtml = `
                    <div class="mb-4">
                        <h6><i class="fas fa-list me-2"></i>Answer Options:</h6>
                        <div class="row">
                            ${options.map(opt => {
                                if (!opt.text && !opt.content) return '';
                                const isCorrect = data.correct_answer === opt.key;
                                return `
                                    <div class="col-md-6 mb-2">
                                        <div class="card ${isCorrect ? 'border-success' : ''}">
                                            <div class="card-body py-2">
                                                <div class="d-flex align-items-start">
                                                    <span class="badge ${isCorrect ? 'bg-success' : 'bg-secondary'} me-2">${opt.key}</span>
                                                    <div class="flex-grow-1">
                                                        ${opt.text || opt.content || ''}
                                                        ${isCorrect ? '<i class="fas fa-check-circle text-success ms-2"></i>' : ''}
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            }).join('')}
                        </div>
                    </div>
                `;
            }
            
            reviewContent.innerHTML = `
                <div class="row">
                    <div class="col-md-8">
                        <!-- Question Details -->
                        <div class="mb-4">
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <span class="badge bg-primary me-2">${data.question_type.replace('_', ' ').toUpperCase()}</span>
                                    <span class="badge bg-warning text-dark">${data.difficulty_level || 'Unknown'}</span>
                                </div>
                                <small class="text-muted">ID: ${data.pending_id}</small>
                            </div>
                            
                            <h5 class="mb-3">${data.question_text || 'No question text'}</h5>
                            
                            ${data.question_content ? `
                                <div class="alert alert-info">
                                    <h6><i class="fas fa-info-circle me-2"></i>Additional Content:</h6>
                                    <div>${data.question_content}</div>
                                </div>
                            ` : ''}
                            
                            ${data.code_snippet ? `
                                <div class="mb-3">
                                    <h6><i class="fas fa-code me-2"></i>Code Snippet:</h6>
                                    <pre class="bg-dark p-3 rounded"><code>${data.code_snippet}</code></pre>
                                </div>
                            ` : ''}
                        </div>
                        
                        ${optionsHtml}
                        
                        ${data.description ? `
                            <div class="mb-4">
                                <h6><i class="fas fa-file-alt me-2"></i>Description:</h6>
                                <p class="text-muted">${data.description}</p>
                            </div>
                        ` : ''}
                        
                        ${data.answer_content ? `
                            <div class="mb-4">
                                <h6><i class="fas fa-lightbulb me-2"></i>Answer Explanation:</h6>
                                <div class="alert alert-success">
                                    ${data.answer_content}
                                </div>
                            </div>
                        ` : ''}
                    </div>
                    
                    <div class="col-md-4">
                        <!-- Metadata -->
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Question Metadata</h6>
                            </div>
                            <div class="card-body">
                                <div class="mb-3">
                                    <strong>Category:</strong><br>
                                    <span class="text-info">${data.category_name || 'Uncategorized'}</span>
                                    ${data.subcategory_name ? `<br><small class="text-muted">${data.subcategory_name}</small>` : ''}
                                </div>
                                
                                <div class="mb-3">
                                    <strong>Created by:</strong><br>
                                    <span class="text-info">${data.creator_name || 'Unknown'}</span>
                                    ${data.creator_email ? `<br><small class="text-muted">${data.creator_email}</small>` : ''}
                                </div>
                                
                                <div class="mb-3">
                                    <strong>Submitted:</strong><br>
                                    <small class="text-muted">${new Date(data.created_at).toLocaleString()}</small>
                                </div>
                                
                                ${data.question_type === 'multiple_choice' ? `
                                    <div class="mb-3">
                                        <strong>Correct Answer:</strong><br>
                                        <span class="badge bg-success">${data.correct_answer}</span>
                                    </div>
                                ` : ''}
                                
                                <div class="mb-0">
                                    <strong>Status:</strong><br>
                                    <span class="badge bg-warning text-dark">${data.status.toUpperCase()}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            `;
        })
        .catch(error => {
            console.error('Error loading question details:', error);
            reviewContent.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Error loading question details. Please try again.
                </div>
            `;
        });
}

// Function to show approval/rejection modal
function showApprovalModal(questionId, action) {
    currentQuestionId = questionId;
    const modal = new bootstrap.Modal(document.getElementById('approvalModal'));
    const modalTitle = document.getElementById('modalTitle');
    const modalMessage = document.getElementById('modalMessage');
    const modalConfirmBtn = document.getElementById('modalConfirmBtn');
    
    document.getElementById('modalQuestionId').value = questionId;
    document.getElementById('modalAction').value = action;
    document.getElementById('modalNotes').value = '';
    
    if (action === 'approve') {
        modalTitle.innerHTML = '<i class="fas fa-check me-2"></i>Approve Question';
        modalMessage.textContent = 'Are you sure you want to approve this question? It will be added to the main quiz database.';
        modalConfirmBtn.className = 'btn btn-success';
        modalConfirmBtn.innerHTML = '<i class="fas fa-check me-1"></i>Approve Question';
    } else {
        modalTitle.innerHTML = '<i class="fas fa-times me-2"></i>Reject Question';
        modalMessage.textContent = 'Are you sure you want to reject this question? It will not be added to the quiz database.';
        modalConfirmBtn.className = 'btn btn-danger';
        modalConfirmBtn.innerHTML = '<i class="fas fa-times me-1"></i>Reject Question';
    }
    
    modal.show();
}

// Handle review modal action buttons
document.getElementById('reviewApprove').addEventListener('click', function() {
    if (currentQuestionId) {
        document.getElementById('reviewModal').querySelector('.btn-close').click();
        setTimeout(() => showApprovalModal(currentQuestionId, 'approve'), 300);
    }
});

document.getElementById('reviewReject').addEventListener('click', function() {
    if (currentQuestionId) {
        document.getElementById('reviewModal').querySelector('.btn-close').click();
        setTimeout(() => showApprovalModal(currentQuestionId, 'reject'), 300);
    }
});

// Auto-dismiss alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert-dismissible');
    alerts.forEach(alert => {
        setTimeout(() => {
            if (alert.parentNode) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 5000);
    });
});
</script>

<style>
/* Custom CSS for enhanced styling */
.stats-card {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 1.5rem;
    text-align: center;
    transition: all 0.3s ease;
}

.stats-card:hover {
    transform: translateY(-2px);
    background: rgba(255, 255, 255, 0.08);
    border-color: rgba(255, 255, 255, 0.2);
}

.question-card {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.question-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.3);
    border-color: rgba(255, 255, 255, 0.2);
}

.question-checkbox {
    position: absolute;
    top: 10px;
    left: 10px;
    z-index: 10;
}

.question-checkbox input[type="checkbox"] {
    transform: scale(1.2);
}

.bulk-actions {
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 1rem;
    margin-bottom: 2rem;
}

.option-item {
    padding: 0.25rem 0;
    border-left: 3px solid transparent;
    padding-left: 0.5rem;
    margin-bottom: 0.25rem;
}

.option-item.text-success {
    border-left-color: #198754;
    background: rgba(25, 135, 84, 0.1);
    border-radius: 4px;
}

.review-modal .modal-dialog {
    max-width: 1200px;
}

.notes-modal .modal-content {
    border: 1px solid rgba(255, 255, 255, 0.2);
    background: rgba(33, 37, 41, 0.95);
    backdrop-filter: blur(10px);
}

.page-header {
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.empty-state {
    background: rgba(255, 255, 255, 0.02);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 12px;
    padding: 3rem;
}

/* Responsive improvements */
@media (max-width: 768px) {
    .question-card {
        margin-bottom: 1rem;
    }
    
    .bulk-actions .row {
        text-align: center;
    }
    
    .bulk-actions .col-md-6:first-child {
        margin-bottom: 1rem;
    }
}

/* Loading animation */
.spinner-border {
    animation: spinner-border 0.75s linear infinite;
}

@keyframes spinner-border {
    to { transform: rotate(360deg); }
}
</style>

</body>
</html>

