<?php include('header.php'); ?>
<?php include('navbar.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Product Engineering Course</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/user_styles.css">
</head>
<body>

<div class="container my-5 d-flex justify-content-center">
  <div class="center-accordion">
    <h2 class="text-center mb-4">Product Engineering Course Content</h2>

    <div class="accordion" id="productEngineeringAccordion">

      <!-- Module 1 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="productModule1Heading">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#productModule1" aria-expanded="true" aria-controls="productModule1">
            Module 1: Fundamentals of Product Engineering
          </button>
        </h2>
        <div id="productModule1" class="accordion-collapse collapse show" aria-labelledby="productModule1Heading" data-bs-parent="#productEngineeringAccordion">
          <div class="accordion-body">
            <ul>
              <li>What is Product Engineering?</li>
              <li>Product Lifecycle Overview</li>
              <li>Key Roles & Responsibilities</li>
              <li>Market-Driven vs. Custom Product Development</li>
              <li>Trends in Product Engineering</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 2 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="productModule2Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#productModule2" aria-expanded="false" aria-controls="productModule2">
            Module 2: Product Design & Architecture
          </button>
        </h2>
        <div id="productModule2" class="accordion-collapse collapse" aria-labelledby="productModule2Heading" data-bs-parent="#productEngineeringAccordion">
          <div class="accordion-body">
            <ul>
              <li>Design Thinking Principles</li>
              <li>Prototyping and Wireframing</li>
              <li>Architecture Patterns</li>
              <li>Security and Scalability Considerations</li>
              <li>Performance Optimization Strategies</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 3 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="productModule3Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#productModule3" aria-expanded="false" aria-controls="productModule3">
            Module 3: Development & Testing
          </button>
        </h2>
        <div id="productModule3" class="accordion-collapse collapse" aria-labelledby="productModule3Heading" data-bs-parent="#productEngineeringAccordion">
          <div class="accordion-body">
            <ul>
              <li>Agile & DevOps in Product Engineering</li>
              <li>Version Control & Code Collaboration</li>
              <li>Unit Testing & Integration Testing</li>
              <li>Automated Testing Frameworks</li>
              <li>QA Strategies & CI/CD Pipelines</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 4 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="productModule4Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#productModule4" aria-expanded="false" aria-controls="productModule4">
            Module 4: Product Deployment & Monitoring
          </button>
        </h2>
        <div id="productModule4" class="accordion-collapse collapse" aria-labelledby="productModule4Heading" data-bs-parent="#productEngineeringAccordion">
          <div class="accordion-body">
            <ul>
              <li>Deployment Strategies (Blue-Green, Canary)</li>
              <li>Cloud-based Deployments (AWS, Azure, GCP)</li>
              <li>Logging and Monitoring Tools</li>
              <li>Performance Metrics and KPIs</li>
              <li>Incident Management and Rollbacks</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 5 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="productModule5Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#productModule5" aria-expanded="false" aria-controls="productModule5">
            Module 5: Continuous Innovation & Maintenance
          </button>
        </h2>
        <div id="productModule5" class="accordion-collapse collapse" aria-labelledby="productModule5Heading" data-bs-parent="#productEngineeringAccordion">
          <div class="accordion-body">
            <ul>
              <li>Gathering User Feedback</li>
              <li>Product Enhancements & Feature Updates</li>
              <li>Technical Debt Management</li>
              <li>Support & Maintenance Models</li>
              <li>Future Proofing Products</li>
            </ul>
          </div>
        </div>
      </div>

    </div> <!-- /.accordion -->
  </div>
</div>

<?php include('footer.php'); ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
