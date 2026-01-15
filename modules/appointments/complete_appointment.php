<?php
// Complete Appointment Handler
// Marks scheduled appointments as completed
// Updates appointment status from 'scheduled' to 'completed'
// Includes authorization check for security

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

// Verify appointment belongs to this doctor before allowing modification
// Prevents doctors from modifying other doctors' appointments
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

// Update appointment status to completed
$stmt = $conn->prepare("UPDATE appointments SET status = 'completed' WHERE appointment_id = ? AND doctor_id = ?");
$stmt->bind_param("ii", $appointment_id, $doctor_id);

if ($stmt->execute()) {
    $_SESSION['success_message'] = "Appointment marked as completed.";
} else {
    $_SESSION['error_message'] = "Error updating appointment.";
}

$stmt->close();
header("Location: view_appointments.php");
exit();
?>
