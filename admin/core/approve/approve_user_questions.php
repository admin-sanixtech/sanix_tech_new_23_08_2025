<?php
session_start();
include 'db_connection.php';

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Check if user is admin (add this check if needed)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../login.php');
    exit();
}

$message = '';

// Handle approve/reject actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $question_id = intval($_POST['question_id']);
    $action = $_POST['action'];

    try {
        // Update status based on the action (approve or reject)
        if ($action === 'approve') {
            $sql_update = "UPDATE quiz_questions SET status = 'approved' WHERE question_id = ?";
            $stmt = $conn->prepare($sql_update);
            $stmt->bind_param("i", $question_id);
            
            if ($stmt->execute()) {
                $message = "<div class='alert alert-success'><i class='fas fa-check-circle'></i> Question approved successfully!</div>";
            } else {
                $message = "<div class='alert alert-danger'><i class='fas fa-exclamation-circle'></i> Failed to approve question.</div>";
            }
            $stmt->close();
            
        } elseif ($action === 'reject') {
            $sql_update = "UPDATE quiz_questions SET status = 'rejected' WHERE question_id = ?";
            $stmt = $conn->prepare($sql_update);
            $stmt->bind_param("i", $question_id);
            
            if ($stmt->execute()) {
                $message = "<div class='alert alert-warning'><i class='fas fa-exclamation-triangle'></i> Question rejected successfully!</div>";
            } else {
                $message = "<div class='alert alert-danger'><i class='fas fa-exclamation-circle'></i> Failed to reject question.</div>";
            }
            $stmt->close();
        }
        
    } catch (Exception $e) {
        $message = "<div class='alert alert-danger'><i class='fas fa-exclamation-circle'></i> Error: " . $e->getMessage() . "</div>";
    }

    // Redirect to prevent form resubmission
    header("Location: " . $_SERVER['PHP_SELF'] . "?msg=" . urlencode(strip_tags($message)));
    exit();
}

// Display message from redirect
if (isset($_GET['msg'])) {
    $message = "<div class='alert alert-info'>" . htmlspecialchars($_GET['msg']) . "</div>";
}

// Fetch pending questions with category and subcategory names
$sql = "SELECT 
    qq.question_id,
    qq.question_text,
    qq.question_type,
    qq.correct_answer,
    qq.option_a,
    qq.option_b,
    qq.option_c,
    qq.option_d,
    qq.code_snippet,
    qq.difficulty_level,
    qq.description,
    qq.created_by,
    qq.created_at,
    c.category_name,
    sc.subcategory_name,
    u.name as creator_name
FROM quiz_questions qq
LEFT JOIN categories c ON qq.category_id = c.category_id
LEFT JOIN subcategories sc ON qq.subcategory_id = sc.subcategory_id
LEFT JOIN users u ON qq.created_by = u.user_id
WHERE qq.status = 'pending'
ORDER BY qq.created_at DESC";

$result = $conn->query($sql);

// Debugging output if needed
if (!$result) {
    die("Error in query: " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="en" data-bs-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pending Questions - Admin Dashboard</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="css/admin_styleone.css">
    
    <style>
        .question-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            margin-bottom: 20px;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .question-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.3);
            border-color: rgba(13, 110, 253, 0.5);
        }
        
        .page-header {
            background: linear-gradient(135deg, rgba(13, 110, 253, 0.1), rgba(102, 16, 242, 0.1));
            border-radius: 15px;
            padding: 2rem;
            margin-bottom: 2rem;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .btn-approve {
            background: linear-gradient(135deg, #198754, #20c997);
            border: none;
            transition: all 0.3s ease;
        }
        
        .btn-approve:hover {
            background: linear-gradient(135deg, #157347, #1aa179);
            transform: translateY(-1px);
        }
        
        .btn-reject {
            background: linear-gradient(135deg, #dc3545, #fd7e14);
            border: none;
            transition: all 0.3s ease;
        }
        
        .btn-reject:hover {
            background: linear-gradient(135deg, #b02a37, #e8681b);
            transform: translateY(-1px);
        }
        
        .difficulty-badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
        }
        
        .code-snippet {
            background: rgba(0, 0, 0, 0.3);
            border-radius: 8px;
            padding: 1rem;
            font-family: 'Courier New', monospace;
            font-size: 0.875rem;
            border-left: 4px solid #007bff;
        }
        
        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }
        
        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
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
                                <i class="fas fa-question-circle me-2"></i>Pending Questions
                            </h2>
                            <p class="text-muted mb-0">Review and approve user-submitted quiz questions</p>
                        </div>
                        <div>
                            <span class="badge bg-warning text-dark fs-6">
                                <i class="fas fa-clock me-1"></i>
                                <?php echo $result->num_rows; ?> Pending
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Messages -->
                <?php if (!empty($message)) echo $message; ?>

                <!-- Questions List -->
                <div class="row">
                    <?php if ($result->num_rows > 0): ?>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <div class="col-lg-6 col-xl-4 mb-4">
                                <div class="question-card card h-100">
                                    <div class="card-header d-flex justify-content-between align-items-center">
                                        <div>
                                            <small class="text-muted">ID: <?php echo htmlspecialchars($row['question_id']); ?></small>
                                            <span class="difficulty-badge badge bg-<?php 
                                                echo match($row['difficulty_level']) {
                                                    'Beginner' => 'success',
                                                    'Intermediate' => 'warning',
                                                    'Advanced' => 'danger',
                                                    'Expert' => 'dark',
                                                    default => 'secondary'
                                                };
                                            ?> ms-2">
                                                <?php echo htmlspecialchars($row['difficulty_level']); ?>
                                            </span>
                                        </div>
                                        <span class="badge bg-<?php 
                                            echo match($row['question_type']) {
                                                'multiple_choice' => 'primary',
                                                'true_false' => 'info',
                                                'code' => 'success',
                                                default => 'secondary'
                                            };
                                        ?>">
                                            <?php echo ucfirst(str_replace('_', ' ', $row['question_type'])); ?>
                                        </span>
                                    </div>
                                    
                                    <div class="card-body">
                                        <!-- Category Information -->
                                        <div class="mb-3">
                                            <small class="text-info">
                                                <i class="fas fa-folder me-1"></i>
                                                <?php echo htmlspecialchars($row['category_name'] ?? 'Unknown Category'); ?>
                                                <?php if ($row['subcategory_name']): ?>
                                                    <i class="fas fa-angle-right mx-1"></i>
                                                    <?php echo htmlspecialchars($row['subcategory_name']); ?>
                                                <?php endif; ?>
                                            </small>
                                        </div>
                                        
                                        <!-- Question Text -->
                                        <h6 class="card-title text-white mb-3">
                                            <?php echo nl2br(htmlspecialchars($row['question_text'])); ?>
                                        </h6>
                                        
                                        <!-- Code Snippet (if applicable) -->
                                        <?php if ($row['code_snippet']): ?>
                                            <div class="code-snippet mb-3">
                                                <small class="text-muted d-block mb-2">
                                                    <i class="fas fa-code me-1"></i>Code Snippet:
                                                </small>
                                                <pre class="mb-0 text-light"><?php echo htmlspecialchars($row['code_snippet']); ?></pre>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <!-- Options (for multiple choice) -->
                                        <?php if ($row['question_type'] === 'multiple_choice'): ?>
                                            <div class="mb-3">
                                                <small class="text-muted d-block mb-2">
                                                    <i class="fas fa-list me-1"></i>Options:
                                                </small>
                                                <div class="options">
                                                    <?php foreach(['option_a' => 'A', 'option_b' => 'B', 'option_c' => 'C', 'option_d' => 'D'] as $option_key => $label): ?>
                                                        <?php if ($row[$option_key]): ?>
                                                            <div class="mb-1 <?php echo $row['correct_answer'] === $label ? 'text-success fw-bold' : 'text-light'; ?>">
                                                                <strong><?php echo $label ?>:</strong> 
                                                                <?php echo htmlspecialchars($row[$option_key]); ?>
                                                                <?php if ($row['correct_answer'] === $label): ?>
                                                                    <i class="fas fa-check-circle text-success ms-1"></i>
                                                                <?php endif; ?>
                                                            </div>
                                                        <?php endif; ?>
                                                    <?php endforeach; ?>
                                                </div>
                                            </div>
                                        <?php else: ?>
                                            <!-- Correct Answer for other types -->
                                            <div class="mb-3">
                                                <small class="text-muted">Correct Answer:</small>
                                                <div class="text-success fw-bold">
                                                    <i class="fas fa-check-circle me-1"></i>
                                                    <?php echo htmlspecialchars($row['correct_answer']); ?>
                                                </div>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <!-- Description -->
                                        <?php if ($row['description']): ?>
                                            <div class="mb-3">
                                                <small class="text-muted d-block mb-1">Description:</small>
                                                <p class="small text-light mb-0">
                                                    <?php echo nl2br(htmlspecialchars($row['description'])); ?>
                                                </p>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <!-- Creator and Date -->
                                        <div class="text-muted small mb-3">
                                            <i class="fas fa-user me-1"></i>
                                            <?php echo htmlspecialchars($row['creator_name'] ?? 'Unknown'); ?>
                                            <br>
                                            <i class="fas fa-calendar me-1"></i>
                                            <?php echo date('M d, Y H:i', strtotime($row['created_at'])); ?>
                                        </div>
                                    </div>
                                    
                                    <!-- Action Buttons -->
                                    <div class="card-footer bg-transparent border-top-0 pt-0">
                                        <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="question_id" value="<?php echo $row['question_id']; ?>">
                                                <input type="hidden" name="action" value="approve">
                                                <button type="submit" class="btn btn-approve btn-sm" 
                                                        onclick="return confirm('Are you sure you want to approve this question?')">
                                                    <i class="fas fa-check me-1"></i>Approve
                                                </button>
                                            </form>
                                            <form method="POST" class="d-inline">
                                                <input type="hidden" name="question_id" value="<?php echo $row['question_id']; ?>">
                                                <input type="hidden" name="action" value="reject">
                                                <button type="submit" class="btn btn-reject btn-sm" 
                                                        onclick="return confirm('Are you sure you want to reject this question?')">
                                                    <i class="fas fa-times me-1"></i>Reject
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col-12">
                            <div class="empty-state">
                                <i class="fas fa-check-circle text-success"></i>
                                <h4 class="text-success">All Caught Up!</h4>
                                <p class="text-muted">No questions are waiting for approval.</p>
                                <a href="dashboard.php" class="btn btn-outline-primary mt-3">
                                    <i class="fas fa-home me-2"></i>Back to Dashboard
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// Add smooth animations
document.addEventListener('DOMContentLoaded', function() {
    // Animate question cards on load
    const questionCards = document.querySelectorAll('.question-card');
    questionCards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
        
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
});

// Auto-hide success/error messages after 5 seconds
setTimeout(function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        if (alert.classList.contains('alert-success') || alert.classList.contains('alert-warning')) {
            alert.style.transition = 'opacity 0.5s ease';
            alert.style.opacity = '0';
            setTimeout(() => {
                if (alert.parentNode) {
                    alert.parentNode.removeChild(alert);
                }
            }, 500);
        }
    });
}, 5000);
</script>

</body>
</html>