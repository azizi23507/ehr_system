# EHR Information System

## Project Overview

A comprehensive Electronic Health Records (EHR) Information System designed for healthcare professionals to manage patient records, appointments, and medical documentation. This system is built from scratch as part of the Information Systems in Health Care course (WS25/26).

## Technology Stack

### Frontend
- HTML5
- CSS3
- Bootstrap 5
- JavaScript (minimal library usage)
- Bootstrap Icons

### Backend
- PHP 8.x
- MySQL/MariaDB

### Email Service
- PHPMailer (Gmail SMTP integration)
- Environment-based configuration

## System Architecture

### Database Design
The system uses a relational database with five core tables:
- **doctors**: User authentication and profile management
- **patients**: Patient demographic information
- **ehr_records**: Comprehensive electronic health records
- **appointments**: Appointment scheduling and management
- **login_attempts**: Security monitoring and rate limiting

### Application Structure
```
ehr-system/
├── config/          Database, email, and session configuration
├── modules/         Core functionality modules
│   ├── auth/       Authentication and authorization
│   ├── patients/   Patient and EHR management
│   ├── appointments/ Appointment scheduling
│   ├── doctors/    Doctor profile management
│   └── dashboard/  Main dashboard interface
├── includes/       Reusable components (header, footer, navbar)
├── css/            Stylesheets
├── js/             JavaScript files
├── uploads/        User-uploaded files and documents
└── database/       SQL schema and migration files
```

## Feature Implementation

### 1. Static Pages and Navigation
The system includes a comprehensive navigation menu with the following static pages:
- Homepage with system overview and features
- About page with contextual information about EHR
- Contact page with communication form
- Login and registration interfaces
- Dashboard with quick access to main functions

### 2. Doctor Registration and Authentication

#### Registration Features
- Complete doctor profile creation with professional details
- Required fields: first name, last name, email, username, password, specialization, license number
- Optional profile image upload
- Email verification requirement before account activation
- Strong password validation:
  - Minimum 8 characters
  - At least one uppercase letter
  - At least one lowercase letter
  - At least one number
  - At least one special character

#### Login System
- Username/email and password authentication
- Email verification status check
- Session management with timeout (30 minutes of inactivity)
- Login attempt tracking for security
- Brute force protection (rate limiting after failed attempts)

#### Password Recovery
- Forgot password functionality with email-based reset
- Secure token generation for password reset links
- Token expiry after 1 hour
- Email notification with reset instructions

#### Email Verification
- Automated email sending upon registration
- Unique verification tokens for each user
- Account activation only after email verification
- Professional HTML email templates

### 3. Patient and EHR Management (CRUD Operations)

#### Patient Management
Each doctor can perform complete CRUD operations on their patients:

**Create**
- Add new patients with demographic information
- Required fields: name, date of birth, gender, contact information
- Optional fields: blood group, address, emergency contacts
- Profile image upload capability

**Read**
- View list of all patients associated with the logged-in doctor
- Search and filter functionality
- Detailed patient profile view
- Access to complete EHR history

**Update**
- Edit patient demographic information
- Update contact details and emergency contacts
- Modify profile images

**Delete**
- Remove patient records with cascade deletion of associated EHR records
- Confirmation dialogs for safety

#### EHR Records Management

**Required Input Types Implementation:**

1. **Text Input Fields (3+ required)**
   - Height (cm)
   - Weight (kg)
   - BMI (calculated or manual)
   - Blood pressure
   - Heart rate (beats per minute)
   - Temperature (Celsius)
   - Doctor's notes
   - Diagnosis
   - Treatment plan

2. **Textarea Data (1+ required)**
   - Medical history (comprehensive patient history)
   - Current medications (detailed medication list)
   - Lab results (laboratory test results)
   - Immunization details (vaccination information)
   - Allergy details (specific allergy information)
   - Doctor notes (detailed clinical observations)

3. **Checkbox Data (1+ required)**
   - Allergy categories (multiple selection):
     - Drug allergies
     - Food allergies
     - Environmental allergies
     - Other allergies

4. **Radio Button Data (1+ required)**
   - Gender selection (Male, Female, Other)
   - Immunization status (Up-to-date, Incomplete, Unknown)

5. **Date Input (1+ required)**
   - Date of birth (patient registration)
   - Visit date (EHR records)
   - Appointment date (scheduling)

6. **Image Upload and Display (1+ required)**
   - Patient profile pictures
   - X-ray images (medical imaging)
   - Medical report documents (PDF, images)
   - Doctor profile pictures
   - Image preview and display functionality
   - File type and size validation

**EHR CRUD Operations:**
- Create new health records with comprehensive data entry
- View complete medical history and records
- Update existing health records
- Delete outdated or incorrect records
- Attach and manage medical documents and images

### 4. Appointment Management System

Complete appointment scheduling functionality:
- Book new appointments with date and time selection
- View appointment calendar and list
- Update appointment details and status
- Cancel appointments
- Appointment status tracking (scheduled, completed, cancelled)
- Integration with patient and doctor records

### 5. System Usability Scale (SUS) Evaluation

Comprehensive usability evaluation implementation:
- 10-question SUS questionnaire
- Standard 5-point Likert scale (Strongly Disagree to Strongly Agree)
- Automatic SUS score calculation (0-100 scale)
- Grade interpretation (A-F grading system)
- Acceptability range classification (Not Acceptable, Marginal, Acceptable)
- Data collection and storage (JSON and CSV formats)
- Results dashboard with statistics and analysis
- Export functionality for data analysis

## Security Features

### Authentication and Authorization
- Bcrypt password hashing (PASSWORD_DEFAULT algorithm)
- Email verification before account activation
- Session-based authentication with secure cookies
- Session timeout after 30 minutes of inactivity
- Login attempt tracking and rate limiting
- Password reset with time-limited tokens (1-hour expiry)

### Data Protection
- Environment-based configuration for sensitive credentials
- .gitignore protection for sensitive files
- SQL injection prevention through prepared statements
- XSS prevention through output escaping
- File upload validation (type and size restrictions)
- CSRF protection considerations

### Access Control
- Doctor-specific data isolation (each doctor sees only their patients)
- Role-based access control
- Protected routes requiring authentication
- Session validation on sensitive operations

## Data Model Requirements Fulfillment

### Form Elements Implementation
The system implements all required form elements as specified:

1. **Input Fields**: Multiple text inputs for patient demographics, vital signs, and medical data
2. **Textarea**: Medical history, medications, lab results, doctor notes
3. **Checkboxes**: Allergy categories with multiple selection capability
4. **Radio Buttons**: Gender selection, immunization status
5. **Date Picker**: Date of birth, visit dates, appointment scheduling
6. **Image Upload**: Profile pictures, X-rays, medical documents with preview functionality

### Database Schema
Comprehensive relational database design with:
- Primary keys and auto-increment functionality
- Foreign key relationships with referential integrity
- Cascade deletion for data consistency
- Timestamp tracking (created_at, updated_at)
- Proper indexing for query optimization
- UTF-8 character set support for international characters

## Demo Data

The system includes realistic demo data for testing:
- Pre-configured test doctor account (username: johndoe, password: Doctor@123)
- Five sample patients with diverse medical profiles
- Multiple EHR records demonstrating various medical conditions:
  - Hypertension management
  - Asthma treatment
  - Type 2 Diabetes monitoring
  - Migraine management
  - General health checkups
- Sample appointments in various states

## Additional Features

### Dashboard
- Quick statistics overview (total patients, recent records, upcoming appointments)
- Recent patient list with quick access
- Upcoming appointments calendar
- Quick action buttons for common tasks

### Email Notifications
- Registration confirmation emails
- Email verification with secure tokens
- Password reset instructions
- Professional HTML email templates
- SMTP integration with error handling

### User Interface
- Responsive design using Bootstrap 5
- Mobile-friendly layouts
- Intuitive navigation structure
- Icon-based visual indicators
- Color-coded status displays
- Loading indicators for asynchronous operations

### File Management
- Secure file upload system
- File type validation (images, PDFs)
- File size restrictions (5MB limit)
- Organized storage structure
- Direct file access prevention
- Image preview functionality

## Development Approach

The system was developed from scratch without using pre-built templates or CMS platforms. All code is hand-written by team members following modern web development practices and security standards.

### Code Quality
- Modular architecture for maintainability
- Separation of concerns (presentation, business logic, data access)
- Reusable components and functions
- Consistent coding style
- Comprehensive inline documentation
- Error handling and logging

### Standards Compliance
- W3C HTML5 validation
- CSS3 best practices
- Bootstrap 5 framework usage
- PHP 8.x compatibility
- MySQL 5.7+ compatibility
- Secure coding practices

## Future Enhancement Possibilities

While the current system meets all project requirements, potential future enhancements could include:
- Multi-language support
- Advanced search and filtering capabilities
- Report generation (PDF exports)
- Data visualization and analytics
- Patient portal for self-service
- Telemedicine integration
- Prescription management
- Lab result integration
- Billing and insurance modules
- Mobile application

## Project Compliance

This project fulfills all specified requirements:
- Frontend: HTML5, CSS3, Bootstrap 5, minimal JavaScript
- Backend: PHP + MySQL
- Navigation menu with static pages
- Complete registration and login system with email verification
- Strong password requirements and forgot password functionality
- Doctor-specific CRUD operations for patients and EHR records
- All required input types: 3+ text inputs, 1 checkbox, 1 radio, 1 date, 1 textarea, 1 image upload
- SUS evaluation system implementation
- Comprehensive documentation and user manual
- Professional presentation materials

## License

This project is developed for academic purposes as part of the Information Systems in Health Care course at Technische Hochschule Deggendorf.
