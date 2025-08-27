<?php include('header.php'); ?>
<?php include('navbar.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Enterprise Integration Course</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/user_styles.css">
</head>
<body>

<div class="container my-5 d-flex justify-content-center">
  <div class="center-accordion">
    <h2 class="text-center mb-4">Enterprise Integration Course Content</h2>

    <div class="accordion" id="enterpriseAccordion">

      <!-- Module 1 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="enterpriseModule1Heading">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#enterpriseModule1" aria-expanded="true" aria-controls="enterpriseModule1">
            Module 1: Fundamentals of Enterprise Integration
          </button>
        </h2>
        <div id="enterpriseModule1" class="accordion-collapse collapse show" aria-labelledby="enterpriseModule1Heading" data-bs-parent="#enterpriseAccordion">
          <div class="accordion-body">
            <ul>
              <li>What is Enterprise Integration?</li>
              <li>Importance in Modern Organizations</li>
              <li>Integration Challenges & Benefits</li>
              <li>Monolithic vs Integrated Architectures</li>
              <li>Overview of Middleware and APIs</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 2 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="enterpriseModule2Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#enterpriseModule2" aria-expanded="false" aria-controls="enterpriseModule2">
            Module 2: Integration Technologies & Tools
          </button>
        </h2>
        <div id="enterpriseModule2" class="accordion-collapse collapse" aria-labelledby="enterpriseModule2Heading" data-bs-parent="#enterpriseAccordion">
          <div class="accordion-body">
            <ul>
              <li>Enterprise Service Bus (ESB)</li>
              <li>Message Brokers and Queues</li>
              <li>RESTful & SOAP Web Services</li>
              <li>Integration Platforms as a Service (iPaaS)</li>
              <li>Popular Tools: MuleSoft, Apache Camel, Dell Boomi</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 3 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="enterpriseModule3Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#enterpriseModule3" aria-expanded="false" aria-controls="enterpriseModule3">
            Module 3: API Management & Microservices
          </button>
        </h2>
        <div id="enterpriseModule3" class="accordion-collapse collapse" aria-labelledby="enterpriseModule3Heading" data-bs-parent="#enterpriseAccordion">
          <div class="accordion-body">
            <ul>
              <li>Introduction to APIs</li>
              <li>Designing and Securing APIs</li>
              <li>API Gateways & Lifecycle Management</li>
              <li>Microservices Architecture & Integration</li>
              <li>Service Mesh Concepts (Istio, Linkerd)</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 4 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="enterpriseModule4Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#enterpriseModule4" aria-expanded="false" aria-controls="enterpriseModule4">
            Module 4: Data & Application Integration
          </button>
        </h2>
        <div id="enterpriseModule4" class="accordion-collapse collapse" aria-labelledby="enterpriseModule4Heading" data-bs-parent="#enterpriseAccordion">
          <div class="accordion-body">
            <ul>
              <li>Data Synchronization & Replication</li>
              <li>Master Data Management (MDM)</li>
              <li>Application Integration Patterns</li>
              <li>Batch vs Real-Time Integration</li>
              <li>ETL/ELT Processes</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 5 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="enterpriseModule5Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#enterpriseModule5" aria-expanded="false" aria-controls="enterpriseModule5">
            Module 5: Security, Monitoring & Best Practices
          </button>
        </h2>
        <div id="enterpriseModule5" class="accordion-collapse collapse" aria-labelledby="enterpriseModule5Heading" data-bs-parent="#enterpriseAccordion">
          <div class="accordion-body">
            <ul>
              <li>Security in Integrated Systems</li>
              <li>Authentication & Authorization Mechanisms</li>
              <li>Logging, Auditing & Monitoring</li>
              <li>Scalability & Fault Tolerance</li>
              <li>Industry Best Practices & Standards</li>
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
