<?php
session_start();
require_once 'db_connect.php';

$error_message = '';
$success_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required fields
    if (empty($_POST['username']) || empty($_POST['email']) || empty($_POST['password']) || empty($_POST['confirm_password'])) {
        $error_message = 'All required fields must be filled out.';
    } elseif ($_POST['password'] !== $_POST['confirm_password']) {
        $error_message = 'Passwords do not match.';
    } else {
        try {
            // Check if username already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$_POST['username']]);
            if ($stmt->fetch()) {
                $error_message = 'Username already exists.';
                throw new Exception();
            }

            // Check if email already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$_POST['email']]);
            if ($stmt->fetch()) {
                $error_message = 'Email already exists.';
                throw new Exception();
            }

            // Insert new user
            $stmt = $pdo->prepare("
                INSERT INTO users (username, email, password, full_name, phone, status) 
                VALUES (?, ?, ?, ?, ?, 'active')
            ");
            
            $stmt->execute([
                $_POST['username'],
                $_POST['email'],
                password_hash($_POST['password'], PASSWORD_DEFAULT),
                $_POST['full_name'],
                $_POST['phone'] ?? null
            ]);

            $_SESSION['message'] = 'User created successfully!';
            $_SESSION['message_type'] = 'success';
            header('Location: list_users.php');
            exit();

        } catch (Exception $e) {
            $error_message = 'Error creating user: ' . $e->getMessage();
        }
    }
}

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
    <link rel="stylesheet" href="css/style.css">
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