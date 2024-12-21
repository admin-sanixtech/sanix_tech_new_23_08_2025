<?php
header('Content-Type: application/json');

// Ensure the directory exists
$imageDir = "images/";
if (!is_dir($imageDir)) {
    mkdir($imageDir, 0777, true);
}

// Handle the uploaded image
if (isset($_FILES['file'])) {
    $file = $_FILES['file'];
    $targetPath = $imageDir . basename($file['name']);

    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        // Return the URL for the uploaded image
        echo json_encode(['location' => "/images/" . basename($file['name'])]);
    } else {
        echo json_encode(['error' => 'Failed to upload image.']);
    }
} else {
    echo json_encode(['error' => 'No file uploaded.']);
}
