<?php


// Start the session if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


include 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: https://sanixtech.in");
    exit;
  }

// Get the logged-in user's ID
$userId = $_SESSION['user_id'];

// Prepare the SQL query to get questions, user answers, correct answers, descriptions, and submission time
$resultsQuery = "
    SELECT 
        q.question_text, 
        a.answer_text AS user_answer, 
        correct_answer.answer_text AS correct_answer, 
        ua.submission_time, 
        correct_description.description AS correct_description,
        IF(a.is_correct = 1, 'Correct', 'Wrong') AS result
    FROM sanixazs_main_db.quiz_questions q
    JOIN sanixazs_main_db.user_answers ua ON q.question_id = ua.question_id
    JOIN sanixazs_main_db.answers a ON ua.selected_answer = a.answer_id
    JOIN sanixazs_main_db.answers correct_answer ON q.correct_answer_id = correct_answer.answer_id
    LEFT JOIN sanixazs_main_db.correct_answer_descriptions correct_description ON q.question_id = correct_description.question_id
    WHERE ua.user_id = ?
";
$stmt = $conn->prepare($resultsQuery);

// Check for preparation errors
if (!$stmt) {
    die("SQL Error: " . $conn->error);
}

$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="card-body">
    <table class="table">
        <thead>
            <tr>
                <th scope="col">S.No</th>
                <th scope="col">Question</th>
                <th scope="col">Correct Answer</th>
                <th scope="col">User Answer</th>
                <th scope="col">Submission Time</th>
                <th scope="col">Is Correct</th>
                <th scope="col">Correct Answer Description</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                $index = 1;
                while ($row = $result->fetch_assoc()) {
                    // Use null coalescing operator for safer access to array keys
                    $correctAnswer = $row['correct_answer'] ?? 'N/A';
                    $submissionTime = $row['submission_time'] ?? 'N/A';
                    $correctDescription = $row['correct_description'] ?? 'N/A';
                    $isCorrect = ($row['user_answer'] === $correctAnswer) ? 'Yes' : 'No';

                    echo "<tr>";
                    echo "<th scope='row'>{$index}</th>";
                    echo "<td>" . htmlspecialchars($row['question_text']) . "</td>";
                    echo "<td>" . htmlspecialchars($correctAnswer) . "</td>";
                    echo "<td>" . htmlspecialchars($row['user_answer']) . "</td>";
                    echo "<td>" . htmlspecialchars($submissionTime) . "</td>";
                    echo "<td>{$isCorrect}</td>";
                    echo "<td>" . htmlspecialchars($correctDescription) . "</td>";
                    echo "</tr>";
                    $index++;
                }
            } else {
                echo "<tr><td colspan='7'>No attempted questions available.</td></tr>";
            }
            ?>
        </tbody>
    </table>
</div>

<?php
$stmt->close();
$conn->close();
?>
