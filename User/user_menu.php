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

if (isset($_GET['category_id'])) {
    $category_id = $_GET['category_id'];
    // Query the database to fetch questions for the selected category
    $query = "SELECT * FROM quiz_questions WHERE category_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $category_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Display questions here
    while ($row = $result->fetch_assoc()) {
        // Display question details
        echo '<div class="question-box">';
        echo '<h4>' . htmlspecialchars($row['question']) . '</h4>';
        // Display options and the correct answer, if needed
        echo '</div>';
    }
} 
?>

<!-- Sidebar Content -->
<div class="h-100">
    <div class="sidebar-logo">
        <a href="#">Sanix Technologies</a>
    </div>
    <ul class="sidebar-nav">
        <li class="sidebar-item">
            <a href="/user/user_dashboard.php" class="sidebar-link">
                <i class="fa-solid fa-list pe-2"></i>Dashboard
            </a>
        </li>

        <!-- Take Quiz Section -->
        <li class="sidebar-item">
            <a href="#" class="sidebar-link collapsed" data-bs-target="#Quiz" data-bs-toggle="collapse" aria-expanded="false">
                <i class="fa-solid fa-sliders pe-2"></i>Take Quiz
            </a>
            <ul id="Quiz" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                <li class="sidebar-item"><a href="/user/python_quiz.php" class="sidebar-link">Python</a></li>
                <li class="sidebar-item"><a href="/user/digital_marketing_quiz.php" class="sidebar-link">Digital Marketing</a></li>
                <li class="sidebar-item"><a href="/user/sql_quiz.php" class="sidebar-link">SQL</a></li>
                <li class="sidebar-item"><a href="/user/Powerbi_quiz.php" class="sidebar-link">Power BI</a></li>
                <li class="sidebar-item"><a href="/user/azure_services_quiz.php" class="sidebar-link">Azure Services</a></li>
                <li class="sidebar-item"><a href="/user/cyber_security_quiz.php" class="sidebar-link">Cyber Security</a></li>
                <li class="sidebar-item"><a href="/user/AI_quiz.php" class="sidebar-link">AI</a></li>
                <li class="sidebar-item"><a href="/user/datascience_quiz.php" class="sidebar-link">Data Science</a></li>
                <li class="sidebar-item"><a href="/user/ml_quiz.php" class="sidebar-link">Machine Learning</a></li>
            </ul>      
        </li>

        <!-- Learning Zone Section -->
        <li class="sidebar-item">
            <a href="#" class="sidebar-link collapsed" data-bs-target="#Learning_Zone" data-bs-toggle="collapse" aria-expanded="false">
                <i class="fa-solid fa-book-open pe-2"></i>Learning Zone
            </a>
            <ul id="Learning_Zone" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                <li class="sidebar-item"><a href="/user/course_python.php" class="sidebar-link">Python</a></li>
                <li class="sidebar-item"><a href="/user/course_sql.php" class="sidebar-link">SQL</a></li>
                <li class="sidebar-item"><a href="/user/course_datascience.php" class="sidebar-link">Data Science</a></li>
                <li class="sidebar-item"><a href="/user/course_machine_learning.php" class="sidebar-link">Machine Learning</a></li>
            </ul>
        </li>

        <!-- Interview Questions Section -->
        <li class="sidebar-item">
            <a href="#" class="sidebar-link collapsed" data-bs-target="#Interview_Questions" data-bs-toggle="collapse" aria-expanded="false">
                <i class="fa-regular fa-user pe-2"></i>Interview Questions
            </a>
            <ul id="Interview_Questions" class="sidebar-dropdown list-unstyled collapse" data-bs-parent="#sidebar">
                <?php
                // Reset the result set pointer
                $result_categories->data_seek(0);

                // Loop through categories and create Interview Questions menu items dynamically
                if ($result_categories->num_rows > 0) {
                    while ($row = $result_categories->fetch_assoc()) {
                        $category_id = $row['category_id'];
                        $category_name = $row['category_name'];
                        // Generate a URL-friendly slug for the interview questions
                        $category_slug = strtolower(str_replace(' ', '_', $category_name));
                        // Pass category_id in the URL
                        echo '<li class="sidebar-item"><a href="/user/user_view_int_question.php?category_id=' . $category_id . '" class="sidebar-link">' . $category_name . '</a></li>';
                    }
                } else {
                    echo '<li class="sidebar-item"><a href="#" class="sidebar-link">No Interview Questions Available</a></li>';
                }
                ?>
            </ul>
        </li>

        <!-- Additional Sections -->
        <li class="sidebar-item"><a href="/user/projects_view.php" class="sidebar-link">Projects</a></li>
        <li class="sidebar-item"><a href="/user/subscription_plans.php" class="sidebar-link">Subscription</a></li>
        <li class="sidebar-item"><a href="/user/user_discussions.php" class="sidebar-link">Discussions</a></li>
        <li class="sidebar-item"><a href="/user/user_progress_form.php" class="sidebar-link">My Progress</a></li>
        <li class="sidebar-item"><a href="/user/withdrawal.php" class="sidebar-link">Withdrawal</a></li>
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
