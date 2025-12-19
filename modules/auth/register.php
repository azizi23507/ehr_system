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
$page_title = "Register";
$base_url = "../../";

// Initialize variables
$success_message = "";
$error_message = "";
$first_name = $last_name = $email = $username = $phone = $specialization = $license_number = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get and sanitize form data
    $first_name = htmlspecialchars(trim($_POST['first_name']));
    $last_name = htmlspecialchars(trim($_POST['last_name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $username = htmlspecialchars(trim($_POST['username']));
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $phone = htmlspecialchars(trim($_POST['phone']));
    $specialization = htmlspecialchars(trim($_POST['specialization']));
    $license_number = htmlspecialchars(trim($_POST['license_number']));
    
    // Validation
    $errors = [];
    
    // Check required fields
    if (empty($first_name)) $errors[] = "First name is required.";
    if (empty($last_name)) $errors[] = "Last name is required.";
    if (empty($email)) $errors[] = "Email is required.";
    if (empty($username)) $errors[] = "Username is required.";
    if (empty($password)) $errors[] = "Password is required.";
    if (empty($specialization)) $errors[] = "Specialization is required.";
    if (empty($license_number)) $errors[] = "License number is required.";
    
    // Validate email format
    if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid email format.";
    }
    
    // Validate username (alphanumeric, 4-20 characters)
    if (!empty($username) && !preg_match('/^[a-zA-Z0-9_]{4,20}$/', $username)) {
        $errors[] = "Username must be 4-20 characters long and contain only letters, numbers, and underscores.";
    }
    
    // Validate password strength
    if (!empty($password)) {
        if (strlen($password) < 8) {
            $errors[] = "Password must be at least 8 characters long.";
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = "Password must contain at least one uppercase letter.";
        }
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = "Password must contain at least one lowercase letter.";
        }
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = "Password must contain at least one number.";
        }
        if (!preg_match('/[^a-zA-Z0-9]/', $password)) {
            $errors[] = "Password must contain at least one special character.";
        }
    }
    
    // Check if passwords match
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }
    
    // If no validation errors, proceed with registration
    if (empty($errors)) {
        // Check if email already exists
        $stmt = $conn->prepare("SELECT doctor_id FROM doctors WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error_message = "Email already registered. Please use a different email or login.";
        } else {
            $stmt->close();
            
            // Check if username already exists
            $stmt = $conn->prepare("SELECT doctor_id FROM doctors WHERE username = ?");
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $error_message = "Username already taken. Please choose a different username.";
            } else {
                $stmt->close();
                
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Generate verification token
                $verification_token = bin2hex(random_bytes(32));
                
                // Handle profile image upload
                $profile_image = null;
                if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
                    $allowed = ['jpg', 'jpeg', 'png', 'gif'];
                    $filename = $_FILES['profile_image']['name'];
                    $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                    
                    if (in_array($ext, $allowed) && $_FILES['profile_image']['size'] <= 5000000) {
                        $new_filename = 'doctor_' . uniqid() . '.' . $ext;
                        $upload_path = '../../uploads/profile_pics/' . $new_filename;
                        
                        if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path)) {
                            $profile_image = $new_filename;
                        }
                    }
                }
                
                // Insert new doctor into database
                $stmt = $conn->prepare("INSERT INTO doctors (first_name, last_name, email, username, password, phone, specialization, license_number, profile_image, verification_token, is_verified) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 0)");
                $stmt->bind_param("ssssssssss", $first_name, $last_name, $email, $username, $hashed_password, $phone, $specialization, $license_number, $profile_image, $verification_token);
                
                if ($stmt->execute()) {
                    // Success - In a real application, send verification email here
                    // For this project, we'll auto-verify for testing purposes
                    
                    // Auto-verify the account (remove this in production)
                    $doctor_id = $stmt->insert_id;
                    $update_stmt = $conn->prepare("UPDATE doctors SET is_verified = 1 WHERE doctor_id = ?");
                    $update_stmt->bind_param("i", $doctor_id);
                    $update_stmt->execute();
                    $update_stmt->close();
                    
                    $success_message = "Registration successful! You can now login with your credentials.";
                    
                    // Clear form fields
                    $first_name = $last_name = $email = $username = $phone = $specialization = $license_number = "";
                    
                    // Redirect to login page after 2 seconds
                    header("refresh:2;url=login.php");
                } else {
                    $error_message = "Registration failed. Please try again.";
                }
                
                $stmt->close();
            }
        }
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
                <i class="bi bi-person-plus-fill text-primary" style="font-size: 3rem;"></i>
                <h2 class="mt-3">Doctor Registration</h2>
                <p class="text-muted">Create your account to get started</p>
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
            
            <!-- Registration Form -->
            <form method="POST" action="" id="registerForm" enctype="multipart/form-data">
                <div class="row">
                    <!-- First Name -->
                    <div class="col-md-6 mb-3">
                        <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="first_name" name="first_name" 
                               value="<?php echo htmlspecialchars($first_name); ?>" 
                               placeholder="Enter first name" required>
                    </div>
                    
                    <!-- Last Name -->
                    <div class="col-md-6 mb-3">
                        <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="last_name" name="last_name" 
                               value="<?php echo htmlspecialchars($last_name); ?>" 
                               placeholder="Enter last name" required>
                    </div>
                </div>
                
                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="email" name="email" 
                           value="<?php echo htmlspecialchars($email); ?>" 
                           placeholder="doctor@example.com" required>
                    <small class="text-muted">We'll send a verification link to this email</small>
                </div>
                
                <!-- Username -->
                <div class="mb-3">
                    <label for="username" class="form-label">Username <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="username" name="username" 
                           value="<?php echo htmlspecialchars($username); ?>" 
                           placeholder="Choose a username" required>
                    <small class="text-muted">4-20 characters, letters, numbers, and underscores only</small>
                </div>
                
                <!-- Password -->
                <div class="mb-3">
                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="password" name="password" 
                           placeholder="Create a strong password" required
                           onkeyup="updatePasswordStrength(this, 'passwordStrength')">
                    <small id="passwordStrength" class="text-muted">Password strength will appear here</small>
                    <small class="d-block text-muted mt-1">
                        Must be at least 8 characters with uppercase, lowercase, number, and special character
                    </small>
                </div>
                
                <!-- Confirm Password -->
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                           placeholder="Re-enter your password" required>
                </div>
                
                <!-- Phone -->
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <input type="tel" class="form-control" id="phone" name="phone" 
                           value="<?php echo htmlspecialchars($phone); ?>" 
                           placeholder="+49 XXX XXXXXXX">
                </div>
                
                <!-- Specialization -->
                <div class="mb-3">
                    <label for="specialization" class="form-label">Specialization <span class="text-danger">*</span></label>
                    <select class="form-select" id="specialization" name="specialization" required>
                        <option value="">Select your specialization</option>
                        <option value="General Physician" <?php echo ($specialization == "General Physician") ? "selected" : ""; ?>>General Physician</option>
                        <option value="Cardiologist" <?php echo ($specialization == "Cardiologist") ? "selected" : ""; ?>>Cardiologist</option>
                        <option value="Dermatologist" <?php echo ($specialization == "Dermatologist") ? "selected" : ""; ?>>Dermatologist</option>
                        <option value="Neurologist" <?php echo ($specialization == "Neurologist") ? "selected" : ""; ?>>Neurologist</option>
                        <option value="Pediatrician" <?php echo ($specialization == "Pediatrician") ? "selected" : ""; ?>>Pediatrician</option>
                        <option value="Psychiatrist" <?php echo ($specialization == "Psychiatrist") ? "selected" : ""; ?>>Psychiatrist</option>
                        <option value="Surgeon" <?php echo ($specialization == "Surgeon") ? "selected" : ""; ?>>Surgeon</option>
                        <option value="Orthopedic" <?php echo ($specialization == "Orthopedic") ? "selected" : ""; ?>>Orthopedic</option>
                        <option value="ENT Specialist" <?php echo ($specialization == "ENT Specialist") ? "selected" : ""; ?>>ENT Specialist</option>
                        <option value="Gynecologist" <?php echo ($specialization == "Gynecologist") ? "selected" : ""; ?>>Gynecologist</option>
                        <option value="Other" <?php echo ($specialization == "Other") ? "selected" : ""; ?>>Other</option>
                    </select>
                </div>
                
                <!-- License Number -->
                <div class="mb-3">
                    <label for="license_number" class="form-label">Medical License Number <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="license_number" name="license_number" 
                           value="<?php echo htmlspecialchars($license_number); ?>" 
                           placeholder="Enter your license number" required>
                </div>
                
                <!-- Profile Picture -->
                <div class="mb-3">
                    <label for="profile_image" class="form-label">Profile Picture</label>
                    <input type="file" class="form-control" id="profile_image" name="profile_image" accept="image/*" onchange="previewImage(this, 'imagePreview')">
                    <small class="text-muted">Upload your profile picture (Max 5MB, JPG/PNG/GIF)</small>
                    <img id="imagePreview" src="" style="display:none; max-width: 200px; margin-top: 10px;" class="img-thumbnail">
                </div>
                
                <!-- Terms and Conditions -->
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="terms" required>
                    <label class="form-check-label" for="terms">
                        I agree to the <a href="#" class="text-decoration-none">Terms and Conditions</a>
                    </label>
                </div>
                
                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary w-100 mb-3">
                    <i class="bi bi-person-plus"></i> Register
                </button>
                
                <!-- Login Link -->
                <div class="auth-links">
                    Already have an account? <a href="login.php">Login here</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
