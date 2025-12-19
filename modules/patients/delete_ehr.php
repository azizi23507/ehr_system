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

// Get EHR ID from URL
if (!isset($_GET['id']) || empty($_GET['id'])) {
    header("Location: ehr_records.php");
    exit();
}

$ehr_id = (int)$_GET['id'];

// Get patient_id if provided (for redirect)
$patient_id = isset($_GET['patient_id']) ? (int)$_GET['patient_id'] : null;

// Verify EHR record belongs to this doctor and get file names
$stmt = $conn->prepare("SELECT xray_image, report_document, patient_id FROM ehr_records WHERE ehr_id = ? AND doctor_id = ?");
$stmt->bind_param("ii", $ehr_id, $doctor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    header("Location: ehr_records.php");
    exit();
}

$record = $result->fetch_assoc();
$stmt->close();

// Delete uploaded files if they exist
if ($record['xray_image'] && file_exists('../../uploads/documents/' . $record['xray_image'])) {
    unlink('../../uploads/documents/' . $record['xray_image']);
}
if ($record['report_document'] && file_exists('../../uploads/documents/' . $record['report_document'])) {
    unlink('../../uploads/documents/' . $record['report_document']);
}

// Delete EHR record
$stmt = $conn->prepare("DELETE FROM ehr_records WHERE ehr_id = ? AND doctor_id = ?");
$stmt->bind_param("ii", $ehr_id, $doctor_id);

if ($stmt->execute()) {
    // Redirect based on where we came from
    if ($patient_id) {
        header("Location: patient_ehr_records.php?patient_id=" . $patient_id . "&deleted=1");
    } else {
        header("Location: ehr_records.php?deleted=1");
    }
} else {
    // Redirect with error
    if ($patient_id) {
        header("Location: patient_ehr_records.php?patient_id=" . $patient_id . "&error=1");
    } else {
        header("Location: ehr_records.php?error=1");
    }
}

$stmt->close();
exit();
?>
