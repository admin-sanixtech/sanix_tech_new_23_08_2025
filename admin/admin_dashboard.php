<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
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
    <title>Admin Dashboard</title>
    <link  rel="stylesheet"  href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css"  />
    <script src="https://kit.fontawesome.com/ae360af17e.js"  crossorigin="anonymous" ></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
	  <link rel="stylesheet" href="css/admin_styleone.css" />
  </head>

  <body>
    <div class="wrapper">
      <aside id="sidebar" class="js-sidebar">
        <?php include 'admin_menu.php'; ?>
      </aside>
      <div class="main">
        <?php include 'admin_navbar.php'; ?>
        <main class="content px-3 py-2">
          <div class="container-fluid">
            <div class="mb-3">
              <h4>Admin Dashboard</h4>
            </div>
            <?php include 'admin_dashboard_cnt.php'; ?>
            <!-- Table Element -->
            <div class="card border-0">
              <div class="card-header">
                <h5 class="card-title">Basic Table</h5>
                <h6 class="card-subtitle text-muted">
                  Lorem ipsum dolor sit amet consectetur adipisicing elit.
                  Voluptatum ducimus, necessitatibus reprehenderit itaque!
                </h6>
              </div>
              <div class="card-body">
                <table class="table">
                  <thead>
                    <tr>
                      <th scope="col">#</th>
                      <th scope="col">First</th>
                      <th scope="col">Last</th>
                      <th scope="col">Handle</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <th scope="row">1</th>
                      <td>Mark</td>
                      <td>Otto</td>
                      <td>@mdo</td>
                    </tr>
                    <tr>
                      <th scope="row">2</th>
                      <td>Jacob</td>
                      <td>Thornton</td>
                      <td>@fat</td>
                    </tr>
                    <tr>
                      <th scope="row">3</th>
                      <td colspan="2">Larry the Bird</td>
                      <td>@twitter</td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </main>
        <a href="#" class="theme-toggle">
          <i class="fa-regular fa-moon"></i>
          <i class="fa-regular fa-sun"></i>
        </a>
        <?php include 'admin_footer.php'; ?>
      </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/script.js"></script>
    <script>
        // Sample data for the bar chart
        const courseLabels = ['Course 1', 'Course 2', 'Course 3', 'Course 4', 'Course 5']; // Replace with actual course names
        const courseData = [12, 19, 3, 5, 2]; // Replace with actual data

        const ctxBar = document.getElementById('coursesBarChart').getContext('2d');
        const coursesBarChart = new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: courseLabels,
                datasets: [{
                    label: 'Number of Students',
                    data: courseData,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Sample data for the pie chart
        const visitorLabels = ['Direct', 'Referral', 'Social Media', 'Organic Search']; // Replace with actual visitor sources
        const visitorData = [300, 150, 100, 200]; // Replace with actual visitor counts

        const ctxPie = document.getElementById('visitorsPieChart').getContext('2d');
        const visitorsPieChart = new Chart(ctxPie, {
            type: 'pie',
            data: {
                labels: visitorLabels,
                datasets: [{
                    label: 'Visitors',
                    data: visitorData,
                    backgroundColor: [
                        'rgba(255, 99, 132, 0.2)',
                        'rgba(54, 162, 235, 0.2)',
                        'rgba(255, 206, 86, 0.2)',
                        'rgba(75, 192, 192, 0.2)'
                    ],
                    borderColor: [
                        'rgba(255, 99, 132, 1)',
                        'rgba(54, 162, 235, 1)',
                        'rgba(255, 206, 86, 1)',
                        'rgba(75, 192, 192, 1)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: true,
                        text: 'Visitors Source'
                    }
                }
            }
        });
        </script>

  </body>
</html>
