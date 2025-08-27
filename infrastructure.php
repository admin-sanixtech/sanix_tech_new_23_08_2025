<?php include('header.php'); ?>
<?php include('navbar.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>IT Infrastructure Services</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/user_styles.css">
</head>
<body>

<div class="container my-5 d-flex justify-content-center">
  <div class="center-accordion">
    <h2 class="text-center mb-4">IT Infrastructure Services</h2>

    <div class="accordion" id="infrastructureAccordion">

      <!-- Module 1 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="infraModule1Heading">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#infraModule1" aria-expanded="true" aria-controls="infraModule1">
            Module 1: Introduction to IT Infrastructure
          </button>
        </h2>
        <div id="infraModule1" class="accordion-collapse collapse show" aria-labelledby="infraModule1Heading" data-bs-parent="#infrastructureAccordion">
          <div class="accordion-body">
            <ul>
              <li>What is IT Infrastructure?</li>
              <li>Components: Hardware, Software, Network</li>
              <li>On-Prem vs Cloud Infrastructure</li>
              <li>Modern Infrastructure Trends</li>
              <li>Infrastructure Lifecycle Management</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 2 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="infraModule2Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#infraModule2" aria-expanded="false" aria-controls="infraModule2">
            Module 2: Network & Server Management
          </button>
        </h2>
        <div id="infraModule2" class="accordion-collapse collapse" aria-labelledby="infraModule2Heading" data-bs-parent="#infrastructureAccordion">
          <div class="accordion-body">
            <ul>
              <li>LAN, WAN, and Wireless Networks</li>
              <li>Switches, Routers, Firewalls</li>
              <li>Server Types & Configuration</li>
              <li>Storage Solutions: SAN & NAS</li>
              <li>Monitoring & Performance Tuning</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 3 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="infraModule3Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#infraModule3" aria-expanded="false" aria-controls="infraModule3">
            Module 3: Cloud Infrastructure
          </button>
        </h2>
        <div id="infraModule3" class="accordion-collapse collapse" aria-labelledby="infraModule3Heading" data-bs-parent="#infrastructureAccordion">
          <div class="accordion-body">
            <ul>
              <li>Cloud Models: IaaS, PaaS, SaaS</li>
              <li>Public vs Private vs Hybrid Cloud</li>
              <li>Leading Platforms: AWS, Azure, GCP</li>
              <li>Virtualization & Containers</li>
              <li>Cloud Security Essentials</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 4 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="infraModule4Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#infraModule4" aria-expanded="false" aria-controls="infraModule4">
            Module 4: Infrastructure Security & Compliance
          </button>
        </h2>
        <div id="infraModule4" class="accordion-collapse collapse" aria-labelledby="infraModule4Heading" data-bs-parent="#infrastructureAccordion">
          <div class="accordion-body">
            <ul>
              <li>Data Center Security Best Practices</li>
              <li>Endpoint Protection</li>
              <li>Backup and Disaster Recovery</li>
              <li>Compliance: ISO, SOC 2, NIST</li>
              <li>Patch & Configuration Management</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 5 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="infraModule5Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#infraModule5" aria-expanded="false" aria-controls="infraModule5">
            Module 5: Infrastructure Automation & Monitoring
          </button>
        </h2>
        <div id="infraModule5" class="accordion-collapse collapse" aria-labelledby="infraModule5Heading" data-bs-parent="#infrastructureAccordion">
          <div class="accordion-body">
            <ul>
              <li>Introduction to Infrastructure as Code (IaC)</li>
              <li>Tools: Terraform, Ansible, Puppet</li>
              <li>CI/CD Pipeline Integration</li>
              <li>Monitoring with Prometheus, Grafana, Nagios</li>
              <li>Incident Management & Alerting</li>
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
