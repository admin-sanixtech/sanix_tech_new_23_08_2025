<?php
header('Content-Type: application/json');
include 'db_connection.php';

try {
    // Function to get time ago
    function timeAgo($datetime) {
        $time = time() - strtotime($datetime);
        if ($time < 60) return 'Just now';
        if ($time < 3600) return floor($time/60) . ' minutes ago';
        if ($time < 86400) return floor($time/3600) . ' hours ago';
        if ($time < 604800) return floor($time/86400) . ' days ago';
        return date('M j, Y', strtotime($datetime));
    }
    
    // Get latest 10 jobs for sidebar
    $sql = "SELECT id, title, role, location, created_at, salary_range, company_name 
            FROM job_post 
            WHERE is_approved = 1 
            ORDER BY created_at DESC 
            LIMIT 10";
    
    $result = $conn->query($sql);
    $jobs = array();
    
    if ($result && $result->num_rows > 0) {
        while ($job = $result->fetch_assoc()) {
            $jobs[] = [
                'id' => $job['id'],
                'title' => $job['title'],
                'role' => $job['role'],
                'location' => $job['location'],
                'company' => $job['company_name'] ?? 'Sanix Technologies',
                'salary_range' => $job['salary_range'] ?? 'Salary not disclosed',
                'time_ago' => timeAgo($job['created_at']),
                'created_at' => $job['created_at']
            ];
        }
    }
    
    echo json_encode(['success' => true, 'jobs' => $jobs]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
?>