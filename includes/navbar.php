<?php
// Start session if not already started (for checking login status)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
$is_logged_in = isset($_SESSION['doctor_id']) && !empty($_SESSION['doctor_id']);
$doctor_name = $is_logged_in ? $_SESSION['doctor_name'] : 'Guest';
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary sticky-top">
    <div class="container-fluid">
        <!-- Brand/Logo -->
        <a class="navbar-brand" href="<?php echo $base_url; ?>index.php">
            <i class="bi bi-heart-pulse-fill"></i> EHR System
        </a>
        
        <!-- Mobile Toggle Button -->
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <!-- Navigation Menu -->
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <!-- Home Link -->
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>" href="<?php echo $base_url; ?>index.php">
                        <i class="bi bi-house-door"></i> Home
                    </a>
                </li>
                
                <!-- About EHR Link -->
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'about.php') ? 'active' : ''; ?>" href="<?php echo $base_url; ?>about.php">
                        <i class="bi bi-info-circle"></i> About EHR
                    </a>
                </li>
                
                <?php if($is_logged_in): ?>
                    <!-- Dashboard - Only show if logged in -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'dashboard') !== false) ? 'active' : ''; ?>" href="<?php echo $base_url; ?>modules/dashboard/dashboard.php">
                            <i class="bi bi-speedometer2"></i> Dashboard
                        </a>
                    </li>
                    
                    <!-- Patients - Only show if logged in -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'patients') !== false) ? 'active' : ''; ?>" href="<?php echo $base_url; ?>modules/patients/view_patients.php">
                            <i class="bi bi-people"></i> Patients
                        </a>
                    </li>
                    
                    <!-- EHR Records - Only show if logged in -->
                    <li class="nav-item">
                        <a class="nav-link <?php echo (strpos($_SERVER['PHP_SELF'], 'ehr_records') !== false) ? 'active' : ''; ?>" href="<?php echo $base_url; ?>modules/patients/ehr_records.php">
                            <i class="bi bi-file-medical"></i> EHR Records
                        </a>
                    </li>
                <?php endif; ?>
                
                <!-- Contact Us -->
                <li class="nav-item">
                    <a class="nav-link <?php echo (basename($_SERVER['PHP_SELF']) == 'contact.php') ? 'active' : ''; ?>" href="<?php echo $base_url; ?>contact.php">
                        <i class="bi bi-envelope"></i> Contact
                    </a>
                </li>
            </ul>
            
            <!-- Right Side Menu (Login/Logout/Profile) -->
            <ul class="navbar-nav ms-auto">
                <?php if($is_logged_in): ?>
                    <!-- User Dropdown Menu -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <?php 
                            // Get doctor profile image
                            if (isset($_SESSION['doctor_id'])) {
                                $doctor_id = $_SESSION['doctor_id'];
                                
                                // Check if database connection exists, if not include it
                                if (!isset($conn)) {
                                    require_once $base_url . 'config/database.php';
                                }
                                
                                $stmt = $conn->prepare("SELECT profile_image FROM doctors WHERE doctor_id = ?");
                                $stmt->bind_param("i", $doctor_id);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                $doctor_profile = $result->fetch_assoc();
                                $stmt->close();
                                
                                if ($doctor_profile && $doctor_profile['profile_image']): 
                            ?>
                                <img src="<?php echo $base_url; ?>uploads/profile_pics/<?php echo $doctor_profile['profile_image']; ?>" class="rounded-circle" width="30" height="30" style="object-fit: cover; margin-right: 5px;">
                            <?php else: ?>
                                <i class="bi bi-person-circle"></i>
                            <?php 
                                endif;
                            }
                            ?>
                            Dr. <?php echo htmlspecialchars($doctor_name); ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                            <li>
                                <a class="dropdown-item" href="<?php echo $base_url; ?>modules/doctors/profile.php">
                                    <i class="bi bi-person"></i> My Profile
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <a class="dropdown-item text-danger" href="<?php echo $base_url; ?>modules/auth/logout.php">
                                    <i class="bi bi-box-arrow-right"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </li>
                <?php else: ?>
                    <!-- Login Button - Show only if NOT logged in -->
                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_url; ?>modules/auth/login.php">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </a>
                    </li>
                    
                    <!-- Register Button - Show only if NOT logged in -->
                    <li class="nav-item">
                        <a class="btn btn-outline-light ms-2" href="<?php echo $base_url; ?>modules/auth/register.php">
                            <i class="bi bi-person-plus"></i> Register
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
