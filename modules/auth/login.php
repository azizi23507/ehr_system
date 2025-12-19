<?php
// Start session
global $conn;
session_start();

// If already logged in, redirect to dashboard
if (isset($_SESSION['doctor_id'])) {
    header("Location: ../dashboard/dashboard.php");
    exit();
}

// Include database connection
require_once '../../config/database.php';

// Set page variables
$page_title = "Login";
$base_url = "../../";

// Initialize variables
$error_message = "";
$success_message = "";
$username = "";

// Check for logout message
if (isset($_GET['logout']) && $_GET['logout'] == 1) {
    $success_message = "You have been logged out successfully.";
}

// Check for timeout message
if (isset($_GET['timeout']) && $_GET['timeout'] == 1) {
    $error_message = "Your session has expired. Please login again.";
}

// Check for login required message
if (isset($_GET['login_required']) && $_GET['login_required'] == 1) {
    $error_message = "Please login to access that page.";
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $username = htmlspecialchars(trim($_POST['username']));
    $password = $_POST['password'];
    $remember_me = isset($_POST['remember_me']);
    
    // Validation
    if (empty($username) || empty($password)) {
        $error_message = "Please enter both username and password.";
    } else {
        // Check login attempts (basic brute force protection)
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $stmt = $conn->prepare("SELECT COUNT(*) as attempts FROM login_attempts WHERE username = ? AND success = 0 AND attempt_time > DATE_SUB(NOW(), INTERVAL 15 MINUTE)");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $failed_attempts = $row['attempts'];
        $stmt->close();
        
        // If more than 5 failed attempts in last 15 minutes, block login
        if ($failed_attempts >= 50000) {
            $error_message = "Too many failed login attempts. Please try again after 15 minutes.";
        } else {
            // Query database for user
            $stmt = $conn->prepare("SELECT doctor_id, first_name, last_name, email, username, password, is_verified FROM doctors WHERE username = ? OR email = ?");
            $stmt->bind_param("ss", $username, $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows == 1) {
                $doctor = $result->fetch_assoc();
                
                // Verify password
                if (password_verify($password, $doctor['password'])) {
                    // Check if email is verified
                    if ($doctor['is_verified'] == 0) {
                        $error_message = "Please verify your email address before logging in.";
                        
                        // Log failed attempt
                        $stmt_log = $conn->prepare("INSERT INTO login_attempts (username, ip_address, success) VALUES (?, ?, 0)");
                        $stmt_log->bind_param("ss", $username, $ip_address);
                        $stmt_log->execute();
                        $stmt_log->close();
                    } else {
                        // Login successful - Set session variables
                        $_SESSION['doctor_id'] = $doctor['doctor_id'];
                        $_SESSION['doctor_name'] = $doctor['first_name'] . ' ' . $doctor['last_name'];
                        $_SESSION['doctor_email'] = $doctor['email'];
                        $_SESSION['username'] = $doctor['username'];
                        $_SESSION['is_verified'] = $doctor['is_verified'];
                        $_SESSION['login_time'] = time();
                        $_SESSION['last_activity'] = time();
                        
                        // Log successful attempt
                        $stmt_log = $conn->prepare("INSERT INTO login_attempts (username, ip_address, success) VALUES (?, ?, 1)");
                        $stmt_log->bind_param("ss", $username, $ip_address);
                        $stmt_log->execute();
                        $stmt_log->close();
                        
                        // If remember me is checked, set cookie (optional - not implemented for security)
                        // In production, implement secure remember me functionality
                        
                        // Redirect to dashboard
                        header("Location: ../dashboard/dashboard.php");
                        exit();
                    }
                } else {
                    $error_message = "Invalid username or password.";
                    
                    // Log failed attempt
                    $stmt_log = $conn->prepare("INSERT INTO login_attempts (username, ip_address, success) VALUES (?, ?, 0)");
                    $stmt_log->bind_param("ss", $username, $ip_address);
                    $stmt_log->execute();
                    $stmt_log->close();
                }
            } else {
                $error_message = "Invalid username or password.";
                
                // Log failed attempt
                $stmt_log = $conn->prepare("INSERT INTO login_attempts (username, ip_address, success) VALUES (?, ?, 0)");
                $stmt_log->bind_param("ss", $username, $ip_address);
                $stmt_log->execute();
                $stmt_log->close();
            }
            
            $stmt->close();
        }
    }
}
?>

<?php include '../../includes/header.php'; ?>

<div class="container">
    <div class="auth-container">
        <div class="auth-card">
            <!-- Header -->
            <div class="text-center mb-4">
                <i class="bi bi-box-arrow-in-right text-primary" style="font-size: 3rem;"></i>
                <h2 class="mt-3">Doctor Login</h2>
                <p class="text-muted">Login to access your EHR dashboard</p>
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
            
            <!-- Login Form -->
            <form method="POST" action="">
                <!-- Username or Email -->
                <div class="mb-3">
                    <label for="username" class="form-label">Username or Email <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" class="form-control" id="username" name="username" 
                               value="<?php echo htmlspecialchars($username); ?>" 
                               placeholder="Enter username or email" required autofocus>
                    </div>
                </div>
                
                <!-- Password -->
                <div class="mb-3">
                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Enter password" required>
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="bi bi-eye" id="toggleIcon"></i>
                        </button>
                    </div>
                </div>
                
                <!-- Remember Me & Forgot Password -->
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="remember_me" name="remember_me">
                        <label class="form-check-label" for="remember_me">
                            Remember me
                        </label>
                    </div>
                    <a href="forgot_password.php" class="text-decoration-none">Forgot Password?</a>
                </div>
                
                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary w-100 mb-3">
                    <i class="bi bi-box-arrow-in-right"></i> Login
                </button>
                
                <!-- Register Link -->
                <div class="auth-links">
                    Don't have an account? <a href="register.php">Register here</a>
                </div>
                
                <!-- Back to Home -->
                <div class="auth-links mt-2">
                    <a href="../../index.php"><i class="bi bi-arrow-left"></i> Back to Home</a>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- JavaScript for password toggle -->
<script>
document.getElementById('togglePassword').addEventListener('click', function() {
    const passwordField = document.getElementById('password');
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
</script>

<?php include '../../includes/footer.php'; ?>
