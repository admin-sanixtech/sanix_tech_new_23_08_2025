<?php
include 'db_connection.php';
session_start();

$token = $_GET['token'] ?? '';
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $verification_pin = trim($_POST['verification_pin']);
    $token = $_POST['token'];
    
    if (empty($verification_pin) || empty($token)) {
        $message = 'Please enter the verification PIN.';
        $messageType = 'error';
    } else {
        try {
            // Check if the token and PIN are valid
            $stmt = $pdo->prepare("SELECT u.user_id, u.email, u.username 
                                   FROM users u 
                                   WHERE u.verification_token = ? 
                                   AND u.verification_pin = ? 
                                   AND u.is_verified = FALSE 
                                   AND u.created_at > DATE_SUB(NOW(), INTERVAL 24 HOUR)");
            $stmt->execute([$token, $verification_pin]);
            $user = $stmt->fetch();
            
            if ($user) {
                // Update user as verified
                $stmt = $pdo->prepare("UPDATE users 
                                       SET is_verified = TRUE, 
                                           email_verified_at = NOW(), 
                                           status = 'active',
                                           verification_pin = NULL, 
                                           verification_token = NULL 
                                       WHERE user_id = ?");
                $stmt->execute([$user['user_id']]);
                
                // Log the verification
                $stmt = $pdo->prepare("UPDATE email_verification_logs 
                                       SET status = 'verified', verified_at = NOW() 
                                       WHERE user_id = ? AND verification_token = ?");
                $stmt->execute([$user['user_id'], $token]);
                
                $message = 'Email verified successfully! You can now login to your account.';
                $messageType = 'success';
                
                // Redirect to login after 3 seconds
                echo "<script>setTimeout(function(){ window.location.href='login.php'; }, 3000);</script>";
            } else {
                // Increment attempt count
                $stmt = $pdo->prepare("UPDATE email_verification_logs 
                                       SET attempts = attempts + 1 
                                       WHERE verification_token = ?");
                $stmt->execute([$token]);
                
                $message = 'Invalid verification PIN or token expired. Please try again or request a new verification email.';
                $messageType = 'error';
            }
        } catch (PDOException $e) {
            $message = 'An error occurred during verification. Please try again.';
            $messageType = 'error';
        }
    }
}

// Get user info for display
$userInfo = null;
if (!empty($token)) {
    try {
        $stmt = $pdo->prepare("SELECT username, email FROM users WHERE verification_token = ? AND is_verified = FALSE");
        $stmt->execute([$token]);
        $userInfo = $stmt->fetch();
    } catch (PDOException $e) {
        // Handle error silently
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification - Sanix Technology</title>
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

        .verification-container {
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

        .verification-icon {
            font-size: 4rem;
            color: #667eea;
            margin-bottom: 20px;
        }

        .verification-title {
            color: #333;
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .verification-subtitle {
            color: #666;
            margin-bottom: 30px;
            font-size: 1rem;
        }

        .pin-input {
            font-size: 2rem;
            text-align: center;
            letter-spacing: 0.5rem;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 15px;
            margin: 20px 0;
            max-width: 300px;
            width: 100%;
        }

        .pin-input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            outline: none;
        }

        .btn-verify {
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

        .btn-verify:hover {
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

        .resend-link {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
            margin-top: 20px;
            display: inline-block;
        }

        .resend-link:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        .user-info {
            background: rgba(102, 126, 234, 0.1);
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid rgba(102, 126, 234, 0.2);
        }
    </style>
</head>
<body>
    <div class="verification-container">
        <div class="verification-icon">
            <i class="fas fa-envelope-circle-check"></i>
        </div>
        
        <h2 class="verification-title">Verify Your Email</h2>
        
        <?php if ($userInfo): ?>
            <div class="user-info">
                <strong><?= htmlspecialchars($userInfo['username']) ?></strong><br>
                <small><?= htmlspecialchars($userInfo['email']) ?></small>
            </div>
        <?php endif; ?>
        
        <p class="verification-subtitle">
            We've sent a 6-digit verification code to your email address. 
            Please enter the code below to verify your account.
        </p>

        <?php if (!empty($message)): ?>
            <div class="alert <?= $messageType === 'success' ? 'alert-success' : 'alert-danger' ?>">
                <i class="fas <?= $messageType === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle' ?>"></i>
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <?php if (empty($token)): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                Invalid verification link. Please check your email for the correct link.
            </div>
            <a href="login.php" class="btn btn-verify">Go to Login</a>
        <?php elseif ($messageType !== 'success'): ?>
            <form method="POST" action="">
                <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                
                <div class="form-group">
                    <input type="text" 
                           name="verification_pin" 
                           class="form-control pin-input" 
                           placeholder="000000" 
                           maxlength="6" 
                           pattern="[0-9]{6}" 
                           required
                           autocomplete="off">
                </div>
                
                <button type="submit" class="btn btn-verify">
                    <i class="fas fa-check"></i> Verify Email
                </button>
            </form>
            
            <a href="resend-verification.php?token=<?= htmlspecialchars($token) ?>" class="resend-link">
                <i class="fas fa-redo"></i> Resend Verification Code
            </a>
        <?php endif; ?>
        
        <div style="margin-top: 30px;">
            <a href="login.php" style="color: #666; text-decoration: none;">
                <i class="fas fa-arrow-left"></i> Back to Login
            </a>
        </div>
    </div>

    <script>
        // Auto-focus on PIN input
        document.addEventListener('DOMContentLoaded', function() {
            const pinInput = document.querySelector('.pin-input');
            if (pinInput) {
                pinInput.focus();
            }
        });

        // Only allow numbers in PIN input
        document.querySelector('.pin-input')?.addEventListener('input', function(e) {
            this.value = this.value.replace(/[^0-9]/g, '');
        });

        // Auto-submit when 6 digits are entered
        document.querySelector('.pin-input')?.addEventListener('input', function(e) {
            if (this.value.length === 6) {
                // Small delay to show the complete PIN
                setTimeout(() => {
                    this.form.submit();
                }, 500);
            }
        });
    </script>
</body>
</html>