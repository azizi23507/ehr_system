<?php
// Start session
global $conn;
session_start();

// Include database connection
require_once '../../config/database.php';

// Set page variables
$page_title = "Reset Password";
$base_url = "../../";

// Initialize variables
$success_message = "";
$error_message = "";
$token_valid = false;
$doctor_id = null;

// Check if token is provided
if (!isset($_GET['token']) || empty($_GET['token'])) {
    $error_message = "Invalid or missing reset token.";
} else {
    $token = htmlspecialchars($_GET['token']);
    
    // Verify token
    $stmt = $conn->prepare("SELECT doctor_id, first_name, reset_token_expiry FROM doctors WHERE reset_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $doctor = $result->fetch_assoc();
        $doctor_id = $doctor['doctor_id'];
        
        // Check if token has expired
        if (strtotime($doctor['reset_token_expiry']) < time()) {
            $error_message = "Reset token has expired. Please request a new password reset.";
        } else {
            $token_valid = true;
        }
    } else {
        $error_message = "Invalid reset token.";
    }
    
    $stmt->close();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $token_valid) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validation
    $errors = [];
    
    if (empty($new_password)) {
        $errors[] = "Please enter a new password.";
    }
    
    // Validate password strength
    if (!empty($new_password)) {
        if (strlen($new_password) < 8) {
            $errors[] = "Password must be at least 8 characters long.";
        }
        if (!preg_match('/[A-Z]/', $new_password)) {
            $errors[] = "Password must contain at least one uppercase letter.";
        }
        if (!preg_match('/[a-z]/', $new_password)) {
            $errors[] = "Password must contain at least one lowercase letter.";
        }
        if (!preg_match('/[0-9]/', $new_password)) {
            $errors[] = "Password must contain at least one number.";
        }
        if (!preg_match('/[^a-zA-Z0-9]/', $new_password)) {
            $errors[] = "Password must contain at least one special character.";
        }
    }
    
    // Check if passwords match
    if ($new_password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }
    
    // If no validation errors, proceed with password reset
    if (empty($errors)) {
        // Hash new password
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        
        // Update password and clear reset token
        $update_stmt = $conn->prepare("UPDATE doctors SET password = ?, reset_token = NULL, reset_token_expiry = NULL WHERE doctor_id = ?");
        $update_stmt->bind_param("si", $hashed_password, $doctor_id);
        
        if ($update_stmt->execute()) {
            $success_message = "Password reset successful! You can now login with your new password.";
            $token_valid = false; // Prevent form from showing again
            
            // Redirect to login page after 3 seconds
            header("refresh:3;url=login.php");
        } else {
            $error_message = "Failed to reset password. Please try again.";
        }
        
        $update_stmt->close();
    } else {
        // Display validation errors
        $error_message = implode("<br>", $errors);
    }
}
?>

<?php include '../../includes/header.php'; ?>

<div class="container">
    <div class="auth-container">
        <div class="auth-card">
            <!-- Header -->
            <div class="text-center mb-4">
                <i class="bi bi-shield-lock text-primary" style="font-size: 3rem;"></i>
                <h2 class="mt-3">Reset Your Password</h2>
                <p class="text-muted">Enter your new password below</p>
            </div>
            
            <!-- Success Message -->
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> <?php echo $success_message; ?>
                    <p class="mb-0 mt-2">Redirecting to login page...</p>
                </div>
            <?php endif; ?>
            
            <!-- Error Message -->
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle"></i> <?php echo $error_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                
                <?php if (!$token_valid && !$success_message): ?>
                    <div class="text-center mt-3">
                        <a href="forgot_password.php" class="btn btn-primary">
                            <i class="bi bi-arrow-left"></i> Request New Reset Link
                        </a>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            
            <!-- Reset Password Form -->
            <?php if ($token_valid): ?>
                <form method="POST" action="">
                    <!-- New Password -->
                    <div class="mb-3">
                        <label for="new_password" class="form-label">New Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" id="new_password" name="new_password" 
                                   placeholder="Enter new password" required autofocus
                                   onkeyup="updatePasswordStrength(this, 'passwordStrength')">
                            <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                <i class="bi bi-eye" id="toggleIcon"></i>
                            </button>
                        </div>
                        <small id="passwordStrength" class="text-muted">Password strength will appear here</small>
                        <small class="d-block text-muted mt-1">
                            Must be at least 8 characters with uppercase, lowercase, number, and special character
                        </small>
                    </div>
                    
                    <!-- Confirm Password -->
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="bi bi-lock"></i></span>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                   placeholder="Re-enter new password" required>
                            <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                <i class="bi bi-eye" id="toggleConfirmIcon"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Submit Button -->
                    <button type="submit" class="btn btn-primary w-100 mb-3">
                        <i class="bi bi-check-circle"></i> Reset Password
                    </button>
                    
                    <!-- Back to Login -->
                    <div class="auth-links">
                        <a href="login.php"><i class="bi bi-arrow-left"></i> Back to Login</a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- JavaScript for password toggle -->
<script>
// Toggle new password visibility
document.getElementById('togglePassword').addEventListener('click', function() {
    const passwordField = document.getElementById('new_password');
    const toggleIcon = document.getElementById('toggleIcon');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleIcon.classList.remove('bi-eye');
        toggleIcon.classList.add('bi-eye-slash');
    } else {
        passwordField.type = 'password';
        toggleIcon.classList.remove('bi-eye-slash');
        toggleIcon.classList.add('bi-eye');
    }
});

// Toggle confirm password visibility
document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
    const passwordField = document.getElementById('confirm_password');
    const toggleIcon = document.getElementById('toggleConfirmIcon');
    
    if (passwordField.type === 'password') {
        passwordField.type = 'text';
        toggleIcon.classList.remove('bi-eye');
        toggleIcon.classList.add('bi-eye-slash');
    } else {
        passwordField.type = 'password';
        toggleIcon.classList.remove('bi-eye-slash');
        toggleIcon.classList.add('bi-eye');
    }
});
</script>

<?php include '../../includes/footer.php'; ?>
