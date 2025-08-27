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
    <h2 class="text-center mb-4">Cybersecurity Course Content</h2>

    <div class="accordion" id="cybersecurityAccordion">

      <!-- Module 1 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="cyberModule1Heading">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#cyberModule1" aria-expanded="true" aria-controls="cyberModule1">
            Module 1: Introduction to Cybersecurity
          </button>
        </h2>
        <div id="cyberModule1" class="accordion-collapse collapse show" aria-labelledby="cyberModule1Heading" data-bs-parent="#cybersecurityAccordion">
          <div class="accordion-body">
            <ul>
              <li>What is Cybersecurity?</li>
              <li>Importance in the Digital Age</li>
              <li>Common Threats & Vulnerabilities</li>
              <li>Types of Cyber Attacks</li>
              <li>Overview of Cybersecurity Domains</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 2 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="cyberModule2Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#cyberModule2" aria-expanded="false" aria-controls="cyberModule2">
            Module 2: Network Security
          </button>
        </h2>
        <div id="cyberModule2" class="accordion-collapse collapse" aria-labelledby="cyberModule2Heading" data-bs-parent="#cybersecurityAccordion">
          <div class="accordion-body">
            <ul>
              <li>Network Architecture & Protocols</li>
              <li>Firewalls & VPNs</li>
              <li>Intrusion Detection & Prevention Systems</li>
              <li>Network Monitoring Tools</li>
              <li>Securing Wireless Networks</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 3 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="cyberModule3Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#cyberModule3" aria-expanded="false" aria-controls="cyberModule3">
            Module 3: Application & Web Security
          </button>
        </h2>
        <div id="cyberModule3" class="accordion-collapse collapse" aria-labelledby="cyberModule3Heading" data-bs-parent="#cybersecurityAccordion">
          <div class="accordion-body">
            <ul>
              <li>OWASP Top 10</li>
              <li>Secure Coding Practices</li>
              <li>Web App Firewalls</li>
              <li>Penetration Testing Basics</li>
              <li>Code Auditing Tools</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 4 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="cyberModule4Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#cyberModule4" aria-expanded="false" aria-controls="cyberModule4">
            Module 4: Risk Management & Compliance
          </button>
        </h2>
        <div id="cyberModule4" class="accordion-collapse collapse" aria-labelledby="cyberModule4Heading" data-bs-parent="#cybersecurityAccordion">
          <div class="accordion-body">
            <ul>
              <li>Risk Assessment Methodologies</li>
              <li>Information Security Policies</li>
              <li>Data Protection Laws (GDPR, HIPAA)</li>
              <li>ISO/IEC 27001 Overview</li>
              <li>Incident Response Planning</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 5 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="cyberModule5Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#cyberModule5" aria-expanded="false" aria-controls="cyberModule5">
            Module 5: Ethical Hacking & Cyber Defense
          </button>
        </h2>
        <div id="cyberModule5" class="accordion-collapse collapse" aria-labelledby="cyberModule5Heading" data-bs-parent="#cybersecurityAccordion">
          <div class="accordion-body">
            <ul>
              <li>What is Ethical Hacking?</li>
              <li>Footprinting & Reconnaissance</li>
              <li>Social Engineering Techniques</li>
              <li>Defense-in-Depth Strategies</li>
              <li>Red Team vs Blue Team Exercises</li>
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
