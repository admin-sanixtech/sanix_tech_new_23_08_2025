<?php
//admin_dashboard.php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Define possible paths for db_connection.php
$possiblePaths = [
    __DIR__ . '/../config/db_connection.php',   // ../config/
    __DIR__ . '/config/db_connection.php',     // current/config/
    __DIR__ . '/../../config/db_connection.php', // ../../config/
    __DIR__ . '/db_connection.php',            // same folder
    __DIR__ . '/home2/sanixazs/public_html/admin/config/db_connection.php',            // same folder
    __DIR__ . '/admin/config/db_connection.php',            // same folder
];

// Flag for tracking inclusion
$fileIncluded = false;
foreach ($possiblePaths as $path) {
    if (file_exists($path)) {
        include $path;
        $fileIncluded = true;
        break;
    }
}

if (!$fileIncluded) {
    die("Error: db_connection.php not found in any expected locations.");
}

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: http://sanixtech.in/login.php"); 
    exit;
}

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
            $updatePhotoQuery = "UPDATE sanixazs_main_db.users SET photo = ? WHERE user_id = ?";
            $stmt = $conn->prepare($updatePhotoQuery);
            $stmt->bind_param("si", $fileName, $userId);
            $stmt->execute();
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

// Today's date for filtering
$todaysDate = date('Y-m-d');

// =================== TOTAL COUNTS ===================
// Total user count
$totalUserCount = $conn->query("SELECT COUNT(*) as count FROM sanixazs_main_db.users")->fetch_assoc()['count'];

// Total categories count
$totalCategoriesCount = $conn->query("SELECT COUNT(*) as count FROM sanixazs_main_db.categories")->fetch_assoc()['count'];

// Total subcategories count
$totalSubCategoriesCount = $conn->query("SELECT COUNT(*) as count FROM sanixazs_main_db.subcategories")->fetch_assoc()['count'];

// Total jobs count
$totalJobsCount = $conn->query("SELECT COUNT(*) as count FROM sanixazs_main_db.job_post")->fetch_assoc()['count'];

// Total posts count
$totalPostsCount = $conn->query("SELECT COUNT(*) as count FROM sanixazs_main_db.posts")->fetch_assoc()['count'];

// Total testimonials count
$totalTestimonialsCount = $conn->query("SELECT COUNT(*) as count FROM sanixazs_main_db.testimonials")->fetch_assoc()['count'];

// Total quiz questions count
$totalQuizQuestionsCount = $conn->query("SELECT COUNT(*) as count FROM sanixazs_main_db.quiz_questions")->fetch_assoc()['count'];

// =================== APPROVED COUNTS ===================
// Approved categories count
$approvedCategoriesCount = $conn->query("SELECT COUNT(*) as count FROM sanixazs_main_db.categories WHERE status = 'approved'")->fetch_assoc()['count'];

// Approved subcategories count
$approvedSubCategoriesCount = $conn->query("SELECT COUNT(*) as count FROM sanixazs_main_db.subcategories WHERE status = 'approved'")->fetch_assoc()['count'];

// Approved jobs count
$approvedJobsCount = $conn->query("SELECT COUNT(*) as count FROM sanixazs_main_db.job_post WHERE status = 'approved'")->fetch_assoc()['count'];

// Approved posts count
$approvedPostsCount = $conn->query("SELECT COUNT(*) as count FROM sanixazs_main_db.posts WHERE status = 'approved'")->fetch_assoc()['count'];

// Approved quiz questions count
$approvedQuizQuestionsCount = $conn->query("SELECT COUNT(*) as count FROM sanixazs_main_db.quiz_questions WHERE status = 'approved'")->fetch_assoc()['count'];

// =================== TODAY'S DATA ===================
// Today's added users
$todayUsersQuery = "SELECT user_id, username, email, created_at FROM sanixazs_main_db.users WHERE DATE(created_at) = ? ORDER BY created_at DESC LIMIT 10";
$stmt = $conn->prepare($todayUsersQuery);
$stmt->bind_param("s", $todaysDate);
$stmt->execute();
$todayUsersResult = $stmt->get_result();
$todayUsers = [];
while ($row = $todayUsersResult->fetch_assoc()) {
    $todayUsers[] = $row;
}

// Latest 10 quiz results
$latestQuizQuery = "SELECT qr.*, u.username, qq.question_text 
                   FROM sanixazs_main_db.quiz_results qr 
                   JOIN sanixazs_main_db.users u 
                        ON qr.user_id = u.user_id 
                   JOIN sanixazs_main_db.quiz_questions qq 
                        ON qr.question_id = qq.question_id 
                   ORDER BY qr.attempted_at DESC 
                   LIMIT 10";

$latestQuizResult = $conn->query($latestQuizQuery);
$latestQuizzes = [];
while ($row = $latestQuizResult->fetch_assoc()) {
    $latestQuizzes[] = $row;
}

// Quiz statistics for current user
$attemptedQuery = "SELECT COUNT(*) AS attempted FROM sanixazs_main_db.user_quiz_attempts WHERE user_id = ?";
$stmt = $conn->prepare($attemptedQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$attemptedResult = $stmt->get_result();
$attempted = $attemptedResult->fetch_assoc()['attempted'];

// Correct answers for current user
$correctAnswersQuery = "SELECT COUNT(*) AS correct FROM sanixazs_main_db.user_quiz_attempts WHERE user_id = ? AND is_correct = 1";
$stmt = $conn->prepare($correctAnswersQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$correctAnswersResult = $stmt->get_result();
$correctAnswers = $correctAnswersResult->fetch_assoc()['correct'];
$starRating = floor($correctAnswers / 10);

// Display user photo, fallback to default if not available
$photoPath = !empty($user['photo']) ? 'uploads/' . htmlspecialchars($user['photo']) : 'uploads/default_profile.png';
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
  <head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard - Sanix Technologies</title>
    <link  rel="stylesheet"  href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css"  />
    <script src="https://kit.fontawesome.com/ae360af17e.js"  crossorigin="anonymous" ></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <?php
        $cssPath = __DIR__ . "/../../css/admin_styleone.css"; 

        if (file_exists($cssPath)) {
            echo '<link rel="stylesheet" href="../../css/admin_styleone.css">';
        } else {
            echo "<!-- CSS file not found: ../../css/admin_styleone.css -->";
        }
        ?>

    
    <style>
        /* Modern Dashboard Enhancements */
        .main {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            position: relative;
        }

        .main::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 25% 25%, rgba(255,255,255,0.1) 0%, transparent 50%),
                radial-gradient(circle at 75% 75%, rgba(255,255,255,0.05) 0%, transparent 50%);
            pointer-events: none;
        }

        .content {
            position: relative;
            z-index: 2;
        }

        /* Welcome Section */
        .welcome-section {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            color: white;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .welcome-title {
            font-size: 2.5rem;
            font-weight: 700;
            background: linear-gradient(45deg, #ffffff, #e3f2fd);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 0.5rem;
        }

        .welcome-subtitle {
            color: rgba(255, 255, 255, 0.8);
            font-size: 1.1rem;
        }

        /* Enhanced Cards */
        .modern-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: none;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow: hidden;
            position: relative;
        }

        .modern-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #667eea, #764ba2, #f093fb, #f5576c);
        }

        .modern-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 50px rgba(0, 0, 0, 0.15);
        }

        .card-header-modern {
            background: transparent;
            border: none;
            padding: 1.5rem 1.5rem 0;
        }

        .card-body-modern {
            padding: 1.5rem;
        }

        /* Stats Cards */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .stats-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            padding: 1.2rem;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .stats-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, var(--card-gradient-start), var(--card-gradient-end));
            opacity: 0.1;
            transition: opacity 0.3s ease;
        }

        .stats-card:hover::before {
            opacity: 0.2;
        }

        .stats-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 12px 35px rgba(0, 0, 0, 0.15);
        }

        .stats-card.users { --card-gradient-start: #667eea; --card-gradient-end: #764ba2; }
        .stats-card.categories { --card-gradient-start: #f093fb; --card-gradient-end: #f5576c; }
        .stats-card.jobs { --card-gradient-start: #4facfe; --card-gradient-end: #00f2fe; }
        .stats-card.posts { --card-gradient-start: #43e97b; --card-gradient-end: #38f9d7; }
        .stats-card.testimonials { --card-gradient-start: #fa709a; --card-gradient-end: #fee140; }
        .stats-card.quizzes { --card-gradient-start: #a8edea; --card-gradient-end: #fed6e3; }
        .stats-card.approved { --card-gradient-start: #96fbc4; --card-gradient-end: #f9f586; }

        .stats-icon {
            width: 50px;
            height: 50px;
            background: linear-gradient(135deg, var(--card-gradient-start), var(--card-gradient-end));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 0.8rem;
            color: white;
            font-size: 1.2rem;
        }

        .stats-number {
            font-size: 1.8rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 0.3rem;
        }

        .stats-label {
            color: #7f8c8d;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.8rem;
        }

        /* Chart Cards */
        .chart-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 1.5rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            height: 400px; /* Fixed height for charts */
        }

        .chart-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .chart-title i {
            color: #667eea;
        }

        /* Chart canvas sizing */
        .chart-canvas {
            width: 100% !important;
            max-height: 300px !important;
        }

        /* Table Enhancements */
        .modern-table {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .table {
            margin-bottom: 0;
        }

        .table thead th {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.85rem;
            padding: 1rem;
        }

        .table tbody tr {
            border: none;
        }

        .table tbody tr:nth-child(even) {
            background: rgba(102, 126, 234, 0.05);
        }

        .table tbody td {
            padding: 0.8rem 1rem;
            vertical-align: middle;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
            font-size: 0.9rem;
        }

        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .action-btn {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 1rem 1.2rem;
            border-radius: 15px;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.3);
            border: none;
            font-weight: 600;
            font-size: 0.9rem;
        }

        .action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .action-btn i {
            display: block;
            font-size: 1.3rem;
            margin-bottom: 0.5rem;
        }

        /* Section Headers */
        .section-header {
            color: white;
            font-size: 1.5rem;
            font-weight: 700;
            margin: 2rem 0 1rem 0;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            text-align: center;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .welcome-title {
                font-size: 2rem;
            }
            
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
                gap: 0.8rem;
            }
            
            .stats-card {
                padding: 1rem;
            }
            
            .stats-number {
                font-size: 1.5rem;
            }
            
            .chart-container {
                height: 350px;
                padding: 1rem;
            }
        }

        /* Animation keyframes */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeInUp 0.6s ease-out forwards;
        }

        /* Badge styles */
        .status-badge {
            padding: 0.4rem 0.8rem;
            border-radius: 50px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .badge-correct {
            background: #d4edda;
            color: #155724;
        }

        .badge-incorrect {
            background: #f8d7da;
            color: #721c24;
        }
    </style>
  </head>

  <body>
    <div class="wrapper">
      <aside id="sidebar" class="js-sidebar">
        <?php
          $menuPath = __DIR__ . '/admin_menu.php';

          if (file_exists($menuPath)) {
              include $menuPath;
          } else {
              die("Error: File not found at $menuPath");
          }
        ?>
      </aside>
      <div class="main">

        <?php
          $navbarPath = __DIR__ . '/common/admin_navbar.php';

          if (file_exists($navbarPath)) {
              include $navbarPath;
          } else {
              die("Error: File not found at $navbarPath");
          }
        ?>

        <main class="content px-3 py-2">
          <div class="container-fluid">
            
            <!-- Welcome Section -->
            <div class="welcome-section animate-fade-in">
              <div class="row align-items-center">
                <div class="col-md-8">
                  <h1 class="welcome-title">Welcome back, <?php echo htmlspecialchars($user['username'] ?? 'Admin'); ?>! 👋</h1>
                  <p class="welcome-subtitle">Here's what's happening with your platform today</p>
                </div>
                <div class="col-md-4 text-end">
                  <div class="d-flex align-items-center justify-content-end gap-3">
                    <img src="<?php echo $photoPath; ?>" alt="Profile" class="rounded-circle" style="width: 60px; height: 60px; object-fit: cover; border: 3px solid rgba(255,255,255,0.3);">
                    <div class="text-start">
                      <div class="text-white fw-bold"><?php echo date('l'); ?></div>
                      <div class="text-white-50"><?php echo date('M d, Y'); ?></div>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Total Counts Section -->
            <div class="section-header">📊 Total Platform Statistics</div>
            <div class="stats-grid animate-fade-in">
              <div class="stats-card users">
                <div class="stats-icon">
                  <i class="fas fa-users"></i>
                </div>
                <div class="stats-number"><?php echo $totalUserCount; ?></div>
                <div class="stats-label">Total Users</div>
              </div>
              
              <div class="stats-card categories">
                <div class="stats-icon">
                  <i class="fas fa-list"></i>
                </div>
                <div class="stats-number"><?php echo $totalCategoriesCount; ?></div>
                <div class="stats-label">Total Categories</div>
              </div>
              
              <div class="stats-card categories">
                <div class="stats-icon">
                  <i class="fas fa-sitemap"></i>
                </div>
                <div class="stats-number"><?php echo $totalSubCategoriesCount; ?></div>
                <div class="stats-label">Sub Categories</div>
              </div>
              
              <div class="stats-card jobs">
                <div class="stats-icon">
                  <i class="fas fa-briefcase"></i>
                </div>
                <div class="stats-number"><?php echo $totalJobsCount; ?></div>
                <div class="stats-label">Total Jobs</div>
              </div>
              
              <div class="stats-card posts">
                <div class="stats-icon">
                  <i class="fas fa-file-alt"></i>
                </div>
                <div class="stats-number"><?php echo $totalPostsCount; ?></div>
                <div class="stats-label">Total Posts</div>
              </div>
              
              <div class="stats-card testimonials">
                <div class="stats-icon">
                  <i class="fas fa-star"></i>
                </div>
                <div class="stats-number"><?php echo $totalTestimonialsCount; ?></div>
                <div class="stats-label">Testimonials</div>
              </div>
              
              <div class="stats-card quizzes">
                <div class="stats-icon">
                  <i class="fas fa-question-circle"></i>
                </div>
                <div class="stats-number"><?php echo $totalQuizQuestionsCount; ?></div>
                <div class="stats-label">Quiz Questions</div>
              </div>
            </div>

            <!-- Approved Counts Section -->
            <div class="section-header">✅ Approved Content Statistics</div>
            <div class="stats-grid animate-fade-in">
              <div class="stats-card approved">
                <div class="stats-icon">
                  <i class="fas fa-check-circle"></i>
                </div>
                <div class="stats-number"><?php echo $approvedCategoriesCount; ?></div>
                <div class="stats-label">Approved Categories</div>
              </div>
              
              <div class="stats-card approved">
                <div class="stats-icon">
                  <i class="fas fa-check-double"></i>
                </div>
                <div class="stats-number"><?php echo $approvedSubCategoriesCount; ?></div>
                <div class="stats-label">Approved Sub Categories</div>
              </div>
              
              <div class="stats-card approved">
                <div class="stats-icon">
                  <i class="fas fa-briefcase"></i>
                </div>
                <div class="stats-number"><?php echo $approvedJobsCount; ?></div>
                <div class="stats-label">Approved Jobs</div>
              </div>
              
              <div class="stats-card approved">
                <div class="stats-icon">
                  <i class="fas fa-file-check"></i>
                </div>
                <div class="stats-number"><?php echo $approvedPostsCount; ?></div>
                <div class="stats-label">Approved Posts</div>
              </div>
              
              <div class="stats-card approved">
                <div class="stats-icon">
                  <i class="fas fa-question"></i>
                </div>
                <div class="stats-number"><?php echo $approvedQuizQuestionsCount; ?></div>
                <div class="stats-label">Approved Quiz Questions</div>
              </div>
            </div>

            <!-- Quick Actions -->
            <div class="quick-actions animate-fade-in">
              <a href="/admin/admin_add_question.php" class="action-btn">
                <i class="fas fa-plus"></i>
                Add Question
              </a>
              <a href="/admin/admin_create_post.php" class="action-btn">
                <i class="fas fa-edit"></i>
                Create Post
              </a>
              <a href="users_details.php" class="action-btn">
                <i class="fas fa-users"></i>
                Manage Users
              </a>
              <a href="/admin/projects_view.php" class="action-btn">
                <i class="fas fa-project-diagram"></i>
                View Projects
              </a>
            </div>

            <!-- Charts Row -->
            <div class="row animate-fade-in">
              <div class="col-lg-6">
                <div class="chart-container">
                  <h5 class="chart-title">
                    <i class="fas fa-chart-bar"></i>
                    Content Distribution
                  </h5>
                  <canvas id="contentBarChart" class="chart-canvas"></canvas>
                </div>
              </div>
              <div class="col-lg-6">
                <div class="chart-container">
                  <h5 class="chart-title">
                    <i class="fas fa-chart-pie"></i>
                    Approval Status
                  </h5>
                  <canvas id="approvalPieChart" class="chart-canvas"></canvas>
                </div>
              </div>
            </div>

            <!-- Tables Row -->
            <div class="row animate-fade-in">
              <!-- Today's Added Users -->
              <div class="col-lg-6 mb-4">
                <div class="modern-card modern-table">
                  <div class="card-header-modern">
                    <h5 class="chart-title">
                      <i class="fas fa-user-plus"></i>
                      Today's New Users
                    </h5>
                    <p class="text-muted mb-0">Users registered today (<?php echo date('M d, Y'); ?>)</p>
                  </div>
                  <div class="card-body-modern p-0">
                    <?php if (count($todayUsers) > 0): ?>
                    <table class="table">
                      <thead>
                        <tr>
                          <th scope="col">Username</th>
                          <th scope="col">Email</th>
                          <th scope="col">Time</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($todayUsers as $index => $todayUser): ?>
                        <tr>
                          <td>
                            <div class="d-flex align-items-center">
                              <img src="uploads/default_profile.png" class="rounded-circle me-2" width="28" height="28" alt="">
                              <?php echo htmlspecialchars($todayUser['username']); ?>
                            </div>
                          </td>
                          <td><?php echo htmlspecialchars($todayUser['email']); ?></td>
                          <td><small class="text-muted"><?php echo date('H:i', strtotime($todayUser['created_at'])); ?></small></td>
                        </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                    <?php else: ?>
                    <div class="text-center py-4">
                      <i class="fas fa-calendar-times text-muted" style="font-size: 3rem;"></i>
                      <p class="text-muted mt-2">No new users registered today</p>
                    </div>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
              
              <!-- Latest Quiz Results -->
              <div class="col-lg-6 mb-4">
                <div class="modern-card modern-table">
                  <div class="card-header-modern">
                    <h5 class="chart-title">
                      <i class="fas fa-quiz"></i>
                      Latest Quiz Results
                    </h5>
                    <p class="text-muted mb-0">Recent 10 quiz attempts</p>
                  </div>
                  <div class="card-body-modern p-0">
                    <?php if (count($latestQuizzes) > 0): ?>
                    <table class="table">
                      <thead>
                        <tr>
                          <th scope="col">User</th>
                          <th scope="col">Score</th>
                          <th scope="col">Result</th>
                          <th scope="col">Date</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php foreach ($latestQuizzes as $quiz): ?>
                        <tr>
                          <td>
                            <div class="d-flex align-items-center">
                              <img src="uploads/default_profile.png" class="rounded-circle me-2" width="28" height="28" alt="">
                              <?php echo htmlspecialchars($quiz['username']); ?>
                            </div>
                          </td>
                          <td><strong><?php echo $quiz['score']; ?>%</strong></td>
                          <td>
                            <?php if ($quiz['score'] >= 70): ?>
                              <span class="status-badge badge-correct">
                                <i class="fas fa-check"></i> Pass
                              </span>
                            <?php else: ?>
                              <span class="status-badge badge-incorrect">
                                <i class="fas fa-times"></i> Fail
                              </span>
                            <?php endif; ?>
                          </td>
                          <td><small class="text-muted"><?php echo date('M d', strtotime($quiz['attempt_date'])); ?></small></td>
                        </tr>
                        <?php endforeach; ?>
                      </tbody>
                    </table>
                    <?php else: ?>
                    <div class="text-center py-4">
                      <i class="fas fa-clipboard-list text-muted" style="font-size: 3rem;"></i>
                      <p class="text-muted mt-2">No quiz results available</p>
                    </div>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            </div>

            <!-- Performance Metrics -->
            <div class="row animate-fade-in">
              <div class="col-12">
                <div class="modern-card">
                  <div class="card-header-modern">
                    <h5 class="chart-title">
                      <i class="fas fa-analytics"></i>
                      Platform Performance Overview
                    </h5>
                  </div>
                  <div class="card-body-modern">
                    <div class="row text-center">
                      <div class="col-md-3">
                        <div class="mb-3">
                          <div class="stats-icon mx-auto" style="background: linear-gradient(135deg, #667eea, #764ba2);">
                            <i class="fas fa-percentage"></i>
                          </div>
                          <h4 class="text-primary"><?php echo $attempted > 0 ? round(($correctAnswers / $attempted) * 100, 1) : 0; ?>%</h4>
                          <p class="text-muted">Success Rate</p>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="mb-3">
                          <div class="stats-icon mx-auto" style="background: linear-gradient(135deg, #f093fb, #f5576c);">
                            <i class="fas fa-star"></i>
                          </div>
                          <h4 class="text-warning">
                            <?php for ($i = 0; $i < 5; $i++): ?>
                              <i class="fas fa-star <?php echo $i < $starRating ? 'text-warning' : 'text-muted'; ?>"></i>
                            <?php endfor; ?>
                          </h4>
                          <p class="text-muted">Star Rating</p>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="mb-3">
                          <div class="stats-icon mx-auto" style="background: linear-gradient(135deg, #4facfe, #00f2fe);">
                            <i class="fas fa-clock"></i>
                          </div>
                          <h4 class="text-info"><?php echo $attempted; ?></h4>
                          <p class="text-muted">Total Attempts</p>
                        </div>
                      </div>
                      <div class="col-md-3">
                        <div class="mb-3">
                          <div class="stats-icon mx-auto" style="background: linear-gradient(135deg, #43e97b, #38f9d7);">
                            <i class="fas fa-trophy"></i>
                          </div>
                          <h4 class="text-success"><?php echo $correctAnswers; ?></h4>
                          <p class="text-muted">Correct Answers</p>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
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
        // Enhanced Chart.js configuration with modern styling
        Chart.defaults.font.family = 'system-ui, -apple-system, sans-serif';
        Chart.defaults.font.size = 11;
        Chart.defaults.color = '#64748b';

        // Content Distribution Bar Chart
        const contentLabels = ['Categories', 'Sub Categories', 'Jobs', 'Posts', 'Quiz Questions'];
        const contentData = [
            <?php echo $totalCategoriesCount; ?>,
            <?php echo $totalSubCategoriesCount; ?>,
            <?php echo $totalJobsCount; ?>,
            <?php echo $totalPostsCount; ?>,
            <?php echo $totalQuizQuestionsCount; ?>
        ];

        const ctxBar = document.getElementById('contentBarChart').getContext('2d');
        const contentBarChart = new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: contentLabels,
                datasets: [{
                    label: 'Total Count',
                    data: contentData,
                    backgroundColor: [
                        'rgba(102, 126, 234, 0.8)',
                        'rgba(240, 147, 251, 0.8)',
                        'rgba(79, 172, 254, 0.8)',
                        'rgba(67, 233, 123, 0.8)',
                        'rgba(250, 112, 154, 0.8)'
                    ],
                    borderColor: [
                        'rgba(102, 126, 234, 1)',
                        'rgba(240, 147, 251, 1)',
                        'rgba(79, 172, 254, 1)',
                        'rgba(67, 233, 123, 1)',
                        'rgba(250, 112, 154, 1)'
                    ],
                    borderWidth: 2,
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: 'white',
                        bodyColor: 'white',
                        cornerRadius: 8,
                        displayColors: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        },
                        ticks: {
                            font: {
                                size: 10
                            }
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        },
                        ticks: {
                            maxRotation: 45,
                            font: {
                                size: 10
                            }
                        }
                    }
                }
            }
        });

        // Approval Status Pie Chart
        const approvalLabels = ['Categories', 'Sub Categories', 'Jobs', 'Posts', 'Quiz Questions'];
        const approvalData = [
            <?php echo $approvedCategoriesCount; ?>,
            <?php echo $approvedSubCategoriesCount; ?>,
            <?php echo $approvedJobsCount; ?>,
            <?php echo $approvedPostsCount; ?>,
            <?php echo $approvedQuizQuestionsCount; ?>
        ];
        const approvalColors = [
            'rgba(102, 126, 234, 0.8)',
            'rgba(240, 147, 251, 0.8)',
            'rgba(79, 172, 254, 0.8)',
            'rgba(67, 233, 123, 0.8)',
            'rgba(250, 112, 154, 0.8)'
        ];

        const ctxPie = document.getElementById('approvalPieChart').getContext('2d');
        const approvalPieChart = new Chart(ctxPie, {
            type: 'doughnut',
            data: {
                labels: approvalLabels,
                datasets: [{
                    data: approvalData,
                    backgroundColor: approvalColors,
                    borderWidth: 0,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 15,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: {
                                size: 10
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: 'white',
                        bodyColor: 'white',
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                const percentage = total > 0 ? Math.round((context.parsed / total) * 100) : 0;
                                return context.label + ': ' + context.parsed + ' (' + percentage + '%)';
                            }
                        }
                    }
                },
                cutout: '60%'
            }
        });

        // Add animation on scroll
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('animate-fade-in');
                }
            });
        }, observerOptions);

        document.querySelectorAll('.stats-card, .chart-container, .modern-card').forEach(el => {
            observer.observe(el);
        });

        // Refresh charts on window resize
        window.addEventListener('resize', function() {
            contentBarChart.resize();
            approvalPieChart.resize();
        });
    </script>

  </body>
</html>