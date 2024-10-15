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
include 'db_connection.php';

// Fetch questions from the database
$quizQuery = "SELECT * FROM sanixazs_main_db.quiz_questions";
$quizResult = $conn->query($quizQuery);

if (!$quizResult) {
    die("Error in query: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Take Quiz</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <h1>Take Quiz</h1>
    <form method="post" action="submit_quiz.php">
        <?php if ($quizResult->num_rows > 0) {
            while ($question = $quizResult->fetch_assoc()) {
                echo "<div>
                        <h4>" . htmlspecialchars($question['question_text']) . "</h4>";
                
                // Fetch answers for the question
                $answersQuery = "SELECT * FROM answers WHERE question_id = ?";
                $stmt = $conn->prepare($answersQuery);

                if (!$stmt) {
                    die("Error preparing statement: " . $conn->error);
                }

                $stmt->bind_param("i", $question['question_id']);
                $stmt->execute();
                $answersResult = $stmt->get_result();

                if ($answersResult->num_rows > 0) {
                    while ($answer = $answersResult->fetch_assoc()) {
                        echo "<label>
                                <input type='radio' name='answer[" . htmlspecialchars($question['question_id']) . "]' value='" . htmlspecialchars($answer['answer_id']) . "'>
                                " . htmlspecialchars($answer['answer_text']) . "
                              </label><br>";
                    }
                } else {
                    echo "<p>No answers available for this question.</p>";
                }

                echo "</div><hr>";
            }
        } else {
            echo "<p>No questions available.</p>";
        } ?>
        <input type="submit" value="Submit Quiz">
    </form>
    <p><a href="user_dashboard.php">Back to Dashboard</a></p>
</body>
</html>
