<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and store the question in the session
    $_SESSION['question'] = $_POST['question'];

    // Redirect to the answer page
    header("Location: answer_page.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Question Form</title>
</head>
<body>

<form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
    <label for="question">Ask a question:</label>
    <input type="text" id="question" name="question" required>
    <button type="submit">Submit</button>
</form>

</body>
</html>
