# SESSION CONFIGURATION EXPLANATION

## What is a Session?

A **session** is like a "memory" that remembers who you are as you navigate different pages.

**Real-world analogy**:
- You login to the EHR system ➔ You get a "badge"
- You navigate to different pages ➔ Your "badge" proves who you are
- You logout or close browser ➔ Your "badge" is taken away

**Without sessions**: You'd have to login on EVERY page! 😫
**With sessions**: Login once, stay logged in! ✅

---

## How Sessions Work - Simple Explanation

### Step 1: User logs in
```
1. User enters username/password
2. PHP checks database
3. If correct: PHP creates a session
4. PHP stores doctor_id, name, email in session
5. PHP sends a cookie to user's browser
```

### Step 2: User visits another page
```
1. Browser automatically sends cookie to server
2. PHP reads cookie and loads session data
3. PHP knows: "This is Dr. John, doctor_id = 5"
4. Page displays personalized content
```

### Step 3: User logs out
```
1. User clicks logout button
2. PHP destroys session data
3. Cookie becomes invalid
4. User must login again
```

---

## Code Breakdown - What Each Part Does

### Part 1: Start Session
```php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
```

**What this does**:
- Checks if session is already started
- If not, starts a new session
- MUST be at the top of every page that uses sessions

**Why check first?**
- If you call `session_start()` twice, PHP shows an error
- This check prevents that error

---

### Part 2: Security Settings
```php
session_regenerate_id(true);
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
```

**What these do**:

1. **session_regenerate_id()**: Creates new session ID
   - Prevents "session fixation" attacks
   - Attacker can't steal/predict session IDs

2. **cookie_httponly**: JavaScript cannot access session cookie
   - Prevents XSS (Cross-Site Scripting) attacks
   - Even if hacker injects JavaScript, can't steal session

3. **use_only_cookies**: Session ID only in cookies, not in URL
   - Prevents session ID from appearing in browser address bar
   - URLs can be shared/logged, exposing session ID

**Why these matter in healthcare**:
- Patient data is VERY sensitive (HIPAA, GDPR)
- Sessions must be extremely secure
- Prevents hackers from stealing doctor accounts

---

### Part 3: Auto-Logout (Session Timeout)
```php
define('SESSION_TIMEOUT', 1800); // 30 minutes

if (isset($_SESSION['last_activity'])) {
    $inactive_time = time() - $_SESSION['last_activity'];
    
    if ($inactive_time > SESSION_TIMEOUT) {
        session_unset();
        session_destroy();
        header("Location: ../index.php?timeout=1");
        exit();
    }
}

$_SESSION['last_activity'] = time();
```

**How this works**:

1. When page loads: Check current time vs. last activity time
2. If difference > 30 minutes: Session expired
3. Destroy session and redirect to login page
4. Update last activity time to NOW

**Real-world scenario**:
- Dr. Smith logs in at 10:00 AM
- Dr. Smith goes to lunch at 10:15 AM (leaves computer unlocked)
- Someone else sits at computer at 10:50 AM (35 minutes later)
- Session has expired! They cannot access patient data! ✅

**Why 30 minutes?**
- Balance between security and usability
- Long enough: Doctor can review patient file
- Short enough: Unauthorized access prevented

You can change this: `SESSION_TIMEOUT = 3600` (1 hour)

---

### Part 4: Helper Functions

These are **utility functions** to make your life easier!

#### Function 1: `isLoggedIn()`
```php
function isLoggedIn() {
    return isset($_SESSION['doctor_id']) && !empty($_SESSION['doctor_id']);
}
```

**What it does**: Checks if doctor is logged in
**How to use**:
```php
if (isLoggedIn()) {
    echo "Welcome back, doctor!";
} else {
    echo "Please login first.";
}
```

---

#### Function 2: `requireLogin()`
```php
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: ../index.php?login_required=1");
        exit();
    }
}
```

**What it does**: Kicks out anyone not logged in
**How to use**: Put this at the top of protected pages
```php
<?php
require_once '../../config/session.php';
requireLogin(); // If not logged in, redirect to login page

// Rest of your page code here...
?>
```

**Example**: Dashboard page should ONLY be accessible if logged in

---

#### Function 3: `getDoctorId()`
```php
function getDoctorId() {
    return $_SESSION['doctor_id'] ?? null;
}
```

**What it does**: Returns the logged-in doctor's ID
**How to use**: When querying database for doctor's patients
```php
$doctor_id = getDoctorId();
$sql = "SELECT * FROM patients WHERE doctor_id = $doctor_id";
```

**Why this matters**: Each doctor sees ONLY their own patients!

---

#### Function 4: `getDoctorName()`
```php
function getDoctorName() {
    if (isset($_SESSION['doctor_name'])) {
        return $_SESSION['doctor_name'];
    }
    return 'Guest';
}
```

**What it does**: Returns doctor's full name
**How to use**: Display in navigation bar
```php
echo "Welcome, Dr. " . getDoctorName();
// Output: "Welcome, Dr. John Doe"
```

---

#### Function 5: `setUserSession()`
```php
function setUserSession($doctor_id, $doctor_name, $doctor_email, $username) {
    $_SESSION['doctor_id'] = $doctor_id;
    $_SESSION['doctor_name'] = $doctor_name;
    $_SESSION['doctor_email'] = $doctor_email;
    $_SESSION['username'] = $username;
    $_SESSION['login_time'] = time();
    $_SESSION['last_activity'] = time();
}
```

**What it does**: Creates session after successful login
**How to use**: In login.php after password verification
```php
// After checking username/password is correct:
setUserSession($row['doctor_id'], $row['first_name'] . ' ' . $row['last_name'], 
               $row['email'], $row['username']);
header("Location: dashboard.php");
```

**What gets stored in session**:
- Doctor's ID (for database queries)
- Doctor's name (for display)
- Doctor's email (for display/verification)
- Username (for display)
- Login time (for tracking)
- Last activity time (for timeout)

---

#### Function 6: `logout()`
```php
function logout() {
    session_unset();
    session_destroy();
    header("Location: ../index.php?logout=1");
    exit();
}
```

**What it does**: 
1. Removes all session data
2. Destroys the session
3. Redirects to homepage

**How to use**: In logout.php
```php
<?php
require_once '../../config/session.php';
logout();
?>
```

---

## Complete Flow Example

### Scenario: Dr. Smith wants to view her patients

**Page 1: Login (modules/auth/login.php)**
```php
// Doctor enters: username="drsmith", password="Smith@123"

// Check database
$sql = "SELECT * FROM doctors WHERE username='drsmith'";
$result = $conn->query($sql);
$doctor = $result->fetch_assoc();

// Verify password
if (password_verify("Smith@123", $doctor['password'])) {
    // Login successful!
    setUserSession($doctor['doctor_id'], 
                   $doctor['first_name'] . ' ' . $doctor['last_name'],
                   $doctor['email'], 
                   $doctor['username']);
    
    // Redirect to dashboard
    header("Location: ../dashboard/dashboard.php");
}
```

**Session now contains**:
```
$_SESSION['doctor_id'] = 5
$_SESSION['doctor_name'] = "Sarah Smith"
$_SESSION['doctor_email'] = "sarah.smith@hospital.com"
$_SESSION['username'] = "drsmith"
```

---

**Page 2: Dashboard (modules/dashboard/dashboard.php)**
```php
require_once '../../config/session.php';
requireLogin(); // Check if logged in

// Get doctor's ID from session
$doctor_id = getDoctorId(); // Returns 5

// Query ONLY this doctor's patients
$sql = "SELECT * FROM patients WHERE doctor_id = $doctor_id";
$result = $conn->query($sql);

// Display patients
echo "Welcome, Dr. " . getDoctorName(); // "Welcome, Dr. Sarah Smith"

while ($patient = $result->fetch_assoc()) {
    echo $patient['first_name'] . " " . $patient['last_name'];
}
```

---

**Page 3: Logout (modules/auth/logout.php)**
```php
require_once '../../config/session.php';
logout(); // Destroys session, redirects to homepage
```

**Session is now empty**:
```
$_SESSION = array(); // No data
```

---

## Why We Need This File

### Without session.php:
- ❌ Every page must manually check if user is logged in
- ❌ Repeated code everywhere
- ❌ Easy to make mistakes (forget to check login on one page)
- ❌ No automatic logout
- ❌ Security vulnerabilities

### With session.php:
- ✅ One line: `requireLogin()` protects any page
- ✅ Helper functions make coding easier
- ✅ Centralized security settings
- ✅ Automatic timeout protection
- ✅ Easy to maintain

---

## How to Use in Your Project

### Step 1: Protected Page (requires login)
```php
<?php
require_once '../../config/database.php';
require_once '../../config/session.php';
requireLogin(); // Must be logged in to access this page
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
</head>
<body>
    <h1>Welcome, <?php echo getDoctorName(); ?>!</h1>
    <!-- Rest of your page -->
</body>
</html>
```

### Step 2: Public Page (no login required)
```php
<?php
require_once 'config/session.php';
// No requireLogin() call - anyone can access

// But you can still check if logged in:
if (isLoggedIn()) {
    echo "Hello, " . getDoctorName();
} else {
    echo "Please login";
}
?>
```

---

## Testing Session Functionality

Want to see if sessions work? Uncomment the debugging code at the bottom of session.php:

```php
echo "<pre>";
echo "Session Status: " . (isLoggedIn() ? "Logged In" : "Not Logged In") . "<br>";
echo "Doctor ID: " . getDoctorId() . "<br>";
echo "Doctor Name: " . getDoctorName() . "<br>";
echo "Time remaining: " . (SESSION_TIMEOUT - (time() - $_SESSION['last_activity'])) . " seconds<br>";
echo "</pre>";
```

This will display session information on every page!

---

## Common Issues and Solutions

### Issue 1: "Headers already sent" error
**Cause**: You have output (echo, HTML, even a space) before `session_start()`
**Solution**: Make sure session.php is included BEFORE any output

### Issue 2: Session not persisting between pages
**Cause**: Cookies are disabled in browser
**Solution**: Enable cookies or use session.use_trans_sid

### Issue 3: Auto-logout happening too quickly
**Cause**: SESSION_TIMEOUT is too short
**Solution**: Increase value (e.g., 3600 for 1 hour)

### Issue 4: Session data disappearing randomly
**Cause**: Server deletes session files (shared hosting)
**Solution**: Configure session.save_path or use database sessions

---

## Security Best Practices (Already Implemented!)

✅ **Session ID regeneration** - Prevents session fixation
✅ **HTTPOnly cookies** - Prevents XSS attacks
✅ **Automatic timeout** - Limits exposure time
✅ **Server-side validation** - Never trust client data
✅ **Secure session storage** - Session data on server, not client

For your university project, this is **more than enough**! 🎓

---

## What's Next?

Now that we have:
1. ✅ Database (structure for data)
2. ✅ Database config (connection to MySQL)
3. ✅ Session config (user authentication)

We can build:
- Registration page (create doctor account)
- Login page (authenticate doctor)
- Dashboard (view patients)
- CRUD pages (manage EHR records)

Let's continue! 🚀
