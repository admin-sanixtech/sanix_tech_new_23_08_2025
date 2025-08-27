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
<div class="row">
    <div class="col-md-2 mb-4">
        <div class="card h-100 shadow-sm">
            <img src="uploads/python_logo.png" class="card-img-top" alt="Python" style="height: 100px; object-fit: cover;">
            <div class="card-body">
                <h5 class="card-title">Python</h5>
                <a href="category.php?id=1" class="btn btn-primary">View Posts</a>
            </div>
        </div>
    </div>

    <div class="col-md-2 mb-4">
        <div class="card h-100 shadow-sm">
            <img src="uploads/c.png" class="card-img-top" alt="Digital Marketing" style="height: 100px; object-fit: cover;">
            <div class="card-body">
                <h5 class="card-title">Digital Marketing</h5>
                <a href="category.php?id=2" class="btn btn-primary">View Posts</a>
            </div>
        </div>
    </div>

    <!-- Repeat for each category -->
</div>
<?php include('footer.php'); ?>

