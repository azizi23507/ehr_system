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
$page_title = "Patient EHR Records";
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

// Get all EHR records for this patient
$stmt = $conn->prepare("SELECT * FROM ehr_records WHERE patient_id = ? AND doctor_id = ? ORDER BY visit_date DESC");
$stmt->bind_param("ii", $patient_id, $doctor_id);
$stmt->execute();
$ehr_records = $stmt->get_result();
$stmt->close();
?>

<?php include '../../includes/header.php'; ?>

<div class="container mt-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="bi bi-file-medical"></i> EHR Records</h2>
            <p class="text-muted">Patient: <?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></p>
        </div>
        <div>
            <a href="add_ehr.php?patient_id=<?php echo $patient_id; ?>" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Add EHR Record
            </a>
            <a href="view_patient.php?id=<?php echo $patient_id; ?>" class="btn btn-secondary">
                <i class="bi bi-arrow-left"></i> Back to Patient
            </a>
        </div>
    </div>
    
    <!-- EHR Records List -->
    <div class="card">
        <div class="card-body">
            <?php if ($ehr_records->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-primary">
                            <tr>
                                <th>Visit Date</th>
                                <th>Blood Pressure</th>
                                <th>Heart Rate</th>
                                <th>Temperature</th>
                                <th>Diagnosis</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($record = $ehr_records->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo date('d/m/Y', strtotime($record['visit_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($record['blood_pressure'] ?: 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($record['heart_rate'] ? $record['heart_rate'] . ' bpm' : 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars($record['temperature'] ? $record['temperature'] . '°C' : 'N/A'); ?></td>
                                    <td><?php echo htmlspecialchars(substr($record['diagnosis'] ?: 'No diagnosis', 0, 50)) . (strlen($record['diagnosis']) > 50 ? '...' : ''); ?></td>
                                    <td>
                                        <a href="view_ehr.php?id=<?php echo $record['ehr_id']; ?>" class="btn btn-sm btn-info" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="edit_ehr.php?id=<?php echo $record['ehr_id']; ?>" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="delete_ehr.php?id=<?php echo $record['ehr_id']; ?>&patient_id=<?php echo $patient_id; ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirmDelete('this EHR record')">
                                            <i class="bi bi-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> No EHR records found for this patient. 
                    <a href="add_ehr.php?patient_id=<?php echo $patient_id; ?>" class="alert-link">Add the first EHR record</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
