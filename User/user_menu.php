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
    $sql_categories = "SELECT category_id, category_name FROM categories ORDER BY category_name ASC";
    $result_categories = $conn->query($sql_categories);
    if (!$result_categories) {
        throw new Exception("Error in category query: " . $conn->error);
    }
} catch (Exception $e) {
    die("Error: " . $e->getMessage());
}
?>

<!-- Sidebar Content -->
<div class="h-100">
    <div class="sidebar-logo">
        <a href="#">Sanix Technology</a>
    </div>
    <ul class="sidebar-nav">
        <li class="sidebar-item">
            <a href="/User/user_dashboard.php" class="sidebar-link">
                <i class="fa-solid fa-list pe-2"></i>Dashboard
            </a>
        </li>

        <!-- Take Quiz Section -->
        <li class="sidebar-item">
            <a href="#" class="sidebar-link collapsed" data-bs-target="#Quiz" data-bs-toggle="collapse" aria-expanded="false">
                <i class="fa-solid fa-sliders pe-2"></i>Take Quiz
            </a>
            <ul id="Quiz" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                <li class="sidebar-item"><a href="/User/python_quiz.php" class="sidebar-link">Python</a></li>
                <li class="sidebar-item"><a href="/User/digital_marketing_quiz.php" class="sidebar-link">Digital Marketing</a></li>
                <li class="sidebar-item"><a href="/User/sql_quiz.php" class="sidebar-link">SQL</a></li>
                <li class="sidebar-item"><a href="/User/Powerbi_quiz.php" class="sidebar-link">Power BI</a></li>
                <li class="sidebar-item"><a href="/User/azure_services_quiz.php" class="sidebar-link">Azure Services</a></li>
                <li class="sidebar-item"><a href="/User/cyber_security_quiz.php" class="sidebar-link">Cyber Security</a></li>
                <li class="sidebar-item"><a href="/User/AI_quiz.php" class="sidebar-link">AI</a></li>
                <li class="sidebar-item"><a href="/User/datascience_quiz.php" class="sidebar-link">Data Science</a></li>
                <li class="sidebar-item"><a href="/User/ml_quiz.php" class="sidebar-link">Machine Learning</a></li>
            </ul>
        </li>

        <!-- Learning Zone Section -->
        <li class="sidebar-item">
            <a href="#" class="sidebar-link collapsed" data-bs-target="#CourseContent" data-bs-toggle="collapse" aria-expanded="false">
                <i class="fa-solid fa-book pe-2"></i>Learning Zone
            </a>
            <ul id="CourseContent" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                <?php
                // Loop through categories and create menu items dynamically
                if ($result_categories->num_rows > 0) {
                    while ($row = $result_categories->fetch_assoc()) {
                        $category_id = $row['category_id'];
                        $category_name = $row['category_name'];
                        // Generate a URL-friendly slug for the course
                        $category_slug = strtolower(str_replace(' ', '_', $category_name));
                        echo '<li class="sidebar-item"><a href="/User/course_' . $category_slug . '.php" class="sidebar-link">' . $category_name . '</a></li>';
                    }
                } else {
                    echo '<li class="sidebar-item"><a href="#" class="sidebar-link">No Courses Available</a></li>';
                }
                ?>
            </ul>
        </li>


        <!-- Material Section -->
        <li class="sidebar-item">
            <a href="#" class="sidebar-link collapsed" data-bs-target="#Material" data-bs-toggle="collapse" aria-expanded="false">
                <i class="fa-regular fa-user pe-2"></i>Material
            </a>
            <ul id="Material" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                <li class="sidebar-item"><a href="/User/user_books.php" class="sidebar-link">Books</a></li>
                <li class="sidebar-item"><a href="/User/user_hand_written.php" class="sidebar-link">Hand Written Notes</a></li>
                <li class="sidebar-item"><a href="/User/user_cheet_sheets.php" class="sidebar-link">Cheet Sheets</a></li>
                <li class="sidebar-item"><a href="/User/user_vidoes.php" class="sidebar-link">Videos</a></li>
            </ul>
        </li>

        <!-- Contribute Section -->
        <li class="sidebar-item">
            <a href="#" class="sidebar-link collapsed" data-bs-target="#Contribute" data-bs-toggle="collapse" aria-expanded="false">
                <i class="fa-regular fa-user pe-2"></i>Contribute
            </a>
            <ul id="Contribute" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                <li class="sidebar-item"><a href="/User/user_add_question.php" class="sidebar-link">Add Questions</a></li>
                <li class="sidebar-item"><a href="/User/user_add_post.php" class="sidebar-link">Add Post</a></li>
                <li class="sidebar-item"><a href="/User/user_add_testimonial.php" class="sidebar-link">Add Testimonial</a></li>
                <li class="sidebar-item"><a href="/User/user_interview_exp.php" class="sidebar-link">Share Interview Experience</a></li>
                <li class="sidebar-item"><a href="/User/user_books.php" class="sidebar-link">Books</a></li>
                <li class="sidebar-item"><a href="/User/user_hand_written.php" class="sidebar-link">Hand Written</a></li>
            </ul>
        </li>

        <!-- Interview Questions Section -->
        <li class="sidebar-item">
            <a href="#" class="sidebar-link collapsed" data-bs-target="#Interview_Questions" data-bs-toggle="collapse" aria-expanded="false">
                <i class="fa-regular fa-user pe-2"></i>Interview Questions
            </a>
            <ul id="Interview_Questions" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                <li class="sidebar-item"><a href="/User/user_view_python_int_question.php" class="sidebar-link">Python</a></li>
                <li class="sidebar-item"><a href="/User/user_view_sql_int_question.php" class="sidebar-link">SQL</a></li>
                <li class="sidebar-item"><a href="/User/user_view_powerbi_int_question.php" class="sidebar-link">Power BI</a></li>
                <li class="sidebar-item"><a href="/User/user_view_azure_services_int_question.php" class="sidebar-link">Azure Services</a></li>
                <li class="sidebar-item"><a href="/User/user_view_aws_int_question.php" class="sidebar-link">AWS</a></li>
                <li class="sidebar-item"><a href="/User/user_view_gcp_int_question.php" class="sidebar-link">GCP</a></li>
            </ul>
        </li>

        <!-- Additional Sections -->
        <li class="sidebar-item"><a href="/User/projects_view.php" class="sidebar-link">Projects</a></li>
        <li class="sidebar-item"><a href="/User/subscription_plans.php" class="sidebar-link">Subscription</a></li>
        <li class="sidebar-item"><a href="/User/user_disscussions.php" class="sidebar-link">Discussions</a></li>
        <li class="sidebar-item"><a href="/User/withdrawal.php" class="sidebar-link">Withdrawal</a></li>
    </ul>
</div>

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
    });
</script>
