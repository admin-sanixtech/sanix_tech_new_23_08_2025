<?php include('header.php'); ?>
<?php include('navbar.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Software Development</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/user_styles.css">
</head>
<body>

<div class="container my-5 d-flex justify-content-center">
  <div class="center-accordion">
    <h2 class="text-center mb-4">Software Development</h2>

    <div class="accordion" id="softwareDevAccordion">

      <!-- Module 1 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="sdModule1Heading">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#sdModule1" aria-expanded="true" aria-controls="sdModule1">
            Module 1: Software Development Life Cycle (SDLC)
          </button>
        </h2>
        <div id="sdModule1" class="accordion-collapse collapse show" aria-labelledby="sdModule1Heading" data-bs-parent="#softwareDevAccordion">
          <div class="accordion-body">
            <ul>
              <li>Introduction to SDLC Models</li>
              <li>Requirement Analysis</li>
              <li>Design & Architecture</li>
              <li>Development & Implementation</li>
              <li>Testing & Deployment</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 2 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="sdModule2Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sdModule2" aria-expanded="false" aria-controls="sdModule2">
            Module 2: Programming Fundamentals
          </button>
        </h2>
        <div id="sdModule2" class="accordion-collapse collapse" aria-labelledby="sdModule2Heading" data-bs-parent="#softwareDevAccordion">
          <div class="accordion-body">
            <ul>
              <li>Languages: Python, Java, C#</li>
              <li>OOP Concepts</li>
              <li>Data Structures & Algorithms</li>
              <li>Version Control (Git)</li>
              <li>Code Reviews & Best Practices</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 3 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="sdModule3Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sdModule3" aria-expanded="false" aria-controls="sdModule3">
            Module 3: Web & API Development
          </button>
        </h2>
        <div id="sdModule3" class="accordion-collapse collapse" aria-labelledby="sdModule3Heading" data-bs-parent="#softwareDevAccordion">
          <div class="accordion-body">
            <ul>
              <li>Frontend Technologies (HTML, CSS, JS, React)</li>
              <li>Backend Frameworks (Node.js, Django, Spring)</li>
              <li>RESTful API Design</li>
              <li>Authentication & Security</li>
              <li>Integration & Testing</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 4 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="sdModule4Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sdModule4" aria-expanded="false" aria-controls="sdModule4">
            Module 4: Software Testing & QA
          </button>
        </h2>
        <div id="sdModule4" class="accordion-collapse collapse" aria-labelledby="sdModule4Heading" data-bs-parent="#softwareDevAccordion">
          <div class="accordion-body">
            <ul>
              <li>Manual Testing Techniques</li>
              <li>Automation Tools (Selenium, JUnit)</li>
              <li>Test Case Writing</li>
              <li>Bug Tracking & Reporting</li>
              <li>Performance Testing</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 5 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="sdModule5Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#sdModule5" aria-expanded="false" aria-controls="sdModule5">
            Module 5: DevOps & Deployment
          </button>
        </h2>
        <div id="sdModule5" class="accordion-collapse collapse" aria-labelledby="sdModule5Heading" data-bs-parent="#softwareDevAccordion">
          <div class="accordion-body">
            <ul>
              <li>CI/CD Concepts</li>
              <li>Docker & Containerization</li>
              <li>Cloud Platforms (AWS, Azure)</li>
              <li>Monitoring & Logging</li>
              <li>Post-deployment Support</li>
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
