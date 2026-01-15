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
$page_title = "Dashboard";
$base_url = "../../";

// Get doctor information
$doctor_id = $_SESSION['doctor_id'];
$doctor_name = $_SESSION['doctor_name'];

// Get statistics
// Count total patients
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM patients WHERE doctor_id = ?");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
$total_patients = $result->fetch_assoc()['total'];
$stmt->close();

// Count total EHR records
$stmt = $conn->prepare("SELECT COUNT(*) as total FROM ehr_records WHERE doctor_id = ?");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$result = $stmt->get_result();
$total_records = $result->fetch_assoc()['total'];
$stmt->close();

// Get recent patients (last 5)
$stmt = $conn->prepare("SELECT patient_id, first_name, last_name, date_of_birth, gender, created_at FROM patients WHERE doctor_id = ? ORDER BY created_at DESC LIMIT 5");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$recent_patients = $stmt->get_result();
$stmt->close();
?>

<?php include '../../includes/header.php'; ?>

<div class="container mt-4">
    <!-- Welcome Message -->
    <div class="row mb-4">
        <div class="col-12">
            <h2>Welcome, Dr. <?php echo htmlspecialchars($doctor_name); ?>!</h2>
            <p class="text-muted">Here's an overview of your EHR system</p>
        </div>
    </div>
    
    <!-- Statistics Cards -->
    <div class="row mb-4">
        <!-- Total Patients Card -->
        <div class="col-md-4 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Total Patients</h5>
                            <h2 class="mb-0"><?php echo $total_patients; ?></h2>
                        </div>
                        <div>
                            <i class="bi bi-people" style="font-size: 3rem; opacity: 0.5;"></i>
                        </div>
                    </div>
                    <a href="../patients/view_patients.php" class="btn btn-light btn-sm mt-3">
                        View Patients <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Total EHR Records Card -->
        <div class="col-md-4 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Total EHR Records</h5>
                            <h2 class="mb-0"><?php echo $total_records; ?></h2>
                        </div>
                        <div>
                            <i class="bi bi-file-medical" style="font-size: 3rem; opacity: 0.5;"></i>
                        </div>
                    </div>
                    <a href="../patients/ehr_records.php" class="btn btn-light btn-sm mt-3">
                        View Records <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Appointments Card -->
        <div class="col-md-4 mb-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Appointments</h5>
                            <h2 class="mb-0">
                                <?php
                                try {
                                    $apt_stmt = $conn->prepare("SELECT COUNT(*) as total FROM appointments WHERE doctor_id = ? AND status = 'scheduled' AND appointment_date >= CURDATE()");
                                    $apt_stmt->bind_param("i", $doctor_id);
                                    $apt_stmt->execute();
                                    $apt_result = $apt_stmt->get_result();
                                    echo $apt_result->fetch_assoc()['total'];
                                    $apt_stmt->close();
                                } catch (mysqli_sql_exception $e) {
                                    echo "0";
                                }
                                ?>
                            </h2>
                        </div>
                        <div>
                            <i class="bi bi-calendar3" style="font-size: 3rem; opacity: 0.5;"></i>
                        </div>
                    </div>
                    <a href="../appointments/view_appointments.php" class="btn btn-light btn-sm mt-3">
                        View Appointments <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-lightning"></i> Quick Actions</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-2">
                            <a href="../patients/add_patient.php" class="btn btn-outline-primary w-100">
                                <i class="bi bi-person-plus"></i> Add New Patient
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="../patients/view_patients.php" class="btn btn-outline-primary w-100">
                                <i class="bi bi-people"></i> View All Patients
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="../patients/ehr_records.php" class="btn btn-outline-primary w-100">
                                <i class="bi bi-file-medical"></i> View EHR Records
                            </a>
                        </div>
                        <div class="col-md-3 mb-2">
                            <a href="../appointments/book_appointment.php" class="btn btn-outline-primary w-100">
                                <i class="bi bi-calendar-plus"></i> Book Appointment
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Patients -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-clock-history"></i> Recent Patients</h5>
                </div>
                <div class="card-body">
                    <?php if ($recent_patients->num_rows > 0): ?>
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Date of Birth</th>
                                        <th>Gender</th>
                                        <th>Added On</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($patient = $recent_patients->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($patient['date_of_birth'])); ?></td>
                                            <td><?php echo htmlspecialchars($patient['gender']); ?></td>
                                            <td><?php echo date('d/m/Y', strtotime($patient['created_at'])); ?></td>
                                            <td>
                                                <a href="../patients/view_patient.php?id=<?php echo $patient['patient_id']; ?>" class="btn btn-sm btn-primary">
                                                    <i class="bi bi-eye"></i> View
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle"></i> No patients added yet. 
                            <a href="../patients/add_patient.php" class="alert-link">Add your first patient</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
