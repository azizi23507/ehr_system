<?php
// Book Appointment Module
// Allows doctors to schedule appointments for their patients
// Includes patient selection, date/time picker, and reason entry
// Part of the EHR System appointments management

global $conn;
session_start();

// Authentication check - redirect to login if not authenticated
if (!isset($_SESSION['doctor_id'])) {
    header("Location: ../auth/login.php?login_required=1");
    exit();
}

require_once '../../config/database.php';

$page_title = "Book Appointment";
$base_url = "../../";

$doctor_id = $_SESSION['doctor_id'];

$success_message = "";
$error_message = "";

// Handle appointment booking form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Sanitize and validate input data
    $patient_id = (int)$_POST['patient_id'];
    $appointment_date = $_POST['appointment_date'];
    $appointment_time = $_POST['appointment_time'];
    $reason = htmlspecialchars(trim($_POST['reason']));
    $notes = htmlspecialchars(trim($_POST['notes']));
    
    // Validate required fields
    if (empty($patient_id) || empty($appointment_date) || empty($appointment_time)) {
        $error_message = "Please fill in all required fields.";
    } else {
        // Verify patient belongs to this doctor for security
        // Prevents booking appointments for other doctors' patients
        $verify_stmt = $conn->prepare("SELECT patient_id FROM patients WHERE patient_id = ? AND doctor_id = ?");
        $verify_stmt->bind_param("ii", $patient_id, $doctor_id);
        $verify_stmt->execute();
        $verify_result = $verify_stmt->get_result();
        
        if ($verify_result->num_rows == 0) {
            $error_message = "Invalid patient selection.";
        } else {
            // Insert appointment with prepared statement for SQL injection protection
            $stmt = $conn->prepare("INSERT INTO appointments (patient_id, doctor_id, appointment_date, appointment_time, reason, notes, status) VALUES (?, ?, ?, ?, ?, ?, 'scheduled')");
            $stmt->bind_param("iissss", $patient_id, $doctor_id, $appointment_date, $appointment_time, $reason, $notes);
            
            if ($stmt->execute()) {
                $success_message = "Appointment booked successfully!";
                $_SESSION['success_message'] = $success_message;
                header("Location: view_appointments.php");
                exit();
            } else {
                $error_message = "Error booking appointment: " . $conn->error;
            }
            $stmt->close();
        }
        $verify_stmt->close();
    }
}

// Fetch all patients for this doctor to populate dropdown
// Orders by name for easy selection
$patients_stmt = $conn->prepare("SELECT patient_id, first_name, last_name FROM patients WHERE doctor_id = ? ORDER BY first_name, last_name");
$patients_stmt->bind_param("i", $doctor_id);
$patients_stmt->execute();
$patients = $patients_stmt->get_result();
$patients_stmt->close();
?>

<?php include '../../includes/header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-calendar-plus"></i> Book Appointment</h2>
                <a href="view_appointments.php" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> Back to Appointments
                </a>
            </div>
            
            <?php if ($error_message): ?>
                <div class="alert alert-danger alert-dismissible fade show">
                    <i class="bi bi-exclamation-circle"></i> <?php echo $error_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <?php if ($success_message): ?>
                <div class="alert alert-success alert-dismissible fade show">
                    <i class="bi bi-check-circle"></i> <?php echo $success_message; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>
            
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Appointment Details</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="patient_id" class="form-label">Select Patient <span class="text-danger">*</span></label>
                            <select class="form-select" id="patient_id" name="patient_id" required>
                                <option value="">Choose a patient...</option>
                                <?php while ($patient = $patients->fetch_assoc()): ?>
                                    <option value="<?php echo $patient['patient_id']; ?>">
                                        <?php echo htmlspecialchars($patient['first_name'] . ' ' . $patient['last_name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="appointment_date" class="form-label">Date <span class="text-danger">*</span></label>
                                <input type="date" class="form-control" id="appointment_date" name="appointment_date" 
                                       min="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            
                            <div class="col-md-6 mb-3">
                                <label for="appointment_time" class="form-label">Time <span class="text-danger">*</span></label>
                                <input type="time" class="form-control" id="appointment_time" name="appointment_time" required>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="reason" class="form-label">Reason for Visit</label>
                            <textarea class="form-control" id="reason" name="reason" rows="3" placeholder="Brief description of the appointment purpose"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="notes" class="form-label">Additional Notes</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="Any additional information"></textarea>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-calendar-check"></i> Book Appointment
                            </button>
                            <a href="view_appointments.php" class="btn btn-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
