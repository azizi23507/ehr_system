# COMMON INCLUDES EXPLANATION

This document explains the reusable components (header, footer, navbar) and supporting files (CSS, JS) that are used across all pages of the EHR system.

---

## WHY WE NEED COMMON INCLUDES

**Problem**: If we write header and footer code on every page:
- 😫 Code repetition (same code 20+ times)
- 😫 Hard to maintain (change logo? edit 20+ files!)
- 😫 Inconsistent design (forgot to update one page)
- 😫 More bugs (typos across multiple files)

**Solution**: Write once, include everywhere!
- ✅ One header file, used on all pages
- ✅ One footer file, used on all pages
- ✅ Change once, updates everywhere
- ✅ Consistent design across entire system

---

## FILE STRUCTURE

```
includes/
├── header.php      → Top of every page (HTML head, navigation)
├── navbar.php      → Navigation menu
└── footer.php      → Bottom of every page (footer, scripts)

css/
└── style.css       → All custom styles

js/
└── main.js         → All custom JavaScript functions
```

---

## 1. HEADER.PHP

### What it does:
- Opens HTML document
- Sets up `<head>` section (title, meta tags, CSS links)
- Includes navigation bar
- Opens main content area

### Key Features:

**Dynamic Page Title:**
```php
<title><?php echo isset($page_title) ? $page_title . ' - EHR System' : 'EHR System'; ?></title>
```
- Each page can set its own title
- Example: `$page_title = "Dashboard"` → Browser shows "Dashboard - EHR System"

**Bootstrap 5 CDN:**
- Loads Bootstrap CSS from Content Delivery Network (fast!)
- No need to download Bootstrap files

**Bootstrap Icons:**
- Icons for buttons, navigation, etc.
- Example: `<i class="bi bi-house-door"></i>` shows a house icon

**Custom CSS:**
```php
<link rel="stylesheet" href="<?php echo $base_url; ?>css/style.css">
```
- Loads our custom styles
- `$base_url` ensures correct path from any page

**Extra CSS Support:**
```php
<?php if(isset($extra_css)): ?>
    <?php foreach($extra_css as $css): ?>
        <link rel="stylesheet" href="<?php echo $base_url . $css; ?>">
    <?php endforeach; ?>
<?php endif; ?>
```
- Allows specific pages to load additional CSS files
- Example: Dashboard might need extra chart styling

### How to use in a page:
```php
<?php
$page_title = "My Page Title";
$base_url = "../../"; // Adjust based on page location
include '../../includes/header.php';
?>

<!-- Your page content here -->

<?php include '../../includes/footer.php'; ?>
```

---

## 2. NAVBAR.PHP

### What it does:
- Creates responsive navigation menu
- Shows different menu items for logged-in vs logged-out users
- Highlights current page (active state)

### Key Features:

**Session Check:**
```php
$is_logged_in = isset($_SESSION['doctor_id']) && !empty($_SESSION['doctor_id']);
```
- Checks if doctor is logged in
- Used to show/hide menu items

**Responsive Design:**
```html
<button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
```
- On mobile: Shows hamburger menu button
- On desktop: Shows full menu

**Dynamic Active State:**
```php
<?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>
```
- Highlights the current page in the menu
- `basename($_SERVER['PHP_SELF'])` gets current filename
- If it matches, adds 'active' class

**Conditional Menu Items:**
```php
<?php if($is_logged_in): ?>
    <!-- Dashboard, Patients, EHR Records links -->
<?php else: ?>
    <!-- Login, Register buttons -->
<?php endif; ?>
```
- Logged-in doctors see: Dashboard, Patients, EHR Records
- Logged-out visitors see: Login, Register

**User Dropdown:**
```php
<li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle">
        Dr. <?php echo htmlspecialchars($doctor_name); ?>
    </a>
    <ul class="dropdown-menu">
        <li><a href="profile.php">My Profile</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</li>
```
- Shows doctor's name
- Dropdown with Profile and Logout options
- `htmlspecialchars()` prevents XSS attacks

### Menu Structure:

**For Everyone:**
- Home
- About EHR
- Contact

**Only for Logged-in Doctors:**
- Dashboard
- Patients
- EHR Records
- Profile (dropdown)
- Logout (dropdown)

**Only for Logged-out Visitors:**
- Login
- Register

---

## 3. FOOTER.PHP

### What it does:
- Closes main content area
- Displays footer with info and links
- Loads JavaScript files
- Closes HTML document

### Key Features:

**Footer Content:**
- About section (info about EHR system)
- Quick links (navigation shortcuts)
- Contact information

**Dynamic Current Year:**
```php
&copy; <?php echo date('Y'); ?> EHR System.
```
- Automatically shows current year (2025, 2026, etc.)
- No need to manually update every year!

**Bootstrap JavaScript:**
```html
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
```
- Loads Bootstrap JS for dropdowns, modals, tooltips, etc.
- Bundle version includes Popper.js (for positioning)

**Custom JavaScript:**
```html
<script src="<?php echo $base_url; ?>js/main.js"></script>
```
- Loads our custom functions

**Extra JS Support:**
```php
<?php if(isset($extra_js)): ?>
    <?php foreach($extra_js as $js): ?>
        <script src="<?php echo $base_url . $js; ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>
```
- Pages can load additional JavaScript files
- Example: Patient page might need chart.js for graphs

---

## 4. STYLE.CSS

### What it includes:

**General Styles:**
- Body layout (flex for sticky footer)
- Font family and colors
- Background color

**Navigation Styles:**
- Navbar shadow and spacing
- Hover effects on links
- Active page highlighting

**Card Styles:**
- Rounded corners, shadows
- Hover animations (lift effect)
- Colored headers

**Button Styles:**
- Hover effects (lift and shadow)
- Color variations (primary, success, danger)
- Rounded corners

**Form Styles:**
- Input field focus effects (blue border)
- Label styling
- Checkbox/radio customization

**Table Styles:**
- Header background color
- Hover effect on rows
- Rounded corners

**Alert Styles:**
- Custom shadows
- Border radius

**Hero Section (Homepage):**
- Gradient background
- Large heading
- Call-to-action layout

**Dashboard Cards:**
- Gradient backgrounds
- Icon styling
- Statistics display

**Login/Register Forms:**
- Centered container
- Card with shadow
- Full-width buttons

**Responsive Design:**
- Mobile adjustments (@media queries)
- Smaller fonts on mobile
- Adjusted padding

### CSS Organization:
```css
/* ============================================
   SECTION NAME
   ============================================ */
   
/* Styles here */
```
- Each section clearly labeled
- Easy to find and modify specific parts

---

## 5. MAIN.JS

### What it includes:

**1. Auto-hide Alerts (Lines 10-19)**
```javascript
const alerts = document.querySelectorAll('.alert:not(.alert-permanent)');
setTimeout(function() {
    const bsAlert = new bootstrap.Alert(alert);
    bsAlert.close();
}, 5000);
```
- **What it does**: Success/error messages disappear after 5 seconds
- **Why**: Better UX - user doesn't need to manually close
- **Exception**: Alerts with class `alert-permanent` stay visible

**2. Confirm Delete (Lines 24-26)**
```javascript
function confirmDelete(itemName) {
    return confirm('Are you sure you want to delete ' + itemName + '?');
}
```
- **What it does**: Shows confirmation before deleting
- **How to use**: `<button onclick="if(confirmDelete('Patient John')){...}">Delete</button>`
- **Why**: Prevents accidental deletions

**3. Form Validation (Lines 31-47)**
```javascript
function validateForm(formId) {
    // Checks all required fields are filled
}
```
- **What it does**: Validates form before submission
- **Highlights** empty required fields in red
- **Returns**: true if valid, false if invalid

**4. Password Strength Checker (Lines 52-95)**
```javascript
function checkPasswordStrength(password) {
    // Returns: { score: 5, label: 'Strong' }
}
```
- **What it does**: Evaluates password strength
- **Checks for**: length, uppercase, lowercase, numbers, special chars
- **Labels**: Weak (0-2), Medium (3-4), Strong (5-6)
- **Usage**: Show user if their password is secure enough

**5. Image Preview (Lines 100-132)**
```javascript
function previewImage(input, previewId) {
    // Shows image before uploading
}
```
- **What it does**: Displays selected image before upload
- **Validates**: File type (must be image) and size (max 5MB)
- **Usage**: Profile picture upload, X-ray upload, etc.

**6. Loading Spinner (Lines 137-154)**
```javascript
function showLoadingSpinner() {
    // Displays loading overlay
}
function hideLoadingSpinner() {
    // Removes loading overlay
}
```
- **What it does**: Shows loading animation during operations
- **Usage**: Form submission, AJAX requests, database operations

**7. Format Date (Lines 159-177)**
```javascript
function formatDate(dateString, format = 'DD/MM/YYYY') {
    // Converts date format
}
```
- **What it does**: Changes date display format
- **Supports**: DD/MM/YYYY, MM/DD/YYYY, YYYY-MM-DD
- **Usage**: Display dates in user-friendly format

**8. Calculate BMI (Lines 182-204)**
```javascript
function calculateBMI(heightCm, weightKg) {
    // Returns BMI value
}
function getBMICategory(bmi) {
    // Returns: Underweight, Normal, Overweight, Obese
}
```
- **What it does**: Calculates Body Mass Index
- **Formula**: BMI = weight(kg) / (height(m))²
- **Usage**: EHR record creation/editing

**9. Search Table (Lines 209-235)**
```javascript
function searchTable(inputId, tableId) {
    // Filters table rows based on search
}
```
- **What it does**: Real-time table search
- **Usage**: Search patients, search EHR records
- **Shows/hides** rows based on match

**10. Print Section (Lines 240-260)**
```javascript
function printSection(sectionId) {
    // Opens print dialog for specific section
}
```
- **What it does**: Prints part of page (not whole page)
- **Usage**: Print patient record, print EHR report

**11. Copy to Clipboard (Lines 265-284)**
```javascript
function copyToClipboard(text, buttonElement) {
    // Copies text and shows feedback
}
```
- **What it does**: Copies text with user feedback
- **Shows**: "Copied!" message for 2 seconds
- **Usage**: Copy patient ID, copy record number

**12. Debug Log (Lines 289-300)**
```javascript
function debugLog(message, data) {
    // Console logs only in development
}
```
- **What it does**: Conditional logging
- **Only logs** on localhost (development)
- **Doesn't log** on production (live server)

**13. Initialize Tooltips (Lines 305-310)**
```javascript
document.addEventListener('DOMContentLoaded', function() {
    // Activates all Bootstrap tooltips
});
```
- **What it does**: Enables hover tooltips
- **Usage**: Add `data-bs-toggle="tooltip"` to any element

**14. Scroll to Top (Lines 315-333)**
```javascript
function scrollToTop() {
    window.scrollTo({ top: 0, behavior: 'smooth' });
}
```
- **What it does**: Smooth scroll to page top
- **Shows button** when scrolled down 300px
- **Usage**: Long pages (patient list, EHR list)

---

## HOW EVERYTHING WORKS TOGETHER

### Page Structure Template:
```php
<?php
// 1. Set page variables
$page_title = "Dashboard";
$base_url = "../../";

// 2. Include header (includes navbar automatically)
include '../../includes/header.php';
?>

<!-- 3. Your page content here -->
<div class="container">
    <h1>Dashboard</h1>
    <p>Welcome to your dashboard!</p>
</div>

<?php 
// 4. Include footer (includes scripts automatically)
include '../../includes/footer.php'; 
?>
```

### What Gets Loaded:
1. ✅ HTML structure (header.php)
2. ✅ Bootstrap CSS (header.php)
3. ✅ Custom CSS (header.php)
4. ✅ Navigation menu (navbar.php, included in header.php)
5. ✅ Your page content (your code)
6. ✅ Footer (footer.php)
7. ✅ Bootstrap JS (footer.php)
8. ✅ Custom JS (footer.php)

---

## BENEFITS OF THIS STRUCTURE

### 1. Maintainability
- Change logo? Edit one file (navbar.php)
- Update footer? Edit one file (footer.php)
- No need to touch 20+ pages

### 2. Consistency
- All pages look the same
- Same navigation everywhere
- Same footer everywhere

### 3. Efficiency
- Write once, use everywhere
- Faster development
- Less code to manage

### 4. Scalability
- Easy to add new pages
- Just include header and footer
- New page ready in minutes

### 5. Team Collaboration
- Clear structure
- Easy to understand
- Team members can work on different pages without conflicts

---

## COMMON ISSUES AND SOLUTIONS

### Issue 1: CSS/JS Not Loading
**Cause**: Wrong `$base_url` path

**Solution**: Check how many folders deep you are
```php
// In root folder (index.php)
$base_url = "./";

// In modules/auth/ (login.php)
$base_url = "../../";

// In modules/patients/ (view_patients.php)
$base_url = "../../";
```

### Issue 2: Navbar Not Showing User Name
**Cause**: Session not started before including navbar

**Solution**: Start session before including header
```php
<?php
session_start(); // Must be before any output
include 'includes/header.php';
?>
```

### Issue 3: Active Menu Item Not Highlighting
**Cause**: Page filename doesn't match condition

**Solution**: Check the condition in navbar.php matches your filename

### Issue 4: JavaScript Functions Not Working
**Cause**: main.js loaded before elements exist

**Solution**: Functions already wrapped in DOMContentLoaded or called after elements exist

---

## NEXT STEPS

Now that we have the common structure, we can create:
1. ✅ Homepage (index.php)
2. ✅ About page (about.php)
3. ✅ Contact page (contact.php)
4. ✅ Registration page (modules/auth/register.php)
5. ✅ Login page (modules/auth/login.php)
6. ✅ Dashboard (modules/dashboard/dashboard.php)

Each page will simply include header and footer, with their specific content in between!

---

## TESTING CHECKLIST

Before moving forward, test:
- [ ] Bootstrap CSS loads (page has Bootstrap styling)
- [ ] Bootstrap Icons show (test: `<i class="bi bi-house"></i>`)
- [ ] Custom CSS loads (check for gradient header, rounded cards)
- [ ] Navigation bar appears
- [ ] Footer appears
- [ ] Bootstrap JS works (test: navbar collapse on mobile)
- [ ] Custom JS works (test: alert auto-hide after 5 seconds)
- [ ] Responsive design works (test on mobile view)

All good? Let's continue building! 🚀
