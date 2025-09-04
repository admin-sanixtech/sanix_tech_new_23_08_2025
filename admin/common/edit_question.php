<!-- edit_question.php -->
<?php
include 'db_connection.php';

$question_id = $_GET['id'] ?? 0;
$sql = "SELECT * FROM questions WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $question_id);
$stmt->execute();
$question = $stmt->get_result()->fetch_assoc();

$sql_answers = "SELECT * FROM answers WHERE question_id = ?";
$stmt = $conn->prepare($sql_answers);
$stmt->bind_param("i", $question_id);
$stmt->execute();
$answers_result = $stmt->get_result();
$answers = $answers_result->fetch_all(MYSQLI_ASSOC);
?>

<h2>Edit Question</h2>
<form action="update_question.php" method="POST">
    <input type="hidden" name="question_id" value="<?php echo htmlspecialchars($question['id']); ?>">
    <div class="form-group">
        <label for="question_text">Question:</label>
        <textarea id="question_text" name="question_text" required><?php echo htmlspecialchars($question['question_text']); ?></textarea>
    </div>
    <div class="form-group">
        <label for="question_type">Question Type:</label>
        <select id="question_type" name="question_type" required>
            <option value="multiple_choice" <?php echo ($question['question_type'] === 'multiple_choice') ? 'selected' : ''; ?>>Multiple Choice</option>
            <option value="true_false" <?php echo ($question['question_type'] === 'true_false') ? 'selected' : ''; ?>>True/False</option>
            <option value="code" <?php echo ($question['question_type'] === 'code') ? 'selected' : ''; ?>>Code</option>
        </select>
    </div>

    <?php if ($question['question_type'] === 'multiple_choice'): ?>
        <h3>Answers:</h3>
        <?php foreach ($answers as $answer): ?>
            <div class="form-group">
                <input type="hidden" name="answer_ids[]" value="<?php echo htmlspecialchars($answer['id']); ?>">
                <input type="text" name="answers[]" value="<?php echo htmlspecialchars($answer['answer_text']); ?>" required>
                <label><input type="radio" name="correct_answer" value="<?php echo htmlspecialchars($answer['id']); ?>" <?php echo ($answer['is_correct']) ? 'checked' : ''; ?>> Correct</label>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>

    <button type="submit" class="btn btn-primary">Update Question</button>
</form>
