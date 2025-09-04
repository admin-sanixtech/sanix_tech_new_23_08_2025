<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// PHP configuration for large content
ini_set('post_max_size', '64M');
ini_set('max_input_vars', '5000');
ini_set('memory_limit', '256M');
ini_set('max_execution_time', '300');

session_start();
// include '../db_connection.php';
require_once(__DIR__ . '/../../config/db_connection.php');

// Check admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: http://sanixtech.in/login.php');
    exit();
}

$message = '';
$errors = [];

// Debug: Check database connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Check database column type early (before any potential connection closures)
$db_status_message = '';
try {
    $column_check = $conn->query("SELECT DATA_TYPE, CHARACTER_MAXIMUM_LENGTH FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_NAME = 'job_post' AND COLUMN_NAME = 'description'");
    if ($column_check && $column_check->num_rows > 0) {
        $column_info = $column_check->fetch_assoc();
        if ($column_info['DATA_TYPE'] === 'longtext') {
            $db_status_message = "<span class='text-success'>✅ LONGTEXT - Supports unlimited description length</span>";
        } else {
            $db_status_message = "<span class='text-warning'>⚠️ " . strtoupper($column_info['DATA_TYPE']) . " - Limited to " . number_format($column_info['CHARACTER_MAXIMUM_LENGTH']) . " characters</span>";
            $db_status_message .= "<br><small><strong>To fix:</strong> Run this SQL: <code>ALTER TABLE job_post MODIFY COLUMN description LONGTEXT;</code></small>";
        }
    } else {
        $db_status_message = "<span class='text-danger'>❌ Could not check database column type</span>";
    }
} catch (Exception $e) {
    $db_status_message = "<span class='text-danger'>❌ Database error: " . $e->getMessage() . "</span>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required fields
    $required_fields = ['title', 'role', 'email_to', 'description', 'location'];
    foreach ($required_fields as $field) {
        if (empty($_POST[$field])) {
            $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
        }
    }
    
    // Enhanced description validation
    $description = trim($_POST['description']);
    if (!empty($description)) {
        $desc_length = strlen($description);
        
        if ($desc_length < 50) {
            $errors[] = 'Job description must be at least 50 characters long.';
        }
        
        // Check if we need LONGTEXT (more than 65,535 characters)
        if ($desc_length > 65535) {
            // Use the already checked column info to avoid another query
            if (strpos($db_status_message, 'LONGTEXT') === false) {
                $errors[] = "Description too long ({$desc_length} characters). Database needs LONGTEXT upgrade. Run: ALTER TABLE job_post MODIFY COLUMN description LONGTEXT;";
            }
        }
    }
    
    // Validate email
    if (!empty($_POST['email_to']) && !filter_var($_POST['email_to'], FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }
    
    // Validate application deadline
    if (!empty($_POST['application_deadline']) && strtotime($_POST['application_deadline']) < time()) {
        $errors[] = 'Application deadline must be in the future.';
    }
    
    if (empty($errors)) {
        // Create uploads directory if it doesn't exist
        $upload_dir = '../uploads/job_posts/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        // Sanitize and collect form data
        $title = trim($_POST['title']);
        $role = trim($_POST['role']);
        $email_to = trim($_POST['email_to']);
        $description = trim($_POST['description']);
        $location = trim($_POST['location']);
        $salary_range = trim($_POST['salary_range'] ?? '');
        $employment_type = $_POST['employment_type'] ?? 'full-time';
        $experience_level = trim($_POST['experience_level'] ?? '');
        $skills_required = trim($_POST['skills_required'] ?? '');
        $company_name = trim($_POST['company_name'] ?? '');
        $application_deadline = $_POST['application_deadline'] ?? null;
        $created_by = $_SESSION['user_id'];
        
        $company_logo = '';
        $job_images = [];
        
        try {
            // Handle company logo upload
            if (isset($_FILES['company_logo']) && $_FILES['company_logo']['error'] === UPLOAD_ERR_OK) {
                $logo_result = uploadImage($_FILES['company_logo'], $upload_dir, 'logo');
                if ($logo_result['success']) {
                    $company_logo = $logo_result['path'];
                } else {
                    $errors[] = 'Logo upload failed: ' . $logo_result['error'];
                }
            }
            
            // Handle multiple job images upload
            if (isset($_FILES['job_images']) && is_array($_FILES['job_images']['name'])) {
                for ($i = 0; $i < count($_FILES['job_images']['name']); $i++) {
                    if ($_FILES['job_images']['error'][$i] === UPLOAD_ERR_OK) {
                        $temp_file = [
                            'name' => $_FILES['job_images']['name'][$i],
                            'type' => $_FILES['job_images']['type'][$i],
                            'tmp_name' => $_FILES['job_images']['tmp_name'][$i],
                            'size' => $_FILES['job_images']['size'][$i]
                        ];
                        
                        $image_result = uploadImage($temp_file, $upload_dir, 'image_' . $i);
                        if ($image_result['success']) {
                            $job_images[] = $image_result['path'];
                        }
                    }
                }
            }
            
            if (empty($errors)) {
                $job_images_json = json_encode($job_images);
                
                // Enhanced database insertion
                $table_check = $conn->query("DESCRIBE job_post");
                if (!$table_check) {
                    $errors[] = "Table 'job_post' does not exist: " . $conn->error;
                } else {
                    // Get existing columns
                    $existing_columns = [];
                    while ($row = $table_check->fetch_assoc()) {
                        $existing_columns[] = $row['Field'];
                    }
                    
                    // Build dynamic INSERT query
                    $sql = "INSERT INTO job_post (title, role, email_to, description, location, created_by, status, is_approved";
                    $values = "?, ?, ?, ?, ?, ?, 'pending', 0";
                    $bind_types = 'sssssi';
                    $bind_values = [$title, $role, $email_to, $description, $location, $created_by];
                    
                    // Add optional fields if they exist
                    $optional_fields = [
                        'salary_range' => $salary_range,
                        'employment_type' => $employment_type,
                        'experience_level' => $experience_level,
                        'skills_required' => $skills_required,
                        'company_name' => $company_name,
                        'company_logo' => $company_logo,
                        'job_images' => $job_images_json
                    ];
                    
                    foreach ($optional_fields as $field => $value) {
                        if (in_array($field, $existing_columns)) {
                            $sql .= ", $field";
                            $values .= ", ?";
                            $bind_types .= 's';
                            $bind_values[] = $value;
                        }
                    }
                    
                    if (in_array('application_deadline', $existing_columns) && !empty($application_deadline)) {
                        $sql .= ", application_deadline";
                        $values .= ", ?";
                        $bind_types .= 's';
                        $bind_values[] = $application_deadline;
                    }
                    
                    $sql .= ") VALUES ($values)";
                    
                    $stmt = $conn->prepare($sql);
                    if ($stmt) {
                        if (!empty($bind_values)) {
                            $stmt->bind_param($bind_types, ...$bind_values);
                        }
                        
                        if ($stmt->execute()) {
                            $job_post_id = $conn->insert_id;
                            
                            // Verify the description was saved correctly
                            $verify_stmt = $conn->prepare("SELECT LENGTH(description) as desc_length FROM job_post WHERE job_id = ?");
                            $verify_stmt->bind_param("i", $job_post_id);
                            $verify_stmt->execute();
                            $verify_result = $verify_stmt->get_result();
                            $verify_data = $verify_result->fetch_assoc();
                            
                            // Add approval history if table exists
                            $history_check = $conn->query("SHOW TABLES LIKE 'job_approval_history'");
                            if ($history_check && $history_check->num_rows > 0) {
                                $history_stmt = $conn->prepare("INSERT INTO job_approval_history (job_post_id, admin_id, action, comments, created_at) VALUES (?, ?, 'pending_review', 'Job post submitted for approval', NOW())");
                                if ($history_stmt) {
                                    $history_stmt->bind_param("ii", $job_post_id, $created_by);
                                    $history_stmt->execute();
                                    $history_stmt->close();
                                }
                            }
                            
                            // Notify other admins
                            $notification_check = $conn->query("SHOW TABLES LIKE 'admin_notifications'");
                            if ($notification_check && $notification_check->num_rows > 0) {
                                $notify_stmt = $conn->prepare("INSERT INTO admin_notifications (admin_id, job_post_id, message, created_at) SELECT user_id, ?, CONCAT('New job post \"', ?, '\" requires approval'), NOW() FROM users WHERE role = 'admin' AND user_id != ?");
                                if ($notify_stmt) {
                                    $notify_stmt->bind_param("isi", $job_post_id, $title, $created_by);
                                    $notify_stmt->execute();
                                    $notify_stmt->close();
                                }
                            }
                            
                            $message = "<div class='alert alert-success'><i class='fas fa-check-circle'></i> Job posted successfully! Job ID: #$job_post_id<br><small>Description saved: {$verify_data['desc_length']} characters</small></div>";
                            
                            // Clear form data after successful submission
                            $_POST = [];
                            
                        } else {
                            $errors[] = "Database execute error: " . $stmt->error;
                        }
                        $stmt->close();
                    } else {
                        $errors[] = "Database prepare error: " . $conn->error;
                    }
                }
            }
            
        } catch (Exception $e) {
            $errors[] = "System error: " . $e->getMessage();
        }
    }
    
    if (!empty($errors)) {
        $message = "<div class='alert alert-danger'><i class='fas fa-exclamation-triangle'></i> <strong>Please fix the following errors:</strong><ul class='mb-0 mt-2'>";
        foreach ($errors as $error) {
            $message .= "<li>$error</li>";
        }
        $message .= "</ul></div>";
    }
}

// Image upload function
function uploadImage($file, $upload_dir, $prefix = 'img') {
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if (!in_array($file['type'], $allowed_types)) {
        return ['success' => false, 'error' => 'Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.'];
    }
    
    if ($file['size'] > $max_size) {
        return ['success' => false, 'error' => 'File size too large. Maximum 5MB allowed.'];
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = time() . '_' . $prefix . '_' . uniqid() . '.' . strtolower($extension);
    $full_path = $upload_dir . $filename;
    $relative_path = 'uploads/job_posts/' . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $full_path)) {
        return ['success' => true, 'path' => $relative_path];
    } else {
        return ['success' => false, 'error' => 'Failed to move uploaded file.'];
    }
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post New Job - Admin Dashboard</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../../css/admin_styleone.css">
      
    <style>
        .form-section {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 2rem;
            backdrop-filter: blur(10px);
        }
        
        .form-section h5 {
            color: #fff;
            margin-bottom: 1.5rem;
            padding-bottom: 0.5rem;
            border-bottom: 2px solid #0d6efd;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .image-preview {
            max-width: 120px;
            max-height: 120px;
            object-fit: cover;
            border-radius: 8px;
            border: 2px solid rgba(255, 255, 255, 0.1);
            margin: 5px;
            transition: transform 0.2s;
        }
        
        .image-preview:hover {
            transform: scale(1.05);
        }
        
        .preview-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-top: 15px;
            padding: 15px;
            background: rgba(255, 255, 255, 0.02);
            border-radius: 8px;
            min-height: 60px;
            align-items: center;
        }
        
        .preview-container:empty::after {
            content: "No images selected";
            color: #6c757d;
            font-style: italic;
        }
        
        .form-control, .form-select {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: #fff;
            transition: all 0.3s ease;
        }
        
        .form-control:focus, .form-select:focus {
            background: rgba(255, 255, 255, 0.15);
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
            color: #fff;
        }
        
        .form-control::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #0d6efd, #6610f2);
            border: none;
            padding: 12px 30px;
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            background: linear-gradient(135deg, #0b5ed7, #520dc2);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(13, 110, 253, 0.3);
        }
        
        .page-header {
            background: linear-gradient(135deg, rgba(13, 110, 253, 0.1), rgba(102, 16, 242, 0.1));
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .upload-zone {
            border: 2px dashed rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .upload-zone:hover {
            border-color: #0d6efd;
            background: rgba(13, 110, 253, 0.05);
        }
        
        .required-field::after {
            content: " *";
            color: #dc3545;
        }
        
        .char-counter {
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        
        @media (max-width: 768px) {
            .form-section {
                padding: 1rem;
            }
        }
    </style>
</head>

<body>
<div class="wrapper">
    <!-- Sidebar -->
    <aside id="sidebar" class="js-sidebar">
        <?php include '../../admin_menu.php'; ?>
    </aside>
    
    <!-- Main Content -->
    <div class="main">
        <!-- Top Navigation -->
        <?php include '../../admin_navbar.php'; ?>
        
        <main class="content px-3 py-4">
            <div class="container-fluid">
                <!-- Page Header -->
                <div class="page-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1">
                                <i class="fas fa-briefcase me-2"></i>Post New Job
                            </h2>
                            <p class="text-muted mb-0">Create and submit a new job posting for approval</p>
                        </div>
                        <div>
                            <a href="admin_job_approvals.php" class="btn btn-outline-info">
                                <i class="fas fa-clock me-2"></i>Pending Approvals
                            </a>
                            <a href="manage_jobs.php" class="btn btn-outline-secondary ms-2">
                                <i class="fas fa-list me-2"></i>All Jobs
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Database Status Check -->
                <div class="alert alert-info">
                    <i class="fas fa-database me-2"></i>
                    <strong>Database Status:</strong>
                    <?php echo $db_status_message; ?>
                </div>
                
                <!-- Messages -->
                <?php if (!empty($message)) echo $message; ?>
                
                <!-- Job Posting Form -->
                <form method="POST" enctype="multipart/form-data" id="jobForm">
                    <!-- Basic Job Information -->
                    <div class="form-section">
                        <h5>
                            <i class="fas fa-info-circle"></i>
                            Basic Job Information
                        </h5>
                        
                        <div class="row">
                            <div class="col-lg-6 mb-3">
                                <label class="form-label required-field">Job Title</label>
                                <input type="text" class="form-control" name="title" 
                                       placeholder="e.g., Senior Full Stack Developer" 
                                       value="<?= htmlspecialchars($_POST['title'] ?? '') ?>" required>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label required-field">Role Category</label>
                                <input type="text" class="form-control" name="role" 
                                       placeholder="e.g., Python Developer, UI/UX Designer" 
                                       value="<?= htmlspecialchars($_POST['role'] ?? '') ?>" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-lg-6 mb-3">
                                <label class="form-label required-field">Application Email</label>
                                <input type="email" class="form-control" name="email_to" 
                                       placeholder="jobs@company.com" 
                                       value="<?= htmlspecialchars($_POST['email_to'] ?? '') ?>" required>
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label required-field">Location</label>
                                <input type="text" class="form-control" name="location" 
                                       placeholder="e.g., New York, NY / Remote" 
                                       value="<?= htmlspecialchars($_POST['location'] ?? '') ?>" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-lg-6 mb-3">
                                <label class="form-label">Salary Range</label>
                                <input type="text" class="form-control" name="salary_range" 
                                       placeholder="e.g., $70,000 - $90,000 per year" 
                                       value="<?= htmlspecialchars($_POST['salary_range'] ?? '') ?>">
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label">Employment Type</label>
                                <select class="form-select" name="employment_type">
                                    <option value="full-time" <?= ($_POST['employment_type'] ?? '') === 'full-time' ? 'selected' : '' ?>>Full-time</option>
                                    <option value="part-time" <?= ($_POST['employment_type'] ?? '') === 'part-time' ? 'selected' : '' ?>>Part-time</option>
                                    <option value="contract" <?= ($_POST['employment_type'] ?? '') === 'contract' ? 'selected' : '' ?>>Contract</option>
                                    <option value="internship" <?= ($_POST['employment_type'] ?? '') === 'internship' ? 'selected' : '' ?>>Internship</option>
                                    <option value="freelance" <?= ($_POST['employment_type'] ?? '') === 'freelance' ? 'selected' : '' ?>>Freelance</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label required-field">Job Description</label>
                            <textarea class="form-control" name="description" rows="6" 
                                      placeholder="Provide a detailed job description including responsibilities, qualifications, and what makes this role unique..." required><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea>
                            <div id="desc-counter" class="char-counter text-muted"></div>
                        </div>
                    </div>
                    
                    <!-- Company Information -->
                    <div class="form-section">
                        <h5>
                            <i class="fas fa-building"></i>
                            Company Information
                        </h5>
                        
                        <div class="row">
                            <div class="col-lg-8 mb-3">
                                <label class="form-label">Company Name</label>
                                <input type="text" class="form-control" name="company_name" 
                                       placeholder="Enter company name" 
                                       value="<?= htmlspecialchars($_POST['company_name'] ?? '') ?>">
                            </div>
                            <div class="col-lg-4 mb-3">
                                <label class="form-label">Company Logo</label>
                                <div class="upload-zone">
                                    <input type="file" class="form-control" name="company_logo" 
                                           accept="image/*" onchange="previewLogo(this)">
                                    <small class="text-muted d-block mt-2">Max 5MB (JPG, PNG, GIF, WebP)</small>
                                </div>
                                <div id="logo-preview" class="preview-container"></div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Requirements & Timeline -->
                    <div class="form-section">
                        <h5>
                            <i class="fas fa-clipboard-check"></i>
                            Requirements & Timeline
                        </h5>
                        
                        <div class="row">
                            <div class="col-lg-6 mb-3">
                                <label class="form-label">Experience Level</label>
                                <input type="text" class="form-control" name="experience_level" 
                                       placeholder="e.g., 3-5 years, Entry Level, Senior" 
                                       value="<?= htmlspecialchars($_POST['experience_level'] ?? '') ?>">
                            </div>
                            <div class="col-lg-6 mb-3">
                                <label class="form-label">Application Deadline</label>
                                <input type="date" class="form-control" name="application_deadline" 
                                       min="<?= date('Y-m-d', strtotime('+1 day')) ?>"
                                       value="<?= $_POST['application_deadline'] ?? '' ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Required Skills</label>
                            <textarea class="form-control" name="skills_required" rows="3" 
                                      placeholder="List key skills and technologies (e.g., Python, React, AWS, Docker, etc.)"><?= htmlspecialchars($_POST['skills_required'] ?? '') ?></textarea>
                            <small class="text-muted">Separate skills with commas for better formatting</small>
                        </div>
                    </div>
                    
                    <!-- Job Images -->
                    <div class="form-section">
                        <h5>
                            <i class="fas fa-images"></i>
                            Job Images & Visuals
                        </h5>
                        
                        <div class="upload-zone">
                            <input type="file" class="form-control" name="job_images[]" 
                                   accept="image/*" multiple onchange="previewImages(this)">
                            <div class="mt-2">
                                <i class="fas fa-cloud-upload-alt fa-2x text-muted"></i>
                                <p class="text-muted mb-1">Upload office photos, team pictures, or work environment images</p>
                                <small class="text-muted">Multiple files allowed • Max 5MB each • JPG, PNG, GIF, WebP</small>
                            </div>
                        </div>
                        <div id="images-preview" class="preview-container"></div>
                    </div>
                    
                    <!-- Submit Section -->
                    <div class="text-center py-4">
                        <button type="submit" class="btn btn-primary btn-lg px-5 me-3" id="submitBtn">
                            <i class="fas fa-paper-plane me-2"></i>
                            Submit for Approval
                        </button>
                        <a href="dashboard.php" class="btn btn-outline-secondary btn-lg px-5">
                            <i class="fas fa-times me-2"></i>
                            Cancel
                        </a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Image preview functions
function previewLogo(input) {
    const preview = document.getElementById('logo-preview');
    preview.innerHTML = '';
    
    if (input.files && input.files[0]) {
        if (validateImage(input.files[0])) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'image-preview';
                img.alt = 'Company Logo Preview';
                preview.appendChild(img);
            };
            reader.readAsDataURL(input.files[0]);
        }
    }
}

function previewImages(input) {
    const preview = document.getElementById('images-preview');
    preview.innerHTML = '';
    
    if (input.files) {
        Array.from(input.files).forEach((file, index) => {
            if (validateImage(file)) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'image-preview';
                    img.alt = `Job Image Preview ${index + 1}`;
                    preview.appendChild(img);
                };
                reader.readAsDataURL(file);
            }
        });
    }
}

function validateImage(file) {
    const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
    const maxSize = 5 * 1024 * 1024; // 5MB
    
    if (!allowedTypes.includes(file.type)) {
        alert('Invalid file type. Please select JPG, PNG, GIF, or WebP images only.');
        return false;
    }
    
    if (file.size > maxSize) {
        alert('File size too large. Please select images smaller than 5MB.');
        return false;
    }
    
    return true;
}

// Description character counter and validation
document.addEventListener('DOMContentLoaded', function() {
    const descriptionTextarea = document.querySelector('textarea[name="description"]');
    const skillsTextarea = document.querySelector('textarea[name="skills_required"]');
    
    // Create character counter for description
    if (descriptionTextarea) {
        const counterDiv = document.getElementById('desc-counter');
        
        function updateDescriptionCounter() {
            const length = descriptionTextarea.value.length;
            const maxRecommended = 5000;
            const maxText = 65535;
            
            let message = `${length.toLocaleString()} characters`;
            let className = 'char-counter text-muted';
            
            if (length < 50) {
                message += ' (minimum 50 required)';
                className = 'char-counter text-danger';
            } else if (length > maxRecommended) {
                message += ' (very long description)';
                className = 'char-counter text-warning';
            } else if (length > maxText) {
                message += ' (exceeds TEXT limit - upgrade to LONGTEXT required)';
                className = 'char-counter text-danger';
            } else {
                message += ' (good length)';
                className = 'char-counter text-success';
            }
            
            counterDiv.textContent = message;
            counterDiv.className = className;
        }
        
        descriptionTextarea.addEventListener('input', updateDescriptionCounter);
        updateDescriptionCounter();
    }
    
    // Auto-expand textareas
    function autoResize(textarea) {
        textarea.style.height = 'auto';
        textarea.style.height = textarea.scrollHeight + 'px';
    }
    
    [descriptionTextarea, skillsTextarea].forEach(textarea => {
        if (textarea) {
            textarea.addEventListener('input', () => autoResize(textarea));
            autoResize(textarea);
        }
    });
});

// Enhanced form submission with description validation
document.getElementById('jobForm').addEventListener('submit', function(e) {
    const description = document.querySelector('textarea[name="description"]').value;
    const submitBtn = document.getElementById('submitBtn');
    
    // Validate description length
    if (description.length < 50) {
        e.preventDefault();
        alert('Job description must be at least 50 characters long.');
        return;
    }
    
    if (description.length > 65535) {
        const confirmed = confirm('Your description is very long (' + description.length.toLocaleString() + ' characters). It may be truncated if the database column is not LONGTEXT. Continue anyway?');
        if (!confirmed) {
            e.preventDefault();
            return;
        }
    }
    
    // Show loading state
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Submitting...';
    
    console.log('Submitting job post with description length:', description.length);
    
    // Re-enable button after timeout
    setTimeout(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-paper-plane me-2"></i>Submit for Approval';
    }, 10000);
});

// Auto-resize textareas
document.querySelectorAll('textarea').forEach(textarea => {
    textarea.addEventListener('input', function() {
        this.style.height = 'auto';
        this.style.height = (this.scrollHeight) + 'px';
    });
});

// Skills input formatting
const skillsTextarea = document.querySelector('textarea[name="skills_required"]');
if (skillsTextarea) {
    skillsTextarea.addEventListener('blur', function() {
        this.value = this.value.split(',').map(skill => skill.trim()).join(', ');
    });
}
</script>

<!-- Quick Fix Alert -->
<div class="position-fixed bottom-0 end-0 p-3" style="z-index: 1050">
    <div class="alert alert-info alert-dismissible fade show" role="alert">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Quick Database Fix:</strong> If descriptions are being truncated, run this SQL:
        <br><code>ALTER TABLE job_post MODIFY COLUMN description LONGTEXT;</code>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
</div>

</body>
</html>