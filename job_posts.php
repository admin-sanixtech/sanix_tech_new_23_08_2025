<?php
include 'db_connection.php';

// Pagination settings
$limit = 3; // jobs per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

// Count total job posts
$countQuery = "SELECT COUNT(*) AS total FROM job_post WHERE is_approved = 1";
$countResult = $conn->query($countQuery);
$totalPosts = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalPosts / $limit);

// Fetch paginated job posts
$stmt = $conn->prepare("SELECT * FROM job_post WHERE is_approved = 1 ORDER BY created_at DESC LIMIT ?, ?");
$stmt->bind_param("ii", $offset, $limit);
$stmt->execute();
$result = $stmt->get_result();
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

    <?php include 'navbar.php'; ?> <!-- includew your common navbar -->

<div class="container-fluid my-5 px-0">
  <div class="row gx-0">
     <!-- Sidebar Accordion -->
    <aside class="col-md-3 px-3" > 
        <div class="accordion" id="uiuxCourse">

            <!-- Module 1: Introduction to UI/UX -->
            <div class="accordion-item">
                <h2 class="accordion-header" id="introHeading">
                    <button class="accordion-button" type="button">
                        data related
                    </button>
                </h2>
                <div id="introModule" class="accordion-collapse collapse show" aria-labelledby="introHeading" data-bs-parent="#uiuxCourse">
                    <div class="accordion-body">
                        <ul>
                            <li>Data Engineer</li>
                            <li>Data Scientist</li>
                            <li>Data Analyst</li>
                            <li>Data Architect</li>
                            <li>Data Manager</li>
                        </ul>
                    </div>
                </div>
            </div>
            </aside>
        <div class="col-md-7" style="border-right: 2px solid #ccc">
         <div id="html-description" class="p-3" style="background-color: #85bdc6ff">
               <h3>Job Posts</h3>
                <?php while ($job = $result->fetch_assoc()): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5><?= htmlspecialchars($job['title']) ?> (<?= htmlspecialchars($job['role']) ?>)</h5>
                            <p><?= nl2br(htmlspecialchars($job['description'])) ?></p>
                            <p><b>Location:</b> <?= htmlspecialchars($job['location']) ?></p>
                            <p><b>Email:</b> <?= htmlspecialchars($job['email_to']) ?></p>
                            <div class="alert alert-light p-2">
                                <p class="text-muted mb-0"><small>Posted on <?= $job['created_at'] ?></small></p>
                            </div>
                        </div>
                    </div>
                    <?php endwhile; ?>
                    </div>

            </div>
              <?php include('right_sidebar.php'); ?>

<nav>
    <ul class="pagination justify-content-center">
        <!-- Previous Button -->
        <?php if ($page > 1): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a>
            </li>
        <?php endif; ?>

        <?php
        // Always show first page
        if ($page > 2) {
            echo '<li class="page-item"><a class="page-link" href="?page=1">1</a></li>';
            if ($page > 3) {
                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }

        // Show only current-1, current, current+1
        for ($i = max(1, $page - 1); $i <= min($totalPages, $page + 1); $i++): ?>
            <li class="page-item <?php if ($i == $page) echo 'active'; ?>">
                <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
            </li>
        <?php endfor;

        // Always show last page
        if ($page < $totalPages - 1) {
            if ($page < $totalPages - 2) {
                echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
            echo '<li class="page-item"><a class="page-link" href="?page=' . $totalPages . '">' . $totalPages . '</a></li>';
        }
        ?>

        <!-- Next Button -->
        <?php if ($page < $totalPages): ?>
            <li class="page-item">
                <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
            </li>
        <?php endif; ?>
    </ul>
</nav>

        </div>
    </div>
</div>

<?php include('footer.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
