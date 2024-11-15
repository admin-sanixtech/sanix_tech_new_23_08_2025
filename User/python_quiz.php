<?php
session_start();

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Include database connection
include_once 'db_connection.php';

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check for form submission for difficulty selection
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['select_quiz'])) {
    $_SESSION['selected_difficulty'] = $_POST['difficulty'];
    $_SESSION['current_question_index'] = 0; // Reset to the first question
}

// Selected difficulty level
$selectedDifficulty = $_SESSION['selected_difficulty'] ?? null;

// Build the query to fetch only Python questions based on selected difficulty
$quizQuery = "SELECT * FROM sanixazs_main_db.quiz_questions WHERE category_id = ?";
if ($selectedDifficulty) {
    $quizQuery .= " AND difficulty_level = ?";
}
$stmt = $conn->prepare($quizQuery);

// Set Python category_id (e.g., assuming it's 1)
$pythonCategoryId = 1;

if ($selectedDifficulty) {
    $stmt->bind_param("is", $pythonCategoryId, $selectedDifficulty);
} else {
    $stmt->bind_param("i", $pythonCategoryId);
}

// Execute the statement
$stmt->execute();
$quizResult = $stmt->get_result();

// Store questions in an array
$questions = [];
while ($question = $quizResult->fetch_assoc()) {
    $questions[] = $question;
}

// Check if questions were retrieved
if (empty($questions)) {
    die("No questions found for the selected difficulty.");
}

// Initialize current question index
if (!isset($_SESSION['current_question_index'])) {
    $_SESSION['current_question_index'] = 0;
}

// Handle navigation (Next, Previous, Question Number)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle specific question navigation
    if (isset($_POST['question_index'])) {
        $_SESSION['current_question_index'] = intval($_POST['question_index']);
    } elseif (isset($_POST['next'])) {
        $_SESSION['current_question_index']++;
    } elseif (isset($_POST['previous'])) {
        $_SESSION['current_question_index']--;
    }

    // Validate question index
    if ($_SESSION['current_question_index'] < 0) {
        $_SESSION['current_question_index'] = 0;
    } elseif ($_SESSION['current_question_index'] >= count($questions)) {
        $_SESSION['current_question_index'] = count($questions) - 1; // Stay on the last question
    }

    // Handle answer submission
    if (isset($_POST['submit_answers'])) {
        // Check if any answers have been submitted
        if (isset($_POST['answer']) && is_array($_POST['answer'])) {
            foreach ($_POST['answer'] as $question_id => $answer_id) {
                // Insert user's answer into the database
                $stmt = $conn->prepare("INSERT INTO sanixazs_main_db.user_answers (user_id, question_id, selected_answer) VALUES (?, ?, ?)");
                $stmt->bind_param("iii", $_SESSION['user_id'], $question_id, $answer_id);
                $stmt->execute();
            }
            // Redirect to results page
            header("Location: submit_quiz.php");
            exit;
        } else {
            // Set a message indicating no answers were selected
            $errorMessage = "You must answer at least one question before submitting.";
        }
    }

    // Clear current question answer (if desired)
    if (isset($_POST['clear_answer'])) {
        // Logic to clear the answer can be added here if needed
        // This will depend on how you want to handle clearing
    }
}

// Get the current question
$currentQuestion = $questions[$_SESSION['current_question_index']];

// Fetch answers for the current question
$answersQuery = "SELECT * FROM sanixazs_main_db.answers WHERE question_id = ?";
$stmt = $conn->prepare($answersQuery);
$stmt->bind_param("i", $currentQuestion['question_id']);
$stmt->execute();
$answersResult = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Python Quiz</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" />
    <style>
        .question-container { display: flex; }
        .question-box { flex: 3; padding: 15px; }
        .answer-box { margin-top: 10px; background-color: #f9f9f9; padding: 15px; border-radius: 5px; }
        .question-nav { flex: 1; padding: 15px; }
        .question-nav button { display: block; margin: 5px 0; }
        .actions { margin-top: 20px; }
    </style>
</head>
<body>

<div class="container">
    <!-- Difficulty Selection Form -->
    <form method="post" action="python_quiz.php" class="mb-4">
        <label for="difficulty" class="form-label">Select Difficulty Level:</label>
        <select name="difficulty" id="difficulty" class="form-select" required>
            <option value="">--Select Difficulty--</option>
            <option value="Beginner">Beginner</option>
            <option value="Intermediate">Intermediate</option>
            <option value="Advanced">Advanced</option>
            <option value="Expert">Expert</option>
        </select>
        <button type="submit" name="select_quiz" class="btn btn-primary mt-3">Start Quiz</button>
    </form>

    <!-- Quiz Section -->
    <?php if (!empty($questions)): ?>
        <div class="question-container">
            <!-- Question Display -->
            <div class="question-box">
                <h4>Question <?php echo $_SESSION['current_question_index'] + 1; ?>: <?php echo htmlspecialchars($currentQuestion['question_text']); ?></h4>
                <div class="answer-box">
                    <form method="post" action="python_quiz.php">
                        <?php if ($answersResult->num_rows > 0): ?>
                            <?php while ($answer = $answersResult->fetch_assoc()): ?>
                                <label>
                                    <input type="radio" name="answer[<?php echo htmlspecialchars($currentQuestion['question_id']); ?>]" value="<?php echo htmlspecialchars($answer['answer_id']); ?>">
                                    <?php echo htmlspecialchars($answer['answer_text']); ?>
                                </label><br>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p>No answers available for this question.</p>
                        <?php endif; ?>
                </div>
            </div>

            <div class="question-nav">
                <h5>Questions</h5>
                <div class="d-flex flex-wrap">
                    <?php for ($i = 0; $i < count($questions); $i++): ?>
                        <form method="post" action="python_quiz.php" style="display:inline;">
                            <button type="submit" name="question_index" value="<?php echo $i; ?>" class="btn btn-outline-primary btn-sm m-1">
                                <?php echo $i + 1; ?>
                            </button>
                        </form>
                    <?php endfor; ?>
                </div>
            </div>
        </div>

        <div class="actions">
            <button type="submit" name="previous" class="btn btn-secondary">Previous</button>
            <button type="submit" name="next" class="btn btn-success">Next</button>
            <button type="submit" name="submit_answers" class="btn btn-primary">Submit Answers</button>
            <button type="submit" name="clear_answer" class="btn btn-danger">Clear Answer</button>
        </div>
        </form> <!-- Close the main form here -->
    <?php else: ?>
        <p>No questions available for this quiz.</p>
    <?php endif; ?>

    <div class="mt-4">
        <a href="user_dashboard.php" class="btn btn-info">Visit Again</a>
    </div>
</div>

</body>
</html>
