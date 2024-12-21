<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
  header("Location: https://sanixtech.in");
  exit;
}

// Include database connection
include 'db_connection.php';

// Fetch user information
$userId = $_SESSION['user_id'];
$userQuery = "SELECT * FROM sanixazs_main_db.users WHERE user_id = ?";
$stmt = $conn->prepare($userQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$userResult = $stmt->get_result();
$user = $userResult->fetch_assoc();

// Handle profile photo update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_photo'])) {
    $targetDir = "uploads/";
    $fileName = basename($_FILES['profile_photo']['name']);
    $targetFilePath = $targetDir . $fileName;
    $imageFileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
    $allowedTypes = array('jpg', 'png', 'jpeg', 'gif');

    if (in_array($imageFileType, $allowedTypes)) {
        if (move_uploaded_file($_FILES['profile_photo']['tmp_name'], $targetFilePath)) {
            // Update user photo in the database
            $updatePhotoQuery = "UPDATE sanixazs_main_db.users SET photo = ? WHERE user_id = ?";
            $stmt = $conn->prepare($updatePhotoQuery);
            $stmt->bind_param("si", $fileName, $userId);
            $stmt->execute();

            // Return the path of the uploaded file for the AJAX request
            echo $targetFilePath;
            exit;
        } else {
            echo "Error uploading the file.";
            exit;
        }
    } else {
        echo "Only JPG, JPEG, PNG, and GIF files are allowed.";
        exit;
    }
}

// Fetch quiz statistics
$attemptedQuery = "SELECT COUNT(*) AS attempted FROM sanixazs_main_db.user_quiz_attempts WHERE user_id = ?";
$stmt = $conn->prepare($attemptedQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$attemptedResult = $stmt->get_result();
$attempted = $attemptedResult->fetch_assoc()['attempted'];

// Fetch today's added questions
$todaysDate = date('Y-m-d');
$todayAddedQuery = "SELECT COUNT(*) AS added_today FROM sanixazs_main_db.quiz_questions WHERE DATE(created_at) = ?";
$stmt = $conn->prepare($todayAddedQuery);
$stmt->bind_param("s", $todaysDate);
$stmt->execute();
$todayAddedResult = $stmt->get_result();
$addedToday = $todayAddedResult->fetch_assoc()['added_today'];

// Fetch correct answers for star rating
$correctAnswersQuery = "SELECT COUNT(*) AS correct FROM sanixazs_main_db.user_quiz_attempts WHERE user_id = ? AND is_correct = 1";
$stmt = $conn->prepare($correctAnswersQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$correctAnswersResult = $stmt->get_result();
$correctAnswers = $correctAnswersResult->fetch_assoc()['correct'];
$starRating = floor($correctAnswers / 10); // 1 star for every 10 correct answers

// Display user photo, fallback to default if not available
$photoPath = !empty($user['photo']) ? 'uploads/' . htmlspecialchars($user['photo']) : 'uploads/default_profile.png';
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>User Dashboard</title>
    <link  rel="stylesheet"  href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css"  />
    <script  src="https://kit.fontawesome.com/ae360af17e.js"  crossorigin="anonymous" ></script>
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
              <h4>My Dashboard</h4>
            </div>
            <?php include 'user_dashboard_cnt.php'; ?>
            <!-- Table Element -->
            <div class="card border-0">
              <div class="card-header">
                <h5 class="card-title">User Attempted Questions</h5>
                <h6 class="card-subtitle text-muted">
                  check details of attempted questions
                </h6>
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
