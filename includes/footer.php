</main>
<!-- End of Main Content -->

<!-- Footer -->
<footer class="bg-dark text-white mt-5 py-4">
    <div class="container">
        <div class="row">
            <!-- About Section -->
            <div class="col-md-4 mb-3">
                <h5><i class="bi bi-heart-pulse-fill"></i> EHR System</h5>
                <p class="text-light">
                    A comprehensive Electronic Health Records management system designed for healthcare professionals.
                </p>
            </div>

            <!-- Quick Links -->
            <div class="col-md-4 mb-3">
                <h5>Quick Links</h5>
                <ul class="list-unstyled">
                    <li><a href="<?php echo $base_url; ?>index.php" class="text-light text-decoration-none">Home</a></li>
                    <li><a href="<?php echo $base_url; ?>about.php" class="text-light text-decoration-none">About EHR</a></li>
                    <li><a href="<?php echo $base_url; ?>contact.php" class="text-light text-decoration-none">Contact Us</a></li>
                    <li><a href="<?php echo $base_url; ?>sus_evaluation.php" class="text-light text-decoration-none">SUS Evaluation</a></li>
                    <?php if(!$is_logged_in): ?>
                        <li><a href="<?php echo $base_url; ?>modules/auth/login.php" class="text-light text-decoration-none">Login</a></li>
                        <li><a href="<?php echo $base_url; ?>modules/auth/register.php" class="text-light text-decoration-none">Register</a></li>
                    <?php endif; ?>
                </ul>
            </div>

            <!-- Contact Info -->
            <div class="col-md-4 mb-3">
                <h5>Contact Information</h5>
                <ul class="list-unstyled text-light">
                    <li><i class="bi bi-geo-alt"></i> Deggendorf Institute of Technology</li>
                    <li><i class="bi bi-envelope"></i> info@ehr-system.com</li>
                    <li><i class="bi bi-phone"></i> +49 XXX XXXXXXX</li>
                </ul>
            </div>
        </div>

        <!-- Copyright -->
        <hr class="bg-secondary">
        <div class="row">
            <div class="col-md-12 text-center">
                <p class="mb-0 text-light">
                    &copy; <?php echo date('Y'); ?> EHR System. All Rights Reserved.
                    <br>
                    <small>Developed for Information Systems in Health Care (WS25/26)</small>
                </p>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap 5 JavaScript Bundle (includes Popper) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JavaScript -->
<script src="<?php echo $base_url; ?>js/main.js"></script>

<!-- Additional JavaScript for specific pages -->
<?php if(isset($extra_js)): ?>
    <?php foreach($extra_js as $js): ?>
        <script src="<?php echo $base_url . $js; ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>