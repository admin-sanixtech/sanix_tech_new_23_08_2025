<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


include 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: https://sanixtech.in");
    exit;
  }

$message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $category_id = $_POST['category_id'];
    $subcategory_id = $_POST['subcategory_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];

    // Insert the post into the database with "pending" status
    $sql = "INSERT INTO posts (category_id, subcategory_id, title, description, createdby, status) 
            VALUES (?, ?, ?, ?, ?, 'pending')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("iissi", $category_id, $subcategory_id, $title, $description, $user_id);

    if ($stmt->execute()) {
        // Send email to the user
        $user_email = ""; // Fetch the user's email from the database
        $user_query = "SELECT email FROM users WHERE user_id = ?";
        $user_stmt = $conn->prepare($user_query);
        $user_stmt->bind_param("i", $user_id);
        $user_stmt->execute();
        $user_result = $user_stmt->get_result();

        if ($user_result->num_rows > 0) {
            $user_row = $user_result->fetch_assoc();
            $user_email = $user_row['email'];
        }

        if ($user_email) {
            $subject = "Thank you for creating a post!";
            $message_body = "
                Hello,<br><br>
                Thank you for creating the post titled: <strong>$title</strong>.<br><br>
                Description: $description<br><br>
                Your post is currently pending admin approval. Once approved, you will receive a notification.<br><br>
                Visit us at <a href='https://sanixtech.in'>sanixtech.in</a>.<br><br>
                Regards,<br>SanixTech Team
            ";

            // Send the email
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= 'From: no-reply@sanixtech.in' . "\r\n";

            mail($user_email, $subject, $message_body, $headers);
        }

        $message = "<div class='alert alert-success'>Post created successfully and sent for admin approval!</div>";
    } else {
        $message = "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}

// Fetch categories for the dropdown
$categories_query = "SELECT category_id, category_name FROM categories";
$categories_result = $conn->query($categories_query);

if (!$categories_result) {
    die("Error fetching categories: " . $conn->error);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create User Post</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/user_styleone.css" />
</head>

<body>
<div class="wrapper">
      <aside id="sidebar" class="js-sidebar">
        <?php include 'user_menu.php'; ?>
      </aside>
      <div class="main">
        <?php include 'user_navbar.php'; ?>
        <main class="content px-3 py-2">
          <div class="container-fluid">
            <div class="mb-3">

<div class="container mt-5">
    <h2>Create a New Post</h2>

    <?php if (!empty($message)) echo $message; ?>

    <form action="user_add_post.php" method="POST">
        <div class="mb-3">
            <label for="category_id" class="form-label">Category</label>
            <select name="category_id" id="category_id" class="form-select" required>
                <option value="">Select Category</option>
                <?php while ($row = $categories_result->fetch_assoc()): ?>
                    <option value="<?php echo $row['category_id']; ?>">
                        <?php echo htmlspecialchars($row['category_name']); ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="subcategory_id" class="form-label">Subcategory</label>
            <select name="subcategory_id" id="subcategory_id" class="form-select" required>
                <option value="">Select Subcategory</option>
                <!-- Subcategories will be populated dynamically based on selected category -->
            </select>
        </div>

        <div class="mb-3">
            <label for="title" class="form-label">Title</label>
            <input type="text" name="title" id="title" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" class="form-control" rows="5" required></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Create Post</button>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Function to load subcategories when a category is selected
document.getElementById('category_id').addEventListener('change', function() {
    var category_id = this.value;

    if (category_id) {
        // Send an AJAX request to fetch subcategories for the selected category
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'get_subcategories.php?category_id=' + category_id, true);
        xhr.onload = function() {
            if (xhr.status === 200) {
                document.getElementById('subcategory_id').innerHTML = xhr.responseText;
            }
        };
        xhr.send();
    } else {
        // Clear subcategory dropdown if no category is selected
        document.getElementById('subcategory_id').innerHTML = '<option value="">Select Subcategory</option>';
    }
});
</script>

</body>
</html>
