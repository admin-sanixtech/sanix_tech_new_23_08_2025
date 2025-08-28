<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require 'db_connection.php'; // adjust path

// Example: fetch only Digital Marketing posts
// Inside c_course.php
$category_id = 20; 

$sql = "SELECT * FROM posts WHERE category_id = $category_id";

/*$sql = "SELECT * FROM posts WHERE category_id = 20"; */

$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error);
}
$firstRow = null;
if ($result && $result->num_rows > 0) {
    $result->data_seek(0);
    $firstRow = $result->fetch_assoc();
    $result->data_seek(0); // reset pointer for loop
}

?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sanix Technologies</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">
<link rel="stylesheet" href="css/user_styles.css?v=<?php echo time(); ?>">

    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css"/>
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>
<body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
       <?php include('header.php'); ?> <!-- Include your common header -->

      <?php include 'navbar.php'; ?> <!-- includew your common navbar -->

<div class="container-fluid my-5 px-0">
  <div class="row gx-0">
     <!-- Sidebar Accordion -->
    <aside class="col-md-3 px-0" > 
    <h4 class="text-center mb-4">C course content</h4>

   <div class="accordion" id="sqlAccordion">
           <div class="accordion-body">
                <ul>
                    <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                      <li class="topic-link" data-title="<?php echo htmlspecialchars($row['title']); ?>">
                    <?php echo htmlspecialchars($row['title']); ?>
                      </li>
                    
                    
                    <?php endwhile; ?>
                      <?php else: ?>
                          <li>No topics found</li>
                      <?php endif; ?>
                </ul>

          </div>
 
    </aside>
<div class="col-md-7" style="border-right: 2px solid #ccc">
         <div id="html-description" class="p-3" style="background-color: #85bdc6ff">
  <?php if ($firstRow): ?>
      <h3><?php echo htmlspecialchars($firstRow['title']); ?></h3>
      <p><?php echo htmlspecialchars($firstRow['description']); ?></p>
  <?php else: ?>
      <h3 class="heading">Select a topic to view details here</h3>
  <?php endif; ?>
</div>


            </div>
              <?php include('right_sidebar.php'); ?>

  </div>
</div>
<?php include('footer.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function() {
    const items = document.querySelectorAll(".topic-link");
    const descriptionBox = document.getElementById("html-description");

    // Attach click listeners to all list items
    items.forEach(function(item) {
        item.addEventListener("click", function() {
            let title = this.getAttribute("data-title");

            fetch("course_description.php?title=" + encodeURIComponent(title))
                .then(response => response.text())
                .then(data => {
                    descriptionBox.innerHTML = data;

                    // Remove active class from all
                    items.forEach(li => li.classList.remove("active-topic"));

                    // Highlight clicked one
                    this.classList.add("active-topic");
                })
             
        });
    });

    
});
</script>


</body>
</html>
