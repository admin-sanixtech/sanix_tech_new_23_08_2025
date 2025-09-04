<?php
// admin_add_quiz_questions.php

// Start the session if it's not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once(__DIR__ . '/../../config/db_connection.php');

// Enable error reporting to help with debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if user is admin (add this check if needed)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: http://sanixtech.in/login.php');
    exit();
}

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$message = '';
$messageType = '';

// Fetch categories from the database
$categories_query = "SELECT category_id, category_name FROM categories ORDER BY category_name";
$categories = $conn->query($categories_query);

if (!$categories) {
    die("Error retrieving categories: " . $conn->error);
}

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $category_id = intval($_POST['category_id']);
        $subcategory_id = intval($_POST['subcategory_id']);
        $question_text = trim($_POST['question_text']);
        $question_content = isset($_POST['question_content']) ? trim($_POST['question_content']) : null;
        $question_type = $_POST['question_type'];
        $difficulty_level = $_POST['difficulty_level'];
        $description = trim($_POST['description']);
        $answer_content = trim($_POST['answer_content']);
        $code_snippet = isset($_POST['code_snippet']) ? trim($_POST['code_snippet']) : null;
        $created_by = $_SESSION['user_id'];

        // Validate required fields
        if (empty($category_id) || empty($subcategory_id) || empty($question_text) || empty($question_type) || empty($difficulty_level) || empty($description)) {
            throw new Exception("Please fill out all required fields.");
        }

        // Handle different question types
        $option_a = $option_b = $option_c = $option_d = $correct_answer = null;
        $option_a_content = $option_b_content = $option_c_content = $option_d_content = null;

        if ($question_type === 'multiple_choice') {
            $option_a = trim($_POST['mc_option_a']);
            $option_b = trim($_POST['mc_option_b']);
            $option_c = trim($_POST['mc_option_c']);
            $option_d = trim($_POST['mc_option_d']);
            
            // Handle extended content for options
            $option_a_content = isset($_POST['mc_option_a_content']) ? trim($_POST['mc_option_a_content']) : null;
            $option_b_content = isset($_POST['mc_option_b_content']) ? trim($_POST['mc_option_b_content']) : null;
            $option_c_content = isset($_POST['mc_option_c_content']) ? trim($_POST['mc_option_c_content']) : null;
            $option_d_content = isset($_POST['mc_option_d_content']) ? trim($_POST['mc_option_d_content']) : null;
            
            $correct_answer = $_POST['mc_correct_answer'];

            if (empty($option_a) || empty($option_b) || empty($option_c) || empty($option_d) || empty($correct_answer)) {
                throw new Exception("Please fill out all options and select the correct answer.");
            }
        } elseif ($question_type === 'true_false') {
            $option_a = 'True';
            $option_b = 'False';
            $correct_answer = $_POST['tf_correct_answer'];

            if (empty($correct_answer)) {
                throw new Exception("Please select the correct answer for True/False question.");
            }
        } elseif ($question_type === 'code') {
            $correct_answer = trim($_POST['code_answer']);
            
            if (empty($correct_answer)) {
                throw new Exception("Please provide the correct answer for the code question.");
            }
        } elseif ($question_type === 'paragraph') {
            $correct_answer = trim($_POST['paragraph_answer']);
            
            if (empty($correct_answer)) {
                throw new Exception("Please provide the expected answer for the paragraph question.");
            }
        }

        // Insert question into quiz_questions_pending table for approval workflow
        $sql = "INSERT INTO quiz_questions_pending 
                (category_id, subcategory_id, question_text, question_content, question_type, 
                 option_a, option_a_content, option_b, option_b_content, option_c, option_c_content, 
                 option_d, option_d_content, correct_answer, difficulty_level, description, 
                 answer_content, code_snippet, created_by, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
        
        $stmt = $conn->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("iissssssssssssssssi", 
            $category_id, $subcategory_id, $question_text, $question_content, $question_type, 
            $option_a, $option_a_content, $option_b, $option_b_content, $option_c, $option_c_content, 
            $option_d, $option_d_content, $correct_answer, $difficulty_level, 
            $description, $answer_content, $code_snippet, $created_by);
        
        if ($stmt->execute()) {
            $pending_id = $conn->insert_id;
            $message = "Question submitted successfully and is now pending approval! (ID: #$pending_id)";
            $messageType = "success";
            
            // Clear any saved draft after successful submission
            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    if (typeof(Storage) !== 'undefined') {
                        localStorage.removeItem('quiz_question_draft');
                    }
                });
            </script>";
            
            // Reset form data
            $_POST = array();
        } else {
            throw new Exception("Failed to add question: " . $stmt->error);
        }
        $stmt->close();

    } catch (Exception $e) {
        $message = $e->getMessage();
        $messageType = "danger";
    }
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Question - Admin Dashboard</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../css/admin_styleone.css">
    
    <style>
        /* Custom styles for admin quiz questions form */
        .form-card {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%);
            border: 1px solid #333;
            border-radius: 12px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .page-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            color: white;
        }

        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
            padding: 1rem 0;
            border-bottom: 1px solid #333;
        }

        .step {
            display: flex;
            flex-direction: column;
            align-items: center;
            flex: 1;
            position: relative;
            color: #6c757d;
        }

        .step.active {
            color: #007bff;
        }

        .step.completed {
            color: #28a745;
        }

        .step-circle {
            width: 35px;
            height: 35px;
            border-radius: 50%;
            background: #333;
            border: 2px solid #6c757d;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }

        .step.active .step-circle {
            background: #007bff;
            border-color: #007bff;
            color: white;
        }

        .step.completed .step-circle {
            background: #28a745;
            border-color: #28a745;
            color: white;
        }

        .step:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 17px;
            left: 60%;
            width: 80%;
            height: 2px;
            background: #333;
            z-index: -1;
        }

        .step.completed:not(:last-child)::after {
            background: #28a745;
        }

        .difficulty-badges {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .difficulty-badge {
            padding: 0.5rem 1rem;
            border: 2px solid #333;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: #1a1a2e;
            color: #fff;
        }

        .difficulty-badge:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }

        .difficulty-badge.beginner {
            border-color: #28a745;
        }

        .difficulty-badge.beginner.selected {
            background: #28a745;
            color: white;
        }

        .difficulty-badge.intermediate {
            border-color: #ffc107;
        }

        .difficulty-badge.intermediate.selected {
            background: #ffc107;
            color: #000;
        }

        .difficulty-badge.advanced {
            border-color: #fd7e14;
        }

        .difficulty-badge.advanced.selected {
            background: #fd7e14;
            color: white;
        }

        .difficulty-badge.expert {
            border-color: #dc3545;
        }

        .difficulty-badge.expert.selected {
            background: #dc3545;
            color: white;
        }

        .question-type-card {
            display: none;
            background: #0f1419;
            border: 1px solid #333;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }

        .question-type-card.show {
            display: block;
            animation: fadeInUp 0.3s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .option-group {
            margin-bottom: 1.5rem;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
        }

        .expandable-section {
            margin-top: 0.5rem;
        }

        .expandable-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            padding: 0.5rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .expandable-header:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .expandable-content {
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease;
            padding: 0 0.5rem;
        }

        .expandable-content.show {
            max-height: 500px;
            padding: 1rem 0.5rem;
        }

        .expand-icon {
            transition: transform 0.3s ease;
        }

        .large-textarea {
            min-height: 120px;
            resize: vertical;
        }

        .extra-large-textarea {
            min-height: 150px;
            resize: vertical;
        }

        .code-editor {
            font-family: 'Courier New', monospace;
            background: #0d1117;
            border: 1px solid #30363d;
            color: #c9d1d9;
            min-height: 200px;
        }

        .char-counter {
            text-align: right;
            margin-top: 0.25rem;
            font-size: 0.875rem;
        }

        .preview-question {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            padding: 1rem;
        }

        .preview-options {
            margin-top: 1rem;
        }

        .preview-option {
            padding: 0.5rem;
            margin-bottom: 0.5rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid #333;
            border-radius: 6px;
            transition: all 0.2s ease;
        }

        .preview-option.border-success {
            border-color: #28a745;
            background: rgba(40, 167, 69, 0.1);
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, #5a6fd8 0%, #6a4190 100%);
            transform: translateY(-1px);
        }

        .btn-secondary {
            background: #6c757d;
            border: none;
        }

        .btn-outline-warning {
            border-color: #ffc107;
            color: #ffc107;
        }

        .btn-outline-warning:hover {
            background: #ffc107;
            color: #000;
        }

        .sticky-top {
            position: sticky !important;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .step-indicator {
                flex-direction: column;
                gap: 1rem;
            }

            .step:not(:last-child)::after {
                display: none;
            }

            .difficulty-badges {
                flex-direction: column;
            }

            .col-lg-4 {
                margin-top: 2rem;
            }

            .sticky-top {
                position: static !important;
            }
        }

        /* Enhanced form styling */
        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .form-select:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        /* Animation for notifications */
        .alert.position-fixed {
            animation: slideInRight 0.3s ease;
        }

        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        /* Loading states */
        .btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        /* Enhanced preview styling */
        .preview-question h6 {
            line-height: 1.5;
            margin-bottom: 1rem;
        }

        .preview-question pre {
            background: #0d1117;
            border: 1px solid #30363d;
            border-radius: 6px;
            padding: 1rem;
            overflow-x: auto;
            font-size: 0.875rem;
        }

        /* Enhanced expandable sections */
        .expandable-header small {
            opacity: 0.8;
        }

        .expandable-content textarea {
            margin-top: 0.5rem;
        }

        /* Better mobile experience */
        @media (max-width: 576px) {
            .container-fluid {
                padding: 0.5rem;
            }

            .card-body {
                padding: 1rem !important;
            }

            .btn {
                font-size: 0.875rem;
                padding: 0.375rem 0.75rem;
            }
        }
    </style>
</head>

<body>
<div class="wrapper">
    <!-- Sidebar -->
    <aside id="sidebar" class="js-sidebar">
        <?php
         include(__DIR__ . '/../../admin_menu.php');
        ?>
    </aside>
    
    <!-- Main Content -->
    <div class="main">
        <!-- Top Navigation -->
        <?php
         include(__DIR__ . '/../../admin_navbar.php');
         ?>

        <main class="content px-3 py-4">
            <div class="container-fluid">
                <!-- Page Header -->
                <div class="page-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1">
                                <i class="fas fa-plus-circle me-2"></i>Add New Question
                            </h2>
                            <p class="mb-0 opacity-75">Create a new quiz question - will be sent for approval</p>
                        </div>
                        <div>
                            <a href="approve_user_questions.php" class="btn btn-outline-light me-2">
                                <i class="fas fa-clock me-2"></i>Pending Questions
                            </a>
                            <a href="dashboard.php" class="btn btn-outline-light">
                                <i class="fas fa-home me-2"></i>Dashboard
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Messages -->
                <?php if (!empty($message)): ?>
                    <div class="alert alert-<?php echo $messageType; ?> alert-dismissible fade show" role="alert">
                        <i class="fas fa-<?php echo $messageType === 'success' ? 'check-circle' : 'exclamation-circle'; ?> me-2"></i>
                        <?php echo htmlspecialchars($message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <!-- Form Card -->
                <div class="form-card card">
                    <div class="card-header">
                        <h5 class="mb-0 text-white">
                            <i class="fas fa-edit me-2"></i>Question Details
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <form id="questionForm" method="POST">
                            <!-- Step Indicator -->
                            <div class="step-indicator">
                                <div class="step active" data-step="1">
                                    <div class="step-circle">1</div>
                                    <small>Basic Info</small>
                                </div>
                                <div class="step" data-step="2">
                                    <div class="step-circle">2</div>
                                    <small>Question Type</small>
                                </div>
                                <div class="step" data-step="3">
                                    <div class="step-circle">3</div>
                                    <small>Content</small>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-lg-8">
                                    <!-- Category Selection -->
                                    <div class="row mb-4">
                                        <div class="col-md-6">
                                            <label for="category_id" class="form-label">
                                                <i class="fas fa-folder me-1"></i>Category *
                                            </label>
                                            <select id="category_id" name="category_id" class="form-select" required>
                                                <option value="">Select Category</option>
                                                <?php 
                                                $categories->data_seek(0); // Reset result pointer
                                                while ($cat = $categories->fetch_assoc()): ?>
                                                    <option value="<?= htmlspecialchars($cat['category_id']) ?>"
                                                        <?= (isset($_POST['category_id']) && $_POST['category_id'] == $cat['category_id']) ? 'selected' : '' ?>>
                                                        <?= htmlspecialchars($cat['category_name']) ?>
                                                    </option>
                                                <?php endwhile; ?>
                                            </select>
                                        </div>
                                        <div class="col-md-6">
                                            <label for="subcategory_id" class="form-label">
                                                <i class="fas fa-folder-open me-1"></i>Subcategory *
                                            </label>
                                            <select id="subcategory_id" name="subcategory_id" class="form-select" required>
                                                <option value="">Select Category First</option>
                                            </select>
                                        </div>
                                    </div>

                                    <!-- Question Text -->
                                    <div class="mb-4">
                                        <label for="question_text" class="form-label">
                                            <i class="fas fa-question-circle me-1"></i>Question *
                                        </label>
                                        <textarea id="question_text" name="question_text" class="form-control large-textarea" rows="3" 
                                                  placeholder="Enter your question here..." required maxlength="5000"><?= isset($_POST['question_text']) ? htmlspecialchars($_POST['question_text']) : '' ?></textarea>
                                        <div class="char-counter">
                                            <span id="question_text_count">0</span>/5000 characters
                                        </div>
                                    </div>

                                    <!-- Extended Question Content -->
                                    <div class="expandable-section">
                                        <div class="expandable-header" onclick="toggleExpandable(this)">
                                            <div>
                                                <i class="fas fa-plus-circle me-2"></i>
                                                <strong>Extended Question Content</strong> <small class="text-muted">(Optional)</small>
                                            </div>
                                            <i class="fas fa-chevron-down expand-icon"></i>
                                        </div>
                                        <div class="expandable-content">
                                            <p class="text-muted small mb-3">
                                                Add additional context, images, detailed explanations, or any extra content for the question.
                                            </p>
                                            <textarea name="question_content" class="form-control extra-large-textarea" rows="5" 
                                                      placeholder="Enter extended question content, context, or additional information..." maxlength="10000"><?= isset($_POST['question_content']) ? htmlspecialchars($_POST['question_content']) : '' ?></textarea>
                                            <div class="char-counter">
                                                <span id="question_content_count">0</span>/10000 characters
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Question Type Selection -->
                                    <div class="mb-4">
                                        <label for="question_type" class="form-label">
                                            <i class="fas fa-list-alt me-1"></i>Question Type *
                                        </label>
                                        <select id="question_type" name="question_type" class="form-select" required>
                                            <option value="">Select Question Type</option>
                                            <option value="multiple_choice" <?= (isset($_POST['question_type']) && $_POST['question_type'] == 'multiple_choice') ? 'selected' : '' ?>>Multiple Choice</option>
                                            <option value="true_false" <?= (isset($_POST['question_type']) && $_POST['question_type'] == 'true_false') ? 'selected' : '' ?>>True/False</option>
                                            <option value="code" <?= (isset($_POST['question_type']) && $_POST['question_type'] == 'code') ? 'selected' : '' ?>>Code Question</option>
                                            <option value="paragraph" <?= (isset($_POST['question_type']) && $_POST['question_type'] == 'paragraph') ? 'selected' : '' ?>>Paragraph/Essay</option>
                                        </select>
                                    </div>

                                    <!-- Difficulty Level -->
                                    <div class="mb-4">
                                        <label class="form-label">
                                            <i class="fas fa-signal me-1"></i>Difficulty Level *
                                        </label>
                                        <div class="difficulty-badges mt-2">
                                            <div class="difficulty-badge beginner <?= (isset($_POST['difficulty_level']) && $_POST['difficulty_level'] == 'Beginner') ? 'selected' : '' ?>" data-value="Beginner">
                                                <i class="fas fa-star me-1"></i>Beginner
                                            </div>
                                            <div class="difficulty-badge intermediate <?= (isset($_POST['difficulty_level']) && $_POST['difficulty_level'] == 'Intermediate') ? 'selected' : '' ?>" data-value="Intermediate">
                                                <i class="fas fa-star me-1"></i><i class="fas fa-star me-1"></i>Intermediate
                                            </div>
                                            <div class="difficulty-badge advanced <?= (isset($_POST['difficulty_level']) && $_POST['difficulty_level'] == 'Advanced') ? 'selected' : '' ?>" data-value="Advanced">
                                                <i class="fas fa-star me-1"></i><i class="fas fa-star me-1"></i><i class="fas fa-star me-1"></i>Advanced
                                            </div>
                                            <div class="difficulty-badge expert <?= (isset($_POST['difficulty_level']) && $_POST['difficulty_level'] == 'Expert') ? 'selected' : '' ?>" data-value="Expert">
                                                <i class="fas fa-star me-1"></i><i class="fas fa-star me-1"></i><i class="fas fa-star me-1"></i><i class="fas fa-star me-1"></i>Expert
                                            </div>
                                        </div>
                                        <input type="hidden" name="difficulty_level" id="difficulty_level" required>
                                    </div>

                                    <!-- Dynamic Question Type Content -->
                                    <div id="question-type-content">
                                        <!-- Multiple Choice Options -->
                                        <div id="multiple-choice-options" class="question-type-card">
                                            <h6 class="text-info mb-3">
                                                <i class="fas fa-check-square me-2"></i>Multiple Choice Options
                                            </h6>
                                            
                                            <!-- Option A -->
                                            <div class="option-group">
                                                <label class="form-label">Option A *</label>
                                                <input type="text" name="mc_option_a" class="form-control mb-2" placeholder="Enter option A"
                                                       value="<?= isset($_POST['mc_option_a']) ? htmlspecialchars($_POST['mc_option_a']) : '' ?>" maxlength="1000">
                                                <div class="expandable-section">
                                                    <div class="expandable-header" onclick="toggleExpandable(this)">
                                                        <small class="text-muted">Extended Content for Option A</small>
                                                        <i class="fas fa-chevron-down expand-icon"></i>
                                                    </div>
                                                    <div class="expandable-content">
                                                        <textarea name="mc_option_a_content" class="form-control" rows="3" 
                                                                  placeholder="Extended explanation or content for Option A..." maxlength="2000"><?= isset($_POST['mc_option_a_content']) ? htmlspecialchars($_POST['mc_option_a_content']) : '' ?></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Option B -->
                                            <div class="option-group">
                                                <label class="form-label">Option B *</label>
                                                <input type="text" name="mc_option_b" class="form-control mb-2" placeholder="Enter option B"
                                                       value="<?= isset($_POST['mc_option_b']) ? htmlspecialchars($_POST['mc_option_b']) : '' ?>" maxlength="1000">
                                                <div class="expandable-section">
                                                    <div class="expandable-header" onclick="toggleExpandable(this)">
                                                        <small class="text-muted">Extended Content for Option B</small>
                                                        <i class="fas fa-chevron-down expand-icon"></i>
                                                    </div>
                                                    <div class="expandable-content">
                                                        <textarea name="mc_option_b_content" class="form-control" rows="3" 
                                                                  placeholder="Extended explanation or content for Option B..." maxlength="2000"><?= isset($_POST['mc_option_b_content']) ? htmlspecialchars($_POST['mc_option_b_content']) : '' ?></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Option C -->
                                            <div class="option-group">
                                                <label class="form-label">Option C *</label>
                                                <input type="text" name="mc_option_c" class="form-control mb-2" placeholder="Enter option C"
                                                       value="<?= isset($_POST['mc_option_c']) ? htmlspecialchars($_POST['mc_option_c']) : '' ?>" maxlength="1000">
                                                <div class="expandable-section">
                                                    <div class="expandable-header" onclick="toggleExpandable(this)">
                                                        <small class="text-muted">Extended Content for Option C</small>
                                                        <i class="fas fa-chevron-down expand-icon"></i>
                                                    </div>
                                                    <div class="expandable-content">
                                                        <textarea name="mc_option_c_content" class="form-control" rows="3" 
                                                                  placeholder="Extended explanation or content for Option C..." maxlength="2000"><?= isset($_POST['mc_option_c_content']) ? htmlspecialchars($_POST['mc_option_c_content']) : '' ?></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <!-- Option D -->
                                            <div class="option-group">
                                                <label class="form-label">Option D *</label>
                                                <input type="text" name="mc_option_d" class="form-control mb-2" placeholder="Enter option D"
                                                       value="<?= isset($_POST['mc_option_d']) ? htmlspecialchars($_POST['mc_option_d']) : '' ?>" maxlength="1000">
                                                <div class="expandable-section">
                                                    <div class="expandable-header" onclick="toggleExpandable(this)">
                                                        <small class="text-muted">Extended Content for Option D</small>
                                                        <i class="fas fa-chevron-down expand-icon"></i>
                                                    </div>
                                                    <div class="expandable-content">
                                                        <textarea name="mc_option_d_content" class="form-control" rows="3" 
                                                                  placeholder="Extended explanation or content for Option D..." maxlength="2000"><?= isset($_POST['mc_option_d_content']) ? htmlspecialchars($_POST['mc_option_d_content']) : '' ?></textarea>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="mb-3">
                                                <label class="form-label text-success">
                                                    <i class="fas fa-check-circle me-1"></i>Correct Answer *
                                                </label>
                                                <select name="mc_correct_answer" class="form-select">
                                                    <option value="">Select Correct Answer</option>
                                                    <option value="A" <?= (isset($_POST['mc_correct_answer']) && $_POST['mc_correct_answer'] == 'A') ? 'selected' : '' ?>>A</option>
                                                    <option value="B" <?= (isset($_POST['mc_correct_answer']) && $_POST['mc_correct_answer'] == 'B') ? 'selected' : '' ?>>B</option>
                                                    <option value="C" <?= (isset($_POST['mc_correct_answer']) && $_POST['mc_correct_answer'] == 'C') ? 'selected' : '' ?>>C</option>
                                                    <option value="D" <?= (isset($_POST['mc_correct_answer']) && $_POST['mc_correct_answer'] == 'D') ? 'selected' : '' ?>>D</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- True/False Options -->
                                        <div id="true-false-options" class="question-type-card">
                                            <h6 class="text-info mb-3">
                                                <i class="fas fa-toggle-on me-2"></i>True/False Answer
                                            </h6>
                                            <div class="mb-3">
                                                <label class="form-label text-success">
                                                    <i class="fas fa-check-circle me-1"></i>Correct Answer *
                                                </label>
                                                <select name="tf_correct_answer" class="form-select">
                                                    <option value="">Select Correct Answer</option>
                                                    <option value="A" <?= (isset($_POST['tf_correct_answer']) && $_POST['tf_correct_answer'] == 'A') ? 'selected' : '' ?>>True</option>
                                                    <option value="B" <?= (isset($_POST['tf_correct_answer']) && $_POST['tf_correct_answer'] == 'B') ? 'selected' : '' ?>>False</option>
                                                </select>
                                            </div>
                                        </div>

                                        <!-- Code Question Options -->
                                        <div id="code-options" class="question-type-card">
                                            <h6 class="text-info mb-3">
                                                <i class="fas fa-code me-2"></i>Code Question
                                            </h6>
                                            <div class="mb-3">
                                                <label class="form-label">Code Snippet (Optional)</label>
                                                <textarea name="code_snippet" class="form-control code-editor" rows="8" 
                                                          placeholder="// Enter code snippet here..." maxlength="10000"><?= isset($_POST['code_snippet']) ? htmlspecialchars($_POST['code_snippet']) : '' ?></textarea>
                                                <div class="char-counter">
                                                    <span id="code_snippet_count">0</span>/10000 characters
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-success">
                                                    <i class="fas fa-check-circle me-1"></i>Correct Answer *
                                                </label>
                                                <textarea name="code_answer" class="form-control large-textarea" rows="5" 
                                                          placeholder="Enter the correct answer or expected output..." maxlength="5000"><?= isset($_POST['code_answer']) ? htmlspecialchars($_POST['code_answer']) : '' ?></textarea>
                                                <div class="char-counter">
                                                    <span id="code_answer_count">0</span>/5000 characters
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Paragraph/Essay Question Options -->
                                        <div id="paragraph-options" class="question-type-card">
                                            <h6 class="text-info mb-3">
                                                <i class="fas fa-paragraph me-2"></i>Paragraph/Essay Question
                                            </h6>
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle me-2"></i>
                                                For paragraph/essay questions, provide a comprehensive model answer that covers the key points students should address.
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-success">
                                                    <i class="fas fa-check-circle me-1"></i>Model Answer/Key Points *
                                                </label>
                                                <textarea name="paragraph_answer" class="form-control extra-large-textarea" rows="8" 
                                                          placeholder="Enter the model answer, key points, or rubric for evaluation..." maxlength="10000"><?= isset($_POST['paragraph_answer']) ? htmlspecialchars($_POST['paragraph_answer']) : '' ?></textarea>
                                                <div class="char-counter">
                                                    <span id="paragraph_answer_count">0</span>/10000 characters
                                                </div>
                                                <small class="text-muted">
                                                    Include: Main concepts to cover, key terms, expected depth of analysis, scoring criteria, etc.
                                                </small>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Description and Additional Content -->
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="description" class="form-label">
                                                <i class="fas fa-info-circle me-1"></i>Answer Explanation *
                                            </label>
                                            <textarea id="description" name="description" class="form-control large-textarea" rows="5" 
                                                      placeholder="Explain why this is the correct answer..." required maxlength="5000"><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?></textarea>
                                            <div class="char-counter">
                                                <span id="description_count">0</span>/5000 characters
                                            </div>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="answer_content" class="form-label">
                                                <i class="fas fa-plus-circle me-1"></i>Additional Learning Content
                                            </label>
                                            <textarea id="answer_content" name="answer_content" class="form-control large-textarea" rows="5" 
                                                      placeholder="Additional learning content, references, related concepts..." maxlength="5000"><?= isset($_POST['answer_content']) ? htmlspecialchars($_POST['answer_content']) : '' ?></textarea>
                                            <div class="char-counter">
                                                <span id="answer_content_count">0</span>/5000 characters
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Submit Buttons -->
                                    <div class="d-flex justify-content-end gap-3 mt-4">
                                        <button type="button" class="btn btn-outline-warning" onclick="clearDraft()" id="clearDraftBtn" style="display: none;">
                                            <i class="fas fa-trash me-2"></i>Clear Draft
                                        </button>
                                        <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                            <i class="fas fa-undo me-2"></i>Reset Form
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-paper-plane me-2"></i>Submit for Approval
                                        </button>
                                    </div>
                                    
                                    <!-- Keyboard Shortcuts Help -->
                                    <div class="text-muted small mt-3">
                                        <strong>Keyboard Shortcuts:</strong> 
                                        <span class="me-3">Ctrl+S: Save Draft</span>
                                        <span class="me-3">Ctrl+Enter: Submit</span>
                                        <span>Ctrl+R: Reset Form</span>
                                    </div>
                                </div>

                                <!-- Preview Panel -->
                                <div class="col-lg-4">
                                    <div class="sticky-top" style="top: 20px;">
                                        <div class="form-card card">
                                            <div class="card-header">
                                                <h6 class="mb-0">
                                                    <i class="fas fa-eye me-2"></i>Preview
                                                </h6>
                                            </div>
                                            <div class="card-body" id="preview-content">
                                                <p class="text-muted text-center">
                                                    <i class="fas fa-info-circle me-2"></i>
                                                    Fill out the form to see preview
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Character Usage Summary -->
                                        <div class="form-card card mt-3">
                                            <div class="card-header">
                                                <h6 class="mb-0">
                                                    <i class="fas fa-chart-bar me-2"></i>Content Summary
                                                </h6>
                                            </div>
                                            <div class="card-body" id="content-summary">
                                                <small class="text-muted">Character usage will appear here as you type</small>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Initialize form on page load
document.addEventListener('DOMContentLoaded', function() {
    // Load subcategories if category is selected
    const categorySelect = document.getElementById('category_id');
    if (categorySelect.value) {
        loadSubcategories(categorySelect.value);
    }
    
    // Show question type options if selected
    const questionTypeSelect = document.getElementById('question_type');
    if (questionTypeSelect.value) {
        showQuestionTypeOptions(questionTypeSelect.value);
    }
    
    // Set difficulty level if selected
    const selectedDifficulty = document.querySelector('.difficulty-badge.selected');
    if (selectedDifficulty) {
        document.getElementById('difficulty_level').value = selectedDifficulty.dataset.value;
    }
    
    // Initialize character counters
    initializeCharacterCounters();
    
    // Update preview
    updatePreview();
    
    // Add event listeners
    setupEventListeners();
    
    // Initialize form monitoring
    monitorFormChanges();
    
    // Update clear draft button visibility
    updateClearDraftButton();
    
    // Initial validation check
    setTimeout(showValidationFeedback, 500);
});

function initializeCharacterCounters() {
    const textareas = [
        'question_text', 'question_content', 'code_snippet', 'code_answer', 
        'paragraph_answer', 'description', 'answer_content'
    ];
    
    textareas.forEach(id => {
        const element = document.querySelector(`[name="${id}"]`);
        const counter = document.getElementById(`${id}_count`);
        if (element && counter) {
            updateCharacterCount(element, counter);
            element.addEventListener('input', () => updateCharacterCount(element, counter));
        }
    });
}

function updateCharacterCount(element, counter) {
    if (counter) {
        counter.textContent = element.value.length;
        
        // Color coding based on usage
        const maxLength = parseInt(element.getAttribute('maxlength')) || 1000;
        const usage = element.value.length / maxLength;
        
        if (usage > 0.9) {
            counter.style.color = '#dc3545';
        } else if (usage > 0.7) {
            counter.style.color = '#ffc107';
        } else {
            counter.style.color = 'rgba(255, 255, 255, 0.6)';
        }
    }
}

function setupEventListeners() {
    // Category/Subcategory filtering
    document.getElementById('category_id').addEventListener('change', function() {
        const categoryId = this.value;
        loadSubcategories(categoryId);
    });

    // Question type handling
    document.getElementById('question_type').addEventListener('change', function() {
        showQuestionTypeOptions(this.value);
    });

    // Difficulty level selection
    document.querySelectorAll('.difficulty-badge').forEach(badge => {
        badge.addEventListener('click', function() {
            selectDifficulty(this);
        });
    });

    // Real-time preview update
    const formElements = document.querySelectorAll('#questionForm input, #questionForm select, #questionForm textarea');
    formElements.forEach(element => {
        element.addEventListener('input', updatePreview);
        element.addEventListener('change', updatePreview);
    });
}

function toggleExpandable(header) {
    const content = header.nextElementSibling;
    const icon = header.querySelector('.expand-icon');
    
    if (content.classList.contains('show')) {
        content.classList.remove('show');
        icon.classList.remove('fa-chevron-up');
        icon.classList.add('fa-chevron-down');
    } else {
        content.classList.add('show');
        icon.classList.remove('fa-chevron-down');
        icon.classList.add('fa-chevron-up');
    }
}

function loadSubcategories(categoryId) {
    const subcategorySelect = document.getElementById('subcategory_id');
    
    if (!categoryId) {
        subcategorySelect.innerHTML = '<option value="">Select Category First</option>';
        subcategorySelect.disabled = true;
        return;
    }
    
    // Clear subcategory options
    subcategorySelect.innerHTML = '<option value="">Loading...</option>';
    subcategorySelect.disabled = true;
    
    // Updated path - since all files are in same directory, use relative path
    fetch('get_subcategories_json.php?category_id=' + categoryId)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            
            subcategorySelect.innerHTML = '<option value="">Select Subcategory</option>';
            
            if (Array.isArray(data) && data.length > 0) {
                data.forEach(subcat => {
                    const selected = subcategorySelect.dataset.selected == subcat.subcategory_id ? 'selected' : '';
                    subcategorySelect.innerHTML += `<option value="${subcat.subcategory_id}" ${selected}>${subcat.subcategory_name}</option>`;
                });
            } else {
                subcategorySelect.innerHTML += '<option value="">No subcategories found</option>';
            }
            
            subcategorySelect.disabled = false;
            updatePreview();
        })
        .catch(error => {
            console.error('Error loading subcategories:', error);
            subcategorySelect.innerHTML = '<option value="">Error loading subcategories</option>';
            subcategorySelect.disabled = false;
            handleAjaxError(error, 'Loading subcategories');
        });
}

function showQuestionTypeOptions(questionType) {
    const allCards = document.querySelectorAll('.question-type-card');
    
    // Hide all cards and remove required attributes
    allCards.forEach(card => {
        card.classList.remove('show');
        card.querySelectorAll('input, select, textarea').forEach(field => {
            field.removeAttribute('required');
        });
    });
    
    // Show relevant card and set required fields
    if (questionType === 'multiple_choice') {
        const card = document.getElementById('multiple-choice-options');
        card.classList.add('show');
        card.querySelectorAll('input[name^="mc_option_"], select[name="mc_correct_answer"]').forEach(field => {
            field.setAttribute('required', 'required');
        });
    } else if (questionType === 'true_false') {
        const card = document.getElementById('true-false-options');
        card.classList.add('show');
        card.querySelector('select[name="tf_correct_answer"]').setAttribute('required', 'required');
    } else if (questionType === 'code') {
        const card = document.getElementById('code-options');
        card.classList.add('show');
        card.querySelector('textarea[name="code_answer"]').setAttribute('required', 'required');
    } else if (questionType === 'paragraph') {
        const card = document.getElementById('paragraph-options');
        card.classList.add('show');
        card.querySelector('textarea[name="paragraph_answer"]').setAttribute('required', 'required');
    }
    
    updatePreview();
}

function selectDifficulty(badge) {
    // Remove selected class from all badges
    document.querySelectorAll('.difficulty-badge').forEach(b => b.classList.remove('selected'));
    
    // Add selected class to clicked badge
    badge.classList.add('selected');
    
    // Set hidden input value
    document.getElementById('difficulty_level').value = badge.dataset.value;
    
    updatePreview();
}

function updatePreview() {
    const questionText = document.getElementById('question_text').value;
    const questionContent = document.querySelector('[name="question_content"]').value;
    const questionType = document.getElementById('question_type').value;
    const difficulty = document.getElementById('difficulty_level').value;
    const description = document.getElementById('description').value;
    
    let previewHTML = '';
    
    if (questionText) {
        previewHTML += `<div class="preview-question">
                          <h6 class="text-white mb-3">${escapeHtml(questionText)}</h6>`;
        
        if (questionContent) {
            previewHTML += `<div class="mt-2 p-2 bg-dark bg-opacity-25 rounded">
                              <small class="text-info">Extended Content:</small>
                              <div class="mt-1">${escapeHtml(questionContent.substring(0, 200))}${questionContent.length > 200 ? '...' : ''}</div>
                            </div>`;
        }
        
        if (difficulty) {
            const difficultyClass = getDifficultyClass(difficulty);
            previewHTML += `<span class="badge ${difficultyClass} mb-3">${difficulty}</span><br>`;
        }
        
        if (questionType === 'multiple_choice') {
            const optionA = document.querySelector('input[name="mc_option_a"]').value;
            const optionB = document.querySelector('input[name="mc_option_b"]').value;
            const optionC = document.querySelector('input[name="mc_option_c"]').value;
            const optionD = document.querySelector('input[name="mc_option_d"]').value;
            const correctAnswer = document.querySelector('select[name="mc_correct_answer"]').value;
            
            if (optionA || optionB || optionC || optionD) {
                previewHTML += '<div class="preview-options mt-3">';
                if (optionA) previewHTML += `<div class="preview-option ${correctAnswer === 'A' ? 'border-success' : ''}"><strong>A:</strong> ${escapeHtml(optionA)}</div>`;
                if (optionB) previewHTML += `<div class="preview-option ${correctAnswer === 'B' ? 'border-success' : ''}"><strong>B:</strong> ${escapeHtml(optionB)}</div>`;
                if (optionC) previewHTML += `<div class="preview-option ${correctAnswer === 'C' ? 'border-success' : ''}"><strong>C:</strong> ${escapeHtml(optionC)}</div>`;
                if (optionD) previewHTML += `<div class="preview-option ${correctAnswer === 'D' ? 'border-success' : ''}"><strong>D:</strong> ${escapeHtml(optionD)}</div>`;
                previewHTML += '</div>';
            }
        } else if (questionType === 'true_false') {
            const correctAnswer = document.querySelector('select[name="tf_correct_answer"]').value;
            previewHTML += '<div class="preview-options mt-3">';
            previewHTML += `<div class="preview-option ${correctAnswer === 'A' ? 'border-success' : ''}"><strong>A:</strong> True</div>`;
            previewHTML += `<div class="preview-option ${correctAnswer === 'B' ? 'border-success' : ''}"><strong>B:</strong> False</div>`;
            previewHTML += '</div>';
        } else if (questionType === 'code') {
            const codeSnippet = document.querySelector('textarea[name="code_snippet"]').value;
            const codeAnswer = document.querySelector('textarea[name="code_answer"]').value;
            
            if (codeSnippet) {
                previewHTML += `<div class="mt-3">
                                  <strong>Code:</strong>
                                  <pre class="code-editor mt-2">${escapeHtml(codeSnippet.substring(0, 500))}${codeSnippet.length > 500 ? '\n...' : ''}</pre>
                                </div>`;
            }
            
            if (codeAnswer) {
                previewHTML += `<div class="mt-3 text-success">
                                  <strong>Expected Answer:</strong>
                                  <div class="mt-1">${escapeHtml(codeAnswer.substring(0, 200))}${codeAnswer.length > 200 ? '...' : ''}</div>
                                </div>`;
            }
        } else if (questionType === 'paragraph') {
            const paragraphAnswer = document.querySelector('textarea[name="paragraph_answer"]').value;
            
            if (paragraphAnswer) {
                previewHTML += `<div class="mt-3 text-success">
                                  <strong>Model Answer/Key Points:</strong>
                                  <div class="mt-1">${escapeHtml(paragraphAnswer.substring(0, 300))}${paragraphAnswer.length > 300 ? '...' : ''}</div>
                                </div>`;
            }
        }
        
        if (description) {
            previewHTML += `<div class="mt-3 pt-3 border-top border-secondary">
                              <strong class="text-info">Explanation:</strong>
                              <div class="mt-2 text-muted">${escapeHtml(description.substring(0, 200))}${description.length > 200 ? '...' : ''}</div>
                            </div>`;
        }
        
        previewHTML += '</div>';
    } else {
        previewHTML = `<p class="text-muted text-center">
                         <i class="fas fa-info-circle me-2"></i>
                         Fill out the form to see preview
                       </p>`;
    }
    
    document.getElementById('preview-content').innerHTML = previewHTML;
    
    // Update step indicators
    updateStepIndicators();
    
    // Update content summary
    updateContentSummary();
}

function updateContentSummary() {
    const summary = document.getElementById('content-summary');
    if (!summary) return;
    
    const fields = [
        { name: 'question_text', label: 'Question', max: 5000 },
        { name: 'question_content', label: 'Extended Content', max: 10000 },
        { name: 'description', label: 'Explanation', max: 5000 },
        { name: 'answer_content', label: 'Additional Content', max: 5000 },
        { name: 'code_snippet', label: 'Code Snippet', max: 10000 },
        { name: 'code_answer', label: 'Code Answer', max: 5000 },
        { name: 'paragraph_answer', label: 'Paragraph Answer', max: 10000 }
    ];
    
    let summaryHTML = '';
    let totalChars = 0;
    let hasContent = false;
    
    fields.forEach(field => {
        const element = document.querySelector(`[name="${field.name}"]`);
        if (element && element.value.trim()) {
            const length = element.value.length;
            const percentage = Math.round((length / field.max) * 100);
            const colorClass = percentage > 90 ? 'text-danger' : percentage > 70 ? 'text-warning' : 'text-success';
            
            summaryHTML += `
                <div class="d-flex justify-content-between align-items-center mb-1">
                    <small>${field.label}:</small>
                    <small class="${colorClass}">${length}/${field.max}</small>
                </div>
            `;
            totalChars += length;
            hasContent = true;
        }
    });
    
    if (hasContent) {
        summaryHTML = `
            <div class="mb-2">
                <strong class="text-info">Total Characters: ${totalChars.toLocaleString()}</strong>
            </div>
            ${summaryHTML}
        `;
    } else {
        summaryHTML = '<small class="text-muted">Character usage will appear here as you type</small>';
    }
    
    summary.innerHTML = summaryHTML;
}

function updateStepIndicators() {
    const steps = document.querySelectorAll('.step');
    const categoryId = document.getElementById('category_id').value;
    const subcategoryId = document.getElementById('subcategory_id').value;
    const questionText = document.getElementById('question_text').value;
    const questionType = document.getElementById('question_type').value;
    const difficulty = document.getElementById('difficulty_level').value;
    const description = document.getElementById('description').value;
    
    // Reset all steps
    steps.forEach(step => {
        step.classList.remove('active', 'completed');
    });
    
    // Step 1: Basic Info
    if (categoryId && subcategoryId && questionText) {
        steps[0].classList.add('completed');
    } else {
        steps[0].classList.add('active');
        return;
    }
    
    // Step 2: Question Type
    if (questionType && difficulty) {
        steps[1].classList.add('completed');
    } else {
        steps[1].classList.add('active');
        return;
    }
    
    // Step 3: Content
    let hasValidAnswers = false;
    if (questionType === 'multiple_choice') {
        const optionA = document.querySelector('input[name="mc_option_a"]').value;
        const optionB = document.querySelector('input[name="mc_option_b"]').value;
        const optionC = document.querySelector('input[name="mc_option_c"]').value;
        const optionD = document.querySelector('input[name="mc_option_d"]').value;
        const correctAnswer = document.querySelector('select[name="mc_correct_answer"]').value;
        hasValidAnswers = optionA && optionB && optionC && optionD && correctAnswer;
    } else if (questionType === 'true_false') {
        const correctAnswer = document.querySelector('select[name="tf_correct_answer"]').value;
        hasValidAnswers = correctAnswer;
    } else if (questionType === 'code') {
        const codeAnswer = document.querySelector('textarea[name="code_answer"]').value;
        hasValidAnswers = codeAnswer;
    } else if (questionType === 'paragraph') {
        const paragraphAnswer = document.querySelector('textarea[name="paragraph_answer"]').value;
        hasValidAnswers = paragraphAnswer;
    }
    
    if (hasValidAnswers && description) {
        steps[2].classList.add('completed');
    } else {
        steps[2].classList.add('active');
    }
}

function getDifficultyClass(difficulty) {
    switch(difficulty.toLowerCase()) {
        case 'beginner': return 'bg-success';
        case 'intermediate': return 'bg-warning text-dark';
        case 'advanced': return 'bg-danger';
        case 'expert': return 'bg-dark';
        default: return 'bg-secondary';
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function resetForm() {
    if (confirm('Are you sure you want to reset the form? All data will be lost.')) {
        document.getElementById('questionForm').reset();
        
        // Clear any saved draft
        localStorage.removeItem('quiz_question_draft');
        
        // Reset difficulty badges
        document.querySelectorAll('.difficulty-badge').forEach(badge => {
            badge.classList.remove('selected');
        });
        
        // Reset subcategory dropdown
        document.getElementById('subcategory_id').innerHTML = '<option value="">Select Category First</option>';
        document.getElementById('subcategory_id').disabled = true;
        
        // Hide question type cards
        document.querySelectorAll('.question-type-card').forEach(card => {
            card.classList.remove('show');
        });
        
        // Close expandable sections
        document.querySelectorAll('.expandable-content').forEach(content => {
            content.classList.remove('show');
        });
        document.querySelectorAll('.expand-icon').forEach(icon => {
            icon.classList.remove('fa-chevron-up');
            icon.classList.add('fa-chevron-down');
        });
        
        // Clear hidden input
        document.getElementById('difficulty_level').value = '';
        
        // Reset character counters
        document.querySelectorAll('.char-counter span').forEach(counter => {
            counter.textContent = '0';
            counter.style.color = 'rgba(255, 255, 255, 0.6)';
        });
        
        // Update preview and clear draft button
        updatePreview();
        updateClearDraftButton();
        showValidationFeedback();
    }
}

// Form validation before submit
document.getElementById('questionForm').addEventListener('submit', function(e) {
    const questionType = document.getElementById('question_type').value;
    let isValid = true;
    let errorMessage = '';
    
    // Check basic required fields
    const requiredFields = ['category_id', 'subcategory_id', 'question_text', 'question_type', 'difficulty_level', 'description'];
    for (let field of requiredFields) {
        const element = document.querySelector(`[name="${field}"]`) || document.getElementById(field);
        if (!element || !element.value.trim()) {
            isValid = false;
            errorMessage = `Please fill out the ${field.replace('_', ' ')} field.`;
            break;
        }
    }
    
    // Check question type specific validation
    if (isValid && questionType === 'multiple_choice') {
        const mcFields = ['mc_option_a', 'mc_option_b', 'mc_option_c', 'mc_option_d', 'mc_correct_answer'];
        for (let field of mcFields) {
            const element = document.querySelector(`[name="${field}"]`);
            if (!element || !element.value.trim()) {
                isValid = false;
                errorMessage = 'Please fill out all multiple choice options and select the correct answer.';
                break;
            }
        }
    } else if (isValid && questionType === 'true_false') {
        const tfAnswer = document.querySelector('[name="tf_correct_answer"]');
        if (!tfAnswer || !tfAnswer.value.trim()) {
            isValid = false;
            errorMessage = 'Please select the correct answer for the True/False question.';
        }
    } else if (isValid && questionType === 'code') {
        const codeAnswer = document.querySelector('[name="code_answer"]');
        if (!codeAnswer || !codeAnswer.value.trim()) {
            isValid = false;
            errorMessage = 'Please provide the correct answer for the code question.';
        }
    } else if (isValid && questionType === 'paragraph') {
        const paragraphAnswer = document.querySelector('[name="paragraph_answer"]');
        if (!paragraphAnswer || !paragraphAnswer.value.trim()) {
            isValid = false;
            errorMessage = 'Please provide the model answer for the paragraph question.';
        }
    }
    
    if (!isValid) {
        e.preventDefault();
        alert(errorMessage);
        return false;
    }
    
    // Show confirmation before submitting
    if (!confirm('Are you sure you want to submit this question for approval?')) {
        e.preventDefault();
        return false;
    }
    
    // Clear draft since we're submitting
    localStorage.removeItem('quiz_question_draft');
    
    // Disable submit button to prevent double submission
    const submitBtn = e.target.querySelector('button[type="submit"]');
    if (submitBtn) {
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
    }
});

// Improved auto-save functionality - only saves substantial drafts
function autoSave() {
    const formData = new FormData(document.getElementById('questionForm'));
    const data = {};
    
    for (let [key, value] of formData.entries()) {
        if (value.trim()) {
            data[key] = value;
        }
    }
    
    // Only save if there's substantial content
    const hasSubstantialContent = data.question_text?.length > 20 || 
                                 data.description?.length > 20 || 
                                 Object.keys(data).length > 3;
    
    if (hasSubstantialContent) {
        localStorage.setItem('quiz_question_draft', JSON.stringify(data));
        console.log('Draft saved automatically');
        updateClearDraftButton();
    }
}

// Improved load draft function
function loadDraft() {
    const draft = localStorage.getItem('quiz_question_draft');
    if (!draft) return;
    
    try {
        const data = JSON.parse(draft);
        
        // Check if form already has data (from server-side, like after validation error)
        const hasFormData = document.getElementById('question_text').value.trim() !== '' ||
                           document.getElementById('category_id').value !== '' ||
                           document.getElementById('question_type').value !== '';
        
        // Only prompt if form is empty and draft has substantial content
        const hasSubstantialDraft = Object.keys(data).length > 2 && 
                                   (data.question_text?.length > 20 || data.description?.length > 20);
        
        if (!hasFormData && hasSubstantialDraft && 
            confirm('Found a saved draft. Would you like to restore it?')) {
            
            Object.keys(data).forEach(key => {
                const element = document.querySelector(`[name="${key}"]`);
                if (element) {
                    element.value = data[key];
                    
                    // Trigger events for special fields
                    if (key === 'category_id') {
                        loadSubcategories(data[key]);
                    } else if (key === 'question_type') {
                        showQuestionTypeOptions(data[key]);
                    } else if (key === 'difficulty_level') {
                        const badge = document.querySelector(`[data-value="${data[key]}"]`);
                        if (badge) {
                            selectDifficulty(badge);
                        }
                    }
                }
            });
            
            // Update character counters and preview
            initializeCharacterCounters();
            updatePreview();
        }
        
        // Always clear the draft after checking (whether restored or not)
        localStorage.removeItem('quiz_question_draft');
        updateClearDraftButton();
        
    } catch (error) {
        console.error('Error loading draft:', error);
        localStorage.removeItem('quiz_question_draft');
        updateClearDraftButton();
    }
}

// Clear draft function
function clearDraft() {
    if (confirm('Are you sure you want to clear the saved draft?')) {
        localStorage.removeItem('quiz_question_draft');
        updateClearDraftButton();
        
        // Show brief notification
        showNotification('Draft cleared!', 'warning', 'fas fa-trash');
    }
}

// Update clear draft button visibility
function updateClearDraftButton() {
    const button = document.getElementById('clearDraftBtn');
    const hasDraft = localStorage.getItem('quiz_question_draft');
    
    if (button) {
        button.style.display = hasDraft ? 'inline-block' : 'none';
    }
}

// Enhanced notification system
function showNotification(message, type = 'success', icon = 'fas fa-check') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} position-fixed`;
    notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; opacity: 0.95; min-width: 250px;';
    notification.innerHTML = `<i class="${icon} me-2"></i>${message}`;
    document.body.appendChild(notification);
    
    setTimeout(() => {
        if (document.body.contains(notification)) {
            notification.style.animation = 'slideOutRight 0.3s ease forwards';
            setTimeout(() => {
                if (document.body.contains(notification)) {
                    document.body.removeChild(notification);
                }
            }, 300);
        }
    }, 3000);
}

// Auto-save every 30 seconds (only if substantial content)
setInterval(autoSave, 30000);

// Save draft when user leaves page (only if substantial content)
window.addEventListener('beforeunload', function(e) {
    const hasFormData = document.getElementById('question_text').value.trim() !== '' ||
                       document.getElementById('description').value.trim() !== '';
    
    if (hasFormData) {
        autoSave(); // Save before leaving
        e.preventDefault();
        e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
        return e.returnValue;
    }
});

// Load draft when page loads with improved logic
window.addEventListener('load', function() {
    setTimeout(loadDraft, 1000);
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl+S to save draft
    if (e.ctrlKey && e.key === 's') {
        e.preventDefault();
        autoSave();
        showNotification('Draft saved!', 'success', 'fas fa-save');
    }
    
    // Ctrl+Enter to submit form
    if (e.ctrlKey && e.key === 'Enter') {
        e.preventDefault();
        const submitBtn = document.querySelector('button[type="submit"]');
        if (submitBtn && !submitBtn.disabled) {
            document.getElementById('questionForm').dispatchEvent(new Event('submit'));
        }
    }
    
    // Ctrl+R to reset form
    if (e.ctrlKey && e.key === 'r') {
        e.preventDefault();
        resetForm();
    }
});

// Add tooltips for better UX
function addTooltips() {
    const tooltips = [
        { element: '#category_id', text: 'Select the main category for this question' },
        { element: '#subcategory_id', text: 'Select a specific subcategory to narrow down the topic' },
        { element: '#question_type', text: 'Choose the format of your question' },
        { element: '.difficulty-badge', text: 'Click to select the difficulty level' },
        { element: '#description', text: 'Provide a detailed explanation of why the answer is correct' },
        { element: '#answer_content', text: 'Add supplementary learning material or references' }
    ];
    
    tooltips.forEach(tooltip => {
        const elements = document.querySelectorAll(tooltip.element);
        elements.forEach(element => {
            element.setAttribute('title', tooltip.text);
            element.setAttribute('data-bs-toggle', 'tooltip');
        });
    });
    
    // Initialize Bootstrap tooltips if available
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
}

// Initialize tooltips when DOM is ready
document.addEventListener('DOMContentLoaded', addTooltips);

// Enhanced form monitoring for better draft management
function monitorFormChanges() {
    const formElements = document.querySelectorAll('#questionForm input, #questionForm select, #questionForm textarea');
    
    formElements.forEach(element => {
        element.addEventListener('input', function() {
            updateClearDraftButton();
            showValidationFeedback();
        });
        element.addEventListener('change', function() {
            updateClearDraftButton();
            showValidationFeedback();
        });
    });
}

// Enhanced error handling for AJAX requests
function handleAjaxError(error, context) {
    console.error(`Error in ${context}:`, error);
    showNotification(`Error: ${context} failed. Please try again.`, 'danger', 'fas fa-exclamation-triangle');
}

// Form data validation helper
function validateFormData() {
    const errors = [];
    
    // Check required fields
    const requiredFields = [
        { id: 'category_id', name: 'Category' },
        { id: 'subcategory_id', name: 'Subcategory' },
        { id: 'question_text', name: 'Question Text' },
        { id: 'question_type', name: 'Question Type' },
        { id: 'difficulty_level', name: 'Difficulty Level' },
        { id: 'description', name: 'Answer Explanation' }
    ];
    
    requiredFields.forEach(field => {
        const element = document.getElementById(field.id) || document.querySelector(`[name="${field.id}"]`);
        if (!element || !element.value.trim()) {
            errors.push(`${field.name} is required`);
        }
    });
    
    // Validate question type specific fields
    const questionType = document.getElementById('question_type').value;
    
    if (questionType === 'multiple_choice') {
        const mcFields = ['mc_option_a', 'mc_option_b', 'mc_option_c', 'mc_option_d', 'mc_correct_answer'];
        mcFields.forEach(field => {
            const element = document.querySelector(`[name="${field}"]`);
            if (!element || !element.value.trim()) {
                errors.push(`Multiple choice ${field.replace('mc_', '').replace('_', ' ')} is required`);
            }
        });
    } else if (questionType === 'true_false') {
        const tfAnswer = document.querySelector('[name="tf_correct_answer"]');
        if (!tfAnswer || !tfAnswer.value.trim()) {
            errors.push('True/False correct answer is required');
        }
    } else if (questionType === 'code') {
        const codeAnswer = document.querySelector('[name="code_answer"]');
        if (!codeAnswer || !codeAnswer.value.trim()) {
            errors.push('Code question answer is required');
        }
    } else if (questionType === 'paragraph') {
        const paragraphAnswer = document.querySelector('[name="paragraph_answer"]');
        if (!paragraphAnswer || !paragraphAnswer.value.trim()) {
            errors.push('Paragraph question model answer is required');
        }
    }
    
    return errors;
}

// Real-time form validation feedback
function showValidationFeedback() {
    const errors = validateFormData();
    const submitBtn = document.querySelector('button[type="submit"]');
    
    if (submitBtn) {
        if (errors.length === 0) {
            submitBtn.disabled = false;
            submitBtn.classList.remove('btn-secondary');
            submitBtn.classList.add('btn-primary');
        } else {
            submitBtn.disabled = true;
            submitBtn.classList.remove('btn-primary');
            submitBtn.classList.add('btn-secondary');
        }
    }
}

// Export/Import functionality for questions
function exportQuestion() {
    const formData = new FormData(document.getElementById('questionForm'));
    const data = {};
    
    for (let [key, value] of formData.entries()) {
        if (value.trim()) {
            data[key] = value;
        }
    }
    
    if (Object.keys(data).length === 0) {
        showNotification('No data to export. Please fill out the form first.', 'warning', 'fas fa-exclamation-triangle');
        return;
    }
    
    const jsonString = JSON.stringify(data, null, 2);
    const blob = new Blob([jsonString], { type: 'application/json' });
    const url = URL.createObjectURL(blob);
    
    const a = document.createElement('a');
    a.href = url;
    a.download = `question_${new Date().getTime()}.json`;
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    URL.revokeObjectURL(url);
    
    showNotification('Question exported successfully!', 'success', 'fas fa-download');
}

function importQuestion(event) {
    const file = event.target.files[0];
    if (!file) return;
    
    const reader = new FileReader();
    reader.onload = function(e) {
        try {
            const data = JSON.parse(e.target.result);
            
            // Clear any existing draft first
            localStorage.removeItem('quiz_question_draft');
            
            Object.keys(data).forEach(key => {
                const element = document.querySelector(`[name="${key}"]`);
                if (element) {
                    element.value = data[key];
                }
            });
            
            // Trigger necessary updates
            if (data.category_id) loadSubcategories(data.category_id);
            if (data.question_type) showQuestionTypeOptions(data.question_type);
            if (data.difficulty_level) {
                const badge = document.querySelector(`[data-value="${data.difficulty_level}"]`);
                if (badge) selectDifficulty(badge);
            }
            
            initializeCharacterCounters();
            updatePreview();
            updateClearDraftButton();
            showValidationFeedback();
            
            showNotification('Question imported successfully!', 'success', 'fas fa-upload');
            
        } catch (error) {
            showNotification('Error importing question: Invalid file format', 'danger', 'fas fa-exclamation-triangle');
        }
    };
    
    reader.readAsText(file);
    // Reset file input
    event.target.value = '';
}

// Add CSS for slideOutRight animation
const additionalCSS = `
    @keyframes slideOutRight {
        from {
            transform: translateX(0);
            opacity: 1;
        }
        to {
            transform: translateX(100%);
            opacity: 0;
        }
    }
`;

// Inject additional CSS
const style = document.createElement('style');
style.textContent = additionalCSS;
document.head.appendChild(style);

</script>

</body>
</html>