<?php
// Start session
global $conn;
session_start();

// Include database connection
require_once '../../config/database.php';

// Set page variables
$page_title = "Forgot Password";
$base_url = "../../";

// Initialize variables
$success_message = "";
$error_message = "";
$email = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = htmlspecialchars(trim($_POST['email']));
    
    // Validation
    if (empty($email)) {
        $error_message = "Please enter your email address.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
    } else {
        // Check if email exists in database
        $stmt = $conn->prepare("SELECT doctor_id, first_name, last_name FROM doctors WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $doctor = $result->fetch_assoc();
            
            // Generate reset token
            $reset_token = bin2hex(random_bytes(32));
            $reset_token_expiry = date('Y-m-d H:i:s', strtotime('+1 hour')); // Token expires in 1 hour
            
            // Update database with reset token
            $update_stmt = $conn->prepare("UPDATE doctors SET reset_token = ?, reset_token_expiry = ? WHERE email = ?");
            $update_stmt->bind_param("sss", $reset_token, $reset_token_expiry, $email);
            
            if ($update_stmt->execute()) {
                // In a real application, send email with reset link here
                // Reset link would be: reset_password.php?token=$reset_token
                
                // For this project, we'll just show the token (in production, send via email)
                $success_message = "Password reset instructions have been sent to your email address.";
                
                // For demonstration purposes, show the reset link
                $reset_link = "reset_password.php?token=" . $reset_token;
                $success_message .= "<br><br><small class='text-muted'>For testing purposes, your reset link is:<br><a href='$reset_link'>$reset_link</a></small>";
                
                $email = ""; // Clear email field
            } else {
                $error_message = "Failed to generate reset link. Please try again.";
            }
            
            $update_stmt->close();
        } else {
            // Don't reveal if email exists or not (security best practice)
            $success_message = "If an account with that email exists, password reset instructions have been sent.";
            $email = ""; // Clear email field
        }
        
        $stmt->close();
    }
}
?>

<?php include '../../includes/header.php'; ?>

<div class="container">
    <div class="auth-container">
        <div class="auth-card">
            <!-- Header -->
            <div class="text-center mb-4">
                <i class="bi bi-key text-primary" style="font-size: 3rem;"></i>
                <h2 class="mt-3">Forgot Password?</h2>
                <p class="text-muted">Enter your email address and we'll send you instructions to reset your password</p>
            </div>
            
            <!-- Success Message -->
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> <?php echo $success_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- Error Message -->
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle"></i> <?php echo $error_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- Forgot Password Form -->
            <form method="POST" action="">
                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email" 
                               value="<?php echo htmlspecialchars($email); ?>" 
                               placeholder="Enter your registered email" required autofocus>
                    </div>
                </div>
                
                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary w-100 mb-3">
                    <i class="bi bi-send"></i> Send Reset Link
                </button>
                
                <!-- Back to Login -->
                <div class="auth-links">
                    Remember your password? <a href="login.php">Login here</a>
                </div>
                
                <!-- Register Link -->
                <div class="auth-links mt-2">
                    Don't have an account? <a href="register.php">Register here</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
