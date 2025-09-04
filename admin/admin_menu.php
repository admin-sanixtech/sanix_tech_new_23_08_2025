<?php
// admin_menu.php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session only if it's not already started
}

// Include your database connection
$database_path = __DIR__ . '/config/db_connection.php';

if (file_exists($database_path)) {
    include $database_path;
} else {
    die("Error: db_connection.php File not found from admin_menu.php at $database_path");
}
        

// Fetch counts for items needing approval
$pending_User_Post_Count = $conn->query("SELECT COUNT(*) as count FROM posts  WHERE status = 'pending'")->fetch_assoc()['count'];

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
$pending_User_quiz_question = $conn->query("SELECT COUNT(*) as count FROM quiz_questions WHERE status = 'pending'")->fetch_assoc()['count'];

$pending_job_approval_Count = $conn->query("SELECT COUNT(*) as count FROM job_post WHERE status = 'pending'")->fetch_assoc()['count'];
$pending_job_post_Count = $conn->query("SELECT COUNT(*) as count FROM job_post WHERE status = 'pending'")->fetch_assoc()['count'];

$pending_User_quiz_question_approval_Count = $conn->query("SELECT COUNT(*) as count FROM sanixazs_main_db.quiz_questions_pending WHERE status = 'pending'")->fetch_assoc()['count'];
$approved_subcategories_Count = $conn->query("SELECT COUNT(*) as count FROM subcategories WHERE status = 'approved'")->fetch_assoc()['count'];
$approved_categories_Count = $conn->query("SELECT COUNT(*) as count FROM categories WHERE status = 'approved'")->fetch_assoc()['count'];
$approved_quiz_questions = $conn->query("SELECT COUNT(*) as count FROM quiz_questions_pending WHERE status = 'approved'")->fetch_assoc()['count'];
$pending_quiz_questions = $conn->query("SELECT COUNT(*) as count FROM quiz_questions_pending WHERE status = 'pending'")->fetch_assoc()['count'];
$pending_subcategories = $conn->query("SELECT COUNT(*) as count FROM subcategories WHERE status = 'pending'")->fetch_assoc()['count'];

$total_approval_pending = $pending_subcategories + $pending_quiz_questions ;
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
    <link rel="stylesheet" href="../../css/admin_menu_styles.css" />
    <link rel="stylesheet" href="../../css/admin_styleone.css">
</head>
<body>
    <div class="d-flex">
        <!-- Sidebar -->
        <div class="sidebar">
            <div class="h-100">
                <div class="sidebar-logo"><a href="#">Sanix Technologies</a></div>
                <ul class="sidebar-nav">
                    <li class="sidebar-header">Admin Elements</li>
                    <li class="sidebar-item"><a href="/admin/core/user/users_details.php" class="sidebar-link">Users Details</a></li>
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
                            <span class="badge bg-danger"><?php echo $total_approval_pending ?></span>
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
                    <li class="sidebar-item"><a href="/admin/send_news_email.php" class="sidebar-link">Send Emails to Users</a></li>
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
                        <a href="/admin/core/approve/approve_user_quiz_questions.php" class="content-box">
                            <span class="badge bg-warning"><?php echo $pending_quiz_questions ?></span>
                            <i class="fa-solid fa-question-circle"></i>
                            <h5>Approve Quiz Questions</h5>
                        </a>
                        <a href="/admin/core/approve/approve_user_posts.php" class="content-box">
                            <span class="badge bg-warning"><?php echo $pending_User_Post_Count ?></span>
                            <i class="fa-solid fa-edit"></i>
                            <h5>Approve Posts</h5>
                        </a>
                        <a href="/admin/core/approve/approve_user_books.php" class="content-box">
                            <span class="badge bg-warning">7</span>
                            <i class="fa-solid fa-book"></i>
                            <h5>Approve Books</h5>
                        </a>
                        <a href="/admin/core/approve/approve_user_cheatsheets.php" class="content-box">
                            <span class="badge bg-warning">2</span>
                            <i class="fa-solid fa-file-text"></i>
                            <h5>Approve Cheatsheet</h5>
                        </a>
                        <a href="/admin/core/approve/approve_user_testimonials.php" class="content-box">
                            <span class="badge bg-warning">4</span>
                            <i class="fa-solid fa-star"></i>
                            <h5>Approve Testimonial</h5>
                        </a>
                        <a href="/admin/core/approve/approve_user_interview_exp.php" class="content-box">
                            <span class="badge bg-warning">4</span>
                            <i class="fa-solid fa-user-tie"></i>
                            <h5>Approve Interview Experience</h5>
                        </a>
                        <a href="/admin/core/approve/admin_approve_job_post.php" class="content-box">
                            <span class="badge bg-warning"><?php echo $pending_job_approval_Count ?></span>
                            <i class="fa-solid fa-briefcase"></i>
                            <h5>Approve Job Post</h5>
                        </a>
                        <a href="/admin/core/approve/approve_news_email.php" class="content-box">
                            <span class="badge bg-warning"><?php echo $pending_job_approval_Count ?></span>
                            <i class="fa-solid fa-briefcase"></i>
                            <h5>Approve News Emails</h5>
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
                        <a href="/admin/core/add/admin_add_quiz_questions.php" class="content-box">
                            <i class="fa-solid fa-question-circle"></i>
                            <h5>Add Quiz Questions</h5>
                        </a>
                        <a href="/admin/core/add/admin_add_post.php" class="content-box">
                            <i class="fa-solid fa-edit"></i>
                            <h5>Add Post</h5>
                        </a>
                        <a href="/admin/core/add/admin_add_books.php" class="content-box">
                            <i class="fa-solid fa-book"></i>
                            <h5>Add Books</h5>
                        </a>
                        <a href="/admin/core/add/admin_add_hand_written.php" class="content-box">
                            <i class="fa-solid fa-pencil-alt"></i>
                            <h5>Add Hand Written</h5>
                        </a>
                        <a href="/admin/core/add/admin_add_category.php" class="content-box">
                            <i class="fa-solid fa-folder"></i>
                            <h5>Add Category</h5>
                        </a>
                        <a href="/admin/core/add/admin_add_subcategory.php" class="content-box">
                            <i class="fa-solid fa-folder-open"></i>
                            <h5>Add SubCategory</h5>
                        </a>
                        <a href="/admin/core/add/admin_add_projects.php" class="content-box">
                            <i class="fa-solid fa-project-diagram"></i>
                            <h5>Add Projects</h5>
                        </a>
                        <a href="/admin/core/add/admin_add_interviewers.php" class="content-box">
                            <i class="fa-solid fa-user-tie"></i>
                            <h5>Add Interviewers</h5>
                        </a>
                        <a href="/admin/core/add/admin_add_job_post.php" class="content-box">
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