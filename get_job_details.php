<?php
header('Content-Type: application/json');
include 'db_connection.php';

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo json_encode(['success' => false, 'message' => 'Job ID is required']);
    exit;
}

$jobId = (int)$_GET['id'];

try {
    // Fetch job details
    $sql = "SELECT *, DATE_FORMAT(created_at, '%M %d, %Y') as formatted_date FROM job_post WHERE id = ? AND is_approved = 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $jobId);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Job not found']);
        exit;
    }
    
    $job = $result->fetch_assoc();
    
    // Function to extract technologies from description
    function extractTechnologies($description, $technologies = '') {
        $allTech = $technologies . ' ' . $description;
        $techKeywords = [
            'React', 'Angular', 'Vue', 'JavaScript', 'TypeScript', 'Python', 'Java', 'PHP', 
            'Node.js', 'Express', 'MongoDB', 'MySQL', 'PostgreSQL', 'AWS', 'Azure', 'GCP',
            'Docker', 'Kubernetes', 'Git', 'Jenkins', 'CI/CD', 'REST API', 'GraphQL',
            'HTML', 'CSS', 'SASS', 'Bootstrap', 'Tailwind', 'jQuery', 'Redux', 'Next.js',
            'Laravel', 'Spring', 'Django', 'Flask', 'Ruby', 'Go', 'Rust', 'C++', 'C#',
            'Figma', 'Adobe XD', 'Sketch', 'Photoshop', 'Illustrator'
        ];
        $foundTech = array();
        
        foreach ($techKeywords as $tech) {
            if (stripos($allTech, $tech) !== false) {
                $foundTech[] = $tech;
            }
        }
        
        return array_unique($foundTech);
    }
    
    // Function to get time ago
    function timeAgo($datetime) {
        $time = time() - strtotime($datetime);
        if ($time < 60) return 'Just now';
        if ($time < 3600) return floor($time/60) . ' minutes ago';
        if ($time < 86400) return floor($time/3600) . ' hours ago';
        if ($time < 604800) return floor($time/86400) . ' days ago';
        return date('M j, Y', strtotime($datetime));
    }
    
    // Process job data
    $technologies = extractTechnologies($job['description'], isset($job['technologies']) ? $job['technologies'] : '');
    
    // Split requirements if they exist
    $requirements = array();
    if (isset($job['requirements']) && !empty($job['requirements'])) {
        $requirements = array_filter(explode("\n", $job['requirements']));
    }
    
    // Prepare response
    $jobData = [
        'id' => $job['id'],
        'title' => $job['title'],
        'company' => $job['company_name'] ?? 'Sanix Technologies',
        'location' => $job['location'],
        'role' => $job['role'],
        'description' => $job['description'],
        'email' => $job['email_to'],
        'posted' => timeAgo($job['created_at']),
        'formatted_date' => $job['formatted_date'],
        'salary_range' => $job['salary_range'] ?? '',
        'job_type' => $job['job_type'] ?? 'Full Time',
        'experience_required' => $job['experience_required'] ?? '',
        'technologies' => $technologies,
        'requirements' => $requirements,
        'benefits' => isset($job['benefits']) ? explode("\n", $job['benefits']) : [],
        'apply_url' => $job['apply_url'] ?? '',
        'is_featured' => $job['is_featured'] ?? 0,
        'application_deadline' => $job['application_deadline'] ?? null
    ];
    
    echo json_encode(['success' => true, 'job' => $jobData]);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
?>