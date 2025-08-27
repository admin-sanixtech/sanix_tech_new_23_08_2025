<?php include('header.php'); ?>
<?php include('navbar.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>IT Staffing & Consulting</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/user_styles.css">
</head>
<body>

<div class="container my-5 d-flex justify-content-center">
  <div class="center-accordion">
    <h2 class="text-center mb-4">IT Staffing & Consulting</h2>

    <div class="accordion" id="staffingAccordion">

      <!-- Module 1 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="staffingModule1Heading">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#staffingModule1" aria-expanded="true" aria-controls="staffingModule1">
            Module 1: Introduction to IT Staffing
          </button>
        </h2>
        <div id="staffingModule1" class="accordion-collapse collapse show" aria-labelledby="staffingModule1Heading" data-bs-parent="#staffingAccordion">
          <div class="accordion-body">
            <ul>
              <li>Understanding the IT Talent Landscape</li>
              <li>Roles in Demand (Developers, Analysts, Engineers)</li>
              <li>Staffing Models: Contract, Permanent, Remote</li>
              <li>Recruitment Lifecycle</li>
              <li>Trends in IT Staffing</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 2 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="staffingModule2Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#staffingModule2" aria-expanded="false" aria-controls="staffingModule2">
            Module 2: Technical Recruitment Strategies
          </button>
        </h2>
        <div id="staffingModule2" class="accordion-collapse collapse" aria-labelledby="staffingModule2Heading" data-bs-parent="#staffingAccordion">
          <div class="accordion-body">
            <ul>
              <li>Job Requirement Analysis</li>
              <li>Resume Screening & Shortlisting</li>
              <li>Technical Interviewing Techniques</li>
              <li>Assessment Tools and Platforms</li>
              <li>Candidate Experience & Feedback</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 3 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="staffingModule3Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#staffingModule3" aria-expanded="false" aria-controls="staffingModule3">
            Module 3: Consulting Services & Solutions
          </button>
        </h2>
        <div id="staffingModule3" class="accordion-collapse collapse" aria-labelledby="staffingModule3Heading" data-bs-parent="#staffingAccordion">
          <div class="accordion-body">
            <ul>
              <li>Overview of IT Consulting</li>
              <li>Project-Based Consulting</li>
              <li>Business Process Optimization</li>
              <li>IT Infrastructure & Cloud Advisory</li>
              <li>Custom Software Consulting</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 4 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="staffingModule4Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#staffingModule4" aria-expanded="false" aria-controls="staffingModule4">
            Module 4: Compliance & Workforce Management
          </button>
        </h2>
        <div id="staffingModule4" class="accordion-collapse collapse" aria-labelledby="staffingModule4Heading" data-bs-parent="#staffingAccordion">
          <div class="accordion-body">
            <ul>
              <li>HR & Legal Compliance</li>
              <li>Background Verification & Onboarding</li>
              <li>Timesheets & Payroll Systems</li>
              <li>Managing Remote Teams</li>
              <li>Client-Specific Guidelines</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 5 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="staffingModule5Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#staffingModule5" aria-expanded="false" aria-controls="staffingModule5">
            Module 5: Trends & Future of IT Staffing
          </button>
        </h2>
        <div id="staffingModule5" class="accordion-collapse collapse" aria-labelledby="staffingModule5Heading" data-bs-parent="#staffingAccordion">
          <div class="accordion-body">
            <ul>
              <li>Remote Work and Hybrid Models</li>
              <li>AI in Recruitment</li>
              <li>Upskilling & Reskilling Initiatives</li>
              <li>Freelance & Gig Economy</li>
              <li>Global IT Workforce Trends</li>
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
