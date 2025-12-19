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
$page_title = "View Patient";
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

// Get EHR records count for this patient
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM ehr_records WHERE patient_id = ?");
$stmt->bind_param("i", $patient_id);
$stmt->execute();
$ehr_count = $stmt->get_result()->fetch_assoc()['total'];
$stmt->close();
?>

<?php include '../../includes/header.php'; ?>

<div class="container mt-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-person"></i> Patient Details</h2>
        <div>
            <a href="edit_patient.php?id=<?php echo $patient_id; ?>" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="view_patients.php" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>
    
    <div class="row">
        <!-- Patient Information Card -->
        <div class="col-md-4 mb-4">
            <div class="card">
                <div class="card-body text-center">
                    <?php if ($patient['profile_image']): ?>
                        <img src="../../uploads/profile_pics/<?php echo $patient['profile_image']; ?>" class="profile-image mb-3" alt="Patient Photo">
                    <?php else: ?>
                        <i class="bi bi-person-circle text-muted" style="font-size: 10rem;"></i>
                    <?php endif; ?>
                    
                    <h4><?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></h4>
                    <p class="text-muted">Patient ID: <?php echo $patient['patient_id']; ?></p>
                    
                    <div class="d-grid gap-2 mt-3">
                        <a href="add_ehr.php?patient_id=<?php echo $patient_id; ?>" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Add EHR Record
                        </a>
                        <a href="patient_ehr_records.php?patient_id=<?php echo $patient_id; ?>" class="btn btn-outline-primary">
                            <i class="bi bi-file-medical"></i> View EHR Records (<?php echo $ehr_count; ?>)
                        </a>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Patient Details -->
        <div class="col-md-8">
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Personal Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-md-4"><strong>First Name:</strong></div>
                        <div class="col-md-8"><?php echo htmlspecialchars($patient['first_name']); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4"><strong>Last Name:</strong></div>
                        <div class="col-md-8"><?php echo htmlspecialchars($patient['last_name']); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4"><strong>Date of Birth:</strong></div>
                        <div class="col-md-8"><?php echo date('d/m/Y', strtotime($patient['date_of_birth'])); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4"><strong>Gender:</strong></div>
                        <div class="col-md-8"><?php echo htmlspecialchars($patient['gender']); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4"><strong>Blood Group:</strong></div>
                        <div class="col-md-8"><?php echo htmlspecialchars($patient['blood_group'] ?: 'Not specified'); ?></div>
                    </div>
                </div>
            </div>
            
            <div class="card mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Contact Information</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-md-4"><strong>Phone:</strong></div>
                        <div class="col-md-8"><?php echo htmlspecialchars($patient['phone'] ?: 'Not provided'); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4"><strong>Email:</strong></div>
                        <div class="col-md-8"><?php echo htmlspecialchars($patient['email'] ?: 'Not provided'); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4"><strong>Address:</strong></div>
                        <div class="col-md-8"><?php echo nl2br(htmlspecialchars($patient['address'] ?: 'Not provided')); ?></div>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Emergency Contact</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-md-4"><strong>Contact Name:</strong></div>
                        <div class="col-md-8"><?php echo htmlspecialchars($patient['emergency_contact_name'] ?: 'Not provided'); ?></div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-md-4"><strong>Contact Phone:</strong></div>
                        <div class="col-md-8"><?php echo htmlspecialchars($patient['emergency_contact_phone'] ?: 'Not provided'); ?></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
