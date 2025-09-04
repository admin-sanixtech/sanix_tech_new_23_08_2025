<?php
include 'db_connection.php';
include 'email-functions.php';

$token = $_GET['token'] ?? '';
$message = '';
$messageType = '';

// Get user email from token
$userEmail = '';
if (!empty($token)) {
    try {
        $stmt = $pdo->prepare("SELECT email FROM users WHERE verification_token = ? AND is_verified = FALSE");
        $stmt->execute([$token]);
        $user = $stmt->fetch();
        if ($user) {
            $userEmail = $user['email'];
        }
    } catch (PDOException $e) {
        // Handle error
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    
    if (empty($email)) {
        $message = 'Please enter your email address.';
        $messageType = 'error';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = 'Please enter a valid email address.';
        $messageType = 'error';
    } else {
        $result = resendVerificationEmail($email);
        $message = $result['message'];
        $messageType = $result['success'] ? 'success' : 'error';
        
        if ($result['success']) {
            // Redirect to verification page with new token
            echo "<script>setTimeout(function(){ window.location.href='verify-email.php?token=" . $result['token'] . "'; }, 3000);</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resend Verification - Sanix Technology</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .resend-container {
            max-width: 500px;
            width: 100%;
            margin: 0 auto;
            padding: 40px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            text-align: center;
        }

        .resend-icon {
            font-size: 4rem;
            color: #667eea;
            margin-bottom: 20px;
        }

        .resend-title {
            color: #333;
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .resend-subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 1rem;
            line-height: 1.6;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 15px;
            font-size: 1rem;
            text-align: center;
            margin: 20px 0;
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            outline: none;
        }

        .btn-resend {
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

        .btn-resend:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .alert {
            border-radius: 12px;
            margin: 20px 0;
            border: none;
            text-align: left;
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

        .back-link {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            margin-top: 30px;
            display: inline-block;
        }

        .back-link:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        .info-box {
            background: rgba(102, 126, 234, 0.1);
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
            border: 1px solid rgba(102, 126, 234, 0.2);
            text-align: left;
        }

        .info-box h5 {
            color: #667eea;
            margin-bottom: 15px;
        }

        .info-box ul {
            margin-bottom: 0;
            padding-left: 20px;
        }

        .info-box li {
            margin-bottom: 8px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="resend-container">
        <div class="resend-icon">
            <i class="fas fa-paper-plane"></i>
        </div>
        
        <h2 class="resend-title">Resend Verification</h2>
        <p class="resend-subtitle">
            Didn't receive the verification email? Don't worry! 
            Enter your email address below and we'll send you a new verification code.
        </p>

        <?php if (!empty($message)): ?>
            <div class="alert <?= $messageType === 'success' ? 'alert-success' : 'alert-danger' ?>">
                <i class="fas <?= $messageType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?>"></i>
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <?php if ($messageType !== 'success'): ?>
            <form method="POST" action="">
                <div class="form-group">
                    <input type="email" 
                           name="email" 
                           class="form-control" 
                           placeholder="Enter your email address"
                           value="<?= htmlspecialchars($userEmail) ?>"
                           required
                           autocomplete="email">
                </div>
                
                <button type="submit" class="btn btn-resend">
                    <i class="fas fa-paper-plane"></i> Resend Verification Email
                </button>
            </form>

            <div class="info-box">
                <h5><i class="fas fa-info-circle"></i> Important Information</h5>
                <ul>
                    <li>You can request up to 3 verification emails per hour</li>
                    <li>Verification codes expire after 24 hours</li>
                    <li>Check your spam/junk folder if you don't see the email</li>
                    <li>Make sure the email address is correct</li>
                </ul>
            </div>
        <?php endif; ?>
        
        <div style="margin-top: 30px;">
            <?php if (!empty($token)): ?>
                <a href="verify-email.php?token=<?= htmlspecialchars($token) ?>" class="back-link">
                    <i class="fas fa-arrow-left"></i> Back to Verification
                </a>
            <?php else: ?>
                <a href="login.php" class="back-link">
                    <i class="fas fa-arrow-left"></i> Back to Login
                </a>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Auto-focus on email input
        document.addEventListener('DOMContentLoaded', function() {
            const emailInput = document.querySelector('input[name="email"]');
            if (emailInput && !emailInput.value) {
                emailInput.focus();
            }
        });

        // Form validation
        document.querySelector('form')?.addEventListener('submit', function(e) {
            const email = this.email.value.trim();
            if (!email) {
                e.preventDefault();
                alert('Please enter your email address.');
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