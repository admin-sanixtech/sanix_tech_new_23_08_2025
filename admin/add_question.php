<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include 'db_connection.php';

// Ensure user is logged in and has admin privileges
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "Access denied. You must be an admin to view this page.";
    exit;
}

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

    // Insert into quiz_questions table
    $sql = "INSERT INTO sanixazs_main_db.quiz_questions 
            (category_id, subcategory_id, question_text, question_type, option_a, option_b, option_c, option_d, correct_answer) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iisssssss", $category_id, $subcategory_id, $question_text, $question_type, $option_a, $option_b, $option_c, $option_d, $correct_answer);
    $stmt->execute();

    // Get the last inserted question ID
    $question_id = $stmt->insert_id;

    // Insert into answers table for multiple-choice or true/false questions
    if ($question_type === 'multiple_choice' || $question_type === 'true_false') {
        // Insert answers for each option (A, B, C, D) if provided
        $options = ['A' => $option_a, 'B' => $option_b, 'C' => $option_c, 'D' => $option_d];
        foreach ($options as $option_key => $option_value) {
            if (!empty($option_value)) {
                $is_correct = ($correct_answer === $option_key) ? 1 : 0;
                $sql_answer = "INSERT INTO sanixazs_main_db.answers (question_id, answer_text, is_correct, description) 
                               VALUES (?, ?, ?, ?)";
                $stmt_answer = $conn->prepare($sql_answer);
                $stmt_answer->bind_param("isis", $question_id, $option_value, $is_correct, $description);
                $stmt_answer->execute();
            }
        }
    } elseif ($question_type === 'code') {
        // Insert code snippet as answer
        $sql_answer = "INSERT INTO sanixazs_main_db.answers (question_id, answer_text, is_correct, description) 
                       VALUES (?, ?, ?, ?)";
        $stmt_answer = $conn->prepare($sql_answer);
        $stmt_answer->bind_param("isis", $question_id, $correct_answer, 1, $description);
        $stmt_answer->execute();
    }

    header('Location: admin_dashboard.php?menu=quiz');
    exit;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Question</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h3>Add New Question</h3>
    <form action="add_question.php" method="POST">
        <div class="form-group">
            <label for="category_id">Category:</label>
            <select id="category_id" name="category_id" required>
                <?php
                $categories = $conn->query("SELECT * FROM sanixazs_main_db.categories");
                if ($categories && $categories->num_rows > 0) {
                    while ($cat = $categories->fetch_assoc()) {
                        echo "<option value='" . $cat['category_id'] . "'>" . htmlspecialchars($cat['category_name']) . "</option>";
                    }
                } else {
                    echo "<option value=''>No categories available</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="subcategory_id">Subcategory:</label>
            <select id="subcategory_id" name="subcategory_id" required>
                <?php
                $subcategories = $conn->query("SELECT * FROM sanixazs_main_db.subcategories");
                if ($subcategories && $subcategories->num_rows > 0) {
                    while ($subcat = $subcategories->fetch_assoc()) {
                        echo "<option value='" . $subcat['subcategory_id'] . "'>" . htmlspecialchars($subcat['subcategory_name']) . "</option>";
                    }
                } else {
                    echo "<option value=''>No subcategories available</option>";
                }
                ?>
            </select>
        </div>
        <div class="form-group">
            <label for="question_text">Question:</label>
            <textarea id="question_text" name="question_text" required></textarea>
        </div>
        <div class="form-group">
            <label for="question_type">Question Type:</label>
            <select id="question_type" name="question_type" required onchange="showOptions(this.value)">
                <option value="multiple_choice">Multiple Choice</option>
                <option value="true_false">True/False</option>
                <option value="code">Code</option>
            </select>
        </div>
        <div id="options-container">
            <!-- Options fields will be shown here -->
        </div>

        <div class="form-group">
            <label for="description">Answer Description:</label>
            <textarea id="description" name="description" required></textarea>
        </div>
        
        <div class="form-group">
            <label for="answer_content">Additional Answer Content:</label>
            <textarea id="answer_content" name="answer_content" required></textarea>
        </div>
        
        <button type="submit" class="btn btn-primary">Add Question</button>
    </form>

    <script>
    function showOptions(type) {
        let container = document.getElementById('options-container');
        container.innerHTML = '';

        if (type === 'multiple_choice') {
            container.innerHTML = `
                <div class="form-group">
                    <label for="option_a">Option A:</label>
                    <input type="text" id="option_a" name="option_a">
                </div>
                <div class="form-group">
                    <label for="option_b">Option B:</label>
                    <input type="text" id="option_b" name="option_b">
                </div>
                <div class="form-group">
                    <label for="option_c">Option C:</label>
                    <input type="text" id="option_c" name="option_c">
                </div>
                <div class="form-group">
                    <label for="option_d">Option D:</label>
                    <input type="text" id="option_d" name="option_d">
                </div>
                <div class="form-group">
                    <label for="correct_answer">Correct Answer:</label>
                    <select id="correct_answer" name="correct_answer">
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="C">C</option>
                        <option value="D">D</option>
                    </select>
                </div>
            `;
        } else if (type === 'true_false') {
            container.innerHTML = `
                <div class="form-group">
                    <label for="correct_answer">Correct Answer:</label>
                    <select id="correct_answer" name="correct_answer">
                        <option value="true">True</option>
                        <option value="false">False</option>
                    </select>
                </div>
            `;
        } else if (type === 'code') {
            container.innerHTML = `
                <div class="form-group">
                    <label for="code_snippet">Code Snippet:</label>
                    <textarea id="code_snippet" name="code_snippet"></textarea>
                </div>
            `;
        }
    }
    </script>
</body>
</html>
