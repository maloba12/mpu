<?php
session_start();

$error_message = '';
$success_message = '';

// Check for session messages
if (isset($_SESSION['message'])) {
    if ($_SESSION['message_type'] == 'success') {
        $success_message = $_SESSION['message'];
    } else {
        $error_message = $_SESSION['message'];
    }
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add User - User Management System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 0;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 1000px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            overflow: hidden;
        }

        header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 30px;
            text-align: center;
        }

        header h1 {
            font-size: 2em;
            font-weight: 600;
            margin-bottom: 15px;
        }

        nav {
            display: flex;
            justify-content: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .nav-link {
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 20px;
            transition: all 0.3s ease;
            font-weight: 500;
        }

        .nav-link:hover, .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: white;
        }

        main {
            padding: 40px;
        }

        .signup-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 450px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .signup-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .signup-header h1 {
            color: #333;
            font-size: 2.2em;
            font-weight: 600;
            margin-bottom: 10px;
        }

        .signup-header p {
            color: #666;
            font-size: 1.1em;
        }

        .form-group {
            margin-bottom: 20px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
            font-size: 0.95em;
        }

        .form-group input {
            width: 100%;
            padding: 15px 20px;
            border: 2px solid #e1e8ed;
            border-radius: 12px;
            font-size: 1em;
            transition: all 0.3s ease;
            background: #fff;
        }

        .form-group input:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
        }

        .form-group input:hover {
            border-color: #c1c9d2;
        }

        .signup-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1.1em;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 10px;
        }

        .signup-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(102, 126, 234, 0.3);
        }

        .signup-btn:active {
            transform: translateY(0);
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-weight: 500;
            animation: slideIn 0.3s ease;
        }

        .alert-error {
            background-color: #fee;
            color: #c53030;
            border: 1px solid #fed7d7;
        }

        .alert-success {
            background-color: #f0fff4;
            color: #38a169;
            border: 1px solid #c6f6d5;
        }

        .form-footer {
            text-align: center;
            margin-top: 25px;
            padding-top: 20px;
            border-top: 1px solid #e1e8ed;
        }

        .form-footer a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }

        .form-footer a:hover {
            color: #764ba2;
            text-decoration: underline;
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #666;
            font-size: 1.2em;
            user-select: none;
        }

        .password-toggle:hover {
            color: #333;
        }

        .password-strength {
            margin-top: 5px;
            font-size: 0.85em;
            padding: 5px 0;
        }

        .strength-weak { color: #e53e3e; }
        .strength-medium { color: #dd6b20; }
        .strength-strong { color: #38a169; }

        .form-row {
            display: flex;
            gap: 15px;
        }

        .form-row .form-group {
            flex: 1;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @media (max-width: 480px) {
            .signup-container {
                padding: 30px 20px;
                margin: 10px;
            }
            
            .signup-header h1 {
                font-size: 1.8em;
            }

            .form-row {
                flex-direction: column;
                gap: 0;
            }
        }

        /* Loading state */
        .signup-btn.loading {
            opacity: 0.7;
            cursor: not-allowed;
            position: relative;
        }

        .signup-btn.loading::after {
            content: '';
            position: absolute;
            width: 20px;
            height: 20px;
            margin: auto;
            border: 2px solid transparent;
            border-top-color: #ffffff;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .required {
            color: #e53e3e;
        }
    </style>
</head>
<body>
    <div class="signup-container">
        <div class="signup-header">
            <h1>Create Account</h1>
            <p>Join us today and get started</p>
        </div>

        <?php if ($error_message): ?>
            <div class="alert alert-error">
                <?php echo htmlspecialchars($error_message); ?>
            </div>
        <?php endif; ?>

        <?php if ($success_message): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($success_message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" id="signupForm">
            <div class="form-group">
                <label for="full_name">Full Name</label>
                <input type="text" 
                       id="full_name" 
                       name="full_name" 
                       autocomplete="name"
                       value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>"
                       placeholder="Enter your full name">
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="username">Username <span class="required">*</span></label>
                    <input type="text" 
                           id="username" 
                           name="username" 
                           required 
                           autocomplete="username"
                           value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>"
                           placeholder="Choose a username">
                </div>

                <div class="form-group">
                    <label for="email">Email <span class="required">*</span></label>
                    <input type="email" 
                           id="email" 
                           name="email" 
                           required 
                           autocomplete="email"
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
                           placeholder="Enter your email">
                </div>
            </div>

            <div class="form-group">
                <label for="password">Password <span class="required">*</span></label>
                <div style="position: relative;">
                    <input type="password" 
                           id="password" 
                           name="password" 
                           required 
                           autocomplete="new-password"
                           placeholder="Create a password"
                           onkeyup="checkPasswordStrength()">
                    <span class="password-toggle" onclick="togglePassword('password', this)">👁</span>
                </div>
                <div id="password-strength" class="password-strength"></div>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password <span class="required">*</span></label>
                <div style="position: relative;">
                    <input type="password" 
                           id="confirm_password" 
                           name="confirm_password" 
                           required 
                           autocomplete="new-password"
                           placeholder="Confirm your password"
                           onkeyup="checkPasswordMatch()">
                    <span class="password-toggle" onclick="togglePassword('confirm_password', this)">👁</span>
                </div>
                <div id="password-match" class="password-strength"></div>
            </div>

            <button type="submit" class="signup-btn" id="submitBtn">
                Create Account
            </button>
        </form>

        <div class="form-footer">
            <p>Already have an account? <a href="login.php">Sign in here</a></p>
        </div>
    </div>

    <script>
        // Password toggle functionality
        function togglePassword(fieldId, toggleIcon) {
            const passwordInput = document.getElementById(fieldId);
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.textContent = '🙈';
            } else {
                passwordInput.type = 'password';
                toggleIcon.textContent = '👁';
            }
        }

        // Password strength checker
        function checkPasswordStrength() {
            const password = document.getElementById('password').value;
            const strengthDiv = document.getElementById('password-strength');
            
            if (password.length === 0) {
                strengthDiv.textContent = '';
                return;
            }
            
            let strength = 0;
            let feedback = [];
            
            // Length check
            if (password.length >= 8) strength++;
            else feedback.push('at least 8 characters');
            
            // Uppercase check
            if (/[A-Z]/.test(password)) strength++;
            else feedback.push('an uppercase letter');
            
            // Lowercase check
            if (/[a-z]/.test(password)) strength++;
            else feedback.push('a lowercase letter');
            
            // Number check
            if (/\d/.test(password)) strength++;
            else feedback.push('a number');
            
            // Special character check
            if (/[^A-Za-z0-9]/.test(password)) strength++;
            else feedback.push('a special character');
            
            if (strength < 2) {
                strengthDiv.className = 'password-strength strength-weak';
                strengthDiv.textContent = 'Weak - Add ' + feedback.slice(0, 2).join(', ');
            } else if (strength < 4) {
                strengthDiv.className = 'password-strength strength-medium';
                strengthDiv.textContent = 'Medium - Consider adding ' + feedback.slice(0, 1).join(', ');
            } else {
                strengthDiv.className = 'password-strength strength-strong';
                strengthDiv.textContent = 'Strong password!';
            }
        }

        // Password match checker
        function checkPasswordMatch() {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const matchDiv = document.getElementById('password-match');
            
            if (confirmPassword.length === 0) {
                matchDiv.textContent = '';
                return;
            }
            
            if (password === confirmPassword) {
                matchDiv.className = 'password-strength strength-strong';
                matchDiv.textContent = 'Passwords match!';
            } else {
                matchDiv.className = 'password-strength strength-weak';
                matchDiv.textContent = 'Passwords do not match';
            }
        }

        // Form submission with loading state
        document.getElementById('signupForm').addEventListener('submit', function(e) {
            const submitBtn = document.getElementById('submitBtn');
            submitBtn.classList.add('loading');
            submitBtn.textContent = 'Creating Account...';
            submitBtn.disabled = true;
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-10px)';
                setTimeout(function() {
                    alert.remove();
                }, 300);
            });
        }, 5000);

        // Focus first input on page load
        window.addEventListener('load', function() {
            const fullNameInput = document.getElementById('full_name');
            fullNameInput.focus();
        });

        // Real-time username availability check (optional)
        let usernameTimeout;
        document.getElementById('username').addEventListener('input', function() {
            clearTimeout(usernameTimeout);
            const username = this.value.trim();
            
            if (username.length < 3) return;
            
            usernameTimeout = setTimeout(function() {
                // You can implement AJAX check here if needed
                // checkUsernameAvailability(username);
            }, 500);
        });
    </script>
</body>
</html>