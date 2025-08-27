<?php
//send_news_email.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'db_connection.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$message = '';
$messageType = '';

// Check database connection
if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}

// Handle AJAX image upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['imageUpload'])) {
    header('Content-Type: application/json');
    
    try {
        $imageDir = "../uploads/email_images/";
        if (!is_dir($imageDir)) {
            mkdir($imageDir, 0777, true);
        }

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $fileType = $_FILES['imageUpload']['type'];
        
        if (!in_array($fileType, $allowedTypes)) {
            throw new Exception('Invalid file type. Only JPEG, PNG, GIF, and WebP are allowed.');
        }
        
        if ($_FILES['imageUpload']['size'] > 5 * 1024 * 1024) { // 5MB limit
            throw new Exception('File size too large. Maximum 5MB allowed.');
        }

        $fileName = uniqid() . '_' . basename($_FILES['imageUpload']['name']);
        $imagePath = $imageDir . $fileName;
        
        if (move_uploaded_file($_FILES['imageUpload']['tmp_name'], $imagePath)) {
            echo json_encode([
                "success" => true,
                "url" => "https://sanixtech.in/uploads/email_images/" . $fileName
            ]);
        } else {
            throw new Exception('Failed to save uploaded file.');
        }
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            "success" => false,
            "error" => $e->getMessage()
        ]);
    }
    exit;
}

// Handle AJAX email sending
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_action'])) {
    header('Content-Type: application/json');
    
    try {
        $subject = trim($_POST['subject']);
        $emailContent = $_POST['message'];
        $recipientType = $_POST['recipient_type'];
        $selectedUsers = isset($_POST['selected_users']) ? $_POST['selected_users'] : [];
        
        if (empty($subject) || empty($emailContent)) {
            throw new Exception('Subject and message are required.');
        }
        
        $emailsSent = 0;
        $totalEmails = 0;
        
        if ($recipientType === 'all') {
            // Send to all users
            $sql = "SELECT email, name FROM users WHERE email IS NOT NULL AND email != ''";
            $result = $conn->query($sql);
            
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    if (sendEmail($row['email'], $row['name'], $subject, $emailContent)) {
                        $emailsSent++;
                    }
                    $totalEmails++;
                }
            }
        } elseif ($recipientType === 'selected' && !empty($selectedUsers)) {
            // Send to selected users
            $placeholders = str_repeat('?,', count($selectedUsers) - 1) . '?';
            $sql = "SELECT email, name FROM users WHERE user_id IN ($placeholders) AND email IS NOT NULL AND email != ''";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param(str_repeat('i', count($selectedUsers)), ...$selectedUsers);
            $stmt->execute();
            $result = $stmt->get_result();
            
            while ($row = $result->fetch_assoc()) {
                if (sendEmail($row['email'], $row['name'], $subject, $emailContent)) {
                    $emailsSent++;
                }
                $totalEmails++;
            }
            $stmt->close();
        } else {
            throw new Exception('No valid recipients selected.');
        }
        
        // Log email activity
        $logSql = "INSERT INTO email_logs (admin_id, subject, recipient_type, recipients_count, sent_count, created_at) VALUES (?, ?, ?, ?, ?, NOW())";
        $logStmt = $conn->prepare($logSql);
        if ($logStmt) {
            $logStmt->bind_param("issii", $_SESSION['user_id'], $subject, $recipientType, $totalEmails, $emailsSent);
            $logStmt->execute();
            $logStmt->close();
        }
        
        echo json_encode([
            'success' => true,
            'message' => "Emails sent successfully! ({$emailsSent}/{$totalEmails} delivered)",
            'sent_count' => $emailsSent,
            'total_count' => $totalEmails
        ]);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ]);
    }
    exit;
}

// Function to send email
function sendEmail($to, $name, $subject, $content) {
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: Sanix Tech <info@sanixtech.in>\r\n";
    $headers .= "Reply-To: info@sanixtech.in\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";
    
    $emailTemplate = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>{$subject}</title>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center; }
            .content { padding: 20px; background: #f9f9f9; }
            .footer { background: #333; color: white; padding: 15px; text-align: center; font-size: 12px; }
            img { max-width: 100%; height: auto; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Sanix Tech</h1>
            </div>
            <div class='content'>
                <p>Hello " . htmlspecialchars($name) . ",</p>
                {$content}
            </div>
            <div class='footer'>
                <p>&copy; 2024 Sanix Tech. All rights reserved.</p>
                <p>This email was sent from our admin panel.</p>
            </div>
        </div>
    </body>
    </html>";
    
    return mail($to, $subject, $emailTemplate, $headers);
}

// Fetch all users for selection
$usersQuery = "SELECT user_id, name, email, role FROM users WHERE email IS NOT NULL AND email != '' ORDER BY name ASC";
$usersResult = $conn->query($usersQuery);

// Fetch recent email logs
$logsQuery = "SELECT * FROM email_logs ORDER BY created_at DESC LIMIT 10";
$logsResult = $conn->query($logsQuery);
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Email - Admin Dashboard</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Quill Rich Text Editor -->
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/admin_styleone.css">
    
    <style>
        .email-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            backdrop-filter: blur(10px);
            margin-bottom: 2rem;
        }
        
        .email-card .card-header {
            background: linear-gradient(135deg, rgba(102, 126, 234, 0.2), rgba(118, 75, 162, 0.2));
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px 15px 0 0 !important;
        }
        
        .page-header {
            background: linear-gradient(135deg, rgba(13, 110, 253, 0.1), rgba(102, 16, 242, 0.1));
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .recipient-card {
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
        }
        
        .user-select-container {
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            padding: 1rem;
            background: rgba(0, 0, 0, 0.1);
        }
        
        .user-checkbox {
            margin-bottom: 0.75rem;
            padding: 0.5rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 6px;
            transition: background 0.2s;
        }
        
        .user-checkbox:hover {
            background: rgba(255, 255, 255, 0.1);
        }
        
        .ql-editor {
            min-height: 200px;
            color: #fff !important;
            background: rgba(0, 0, 0, 0.3);
        }
        
        .ql-toolbar {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.2) !important;
        }
        
        .ql-container {
            border-color: rgba(255, 255, 255, 0.2) !important;
        }
        
        .btn-send {
            background: linear-gradient(135deg, #28a745, #20c997);
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-send:hover {
            background: linear-gradient(135deg, #218838, #1aa179);
            transform: translateY(-1px);
        }
        
        .toast-container {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1060;
        }
        
        .stats-row {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }
        
        .stat-card {
            flex: 1;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            padding: 1.5rem;
            text-align: center;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .stat-number {
            font-size: 2rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }
        
        .preview-modal .modal-content {
            background: #fff;
            color: #333;
        }
        
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.8);
            display: none;
            align-items: center;
            justify-content: center;
            border-radius: 15px;
            z-index: 1000;
        }
        
        .table-dark {
            background: rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
        }
        
        @media (max-width: 768px) {
            .stats-row {
                flex-direction: column;
            }
        }
    </style>
</head>

<body>
<div class="wrapper">
    <!-- Sidebar -->
    <aside id="sidebar" class="js-sidebar">
        <?php include 'admin_menu.php'; ?>
    </aside>
    
    <!-- Main Content -->
    <div class="main">
        <!-- Top Navigation -->
        <?php include 'admin_navbar.php'; ?>
        
        <main class="content px-3 py-4">
            <div class="container-fluid">
                <!-- Page Header -->
                <div class="page-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h2 class="mb-1">
                                <i class="fas fa-envelope me-2"></i>Email System
                            </h2>
                            <p class="text-muted mb-0">Send newsletters and communications to users</p>
                        </div>
                        <div>
                            <button class="btn btn-outline-info me-2" data-bs-toggle="modal" data-bs-target="#previewModal">
                                <i class="fas fa-eye me-2"></i>Preview Email
                            </button>
                            <a href="dashboard.php" class="btn btn-outline-secondary">
                                <i class="fas fa-home me-2"></i>Dashboard
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Toast Container -->
                <div class="toast-container"></div>
                
                <!-- Statistics -->
                <div class="stats-row">
                    <div class="stat-card">
                        <div class="stat-number text-info">
                            <?php echo $usersResult ? $usersResult->num_rows : 0; ?>
                        </div>
                        <div class="text-muted">Total Users</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number text-success" id="selected-count">0</div>
                        <div class="text-muted">Selected Recipients</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number text-warning">
                            <?php echo $logsResult ? $logsResult->num_rows : 0; ?>
                        </div>
                        <div class="text-muted">Recent Campaigns</div>
                    </div>
                </div>
                
                <!-- Email Form -->
                <div class="email-card card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-paper-plane me-2"></i>Compose Email
                        </h5>
                    </div>
                    <div class="card-body position-relative">
                        <div class="loading-overlay" id="loadingOverlay">
                            <div class="text-center text-white">
                                <div class="spinner-border mb-3" style="width: 3rem; height: 3rem;"></div>
                                <p>Sending emails...</p>
                            </div>
                        </div>
                        
                        <form id="emailForm">
                            <!-- Subject -->
                            <div class="mb-4">
                                <label for="subject" class="form-label text-info">
                                    <i class="fas fa-heading me-2"></i>Subject Line
                                </label>
                                <input type="text" class="form-control" id="subject" name="subject" 
                                       placeholder="Enter email subject..." required>
                            </div>
                            
                            <!-- Recipients -->
                            <div class="recipient-card">
                                <h6 class="text-info mb-3">
                                    <i class="fas fa-users me-2"></i>Select Recipients
                                </h6>
                                
                                <div class="row mb-3">
                                    <div class="col-md-6">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="recipient_type" 
                                                   id="all_users" value="all" checked>
                                            <label class="form-check-label" for="all_users">
                                                <i class="fas fa-globe me-1"></i>Send to All Users
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="radio" name="recipient_type" 
                                                   id="selected_users" value="selected">
                                            <label class="form-check-label" for="selected_users">
                                                <i class="fas fa-user-friends me-1"></i>Select Specific Users
                                            </label>
                                        </div>
                                    </div>
                                </div>
                                
                                <div id="userSelectionContainer" style="display: none;">
                                    <div class="row mb-3">
                                        <div class="col-md-6">
                                            <input type="text" class="form-control" id="userSearch" 
                                                   placeholder="Search users...">
                                        </div>
                                        <div class="col-md-6 text-end">
                                            <button type="button" class="btn btn-outline-info btn-sm" onclick="selectAllUsers()">
                                                Select All
                                            </button>
                                            <button type="button" class="btn btn-outline-secondary btn-sm ms-2" onclick="deselectAllUsers()">
                                                Deselect All
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="user-select-container" id="userList">
                                        <?php if ($usersResult && $usersResult->num_rows > 0): ?>
                                            <?php while ($user = $usersResult->fetch_assoc()): ?>
                                                <div class="user-checkbox" data-name="<?php echo strtolower($user['name'] . ' ' . $user['email']); ?>">
                                                    <div class="form-check">
                                                        <input class="form-check-input user-select" type="checkbox" 
                                                               value="<?php echo $user['user_id']; ?>" 
                                                               id="user_<?php echo $user['user_id']; ?>">
                                                        <label class="form-check-label" for="user_<?php echo $user['user_id']; ?>">
                                                            <strong><?php echo htmlspecialchars($user['name']); ?></strong>
                                                            <br><small class="text-muted"><?php echo htmlspecialchars($user['email']); ?></small>
                                                            <span class="badge bg-<?php echo $user['role'] === 'admin' ? 'danger' : 'info'; ?> ms-2">
                                                                <?php echo ucfirst($user['role']); ?>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>
                                            <?php endwhile; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Message Editor -->
                            <div class="mb-4">
                                <label class="form-label text-info">
                                    <i class="fas fa-edit me-2"></i>Email Content
                                </label>
                                <div id="emailEditor"></div>
                                <small class="text-muted">Use the toolbar to format text and insert images</small>
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="d-flex justify-content-between">
                                <div>
                                    <button type="button" class="btn btn-outline-secondary" onclick="clearForm()">
                                        <i class="fas fa-trash me-2"></i>Clear Form
                                    </button>
                                    <button type="button" class="btn btn-outline-info ms-2" data-bs-toggle="modal" data-bs-target="#previewModal">
                                        <i class="fas fa-eye me-2"></i>Preview
                                    </button>
                                </div>
                                <button type="submit" class="btn btn-send">
                                    <i class="fas fa-paper-plane me-2"></i>Send Email
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
                
                <!-- Recent Email Logs -->
                <?php if ($logsResult && $logsResult->num_rows > 0): ?>
                <div class="email-card card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            <i class="fas fa-history me-2"></i>Recent Email Campaigns
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-dark table-hover">
                                <thead>
                                    <tr>
                                        <th>Subject</th>
                                        <th>Recipients</th>
                                        <th>Sent/Total</th>
                                        <th>Date</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($log = $logsResult->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($log['subject']); ?></td>
                                            <td>
                                                <span class="badge bg-info">
                                                    <?php echo ucfirst($log['recipient_type']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <span class="<?php echo $log['sent_count'] == $log['recipients_count'] ? 'text-success' : 'text-warning'; ?>">
                                                    <?php echo $log['sent_count']; ?>/<?php echo $log['recipients_count']; ?>
                                                </span>
                                            </td>
                                            <td><?php echo date('M d, Y H:i', strtotime($log['created_at'])); ?></td>
                                            <td>
                                                <?php if ($log['sent_count'] == $log['recipients_count']): ?>
                                                    <span class="badge bg-success">Complete</span>
                                                <?php else: ?>
                                                    <span class="badge bg-warning">Partial</span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade preview-modal" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Email Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="previewContent">
                <!-- Preview content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<!-- Quill Rich Text Editor -->
<script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

<script>
// Initialize Quill editor
let quill = new Quill('#emailEditor', {
    theme: 'snow',
    modules: {
        toolbar: [
            [{ 'header': [1, 2, 3, false] }],
            ['bold', 'italic', 'underline', 'strike'],
            [{ 'color': [] }, { 'background': [] }],
            [{ 'align': [] }],
            [{ 'list': 'ordered'}, { 'list': 'bullet' }],
            ['blockquote', 'code-block'],
            ['link', 'image'],
            ['clean']
        ]
    }
});

// Custom image handler for Quill
quill.getModule('toolbar').addHandler('image', function() {
    const input = document.createElement('input');
    input.setAttribute('type', 'file');
    input.setAttribute('accept', 'image/*');
    input.click();
    
    input.onchange = function() {
        const file = input.files[0];
        if (file) {
            const formData = new FormData();
            formData.append('imageUpload', file);
            
            // Show loading state
            showToast('info', 'Uploading image...');
            
            fetch(window.location.href, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const range = quill.getSelection();
                    quill.insertEmbed(range.index, 'image', data.url);
                    showToast('success', 'Image uploaded successfully!');
                } else {
                    showToast('error', 'Image upload failed: ' + data.error);
                }
            })
            .catch(error => {
                showToast('error', 'Upload error: ' + error.message);
            });
        }
    };
});

// Handle recipient type change
document.querySelectorAll('input[name="recipient_type"]').forEach(radio => {
    radio.addEventListener('change', function() {
        const container = document.getElementById('userSelectionContainer');
        if (this.value === 'selected') {
            container.style.display = 'block';
        } else {
            container.style.display = 'none';
        }
        updateSelectedCount();
    });
});

// Handle user selection
document.querySelectorAll('.user-select').forEach(checkbox => {
    checkbox.addEventListener('change', updateSelectedCount);
});

// Update selected count
function updateSelectedCount() {
    const recipientType = document.querySelector('input[name="recipient_type"]:checked').value;
    const selectedCount = recipientType === 'all' ? 
        <?php echo $usersResult ? $usersResult->num_rows : 0; ?> : 
        document.querySelectorAll('.user-select:checked').length;
    
    document.getElementById('selected-count').textContent = selectedCount;
}

// User search functionality
document.getElementById('userSearch').addEventListener('input', function() {
    const searchTerm = this.value.toLowerCase();
    const userBoxes = document.querySelectorAll('.user-checkbox');
    
    userBoxes.forEach(box => {
        const searchData = box.getAttribute('data-name');
        if (searchData.includes(searchTerm)) {
            box.style.display = 'block';
        } else {
            box.style.display = 'none';
        }
    });
});

// Select/Deselect all users
function selectAllUsers() {
    document.querySelectorAll('.user-select').forEach(checkbox => {
        if (checkbox.closest('.user-checkbox').style.display !== 'none') {
            checkbox.checked = true;
        }
    });
    updateSelectedCount();
}

function deselectAllUsers() {
    document.querySelectorAll('.user-select').forEach(checkbox => {
        checkbox.checked = false;
    });
    updateSelectedCount();
}

// Clear form
function clearForm() {
    if (confirm('Are you sure you want to clear the form?')) {
        document.getElementById('emailForm').reset();
        quill.setText('');
        document.getElementById('userSelectionContainer').style.display = 'none';
        deselectAllUsers();
        updateSelectedCount();
    }
}
// Preview email
document.getElementById('previewModal').addEventListener('show.bs.modal', function() {
    const subject = document.getElementById('subject').value;
    const content = quill.root.innerHTML;
    
    const previewHTML = `
        <div style="max-width: 600px; margin: 0 auto; font-family: Arial, sans-serif;">
            <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 20px; text-align: center;">
                <h1>Sanix Tech</h1>
            </div>
            <div style="padding: 20px; background: #f9f9f9; color: #333;">
                <h2 style="color: #333; margin-bottom: 20px;">${subject || 'No Subject'}</h2>
                <p>Hello [User Name],</p>
                ${content || '<p>No content added yet.</p>'}
            </div>
            <div style="background: #333; color: white; padding: 15px; text-align: center; font-size: 12px;">
                <p>&copy; 2024 Sanix Tech. All rights reserved.</p>
                <p>This email was sent from our admin panel.</p>
            </div>
        </div>
    `;
    
    document.getElementById('previewContent').innerHTML = previewHTML;
});

// Handle form submission
document.getElementById('emailForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const subject = document.getElementById('subject').value.trim();
    const content = quill.root.innerHTML.trim();
    const recipientType = document.querySelector('input[name="recipient_type"]:checked').value;
    
    // Validation
    if (!subject) {
        showToast('error', 'Please enter a subject line.');
        return;
    }
    
    if (!content || content === '<p><br></p>') {
        showToast('error', 'Please add some content to your email.');
        return;
    }
    
    // Get selected users if needed
    let selectedUsers = [];
    if (recipientType === 'selected') {
        selectedUsers = Array.from(document.querySelectorAll('.user-select:checked')).map(cb => cb.value);
        if (selectedUsers.length === 0) {
            showToast('error', 'Please select at least one user to send the email to.');
            return;
        }
    }
    
    // Show loading overlay
    document.getElementById('loadingOverlay').style.display = 'flex';
    
    // Prepare form data
    const formData = new FormData();
    formData.append('ajax_action', 'send_email');
    formData.append('subject', subject);
    formData.append('message', content);
    formData.append('recipient_type', recipientType);
    
    // Add selected users if any
    if (selectedUsers.length > 0) {
        selectedUsers.forEach(userId => {
            formData.append('selected_users[]', userId);
        });
    }
    
    // Send AJAX request
    fetch(window.location.href, {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('loadingOverlay').style.display = 'none';
        
        if (data.success) {
            showToast('success', data.message);
            
            // Ask if user wants to clear the form
            setTimeout(() => {
                if (confirm('Email sent successfully! Would you like to clear the form for a new email?')) {
                    clearForm();
                }
            }, 2000);
            
            // Refresh the page to update email logs
            setTimeout(() => {
                window.location.reload();
            }, 3000);
        } else {
            showToast('error', 'Failed to send email: ' + data.message);
        }
    })
    .catch(error => {
        document.getElementById('loadingOverlay').style.display = 'none';
        showToast('error', 'Network error: ' + error.message);
        console.error('Email sending error:', error);
    });
});

// Toast notification function
function showToast(type, message) {
    const toastContainer = document.querySelector('.toast-container');
    const toastId = 'toast_' + Date.now();
    
    const bgClass = {
        'success': 'bg-success',
        'error': 'bg-danger',
        'warning': 'bg-warning',
        'info': 'bg-info'
    }[type] || 'bg-info';
    
    const iconClass = {
        'success': 'fa-check-circle',
        'error': 'fa-exclamation-circle',
        'warning': 'fa-exclamation-triangle',
        'info': 'fa-info-circle'
    }[type] || 'fa-info-circle';
    
    const toastHTML = `
        <div id="${toastId}" class="toast ${bgClass} text-white" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="5000">
            <div class="toast-header ${bgClass} text-white border-0">
                <i class="fas ${iconClass} me-2"></i>
                <strong class="me-auto">${type.charAt(0).toUpperCase() + type.slice(1)}</strong>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
            <div class="toast-body">
                ${message}
            </div>
        </div>
    `;
    
    toastContainer.insertAdjacentHTML('beforeend', toastHTML);
    
    const toastElement = document.getElementById(toastId);
    const toast = new bootstrap.Toast(toastElement);
    toast.show();
    
    // Remove toast element after it's hidden
    toastElement.addEventListener('hidden.bs.toast', function() {
        toastElement.remove();
    });
}

// Initialize selected count on page load
document.addEventListener('DOMContentLoaded', function() {
    updateSelectedCount();
    
    // Auto-save draft functionality (optional)
    let autoSaveTimer;
    
    function autoSaveDraft() {
        const subject = document.getElementById('subject').value;
        const content = quill.root.innerHTML;
        
        if (subject || content !== '<p><br></p>') {
            localStorage.setItem('email_draft', JSON.stringify({
                subject: subject,
                content: content,
                timestamp: Date.now()
            }));
        }
    }
    
    function loadDraft() {
        const draft = localStorage.getItem('email_draft');
        if (draft) {
            try {
                const draftData = JSON.parse(draft);
                const oneHour = 60 * 60 * 1000; // 1 hour in milliseconds
                
                // Only load draft if it's less than 1 hour old
                if (Date.now() - draftData.timestamp < oneHour) {
                    if (confirm('A recent draft was found. Would you like to restore it?')) {
                        document.getElementById('subject').value = draftData.subject;
                        quill.root.innerHTML = draftData.content;
                        showToast('info', 'Draft restored successfully!');
                    }
                } else {
                    localStorage.removeItem('email_draft');
                }
            } catch (e) {
                localStorage.removeItem('email_draft');
            }
        }
    }
    
    // Load draft on page load
    loadDraft();
    
    // Auto-save every 30 seconds
    setInterval(autoSaveDraft, 30000);
    
    // Save draft when user types
    document.getElementById('subject').addEventListener('input', function() {
        clearTimeout(autoSaveTimer);
        autoSaveTimer = setTimeout(autoSaveDraft, 2000);
    });
    
    quill.on('text-change', function() {
        clearTimeout(autoSaveTimer);
        autoSaveTimer = setTimeout(autoSaveDraft, 2000);
    });
});

// Clear draft when form is successfully sent or manually cleared
function clearDraft() {
    localStorage.removeItem('email_draft');
}

// Update the clearForm function to also clear draft
const originalClearForm = clearForm;
clearForm = function() {
    if (confirm('Are you sure you want to clear the form?')) {
        document.getElementById('emailForm').reset();
        quill.setText('');
        document.getElementById('userSelectionContainer').style.display = 'none';
        deselectAllUsers();
        updateSelectedCount();
        clearDraft();
        showToast('info', 'Form cleared successfully!');
    }
};

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl+Enter to send email
    if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
        e.preventDefault();
        document.getElementById('emailForm').dispatchEvent(new Event('submit'));
    }
    
    // Ctrl+S to save draft
    if ((e.ctrlKey || e.metaKey) && e.key === 's') {
        e.preventDefault();
        autoSaveDraft();
        showToast('info', 'Draft saved!');
    }
    
    // Escape to close modals
    if (e.key === 'Escape') {
        const openModal = document.querySelector('.modal.show');
        if (openModal) {
            bootstrap.Modal.getInstance(openModal).hide();
        }
    }
});

// Add character counter for subject line
document.getElementById('subject').addEventListener('input', function() {
    const maxLength = 78; // Recommended email subject length
    const currentLength = this.value.length;
    
    // Remove existing counter
    const existingCounter = this.parentNode.querySelector('.char-counter');
    if (existingCounter) {
        existingCounter.remove();
    }
    
    // Add new counter
    const counter = document.createElement('small');
    counter.className = 'char-counter d-block mt-1';
    counter.style.color = currentLength > maxLength ? '#dc3545' : '#6c757d';
    counter.textContent = `${currentLength}/${maxLength} characters`;
    
    if (currentLength > maxLength) {
        counter.innerHTML += ' <span class="text-warning"><i class="fas fa-exclamation-triangle ms-1"></i> Too long</span>';
    }
    
    this.parentNode.appendChild(counter);
});

// Add word counter for email content
quill.on('text-change', function() {
    const text = quill.getText();
    const wordCount = text.trim().split(/\s+/).filter(word => word.length > 0).length;
    
    // Remove existing counter
    const existingCounter = document.querySelector('.word-counter');
    if (existingCounter) {
        existingCounter.remove();
    }
    
    // Add new counter
    const counter = document.createElement('small');
    counter.className = 'word-counter text-muted d-block mt-2';
    counter.textContent = `${wordCount} words`;
    
    document.getElementById('emailEditor').parentNode.appendChild(counter);
});

// Handle browser back/forward buttons
window.addEventListener('beforeunload', function(e) {
    const subject = document.getElementById('subject').value;
    const content = quill.root.innerHTML;
    
    if (subject || content !== '<p><br></p>') {
        e.preventDefault();
        e.returnValue = 'You have unsaved changes. Are you sure you want to leave?';
        return e.returnValue;
    }
});

console.log('Email system JavaScript loaded successfully!');