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
    <h2 class="text-center mb-4">Custom Solutions Course Content</h2>

    <div class="accordion" id="customSolutionsAccordion">

      <!-- Module 1 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="customModule1Heading">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#customModule1" aria-expanded="true" aria-controls="customModule1">
            Module 1: Understanding Client Requirements
          </button>
        </h2>
        <div id="customModule1" class="accordion-collapse collapse show" aria-labelledby="customModule1Heading" data-bs-parent="#customSolutionsAccordion">
          <div class="accordion-body">
            <ul>
              <li>Business Requirement Gathering</li>
              <li>Client Interviews & Use Cases</li>
              <li>Problem Framing and Scoping</li>
              <li>Functional vs Non-functional Requirements</li>
              <li>Creating Requirement Documentation</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 2 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="customModule2Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#customModule2" aria-expanded="false" aria-controls="customModule2">
            Module 2: Designing Tailored Solutions
          </button>
        </h2>
        <div id="customModule2" class="accordion-collapse collapse" aria-labelledby="customModule2Heading" data-bs-parent="#customSolutionsAccordion">
          <div class="accordion-body">
            <ul>
              <li>Solution Architecture Overview</li>
              <li>Wireframing and Prototyping</li>
              <li>Technology Selection Criteria</li>
              <li>Custom Workflows and Integrations</li>
              <li>Scalability & Flexibility Considerations</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 3 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="customModule3Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#customModule3" aria-expanded="false" aria-controls="customModule3">
            Module 3: Development and Implementation
          </button>
        </h2>
        <div id="customModule3" class="accordion-collapse collapse" aria-labelledby="customModule3Heading" data-bs-parent="#customSolutionsAccordion">
          <div class="accordion-body">
            <ul>
              <li>Agile Development Practices</li>
              <li>Version Control and CI/CD</li>
              <li>Code Reviews & Quality Assurance</li>
              <li>Integration with Third-party APIs</li>
              <li>Environment Setup & Deployment</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 4 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="customModule4Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#customModule4" aria-expanded="false" aria-controls="customModule4">
            Module 4: Testing & User Feedback
          </button>
        </h2>
        <div id="customModule4" class="accordion-collapse collapse" aria-labelledby="customModule4Heading" data-bs-parent="#customSolutionsAccordion">
          <div class="accordion-body">
            <ul>
              <li>Functional Testing</li>
              <li>User Acceptance Testing (UAT)</li>
              <li>Bug Tracking & Fixing</li>
              <li>Feedback Loop with Stakeholders</li>
              <li>Iterative Improvement</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 5 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="customModule5Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#customModule5" aria-expanded="false" aria-controls="customModule5">
            Module 5: Delivery & Maintenance
          </button>
        </h2>
        <div id="customModule5" class="accordion-collapse collapse" aria-labelledby="customModule5Heading" data-bs-parent="#customSolutionsAccordion">
          <div class="accordion-body">
            <ul>
              <li>Final Delivery & Handover</li>
              <li>User Training & Documentation</li>
              <li>Post-deployment Support</li>
              <li>Monitoring and SLA Management</li>
              <li>Future Enhancements Planning</li>
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
