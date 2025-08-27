<?php include('header.php'); ?>
<?php include('navbar.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Professional Consulting Services</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/user_styles.css">
</head>
<body>

<div class="container my-5 d-flex justify-content-center">
  <div class="center-accordion">
    <h2 class="text-center mb-4">Professional Consulting Services</h2>

    <div class="accordion" id="consultingAccordion">

      <!-- Module 1 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="consultingModule1Heading">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#consultingModule1" aria-expanded="true" aria-controls="consultingModule1">
            Module 1: Overview of Professional Consulting
          </button>
        </h2>
        <div id="consultingModule1" class="accordion-collapse collapse show" aria-labelledby="consultingModule1Heading" data-bs-parent="#consultingAccordion">
          <div class="accordion-body">
            <ul>
              <li>What is Professional Consulting?</li>
              <li>Types of Consulting Services</li>
              <li>Key Skills and Competencies</li>
              <li>Consulting Lifecycle</li>
              <li>Client Engagement Models</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 2 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="consultingModule2Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#consultingModule2" aria-expanded="false" aria-controls="consultingModule2">
            Module 2: Business Strategy & Advisory
          </button>
        </h2>
        <div id="consultingModule2" class="accordion-collapse collapse" aria-labelledby="consultingModule2Heading" data-bs-parent="#consultingAccordion">
          <div class="accordion-body">
            <ul>
              <li>Strategic Planning & Execution</li>
              <li>Market & Competitive Analysis</li>
              <li>Organizational Development</li>
              <li>Change Management</li>
              <li>Operational Efficiency</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 3 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="consultingModule3Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#consultingModule3" aria-expanded="false" aria-controls="consultingModule3">
            Module 3: Technology & Digital Transformation
          </button>
        </h2>
        <div id="consultingModule3" class="accordion-collapse collapse" aria-labelledby="consultingModule3Heading" data-bs-parent="#consultingAccordion">
          <div class="accordion-body">
            <ul>
              <li>Digital Readiness Assessment</li>
              <li>IT Strategy & Roadmap</li>
              <li>Cloud & Infrastructure Consulting</li>
              <li>Automation & RPA</li>
              <li>Cybersecurity Advisory</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 4 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="consultingModule4Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#consultingModule4" aria-expanded="false" aria-controls="consultingModule4">
            Module 4: Financial & Risk Consulting
          </button>
        </h2>
        <div id="consultingModule4" class="accordion-collapse collapse" aria-labelledby="consultingModule4Heading" data-bs-parent="#consultingAccordion">
          <div class="accordion-body">
            <ul>
              <li>Financial Analysis & Forecasting</li>
              <li>Regulatory Compliance</li>
              <li>Risk Management Strategies</li>
              <li>Internal Controls</li>
              <li>Cost Optimization</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 5 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="consultingModule5Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#consultingModule5" aria-expanded="false" aria-controls="consultingModule5">
            Module 5: Industry-Specific Consulting
          </button>
        </h2>
        <div id="consultingModule5" class="accordion-collapse collapse" aria-labelledby="consultingModule5Heading" data-bs-parent="#consultingAccordion">
          <div class="accordion-body">
            <ul>
              <li>Healthcare Consulting</li>
              <li>Retail & eCommerce</li>
              <li>Manufacturing & Logistics</li>
              <li>Banking & Financial Services</li>
              <li>Government & Public Sector</li>
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
