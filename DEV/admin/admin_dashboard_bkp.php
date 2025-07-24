<?php
error_reporting(E_ALL); // Enable error reporting
ini_set('display_errors', 1); // Display errors

session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

include 'db_connection.php'; 


// Your existing database connection and data fetching logic
// For demonstration, I'll use dummy data in place of your database queries.

$totalRevenue = 0; // Replace this with actual data from your database
$conversionRate = 0; // Replace this with actual data from your database
$avgOrderValue = 0; // Replace this with actual data from your database
$recentTransactions = [
    ['id' => '#TRX-1234', 'customer' => 'John Doe', 'email' => 'john@example.com', 'product' => 'Premium Plan', 'amount' => 299.99, 'status' => 'Completed', 'date' => '2023-05-28'],
    // Add more transactions as needed, or fetch from the database
];

// Fetch total users
$totalUsersQuery = "SELECT COUNT(*) as total_users FROM users";
$totalUsersResult = mysqli_query($conn, $totalUsersQuery);
$totalUsers = mysqli_fetch_assoc($totalUsersResult)['total_users'];

// Fetch active users count
$activeUsersQuery = "SELECT COUNT(*) as active_users FROM users WHERE last_login >= NOW() - INTERVAL 30 DAY";
$activeUsersResult = mysqli_query($conn, $activeUsersQuery);
$activeUsers = mysqli_fetch_assoc($activeUsersResult)['active_users'];

// Fetch today's added users
$todaysDate = date('Y-m-d');
$todaysUsersQuery = "SELECT COUNT(*) as todays_users FROM users WHERE DATE(created_at) = '$todaysDate'";
$todaysUsersResult = mysqli_query($conn, $todaysUsersQuery);
$todaysUsers = mysqli_fetch_assoc($todaysUsersResult)['todays_users'];

// Fetch total questions added
$totalQuestionsAddedQuery = "SELECT COUNT(*) as total_questions_added FROM quiz_questions";
$totalQuestionsAddedResult = mysqli_query($conn, $totalQuestionsAddedQuery);
$totalQuestionsAdded = mysqli_fetch_assoc($totalQuestionsAddedResult)['total_questions_added'];

// Fetch total questions
$totalQuestionsQuery = "SELECT COUNT(*) as total_questions FROM quiz_questions";
$totalQuestionsResult = mysqli_query($conn, $totalQuestionsQuery);
$totalQuestions = mysqli_fetch_assoc($totalQuestionsResult)['total_questions'];

// Fetch today's added questions
$todaysQuestionsQuery = "SELECT COUNT(*) as todays_questions FROM quiz_questions WHERE DATE(created_at) = '$todaysDate'";
$todaysQuestionsResult = mysqli_query($conn, $todaysQuestionsQuery);
$todaysQuestions = mysqli_fetch_assoc($todaysQuestionsResult)['todays_questions'];

// Fetch questions attended by users
$attendedQuestionsQuery = "
    SELECT u.name as user_name, q.question_text 
    FROM quiz_results r
    JOIN users u ON r.user_id = u.user_id
    JOIN quiz_questions q ON r.question_id = q.question_id
";
$attendedQuestionsResult = mysqli_query($conn, $attendedQuestionsQuery);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta content="width=device-width, initial-scale=1.0" name="viewport"/>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="css/admin_styles.css"> <!-- Link to your CSS file -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet"/>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet"/>
    <script src="https://d3js.org/d3.v7.min.js"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        .tooltip { position: absolute; background: rgba(0,0,0,0.7); color: white; padding: 5px; pointer-events: none; }
        .hover-lift { transition: transform 0.3s ease-in-out; }
        .hover-lift:hover { transform: translateY(-5px); }
    </style>
</head>

  
<body class="bg-gray-50">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <nav class="bg-gray-900 w-64 flex flex-col">
        <?php
        include 'header.php';
        include 'sidebar.php';
        ?>

        <!-- Main Content -->
        <main class="flex-1 overflow-x-hidden overflow-y-auto bg-gray-50">
            <div class="container mx-auto px-6 py-8">
                <h2 class="text-3xl font-semibold text-gray-800 mb-6">Dashboard Overview</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white shadow p-6 hover-lift">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-700">Total Revenue</h3>
                            <i class="fas fa-dollar-sign text-green-500 text-2xl"></i>
                        </div>
                        <p class="text-3xl font-bold text-gray-800">$<?php echo number_format($totalRevenue); ?></p>
                        <p class="text-green-500 text-sm mt-2"><i class="fas fa-arrow-up mr-1"></i>8.3% increase</p>
                    </div>
                    <div class="bg-white shadow p-6 hover-lift">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-700">Active Users</h3>
                            <i class="fas fa-users text-blue-500 text-2xl"></i>
                        </div>
                        <p class="text-3xl font-bold text-gray-800"><?php echo $totalUsers; ?></p>
                        <p class="text-green-500 text-sm mt-2"><i class="fas fa-arrow-up mr-1"></i>5.2% increase</p>
                    </div>
                    <div class="bg-white shadow p-6 hover-lift">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-700">Total Questions</h3>
                            <i class="fas fa-chart-pie text-purple-500 text-2xl"></i>
                        </div>
                        <p class="text-3xl font-bold text-gray-800"><?php echo $totalQuestions; ?></p>
                        <p class="text-red-500 text-sm mt-2"><i class="fas fa-arrow-down mr-1"></i>0.5% decrease</p>
                    </div>
                    <div class="bg-white shadow p-6 hover-lift">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-700">Avg. Order Value</h3>
                            <i class="fas fa-shopping-cart text-yellow-500 text-2xl"></i>
                        </div>
                        <p class="text-3xl font-bold text-gray-800">$<?php echo number_format($avgOrderValue, 2); ?></p>
                        <p class="text-green-500 text-sm mt-2"><i class="fas fa-arrow-up mr-1"></i>2.1% increase</p>
                    </div>
                    <div class="bg-white shadow p-6 hover-lift">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-700">Total Revenue</h3>
                            <i class="fas fa-dollar-sign text-green-500 text-2xl"></i>
                        </div>
                        <p class="text-3xl font-bold text-gray-800">$<?php echo number_format($totalRevenue); ?></p>
                        <p class="text-green-500 text-sm mt-2"><i class="fas fa-arrow-up mr-1"></i>8.3% increase</p>
                    </div>
                    <div class="bg-white shadow p-6 hover-lift">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-700">Active Users</h3>
                            <i class="fas fa-users text-blue-500 text-2xl"></i>
                        </div>
                        <p class="text-3xl font-bold text-gray-800"><?php echo $totalUsers; ?></p>
                        <p class="text-green-500 text-sm mt-2"><i class="fas fa-arrow-up mr-1"></i>5.2% increase</p>
                    </div>
                    <div class="bg-white shadow p-6 hover-lift">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-700">Conversion Rate</h3>
                            <i class="fas fa-chart-pie text-purple-500 text-2xl"></i>
                        </div>
                        <p class="text-3xl font-bold text-gray-800"><?php echo $conversionRate; ?>%</p>
                        <p class="text-red-500 text-sm mt-2"><i class="fas fa-arrow-down mr-1"></i>0.5% decrease</p>
                    </div>
                    <div class="bg-white shadow p-6 hover-lift">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-semibold text-gray-700">Avg. Order Value</h3>
                            <i class="fas fa-shopping-cart text-yellow-500 text-2xl"></i>
                        </div>
                        <p class="text-3xl font-bold text-gray-800">$<?php echo number_format($avgOrderValue, 2); ?></p>
                        <p class="text-green-500 text-sm mt-2"><i class="fas fa-arrow-up mr-1"></i>2.1% increase</p>
                    </div>
                </div>
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 mb-8">
                    <div class="bg-white shadow p-6">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">Revenue Trend</h3>
                        <div class="h-80" id="revenue-trend-chart"></div>
                    </div>
                    <div class="bg-white shadow p-6">
                        <h3 class="text-xl font-semibold text-gray-800 mb-4">User Acquisition</h3>
                        <div class="h-80" id="user-acquisition-chart"></div>
                    </div>
                </div>
                <div class="bg-white shadow overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h3 class="text-xl font-semibold text-gray-800">Recent Transactions</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead class="bg-gray-100">
                                <tr>
                                    <th class="px-4 py-2">Transaction ID</th>
                                    <th class="px-4 py-2">Customer</th>
                                    <th class="px-4 py-2">Email</th>
                                    <th class="px-4 py-2">Product</th>
                                    <th class="px-4 py-2">Amount</th>
                                    <th class="px-4 py-2">Status</th>
                                    <th class="px-4 py-2">Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentTransactions as $transaction): ?>
                                    <tr>
                                        <td class="border px-4 py-2"><?php echo $transaction['id']; ?></td>
                                        <td class="border px-4 py-2"><?php echo $transaction['customer']; ?></td>
                                        <td class="border px-4 py-2"><?php echo $transaction['email']; ?></td>
                                        <td class="border px-4 py-2"><?php echo $transaction['product']; ?></td>
                                        <td class="border px-4 py-2">$<?php echo number_format($transaction['amount'], 2); ?></td>
                                        <td class="border px-4 py-2"><?php echo $transaction['status']; ?></td>
                                        <td class="border px-4 py-2"><?php echo $transaction['date']; ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <?php
include 'header.php';
include 'sidebar.php';
?>

   
</body>
</html>




