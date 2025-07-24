<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
include 'db_connection.php';

// Check if admin is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    die("Access denied. You must be an admin to view this page.");
}

$messages = [];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['submit_analysis'])) {
    // Sanitize and prepare inputs
    $user_id = $_SESSION['user_id'];
    $analyst_name = $conn->real_escape_string($_POST['analyst_name']);
    $company_name = $conn->real_escape_string($_POST['company_name']);
    $stock_symbol = $conn->real_escape_string($_POST['stock_symbol']);
    $fo_available = $conn->real_escape_string($_POST['fo_available']);
    $analysis_date = $conn->real_escape_string($_POST['analysis_date']);
    $analysis_text = $conn->real_escape_string($_POST['analysis_text']);
    $rating = $conn->real_escape_string($_POST['rating']);
    $target_price = $_POST['target_price'] !== '' ? floatval($_POST['target_price']) : null;
    $current_price = $_POST['current_price'] !== '' ? floatval($_POST['current_price']) : null;
    $time_frame = $conn->real_escape_string($_POST['time_frame']);
    $sector = $conn->real_escape_string($_POST['sector']);
    $confidence_level = $_POST['confidence_level'] !== '' ? intval($_POST['confidence_level']) : null;
    $is_published = isset($_POST['is_published']) ? 1 : 0;

    // Insert query
    $sql = "INSERT INTO sanixazs_main_db.stock_analysis (
                user_id, analyst_name, company_name, stock_symbol, fo_available,
                analysis_date, analysis_text, rating, target_price, current_price,
                time_frame, sector, confidence_level, is_published
            ) VALUES (
                ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?
            )";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        "isssssssdsssii",
        $user_id,           // i
        $analyst_name,      // s
        $company_name,      // s
        $stock_symbol,      // s
        $fo_available,      // s
        $analysis_date,     // s
        $analysis_text,     // s
        $rating,            // s
        $target_price,      // d
        $current_price,     // d
        $time_frame,        // s
        $sector,            // s
        $confidence_level,  // i
        $is_published       // i
    );

    if ($stmt->execute()) {
        $messages[] = "Stock analysis submitted successfully!";
    } else {
        $messages[] = "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Add Stock Market Analysis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Add Stock Market Analysis</h2>

    <?php foreach ($messages as $msg): ?>
        <div class="alert alert-info"><?= $msg ?></div>
    <?php endforeach; ?>

    <form method="POST">
        <div class="mb-3">
            <label class="form-label">Analyst Name</label>
            <input type="text" name="analyst_name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Company Name</label>
            <input type="text" name="company_name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Stock Symbol</label>
            <input type="text" name="stock_symbol" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">F&O Available</label>
            <select name="fo_available" class="form-select" required>
                <option value="Yes">Yes</option>
                <option value="No">No</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Analysis Date</label>
            <input type="date" name="analysis_date" class="form-control" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Analysis Text</label>
            <textarea name="analysis_text" class="form-control" rows="5" required></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label">Rating</label>
            <select name="rating" class="form-select">
                <option value="Bullish">Bullish</option>
                <option value="Bearish">Bearish</option>
                <option value="Neutral">Neutral</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Target Price</label>
            <input type="number" name="target_price" step="0.01" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Current Price</label>
            <input type="number" name="current_price" step="0.01" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Time Frame</label>
            <select name="time_frame" class="form-select">
                <option value="Short-term">Short-term</option>
                <option value="Mid-term">Mid-term</option>
                <option value="Long-term">Long-term</option>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label">Sector</label>
            <input type="text" name="sector" class="form-control">
        </div>

        <div class="mb-3">
            <label class="form-label">Confidence Level (%)</label>
            <input type="number" name="confidence_level" class="form-control" min="0" max="100">
        </div>

        <div class="form-check mb-3">
            <input type="checkbox" name="is_published" class="form-check-input" id="publish">
            <label class="form-check-label" for="publish">Publish</label>
        </div>

        <button type="submit" name="submit_analysis" class="btn btn-primary">Submit Analysis</button>
    </form>
</body>
</html>
