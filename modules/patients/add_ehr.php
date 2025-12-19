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
$page_title = "Add EHR Record";
$base_url = "../../";

// Get doctor ID
$doctor_id = $_SESSION['doctor_id'];

// Get patient ID from URL
if (!isset($_GET['patient_id']) || empty($_GET['patient_id'])) {
    header("Location: view_patients.php");
    exit();
}

$patient_id = (int)$_GET['patient_id'];

// Verify patient belongs to this doctor
$stmt = $conn->prepare("SELECT first_name, last_name FROM patients WHERE patient_id = ? AND doctor_id = ?");
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
    // Get form data - TEXT INPUTS (Requirement: at least 3 input data)
    $height = floatval($_POST['height']);
    $weight = floatval($_POST['weight']);
    $blood_pressure = htmlspecialchars(trim($_POST['blood_pressure']));
    $heart_rate = intval($_POST['heart_rate']);
    $temperature = floatval($_POST['temperature']);
    
    // Calculate BMI
    $bmi = ($height > 0 && $weight > 0) ? round($weight / (($height / 100) ** 2), 2) : 0;
    
    // TEXTAREA (Requirement: 1 textarea data)
    $medical_history = htmlspecialchars(trim($_POST['medical_history']));
    $current_medications = htmlspecialchars(trim($_POST['current_medications']));
    $allergy_details = htmlspecialchars(trim($_POST['allergy_details']));
    $lab_results = htmlspecialchars(trim($_POST['lab_results']));
    $diagnosis = htmlspecialchars(trim($_POST['diagnosis']));
    $treatment_plan = htmlspecialchars(trim($_POST['treatment_plan']));
    $doctor_notes = htmlspecialchars(trim($_POST['doctor_notes']));
    
    // CHECKBOX (Requirement: 1 checkbox data)
    $allergy_drugs = isset($_POST['allergy_drugs']) ? 1 : 0;
    $allergy_food = isset($_POST['allergy_food']) ? 1 : 0;
    $allergy_environmental = isset($_POST['allergy_environmental']) ? 1 : 0;
    $allergy_other = isset($_POST['allergy_other']) ? 1 : 0;
    
    // RADIO BUTTON (Requirement: 1 radio data)
    $immunization_status = $_POST['immunization_status'];
    $immunization_details = htmlspecialchars(trim($_POST['immunization_details']));
    
    // DATE INPUT (Requirement: 1 input data for date)
    $visit_date = $_POST['visit_date'];
    
    // IMAGE UPLOAD (Requirement: 1 image upload and display)
    $xray_image = null;
    $report_document = null;
    
    // Handle X-ray image upload
    if (isset($_FILES['xray_image']) && $_FILES['xray_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $filename = $_FILES['xray_image']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed) && $_FILES['xray_image']['size'] <= 5000000) {
            $new_filename = 'xray_' . uniqid() . '.' . $ext;
            $upload_path = '../../uploads/documents/' . $new_filename;
            
            if (move_uploaded_file($_FILES['xray_image']['tmp_name'], $upload_path)) {
                $xray_image = $new_filename;
            }
        }
    }
    
    // Handle report document upload
    if (isset($_FILES['report_document']) && $_FILES['report_document']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
        $filename = $_FILES['report_document']['name'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (in_array($ext, $allowed) && $_FILES['report_document']['size'] <= 5000000) {
            $new_filename = 'report_' . uniqid() . '.' . $ext;
            $upload_path = '../../uploads/documents/' . $new_filename;
            
            if (move_uploaded_file($_FILES['report_document']['tmp_name'], $upload_path)) {
                $report_document = $new_filename;
            }
        }
    }
    
    // Insert into database
    $stmt = $conn->prepare("INSERT INTO ehr_records (patient_id, doctor_id, height, weight, bmi, blood_pressure, heart_rate, temperature, medical_history, current_medications, allergy_drugs, allergy_food, allergy_environmental, allergy_other, allergy_details, immunization_status, immunization_details, lab_results, diagnosis, treatment_plan, doctor_notes, visit_date, xray_image, report_document) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param("iidddsidssiiiiississsss", 
        $patient_id, $doctor_id, $height, $weight, $bmi, $blood_pressure, $heart_rate, $temperature,
        $medical_history, $current_medications, $allergy_drugs, $allergy_food, $allergy_environmental, 
        $allergy_other, $allergy_details, $immunization_status, $immunization_details, $lab_results,
        $diagnosis, $treatment_plan, $doctor_notes, $visit_date, $xray_image, $report_document
    );
    
    if ($stmt->execute()) {
        $success_message = "EHR record added successfully!";
        header("refresh:2;url=patient_ehr_records.php?patient_id=" . $patient_id);
    } else {
        $error_message = "Failed to add EHR record.";
    }
    
    $stmt->close();
}
?>

<?php include '../../includes/header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i class="bi bi-file-medical-fill"></i> Add EHR Record</h2>
                    <p class="text-muted">Patient: <?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></p>
                </div>
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
            
            <!-- EHR Form -->
            <form method="POST" enctype="multipart/form-data">
                <!-- Visit Date (DATE INPUT - Required) -->
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Visit Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Visit Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="visit_date" value="<?php echo date('Y-m-d'); ?>" required>
                            <small class="text-muted">Date of this medical record</small>
                        </div>
                    </div>
                </div>
                
                <!-- Vital Signs (TEXT INPUTS - Required: at least 3) -->
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Vital Signs</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Height (cm) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" name="height" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Weight (kg) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" name="weight" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Blood Pressure <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="blood_pressure" placeholder="120/80" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Heart Rate (bpm)</label>
                                <input type="number" class="form-control" name="heart_rate" placeholder="72">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Temperature (°C)</label>
                                <input type="number" step="0.1" class="form-control" name="temperature" placeholder="36.5">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Medical History (TEXTAREA - Required) -->
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Medical History</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Medical History</label>
                            <textarea class="form-control" name="medical_history" rows="4" placeholder="Past illnesses, surgeries, conditions..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Current Medications</label>
                            <textarea class="form-control" name="current_medications" rows="3" placeholder="List of current medications..."></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Allergies (CHECKBOX - Required) -->
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Allergies</h5>
                    </div>
                    <div class="card-body">
                        <label class="form-label">Check all that apply:</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="allergy_drugs" id="allergy_drugs">
                            <label class="form-check-label" for="allergy_drugs">Drug Allergies</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="allergy_food" id="allergy_food">
                            <label class="form-check-label" for="allergy_food">Food Allergies</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="allergy_environmental" id="allergy_environmental">
                            <label class="form-check-label" for="allergy_environmental">Environmental Allergies</label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="allergy_other" id="allergy_other">
                            <label class="form-check-label" for="allergy_other">Other Allergies</label>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Allergy Details</label>
                            <textarea class="form-control" name="allergy_details" rows="2" placeholder="Specify allergy details..."></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Immunization (RADIO BUTTON - Required) -->
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Immunization Status</h5>
                    </div>
                    <div class="card-body">
                        <label class="form-label">Select immunization status: <span class="text-danger">*</span></label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="immunization_status" value="Up-to-date" id="imm1" required>
                            <label class="form-check-label" for="imm1">Up-to-date</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="immunization_status" value="Incomplete" id="imm2" required>
                            <label class="form-check-label" for="imm2">Incomplete</label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="immunization_status" value="Unknown" id="imm3" required>
                            <label class="form-check-label" for="imm3">Unknown</label>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Immunization Details</label>
                            <textarea class="form-control" name="immunization_details" rows="2" placeholder="List vaccines and dates..."></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Lab Results & Diagnosis -->
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Lab Results & Diagnosis</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Lab Results</label>
                            <textarea class="form-control" name="lab_results" rows="3" placeholder="Laboratory test results..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Diagnosis</label>
                            <textarea class="form-control" name="diagnosis" rows="3" placeholder="Medical diagnosis..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Treatment Plan</label>
                            <textarea class="form-control" name="treatment_plan" rows="3" placeholder="Treatment recommendations..."></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Doctor's Notes</label>
                            <textarea class="form-control" name="doctor_notes" rows="3" placeholder="Additional notes..."></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Document Upload (IMAGE UPLOAD - Required) -->
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Medical Documents</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">X-ray / Medical Image</label>
                            <input type="file" class="form-control" name="xray_image" accept="image/*" onchange="previewImage(this, 'xrayPreview')">
                            <small class="text-muted">Upload X-ray or medical images (Max 5MB)</small>
                            <img id="xrayPreview" src="" style="display:none; max-width: 300px; margin-top: 10px;" class="img-thumbnail">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lab Report / Document</label>
                            <input type="file" class="form-control" name="report_document" accept="image/*,.pdf" onchange="previewImage(this, 'reportPreview')">
                            <small class="text-muted">Upload lab reports or documents (Max 5MB)</small>
                            <img id="reportPreview" src="" style="display:none; max-width: 300px; margin-top: 10px;" class="img-thumbnail">
                        </div>
                    </div>
                </div>
                
                <!-- Submit Button -->
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-save"></i> Save EHR Record
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
