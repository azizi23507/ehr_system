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
$page_title = "All EHR Records";
$base_url = "../../";

// Get doctor ID
$doctor_id = $_SESSION['doctor_id'];

// Get all EHR records with patient info
$stmt = $conn->prepare("SELECT e.*, p.first_name, p.last_name FROM ehr_records e JOIN patients p ON e.patient_id = p.patient_id WHERE e.doctor_id = ? ORDER BY e.visit_date DESC");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$ehr_records = $stmt->get_result();
$stmt->close();
?>

<?php include '../../includes/header.php'; ?>

<div class="container mt-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-file-medical"></i> All EHR Records</h2>
        <a href="../dashboard/dashboard.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Back to Dashboard
        </a>
    </div>
    
    <!-- Search Box -->
    <div class="card mb-4">
        <div class="card-body">
            <input type="text" id="searchInput" class="form-control" placeholder="Search by patient name or diagnosis..." onkeyup="searchTable('searchInput', 'ehrTable')">
        </div>
    </div>
    
    <!-- EHR Records Table -->
    <div class="card">
        <div class="card-body">
            <?php if ($ehr_records->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover" id="ehrTable">
                        <thead class="table-primary">
                            <tr>
                                <th>Patient Name</th>
                                <th>Visit Date</th>
                                <th>Blood Pressure</th>
                                <th>BMI</th>
                                <th>Diagnosis</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($record = $ehr_records->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($record['first_name'] . ' ' . $record['last_name']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($record['visit_date'])); ?></td>
                                    <td><?php echo htmlspecialchars($record['blood_pressure'] ?: 'N/A'); ?></td>
                                    <td><?php echo $record['bmi'] ? number_format($record['bmi'], 2) : 'N/A'; ?></td>
                                    <td><?php echo htmlspecialchars(substr($record['diagnosis'] ?: 'No diagnosis', 0, 40)) . (strlen($record['diagnosis']) > 40 ? '...' : ''); ?></td>
                                    <td>
                                        <a href="view_ehr.php?id=<?php echo $record['ehr_id']; ?>" class="btn btn-sm btn-info" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="edit_ehr.php?id=<?php echo $record['ehr_id']; ?>" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="delete_ehr.php?id=<?php echo $record['ehr_id']; ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirmDelete('this EHR record')">
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
                    <i class="bi bi-info-circle"></i> No EHR records found. 
                    <a href="view_patients.php" class="alert-link">Add patients and create EHR records</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
