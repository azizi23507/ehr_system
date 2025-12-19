# DATABASE SCHEMA EXPLANATION

## Overview
This database is designed for an Electronic Health Records (EHR) system from a doctor's perspective. Each doctor can only access their own patients' records.

---

## TABLES EXPLAINED

### 1. **doctors** table
**Purpose**: Stores all doctor accounts and authentication information

**Key Fields**:
- `doctor_id`: Unique ID for each doctor (auto-generated)
- `email` & `username`: For login (both must be unique)
- `password`: Stores hashed password (NOT plain text for security)
- `is_verified`: Checks if doctor verified their email (0=no, 1=yes)
- `verification_token`: Random code sent to email for verification
- `reset_token`: Code for password reset functionality
- `specialization`: Doctor's medical specialty (e.g., Cardiologist)

**Why we need this**: 
- Handles doctor registration and login
- Implements email verification requirement
- Supports "forgot password" feature

---

### 2. **patients** table
**Purpose**: Stores basic information about patients

**Key Fields**:
- `patient_id`: Unique ID for each patient
- `doctor_id`: Links patient to their doctor (FOREIGN KEY)
- `gender`: Uses ENUM (Male/Female/Other) - this is our **RADIO BUTTON requirement**
- `profile_image`: Path to uploaded profile picture - **IMAGE UPLOAD requirement**
- `address`: Patient's full address

**Why we need this**:
- Each doctor has their own list of patients
- When doctor logs in, they only see THEIR patients (filtered by doctor_id)
- Contains demographic information required by project

**Important**: `doctor_id` is a FOREIGN KEY - if a doctor is deleted, all their patients are also deleted (CASCADE)

---

### 3. **ehr_records** table
**Purpose**: Main table storing Electronic Health Records

**This table fulfills ALL project requirements**:

✅ **At least 3 input data**: height, weight, bmi, blood_pressure, heart_rate, temperature
✅ **1 checkbox data**: Allergies (allergy_drugs, allergy_food, allergy_environmental, allergy_other)
✅ **1 radio data**: immunization_status (Up-to-date, Incomplete, Unknown)
✅ **1 date input**: visit_date
✅ **1 textarea data**: medical_history, current_medications, lab_results, diagnosis, treatment_plan, doctor_notes
✅ **1 image upload**: xray_image, report_document

**Key Fields Explained**:

**Demographics Section**:
- `height`, `weight`, `bmi`: Physical measurements (text inputs)

**Medical History**:
- `medical_history`: Past illnesses, surgeries, conditions (textarea)
- `current_medications`: List of medicines patient is taking (textarea)

**Allergies (Checkboxes)**:
- Each allergy type is a separate checkbox (0=no, 1=yes)
- `allergy_details`: Additional text about allergies

**Immunization (Radio Button)**:
- Doctor selects ONE option: Up-to-date, Incomplete, or Unknown

**Vital Signs**:
- `blood_pressure`: e.g., "120/80"
- `heart_rate`: Beats per minute
- `temperature`: Body temperature in Celsius

**Date Field**:
- `visit_date`: When this record was created (uses datepicker in frontend)

**Images/Documents**:
- `xray_image`: Stores filename of uploaded X-ray image
- `report_document`: Can store lab report images

**Why we need this**:
- This is the core CRUD functionality
- Doctor can Create, Read, Update, Delete these records
- Includes all required input types for the project

---

### 4. **login_attempts** table
**Purpose**: Security - tracks login attempts to prevent brute force attacks

**Key Fields**:
- `username`: Who tried to login
- `ip_address`: From where
- `attempt_time`: When
- `success`: Was login successful? (0=failed, 1=success)

**Why we need this**:
- If someone tries wrong password 5 times, we can block them temporarily
- Good security practice for healthcare systems

---

## RELATIONSHIPS (How tables connect)

```
doctors (1) -----> (Many) patients
   |
   |
   +-------------> (Many) ehr_records

patients (1) -----> (Many) ehr_records
```

**Explanation**:
- One doctor can have MANY patients
- One patient can have MANY EHR records (multiple visits)
- One doctor creates MANY EHR records (for different patients)

**Important Security Feature**:
When doctor logs in, we filter by `doctor_id`:
- Show only patients where `patient_id IN (SELECT patient_id FROM patients WHERE doctor_id = logged_in_doctor_id)`
- Show only EHR records where `doctor_id = logged_in_doctor_id`

---

## INDEXES (Performance Optimization)

We created indexes on:
- Email and username lookups (for login)
- Doctor-patient relationships
- Patient-EHR relationships

**Why**: Makes searching MUCH faster when we have thousands of records

---

## TEST DATA

We inserted one test doctor:
- **Username**: johndoe
- **Password**: Doctor@123 (hashed in database)
- **Email**: john.doe@hospital.com

You can use this to test login after we build the system!

---

## SECURITY NOTES

1. **Passwords are HASHED**: Never store plain text passwords
2. **Email verification**: Doctors must verify email before full access
3. **Password reset**: Secure token-based system
4. **Foreign Keys**: Maintain data integrity
5. **CASCADE DELETE**: When doctor deleted, their data is removed

---

## HOW TO USE THIS FILE

1. Open **phpMyAdmin** (WAMP/XAMPP: http://localhost/phpmyadmin)
2. Click "SQL" tab
3. Copy and paste the entire `ehr_database.sql` file
4. Click "Go"
5. Database `ehr_system` will be created with all tables!

---

## NEXT STEPS

After creating the database:
1. Create config file to connect PHP to this database
2. Build registration page (inserts into `doctors` table)
3. Build login page (checks `doctors` table)
4. Build patient management (CRUD on `patients` table)
5. Build EHR management (CRUD on `ehr_records` table)
