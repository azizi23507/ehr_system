<?php
// Start session
global $conn;
session_start();

// Check if user is logged in
if (!isset($_SESSION['doctor_id'])) {
    header("Location: ../auth/login.php?login_required=1");
    exit();
}

// Include database connection
require_once '../../config/database.php';

// Set page variables
$page_title = "My Profile";
$base_url = "../../";

// Get doctor ID
$doctor_id = $_SESSION['doctor_id'];

// Get doctor information
$stmt = $conn->prepare("SELECT * FROM doctors WHERE doctor_id = ?");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$doctor = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Initialize variables
$success_message = "";
$error_message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $first_name = htmlspecialchars(trim($_POST['first_name']));
    $last_name = htmlspecialchars(trim($_POST['last_name']));
    $email = htmlspecialchars(trim($_POST['email']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $specialization = htmlspecialchars(trim($_POST['specialization']));
    
    // Handle profile image upload
    $profile_image = $doctor['profile_image']; // Keep existing image by default
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['profile_image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed) && $_FILES['profile_image']['size'] <= 5000000) {
            $new_filename = 'doctor_' . uniqid() . '.' . $ext;
            $upload_path = '../../uploads/profile_pics/' . $new_filename;
            
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path)) {
                // Delete old image if exists
                if ($doctor['profile_image'] && file_exists('../../uploads/profile_pics/' . $doctor['profile_image'])) {
                    unlink('../../uploads/profile_pics/' . $doctor['profile_image']);
                }
                $profile_image = $new_filename;
            }
        }
    }
    
    // Update doctor information
    $stmt = $conn->prepare("UPDATE doctors SET first_name=?, last_name=?, email=?, phone=?, specialization=?, profile_image=? WHERE doctor_id=?");
    $stmt->bind_param("ssssssi", $first_name, $last_name, $email, $phone, $specialization, $profile_image, $doctor_id);
    
    if ($stmt->execute()) {
        // Update session
        $_SESSION['doctor_name'] = $first_name . ' ' . $last_name;
        $_SESSION['doctor_email'] = $email;
        
        $success_message = "Profile updated successfully!";
        $doctor = [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'username' => $doctor['username'],
            'phone' => $phone,
            'specialization' => $specialization,
            'license_number' => $doctor['license_number'],
            'profile_image' => $profile_image,
            'created_at' => $doctor['created_at']
        ];
    } else {
        $error_message = "Failed to update profile.";
    }
    $stmt->close();
}
?>

<?php include '../../includes/header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-person-circle"></i> My Profile</h2>
                <a href="../dashboard/dashboard.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Dashboard
                </a>
            </div>
            
            <!-- Success Message -->
            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle"></i> <?php echo $success_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- Error Message -->
            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-triangle"></i> <?php echo $error_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <!-- Profile Information -->
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Profile Picture</h5>
                </div>
                <div class="card-body text-center">
                    <?php if ($doctor['profile_image']): ?>
                        <img src="../../uploads/profile_pics/<?php echo $doctor['profile_image']; ?>" class="rounded-circle mb-3" width="150" height="150" style="object-fit: cover;">
                    <?php else: ?>
                        <i class="bi bi-person-circle text-muted mb-3" style="font-size: 8rem;"></i>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Account Information -->
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Account Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-md-4"><strong>Username:</strong></div>
                        <div class="col-md-8"><?php echo htmlspecialchars($doctor['username']); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4"><strong>License Number:</strong></div>
                        <div class="col-md-8"><?php echo htmlspecialchars($doctor['license_number']); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4"><strong>Member Since:</strong></div>
                        <div class="col-md-8"><?php echo date('d/m/Y', strtotime($doctor['created_at'])); ?></div>
                    </div>
                </div>
            </div>
            
            <!-- Edit Profile Form -->
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Edit Profile</h5>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="first_name" value="<?php echo htmlspecialchars($doctor['first_name']); ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="last_name" value="<?php echo htmlspecialchars($doctor['last_name']); ?>" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($doctor['email']); ?>" required>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Phone</label>
                            <input type="tel" class="form-control" name="phone" value="<?php echo htmlspecialchars($doctor['phone']); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Specialization <span class="text-danger">*</span></label>
                            <select class="form-select" name="specialization" required>
                                <option value="">Select your specialization</option>
                                <option value="General Physician" <?php echo ($doctor['specialization'] == "General Physician") ? "selected" : ""; ?>>General Physician</option>
                                <option value="Cardiologist" <?php echo ($doctor['specialization'] == "Cardiologist") ? "selected" : ""; ?>>Cardiologist</option>
                                <option value="Dermatologist" <?php echo ($doctor['specialization'] == "Dermatologist") ? "selected" : ""; ?>>Dermatologist</option>
                                <option value="Neurologist" <?php echo ($doctor['specialization'] == "Neurologist") ? "selected" : ""; ?>>Neurologist</option>
                                <option value="Pediatrician" <?php echo ($doctor['specialization'] == "Pediatrician") ? "selected" : ""; ?>>Pediatrician</option>
                                <option value="Psychiatrist" <?php echo ($doctor['specialization'] == "Psychiatrist") ? "selected" : ""; ?>>Psychiatrist</option>
                                <option value="Surgeon" <?php echo ($doctor['specialization'] == "Surgeon") ? "selected" : ""; ?>>Surgeon</option>
                                <option value="Orthopedic" <?php echo ($doctor['specialization'] == "Orthopedic") ? "selected" : ""; ?>>Orthopedic</option>
                                <option value="ENT Specialist" <?php echo ($doctor['specialization'] == "ENT Specialist") ? "selected" : ""; ?>>ENT Specialist</option>
                                <option value="Gynecologist" <?php echo ($doctor['specialization'] == "Gynecologist") ? "selected" : ""; ?>>Gynecologist</option>
                                <option value="Other" <?php echo ($doctor['specialization'] == "Other") ? "selected" : ""; ?>>Other</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Profile Picture</label>
                            <?php if ($doctor['profile_image']): ?>
                                <div class="mb-2">
                                    <img src="../../uploads/profile_pics/<?php echo $doctor['profile_image']; ?>" style="max-width: 150px;" class="img-thumbnail rounded-circle">
                                </div>
                            <?php endif; ?>
                            <input type="file" class="form-control" name="profile_image" accept="image/*" onchange="previewImage(this, 'imagePreview')">
                            <small class="text-muted">Leave empty to keep current image. Max 5MB (JPG/PNG/GIF)</small>
                            <img id="imagePreview" src="" style="display:none; max-width: 150px; margin-top: 10px;" class="img-thumbnail rounded-circle">
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-save"></i> Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
