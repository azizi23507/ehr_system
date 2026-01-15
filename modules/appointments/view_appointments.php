<?php
// View Appointments Module
// Displays all appointments for the logged-in doctor
// Features: Filtering by status, statistics cards, status management
// Allows doctors to view, complete, or cancel appointments

global $conn;
session_start();

// Authentication check
if (!isset($_SESSION['doctor_id'])) {
    header("Location: ../auth/login.php?login_required=1");
    exit();
}

require_once '../../config/database.php';

$page_title = "My Appointments";
$base_url = "../../";

$doctor_id = $_SESSION['doctor_id'];

// Display success message from session if set
$success_message = "";
if (isset($_SESSION['success_message'])) {
    $success_message = $_SESSION['success_message'];
    unset($_SESSION['success_message']);
}

// Get filter parameter for appointment status
// Options: upcoming, completed, cancelled, all
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'upcoming';

// Query appointments based on selected filter
// Joins with patients table to get patient information
if ($filter == 'all') {
    $stmt = $conn->prepare("SELECT a.*, p.first_name, p.last_name, p.phone FROM appointments a JOIN patients p ON a.patient_id = p.patient_id WHERE a.doctor_id = ? ORDER BY a.appointment_date DESC, a.appointment_time DESC");
    $stmt->bind_param("i", $doctor_id);
} elseif ($filter == 'completed') {
    $stmt = $conn->prepare("SELECT a.*, p.first_name, p.last_name, p.phone FROM appointments a JOIN patients p ON a.patient_id = p.patient_id WHERE a.doctor_id = ? AND a.status = 'completed' ORDER BY a.appointment_date DESC, a.appointment_time DESC");
    $stmt->bind_param("i", $doctor_id);
} elseif ($filter == 'cancelled') {
    $stmt = $conn->prepare("SELECT a.*, p.first_name, p.last_name, p.phone FROM appointments a JOIN patients p ON a.patient_id = p.patient_id WHERE a.doctor_id = ? AND a.status = 'cancelled' ORDER BY a.appointment_date DESC, a.appointment_time DESC");
    $stmt->bind_param("i", $doctor_id);
} else {
    // Default: Show upcoming scheduled appointments
    // Filters by status 'scheduled' and future dates only
    $stmt = $conn->prepare("SELECT a.*, p.first_name, p.last_name, p.phone FROM appointments a JOIN patients p ON a.patient_id = p.patient_id WHERE a.doctor_id = ? AND a.status = 'scheduled' AND a.appointment_date >= CURDATE() ORDER BY a.appointment_date ASC, a.appointment_time ASC");
    $stmt->bind_param("i", $doctor_id);
}

$stmt->execute();
$appointments = $stmt->get_result();
$stmt->close();

// Calculate appointment statistics for dashboard cards
// Counts appointments by status for display
$stats_stmt = $conn->prepare("SELECT 
    COUNT(CASE WHEN status = 'scheduled' AND appointment_date >= CURDATE() THEN 1 END) as upcoming,
    COUNT(CASE WHEN status = 'completed' THEN 1 END) as completed,
    COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled
FROM appointments WHERE doctor_id = ?");
$stats_stmt->bind_param("i", $doctor_id);
$stats_stmt->execute();
$stats = $stats_stmt->get_result()->fetch_assoc();
$stats_stmt->close();
?>

<?php include '../../includes/header.php'; ?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-calendar3"></i> My Appointments</h2>
        <a href="book_appointment.php" class="btn btn-primary">
            <i class="bi bi-calendar-plus"></i> Book New Appointment
        </a>
    </div>
    
    <?php if ($success_message): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle"></i> <?php echo $success_message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">Upcoming</h5>
                    <h2 class="mb-0"><?php echo $stats['upcoming']; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">Completed</h5>
                    <h2 class="mb-0"><?php echo $stats['completed']; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h5 class="card-title">Cancelled</h5>
                    <h2 class="mb-0"><?php echo $stats['cancelled']; ?></h2>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card mb-4">
        <div class="card-body">
            <div class="btn-group" role="group">
                <a href="view_appointments.php?filter=upcoming" class="btn btn-<?php echo $filter == 'upcoming' ? 'primary' : 'outline-primary'; ?>">
                    Upcoming
                </a>
                <a href="view_appointments.php?filter=completed" class="btn btn-<?php echo $filter == 'completed' ? 'primary' : 'outline-primary'; ?>">
                    Completed
                </a>
                <a href="view_appointments.php?filter=cancelled" class="btn btn-<?php echo $filter == 'cancelled' ? 'primary' : 'outline-primary'; ?>">
                    Cancelled
                </a>
                <a href="view_appointments.php?filter=all" class="btn btn-<?php echo $filter == 'all' ? 'primary' : 'outline-primary'; ?>">
                    All
                </a>
            </div>
        </div>
    </div>
    
    <div class="card">
        <div class="card-body">
            <?php if ($appointments->num_rows > 0): ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-primary">
                            <tr>
                                <th>Patient</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Reason</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($appointment = $appointments->fetch_assoc()): ?>
                                <tr>
                                    <td>
                                        <strong><?php echo htmlspecialchars($appointment['first_name'] . ' ' . $appointment['last_name']); ?></strong><br>
                                        <small class="text-muted"><?php echo htmlspecialchars($appointment['phone']); ?></small>
                                    </td>
                                    <td><?php echo date('d/m/Y', strtotime($appointment['appointment_date'])); ?></td>
                                    <td><?php echo date('H:i', strtotime($appointment['appointment_time'])); ?></td>
                                    <td><?php echo htmlspecialchars(substr($appointment['reason'] ?: 'No reason provided', 0, 40)) . (strlen($appointment['reason']) > 40 ? '...' : ''); ?></td>
                                    <td>
                                        <?php
                                        $status_class = '';
                                        if ($appointment['status'] == 'scheduled') $status_class = 'bg-primary';
                                        elseif ($appointment['status'] == 'completed') $status_class = 'bg-success';
                                        else $status_class = 'bg-danger';
                                        ?>
                                        <span class="badge <?php echo $status_class; ?>"><?php echo ucfirst($appointment['status']); ?></span>
                                    </td>
                                    <td>
                                        <?php if ($appointment['status'] == 'scheduled'): ?>
                                            <a href="complete_appointment.php?id=<?php echo $appointment['appointment_id']; ?>" 
                                               class="btn btn-sm btn-success" title="Mark as Completed">
                                                <i class="bi bi-check-circle"></i>
                                            </a>
                                            <a href="cancel_appointment.php?id=<?php echo $appointment['appointment_id']; ?>" 
                                               class="btn btn-sm btn-danger" title="Cancel"
                                               onclick="return confirm('Are you sure you want to cancel this appointment?')">
                                                <i class="bi bi-x-circle"></i>
                                            </a>
                                        <?php else: ?>
                                            <span class="text-muted">No actions</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> No appointments found. 
                    <a href="book_appointment.php" class="alert-link">Book your first appointment</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php include '../../includes/footer.php'; ?>
