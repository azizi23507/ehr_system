-- ============================================
-- EHR INFORMATION SYSTEM DATABASE SCHEMA
-- ============================================
-- This file creates all necessary tables for the EHR system
-- Run this file in phpMyAdmin or MySQL command line to setup the database

-- Create database
CREATE DATABASE IF NOT EXISTS ehr_system;
USE ehr_system;

-- ============================================
-- TABLE: doctors
-- Purpose: Store doctor registration and login information
-- ============================================
CREATE TABLE IF NOT EXISTS doctors (
    doctor_id INT AUTO_INCREMENT PRIMARY KEY,
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,  -- Will store hashed password
    phone VARCHAR(20),
    specialization VARCHAR(100),
    license_number VARCHAR(50),
    profile_image VARCHAR(255),  -- Profile picture
    is_verified TINYINT(1) DEFAULT 0,  -- Email verification status (0 = not verified, 1 = verified)
    verification_token VARCHAR(100),    -- Token for email verification
    reset_token VARCHAR(100),           -- Token for password reset
    reset_token_expiry DATETIME,        -- Expiry time for reset token
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABLE: patients
-- Purpose: Store basic patient information
-- ============================================
CREATE TABLE IF NOT EXISTS patients (
    patient_id INT AUTO_INCREMENT PRIMARY KEY,
    doctor_id INT NOT NULL,  -- Links patient to their doctor
    first_name VARCHAR(50) NOT NULL,
    last_name VARCHAR(50) NOT NULL,
    date_of_birth DATE NOT NULL,
    gender ENUM('Male', 'Female', 'Other') NOT NULL,  -- Radio button data
    blood_group VARCHAR(5),
    phone VARCHAR(20),
    email VARCHAR(100),
    address TEXT,
    emergency_contact_name VARCHAR(100),
    emergency_contact_phone VARCHAR(20),
    profile_image VARCHAR(255),  -- Image upload function
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (doctor_id) REFERENCES doctors(doctor_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABLE: ehr_records
-- Purpose: Store Electronic Health Records for patients
-- ============================================
CREATE TABLE IF NOT EXISTS ehr_records (
    ehr_id INT AUTO_INCREMENT PRIMARY KEY,
    patient_id INT NOT NULL,
    doctor_id INT NOT NULL,  -- Doctor who created/owns this record
    
    -- Demographics (text inputs - requirement: at least 3 input data)
    height DECIMAL(5,2),  -- in cm
    weight DECIMAL(5,2),  -- in kg
    bmi DECIMAL(4,2),     -- calculated or input
    
    -- Medical History (textarea - requirement: 1 textarea data)
    medical_history TEXT,
    
    -- Current Medications (textarea)
    current_medications TEXT,
    
    -- Allergies (checkbox data - requirement: 1 checkbox data)
    allergy_drugs TINYINT(1) DEFAULT 0,
    allergy_food TINYINT(1) DEFAULT 0,
    allergy_environmental TINYINT(1) DEFAULT 0,
    allergy_other TINYINT(1) DEFAULT 0,
    allergy_details TEXT,
    
    -- Immunization Status (radio data - requirement: 1 radio data)
    immunization_status ENUM('Up-to-date', 'Incomplete', 'Unknown') DEFAULT 'Unknown',
    immunization_details TEXT,
    
    -- Vital Signs
    blood_pressure VARCHAR(20),  -- e.g., "120/80"
    heart_rate INT,              -- beats per minute
    temperature DECIMAL(4,2),    -- in Celsius
    
    -- Lab Results
    lab_results TEXT,
    
    -- Visit/Record Date (date input - requirement: 1 input data for date)
    visit_date DATE NOT NULL,
    
    -- Additional Notes
    diagnosis TEXT,
    treatment_plan TEXT,
    doctor_notes TEXT,
    
    -- Images/Documents (image upload - requirement: 1 image upload and display)
    xray_image VARCHAR(255),
    report_document VARCHAR(255),
    
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    FOREIGN KEY (patient_id) REFERENCES patients(patient_id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES doctors(doctor_id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- TABLE: login_attempts
-- Purpose: Track failed login attempts for security
-- ============================================
CREATE TABLE IF NOT EXISTS login_attempts (
    attempt_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    attempt_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    success TINYINT(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- INDEXES for better performance
-- ============================================
CREATE INDEX idx_doctor_email ON doctors(email);
CREATE INDEX idx_doctor_username ON doctors(username);
CREATE INDEX idx_patient_doctor ON patients(doctor_id);
CREATE INDEX idx_ehr_patient ON ehr_records(patient_id);
CREATE INDEX idx_ehr_doctor ON ehr_records(doctor_id);
CREATE INDEX idx_login_username ON login_attempts(username);

-- ============================================
-- Insert a test doctor (password: Doctor@123)
-- Password is hashed using PHP password_hash()
-- ============================================
INSERT INTO doctors (first_name, last_name, email, username, password, phone, specialization, license_number, profile_image, is_verified) 
VALUES ('John', 'Doe', 'john.doe@hospital.com', 'johndoe', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', '+1234567890', 'General Physician', 'DOC12345', 'doctor_johndoe.jpg', 1);

-- ============================================
-- DEMO DATA: Sample Patients
-- ============================================
-- Adding 5 demo patients for the test doctor (doctor_id = 1)

INSERT INTO patients (doctor_id, first_name, last_name, date_of_birth, gender, blood_group, phone, email, address, emergency_contact_name, emergency_contact_phone) 
VALUES 
(1, 'Sarah', 'Johnson', '1985-03-15', 'Female', 'A+', '+49 176 12345678', 'sarah.johnson@email.com', 'Hauptstrasse 10, 94469 Deggendorf', 'Michael Johnson', '+49 176 87654321'),
(1, 'Michael', 'Smith', '1978-07-22', 'Male', 'O+', '+49 151 23456789', 'michael.smith@email.com', 'Bahnhofstrasse 5, 94469 Deggendorf', 'Emma Smith', '+49 151 98765432'),
(1, 'Emma', 'Brown', '1992-11-08', 'Female', 'B+', '+49 162 34567890', 'emma.brown@email.com', 'Donaustrasse 20, 94469 Deggendorf', 'Robert Brown', '+49 162 09876543'),
(1, 'Robert', 'Davis', '1965-05-30', 'Male', 'AB-', '+49 171 45678901', 'robert.davis@email.com', 'Graflingerstrasse 15, 94469 Deggendorf', 'Linda Davis', '+49 171 10987654'),
(1, 'Linda', 'Wilson', '1980-09-12', 'Female', 'O-', '+49 160 56789012', 'linda.wilson@email.com', 'Edlmairstrasse 8, 94469 Deggendorf', 'James Wilson', '+49 160 21098765');

-- ============================================
-- DEMO DATA: Sample EHR Records
-- ============================================
-- Adding sample EHR records for demo patients

-- EHR Record for Sarah Johnson (patient_id = 1)
INSERT INTO ehr_records (patient_id, doctor_id, height, weight, bmi, blood_pressure, heart_rate, temperature, medical_history, current_medications, allergy_drugs, allergy_food, allergy_environmental, allergy_other, allergy_details, immunization_status, immunization_details, lab_results, diagnosis, treatment_plan, doctor_notes, visit_date, xray_image) 
VALUES 
(1, 1, 165.0, 62.0, 22.77, '120/80', 72, 36.6, 'No significant medical history. Regular checkups.', 'Multivitamin daily', 0, 1, 0, 0, 'Mild lactose intolerance', 'Up-to-date', 'COVID-19 vaccine (2 doses), Annual flu shot', 'Blood test - All normal ranges', 'Annual health checkup - Good overall health', 'Continue current lifestyle. Next checkup in 6 months.', 'Patient is healthy and active. No concerns.', '2024-11-15', 'xray_chest_sarah.jpg'),

(1, 1, 165.0, 63.5, 23.32, '118/78', 70, 36.5, 'Previous visit: No significant issues', 'Multivitamin', 0, 1, 0, 0, 'Lactose intolerance', 'Up-to-date', 'All vaccinations current', 'Complete blood count - Normal', 'Routine followup - Excellent health', 'Maintain healthy diet and exercise routine', 'Patient continues to maintain good health', '2024-12-10', NULL);

-- EHR Record for Michael Smith (patient_id = 2)
INSERT INTO ehr_records (patient_id, doctor_id, height, weight, bmi, blood_pressure, heart_rate, temperature, medical_history, current_medications, allergy_drugs, allergy_food, allergy_environmental, allergy_other, allergy_details, immunization_status, immunization_details, lab_results, diagnosis, treatment_plan, doctor_notes, visit_date, xray_image) 
VALUES 
(2, 1, 178.0, 85.0, 26.83, '130/85', 78, 36.8, 'Hypertension diagnosed 2020. Family history of heart disease.', 'Lisinopril 10mg daily, Aspirin 81mg daily', 1, 0, 0, 0, 'Allergic to Penicillin', 'Up-to-date', 'Flu vaccine 2024, Tetanus booster 2022', 'Cholesterol: 220 mg/dL (slightly elevated), Blood sugar: Normal', 'Hypertension - controlled with medication', 'Continue current medications. Diet modification - reduce salt intake. Exercise 30min daily.', 'Blood pressure well controlled. Advised weight loss.', '2024-11-20', 'xray_chest_michael.jpg'),

(2, 1, 178.0, 83.0, 26.20, '125/82', 75, 36.7, 'Hypertension under control', 'Lisinopril 10mg, Aspirin 81mg', 1, 0, 0, 0, 'Penicillin allergy', 'Up-to-date', 'Current', 'Cholesterol improved: 195 mg/dL', 'Hypertension - improving', 'Continue medications. Good progress on diet', 'Patient showing improvement', '2024-12-15', NULL);

-- EHR Record for Emma Brown (patient_id = 3)
INSERT INTO ehr_records (patient_id, doctor_id, height, weight, bmi, blood_pressure, heart_rate, temperature, medical_history, current_medications, allergy_drugs, allergy_food, allergy_environmental, allergy_other, allergy_details, immunization_status, immunization_details, lab_results, diagnosis, treatment_plan, doctor_notes, visit_date, xray_image) 
VALUES 
(3, 1, 160.0, 55.0, 21.48, '115/75', 68, 36.4, 'Asthma since childhood. Well controlled with medication.', 'Albuterol inhaler as needed, Fluticasone inhaler twice daily', 0, 0, 1, 0, 'Pollen, dust mites', 'Up-to-date', 'All childhood vaccinations complete, COVID-19 vaccinated', 'Pulmonary function test - Normal ranges', 'Asthma - well controlled', 'Continue current inhaler regimen. Avoid known allergens. Emergency inhaler always available.', 'Asthma management excellent. Patient compliant with treatment.', '2024-11-25', 'xray_chest_emma.jpg');

-- EHR Record for Robert Davis (patient_id = 4)
INSERT INTO ehr_records (patient_id, doctor_id, height, weight, bmi, blood_pressure, heart_rate, temperature, medical_history, current_medications, allergy_drugs, allergy_food, allergy_environmental, allergy_other, allergy_details, immunization_status, immunization_details, lab_results, diagnosis, treatment_plan, doctor_notes, visit_date, xray_image) 
VALUES 
(4, 1, 175.0, 92.0, 30.04, '140/90', 82, 37.0, 'Type 2 Diabetes diagnosed 2018. Knee surgery 2015.', 'Metformin 500mg twice daily, Atorvastatin 20mg daily', 0, 1, 0, 0, 'Shellfish allergy', 'Incomplete', 'Missing pneumonia vaccine - scheduled', 'HbA1c: 7.2% (fair control), LDL: 110 mg/dL', 'Type 2 Diabetes Mellitus, Hyperlipidemia', 'Adjust Metformin to 1000mg twice daily. Continue statin. Diet counseling - carbohydrate control. Monitor blood sugar daily.', 'Diabetes control needs improvement. Discussed lifestyle modifications.', '2024-12-01', 'xray_knee_robert.jpg'),

(4, 1, 175.0, 90.0, 29.39, '135/88', 80, 36.8, 'Diabetes, previous knee surgery', 'Metformin 1000mg twice daily, Atorvastatin 20mg', 0, 1, 0, 0, 'Shellfish', 'Up-to-date', 'Pneumonia vaccine administered', 'HbA1c: 6.8% (improved)', 'Type 2 Diabetes - improved control', 'Continue current regimen', 'Good progress, patient motivated', '2024-12-18', NULL);

-- EHR Record for Linda Wilson (patient_id = 5)
INSERT INTO ehr_records (patient_id, doctor_id, height, weight, bmi, blood_pressure, heart_rate, temperature, medical_history, current_medications, allergy_drugs, allergy_food, allergy_environmental, allergy_other, allergy_details, immunization_status, immunization_details, lab_results, diagnosis, treatment_plan, doctor_notes, visit_date, xray_image) 
VALUES 
(5, 1, 168.0, 58.0, 20.55, '110/70', 65, 36.3, 'Migraine headaches - occasional. No other significant history.', 'Ibuprofen 400mg as needed for headaches', 1, 0, 0, 0, 'Aspirin causes stomach upset', 'Up-to-date', 'All vaccinations current including HPV series', 'Complete metabolic panel - All normal', 'Migraine headaches - episodic', 'Continue Ibuprofen as needed. Keep headache diary. Identify triggers. Adequate sleep and hydration.', 'Migraines occur 2-3 times per month. Manageable with current treatment.', '2024-12-05', 'mri_brain_linda.jpg');

-- ============================================
-- END OF DATABASE SCHEMA AND DEMO DATA
-- ============================================
