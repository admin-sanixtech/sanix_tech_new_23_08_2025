<?php
//admin_approve_category.php
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

$messages = [];

// Define base URL for uploads
$base_url = 'http://sanixtech.in/';

// Handle approval/rejection actions
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['approve']) && !empty($_GET['approve'])) {
        $category_id = intval($_GET['approve']);
        $sql = "UPDATE sanixazs_main_db.categories SET status = 'approved', updated_at = CURRENT_TIMESTAMP WHERE category_id = $category_id";
        if ($conn->query($sql) === TRUE) {
            $messages[] = "Category approved successfully!";
        } else {
            $messages[] = "Error approving category: " . $conn->error;
        }
    }
    
    if (isset($_GET['reject']) && !empty($_GET['reject'])) {
        $category_id = intval($_GET['reject']);
        $sql = "UPDATE sanixazs_main_db.categories SET status = 'rejected', updated_at = CURRENT_TIMESTAMP WHERE category_id = $category_id";
        if ($conn->query($sql) === TRUE) {
            $messages[] = "Category rejected successfully!";
        } else {
            $messages[] = "Error rejecting category: " . $conn->error;
        }
    }
}

// Handle bulk actions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['bulk_action']) && isset($_POST['selected_categories'])) {
        $action = $_POST['bulk_action'];
        $selected_ids = $_POST['selected_categories'];
        
        if (!empty($selected_ids) && in_array($action, ['approve', 'reject'])) {
            $ids = implode(',', array_map('intval', $selected_ids));
            $status = ($action === 'approve') ? 'approved' : 'rejected';
            
            $sql = "UPDATE sanixazs_main_db.categories SET status = '$status', updated_at = CURRENT_TIMESTAMP WHERE category_id IN ($ids)";
            if ($conn->query($sql) === TRUE) {
                $count = count($selected_ids);
                $messages[] = "$count categories $action" . "d successfully!";
            } else {
                $messages[] = "Error performing bulk action: " . $conn->error;
            }
        }
    }
}

// Function to get full image URL
function getImageUrl($imagePath, $baseUrl) {
    if (empty($imagePath)) {
        return '';
    }
    
    // If it's already a full URL, return as is
    if (strpos($imagePath, 'http') === 0) {
        return $imagePath;
    }
    
    // Check multiple possible locations
    $possiblePaths = [
        $baseUrl . $imagePath,
        $baseUrl . 'uploads/' . basename($imagePath),
        $baseUrl . 'admin/uploads/' . basename($imagePath),
        $baseUrl . 'admin/category/uploads/' . basename($imagePath)
    ];
    
    foreach ($possiblePaths as $path) {
        $headers = @get_headers($path);
        if ($headers && strpos($headers[0], '200') !== false) {
            return $path;
        }
    }
    
    // If no valid path found, return the first possibility
    return $possiblePaths[0];
}

// Get status badge class
function getStatusBadge($status) {
    switch ($status) {
        case 'approved':
            return '<span class="badge bg-success">Approved</span>';
        case 'rejected':
            return '<span class="badge bg-danger">Rejected</span>';
        case 'pending':
        default:
            return '<span class="badge bg-warning">Pending</span>';
    }
}

// Get filter from URL
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'pending';

// Build query based on filter
$where_clause = "";
switch ($filter) {
    case 'approved':
        $where_clause = "WHERE status = 'approved'";
        break;
    case 'rejected':
        $where_clause = "WHERE status = 'rejected'";
        break;
    case 'all':
        $where_clause = "";
        break;
    case 'pending':
    default:
        $where_clause = "WHERE status = 'pending'";
        break;
}

// Get category counts for badges
$pending_count = $conn->query("SELECT COUNT(*) as count FROM sanixazs_main_db.categories WHERE status = 'pending'")->fetch_assoc()['count'];
$approved_count = $conn->query("SELECT COUNT(*) as count FROM sanixazs_main_db.categories WHERE status = 'approved'")->fetch_assoc()['count'];
$rejected_count = $conn->query("SELECT COUNT(*) as count FROM sanixazs_main_db.categories WHERE status = 'rejected'")->fetch_assoc()['count'];
$total_count = $conn->query("SELECT COUNT(*) as count FROM sanixazs_main_db.categories")->fetch_assoc()['count'];

// Fetch categories based on filter
$category_result = $conn->query("SELECT * FROM sanixazs_main_db.categories $where_clause ORDER BY created_at DESC");
$categories = [];
if ($category_result->num_rows > 0) {
    while ($row = $category_result->fetch_assoc()) {
        $categories[] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8" />
    <title>Approve Categories</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="../../css/admin_styleone.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
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
                    <div class="content">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h2><i class="fas fa-check-circle"></i> Category Approval Center</h2>
                            <a href="admin_add_category.php" class="btn btn-secondary">
                                <i class="fas fa-plus"></i> Add Category
                            </a>
                        </div>

                        <!-- Display Messages -->
                        <?php if (!empty($messages)) : ?>
                            <div class="alert alert-info alert-dismissible fade show">
                                <?php foreach ($messages as $message) : ?>
                                    <p class="mb-0"><?php echo $message; ?></p>
                                <?php endforeach; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>

                        <!-- Filter Tabs -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="nav nav-pills" role="tablist">
                                    <a class="nav-link <?php echo ($filter === 'pending') ? 'active' : ''; ?>" 
                                       href="?filter=pending">
                                        <i class="fas fa-clock"></i> Pending 
                                        <span class="badge bg-warning text-dark ms-1"><?php echo $pending_count; ?></span>
                                    </a>
                                    <a class="nav-link <?php echo ($filter === 'approved') ? 'active' : ''; ?>" 
                                       href="?filter=approved">
                                        <i class="fas fa-check"></i> Approved 
                                        <span class="badge bg-success ms-1"><?php echo $approved_count; ?></span>
                                    </a>
                                    <a class="nav-link <?php echo ($filter === 'rejected') ? 'active' : ''; ?>" 
                                       href="?filter=rejected">
                                        <i class="fas fa-times"></i> Rejected 
                                        <span class="badge bg-danger ms-1"><?php echo $rejected_count; ?></span>
                                    </a>
                                    <a class="nav-link <?php echo ($filter === 'all') ? 'active' : ''; ?>" 
                                       href="?filter=all">
                                        <i class="fas fa-list"></i> All 
                                        <span class="badge bg-secondary ms-1"><?php echo $total_count; ?></span>
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Bulk Actions Form -->
                        <?php if ($filter === 'pending' && !empty($categories)) : ?>
                        <form method="POST" id="bulkForm">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <div class="input-group">
                                        <select name="bulk_action" class="form-select" required>
                                            <option value="">Select Action</option>
                                            <option value="approve">Approve Selected</option>
                                            <option value="reject">Reject Selected</option>
                                        </select>
                                        <button type="submit" class="btn btn-primary">Apply</button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <button type="button" class="btn btn-outline-secondary" onclick="toggleAll()">
                                        <i class="fas fa-check-square"></i> Select All
                                    </button>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Categories Table -->
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-dark">
                                    <tr>
                                        <?php if ($filter === 'pending') : ?>
                                        <th width="50">
                                            <input type="checkbox" id="selectAll" onchange="toggleAll()">
                                        </th>
                                        <?php endif; ?>
                                        <th>SNO</th>
                                        <th>Category</th>
                                        <th>Image</th>
                                        <th>Status</th>
                                        <th>Submitted</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (!empty($categories)) : ?>
                                        <?php foreach ($categories as $index => $category) : ?>
                                            <tr>
                                                <?php if ($filter === 'pending') : ?>
                                                <td>
                                                    <input type="checkbox" name="selected_categories[]" 
                                                           value="<?php echo $category['category_id']; ?>" 
                                                           class="category-checkbox">
                                                </td>
                                                <?php endif; ?>
                                                <td><?php echo $index + 1; ?></td>
                                                <td>
                                                    <div>
                                                        <strong><?php echo htmlspecialchars($category['category_name']); ?></strong>
                                                        <br>
                                                        <small class="text-muted">ID: <?php echo $category['category_id']; ?></small>
                                                    </div>
                                                </td>
                                                <td>
                                                    <?php if (!empty($category['category_image'])): ?>
                                                        <?php $imageUrl = getImageUrl($category['category_image'], $base_url); ?>
                                                        <img src="<?php echo htmlspecialchars($imageUrl); ?>" 
                                                             width="80" height="80" alt="Category Image"
                                                             style="object-fit: cover; border-radius: 8px; cursor: pointer;"
                                                             onclick="showImageModal('<?php echo htmlspecialchars($imageUrl); ?>', '<?php echo htmlspecialchars($category['category_name']); ?>')"
                                                             onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iODAiIGhlaWdodD0iODAiIHZpZXdCb3g9IjAgMCA4MCA4MCIgZmlsbD0ibm9uZSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIj4KPHJlY3Qgd2lkdGg9IjgwIiBoZWlnaHQ9IjgwIiBmaWxsPSIjZjNmNGY2Ii8+CjxwYXRoIGQ9Ik00MCA0MEMzNy43OTA5IDQwIDM2IDM4LjIwOTEgMzYgMzZDMzYgMzMuNzkwOSAzNy43OTA5IDMyIDQwIDMyQzQyLjIwOTEgMzIgNDQgMzMuNzkwOSA0NCAzNkM0NCA0MS43OTA5IDQyLjIwOTEgNDAgNDAgNDBaIiBmaWxsPSIjOWNhM2FmIi8+CjxwYXRoIGQ9Ik0yOCA1Nkw0MCA0NEw1MiA1NkgyOFoiIGZpbGw9IiM5Y2EzYWYiLz4KPC9zdmc+'; this.alt='Image not found';">
                                                    <?php else: ?>
                                                        <div class="text-center text-muted bg-light rounded" style="width: 80px; height: 80px; display: flex; align-items: center; justify-content: center;">
                                                            <i class="fas fa-image fa-2x"></i>
                                                        </div>
                                                    <?php endif; ?>
                                                </td>
                                                <td><?php echo getStatusBadge($category['status']); ?></td>
                                                <td>
                                                    <small>
                                                        <?php echo date('M d, Y H:i', strtotime($category['created_at'])); ?>
                                                        <?php if ($category['updated_at'] != $category['created_at']): ?>
                                                            <br><span class="text-muted">Updated: <?php echo date('M d, Y H:i', strtotime($category['updated_at'])); ?></span>
                                                        <?php endif; ?>
                                                    </small>
                                                </td>
                                                <td>
                                                    <div class="btn-group" role="group">
                                                        <?php if ($category['status'] === 'pending'): ?>
                                                            <a href="?approve=<?php echo $category['category_id']; ?>&filter=<?php echo $filter; ?>" 
                                                               class="btn btn-success btn-sm" 
                                                               onclick="return confirm('Approve this category?');">
                                                                <i class="fas fa-check"></i>
                                                            </a>
                                                            <a href="?reject=<?php echo $category['category_id']; ?>&filter=<?php echo $filter; ?>" 
                                                               class="btn btn-danger btn-sm" 
                                                               onclick="return confirm('Reject this category?');">
                                                                <i class="fas fa-times"></i>
                                                            </a>
                                                        <?php elseif ($category['status'] === 'approved'): ?>
                                                            <a href="../../edit_category.php?id=<?php echo $category['category_id']; ?>" 
                                                               class="btn btn-warning btn-sm">
                                                                <i class="fas fa-edit"></i>
                                                            </a>
                                                        <?php elseif ($category['status'] === 'rejected'): ?>
                                                            <a href="?approve=<?php echo $category['category_id']; ?>&filter=<?php echo $filter; ?>" 
                                                               class="btn btn-success btn-sm" 
                                                               onclick="return confirm('Re-approve this category?');">
                                                                <i class="fas fa-redo"></i> Re-approve
                                                            </a>
                                                        <?php endif; ?>
                                                        
                                                        <a href="../../delete_category.php?id=<?php echo $category['category_id']; ?>" 
                                                           class="btn btn-danger btn-sm" 
                                                           onclick="return confirm('Are you sure you want to delete this category?');">
                                                            <i class="fas fa-trash"></i>
                                                        </a>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else : ?>
                                        <tr>
                                            <td colspan="<?php echo ($filter === 'pending') ? '7' : '6'; ?>" class="text-center py-4">
                                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                                <p class="text-muted">
                                                    <?php 
                                                    switch($filter) {
                                                        case 'pending':
                                                            echo "No categories pending approval.";
                                                            break;
                                                        case 'approved':
                                                            echo "No approved categories found.";
                                                            break;
                                                        case 'rejected':
                                                            echo "No rejected categories found.";
                                                            break;
                                                        default:
                                                            echo "No categories found.";
                                                            break;
                                                    }
                                                    ?>
                                                </p>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <?php if ($filter === 'pending' && !empty($categories)) : ?>
                        </form>
                        <?php endif; ?>

                        <!-- Statistics Card -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card bg-dark">
                                    <div class="card-body">
                                        <h5 class="card-title">Category Statistics</h5>
                                        <div class="row text-center">
                                            <div class="col-3">
                                                <div class="stat-item">
                                                    <span class="stat-number text-warning"><?php echo $pending_count; ?></span>
                                                    <span class="stat-label">Pending</span>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="stat-item">
                                                    <span class="stat-number text-success"><?php echo $approved_count; ?></span>
                                                    <span class="stat-label">Approved</span>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="stat-item">
                                                    <span class="stat-number text-danger"><?php echo $rejected_count; ?></span>
                                                    <span class="stat-label">Rejected</span>
                                                </div>
                                            </div>
                                            <div class="col-3">
                                                <div class="stat-item">
                                                    <span class="stat-number text-info"><?php echo $total_count; ?></span>
                                                    <span class="stat-label">Total</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>
                        <?php include '../../admin_footer.php'; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Image Preview Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content bg-dark">
            <div class="modal-header">
                <h5 class="modal-title" id="imageModalLabel">Category Image</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img id="modalImage" src="" alt="" class="img-fluid rounded">
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Toggle all checkboxes
function toggleAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.category-checkbox');
    
    if (selectAll) {
        checkboxes.forEach(checkbox => {
            checkbox.checked = selectAll.checked;
        });
    }
}

// Show image in modal
function showImageModal(imageSrc, categoryName) {
    document.getElementById('modalImage').src = imageSrc;
    document.getElementById('imageModalLabel').textContent = categoryName;
    new bootstrap.Modal(document.getElementById('imageModal')).show();
}

// Form validation for bulk actions
document.getElementById('bulkForm')?.addEventListener('submit', function(e) {
    const selectedCheckboxes = document.querySelectorAll('.category-checkbox:checked');
    if (selectedCheckboxes.length === 0) {
        e.preventDefault();
        alert('Please select at least one category.');
        return false;
    }
    
    const action = document.querySelector('select[name="bulk_action"]').value;
    if (!action) {
        e.preventDefault();
        alert('Please select an action.');
        return false;
    }
    
    const confirmMessage = action === 'approve' ? 
        `Are you sure you want to approve ${selectedCheckboxes.length} categories?` : 
        `Are you sure you want to reject ${selectedCheckboxes.length} categories?`;
    
    if (!confirm(confirmMessage)) {
        e.preventDefault();
        return false;
    }
});

// Auto-refresh pending count every 30 seconds if on pending tab
<?php if ($filter === 'pending'): ?>
setInterval(function() {
    fetch(window.location.href)
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newBadge = doc.querySelector('.nav-link.active .badge');
            const currentBadge = document.querySelector('.nav-link.active .badge');
            if (newBadge && currentBadge && newBadge.textContent !== currentBadge.textContent) {
                currentBadge.textContent = newBadge.textContent;
            }
        })
        .catch(console.error);
}, 30000);
<?php endif; ?>
</script>

<style>
.stat-item {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.stat-number {
    font-size: 2rem;
    font-weight: bold;
    display: block;
}

.stat-label {
    font-size: 0.9rem;
    text-muted;
    display: block;
    margin-top: 0.25rem;
}

.nav-pills .nav-link {
    margin-right: 0.5rem;
    border-radius: 0.375rem;
}

.nav-pills .nav-link:not(.active) {
    background-color: var(--bs-secondary-bg);
    color: var(--bs-body-color);
}

.table th {
    border-bottom: 2px solid var(--bs-border-color);
    font-weight: 600;
}

.btn-group .btn {
    margin-right: 0.25rem;
}

.btn-group .btn:last-child {
    margin-right: 0;
}

@media (max-width: 768px) {
    .stat-number {
        font-size: 1.5rem;
    }
    
    .btn-group {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }
    
    .btn-group .btn {
        margin-right: 0;
    }
}
</style>

</body>
</html>