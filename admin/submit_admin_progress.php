<?php
include 'db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = intval($_POST['user_id']);
    $topic = $_POST['topic'];
    $duration = intval($_POST['duration_minutes']);
    $progress = intval($_POST['progress_percent']);
    $work_description = $_POST['work_description'];
    $date_of_work = $_POST['date_of_work'];
    $status = $_POST['status'];
    $remarks = $_POST['remarks'];

    // Calculate year, month, week, quarter, half-year
    $year = date('Y', strtotime($date_of_work));
    $month = date('n', strtotime($date_of_work));
    $week = date('W', strtotime($date_of_work));
    $quarter = ceil($month / 3);
    $half_year = ($month <= 6) ? 'H1' : 'H2';

    $stmt = $conn->prepare("
        INSERT INTO sanixazs_main_db.user_progress (
            user_id, topic, duration_minutes, progress_percent, 
            work_description, date_of_work, year, month, week, 
            quarter, half_year, status, remarks
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    
    $stmt->bind_param("isiissiisiiss", $user_id, $topic, $duration, $progress, $work_description,
                      $date_of_work, $year, $month, $week, $quarter, $half_year, $status, $remarks);

    if ($stmt->execute()) {
        echo "✅ Progress saved successfully.";
    } else {
        echo "❌ Error: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
} else {
    echo "Invalid request method.";
}
?>
