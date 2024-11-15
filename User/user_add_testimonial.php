<?php
session_start();
include 'db_connection.php';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You need to log in to submit a testimonial.";
    exit;
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $comment = $_POST['comment'];

    // Fetch the user's email from the database
    $sql = "SELECT email FROM users WHERE user_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($user_email);
    $stmt->fetch();
    $stmt->close();

    // Insert the testimonial into the database
    $sql = "INSERT INTO testimonials (user_id, comment) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $user_id, $comment);
    if ($stmt->execute()) {
        // Send email to the user
        $user_subject = "Testimonial Submitted Successfully";
        $user_message = "Dear user,\n\nYour testimonial has been successfully submitted and will be reviewed by the admin.";
        mail($user_email, $user_subject, $user_message);

        // Send email to the admin
        $admin_email = "admin@sanixtech.in";
        $admin_subject = "New Testimonial Submitted";
        $admin_message = "A new testimonial has been submitted by user ID: $user_id. Please review it.";
        mail($admin_email, $admin_subject, $admin_message);

        // Success message
        $message = "<div class='alert alert-success'>Testimonial submitted successfully! It will be reviewed by an admin.</div>";

        // Show a pop-up dialog (using JavaScript)
        echo "<script>
                alert('Your testimonial has been submitted for approval.');
              </script>";
    } else {
        $message = "<div class='alert alert-danger'>Error: " . $conn->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Submit Testimonial</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" />
    <script src="https://kit.fontawesome.com/ae360af17e.js" crossorigin="anonymous"></script>
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
                <div class="card border-0 mb-4">
                    <div class="card-header">
                        <h2>Submit Your Testimonial</h2>
                    </div>    
                    <div class="card-body">
                        <!-- Display Success or Error Message -->
                        <?php if (!empty($message)) echo $message; ?>

                        <!-- Testimonial Form -->
                        <form action="user_add_testimonial.php" method="POST">
                            <div class="mb-3">
                                <textarea id="comment" name="comment" class="form-control" placeholder="Write your testimonial here..." required rows="4"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
