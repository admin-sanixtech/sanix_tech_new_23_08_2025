

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enhanced Email System - Admin Dashboard</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- TinyMCE Editor -->
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/admin_styleone.css">
    <link rel="stylesheet" href="css/admin_news_emails.css">
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
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h2 class="mb-1">
                                <i class="fas fa-envelope me-2"></i>Enhanced Email System
                            </h2>
                            <p class="text-muted mb-0">Create, review, and send newsletters with approval workflow</p>
                        </div>
                        <div>
                            <button class="btn btn-outline-info me-2" data-bs-toggle="modal" data-bs-target="#previewModal">
                                <i class="fas fa-eye me-2"></i>Preview
                            </button>
                            <a href="dashboard.php" class="btn btn-outline-secondary">
                                <i class="fas fa-home me-2"></i>Dashboard
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Toast Container -->
                <div class="toast-container"></div>
                
                <!-- Tabs -->
                <ul class="nav nav-tabs nav-fill mb-4" id="emailTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="compose-tab" data-bs-toggle="tab" data-bs-target="#compose" type="button" role="tab">
                            <i class="fas fa-edit me-2"></i>Compose Email
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="drafts-tab" data-bs-toggle="tab" data-bs-target="#drafts" type="button" role="tab">
                            <i class="fas fa-file-alt me-2"></i>My Drafts
                        </button>
                    </li>
                    <?php if ($pendingResult && $pendingResult->num_rows > 0): ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="approvals-tab" data-bs-toggle="tab" data-bs-target="#approvals" type="button" role="tab">
                            <i class="fas fa-clock me-2"></i>Pending Approvals
                            <span class="badge bg-warning ms-1"><?php echo $pendingResult->num_rows; ?></span>
                        </button>
                    </li>
                    <?php endif; ?>
                </ul>
                
                <!-- Tab Content -->
                <div class="tab-content" id="emailTabsContent">
                    <!-- Compose Tab -->
                    <div class="tab-pane fade show active" id="compose" role="tabpanel">
                        <div class="email-card card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-paper-plane me-2"></i>Compose New Email
                                </h5>
                            </div>
                            <div class="card-body">
                                <form id="emailForm">
                                    <!-- Subject -->
                                    <div class="mb-4">
                                        <label for="subject" class="form-label text-info">
                                            <i class="fas fa-heading me-2"></i>Subject Line
                                        </label>
                                        <input type="text" class="form-control" id="subject" name="subject" 
                                               placeholder="Enter email subject..." required>
                                        <div id="subjectWarning" class="text-warning mt-1" style="display: none;">
                                            <i class="fas fa-exclamation-triangle me-1"></i>
                                            This subject may require approval
                                        </div>
                                    </div>
                                    
                                    <!-- Recipients -->
                                    <div class="mb-4">
                                        <label class="form-label text-info">
                                            <i class="fas fa-users me-2"></i>Recipients
                                        </label>
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="recipient_type" 
                                                           id="all_users" value="all" checked>
                                                    <label class="form-check-label" for="all_users">
                                                        Send to All Users (<?php echo $usersResult ? $usersResult->num_rows : 0; ?>)
                                                    </label>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="recipient_type" 
                                                           id="selected_users" value="selected">
                                                    <label class="form-check-label" for="selected_users">
                                                        Select Specific Users
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div id="userSelectionContainer" style="display: none;" class="mt-3">
                                            <div class="row mb-3">
                                                <div class="col-md-8">
                                                    <input type="text" class="form-control" id="userSearch" 
                                                           placeholder="Search users...">
                                                </div>
                                                <div class="col-md-4 text-end">
                                                    <button type="button" class="btn btn-outline-info btn-sm me-1" onclick="selectAllUsers()">
                                                        Select All
                                                    </button>
                                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="deselectAllUsers()">
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
                                    
                                    <!-- Content Editor -->
                                    <div class="mb-4">
                                        <label class="form-label text-info">
                                            <i class="fas fa-edit me-2"></i>Email Content
                                        </label>
                                        <textarea id="emailContent" name="content" style="height: 400px;"></textarea>
                                        <div id="contentWarning" class="flagged-content mt-2" style="display: none;">
                                            <h6 class="text-warning mb-2">
                                                <i class="fas fa-exclamation-triangle me-2"></i>Content Flagged for Review
                                            </h6>
                                            <p class="mb-2">The following content may require admin approval:</p>
                                            <div id="flaggedItems"></div>
                                        </div>
                                    </div>
                                    
                                    <!-- Action Buttons -->
                                    <div class="d-flex justify-content-between flex-wrap gap-2">
                                        <div>
                                            <button type="button" class="btn btn-outline-secondary" onclick="clearForm()">
                                                <i class="fas fa-trash me-2"></i>Clear Form
                                            </button>
                                            <button type="button" class="btn btn-outline-info ms-2" data-bs-toggle="modal" data-bs-target="#previewModal">
                                                <i class="fas fa-eye me-2"></i>Preview
                                            </button>
                                        </div>
                                        <div>
                                            <button type="button" class="btn btn-outline-warning me-2" onclick="saveDraft()">
                                                <i class="fas fa-save me-2"></i>Save Draft
                                            </button>
                                            <button type="button" class="btn btn-send" onclick="submitForApproval()">
                                                <i class="fas fa-paper-plane me-2"></i>Submit for Review
                                            </button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Drafts Tab -->
                    <div class="tab-pane fade" id="drafts" role="tabpanel">
                        <div class="email-card card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-file-alt me-2"></i>My Email Drafts
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php if ($draftsResult && $draftsResult->num_rows > 0): ?>
                                    <?php while ($draft = $draftsResult->fetch_assoc()): ?>
                                        <div class="draft-item <?php echo $draft['status']; ?>">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <div>
                                                    <h6 class="mb-1"><?php echo htmlspecialchars($draft['subject']); ?></h6>
                                                    <small class="text-muted">
                                                        Created: <?php echo date('M d, Y H:i', strtotime($draft['created_at'])); ?>
                                                        by <?php echo htmlspecialchars($draft['creator_name']); ?>
                                                    </small>
                                                </div>
                                                <div class="text-end">
                                                    <?php
                                                    $statusColors = [
                                                        'draft' => 'secondary',
                                                        'pending_approval' => 'warning',
                                                        'approved' => 'success',
                                                        'rejected' => 'danger',
                                                        'sent' => 'info'
                                                    ];
                                                    $statusColor = $statusColors[$draft['status']] ?? 'secondary';
                                                    ?>
                                                    <span class="status-badge bg-<?php echo $statusColor; ?>">
                                                        <?php echo ucwords(str_replace('_', ' ', $draft['status'])); ?>
                                                    </span>
                                                </div>
                                            </div>
                                            
                                            <p class="text-muted mb-3">
                                                Recipients: <?php echo ucfirst($draft['recipient_type']); ?>
                                                <?php if ($draft['recipient_type'] === 'selected'): ?>
                                                    (<?php echo count(json_decode($draft['selected_users'], true) ?? []); ?> users)
                                                <?php endif; ?>
                                            </p>
                                            
                                            <?php if ($draft['status'] === 'rejected' && $draft['approval_notes']): ?>
                                                <div class="alert alert-danger py-2">
                                                    <small>
                                                        <strong>Rejection reason:</strong> <?php echo htmlspecialchars($draft['approval_notes']); ?>
                                                    </small>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <?php if ($draft['status'] === 'approved' && $draft['approver_name']): ?>
                                                <div class="alert alert-success py-2">
                                                    <small>
                                                        <strong>Approved by:</strong> <?php echo htmlspecialchars($draft['approver_name']); ?>
                                                        <?php if ($draft['approval_notes']): ?>
                                                            - <?php echo htmlspecialchars($draft['approval_notes']); ?>
                                                        <?php endif; ?>
                                                    </small>
                                                </div>
                                            <?php endif; ?>
                                            
                                            <div class="d-flex gap-2 flex-wrap">
                                                <button class="btn btn-outline-info btn-sm" onclick="viewDraft(<?php echo $draft['id']; ?>)">
                                                    <i class="fas fa-eye me-1"></i>View
                                                </button>
                                                
                                                <?php if ($draft['status'] === 'draft' || $draft['status'] === 'rejected'): ?>
                                                    <button class="btn btn-outline-warning btn-sm" onclick="editDraft(<?php echo $draft['id']; ?>)">
                                                        <i class="fas fa-edit me-1"></i>Edit
                                                    </button>
                                                <?php endif; ?>
                                                
                                                <?php if ($draft['status'] === 'approved'): ?>
                                                    <button class="btn btn-success btn-sm" onclick="sendApprovedDraft(<?php echo $draft['id']; ?>)">
                                                        <i class="fas fa-paper-plane me-1"></i>Send Now
                                                    </button>
                                                <?php endif; ?>
                                                
                                                <?php if ($draft['status'] !== 'sent'): ?>
                                                    <button class="btn btn-outline-danger btn-sm" onclick="deleteDraft(<?php echo $draft['id']; ?>)">
                                                        <i class="fas fa-trash me-1"></i>Delete
                                                    </button>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <div class="text-center py-5">
                                        <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">No drafts found</h5>
                                        <p class="text-muted">Create your first email draft to get started.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Approvals Tab -->
                    <?php if ($pendingResult && $pendingResult->num_rows > 0): ?>
                    <div class="tab-pane fade" id="approvals" role="tabpanel">
                        <div class="email-card card">
                            <div class="card-header">
                                <h5 class="mb-0">
                                    <i class="fas fa-clock me-2"></i>Pending Email Approvals
                                </h5>
                            </div>
                            <div class="card-body">
                                <?php while ($pending = $pendingResult->fetch_assoc()): ?>
                                    <div class="draft-item approval-required">
                                        <div class="d-flex justify-content-between align-items-start mb-3">
                                            <div>
                                                <h6 class="mb-1"><?php echo htmlspecialchars($pending['subject']); ?></h6>
                                                <small class="text-muted">
                                                    Submitted: <?php echo date('M d, Y H:i', strtotime($pending['created_at'])); ?>
                                                    by <?php echo htmlspecialchars($pending['creator_name']); ?>
                                                </small>
                                            </div>
                                            <span class="status-badge bg-warning">
                                                Pending Approval
                                            </span>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <h6>Content Preview:</h6>
                                            <div class="border rounded p-2" style="max-height: 150px; overflow-y: auto; background: rgba(255,255,255,0.05);">
                                                <?php echo substr(strip_tags($pending['content']), 0, 200) . '...'; ?>
                                            </div>
                                        </div>
                                        
                                        <div class="mb-3">
                                            <label class="form-label">Approval Notes:</label>
                                            <textarea class="form-control approval-notes" id="notes_<?php echo $pending['id']; ?>" 
                                                      rows="2" placeholder="Add approval or rejection notes..."></textarea>
                                        </div>
                                        
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-approve btn-sm" onclick="approveEmail(<?php echo $pending['id']; ?>)">
                                                <i class="fas fa-check me-1"></i>Approve
                                            </button>
                                            <button class="btn btn-reject btn-sm" onclick="rejectEmail(<?php echo $pending['id']; ?>)">
                                                <i class="fas fa-times me-1"></i>Reject
                                            </button>
                                            <button class="btn btn-outline-info btn-sm" onclick="viewDraft(<?php echo $pending['id']; ?>)">
                                                <i class="fas fa-eye me-1"></i>Full Preview
                                            </button>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
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

<!-- Draft Detail Modal -->
<div class="modal fade" id="draftModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Draft Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="draftContent">
                <!-- Draft content will be loaded here -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src = "js/admin_news_emails.js">

</script>
</body>
</html>