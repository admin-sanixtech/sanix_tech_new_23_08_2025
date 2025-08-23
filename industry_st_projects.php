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

<div class="container my-5 d-flex justify-content-center">
  <div class="center-accordion">
    <h2 class="text-center mb-4">Industry Standard Projects</h2>

    <div class="accordion" id="industryProjectAccordion">

      <!-- Module 1 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="industryModule1Heading">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#industryModule1" aria-expanded="true" aria-controls="industryModule1">
            Module 1: Project Planning & Requirements
          </button>
        </h2>
        <div id="industryModule1" class="accordion-collapse collapse show" aria-labelledby="industryModule1Heading" data-bs-parent="#industryProjectAccordion">
          <div class="accordion-body">
            <ul>
              <li>Client Requirement Analysis</li>
              <li>Project Scope & Timeline Definition</li>
              <li>Technical Specification Documentation</li>
              <li>Agile/Scrum Planning Sessions</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 2 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="industryModule2Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#industryModule2" aria-expanded="false" aria-controls="industryModule2">
            Module 2: Design & Architecture
          </button>
        </h2>
        <div id="industryModule2" class="accordion-collapse collapse" aria-labelledby="industryModule2Heading" data-bs-parent="#industryProjectAccordion">
          <div class="accordion-body">
            <ul>
              <li>UI/UX Prototyping</li>
              <li>System Architecture & Tech Stack Selection</li>
              <li>Database Design & API Planning</li>
              <li>Design Review & Approval</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 3 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="industryModule3Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#industryModule3" aria-expanded="false" aria-controls="industryModule3">
            Module 3: Development & Integration
          </button>
        </h2>
        <div id="industryModule3" class="accordion-collapse collapse" aria-labelledby="industryModule3Heading" data-bs-parent="#industryProjectAccordion">
          <div class="accordion-body">
            <ul>
              <li>Frontend & Backend Coding</li>
              <li>API Development & Integration</li>
              <li>Unit Testing & Version Control</li>
              <li>CI/CD Pipeline Setup</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 4 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="industryModule4Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#industryModule4" aria-expanded="false" aria-controls="industryModule4">
            Module 4: Testing & Deployment
          </button>
        </h2>
        <div id="industryModule4" class="accordion-collapse collapse" aria-labelledby="industryModule4Heading" data-bs-parent="#industryProjectAccordion">
          <div class="accordion-body">
            <ul>
              <li>Functional & Performance Testing</li>
              <li>Security Audit & Bug Fixing</li>
              <li>Staging & Production Deployment</li>
              <li>Client UAT & Final Approval</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 5 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="industryModule5Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#industryModule5" aria-expanded="false" aria-controls="industryModule5">
            Module 5: Technologies & Domains
          </button>
        </h2>
        <div id="industryModule5" class="accordion-collapse collapse" aria-labelledby="industryModule5Heading" data-bs-parent="#industryProjectAccordion">
          <div class="accordion-body">
            <ul>
              <li>Web & Mobile App Development</li>
              <li>Enterprise Software Solutions</li>
              <li>Cloud Computing & DevOps</li>
              <li>AI/ML, Cybersecurity & Blockchain</li>
            </ul>
          </div>
        </div>
      </div>

    </div> <!-- /.accordion -->
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
