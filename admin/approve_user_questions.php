<?php
session_start();
include 'db_connection.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Fetch all pending questions
$sql = "SELECT * FROM pending_questions";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Approve User Questions</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css">
    <script src="https://kit.fontawesome.com/ae360af17e.js" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="css/admin_styleone.css" />
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar (admin menu) -->
        <aside class="col-md-3">
            <?php include 'admin_menu.php'; ?>
        </aside>

        <!-- Main Content -->
        <main class="col-md-9">
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
                            <th>Action</th>
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
                                            <button type="submit" class="btn btn-success">Approve</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center">No pending questions found.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Handle the form submission when the approve button is clicked
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question_id = $_POST['question_id'];

    // Fetch question from pending_questions
    $sql = "SELECT * FROM pending_questions WHERE question_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $question_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $question = $result->fetch_assoc();

    if ($question) {
        // Insert into quiz_questions table
        $sql_insert = "INSERT INTO quiz_questions (category_id, subcategory_id, question_text, question_type, option_a, option_b, option_c, option_d, correct_answer, created_by) 
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param(
            "iisssssssi",
            $question['category_id'],
            $question['subcategory_id'],
            $question['question_text'],
            $question['question_type'],
            $question['option_a'],
            $question['option_b'],
            $question['option_c'],
            $question['option_d'],
            $question['correct_answer'],
            $question['created_by']
        );

        if ($stmt_insert->execute()) {
            // Delete from pending_questions
            $sql_delete = "DELETE FROM pending_questions WHERE question_id = ?";
            $stmt_delete = $conn->prepare($sql_delete);
            $stmt_delete->bind_param("i", $question_id);
            $stmt_delete->execute();

            // Increment Sanix Coin for the user in user_coins table
            $user_id = $question['created_by'];
            $sql_check = "SELECT coins FROM user_coins WHERE user_id = ?";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->bind_param("i", $user_id);
            $stmt_check->execute();
            $result_check = $stmt_check->get_result();

            if ($result_check->num_rows > 0) {
                $sql_update = "UPDATE user_coins SET coins = coins + 1 WHERE user_id = ?";
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->bind_param("i", $user_id);
                $stmt_update->execute();
            } else {
                $sql_insert_coins = "INSERT INTO user_coins (user_id, coins) VALUES (?, 1)";
                $stmt_insert_coins = $conn->prepare($sql_insert_coins);
                $stmt_insert_coins->bind_param("i", $user_id);
                $stmt_insert_coins->execute();
            }

            echo "<div class='alert alert-success'>Question approved, added to quiz, and Sanix Coin awarded to the user.</div>";
        } else {
            echo "<div class='alert alert-danger'>Error inserting question into quiz_questions: " . $stmt_insert->error . "</div>";
        }
    } else {
        echo "<div class='alert alert-danger'>Error: Question not found.</div>";
    }
}

$conn->close();
?>
