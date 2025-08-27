
<?php
// user_dashboard.php - User Interface
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header('Location: login.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'request_interview':
            $stmt = $db->prepare("INSERT INTO interview_requests (user_id, interviewer_id, interview_type, preferred_date, preferred_time, amount, notes) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_SESSION['user_id'],
                $_POST['interviewer_id'] ?: null,
                $_POST['interview_type'],
                $_POST['preferred_date'],
                $_POST['preferred_time'],
                $_POST['amount'],
                $_POST['notes']
            ]);
            
            // Create payment record
            $interview_id = $db->lastInsertId();
            $stmt = $db->prepare("INSERT INTO payments (user_id, amount, payment_type, reference_id, status) VALUES (?, ?, 'single_interview', ?, 'pending')");
            $stmt->execute([$_SESSION['user_id'], $_POST['amount'], $interview_id]);
            
            echo json_encode(['success' => true, 'message' => 'Interview request submitted successfully', 'payment_id' => $db->lastInsertId()]);
            exit();
            
        case 'purchase_plan':
            $stmt = $db->prepare("SELECT * FROM pricing_plans WHERE id = ?");
            $stmt->execute([$_POST['plan_id']]);
            $plan = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($plan) {
                $stmt = $db->prepare("INSERT INTO user_subscriptions (user_id, plan_id, remaining_interviews) VALUES (?, ?, ?)");
                $stmt->execute([$_SESSION['user_id'], $plan['id'], $plan['interviews_count']]);
                
                $subscription_id = $db->lastInsertId();
                $stmt = $db->prepare("INSERT INTO payments (user_id, amount, payment_type, reference_id, status) VALUES (?, ?, 'subscription', ?, 'pending')");
                $stmt->execute([$_SESSION['user_id'], $plan['price'], $subscription_id]);
                
                echo json_encode(['success' => true, 'message' => 'Plan purchase initiated', 'payment_id' => $db->lastInsertId()]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid plan selected']);
            }
            exit();
            
        case 'apply_interviewer':
            $stmt = $db->prepare("INSERT INTO interviewer_applications (name, email, phone, specialization, experience, bio) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $_POST['name'],
                $_POST['email'],
                $_POST['phone'],
                $_POST['specialization'],
                $_POST['experience'],
                $_POST['bio']
            ]);
            
            // Create payment record for interviewer fee
            $stmt = $db->prepare("INSERT INTO payments (user_id, amount, payment_type, reference_id, status) VALUES (?, 500.00, 'interviewer_fee', ?, 'pending')");
            $stmt->execute([$_SESSION['user_id'], $db->lastInsertId()]);
            
            echo json_encode(['success' => true, 'message' => 'Application submitted successfully', 'payment_id' => $db->lastInsertId()]);
            exit();
    }
}

// Fetch user data
$user_stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
$user_stmt->execute([$_SESSION['user_id']]);
$user = $user_stmt->fetch(PDO::FETCH_ASSOC);

// Fetch user's active subscriptions
$subscriptions = $db->prepare("
    SELECT us.*, pp.name as plan_name, pp.price 
    FROM user_subscriptions us 
    JOIN pricing_plans pp ON us.plan_id = pp.id 
    WHERE us.user_id = ? AND us.is_active = TRUE AND us.remaining_interviews > 0
")->execute([$_SESSION['user_id']])->fetchAll(PDO::FETCH_ASSOC);

// Fetch user's interview history
$interview_history = $db->prepare("
    SELECT ir.*, mi.name as interviewer_name, mi.email as interviewer_email 
    FROM interview_requests ir 
    LEFT JOIN mock_interviewers mi ON ir.interviewer_id = mi.id 
    WHERE ir.user_id = ? 
    ORDER BY ir.created_at DESC
")->execute([$_SESSION['user_id']])->fetchAll(PDO::FETCH_ASSOC);

// Fetch available interviewers
$interviewers = $db->query("SELECT * FROM mock_interviewers WHERE status = 'approved' ORDER BY rating DESC")->fetchAll(PDO::FETCH_ASSOC);

// Fetch pricing plans
$pricing_plans = $db->query("SELECT * FROM pricing_plans WHERE is_active = TRUE ORDER BY price ASC")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mock Interview Platform - User Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex">
        <!-- Sidebar -->
        <div class="bg-blue-800 text-white w-64 min-h-screen p-4">
            <div class="mb-8">
                <h1 class="text-xl font-bold">Interview Platform</h1>
                <p class="text-blue-200 text-sm">Welcome, <?= htmlspecialchars($user['name']) ?>!</p>
            </div>
            <nav class="space-y-2">
                <a href="#" onclick="showTab('dashboard')" class="flex items-center space-x-3 text-blue-200 hover:text-white hover:bg-blue-700 rounded-lg p-3 transition-colors">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                <a href="#" onclick="showTab('book-interview')" class="flex items-center space-x-3 text-blue-200 hover:text-white hover:bg-blue-700 rounded-lg p-3 transition-colors">
                    <i class="fas fa-calendar-plus"></i>
                    <span>Book Interview</span>
                </a>
                <a href="#" onclick="showTab('pricing')" class="flex items-center space-x-3 text-blue-200 hover:text-white hover:bg-blue-700 rounded-lg p-3 transition-colors">
                    <i class="fas fa-credit-card"></i>
                    <span>Pricing Plans</span>
                </a>
                <a href="#" onclick="showTab('interviewer-apply')" class="flex items-center space-x-3 text-blue-200 hover:text-white hover:bg-blue-700 rounded-lg p-3 transition-colors">
                    <i class="fas fa-user-tie"></i>
                    <span>Become Interviewer</span>
                </a>
                <a href="#" onclick="showTab('history')" class="flex items-center space-x-3 text-blue-200 hover:text-white hover:bg-blue-700 rounded-lg p-3 transition-colors">
                    <i class="fas fa-history"></i>
                    <span>Interview History</span>
                </a>
                <a href="#" onclick="showTab('profile')" class="flex items-center space-x-3 text-blue-200 hover:text-white hover:bg-blue-700 rounded-lg p-3 transition-colors">
                    <i class="fas fa-user"></i>
                    <span>Profile</span>
                </a>
            </nav>
        </div>

        <!-- Main Content -->
        <div class="flex-1 p-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-3xl font-bold text-gray-800">User Dashboard</h1>
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <p class="text-sm text-gray-600">Remaining Interviews</p>
                        <p class="text-xl font-bold text-blue-600">
                            <?php
                            $remaining = $db->prepare("SELECT SUM(remaining_interviews) FROM user_subscriptions WHERE user_id = ? AND is_active = TRUE");
                            $remaining->execute([$_SESSION['user_id']]);
                            echo $remaining->fetchColumn() ?? 0;
                            ?>
                        </p>
                    </div>
                    <a href="logout.php" class="bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg transition-colors">Logout</a>
                </div>
            </div>

            <!-- Dashboard Tab -->
            <div id="dashboard-tab" class="tab-content">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-blue-100">
                                <i class="fas fa-calendar-check text-blue-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-sm font-medium text-gray-500">Completed Interviews</h3>
                                <p class="text-2xl font-bold text-gray-900">
                                    <?php 
                                    $completed = $db->prepare("SELECT COUNT(*) FROM interview_requests WHERE user_id = ? AND status = 'completed'");
                                    $completed->execute([$_SESSION['user_id']]);
                                    echo $completed->fetchColumn();
                                    ?>
                                </p>
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
                                <p class="text-2xl font-bold text-gray-900">
                                    <?php 
                                    $pending = $db->prepare("SELECT COUNT(*) FROM interview_requests WHERE user_id = ? AND status IN ('pending', 'approved')");
                                    $pending->execute([$_SESSION['user_id']]);
                                    echo $pending->fetchColumn();
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <div class="flex items-center">
                            <div class="p-3 rounded-full bg-green-100">
                                <i class="fas fa-rupee-sign text-green-600 text-xl"></i>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-sm font-medium text-gray-500">Total Spent</h3>
                                <p class="text-2xl font-bold text-gray-900">
                                    ₹<?php 
                                    $spent = $db->prepare("SELECT SUM(amount) FROM payments WHERE user_id = ? AND status = 'completed'");
                                    $spent->execute([$_SESSION['user_id']]);
                                    echo number_format($spent->fetchColumn() ?? 0, 2);
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-md p-6 mb-8">
                    <h3 class="text-lg font-semibold mb-4">Quick Actions</h3>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <button onclick="showTab('book-interview')" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg transition-colors">
                            <i class="fas fa-calendar-plus mr-2"></i> Book New Interview
                        </button>
                        <button onclick="showTab('pricing')" class="bg-green-500 hover:bg-green-600 text-white px-6 py-3 rounded-lg transition-colors">
                            <i class="fas fa-credit-card mr-2"></i> Purchase Plan
                        </button>
                        <button onclick="showTab('interviewer-apply')" class="bg-purple-500 hover:bg-purple-600 text-white px-6 py-3 rounded-lg transition-colors">
                            <i class="fas fa-user-tie mr-2"></i> Become Interviewer
                        </button>
                    </div>
                </div>

                <!-- Recent Interviews -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold mb-4">Recent Interviews</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Interviewer</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php 
                                $recent_interviews = array_slice($interview_history, 0, 5);
                                foreach ($recent_interviews as $interview): 
                                ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900"><?= htmlspecialchars($interview['interviewer_name'] ?? 'Any Available') ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($interview['interview_type']) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= date('M d, Y', strtotime($interview['preferred_date'])) ?></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                            <?= $interview['status'] == 'completed' ? 'bg-green-100 text-green-800' : 
                                               ($interview['status'] == 'approved' ? 'bg-blue-100 text-blue-800' : 
                                               ($interview['status'] == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')) ?>">
                                            <?= ucfirst($interview['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if (count($recent_interviews) == 0): ?>
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500">No interviews yet</td>
                                </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Book Interview Tab -->
            <div id="book-interview-tab" class="tab-content hidden">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Book Mock Interview</h2>
                
                <div class="bg-white rounded-lg shadow-md p-6">
                    <form id="book-interview-form">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Interview Type</label>
                                <select name="interview_type" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select Interview Type</option>
                                    <option value="Technical Coding">Technical Coding</option>
                                    <option value="System Design">System Design</option>
                                    <option value="Behavioral">Behavioral</option>
                                    <option value="Frontend Development">Frontend Development</option>
                                    <option value="Backend Development">Backend Development</option>
                                    <option value="Full Stack">Full Stack</option>
                                    <option value="Data Science">Data Science</option>
                                    <option value="Machine Learning">Machine Learning</option>
                                    <option value="Product Management">Product Management</option>
                                    <option value="UI/UX Design">UI/UX Design</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Preferred Interviewer (Optional)</label>
                                <select name="interviewer_id" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Any Available Interviewer</option>
                                    <?php foreach ($interviewers as $interviewer): ?>
                                    <option value="<?= $interviewer['id'] ?>">
                                        <?= htmlspecialchars($interviewer['name']) ?> - <?= htmlspecialchars($interviewer['specialization']) ?> (⭐ <?= number_format($interviewer['rating'], 1) ?>)
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Preferred Date</label>
                                <input type="date" name="preferred_date" required min="<?= date('Y-m-d', strtotime('+1 day')) ?>" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Preferred Time</label>
                                <select name="preferred_time" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select Time</option>
                                    <option value="09:00:00">9:00 AM</option>
                                    <option value="10:00:00">10:00 AM</option>
                                    <option value="11:00:00">11:00 AM</option>
                                    <option value="12:00:00">12:00 PM</option>
                                    <option value="13:00:00">1:00 PM</option>
                                    <option value="14:00:00">2:00 PM</option>
                                    <option value="15:00:00">3:00 PM</option>
                                    <option value="16:00:00">4:00 PM</option>
                                    <option value="17:00:00">5:00 PM</option>
                                    <option value="18:00:00">6:00 PM</option>
                                    <option value="19:00:00">7:00 PM</option>
                                    <option value="20:00:00">8:00 PM</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Additional Notes (Optional)</label>
                            <textarea name="notes" rows="3" placeholder="Any specific requirements or topics you'd like to focus on..." class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                        
                        <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="font-medium text-blue-900">Interview Cost</h4>
                                    <p class="text-sm text-blue-700">60-minute mock interview session</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-2xl font-bold text-blue-900">₹100</span>
                                </div>
                            </div>
                        </div>
                        
                        <input type="hidden" name="amount" value="100">
                        
                        <div class="mt-6 flex justify-end">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-8 py-3 rounded-lg font-medium transition-colors">
                                <i class="fas fa-calendar-plus mr-2"></i> Book Interview & Pay
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Pricing Plans Tab -->
            <div id="pricing-tab" class="tab-content hidden">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Choose Your Plan</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <?php foreach ($pricing_plans as $plan): ?>
                    <div class="bg-white rounded-lg shadow-md p-6 <?= $plan['name'] == 'Silver Card' ? 'ring-2 ring-blue-500 transform scale-105' : '' ?>">
                        <?php if ($plan['name'] == 'Silver Card'): ?>
                        <div class="text-center mb-4">
                            <span class="bg-blue-500 text-white text-xs px-3 py-1 rounded-full">Most Popular</span>
                        </div>
                        <?php endif; ?>
                        
                        <div class="text-center">
                            <h3 class="text-lg font-semibold text-gray-900"><?= htmlspecialchars($plan['name']) ?></h3>
                            <div class="mt-4">
                                <span class="text-3xl font-bold text-gray-900">₹<?= number_format($plan['price']) ?></span>
                            </div>
                            <p class="mt-2 text-sm text-gray-600"><?= $plan['interviews_count'] ?> Mock Interviews</p>
                            
                            <?php if ($plan['interviews_count'] > 1): ?>
                            <p class="mt-2 text-sm text-green-600 font-medium">
                                Save ₹<?= number_format((100 * $plan['interviews_count']) - $plan['price']) ?>
                            </p>
                            <?php endif; ?>
                        </div>
                        
                        <ul class="mt-6 text-sm text-gray-700 space-y-3">
                            <li class="flex items-center"><i class="fas fa-check text-green-500 mr-3"></i><?= $plan['interviews_count'] ?> Mock Interview<?= $plan['interviews_count'] > 1 ? 's' : '' ?></li>
                            <li class="flex items-center"><i class="fas fa-check text-green-500 mr-3"></i>60 minutes each session</li>
                            <li class="flex items-center"><i class="fas fa-check text-green-500 mr-3"></i>Detailed feedback</li>
                            <li class="flex items-center"><i class="fas fa-check text-green-500 mr-3"></i>Video recording</li>
                            <?php if ($plan['interviews_count'] >= 10): ?>
                            <li class="flex items-center"><i class="fas fa-check text-green-500 mr-3"></i>Priority booking</li>
                            <?php endif; ?>
                            <?php if ($plan['interviews_count'] >= 20): ?>
                            <li class="flex items-center"><i class="fas fa-check text-green-500 mr-3"></i>Choose any interviewer</li>
                            <?php endif; ?>
                            <?php if ($plan['interviews_count'] >= 50): ?>
                            <li class="flex items-center"><i class="fas fa-check text-green-500 mr-3"></i>Personal coach assigned</li>
                            <?php endif; ?>
                        </ul>
                        
                        <div class="mt-8">
                            <button onclick="purchasePlan(<?= $plan['id'] ?>)" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-3 rounded-lg font-medium transition-colors">
                                Purchase Plan
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Become Interviewer Tab -->
            <div id="interviewer-apply-tab" class="tab-content hidden">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Apply to Become a Mock Interviewer</h2>
                
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="mb-6 p-4 bg-yellow-50 rounded-lg border border-yellow-200">
                        <div class="flex">
                            <i class="fas fa-info-circle text-yellow-600 mt-1 mr-3"></i>
                            <div>
                                <h4 class="font-medium text-yellow-800">Application Fee Required</h4>
                                <p class="text-sm text-yellow-700 mt-1">
                                    A one-time registration fee of ₹500 is required to become a mock interviewer. 
                                    This helps us maintain quality standards and covers verification costs.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <form id="interviewer-apply-form">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                <input type="text" name="name" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                <input type="email" name="email" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                <input type="tel" name="phone" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Primary Specialization</label>
                                <select name="specialization" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select Specialization</option>
                                    <option value="Frontend Development">Frontend Development</option>
                                    <option value="Backend Development">Backend Development</option>
                                    <option value="Full Stack Development">Full Stack Development</option>
                                    <option value="Data Science">Data Science</option>
                                    <option value="Machine Learning">Machine Learning</option>
                                    <option value="DevOps">DevOps</option>
                                    <option value="Mobile Development">Mobile Development</option>
                                    <option value="System Design">System Design</option>
                                    <option value="Product Management">Product Management</option>
                                    <option value="UI/UX Design">UI/UX Design</option>
                                </select>
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Years of Experience</label>
                                <select name="experience" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select Experience</option>
                                    <option value="1-2 years">1-2 years</option>
                                    <option value="3-5 years">3-5 years</option>
                                    <option value="6-10 years">6-10 years</option>
                                    <option value="10+ years">10+ years</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">Professional Bio</label>
                            <textarea name="bio" rows="4" required placeholder="Tell us about your professional background, expertise, and why you'd be a great mock interviewer..." class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                        </div>
                        
                        <div class="mt-6 p-4 bg-green-50 rounded-lg">
                            <h4 class="font-medium text-green-900 mb-2">Interviewer Benefits</h4>
                            <ul class="text-sm text-green-700 space-y-1">
                                <li>• Earn ₹80 per interview (after 20% platform fee)</li>
                                <li>• Flexible scheduling - work when you want</li>
                                <li>• Help others advance their careers</li>
                                <li>• Build your reputation and network</li>
                            </ul>
                        </div>
                        
                        <div class="mt-6 p-4 bg-blue-50 rounded-lg">
                            <div class="flex justify-between items-center">
                                <div>
                                    <h4 class="font-medium text-blue-900">Registration Fee</h4>
                                    <p class="text-sm text-blue-700">One-time application processing fee</p>
                                </div>
                                <div class="text-right">
                                    <span class="text-2xl font-bold text-blue-900">₹500</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-6 flex justify-end">
                            <button type="submit" class="bg-green-500 hover:bg-green-600 text-white px-8 py-3 rounded-lg font-medium transition-colors">
                                <i class="fas fa-paper-plane mr-2"></i> Submit Application & Pay
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Interview History Tab -->
            <div id="history-tab" class="tab-content hidden">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Interview History</h2>
                
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Interviewer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Meeting Link</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Amount</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($interview_history as $interview): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= date('M d, Y', strtotime($interview['preferred_date'])) ?>
                                    <br><span class="text-xs text-gray-500"><?= date('g:i A', strtotime($interview['preferred_time'])) ?></span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                    <?= htmlspecialchars($interview['interviewer_name'] ?? 'Any Available') ?>
                                    <?php if ($interview['interviewer_email']): ?>
                                    <br><span class="text-xs text-gray-500"><?= htmlspecialchars($interview['interviewer_email']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($interview['interview_type']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        <?= $interview['status'] == 'completed' ? 'bg-green-100 text-green-800' : 
                                           ($interview['status'] == 'approved' ? 'bg-blue-100 text-blue-800' : 
                                           ($interview['status'] == 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800')) ?>">
                                        <?= ucfirst($interview['status']) ?>
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <?php if ($interview['meeting_link'] && $interview['status'] == 'approved'): ?>
                                    <a href="<?= htmlspecialchars($interview['meeting_link']) ?>" target="_blank" class="text-blue-600 hover:text-blue-900">
                                        <i class="fas fa-video mr-1"></i> Join Meeting
                                    </a>
                                    <?php else: ?>
                                    <span class="text-gray-400">N/A</span>
                                    <?php endif; ?>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">₹<?= number_format($interview['amount'], 2) ?></td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (count($interview_history) == 0): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">No interviews booked yet</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Profile Tab -->
            <div id="profile-tab" class="tab-content hidden">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">My Profile</h2>
                
                <div class="bg-white rounded-lg shadow-md p-6">
                    <form>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Full Name</label>
                                <input type="text" value="<?= htmlspecialchars($user['name']) ?>" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Email Address</label>
                                <input type="email" value="<?= htmlspecialchars($user['email']) ?>" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Phone Number</label>
                                <input type="tel" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Member Since</label>
                                <input type="text" value="<?= date('M d, Y', strtotime($user['created_at'])) ?>" readonly class="w-full border border-gray-300 rounded-md px-3 py-2 bg-gray-100">
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                                Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Payment QR Modal -->
    <div id="payment-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3 text-center">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Complete Payment</h3>
                <div class="mb-4">
                    <div class="bg-gray-100 p-4 rounded-lg mb-4">
                        <i class="fas fa-qrcode text-6xl text-gray-400"></i>
                        <p class="text-sm text-gray-600 mt-2">Scan QR Code to Pay</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl font-bold text-gray-900" id="payment-amount">₹0.00</p>
                        <p class="text-sm text-gray-600" id="payment-description">Payment for services</p>
                    </div>
                </div>
                
                <div class="space-y-3">
                    <button onclick="simulatePayment()" class="w-full bg-green-500 hover:bg-green-600 text-white py-2 rounded-lg transition-colors">
                        <i class="fas fa-mobile-alt mr-2"></i> Pay with UPI
                    </button>
                    <button onclick="simulatePayment()" class="w-full bg-blue-500 hover:bg-blue-600 text-white py-2 rounded-lg transition-colors">
                        <i class="fas fa-credit-card mr-2"></i> Pay with Card
                    </button>
                </div>
                
                <div class="mt6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Specialization</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Experience</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rating</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Interviews</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($interviewers as $interviewer): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= htmlspecialchars($interviewer['name']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($interviewer['email']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($interviewer['specialization']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($interviewer['experience']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    <div class="flex items-center">
                                        <span class="text-yellow-400 mr-1">★</span>
                                        <?= number_format($interviewer['rating'], 1) ?>
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= $interviewer['total_interviews'] ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button class="text-indigo-600 hover:text-indigo-900 mr-3">Edit</button>
                                    <button class="text-red-600 hover:text-red-900">Deactivate</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Applications Tab -->
            <div id="applications-tab" class="tab-content hidden">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Interviewer Applications</h2>
                
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Specialization</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Experience</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Applied Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($pending_applications as $application): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= htmlspecialchars($application['name']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($application['email']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($application['specialization']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($application['experience']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= date('M d, Y', strtotime($application['applied_at'])) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button onclick="approveApplication(<?= $application['id'] ?>)" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs mr-2">Approve</button>
                                    <button onclick="rejectApplication(<?= $application['id'] ?>)" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs">Reject</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (count($pending_applications) == 0): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">No pending applications</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Interview Requests Tab -->
            <div id="interviews-tab" class="tab-content hidden">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Interview Requests</h2>
                
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Interviewer</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Preferred Date</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($pending_interviews as $interview): ?>
                            <tr>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900"><?= htmlspecialchars($interview['user_name']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($interview['interviewer_name'] ?? 'Any') ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= htmlspecialchars($interview['interview_type']) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"><?= date('M d, Y', strtotime($interview['preferred_date'])) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">₹<?= number_format($interview['amount'], 2) ?></td>
                                <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                    <button onclick="approveInterview(<?= $interview['id'] ?>)" class="bg-green-500 hover:bg-green-600 text-white px-3 py-1 rounded text-xs mr-2">Approve</button>
                                    <button onclick="rejectInterview(<?= $interview['id'] ?>)" class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs">Reject</button>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            <?php if (count($pending_interviews) == 0): ?>
                            <tr>
                                <td colspan="6" class="px-6 py-4 text-center text-gray-500">No pending interview requests</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Payments Tab -->
            <div id="payments-tab" class="tab-content hidden">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Recent Payments</h2>
                
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                                <th class="px-