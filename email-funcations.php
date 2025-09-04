<?php
// email-functions.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Make sure you have PHPMailer installed via Composer

function sendVerificationEmail($email, $username, $verification_pin, $verification_token) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com'; // Set your SMTP server
        $mail->SMTPAuth   = true;
        $mail->Username   = 'info@sanixtech.in'; // Your email
        $mail->Password   = 'your-app-password'; // Your app password (not regular password)
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Recipients
        $mail->setFrom('info@sanixtech.in', 'Sanix Technology');
        $mail->addAddress($email, $username);
        $mail->addReplyTo('info@sanixtech.in', 'Sanix Technology');

        // Content
        $mail->isHTML(true);
        $mail->Subject = 'Email Verification - Sanix Technology';
        
        $verification_link = "https://yourdomain.com/verify-email.php?token=" . $verification_token;
        
        $mail->Body = getVerificationEmailTemplate($username, $verification_pin, $verification_link);
        $mail->AltBody = "Hello $username,\n\nWelcome to Sanix Technology! Please verify your email address using this PIN: $verification_pin\n\nOr click this link: $verification_link\n\nThis verification code will expire in 24 hours.\n\nBest regards,\nSanix Technology Team";

        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email sending failed: {$mail->ErrorInfo}");
        return false;
    }
}

function getVerificationEmailTemplate($username, $verification_pin, $verification_link) {
    return "
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Email Verification</title>
        <style>
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                line-height: 1.6;
                color: #333;
                max-width: 600px;
                margin: 0 auto;
                padding: 20px;
                background-color: #f4f4f4;
            }
            .email-container {
                background: #ffffff;
                border-radius: 10px;
                padding: 40px;
                box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            }
            .header {
                text-align: center;
                margin-bottom: 30px;
                padding-bottom: 20px;
                border-bottom: 2px solid #667eea;
            }
            .logo {
                font-size: 2rem;
                font-weight: bold;
                color: #667eea;
                margin-bottom: 10px;
            }
            .title {
                color: #333;
                font-size: 1.8rem;
                margin-bottom: 10px;
            }
            .subtitle {
                color: #666;
                font-size: 1rem;
            }
            .pin-container {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                color: white;
                padding: 30px;
                border-radius: 10px;
                text-align: center;
                margin: 30px 0;
            }
            .pin-label {
                font-size: 0.9rem;
                margin-bottom: 10px;
                opacity: 0.9;
            }
            .pin-code {
                font-size: 2.5rem;
                font-weight: bold;
                letter-spacing: 0.5rem;
                margin: 10px 0;
                font-family: 'Courier New', monospace;
            }
            .btn-verify {
                display: inline-block;
                background: #28a745;
                color: white;
                padding: 15px 30px;
                text-decoration: none;
                border-radius: 8px;
                font-weight: 600;
                margin: 20px 0;
                text-align: center;
            }
            .instructions {
                background: #f8f9fa;
                border-left: 4px solid #667eea;
                padding: 20px;
                margin: 20px 0;
                border-radius: 0 8px 8px 0;
            }
            .footer {
                text-align: center;
                margin-top: 30px;
                padding-top: 20px;
                border-top: 1px solid #eee;
                color: #666;
                font-size: 0.9rem;
            }
            .warning {
                background: #fff3cd;
                border: 1px solid #ffeaa7;
                color: #856404;
                padding: 15px;
                border-radius: 8px;
                margin: 20px 0;
            }
        </style>
    </head>
    <body>
        <div class='email-container'>
            <div class='header'>
                <div class='logo'>🚀 SANIX TECHNOLOGY</div>
                <h1 class='title'>Welcome to Our Platform!</h1>
                <p class='subtitle'>Please verify your email address to get started</p>
            </div>
            
            <p>Hello <strong>$username</strong>,</p>
            
            <p>Thank you for registering with Sanix Technology! We're excited to have you on board. To ensure the security of your account, please verify your email address using the verification code below:</p>
            
            <div class='pin-container'>
                <div class='pin-label'>Your Verification Code</div>
                <div class='pin-code'>$verification_pin</div>
            </div>
            
            <div class='instructions'>
                <h3>📋 How to verify:</h3>
                <ol>
                    <li>Click the verification button below to open the verification page</li>
                    <li>Enter the 6-digit code shown above</li>
                    <li>Your account will be activated immediately</li>
                </ol>
            </div>
            
            <div style='text-align: center;'>
                <a href='$verification_link' class='btn-verify'>
                    ✅ Verify My Email Address
                </a>
            </div>
            
            <div class='warning'>
                <strong>⚠️ Important:</strong>
                <ul style='margin: 10px 0 0 0; padding-left: 20px;'>
                    <li>This verification code will expire in <strong>24 hours</strong></li>
                    <li>If you didn't create this account, please ignore this email</li>
                    <li>For security reasons, never share this code with anyone</li>
                </ul>
            </div>
            
            <p>If the button above doesn't work, you can copy and paste this link into your browser:</p>
            <p style='word-break: break-all; background: #f8f9fa; padding: 10px; border-radius: 4px; font-family: monospace;'>$verification_link</p>
            
            <div class='footer'>
                <p><strong>Need help?</strong> Contact our support team at <a href='mailto:support@sanixtech.in'>support@sanixtech.in</a></p>
                <p>Best regards,<br><strong>The Sanix Technology Team</strong></p>
                <hr>
                <p style='font-size: 0.8rem; color: #999;'>
                    This is an automated message. Please do not reply to this email.<br>
                    © 2025 Sanix Technology. All rights reserved.
                </p>
            </div>
        </div>
    </body>
    </html>";
}

function resendVerificationEmail($email) {
    global $pdo;
    
    try {
        // Get user information
        $stmt = $pdo->prepare("SELECT user_id, username, email FROM users WHERE email = ? AND is_verified = FALSE");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if (!$user) {
            return ['success' => false, 'message' => 'User not found or already verified.'];
        }
        
        // Check if we can resend (limit to 3 attempts per hour)
        $stmt = $pdo->prepare("SELECT COUNT(*) as attempts FROM email_verification_logs 
                               WHERE user_id = ? AND sent_at > DATE_SUB(NOW(), INTERVAL 1 HOUR)");
        $stmt->execute([$user['user_id']]);
        $result = $stmt->fetch();
        
        if ($result['attempts'] >= 3) {
            return ['success' => false, 'message' => 'Too many verification emails sent. Please wait before requesting another.'];
        }
        
        // Generate new PIN and token
        $new_pin = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        $new_token = bin2hex(random_bytes(32));
        
        // Update user with new verification details
        $stmt = $pdo->prepare("UPDATE users SET verification_pin = ?, verification_token = ? WHERE user_id = ?");
        $stmt->execute([$new_pin, $new_token, $user['user_id']]);
        
        // Log the resend attempt
        $stmt = $pdo->prepare("INSERT INTO email_verification_logs (user_id, email, verification_pin, verification_token, sent_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->execute([$user['user_id'], $user['email'], $new_pin, $new_token]);
        
        // Send the email
        $emailSent = sendVerificationEmail($user['email'], $user['username'], $new_pin, $new_token);
        
        if ($emailSent) {
            return ['success' => true, 'message' => 'Verification email resent successfully!', 'token' => $new_token];
        } else {
            return ['success' => false, 'message' => 'Failed to send verification email. Please try again later.'];
        }
        
    } catch (PDOException $e) {
        error_log("Resend verification error: " . $e->getMessage());
        return ['success' => false, 'message' => 'An error occurred. Please try again later.'];
    }
}
?>