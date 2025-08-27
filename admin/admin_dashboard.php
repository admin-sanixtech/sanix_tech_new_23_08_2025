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
    <title>Admin Dashboard - Sanix Technologies</title>
    <link  rel="stylesheet"  href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css"  />
    <script src="https://kit.fontawesome.com/ae360af17e.js"  crossorigin="anonymous" ></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="css/admin_styleone.css" />
    
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
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
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
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stats-card {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 1.5rem;
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
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .stats-card.users { --card-gradient-start: #667eea; --card-gradient-end: #764ba2; }
        .stats-card.quizzes { --card-gradient-start: #f093fb; --card-gradient-end: #f5576c; }
        .stats-card.performance { --card-gradient-start: #4facfe; --card-gradient-end: #00f2fe; }
        .stats-card.content { --card-gradient-start: #43e97b; --card-gradient-end: #38f9d7; }

        .stats-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--card-gradient-start), var(--card-gradient-end));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            color: white;
            font-size: 1.5rem;
        }

        .stats-number {
            font-size: 2.5rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .stats-label {
            color: #7f8c8d;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.9rem;
        }

        /* Chart Cards */
        .chart-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            padding: 2rem;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
        }

        .chart-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: #2c3e50;
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .chart-title i {
            color: #667eea;
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
            font-size: 0.9rem;
            padding: 1rem;
        }

        .table tbody tr {
            border: none;
        }

        .table tbody tr:nth-child(even) {
            background: rgba(102, 126, 234, 0.05);
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
            border-top: 1px solid rgba(0, 0, 0, 0.1);
        }

        /* Quick Actions */
        .quick-actions {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 2rem;
        }

        .action-btn {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 15px;
            text-decoration: none;
            text-align: center;
            transition: all 0.3s ease;
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.3);
            border: none;
            font-weight: 600;
        }

        .action-btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
            color: white;
        }

        .action-btn i {
            display: block;
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
        }

        /* Activity Feed */
        .activity-item {
            display: flex;
            align-items: center;
            padding: 1rem 0;
            border-bottom: 1px solid rgba(0, 0, 0, 0.1);
        }

        .activity-item:last-child {
            border-bottom: none;
        }

        .activity-icon {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            margin-right: 1rem;
        }

        .activity-content h6 {
            margin: 0;
            font-weight: 600;
            color: #2c3e50;
        }

        .activity-content p {
            margin: 0;
            color: #7f8c8d;
            font-size: 0.9rem;
        }

        .activity-time {
            color: #95a5a6;
            font-size: 0.8rem;
            margin-left: auto;
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .welcome-title {
                font-size: 2rem;
            }
            
            .stats-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
    </style>
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

            <!-- Stats Grid -->
            <div class="stats-grid animate-fade-in">
              <div class="stats-card users">
                <div class="stats-icon">
                  <i class="fas fa-users"></i>
                </div>
                <div class="stats-number">1,247</div>
                <div class="stats-label">Total Users</div>
              </div>
              <div class="stats-card quizzes">
                <div class="stats-icon">
                  <i class="fas fa-question-circle"></i>
                </div>
                <div class="stats-number"><?php echo $attempted; ?></div>
                <div class="stats-label">Quiz Attempts</div>
              </div>
              <div class="stats-card performance">
                <div class="stats-icon">
                  <i class="fas fa-chart-line"></i>
                </div>
                <div class="stats-number"><?php echo $correctAnswers; ?></div>
                <div class="stats-label">Correct Answers</div>
              </div>
              <div class="stats-card content">
                <div class="stats-icon">
                  <i class="fas fa-plus-circle"></i>
                </div>
                <div class="stats-number"><?php echo $addedToday; ?></div>
                <div class="stats-label">Added Today</div>
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
              <div class="col-lg-8">
                <div class="chart-container">
                  <h5 class="chart-title">
                    <i class="fas fa-chart-bar"></i>
                    Course Performance
                  </h5>
                  <canvas id="coursesBarChart" width="400" height="200"></canvas>
                </div>
              </div>
              <div class="col-lg-4">
                <div class="chart-container">
                  <h5 class="chart-title">
                    <i class="fas fa-chart-pie"></i>
                    Traffic Sources
                  </h5>
                  <canvas id="visitorsPieChart" width="400" height="300"></canvas>
                </div>
              </div>
            </div>

            <!-- Recent Activity & Table -->
            <div class="row animate-fade-in">
              <div class="col-lg-4">
                <div class="modern-card">
                  <div class="card-header-modern">
                    <h5 class="chart-title">
                      <i class="fas fa-clock"></i>
                      Recent Activity
                    </h5>
                  </div>
                  <div class="card-body-modern">
                    <div class="activity-item">
                      <div class="activity-icon">
                        <i class="fas fa-user-plus"></i>
                      </div>
                      <div class="activity-content">
                        <h6>New User Registered</h6>
                        <p>John Doe joined the platform</p>
                      </div>
                      <div class="activity-time">2m ago</div>
                    </div>
                    <div class="activity-item">
                      <div class="activity-icon">
                        <i class="fas fa-question"></i>
                      </div>
                      <div class="activity-content">
                        <h6>New Question Added</h6>
                        <p>Python basics quiz updated</p>
                      </div>
                      <div class="activity-time">15m ago</div>
                    </div>
                    <div class="activity-item">
                      <div class="activity-icon">
                        <i class="fas fa-check"></i>
                      </div>
                      <div class="activity-content">
                        <h6>Content Approved</h6>
                        <p>3 posts approved for publication</p>
                      </div>
                      <div class="activity-time">1h ago</div>
                    </div>
                    <div class="activity-item">
                      <div class="activity-icon">
                        <i class="fas fa-star"></i>
                      </div>
                      <div class="activity-content">
                        <h6>New Review</h6>
                        <p>5-star rating received</p>
                      </div>
                      <div class="activity-time">2h ago</div>
                    </div>
                  </div>
                </div>
              </div>
              
              <div class="col-lg-8">
                <!-- Table Element -->
                <div class="modern-card modern-table">
                  <div class="card-header-modern">
                    <h5 class="chart-title">
                      <i class="fas fa-table"></i>
                      Recent Users
                    </h5>
                    <p class="text-muted mb-0">Latest registered users on the platform</p>
                  </div>
                  <div class="card-body-modern p-0">
                    <table class="table">
                      <thead>
                        <tr>
                          <th scope="col">#</th>
                          <th scope="col">Name</th>
                          <th scope="col">Email</th>
                          <th scope="col">Status</th>
                          <th scope="col">Action</th>
                        </tr>
                      </thead>
                      <tbody>
                        <tr>
                          <th scope="row">1</th>
                          <td>
                            <div class="d-flex align-items-center">
                              <img src="uploads/default_profile.png" class="rounded-circle me-2" width="32" height="32" alt="">
                              Mark Johnson
                            </div>
                          </td>
                          <td>mark@example.com</td>
                          <td><span class="badge bg-success rounded-pill">Active</span></td>
                          <td>
                            <button class="btn btn-sm btn-outline-primary">View</button>
                          </td>
                        </tr>
                        <tr>
                          <th scope="row">2</th>
                          <td>
                            <div class="d-flex align-items-center">
                              <img src="uploads/default_profile.png" class="rounded-circle me-2" width="32" height="32" alt="">
                              Sarah Wilson
                            </div>
                          </td>
                          <td>sarah@example.com</td>
                          <td><span class="badge bg-warning rounded-pill">Pending</span></td>
                          <td>
                            <button class="btn btn-sm btn-outline-primary">View</button>
                          </td>
                        </tr>
                        <tr>
                          <th scope="row">3</th>
                          <td>
                            <div class="d-flex align-items-center">
                              <img src="uploads/default_profile.png" class="rounded-circle me-2" width="32" height="32" alt="">
                              David Brown
                            </div>
                          </td>
                          <td>david@example.com</td>
                          <td><span class="badge bg-success rounded-pill">Active</span></td>
                          <td>
                            <button class="btn btn-sm btn-outline-primary">View</button>
                          </td>
                        </tr>
                      </tbody>
                    </table>
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
        Chart.defaults.font.size = 12;
        Chart.defaults.color = '#64748b';

        // Sample data for the bar chart
        const courseLabels = ['Python', 'JavaScript', 'React', 'Node.js', 'SQL'];
        const courseData = [85, 72, 68, 91, 56];

        const ctxBar = document.getElementById('coursesBarChart').getContext('2d');
        const coursesBarChart = new Chart(ctxBar, {
            type: 'bar',
            data: {
                labels: courseLabels,
                datasets: [{
                    label: 'Completion Rate (%)',
                    data: courseData,
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
                    borderRadius: 8,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
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
                        max: 100,
                        ticks: {
                            callback: function(value) {
                                return value + '%';
                            }
                        },
                        grid: {
                            color: 'rgba(0, 0, 0, 0.1)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Sample data for the pie chart
        const visitorLabels = ['Direct', 'Search', 'Social', 'Referral'];
        const visitorData = [35, 30, 20, 15];
        const visitorColors = [
            'rgba(102, 126, 234, 0.8)',
            'rgba(240, 147, 251, 0.8)',
            'rgba(79, 172, 254, 0.8)',
            'rgba(67, 233, 123, 0.8)'
        ];

        const ctxPie = document.getElementById('visitorsPieChart').getContext('2d');
        const visitorsPieChart = new Chart(ctxPie, {
            type: 'doughnut',
            data: {
                labels: visitorLabels,
                datasets: [{
                    data: visitorData,
                    backgroundColor: visitorColors,
                    borderWidth: 0,
                    hoverOffset: 10
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle'
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: 'white',
                        bodyColor: 'white',
                        cornerRadius: 8,
                        callbacks: {
                            label: function(context) {
                                return context.label + ': ' + context.parsed + '%';
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
    </script>

  </body>
</html>