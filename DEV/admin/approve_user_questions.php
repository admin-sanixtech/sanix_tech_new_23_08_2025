<?php
session_start();
include 'db_connection.php';

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Handle approve/reject actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question_id = $_POST['question_id'];
    $action = $_POST['action'];

    // Update status based on the action (approve or reject)
    if ($action === 'approve') {
        $sql_update = "UPDATE quiz_questions SET status = 'approved' WHERE question_id = ?";
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("i", $question_id);
        $stmt->execute();
    } elseif ($action === 'reject') {
        $sql_update = "UPDATE quiz_questions SET status = 'rejected' WHERE question_id = ?";
        $stmt = $conn->prepare($sql_update);
        $stmt->bind_param("i", $question_id);
        $stmt->execute();
    }

    // Refresh the page after the action to reflect changes
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch pending questions
$sql = "SELECT * FROM quiz_questions WHERE status = 'pending'";
$result = $conn->query($sql);

// Debugging output if needed
if (!$result) {
    die("Error in query: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Questions</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container">
    <h3 class="mt-4">Pending Questions</h3>
    <table class="table table-bordered table-striped mt-3">
        <thead>
            <tr>
                <th>ID</th>
                <th>Category</th>
                <th>Subcategory</th>
                <th>Question Text</th>
                <th>Question Type</th>
                <th>Options</th>
                <th>Correct Answer</th>
                <th>Created By</th>
                <th>Approve</th>
                <th>Reject</th>
            </tr>
        </thead>
        <tbody>
            <?php if ($result->num_rows > 0): ?>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['question_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['category_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['subcategory_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['question_text']); ?></td>
                        <td><?php echo htmlspecialchars($row['question_type']); ?></td>
                        <td>
                            A: <?php echo htmlspecialchars($row['option_a']); ?><br>
                            B: <?php echo htmlspecialchars($row['option_b']); ?><br>
                            C: <?php echo htmlspecialchars($row['option_c']); ?><br>
                            D: <?php echo htmlspecialchars($row['option_d']); ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['correct_answer']); ?></td>
                        <td><?php echo htmlspecialchars($row['created_by']); ?></td>
                        <td>
                            <form action="" method="POST">
                                <input type="hidden" name="question_id" value="<?php echo htmlspecialchars($row['question_id']); ?>">
                                <input type="hidden" name="action" value="approve">
                                <button type="submit" class="btn btn-success">Approve</button>
                            </form>
                        </td>
                        <td>
                            <form action="" method="POST">
                                <input type="hidden" name="question_id" value="<?php echo htmlspecialchars($row['question_id']); ?>">
                                <input type="hidden" name="action" value="reject">
                                <button type="submit" class="btn btn-danger">Reject</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="10" class="text-center">No pending questions found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
