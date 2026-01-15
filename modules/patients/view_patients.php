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
$page_title = "View Patients";
$base_url = "../../";

// Get doctor ID
$doctor_id = $_SESSION['doctor_id'];

// Get all patients for this doctor
$stmt = $conn->prepare("SELECT * FROM patients WHERE doctor_id = ? ORDER BY created_at DESC");
$stmt->bind_param("i", $doctor_id);
$stmt->execute();
$patients = $stmt->get_result();
$stmt->close();
?>

<?php include '../../includes/header.php'; ?>

<div class="container mt-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-people"></i> My Patients</h2>
        <a href="add_patient.php" class="btn btn-primary">
            <i class="bi bi-person-plus"></i> Add New Patient
        </a>
    </div>
    
    <!-- Search Box -->
    <div class="card mb-4">
        <div class="card-body">
            <input type="text" id="searchInput" class="form-control" placeholder="Search by Patient ID, name, email, or phone..." onkeyup="searchTable('searchInput', 'patientsTable')">
        </div>
    </div>
    
    <!-- Patients Table -->
    <div class="card">
        <div class="card-body">
            <?php if ($patients->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover" id="patientsTable">
                        <thead class="table-primary">
                            <tr>
                                <th>Patient ID</th>
                                <th>Photo</th>
                                <th>Name</th>
                                <th>Date of Birth</th>
                                <th>Gender</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($patient = $patients->fetch_assoc()): ?>
                                <tr>
                                    <td><strong>#<?php echo str_pad($patient['patient_id'], 4, '0', STR_PAD_LEFT); ?></strong></td>
                                    <td>
                                        <?php if ($patient['profile_image']): ?>
                                            <img src="../../uploads/profile_pics/<?php echo $patient['profile_image']; ?>" class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                                        <?php else: ?>
                                            <i class="bi bi-person-circle" style="font-size: 2rem;"></i>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($patient['date_of_birth'])); ?></td>
                                    <td><?php echo htmlspecialchars($patient['gender']); ?></td>
                                    <td><?php echo htmlspecialchars($patient['phone']); ?></td>
                                    <td><?php echo htmlspecialchars($patient['email']); ?></td>
                                    <td>
                                        <a href="view_patient.php?id=<?php echo $patient['patient_id']; ?>" class="btn btn-sm btn-info" title="View">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="edit_patient.php?id=<?php echo $patient['patient_id']; ?>" class="btn btn-sm btn-warning" title="Edit">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                        <a href="delete_patient.php?id=<?php echo $patient['patient_id']; ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirmDelete('this patient')">
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
                    <i class="bi bi-info-circle"></i> No patients found. 
                    <a href="add_patient.php" class="alert-link">Add your first patient</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
