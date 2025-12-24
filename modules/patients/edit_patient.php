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

// Initialize variables for form fields (pre-fill with existing values)
$first_name = $patient['first_name'];
$last_name = $patient['last_name'];
$date_of_birth = $patient['date_of_birth'];
$gender = $patient['gender'];
$blood_group = $patient['blood_group'];
$phone = $patient['phone'];
$email = $patient['email'];
$address = $patient['address'];
$emergency_contact_name = $patient['emergency_contact_name'];
$emergency_contact_phone = $patient['emergency_contact_phone'];
$profile_image = $patient['profile_image'];

$success_message = "";
$error_message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
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

    // Handle profile image upload (keep existing if no new upload)
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['profile_image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (in_array($ext, $allowed) && $_FILES['profile_image']['size'] <= 5000000) {
            $new_filename = uniqid() . '.' . $ext;
            $upload_path = '../../uploads/profile_pics/' . $new_filename;

            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $upload_path)) {
                // Delete old image if exists
                if ($profile_image && file_exists('../../uploads/profile_pics/' . $profile_image)) {
                    @unlink('../../uploads/profile_pics/' . $profile_image);
                }
                $profile_image = $new_filename;
            }
        }
    }

    // Update database
    $stmt = $conn->prepare("UPDATE patients SET first_name=?, last_name=?, date_of_birth=?, gender=?, blood_group=?, phone=?, email=?, address=?, emergency_contact_name=?, emergency_contact_phone=?, profile_image=? WHERE patient_id=? AND doctor_id=?");

    // types: 11 strings for fields, then two integers for patient_id and doctor_id
    $types = "sssssssssssii";
    $stmt->bind_param($types,
        $first_name, $last_name, $date_of_birth, $gender, $blood_group, $phone, $email, $address, $emergency_contact_name, $emergency_contact_phone, $profile_image,
        $patient_id, $doctor_id
    );

    if ($stmt->execute()) {
        $success_message = "Patient updated successfully!";
        // Refresh patient data
        $stmt->close();
        $stmt = $conn->prepare("SELECT * FROM patients WHERE patient_id = ?");
        $stmt->bind_param("i", $patient_id);
        $stmt->execute();
        $patient = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    } else {
        $error_message = "Failed to update patient. Please try again.";
    }
}

?>

<?php include '../../includes/header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-person-lines-fill"></i> Edit Patient</h2>
                <a href="view_patient.php?id=<?php echo $patient_id; ?>" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back
                </a>
            </div>

            <?php if (!empty($success_message)): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle"></i> <?php echo $success_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <?php if (!empty($error_message)): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-triangle"></i> <?php echo $error_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <div class="card">
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="first_name">First Name <span class="text-danger">*</span></label>
                                <input id="first_name" type="text" class="form-control" name="first_name" value="<?php echo htmlspecialchars($patient['first_name']); ?>" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="last_name">Last Name <span class="text-danger">*</span></label>
                                <input id="last_name" type="text" class="form-control" name="last_name" value="<?php echo htmlspecialchars($patient['last_name']); ?>" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="date_of_birth">Date of Birth <span class="text-danger">*</span></label>
                                <input id="date_of_birth" type="date" class="form-control" name="date_of_birth" value="<?php echo $patient['date_of_birth']; ?>" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">Gender <span class="text-danger">*</span></label><br>
                                <div class="form-check form-check-inline">
                                    <input id="gender_m" class="form-check-input" type="radio" name="gender" value="Male" <?php echo ($patient['gender'] == 'Male') ? 'checked' : ''; ?> required>
                                    <label class="form-check-label" for="gender_m">Male</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input id="gender_f" class="form-check-input" type="radio" name="gender" value="Female" <?php echo ($patient['gender'] == 'Female') ? 'checked' : ''; ?> required>
                                    <label class="form-check-label" for="gender_f">Female</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input id="gender_o" class="form-check-input" type="radio" name="gender" value="Other" <?php echo ($patient['gender'] == 'Other') ? 'checked' : ''; ?> required>
                                    <label class="form-check-label" for="gender_o">Other</label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="blood_group">Blood Group</label>
                                <select id="blood_group" class="form-select" name="blood_group">
                                    <option value="">Select</option>
                                    <?php
                                    $groups = ['A+','A-','B+','B-','AB+','AB-','O+','O-'];
                                    foreach ($groups as $g) {
                                        $sel = ($patient['blood_group'] == $g) ? 'selected' : '';
                                        echo "<option value=\"$g\" $sel>$g</option>";
                                    }
                                    ?>
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="phone">Phone</label>
                                <input id="phone" type="tel" class="form-control" name="phone" value="<?php echo htmlspecialchars($patient['phone']); ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="email">Email</label>
                            <input id="email" type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($patient['email']); ?>">
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="address">Address</label>
                            <textarea id="address" class="form-control" name="address" rows="2"><?php echo htmlspecialchars($patient['address']); ?></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="emergency_contact_name">Emergency Contact Name</label>
                                <input id="emergency_contact_name" type="text" class="form-control" name="emergency_contact_name" value="<?php echo htmlspecialchars($patient['emergency_contact_name']); ?>">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label" for="emergency_contact_phone">Emergency Contact Phone</label>
                                <input id="emergency_contact_phone" type="tel" class="form-control" name="emergency_contact_phone" value="<?php echo htmlspecialchars($patient['emergency_contact_phone']); ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label" for="profile_image">Profile Image</label>
                            <?php if ($patient['profile_image']): ?>
                                <div class="mb-2">
                                    <img src="../../uploads/profile_pics/<?php echo $patient['profile_image']; ?>" class="profile-image mb-3" alt="Patient profile image">
                                </div>
                            <?php endif; ?>
                            <input id="profile_image" type="file" class="form-control" name="profile_image" accept="image/*" onchange="previewImage(this, 'imagePreview')">
                            <small class="text-muted">Max 5MB, JPG/PNG/GIF</small>
                            <img id="imagePreview" src="" alt="Profile preview" style="display:none; max-width: 200px; margin-top: 10px;" class="img-thumbnail">
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