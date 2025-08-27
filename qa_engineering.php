<?php include('header.php'); ?>
<?php include('navbar.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>QA Engineering Course</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/user_styles.css">
</head>
<body>

<div class="container my-5 d-flex justify-content-center">
  <div class="center-accordion">
    <h2 class="text-center mb-4">QA Engineering Course Content</h2>

    <div class="accordion" id="qaEngineeringAccordion">

      <!-- Module 1 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="qaModule1Heading">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#qaModule1" aria-expanded="true" aria-controls="qaModule1">
            Module 1: Introduction to QA Engineering
          </button>
        </h2>
        <div id="qaModule1" class="accordion-collapse collapse show" aria-labelledby="qaModule1Heading" data-bs-parent="#qaEngineeringAccordion">
          <div class="accordion-body">
            <ul>
              <li>Role of QA in Software Development</li>
              <li>Types of Testing: Manual vs Automation</li>
              <li>QA Life Cycle & SDLC Models</li>
              <li>Understanding Requirements & Test Planning</li>
              <li>Key QA Tools Overview</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 2 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="qaModule2Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#qaModule2" aria-expanded="false" aria-controls="qaModule2">
            Module 2: Manual Testing Fundamentals
          </button>
        </h2>
        <div id="qaModule2" class="accordion-collapse collapse" aria-labelledby="qaModule2Heading" data-bs-parent="#qaEngineeringAccordion">
          <div class="accordion-body">
            <ul>
              <li>Test Case Design Techniques</li>
              <li>Test Plan & Test Suite Creation</li>
              <li>Defect Life Cycle & Bug Reporting</li>
              <li>Regression, Smoke & Sanity Testing</li>
              <li>Tools: JIRA, TestRail, Bugzilla</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 3 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="qaModule3Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#qaModule3" aria-expanded="false" aria-controls="qaModule3">
            Module 3: Automation Testing
          </button>
        </h2>
        <div id="qaModule3" class="accordion-collapse collapse" aria-labelledby="qaModule3Heading" data-bs-parent="#qaEngineeringAccordion">
          <div class="accordion-body">
            <ul>
              <li>Automation Concepts and Benefits</li>
              <li>Selenium WebDriver Basics</li>
              <li>Writing Test Scripts with Java/Python</li>
              <li>Introduction to TestNG/JUnit</li>
              <li>Automation Framework Design</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 4 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="qaModule4Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#qaModule4" aria-expanded="false" aria-controls="qaModule4">
            Module 4: API and Performance Testing
          </button>
        </h2>
        <div id="qaModule4" class="accordion-collapse collapse" aria-labelledby="qaModule4Heading" data-bs-parent="#qaEngineeringAccordion">
          <div class="accordion-body">
            <ul>
              <li>Introduction to API Testing</li>
              <li>Postman & REST Assured Basics</li>
              <li>Performance Testing with JMeter</li>
              <li>Analyzing Results and Reporting</li>
              <li>Load Testing Strategies</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 5 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="qaModule5Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#qaModule5" aria-expanded="false" aria-controls="qaModule5">
            Module 5: CI/CD & QA in DevOps
          </button>
        </h2>
        <div id="qaModule5" class="accordion-collapse collapse" aria-labelledby="qaModule5Heading" data-bs-parent="#qaEngineeringAccordion">
          <div class="accordion-body">
            <ul>
              <li>Introduction to CI/CD in QA</li>
              <li>Integrating Tests in Jenkins/GitHub Actions</li>
              <li>Reporting & Notification Automation</li>
              <li>QA Metrics & Dashboarding</li>
              <li>Real-Time Monitoring Tools</li>
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
