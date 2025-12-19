<?php
// Start session
session_start();

// Set page variables
$page_title = "Contact Us";
$base_url = "./";

// Initialize variables
$success_message = "";
$error_message = "";

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data and sanitize
    $name = htmlspecialchars(trim($_POST['name'] ?? ''));
    $email = htmlspecialchars(trim($_POST['email'] ?? ''));
    $subject = htmlspecialchars(trim($_POST['subject'] ?? ''));
    $message = htmlspecialchars(trim($_POST['message'] ?? ''));
    
    // Validate form data
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error_message = "All fields are required.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Please enter a valid email address.";
    } else {
        // In a real application, you would:
        // 1. Send email to admin
        // 2. Save message to database
        // 3. Send confirmation email to user
        
        // For now, just show success message
        $success_message = "Thank you for contacting us! We will get back to you soon.";
        
        // Clear form fields after successful submission
        $name = $email = $subject = $message = "";
    }
}
?>

<?php include 'includes/header.php'; ?>

<!-- Page Header -->
<section class="bg-primary text-white py-5">
    <div class="container">
        <h1 class="display-4 fw-bold"><i class="bi bi-envelope"></i> Contact Us</h1>
        <p class="lead">Get in touch with us for any questions or support</p>
    </div>
</section>

<!-- Contact Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Contact Form -->
            <div class="col-lg-7">
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h3 class="card-title mb-4">Send us a Message</h3>
                        
                        <!-- Success Message -->
                        <?php if (!empty($success_message)): ?>
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="bi bi-check-circle"></i> <?php echo $success_message; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Error Message -->
                        <?php if (!empty($error_message)): ?>
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="bi bi-exclamation-triangle"></i> <?php echo $error_message; ?>
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        <?php endif; ?>
                        
                        <!-- Contact Form -->
                        <form method="POST" action="">
                            <div class="row">
                                <!-- Name Field -->
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Full Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="name" name="name" 
                                           value="<?php echo htmlspecialchars($name ?? ''); ?>" 
                                           placeholder="Enter your name" required>
                                </div>
                                
                                <!-- Email Field -->
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" 
                                           value="<?php echo htmlspecialchars($email ?? ''); ?>" 
                                           placeholder="Enter your email" required>
                                </div>
                            </div>
                            
                            <!-- Subject Field -->
                            <div class="mb-3">
                                <label for="subject" class="form-label">Subject <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="subject" name="subject" 
                                       value="<?php echo htmlspecialchars($subject ?? ''); ?>" 
                                       placeholder="Enter subject" required>
                            </div>
                            
                            <!-- Message Field -->
                            <div class="mb-3">
                                <label for="message" class="form-label">Message <span class="text-danger">*</span></label>
                                <textarea class="form-control" id="message" name="message" rows="6" 
                                          placeholder="Enter your message" required><?php echo htmlspecialchars($message ?? ''); ?></textarea>
                            </div>
                            
                            <!-- Submit Button -->
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="bi bi-send"></i> Send Message
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Contact Information -->
            <div class="col-lg-5">
                <!-- Contact Info Card -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h3 class="card-title mb-4">Contact Information</h3>
                        
                        <div class="mb-4">
                            <h5><i class="bi bi-geo-alt text-primary"></i> Address</h5>
                            <p class="text-muted mb-0">
                                Deggendorf Institute of Technology<br>
                                Dieter-Görlitz-Platz 1<br>
                                94469 Deggendorf, Germany
                            </p>
                        </div>
                        
                        <div class="mb-4">
                            <h5><i class="bi bi-envelope text-primary"></i> Email</h5>
                            <p class="mb-0">
                                <a href="mailto:info@ehr-system.com" class="text-decoration-none">info@ehr-system.com</a><br>
                                <a href="mailto:support@ehr-system.com" class="text-decoration-none">support@ehr-system.com</a>
                            </p>
                        </div>
                        
                        <div class="mb-4">
                            <h5><i class="bi bi-phone text-primary"></i> Phone</h5>
                            <p class="text-muted mb-0">
                                +49 991 3615-0<br>
                                <small>Monday - Friday: 9:00 AM - 5:00 PM</small>
                            </p>
                        </div>
                        
                        <div>
                            <h5><i class="bi bi-clock text-primary"></i> Support Hours</h5>
                            <p class="text-muted mb-0">
                                Monday - Friday: 8:00 AM - 6:00 PM<br>
                                Saturday: 9:00 AM - 2:00 PM<br>
                                Sunday: Closed
                            </p>
                        </div>
                    </div>
                </div>
                
                <!-- FAQ Quick Links Card -->
                <div class="card shadow-sm">
                    <div class="card-body p-4">
                        <h3 class="card-title mb-3">Quick Links</h3>
                        <ul class="list-unstyled mb-0">
                            <li class="mb-2">
                                <a href="about.php" class="text-decoration-none">
                                    <i class="bi bi-arrow-right text-primary"></i> About EHR System
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="modules/auth/register.php" class="text-decoration-none">
                                    <i class="bi bi-arrow-right text-primary"></i> Register as Doctor
                                </a>
                            </li>
                            <li class="mb-2">
                                <a href="modules/auth/login.php" class="text-decoration-none">
                                    <i class="bi bi-arrow-right text-primary"></i> Login to Your Account
                                </a>
                            </li>
                            <li>
                                <a href="index.php" class="text-decoration-none">
                                    <i class="bi bi-arrow-right text-primary"></i> Back to Home
                                </a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map Section (Optional - you can add Google Maps embed if needed) -->
<section class="py-5 bg-light">
    <div class="container">
        <h3 class="text-center mb-4">Find Us</h3>
        <div class="ratio ratio-21x9">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d2658.8899676147415!2d12.960636315675447!3d48.82829897928588!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x479a1b9c8f8f8f8f%3A0x8f8f8f8f8f8f8f8f!2sDeggendorf%20Institute%20of%20Technology!5e0!3m2!1sen!2sde!4v1234567890123!5m2!1sen!2sde" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
