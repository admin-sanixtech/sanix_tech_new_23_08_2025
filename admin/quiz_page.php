<?php
// Example categories and subcategories (usually these would be retrieved from a database)
$categories = ['Math', 'Science', 'History'];
$subcategories = [
    'Math' => ['Algebra', 'Geometry', 'Calculus'],
    'Science' => ['Physics', 'Chemistry', 'Biology'],
    'History' => ['Ancient', 'Medieval', 'Modern']
];

// Handle form submission for displaying questions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_category = $_POST['category'] ?? '';
    $selected_subcategory = $_POST['subcategory'] ?? '';
    $question = "Sample question with up to 10,000 characters...";
    // You would typically fetch this question from a database based on category and subcategory
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz Menu - Admin Dashboard</title>
    <link rel="stylesheet" href="css/styles.css"> <!-- Updated path to CSS -->
 <!-- Link to your CSS file -->
</head>
<body>
    <div class="quiz-container">
        <h2>Quiz Menu</h2>
        <form action="admin_dashboard.php?menu=quiz" method="POST">
            <div class="form-group">
                <label for="category">Category:</label>
                <select id="category" name="category" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $category): ?>
                        <option value="<?php echo htmlspecialchars($category); ?>"><?php echo htmlspecialchars($category); ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label for="subcategory">Subcategory:</label>
                <select id="subcategory" name="subcategory" required>
                    <option value="">Select Subcategory</option>
                    <?php
                    if (isset($selected_category) && isset($subcategories[$selected_category])) {
                        foreach ($subcategories[$selected_category] as $subcategory):
                    ?>
                        <option value="<?php echo htmlspecialchars($subcategory); ?>"><?php echo htmlspecialchars($subcategory); ?></option>
                    <?php
                        endforeach;
                    }
                    ?>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Show Question</button>
        </form>

        <?php if (isset($question)): ?>
            <div class="question-section">
                <h3>Question:</h3>
                <p><?php echo htmlspecialchars($question); ?></p>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
