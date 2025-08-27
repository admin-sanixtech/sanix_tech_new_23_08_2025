<?php include('header.php'); ?>
<?php include('navbar.php'); ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Training & Certification</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/user_styles.css">
</head>
<body>

<div class="container my-5 d-flex justify-content-center">
  <div class="center-accordion">
    <h2 class="text-center mb-4">Training & Certification</h2>

    <div class="accordion" id="trainingAccordion">

      <!-- Module 1 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="trainingModule1Heading">
          <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#trainingModule1" aria-expanded="true" aria-controls="trainingModule1">
            Module 1: Introduction to Professional Training
          </button>
        </h2>
        <div id="trainingModule1" class="accordion-collapse collapse show" aria-labelledby="trainingModule1Heading" data-bs-parent="#trainingAccordion">
          <div class="accordion-body">
            <ul>
              <li>Overview of Certification Programs</li>
              <li>Benefits of Industry-Recognized Credentials</li>
              <li>Career Path Planning</li>
              <li>Skill Gap Analysis</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 2 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="trainingModule2Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#trainingModule2" aria-expanded="false" aria-controls="trainingModule2">
            Module 2: Technical Skill Development
          </button>
        </h2>
        <div id="trainingModule2" class="accordion-collapse collapse" aria-labelledby="trainingModule2Heading" data-bs-parent="#trainingAccordion">
          <div class="accordion-body">
            <ul>
              <li>Programming Languages & Frameworks</li>
              <li>DevOps, Cloud & Containerization</li>
              <li>Cybersecurity & Ethical Hacking</li>
              <li>Data Science & AI Technologies</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 3 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="trainingModule3Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#trainingModule3" aria-expanded="false" aria-controls="trainingModule3">
            Module 3: Certification Preparation
          </button>
        </h2>
        <div id="trainingModule3" class="accordion-collapse collapse" aria-labelledby="trainingModule3Heading" data-bs-parent="#trainingAccordion">
          <div class="accordion-body">
            <ul>
              <li>Exam-Oriented Training</li>
              <li>Mock Tests & Assessments</li>
              <li>Practice Labs & Case Studies</li>
              <li>Exam Registration Guidance</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 4 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="trainingModule4Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#trainingModule4" aria-expanded="false" aria-controls="trainingModule4">
            Module 4: Soft Skills & Interview Prep
          </button>
        </h2>
        <div id="trainingModule4" class="accordion-collapse collapse" aria-labelledby="trainingModule4Heading" data-bs-parent="#trainingAccordion">
          <div class="accordion-body">
            <ul>
              <li>Communication & Presentation Skills</li>
              <li>Resume Building & LinkedIn Profile</li>
              <li>Mock Interviews & HR Rounds</li>
              <li>Career Counseling & Placement Tips</li>
            </ul>
          </div>
        </div>
      </div>

      <!-- Module 5 -->
      <div class="accordion-item">
        <h2 class="accordion-header" id="trainingModule5Heading">
          <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#trainingModule5" aria-expanded="false" aria-controls="trainingModule5">
            Module 5: Certification Domains Covered
          </button>
        </h2>
        <div id="trainingModule5" class="accordion-collapse collapse" aria-labelledby="trainingModule5Heading" data-bs-parent="#trainingAccordion">
          <div class="accordion-body">
            <ul>
              <li>Full Stack Development</li>
              <li>Cloud Certifications (AWS, Azure, GCP)</li>
              <li>Cybersecurity Certifications (CEH, CompTIA, CISSP)</li>
              <li>Data & AI Certifications (Power BI, Tableau, ML)</li>
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
