<?php
// Cancel Appointment Handler
// Allows doctors to cancel scheduled appointments
// Updates appointment status to 'cancelled' in database
// Includes security check to ensure doctor owns the appointment

global $conn;
session_start();

// Authentication check
if (!isset($_SESSION['doctor_id'])) {
    header("Location: ../auth/login.php?login_required=1");
    exit();
}

require_once '../../config/database.php';

$doctor_id = $_SESSION['doctor_id'];

// Validate appointment ID parameter
if (!isset($_GET['id'])) {
    header("Location: view_appointments.php");
    exit();
}

$appointment_id = (int)$_GET['id'];

// Verify appointment belongs to this doctor before allowing cancellation
// Security measure to prevent unauthorized cancellations
$verify_stmt = $conn->prepare("SELECT appointment_id FROM appointments WHERE appointment_id = ? AND doctor_id = ?");
$verify_stmt->bind_param("ii", $appointment_id, $doctor_id);
$verify_stmt->execute();
$verify_result = $verify_stmt->get_result();

if ($verify_result->num_rows == 0) {
    $_SESSION['error_message'] = "Appointment not found or access denied.";
    header("Location: view_appointments.php");
    exit();
}
$verify_stmt->close();

// Update appointment status to cancelled
$stmt = $conn->prepare("UPDATE appointments SET status = 'cancelled' WHERE appointment_id = ? AND doctor_id = ?");
$stmt->bind_param("ii", $appointment_id, $doctor_id);

if ($stmt->execute()) {
    $_SESSION['success_message'] = "Appointment cancelled successfully.";
} else {
    $_SESSION['error_message'] = "Error cancelling appointment.";
}

$stmt->close();
header("Location: view_appointments.php");
exit();
?>
