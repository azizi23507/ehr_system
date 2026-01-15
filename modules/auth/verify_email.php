<?php
// Email Verification Handler
// Processes email verification tokens sent to users after registration
// Activates user accounts by setting is_verified flag to 1
// Part of the registration security workflow

global $conn;
session_start();

require_once '../../config/database.php';

$page_title = "Email Verification";
$base_url = "../../";

$success_message = "";
$error_message = "";

// Check if verification token is provided in URL
if (!isset($_GET['token']) || empty($_GET['token'])) {
    $error_message = "Invalid verification link.";
} else {
    $token = htmlspecialchars($_GET['token']);
    
    // Look up doctor account by verification token
    $stmt = $conn->prepare("SELECT doctor_id, first_name, last_name, email, is_verified FROM doctors WHERE verification_token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows == 1) {
        $doctor = $result->fetch_assoc();
        
        // Check if email was already verified
        if ($doctor['is_verified'] == 1) {
            $error_message = "This email has already been verified. You can login now.";
        } else {
            // Activate account by setting is_verified to 1 and clearing token
            $update_stmt = $conn->prepare("UPDATE doctors SET is_verified = 1, verification_token = NULL WHERE doctor_id = ?");
            $update_stmt->bind_param("i", $doctor['doctor_id']);
            
            if ($update_stmt->execute()) {
                $success_message = "Email verified successfully! You can now login to your account.";
                
                // Redirect to login page after 3 seconds
                header("refresh:3;url=login.php");
            } else {
                $error_message = "Verification failed. Please try again.";
            }
            
            $update_stmt->close();
        }
    } else {
        $error_message = "Invalid verification token.";
    }
    
    $stmt->close();
}
?>

<?php include '../../includes/header.php'; ?>

<div class="container">
    <div class="auth-container">
        <div class="auth-card">
            <div class="text-center mb-4">
                <?php if (!empty($success_message)): ?>
                    <i class="bi bi-check-circle text-success" style="font-size: 5rem;"></i>
                    <h2 class="mt-3">Email Verified!</h2>
                <?php else: ?>
                    <i class="bi bi-x-circle text-danger" style="font-size: 5rem;"></i>
                    <h2 class="mt-3">Verification Failed</h2>
                <?php endif; ?>
            </div>
            
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success text-center">
                    <i class="bi bi-check-circle"></i> <?php echo $success_message; ?>
                </div>
                <div class="text-center">
                    <p class="text-muted">You will be redirected to login page in 3 seconds...</p>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger text-center">
                    <i class="bi bi-exclamation-triangle"></i> <?php echo $error_message; ?>
                </div>
            <?php endif; ?>
            
            <div class="text-center mt-4">
                <a href="login.php" class="btn btn-primary">
                    <i class="bi bi-box-arrow-in-right"></i> Go to Login
                </a>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
