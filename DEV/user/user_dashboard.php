<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: https://sanixtech.in");
    exit;
}

// Include DB connection
include 'db_connection.php';

// Get user ID
$userId = $_SESSION['user_id'];

// Fetch user details
$stmt = $conn->prepare("SELECT * FROM sanixazs_main_db.users WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Upload profile photo
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_photo'])) {
    $targetDir = "uploads/";
    $fileName = basename($_FILES['profile_photo']['name']);
    $targetFile = $targetDir . $fileName;
    $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
    $allowed = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($fileType, $allowed)) {
        if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $targetFile)) {
            $stmt = $conn->prepare("UPDATE sanixazs_main_db.users SET photo = ? WHERE user_id = ?");
            $stmt->bind_param("si", $fileName, $userId);
            $stmt->execute();
            $stmt->close();

            echo $targetFile;
            exit;
        } else {
            echo "Error uploading file.";
            exit;
        }
    } else {
        echo "Invalid file type.";
        exit;
    }
}

// Get attempted quiz count
$stmt = $conn->prepare("SELECT COUNT(*) AS attempted FROM sanixazs_main_db.user_quiz_attempts WHERE user_id = ?");
$stmt->bind_param("i", $userId);
$stmt->execute();
$attempted = $stmt->get_result()->fetch_assoc()['attempted'];
$stmt->close();

// Today's added questions
$today = date('Y-m-d');
$stmt = $conn->prepare("SELECT COUNT(*) AS added_today FROM sanixazs_main_db.quiz_questions WHERE DATE(created_at) = ?");
$stmt->bind_param("s", $today);
$stmt->execute();
$addedToday = $stmt->get_result()->fetch_assoc()['added_today'];
$stmt->close();

// Correct answers
$stmt = $conn->prepare("SELECT COUNT(*) AS correct FROM sanixazs_main_db.user_quiz_attempts WHERE user_id = ? AND is_correct = 1");
$stmt->bind_param("i", $userId);
$stmt->execute();
$correct = $stmt->get_result()->fetch_assoc()['correct'];
$stmt->close();

$starRating = floor($correct / 10);

// Photo fallback
$photoPath = !empty($user['photo']) ? 'uploads/' . htmlspecialchars($user['photo']) : 'uploads/default_profile.png';
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>User Dashboard</title>
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
                <div class="mb-3">
                    <h4>Welcome, <?= htmlspecialchars($user['name']); ?></h4>
                </div>

                <?php include 'user_dashboard_cnt.php'; ?>

                <div class="card border-0">
                    <div class="card-header">
                        <h5 class="card-title">User Attempted Questions</h5>
                        <h6 class="card-subtitle text-muted">Check details of attempted questions</h6>
                    </div>
                    <?php include 'user_dashboard_tbl_cntnt.php'; ?>
                </div>
            </div>
        </main>

        <a href="#" class="theme-toggle">
            <i class="fa-regular fa-moon"></i>
            <i class="fa-regular fa-sun"></i>
        </a>

        <?php include 'user_footer.php'; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/script.js"></script>
</body>
</html>
