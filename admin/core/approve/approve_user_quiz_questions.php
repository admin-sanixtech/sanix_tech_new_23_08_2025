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
        if (!$get_stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $get_stmt->bind_param("i", $pending_id);
        $get_stmt->execute();
        $result = $get_stmt->get_result();
        $question_data = $result->fetch_assoc();
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
        if (!$insert_stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
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
            throw new Exception("Failed to insert into quiz_questions table: " . $insert_stmt->error);
        }
        
        $new_question_id = $conn->insert_id;
        $insert_stmt->close();
        
        // Update pending status
        $update_sql = "UPDATE quiz_questions_pending SET 
            status = 'approved', 
            reviewed_at = NOW(), 
            reviewed_by = ?, 
            review_notes = ? 
            WHERE pending_id = ?";
        $update_stmt = $conn->prepare($update_sql);
        if (!$update_stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $update_stmt->bind_param("isi", $admin_id, $notes, $pending_id);
        
        if (!$update_stmt->execute()) {
            throw new Exception("Failed to update pending question status: " . $update_stmt->error);
        }
        $update_stmt->close();
        
        // Log the approval (create table if it doesn't exist)
        $create_log_table = "CREATE TABLE IF NOT EXISTS quiz_question_approvals (
            id INT AUTO_INCREMENT PRIMARY KEY,
            question_id INT NOT NULL,
            admin_id INT NOT NULL,
            action ENUM('approved', 'rejected') NOT NULL,
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $conn->query($create_log_table);
        
        $log_sql = "INSERT INTO quiz_question_approvals (question_id, admin_id, action, notes) VALUES (?, ?, 'approved', ?)";
        $log_stmt = $conn->prepare($log_sql);
        if ($log_stmt) {
            $log_stmt->bind_param("iis", $new_question_id, $admin_id, $notes);
            $log_stmt->execute();
            $log_stmt->close();
        }
        
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
            reviewed_at = NOW(), 
            reviewed_by = ?, 
            review_notes = ? 
            WHERE pending_id = ? AND status = 'pending'";
        $update_stmt = $conn->prepare($update_sql);
        if (!$update_stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $update_stmt->bind_param("isi", $admin_id, $notes, $pending_id);
        
        if (!$update_stmt->execute() || $update_stmt->affected_rows === 0) {
            throw new Exception("Failed to reject question or question not found");
        }
        $update_stmt->close();
        
        // Log the rejection (using pending_id as question_id for rejected questions)
        $create_log_table = "CREATE TABLE IF NOT EXISTS quiz_question_approvals (
            id INT AUTO_INCREMENT PRIMARY KEY,
            question_id INT NOT NULL,
            admin_id INT NOT NULL,
            action ENUM('approved', 'rejected') NOT NULL,
            notes TEXT,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )";
        $conn->query($create_log_table);
        
        $log_sql = "INSERT INTO quiz_question_approvals (question_id, admin_id, action, notes) VALUES (?, ?, 'rejected', ?)";
        $log_stmt = $conn->prepare($log_sql);
        if ($log_stmt) {
            $log_stmt->bind_param("iis", $pending_id, $admin_id, $notes);
            $log_stmt->execute();
            $log_stmt->close();
        }
        
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
    // Get basic question data
    $sql = "SELECT * FROM quiz_questions_pending WHERE pending_id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        error_log("Prepare failed in getQuestionDetails: " . $conn->error);
        return null;
    }
    
    $stmt->bind_param("i", $pending_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $question = $result->fetch_assoc();
    $stmt->close();
    
    if (!$question) {
        return null;
    }
    
    // Get category name
    if ($question['category_id']) {
        $cat_sql = "SELECT category_name FROM categories WHERE category_id = ?";
        $cat_stmt = $conn->prepare($cat_sql);
        if ($cat_stmt) {
            $cat_stmt->bind_param("i", $question['category_id']);
            $cat_stmt->execute();
            $cat_result = $cat_stmt->get_result();
            $cat_data = $cat_result->fetch_assoc();
            $question['category_name'] = $cat_data ? $cat_data['category_name'] : 'Uncategorized';
            $cat_stmt->close();
        } else {
            $question['category_name'] = 'Uncategorized';
        }
    } else {
        $question['category_name'] = 'Uncategorized';
    }
    
    // Get subcategory name
    if ($question['subcategory_id']) {
        $subcat_sql = "SELECT subcategory_name FROM subcategories WHERE subcategory_id = ?";
        $subcat_stmt = $conn->prepare($subcat_sql);
        if ($subcat_stmt) {
            $subcat_stmt->bind_param("i", $question['subcategory_id']);
            $subcat_stmt->execute();
            $subcat_result = $subcat_stmt->get_result();
            $subcat_data = $subcat_result->fetch_assoc();
            $question['subcategory_name'] = $subcat_data ? $subcat_data['subcategory_name'] : '';
            $subcat_stmt->close();
        } else {
            $question['subcategory_name'] = '';
        }
    } else {
        $question['subcategory_name'] = '';
    }
    
    // Get creator details
    if ($question['created_by']) {
        $user_sql = "SELECT username, email FROM users WHERE user_id = ?";
        $user_stmt = $conn->prepare($user_sql);
        if ($user_stmt) {
            $user_stmt->bind_param("i", $question['created_by']);
            $user_stmt->execute();
            $user_result = $user_stmt->get_result();
            $user_data = $user_result->fetch_assoc();
            $question['creator_name'] = $user_data ? $user_data['username'] : 'Unknown User';
            $question['creator_email'] = $user_data ? $user_data['email'] : '';
            $user_stmt->close();
        } else {
            $question['creator_name'] = 'Unknown User';
            $question['creator_email'] = '';
        }
    } else {
        $question['creator_name'] = 'Unknown User';
        $question['creator_email'] = '';
    }
    
    return $question;
}

// Handle AJAX request for question details
if (isset($_GET['ajax']) && $_GET['ajax'] === 'get_question' && isset($_GET['id'])) {
    $question_id = intval($_GET['id']);
    $question = getQuestionDetails($conn, $question_id);
    
    header('Content-Type: application/json');
    
    if ($question) {
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

// Debug mode
$debug_mode = isset($_GET['debug']) && $_GET['debug'] === '1';

// First, let's check if the table exists and has data
$table_check_sql = "SHOW TABLES LIKE 'quiz_questions_pending'";
$table_exists = $conn->query($table_check_sql);

if (!$table_exists || $table_exists->num_rows === 0) {
    die("Error: Table 'quiz_questions_pending' does not exist. Please create the table first.");
}

// Get statistics with better error handling
$stats_sql = "SELECT 
    COUNT(*) as total_questions,
    SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending_count,
    SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved_count,
    SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected_count
FROM quiz_questions_pending";

$stats_result = $conn->query($stats_sql);
if (!$stats_result) {
    die("Error getting statistics: " . $conn->error);
}

$stats = $stats_result->fetch_assoc();

// If debug mode, let's see what's actually in the table
if ($debug_mode) {
    echo "<div class='alert alert-info'>";
    echo "<strong>Debug Information:</strong><br>";
    echo "Database Connection: " . ($conn ? 'Connected' : 'Failed') . "<br>";
    echo "Table exists: Yes<br>";
    echo "Total records: " . $stats['total_questions'] . "<br>";
    echo "Pending records: " . $stats['pending_count'] . "<br>";
    
    // Show sample data
    $sample_sql = "SELECT pending_id, question_text, status, created_at FROM quiz_questions_pending LIMIT 5";
    $sample_result = $conn->query($sample_sql);
    
    if ($sample_result && $sample_result->num_rows > 0) {
        echo "<br><strong>Sample Records:</strong><br>";
        echo "<table class='table table-sm'>";
        echo "<tr><th>ID</th><th>Question</th><th>Status</th><th>Created</th></tr>";
        while ($row = $sample_result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row['pending_id'] . "</td>";
            echo "<td>" . substr($row['question_text'], 0, 50) . "...</td>";
            echo "<td>" . $row['status'] . "</td>";
            echo "<td>" . $row['created_at'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "<br><strong>No sample records found.</strong><br>";
    }
    echo "</div>";
}

// Simplified main query - let's start basic and build up
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
    qp.subcategory_id
FROM quiz_questions_pending qp
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

// Now get additional data for each question (to avoid JOIN issues)
$questions = [];
while ($row = $result->fetch_assoc()) {
    // Get category name
    if ($row['category_id']) {
        $cat_sql = "SELECT category_name FROM categories WHERE category_id = ?";
        $cat_stmt = $conn->prepare($cat_sql);
        if ($cat_stmt) {
            $cat_stmt->bind_param("i", $row['category_id']);
            $cat_stmt->execute();
            $cat_result = $cat_stmt->get_result();
            $cat_data = $cat_result->fetch_assoc();
            $row['category_name'] = $cat_data ? $cat_data['category_name'] : 'Uncategorized';
            $cat_stmt->close();
        } else {
            $row['category_name'] = 'Uncategorized';
        }
    } else {
        $row['category_name'] = 'Uncategorized';
    }
    
    // Get subcategory name
    if ($row['subcategory_id']) {
        $subcat_sql = "SELECT subcategory_name FROM subcategories WHERE subcategory_id = ?";
        $subcat_stmt = $conn->prepare($subcat_sql);
        if ($subcat_stmt) {
            $subcat_stmt->bind_param("i", $row['subcategory_id']);
            $subcat_stmt->execute();
            $subcat_result = $subcat_stmt->get_result();
            $subcat_data = $subcat_result->fetch_assoc();
            $row['subcategory_name'] = $subcat_data ? $subcat_data['subcategory_name'] : '';
            $subcat_stmt->close();
        } else {
            $row['subcategory_name'] = '';
        }
    } else {
        $row['subcategory_name'] = '';
    }
    
    // Get creator name
    if ($row['created_by']) {
        $user_sql = "SELECT username FROM users WHERE user_id = ?";
        $user_stmt = $conn->prepare($user_sql);
        if ($user_stmt) {
            $user_stmt->bind_param("i", $row['created_by']);
            $user_stmt->execute();
            $user_result = $user_stmt->get_result();
            $user_data = $user_result->fetch_assoc();
            $row['creator_name'] = $user_data ? $user_data['username'] : 'Unknown User';
            $user_stmt->close();
        } else {
            $row['creator_name'] = 'Unknown User';
        }
    } else {
        $row['creator_name'] = 'Unknown User';
    }
    
    $questions[] = $row;
}

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
    
    <style>
    /* Custom CSS for enhanced styling */
    body {
        background-color: #1a1a2e;
        color: #ffffff;
    }
    
    .wrapper {
        display: flex;
        min-height: 100vh;
    }
    
    .main {
        flex: 1;
        padding: 20px;
    }
    
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
    
    /* Debug styles */
    .debug-info {
        background: #2d3748;
        border: 1px solid #4a5568;
        border-radius: 8px;
        padding: 1rem;
        margin-bottom: 1rem;
        font-family: monospace;
        font-size: 0.875rem;
    }
    
    .card {
        background-color: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .card-header {
        background-color: rgba(255, 255, 255, 0.02);
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .btn-outline-info {
        border-color: #17a2b8;
        color: #17a2b8;
    }
    
    .btn-outline-info:hover {
        background-color: #17a2b8;
        color: white;
    }

    .modal-content {
        background-color: rgba(33, 37, 41, 0.95);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .modal-header {
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }

    .modal-footer {
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }
    </style>
</head>

<body>
<div class="wrapper">
    <!-- Main Content -->
    <div class="main">
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
                            <?php if (!$debug_mode): ?>
                                <small><a href="?debug=1" class="text-info">Enable Debug Mode</a></small>
                            <?php else: ?>
                                <small><a href="?" class="text-info">Disable Debug Mode</a></small>
                            <?php endif; ?>
                        </div>
                        <div>
                            <span class="badge bg-warning text-dark fs-6">
                                <i class="fas fa-clock me-1"></i>
                                <?php echo $stats['pending_count']; ?> Pending
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Display Message -->
                <?php if ($message): ?>
                    <?php echo $message; ?>
                <?php endif; ?>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="stats-card">
                            <i class="fas fa-database text-primary fs-2 mb-2"></i>
                            <h4 class="mb-1"><?php echo $stats['total_questions']; ?></h4>
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

                <?php if ($stats['pending_count'] > 0): ?>
                    <!-- Bulk Actions -->
                    <form method="post" id="bulkActionForm">
                        <div class="bulk-actions">
                            <div class="row align-items-center">
                                <div class="col-md-6">
                                    <div class="d-flex align-items-center">
                                        <input type="checkbox" id="selectAll" class="form-check-input me-3">
                                        <label for="selectAll" class="form-check-label me-4">Select All</label>
                                        
                                        <select name="bulk_action" class="form-select form-select-sm me-3" style="width: auto;" required>
                                            <option value="">Choose Action</option>
                                            <option value="approve">Bulk Approve</option>
                                            <option value="reject">Bulk Reject</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6 text-end">
                                    <button type="submit" class="btn btn-primary btn-sm" id="bulkActionBtn" disabled>
                                        <i class="fas fa-bolt me-1"></i>Execute Action
                                    </button>
                                    <span id="selectedCount" class="ms-3 text-muted">0 selected</span>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Questions Grid -->
                    <div class="row">
                        <?php foreach ($questions as $question): ?>
                            <div class="col-lg-6 mb-4">
                                <div class="card question-card h-100">
                                    <!-- Selection Checkbox -->
                                    <div class="question-checkbox">
                                        <input type="checkbox" name="selected_questions[]" 
                                               value="<?php echo $question['pending_id']; ?>" 
                                               class="form-check-input question-select"
                                               form="bulkActionForm">
                                    </div>
                                    
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-0">
                                                <i class="fas fa-question-circle me-2"></i>
                                                Question #<?php echo $question['pending_id']; ?>
                                            </h6>
                                            <small class="text-muted">
                                                <i class="fas fa-user me-1"></i><?php echo htmlspecialchars($question['creator_name']); ?>
                                            </small>
                                        </div>
                                        <div class="text-end">
                                            <span class="badge bg-<?php echo match($question['difficulty_level']) {
                                                1 => 'success',
                                                2 => 'warning',
                                                3 => 'danger',
                                                default => 'secondary'
                                            }; ?>">
                                                <?php echo match($question['difficulty_level']) {
                                                    1 => 'Easy',
                                                    2 => 'Medium',
                                                    3 => 'Hard',
                                                    default => 'Unknown'
                                                }; ?>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <div class="card-body">
                                        <!-- Question Text -->
                                        <div class="mb-3">
                                            <strong>Question:</strong>
                                            <p class="mb-2"><?php echo htmlspecialchars($question['question_text']); ?></p>
                                            
                                            <?php if (!empty($question['question_content'])): ?>
                                                <div class="mt-2 p-2 bg-dark rounded">
                                                    <small><?php echo htmlspecialchars($question['question_content']); ?></small>
                                                </div>
                                            <?php endif; ?>
                                        </div>

                                        <!-- Code Snippet -->
                                        <?php if (!empty($question['code_snippet'])): ?>
                                            <div class="mb-3">
                                                <strong>Code:</strong>
                                                <pre class="bg-dark p-2 rounded mt-1"><code><?php echo htmlspecialchars($question['code_snippet']); ?></code></pre>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Options -->
                                        <?php if ($question['question_type'] === 'multiple_choice'): ?>
                                            <div class="mb-3">
                                                <strong>Options:</strong>
                                                <div class="mt-2">
                                                    <?php 
                                                    $options = ['A', 'B', 'C', 'D'];
                                                    foreach ($options as $opt): 
                                                        $option_field = 'option_' . strtolower($opt);
                                                        $option_content_field = 'option_' . strtolower($opt) . '_content';
                                                        $is_correct = $question['correct_answer'] === $opt;
                                                    ?>
                                                        <?php if (!empty($question[$option_field])): ?>
                                                            <div class="option-item <?php echo $is_correct ? 'text-success' : ''; ?>">
                                                                <strong><?php echo $opt; ?>:</strong> 
                                                                <?php echo htmlspecialchars($question[$option_field]); ?>
                                                                <?php if ($is_correct): ?>
                                                                    <i class="fas fa-check-circle ms-2"></i>
                                                                <?php endif; ?>
                                                                <?php if (!empty($question[$option_content_field])): ?>
                                                                    <div class="ms-3 mt-1">
                                                                        <small class="text-muted"><?php echo htmlspecialchars($question[$option_content_field]); ?></small>
                                                                    </div>
                                                                <?php endif; ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Category & Metadata -->
                                        <div class="mb-3">
                                            <small class="text-muted">
                                                <i class="fas fa-folder me-1"></i>
                                                <strong>Category:</strong> <?php echo htmlspecialchars($question['category_name']); ?>
                                                <?php if (!empty($question['subcategory_name'])): ?>
                                                    / <?php echo htmlspecialchars($question['subcategory_name']); ?>
                                                <?php endif; ?>
                                            </small>
                                            <br>
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-1"></i>
                                                <strong>Submitted:</strong> <?php echo date('M j, Y g:i A', strtotime($question['created_at'])); ?>
                                            </small>
                                        </div>
                                    </div>
                                    
                                    <div class="card-footer">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <button type="button" class="btn btn-outline-info btn-sm" 
                                                    onclick="viewFullQuestion(<?php echo $question['pending_id']; ?>)">
                                                <i class="fas fa-eye me-1"></i>Full Review
                                            </button>
                                            <div>
                                                <button type="button" class="btn btn-success btn-sm me-2" 
                                                        onclick="showNotesModal(<?php echo $question['pending_id']; ?>, 'approve')">
                                                    <i class="fas fa-check me-1"></i>Approve
                                                </button>
                                                <button type="button" class="btn btn-danger btn-sm" 
                                                        onclick="showNotesModal(<?php echo $question['pending_id']; ?>, 'reject')">
                                                    <i class="fas fa-times me-1"></i>Reject
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <!-- Pagination -->
                    <?php if ($total_pages > 1): ?>
                        <nav aria-label="Questions pagination" class="mt-4">
                            <ul class="pagination justify-content-center">
                                <!-- Previous Button -->
                                <?php if ($current_page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $current_page - 1; ?>">
                                            <i class="fas fa-chevron-left"></i> Previous
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <!-- Page Numbers -->
                                <?php
                                $start_page = max(1, $current_page - 2);
                                $end_page = min($total_pages, $current_page + 2);
                                
                                if ($start_page > 1):
                                ?>
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
                                    <li class="page-item <?php echo $i == $current_page ? 'active' : ''; ?>">
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

                                <!-- Next Button -->
                                <?php if ($current_page < $total_pages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?page=<?php echo $current_page + 1; ?>">
                                            Next <i class="fas fa-chevron-right"></i>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>

                <?php else: ?>
                    <!-- Empty State -->
                    <div class="empty-state text-center">
                        <i class="fas fa-check-circle text-success fs-1 mb-3"></i>
                        <h4 class="mb-3">All Caught Up!</h4>
                        <p class="text-muted mb-0">No pending questions to review at the moment.</p>
                        <small class="text-muted">New submissions will appear here automatically.</small>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<!-- Full Question Review Modal -->
<div class="modal fade review-modal" id="fullReviewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-search me-2"></i>Full Question Review
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="fullReviewContent">
                <div class="text-center">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading question details...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <div id="fullReviewActions">
                    <!-- Action buttons will be populated by JavaScript -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Notes Modal for Approve/Reject -->
<div class="modal fade notes-modal" id="notesModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form method="post" id="actionForm">
                <div class="modal-header">
                    <h5 class="modal-title" id="notesModalTitle">
                        <i class="fas fa-edit me-2"></i>Add Review Notes
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="question_id" id="actionQuestionId">
                    <input type="hidden" name="action" id="actionType">
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Review Notes (Optional)</label>
                        <textarea class="form-control" name="notes" id="notes" rows="4" 
                                  placeholder="Add any notes about your decision..."></textarea>
                        <div class="form-text">These notes will be saved for future reference.</div>
                    </div>
                    
                    <div class="alert alert-info d-none" id="actionWarning">
                        <i class="fas fa-info-circle me-2"></i>
                        <span id="actionWarningText"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn" id="actionSubmitBtn">
                        <i class="fas fa-check me-1"></i>Confirm Action
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// JavaScript for interactive functionality
document.addEventListener('DOMContentLoaded', function() {
    // Select All functionality
    const selectAllCheckbox = document.getElementById('selectAll');
    const questionCheckboxes = document.querySelectorAll('.question-select');
    const selectedCountSpan = document.getElementById('selectedCount');
    const bulkActionBtn = document.getElementById('bulkActionBtn');

    function updateSelectedCount() {
        const selectedCount = document.querySelectorAll('.question-select:checked').length;
        selectedCountSpan.textContent = `${selectedCount} selected`;
        bulkActionBtn.disabled = selectedCount === 0;
        
        // Update select all checkbox state
        if (selectedCount === 0) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = false;
        } else if (selectedCount === questionCheckboxes.length) {
            selectAllCheckbox.indeterminate = false;
            selectAllCheckbox.checked = true;
        } else {
            selectAllCheckbox.indeterminate = true;
        }
    }

    selectAllCheckbox.addEventListener('change', function() {
        questionCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateSelectedCount();
    });

    questionCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateSelectedCount);
    });

    // Bulk action form submission
    document.getElementById('bulkActionForm').addEventListener('submit', function(e) {
        const selectedCount = document.querySelectorAll('.question-select:checked').length;
        const action = document.querySelector('[name="bulk_action"]').value;
        
        if (selectedCount === 0) {
            e.preventDefault();
            alert('Please select at least one question.');
            return;
        }
        
        const actionText = action === 'approve' ? 'approve' : 'reject';
        if (!confirm(`Are you sure you want to ${actionText} ${selectedCount} question(s)?`)) {
            e.preventDefault();
        }
    });
});

// Function to view full question details
function viewFullQuestion(questionId) {
    const modal = new bootstrap.Modal(document.getElementById('fullReviewModal'));
    const content = document.getElementById('fullReviewContent');
    const actions = document.getElementById('fullReviewActions');
    
    // Reset content
    content.innerHTML = `
        <div class="text-center">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
            </div>
            <p class="mt-2">Loading question details...</p>
        </div>
    `;
    actions.innerHTML = '';
    
    modal.show();
    
    // Fetch question details via AJAX
    fetch(`?ajax=get_question&id=${questionId}`)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            
            // Populate modal with question details
            content.innerHTML = generateFullQuestionHTML(data);
            
            // Add action buttons
            actions.innerHTML = `
                <button type="button" class="btn btn-success me-2" onclick="showNotesModal(${questionId}, 'approve')">
                    <i class="fas fa-check me-1"></i>Approve
                </button>
                <button type="button" class="btn btn-danger" onclick="showNotesModal(${questionId}, 'reject')">
                    <i class="fas fa-times me-1"></i>Reject
                </button>
            `;
        })
        .catch(error => {
            content.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Error loading question: ${error.message}
                </div>
            `;
        });
}

// Function to generate full question HTML
function generateFullQuestionHTML(question) {
    let html = `
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Question #${question.pending_id}</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Question:</strong>
                            <p class="mt-2">${escapeHtml(question.question_text)}</p>
                            ${question.question_content ? `
                                <div class="mt-2 p-3 bg-dark rounded">
                                    <small>${escapeHtml(question.question_content)}</small>
                                </div>
                            ` : ''}
                        </div>

                        ${question.code_snippet ? `
                            <div class="mb-3">
                                <strong>Code Snippet:</strong>
                                <pre class="bg-dark p-3 rounded mt-2"><code>${escapeHtml(question.code_snippet)}</code></pre>
                            </div>
                        ` : ''}

                        ${question.question_type === 'multiple_choice' ? generateOptionsHTML(question) : ''}

                        ${question.description ? `
                            <div class="mb-3">
                                <strong>Description:</strong>
                                <p class="mt-2">${escapeHtml(question.description)}</p>
                            </div>
                        ` : ''}

                        ${question.answer_content ? `
                            <div class="mb-3">
                                <strong>Answer Explanation:</strong>
                                <div class="mt-2 p-3 bg-success bg-opacity-10 border border-success rounded">
                                    ${escapeHtml(question.answer_content)}
                                </div>
                            </div>
                        ` : ''}
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h6 class="mb-0">Question Metadata</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <strong>Category:</strong><br>
                            <small class="text-muted">${escapeHtml(question.category_name)}${question.subcategory_name ? ' / ' + escapeHtml(question.subcategory_name) : ''}</small>
                        </div>
                        <div class="mb-3">
                            <strong>Difficulty:</strong><br>
                            <span class="badge bg-${getDifficultyColor(question.difficulty_level)}">${getDifficultyText(question.difficulty_level)}</span>
                        </div>
                        <div class="mb-3">
                            <strong>Question Type:</strong><br>
                            <small class="text-muted">${question.question_type}</small>
                        </div>
                        <div class="mb-3">
                            <strong>Created By:</strong><br>
                            <small class="text-muted">${escapeHtml(question.creator_name)}</small>
                            ${question.creator_email ? `<br><small class="text-muted">${escapeHtml(question.creator_email)}</small>` : ''}
                        </div>
                        <div class="mb-3">
                            <strong>Submitted:</strong><br>
                            <small class="text-muted">${new Date(question.created_at).toLocaleString()}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    return html;
}

// Function to generate options HTML
function generateOptionsHTML(question) {
    const options = ['A', 'B', 'C', 'D'];
    let html = '<div class="mb-3"><strong>Options:</strong><div class="mt-2">';
    
    options.forEach(opt => {
        const optionField = `option_${opt.toLowerCase()}`;
        const optionContentField = `option_${opt.toLowerCase()}_content`;
        const isCorrect = question.correct_answer === opt;
        
        if (question[optionField]) {
            html += `
                <div class="option-item p-2 mb-2 rounded ${isCorrect ? 'bg-success bg-opacity-10 border border-success' : 'bg-dark'}">
                    <strong>${opt}:</strong> ${escapeHtml(question[optionField])}
                    ${isCorrect ? '<i class="fas fa-check-circle text-success ms-2"></i>' : ''}
                    ${question[optionContentField] ? `
                        <div class="ms-3 mt-1">
                            <small class="text-muted">${escapeHtml(question[optionContentField])}</small>
                        </div>
                    ` : ''}
                </div>
            `;
        }
    });
    
    html += '</div></div>';
    return html;
}

// Function to show notes modal
function showNotesModal(questionId, action) {
    const modal = new bootstrap.Modal(document.getElementById('notesModal'));
    const title = document.getElementById('notesModalTitle');
    const warning = document.getElementById('actionWarning');
    const warningText = document.getElementById('actionWarningText');
    const submitBtn = document.getElementById('actionSubmitBtn');
    
    // Set form values
    document.getElementById('actionQuestionId').value = questionId;
    document.getElementById('actionType').value = action;
    document.getElementById('notes').value = '';
    
    // Update modal content based on action
    if (action === 'approve') {
        title.innerHTML = '<i class="fas fa-check me-2"></i>Approve Question';
        submitBtn.className = 'btn btn-success';
        submitBtn.innerHTML = '<i class="fas fa-check me-1"></i>Approve Question';
        warningText.textContent = 'This question will be added to the main quiz database and become available for users.';
        warning.classList.remove('d-none');
    } else {
        title.innerHTML = '<i class="fas fa-times me-2"></i>Reject Question';
        submitBtn.className = 'btn btn-danger';
        submitBtn.innerHTML = '<i class="fas fa-times me-1"></i>Reject Question';
        warningText.textContent = 'This question will be marked as rejected and will not be added to the quiz database.';
        warning.classList.remove('d-none');
    }
    
    modal.show();
}

// Utility functions
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function getDifficultyColor(level) {
    switch(level) {
        case 1: return 'success';
        case 2: return 'warning';
        case 3: return 'danger';
        default: return 'secondary';
    }
}

function getDifficultyText(level) {
    switch(level) {
        case 1: return 'Easy';
        case 2: return 'Medium';
        case 3: return 'Hard';
        default: return 'Unknown';
    }
}
</script>

</body>
</html>