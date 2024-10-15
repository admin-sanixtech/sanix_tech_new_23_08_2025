<?php
include 'db_connection.php'; // Include database connection
session_start();

// Check if form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    foreach ($_POST['answer'] as $question_id => $answer_id) {
        // Insert user's answer into the database
        $stmt = $conn->prepare("INSERT INTO sanixazs_main_db.user_answers (user_id, question_id, selected_answer) VALUES (?, ?, ?)");
        $stmt->bind_param("iii", $_SESSION['user_id'], $question_id, $answer_id);
        $stmt->execute();
    }
}

// Query to get questions, selected answers, and correct answers
$userId = $_SESSION['user_id'];
$resultsQuery = "
    SELECT 
        q.question_text, 
        a.answer_text AS user_answer, 
        IF(a.is_correct = 1, 'Correct', 'Wrong') AS result
    FROM sanixazs_main_db.quiz_questions q
    JOIN sanixazs_main_db.user_answers ua ON q.question_id = ua.question_id
    JOIN sanixazs_main_db.answers a ON ua.selected_answer = a.answer_id
    WHERE ua.user_id = ?
";
$stmt = $conn->prepare($resultsQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Submit Quiz</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        /* Add your styles here */

        /* Styling for the quiz results table */
        .results-table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            font-size: 18px;
            text-align: left;
        }

        .results-table th, .results-table td {
            padding: 12px;
            border-bottom: 1px solid #ddd;
        }

        .results-table th {
            background-color: #333;
            color: white;
        }

        /* Sidebar styles */
        .sidebar {
            width: 200px;
            background-color: #f4f4f4;
            padding: 15px;
        }

        .sidebar ul {
            list-style-type: none;
            padding: 0;
        }

        .sidebar ul li {
            margin-bottom: 10px;
        }

        .sidebar ul li a {
            text-decoration: none;
            font-size: 18px;
            color: #333;
        }

        /* Main content area */
        .main-content {
            flex-grow: 1;
            padding: 20px;
            background-color: #fff;
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        Sanix Technology
    </div>

    <!-- Container to hold sidebar and main content -->
    <div class="container">
        <!-- Sidebar -->
        <div class="sidebar">
            <ul>
                <li><a href="#">Subjects</a></li>
                <li><a href="user_quiz.php">Take Quiz</a></li>
                <li><a href="#">Interview Questions</a></li>
                <li><a href="#">Interview Preparation</a></li>
                <li><a href="logout.php">Logout</a></li>
            </ul>
        </div>

        <!-- Main content area -->
        <div class="main-content">
            <h1>Quiz Results</h1>

            <!-- Quiz Results Table -->
            <table class="results-table">
                <tr>
                    <th>Serial No</th>
                    <th>Question Attended</th>
                    <th>Your Answer</th>
                    <th>Result</th>
                </tr>

                <?php
                if ($result->num_rows > 0) {
                    $serialNo = 1;
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                                <td>" . $serialNo . "</td>
                                <td>" . htmlspecialchars($row['question_text']) . "</td>
                                <td>" . htmlspecialchars($row['user_answer']) . "</td>
                                <td>" . $row['result'] . "</td>
                              </tr>";
                        $serialNo++;
                    }
                } else {
                    echo "<tr><td colspan='4'>No answers available.</td></tr>";
                }
                ?>
            </table>

            <p><a href="user_dashboard.php">Back to Dashboard</a></p>
        </div>
    </div>

</body>
</html>
