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
$page_title = "Edit EHR Record";
$base_url = "../../";

// Get doctor ID
$doctor_id = $_SESSION['doctor_id'];

// Get EHR ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ehr_records.php");
    exit();
}

$ehr_id = (int)$_GET['id'];

// Get EHR record (only if belongs to this doctor)
$stmt = $conn->prepare("SELECT e.*, p.first_name, p.last_name FROM ehr_records e JOIN patients p ON e.patient_id = p.patient_id WHERE e.ehr_id = ? AND e.doctor_id = ?");
$stmt->bind_param("ii", $ehr_id, $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: ehr_records.php");
    exit();
}

$record = $result->fetch_assoc();
$stmt->close();

// Initialize variables
$success_message = "";
$error_message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data (same as add_ehr.php)
    $height = floatval($_POST['height']);
    $weight = floatval($_POST['weight']);
    $blood_pressure = htmlspecialchars(trim($_POST['blood_pressure']));
    $heart_rate = intval($_POST['heart_rate']);
    $temperature = floatval($_POST['temperature']);
    $bmi = ($height > 0 && $weight > 0) ? round($weight / (($height / 100) ** 2), 2) : 0;
    
    $medical_history = htmlspecialchars(trim($_POST['medical_history']));
    $current_medications = htmlspecialchars(trim($_POST['current_medications']));
    $allergy_details = htmlspecialchars(trim($_POST['allergy_details']));
    $lab_results = htmlspecialchars(trim($_POST['lab_results']));
    $diagnosis = htmlspecialchars(trim($_POST['diagnosis']));
    $treatment_plan = htmlspecialchars(trim($_POST['treatment_plan']));
    $doctor_notes = htmlspecialchars(trim($_POST['doctor_notes']));
    
    $allergy_drugs = isset($_POST['allergy_drugs']) ? 1 : 0;
    $allergy_food = isset($_POST['allergy_food']) ? 1 : 0;
    $allergy_environmental = isset($_POST['allergy_environmental']) ? 1 : 0;
    $allergy_other = isset($_POST['allergy_other']) ? 1 : 0;
    
    $immunization_status = $_POST['immunization_status'];
    $immunization_details = htmlspecialchars(trim($_POST['immunization_details']));
    $visit_date = $_POST['visit_date'];
    
    // Handle file uploads (keep existing if no new upload)
    $xray_image = $record['xray_image'];
    $report_document = $record['report_document'];
    
    if (isset($_FILES['xray_image']) && $_FILES['xray_image']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        $ext = strtolower(pathinfo($_FILES['xray_image']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed) && $_FILES['xray_image']['size'] <= 5000000) {
            $new_filename = 'xray_' . uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['xray_image']['tmp_name'], '../../uploads/documents/' . $new_filename)) {
                if ($record['xray_image'] && file_exists('../../uploads/documents/' . $record['xray_image'])) {
                    unlink('../../uploads/documents/' . $record['xray_image']);
                }
                $xray_image = $new_filename;
            }
        }
    }
    
    if (isset($_FILES['report_document']) && $_FILES['report_document']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
        $ext = strtolower(pathinfo($_FILES['report_document']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed) && $_FILES['report_document']['size'] <= 5000000) {
            $new_filename = 'report_' . uniqid() . '.' . $ext;
            if (move_uploaded_file($_FILES['report_document']['tmp_name'], '../../uploads/documents/' . $new_filename)) {
                if ($record['report_document'] && file_exists('../../uploads/documents/' . $record['report_document'])) {
                    unlink('../../uploads/documents/' . $record['report_document']);
                }
                $report_document = $new_filename;
            }
        }
    }
    
    // Update database
    $stmt = $conn->prepare("UPDATE ehr_records SET height=?, weight=?, bmi=?, blood_pressure=?, heart_rate=?, temperature=?, medical_history=?, current_medications=?, allergy_drugs=?, allergy_food=?, allergy_environmental=?, allergy_other=?, allergy_details=?, immunization_status=?, immunization_details=?, lab_results=?, diagnosis=?, treatment_plan=?, doctor_notes=?, visit_date=?, xray_image=?, report_document=? WHERE ehr_id=? AND doctor_id=?");
    
    // Corrected types: 24 parameters (height:d, weight:d, bmi:d, blood_pressure:s, heart_rate:i, temperature:d,
    // medical_history:s, current_medications:s, allergy_drugs:i, allergy_food:i, allergy_environmental:i, allergy_other:i,
    // allergy_details:s, immunization_status:s, immunization_details:s, lab_results:s, diagnosis:s, treatment_plan:s, doctor_notes:s,
    // visit_date:s, xray_image:s, report_document:s, ehr_id:i, doctor_id:i)
    $stmt->bind_param("dddsidssiiiissssssssssii",
        $height, $weight, $bmi, $blood_pressure, $heart_rate, $temperature,
        $medical_history, $current_medications, $allergy_drugs, $allergy_food, $allergy_environmental, 
        $allergy_other, $allergy_details, $immunization_status, $immunization_details, $lab_results,
        $diagnosis, $treatment_plan, $doctor_notes, $visit_date, $xray_image, $report_document,
        $ehr_id, $doctor_id
    );
    
    if ($stmt->execute()) {
        $success_message = "EHR record updated successfully!";
        // Refresh record data
        $stmt = $conn->prepare("SELECT * FROM ehr_records WHERE ehr_id = ?");
        $stmt->bind_param("i", $ehr_id);
        $stmt->execute();
        $record = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    } else {
        $error_message = "Failed to update EHR record.";
    }
}
?>

<?php include '../../includes/header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-lg-10 mx-auto">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i class="bi bi-pencil"></i> Edit EHR Record</h2>
                    <p class="text-muted">Patient: <?php echo htmlspecialchars($record['first_name'] . ' ' . $record['last_name']); ?></p>
                </div>
                <a href="view_ehr.php?id=<?php echo $ehr_id; ?>" class="btn btn-secondary">
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
            
            <!-- EHR Form (same as add_ehr.php but with values pre-filled) -->
            <form method="POST" enctype="multipart/form-data">
                <!-- Visit Date -->
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Visit Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Visit Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" name="visit_date" value="<?php echo $record['visit_date']; ?>" required>
                        </div>
                    </div>
                </div>
                
                <!-- Vital Signs -->
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Vital Signs</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Height (cm) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" name="height" value="<?php echo $record['height']; ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Weight (kg) <span class="text-danger">*</span></label>
                                <input type="number" step="0.01" class="form-control" name="weight" value="<?php echo $record['weight']; ?>" required>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label">Blood Pressure <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="blood_pressure" value="<?php echo htmlspecialchars($record['blood_pressure']); ?>" required>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Heart Rate (bpm)</label>
                                <input type="number" class="form-control" name="heart_rate" value="<?php echo $record['heart_rate']; ?>">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Temperature (°C)</label>
                                <input type="number" step="0.1" class="form-control" name="temperature" value="<?php echo $record['temperature']; ?>">
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Medical History -->
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Medical History</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Medical History</label>
                            <textarea class="form-control" name="medical_history" rows="4"><?php echo htmlspecialchars($record['medical_history']); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Current Medications</label>
                            <textarea class="form-control" name="current_medications" rows="3"><?php echo htmlspecialchars($record['current_medications']); ?></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Allergies -->
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Allergies</h5>
                    </div>
                    <div class="card-body">
                        <label class="form-label">Check all that apply:</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="allergy_drugs" <?php echo $record['allergy_drugs'] ? 'checked' : ''; ?>>
                            <label class="form-check-label">Drug Allergies</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="allergy_food" <?php echo $record['allergy_food'] ? 'checked' : ''; ?>>
                            <label class="form-check-label">Food Allergies</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="allergy_environmental" <?php echo $record['allergy_environmental'] ? 'checked' : ''; ?>>
                            <label class="form-check-label">Environmental Allergies</label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" name="allergy_other" <?php echo $record['allergy_other'] ? 'checked' : ''; ?>>
                            <label class="form-check-label">Other Allergies</label>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Allergy Details</label>
                            <textarea class="form-control" name="allergy_details" rows="2"><?php echo htmlspecialchars($record['allergy_details']); ?></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Immunization -->
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Immunization Status</h5>
                    </div>
                    <div class="card-body">
                        <label class="form-label">Select immunization status: <span class="text-danger">*</span></label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="immunization_status" value="Up-to-date" <?php echo ($record['immunization_status'] == 'Up-to-date') ? 'checked' : ''; ?> required>
                            <label class="form-check-label">Up-to-date</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="immunization_status" value="Incomplete" <?php echo ($record['immunization_status'] == 'Incomplete') ? 'checked' : ''; ?> required>
                            <label class="form-check-label">Incomplete</label>
                        </div>
                        <div class="form-check mb-3">
                            <input class="form-check-input" type="radio" name="immunization_status" value="Unknown" <?php echo ($record['immunization_status'] == 'Unknown') ? 'checked' : ''; ?> required>
                            <label class="form-check-label">Unknown</label>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Immunization Details</label>
                            <textarea class="form-control" name="immunization_details" rows="2"><?php echo htmlspecialchars($record['immunization_details']); ?></textarea>
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
                            <textarea class="form-control" name="lab_results" rows="3"><?php echo htmlspecialchars($record['lab_results']); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Diagnosis</label>
                            <textarea class="form-control" name="diagnosis" rows="3"><?php echo htmlspecialchars($record['diagnosis']); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Treatment Plan</label>
                            <textarea class="form-control" name="treatment_plan" rows="3"><?php echo htmlspecialchars($record['treatment_plan']); ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Doctor's Notes</label>
                            <textarea class="form-control" name="doctor_notes" rows="3"><?php echo htmlspecialchars($record['doctor_notes']); ?></textarea>
                        </div>
                    </div>
                </div>
                
                <!-- Documents -->
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Medical Documents</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">X-ray / Medical Image</label>
                            <?php if ($record['xray_image']): ?>
                                <div class="mb-2">
                                    <img src="../../uploads/documents/<?php echo $record['xray_image']; ?>" style="max-width: 200px;" class="img-thumbnail" alt="X-ray image">
                                </div>
                            <?php endif; ?>
                            <input type="file" class="form-control" name="xray_image" accept="image/*">
                            <small class="text-muted">Leave empty to keep current image</small>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Lab Report / Document</label>
                            <?php if ($record['report_document']): ?>
                                <div class="mb-2">
                                    <a href="../../uploads/documents/<?php echo $record['report_document']; ?>" target="_blank">Current document</a>
                                </div>
                            <?php endif; ?>
                            <input type="file" class="form-control" name="report_document" accept="image/*,.pdf">
                            <small class="text-muted">Leave empty to keep current document</small>
                        </div>
                    </div>
                </div>
                
                <!-- Submit Button -->
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-save"></i> Update EHR Record
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
