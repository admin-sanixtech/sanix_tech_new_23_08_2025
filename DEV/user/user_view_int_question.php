<?php
// Include database connection file
include('db_connection.php');

// Start the session if needed (to check for user login or other session variables)
session_start();

// Get category_id from the URL (Make sure to sanitize and validate it)
$category_id = isset($_GET['category_id']) ? (int)$_GET['category_id'] : null; // Cast to integer to prevent SQL injection

// Check if category_id is valid
if (!$category_id) {
    echo "Please select a valid category.";
    exit;
}

// Prepare the SQL query to fetch questions based on the selected category
$sql = "SELECT * FROM quiz_questions WHERE category_id = ?";
$stmt = $conn->prepare($sql);

// Check if the prepare was successful
if ($stmt === false) {
    die('Error preparing the query: ' . $conn->error);
}

// Bind the parameter and execute the query
$stmt->bind_param("i", $category_id);
$stmt->execute();

// Get the result of the query
$result = $stmt->get_result();

// Close the statement and connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Interview Questions</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/ae360af17e.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/user_styleone.css">
</head>
<body>
    <div class="wrapper">
        <aside id="sidebar" class="js-sidebar">
            <?php include 'user_menu.php'; ?>
        </aside>
        <div class="main">
            <?php include 'user_navbar.php'; ?>
            
            <div class="container">
                <h2>Interview Questions</h2>
                <div class="questions-list">
                    <?php
                    // Check if any results were returned
                    if ($result->num_rows > 0) {
                        // Fetch and display the questions
                        while ($question = $result->fetch_assoc()) {
                            // Extract the relevant fields and ensure no empty data is displayed
                            $questionText = isset($question['question_text']) ? htmlspecialchars($question['question_text'], ENT_QUOTES, 'UTF-8') : 'No question available';
                            $optionA = isset($question['option_a']) ? htmlspecialchars($question['option_a'], ENT_QUOTES, 'UTF-8') : 'No option A available';
                            $optionB = isset($question['option_b']) ? htmlspecialchars($question['option_b'], ENT_QUOTES, 'UTF-8') : 'No option B available';
                            $optionC = isset($question['option_c']) ? htmlspecialchars($question['option_c'], ENT_QUOTES, 'UTF-8') : 'No option C available';
                            $optionD = isset($question['option_d']) ? htmlspecialchars($question['option_d'], ENT_QUOTES, 'UTF-8') : 'No option D available';
                            $correctAnswer = isset($question['correct_answer']) ? htmlspecialchars($question['correct_answer'], ENT_QUOTES, 'UTF-8') : 'No correct answer available';

                            // Display the question and options
                            echo "<div class='question'>";
                            echo "<div class='question-text'><strong>Question: </strong>$questionText</div>";
                            echo "<div class='options'>";
                            echo "<strong>Options: </strong>";
                            echo "<ul>";
                            echo "<li>A: $optionA</li>";
                            echo "<li>B: $optionB</li>";
                            echo "<li>C: $optionC</li>";
                            echo "<li>D: $optionD</li>";
                            echo "</ul>";
                            echo "</div>";
                            echo "<div class='correct-answer'><strong>Correct Answer: </strong>$correctAnswer</div>";
                            echo "</div><br>";
                        }
                    } else {
                        echo "No questions found for this category.";
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
