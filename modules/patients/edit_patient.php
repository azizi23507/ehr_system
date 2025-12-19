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
$page_title = "Edit Patient";
$base_url = "../../";

// Get doctor ID
$doctor_id = $_SESSION['doctor_id'];

// Get patient ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: view_patients.php");
    exit();
}

$patient_id = (int)$_GET['id'];

// Get patient details (only if belongs to this doctor)
$stmt = $conn->prepare("SELECT * FROM patients WHERE patient_id = ? AND doctor_id = ?");
$stmt->bind_param("ii", $patient_id, $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: view_patients.php");
    exit();
}

$patient = $result->fetch_assoc();
$stmt->close();

// Initialize variables
$success_message = "";
$error_message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $first_name = htmlspecialchars(trim($_POST['first_name']));
    $last_name = htmlspecialchars(trim($_POST['last_name']));
    $date_of_birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];
    $blood_group = htmlspecialchars(trim($_POST['blood_group']));
    $phone = htmlspecialchars(trim($_POST['phone']));
    $email = htmlspecialchars(trim($_POST['email']));
    $address = htmlspecialchars(trim($_POST['address']));
    $emergency_contact_name = htmlspecialchars(trim($_POST['emergency_contact_name']));
    $emergency_contact_phone = htmlspecialchars(trim($_POST['emergency_contact_phone']));
    
    // Handle profile image upload
    $profile_image = $patient['profile_image']; // Keep existing image by default
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['profile_image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed) && $_FILES['profile_image']['size'] <= 5000000) {
            $new_filename = uniqid() . '.' . $ext;
            $upload_path = '../../uploads/profile_pics/' . $new_filename;
            
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path)) {
                // Delete old image if exists
                if ($patient['profile_image'] && file_exists('../../uploads/profile_pics/' . $patient['profile_image'])) {
                    unlink('../../uploads/profile_pics/' . $patient['profile_image']);
                }
                $profile_image = $new_filename;
            }
        }
    }
    
    // Update database
    $stmt = $conn->prepare("UPDATE patients SET first_name=?, last_name=?, date_of_birth=?, gender=?, blood_group=?, phone=?, email=?, address=?, emergency_contact_name=?, emergency_contact_phone=?, profile_image=? WHERE patient_id=? AND doctor_id=?");
    $stmt->bind_param("sssssssssssii", $first_name, $last_name, $date_of_birth, $gender, $blood_group, $phone, $email, $address, $emergency_contact_name, $emergency_contact_phone, $profile_image, $patient_id, $doctor_id);
    
    if ($stmt->execute()) {
        $success_message = "Patient updated successfully!";
        // Refresh patient data
        $patient = [
            'first_name' => $first_name,
            'last_name' => $last_name,
            'date_of_birth' => $date_of_birth,
            'gender' => $gender,
            'blood_group' => $blood_group,
            'phone' => $phone,
            'email' => $email,
            'address' => $address,
            'emergency_contact_name' => $emergency_contact_name,
            'emergency_contact_phone' => $emergency_contact_phone,
            'profile_image' => $profile_image
        ];
    } else {
        $error_message = "Failed to update patient.";
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
                <h2><i class="bi bi-pencil"></i> Edit Patient</h2>
                <a href="view_patient.php?id=<?php echo $patient_id; ?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back
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
            
            <!-- Edit Patient Form -->
            <div class="card">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">First Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="first_name" value="<?php echo htmlspecialchars($patient['first_name']); ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Last Name <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="last_name" value="<?php echo htmlspecialchars($patient['last_name']); ?>" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Date of Birth <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" name="date_of_birth" value="<?php echo $patient['date_of_birth']; ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Gender <span class="text-danger">*</span></label><br>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="gender" value="Male" <?php echo ($patient['gender'] == 'Male') ? 'checked' : ''; ?> required>
                                    <label class="form-check-label">Male</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="gender" value="Female" <?php echo ($patient['gender'] == 'Female') ? 'checked' : ''; ?> required>
                                    <label class="form-check-label">Female</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="gender" value="Other" <?php echo ($patient['gender'] == 'Other') ? 'checked' : ''; ?> required>
                                    <label class="form-check-label">Other</label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Blood Group</label>
                                <select class="form-select" name="blood_group">
                                    <option value="">Select</option>
                                    <option value="A+" <?php echo ($patient['blood_group'] == 'A+') ? 'selected' : ''; ?>>A+</option>
                                    <option value="A-" <?php echo ($patient['blood_group'] == 'A-') ? 'selected' : ''; ?>>A-</option>
                                    <option value="B+" <?php echo ($patient['blood_group'] == 'B+') ? 'selected' : ''; ?>>B+</option>
                                    <option value="B-" <?php echo ($patient['blood_group'] == 'B-') ? 'selected' : ''; ?>>B-</option>
                                    <option value="AB+" <?php echo ($patient['blood_group'] == 'AB+') ? 'selected' : ''; ?>>AB+</option>
                                    <option value="AB-" <?php echo ($patient['blood_group'] == 'AB-') ? 'selected' : ''; ?>>AB-</option>
                                    <option value="O+" <?php echo ($patient['blood_group'] == 'O+') ? 'selected' : ''; ?>>O+</option>
                                    <option value="O-" <?php echo ($patient['blood_group'] == 'O-') ? 'selected' : ''; ?>>O-</option>
                                </select>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Phone</label>
                                <input type="tel" class="form-control" name="phone" value="<?php echo htmlspecialchars($patient['phone']); ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($patient['email']); ?>">
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" name="address" rows="2"><?php echo htmlspecialchars($patient['address']); ?></textarea>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Emergency Contact Name</label>
                                <input type="text" class="form-control" name="emergency_contact_name" value="<?php echo htmlspecialchars($patient['emergency_contact_name']); ?>">
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Emergency Contact Phone</label>
                                <input type="tel" class="form-control" name="emergency_contact_phone" value="<?php echo htmlspecialchars($patient['emergency_contact_phone']); ?>">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Profile Image</label>
                            <?php if ($patient['profile_image']): ?>
                                <div class="mb-2">
                                    <img src="../../uploads/profile_pics/<?php echo $patient['profile_image']; ?>" style="max-width: 200px;" class="img-thumbnail">
                                </div>
                            <?php endif; ?>
                            <input type="file" class="form-control" name="profile_image" accept="image/*" onchange="previewImage(this, 'imagePreview')">
                            <small class="text-muted">Leave empty to keep current image</small>
                            <img id="imagePreview" src="" style="display:none; max-width: 200px; margin-top: 10px;" class="img-thumbnail">
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-save"></i> Update Patient
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
