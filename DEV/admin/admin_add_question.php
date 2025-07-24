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

// Check if the connection was successful
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch categories from the database
$categories_query = "SELECT * FROM categories";
$categories = $conn->query($categories_query);

if (!$categories) {
    die("Error retrieving categories: " . $conn->error);
}

// Fetch subcategories from the database
$subcategories_query = "SELECT * FROM subcategories";
$subcategories = $conn->query($subcategories_query);

if (!$subcategories) {
    die("Error retrieving subcategories: " . $conn->error);
}

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = $_POST['category_id'];
    $subcategory_id = $_POST['subcategory_id'];
    $question_text = $_POST['question_text'];
    $question_type = $_POST['question_type'];
    $option_a = $_POST['option_a'] ?? null;
    $option_b = $_POST['option_b'] ?? null;
    $option_c = $_POST['option_c'] ?? null;
    $option_d = $_POST['option_d'] ?? null;
    $correct_answer = $_POST['correct_answer'] ?? null;
    $description = $_POST['description'];
    $answer_content = $_POST['answer_content'];
    $created_by = $_SESSION['user_id'];

    // Validate required fields
    if (empty($category_id) || empty($subcategory_id) || empty($question_text) || empty($question_type)) {
        echo "Please fill out all required fields.";
        exit;
    }

    // Insert question into quiz_questions table
    $sql = "INSERT INTO sanixazs_main_db.quiz_questions 
            (category_id, subcategory_id, question_text, question_type, option_a, option_b, option_c, option_d, correct_answer, created_by) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisssssssi", $category_id, $subcategory_id, $question_text, $question_type, $option_a, $option_b, $option_c, $option_d, $correct_answer, $created_by);
    $stmt->execute();

    $question_id = $stmt->insert_id;

    // Insert answers based on question type
    if ($question_type === 'multiple_choice' || $question_type === 'true_false') {
        $options = ['A' => $option_a, 'B' => $option_b, 'C' => $option_c, 'D' => $option_d];
        foreach ($options as $option_key => $option_value) {
            if (!empty($option_value)) {
                $is_correct = ($correct_answer === $option_key) ? 1 : 0;
                $sql_answer = "INSERT INTO sanixazs_main_db.answers (question_id, answer_text, is_correct, description, additional_content) 
                               VALUES (?, ?, ?, ?, ?)";
                $stmt_answer = $conn->prepare($sql_answer);
                $stmt_answer->bind_param("isiss", $question_id, $option_value, $is_correct, $description, $answer_content);
                $stmt_answer->execute();
            }
        }
    } elseif ($question_type === 'code') {
        $sql_answer = "INSERT INTO sanixazs_main_db.answers (question_id, answer_text, is_correct, description, additional_content) 
                       VALUES (?, ?, ?, ?, ?)";
        $stmt_answer = $conn->prepare($sql_answer);
        $stmt_answer->bind_param("isiss", $question_id, $correct_answer, 1, $description, $answer_content);
        $stmt_answer->execute();
    }

    // Redirect after successful submission
    header('Location: admin_dashboard.php?menu=quiz');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Add Question</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" />
    <script src="https://kit.fontawesome.com/ae360af17e.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/admin_styleone.css" />
</head>
<body>
<div class="wrapper">
    <aside id="sidebar" class="js-sidebar">
        <?php include 'admin_menu.php'; ?>
    </aside>
    <div class="main">
        <?php include 'admin_navbar.php'; ?>
        <main class="content px-3 py-2">
            <div class="container-fluid">

                <div class="card border-0">
                    <div class="card-header">
                        <h5 class="card-title">Add New Question</h5>
                        <h6 class="card-subtitle text-muted">Submit a question for approval</h6>
                    </div>
                    <div class="card-body">
                        <form action="" method="POST">
                            <div class="mb-3">
                                <label for="category_id" class="form-label">Category:</label>
                                <select id="category_id" name="category_id" class="form-control" required>
                                    <option value="">Select Category</option>
                                    <?php while ($cat = $categories->fetch_assoc()): ?>
                                        <option value="<?= htmlspecialchars($cat['category_id']) ?>"><?= htmlspecialchars($cat['category_name']) ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="subcategory_id" class="form-label">Subcategory:</label>
                                <select id="subcategory_id" name="subcategory_id" class="form-control" required>
                                    <option value="">Select Subcategory</option>
                                    <?php while ($subcat = $subcategories->fetch_assoc()): ?>
                                        <option value="<?= htmlspecialchars($subcat['subcategory_id']) ?>"><?= htmlspecialchars($subcat['subcategory_name']) ?></option>
                                    <?php endwhile; ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="question_text" class="form-label">Question:</label>
                                <textarea id="question_text" name="question_text" class="form-control" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="question_type" class="form-label">Question Type:</label>
                                <select id="question_type" name="question_type" class="form-control" required onchange="showOptions(this.value)">
                                    <option value="">Select Question Type</option>
                                    <option value="multiple_choice">Multiple Choice</option>
                                    <option value="true_false">True/False</option>
                                    <option value="code">Code</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="question_difficulty_level" class="form-label">Difficulty Level:</label>
                                <select id="question_difficulty_level" name="question_difficulty_level" class="form-control" required>
                                    <option value="">Select Difficulty Level</option>
                                    <option value="easy">Easy</option>
                                    <option value="medium">Medium</option>
                                    <option value="hard">Hard</option>
                                </select>
                            </div>
                            <div id="options-container" class="mb-3">
                                <!-- Options fields will be shown here -->
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Answer Description:</label>
                                <textarea id="description" name="description" class="form-control" required></textarea>
                            </div>
                            <div class="mb-3">
                                <label for="answer_content" class="form-label">Additional Answer Content:</label>
                                <textarea id="answer_content" name="answer_content" class="form-control" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Add Question</button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
        <?php include 'admin_footer.php'; ?>
    </div>
</div>
<script>
function showOptions(type) {
    let container = document.getElementById('options-container');
    container.innerHTML = '';

    if (type === 'multiple_choice') {
        container.innerHTML = `
            <div class="mb-3">
                <label for="option_a" class="form-label">Option A:</label>
                <input type="text" id="option_a" name="option_a" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="option_b" class="form-label">Option B:</label>
                <input type="text" id="option_b" name="option_b" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="option_c" class="form-label">Option C:</label>
                <input type="text" id="option_c" name="option_c" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="option_d" class="form-label">Option D:</label>
                <input type="text" id="option_d" name="option_d" class="form-control" required>
            </div>
            <div class="mb-3">
                <label for="correct_answer" class="form-label">Correct Answer:</label>
                <select id="correct_answer" name="correct_answer" class="form-control" required>
                    <option value="">Select Correct Answer</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                </select>
            </div>
        `;
    } else if (type === 'true_false') {
        container.innerHTML = `
            <div class="mb-3">
                <label for="correct_answer" class="form-label">Correct Answer:</label>
                <select id="correct_answer" name="correct_answer" class="form-control" required>
                    <option value="">Select Correct Answer</option>
                    <option value="true">True</option>
                    <option value="false">False</option>
                </select>
            </div>
        `;
    } else if (type === 'code') {
        container.innerHTML = `
            <div class="mb-3">
                <label for="code_snippet" class="form-label">Code Snippet:</label>
                <textarea id="code_snippet" name="code_snippet" class="form-control" required></textarea>
            </div>
        `;
    }
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
