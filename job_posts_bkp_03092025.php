<?php
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
$latestJobsQuery = "SELECT id, title, role, location, created_at, salary_range FROM job_post WHERE is_approved = 1 ORDER BY created_at DESC LIMIT 5";
$latestJobsResult = $conn->query($latestJobsQuery);

// Get trending technologies (most mentioned in job descriptions)
$trendingTechQuery = "SELECT 
    'React' as tech, COUNT(*) as job_count FROM job_post WHERE is_approved = 1 AND (LOWER(description) LIKE '%react%' OR LOWER(technologies) LIKE '%react%')
    UNION ALL
    SELECT 'Python' as tech, COUNT(*) as job_count FROM job_post WHERE is_approved = 1 AND (LOWER(description) LIKE '%python%' OR LOWER(technologies) LIKE '%python%')
    UNION ALL
    SELECT 'Node.js' as tech, COUNT(*) as job_count FROM job_post WHERE is_approved = 1 AND (LOWER(description) LIKE '%node%' OR LOWER(technologies) LIKE '%node%')
    UNION ALL
    SELECT 'AWS' as tech, COUNT(*) as job_count FROM job_post WHERE is_approved = 1 AND (LOWER(description) LIKE '%aws%' OR LOWER(technologies) LIKE '%aws%')
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
    <link rel="stylesheet" href="css/job_portal.css">
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
                            <div class="job-card" data-job-id="<?= $job['id'] ?>">
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
                                    
                                    <?php if (!empty($technologies)): ?>
                                    <div class="mb-3">
                                        <?php foreach (array_slice($technologies, 0, 4) as $tech): ?>
                                            <span class="tech-tag"><?= htmlspecialchars($tech) ?></span>
                                        <?php endforeach; ?>
                                    </div>
                                    <?php endif; ?>
                                    
                                    <div class="d-flex gap-2">
                                        <button class="btn btn-primary flex-fill" onclick="viewJobDetails(<?= $job['id'] ?>)">View Details</button>
                                        <button class="btn btn-outline-primary" onclick="applyNow(<?= $job['id'] ?>, '<?= htmlspecialchars($job['email_to']) ?>')">
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
                                <div class="trending-job" onclick="viewJobDetails(<?= $latestJob['id'] ?>)">
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

                <?php include('right_sidebar.php'); ?>
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
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/job_portal.js"></script>
</body>
</html>

<?php
$conn->close();
?>