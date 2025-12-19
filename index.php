<?php
// Start session to check if user is logged in
session_start();

// Set page variables
$page_title = "Home";
$base_url = "./";

// Check if user is logged in
$is_logged_in = isset($_SESSION['doctor_id']) && !empty($_SESSION['doctor_id']);
?>

<?php include 'includes/header.php'; ?>

<!-- Hero Section -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="display-4 fw-bold">Electronic Health Records System</h1>
                <p class="lead">Manage patient records efficiently and securely. A comprehensive EHR solution for healthcare professionals.</p>
                
                <?php if($is_logged_in): ?>
                    <!-- If logged in, show Dashboard button -->
                    <a href="modules/dashboard/dashboard.php" class="btn btn-light btn-lg me-2">
                        <i class="bi bi-speedometer2"></i> Go to Dashboard
                    </a>
                    <a href="modules/patients/view_patients.php" class="btn btn-outline-light btn-lg">
                        <i class="bi bi-people"></i> View Patients
                    </a>
                <?php else: ?>
                    <!-- If not logged in, show Login and Register buttons -->
                    <a href="modules/auth/login.php" class="btn btn-light btn-lg me-2">
                        <i class="bi bi-box-arrow-in-right"></i> Login
                    </a>
                    <a href="modules/auth/register.php" class="btn btn-outline-light btn-lg">
                        <i class="bi bi-person-plus"></i> Register as Doctor
                    </a>
                <?php endif; ?>
            </div>
            <div class="col-lg-6 text-center">
                <i class="bi bi-hospital" style="font-size: 15rem; color: rgba(255, 255, 255, 0.3);"></i>
            </div>
        </div>
    </div>
</section>

<!-- Features Section -->
<section class="py-5">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">Why Choose Our EHR System?</h2>
            <p class="text-muted">Comprehensive features designed for modern healthcare professionals</p>
        </div>
        
        <div class="row g-4">
            <!-- Feature 1: Patient Management -->
            <div class="col-md-4">
                <div class="feature-box">
                    <i class="bi bi-people-fill"></i>
                    <h3>Patient Management</h3>
                    <p>Easily manage patient demographics, contact information, and medical history in one centralized location.</p>
                </div>
            </div>
            
            <!-- Feature 2: Electronic Health Records -->
            <div class="col-md-4">
                <div class="feature-box">
                    <i class="bi bi-file-medical-fill"></i>
                    <h3>Complete EHR</h3>
                    <p>Store comprehensive health records including diagnoses, medications, allergies, lab results, and vital signs.</p>
                </div>
            </div>
            
            <!-- Feature 3: Secure & Private -->
            <div class="col-md-4">
                <div class="feature-box">
                    <i class="bi bi-shield-lock-fill"></i>
                    <h3>Secure & Private</h3>
                    <p>Advanced security features ensure patient data is protected with encryption and secure authentication.</p>
                </div>
            </div>
            
            <!-- Feature 4: Easy Access -->
            <div class="col-md-4">
                <div class="feature-box">
                    <i class="bi bi-laptop-fill"></i>
                    <h3>Easy Access</h3>
                    <p>Access patient records from anywhere, anytime with our responsive web-based platform.</p>
                </div>
            </div>
            
            <!-- Feature 5: Document Management -->
            <div class="col-md-4">
                <div class="feature-box">
                    <i class="bi bi-folder-fill"></i>
                    <h3>Document Management</h3>
                    <p>Upload and manage medical documents, X-rays, lab reports, and other important files.</p>
                </div>
            </div>
            
            <!-- Feature 6: Quick Search -->
            <div class="col-md-4">
                <div class="feature-box">
                    <i class="bi bi-search"></i>
                    <h3>Quick Search</h3>
                    <p>Find patient records instantly with powerful search and filtering capabilities.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- How It Works Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="fw-bold">How It Works</h2>
            <p class="text-muted">Get started in three simple steps</p>
        </div>
        
        <div class="row g-4">
            <!-- Step 1 -->
            <div class="col-md-4">
                <div class="text-center">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                        1
                    </div>
                    <h4>Register</h4>
                    <p class="text-muted">Create your doctor account with your professional credentials and verify your email.</p>
                </div>
            </div>
            
            <!-- Step 2 -->
            <div class="col-md-4">
                <div class="text-center">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                        2
                    </div>
                    <h4>Add Patients</h4>
                    <p class="text-muted">Add your patients' information and start building their electronic health records.</p>
                </div>
            </div>
            
            <!-- Step 3 -->
            <div class="col-md-4">
                <div class="text-center">
                    <div class="bg-primary text-white rounded-circle d-inline-flex align-items-center justify-content-center mb-3" style="width: 80px; height: 80px; font-size: 2rem;">
                        3
                    </div>
                    <h4>Manage Records</h4>
                    <p class="text-muted">Create, view, update, and manage patient health records with complete CRUD functionality.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section -->
<section class="py-5">
    <div class="container">
        <div class="row text-center">
            <div class="col-md-3">
                <h2 class="fw-bold text-primary">100%</h2>
                <p class="text-muted">Secure</p>
            </div>
            <div class="col-md-3">
                <h2 class="fw-bold text-primary">24/7</h2>
                <p class="text-muted">Access</p>
            </div>
            <div class="col-md-3">
                <h2 class="fw-bold text-primary">Fast</h2>
                <p class="text-muted">Performance</p>
            </div>
            <div class="col-md-3">
                <h2 class="fw-bold text-primary">Easy</h2>
                <p class="text-muted">To Use</p>
            </div>
        </div>
    </div>
</section>

<!-- Call to Action Section -->
<?php if(!$is_logged_in): ?>
<section class="py-5 bg-primary text-white">
    <div class="container text-center">
        <h2 class="fw-bold mb-3">Ready to Get Started?</h2>
        <p class="lead mb-4">Join healthcare professionals using our EHR system to manage patient records efficiently.</p>
        <a href="modules/auth/register.php" class="btn btn-light btn-lg me-2">
            <i class="bi bi-person-plus"></i> Register Now
        </a>
        <a href="about.php" class="btn btn-outline-light btn-lg">
            <i class="bi bi-info-circle"></i> Learn More
        </a>
    </div>
</section>
<?php endif; ?>

<?php include 'includes/footer.php'; ?>
