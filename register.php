try {
                    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, phone_number, photo, role, status, verification_pin, verification_token, created_at) VALUES (?, ?, ?, ?, ?, 'user', 'inactive', ?, ?, NOW())");
                    $stmt->execute([$username, $email, $hashed_password, $full_phone, $photoPath, $verification_pin, $verification_token]);

                    $user_id = $pdo->lastInsertId();

                    // Log the verification attempt
                    $stmt = $pdo->prepare("INSERT INTO email_verification_logs (user_id, email, verification_pin, verification_token, sent_at) VALUES (?, ?, ?, ?, NOW())");
                    $stmt->execute([$user_id, $email, $verification_pin, $verification_token]);

                    // Send verification email
                    $emailSent = sendVerificationEmail($email, $username, $verification_pin, $verification_token);

                    if ($emailSent) {
                        echo "<div class='validation-message success'><i class='fas fa-check-circle'></i> Registration successful! Please check your email for verification instructions.</div>";
                        echo "<script>setTimeout(function(){ window.location.href='verify-email.php?token=" . $verification_token . "'; }, 3000);</script>";
                    } else {
                        echo "<div class='validation-message error'><i class='fas fa-exclamation-circle'></i> Registration successful but failed to send verification email. <a href='resend-verification.php'>Click here to resend</a>.</div>";
                    }
                } catch (PDOException $e) {
                    if (str_contains($e->getMessage(), 'Duplicate entry')) {
                        if (str_contains($e->getMessage(), 'email')) {
                            $errors[] = "Email address is already registered.";
                        }
                        if (str_contains($e->getMessage(), 'phone_number')) {
                            $errors<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Sanix Technology</title>
    <link rel="stylesheet" href="css/user_styles.css">
    <link rel="stylesheet" href="css/register_styles.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .register-wrapper {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px 0;
        }

        .register-container {
            max-width: 500px;
            width: 100%;
            margin: 0 auto;
            padding: 40px;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .register-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .register-header h2 {
            color: #333;
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 10px;
        }

        .register-header p {
            color: #666;
            font-size: 0.9rem;
        }

        .form-group {
            position: relative;
            margin-bottom: 25px;
        }

        .form-group label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            display: flex;
            align-items: center;
        }

        .required {
            color: #e74c3c;
            margin-left: 3px;
        }

        .form-control {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 12px 15px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: rgba(255, 255, 255, 0.9);
        }

        .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
            background: #fff;
        }

        .input-group {
            position: relative;
        }

        .input-group-prepend .input-group-text {
            border: 2px solid #e9ecef;
            border-right: none;
            border-radius: 12px 0 0 12px;
            background: rgba(102, 126, 234, 0.1);
            color: #667eea;
            font-weight: 600;
        }

        .input-group .form-control {
            border-left: none;
            border-radius: 0 12px 12px 0;
        }

        .input-group:focus-within .input-group-text {
            border-color: #667eea;
        }

        .photo-upload {
            position: relative;
            display: inline-block;
            width: 100%;
        }

        .photo-upload input[type="file"] {
            opacity: 0;
            position: absolute;
            z-index: -1;
        }

        .photo-upload-label {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            border: 2px dashed #667eea;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            background: rgba(102, 126, 234, 0.05);
        }

        .photo-upload-label:hover {
            border-color: #764ba2;
            background: rgba(102, 126, 234, 0.1);
        }

        .photo-upload-text {
            color: #667eea;
            font-weight: 600;
            margin-left: 10px;
        }

        .btn-register {
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
            position: relative;
            overflow: hidden;
        }

        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-register:focus {
            outline: none;
            box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        }

        .validation-message {
            font-size: 0.85rem;
            margin-top: 5px;
            padding: 8px 12px;
            border-radius: 8px;
        }

        .validation-message.success {
            background: rgba(40, 167, 69, 0.1);
            color: #28a745;
            border: 1px solid rgba(40, 167, 69, 0.2);
        }

        .validation-message.error {
            background: rgba(220, 53, 69, 0.1);
            color: #dc3545;
            border: 1px solid rgba(220, 53, 69, 0.2);
        }

        .password-strength {
            margin-top: 8px;
        }

        .strength-bar {
            height: 4px;
            border-radius: 2px;
            background: #e9ecef;
            overflow: hidden;
        }

        .strength-fill {
            height: 100%;
            transition: all 0.3s ease;
            border-radius: 2px;
        }

        .strength-weak { background: #dc3545; width: 25%; }
        .strength-fair { background: #ffc107; width: 50%; }
        .strength-good { background: #17a2b8; width: 75%; }
        .strength-strong { background: #28a745; width: 100%; }

        .strength-text {
            font-size: 0.8rem;
            margin-top: 4px;
            font-weight: 500;
        }

        .login-link {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e9ecef;
        }

        .login-link a {
            color: #667eea;
            text-decoration: none;
            font-weight: 600;
        }

        .login-link a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        .preview-image {
            max-width: 100px;
            max-height: 100px;
            border-radius: 8px;
            margin-top: 10px;
            display: none;
        }

        @media (max-width: 576px) {
            .register-container {
                margin: 20px;
                padding: 30px 20px;
            }
        }
    </style>
</head>
<body>
<?php include 'header.php'; ?>
<?php include 'navbar.php'; ?>

<div class="register-wrapper">
    <div class="register-container">
        <div class="register-header">
            <h2><i class="fas fa-user-plus"></i> Create Account</h2>
            <p>Join Sanix Technology today and start your journey with us</p>
        </div>

        <form id="registerForm" action="register.php" method="POST" enctype="multipart/form-data">
            <!-- Username Field -->
            <div class="form-group">
                <label for="username">
                    <i class="fas fa-user"></i> Username
                    <span class="required">*</span>
                </label>
                <input type="text" id="username" name="username" class="form-control" required 
                       placeholder="Enter your username" minlength="3" maxlength="50">
                <div id="username-validation" class="validation-message" style="display: none;"></div>
            </div>

            <!-- Email Field -->
            <div class="form-group">
                <label for="email">
                    <i class="fas fa-envelope"></i> Email Address
                    <span class="required">*</span>
                </label>
                <input type="email" id="email" name="email" class="form-control" required 
                       placeholder="Enter your email address">
                <div id="email-validation" class="validation-message" style="display: none;"></div>
            </div>

            <!-- Password Field -->
            <div class="form-group">
                <label for="password">
                    <i class="fas fa-lock"></i> Password
                    <span class="required">*</span>
                </label>
                <input type="password" id="password" name="password" class="form-control" required 
                       placeholder="Create a strong password" minlength="8">
                <div class="password-strength">
                    <div class="strength-bar">
                        <div id="strength-fill" class="strength-fill"></div>
                    </div>
                    <div id="strength-text" class="strength-text"></div>
                </div>
            </div>

            <!-- Confirm Password Field -->
            <div class="form-group">
                <label for="confirm_password">
                    <i class="fas fa-lock"></i> Confirm Password
                    <span class="required">*</span>
                </label>
                <input type="password" id="confirm_password" name="confirm_password" class="form-control" required 
                       placeholder="Confirm your password">
                <div id="password-match" class="validation-message" style="display: none;"></div>
            </div>

            <!-- Phone Number Field -->
            <div class="form-group">
                <label for="phone_number">
                    <i class="fas fa-phone"></i> Phone Number
                    <span class="required">*</span>
                </label>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <select class="input-group-text" id="country_code" name="country_code" required>
                            <option value="+91">🇮🇳 +91</option>
                            <option value="+1">🇺🇸 +1</option>
                            <option value="+44">🇬🇧 +44</option>
                            <option value="+61">🇦🇺 +61</option>
                            <option value="+81">🇯🇵 +81</option>
                            <option value="+49">🇩🇪 +49</option>
                            <option value="+33">🇫🇷 +33</option>
                            <option value="+86">🇨🇳 +86</option>
                            <option value="+7">🇷🇺 +7</option>
                            <option value="+55">🇧🇷 +55</option>
                        </select>
                    </div>
                    <input type="text" id="phone_number" name="phone_number" class="form-control" required 
                           placeholder="Enter 10-digit phone number" pattern="[0-9]{10}" maxlength="10">
                </div>
                <div id="phone-validation" class="validation-message" style="display: none;"></div>
            </div>

            <!-- Photo Upload Field -->
            <div class="form-group">
                <label for="photo">
                    <i class="fas fa-camera"></i> Profile Photo
                    <small class="text-muted">(Optional)</small>
                </label>
                <div class="photo-upload">
                    <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/gif" onchange="previewImage(this)">
                    <label for="photo" class="photo-upload-label">
                        <i class="fas fa-cloud-upload-alt fa-2x"></i>
                        <div class="photo-upload-text">
                            <div>Click to upload photo</div>
                            <small>JPEG, PNG, GIF (Max: 2MB)</small>
                        </div>
                    </label>
                </div>
                <img id="image-preview" class="preview-image" alt="Preview">
            </div>

            <button type="submit" name="submit" class="btn btn-register">
                <i class="fas fa-user-plus"></i> Create Account
            </button>
        </form>

        <div class="login-link">
            <p>Already have an account? <a href="login.php">Sign in here</a></p>
        </div>

        <?php
        include 'db_connection.php';
        include 'email-functions.php';

        if (isset($_POST['submit'])) {
            $username = trim($_POST['username']);
            $email = trim($_POST['email']);
            $password = $_POST['password'];
            $confirm_password = $_POST['confirm_password'];
            $country_code = $_POST['country_code'];
            $phone_number = trim($_POST['phone_number']);
            $full_phone = $country_code . $phone_number;
            $photo = $_FILES['photo']['name'] ?? '';

            $errors = [];

            // Validation
            if (strlen($username) < 3) {
                $errors[] = "Username must be at least 3 characters long.";
            }

            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $errors[] = "Please enter a valid email address.";
            }

            if (strlen($password) < 8) {
                $errors[] = "Password must be at least 8 characters long.";
            }

            if ($password !== $confirm_password) {
                $errors[] = "Passwords do not match.";
            }

            if (!preg_match('/^[0-9]{10}$/', $phone_number)) {
                $errors[] = "Phone number must be exactly 10 digits.";
            }

            // Check for existing email and phone
            try {
                $stmt = $pdo->prepare("SELECT email, phone_number FROM users WHERE email = ? OR phone_number = ?");
                $stmt->execute([$email, $full_phone]);
                $existing = $stmt->fetch();

                if ($existing) {
                    if ($existing['email'] === $email) {
                        $errors[] = "Email address is already registered.";
                    }
                    if ($existing['phone_number'] === $full_phone) {
                        $errors[] = "Phone number is already registered.";
                    }
                }
            } catch (PDOException $e) {
                $errors[] = "Database error occurred.";
            }

            if (empty($errors)) {
                $hashed_password = password_hash($password, PASSWORD_BCRYPT);
                $verification_pin = str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
                $verification_token = bin2hex(random_bytes(32));

                // Handle photo upload
                $photoPath = null;
                if (!empty($photo)) {
                    $targetDir = "uploads/";
                    if (!is_dir($targetDir)) {
                        mkdir($targetDir, 0755, true);
                    }
                    
                    $fileExtension = strtolower(pathinfo($photo, PATHINFO_EXTENSION));
                    $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
                    
                    if (in_array($fileExtension, $allowedExtensions) && $_FILES['photo']['size'] <= 2097152) {
                        $newFileName = uniqid() . '_' . $photo;
                        $targetFile = $targetDir . $newFileName;
                        
                        if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetFile)) {
                            $photoPath = $targetFile;
                        }
                    }
                }

                try {
                    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, phone_number, photo, role, status, verification_pin, verification_token, created_at) VALUES (?, ?, ?, ?, ?, 'user', 'inactive', ?, ?, NOW())");
                    $stmt->execute([$username, $email, $hashed_password, $full_phone, $photoPath, $verification_pin, $verification_token]);

                    // Send verification email (you'll need to implement this)
                    // sendVerificationEmail($email, $username, $verification_pin, $verification_token);

                    echo "<div class='validation-message success'><i class='fas fa-check-circle'></i> Registration successful! Please check your email for verification instructions.</div>";
                    echo "<script>setTimeout(function(){ window.location.href='verify-email.php?token=" . $verification_token . "'; }, 3000);</script>";
                } catch (PDOException $e) {
                    echo "<div class='validation-message error'><i class='fas fa-exclamation-circle'></i> Registration failed. Please try again.</div>";
                }
            } else {
                foreach ($errors as $error) {
                    echo "<div class='validation-message error'><i class='fas fa-exclamation-circle'></i> " . $error . "</div>";
                }
            }
        }
        ?>
    </div>
</div>

<script>
// Username validation
document.getElementById('username').addEventListener('input', function() {
    const username = this.value.trim();
    const validation = document.getElementById('username-validation');
    
    if (username.length < 3) {
        validation.className = 'validation-message error';
        validation.innerHTML = '<i class="fas fa-times-circle"></i> Username must be at least 3 characters';
        validation.style.display = 'block';
    } else if (username.length >= 3) {
        validation.className = 'validation-message success';
        validation.innerHTML = '<i class="fas fa-check-circle"></i> Username looks good!';
        validation.style.display = 'block';
    }
});

// Email validation
document.getElementById('email').addEventListener('input', function() {
    const email = this.value.trim();
    const validation = document.getElementById('email-validation');
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    
    if (!emailRegex.test(email)) {
        validation.className = 'validation-message error';
        validation.innerHTML = '<i class="fas fa-times-circle"></i> Please enter a valid email address';
        validation.style.display = 'block';
    } else {
        validation.className = 'validation-message success';
        validation.innerHTML = '<i class="fas fa-check-circle"></i> Email format is valid!';
        validation.style.display = 'block';
    }
});

// Password strength checker
document.getElementById('password').addEventListener('input', function() {
    const password = this.value;
    const strengthFill = document.getElementById('strength-fill');
    const strengthText = document.getElementById('strength-text');
    
    let strength = 0;
    let feedback = '';
    
    if (password.length >= 8) strength += 1;
    if (/[a-z]/.test(password)) strength += 1;
    if (/[A-Z]/.test(password)) strength += 1;
    if (/[0-9]/.test(password)) strength += 1;
    if (/[^A-Za-z0-9]/.test(password)) strength += 1;
    
    strengthFill.className = 'strength-fill';
    
    if (strength <= 2) {
        strengthFill.classList.add('strength-weak');
        feedback = 'Weak password';
        strengthText.style.color = '#dc3545';
    } else if (strength === 3) {
        strengthFill.classList.add('strength-fair');
        feedback = 'Fair password';
        strengthText.style.color = '#ffc107';
    } else if (strength === 4) {
        strengthFill.classList.add('strength-good');
        feedback = 'Good password';
        strengthText.style.color = '#17a2b8';
    } else {
        strengthFill.classList.add('strength-strong');
        feedback = 'Strong password';
        strengthText.style.color = '#28a745';
    }
    
    strengthText.textContent = feedback;
});

// Password match validation
document.getElementById('confirm_password').addEventListener('input', function() {
    const password = document.getElementById('password').value;
    const confirmPassword = this.value;
    const validation = document.getElementById('password-match');
    
    if (confirmPassword === '') {
        validation.style.display = 'none';
        return;
    }
    
    if (password !== confirmPassword) {
        validation.className = 'validation-message error';
        validation.innerHTML = '<i class="fas fa-times-circle"></i> Passwords do not match';
        validation.style.display = 'block';
    } else {
        validation.className = 'validation-message success';
        validation.innerHTML = '<i class="fas fa-check-circle"></i> Passwords match!';
        validation.style.display = 'block';
    }
});

// Phone number validation
document.getElementById('phone_number').addEventListener('input', function() {
    const phone = this.value;
    const validation = document.getElementById('phone-validation');
    const phoneRegex = /^[0-9]{10}$/;
    
    this.value = this.value.replace(/[^0-9]/g, '');
    
    if (phone.length === 0) {
        validation.style.display = 'none';
    } else if (!phoneRegex.test(phone)) {
        validation.className = 'validation-message error';
        validation.innerHTML = '<i class="fas fa-times-circle"></i> Phone number must be exactly 10 digits';
        validation.style.display = 'block';
    } else {
        validation.className = 'validation-message success';
        validation.innerHTML = '<i class="fas fa-check-circle"></i> Phone number format is valid!';
        validation.style.display = 'block';
    }
});

// Image preview function
function previewImage(input) {
    const file = input.files[0];
    const preview = document.getElementById('image-preview');
    
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        preview.style.display = 'none';
    }
}

// Form submission validation
document.getElementById('registerForm').addEventListener('submit', function(e) {
    const password = document.getElementById('password').value;
    const confirmPassword = document.getElementById('confirm_password').value;
    const phone = document.getElementById('phone_number').value;
    
    if (password !== confirmPassword) {
        e.preventDefault();
        alert('Passwords do not match!');
        return false;
    }
    
    if (!/^[0-9]{10}$/.test(phone)) {
        e.preventDefault();
        alert('Phone number must be exactly 10 digits!');
        return false;
    }
});
</script>

</body>
</html>