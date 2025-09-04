<?php
// admin_create_post.php
session_start();

require_once(__DIR__ . '/../../config/db_connection.php');


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Secure admin check
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: http://sanixtech.in/login.php');
    exit();
}


$message = '';
$errors = [];

// Debug: Check database connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

$message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $category_id = $_POST['category_id'];
    $subcategory_id = $_POST['subcategory_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $content = isset($_POST['content']) ? $_POST['content'] : '';
    $action = isset($_POST['action']) ? $_POST['action'] : 'publish';
    
    // CHANGED: All posts go to pending status first, regardless of who creates them
    $status = 'pending'; // Always pending for approval workflow
    $published_at = null; // Will be set when actually approved and published
    
    // Calculate word count and reading time
    $word_count = str_word_count(strip_tags($content));
    $reading_time = max(1, ceil($word_count / 200)); // Minimum 1 minute
    
    // Handle featured image upload
    $featured_image = null;
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] === UPLOAD_ERR_OK) {
        $featured_image = handleImageUpload($_FILES['featured_image'], 'featured_');
    }
    
    // Handle additional images
    $images_data = [];
    if (isset($_FILES['additional_images'])) {
        foreach ($_FILES['additional_images']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['additional_images']['error'][$key] === UPLOAD_ERR_OK) {
                $uploaded_file = handleImageUpload([
                    'tmp_name' => $tmp_name,
                    'name' => $_FILES['additional_images']['name'][$key],
                    'type' => $_FILES['additional_images']['type'][$key],
                    'size' => $_FILES['additional_images']['size'][$key]
                ], 'additional_');
                if ($uploaded_file) {
                    $images_data[] = $uploaded_file;
                }
            }
        }
    }
    
    // Convert images array to JSON
    $images_json = !empty($images_data) ? json_encode($images_data) : null;
    
    // Insert the post into the database with correct field mapping
    $sql = "INSERT INTO posts (
        category_id, subcategory_id, title, description, content, 
        featured_image, images_data, word_count, reading_time, 
        createdby, user_id, status, published_at, created_at
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
    
    $stmt = $conn->prepare($sql);
    
    if ($stmt) {
        $stmt->bind_param(
            "iissssiiiisss", 
            $category_id, $subcategory_id, $title, $description, $content,
            $featured_image, $images_json, $word_count, $reading_time,
            $user_id, $user_id, $status, $published_at
        );

        if ($stmt->execute()) {
            $post_id = $conn->insert_id;
            
            // CHANGED: Update success message to reflect pending status
            $success_message = ($action === 'draft') 
                ? 'Post saved as draft successfully!' 
                : 'Post submitted successfully and is now pending approval!';
            
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                // AJAX request
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true, 
                    'message' => $success_message,
                    'post_id' => $post_id,
                    'status' => $status
                ]);
                exit;
            } else {
                $message = "<div class='alert alert-success'>$success_message</div>";
            }
        } else {
            $error_msg = "Database Error: " . $stmt->error;
            
            if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
                header('Content-Type: application/json');
                echo json_encode(['success' => false, 'message' => $error_msg]);
                exit;
            } else {
                $message = "<div class='alert alert-danger'>$error_msg</div>";
            }
        }
        $stmt->close();
    } else {
        $error_msg = "Failed to prepare statement: " . $conn->error;
        
        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => false, 'message' => $error_msg]);
            exit;
        } else {
            $message = "<div class='alert alert-danger'>$error_msg</div>";
        }
    }
}

// Function to handle image uploads
function handleImageUpload($file, $prefix = '') {
    $upload_dir = 'uploads/posts/';
    
    // Create directory if it doesn't exist
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0755, true);
    }
    
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    // Handle both regular file upload array and custom array
    $tmp_name = is_array($file) && isset($file['tmp_name']) ? $file['tmp_name'] : $file['tmp_name'];
    $name = is_array($file) && isset($file['name']) ? $file['name'] : $file['name'];
    $type = is_array($file) && isset($file['type']) ? $file['type'] : $file['type'];
    $size = is_array($file) && isset($file['size']) ? $file['size'] : $file['size'];
    
    if (!in_array($type, $allowed_types)) {
        return false;
    }
    
    if ($size > $max_size) {
        return false;
    }
    
    $file_extension = pathinfo($name, PATHINFO_EXTENSION);
    $new_filename = $prefix . uniqid() . '.' . $file_extension;
    $file_path = $upload_dir . $new_filename;
    
    if (move_uploaded_file($tmp_name, $file_path)) {
        return $file_path;
    }
    
    return false;
}

// Fetch categories for the dropdown
$categories_query = "SELECT category_id, category_name FROM categories ORDER BY category_name";
$categories_result = $conn->query($categories_query);

if (!$categories_result) {
    die("Error fetching categories: " . $conn->error);
}

// Handle AJAX request for subcategories
if (isset($_GET['category_id']) && !isset($_POST['title'])) {
    $category_id = intval($_GET['category_id']);
    
    $query = "SELECT subcategory_id, subcategory_name FROM subcategories WHERE category_id = ? ORDER BY subcategory_name";
    $stmt = $conn->prepare($query);
    
    if ($stmt) {
        $stmt->bind_param("i", $category_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo '<option value="">Select Subcategory</option>';
            while ($row = $result->fetch_assoc()) {
                echo "<option value='" . $row['subcategory_id'] . "'>" . htmlspecialchars($row['subcategory_name']) . "</option>";
            }
        } else {
            echo "<option value=''>No subcategories available</option>";
        }
        $stmt->close();
    }
    exit; // Important: exit after handling AJAX request
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8" />
    <title>Create New Post</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.7/quill.snow.min.css" rel="stylesheet">
    <link rel="stylesheet" href="../../css/admin_styleone.css">
    
    <style>
        .image-upload-area {
            border: 2px dashed #6c757d;
            border-radius: 8px;
            padding: 40px;
            text-align: center;
            background: rgba(255, 255, 255, 0.05);
            transition: all 0.3s ease;
            cursor: pointer;
        }
        
        .image-upload-area:hover {
            border-color: #0d6efd;
            background: rgba(13, 110, 253, 0.1);
        }
        
        .image-upload-area.dragover {
            border-color: #198754;
            background: rgba(25, 135, 84, 0.1);
        }
        
        .uploaded-images {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 15px;
        }
        
        .image-preview {
            position: relative;
            width: 120px;
            height: 120px;
            border-radius: 8px;
            overflow: hidden;
            border: 2px solid #495057;
        }
        
        .image-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        
        .image-remove {
            position: absolute;
            top: 5px;
            right: 5px;
            background: rgba(220, 53, 69, 0.8);
            color: white;
            border: none;
            border-radius: 50%;
            width: 25px;
            height: 25px;
            font-size: 12px;
            cursor: pointer;
        }
        
        .editor-toolbar {
            background: rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 10px;
            margin-bottom: 15px;
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            align-items: center;
        }
        
        .font-size-controls {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .font-size-btn {
            width: 35px;
            height: 35px;
            border: 1px solid #495057;
            background: rgba(255, 255, 255, 0.1);
            color: white;
            border-radius: 4px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .font-size-btn:hover {
            background: rgba(13, 110, 253, 0.2);
            border-color: #0d6efd;
        }
        
        .color-picker-container {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .color-option {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            border: 2px solid #495057;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        .color-option:hover {
            transform: scale(1.1);
            border-color: white;
        }
        
        .color-option.active {
            border-color: #0d6efd;
            border-width: 3px;
        }
        
        #editor-container {
            background: white;
            border-radius: 8px;
            min-height: 400px;
        }
        
        .ql-editor {
            min-height: 350px;
            font-size: 16px;
            line-height: 1.6;
        }
        
        .image-alignment-controls {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        
        .alignment-btn {
            padding: 8px 12px;
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid #495057;
            color: white;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.2s;
        }
        
        .alignment-btn:hover {
            background: rgba(13, 110, 253, 0.2);
            border-color: #0d6efd;
        }
        
        .alignment-btn.active {
            background: #0d6efd;
            border-color: #0d6efd;
        }
        
        .word-count-display {
            text-align: right;
            color: #6c757d;
            font-size: 14px;
            margin-top: 10px;
        }
        
        .progress-container {
            margin-top: 20px;
        }

        .status-notification {
            background: rgba(255, 193, 7, 0.1);
            border: 1px solid #ffc107;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 20px;
        }
        
        .status-notification .fa-info-circle {
            color: #ffc107;
        }
    </style>
</head>
<body>
<div class="wrapper">
    <aside id="sidebar" class="js-sidebar">
        <?php include '../../admin_menu.php'; ?>
    </aside>
    <div class="main">
        <?php include '../../admin_navbar.php'; ?>
        
        <main class="content px-3 py-2">
            <div class="container-fluid">
                <div class="card border-0">
                    <div class="card-header">
                        <h2 class="mb-0">
                            <i class="fas fa-plus-circle me-2"></i>Create New Post
                        </h2>
                    </div>
                    <div class="card-body">
                        
                        <!-- Status notification -->
                        <div class="status-notification">
                            <div class="d-flex align-items-center">
                                <i class="fas fa-info-circle fa-lg me-3"></i>
                                <div>
                                    <strong>Approval Process:</strong>
                                    <p class="mb-0">All posts, including admin-created posts, require approval before being published. 
                                    Your post will be submitted with "Pending" status and needs to be approved through the post management system.</p>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Success/Error Messages -->
                        <div id="message-container">
                            <?php echo $message; ?>
                        </div>
                        
                        <form id="postForm" enctype="multipart/form-data" method="POST">
                            
                            <!-- Basic Post Information -->
                            <div class="row mb-4">
                                <div class="col-md-6">
                                    <label for="category_id" class="form-label">
                                        <i class="fas fa-folder me-2"></i>Category
                                    </label>
                                    <select name="category_id" id="category_id" class="form-select" required>
                                        <option value="">Select Category</option>
                                        <?php
                                        if ($categories_result && $categories_result->num_rows > 0) {
                                            while ($category = $categories_result->fetch_assoc()) {
                                                echo "<option value='" . $category['category_id'] . "'>" . htmlspecialchars($category['category_name']) . "</option>";
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label for="subcategory_id" class="form-label">
                                        <i class="fas fa-folder-open me-2"></i>Subcategory
                                    </label>
                                    <select name="subcategory_id" id="subcategory_id" class="form-select" required>
                                        <option value="">Select Subcategory</option>
                                    </select>
                                </div>
                            </div>
                            
                            <!-- Title -->
                            <div class="mb-4">
                                <label for="title" class="form-label">
                                    <i class="fas fa-heading me-2"></i>Post Title
                                </label>
                                <input type="text" name="title" id="title" class="form-control form-control-lg" 
                                       placeholder="Enter an engaging title..." required maxlength="200">
                                <div class="form-text">Maximum 200 characters</div>
                            </div>
                            
                            <!-- Featured Image Upload -->
                            <div class="mb-4">
                                <label class="form-label">
                                    <i class="fas fa-image me-2"></i>Featured Image
                                </label>
                                <div class="image-upload-area" id="featuredImageUpload">
                                    <i class="fas fa-cloud-upload-alt fa-3x mb-3 text-muted"></i>
                                    <h5>Drop your featured image here or click to browse</h5>
                                    <p class="text-muted">Supports JPG, PNG, GIF up to 5MB</p>
                                    <input type="file" name="featured_image" id="featuredImageInput" accept="image/*" style="display: none;">
                                </div>
                                <div id="featuredImagePreview" class="uploaded-images"></div>
                            </div>
                            
                            <!-- Short Description -->
                            <div class="mb-4">
                                <label for="description" class="form-label">
                                    <i class="fas fa-align-left me-2"></i>Short Description
                                </label>
                                <textarea name="description" id="description" class="form-control" rows="3" 
                                          placeholder="Write a brief description that will appear in post previews..." 
                                          maxlength="500"></textarea>
                                <div class="form-text">Maximum 500 characters - used for post previews and SEO</div>
                            </div>
                            
                            <!-- Rich Text Editor -->
                            <div class="mb-4">
                                <label class="form-label">
                                    <i class="fas fa-edit me-2"></i>Post Content
                                </label>
                                
                                <!-- Custom Editor Toolbar -->
                                <div class="editor-toolbar">
                                    <!-- Font Size Controls -->
                                    <div class="font-size-controls">
                                        <span class="text-muted me-2">Font Size:</span>
                                        <button type="button" class="font-size-btn" id="decreaseFontSize" title="Decrease Font Size">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                        <span id="currentFontSize" class="mx-2 text-muted">16px</span>
                                        <button type="button" class="font-size-btn" id="increaseFontSize" title="Increase Font Size">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                    
                                    <div class="vr"></div>
                                    
                                    <!-- Text Color Options -->
                                    <div class="color-picker-container">
                                        <span class="text-muted me-2">Text Color:</span>
                                        <div class="color-option" data-color="#000000" style="background-color: #000000;" title="Black"></div>
                                        <div class="color-option" data-color="#dc3545" style="background-color: #dc3545;" title="Red"></div>
                                        <div class="color-option" data-color="#0d6efd" style="background-color: #0d6efd;" title="Blue"></div>
                                        <div class="color-option" data-color="#198754" style="background-color: #198754;" title="Green"></div>
                                        <div class="color-option" data-color="#fd7e14" style="background-color: #fd7e14;" title="Orange"></div>
                                        <div class="color-option" data-color="#6f42c1" style="background-color: #6f42c1;" title="Purple"></div>
                                    </div>
                                    
                                    <div class="vr"></div>
                                    
                                    <!-- Image Alignment Controls -->
                                    <div class="image-alignment-controls">
                                        <span class="text-muted me-2">Image Alignment:</span>
                                        <button type="button" class="alignment-btn" data-align="left" title="Align Left">
                                            <i class="fas fa-align-left"></i> Left
                                        </button>
                                        <button type="button" class="alignment-btn" data-align="center" title="Align Center">
                                            <i class="fas fa-align-center"></i> Center
                                        </button>
                                        <button type="button" class="alignment-btn" data-align="right" title="Align Right">
                                            <i class="fas fa-align-right"></i> Right
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Quill Editor Container -->
                                <div id="editor-container"></div>
                                
                                <!-- Word Count Display -->
                                <div class="word-count-display">
                                    <span id="wordCount">0</span> words | 
                                    <span id="charCount">0</span> characters | 
                                    Estimated reading time: <span id="readingTime">0</span> min
                                </div>
                                
                                <!-- Hidden input to store editor content -->
                                <input type="hidden" name="content" id="hiddenContent">
                            </div>
                            
                            <!-- Additional Images -->
                            <div class="mb-4">
                                <label class="form-label">
                                    <i class="fas fa-images me-2"></i>Additional Images
                                </label>
                                <div class="image-upload-area" id="additionalImagesUpload">
                                    <i class="fas fa-plus fa-2x mb-3 text-muted"></i>
                                    <h6>Add more images to your post</h6>
                                    <p class="text-muted">These images can be inserted into your content</p>
                                    <input type="file" name="additional_images[]" id="additionalImagesInput" accept="image/*" multiple style="display: none;">
                                </div>
                                <div id="additionalImagesPreview" class="uploaded-images"></div>
                            </div>
                            
                            <!-- Submit Button with Progress -->
                            <div class="progress-container">
                                <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                    <button type="button" class="btn btn-outline-secondary me-md-2" id="saveDraft">
                                        <i class="fas fa-save me-2"></i>Save as Draft
                                    </button>
                                    <button type="submit" class="btn btn-primary" id="publishBtn">
                                        <i class="fas fa-paper-plane me-2"></i>Submit for Approval
                                    </button>
                                </div>
                                
                                <!-- Upload Progress Bar -->
                                <div class="progress mt-3" id="uploadProgress" style="display: none;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                         role="progressbar" style="width: 0%"></div>
                                </div>
                            </div>
                            
                        </form>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Scripts -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/quill/1.3.7/quill.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Initialize Quill Editor
const quill = new Quill('#editor-container', {
    theme: 'snow',
    placeholder: 'Start writing your amazing content here...',
    modules: {
        toolbar: [
            [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            [{ 'indent': '-1'}, { 'indent': '+1' }],
            [{ 'align': [] }],
            ['link', 'image', 'video'],
            ['blockquote', 'code-block'],
            [{ 'color': [] }, { 'background': [] }],
            ['clean']
        ]
    }
});

// Variables for editor management
let currentFontSize = 16;
let uploadedImages = [];
let featuredImage = null;

// Font size controls
document.getElementById('increaseFontSize').addEventListener('click', function() {
    currentFontSize = Math.min(currentFontSize + 2, 32);
    updateEditorFontSize();
});

document.getElementById('decreaseFontSize').addEventListener('click', function() {
    currentFontSize = Math.max(currentFontSize - 2, 10);
    updateEditorFontSize();
});

function updateEditorFontSize() {
    const editor = document.querySelector('.ql-editor');
    editor.style.fontSize = currentFontSize + 'px';
    document.getElementById('currentFontSize').textContent = currentFontSize + 'px';
}

// Color picker functionality
document.querySelectorAll('.color-option').forEach(option => {
    option.addEventListener('click', function() {
        const color = this.dataset.color;
        
        // Remove active class from all options
        document.querySelectorAll('.color-option').forEach(opt => opt.classList.remove('active'));
        
        // Add active class to clicked option
        this.classList.add('active');
        
        // Apply color to selected text
        quill.format('color', color);
    });
});

// Image alignment controls
document.querySelectorAll('.alignment-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const alignment = this.dataset.align;
        
        // Remove active class from all buttons
        document.querySelectorAll('.alignment-btn').forEach(b => b.classList.remove('active'));
        
        // Add active class to clicked button
        this.classList.add('active');
        
        // Apply alignment to selected content
        quill.format('align', alignment);
    });
});

// Featured Image Upload
document.getElementById('featuredImageUpload').addEventListener('click', function() {
    document.getElementById('featuredImageInput').click();
});

document.getElementById('featuredImageInput').addEventListener('change', function(e) {
    handleFeaturedImageUpload(e.target.files[0]);
});

// Additional Images Upload
document.getElementById('additionalImagesUpload').addEventListener('click', function() {
    document.getElementById('additionalImagesInput').click();
});

document.getElementById('additionalImagesInput').addEventListener('change', function(e) {
    handleAdditionalImagesUpload(e.target.files);
});

// Drag and drop functionality
setupDragAndDrop('featuredImageUpload', handleFeaturedImageUpload);
setupDragAndDrop('additionalImagesUpload', handleAdditionalImagesUpload);

function setupDragAndDrop(elementId, handler) {
    const element = document.getElementById(elementId);
    
    element.addEventListener('dragover', function(e) {
        e.preventDefault();
        this.classList.add('dragover');
    });
    
    element.addEventListener('dragleave', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
    });
    
    element.addEventListener('drop', function(e) {
        e.preventDefault();
        this.classList.remove('dragover');
        
        const files = elementId === 'featuredImageUpload' ? [e.dataTransfer.files[0]] : e.dataTransfer.files;
        handler(files);
    });
}

function handleFeaturedImageUpload(file) {
    if (!file) return;
    
    if (!isValidImage(file)) {
        showMessage('Please select a valid image file (JPG, PNG, GIF) under 5MB.', 'danger');
        return;
    }
    
    featuredImage = file;
    
    const reader = new FileReader();
    reader.onload = function(e) {
        const preview = document.getElementById('featuredImagePreview');
        preview.innerHTML = `
            <div class="image-preview">
                <img src="${e.target.result}" alt="Featured Image">
                <button type="button" class="image-remove" onclick="removeFeaturedImage()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
    };
    reader.readAsDataURL(file);
}

function handleAdditionalImagesUpload(files) {
    Array.from(files).forEach(file => {
        if (!isValidImage(file)) {
            showMessage(`Invalid image: ${file.name}. Please select valid image files.`, 'warning');
            return;
        }
        
        uploadedImages.push(file);
        
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('additionalImagesPreview');
            const imageDiv = document.createElement('div');
            imageDiv.className = 'image-preview';
            imageDiv.innerHTML = `
                <img src="${e.target.result}" alt="Additional Image">
                <button type="button" class="image-remove" onclick="removeAdditionalImage(${uploadedImages.length - 1})">
                    <i class="fas fa-times"></i>
                </button>
            `;
            preview.appendChild(imageDiv);
        };
        reader.readAsDataURL(file);
    });
}

function isValidImage(file) {
    const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    const maxSize = 5 * 1024 * 1024; // 5MB
    
    return validTypes.includes(file.type) && file.size <= maxSize;
}

function removeFeaturedImage() {
    featuredImage = null;
    document.getElementById('featuredImagePreview').innerHTML = '';
    document.getElementById('featuredImageInput').value = '';
}

function removeAdditionalImage(index) {
    uploadedImages.splice(index, 1);
    updateAdditionalImagesPreview();
}

function updateAdditionalImagesPreview() {
    const preview = document.getElementById('additionalImagesPreview');
    preview.innerHTML = '';
    
    uploadedImages.forEach((file, index) => {
        const reader = new FileReader();
        reader.onload = function(e) {
            const imageDiv = document.createElement('div');
            imageDiv.className = 'image-preview';
            imageDiv.innerHTML = `
                <img src="${e.target.result}" alt="Additional Image">
                <button type="button" class="image-remove" onclick="removeAdditionalImage(${index})">
                    <i class="fas fa-times"></i>
                </button>
            `;
            preview.appendChild(imageDiv);
        };
        reader.readAsDataURL(file);
    });
}

// Word count and reading time calculation
quill.on('text-change', function() {
    const text = quill.getText().trim();
    const wordCount = text.split(/\s+/).filter(word => word.length > 0).length;
    const charCount = text.length;
    const readingTime = Math.ceil(wordCount / 200); // Average reading speed: 200 words per minute
    
    document.getElementById('wordCount').textContent = wordCount;
    document.getElementById('charCount').textContent = charCount;
    document.getElementById('readingTime').textContent = readingTime;
    
    // Update hidden input with editor content
    document.getElementById('hiddenContent').value = quill.root.innerHTML;
});

// Category change handler - Fixed AJAX call
document.getElementById('category_id').addEventListener('change', function() {
    const categoryId = this.value;
    const subcategorySelect = document.getElementById('subcategory_id');
    
    if (categoryId) {
        // Show loading state
        subcategorySelect.innerHTML = '<option value="">Loading...</option>';
        subcategorySelect.disabled = true;
        
        fetch(`${window.location.pathname}?category_id=${categoryId}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.text();
        })
        .then(data => {
            subcategorySelect.innerHTML = data;
            subcategorySelect.disabled = false;
        })
        .catch(error => {
            console.error('Error fetching subcategories:', error);
            subcategorySelect.innerHTML = '<option value="">Error loading subcategories</option>';
            subcategorySelect.disabled = false;
            showMessage('Error loading subcategories. Please try again.', 'danger');
        });
    } else {
        subcategorySelect.innerHTML = '<option value="">Select Subcategory</option>';
        subcategorySelect.disabled = false;
    }
});

// Form submission
document.getElementById('postForm').addEventListener('submit', function(e) {
    e.preventDefault();
    submitPost('publish');
});

document.getElementById('saveDraft').addEventListener('click', function() {
    submitPost('draft');
});

function submitPost(action) {
    // Validate required fields
    const categoryId = document.getElementById('category_id').value;
    const subcategoryId = document.getElementById('subcategory_id').value;
    const title = document.getElementById('title').value;
    const description = document.getElementById('description').value;
    
    if (!categoryId || !subcategoryId || !title.trim() || !description.trim()) {
        showMessage('Please fill in all required fields.', 'danger');
        return;
    }
    
    const formData = new FormData();
    
    // Add form fields
    formData.append('category_id', categoryId);
    formData.append('subcategory_id', subcategoryId);
    formData.append('title', title.trim());
    formData.append('description', description.trim());
    formData.append('content', quill.root.innerHTML);
    formData.append('action', action);
    
    // Add featured image if selected
    const featuredImageInput = document.getElementById('featuredImageInput');
    if (featuredImageInput.files && featuredImageInput.files[0]) {
        formData.append('featured_image', featuredImageInput.files[0]);
    }
    
    // Add additional images if selected
    const additionalImagesInput = document.getElementById('additionalImagesInput');
    if (additionalImagesInput.files) {
        for (let i = 0; i < additionalImagesInput.files.length; i++) {
            formData.append(`additional_images[${i}]`, additionalImagesInput.files[i]);
        }
    }
    
    // Show progress bar
    const progressBar = document.getElementById('uploadProgress');
    progressBar.style.display = 'block';
    
    // Disable submit buttons
    const publishBtn = document.getElementById('publishBtn');
    const draftBtn = document.getElementById('saveDraft');
    publishBtn.disabled = true;
    draftBtn.disabled = true;
    
    // Update button text to show loading state
    const originalPublishText = publishBtn.innerHTML;
    const originalDraftText = draftBtn.innerHTML;
    publishBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Processing...';
    draftBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Saving...';
    
    // Submit form
    fetch(window.location.pathname, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showMessage(data.message, 'success');
            
            if (action === 'publish') {
                setTimeout(() => {
                    if (confirm('Post submitted successfully and is now pending approval! Would you like to create another post?')) {
                        resetForm();
                    } else {
                        window.location.href = 'admin_posts.php';
                    }
                }, 2000);
            } else {
                setTimeout(() => {
                    resetForm();
                }, 2000);
            }
        } else {
            showMessage(data.message || 'An error occurred while saving the post.', 'danger');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('An error occurred while saving the post. Please try again.', 'danger');
    })
    .finally(() => {
        // Hide progress bar and restore buttons
        progressBar.style.display = 'none';
        publishBtn.disabled = false;
        draftBtn.disabled = false;
        publishBtn.innerHTML = originalPublishText;
        draftBtn.innerHTML = originalDraftText;
    });
}

function resetForm() {
    // Reset form fields
    document.getElementById('postForm').reset();
    
    // Reset Quill editor
    quill.setContents([]);
    
    // Reset image previews
    document.getElementById('featuredImagePreview').innerHTML = '';
    document.getElementById('additionalImagesPreview').innerHTML = '';
    
    // Reset subcategory dropdown
    document.getElementById('subcategory_id').innerHTML = '<option value="">Select Subcategory</option>';
    
    // Reset word count
    document.getElementById('wordCount').textContent = '0';
    document.getElementById('charCount').textContent = '0';
    document.getElementById('readingTime').textContent = '0';
    
    // Reset variables
    featuredImage = null;
    uploadedImages = [];
    
    // Reset font size
    currentFontSize = 16;
    updateEditorFontSize();
}

function showMessage(message, type) {
    const messageContainer = document.getElementById('message-container');
    messageContainer.innerHTML = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    `;
    
    // Scroll to top to show the message
    messageContainer.scrollIntoView({ behavior: 'smooth' });
    
    // Auto dismiss after 5 seconds for success messages
    if (type === 'success') {
        setTimeout(() => {
            const alert = messageContainer.querySelector('.alert');
            if (alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            }
        }, 5000);
    }
}

// Initialize editor font size
updateEditorFontSize();

// Set initial content in hidden input
document.getElementById('hiddenContent').value = quill.root.innerHTML;
</script>

</body>
</html>