<?php
session_start();
include 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo "You must be logged in to submit your experience.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id']; // Assuming user_id is stored in session
    $experience_text = trim($_POST['experience_text']);

    if (!empty($experience_text)) {
        $sql = "INSERT INTO user_interview_experience (user_id, experience_text) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("is", $user_id, $experience_text);
            if ($stmt->execute()) {
                echo "Your experience has been submitted for approval.";
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Error preparing the statement: " . $conn->error;
        }
    } else {
        echo "Please enter your experience before submitting.";
    }
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Share Your Interview Experience</title>
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
                <div class="card border-0">
                    <div class="card-header">
                        <h2>Share Your Interview Experience</h2>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="" class="mb-3">
                            <div class="mb-3">
                                <textarea name="experience_text" class="form-control" rows="5" placeholder="Describe your experience"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
            <?php include 'user_footer.php'; ?>
        </main>
    </div>
</div>
</body>
</html>
