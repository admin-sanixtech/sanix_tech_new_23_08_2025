<?php
include 'db_connection.php';
$result = $conn->query("SELECT * FROM job_post WHERE is_approved = 1 ORDER BY created_at DESC");

// Pagination settings
$postsPerPage = 3; // adjust as needed
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $postsPerPage;

// Count total job posts
$countQuery = "SELECT COUNT(*) AS total FROM sanixazs_main_db.job_post WHERE is_approved = 1";
$countResult = $conn->query($countQuery);
$totalPosts = $countResult->fetch_assoc()['total'];
$totalPages = ceil($totalPosts / $postsPerPage);

// Fetch paginated job posts
$jobsQuery = "SELECT * FROM sanixazs_main_db.job_post WHERE is_approved = 1 ORDER BY created_at DESC LIMIT ?, ?";
$stmt = $conn->prepare($jobsQuery);
$stmt->bind_param("ii", $offset, $postsPerPage);
$stmt->execute();
$jobsResult = $stmt->get_result();
?>



<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sanix Technologies</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/user_styles.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
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
   

   <div class="row">
         <div class="col-md-10" style="border-right: 2px solid #ccc">
              <div id="html-description" class="p-3" style="background-color: #85bdc6ff">
                    <h3>Job Posts</h3>
                    <?php while ($job = $jobsResult->fetch_assoc()) { ?>
                         <div class="card mb-3">
                             <div class="card-body">
                                 <h5><?= htmlspecialchars($job['title']) ?> (<?= htmlspecialchars($job['role']) ?>)</h5>
                                 <p><?= nl2br(htmlspecialchars($job['description'])) ?></p>
                                 <p><b>Location:</b> <?= htmlspecialchars($job['location']) ?> | <b>Email:</b> <?= htmlspecialchars($job['email_to']) ?></p>
                                 <p class="text-muted"><small>Posted on <?= $job['created_at'] ?></small></p>
                             </div>
                         </div>
                    <?php } ?>
                </div>
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

</body>
</html>

<!--<?php
// Include DB if needed
include_once 'db_connection.php';

// Example query – adjust to match your table schema
$sql = "SELECT * FROM sanixazs_main_db.jobs ORDER BY posted_date DESC LIMIT 10";
$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    while ($job = $result->fetch_assoc()) {
        echo "<div class='card mb-3 p-3'>";
        echo "<h5>" . htmlspecialchars($job['title']) . "</h5>";
        echo "<p class='text-muted'>" . htmlspecialchars($job['location']) . "</p>";
        echo "</div>";
    }
} else {
    echo "<p>No job posts found.</p>";
}
?> --> 