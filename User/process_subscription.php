<?php
session_start();



ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


include 'db_connection.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: https://sanixtech.in");
    exit;
  }

  

// Get the user ID from the session
$user_id = $_SESSION['user_id'];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subscription_plan = $_POST['subscription_plan'];

    // Set the subscription details based on the selected plan
    switch ($subscription_plan) {
        case 'Silver':
            $amount_paid = 50;
            $questions = 50;
            $categories = 1;
            break;
        case 'Bronze':
            $amount_paid = 100;
            $questions = 100;
            $categories = 1;
            break;
        case 'Gold':
            $amount_paid = 200;
            $questions = 200;
            $categories = 2;
            break;
        case 'Diamond':
            $amount_paid = 500;
            $questions = 500;
            $categories = 5;
            break;
        case 'Custom':
            $amount_paid = 1000;
            $questions = 1000;
            $categories = "All Categories";
            break;
        default:
            echo "Invalid subscription plan.";
            exit;
    }

    // Calculate subscription start and end dates
    $subscription_start = date("Y-m-d H:i:s");
    $subscription_end = date("Y-m-d H:i:s", strtotime("+60 days")); // Example: 60-day subscription

    // Insert the subscription details into the database
    $sql = "INSERT INTO subscriptions (user_id, subscription_plan, amount_paid, payment_method, payment_status, payment_date, subscription_start, subscription_end)
            VALUES (?, ?, ?, 'Credit Card', 'Paid', NOW(), ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("isiss", $user_id, $subscription_plan, $amount_paid, $subscription_start, $subscription_end);

    if ($stmt->execute()) {
        echo "Subscription successful! Your plan: $subscription_plan";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Invalid request.";
}

$conn->close();
?>
