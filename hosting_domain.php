<?php include('header.php'); ?>
<?php include('navbar.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Hosting & Domain Course</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/user_styles.css">
</head>
<body>

<div class="container my-5 d-flex justify-content-center">
  <div class="center-accordion">
    <h2 class="text-center mb-4">Hosting & Domain Course Content</h2>

    <div class="accordion" id="hostingDomainAccordion">

      <!-- Module 1 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="hdModule1Heading">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#hdModule1" aria-expanded="true" aria-controls="hdModule1">
            Module 1: Introduction to Domain & Hosting
          </button>
        </h2>
        <div id="hdModule1" class="accordion-collapse collapse show" aria-labelledby="hdModule1Heading" data-bs-parent="#hostingDomainAccordion">
          <div class="accordion-body">
            <ul>
              <li>What is a Domain Name?</li>
              <li>What is Web Hosting?</li>
              <li>Types of Hosting (Shared, VPS, Dedicated, Cloud)</li>
              <li>How Domains and Hosting Work Together</li>
              <li>Top Domain Registrars & Hosting Providers</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 2 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="hdModule2Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#hdModule2" aria-expanded="false" aria-controls="hdModule2">
            Module 2: Domain Name Management
          </button>
        </h2>
        <div id="hdModule2" class="accordion-collapse collapse" aria-labelledby="hdModule2Heading" data-bs-parent="#hostingDomainAccordion">
          <div class="accordion-body">
            <ul>
              <li>Domain Registration Process</li>
              <li>ICANN & WHOIS Lookup</li>
              <li>Domain Forwarding & Masking</li>
              <li>DNS Records (A, CNAME, MX, TXT)</li>
              <li>Domain Privacy Protection</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 3 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="hdModule3Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#hdModule3" aria-expanded="false" aria-controls="hdModule3">
            Module 3: Hosting Control Panels
          </button>
        </h2>
        <div id="hdModule3" class="accordion-collapse collapse" aria-labelledby="hdModule3Heading" data-bs-parent="#hostingDomainAccordion">
          <div class="accordion-body">
            <ul>
              <li>Introduction to cPanel / Plesk</li>
              <li>Email Account Setup</li>
              <li>FTP Account Creation</li>
              <li>File Manager & Backup Tools</li>
              <li>Security Settings (SSL, Hotlink Protection)</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 4 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="hdModule4Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#hdModule4" aria-expanded="false" aria-controls="hdModule4">
            Module 4: Hosting & Domain Troubleshooting
          </button>
        </h2>
        <div id="hdModule4" class="accordion-collapse collapse" aria-labelledby="hdModule4Heading" data-bs-parent="#hostingDomainAccordion">
          <div class="accordion-body">
            <ul>
              <li>Common DNS Issues</li>
              <li>Fixing Website Downtime</li>
              <li>Nameserver Configuration</li>
              <li>Migration Between Hosts</li>
              <li>Renewal and Expiration Management</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 5 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="hdModule5Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#hdModule5" aria-expanded="false" aria-controls="hdModule5">
            Module 5: Advanced Hosting Concepts
          </button>
        </h2>
        <div id="hdModule5" class="accordion-collapse collapse" aria-labelledby="hdModule5Heading" data-bs-parent="#hostingDomainAccordion">
          <div class="accordion-body">
            <ul>
              <li>Cloud Hosting & CDN Integration</li>
              <li>WordPress Hosting & Auto Installers</li>
              <li>Performance Optimization (Caching, Gzip)</li>
              <li>SSL Certificates & HTTPS Setup</li>
              <li>Server-side Security Best Practices</li>
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
