<?php
// users_details.php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

// Check if the user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../login.php");
    exit;
}

require_once(__DIR__ . '/../../config/db_connection.php');

// Verify the connection to the database
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle AJAX requests for user operations
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    try {
        switch ($_POST['action']) {
            case 'delete':
                $user_id = intval($_POST['user_id']);
                $deleteQuery = "DELETE FROM sanixazs_main_db.users WHERE user_id = ?";
                $stmt = $conn->prepare($deleteQuery);
                
                if (!$stmt) {
                    throw new Exception("Prepare failed: " . $conn->error);
                }
                
                $stmt->bind_param("i", $user_id);
                
                if ($stmt->execute()) {
                    echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
                } else {
                    throw new Exception("Execute failed: " . $stmt->error);
                }
                $stmt->close();
                exit;
                
            case 'update':
                $user_id = intval($_POST['user_id']);
                $name = trim($_POST['name']);
                $email = trim($_POST['email']);
                $phone_number = trim($_POST['phone_number']);
                $role = $_POST['role'];
                $status = $_POST['status'];
                
                // Validate required fields
                if (empty($name) || empty($email)) {
                    throw new Exception("Name and email are required");
                }
                
                // Validate email format
                if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    throw new Exception("Invalid email format");
                }
                
                // Check if email exists for other users
                $emailCheckQuery = "SELECT user_id FROM sanixazs_main_db.users WHERE email = ? AND user_id != ?";
                $emailStmt = $conn->prepare($emailCheckQuery);
                if (!$emailStmt) {
                    throw new Exception("Email check prepare failed: " . $conn->error);
                }
                $emailStmt->bind_param("si", $email, $user_id);
                $emailStmt->execute();
                $emailResult = $emailStmt->get_result();
                
                if ($emailResult->num_rows > 0) {
                    throw new Exception("Email already exists for another user");
                }
                $emailStmt->close();
                
                $updateQuery = "UPDATE sanixazs_main_db.users SET username = ?, email = ?, phone_number = ?, role = ?, status = ?, updated_at = CURRENT_TIMESTAMP WHERE user_id = ?";
                $stmt = $conn->prepare($updateQuery);
                
                if (!$stmt) {
                    throw new Exception("Prepare failed: " . $conn->error);
                }
                
                $stmt->bind_param("sssssi", $name, $email, $phone_number, $role, $status, $user_id);
                
                if ($stmt->execute()) {
                    if ($stmt->affected_rows > 0) {
                        echo json_encode(['success' => true, 'message' => 'User updated successfully']);
                    } else {
                        throw new Exception("No rows were updated. User may not exist.");
                    }
                } else {
                    throw new Exception("Execute failed: " . $stmt->error);
                }
                $stmt->close();
                exit;
                
            default:
                throw new Exception("Invalid action");
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        exit;
    }
}

// Pagination and filtering variables
$page = isset($_GET['page']) ? intval($_GET['page']) : 1;
$limit = 50;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$roleFilter = isset($_GET['role']) ? $conn->real_escape_string($_GET['role']) : '';
$statusFilter = isset($_GET['status']) ? $conn->real_escape_string($_GET['status']) : '';

// Build WHERE clause
$whereClause = "WHERE 1=1";
$params = [];
$types = "";

if (!empty($search)) {
    $whereClause .= " AND (name LIKE ? OR email LIKE ? OR phone_number LIKE ?)";
    $searchParam = "%$search%";
    $params[] = $searchParam;
    $params[] = $searchParam;
    $params[] = $searchParam;
    $types .= "sss";
}

if (!empty($roleFilter)) {
    $whereClause .= " AND role = ?";
    $params[] = $roleFilter;
    $types .= "s";
}

if (!empty($statusFilter)) {
    $whereClause .= " AND status = ?";
    $params[] = $statusFilter;
    $types .= "s";
}

// Count total records for pagination
$countQuery = "SELECT COUNT(*) as total FROM sanixazs_main_db.users $whereClause";
$countStmt = $conn->prepare($countQuery);
if (!empty($params)) {
    $countStmt->bind_param($types, ...$params);
}
$countStmt->execute();
$totalRecords = $countStmt->get_result()->fetch_assoc()['total'];
$totalPages = ceil($totalRecords / $limit);

// Fetch users from the database with filters and pagination
$usersQuery = "SELECT user_id, username, email, role, status, created_at, updated_at, phone_number, last_login 
               FROM sanixazs_main_db.users $whereClause 
               ORDER BY created_at DESC LIMIT ? OFFSET ?";

$stmt = $conn->prepare($usersQuery);
$params[] = $limit;
$params[] = $offset;
$types .= "ii";
$stmt->bind_param($types, ...$params);
$stmt->execute();
$usersResult = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>User details</title>
    <link  rel="stylesheet"  href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/css/bootstrap.min.css"  />
    <script src="https://kit.fontawesome.com/ae360af17e.js"  crossorigin="anonymous" ></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="../../css/admin_styleone.css">
    <style>
        .filter-section {
            background-color: var(--bs-dark-border-subtle);
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }
        .pagination-info {
            color: #6c757d;
        }
        .btn-sm {
            margin: 2px;
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
                    <!-- Filter Section -->
                    <div class="filter-section">
                        <h6 class="mb-3"><i class="fas fa-filter"></i> Filter Users</h6>
                        <form method="GET" id="filterForm">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Search</label>
                                    <input type="text" class="form-control" name="search" 
                                           placeholder="Search by username, email, or phone" 
                                           value="<?php echo htmlspecialchars($search); ?>">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Role</label>
                                    <select class="form-select" username="role">
                                        <option value="">All Roles</option>
                                        <option value="admin" <?php echo $roleFilter === 'admin' ? 'selected' : ''; ?>>Admin</option>
                                        <option value="user" <?php echo $roleFilter === 'user' ? 'selected' : ''; ?>>User</option>
                                        <option value="hr" <?php echo $roleFilter === 'hr' ? 'selected' : ''; ?>>HR</option>
                                        <option value="operator" <?php echo $roleFilter === 'operator' ? 'selected' : ''; ?>>Operator</option>
                                        <option value="devloper" <?php echo $roleFilter === 'devloper' ? 'selected' : ''; ?>>Developer</option>
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Status</label>
                                    <select class="form-select" name="status">
                                        <option value="">All Status</option>
                                        <option value="active" <?php echo $statusFilter === 'active' ? 'selected' : ''; ?>>Active</option>
                                        <option value="inactive" <?php echo $statusFilter === 'inactive' ? 'selected' : ''; ?>>Inactive</option>
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-flex gap-2">
                                        <button type="submit" class="btn btn-primary btn-sm">
                                            <i class="fas fa-search"></i> Filter
                                        </button>
                                        <a href="?" class="btn btn-secondary btn-sm">
                                            <i class="fas fa-refresh"></i> Clear
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    <!-- Table Element -->
                    <div class="card border-0">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">User List</h5>
                            <div class="pagination-info">
                                Showing <?php echo $offset + 1; ?> to <?php echo min($offset + $limit, $totalRecords); ?> 
                                of <?php echo $totalRecords; ?> users
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-hover">
                                    <thead class="table-dark">
                                        <tr>
                                            <th>ID</th>
                                            <th>Name</th>
                                            <th>Email</th>
                                            <th>Role</th>
                                            <th>Status</th>
                                            <th>Phone</th>
                                            <th>Created</th>
                                            <th>Last Login</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        if ($usersResult && $usersResult->num_rows > 0) {
                                            while ($user = $usersResult->fetch_assoc()) {
                                                $statusBadge = $user['status'] === 'active' ? 'bg-success' : 'bg-danger';
                                                echo "<tr>
                                                        <td>" . htmlspecialchars($user['user_id']) . "</td>
                                                        <td>" . htmlspecialchars($user['username']) . "</td>
                                                        <td>" . htmlspecialchars($user['email']) . "</td>
                                                        <td><span class='badge bg-info'>" . htmlspecialchars($user['role']) . "</span></td>
                                                        <td><span class='badge $statusBadge'>" . htmlspecialchars($user['status']) . "</span></td>
                                                        <td>" . htmlspecialchars($user['phone_number'] ?: 'N/A') . "</td>
                                                        <td>" . date('M j, Y', strtotime($user['created_at'])) . "</td>
                                                        <td>" . ($user['last_login'] ? date('M j, Y', strtotime($user['last_login'])) : 'Never') . "</td>
                                                        <td>
                                                            <button class='btn btn-warning btn-sm edit-user' 
                                                                    data-user-id='" . $user['user_id'] . "' 
                                                                    data-name='" . htmlspecialchars($user['username']) . "'
                                                                    data-email='" . htmlspecialchars($user['email']) . "'
                                                                    data-phone='" . htmlspecialchars($user['phone_number']) . "'
                                                                    data-role='" . $user['role'] . "'
                                                                    data-status='" . $user['status'] . "'>
                                                                <i class='fas fa-edit'></i>
                                                            </button>
                                                            <button class='btn btn-danger btn-sm delete-user' 
                                                                    data-user-id='" . $user['user_id'] . "'
                                                                    data-name='" . htmlspecialchars($user['username']) . "'>
                                                                <i class='fas fa-trash'></i>
                                                            </button>
                                                        </td>
                                                    </tr>";
                                            }
                                        } else {
                                            echo "<tr><td colspan='9' class='text-center'>No users found.</td></tr>";
                                        }
                                        ?>
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination -->
                            <?php if ($totalPages > 1): ?>
                            <nav aria-label="User pagination">
                                <ul class="pagination justify-content-center">
                                    <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page - 1])); ?>">Previous</a>
                                    </li>
                                    
                                    <?php
                                    $start = max(1, $page - 2);
                                    $end = min($totalPages, $page + 2);
                                    
                                    if ($start > 1) {
                                        echo '<li class="page-item"><a class="page-link" href="?' . http_build_query(array_merge($_GET, ['page' => 1])) . '">1</a></li>';
                                        if ($start > 2) {
                                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                        }
                                    }
                                    
                                    for ($i = $start; $i <= $end; $i++) {
                                        $active = $i === $page ? 'active' : '';
                                        echo '<li class="page-item ' . $active . '"><a class="page-link" href="?' . http_build_query(array_merge($_GET, ['page' => $i])) . '">' . $i . '</a></li>';
                                    }
                                    
                                    if ($end < $totalPages) {
                                        if ($end < $totalPages - 1) {
                                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                        }
                                        echo '<li class="page-item"><a class="page-link" href="?' . http_build_query(array_merge($_GET, ['page' => $totalPages])) . '">' . $totalPages . '</a></li>';
                                    }
                                    ?>
                                    
                                    <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?<?php echo http_build_query(array_merge($_GET, ['page' => $page + 1])); ?>">Next</a>
                                    </li>
                                </ul>
                            </nav>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div class="modal fade" id="editUserModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit User</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="editUserForm">
                    <div class="modal-body">
                        <input type="hidden" id="edit_user_id" name="user_id">
                        <div class="mb-3">
                            <label class="form-label">Name</label>
                            <input type="text" class="form-control" id="edit_name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" id="edit_email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="edit_phone" name="phone_number">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select class="form-select" id="edit_role" name="role" required>
                                <option value="admin">Admin</option>
                                <option value="user">User</option>
                                <option value="hr">HR</option>
                                <option value="operator">Operator</option>
                                <option value="devloper">Developer</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="edit_status" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include 'admin_footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

    <script>
        $(document).ready(function() {
            // Edit user functionality
            $('.edit-user').click(function() {
                const userId = $(this).data('user-id');
                const name = $(this).data('name');
                const email = $(this).data('email');
                const phone = $(this).data('phone');
                const role = $(this).data('role');
                const status = $(this).data('status');

                $('#edit_user_id').val(userId);
                $('#edit_name').val(name);
                $('#edit_email').val(email);
                $('#edit_phone').val(phone);
                $('#edit_role').val(role);
                $('#edit_status').val(status);

                $('#editUserModal').modal('show');
            });

            // Handle edit form submission
            $('#editUserForm').submit(function(e) {
                e.preventDefault();
                
                // Disable submit button to prevent double submission
                const submitBtn = $(this).find('button[type="submit"]');
                submitBtn.prop('disabled', true).text('Saving...');
                
                $.ajax({
                    url: window.location.href,
                    type: 'POST',
                    data: $(this).serialize() + '&action=update',
                    dataType: 'json',
                    success: function(response) {
                        if (response.success) {
                            alert('User updated successfully!');
                            $('#editUserModal').modal('hide');
                            location.reload();
                        } else {
                            alert('Error: ' + response.message);
                        }
                    },
                    error: function(xhr, status, error) {
                        console.log('AJAX Error:', xhr.responseText);
                        alert('Error updating user: ' + error);
                    },
                    complete: function() {
                        // Re-enable submit button
                        submitBtn.prop('disabled', false).text('Save Changes');
                    }
                });
            });

            // Delete user functionality
            $('.delete-user').click(function() {
                const userId = $(this).data('user-id');
                const userName = $(this).data('name');
                
                if (confirm(`Are you sure you want to delete user "${userName}"? This action cannot be undone.`)) {
                    $.ajax({
                        url: window.location.href,
                        type: 'POST',
                        data: {
                            action: 'delete',
                            user_id: userId
                        },
                        dataType: 'json',
                        success: function(response) {
                            if (response.success) {
                                alert('User deleted successfully!');
                                location.reload();
                            } else {
                                alert('Error: ' + response.message);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.log('AJAX Error:', xhr.responseText);
                            alert('Error deleting user: ' + error);
                        }
                    });
                }
            });

            // Sidebar toggle functionality
            $("#menu-toggle").click(function(e) {
                e.preventDefault();
                $("#wrapper").toggleClass("toggled");
            });

            function initMenu() {
                $('#menu ul').hide();
                $('#menu li a').click(function() {
                    var checkElement = $(this).next();
                    if ((checkElement.is('ul')) && (checkElement.is(':visible'))) {
                        return false;
                    }
                    if ((checkElement.is('ul')) && (!checkElement.is(':visible'))) {
                        $('#menu ul:visible').toggle('normal');
                        checkElement.slideDown('normal');
                        return false;
                    }
                });
            }
            initMenu();
        });
    </script>
</body>
</html>