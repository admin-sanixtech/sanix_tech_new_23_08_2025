<?php
header('Content-Type: application/json');
include 'db_connection.php';

// Check if request is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Validate required fields
$required_fields = ['job_id', 'name', 'email', 'phone', 'job_email'];
foreach ($required_fields as $field) {
    if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
        echo json_encode(['success' => false, 'message' => "Field '{$field}' is required"]);
        exit;
    }
}

// Sanitize and validate input
$job_id = (int)$_POST['job_id'];
$name = trim($_POST['name']);
$email = trim($_POST['email']);
$phone = trim($_POST['phone']);
$job_email = trim($_POST['job_email']);
$cover_letter = isset($_POST['cover_letter']) ? trim($_POST['cover_letter']) : '';

// Email validation
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email address']);
    exit;
}

// Phone validation
if (!preg_match('/^[\+]?[1-9][\d]{0,15}$/', preg_replace('/[\s\-\(\)]/', '', $phone))) {
    echo json_encode(['success' => false, 'message' => 'Invalid phone number']);
    exit;
}

try {
    // Check if job exists and is approved
    $jobCheckSql = "SELECT id, title, role, company_name FROM job_post WHERE id = ? AND is_approved = 1";
    $jobCheckStmt = $conn->prepare($jobCheckSql);
    $jobCheckStmt->bind_param("i", $job_id);
    $jobCheckStmt->execute();
    $jobResult = $jobCheckStmt->get_result();
    
    if ($jobResult->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Job not found or not available']);
        exit;
    }
    
    $jobData = $jobResult->fetch_assoc();
    
    // Handle file upload
    $resume_path = '';
    if (isset($_FILES['resume']) && $_FILES['resume']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/resumes/';
        
        // Create directory if it doesn't exist
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        
        $file = $_FILES['resume'];
        $file_extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $allowed_extensions = ['pdf', 'doc', 'docx'];
        
        // Validate file extension
        if (!in_array($file_extension, $allowed_extensions)) {
            echo json_encode(['success' => false, 'message' => 'Only PDF, DOC, and DOCX files are allowed']);
            exit;
        }
        
        // Validate file size (5MB max)
        if ($file['size'] > 5 * 1024 * 1024) {
            echo json_encode(['success' => false, 'message' => 'File size must be less than 5MB']);
            exit;
        }
        
        // Generate unique filename
        $filename = 'resume_' . $job_id . '_' . time() . '_' . uniqid() . '.' . $file_extension;
        $resume_path = $upload_dir . $filename;
        
        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $resume_path)) {
            echo json_encode(['success' => false, 'message' => 'Failed to upload resume']);
            exit;
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Resume is required']);
        exit;
    }
    
    // Insert application into database
    $insertSql = "INSERT INTO job_applications (job_id, applicant_name, applicant_email, applicant_phone, resume_path, cover_letter, applied_at, status) VALUES (?, ?, ?, ?, ?, ?, NOW(), 'pending')";
    $insertStmt = $conn->prepare($insertSql);
    $insertStmt->bind_param("isssss", $job_id, $name, $email, $phone, $resume_path, $cover_letter);
    
    if ($insertStmt->execute()) {
        $application_id = $conn->insert_id;
        
        // Send email notification to employer (optional)
        sendApplicationNotification($job_email, $jobData, [
            'name' => $name,
            'email' => $email,
            'phone' => $phone,
            'cover_letter' => $cover_letter,
            'resume_path' => $resume_path
        ]);
        
        // Send confirmation email to applicant (optional)
        sendApplicationConfirmation($email, $name, $jobData);
        
        echo json_encode([
            'success' => true, 
            'message' => 'Application submitted successfully!',
            'application_id' => $application_id
        ]);
    } else {
        // Delete uploaded file if database insert failed
        if (!empty($resume_path) && file_exists($resume_path)) {
            unlink($resume_path);
        }
        
        echo json_encode(['success' => false, 'message' => 'Failed to submit application']);
    }
    
} catch (Exception $e) {
    // Delete uploaded file if there was an error
    if (!empty($resume_path) && file_exists($resume_path)) {
        unlink($resume_path);
    }
    
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

/**
 * Send email notification to employer
 */
function sendApplicationNotification($employer_email, $job_data, $applicant_data) {
    $subject = "New Job Application for " . $job_data['title'];
    
    $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .header { background: #6366f1; color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; }
            .applicant-info { background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 15px 0; }
            .footer { background: #f1f3f4; padding: 15px; text-align: center; font-size: 12px; color: #666; }
        </style>
    </head>
    <body>
        <div class='header'>
            <h2>New Job Application</h2>
        </div>
        <div class='content'>
            <h3>Job: {$job_data['title']} ({$job_data['role']})</h3>
            <p>You have received a new application for the above position.</p>
            
            <div class='applicant-info'>
                <h4>Applicant Details:</h4>
                <p><strong>Name:</strong> {$applicant_data['name']}</p>
                <p><strong>Email:</strong> {$applicant_data['email']}</p>
                <p><strong>Phone:</strong> {$applicant_data['phone']}</p>
                " . (!empty($applicant_data['cover_letter']) ? "<p><strong>Cover Letter:</strong><br>" . nl2br($applicant_data['cover_letter']) . "</p>" : "") . "
            </div>
            
            <p>The applicant's resume has been attached to this email.</p>
            <p>Please review the application and contact the candidate if suitable.</p>
        </div>
        <div class='footer'>
            <p>This email was sent from Sanix Technologies Job Portal</p>
        </div>
    </body>
    </html>
    ";
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= "From: noreply@sanixtechnologies.com" . "\r\n";
    
    // Send email (you might want to use PHPMailer for better email handling)
    mail($employer_email, $subject, $message, $headers);
}

/**
 * Send confirmation email to applicant
 */
function sendApplicationConfirmation($applicant_email, $applicant_name, $job_data) {
    $subject = "Application Confirmation - " . $job_data['title'];
    
    $message = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }