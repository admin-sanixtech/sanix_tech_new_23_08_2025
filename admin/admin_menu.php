<?php
ob_start();
if (session_status() === PHP_SESSION_NONE) {
    session_start(); // Start session only if it's not already started
}

// Include your database connection
include 'db_connection.php'; // Adjust the path as necessary

// Fetch counts for items needing approval
$pendingUserQuestionsCount = $conn->query("SELECT COUNT(*) as count FROM quiz_questions WHERE status = 'pending'")->fetch_assoc()['count'];
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
ob_end_flush();
?>

<!-- Content For Sidebar -->
<div class="h-100">
    <div class="sidebar-logo"><a href="#">Sanix Technologies</a></div>
    <ul class="sidebar-nav">
        <li class="sidebar-header">Admin Elements</li>
        <li class="sidebar-item"><a href="users_details.php" class="sidebar-link">Users Details</a></li>
        <li class="sidebar-item"><a href="/admin/admin_dashboard.php" class="sidebar-link"><i class="fa-solid fa-list pe-2"></i>Dashboard</a></li>
        <li class="sidebar-item">
            <a href="#" class="sidebar-link collapsed" data-bs-target="#Courses" data-bs-toggle="collapse" aria-expanded="false">
                <i class="fa-solid fa-file-lines pe-2"></i> Learning Zone
            </a>
            <ul id="Courses" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                <li class="sidebar-item"><a href="/admin/course_python.php" class="sidebar-link">Python</a></li>
                <li class="sidebar-item"><a href="/admin/course_sql.php" class="sidebar-link">SQL</a></li>
                <li class="sidebar-item"><a href="/admin/course_powerbi.php" class="sidebar-link">Power BI</a></li>
                <li class="sidebar-item"><a href="/admin/course_ml.php" class="sidebar-link">Mechine Learning</a></li>
                <li class="sidebar-item"><a href="/admin/course_digitalmarketing.php" class="sidebar-link">Digital Marketing</a></li>
            </ul>
        </li>
        <li class="sidebar-item">
            <a href="#" class="sidebar-link collapsed" data-bs-target="#Quiz" data-bs-toggle="collapse" aria-expanded="false">
                <i class="fa-solid fa-sliders pe-2"></i>TakeQuiz
            </a>
            <ul id="Quiz" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                <li class="sidebar-item"><a href="user_quiz.php" class="sidebar-link">Python</a></li>
                <li class="sidebar-item"><a href="#" class="sidebar-link">SQL</a></li>
                <li class="sidebar-item"><a href="#" class="sidebar-link">Power BI</a></li>
            </ul>
        </li>
        <li class="sidebar-item">
            <a href="#" class="sidebar-link collapsed" data-bs-target="#Approve" data-bs-toggle="collapse" aria-expanded="false">
                <i class="fa-regular fa-user pe-2"></i>Approve 
                <span class="badge bg-danger"><?= $pendingUserQuestionsCount + $pendingUserPostCount +$pendingUserBooksCount + $pendingUserCheatsheetCount + $pendingUserTestimonialsCount + $pendingUserInterviewExpCount ?></span>
            </a>
            <ul id="Approve" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                <li class="sidebar-item"><a href="/admin/approve_user_questions.php" class="sidebar-link">User Questions <span class="badge bg-warning"><?= $pendingUserQuestionsCount ?></span></a></li>
                <li class="sidebar-item"><a href="/admin/approve_user_posts.php" class="sidebar-link">User Posts <span class="badge bg-warning"><?= $pendingUserPostCount ?></span></a></li>
                <li class="sidebar-item"><a href="/admin/approve_user_books.php" class="sidebar-link">User Books <span class="badge bg-warning"><?= $pendingUserBooksCount ?></span></a></li>
                <li class="sidebar-item"><a href="/admin/approve_user_cheatsheets.php" class="sidebar-link">User Cheatsheet <span class="badge bg-warning"><?= $pendingUserCheatsheetCount ?></span></a></li>
                <li class="sidebar-item"><a href="/admin/approve_user_testimonials.php" class="sidebar-link">User Testimonial <span class="badge bg-warning"><?= $pendingUserTestimonialsCount ?></span></a></li>
                <li class="sidebar-item"><a href="/admin/approve_user_interview_exp.php" class="sidebar-link">User Interview Experience <span class="badge bg-warning"><?= $pendingUserInterviewExpCount ?></span></a></li>
            </ul>
        </li>
        <li class="sidebar-item">
            <a href="#" class="sidebar-link collapsed" data-bs-target="#Material" data-bs-toggle="collapse" aria-expanded="false">
                <i class="fa-regular fa-user pe-2"></i> Material
            </a>
            <ul id="Material" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                <li class="sidebar-item"><a href="#" class="sidebar-link">Books</a></li>
                <li class="sidebar-item"><a href="#" class="sidebar-link">Hand Written</a></li>
                <li class="sidebar-item"><a href="#" class="sidebar-link">Videos</a></li>
            </ul>
        </li>
        <li class="sidebar-item">
            <a href="#" class="sidebar-link collapsed" data-bs-target="#ADD" data-bs-toggle="collapse" aria-expanded="false">
                <i class="fa-regular fa-user pe-2"></i>ADD
            </a>
            <ul id="ADD" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                <li class="sidebar-item"><a href="/admin/admin_add_question.php" class="sidebar-link">Add Questions</a></li>
                <li class="sidebar-item"><a href="/admin/admin_create_post.php" class="sidebar-link">Add Post</a></li>
                <li class="sidebar-item"><a href="/admin/admin_add_books.php" class="sidebar-link">Add Books</a></li>
                <li class="sidebar-item"><a href="/admin/admin_add_hand_written.php" class="sidebar-link">Add Hand Written</a></li>
                <li class="sidebar-item"><a href="/admin/add_category.php" class="sidebar-link">Add Category</a></li>
                <li class="sidebar-item"><a href="/admin/add_subcategory.php" class="sidebar-link">Add SubCategory</a></li>
                <li class="sidebar-item"><a href="/admin/admin_add_projects.php" class="sidebar-link">Add Projects</a></li>
                <li class="sidebar-item"><a href="/admin/admin_add_interviewers.php" class="sidebar-link">Add interviewers</a></li>
                <li class="sidebar-item"><a href="/admin/admin_post_job.php" class="sidebar-link">Add job post</a></li>
            </ul>
        </li>
        <li class="sidebar-item"><a href="/admin/projects_view.php" class="sidebar-link">Projects</a></li>
        <li class="sidebar-item"><a href="subscription.php" class="sidebar-link">Subscription</a></li>
        <li class="sidebar-item"><a href="#" class="sidebar-link">Interview Questions</a></li>
        <li class="sidebar-item"><a href="view_interview_exp.php" class="sidebar-link">Others Interview Experience</a></li>
        <li class="sidebar-item"><a href="withdrawal.php" class="sidebar-link">Withdrawal</a></li>
        <li class="sidebar-item"><a href="/admin/admin_progress_form.php" class="sidebar-link">my progress</a></li>
        
        <li class="sidebar-header">Multi Level Menu</li>
        <li class="sidebar-item">
            <a href="#" class="sidebar-link collapsed" data-bs-target="#multi" data-bs-toggle="collapse" aria-expanded="false">
                <i class="fa-solid fa-share-nodes pe-2"></i> Multi Dropdown
            </a>
            <ul id="multi" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link collapsed" data-bs-target="#level-1" data-bs-toggle="collapse" aria-expanded="false">News</a>
                    <ul id="level-1" class="sidebar-dropdown list-unstyled collapse">
                        <li class="sidebar-item"><a href="/admin/send_news_email.php" class="sidebar-link">News to users</a></li>
                        <li class="sidebar-item"><a href="#" class="sidebar-link">Level 1.2</a></li>
                    </ul>
                </li>
            </ul>
            <ul id="multi" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link collapsed" data-bs-target="#level-1" data-bs-toggle="collapse" aria-expanded="false">Stock Market</a>
                    <ul id="level-1" class="sidebar-dropdown list-unstyled collapse">
                        <li class="sidebar-item"><a href="/admin/send_news_email.php" class="sidebar-link">stock market News to users</a></li>
                        <li class="sidebar-item"><a href="/admin/admin_stock_analysis.php" class="sidebar-link">My Stock Market Analysis</a></li>
                    </ul
                </li>
            </ul>
            <ul id="multi" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                <li class="sidebar-item">
                    <a href="#" class="sidebar-link collapsed" data-bs-target="#level-1" data-bs-toggle="collapse" aria-expanded="false">view</a>
                    <ul id="level-1" class="sidebar-dropdown list-unstyled collapse">
                        <li class="sidebar-item"><a href="/admin/admin_view_jobs.php" class="sidebar-link">view jobs posted</a></li>
                        <li class="sidebar-item"><a href="/admin/view_user_progress.php" class="sidebar-link">user progress</a></li>
                    </ul
                </li>
            </ul>
        </li>
    </ul>
</div>
