
<?php
// payment_handler.php - Payment Processing
session_start();
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$database = new Database();
$db = $database->getConnection();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $payment_id = $_POST['payment_id'] ?? '';
    $transaction_id = 'TXN_' . uniqid() . '_' . time();
    
    if ($action === 'process_payment' && $payment_id) {
        try {
            // Simulate payment processing
            $stmt = $db->prepare("UPDATE payments SET status = 'completed', transaction_id = ?, completed_at = NOW() WHERE id = ? AND user_id = ?");
            $stmt->execute([$transaction_id, $payment_id, $_SESSION['user_id']]);
            
            // Get payment details to process the service
            $stmt = $db->prepare("SELECT * FROM payments WHERE id = ? AND user_id = ?");
            $stmt->execute([$payment_id, $_SESSION['user_id']]);
            $payment = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($payment) {
                switch ($payment['payment_type']) {
                    case 'single_interview':
                        // Interview payment completed - no additional action needed
                        // Admin will approve the interview request
                        break;
                        
                    case 'subscription':
                        // Activate subscription
                        $stmt = $db->prepare("UPDATE user_subscriptions SET is_active = TRUE WHERE id = ?");
                        $stmt->execute([$payment['reference_id']]);
                        break;
                        
                    case 'interviewer_fee':
                        // Interviewer application fee paid - no additional action needed
                        // Admin will review the application
                        break;
                }
                
                echo json_encode([
                    'success' => true, 
                    'message' => 'Payment processed successfully',
                    'transaction_id' => $transaction_id
                ]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Payment not found']);
            }
            
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Payment processing failed']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Invalid request']);
    }
} else {
    header('Location: user_dashboard.php');
    exit();
}
?>-4">
                                <span class="text-3xl font-bold text-blue-900">₹100</span>
                                <span class="text-blue-600">/interview</span>
                            </div>
                            <p class="mt-4 text-sm text-blue-700">Perfect for trying out our service</p>
                            <ul class="mt-4 text-sm text-blue-700 space-y-2">
                                <li>• 1 Mock Interview</li>
                                <li>• 60 minutes session</li>
                                <li>• Detailed feedback</li>
                                <li>• Meeting recording</li>
                            </ul>
                        </div>
                    </div>

                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-6">
                        <div class="text-center">
                            <h3 class="text-lg font-semibold text-orange-900">Bronze Card</h3>
                            <div class="mt-4">
                                <span class="text-3xl font-bold text-orange-900">₹800</span>
                                <span class="text-orange-600">/10 interviews</span>
                            </div>
                            <p class="mt-4 text-sm text-orange-700">Save ₹200 compared to single interviews</p>
                            <ul class="mt-4 text-sm text-orange-700 space-y-2">
                                <li>• 10 Mock Interviews</li>
                                <li>• 60 minutes each</li>
                                <li>• Priority booking</li>
                                <li>• Progress tracking</li>
                            </ul>
                        </div>
                    </div>

                    <div class="bg-gray-50 border border-gray-200 rounded-lg p-6">
                        <div class="text-center">
                            <h3 class="text-lg font-semibold text-gray-900">Silver Card</h3>
                            <div class="mt-4">
                                <span class="text-3xl font-bold text-gray-900">₹1,400</span>
                                <span class="text-gray-600">/20 interviews</span>
                            </div>
                            <p class="mt-4 text-sm text-gray-700">Save ₹600 - Most popular choice</p>
                            <ul class="mt-4 text-sm text-gray-700 space-y-2">
                                <li>• 20 Mock Interviews</li>
                                <li>• 60 minutes each</li>
                                <li>• Choose any interviewer</li>
                                <li>• Advanced analytics</li>
                            </ul>
                        </div>
                    </div>

                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                        <div class="text-center">
                            <h3 class="text-lg font-semibold text-yellow-900">Gold Card</h3>
                            <div class="mt-4">
                                <span class="text-3xl font-bold text-yellow-900">₹3,000</span>
                                <span class="text-yellow-600">/50 interviews</span>
                            </div>
                            <p class="mt-4 text-sm text-yellow-700">Save ₹2,000 - Best value for serious prep</p>
                            <ul class="mt-4 text-sm text-yellow-700 space-y-2">
                                <li>• 50 Mock Interviews</li>
                                <li>• 60 minutes each</li>
                                <li>• Premium interviewers</li>
                                <li>• Personalized coaching</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Pricing Management -->
                <div class="mt-8 bg-white rounded-lg shadow-md p-6">
                    <h3 class="text-lg font-semibold mb-4">Special Pricing</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="border rounded-lg p-4">
                            <h4 class="font-medium text-gray-900">Interviewer Registration Fee</h4>
                            <p class="text-2xl font-bold text-red-600 mt-2">₹500</p>
                            <p class="text-sm text-gray-600 mt-1">One-time fee for becoming a mock interviewer</p>
                        </div>
                        <div class="border rounded-lg p-4">
                            <h4 class="font-medium text-gray-900">Platform Commission</h4>
                            <p class="text-2xl font-bold text-blue-600 mt-2">20%</p>
                            <p class="text-sm text-gray-600 mt-1">Commission per interview for interviewers</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Interviewer Modal -->
    <div id="add-interviewer-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Add New Mock Interviewer</h3>
                <form id="add-interviewer-form">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Name</label>
                        <input type="text" name="name" required class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <input type="email" name="email" required class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Phone</label>
                        <input type="text" name="phone" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Specialization</label>
                        <select name="specialization" required class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Experience</label>
                        <select name="experience" required class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Select Experience</option>
                            <option value="1-2 years">1-2 years</option>
                            <option value="3-5 years">3-5 years</option>
                            <option value="6-10 years">6-10 years</option>
                            <option value="10+ years">10+ years</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Bio</label>
                        <textarea name="bio" rows="3" class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="document.getElementById('add-interviewer-modal').classList.add('hidden')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">Add Interviewer</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Approve Interview Modal -->
    <div id="approve-interview-modal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Approve Interview</h3>
                <form id="approve-interview-form">
                    <input type="hidden" name="interview_id" id="interview_id">
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700">Meeting Link</label>
                        <input type="url" name="meeting_link" placeholder="https://meet.google.com/xxx-xxx-xxx" required class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <p class="text-xs text-gray-500 mt-1">Provide Google Meet, Zoom, or any video conferencing link</p>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="document.getElementById('approve-interview-modal').classList.add('hidden')" class="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400">Cancel</button>
                        <button type="submit" class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">Approve & Schedule</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Hide all tabs
            document.querySelectorAll('.tab-content').forEach(tab => {
                tab.classList.add('hidden');
            });
            
            // Show selected tab
            document.getElementById(tabName + '-tab').classList.remove('hidden');
            
            // Update active nav item
            document.querySelectorAll('nav a').forEach(link => {
                link.classList.remove('bg-gray-700', 'text-white');
                link.classList.add('text-gray-300');
            });
            event.target.classList.add('bg-gray-700', 'text-white');
            event.target.classList.remove('text-gray-300');
        }

        // Add Interviewer Form Handler
        document.getElementById('add-interviewer-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'add_interviewer');
            
            fetch('admin_dashboard.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });

        // Approve Application
        function approveApplication(id) {
            if (confirm('Are you sure you want to approve this interviewer application?')) {
                const formData = new FormData();
                formData.append('action', 'approve_interviewer');
                formData.append('id', id);
                
                fetch('admin_dashboard.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
            }
        }

        // Reject Application
        function rejectApplication(id) {
            if (confirm('Are you sure you want to reject this application?')) {
                const formData = new FormData();
                formData.append('action', 'reject_application');
                formData.append('id', id);
                
                fetch('admin_dashboard.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred. Please try again.');
                });
            }
        }

        // Approve Interview
        function approveInterview(id) {
            document.getElementById('interview_id').value = id;
            document.getElementById('approve-interview-modal').classList.remove('hidden');
        }

        // Reject Interview
        function rejectInterview(id) {
            if (confirm('Are you sure you want to reject this interview request?')) {
                const formData = new FormData();
                formData.append('action', 'reject_interview');
                formData.append('id', id);
                
                fetch('admin_dashboard.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                });
            }
        }

        // Approve Interview Form Handler
        document.getElementById('approve-interview-form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('action', 'approve_interview');
            formData.append('id', document.getElementById('interview_id').value);
            
            fetch('admin_dashboard.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });

        // Initialize dashboard
        showTab('dashboard');
    </script>
</body>
</html>