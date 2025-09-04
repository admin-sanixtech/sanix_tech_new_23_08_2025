<?php
//login.php
// Enable error reporting (optional, for debugging)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once 'db_connection.php';

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $message = 'Please enter both email and password.';
        $messageType = 'error';
    } else {
        try {
            $stmt = $pdo->prepare("SELECT user_id, username, email, password, role, status, is_verified, verification_token 
                                   FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password'])) {
                if (!$user['is_verified']) {
                    $message = 'Please verify your email address before logging in.';
                    $messageType = 'warning';
                    $unverified_token = $user['verification_token'];
                } elseif ($user['status'] !== 'active') {
                    $message = 'Your account is currently inactive. Please contact support.';
                    $messageType = 'error';
                } else {
                    // Update last login
                    $stmt = $pdo->prepare("UPDATE users SET last_login = NOW() WHERE user_id = ?");
                    $stmt->execute([$user['user_id']]);
                    
                    // Set session variables
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['role'] = $user['role'];
                    
                    // Redirect based on role
                    if ($user['role'] === 'admin') {
                        header('Location: admin/admin_dashboard.php');
                    } elseif ($user['role'] === 'user') {
                        header('Location: user/user_home.php');
                    } else {
                        header('Location: index.php');
                    }
                    exit();
                }
            } else {
                $message = 'Invalid email or password.';
                $messageType = 'error';
            }
        } catch (PDOException $e) {
            $message = 'Login failed. Please try again.';
            $messageType = 'error';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login - Sanix Technology</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap & Font Awesome -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .login-container {
            max-width: 500px;
            width: 100%;
            margin: 50px auto;
            padding: 40px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .login-header h2 {
            color: #333;
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .login-header p {
            color: #666;
            font-size: 0.9rem;
        }

        .form-group label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 15px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            background: #fff;
        }

        .btn-login {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 12px;
            padding: 15px 30px;
            font-size: 1.1rem;
            font-weight: 600;
            color: white;
            width: 100%;
            transition: all 0.3s ease;
            margin-top: 20px;
        }

        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .alert {
            border-radius: 12px;
            margin: 20px 0;
            border: none;
        }

        .alert-success {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
            border: 1px solid rgba(40, 167, 69, 0.2);
        }

        .alert-danger {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            border: 1px solid rgba(220, 53, 69, 0.2);
        }

        .alert-warning {
            background: rgba(255, 193, 7, 0.1);
            color: #856404;
            border: 1px solid rgba(255, 193, 7, 0.2);
        }

        .register-link {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }

        .register-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .register-link a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        .verification-notice {
            background: rgba(255, 193, 7, 0.1);
            border: 1px solid rgba(255, 193, 7, 0.3);
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }

        .btn-verify-email {
            background: #ffc107;
            border: none;
            color: #212529;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
            transition: all 0.3s ease;
        }

        .btn-verify-email:hover {
            background: #e0a800;
            color: #212529;
            text-decoration: none;
            transform: translateY(-1px);
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>

<div class="login-container">
    <div class="login-header">
        <h2><i class="fas fa-sign-in-alt"></i> Welcome Back</h2>
        <p>Sign in to your Sanix Technologies account</p>
    </div>

    <?php if (!empty($message)): ?>
        <div class="alert alert-<?= $messageType === 'success' ? 'success' : ($messageType === 'warning' ? 'warning' : 'danger') ?>">
            <i class="fas <?= $messageType === 'success' ? 'fa-check-circle' : ($messageType === 'warning' ? 'fa-exclamation-triangle' : 'fa-exclamation-circle') ?>"></i>
            <?= htmlspecialchars($message) ?>
            
            <?php if ($messageType === 'warning' && isset($unverified_token)): ?>
                <div class="verification-notice">
                    <p><strong>Account Not Verified</strong></p>
                    <p>Please check your email for the verification code, or request a new one.</p>
                    <a href="verify-email.php?token=<?= htmlspecialchars($unverified_token) ?>" class="btn-verify-email">
                        <i class="fas fa-envelope-circle-check"></i> Verify Email Now
                    </a>
                    <br>
                    <a href="resend-verification.php?token=<?= htmlspecialchars($unverified_token) ?>" style="color: #856404; font-size: 0.9rem; margin-top: 10px; display: inline-block;">
                        <i class="fas fa-redo"></i> Resend Verification Email
                    </a>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="">
        <div class="form-group">
            <label for="email">
                <i class="fas fa-envelope"></i> Email Address
            </label>
            <input type="email" 
                   id="email" 
                   name="email" 
                   class="form-control" 
                   placeholder="Enter your email address"
                   value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                   required>
        </div>

        <div class="form-group">
            <label for="password">
                <i class="fas fa-lock"></i> Password
            </label>
            <input type="password" 
                   id="password" 
                   name="password" 
                   class="form-control" 
                   placeholder="Enter your password"
                   required>
        </div>

        <button type="submit" class="btn btn-login">
            <i class="fas fa-sign-in-alt"></i> Sign In
        </button>
    </form>

    <div class="register-link">
        <p>Don't have an account? <a href="register.php">Create one here</a></p>
        <p><a href="password_reset_request.php">Forgot your password?</a></p>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
    // Auto-focus on email input
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('email').focus();
    });

    // Form validation
    document.querySelector('form').addEventListener('submit', function(e) {
        const email = this.email.value.trim();
        const password = this.password.value;
        
        if (!email || !password) {
            e.preventDefault();
            alert('Please enter both email and password.');
            return false;
        }
        
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(email)) {
            e.preventDefault();
            alert('Please enter a valid email address.');
            return false;
        }
    });
</script>
</body>
</html>
