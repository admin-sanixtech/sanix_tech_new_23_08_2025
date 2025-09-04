<?php
//job_posts.php
include 'db_connection.php';

// Get filter parameters
$city_filter = isset($_GET['city']) ? $_GET['city'] : '';
$role_filter = isset($_GET['role']) ? $_GET['role'] : '';
$job_type_filter = isset($_GET['job_type']) ? $_GET['job_type'] : '';
$company_size_filter = isset($_GET['company_size']) ? $_GET['company_size'] : '';
$experience_filter = isset($_GET['experience']) ? $_GET['experience'] : '';
$salary_filter = isset($_GET['salary']) ? $_GET['salary'] : '';
$work_mode_filter = isset($_GET['work_mode']) ? $_GET['work_mode'] : '';
$posted_filter = isset($_GET['posted']) ? $_GET['posted'] : '';
$search_query = isset($_GET['search']) ? $_GET['search'] : '';

// Pagination settings
$limit = 10; // jobs per page (changed from 3 to 10 as requested)
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Build SQL query with filters for counting
$countQuery = "SELECT COUNT(*) AS total FROM job_post WHERE is_approved = 1";
$countParams = array();
$countTypes = "";

// Apply filters to count query
if (!empty($search_query)) {
    $countQuery .= " AND (LOWER(title) LIKE LOWER(?) OR LOWER(description) LIKE LOWER(?) OR LOWER(role) LIKE LOWER(?))";
    $searchParam = "%" . $search_query . "%";
    $countParams[] = $searchParam;
    $countParams[] = $searchParam;
    $countParams[] = $searchParam;
    $countTypes .= "sss";
}

if (!empty($city_filter)) {
    $countQuery .= " AND LOWER(location) LIKE LOWER(?)";
    $countParams[] = "%" . $city_filter . "%";
    $countTypes .= "s";
}

if (!empty($role_filter)) {
    $countQuery .= " AND LOWER(role) LIKE LOWER(?)";
    $countParams[] = "%" . $role_filter . "%";
    $countTypes .= "s";
}

if (!empty($job_type_filter)) {
    $countQuery .= " AND LOWER(job_type) LIKE LOWER(?)";
    $countParams[] = "%" . $job_type_filter . "%";
    $countTypes .= "s";
}

if (!empty($posted_filter)) {
    switch($posted_filter) {
        case 'today':
            $countQuery .= " AND DATE(created_at) = CURDATE()";
            break;
        case 'week':
            $countQuery .= " AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
            break;
        case 'month':
            $countQuery .= " AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            break;
    }
}

// Execute count query
if (!empty($countParams)) {
    $countStmt = $conn->prepare($countQuery);
    $countStmt->bind_param($countTypes, ...$countParams);
    $countStmt->execute();
    $countResult = $countStmt->get_result();
} else {
    $countResult = $conn->query($countQuery);
}

$totalPosts = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalPosts / $limit);

// Build SQL query with filters for fetching data
$sql = "SELECT *, DATE_FORMAT(created_at, '%M %d, %Y') as formatted_date FROM job_post WHERE is_approved = 1";
$params = array();
$types = "";

// Apply same filters to main query
if (!empty($search_query)) {
    $sql .= " AND (LOWER(title) LIKE LOWER(?) OR LOWER(description) LIKE LOWER(?) OR LOWER(role) LIKE LOWER(?))";
    $searchParam = "%" . $search_query . "%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
    $types .= "sss";
}

if (!empty($city_filter)) {
    $sql .= " AND LOWER(location) LIKE LOWER(?)";
    $params[] = "%" . $city_filter . "%";
    $types .= "s";
}

if (!empty($role_filter)) {
    $sql .= " AND LOWER(role) LIKE LOWER(?)";
    $params[] = "%" . $role_filter . "%";
    $types .= "s";
}

if (!empty($job_type_filter)) {
    $sql .= " AND LOWER(job_type) LIKE LOWER(?)";
    $params[] = "%" . $job_type_filter . "%";
    $types .= "s";
}

if (!empty($posted_filter)) {
    switch($posted_filter) {
        case 'today':
            $sql .= " AND DATE(created_at) = CURDATE()";
            break;
        case 'week':
            $sql .= " AND created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
            break;
        case 'month':
            $sql .= " AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
            break;
    }
}

$sql .= " ORDER BY created_at DESC LIMIT ?, ?";
$params[] = $offset;
$params[] = $limit;
$types .= "ii";

// Execute main query
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Get latest jobs for sidebar (last 5 jobs)
$latestJobsQuery = "SELECT job_id, title, role, location, created_at, salary_range FROM job_post WHERE is_approved = 1 ORDER BY created_at DESC LIMIT 5";
$latestJobsResult = $conn->query($latestJobsQuery);

// Get trending technologies (most mentioned in job descriptions)
$trendingTechQuery = "SELECT 
    'React' as tech, COUNT(*) as job_count FROM job_post WHERE is_approved = 1 AND (LOWER(description) LIKE '%react%' OR LOWER(skills_required) LIKE '%react%')
    UNION ALL
    SELECT 'Python' as tech, COUNT(*) as job_count FROM job_post WHERE is_approved = 1 AND (LOWER(description) LIKE '%python%' OR LOWER(skills_required) LIKE '%python%')
    UNION ALL
    SELECT 'Node.js' as tech, COUNT(*) as job_count FROM job_post WHERE is_approved = 1 AND (LOWER(description) LIKE '%node%' OR LOWER(skills_required) LIKE '%node%')
    UNION ALL
    SELECT 'AWS' as tech, COUNT(*) as job_count FROM job_post WHERE is_approved = 1 AND (LOWER(description) LIKE '%aws%' OR LOWER(skills_required) LIKE '%aws%')
    ORDER BY job_count DESC";
$trendingTechResult = $conn->query($trendingTechQuery);

// Get top companies by job count
$topCompaniesQuery = "SELECT company_name, COUNT(*) as job_count FROM job_post WHERE is_approved = 1 GROUP BY company_name ORDER BY job_count DESC LIMIT 5";
$topCompaniesResult = $conn->query($topCompaniesQuery);

// Function to build pagination URLs with filters
function buildUrl($page, $filters = array()) {
    global $city_filter, $role_filter, $job_type_filter, $company_size_filter, $experience_filter, $salary_filter, $work_mode_filter, $posted_filter, $search_query;
    
    $params = array();
    if (!empty($search_query)) $params['search'] = $search_query;
    if (!empty($city_filter)) $params['city'] = $city_filter;
    if (!empty($role_filter)) $params['role'] = $role_filter;
    if (!empty($job_type_filter)) $params['job_type'] = $job_type_filter;
    if (!empty($company_size_filter)) $params['company_size'] = $company_size_filter;
    if (!empty($experience_filter)) $params['experience'] = $experience_filter;
    if (!empty($salary_filter)) $params['salary'] = $salary_filter;
    if (!empty($work_mode_filter)) $params['work_mode'] = $work_mode_filter;
    if (!empty($posted_filter)) $params['posted'] = $posted_filter;
    
    $params['page'] = $page;
    return '?' . http_build_query($params);
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

// Function to extract technologies from description (basic implementation)
function extractTechnologies($description, $technologies = '') {
    $allTech = $technologies . ' ' . $description;
    $techKeywords = ['React', 'Angular', 'Vue', 'JavaScript', 'TypeScript', 'Python', 'Java', 'PHP', 'Node.js', 'MongoDB', 'MySQL', 'PostgreSQL', 'AWS', 'Docker', 'Kubernetes'];
    $foundTech = array();
    
    foreach ($techKeywords as $tech) {
        if (stripos($allTech, $tech) !== false) {
            $foundTech[] = $tech;
        }
    }
    
    return array_unique($foundTech);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sanix Technologies - Job Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <style>
        /* Job Portal Enhanced Styles */
        :root {
            --primary-color: #6366f1;
            --secondary-color: #8b5cf6;
            --accent-color: #06b6d4;
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --gradient-bg: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-accent: linear-gradient(135deg, #84fab0 0%, #8fd3f4 100%);
            --card-shadow: 0 10px 25px rgba(0,0,0,0.1);
            --hover-shadow: 0 15px 35px rgba(0,0,0,0.15);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Base Styles */
        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
            line-height: 1.6;
            min-height: 100vh;
        }

        /* Header Banner */
        .header-banner {
            background: var(--gradient-bg);
            color: white;
            padding: 3rem 0;
            position: relative;
            overflow: hidden;
        }

        .header-banner::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 20"><defs><radialGradient id="a" cx="50%" cy="0%" r="100%"><stop offset="0%" stop-color="rgba(255,255,255,0.1)"/><stop offset="100%" stop-color="rgba(255,255,255,0)"/></radialGradient></defs><rect width="100" height="20" fill="url(%23a)"/></svg>');
            animation: shimmer 3s ease-in-out infinite;
        }

        @keyframes shimmer {
            0%, 100% { opacity: 0.3; }
            50% { opacity: 0.8; }
        }

        .header-banner .container {
            position: relative;
            z-index: 2;
        }

        /* Job Stats */
        .job-stats {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 1.5rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            display: block;
            text-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        /* Filter Section */
        .filter-section {
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(15px);
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            margin-top: -40px;
            position: relative;
            z-index: 10;
            border: 1px solid rgba(255,255,255,0.2);
        }

        /* Form Controls */
        .form-select, .form-control {
            border-radius: 12px;
            border: 2px solid #e5e7eb;
            transition: var(--transition);
            padding: 0.75rem 1rem;
        }

        .form-select:focus, .form-control:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
            outline: none;
        }

        .search-bar {
            border-radius: 25px;
            padding: 1rem 1.5rem;
            border: 2px solid #e5e7eb;
            font-size: 1.1rem;
            transition: var(--transition);
        }

        .search-bar:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
        }

        .search-btn {
            border-radius: 25px;
            padding: 1rem 2rem;
            background: var(--gradient-bg);
            border: none;
            color: white;
            font-weight: 600;
            transition: var(--transition);
        }

        .search-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.3);
        }

        /* Filter Chips */
        .filter-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
        }

        .filter-chip {
            display: inline-block;
            background: rgba(99, 102, 241, 0.1);
            color: var(--primary-color);
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            cursor: pointer;
            transition: var(--transition);
            border: 1px solid rgba(99, 102, 241, 0.2);
        }

        .filter-chip:hover {
            background: var(--primary-color);
            color: white;
            transform: scale(1.05);
        }

        .filter-chip.active {
            background: var(--primary-color);
            color: white;
        }

        /* Sidebar */
        .sidebar {
            background: white;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            position: sticky;
            top: 20px;
            overflow: hidden;
        }

        .sidebar-header {
            background: var(--gradient-bg);
            color: white;
            padding: 1.5rem;
        }

        .filter-group {
            padding: 1.5rem;
            border-bottom: 1px solid #f1f5f9;
        }

        .filter-group:last-child {
            border-bottom: none;
        }

        .form-check-input:checked {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .category-links {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .category-link {
            display: flex;
            align-items: center;
            padding: 0.75rem 1rem;
            color: #64748b;
            text-decoration: none;
            border-radius: 8px;
            transition: var(--transition);
        }

        .category-link:hover {
            background: rgba(99, 102, 241, 0.1);
            color: var(--primary-color);
            transform: translateX(5px);
        }

        /* Job Cards */
        .job-card {
            background: white;
            border-radius: 16px;
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            border: none;
            margin-bottom: 1.5rem;
            overflow: hidden;
            position: relative;
        }

        .job-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 4px;
            height: 100%;
            background: var(--gradient-bg);
            transform: scaleY(0);
            transform-origin: bottom;
            transition: var(--transition);
        }

        .job-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--hover-shadow);
        }

        .job-card:hover::before {
            transform: scaleY(1);
        }

        .job-card-header {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            padding: 1.5rem;
            border-bottom: 1px solid #e2e8f0;
        }

        .job-title {
            color: var(--primary-color);
            font-weight: 700;
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
            transition: var(--transition);
        }

        .job-card:hover .job-title {
            color: var(--secondary-color);
        }

        .company-badge {
            background: var(--gradient-bg);
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
        }

        /* Job Meta Information */
        .job-meta {
            display: flex;
            gap: 1rem;
            margin: 1rem 0;
            flex-wrap: wrap;
        }

        .meta-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: #64748b;
            font-size: 0.875rem;
            font-weight: 500;
        }

        .meta-item i {
            color: var(--accent-color);
            width: 16px;
            text-align: center;
        }

        /* Technology Tags */
        .tech-tag {
            background: rgba(99, 102, 241, 0.1);
            color: var(--primary-color);
            padding: 0.25rem 0.75rem;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: 500;
            margin: 0.25rem 0.25rem 0.25rem 0;
            display: inline-block;
            transition: var(--transition);
        }

        .tech-tag:hover {
            background: var(--primary-color);
            color: white;
            transform: scale(1.05);
        }

        /* Buttons */
        .btn-primary {
            background: var(--gradient-bg);
            border: none;
            border-radius: 12px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            transition: var(--transition);
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(99, 102, 241, 0.3);
        }

        .btn-outline-primary {
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            border-radius: 12px;
            font-weight: 600;
            transition: var(--transition);
        }

        .btn-outline-primary:hover {
            background: var(--primary-color);
            border-color: var(--primary-color);
            transform: translateY(-2px);
        }

        /* Pagination */
        .pagination {
            margin-top: 2rem;
        }

        .pagination .page-link {
            border-radius: 12px;
            margin: 0 0.25rem;
            border: none;
            color: var(--primary-color);
            font-weight: 500;
            transition: var(--transition);
            padding: 0.75rem 1rem;
        }

        .pagination .page-item.active .page-link {
            background: var(--gradient-bg);
            border: none;
            color: white;
        }

        .pagination .page-link:hover {
            background: rgba(99, 102, 241, 0.1);
            transform: translateY(-2px);
        }

        /* Right Sidebar */
        .sidebar-jobs {
            background: white;
            border-radius: 20px;
            box-shadow: var(--card-shadow);
            position: sticky;
            top: 20px;
            overflow: hidden;
            margin-bottom: 1.5rem;
        }

        .trending-job {
            padding: 1rem 1.5rem;
            border-bottom: 1px solid #f1f5f9;
            transition: var(--transition);
            cursor: pointer;
        }

        .trending-job:hover {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            transform: translateX(5px);
        }

        .trending-job:last-child {
            border-bottom: none;
        }

        .trending-job h6 {
            color: var(--primary-color);
            font-weight: 600;
        }

        /* Trending Technologies */
        .trending-tech {
            margin-bottom: 1rem;
        }

        .progress {
            border-radius: 10px;
            background: #f1f5f9;
        }

        .progress-bar {
            border-radius: 10px;
            transition: width 0.6s ease;
        }

        /* Learning Zone */
        .learning-zone {
            background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%);
            border-radius: 15px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            box-shadow: var(--card-shadow);
        }

        .learning-item {
            padding: 0.75rem 0;
            border-bottom: 1px solid rgba(255,255,255,0.3);
            transition: var(--transition);
        }

        .learning-item:last-child {
            border-bottom: none;
        }

        .learning-item:hover {
            transform: translateX(5px);
        }

        .learning-item a {
            font-weight: 500;
        }

        /* Company Items */
        .company-item {
            transition: var(--transition);
            padding: 0.5rem;
            border-radius: 8px;
        }

        .company-item:hover {
            background: rgba(99, 102, 241, 0.05);
            transform: translateX(5px);
        }

        .company-logo {
            transition: var(--transition);
        }

        .company-item:hover .company-logo {
            transform: scale(1.1);
        }

        /* Modal Styles */
        .modal-content {
            border-radius: 20px;
            border: none;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
            overflow: hidden;
        }

        .modal-header {
            background: var(--gradient-bg);
            color: white;
            border-bottom: none;
            padding: 1.5rem 2rem;
        }

        .modal-body {
            padding: 2rem;
        }

        .modal-footer {
            border-top: 1px solid #f1f5f9;
            padding: 1.5rem 2rem;
        }

        .btn-close-white {
            filter: brightness(0) invert(1);
        }

        /* Job Detail Modal Specific */
        .job-detail-header {
            border-bottom: 1px solid #f1f5f9;
            padding-bottom: 1rem;
        }

        .job-detail-header h4 {
            color: var(--primary-color);
            font-weight: 700;
        }

        .job-detail-header h6 {
            color: #64748b;
            font-weight: 500;
        }

        /* Alert Styles */
        .alert-info {
            background: linear-gradient(135deg, rgba(6, 182, 212, 0.1) 0%, rgba(99, 102, 241, 0.1) 100%);
            border: 1px solid rgba(6, 182, 212, 0.2);
            border-radius: 12px;
            color: #0891b2;
        }

        .no-jobs-found {
            text-align: center;
            padding: 3rem 1rem;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            border-radius: 16px;
            border: none;
        }

        .no-jobs-found i {
            color: #94a3b8;
        }

        /* Loading States */
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            border-radius: 16px;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f4f6;
            border-top: 4px solid var(--primary-color);
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Favorite Heart Animation */
        .fa-heart {
            transition: var(--transition);
        }

        .fa-heart.favorited {
            color: var(--danger-color) !important;
            animation: heartBeat 0.6s ease-in-out;
        }

        @keyframes heartBeat {
            0% { transform: scale(1); }
            50% { transform: scale(1.3); }
            100% { transform: scale(1); }
        }

        /* View Toggle Buttons */
        .btn-group .btn {
            border-radius: 8px;
            margin: 0 2px;
            transition: var(--transition);
        }

        .btn-group .btn.active {
            background: var(--primary-color);
            color: white;
            border-color: var(--primary-color);
        }

        /* Form Validation Styles */
        .form-control:invalid {
            border-color: var(--danger-color);
        }

        .form-control:valid {
            border-color: var(--success-color);
        }

        .invalid-feedback {
            display: block;
            color: var(--danger-color);
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        /* Badge Styles */
        .badge {
            border-radius: 12px;
            padding: 0.5rem 0.75rem;
            font-weight: 500;
        }

        .bg-primary {
            background: var(--gradient-bg) !important;
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .sidebar {
                margin-top: 2rem;
                position: static;
            }
            
            .job-meta {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .filter-section {
                margin-top: -20px;
                border-radius: 15px;
            }
            
            .header-banner {
                padding: 2rem 0;
            }
            
            .stat-number {
                font-size: 1.5rem;
            }
        }

        @media (max-width: 768px) {
            .job-card {
                margin-bottom: 1rem;
            }
            
            .job-card-header {
                padding: 1rem;
            }
            
            .card-body {
                padding: 1rem;
            }
            
            .filter-chips {
                justify-content: center;
            }
            
            .job-meta {
                justify-content: center;
                text-align: center;
            }
            
            .search-bar {
                font-size: 1rem;
                padding: 0.75rem 1rem;
            }
            
            .search-btn {
                padding: 0.75rem 1.5rem;
            }
        }

        @media (max-width: 576px) {
            .filter-section {
                padding: 1.5rem;
                margin-top: -15px;
            }
            
            .sidebar-header {
                padding: 1rem;
            }
            
            .filter-group {
                padding: 1rem;
            }
            
            .trending-job {
                padding: 0.75rem 1rem;
            }
            
            .learning-zone {
                padding: 1rem;
            }
            
            .job-stats {
                padding: 1rem;
                margin-top: 1rem;
            }
            
            .stat-number {
                font-size: 1.25rem;
            }
        }

        /* Hover Effects */
        .job-card .btn {
            transition: var(--transition);
        }

        .job-card:hover .btn-primary {
            background: var(--secondary-color);
        }

        .job-card:hover .btn-outline-primary {
            border-color: var(--secondary-color);
            color: var(--secondary-color);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--gradient-bg);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--secondary-color);
        }

        /* Animation Classes */
        .fade-in {
            animation: fadeIn 0.5s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .slide-in-left {
            animation: slideInLeft 0.5s ease-out;
        }

        @keyframes slideInLeft {
            from { opacity: 0; transform: translateX(-30px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .slide-in-right {
            animation: slideInRight 0.5s ease-out;
        }

        @keyframes slideInRight {
            from { opacity: 0; transform: translateX(30px); }
            to { opacity: 1; transform: translateX(0); }
        }

        /* Utility Classes */
        .text-gradient {
            background: var(--gradient-bg);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .glass-effect {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .shadow-hover {
            transition: var(--transition);
        }

        .shadow-hover:hover {
            box-shadow: var(--hover-shadow);
            transform: translateY(-3px);
        }

        /* Print Styles */
        @media print {
            .sidebar, .filter-section, .modal, .pagination {
                display: none !important;
            }
            
            .job-card {
                break-inside: avoid;
                box-shadow: none;
                border: 1px solid #ccc;
                margin-bottom: 1rem;
            }
            
            .col-lg-6 {
                width: 100% !important;
            }
        }
    </style>
</head>
<body>
    <?php include('header.php'); ?>
    <?php include('navbar.php'); ?>

    <!-- Header Banner -->
    <div class="header-banner">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="display-5 fw-bold mb-2">Find Your Dream Job</h1>
                    <p class="lead mb-0">Discover amazing opportunities at Sanix Technologies</p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="job-stats">
                        <div class="row">
                            <div class="col-6 stat-item">
                                <span class="stat-number"><?= number_format($totalPosts) ?></span>
                                <small>Active Jobs</small>
                            </div>
                            <div class="col-6 stat-item">
                                <span class="stat-number"><?= $topCompaniesResult->num_rows ?></span>
                                <small>Companies</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Search & Filter Section -->
    <div class="container-fluid px-4">
        <div class="filter-section p-4 mb-4">
            <!-- Search Bar -->
            <form method="GET" action="" id="filterForm">
                <div class="row mb-4">
                    <div class="col-md-8">
                        <div class="input-group">
                            <input type="text" class="form-control search-bar" placeholder="Search jobs, companies, keywords..." 
                                   id="searchInput" name="search" value="<?= htmlspecialchars($search_query) ?>">
                            <button class="btn search-btn" type="submit">
                                <i class="fas fa-search me-2"></i>Search
                            </button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="btn btn-outline-primary w-100" onclick="clearAllFilters()">
                            <i class="fas fa-refresh me-2"></i>Clear Filters
                        </button>
                    </div>
                </div>

                <!-- Quick Filters -->
                <div class="row mb-3">
                    <div class="col-12">
                        <h6 class="mb-3">Quick Filters:</h6>
                        <div class="filter-chips">
                            <span class="filter-chip <?= $work_mode_filter == 'remote' ? 'active' : '' ?>" onclick="applyQuickFilter('work_mode', 'remote')">Remote Jobs</span>
                            <span class="filter-chip <?= $job_type_filter == 'fulltime' ? 'active' : '' ?>" onclick="applyQuickFilter('job_type', 'fulltime')">Full Time</span>
                            <span class="filter-chip <?= $job_type_filter == 'parttime' ? 'active' : '' ?>" onclick="applyQuickFilter('job_type', 'parttime')">Part Time</span>
                            <span class="filter-chip <?= $experience_filter == 'fresher' ? 'active' : '' ?>" onclick="applyQuickFilter('experience', 'fresher')">Fresher</span>
                            <span class="filter-chip <?= $experience_filter == 'senior' ? 'active' : '' ?>" onclick="applyQuickFilter('experience', 'senior')">Senior Level</span>
                        </div>
                    </div>
                </div>

                <!-- Main Filters -->
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">City</label>
                        <select class="form-select" id="cityFilter" name="city" onchange="submitForm()">
                            <option value="">All Cities</option>
                            <option value="mumbai" <?= $city_filter == 'mumbai' ? 'selected' : '' ?>>Mumbai</option>
                            <option value="delhi" <?= $city_filter == 'delhi' ? 'selected' : '' ?>>Delhi</option>
                            <option value="bangalore" <?= $city_filter == 'bangalore' ? 'selected' : '' ?>>Bangalore</option>
                            <option value="chennai" <?= $city_filter == 'chennai' ? 'selected' : '' ?>>Chennai</option>
                            <option value="kolkata" <?= $city_filter == 'kolkata' ? 'selected' : '' ?>>Kolkata</option>
                            <option value="hyderabad" <?= $city_filter == 'hyderabad' ? 'selected' : '' ?>>Hyderabad</option>
                            <option value="pune" <?= $city_filter == 'pune' ? 'selected' : '' ?>>Pune</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Role Category</label>
                        <select class="form-select" id="roleFilter" name="role" onchange="submitForm()">
                            <option value="">All Roles</option>
                            <option value="developer" <?= $role_filter == 'developer' ? 'selected' : '' ?>>Developer</option>
                            <option value="designer" <?= $role_filter == 'designer' ? 'selected' : '' ?>>Designer</option>
                            <option value="manager" <?= $role_filter == 'manager' ? 'selected' : '' ?>>Manager</option>
                            <option value="analyst" <?= $role_filter == 'analyst' ? 'selected' : '' ?>>Analyst</option>
                            <option value="tester" <?= $role_filter == 'tester' ? 'selected' : '' ?>>Tester</option>
                            <option value="data engineer" <?= $role_filter == 'data engineer' ? 'selected' : '' ?>>Data Engineer</option>
                            <option value="data scientist" <?= $role_filter == 'data scientist' ? 'selected' : '' ?>>Data Scientist</option>
                            <option value="data analyst" <?= $role_filter == 'data analyst' ? 'selected' : '' ?>>Data Analyst</option>
                            <option value="python developer" <?= $role_filter == 'python developer' ? 'selected' : '' ?>>Python Developer</option>
                            <option value="sql developer" <?= $role_filter == 'sql developer' ? 'selected' : '' ?>>SQL Developer</option>
                            <option value="php developer" <?= $role_filter == 'php developer' ? 'selected' : '' ?>>PHP Developer</option>
                            <option value="react js developer" <?= $role_filter == 'react js developer' ? 'selected' : '' ?>>React JS Developer</option>
                            <option value="java developer" <?= $role_filter == 'java developer' ? 'selected' : '' ?>>Java Developer</option>
                            <option value="web developer" <?= $role_filter == 'web developer' ? 'selected' : '' ?>>Web Developer</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Job Type</label>
                        <select class="form-select" id="jobTypeFilter" name="job_type" onchange="submitForm()">
                            <option value="">All Types</option>
                            <option value="fulltime" <?= $job_type_filter == 'fulltime' ? 'selected' : '' ?>>Full Time</option>
                            <option value="parttime" <?= $job_type_filter == 'parttime' ? 'selected' : '' ?>>Part Time</option>
                            <option value="contract" <?= $job_type_filter == 'contract' ? 'selected' : '' ?>>Contract</option>
                            <option value="internship" <?= $job_type_filter == 'internship' ? 'selected' : '' ?>>Internship</option>
                            <option value="remote" <?= $job_type_filter == 'remote' ? 'selected' : '' ?>>Remote</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">Posted</label>
                        <select class="form-select" id="postedFilter" name="posted" onchange="submitForm()">
                            <option value="">Any Time</option>
                            <option value="today" <?= $posted_filter == 'today' ? 'selected' : '' ?>>Today</option>
                            <option value="week" <?= $posted_filter == 'week' ? 'selected' : '' ?>>This Week</option>
                            <option value="month" <?= $posted_filter == 'month' ? 'selected' : '' ?>>This Month</option>
                        </select>
                    </div>
                </div>

                <!-- Hidden inputs for other filters -->
                <input type="hidden" name="company_size" id="companySizeHidden" value="<?= $company_size_filter ?>">
                <input type="hidden" name="experience" id="experienceHidden" value="<?= $experience_filter ?>">
                <input type="hidden" name="salary" id="salaryHidden" value="<?= $salary_filter ?>">
                <input type="hidden" name="work_mode" id="workModeHidden" value="<?= $work_mode_filter ?>">
                <input type="hidden" name="page" id="pageHidden" value="<?= $page ?>">
            </form>

            <div class="row mt-3">
                <div class="col-12">
                    <small class="text-muted">
                        <span id="jobCount">Showing <?= min($totalPosts, $limit) ?> of <?= number_format($totalPosts) ?> job(s)</span>
                        <?php if (!empty($city_filter) || !empty($role_filter) || !empty($search_query) || !empty($job_type_filter) || !empty($posted_filter)): ?>
                            <span class="ms-2"><span class="badge bg-primary">Filtered</span></span>
                        <?php endif; ?>
                    </small>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container-fluid px-4">
        <div class="row">
            <!-- Left Sidebar - Advanced Filters -->
            <div class="col-lg-3">
                <div class="sidebar">
                    <div class="sidebar-header">
                        <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Advanced Filters</h5>
                    </div>
                    
                    <!-- Experience Level -->
                    <div class="filter-group">
                        <h6 class="fw-semibold mb-3">Experience Level</h6>
                        <div class="experience-levels">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="experienceLevel" id="fresher" 
                                       value="fresher" <?= $experience_filter == 'fresher' ? 'checked' : '' ?> onchange="applyExperienceFilter('fresher')">
                                <label class="form-check-label" for="fresher">Fresher (0-1 year)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="experienceLevel" id="junior" 
                                       value="junior" <?= $experience_filter == 'junior' ? 'checked' : '' ?> onchange="applyExperienceFilter('junior')">
                                <label class="form-check-label" for="junior">Junior (1-3 years)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="experienceLevel" id="mid" 
                                       value="mid" <?= $experience_filter == 'mid' ? 'checked' : '' ?> onchange="applyExperienceFilter('mid')">
                                <label class="form-check-label" for="mid">Mid-level (3-7 years)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="experienceLevel" id="senior" 
                                       value="senior" <?= $experience_filter == 'senior' ? 'checked' : '' ?> onchange="applyExperienceFilter('senior')">
                                <label class="form-check-label" for="senior">Senior (7+ years)</label>
                            </div>
                        </div>
                    </div>

                    <!-- Work Mode -->
                    <div class="filter-group">
                        <h6 class="fw-semibold mb-3">Work Mode</h6>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="workMode" id="wfh" 
                                   value="remote" <?= $work_mode_filter == 'remote' ? 'checked' : '' ?> onchange="applyWorkModeFilter('remote')">
                            <label class="form-check-label" for="wfh">Work from Home</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="workMode" id="hybrid" 
                                   value="hybrid" <?= $work_mode_filter == 'hybrid' ? 'checked' : '' ?> onchange="applyWorkModeFilter('hybrid')">
                            <label class="form-check-label" for="hybrid">Hybrid</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="workMode" id="office" 
                                   value="office" <?= $work_mode_filter == 'office' ? 'checked' : '' ?> onchange="applyWorkModeFilter('office')">
                            <label class="form-check-label" for="office">Office</label>
                        </div>
                    </div>

                    <!-- Role Categories -->
                    <div class="filter-group">
                        <h6 class="fw-semibold mb-3">Categories</h6>
                        <div class="category-links">
                            <a href="javascript:void(0)" onclick="filterByRole('data engineer')" class="category-link">
                                <i class="fas fa-database me-2"></i>Data Engineer
                            </a>
                            <a href="javascript:void(0)" onclick="filterByRole('data scientist')" class="category-link">
                                <i class="fas fa-chart-line me-2"></i>Data Scientist
                            </a>
                            <a href="javascript:void(0)" onclick="filterByRole('data analyst')" class="category-link">
                                <i class="fas fa-analytics me-2"></i>Data Analyst
                            </a>
                            <a href="javascript:void(0)" onclick="filterByRole('python developer')" class="category-link">
                                <i class="fab fa-python me-2"></i>Python Developer
                            </a>
                            <a href="javascript:void(0)" onclick="filterByRole('react js developer')" class="category-link">
                                <i class="fab fa-react me-2"></i>React Developer
                            </a>
                            <a href="javascript:void(0)" onclick="filterByRole('java developer')" class="category-link">
                                <i class="fab fa-java me-2"></i>Java Developer
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Middle Content - Job Listings -->
            <div class="col-lg-6">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="fw-bold">Job Listings</h4>
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary active" onclick="toggleView('card')">
                            <i class="fas fa-th-large"></i>
                        </button>
                        <button type="button" class="btn btn-outline-primary" onclick="toggleView('list')">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                </div>

                <div id="jobListings">
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($job = $result->fetch_assoc()): ?>
                            <?php 
                                $technologies = extractTechnologies($job['description'], isset($job['technologies']) ? $job['technologies'] : '');
                                $timePosted = timeAgo($job['created_at']);
                            ?>
                            <div class="job-card" data-job-id="<?= $job['job_id'] ?>">
                                <div class="job-card-header">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h5 class="job-title"><?= htmlspecialchars($job['title']) ?></h5>
                                            <span class="company-badge"><?= htmlspecialchars($job['company_name'] ?? 'Sanix Technologies') ?></span>
                                        </div>
                                        <div class="text-end">
                                            <i class="far fa-heart fs-5 text-muted" style="cursor: pointer;" onclick="toggleFavorite(this)"></i>
                                        </div>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="job-meta">
                                        <div class="meta-item">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <span><?= htmlspecialchars($job['location']) ?></span>
                                        </div>
                                        <div class="meta-item">
                                            <i class="fas fa-briefcase"></i>
                                            <span><?= htmlspecialchars($job['role']) ?></span>
                                        </div>
                                        <?php if (isset($job['salary_range']) && !empty($job['salary_range'])): ?>
                                        <div class="meta-item">
                                            <i class="fas fa-rupee-sign"></i>
                                            <span><?= htmlspecialchars($job['salary_range']) ?></span>
                                        </div>
                                        <?php endif; ?>
                                        <div class="meta-item">
                                            <i class="fas fa-calendar"></i>
                                            <span><?= $timePosted ?></span>
                                        </div>
                                    </div>
                                    <p class="text-muted mb-3"><?= htmlspecialchars(substr($job['description'], 0, 150)) . (strlen($job['description']) > 150 ? '...' : '') ?></p>
                                    
                                    <?php if (!empty($skills_required)): ?>
                                    <div class="mb-3">
                                        <?php foreach (array_slice($skills_required, 0, 4) as $skills_req): ?>
                                            <span class="tech-tag"><?= htmlspecialchars($skills_req) ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-primary flex-fill" onclick="viewJobDetails(<?= $job['job_id'] ?>)">View Details</button>
                                        <button class="btn btn-outline-primary" onclick="applyNow(<?= $job['job_id'] ?>, '<?= htmlspecialchars($job['email_to']) ?>')">
                                            <i class="fas fa-paper-plane me-1"></i>Apply Now
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="alert alert-info no-jobs-found">
                            <div class="text-center p-5">
                                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                                <h5>No jobs found</h5>
                                <p>No job postings match your current filter criteria. Try adjusting your filters or check back later for new postings.</p>
                                <?php if (!empty($city_filter) || !empty($role_filter) || !empty($search_query) || !empty($job_type_filter) || !empty($posted_filter)): ?>
                                    <button class="btn btn-primary btn-sm" onclick="clearAllFilters()">Clear All Filters</button>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Pagination -->
                <?php if ($totalPages > 1): ?>
                    <nav aria-label="Job pagination" class="mt-4">
                        <ul class="pagination justify-content-center">
                            <!-- Previous Button -->
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= buildUrl($page - 1) ?>">
                                        <i class="fas fa-chevron-left"></i>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php
                            // Always show first page
                            if ($page > 2) {
                                echo '<li class="page-item"><a class="page-link" href="' . buildUrl(1) . '">1</a></li>';
                                if ($page > 3) {
                                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                }
                            }

                            // Show only current-1, current, current+1
                            for ($i = max(1, $page - 1); $i <= min($totalPages, $page + 1); $i++): ?>
                                <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                                    <a class="page-link" href="<?= buildUrl($i) ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor;

                            // Always show last page
                            if ($page < $totalPages - 1) {
                                if ($page < $totalPages - 2) {
                                    echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                }
                                echo '<li class="page-item"><a class="page-link" href="' . buildUrl($totalPages) . '">' . $totalPages . '</a></li>';
                            }
                            ?>

                            <!-- Next Button -->
                            <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="<?= buildUrl($page + 1) ?>">
                                        <i class="fas fa-chevron-right"></i>
                                    </a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>

            <!-- Right Sidebar -->
            <div class="col-lg-3">
                <!-- Latest Jobs -->
                <div class="sidebar-jobs mb-4">
                    <div class="sidebar-header">
                        <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Latest Jobs</h5>
                    </div>
                    <div id="latestJobs">
                        <?php if ($latestJobsResult && $latestJobsResult->num_rows > 0): ?>
                            <?php while ($latestJob = $latestJobsResult->fetch_assoc()): ?>
                                <div class="trending-job" onclick="viewJobDetails(<?= $latestJob['job_id'] ?>)">
                                    <h6 class="mb-1"><?= htmlspecialchars($latestJob['title']) ?></h6>
                                    <small class="text-muted"><?= htmlspecialchars($latestJob['role']) ?> • <?= htmlspecialchars($latestJob['location']) ?></small>
                                    <div class="d-flex justify-content-between mt-2">
                                        <small class="text-primary"><?= htmlspecialchars($latestJob['salary_range'] ?? 'Salary not disclosed') ?></small>
                                        <small class="text-muted"><?= timeAgo($latestJob['created_at']) ?></small>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </div>
                    <div class="p-3">
                        <button class="btn btn-outline-primary w-100 btn-sm" onclick="viewAllJobs()">
                            View All Latest Jobs
                        </button>
                    </div>
                </div>

                <!-- Trending Technologies -->
                <div class="sidebar-jobs mb-4">
                    <div class="sidebar-header">
                        <h5 class="mb-0"><i class="fas fa-fire me-2"></i>Trending Skills</h5>
                    </div>
                    <div class="p-3">
                        <?php if ($trendingTechResult && $trendingTechResult->num_rows > 0): ?>
                            <?php 
                            $maxCount = 0;
                            $techData = array();
                            while ($tech = $trendingTechResult->fetch_assoc()) {
                                $techData[] = $tech;
                                if ($tech['job_count'] > $maxCount) $maxCount = $tech['job_count'];
                            }
                            ?>
                            <?php foreach ($techData as $tech): ?>
                                <div class="trending-tech mb-3">
                                    <div class="d-flex justify-content-between mb-1">
                                        <span class="fw-semibold"><?= htmlspecialchars($tech['tech']) ?></span>
                                        <span class="text-primary"><?= $tech['job_count'] ?> jobs</span>
                                    </div>
                                    <div class="progress" style="height: 8px;">
                                        <div class="progress-bar" role="progressbar" 
                                             style="width: <?= ($tech['job_count'] / $maxCount) * 100 ?>%; background: var(--gradient-bg);"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Learning Zone -->
                <div class="learning-zone">
                    <h6 class="fw-bold mb-3"><i class="fas fa-graduation-cap me-2"></i>Learning Zone</h6>
                    <div class="learning-item mb-2">
                        <a href="#" class="text-decoration-none text-dark">
                            <i class="fas fa-play-circle me-2 text-primary"></i>
                            Complete React Developer Course
                        </a>
                        <div class="text-muted small">1,234 enrolled</div>
                    </div>
                    <div class="learning-item mb-2">
                        <a href="#" class="text-decoration-none text-dark">
                            <i class="fas fa-play-circle me-2 text-primary"></i>
                            Python for Data Science
                        </a>
                        <div class="text-muted small">987 enrolled</div>
                    </div>
                    <div class="learning-item mb-3">
                        <a href="#" class="text-decoration-none text-dark">
                            <i class="fas fa-play-circle me-2 text-primary"></i>
                            AWS Cloud Practitioner
                        </a>
                        <div class="text-muted small">756 enrolled</div>
                    </div>
                    <button class="btn btn-primary btn-sm w-100">Explore All Courses</button>
                </div>

                <!-- Popular Companies -->
                <div class="sidebar-jobs">
                    <div class="sidebar-header">
                        <h5 class="mb-0"><i class="fas fa-building me-2"></i>Top Hiring</h5>
                    </div>
                    <div class="p-3">
                        <?php if ($topCompaniesResult && $topCompaniesResult->num_rows > 0): ?>
                            <?php while ($company = $topCompaniesResult->fetch_assoc()): ?>
                                <div class="company-item d-flex align-items-center mb-3">
                                    <div class="company-logo me-3">
                                        <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                            <i class="fas fa-building text-white"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0"><?= htmlspecialchars($company['company_name']) ?></h6>
                                        <small class="text-muted"><?= $company['job_count'] ?> open positions</small>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Job Details Modal -->
    <div class="modal fade" id="jobModal" tabindex="-1" aria-labelledby="jobModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="jobModalLabel">Job Details</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" id="jobModalBody">
                    <!-- Job details will be loaded here via AJAX -->
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="modalApplyBtn">
                        <i class="fas fa-paper-plane me-2"></i>Apply Now
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Apply Modal -->
    <div class="modal fade" id="applyModal" tabindex="-1" aria-labelledby="applyModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="applyModalLabel">Apply for Job</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="applyForm" method="POST" action="apply_job.php" enctype="multipart/form-data">
                        <input type="hidden" name="job_id" id="applyJobId">
                        <input type="hidden" name="job_email" id="applyJobEmail">
                        
                        <div class="mb-3">
                            <label for="applicantName" class="form-label">Full Name *</label>
                            <input type="text" class="form-control" id="applicantName" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="applicantEmail" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="applicantEmail" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="applicantPhone" class="form-label">Phone *</label>
                            <input type="tel" class="form-control" id="applicantPhone" name="phone" required>
                        </div>
                        <div class="mb-3">
                            <label for="resume" class="form-label">Resume *</label>
                            <input type="file" class="form-control" id="resume" name="resume" accept=".pdf,.doc,.docx" required>
                        </div>
                        <div class="mb-3">
                            <label for="coverLetter" class="form-label">Cover Letter (Optional)</label>
                            <textarea class="form-control" id="coverLetter" name="cover_letter" rows="4" placeholder="Tell us why you're interested in this position..."></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="submitApplication()">
                        <i class="fas fa-paper-plane me-2"></i>Submit Application
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php include('footer.php'); ?>

    <!-- Hidden job data for JavaScript -->
    <script>
        const jobsData = [
            <?php 
            // Reset result pointer to beginning
            $result->data_seek(0);
            $jobsArray = array();
            while ($job = $result->fetch_assoc()) {
                $technologies = extractTechnologies($job['description'], isset($job['technologies']) ? $job['technologies'] : '');
                $jobsArray[] = json_encode(array(
                    'id' => $job['job_id'],
                    'title' => $job['title'],
                    'company' => $job['company_name'] ?? 'Sanix Technologies',
                    'location' => $job['location'],
                    'role' => $job['role'],
                    'description' => $job['description'],
                    'email' => $job['email_to'],
                    'posted' => timeAgo($job['created_at']),
                    'formatted_date' => $job['formatted_date'],
                    'salary_range' => $job['salary_range'] ?? '',
                    'technologies' => $technologies,
                    'requirements' => isset($job['requirements']) ? explode('\n', $job['requirements']) : []
                ));
            }
            echo implode(',', $jobsArray);
            ?>
        ];
        
        const currentFilters = {
            search: '<?= htmlspecialchars($search_query) ?>',
            city: '<?= htmlspecialchars($city_filter) ?>',
            role: '<?= htmlspecialchars($role_filter) ?>',
            job_type: '<?= htmlspecialchars($job_type_filter) ?>',
            company_size: '<?= htmlspecialchars($company_size_filter) ?>',
            experience: '<?= htmlspecialchars($experience_filter) ?>',
            salary: '<?= htmlspecialchars($salary_filter) ?>',
            work_mode: '<?= htmlspecialchars($work_mode_filter) ?>',
            posted: '<?= htmlspecialchars($posted_filter) ?>'
        };
        
        const currentPage = <?= $page ?>;
        const totalPages = <?= $totalPages ?>;
        const totalJobs = <?= $totalPosts ?>;


        /**
 * Enhanced Job Portal JavaScript
 * Handles all interactive functionality for the job portal
 */

// Global variables
let currentJobId = null;
let currentJobEmail = null;
let searchTimeout = null;
let isLoading = false;

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeFilters();
    setupEventListeners();
    animateJobCards();
    initializeTooltips();
});

/**
 * Initialize filter states from URL parameters
 */
function initializeFilters() {
    const urlParams = new URLSearchParams(window.location.search);
    
    // Set filter values from URL
    const cityParam = urlParams.get('city');
    const roleParam = urlParams.get('role');
    const jobTypeParam = urlParams.get('job_type');
    const postedParam = urlParams.get('posted');
    const searchParam = urlParams.get('search');
    
    if (cityParam) document.getElementById('cityFilter').value = cityParam;
    if (roleParam) document.getElementById('roleFilter').value = roleParam;
    if (jobTypeParam) document.getElementById('jobTypeFilter').value = jobTypeParam;
    if (postedParam) document.getElementById('postedFilter').value = postedParam;
    if (searchParam) document.getElementById('searchInput').value = searchParam;
    
    // Update filter chips active state
    updateFilterChipsState();
}

/**
 * Setup all event listeners
 */
function setupEventListeners() {
    // Real-time search with debouncing
    const searchInput = document.getElementById('searchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                if (this.value.length >= 3 || this.value.length === 0) {
                    submitForm();
                }
            }, 500);
        });
    }
    
    // Handle form submission
    const filterForm = document.getElementById('filterForm');
    if (filterForm) {
        filterForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitForm();
        });
    }
    
    // Auto-refresh latest jobs every 2 minutes
    setInterval(refreshLatestJobs, 120000);
    
    // Add smooth scrolling for all anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
}

/**
 * Submit form with current filter values
 */
function submitForm() {
    if (isLoading) return;
    
    showLoadingState();
    
    // Reset page to 1 when filters change
    document.getElementById('pageHidden').value = '1';
    
    const form = document.getElementById('filterForm');
    form.submit();
}

/**
 * Apply quick filter
 */
function applyQuickFilter(filterType, filterValue) {
    // Clear conflicting filters
    if (filterType === 'job_type') {
        document.getElementById('jobTypeFilter').value = filterValue;
        // Clear work mode if job type is selected
        clearWorkModeFilters();
    } else if (filterType === 'work_mode') {
        document.getElementById('workModeHidden').value = filterValue;
        // Update radio buttons
        const workModeRadios = document.querySelectorAll('input[name="workMode"]');
        workModeRadios.forEach(radio => {
            radio.checked = radio.value === filterValue;
        });
    } else if (filterType === 'experience') {
        document.getElementById('experienceHidden').value = filterValue;
        // Update radio buttons
        const expRadios = document.querySelectorAll('input[name="experienceLevel"]');
        expRadios.forEach(radio => {
            radio.checked = radio.value === filterValue;
        });
    }
    
    updateFilterChipsState();
    submitForm();
}

/**
 * Clear all filters
 */
function clearAllFilters() {
    // Reset all form elements
    document.getElementById('cityFilter').value = '';
    document.getElementById('roleFilter').value = '';
    document.getElementById('jobTypeFilter').value = '';
    document.getElementById('postedFilter').value = '';
    document.getElementById('searchInput').value = '';
    
    // Reset hidden inputs
    document.getElementById('companySizeHidden').value = '';
    document.getElementById('experienceHidden').value = '';
    document.getElementById('salaryHidden').value = '';
    document.getElementById('workModeHidden').value = '';
    document.getElementById('pageHidden').value = '1';
    
    // Reset checkboxes and radio buttons
    const checkboxes = document.querySelectorAll('input[type="checkbox"], input[type="radio"]');
    checkboxes.forEach(cb => cb.checked = false);
    
    updateFilterChipsState();
    
    // Redirect to clean URL
    window.location.href = window.location.pathname;
}

/**
 * Apply experience filter
 */
function applyExperienceFilter(experience) {
    document.getElementById('experienceHidden').value = experience;
    updateFilterChipsState();
    submitForm();
}

/**
 * Apply work mode filter
 */
function applyWorkModeFilter(workMode) {
    document.getElementById('workModeHidden').value = workMode;
    updateFilterChipsState();
    submitForm();
}

/**
 * Clear work mode filters
 */
function clearWorkModeFilters() {
    document.getElementById('workModeHidden').value = '';
    const workModeRadios = document.querySelectorAll('input[name="workMode"]');
    workModeRadios.forEach(radio => radio.checked = false);
}

/**
 * Filter by specific role
 */
function filterByRole(role) {
    document.getElementById('roleFilter').value = role;
    updateFilterChipsState();
    submitForm();
}

/**
 * Update filter chips active state
 */
function updateFilterChipsState() {
    const chips = document.querySelectorAll('.filter-chip');
    chips.forEach(chip => {
        chip.classList.remove('active');
    });
    
    // Check current filter values and activate corresponding chips
    const jobType = document.getElementById('jobTypeFilter').value;
    const workMode = document.getElementById('workModeHidden').value;
    const experience = document.getElementById('experienceHidden').value;
    
    if (jobType === 'fulltime') {
        document.querySelector('.filter-chip[onclick*="fulltime"]')?.classList.add('active');
    }
    if (jobType === 'parttime') {
        document.querySelector('.filter-chip[onclick*="parttime"]')?.classList.add('active');
    }
    if (workMode === 'remote') {
        document.querySelector('.filter-chip[onclick*="remote"]')?.classList.add('active');
    }
    if (experience === 'fresher') {
        document.querySelector('.filter-chip[onclick*="fresher"]')?.classList.add('active');
    }
    if (experience === 'senior') {
        document.querySelector('.filter-chip[onclick*="senior"]')?.classList.add('active');
    }
}

/**
 * View job details in modal
 */
function viewJobDetails(jobId) {
    currentJobId = jobId;
    
    // Show loading in modal
    const modalBody = document.getElementById('jobModalBody');
    modalBody.innerHTML = `
        <div class="text-center p-5">
            <div class="spinner mx-auto mb-3"></div>
            <p>Loading job details...</p>
        </div>
    `;
    
    // Show modal
    const modal = new bootstrap.Modal(document.getElementById('jobModal'));
    modal.show();
    
    // Fetch job details via AJAX
    fetch(`get_job_details.php?id=${jobId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayJobDetails(data.job);
            } else {
                modalBody.innerHTML = `
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Error loading job details. Please try again.
                    </div>
                `;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            modalBody.innerHTML = `
                <div class="alert alert-danger">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Network error. Please check your connection and try again.
                </div>
            `;
        });
}

/**
 * Display job details in modal
 */
function displayJobDetails(job) {
    const modalBody = document.getElementById('jobModalBody');
    
    modalBody.innerHTML = `
        <div class="job-detail-header mb-4">
            <h4 class="text-primary">${job.title}</h4>
            <h6 class="text-muted">${job.company || 'Sanix Technologies'}</h6>
            <div class="job-meta mt-3">
                <div class="meta-item">
                    <i class="fas fa-map-marker-alt"></i>
                    <span>${job.location}</span>
                </div>
                <div class="meta-item">
                    <i class="fas fa-briefcase"></i>
                    <span>${job.role}</span>
                </div>
                ${job.salary_range ? `
                <div class="meta-item">
                    <i class="fas fa-rupee-sign"></i>
                    <span>${job.salary_range}</span>
                </div>
                ` : ''}
                <div class="meta-item">
                    <i class="fas fa-calendar"></i>
                    <span>Posted: ${job.formatted_date}</span>
                </div>
            </div>
        </div>

        <div class="mb-4">
            <h6 class="fw-bold">Job Description</h6>
            <div style="white-space: pre-line;">${job.description}</div>
        </div>

        ${job.requirements && job.requirements.length > 0 ? `
        <div class="mb-4">
            <h6 class="fw-bold">Requirements</h6>
            <ul class="list-unstyled">
                ${job.requirements.map(req => `<li class="mb-2"><i class="fas fa-check text-success me-2"></i>${req}</li>`).join('')}
            </ul>
        </div>
        ` : ''}

        ${job.technologies && job.technologies.length > 0 ? `
        <div class="mb-4">
            <h6 class="fw-bold">Technologies</h6>
            <div>
                ${job.technologies.map(tech => `<span class="tech-tag">${tech}</span>`).join('')}
            </div>
        </div>
        ` : ''}

        <div class="alert alert-info">
            <i class="fas fa-envelope me-2"></i>
            <strong>Contact:</strong> ${job.email}
        </div>
    `;
    
    // Update apply button
    const applyBtn = document.getElementById('modalApplyBtn');
    applyBtn.onclick = () => applyFromModal(job.id, job.email);
}

/**
 * Apply for job
 */
function applyNow(jobId, jobEmail) {
    currentJobId = jobId;
    currentJobEmail = jobEmail;
    
    // Find job data
    const job = jobsData.find(j => j.id == jobId);
    const jobTitle = job ? job.title : 'this position';
    
    document.getElementById('applyModalLabel').textContent = `Apply for ${jobTitle}`;
    document.getElementById('applyJobId').value = jobId;
    document.getElementById('applyJobEmail').value = jobEmail;
    
    const modal = new bootstrap.Modal(document.getElementById('applyModal'));
    modal.show();
}

/**
 * Apply from job details modal
 */
function applyFromModal(jobId, jobEmail) {
    const jobModal = bootstrap.Modal.getInstance(document.getElementById('jobModal'));
    jobModal.hide();
    
    setTimeout(() => {
        applyNow(jobId, jobEmail);
    }, 300);
}

/**
 * Submit job application
 */
function submitApplication() {
    const form = document.getElementById('applyForm');
    
    if (form.checkValidity()) {
        // Show loading state
        const submitBtn = document.querySelector('#applyModal .btn-primary');
        const originalText = submitBtn.innerHTML;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
        submitBtn.disabled = true;
        
        // Create FormData object
        const formData = new FormData(form);
        
        // Submit via AJAX
        fetch('apply_job.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Show success message
                showNotification('Application submitted successfully! We will contact you soon.', 'success');
                
                // Close modal and reset form
                const modal = bootstrap.Modal.getInstance(document.getElementById('applyModal'));
                modal.hide();
                form.reset();
            } else {
                showNotification('Error submitting application: ' + data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('Network error. Please try again.', 'error');
        })
        .finally(() => {
            // Restore button state
            submitBtn.innerHTML = originalText;
            submitBtn.disabled = false;
        });
    } else {
        form.reportValidity();
    }
}

/**
 * Toggle favorite status
 */
function toggleFavorite(element) {
    if (element.classList.contains('far')) {
        element.classList.remove('far');
        element.classList.add('fas', 'favorited');
        element.style.color = '#ef4444';
        
        // Add to favorites (you can implement localStorage or send to backend)
        const jobCard = element.closest('.job-card');
        const jobId = jobCard.dataset.jobId;
        addToFavorites(jobId);
        
        showNotification('Added to favorites!', 'success');
    } else {
        element.classList.remove('fas', 'favorited');
        element.classList.add('far');
        element.style.color = '#6c757d';
        
        // Remove from favorites
        const jobCard = element.closest('.job-card');
        const jobId = jobCard.dataset.jobId;
        removeFromFavorites(jobId);
        
        showNotification('Removed from favorites', 'info');
    }
}

/**
 * Add job to favorites
 */
function addToFavorites(jobId) {
    // In real implementation, you might want to save to database
    let favorites = JSON.parse(sessionStorage.getItem('favoriteJobs') || '[]');
    if (!favorites.includes(jobId)) {
        favorites.push(jobId);
        sessionStorage.setItem('favoriteJobs', JSON.stringify(favorites));
    }
}

/**
 * Remove job from favorites
 */
function removeFromFavorites(jobId) {
    let favorites = JSON.parse(sessionStorage.getItem('favoriteJobs') || '[]');
    favorites = favorites.filter(id => id !== jobId);
    sessionStorage.setItem('favoriteJobs', JSON.stringify(favorites));
}

/**
 * Toggle view between card and list
 */
function toggleView(viewType) {
    const buttons = document.querySelectorAll('.btn-group .btn');
    buttons.forEach(btn => btn.classList.remove('active'));
    
    const jobListings = document.getElementById('jobListings');
    
    if (viewType === 'card') {
        buttons[0].classList.add('active');
        jobListings.classList.remove('list-view');
        jobListings.classList.add('card-view');
    } else {
        buttons[1].classList.add('active');
        jobListings.classList.remove('card-view');
        jobListings.classList.add('list-view');
        
        // Apply list view styles
        const jobCards = document.querySelectorAll('.job-card');
        jobCards.forEach(card => {
            card.style.marginBottom = '0.5rem';
        });
    }
}

/**
 * Show loading state
 */
function showLoadingState() {
    isLoading = true;
    const jobListings = document.getElementById('jobListings');
    
    // Add loading overlay
    const loadingOverlay = document.createElement('div');
    loadingOverlay.className = 'loading-overlay';
    loadingOverlay.innerHTML = `
        <div>
            <div class="spinner mx-auto mb-3"></div>
            <p class="text-muted">Loading jobs...</p>
        </div>
    `;
    
    jobListings.style.position = 'relative';
    jobListings.appendChild(loadingOverlay);
}

/**
 * Hide loading state
 */
function hideLoadingState() {
    isLoading = false;
    const loadingOverlay = document.querySelector('.loading-overlay');
    if (loadingOverlay) {
        loadingOverlay.remove();
    }
}

/**
 * Animate job cards on load
 */
function animateJobCards() {
    const jobCards = document.querySelectorAll('.job-card');
    
    jobCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        
        setTimeout(() => {
            card.style.transition = 'all 0.5s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

/**
 * Initialize tooltips
 */
function initializeTooltips() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    const tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
}

/**
 * Show notification
 */
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existingNotifications = document.querySelectorAll('.notification-toast');
    existingNotifications.forEach(n => n.remove());
    
    const notification = document.createElement('div');
    notification.className = `notification-toast alert alert-${type === 'error' ? 'danger' : type} alert-dismissible`;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        border-radius: 12px;
        box-shadow: var(--hover-shadow);
        animation: slideInRight 0.5s ease;
    `;
    
    notification.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'} me-2"></i>
            <span>${message}</span>
        </div>
        <button type="button" class="btn-close" onclick="this.parentElement.remove()"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (notification.parentElement) {
            notification.style.animation = 'fadeOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }
    }, 5000);
}

/**
 * Refresh latest jobs in sidebar
 */
function refreshLatestJobs() {
    fetch('get_latest_jobs.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateLatestJobsSidebar(data.jobs);
            }
        })
        .catch(error => {
            console.error('Error refreshing latest jobs:', error);
        });
}

/**
 * Update latest jobs sidebar
 */
function updateLatestJobsSidebar(jobs) {
    const latestJobsContainer = document.getElementById('latestJobs');
    
    latestJobsContainer.innerHTML = jobs.map(job => `
        <div class="trending-job" onclick="viewJobDetails(${job.id})">
            <h6 class="mb-1">${job.title}</h6>
            <small class="text-muted">${job.role} • ${job.location}</small>
            <div class="d-flex justify-content-between mt-2">
                <small class="text-primary">${job.salary_range || 'Salary not disclosed'}</small>
                <small class="text-muted">${job.time_ago}</small>
            </div>
        </div>
    `).join('');
}

/**
 * View all jobs (clear filters)
 */
function viewAllJobs() {
    clearAllFilters();
}

/**
 * Handle pagination
 */
function changePage(page) {
    document.getElementById('pageHidden').value = page;
    showLoadingState();
    
    // Scroll to top of job listings
    const jobListings = document.getElementById('jobListings');
    jobListings.scrollIntoView({ behavior: 'smooth', block: 'start' });
    
    // Submit form with new page
    setTimeout(() => {
        submitForm();
    }, 500);
}

/**
 * Share job on social media
 */
function shareJob(jobId, platform) {
    const job = jobsData.find(j => j.id == jobId);
    if (!job) return;
    
    const url = encodeURIComponent(window.location.origin + '/job-details.php?id=' + jobId);
    const text = encodeURIComponent(`Check out this job: ${job.title} at ${job.company}`);
    
    let shareUrl = '';
    
    switch(platform) {
        case 'linkedin':
            shareUrl = `https://www.linkedin.com/sharing/share-offsite/?url=${url}`;
            break;
        case 'twitter':
            shareUrl = `https://twitter.com/intent/tweet?url=${url}&text=${text}`;
            break;
        case 'whatsapp':
            shareUrl = `https://wa.me/?text=${text}%20${url}`;
            break;
        case 'email':
            shareUrl = `mailto:?subject=${encodeURIComponent(job.title + ' - Job Opportunity')}&body=${text}%20${url}`;
            break;
    }
    
    if (shareUrl) {
        window.open(shareUrl, '_blank', 'width=600,height=400');
    }
}

/**
 * Copy job link to clipboard
 */
function copyJobLink(jobId) {
    const url = window.location.origin + '/job-details.php?id=' + jobId;
    
    navigator.clipboard.writeText(url).then(() => {
        showNotification('Job link copied to clipboard!', 'success');
    }).catch(() => {
        // Fallback for older browsers
        const textArea = document.createElement('textarea');
        textArea.value = url;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        showNotification('Job link copied to clipboard!', 'success');
    });
}

/**
 * Advanced search functionality
 */
function advancedSearch() {
    const searchTerm = document.getElementById('searchInput').value;
    
    if (searchTerm.length < 2) {
        showNotification('Please enter at least 2 characters to search', 'warning');
        return;
    }
    
    submitForm();
}

/**
 * Save search preferences
 */
function saveSearchPreferences() {
    const preferences = {
        city: document.getElementById('cityFilter').value,
        role: document.getElementById('roleFilter').value,
        jobType: document.getElementById('jobTypeFilter').value,
        experience: document.getElementById('experienceHidden').value,
        workMode: document.getElementById('workModeHidden').value
    };
    
    sessionStorage.setItem('jobSearchPreferences', JSON.stringify(preferences));
    showNotification('Search preferences saved!', 'success');
}

/**
 * Load saved search preferences
 */
function loadSearchPreferences() {
    const preferences = JSON.parse(sessionStorage.getItem('jobSearchPreferences') || '{}');
    
    if (Object.keys(preferences).length > 0) {
        document.getElementById('cityFilter').value = preferences.city || '';
        document.getElementById('roleFilter').value = preferences.role || '';
        document.getElementById('jobTypeFilter').value = preferences.jobType || '';
        document.getElementById('experienceHidden').value = preferences.experience || '';
        document.getElementById('workModeHidden').value = preferences.workMode || '';
        
        // Update radio buttons
        if (preferences.experience) {
            const expRadio = document.getElementById(preferences.experience);
            if (expRadio) expRadio.checked = true;
        }
        
        if (preferences.workMode) {
            const workRadio = document.getElementById(preferences.workMode === 'remote' ? 'wfh' : preferences.workMode);
            if (workRadio) workRadio.checked = true;
        }
        
        updateFilterChipsState();
        showNotification('Search preferences loaded!', 'info');
    }
}

/**
 * Export job listings to CSV
 */
function exportJobs() {
    const csvContent = generateCSV();
    const blob = new Blob([csvContent], { type: 'text/csv' });
    const url = window.URL.createObjectURL(blob);
    
    const a = document.createElement('a');
    a.href = url;
    a.download = 'job_listings_' + new Date().toISOString().split('T')[0] + '.csv';
    document.body.appendChild(a);
    a.click();
    document.body.removeChild(a);
    window.URL.revokeObjectURL(url);
    
    showNotification('Job listings exported successfully!', 'success');
}

/**
 * Generate CSV content
 */
function generateCSV() {
    const headers = ['Title', 'Company', 'Location', 'Role', 'Posted Date', 'Email'];
    const rows = jobsData.map(job => [
        `"${job.title}"`,
        `"${job.company}"`,
        `"${job.location}"`,
        `"${job.role}"`,
        `"${job.formatted_date}"`,
        `"${job.email}"`
    ]);
    
    return [headers.join(','), ...rows.map(row => row.join(','))].join('\n');
}

/**
 * Handle keyboard shortcuts
 */
document.addEventListener('keydown', function(e) {
    // Ctrl/Cmd + K for search focus
    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
        e.preventDefault();
        document.getElementById('searchInput').focus();
    }
    
    // Escape to close modals
    if (e.key === 'Escape') {
        const openModals = document.querySelectorAll('.modal.show');
        openModals.forEach(modal => {
            const modalInstance = bootstrap.Modal.getInstance(modal);
            if (modalInstance) modalInstance.hide();
        });
    }
});

/**
 * Infinite scroll for job listings (optional feature)
 */
function enableInfiniteScroll() {
    let loading = false;
    
    window.addEventListener('scroll', () => {
        if (loading) return;
        
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
        const windowHeight = window.innerHeight;
        const docHeight = document.documentElement.offsetHeight;
        
        if (scrollTop + windowHeight >= docHeight - 1000) {
            loading = true;
            loadMoreJobs();
        }
    });
}

/**
 * Load more jobs for infinite scroll
 */
function loadMoreJobs() {
    // This would make an AJAX call to load more jobs
    // For now, just a placeholder
    console.log('Loading more jobs...');
}

/**
 * Initialize job card animations
 */
function initializeJobCardAnimations() {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
            }
        });
    }, { threshold: 0.1 });
    
    document.querySelectorAll('.job-card').forEach(card => {
        observer.observe(card);
    });
}

/**
 * Format salary display
 */
function formatSalary(min, max) {
    if (!min && !max) return 'Salary not disclosed';
    if (min && max) return `₹${min}-${max} LPA`;
    if (min) return `₹${min}+ LPA`;
    return 'Competitive salary';
}

/**
 * Calculate reading time for job description
 */
function calculateReadingTime(text) {
    const wordsPerMinute = 200;
    const words = text.trim().split(/\s+/).length;
    const minutes = Math.ceil(words / wordsPerMinute);
    return minutes === 1 ? '1 min read' : `${minutes} min read`;
}

/**
 * Validate form inputs
 */
function validateForm(formId) {
    const form = document.getElementById(formId);
    const inputs = form.querySelectorAll('input[required], textarea[required], select[required]');
    let isValid = true;
    
    inputs.forEach(input => {
        const value = input.value.trim();
        const errorElement = input.parentElement.querySelector('.invalid-feedback');
        
        // Remove existing error messages
        if (errorElement) errorElement.remove();
        
        // Email validation
        if (input.type === 'email' && value) {
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailRegex.test(value)) {
                showFieldError(input, 'Please enter a valid email address');
                isValid = false;
            }
        }
        
        // Phone validation
        if (input.type === 'tel' && value) {
            const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
            if (!phoneRegex.test(value.replace(/[\s\-\(\)]/g, ''))) {
                showFieldError(input, 'Please enter a valid phone number');
                isValid = false;
            }
        }
        
        // File validation
        if (input.type === 'file' && input.files.length > 0) {
            const file = input.files[0];
            const maxSize = 5 * 1024 * 1024; // 5MB
            const allowedTypes = ['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            
            if (file.size > maxSize) {
                showFieldError(input, 'File size must be less than 5MB');
                isValid = false;
            }
            
            if (!allowedTypes.includes(file.type)) {
                showFieldError(input, 'Only PDF, DOC, and DOCX files are allowed');
                isValid = false;
            }
        }
        
        // Required field validation
        if (input.hasAttribute('required') && !value) {
            showFieldError(input, 'This field is required');
            isValid = false;
        }
    });
    
    return isValid;
}

/**
 * Show field error
 */
function showFieldError(input, message) {
    input.classList.add('is-invalid');
    
    const errorDiv = document.createElement('div');
    errorDiv.className = 'invalid-feedback';
    errorDiv.textContent = message;
    
    input.parentElement.appendChild(errorDiv);
}

/**
 * Clear field errors
 */
function clearFieldErrors(formId) {
    const form = document.getElementById(formId);
    const invalidInputs = form.querySelectorAll('.is-invalid');
    const errorMessages = form.querySelectorAll('.invalid-feedback');
    
    invalidInputs.forEach(input => input.classList.remove('is-invalid'));
    errorMessages.forEach(error => error.remove());
}

/**
 * Auto-save form data
 */
function autoSaveFormData(formId) {
    const form = document.getElementById(formId);
    const inputs = form.querySelectorAll('input, textarea, select');
    
    inputs.forEach(input => {
        input.addEventListener('input', () => {
            const formData = new FormData(form);
            const dataObject = {};
            
            for (let [key, value] of formData.entries()) {
                dataObject[key] = value;
            }
            
            sessionStorage.setItem(`formData_${formId}`, JSON.stringify(dataObject));
        });
    });
}

/**
 * Restore form data
 */
function restoreFormData(formId) {
    const savedData = sessionStorage.getItem(`formData_${formId}`);
    if (!savedData) return;
    
    const data = JSON.parse(savedData);
    const form = document.getElementById(formId);
    
    Object.keys(data).forEach(key => {
        const input = form.querySelector(`[name="${key}"]`);
        if (input && input.type !== 'file') {
            input.value = data[key];
        }
    });
}

/**
 * Track user interactions for analytics
 */
function trackEvent(eventName, properties = {}) {
    // This would integrate with your analytics service
    console.log('Event tracked:', eventName, properties);
    
    // Example: Send to Google Analytics
    if (typeof gtag !== 'undefined') {
        gtag('event', eventName, properties);
    }
}

/**
 * Initialize page with all features
 */
function initializePage() {
    initializeFilters();
    setupEventListeners();
    animateJobCards();
    initializeTooltips();
    initializeJobCardAnimations();
    
    // Auto-save application form data
    autoSaveFormData('applyForm');
    
    // Restore any saved form data
    restoreFormData('applyForm');
    
    // Track page view
    trackEvent('job_portal_page_view', {
        total_jobs: totalJobs,
        current_page: currentPage,
        filters_applied: Object.values(currentFilters).filter(v => v && v.length > 0).length
    });
}

/**
 * Enhanced error handling
 */
window.addEventListener('error', function(e) {
    console.error('JavaScript error:', e.error);
    showNotification('An unexpected error occurred. Please refresh the page.', 'error');
});

/**
 * Performance monitoring
 */
function measurePerformance() {
    if ('performance' in window) {
        window.addEventListener('load', () => {
            const loadTime = performance.timing.loadEventEnd - performance.timing.navigationStart;
            console.log('Page load time:', loadTime + 'ms');
            
            // Track performance
            trackEvent('page_performance', {
                load_time: loadTime,
                page_type: 'job_portal'
            });
        });
    }
}

// Initialize everything when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializePage);
} else {
    initializePage();
}

// Measure performance
measurePerformance();

// Add CSS animations dynamically
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeOut {
        from { opacity: 1; transform: translateX(0); }
        to { opacity: 0; transform: translateX(100%); }
    }
    
    .notification-toast {
        animation: slideInRight 0.5s ease !important;
    }
    
    .job-card.loading {
        opacity: 0.6;
        pointer-events: none;
    }
    
    .filter-chip.loading {
        opacity: 0.6;
        pointer-events: none;
    }
`;
document.head.appendChild(style);

    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
