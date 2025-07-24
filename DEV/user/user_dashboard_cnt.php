<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


include 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: https://sanixtech.in");
    exit;
  }

// Check if session is not already started before starting it
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


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

// fetch total questions attempted by user
// Query to count the total attempted questions by this user
$query = "SELECT COUNT(*) AS attempted_count FROM user_answers WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$attemptedQuestions = $data['attempted_count'] ?? 0;


// Fetch total total_post_count
$total_post_Query = "SELECT COUNT(*) as total_post_count FROM posts";
$total_post_Result = mysqli_query($conn, $total_post_Query);
$total_post_count = mysqli_fetch_assoc($total_post_Result)['total_post_count'];


// Fetch questions attended by users
$attendedQuestionsQuery = "
    SELECT u.name as user_name, q.question_text 
    FROM quiz_results r
    JOIN users u ON r.user_id = u.user_id
    JOIN quiz_questions q ON r.question_id = q.question_id
";
$attendedQuestionsResult = mysqli_query($conn, $attendedQuestionsQuery);
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
                        <h4 class="mb-2"><?php echo $attemptedQuestions; ?></h4>
                        <p class="mb-2">Total Questions attempted</p>
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
                        <h4 class="mb-2"><?php echo $total_post_count; ?></h4>
                        <p class="mb-2">Total Post Available</p>
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
