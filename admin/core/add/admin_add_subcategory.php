<?php

//admin_add_subcategory.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Secure admin check
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header('Location: http://sanixtech.in/login.php');
    exit();
}

// Initialize variables
$success = '';
$error = '';
$pdo = null;
$conn = null;

// Try multiple paths for database connection
$possible_paths = [
    __DIR__ . '/../../config/db_connection.php',
    __DIR__ . '/../config/db_connection.php',
    __DIR__ . '/config/db_connection.php',
    $_SERVER['DOCUMENT_ROOT'] . '/config/db_connection.php',
    dirname(dirname(dirname(__FILE__))) . '/config/db_connection.php'
];

$connection_loaded = false;
foreach ($possible_paths as $path) {
    if (file_exists($path)) {
        require_once($path);
        $connection_loaded = true;
        break;
    }
}

if (!$connection_loaded) {
    $error = 'Database connection file not found. Checked paths: ' . implode(', ', $possible_paths);
}

// Alternative: Direct database connection if file not found
if (!$connection_loaded || !isset($pdo) || $pdo === null) {
    try {
        $servername = "localhost";
        $username   = "sanixazs";
        $password   = "Kri1Lin2@#$%";
        $dbname     = "sanixazs_main_db";
        
        // Create PDO connection
        $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
        
        // Also create MySQLi connection
        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            throw new Exception("MySQLi connection failed: " . $conn->connect_error);
        }
        $conn->set_charset("utf8mb4");
        
        $error = ''; // Clear any previous error
        
    } catch (PDOException $e) {
        $error = 'PDO Database connection failed: ' . $e->getMessage();
    } catch (Exception $e) {
        $error = 'Database connection error: ' . $e->getMessage();
    }
}

// Final check if PDO is available
if (!isset($pdo) || $pdo === null) {
    $error = 'PDO connection is still not available after all attempts.';
}

// ---------- add‑subcategory form handler ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_subcategory']) && empty($error)) {
    $category_id      = $_POST['category_id'] ?? '';
    $subcategory_name = trim($_POST['subcategory_name'] ?? '');

    if ($category_id && $subcategory_name) {
        try {
            // Check if subcategory already exists in this category
            $checkStmt = $pdo->prepare(
                "SELECT COUNT(*) FROM subcategories 
                 WHERE category_id = :cid AND subcategory_name = :sname"
            );
            $checkStmt->execute([
                ':cid'   => $category_id,
                ':sname' => $subcategory_name
            ]);
            
            if ($checkStmt->fetchColumn() > 0) {
                $error = 'Sub-category already exists in this category.';
            } else {
                // Insert subcategory with pending status for admin approval
                $stmt = $pdo->prepare(
                    "INSERT INTO subcategories (category_id, subcategory_name, status, created_at)
                     VALUES (:cid, :sname, 'pending', NOW())"
                );
                $stmt->execute([
                    ':cid'   => $category_id,
                    ':sname' => $subcategory_name
                ]);
                $success = 'Sub‑category added and sent for admin approval!';
                
                // Clear form data
                $_POST = [];
            }
        } catch (PDOException $e) {
            $error = 'DB error: ' . $e->getMessage();
        }
    } else {
        $error = 'Please fill in all fields.';
    }
}

// ---------- fetch categories for <select> ----------
$categories = [];
if (empty($error) && isset($pdo)) {
    try {
        $categoriesStmt = $pdo->query("SELECT category_id, category_name FROM categories ORDER BY category_name");
        $categories = $categoriesStmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = 'Error fetching categories: ' . $e->getMessage();
    }
}

// ---------- fetch ALL approved sub‑categories for initial table ----------
$subrows = [];
if (empty($error) && isset($pdo)) {
    try {
        $subStmt = $pdo->query(
            "SELECT sc.subcategory_id, sc.subcategory_name, sc.status, c.category_name
               FROM subcategories sc
          JOIN categories c ON sc.category_id = c.category_id
           WHERE sc.status = 'approved'
           ORDER BY c.category_name, sc.subcategory_name"
        );
        $subrows = $subStmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $error = 'Error fetching subcategories: ' . $e->getMessage();
    }
}

// Get count of pending subcategories for the badge
$pendingCount = 0;
if (empty($error) && isset($pdo)) {
    try {
        $pendingStmt = $pdo->query("SELECT COUNT(*) FROM subcategories WHERE status = 'pending'");
        $pendingCount = $pendingStmt->fetchColumn();
    } catch (PDOException $e) {
        // Silent fail for count
        $pendingCount = 0;
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <title>Manage Sub‑categories</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
    <link rel="stylesheet" href="../../css/admin_styleone.css">
    <style>
        .debug-info {
            background: #1a1a1a;
            border: 1px solid #333;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            font-family: monospace;
            font-size: 12px;
        }
        .connection-status {
            padding: 5px 10px;
            border-radius: 3px;
            margin: 2px 0;
        }
        .status-success { background: #d4edda; color: #155724; }
        .status-error { background: #f8d7da; color: #721c24; }
    </style>
</head>
<body class="d-flex">

<aside id="sidebar">
    <?php include '../../admin_menu.php'; ?>
</aside>

<div class="flex-grow-1">
    <?php include '../../admin_navbar.php'; ?>

    <main class="container my-4">
        <h2 class="mb-3">Add Sub‑category</h2>
        <?php if (!empty($success)): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> <?= htmlspecialchars($success) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <?php if (!empty($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle"></i> <?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($pdo) && $pdo !== null): ?>
        <form method="POST" class="row g-3 mb-5">
            <div class="col-md-4">  
                <label class="form-label">Category <span class="text-danger">*</span></label>
                <select id="filter_category_id" name="category_id" class="form-select" required>
                    <option value="">Select Category</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat['category_id']) ?>" 
                                <?= (isset($_POST['category_id']) && $_POST['category_id'] == $cat['category_id']) ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['category_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Sub‑category name <span class="text-danger">*</span></label>
                <input type="text" 
                       name="subcategory_name" 
                       class="form-control" 
                       value="<?= isset($_POST['subcategory_name']) ? htmlspecialchars($_POST['subcategory_name']) : '' ?>"
                       placeholder="Enter subcategory name"
                       required>
            </div>

            <div class="col-md-2 align-self-end">
                <button type="submit" name="add_subcategory" class="btn btn-primary w-100">
                    <i class="fas fa-plus"></i> Add
                </button>
            </div>
            
            <div class="col-md-2 align-self-end">
                <button type="reset" class="btn btn-secondary w-100">
                    <i class="fas fa-eraser"></i> Clear
                </button>
            </div>
        </form>

        <div class="row mb-3">
            <div class="col-md-6">
                <h3 class="mb-3">
                    <i class="fas fa-check-circle text-success"></i> 
                    Approved Sub‑categories (<?= count($subrows) ?>)
                </h3>
            </div>
            <div class="col-md-6 text-end">
                <a href="admin_approve_subcategory.php" class="btn btn-warning position-relative">
                    <i class="fas fa-clock"></i> Pending Approvals
                    <?php if ($pendingCount > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?= $pendingCount ?>
                            <span class="visually-hidden">pending approvals</span>
                        </span>
                    <?php endif; ?>
                </a>
            </div>
        </div>

        <!-- Category Filter for Table -->
        <div class="row mb-3">
            <div class="col-md-4">
                <label class="form-label">Filter by Category</label>
                <select id="table_category_filter" class="form-select">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= htmlspecialchars($cat['category_id']) ?>"><?= htmlspecialchars($cat['category_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
            
        <div class="table-responsive">
            <table id="sub_table" class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th width="8%">#</th>
                        <th width="35%">Category</th>
                        <th width="35%">Sub‑category</th>
                        <th width="22%">Status</th>
                    </tr>
                </thead>
                <tbody>
                <?php if ($subrows): 
                    $i = 1;
                    foreach ($subrows as $r): ?>
                        <tr>
                            <td><?= $i++ ?></td>
                            <td><?= htmlspecialchars($r['category_name']) ?></td>
                            <td><?= htmlspecialchars($r['subcategory_name']) ?></td>
                            <td>
                                <span class="badge bg-success">
                                    <i class="fas fa-check"></i> Approved
                                </span>
                            </td>
                        </tr>
                <?php endforeach; 
                else: ?>
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                <i class="fas fa-info-circle text-muted"></i>
                                <br>No approved subcategories found.
                            </td>
                        </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <?php if (count($subrows) > 10): ?>
        <div class="text-center mt-3">
            <button id="loadMoreBtn" class="btn btn-outline-primary">
                <i class="fas fa-chevron-down"></i> Load More
            </button>
        </div>
        <?php endif; ?>
        
        <?php else: ?>
        <div class="alert alert-warning">
            <h5><i class="fas fa-exclamation-triangle"></i> Database Connection Required</h5>
            <p>The application cannot connect to the database. Please check:</p>
            <ul>
                <li>Database connection file exists at the correct path</li>
                <li>Database credentials are correct</li>
                <li>Database server is running</li>
                <li>File permissions are correct</li>
            </ul>
            <hr>
            <h6>Troubleshooting Steps:</h6>
            <ol>
                <li>Verify the database connection file path: <code><?= __DIR__ . '/../../config/db_connection.php' ?></code></li>
                <li>Check if the file exists: <?= file_exists(__DIR__ . '/../../config/db_connection.php') ? 'EXISTS' : 'NOT FOUND' ?></li>
                <li>Verify database credentials in the connection file</li>
                <li>Test database connection independently</li>
            </ol>
        </div>
        <?php endif; ?>
    </main>

    <?php include '../../admin_footer.php'; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
    // AJAX refresh when table category filter changes
    $('#table_category_filter').on('change', function () {
        const cid = this.value;
        const tbody = $('#sub_table tbody');
        
        // Show loading
        tbody.html('<tr><td colspan="4" class="text-center"><i class="fas fa-spinner fa-spin"></i> Loading...</td></tr>');
        
        $.post('get_subcategories.php', { category_id: cid })
            .done(function (html) {
                tbody.html(html);
            })
            .fail(function(xhr, status, error) {
                tbody.html('<tr><td colspan="4" class="text-center text-danger"><i class="fas fa-exclamation-triangle"></i> Error loading data: ' + error + '</td></tr>');
            });
    });
    
    // Form validation
    $('form').on('submit', function(e) {
        const categoryId = $('#filter_category_id').val();
        const subcategoryName = $('input[name="subcategory_name"]').val().trim();
        
        if (!categoryId) {
            e.preventDefault();
            alert('Please select a category.');
            $('#filter_category_id').focus();
            return false;
        }
        
        if (!subcategoryName) {
            e.preventDefault();
            alert('Please enter subcategory name.');
            $('input[name="subcategory_name"]').focus();
            return false;
        }
        
        if (subcategoryName.length < 2) {
            e.preventDefault();
            alert('Subcategory name must be at least 2 characters long.');
            $('input[name="subcategory_name"]').focus();
            return false;
        }
        
        // Show loading state
        $(this).find('button[type="submit"]').prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Adding...');
    });
    
    // Auto-hide alerts after 5 seconds
    $('.alert').delay(5000).fadeOut();
    
    // Clear form on reset
    $('button[type="reset"]').on('click', function() {
        $('#filter_category_id').val('');
        $('input[name="subcategory_name"]').val('');
    });
});

// Debug function to check if variables are available
function checkConnections() {
    console.log('=== Database Connection Debug ===');
    <?php if (isset($pdo) && $pdo !== null): ?>
    console.log('✓ PDO connection: Available');
    <?php else: ?>
    console.log('✗ PDO connection: NOT Available');
    <?php endif; ?>
    
    <?php if (isset($conn) && $conn !== null): ?>
    console.log('✓ MySQLi connection: Available');
    <?php else: ?>
    console.log('✗ MySQLi connection: NOT Available');
    <?php endif; ?>
    
    console.log('Current file: <?= __FILE__ ?>');
    console.log('Looking for DB config at: <?= __DIR__ . "/../../config/db_connection.php" ?>');
    console.log('DB file exists: <?= file_exists(__DIR__ . "/../../config/db_connection.php") ? "true" : "false" ?>');
    console.log('=== End Debug Info ===');
}

// Call debug function on page load
checkConnections();
</script>

</body>
</html>