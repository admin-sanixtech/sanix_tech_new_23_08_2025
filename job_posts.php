<?php
include 'db_connection.php';

// Get filter parameters
$city_filter = isset($_GET['city']) ? $_GET['city'] : '';
$role_filter = isset($_GET['role']) ? $_GET['role'] : '';

// Pagination settings
$limit = 3; // jobs per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Build SQL query with filters for counting
$countQuery = "SELECT COUNT(*) AS total FROM job_post WHERE is_approved = 1";
$countParams = array();
$countTypes = "";

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
$sql = "SELECT * FROM job_post WHERE is_approved = 1";
$params = array();
$types = "";

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

$sql .= " ORDER BY created_at DESC LIMIT ?, ?";
$params[] = $offset;
$params[] = $limit;
$types .= "ii";

// Execute main query
$stmt = $conn->prepare($sql);
$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Function to build pagination URLs with filters
function buildUrl($page, $city = '', $role = '') {
    $params = array();
    if (!empty($city)) $params['city'] = $city;
    if (!empty($role)) $params['role'] = $role;
    $params['page'] = $page;
    return '?' . http_build_query($params);
}
?>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sanix Technologies</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/user_styles.css">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/user_styles.css">
</head>
<body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php include('header.php'); ?> <!-- Include your common header -->
    <?php include 'navbar.php'; ?> <!-- Include your common navbar -->
    
    <!-- Filter Section -->
    <div class="container-fluid">
        <div class="card mb-3 p-3">
            <div class="row align-items-center">
                <div class="col-md-2">
                    <label class="form-label">Filter by City:</label>
                </div>
                <div class="col-md-4">
                    <select class="form-select" id="cityFilter" onchange="applyFilters()">
                        <option value="">All Cities</option>
                        <option value="mumbai" <?= $city_filter == 'mumbai' ? 'selected' : '' ?>>Mumbai</option>
                        <option value="delhi" <?= $city_filter == 'delhi' ? 'selected' : '' ?>>Delhi</option>
                        <option value="bangalore" <?= $city_filter == 'bangalore' ? 'selected' : '' ?>>Bangalore</option>
                        <option value="chennai" <?= $city_filter == 'chennai' ? 'selected' : '' ?>>Chennai</option>
                        <option value="kolkata" <?= $city_filter == 'kolkata' ? 'selected' : '' ?>>Kolkata</option>
                        <option value="hyderabad" <?= $city_filter == 'hyderabad' ? 'selected' : '' ?>>Hyderabad</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">Filter by Role:</label>
                </div>
                <div class="col-md-4">
                    <select class="form-select" id="roleFilter" onchange="applyFilters()">
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
            </div>
            
            <!-- Results count -->
            <div class="row mt-2">
                <div class="col-12">
                    <small class="text-muted">
                        Showing <?= min($totalPosts, $limit) ?> of <?= $totalPosts ?> job(s)
                        <?php if (!empty($city_filter) || !empty($role_filter)): ?>
                            (filtered)
                        <?php endif; ?>
                    </small>
                </div>
            </div>
        </div>
    </div>

    <div class="container-fluid my-5 px-0">
        <div class="row gx-0">
            <!-- Sidebar Accordion -->
            <aside class="col-md-3 px-3"> 
                <div class="accordion" id="uiuxCourse">
                    <!-- Module 1: Data Related -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="dataHeading">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#dataModule" aria-expanded="true" aria-controls="dataModule">
                                Data Related
                            </button>
                        </h2>
                        <div id="dataModule" class="accordion-collapse collapse show" aria-labelledby="dataHeading" data-bs-parent="#uiuxCourse">
                            <div class="accordion-body">
                                <ul class="list-unstyled">
                                    <li><a href="javascript:void(0)" onclick="filterByRole('data engineer')" class="text-decoration-none">Data Engineer</a></li>
                                    <li><a href="javascript:void(0)" onclick="filterByRole('data scientist')" class="text-decoration-none">Data Scientist</a></li>
                                    <li><a href="javascript:void(0)" onclick="filterByRole('data analyst')" class="text-decoration-none">Data Analyst</a></li>
                                    <li><a href="javascript:void(0)" onclick="filterByRole('data architect')" class="text-decoration-none">Data Architect</a></li>
                                    <li><a href="javascript:void(0)" onclick="filterByRole('data manager')" class="text-decoration-none">Data Manager</a></li>
                                </ul>
                            </div>
                        </div>
                    </div> 
                    
                    <!-- Module 2: Development Related -->
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="devHeading">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#devModule" aria-expanded="false" aria-controls="devModule">
                                Development Related
                            </button>
                        </h2>
                        <div id="devModule" class="accordion-collapse collapse" aria-labelledby="devHeading" data-bs-parent="#uiuxCourse">
                            <div class="accordion-body">
                                <ul class="list-unstyled">
                                    <li><a href="javascript:void(0)" onclick="filterByRole('python developer')" class="text-decoration-none">Python Developer</a></li>
                                    <li><a href="javascript:void(0)" onclick="filterByRole('sql developer')" class="text-decoration-none">SQL Developer</a></li>
                                    <li><a href="javascript:void(0)" onclick="filterByRole('php developer')" class="text-decoration-none">PHP Developer</a></li>
                                    <li><a href="javascript:void(0)" onclick="filterByRole('react js developer')" class="text-decoration-none">React JS Developer</a></li>
                                    <li><a href="javascript:void(0)" onclick="filterByRole('java developer')" class="text-decoration-none">Java Developer</a></li>
                                    <li><a href="javascript:void(0)" onclick="filterByRole('web developer')" class="text-decoration-none">Web Developer</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </aside>
            
            <!-- Main Content -->
            <div class="col-md-7" style="border-right: 2px solid #ccc">
                <div id="html-description" class="p-3" style="background-color: #85bdc6ff">
                    <h3>Job Posts</h3>
                    
                    <?php if ($result && $result->num_rows > 0): ?>
                        <?php while ($job = $result->fetch_assoc()): ?>
                            <div class="card mb-3">
                                <div class="card-body">
                                    <h5><?= htmlspecialchars($job['title']) ?> (<?= htmlspecialchars($job['role']) ?>)</h5>
                                    <p><?= nl2br(htmlspecialchars($job['description'])) ?></p>
                                    <p><b>Location:</b> <?= htmlspecialchars($job['location']) ?></p>
                                    <p><b>Email:</b> <?= htmlspecialchars($job['email_to']) ?></p>
                                    <div class="alert alert-light p-2">
                                        <p class="text-muted mb-0"><small>Posted on <?= date('F j, Y', strtotime($job['created_at'])) ?></small></p>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <h5>No jobs found</h5>
                            <p>No job postings match your current filter criteria. Try adjusting your filters or check back later for new postings.</p>
                            <?php if (!empty($city_filter) || !empty($role_filter)): ?>
                                <a href="?" class="btn btn-primary btn-sm">Clear All Filters</a>
                            <?php endif; ?>
                        </div>
                    <?php endif; ?>
                    
                    <!-- Pagination -->
                    <?php if ($totalPages > 1): ?>
                        <nav aria-label="Job pagination">
                            <ul class="pagination justify-content-center">
                                <!-- Previous Button -->
                                <?php if ($page > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= buildUrl($page - 1, $city_filter, $role_filter) ?>">Previous</a>
                                    </li>
                                <?php endif; ?>

                                <?php
                                // Always show first page
                                if ($page > 2) {
                                    echo '<li class="page-item"><a class="page-link" href="' . buildUrl(1, $city_filter, $role_filter) . '">1</a></li>';
                                    if ($page > 3) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }
                                }

                                // Show only current-1, current, current+1
                                for ($i = max(1, $page - 1); $i <= min($totalPages, $page + 1); $i++): ?>
                                    <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                                        <a class="page-link" href="<?= buildUrl($i, $city_filter, $role_filter) ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor;

                                // Always show last page
                                if ($page < $totalPages - 1) {
                                    if ($page < $totalPages - 2) {
                                        echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                    }
                                    echo '<li class="page-item"><a class="page-link" href="' . buildUrl($totalPages, $city_filter, $role_filter) . '">' . $totalPages . '</a></li>';
                                }
                                ?>

                                <!-- Next Button -->
                                <?php if ($page < $totalPages): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="<?= buildUrl($page + 1, $city_filter, $role_filter) ?>">Next</a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Right Sidebar -->
            <?php include('right_sidebar.php'); ?>
        </div>
    </div>

    <?php include('footer.php'); ?>
    
    <script>
        function applyFilters() {
            const cityFilter = document.getElementById('cityFilter').value;
            const roleFilter = document.getElementById('roleFilter').value;
            
            // Build URL with parameters
            let url = window.location.pathname;
            let params = new URLSearchParams();
            
            if (cityFilter) {
                params.append('city', cityFilter);
            }
            if (roleFilter) {
                params.append('role', roleFilter);
            }
            
            // Always go to page 1 when filters change
            params.append('page', '1');
            
            // Reload page with filters
            if (params.toString()) {
                window.location.href = url + '?' + params.toString();
            } else {
                window.location.href = url;
            }
        }
        
        function filterByRole(role) {
            const cityFilter = document.getElementById('cityFilter').value;
            
            let url = window.location.pathname;
            let params = new URLSearchParams();
            
            if (cityFilter) {
                params.append('city', cityFilter);
            }
            params.append('role', role);
            params.append('page', '1');
            
            window.location.href = url + '?' + params.toString();
        }
        
        // Update dropdown selections based on URL parameters on page load
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            const cityParam = urlParams.get('city');
            const roleParam = urlParams.get('role');
            
            if (cityParam) {
                document.getElementById('cityFilter').value = cityParam;
            }
            if (roleParam) {
                document.getElementById('roleFilter').value = roleParam;
            }
        });
    </script>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
$conn->close();
?>