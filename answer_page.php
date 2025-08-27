<?php
session_start();

// Check if the question is set in the session
if (!isset($_SESSION['question'])) {
    // Redirect to the question form if no question is set
    header("Location: question_form.php");
    exit;
}

// Get the question from the session
$question = $_SESSION['question'];

// Process the answer if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $answer = $_POST['answer'];
    // You can do something with the answer here, e.g., store it in a database

    // For simplicity, let's just display the question and answer on this page
    echo "<p><strong>Question:</strong> $question</p>";
    echo "<p><strong>Answer:</strong> $answer</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Answer Page</title>
</head>
<body>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <p><strong>Question:</strong> <?php echo $question; ?></p>
    <label for="answer">Type your answer:</label>
    <input type="text" id="answer" name="answer" required>
    <button type="submit">Submit Answer</button>
</form>

</body>
</html>
