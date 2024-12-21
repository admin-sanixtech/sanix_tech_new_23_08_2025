<?php

// Check if session is not already started before starting it
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'db_connection.php';

// Retrieve Sanix Coins for the logged-in user
$user_id = $_SESSION['user_id']; // Assuming user_id is stored in session
$sanix_coins = 0; // Default value if no coins

$sql = "SELECT coins FROM user_coins WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $sanix_coins = $row['coins'];
}


// Fetch total questions
$totalQuestionsQuery = "SELECT COUNT(*) as total_questions FROM quiz_questions";
$totalQuestionsResult = mysqli_query($conn, $totalQuestionsQuery);
$totalQuestions = mysqli_fetch_assoc($totalQuestionsResult)['total_questions'];

$totalUsersQuery = "SELECT COUNT(*) as total_users FROM users";
$totalUsersResult = mysqli_query($conn, $totalUsersQuery);
$totalUsers= mysqli_fetch_assoc($totalUsersResult)['total_users'];

$totalbooksQuery = "SELECT COUNT(*) as total_books  FROM books ";
$totalbooksResult = mysqli_query($conn, $totalbooksQuery);
$totalbooks= mysqli_fetch_assoc($totalbooksResult)['total_books'];

$totalCategoriesQuery   = "SELECT COUNT(*) as total_categories FROM categories";
$totalCategoriesResult = mysqli_query($conn, $totalCategoriesQuery);
$totalCategories = mysqli_fetch_assoc($totalCategoriesResult)['total_categories'];

$totalSubcategoriesQuery = "SELECT COUNT(*) as total_subcategories FROM subcategories";
$totalSubcategoriesResult = mysqli_query($conn, $totalSubcategoriesQuery);
$totalSubcategories = mysqli_fetch_assoc($totalSubcategoriesResult)['total_subcategories'];

// Fetch questions attended by users
$attendedQuestionsQuery = "
    SELECT u.name as user_name, q.question_text 
    FROM quiz_results r
    JOIN users u ON r.user_id = u.user_id
    JOIN quiz_questions q ON r.question_id = q.question_id
";
$attendedQuestionsResult = mysqli_query($conn, $attendedQuestionsQuery);


// Fetch the top 10 contributors who are not admins
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

// Fetch the top 10 quiz performers
$sql_performers = "
    SELECT u.name, COUNT(r.question_id) AS attempted_count
    FROM quiz_results r
    JOIN users u ON r.user_id = u.user_id
    GROUP BY u.user_id
    ORDER BY attempted_count DESC
    LIMIT 10
";
$result_performers = $conn->query($sql_performers);


//$awstatsFilePath  = realpath('/home2/sanixazs/public_html/admin/awstats112024.sanixtech.in.txt');
$awstatsFilePath  = realpath('/home2/sanixazs/tmp/awstats/awstats122024.sanixtech.in.txt');
$visitorCount = 0;

// Check if the file exists and is readable
if (file_exists($awstatsFilePath) && is_readable($awstatsFilePath)) {
    // Read the file content
    $fileContent = file_get_contents($awstatsFilePath);

    // Look for "BEGIN_VISITOR" followed by a space and the visitor count
    if (preg_match('/BEGIN_VISITOR\s+(\d+)/', $fileContent, $matches)) {
        $visitorCount = $matches[1];
    } else {
        echo "Visitor count not found in AWstats file.";
    }
} else {
    echo "AWstats file not found or is not readable.";
}

?>

<div class="row">
    <div class="col-12 col-md-6 d-flex">
        <div class="card flex-fill border-0 illustration">
            <div class="card-body p-0 d-flex flex-fill">
                <div class="row g-0 w-100">
                    <div class="col-6">
                        <div class="p-3 m-1">
                            <h4>Welcome Back, <?php echo htmlspecialchars($_SESSION['email']); ?></h4>
                            <p class="mb-0"><?php echo htmlspecialchars($_SESSION['email']); ?> Dashboard, Sanix Technology</p>
                        </div>
                    </div>
                    <div class="col-6 align-self-end text-end">
                        <img src="images/customer-support.jpg" class="img-fluid illustration-img" alt="Customer Support Image" />
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Display Sanix Coins -->
    <div class="col-12 col-md-6 d-flex">
        <div class="card flex-fill border-0">
            <div class="card-body py-4">
                <div class="d-flex align-items-start">
                    <div class="flex-grow-1">
                        <h4 class="mb-2"><?php echo $sanix_coins; ?> Sanix Coins</h4>
                        <p class="mb-2">Your Sanix Coin Balance</p>
                        <div class="mb-0">
                            <span class="badge text-success me-2">+9.0%</span>
                            <span class="text-muted">Since Last Month</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Additional boxes -->
<div class="row">
    <!-- First Box -->
    <div class="col-12 col-md-4 d-flex">
        <div class="card flex-fill border-0 illustration">
            <div class="card-body p-0 d-flex flex-fill">
                <div class="row g-0 w-100">
                    <div class="col-6">
                        <div class="p-3 m-1">
                            <h4><?php echo $totalQuestions; ?></h4>
                            <p class="mb-0">Total Questions</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Box -->
    <div class="col-12 col-md-4 d-flex">
        <div class="card flex-fill border-0">
            <div class="card-body py-4">
                <div class="d-flex align-items-start">
                    <div class="flex-grow-1">
                        <h4 class="mb-2"><?php echo $visitorCount; ?></h4>
                        <p class="mb-2">Total visitors Count</p>
                        <div class="mb-0">
                            <span class="badge text-success me-2">+9.0%</span>
                            <span class="text-muted">Since Last Month</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Third Box -->
    <div class="col-12 col-md-4 d-flex">
        <div class="card flex-fill border-0">
            <div class="card-body py-4">
                <div class="d-flex align-items-start">
                    <div class="flex-grow-1">
                        <h4 class="mb-2"><?php echo $totalCategories; ?></h4>
                        <p class="mb-2">Total Categories</p>
                        <div class="mb-0">
                            <span class="badge text-info me-2">+5.5%</span>
                            <span class="text-muted">Growth This Quarter</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Additional boxes -->
<div class="row">
    <!-- First Box -->
    <div class="col-12 col-md-4 d-flex">
        <div class="card flex-fill border-0 illustration">
            <div class="card-body p-0 d-flex flex-fill">
                <div class="row g-0 w-100">
                    <div class="col-6">
                        <div class="p-3 m-1">
                            <h4><?php echo $totalUsers; ?></h4>
                            <p class="mb-0">Total Registered Users</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Box -->
    <div class="col-12 col-md-4 d-flex">
        <div class="card flex-fill border-0">
            <div class="card-body py-4">
                <div class="d-flex align-items-start">
                    <div class="flex-grow-1">
                        <h4 class="mb-2"><?php echo $totalSubcategories; ?></h4>
                        <p class="mb-2">Total Subcategories</p>
                        <div class="mb-0">
                            <span class="badge text-success me-2">+9.0%</span>
                            <span class="text-muted">additional information</span>
                        </div>
                    </div>
                </div> 
            </div>
        </div>
    </div>

    <!-- Third Box -->
    <div class="col-12 col-md-4 d-flex">
        <div class="card flex-fill border-0">
            <div class="card-body py-4">
                <div class="d-flex align-items-start">
                    <div class="flex-grow-1">
                        <h4 class="mb-2"><?php echo $totalbooks; ?></h4>
                        <p class="mb-2">Total Books</p>
                        <div class="mb-0">
                            <span class="badge text-info me-2">+5.5%</span>
                            <span class="text-muted">Growth This Quarter</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- Charts Section -->
<div class="row">
    <!-- Courses Bar Chart -->
    <div class="col-12 col-md-6 d-flex">
        <div class="card flex-fill border-0">
            <div class="card-body">
                <h4 class="mb-4">Courses Bar Chart</h4>
                <div class="chart-container">
                    <canvas id="coursesBarChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Visitors Pie Chart -->
    <div class="col-12 col-md-6 d-flex">
        <div class="card flex-fill border-0">
            <div class="card-body">
                <h4 class="mb-4">Total Visitors Pie Chart</h4>
                <div class="chart-container">
                    <canvas id="visitorsPieChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- total contributors and top performers -->
<div class="container-fluid">
    <div class="row">
        
        <!-- Top 10 Contributors -->
        <div class="col-12 col-md-6 d-flex">
            <div class="card flex-fill border-0 illustration">
                <div class="card-body p-0 d-flex flex-fill">
                    <div class="row g-0 w-100">
                        <div class="col-12">
                            <div class="p-3 m-1">
                                <h4>Top 10 Contributors</h4>
                                <table class="table table-striped mt-3">
                                    <thead>
                                        <tr>
                                            <th>S.No</th>
                                            <th>Username</th>
                                            <th>Questions Uploaded</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sno = 1;
                                        if ($result_contributors->num_rows > 0) {
                                            while ($row = $result_contributors->fetch_assoc()) {
                                                echo "<tr>";
                                                echo "<td>" . $sno++ . "</td>";
                                                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['question_count']) . "</td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='3' class='text-center'>No contributors found.</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Top 10 Quiz Performers -->
        <div class="col-12 col-md-6 d-flex">
            <div class="card flex-fill border-0">
                <div class="card-body p-0 d-flex flex-fill">
                    <div class="row g-0 w-100">
                        <div class="col-12">
                            <div class="p-3 m-1">
                                <h4>Top 10 Quiz Performers</h4>
                                <table class="table table-striped mt-3">
                                    <thead>
                                        <tr>
                                            <th>S.No</th>
                                            <th>Username</th>
                                            <th>Attempted Questions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $sno = 1;
                                        if ($result_performers->num_rows > 0) {
                                            while ($row = $result_performers->fetch_assoc()) {
                                                echo "<tr>";
                                                echo "<td>" . $sno++ . "</td>";
                                                echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                                                echo "<td>" . htmlspecialchars($row['attempted_count']) . "</td>";
                                                echo "</tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='3' class='text-center'>No quiz performers found.</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>