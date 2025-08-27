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
     <h2 class="text-center mb-4">Data Science Projects</h2>
      <div class="accordion" id="dsAccordion">

        <!-- Module 1 -->
        <div class="accordion-item">
          <h2 class="accordion-header" id="dsModule1Heading">
            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#dsModule1" aria-expanded="true" aria-controls="dsModule1">
              Module 1: Beginner Level Projects
            </button>
          </h2>
          <div id="dsModule1" class="accordion-collapse collapse show" aria-labelledby="dsModule1Heading" data-bs-parent="#dsAccordion">
            <div class="accordion-body">
              <ul>
                <li>House Price Prediction</li>
                <li>Student Performance Analysis</li>
                <li>Customer Segmentation</li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Module 2 -->
        <div class="accordion-item">
          <h2 class="accordion-header" id="dsModule2Heading">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#dsModule2" aria-expanded="false" aria-controls="dsModule2">
              Module 2: Intermediate Level Projects
            </button>
          </h2>
          <div id="dsModule2" class="accordion-collapse collapse" aria-labelledby="dsModule2Heading" data-bs-parent="#dsAccordion">
            <div class="accordion-body">
              <ul>
                <li>Loan Default Prediction</li>
                <li>Sentiment Analysis using NLP</li>
                <li>Fraud Detection System</li>
              </ul>
            </div>
          </div>
        </div>

        <!-- Module 3 -->
        <div class="accordion-item">
          <h2 class="accordion-header" id="dsModule3Heading">
            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#dsModule3" aria-expanded="false" aria-controls="dsModule3">
              Module 3: Advanced Projects
            </button>
          </h2>
          <div id="dsModule3" class="accordion-collapse collapse" aria-labelledby="dsModule3Heading" data-bs-parent="#dsAccordion">
            <div class="accordion-body">
              <ul>
                <li>Stock Price Prediction with LSTM</li>
                <li>Recommender System</li>
                <li>Autonomous Vehicle Image Classification</li>
              </ul>
            </div>
          </div>
        </div>

      </div>
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
<?php include('footer.php'); ?> <!-- Include your common footer -->
</body>
</html>