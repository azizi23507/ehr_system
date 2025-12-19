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

// Get doctor ID
$doctor_id = $_SESSION['doctor_id'];

// Get patient ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: view_patients.php");
    exit();
}

$patient_id = (int)$_GET['id'];

// Verify patient belongs to this doctor
$stmt = $conn->prepare("SELECT patient_id, profile_image FROM patients WHERE patient_id = ? AND doctor_id = ?");
$stmt->bind_param("ii", $patient_id, $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: view_patients.php");
    exit();
}

$patient = $result->fetch_assoc();
$stmt->close();

// Delete patient and all related records (CASCADE will handle ehr_records)
$stmt = $conn->prepare("DELETE FROM patients WHERE patient_id = ? AND doctor_id = ?");
$stmt->bind_param("ii", $patient_id, $doctor_id);

if ($stmt->execute()) {
    // Delete profile image if exists
    if ($patient['profile_image'] && file_exists('../../uploads/profile_pics/' . $patient['profile_image'])) {
        unlink('../../uploads/profile_pics/' . $patient['profile_image']);
    }
    
    // Redirect with success message
    header("Location: view_patients.php?deleted=1");
} else {
    // Redirect with error message
    header("Location: view_patients.php?error=1");
}

$stmt->close();
exit();
?>
