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
$page_title = "View EHR Record";
$base_url = "../../";

// Get doctor ID
$doctor_id = $_SESSION['doctor_id'];

// Get EHR ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ehr_records.php");
    exit();
}

$ehr_id = (int)$_GET['id'];

// Get EHR record with patient info (only if belongs to this doctor)
$stmt = $conn->prepare("SELECT e.*, p.first_name, p.last_name, p.patient_id FROM ehr_records e JOIN patients p ON e.patient_id = p.patient_id WHERE e.ehr_id = ? AND e.doctor_id = ?");
$stmt->bind_param("ii", $ehr_id, $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: ehr_records.php");
    exit();
}

$record = $result->fetch_assoc();
$stmt->close();
?>

<?php include '../../includes/header.php'; ?>

<div class="container mt-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="bi bi-file-medical-fill"></i> EHR Record Details</h2>
            <p class="text-muted">Patient: <?php echo htmlspecialchars($record['first_name'] . ' ' . $record['last_name']); ?></p>
        </div>
        <div>
            <a href="edit_ehr.php?id=<?php echo $ehr_id; ?>" class="btn btn-warning">
                <i class="bi bi-pencil"></i> Edit
            </a>
            <a href="patient_ehr_records.php?patient_id=<?php echo $record['patient_id']; ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back
            </a>
        </div>
    </div>
    
    <div class="row">
        <!-- Visit Information -->
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Visit Information</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>Visit Date:</strong> <?php echo date('d/m/Y', strtotime($record['visit_date'])); ?>
                        </div>
                        <div class="col-md-6">
                            <strong>Record Created:</strong> <?php echo date('d/m/Y H:i', strtotime($record['created_at'])); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Vital Signs -->
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Vital Signs</h5>
                </div>
                <div class="card-body">
                    <p><strong>Height:</strong> <?php echo $record['height'] ? $record['height'] . ' cm' : 'Not recorded'; ?></p>
                    <p><strong>Weight:</strong> <?php echo $record['weight'] ? $record['weight'] . ' kg' : 'Not recorded'; ?></p>
                    <p><strong>BMI:</strong> <?php echo $record['bmi'] ? number_format($record['bmi'], 2) : 'Not calculated'; ?></p>
                    <p><strong>Blood Pressure:</strong> <?php echo htmlspecialchars($record['blood_pressure'] ?: 'Not recorded'); ?></p>
                    <p><strong>Heart Rate:</strong> <?php echo $record['heart_rate'] ? $record['heart_rate'] . ' bpm' : 'Not recorded'; ?></p>
                    <p class="mb-0"><strong>Temperature:</strong> <?php echo $record['temperature'] ? $record['temperature'] . '°C' : 'Not recorded'; ?></p>
                </div>
            </div>
        </div>
        
        <!-- Allergies & Immunization -->
        <div class="col-md-6 mb-3">
            <div class="card h-100">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Allergies & Immunization</h5>
                </div>
                <div class="card-body">
                    <p><strong>Allergies:</strong></p>
                    <ul class="mb-2">
                        <?php if ($record['allergy_drugs']): ?><li>Drug Allergies</li><?php endif; ?>
                        <?php if ($record['allergy_food']): ?><li>Food Allergies</li><?php endif; ?>
                        <?php if ($record['allergy_environmental']): ?><li>Environmental Allergies</li><?php endif; ?>
                        <?php if ($record['allergy_other']): ?><li>Other Allergies</li><?php endif; ?>
                        <?php if (!$record['allergy_drugs'] && !$record['allergy_food'] && !$record['allergy_environmental'] && !$record['allergy_other']): ?>
                            <li>No allergies recorded</li>
                        <?php endif; ?>
                    </ul>
                    <?php if ($record['allergy_details']): ?>
                        <p class="mb-2"><strong>Allergy Details:</strong><br><?php echo nl2br(htmlspecialchars($record['allergy_details'])); ?></p>
                    <?php endif; ?>
                    <p class="mb-0"><strong>Immunization Status:</strong> 
                        <span class="badge bg-<?php echo $record['immunization_status'] == 'Up-to-date' ? 'success' : ($record['immunization_status'] == 'Incomplete' ? 'warning' : 'secondary'); ?>">
                            <?php echo htmlspecialchars($record['immunization_status']); ?>
                        </span>
                    </p>
                    <?php if ($record['immunization_details']): ?>
                        <p class="mt-2 mb-0"><small><?php echo nl2br(htmlspecialchars($record['immunization_details'])); ?></small></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        
        <!-- Medical History -->
        <?php if ($record['medical_history']): ?>
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Medical History</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($record['medical_history'])); ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Current Medications -->
        <?php if ($record['current_medications']): ?>
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Current Medications</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($record['current_medications'])); ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Lab Results -->
        <?php if ($record['lab_results']): ?>
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Lab Results</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($record['lab_results'])); ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Diagnosis -->
        <?php if ($record['diagnosis']): ?>
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Diagnosis</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($record['diagnosis'])); ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Treatment Plan -->
        <?php if ($record['treatment_plan']): ?>
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Treatment Plan</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($record['treatment_plan'])); ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Doctor's Notes -->
        <?php if ($record['doctor_notes']): ?>
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Doctor's Notes</h5>
                </div>
                <div class="card-body">
                    <p class="mb-0"><?php echo nl2br(htmlspecialchars($record['doctor_notes'])); ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Medical Documents -->
        <?php if ($record['xray_image'] || $record['report_document']): ?>
        <div class="col-md-12 mb-3">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Medical Documents</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <?php if ($record['xray_image']): ?>
                        <div class="col-md-6 mb-3">
                            <h6>X-ray / Medical Image:</h6>
                            <a href="../../uploads/documents/<?php echo $record['xray_image']; ?>" target="_blank">
                                <img src="../../uploads/documents/<?php echo $record['xray_image']; ?>" class="img-thumbnail" style="max-width: 100%;">
                            </a>
                        </div>
                        <?php endif; ?>
                        
                        <?php if ($record['report_document']): ?>
                        <div class="col-md-6 mb-3">
                            <h6>Lab Report / Document:</h6>
                            <?php
                            $ext = pathinfo($record['report_document'], PATHINFO_EXTENSION);
                            if (in_array(strtolower($ext), ['jpg', 'jpeg', 'png', 'gif'])):
                            ?>
                                <a href="../../uploads/documents/<?php echo $record['report_document']; ?>" target="_blank">
                                    <img src="../../uploads/documents/<?php echo $record['report_document']; ?>" class="img-thumbnail" style="max-width: 100%;">
                                </a>
                            <?php else: ?>
                                <a href="../../uploads/documents/<?php echo $record['report_document']; ?>" target="_blank" class="btn btn-primary">
                                    <i class="bi bi-download"></i> Download Document
                                </a>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
