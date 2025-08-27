<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session only if it's not already started
}

// Include your database connection
include 'db_connection.php'; // Adjust the path as necessary

// Fetch counts for items needing approval
$pending_User_Questions_Count = $conn->query("SELECT COUNT(*) as count FROM quiz_questions WHERE status = 'pending'")->fetch_assoc()['count'];
$pendingUserPostCount = $conn->query("SELECT COUNT(*) as count FROM posts  WHERE status = 'pending'")->fetch_assoc()['count'];

// Count of unapproved books
$unapprovedBooks = $conn->query("SELECT COUNT(*) as count FROM user_books WHERE approved = 0");
$pendingUserBooksCount = $unapprovedBooks->fetch_assoc()['count'];

// Count of unapproved cheatsheets
$pendingUserCheatsheetCount = $conn->query("SELECT COUNT(*) as count FROM user_cheatsheets WHERE approved = 0")->fetch_assoc()['count'];

// Count of unapproved testimonials
$pendingUserTestimonialsCount = $conn->query("SELECT COUNT(*) as count FROM testimonials WHERE approved = 0")->fetch_assoc()['count'];

// Count of unapproved interview experiences
$pendingUserInterviewExpCount = $conn->query("SELECT COUNT(*) as count FROM user_interview_experience WHERE is_approved = 0")->fetch_assoc()['count'];

// Count of unapproved job post
$pending_job_approval_Count = $conn->query("SELECT COUNT(*) as count FROM job_post WHERE status = 'pending'")->fetch_assoc()['count'];
ob_end_flush();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Menu with Beautiful Modals</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* Sidebar Styling */
        .sidebar {
            background: #343a40;
            color: white;
            min-height: 100vh;
            width: 280px;
        }
        
        .sidebar-logo {
            padding: 1.5rem 1rem;
            font-size: 1.2rem;
            font-weight: bold;
            border-bottom: 1px solid #495057;
        }
        
        .sidebar-logo a {
            color: white;
            text-decoration: none;
        }
        
        .sidebar-nav {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .sidebar-header {
            padding: 1rem 1rem 0.5rem;
            font-size: 0.875rem;
            text-transform: uppercase;
            color: #adb5bd;
            font-weight: 600;
        }
        
        .sidebar-item {
            margin: 0;
        }
        
        .sidebar-link {
            display: block;
            padding: 0.75rem 1rem;
            color: #adb5bd;
            text-decoration: none;
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .sidebar-link:hover {
            background: #495057;
            color: white;
        }
        
        .badge {
            font-size: 0.75rem;
        }

        /* Modal Styling */
        .content-modal .modal-dialog {
            max-width: 900px;
        }
        
        .content-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            padding: 2rem 0;
        }
        
        .content-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            padding: 2rem 1.5rem;
            text-align: center;
            color: white;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
            display: block;
        }
        
        .content-box:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
            color: white;
            text-decoration: none;
        }
        
        .content-box::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .content-box:hover::before {
            opacity: 1;
        }
        
        .content-box i {
            font-size: 2.5rem;
            margin-bottom: 1rem;
            display: block;
        }
        
        .content-box h5 {
            margin: 0;
            font-weight: 600;
            font-size: 1.1rem;
        }
        
        .content-box .badge {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 0.8rem;
            padding: 0.4rem 0.6rem;
        }

        /* Different gradient colors for variety */
        .content-box:nth-child(1) { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .content-box:nth-child(2) { background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%); }
        .content-box:nth-child(3) { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }
        .content-box:nth-child(4) { background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%); }
        .content-box:nth-child(5) { background: linear-gradient(135deg, #fa709a 0%, #fee140 100%); }
        .content-box:nth-child(6) { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); }
        .content-box:nth-child(7) { background: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%); }
        .content-box:nth-child(8) { background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); }
        .content-box:nth-child(9) { background: linear-gradient(135deg, #a8edea 0%, #fed6e3 100%); }

        /* Main content area */
        .main-content {
            flex: 1;
            padding: 2rem;
            background: #f8f9fa;
        }

        /* Custom scrollbar for modals */
        .modal-body::-webkit-scrollbar {
            width: 6px;
        }

        .modal-body::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .modal-body::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        .modal-body::-webkit-scrollbar-thumb:hover {
            background: #555;
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
                    <li class="sidebar-header">Admin Elements</li>
                    <li class="sidebar-item"><a href="users_details.php" class="sidebar-link">Users Details</a></li>
                    <li class="sidebar-item"><a href="/admin/admin_dashboard.php" class="sidebar-link"><i class="fa-solid fa-list pe-2"></i>Dashboard</a></li>
                    <li class="sidebar-item"><a href="/admin/admin_learning_zone.php" class="sidebar-link"><i class="fa-solid fa-list pe-2"></i>Learning Zone</a></li>
                    
                    <!-- TakeQuiz Modal Trigger -->
                    <li class="sidebar-item">
                        <a href="#" class="sidebar-link" data-bs-toggle="modal" data-bs-target="#quizModal">
                            <i class="fa-solid fa-sliders pe-2"></i>TakeQuiz
                        </a>
                    </li>
                    
                    <!-- Approve Modal Trigger -->
                    <li class="sidebar-item">
                        <a href="#" class="sidebar-link" data-bs-toggle="modal" data-bs-target="#approveModal">
                            <i class="fa-regular fa-user pe-2"></i>Approve 
                            <span class="badge bg-danger">25</span>
                        </a>
                    </li>
                    
                    <!-- Material Modal Trigger -->
                    <li class="sidebar-item">
                        <a href="#" class="sidebar-link" data-bs-toggle="modal" data-bs-target="#materialModal">
                            <i class="fa-regular fa-user pe-2"></i> Material
                        </a>
                    </li>
                    
                    <!-- ADD Modal Trigger -->
                    <li class="sidebar-item">
                        <a href="#" class="sidebar-link" data-bs-toggle="modal" data-bs-target="#addModal">
                            <i class="fa-solid fa-plus pe-2"></i>ADD
                        </a>
                    </li>

                    <!-- view Modal Trigger -->
                    <li class="sidebar-item">
                        <a href="#" class="sidebar-link" data-bs-toggle="modal" data-bs-target="#viewModal">
                            <i class="fa-solid fa-plus pe-2"></i>view
                        </a>
                    </li>
                    
                    <!-- Other menu items -->
                    <li class="sidebar-item"><a href="/admin/projects_view.php" class="sidebar-link">Projects</a></li>
                    <li class="sidebar-item"><a href="subscription.php" class="sidebar-link">Subscription</a></li>
                    <li class="sidebar-item"><a href="#" class="sidebar-link">Interview Questions</a></li>
                    <li class="sidebar-item"><a href="view_interview_exp.php" class="sidebar-link">Others Interview Experience</a></li>
                    <li class="sidebar-item"><a href="withdrawal.php" class="sidebar-link">Withdrawal</a></li>
                    <li class="sidebar-item"><a href="/admin/admin_progress_form.php" class="sidebar-link">My Progress</a></li>
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
                    <div class="content-grid">
                        <a href="user_quiz.php" class="content-box">
                            <i class="fa-brands fa-python"></i>
                            <h5>Python Quiz</h5>
                        </a>
                        <a href="#" class="content-box">
                            <i class="fa-solid fa-database"></i>
                            <h5>SQL Quiz</h5>
                        </a>
                        <a href="#" class="content-box">
                            <i class="fa-solid fa-chart-bar"></i>
                            <h5>Power BI Quiz</h5>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Approve Modal -->
    <div class="modal fade content-modal" id="approveModal" tabindex="-1" aria-labelledby="approveModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" id="approveModalLabel">
                        <i class="fa-regular fa-user me-2"></i>Approve Content
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="content-grid">
                        <a href="/admin/approve_user_questions.php" class="content-box">
                            <span class="badge bg-warning"><?php echo $pending_User_Questions_Count ?></span>
                            <i class="fa-solid fa-question-circle"></i>
                            <h5>User Questions</h5>
                        </a>
                        <a href="/admin/approve_user_posts.php" class="content-box">
                            <span class="badge bg-warning">3</span>
                            <i class="fa-solid fa-edit"></i>
                            <h5>User Posts</h5>
                        </a>
                        <a href="/admin/approve_user_books.php" class="content-box">
                            <span class="badge bg-warning">7</span>
                            <i class="fa-solid fa-book"></i>
                            <h5>User Books</h5>
                        </a>
                        <a href="/admin/approve_user_cheatsheets.php" class="content-box">
                            <span class="badge bg-warning">2</span>
                            <i class="fa-solid fa-file-text"></i>
                            <h5>User Cheatsheet</h5>
                        </a>
                        <a href="/admin/approve_user_testimonials.php" class="content-box">
                            <span class="badge bg-warning">4</span>
                            <i class="fa-solid fa-star"></i>
                            <h5>User Testimonial</h5>
                        </a>
                        <a href="/admin/approve_user_interview_exp.php" class="content-box">
                            <span class="badge bg-warning">4</span>
                            <i class="fa-solid fa-user-tie"></i>
                            <h5>Interview Experience</h5>
                        </a>
                        <a href="/admin/admin_job_approvals.php" class="content-box">
                            <span class="badge bg-warning"><?php echo $pending_job_approval_Count ?></span>
                            <i class="fa-solid fa-briefcase"></i>
                            <h5>Job Approvals</h5>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Material Modal -->
    <div class="modal fade content-modal" id="materialModal" tabindex="-1" aria-labelledby="materialModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" id="materialModalLabel">
                        <i class="fa-regular fa-user me-2"></i>Material
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="content-grid">
                        <a href="#" class="content-box">
                            <i class="fa-solid fa-book"></i>
                            <h5>Books</h5>
                        </a>
                        <a href="#" class="content-box">
                            <i class="fa-solid fa-pencil-alt"></i>
                            <h5>Hand Written</h5>
                        </a>
                        <a href="#" class="content-box">
                            <i class="fa-solid fa-video"></i>
                            <h5>Videos</h5>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- ADD Modal -->
    <div class="modal fade content-modal" id="addModal" tabindex="-1" aria-labelledby="addModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" id="addModalLabel">
                        <i class="fa-solid fa-plus me-2"></i>Add New Content
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="content-grid">
                        <a href="/admin/admin_add_question.php" class="content-box">
                            <i class="fa-solid fa-question-circle"></i>
                            <h5>Add Questions</h5>
                        </a>
                        <a href="/admin/admin_create_post.php" class="content-box">
                            <i class="fa-solid fa-edit"></i>
                            <h5>Add Post</h5>
                        </a>
                        <a href="/admin/admin_add_books.php" class="content-box">
                            <i class="fa-solid fa-book"></i>
                            <h5>Add Books</h5>
                        </a>
                        <a href="/admin/admin_add_hand_written.php" class="content-box">
                            <i class="fa-solid fa-pencil-alt"></i>
                            <h5>Add Hand Written</h5>
                        </a>
                        <a href="/admin/add_category.php" class="content-box">
                            <i class="fa-solid fa-folder"></i>
                            <h5>Add Category</h5>
                        </a>
                        <a href="/admin/add_subcategory.php" class="content-box">
                            <i class="fa-solid fa-folder-open"></i>
                            <h5>Add SubCategory</h5>
                        </a>
                        <a href="/admin/admin_add_projects.php" class="content-box">
                            <i class="fa-solid fa-project-diagram"></i>
                            <h5>Add Projects</h5>
                        </a>
                        <a href="/admin/admin_add_interviewers.php" class="content-box">
                            <i class="fa-solid fa-user-tie"></i>
                            <h5>Add Interviewers</h5>
                        </a>
                        <a href="/admin/admin_post_job.php" class="content-box">
                            <i class="fa-solid fa-briefcase"></i>
                            <h5>Add Job Post</h5>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- view Modal -->
    <div class="modal fade content-modal" id="viewModal" tabindex="-1" aria-labelledby="viewModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header border-0">
                    <h5 class="modal-title fw-bold" id="viewModalLabel">
                        <i class="fa-solid fa-plus me-2"></i>view New Content
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="content-grid">
                        <a href="/admin/admin_view_question.php" class="content-box">
                            <i class="fa-solid fa-question-circle"></i>
                            <h5>view Questions</h5>
                        </a>
                        <a href="/admin/admin_create_post.php" class="content-box">
                            <i class="fa-solid fa-edit"></i>
                            <h5>view Post</h5>
                        </a>
                        <a href="/admin/admin_view_books.php" class="content-box">
                            <i class="fa-solid fa-book"></i>
                            <h5>view Books</h5>
                        </a>
                        <a href="/admin/admin_view_hand_written.php" class="content-box">
                            <i class="fa-solid fa-pencil-alt"></i>
                            <h5>view Hand Written</h5>
                        </a>
                        <a href="/admin/view_category.php" class="content-box">
                            <i class="fa-solid fa-folder"></i>
                            <h5>view Category</h5>
                        </a>
                        <a href="/admin/view_subcategory.php" class="content-box">
                            <i class="fa-solid fa-folder-open"></i>
                            <h5>view SubCategory</h5>
                        </a>
                        <a href="/admin/admin_view_projects.php" class="content-box">
                            <i class="fa-solid fa-project-diagram"></i>
                            <h5>view Projects</h5>
                        </a>
                        <a href="/admin/admin_view_interviewers.php" class="content-box">
                            <i class="fa-solid fa-user-tie"></i>
                            <h5>view Interviewers</h5>
                        </a>
                        <a href="/admin/admin_post_job.php" class="content-box">
                            <i class="fa-solid fa-briefcase"></i>
                            <h5>view Job Post</h5>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>