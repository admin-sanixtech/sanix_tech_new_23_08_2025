<?php
// Start the session only if it hasn't been started already
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: https://sanixtech.in");
    exit;
}

try {
    // Fetch categories ordered by `category_name`
    $sql_categories = "SELECT category_id, category_name, category_image FROM categories ORDER BY category_name ASC";
    $result_categories = $conn->query($sql_categories);
    if (!$result_categories) {
        throw new Exception("Error in category query: " . $conn->error);
    }
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}

// Fetch user-specific counts for notifications/badges
$user_id = $_SESSION['user_id'];
$user_posts_count = $conn->query("SELECT COUNT(*) as count FROM posts WHERE user_id = '$user_id'")->fetch_assoc()['count'];
$user_quiz_attempts = $conn->query("SELECT COUNT(*) as count FROM user_quiz_results WHERE user_id = '$user_id'")->fetch_assoc()['count'];

// Function to get appropriate icon for category
function getCategoryIcon($category_name) {
    $name_lower = strtolower($category_name);
    
    if (strpos($name_lower, 'python') !== false) return 'fa-brands fa-python';
    if (strpos($name_lower, 'sql') !== false) return 'fa-solid fa-database';
    if (strpos($name_lower, 'data') !== false) return 'fa-solid fa-chart-pie';
    if (strpos($name_lower, 'machine') !== false || strpos($name_lower, 'ml') !== false) return 'fa-solid fa-brain';
    if (strpos($name_lower, 'cyber') !== false || strpos($name_lower, 'security') !== false) return 'fa-solid fa-shield-alt';
    if (strpos($name_lower, 'digital') !== false || strpos($name_lower, 'marketing') !== false) return 'fa-solid fa-bullhorn';
    if (strpos($name_lower, 'power') !== false || strpos($name_lower, 'bi') !== false) return 'fa-solid fa-chart-bar';
    if (strpos($name_lower, 'azure') !== false) return 'fa-brands fa-microsoft';
    if (strpos($name_lower, 'ai') !== false || strpos($name_lower, 'artificial') !== false) return 'fa-solid fa-robot';
    
    return 'fa-solid fa-code'; // Default icon
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard - Sanix Technologies</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #667eea;
            --primary-dark: #5a67d8;
            --secondary-color: #764ba2;
            --accent-color: #f093fb;
            --success-color: #48bb78;
            --warning-color: #ed8936;
            --danger-color: #f56565;
            --info-color: #4299e1;
            --light-bg: #f8fafc;
            --dark-bg: #2d3748;
            --sidebar-width: 280px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
        }

        .sidebar {
            width: var(--sidebar-width);
            min-height: 100vh;
            background: linear-gradient(180deg, #2d3748 0%, #1a202c 100%);
            box-shadow: 4px 0 20px rgba(0, 0, 0, 0.1);
            position: fixed;
            left: 0;
            top: 0;
            z-index: 1000;
        }

        .sidebar-logo {
            padding: 2rem 1.5rem;
            border-bottom: 1px solid #4a5568;
        }

        .sidebar-logo a {
            color: #ffffff;
            text-decoration: none;
            font-size: 1.5rem;
            font-weight: 700;
            background: linear-gradient(45deg, var(--accent-color), var(--primary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .sidebar-nav {
            list-style: none;
            padding: 1rem 0;
        }

        .sidebar-header {
            color: #a0aec0;
            font-size: 0.875rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            padding: 1rem 1.5rem 0.5rem;
            margin-bottom: 0.5rem;
        }

        .sidebar-item {
            margin: 0.25rem 0;
        }

        .sidebar-link {
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: #cbd5e0;
            text-decoration: none;
            padding: 0.875rem 1.5rem;
            font-weight: 500;
            transition: all 0.3s ease;
            border-radius: 0;
            position: relative;
            overflow: hidden;
        }

        .sidebar-link::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 4px;
            background: linear-gradient(45deg, var(--accent-color), var(--primary-color));
            transform: scaleY(0);
            transition: transform 0.3s ease;
        }

        .sidebar-link:hover {
            color: #ffffff;
            background: rgba(255, 255, 255, 0.05);
            transform: translateX(8px);
        }

        .sidebar-link:hover::before {
            transform: scaleY(1);
        }

        .sidebar-link i {
            font-size: 1.1rem;
            width: 24px;
        }

        .badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 12px;
            font-weight: 600;
        }

        /* Modal Styles */
        .content-modal .modal-content {
            border: none;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            background: linear-gradient(135deg, #ffffff 0%, #f8fafc 100%);
        }

        .content-modal .modal-header {
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            color: white;
            border-radius: 20px 20px 0 0;
            padding: 1.5rem 2rem;
        }

        .content-modal .modal-title {
            font-size: 1.25rem;
            display: flex;
            align-items: center;
        }

        .content-modal .btn-close {
            filter: brightness(0) invert(1);
            opacity: 0.8;
        }

        .content-modal .btn-close:hover {
            opacity: 1;
        }

        .content-modal .modal-body {
            padding: 2rem;
        }

        .content-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1.5rem;
        }

        .content-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 2rem 1rem;
            background: linear-gradient(135deg, #ffffff 0%, #f7fafc 100%);
            border: 2px solid #e2e8f0;
            border-radius: 16px;
            text-decoration: none;
            color: #2d3748;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            min-height: 140px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .content-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s ease;
        }

        .content-box:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
            border-color: var(--primary-color);
            background: linear-gradient(135deg, #ffffff 0%, #edf2f7 100%);
        }

        .content-box:hover::before {
            left: 100%;
        }

        .content-box i {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            transition: all 0.3s ease;
        }

        .content-box:hover i {
            transform: scale(1.1);
        }

        .content-box h5 {
            margin: 0;
            font-weight: 600;
            font-size: 1rem;
            text-align: center;
            color: #2d3748;
        }

        .content-box .badge {
            position: absolute;
            top: 12px;
            right: 12px;
            z-index: 10;
        }

        .content-box.disabled {
            opacity: 0.6;
            cursor: not-allowed;
            background: #f7fafc;
        }

        .content-box.disabled:hover {
            transform: none;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .sidebar {
                width: 100%;
                position: relative;
                min-height: auto;
            }
            
            .content-grid {
                grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
                gap: 1rem;
            }
            
            .content-box {
                padding: 1.5rem 1rem;
                min-height: 120px;
            }
            
            .content-box i {
                font-size: 2rem;
            }
        }

        @media (max-width: 480px) {
            .content-grid {
                grid-template-columns: 1fr;
            }
        }

        /* Search Container Styles */
        .search-container .input-group {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .search-container .input-group-text {
            background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
            border: 2px solid #e2e8f0;
            border-right: none;
        }

        .search-container .form-control {
            border: 2px solid #e2e8f0;
            border-left: none;
            background: #ffffff;
            font-size: 0.95rem;
            padding: 0.75rem 1rem;
        }

        .search-container .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .search-container .form-control:focus + .input-group-text {
            border-color: var(--primary-color);
        }

        /* Animation for search results */
        .content-box.hidden {
            display: none;
        }

        .content-box.show {
            animation: fadeInUp 0.3s ease forwards;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .content-modal.fade .modal-dialog {
            transform: scale(0.8) translateY(-50px);
            transition: all 0.3s ease;
        }

        .content-modal.show .modal-dialog {
            transform: scale(1) translateY(0);
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="h-100">
                <div class="sidebar-logo"><a href="#">Sanix Technologies</a></div>
                <ul class="sidebar-nav">
                    <li class="sidebar-header">User Dashboard</li>
                    <li class="sidebar-item"><a href="/user/user_dashboard.php" class="sidebar-link"><i class="fa-solid fa-home pe-2"></i>Dashboard</a></li>
                    
                    <!-- Take Quiz Modal Trigger -->
                    <li class="sidebar-item">
                        <a href="#" class="sidebar-link" data-bs-toggle="modal" data-bs-target="#quizModal">
                            <i class="fa-solid fa-sliders pe-2"></i>Take Quiz
                            <span class="badge bg-info"><?php echo $user_quiz_attempts; ?></span>
                        </a>
                    </li>
                    
                    <!-- Learning Zone Modal Trigger -->
                    <li class="sidebar-item">
                        <a href="#" class="sidebar-link" data-bs-toggle="modal" data-bs-target="#learningModal">
                            <i class="fa-solid fa-book-open pe-2"></i>Learning Zone
                        </a>
                    </li>
                    
                    <!-- Interview Questions Modal Trigger -->
                    <li class="sidebar-item">
                        <a href="#" class="sidebar-link" data-bs-toggle="modal" data-bs-target="#interviewModal">
                            <i class="fa-regular fa-user pe-2"></i>Interview Questions
                        </a>
                    </li>
                    
                    <!-- My Content Modal Trigger -->
                    <li class="sidebar-item">
                        <a href="#" class="sidebar-link" data-bs-toggle="modal" data-bs-target="#myContentModal">
                            <i class="fa-solid fa-user-edit pe-2"></i>My Content
                            <span class="badge bg-success"><?php echo $user_posts_count; ?></span>
                        </a>
                    </li>

                    <!-- View Content Modal Trigger -->
                    <li class="sidebar-item">
                        <a href="#" class="sidebar-link" data-bs-toggle="modal" data-bs-target="#viewModal">
                            <i class="fa-solid fa-eye pe-2"></i>View Content
                        </a>
                    </li>

                    <!-- Career Hub Modal Trigger -->
                    <li class="sidebar-item">
                        <a href="#" class="sidebar-link" data-bs-toggle="modal" data-bs-target="#careerModal">
                            <i class="fa-solid fa-briefcase pe-2"></i>Career Hub
                        </a>
                    </li>
                    
                    <!-- Direct menu items -->
                    <li class="sidebar-item"><a href="/user/projects_view.php" class="sidebar-link"><i class="fa-solid fa-project-diagram pe-2"></i>Projects</a></li>
                    <li class="sidebar-item"><a href="/user/subscription_plans.php" class="sidebar-link"><i class="fa-solid fa-crown pe-2"></i>Subscription</a></li>
                    <li class="sidebar-item"><a href="/user/user_discussions.php" class="sidebar-link"><i class="fa-solid fa-comments pe-2"></i>Discussions</a></li>
                    <li class="sidebar-item"><a href="/user/user_progress_form.php" class="sidebar-link"><i class="fa-solid fa-chart-line pe-2"></i>My Progress</a></li>
                    <li class="sidebar-item"><a href="/user/withdrawal.php" class="sidebar-link"><i class="fa-solid fa-wallet pe-2"></i>Withdrawal</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Quiz Modal -->
    <div class="modal fade content-modal" id="quizModal" tabindex="-1" aria-labelledby="quizModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" id="quizModalLabel">
                        <i class="fa-solid fa-sliders me-2"></i>Take Quiz
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Search Box for Quiz -->
                    <div class="search-container mb-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fa-solid fa-search text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-start-0 ps-0" id="quizSearch" placeholder="Search quiz categories...">
                        </div>
                    </div>
                    <div class="content-grid" id="quizGrid">
                        <?php
                        // Reset the result set pointer for quiz categories
                        $result_categories->data_seek(0);

                        if ($result_categories->num_rows > 0) {
                            while ($row = $result_categories->fetch_assoc()) {
                                $category_id = $row['category_id'];
                                $category_name = $row['category_name'];
                                $icon_class = getCategoryIcon($category_name);
                                
                                // Create quiz URL based on category name
                                $quiz_url = '/user/' . strtolower(str_replace([' ', '-'], '_', $category_name)) . '_quiz.php';
                                
                                echo '<a href="' . $quiz_url . '" class="content-box">';
                                echo '<i class="' . $icon_class . '"></i>';
                                echo '<h5>' . htmlspecialchars($category_name) . ' Quiz</h5>';
                                echo '</a>';
                            }
                        } else {
                            echo '<div class="content-box disabled">';
                            echo '<i class="fa-solid fa-exclamation-triangle"></i>';
                            echo '<h5>No Quiz Categories Available</h5>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Learning Zone Modal -->
    <div class="modal fade content-modal" id="learningModal" tabindex="-1" aria-labelledby="learningModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" id="learningModalLabel">
                        <i class="fa-solid fa-book-open me-2"></i>Learning Zone
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <!-- Search Box for Interview Questions -->
                    <div class="search-container mb-4">
                        <div class="input-group">
                            <span class="input-group-text bg-light border-end-0">
                                <i class="fa-solid fa-search text-muted"></i>
                            </span>
                            <input type="text" class="form-control border-start-0 ps-0" id="interviewSearch" placeholder="Search interview question categories...">
                        </div>
                    </div>
                    <div class="content-grid" id="interviewGrid">
                        <?php
                        // Reset the result set pointer for learning categories
                        $result_categories->data_seek(0);

                        if ($result_categories->num_rows > 0) {
                            while ($row = $result_categories->fetch_assoc()) {
                                $category_id = $row['category_id'];
                                $category_name = $row['category_name'];
                                $icon_class = getCategoryIcon($category_name);
                                
                                // Create course URL based on category name
                                $course_url = '/user/course_' . strtolower(str_replace([' ', '-'], '_', $category_name)) . '.php';
                                
                                echo '<a href="' . $course_url . '" class="content-box">';
                                echo '<i class="' . $icon_class . '"></i>';
                                echo '<h5>' . htmlspecialchars($category_name) . ' Course</h5>';
                                echo '</a>';
                            }
                        } else {
                            echo '<div class="content-box disabled">';
                            echo '<i class="fa-solid fa-exclamation-triangle"></i>';
                            echo '<h5>No Learning Categories Available</h5>';
                            echo '</div>';
                        }
                        ?>
                        
                        <!-- Additional Learning Resources -->
                        <a href="/user/user_view_books.php" class="content-box">
                            <i class="fa-solid fa-book"></i>
                            <h5>Study Materials</h5>
                        </a>
                        <a href="/user/user_view_videos.php" class="content-box">
                            <i class="fa-solid fa-video"></i>
                            <h5>Video Tutorials</h5>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Interview Questions Modal -->
    <div class="modal fade content-modal" id="interviewModal" tabindex="-1" aria-labelledby="interviewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" id="interviewModalLabel">
                        <i class="fa-regular fa-user me-2"></i>Interview Questions
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="content-grid">
                        <?php
                        // Reset the result set pointer for interview categories
                        $result_categories->data_seek(0);

                        if ($result_categories->num_rows > 0) {
                            while ($row = $result_categories->fetch_assoc()) {
                                $category_id = $row['category_id'];
                                $category_name = $row['category_name'];
                                $icon_class = getCategoryIcon($category_name);
                                
                                echo '<a href="/user/user_view_int_question.php?category_id=' . $category_id . '" class="content-box">';
                                echo '<i class="' . $icon_class . '"></i>';
                                echo '<h5>' . htmlspecialchars($category_name) . '</h5>';
                                echo '</a>';
                            }
                        } else {
                            echo '<div class="content-box disabled">';
                            echo '<i class="fa-solid fa-exclamation-triangle"></i>';
                            echo '<h5>No Categories Available</h5>';
                            echo '</div>';
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- My Content Modal -->
    <div class="modal fade content-modal" id="myContentModal" tabindex="-1" aria-labelledby="myContentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" id="myContentModalLabel">
                        <i class="fa-solid fa-user-edit me-2"></i>My Content
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="content-grid">
                        <a href="/user/user_create_post.php" class="content-box">
                            <i class="fa-solid fa-edit"></i>
                            <h5>Create Post</h5>
                        </a>
                        <a href="/user/user_my_posts.php" class="content-box">
                            <span class="badge bg-info"><?php echo $user_posts_count; ?></span>
                            <i class="fa-solid fa-file-text"></i>
                            <h5>My Posts</h5>
                        </a>
                        <a href="/user/user_add_quiz_question.php" class="content-box">
                            <i class="fa-solid fa-question-circle"></i>
                            <h5>Submit Quiz Question</h5>
                        </a>
                        <a href="/user/user_share_experience.php" class="content-box">
                            <i class="fa-solid fa-user-tie"></i>
                            <h5>Share Interview Experience</h5>
                        </a>
                        <a href="/user/user_add_testimonial.php" class="content-box">
                            <i class="fa-solid fa-star"></i>
                            <h5>Add Testimonial</h5>
                        </a>
                        <a href="/user/user_recommend_book.php" class="content-box">
                            <i class="fa-solid fa-book"></i>
                            <h5>Recommend Book</h5>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- View Content Modal -->
    <div class="modal fade content-modal" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" id="viewModalLabel">
                        <i class="fa-solid fa-eye me-2"></i>View Content
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="content-grid">
                        <a href="/user/user_view_all_posts.php" class="content-box">
                            <i class="fa-solid fa-file-text"></i>
                            <h5>View All Posts</h5>
                        </a>
                        <a href="/user/user_view_books.php" class="content-box">
                            <i class="fa-solid fa-book"></i>
                            <h5>View Books</h5>
                        </a>
                        <a href="/user/user_view_handwritten.php" class="content-box">
                            <i class="fa-solid fa-pencil-alt"></i>
                            <h5>View Hand Written Notes</h5>
                        </a>
                        <a href="/user/user_view_cheatsheets.php" class="content-box">
                            <i class="fa-solid fa-file-code"></i>
                            <h5>View Cheat Sheets</h5>
                        </a>
                        <a href="/user/user_view_testimonials.php" class="content-box">
                            <i class="fa-solid fa-star"></i>
                            <h5>View Testimonials</h5>
                        </a>
                        <a href="/user/user_view_interview_experiences.php" class="content-box">
                            <i class="fa-solid fa-user-tie"></i>
                            <h5>View Interview Experiences</h5>
                        </a>
                        <a href="/user/user_quiz_results.php" class="content-box">
                            <i class="fa-solid fa-chart-bar"></i>
                            <h5>My Quiz Results</h5>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Career Hub Modal -->
    <div class="modal fade content-modal" id="careerModal" tabindex="-1" aria-labelledby="careerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" id="careerModalLabel">
                        <i class="fa-solid fa-briefcase me-2"></i>Career Hub
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="content-grid">
                        <a href="/user/user_post_job.php" class="content-box">
                            <i class="fa-solid fa-plus-circle"></i>
                            <h5>Post Job</h5>
                        </a>
                        <a href="/user/user_view_jobs.php" class="content-box">
                            <i class="fa-solid fa-search"></i>
                            <h5>Browse Jobs</h5>
                        </a>
                        <a href="/user/user_applied_jobs.php" class="content-box">
                            <i class="fa-solid fa-clipboard-list"></i>
                            <h5>Applied Jobs</h5>
                        </a>
                        <a href="/user/user_resume_builder.php" class="content-box">
                            <i class="fa-solid fa-file-pdf"></i>
                            <h5>Resume Builder</h5>
                        </a>
                        <a href="/user/user_career_guidance.php" class="content-box">
                            <i class="fa-solid fa-compass"></i>
                            <h5>Career Guidance</h5>
                        </a>
                        <a href="/user/user_salary_insights.php" class="content-box">
                            <i class="fa-solid fa-dollar-sign"></i>
                            <h5>Salary Insights</h5>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function () {
            $('.sidebar-link').on('click', function () {
                var target = $(this).data('bs-target');
                if ($(target).hasClass('collapse')) {
                    $(target).collapse('show');
                } else {
                    $(target).collapse('hide');
                }
            });

            // Quiz Search Functionality
            $('#quizSearch').on('keyup', function() {
                var searchValue = $(this).val().toLowerCase();
                $('#quizGrid .content-box').each(function() {
                    var categoryText = $(this).find('h5').text().toLowerCase();
                    if (categoryText.includes(searchValue)) {
                        $(this).removeClass('hidden').addClass('show');
                    } else {
                        $(this).removeClass('show').addClass('hidden');
                    }
                });

                // Show "No results found" message if no categories match
                var visibleItems = $('#quizGrid .content-box:not(.hidden)').length;
                $('#quizGrid .no-results').remove();
                if (visibleItems === 0 && searchValue !== '') {
                    $('#quizGrid').append('<div class="no-results text-center py-4 col-12"><i class="fa-solid fa-search text-muted mb-2" style="font-size: 2rem;"></i><p class="text-muted">No quiz categories found matching "' + searchValue + '"</p></div>');
                }
            });

            // Interview Questions Search Functionality
            $('#interviewSearch').on('keyup', function() {
                var searchValue = $(this).val().toLowerCase();
                $('#interviewGrid .content-box').each(function() {
                    var categoryText = $(this).find('h5').text().toLowerCase();
                    if (categoryText.includes(searchValue)) {
                        $(this).removeClass('hidden').addClass('show');
                    } else {
                        $(this).removeClass('show').addClass('hidden');
                    }
                });

                // Show "No results found" message if no categories match
                var visibleItems = $('#interviewGrid .content-box:not(.hidden)').length;
                $('#interviewGrid .no-results').remove();
                if (visibleItems === 0 && searchValue !== '') {
                    $('#interviewGrid').append('<div class="no-results text-center py-4 col-12"><i class="fa-solid fa-search text-muted mb-2" style="font-size: 2rem;"></i><p class="text-muted">No interview categories found matching "' + searchValue + '"</p></div>');
                }
            });

            // Clear search when modal is closed
            $('.content-modal').on('hidden.bs.modal', function() {
                $(this).find('input[type="text"]').val('');
                $(this).find('.content-box').removeClass('hidden show');
                $(this).find('.no-results').remove();
            });
        });
    </script>
</body>
</html>