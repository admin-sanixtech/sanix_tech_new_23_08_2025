
// admin_dashboard.php
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'add_interviewer':
            $stmt = $db->prepare("INSERT INTO mock_interviewers (name, email, phone, specialization, experience, bio, status, approved_by, approved_at) VALUES (?, ?, ?, ?, ?, ?, 'approved', ?, NOW())");
            $stmt->execute([
                $_POST['name'],
                $_POST['email'],
                $_POST['phone'],
                $_POST['specialization'],
                $_POST['experience'],
                $_POST['bio'],
                $_SESSION['user_id']
            ]);
            echo json_encode(['success' => true, 'message' => 'Interviewer added successfully']);
            exit();
            
        case 'approve_interviewer':
            $stmt = $db->prepare("UPDATE interviewer_applications SET status = 'approved', reviewed_by = ?, reviewed_at = NOW() WHERE id = ?");
            $stmt->execute([$_SESSION['user_id'], $_POST['id']]);
            
            // Copy to mock_interviewers table
            $stmt = $db->prepare("SELECT * FROM interviewer_applications WHERE id = ?");
            $stmt->execute([$_POST['id']]);
            $application = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $stmt = $db->prepare("INSERT INTO mock_interviewers (name, email, phone, specialization, experience, bio, status, approved_by, approved_at) VALUES (?, ?, ?, ?, ?, ?, 'approved', ?, NOW())");
            $stmt->execute([
                $application['name'],
                $application['email'],
                $application['phone'],
                $application['specialization'],
                $application['experience'],
                $application['bio'],
                $_SESSION['user_id']
            ]);
            
            echo json_encode(['success' => true, 'message' => 'Interviewer approved successfully']);
            exit();
            
        case 'reject_application':
            $stmt = $db->prepare("UPDATE interviewer_applications SET status = 'rejected', reviewed_by = ?, reviewed_at = NOW() WHERE id = ?");
            $stmt->execute([$_SESSION['user_id'], $_POST['id']]);
            echo json_encode(['success' => true, 'message' => 'Application rejected']);
            exit();
            
        case 'approve_interview':
            $stmt = $db->prepare("UPDATE interview_requests SET status = 'approved', meeting_link = ?, scheduled_at = NOW() WHERE id = ?");
            $stmt->execute([$_POST['meeting_link'], $_POST['id']]);
            echo json_encode(['success' => true, 'message' => 'Interview approved']);
            exit();
    }
}

// Fetch dashboard data
$stats_query = "
    SELECT 
        (SELECT COUNT(*) FROM mock_interviewers WHERE status = 'approved') as total_interviewers,
        (SELECT COUNT(*) FROM interview_requests WHERE status = 'pending') as pending_interviews,
        (SELECT COUNT(*) FROM interviewer_applications WHERE status = 'pending') as pending_applications,
        (SELECT SUM(amount) FROM payments WHERE status = 'completed' AND DATE(created_at) = CURDATE()) as today_revenue
";
$stats = $db->query($stats_query)->fetch(PDO::FETCH_ASSOC);

// Fetch pending applications
$pending_applications = $db->query("SELECT * FROM interviewer_applications WHERE status = 'pending' ORDER BY applied_at DESC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch pending interview requests
$pending_interviews_query = "
    SELECT ir.*, u.name as user_name, u.email as user_email, mi.name as interviewer_name 
    FROM interview_requests ir 
    JOIN users u ON ir.user_id = u.id 
    LEFT JOIN mock_interviewers mi ON ir.interviewer_id = mi.id 
    WHERE ir.status = 'pending' 
    ORDER BY ir.created_at DESC
";
$pending_interviews = $db->query($pending_interviews_query)->fetchAll(PDO::FETCH_ASSOC);

// Fetch all interviewers
$interviewers = $db->query("SELECT * FROM mock_interviewers WHERE status = 'approved' ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch recent payments
$recent_payments = $db->query("
    SELECT p.*, u.name as user_name, pl.name as plan_name 
    FROM payments p 
    JOIN users u ON p.user_id = u.id 
    LEFT JOIN user_subscriptions us ON p.reference_id = us.id AND p.payment_type = 'subscription'
    LEFT JOIN pricing_plans pl ON us.plan_id = pl.id
    ORDER BY p.created_at DESC 
    LIMIT 10
")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mock Interview Management - Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="bg-gray-800 text-white w-64 min-h-screen p-4">
            <div class="mb-8">
                <h1 class="text-xl font-bold">Interview Admin</h1>
            </div>
            <nav class="space-y-2">
                <a href="#" onclick="showTab('dashboard')" class="flex items-center space-x-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg p-3 transition-colors">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                <a href="#" onclick="showTab('interviewers')" class="flex items-center space-x-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg p-3 transition-colors">
                    <i class="fas fa-users"></i>
                    <span>Mock Interviewers</span>
                </a>
                <a href="#" onclick="showTab('applications')" class="flex items-center space-x-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg p-3 transition-colors">
                    <i class="fas fa-file-alt"></i>
                    <span>Applications</span>
                    <?php if (count($pending_applications) > 0): ?>
                        <span class="bg-red-500 text-xs rounded-full px-2 py-1"><?= count($pending_applications) ?></span>
                    <?php endif; ?>
                </a>
                <a href="#" onclick="showTab('interviews')" class="flex items-center space-x-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg p-3 transition-colors">
                    <i class="fas fa-calendar"></i>
                    <span>Interview Requests</span>
                    <?php if (count($pending_interviews) > 0): ?>
                        <span class="bg-red-500 text-xs rounded-full px-2 py-1"><?= count($pending_interviews) ?></span>
                    <?php endif; ?>
                </a>
                <a href="#" onclick="showTab('payments')" class="flex items-center space-x-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg p-3 transition-colors">
                    <i class="fas fa-rupee-sign"></i>
                    <span>Payments</span>
                </a>
                <a href="#" onclick="showTab('pricing')" class="flex items-center space-x-3 text-gray-300 hover:text-white hover:bg-gray-700 rounded-lg p-3 transition-colors">
                    <i class="fas fa-tags"></i>
                    <span>Pricing Plans</span>
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800">Admin Dashboard</h1>
                <div class="flex items-center space-x-4">
                    <span class="text-gray-600">Welcome, <?= $_SESSION['name'] ?></span>
                    <a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors">Logout</a>
                </div>
            </div>

            <!-- Dashboard Tab -->
            <div id="dashboard-tab" class="tab-content">
                <!-- Stats Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100">
                                <i class="fas fa-users text-blue-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-sm font-medium text-gray-500">Total Interviewers</h3>
                                <p class="text-2xl font-bold text-gray-900"><?= $stats['total_interviewers'] ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-yellow-100">
                                <i class="fas fa-clock text-yellow-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-sm font-medium text-gray-500">Pending Interviews</h3>
                                <p class="text-2xl font-bold text-gray-900"><?= $stats['pending_interviews'] ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-red-100">
                                <i class="fas fa-file-alt text-red-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-sm font-medium text-gray-500">Pending Applications</h3>
                                <p class="text-2xl font-bold text-gray-900"><?= $stats['pending_applications'] ?></p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100">
                                <i class="fas fa-rupee-sign text-green-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-sm font-medium text-gray-500">Today's Revenue</h3>
                                <p class="text-2xl font-bold text-gray-900">₹<?= number_format($stats['today_revenue'] ?? 0, 2) ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                    <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <button onclick="showTab('interviewers'); document.getElementById('add-interviewer-modal').classList.remove('hidden')" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-colors">
                            <i class="fas fa-user-plus mr-2"></i> Add New Interviewer
                        </button>
                        <button onclick="showTab('applications')" class="bg-yellow-500 hover:bg-yellow-600 text-white px-6 py-3 rounded-lg transition-colors">
                            <i class="fas fa-eye mr-2"></i> Review Applications
                        </button>
                        <button onclick="showTab('interviews')" class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg transition-colors">
                            <i class="fas fa-calendar-check mr-2"></i> Manage Interviews
                        </button>
                    </div>
                </div>
            </div>

            <!-- Interviewers Tab -->
            <div id="interviewers-tab" class="tab-content hidden">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-gray-800">Mock Interviewers</h2>
                    <button onclick="document.getElementById('add-interviewer-modal').classList.remove('hidden')" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-colors">
                        <i class="fas fa-plus mr-2"></i> Add New Interviewer
                    </button>
                </div>

                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Transaction ID</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($recent_payments as $payment): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= htmlspecialchars($payment['user_name']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₹<?= number_format($payment['amount'], 2) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php if ($payment['payment_type'] == 'subscription'): ?>
                                        <?= htmlspecialchars($payment['plan_name'] ?? 'Subscription') ?>
                                    <?php else: ?>
                                        <?= ucfirst(str_replace('_', ' ', $payment['payment_type'])) ?>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= date('M d, Y H:i', strtotime($payment['created_at'])) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        <?= $payment['status'] == 'completed' ? 'bg-green-100 text-green-800' : ($payment['status'] == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') ?>">
                                        <?= ucfirst($payment['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($payment['transaction_id'] ?? 'N/A') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Pricing Plans Tab -->
            <div id="pricing-tab" class="tab-content hidden">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Pricing Plans</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-6">
                        <div class="text-center">
                            <h3 class="text-lg font-semibold text-blue-900">Single Interview</h3>
                            <div class="mt-4">
                    <button onclick="closePaymentModal()" class="text-gray-500 hover:text-gray-700">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        let currentPaymentId = null;
        
        function showTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.add('hidden');
            });
            
            // Show selected tab
            document.getElementById(tabName + '-tab').classList.remove('hidden');
            
            // Update active nav item
            document.querySelectorAll('nav a').forEach(link => {
                link.classList.remove('bg-blue-700', 'text-white');
                link.classList.add('text-blue-200');
            });
            event.target.classList.add('bg-blue-700', 'text-white');
            event.target.classList.remove('text-blue-200');
        }

        // Book Interview Form Handler
        document.getElementById('book-interview-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'request_interview');
            
            fetch('user_dashboard.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentPaymentId = data.payment_id;
                    showPaymentModal('₹100.00', 'Mock Interview Payment');
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });

        // Purchase Plan Function
        function purchasePlan(planId) {
            const formData = new FormData();
            formData.append('action', 'purchase_plan');
            formData.append('plan_id', planId);
            
            fetch('user_dashboard.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentPaymentId = data.payment_id;
                    // Get plan details for payment modal
                    const planCards = document.querySelectorAll('[onclick="purchasePlan(' + planId + ')"]');
                    const planCard = planCards[0].closest('.bg-white');
                    const planName = planCard.querySelector('h3').textContent;
                    const planPrice = planCard.querySelector('.text-3xl').textContent;
                    
                    showPaymentModal(planPrice, planName + ' Purchase');
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        }

        // Interviewer Application Form Handler
        document.getElementById('interviewer-apply-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'apply_interviewer');
            
            fetch('user_dashboard.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    currentPaymentId = data.payment_id;
                    showPaymentModal('₹500.00', 'Interviewer Registration Fee');
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });

        // Payment Modal Functions
        function showPaymentModal(amount, description) {
            document.getElementById('payment-amount').textContent = amount;
            document.getElementById('payment-description').textContent = description;
            document.getElementById('payment-modal').classList.remove('hidden');
        }

        function closePaymentModal() {
            document.getElementById('payment-modal').classList.add('hidden');
            currentPaymentId = null;
        }

        function simulatePayment() {
            if (!currentPaymentId) {
                alert('No payment to process');
                return;
            }

            // Simulate payment processing
            const paymentButton = event.target;
            const originalText = paymentButton.innerHTML;
            paymentButton.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Processing...';
            paymentButton.disabled = true;

            setTimeout(() => {
                // Simulate successful payment
                alert('Payment successful! Your request has been processed.');
                closePaymentModal();
                location.reload(); // Refresh to show updated data
            }, 2000);
        }

        // Initialize dashboard
        showTab('dashboard');

        // Auto-update pending interview times
        setInterval(() => {
            const timeElements = document.querySelectorAll('[data-time]');
            timeElements.forEach(element => {
                const time = new Date(element.getAttribute('data-time'));
                const now = new Date();
                const diff = time - now;
                
                if (diff > 0) {
                    const hours = Math.floor(diff / (1000 * 60 * 60));
                    const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                    element.textContent = `In ${hours}h ${minutes}m`;
                } else {
                    element.textContent = 'Started';
                    element.classList.add('text-red-600');
                }
            });
        }, 60000); // Update every minute
    </script>
</body>
</html>