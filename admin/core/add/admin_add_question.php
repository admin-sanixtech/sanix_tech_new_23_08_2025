<?php
// admin_add_question.php

// Start the session if it's not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'db_connection.php'; // Include the database connection

// Enable error reporting to help with debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if user is admin (add this check if needed)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
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

        if ($question_type === 'multiple_choice') {
            $option_a = trim($_POST['mc_option_a']);
            $option_b = trim($_POST['mc_option_b']);
            $option_c = trim($_POST['mc_option_c']);
            $option_d = trim($_POST['mc_option_d']);
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
        }

        // Insert question into quiz_questions table
        $sql = "INSERT INTO quiz_questions 
                (category_id, subcategory_id, question_text, question_type, option_a, option_b, option_c, option_d, 
                 correct_answer, difficulty_level, description, answer_content, code_snippet, created_by, status) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'approved')";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("iisssssssssssi", $category_id, $subcategory_id, $question_text, $question_type, 
                         $option_a, $option_b, $option_c, $option_d, $correct_answer, $difficulty_level, 
                         $description, $answer_content, $code_snippet, $created_by);
        
        if ($stmt->execute()) {
            $message = "Question added successfully!";
            $messageType = "success";
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
    <link rel="stylesheet" href="css/admin_styleone.css">
    
    <style>
        .page-header {
            background: linear-gradient(135deg, rgba(13, 110, 253, 0.1), rgba(102, 16, 242, 0.1));
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .form-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            backdrop-filter: blur(10px);
        }
        
        .form-card .card-header {
            background: linear-gradient(135deg, rgba(13, 110, 253, 0.2), rgba(102, 16, 242, 0.2));
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px 15px 0 0 !important;
        }
        
        .form-control, .form-select {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff !important;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            background: rgba(255, 255, 255, 0.08);
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
            color: #fff !important;
        }
        
        /* Fix dropdown options visibility */
        .form-select option {
            background: #212529 !important;
            color: #fff !important;
            padding: 0.5rem;
        }
        
        .form-select option:hover,
        .form-select option:focus,
        .form-select option:checked {
            background: #0d6efd !important;
            color: #fff !important;
        }
        
        /* Ensure dropdown text is visible */
        select.form-select {
            color: #fff !important;
        }
        
        select.form-select:disabled {
            background: rgba(255, 255, 255, 0.02);
            color: rgba(255, 255, 255, 0.5) !important;
            border-color: rgba(255, 255, 255, 0.1);
        }
        
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }
        
        .form-label {
            color: #e9ecef;
            font-weight: 500;
            margin-bottom: 0.5rem;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #0d6efd, #6610f2);
            border: none;
            padding: 0.75rem 2rem;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #0b5ed7, #5a0fc8);
            transform: translateY(-1px);
        }
        
        .btn-secondary {
            background: linear-gradient(135deg, #6c757d, #495057);
            border: none;
        }
        
        .question-type-card {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 1rem;
            margin-top: 1rem;
            display: none;
            animation: fadeInUp 0.3s ease;
        }
        
        .question-type-card.show {
            display: block;
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .option-group {
            background: rgba(255, 255, 255, 0.02);
            border-radius: 8px;
            padding: 1rem;
            margin-bottom: 1rem;
        }
        
        .difficulty-badges {
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        
        .difficulty-badge {
            padding: 0.5rem 1rem;
            border: 2px solid transparent;
            border-radius: 50px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.05);
        }
        
        .difficulty-badge:hover {
            transform: translateY(-1px);
        }
        
        .difficulty-badge.beginner { border-color: #198754; color: #198754; }
        .difficulty-badge.intermediate { border-color: #ffc107; color: #ffc107; }
        .difficulty-badge.advanced { border-color: #fd7e14; color: #fd7e14; }
        .difficulty-badge.expert { border-color: #dc3545; color: #dc3545; }
        
        .difficulty-badge.selected.beginner { background: #198754; color: #fff; }
        .difficulty-badge.selected.intermediate { background: #ffc107; color: #000; }
        .difficulty-badge.selected.advanced { background: #fd7e14; color: #fff; }
        .difficulty-badge.selected.expert { background: #dc3545; color: #fff; }
        
        .code-editor {
            background: #1e1e1e;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            color: #d4d4d4;
            font-family: 'Courier New', monospace;
            font-size: 14px;
            line-height: 1.5;
            padding: 1rem;
            min-height: 120px;
        }
        
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
        }
        
        .step {
            flex: 1;
            text-align: center;
            position: relative;
        }
        
        .step::after {
            content: '';
            position: absolute;
            top: 15px;
            left: 50%;
            width: 100%;
            height: 2px;
            background: rgba(255, 255, 255, 0.1);
            z-index: -1;
        }
        
        .step:last-child::after {
            display: none;
        }
        
        .step-circle {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.5rem;
            transition: all 0.3s ease;
        }
        
        .step.active .step-circle {
            background: #0d6efd;
            color: #fff;
        }
        
        .step.completed .step-circle {
            background: #198754;
            color: #fff;
        }

        .preview-question {
            background: rgba(255, 255, 255, 0.02);
            border-radius: 8px;
            padding: 1rem;
        }

        .preview-options {
            margin-top: 1rem;
        }

        .preview-option {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 5px;
            padding: 0.5rem;
            margin-bottom: 0.5rem;
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
                                <i class="fas fa-plus-circle me-2"></i>Add New Question
                            </h2>
                            <p class="text-muted mb-0">Create a new quiz question for your platform</p>
                        </div>
                        <div>
                            <a href="approve_user_questions.php" class="btn btn-outline-warning me-2">
                                <i class="fas fa-clock me-2"></i>Pending Questions
                            </a>
                            <a href="dashboard.php" class="btn btn-outline-secondary">
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
                                        <textarea id="question_text" name="question_text" class="form-control" rows="3" 
                                                  placeholder="Enter your question here..." required><?= isset($_POST['question_text']) ? htmlspecialchars($_POST['question_text']) : '' ?></textarea>
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
                                            <div class="row">
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Option A *</label>
                                                    <input type="text" name="mc_option_a" class="form-control" placeholder="Enter option A"
                                                           value="<?= isset($_POST['mc_option_a']) ? htmlspecialchars($_POST['mc_option_a']) : '' ?>">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Option B *</label>
                                                    <input type="text" name="mc_option_b" class="form-control" placeholder="Enter option B"
                                                           value="<?= isset($_POST['mc_option_b']) ? htmlspecialchars($_POST['mc_option_b']) : '' ?>">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Option C *</label>
                                                    <input type="text" name="mc_option_c" class="form-control" placeholder="Enter option C"
                                                           value="<?= isset($_POST['mc_option_c']) ? htmlspecialchars($_POST['mc_option_c']) : '' ?>">
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <label class="form-label">Option D *</label>
                                                    <input type="text" name="mc_option_d" class="form-control" placeholder="Enter option D"
                                                           value="<?= isset($_POST['mc_option_d']) ? htmlspecialchars($_POST['mc_option_d']) : '' ?>">
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
                                                <textarea name="code_snippet" class="form-control code-editor" rows="6" 
                                                          placeholder="// Enter code snippet here..."><?= isset($_POST['code_snippet']) ? htmlspecialchars($_POST['code_snippet']) : '' ?></textarea>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label text-success">
                                                    <i class="fas fa-check-circle me-1"></i>Correct Answer *
                                                </label>
                                                <textarea name="code_answer" class="form-control" rows="3" 
                                                          placeholder="Enter the correct answer or expected output..."><?= isset($_POST['code_answer']) ? htmlspecialchars($_POST['code_answer']) : '' ?></textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Description and Additional Content -->
                                    <div class="row">
                                        <div class="col-md-6 mb-3">
                                            <label for="description" class="form-label">
                                                <i class="fas fa-info-circle me-1"></i>Answer Explanation *
                                            </label>
                                            <textarea id="description" name="description" class="form-control" rows="4" 
                                                      placeholder="Explain why this is the correct answer..." required><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?></textarea>
                                        </div>
                                        <div class="col-md-6 mb-3">
                                            <label for="answer_content" class="form-label">
                                                <i class="fas fa-plus-circle me-1"></i>Additional Content
                                            </label>
                                            <textarea id="answer_content" name="answer_content" class="form-control" rows="4" 
                                                      placeholder="Additional learning content, references, etc..."><?= isset($_POST['answer_content']) ? htmlspecialchars($_POST['answer_content']) : '' ?></textarea>
                                        </div>
                                    </div>

                                    <!-- Submit Buttons -->
                                    <div class="d-flex justify-content-end gap-3 mt-4">
                                        <button type="button" class="btn btn-secondary" onclick="resetForm()">
                                            <i class="fas fa-undo me-2"></i>Reset Form
                                        </button>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>Add Question
                                        </button>
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
    
    // Update preview
    updatePreview();
    
    // Add event listeners
    setupEventListeners();
});

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
    
    // Fetch subcategories for selected category
    fetch(`get_subcategories_json.php?category_id=${categoryId}`)
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
    const questionType = document.getElementById('question_type').value;
    const difficulty = document.getElementById('difficulty_level').value;
    const description = document.getElementById('description').value;
    
    let previewHTML = '';
    
    if (questionText) {
        previewHTML += `<div class="preview-question">
                          <h6 class="text-white mb-3">${escapeHtml(questionText)}</h6>`;
        
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
                                  <pre class="code-editor mt-2">${escapeHtml(codeSnippet)}</pre>
                                </div>`;
            }
            
            if (codeAnswer) {
                previewHTML += `<div class="mt-3 text-success">
                                  <strong>Expected Answer:</strong>
                                  <div class="mt-1">${escapeHtml(codeAnswer)}</div>
                                </div>`;
            }
        }
        
        if (description) {
            previewHTML += `<div class="mt-3 pt-3 border-top border-secondary">
                              <strong class="text-info">Explanation:</strong>
                              <div class="mt-2 text-muted">${escapeHtml(description)}</div>
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
        
        // Clear hidden input
        document.getElementById('difficulty_level').value = '';
        
        // Update preview
        updatePreview();
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
        const element = document.getElementById(field) || document.querySelector(`[name="${field}"]`);
        if (!element || !element.value.trim()) {
            isValid = false;
            errorMessage = 'Please fill out all required fields.';
            break;
        }
    }
    
    // Check question type specific fields
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
        if (!tfAnswer || !tfAnswer.value) {
            isValid = false;
            errorMessage = 'Please select the correct answer for True/False question.';
        }
    } else if (isValid && questionType === 'code') {
        const codeAnswer = document.querySelector('[name="code_answer"]');
        if (!codeAnswer || !codeAnswer.value.trim()) {
            isValid = false;
            errorMessage = 'Please provide the correct answer for the code question.';
        }
    }
    
    if (!isValid) {
        e.preventDefault();
        alert(errorMessage);
        return false;
    }
});
</script>
</body>
</html>