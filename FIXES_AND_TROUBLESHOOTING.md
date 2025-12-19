# FIXES AND TROUBLESHOOTING GUIDE

## Issues Fixed in This Version

### ✅ Issue #1: Navbar Database Connection Error
**Problem:** Navbar tried to include database.php multiple times causing conflicts  
**Fix:** Added check `if (!isset($conn))` before including database  
**Status:** FIXED ✅

### ✅ Issue #2: Registration Form Image Upload Not Working
**Problem:** Form was missing `enctype="multipart/form-data"`  
**Fix:** Added enctype to registration form  
**Status:** FIXED ✅

### ✅ Issue #3: HTML Syntax Error in Add EHR Page
**Problem:** Line 251 had `<div class="="form-check">` (double quotes)  
**Fix:** Corrected to `<div class="form-check">`  
**Status:** FIXED ✅

---

## Common Issues & Solutions

### 🔴 Issue: "Access denied for user 'root'@'localhost'"

**Cause:** Your MySQL has a password set, but config file uses empty password

**Solution 1 - Set Password in Config (Recommended):**
1. Open `config/database.php`
2. Find: `define('DB_PASS', '');`
3. Change to: `define('DB_PASS', 'your_password');`
4. Common passwords: `root`, `password`, or empty

**Solution 2 - Find Your WAMP Password:**
1. Left-click WAMP icon → MySQL → MySQL Console
2. Type password and press Enter
3. If it connects, that's your password!
4. Put that password in `config/database.php`

**Solution 3 - Reset MySQL Password to Empty:**
1. Left-click WAMP icon
2. MySQL → MySQL Settings → Change root password
3. Leave empty, click OK
4. Restart All Services
5. Now `DB_PASS = ''` will work

---

### 🔴 Issue: Login Says "Invalid username or password"

**Cause:** Password hash in database might not match

**Solution 1 - Use Test File (EASIEST):**
1. Open: http://localhost/ehr-system/test_password.php
2. It will show if password matches
3. If not, it generates correct SQL to fix it
4. Copy the SQL and run in phpMyAdmin

**Solution 2 - Run SQL Directly:**
```sql
USE ehr_system;

-- Check current hash
SELECT username, password FROM doctors WHERE username = 'johndoe';

-- If password doesn't work, run this:
UPDATE doctors 
SET password = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi' 
WHERE username = 'johndoe';
```

**Solution 3 - Register New Account:**
Just click "Register" and create a fresh account!

**Demo Login:**
- Username: `johndoe`
- Password: `Doctor@123`

---

### 🔴 Issue: Database Not Imported / Tables Missing

**Solution:**
1. Open phpMyAdmin: http://localhost/phpmyadmin
2. Click "Import" tab
3. Choose file: `ehr-system/database/ehr_database.sql`
4. Click "Go"
5. Wait for success message
6. Check left sidebar - should see `ehr_system` database with 4 tables

---

### 🔴 Issue: Images Not Uploading

**Cause:** Upload folder doesn't have write permission

**Solution:**
1. Right-click `ehr-system/uploads` folder
2. Properties → Security tab
3. Edit → Add "Users" → Allow "Write"
4. Apply to all subfolders

**OR Check PHP Settings:**
1. WAMP: Left-click icon → PHP → php.ini
2. XAMPP: `C:\xampp\php\php.ini`
3. Find: `upload_max_filesize = 2M`
4. Change to: `upload_max_filesize = 10M`
5. Find: `post_max_size = 8M`
6. Change to: `post_max_size = 10M`
7. Restart Apache

---

### 🔴 Issue: Blank White Page

**Cause:** PHP error not displaying

**Solution - Enable Error Display:**
1. Open the page file (e.g., `index.php`)
2. Add at the very top (after `<?php`):
```php
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
```
3. Refresh page - you'll see actual error
4. Fix the error shown
5. Remove error display code when fixed

---

### 🔴 Issue: Doctor Profile Picture Not Showing

**Cause:** Image file missing or path incorrect

**Check:**
1. File exists: `uploads/profile_pics/doctor_johndoe.jpg`
2. Database has filename: Run SQL:
```sql
SELECT profile_image FROM doctors WHERE username = 'johndoe';
```
3. Should show: `doctor_johndoe.jpg`

**Fix:**
If image missing, doctor will show placeholder icon (this is normal!)

---

### 🔴 Issue: EHR Medical Images Not Showing

**Check:**
1. Files exist in: `uploads/documents/`
2. Should have:
   - xray_chest_sarah.jpg
   - xray_chest_michael.jpg
   - xray_chest_emma.jpg
   - xray_knee_robert.jpg
   - mri_brain_linda.jpg

**Solution:**
1. Make sure you imported the COMPLETE database file
2. Check if files exist in uploads/documents/
3. If missing, they'll show as "No image uploaded" (this is OK)

---

## Testing Checklist

Run through this checklist after setup:

- [ ] Open http://localhost/ehr-system/
- [ ] Homepage loads without errors
- [ ] Click "Login" - form appears
- [ ] Login with johndoe / Doctor@123
- [ ] Dashboard shows statistics
- [ ] Profile picture shows in top-right
- [ ] Click "Patients" - see 5 demo patients
- [ ] Click on a patient - see details
- [ ] Click "View EHR Records" - see medical records
- [ ] Open an EHR record - see X-ray/MRI image
- [ ] Click "Add Patient" - form works
- [ ] Click "Add EHR Record" - form works
- [ ] Click Profile - shows doctor info
- [ ] Click Logout - returns to homepage

If ALL checks pass: ✅ System working perfectly!

---

## Files Included for Testing

### test_password.php
Open: http://localhost/ehr-system/test_password.php

**What it does:**
- Tests if password hash is correct
- Generates fresh hash if needed
- Shows SQL to fix password
- Verifies login credentials

**When to use:**
- Login not working
- Want to verify password
- Need to reset demo account

---

## Database Structure Check

Run this SQL to verify everything is set up:

```sql
USE ehr_system;

-- Check tables exist
SHOW TABLES;
-- Should show: doctors, ehr_records, login_attempts, patients

-- Check demo doctor exists
SELECT doctor_id, username, first_name, last_name, is_verified, profile_image 
FROM doctors 
WHERE username = 'johndoe';
-- Should show: doctor_id=1, is_verified=1

-- Check demo patients
SELECT COUNT(*) as patient_count FROM patients;
-- Should show: 5

-- Check demo EHR records
SELECT COUNT(*) as ehr_count FROM ehr_records;
-- Should show: 8

-- Check EHR records with images
SELECT ehr_id, patient_id, xray_image, visit_date 
FROM ehr_records 
WHERE xray_image IS NOT NULL;
-- Should show: 5 records
```

---

## Quick Reset (If Everything Breaks)

If system is completely broken, start fresh:

1. **Drop Database:**
```sql
DROP DATABASE IF EXISTS ehr_system;
```

2. **Re-import:**
- phpMyAdmin → Import → `ehr_database.sql`

3. **Clear Browser Cache:**
- Ctrl+Shift+Delete → Clear cache

4. **Restart WAMP/XAMPP:**
- Stop all services
- Start again

5. **Test:**
- http://localhost/ehr-system/
- Login: johndoe / Doctor@123

---

## Getting Help

### Check These First:
1. Error message (if any)
2. Browser console (F12 → Console tab)
3. WAMP/XAMPP logs
4. This troubleshooting guide

### Common Error Messages:

**"Call to undefined function mysqli_connect"**
→ Solution: Enable mysqli extension in php.ini

**"Failed to open stream"**
→ Solution: Check file paths and permissions

**"Undefined variable $conn"**
→ Solution: Make sure database.php is included

**"Headers already sent"**
→ Solution: Remove any output before session_start()

---

## System Requirements Met ✅

- ✅ PHP 7.4 or higher
- ✅ MySQL 5.7 or higher
- ✅ Apache web server
- ✅ GD library for image processing
- ✅ mysqli extension enabled

All requirements are included in WAMP/XAMPP by default!

---

## Final Notes

### About Demo Data:
- Demo doctor: johndoe (password: Doctor@123)
- 5 demo patients with realistic data
- 8 EHR records with complete medical info
- 5 medical images (X-rays and MRI)
- 1 doctor profile picture

### About Placeholders:
- Missing images show placeholder icons
- This is NORMAL and looks professional
- System works perfectly without images

### About Security:
- Passwords are hashed (secure)
- SQL injection prevention (prepared statements)
- XSS protection (htmlspecialchars)
- Session timeout after 30 minutes
- Brute force protection (5 attempts limit)

---

## Need More Help?

1. Read INSTALLATION_GUIDE.md
2. Read QUICK_START.md
3. Check error logs in WAMP/XAMPP
4. Use test_password.php for password issues
5. Run database structure check SQL

**Everything should work perfectly after these fixes!** ✅
