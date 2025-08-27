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
    <h2 class="text-center mb-4">Data Migration Course Content</h2>

    <div class="accordion" id="dataMigrationAccordion">

      <!-- Module 1 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="migrationModule1Heading">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#migrationModule1" aria-expanded="true" aria-controls="migrationModule1">
            Module 1: Introduction to Data Migration
          </button>
        </h2>
        <div id="migrationModule1" class="accordion-collapse collapse show" aria-labelledby="migrationModule1Heading" data-bs-parent="#dataMigrationAccordion">
          <div class="accordion-body">
            <ul>
              <li>What is Data Migration?</li>
              <li>Types of Data Migration (Storage, Database, Application)</li>
              <li>Common Challenges in Data Migration</li>
              <li>Phases of a Migration Project</li>
              <li>Tools and Technologies Overview</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 2 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="migrationModule2Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#migrationModule2" aria-expanded="false" aria-controls="migrationModule2">
            Module 2: Planning and Strategy
          </button>
        </h2>
        <div id="migrationModule2" class="accordion-collapse collapse" aria-labelledby="migrationModule2Heading" data-bs-parent="#dataMigrationAccordion">
          <div class="accordion-body">
            <ul>
              <li>Requirement Gathering & Data Assessment</li>
              <li>Risk Analysis & Mitigation</li>
              <li>Migration Strategy: Big Bang vs Incremental</li>
              <li>Data Mapping and Transformation Rules</li>
              <li>Stakeholder Management</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 3 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="migrationModule3Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#migrationModule3" aria-expanded="false" aria-controls="migrationModule3">
            Module 3: Tools and Technologies
          </button>
        </h2>
        <div id="migrationModule3" class="accordion-collapse collapse" aria-labelledby="migrationModule3Heading" data-bs-parent="#dataMigrationAccordion">
          <div class="accordion-body">
            <ul>
              <li>Popular Tools: AWS DMS, Azure Data Factory, Talend</li>
              <li>Database-Specific Tools (Oracle, SQL Server, MySQL)</li>
              <li>Custom Scripting with Python & Shell</li>
              <li>Data Integration Platforms</li>
              <li>APIs for Data Transfer</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 4 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="migrationModule4Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#migrationModule4" aria-expanded="false" aria-controls="migrationModule4">
            Module 4: Data Quality and Validation
          </button>
        </h2>
        <div id="migrationModule4" class="accordion-collapse collapse" aria-labelledby="migrationModule4Heading" data-bs-parent="#dataMigrationAccordion">
          <div class="accordion-body">
            <ul>
              <li>Data Profiling Techniques</li>
              <li>Pre-Migration and Post-Migration Testing</li>
              <li>Data Reconciliation & Auditing</li>
              <li>Error Handling and Logging</li>
              <li>Automated Validation Scripts</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 5 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="migrationModule5Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#migrationModule5" aria-expanded="false" aria-controls="migrationModule5">
            Module 5: Execution & Post-Migration Activities
          </button>
        </h2>
        <div id="migrationModule5" class="accordion-collapse collapse" aria-labelledby="migrationModule5Heading" data-bs-parent="#dataMigrationAccordion">
          <div class="accordion-body">
            <ul>
              <li>Migration Execution Steps</li>
              <li>Downtime Management</li>
              <li>Roll-back Planning</li>
              <li>User Acceptance Testing (UAT)</li>
              <li>Monitoring & Support After Migration</li>
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
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
