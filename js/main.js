// ============================================
// MAIN JAVASCRIPT FILE
// ============================================
// Common JavaScript functions used across the EHR system

// ============================================
// AUTO-HIDE ALERTS AFTER 5 SECONDS
// ============================================
document.addEventListener('DOMContentLoaded', function() {
    // Get all alert elements
    const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
    
    // Auto-hide each alert after 5 seconds
    alerts.forEach(function(alert) {
        setTimeout(function() {
            // Use Bootstrap's built-in alert close method
            const bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000); // 5000 milliseconds = 5 seconds
    });
});

// ============================================
// CONFIRM DELETE ACTION
// ============================================
// Shows confirmation dialog before deleting items
function confirmDelete(itemName) {
    return confirm('Are you sure you want to delete ' + itemName + '? This action cannot be undone.');
}

// ============================================
// FORM VALIDATION - CHECK REQUIRED FIELDS
// ============================================
// Validates that all required fields are filled before form submission
function validateForm(formId) {
    const form = document.getElementById(formId);
    if (!form) return false;
    
    const requiredFields = form.querySelectorAll('[required]');
    let isValid = true;
    
    requiredFields.forEach(function(field) {
        if (!field.value.trim()) {
            field.classList.add('is-invalid');
            isValid = false;
        } else {
            field.classList.remove('is-invalid');
        }
    });
    
    return isValid;
}

// ============================================
// PASSWORD STRENGTH CHECKER
// ============================================
// Checks password strength and displays indicator
function checkPasswordStrength(password) {
    let strength = 0;
    
    // Check password length
    if (password.length >= 8) strength++;
    if (password.length >= 12) strength++;
    
    // Check for lowercase letters
    if (/[a-z]/.test(password)) strength++;
    
    // Check for uppercase letters
    if (/[A-Z]/.test(password)) strength++;
    
    // Check for numbers
    if (/[0-9]/.test(password)) strength++;
    
    // Check for special characters
    if (/[^a-zA-Z0-9]/.test(password)) strength++;
    
    return {
        score: strength,
        label: getStrengthLabel(strength)
    };
}

// Helper function for password strength label
function getStrengthLabel(score) {
    if (score <= 2) return 'Weak';
    if (score <= 4) return 'Medium';
    return 'Strong';
}

// Update password strength indicator
function updatePasswordStrength(passwordField, indicatorId) {
    const password = passwordField.value;
    const indicator = document.getElementById(indicatorId);
    
    if (!indicator) return;
    
    const strength = checkPasswordStrength(password);
    
    // Update indicator text and color
    indicator.textContent = 'Password Strength: ' + strength.label;
    
    // Remove all strength classes
    indicator.classList.remove('text-danger', 'text-warning', 'text-success');
    
    // Add appropriate class based on strength
    if (strength.score <= 2) {
        indicator.classList.add('text-danger');
    } else if (strength.score <= 4) {
        indicator.classList.add('text-warning');
    } else {
        indicator.classList.add('text-success');
    }
}

// ============================================
// PREVIEW IMAGE BEFORE UPLOAD
// ============================================
// Shows preview of image before uploading
function previewImage(input, previewId) {
    const file = input.files[0];
    const preview = document.getElementById(previewId);
    
    if (!preview) return;
    
    if (file) {
        // Check if file is an image
        if (!file.type.startsWith('image/')) {
            alert('Please select an image file.');
            input.value = '';
            return;
        }
        
        // Check file size (max 5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert('Image size must be less than 5MB.');
            input.value = '';
            return;
        }
        
        // Create FileReader to read the image
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        };
        
        reader.readAsDataURL(file);
    } else {
        preview.src = '';
        preview.style.display = 'none';
    }
}

// ============================================
// SHOW LOADING SPINNER
// ============================================
// Displays loading overlay during AJAX requests or form submissions
function showLoadingSpinner() {
    const spinner = document.createElement('div');
    spinner.id = 'loading-spinner';
    spinner.className = 'spinner-overlay';
    spinner.innerHTML = `
        <div class="spinner-border text-light" role="status" style="width: 4rem; height: 4rem;">
            <span class="visually-hidden">Loading...</span>
        </div>
    `;
    document.body.appendChild(spinner);
}

// Hide loading spinner
function hideLoadingSpinner() {
    const spinner = document.getElementById('loading-spinner');
    if (spinner) {
        spinner.remove();
    }
}

// ============================================
// FORMAT DATE FOR DISPLAY
// ============================================
// Converts date from YYYY-MM-DD to DD/MM/YYYY or other format
function formatDate(dateString, format = 'DD/MM/YYYY') {
    if (!dateString) return '';
    
    const date = new Date(dateString);
    const day = String(date.getDate()).padStart(2, '0');
    const month = String(date.getMonth() + 1).padStart(2, '0');
    const year = date.getFullYear();
    
    if (format === 'DD/MM/YYYY') {
        return day + '/' + month + '/' + year;
    } else if (format === 'MM/DD/YYYY') {
        return month + '/' + day + '/' + year;
    } else if (format === 'YYYY-MM-DD') {
        return year + '-' + month + '-' + day;
    }
    
    return dateString;
}

// ============================================
// CALCULATE BMI
// ============================================
// Calculates Body Mass Index from height (cm) and weight (kg)
function calculateBMI(heightCm, weightKg) {
    if (!heightCm || !weightKg || heightCm <= 0 || weightKg <= 0) {
        return null;
    }
    
    // Convert height from cm to meters
    const heightM = heightCm / 100;
    
    // Calculate BMI
    const bmi = weightKg / (heightM * heightM);
    
    // Round to 2 decimal places
    return Math.round(bmi * 100) / 100;
}

// Get BMI category
function getBMICategory(bmi) {
    if (bmi < 18.5) return 'Underweight';
    if (bmi < 25) return 'Normal weight';
    if (bmi < 30) return 'Overweight';
    return 'Obese';
}

// ============================================
// SEARCH/FILTER TABLE
// ============================================
// Filters table rows based on search input
function searchTable(inputId, tableId) {
    const input = document.getElementById(inputId);
    const table = document.getElementById(tableId);
    
    if (!input || !table) return;
    
    const filter = input.value.toLowerCase();
    const rows = table.getElementsByTagName('tr');
    
    // Loop through all table rows (skip header row)
    for (let i = 1; i < rows.length; i++) {
        const row = rows[i];
        const cells = row.getElementsByTagName('td');
        let found = false;
        
        // Search in all cells of the row
        for (let j = 0; j < cells.length; j++) {
            const cellText = cells[j].textContent || cells[j].innerText;
            if (cellText.toLowerCase().indexOf(filter) > -1) {
                found = true;
                break;
            }
        }
        
        // Show or hide the row
        row.style.display = found ? '' : 'none';
    }
}

// ============================================
// PRINT PAGE/SECTION
// ============================================
// Prints the specified section of the page
function printSection(sectionId) {
    const section = document.getElementById(sectionId);
    if (!section) return;
    
    // Create a new window for printing
    const printWindow = window.open('', '', 'height=600,width=800');
    
    // Write the section content to the new window
    printWindow.document.write('<html><head><title>Print</title>');
    printWindow.document.write('<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">');
    printWindow.document.write('<link rel="stylesheet" href="../css/style.css">');
    printWindow.document.write('</head><body>');
    printWindow.document.write(section.innerHTML);
    printWindow.document.write('</body></html>');
    
    // Wait for content to load, then print
    printWindow.document.close();
    setTimeout(function() {
        printWindow.print();
        printWindow.close();
    }, 500);
}

// ============================================
// COPY TO CLIPBOARD
// ============================================
// Copies text to clipboard and shows feedback
function copyToClipboard(text, buttonElement) {
    navigator.clipboard.writeText(text).then(function() {
        // Success - show feedback
        if (buttonElement) {
            const originalText = buttonElement.innerHTML;
            buttonElement.innerHTML = '<i class="bi bi-check"></i> Copied!';
            buttonElement.classList.add('btn-success');
            
            setTimeout(function() {
                buttonElement.innerHTML = originalText;
                buttonElement.classList.remove('btn-success');
            }, 2000);
        }
    }).catch(function(err) {
        console.error('Could not copy text: ', err);
        alert('Failed to copy to clipboard');
    });
}

// ============================================
// CONSOLE LOG HELPER (for debugging)
// ============================================
// Only logs in development environment
function debugLog(message, data = null) {
    // Check if we're in development mode
    // You can set this based on your environment
    const isDevelopment = window.location.hostname === 'localhost';
    
    if (isDevelopment) {
        if (data !== null) {
            console.log(message, data);
        } else {
            console.log(message);
        }
    }
}

// ============================================
// INITIALIZE TOOLTIPS (Bootstrap)
// ============================================
// Activates all Bootstrap tooltips on the page
document.addEventListener('DOMContentLoaded', function() {
    const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// ============================================
// SCROLL TO TOP BUTTON
// ============================================
// Shows/hides scroll to top button and handles click
window.addEventListener('scroll', function() {
    const scrollButton = document.getElementById('scroll-to-top');
    if (scrollButton) {
        if (window.scrollY > 300) {
            scrollButton.style.display = 'block';
        } else {
            scrollButton.style.display = 'none';
        }
    }
});

function scrollToTop() {
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
}
