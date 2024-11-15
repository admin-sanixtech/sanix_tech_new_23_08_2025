<?php
session_start();
include 'db_connection.php';

// Ensure user is logged in and has admin privileges
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "Access denied. You must be an admin to view this page.";
    exit;
}

// Fetch all quiz questions from the database
$sql = "SELECT qq.question_id, qq.question_text, qq.question_type, qq.option_a, qq.option_b, qq.option_c, qq.option_d, qq.correct_answer, 
        qq.code_snippet, c.category_name, sc.subcategory_name, qq.created_at, qq.updated_at 
        FROM sanixazs_main_db.quiz_questions qq
        JOIN sanixazs_main_db.categories c ON qq.category_id = c.category_id
        JOIN sanixazs_main_db.subcategories sc ON qq.subcategory_id = sc.subcategory_id";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Quiz Questions</title>
    <link rel="stylesheet" href="css/admin_styles.css">
</head>
<body>
    
    <h3>All Quiz Questions</h3>
    
    <table border="1" cellpadding="10">
        <thead>
            <tr>
                <th>Question ID</th>
                <th>Category</th>
                <th>Subcategory</th>
                <th>Question Text</th>
                <th>Question Type</th>
                <th>Options</th>
                <th>Correct Answer</th>
                <th>Code Snippet</th>
                <th>Created At</th>
                <th>Updated At</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row['question_id'] . "</td>";
                    echo "<td>" . htmlspecialchars($row['category_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['subcategory_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['question_text']) . "</td>";
                    echo "<td>" . htmlspecialchars($row['question_type']) . "</td>";
                    echo "<td>";
                    if ($row['question_type'] === 'multiple_choice') {
                        echo "A: " . htmlspecialchars($row['option_a']) . "<br>";
                        echo "B: " . htmlspecialchars($row['option_b']) . "<br>";
                        echo "C: " . htmlspecialchars($row['option_c']) . "<br>";
                        echo "D: " . htmlspecialchars($row['option_d']) . "<br>";
                    } elseif ($row['question_type'] === 'true_false') {
                        echo "True/False";
                    }
                    echo "</td>";
                    echo "<td>" . htmlspecialchars($row['correct_answer']) . "</td>";
                    echo "<td>" . nl2br(htmlspecialchars($row['code_snippet'])) . "</td>";
                    echo "<td>" . $row['created_at'] . "</td>";
                    echo "<td>" . $row['updated_at'] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='10'>No questions found</td></tr>";
            }
            ?>
        </tbody>
    </table>

</body>
</html>
