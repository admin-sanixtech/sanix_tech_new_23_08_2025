<?php
// Check if session is not already started before starting it
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_connection.php';

// Retrieve Sanix Coins for the logged-in user
$user_id = $_SESSION['user_id'];
$sanix_coins = 0;

$sql = "SELECT coins FROM user_coins WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $sanix_coins = $row['coins'];
}

// Fetch all statistics
$totalQuestionsQuery = "SELECT COUNT(*) as total_questions FROM quiz_questions";
$totalQuestionsResult = mysqli_query($conn, $totalQuestionsQuery);
$totalQuestions = mysqli_fetch_assoc($totalQuestionsResult)['total_questions'];

$totalUsersQuery = "SELECT COUNT(*) as total_users FROM users";
$totalUsersResult = mysqli_query($conn, $totalUsersQuery);
$totalUsers = mysqli_fetch_assoc($totalUsersResult)['total_users'];

$totalbooksQuery = "SELECT COUNT(*) as total_books FROM books";
$totalbooksResult = mysqli_query($conn, $totalbooksQuery);
$totalbooks = mysqli_fetch_assoc($totalbooksResult)['total_books'];

$totalCategoriesQuery = "SELECT COUNT(*) as total_categories FROM categories";
$totalCategoriesResult = mysqli_query($conn, $totalCategoriesQuery);
$totalCategories = mysqli_fetch_assoc($totalCategoriesResult)['total_categories'];

$totalSubcategoriesQuery = "SELECT COUNT(*) as total_subcategories FROM subcategories";
$totalSubcategoriesResult = mysqli_query($conn, $totalSubcategoriesQuery);
$totalSubcategories = mysqli_fetch_assoc($totalSubcategoriesResult)['total_subcategories'];

// Visitor count (simplified for demo)
$visitorCount = 5678; // Replace with your actual visitor count logic

// Top contributors
$sql_contributors = "
    SELECT u.name, COUNT(q.question_id) AS question_count
    FROM quiz_questions q
    JOIN users u ON q.created_by = u.user_id
    WHERE u.role != 'admin'
    GROUP BY u.user_id
    ORDER BY question_count DESC
    LIMIT 10
";
$result_contributors = $conn->query($sql_contributors);

// Top performers
$sql_performers = "
    SELECT u.name, COUNT(r.question_id) AS attempted_count
    FROM quiz_results r
    JOIN users u ON r.user_id = u.user_id
    GROUP BY u.user_id
    ORDER BY attempted_count DESC
    LIMIT 10
";
$result_performers = $conn->query($sql_performers);
?>

<!-- Welcome Card -->
<div class="welcome-card">
    <div class="row align-items-center">
        <div class="col-md-8">
            <h2 style="font-size: 2rem; font-weight: 700; margin-bottom: 1rem;">
                <i class="fa-solid fa-hand-wave me-2"></i>
                Welcome Back, <?php echo htmlspecialchars($_SESSION['email']); ?>!
            </h2>
            <p style="font-size: 1.1rem; opacity: 0.9; margin-bottom: 1.5rem;">
                Sanix Technology Dashboard - Manage your platform with ease and efficiency
            </p>
            <div style="display: flex; flex-wrap: wrap; gap: 1rem;">
                <div style="background: rgba(255, 255, 255, 0.2); padding: 0.75rem 1.5rem; border-radius: 50px; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-coins" style="color: #ffd700;"></i>
                    <strong><?php echo number_format($sanix_coins); ?> Sanix Coins</strong>
                </div>
                <div style="background: rgba(16, 185, 129, 0.2); padding: 0.75rem 1.5rem; border-radius: 50px; display: flex; align-items: center; gap: 0.5rem;">
                    <i class="fa-solid fa-arrow-trend-up" style="color: #10b981;"></i>
                    <span>+9.0% Growth</span>
                </div>
            </div>
        </div>
        <div class="col-md-4 text-end">
            <div style="width: 120px; height: 120px; background: rgba(255, 255, 255, 0.2); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-left: auto; font-size: 3rem;">
                <i class="fa-solid fa-chart-line"></i>
            </div>
        </div>
    </div>
</div>

<!-- Statistics Grid -->
<div class="stats-container">
    <div class="stat-card">
        <div class="stat-icon">
            <i class="fa-solid fa-question-circle"></i>
        </div>
        <div class="stat-value"><?php echo number_format($totalQuestions); ?></div>
        <div class="stat-label">Total Questions</div>
        <div class="stat-change">
            <i class="fa-solid fa-arrow-up me-1"></i>
            12% increase this month
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon">
            <i class="fa-solid fa-users"></i>
        </div>
        <div class="stat-value"><?php echo number_format($visitorCount); ?></div>
        <div class="stat-label">Total Visitors</div>
        <div class="stat-change">
            <i class="fa-solid fa-arrow-up me-1"></i>
            9% increase this month
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon">
            <i class="fa-solid fa-folder"></i>
        </div>
        <div class="stat-value"><?php echo number_format($totalCategories); ?></div>
        <div class="stat-label">Total Categories</div>
        <div class="stat-change">
            <i class="fa-solid fa-arrow-up me-1"></i>
            5.5% growth this quarter
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon">
            <i class="fa-solid fa-user-check"></i>
        </div>
        <div class="stat-value"><?php echo number_format($totalUsers); ?></div>
        <div class="stat-label">Registered Users</div>
        <div class="stat-change">
            <i class="fa-solid fa-arrow-up me-1"></i>
            15% increase this month
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon">
            <i class="fa-solid fa-layer-group"></i>
        </div>
        <div class="stat-value"><?php echo number_format($totalSubcategories); ?></div>
        <div class="stat-label">Subcategories</div>
        <div class="stat-change">
            <i class="fa-solid fa-info-circle me-1"></i>
            Additional information
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon">
            <i class="fa-solid fa-book"></i>
        </div>
        <div class="stat-value"><?php echo number_format($totalbooks); ?></div>
        <div class="stat-label">Total Books</div>
        <div class="stat-change">
            <i class="fa-solid fa-arrow-up me-1"></i>
            Growth this quarter
        </div>
    </div>
</div>

<!-- Charts Section -->
<div class="charts-grid">
    <div class="chart-container">
        <h3 class="chart-title">
            <i class="fa-solid fa-chart-bar"></i>
            Courses Overview
        </h3>
        <div style="position: relative; height: 300px;">
            <canvas id="coursesBarChart"></canvas>
        </div>
    </div>

    <div class="chart-container">
        <h3 class="chart-title">
            <i class="fa-solid fa-chart-pie"></i>
            Visitor Sources
        </h3>
        <div style="position: relative; height: 300px;">
            <canvas id="visitorsPieChart"></canvas>
        </div>
    </div>
</div>

<!-- Leaderboards -->
<div class="leaderboard-container">
    <div class="leaderboard-card">
        <h3 class="leaderboard-title">
            <i class="fa-solid fa-trophy"></i>
            Top Contributors
        </h3>
        <table class="leaderboard-table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Username</th>
                    <th>Questions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $rank = 1;
                if ($result_contributors->num_rows > 0) {
                    while ($row = $result_contributors->fetch_assoc()) {
                        $rankClass = '';
                        if ($rank == 1) $rankClass = 'rank-1';
                        elseif ($rank == 2) $rankClass = 'rank-2';
                        elseif ($rank == 3) $rankClass = 'rank-3';
                        else $rankClass = 'rank-other';

                        echo "<tr>";
                        echo "<td><span class='rank-badge {$rankClass}'>{$rank}</span></td>";
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                        echo "<td><strong>" . number_format($row['question_count']) . "</strong></td>";
                        echo "</tr>";
                        $rank++;
                    }
                } else {
                    echo "<tr><td colspan='3' class='text-center'>No contributors found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <div class="leaderboard-card">
        <h3 class="leaderboard-title">
            <i class="fa-solid fa-star"></i>
            Top Performers
        </h3>
        <table class="leaderboard-table">
            <thead>
                <tr>
                    <th>Rank</th>
                    <th>Username</th>
                    <th>Attempts</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $rank = 1;
                if ($result_performers->num_rows > 0) {
                    while ($row = $result_performers->fetch_assoc()) {
                        $rankClass = '';
                        if ($rank == 1) $rankClass = 'rank-1';
                        elseif ($rank == 2) $rankClass = 'rank-2';
                        elseif ($rank == 3) $rankClass = 'rank-3';
                        else $rankClass = 'rank-other';

                        echo "<tr>";
                        echo "<td><span class='rank-badge {$rankClass}'>{$rank}</span></td>";
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                        echo "<td><strong>" . number_format($row['attempted_count']) . "</strong></td>";
                        echo "</tr>";
                        $rank++;
                    }
                } else {
                    echo "<tr><td colspan='3' class='text-center'>No performers found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>