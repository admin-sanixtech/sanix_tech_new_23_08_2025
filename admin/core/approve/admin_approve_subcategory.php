<?php

//admin_approve_subcategory.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once(__DIR__ . '/../../config/db_connection.php');

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Secure admin check
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: http://sanixtech.in/login.php');
    exit();
}

// ---------- Handle approval/rejection actions ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $subcategory_id = $_POST['subcategory_id'] ?? '';
    $action = $_POST['action'] ?? '';
    
    if ($subcategory_id && in_array($action, ['approve', 'reject'])) {
        try {
            $status = ($action === 'approve') ? 'approved' : 'rejected';
            $stmt = $pdo->prepare(
                "UPDATE subcategories 
                 SET status = :status, updated_at = NOW() 
                 WHERE subcategory_id = :id"
            );
            $stmt->execute([
                ':status' => $status,
                ':id' => $subcategory_id
            ]);
            
            $success = 'Sub-category ' . ($action === 'approve' ? 'approved' : 'rejected') . ' successfully!';
        } catch (PDOException $e) {
            $error = 'DB error: ' . $e->getMessage();
        }
    }
    
    // Handle bulk actions
    if (isset($_POST['bulk_action']) && isset($_POST['selected_items'])) {
        $bulk_action = $_POST['bulk_action'];
        $selected_items = $_POST['selected_items'];
        
        if (in_array($bulk_action, ['approve', 'reject']) && !empty($selected_items)) {
            try {
                $status = ($bulk_action === 'approve') ? 'approved' : 'rejected';
                $placeholders = str_repeat('?,', count($selected_items) - 1) . '?';
                
                $stmt = $pdo->prepare(
                    "UPDATE subcategories 
                     SET status = ?, updated_at = NOW() 
                     WHERE subcategory_id IN ($placeholders)"
                );
                $stmt->execute(array_merge([$status], $selected_items));
                
                $count = count($selected_items);
                $success = $count . ' sub-categories ' . ($bulk_action === 'approve' ? 'approved' : 'rejected') . ' successfully!';
            } catch (PDOException $e) {
                $error = 'Bulk action error: ' . $e->getMessage();
            }
        }
    }
}

// ---------- Fetch pending subcategories ----------
try {
    $pendingStmt = $pdo->query(
        "SELECT sc.subcategory_id, sc.subcategory_name, sc.status, sc.created_at, c.category_name
           FROM subcategories sc
      JOIN categories c ON sc.category_id = c.category_id
       WHERE sc.status = 'pending'
       ORDER BY sc.created_at DESC"
    );
    $pendingSubcategories = $pendingStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $pendingSubcategories = [];
    $error = 'Error fetching pending subcategories: ' . $e->getMessage();
}

// ---------- Fetch recently approved/rejected subcategories ----------
try {
    $recentStmt = $pdo->query(
        "SELECT sc.subcategory_id, sc.subcategory_name, sc.status, sc.updated_at, c.category_name
           FROM subcategories sc
      JOIN categories c ON sc.category_id = c.category_id
       WHERE sc.status IN ('approved', 'rejected')
       ORDER BY sc.updated_at DESC
       LIMIT 20"
    );
    $recentSubcategories = $recentStmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $recentSubcategories = [];
}

// Get counts for dashboard
try {
    $countStmt = $pdo->query(
        "SELECT 
            COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_count,
            COUNT(CASE WHEN status = 'approved' THEN 1 END) as approved_count,
            COUNT(CASE WHEN status = 'rejected' THEN 1 END) as rejected_count
         FROM subcategories"
    );
    $counts = $countStmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $counts = ['pending_count' => 0, 'approved_count' => 0, 'rejected_count' => 0];
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Approve Sub-categories</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link rel="stylesheet" href="../css/admin_styleone.css" />
</head>
<body class="d-flex">

<aside id="sidebar"><?php include 'admin_menu.php'; ?></aside>

<div class="flex-grow-1">
    <?php include 'admin_navbar.php'; ?>

    <main class="container my-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="mb-0">Sub-category Approvals</h2>
            <a href="admin_add_subcategory.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Add Sub-categories
            </a>
        </div>

        <?php if (!empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <?= $success ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php elseif (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <?= $error ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="row mb-4">
            <div class="col-md-4">
                <div class="card bg-warning text-dark">
                    <div class="card-body text-center">
                        <i class="fas fa-clock fa-2x mb-2"></i>
                        <h5 class="card-title"><?= $counts['pending_count'] ?></h5>
                        <p class="card-text">Pending Approval</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-success text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-check-circle fa-2x mb-2"></i>
                        <h5 class="card-title"><?= $counts['approved_count'] ?></h5>
                        <p class="card-text">Approved</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card bg-danger text-white">
                    <div class="card-body text-center">
                        <i class="fas fa-times-circle fa-2x mb-2"></i>
                        <h5 class="card-title"><?= $counts['rejected_count'] ?></h5>
                        <p class="card-text">Rejected</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Approvals -->
        <div class="card mb-4">
            <div class="card-header">
                <h4 class="mb-0"><i class="fas fa-hourglass-half"></i> Pending Sub-categories (<?= count($pendingSubcategories) ?>)</h4>
            </div>
            <div class="card-body">
                <?php if (!empty($pendingSubcategories)): ?>
                    <form method="POST" id="bulkForm">
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <div class="btn-group" role="group">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="selectAll()">
                                        <i class="fas fa-check-square"></i> Select All
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="selectNone()">
                                        <i class="fas fa-square"></i> Select None
                                    </button>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group">
                                    <select name="bulk_action" class="form-select form-select-sm" required>
                                        <option value="">Bulk Action</option>
                                        <option value="approve">Approve Selected</option>
                                        <option value="reject">Reject Selected</option>
                                    </select>
                                    <button type="submit" name="bulk_submit" class="btn btn-sm btn-primary">Apply</button>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-striped align-middle">
                                <thead>
                                    <tr>
                                        <th width="5%">
                                            <input type="checkbox" id="selectAllCheckbox" onchange="toggleAll()">
                                        </th>
                                        <th width="5%">#</th>
                                        <th width="25%">Category</th>
                                        <th width="25%">Sub-category</th>
                                        <th width="15%">Created</th>
                                        <th width="10%">Status</th>
                                        <th width="15%">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php $i = 1; foreach ($pendingSubcategories as $sub): ?>
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="selected_items[]" value="<?= $sub['subcategory_id'] ?>" class="row-checkbox">
                                        </td>
                                        <td><?= $i++ ?></td>
                                        <td><?= htmlspecialchars($sub['category_name']) ?></td>
                                        <td><?= htmlspecialchars($sub['subcategory_name']) ?></td>
                                        <td><?= date('M j, Y', strtotime($sub['created_at'])) ?></td>
                                        <td>
                                            <span class="badge bg-warning">Pending</span>
                                        </td>
                                        <td>
                                            <div class="btn-group btn-group-sm" role="group">
                                                <button type="button" class="btn btn-success" 
                                                        onclick="approveReject(<?= $sub['subcategory_id'] ?>, 'approve')">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                <button type="button" class="btn btn-danger" 
                                                        onclick="approveReject(<?= $sub['subcategory_id'] ?>, 'reject')">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </form>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                        <h5>No Pending Approvals</h5>
                        <p class="text-muted">All sub-categories have been reviewed.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="card">
            <div class="card-header">
                <h4 class="mb-0"><i class="fas fa-history"></i> Recent Activity</h4>
            </div>
            <div class="card-body">
                <?php if (!empty($recentSubcategories)): ?>
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>Category</th>
                                    <th>Sub-category</th>
                                    <th>Status</th>
                                    <th>Updated</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($recentSubcategories as $sub): ?>
                                <tr>
                                    <td><?= htmlspecialchars($sub['category_name']) ?></td>
                                    <td><?= htmlspecialchars($sub['subcategory_name']) ?></td>
                                    <td>
                                        <?php if ($sub['status'] === 'approved'): ?>
                                            <span class="badge bg-success">Approved</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Rejected</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= date('M j, Y H:i', strtotime($sub['updated_at'])) ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">No recent activity.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <?php include 'admin_footer.php'; ?>
</div>

<!-- Hidden form for individual actions -->
<form id="actionForm" method="POST" style="display: none;">
    <input type="hidden" name="subcategory_id" id="actionSubcategoryId">
    <input type="hidden" name="action" id="actionType">
</form>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
// Individual approve/reject actions
function approveReject(subcategoryId, action) {
    if (confirm('Are you sure you want to ' + action + ' this sub-category?')) {
        document.getElementById('actionSubcategoryId').value = subcategoryId;
        document.getElementById('actionType').value = action;
        document.getElementById('actionForm').submit();
    }
}

// Bulk selection functions
function selectAll() {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(checkbox => checkbox.checked = true);
    document.getElementById('selectAllCheckbox').checked = true;
}

function selectNone() {
    const checkboxes = document.querySelectorAll('.row-checkbox');
    checkboxes.forEach(checkbox => checkbox.checked = false);
    document.getElementById('selectAllCheckbox').checked = false;
}

function toggleAll() {
    const selectAllCheckbox = document.getElementById('selectAllCheckbox');
    const checkboxes = document.querySelectorAll('.row-checkbox');
    
    checkboxes.forEach(checkbox => {
        checkbox.checked = selectAllCheckbox.checked;
    });
}

// Bulk form validation
document.getElementById('bulkForm').addEventListener('submit', function(e) {
    const checkedBoxes = document.querySelectorAll('.row-checkbox:checked');
    const bulkAction = document.querySelector('select[name="bulk_action"]').value;
    
    if (checkedBoxes.length === 0) {
        e.preventDefault();
        alert('Please select at least one item.');
        return;
    }
    
    if (!bulkAction) {
        e.preventDefault();
        alert('Please select a bulk action.');
        return;
    }
    
    if (!confirm(`Are you sure you want to ${bulkAction} ${checkedBoxes.length} selected items?`)) {
        e.preventDefault();
    }
});
</script>

</body>
</html>