<?php
// Start session
session_start();

// Set page variables
$page_title = "About EHR";
$base_url = "./";
?>

<?php include 'includes/header.php'; ?>

<!-- Page Header -->
<section class="bg-primary text-white py-5">
    <div class="container">
        <h1 class="display-4 fw-bold"><i class="bi bi-info-circle"></i> About Electronic Health Records</h1>
        <p class="lead">Understanding the importance and benefits of EHR systems in modern healthcare</p>
    </div>
</section>

<!-- What is EHR Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h2 class="fw-bold mb-4">What is an Electronic Health Record (EHR)?</h2>
                <p class="lead text-muted">
                    An Electronic Health Record (EHR) is a digital version of a patient's paper chart. EHRs are real-time, 
                    patient-centered records that make information available instantly and securely to authorized users.
                </p>
                
                <p>
                    While an EHR does contain the medical and treatment histories of patients, an EHR system is built to go 
                    beyond standard clinical data collected in a provider's office and can be inclusive of a broader view of 
                    a patient's care. EHRs can:
                </p>
                
                <ul class="mb-4">
                    <li>Contain a patient's medical history, diagnoses, medications, treatment plans, immunization dates, allergies, radiology images, and laboratory test results</li>
                    <li>Allow access to evidence-based tools that providers can use to make decisions about a patient's care</li>
                    <li>Automate and streamline provider workflow</li>
                    <li>Enable providers to share information with other healthcare providers and organizations</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<!-- Importance Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h2 class="fw-bold mb-4">Why are EHRs Important?</h2>
                
                <div class="accordion" id="importanceAccordion">
                    <!-- Accordion Item 1 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne">
                                <i class="bi bi-check-circle text-success me-2"></i> Improved Patient Care
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse show" data-bs-parent="#importanceAccordion">
                            <div class="accordion-body">
                                EHRs provide complete and accurate information about patients at the point of care. This enables 
                                healthcare providers to make better decisions and provide higher quality care. Quick access to 
                                patient records means more coordinated and efficient care.
                            </div>
                        </div>
                    </div>
                    
                    <!-- Accordion Item 2 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo">
                                <i class="bi bi-check-circle text-success me-2"></i> Enhanced Patient Safety
                            </button>
                        </h2>
                        <div id="collapseTwo" class="accordion-collapse collapse" data-bs-parent="#importanceAccordion">
                            <div class="accordion-body">
                                EHRs help reduce medical errors by providing accurate, up-to-date, and complete information about 
                                patients. They alert providers to potential safety issues such as allergies, drug interactions, 
                                and other critical health information.
                            </div>
                        </div>
                    </div>
                    
                    <!-- Accordion Item 3 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree">
                                <i class="bi bi-check-circle text-success me-2"></i> Increased Efficiency
                            </button>
                        </h2>
                        <div id="collapseThree" class="accordion-collapse collapse" data-bs-parent="#importanceAccordion">
                            <div class="accordion-body">
                                EHRs eliminate the need for paper records, reducing storage space and administrative costs. They 
                                enable quick retrieval of patient information, reducing wait times and improving workflow efficiency 
                                in healthcare facilities.
                            </div>
                        </div>
                    </div>
                    
                    <!-- Accordion Item 4 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour">
                                <i class="bi bi-check-circle text-success me-2"></i> Better Coordination of Care
                            </button>
                        </h2>
                        <div id="collapseFour" class="accordion-collapse collapse" data-bs-parent="#importanceAccordion">
                            <div class="accordion-body">
                                EHRs facilitate better communication and coordination among healthcare providers. Multiple providers 
                                can access the same patient information simultaneously, ensuring everyone involved in a patient's 
                                care has the most current information.
                            </div>
                        </div>
                    </div>
                    
                    <!-- Accordion Item 5 -->
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive">
                                <i class="bi bi-check-circle text-success me-2"></i> Data Security and Privacy
                            </button>
                        </h2>
                        <div id="collapseFive" class="accordion-collapse collapse" data-bs-parent="#importanceAccordion">
                            <div class="accordion-body">
                                EHRs provide better security than paper records. They include encryption, secure user authentication, 
                                and audit trails to track who accessed what information and when. This helps protect patient privacy 
                                and comply with regulations like HIPAA.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Benefits Section -->
<section class="py-5">
    <div class="container">
        <h2 class="fw-bold text-center mb-5">Key Benefits of EHR Systems</h2>
        
        <div class="row g-4">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-graph-up text-primary"></i> Clinical Outcomes</h5>
                        <ul class="mb-0">
                            <li>Improved accuracy and completeness of documentation</li>
                            <li>Better clinical decision support</li>
                            <li>Reduction in medical errors</li>
                            <li>Enhanced patient safety</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-clock-history text-primary"></i> Efficiency</h5>
                        <ul class="mb-0">
                            <li>Faster access to patient records</li>
                            <li>Reduced paperwork and administrative burden</li>
                            <li>Streamlined workflows</li>
                            <li>Time savings for healthcare providers</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-people text-primary"></i> Patient Engagement</h5>
                        <ul class="mb-0">
                            <li>Patients can access their own health information</li>
                            <li>Better communication between patients and providers</li>
                            <li>Increased patient involvement in their care</li>
                            <li>Improved patient satisfaction</li>
                        </ul>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-body">
                        <h5 class="card-title"><i class="bi bi-currency-dollar text-primary"></i> Cost Savings</h5>
                        <ul class="mb-0">
                            <li>Reduction in duplicate testing</li>
                            <li>Lower storage and administrative costs</li>
                            <li>Decreased medication errors</li>
                            <li>Improved billing and coding accuracy</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Our EHR System Section -->
<section class="py-5 bg-light">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h2 class="fw-bold mb-4">About Our EHR System</h2>
                <p>
                    Our Electronic Health Records system is designed specifically for healthcare professionals who need a 
                    reliable, secure, and easy-to-use solution for managing patient information. Built with modern web 
                    technologies and following industry best practices, our system provides:
                </p>
                
                <div class="row g-3 mt-3">
                    <div class="col-md-6">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                            <div>
                                <strong>Comprehensive Patient Management</strong>
                                <p class="text-muted mb-0">Complete patient demographics and contact information</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                            <div>
                                <strong>Full CRUD Operations</strong>
                                <p class="text-muted mb-0">Create, Read, Update, and Delete patient records</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                            <div>
                                <strong>Secure Authentication</strong>
                                <p class="text-muted mb-0">Email verification and strong password requirements</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                            <div>
                                <strong>Document Management</strong>
                                <p class="text-muted mb-0">Upload and store medical documents and images</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                            <div>
                                <strong>Responsive Design</strong>
                                <p class="text-muted mb-0">Access from any device - desktop, tablet, or mobile</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-check-circle-fill text-success me-2 mt-1"></i>
                            <div>
                                <strong>User-Friendly Interface</strong>
                                <p class="text-muted mb-0">Intuitive design for easy navigation and use</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 text-center">
                    <a href="modules/auth/register.php" class="btn btn-primary btn-lg">
                        <i class="bi bi-person-plus"></i> Get Started Today
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- References Section -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto">
                <h3 class="fw-bold mb-3">Learn More About EHRs</h3>
                <p class="text-muted">For more information about Electronic Health Records, visit:</p>
                <ul>
                    <li><a href="https://www.healthit.gov/topic/health-it-basics/benefits-ehrs" target="_blank">HealthIT.gov - Benefits of EHRs</a></li>
                    <li><a href="https://www.who.int/publications/i/item/9789241550529" target="_blank">WHO - Electronic Health Records Manual</a></li>
                    <li><a href="https://www.ncbi.nlm.nih.gov/pmc/articles/PMC6371636/" target="_blank">Research on EHR Implementation</a></li>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
