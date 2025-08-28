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
</head>
<body>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
       <?php include('header.php'); ?> <!-- Include your common header -->

      <?php include 'navbar.php'; ?> <!-- includew your common navbar -->

  <div class="container-fluid my-5 px-0">
  <div class="row gx-0">
     <!-- Sidebar Accordion -->
    <aside class="col-md-3 px-3"> 
    <h2 class="text-center mb-4">Data Engineering Course Content</h2>

    <div class="accordion" id="dataEngAccordion">

      <!-- Module 1 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="dataEngModule1Heading">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#dataEngModule1" aria-expanded="true" aria-controls="dataEngModule1">
            Module 1: Introduction to Data Engineering
          </button>
        </h2>
        <div id="dataEngModule1" class="accordion-collapse collapse show" aria-labelledby="dataEngModule1Heading" data-bs-parent="#dataEngAccordion">
          <div class="accordion-body">
            <ul>
              <li>What is Data Engineering?</li>
              <li>Role of a Data Engineer</li>
              <li>Data Engineering vs Data Science</li>
              <li>Tools & Technologies Overview</li>
              <li>Understanding Big Data Ecosystem</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 2 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="dataEngModule2Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#dataEngModule2" aria-expanded="false" aria-controls="dataEngModule2">
            Module 2: Data Warehousing Concepts
          </button>
        </h2>
        <div id="dataEngModule2" class="accordion-collapse collapse" aria-labelledby="dataEngModule2Heading" data-bs-parent="#dataEngAccordion">
          <div class="accordion-body">
            <ul>
              <li>ETL vs ELT</li>
              <li>Data Lakes vs Data Warehouses</li>
              <li>Star & Snowflake Schemas</li>
              <li>Data Modeling Basics</li>
              <li>Popular Tools (Redshift, BigQuery, Snowflake)</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 3 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="dataEngModule3Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#dataEngModule3" aria-expanded="false" aria-controls="dataEngModule3">
            Module 3: Data Pipelines & Workflow Orchestration
          </button>
        </h2>
        <div id="dataEngModule3" class="accordion-collapse collapse" aria-labelledby="dataEngModule3Heading" data-bs-parent="#dataEngAccordion">
          <div class="accordion-body">
            <ul>
              <li>Building ETL Pipelines</li>
              <li>Batch vs Streaming Data</li>
              <li>Apache Airflow Basics</li>
              <li>Apache Kafka for Data Streaming</li>
              <li>Workflow Scheduling & Monitoring</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 4 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="dataEngModule4Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#dataEngModule4" aria-expanded="false" aria-controls="dataEngModule4">
            Module 4: Working with Big Data Tools
          </button>
        </h2>
        <div id="dataEngModule4" class="accordion-collapse collapse" aria-labelledby="dataEngModule4Heading" data-bs-parent="#dataEngAccordion">
          <div class="accordion-body">
            <ul>
              <li>Introduction to Hadoop Ecosystem</li>
              <li>Apache Spark Overview</li>
              <li>HDFS & Data Storage</li>
              <li>Data Processing with PySpark</li>
              <li>Cluster Computing Concepts</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 5 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="dataEngModule5Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#dataEngModule5" aria-expanded="false" aria-controls="dataEngModule5">
            Module 5: Cloud Data Engineering
          </button>
        </h2>
        <div id="dataEngModule5" class="accordion-collapse collapse" aria-labelledby="dataEngModule5Heading" data-bs-parent="#dataEngAccordion">
          <div class="accordion-body">
            <ul>
              <li>Cloud Platforms: AWS, Azure, GCP</li>
              <li>Using Cloud Storage & Databases</li>
              <li>Serverless Data Processing (Lambda, Cloud Functions)</li>
              <li>Cloud-native Data Pipelines</li>
              <li>Security & Compliance</li>
            </ul>
          </div>
        </div>
      </div>

    </div> <!-- /.accordion -->
    </aside>
  </div>
</div>
<script>
 // Handle list item click
  document.querySelectorAll('.accordion-body li').forEach(item => {
    item.style.cursor = 'pointer';
    item.addEventListener('click', function () {
      const text = this.textContent.trim();
      const content = contentMap[text] || `<h4>${text}</h4><p>Details coming soon...</p>`;
      document.getElementById('contentDisplay').innerHTML = content;

      // Optional: Visually highlight the active item
      document.querySelectorAll('.accordion-body li').forEach(li => li.classList.remove('active'));
      this.classList.add('active');
    });
  });
</script>

<?php include('footer.php'); ?>
</body>
</html>


