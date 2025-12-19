# EHR SYSTEM - INSTALLATION & SETUP GUIDE

This guide will help you set up and run the EHR System on your local computer using WAMP or XAMPP.

---

## PREREQUISITES

Before starting, make sure you have:
- Windows operating system
- WAMP or XAMPP installed on your computer
- A web browser (Chrome, Firefox, Edge)
- A text editor (VS Code recommended)

---

## STEP 1: INSTALL WAMP OR XAMPP

### Option A: Install WAMP

1. **Download WAMP**
   - Visit: https://www.wampserver.com/en/
   - Download WampServer 3.x (64-bit or 32-bit based on your system)
   - File size: ~400 MB

2. **Install WAMP**
   - Run the downloaded installer
   - Choose installation directory (default: `C:\wamp64` or `C:\wamp`)
   - Follow the installation wizard
   - Complete the installation

3. **Start WAMP**
   - Double-click WAMP icon on desktop
   - Wait for the icon in system tray to turn **GREEN**
   - Orange = Services starting
   - Red = Services not running
   - Green = All services running ✅

4. **Test WAMP**
   - Open browser and go to: http://localhost
   - You should see the WAMP welcome page
   - Click "phpMyAdmin" to verify MySQL is working

---

### Option B: Install XAMPP

1. **Download XAMPP**
   - Visit: https://www.apachefriends.org/
   - Download XAMPP for Windows (latest version)
   - File size: ~150 MB

2. **Install XAMPP**
   - Run the downloaded installer
   - Choose installation directory (default: `C:\xampp`)
   - Select components: Apache, MySQL, PHP, phpMyAdmin (all selected by default)
   - Complete the installation

3. **Start XAMPP**
   - Open XAMPP Control Panel
   - Click "Start" next to **Apache**
   - Click "Start" next to **MySQL**
   - Both should show "Running" in green ✅

4. **Test XAMPP**
   - Open browser and go to: http://localhost
   - You should see the XAMPP welcome page
   - Click "phpMyAdmin" to verify MySQL is working

---

## STEP 2: SETUP PROJECT FILES

### Extract Project Files

1. **Download the project**
   - Download the `ehr-system` folder (or extract from .zip/.tar.gz)

2. **Copy to correct location**

   **For WAMP:**
   - Copy the entire `ehr-system` folder
   - Paste it in: `C:\wamp64\www\`
   - Final path should be: `C:\wamp64\www\ehr-system\`
   
   **For XAMPP:**
   - Copy the entire `ehr-system` folder
   - Paste it in: `C:\xampp\htdocs\`
   - Final path should be: `C:\xampp\htdocs\ehr-system\`

3. **Verify folder structure**
   ```
   ehr-system/
   ├── config/
   ├── css/
   ├── database/
   ├── images/
   ├── includes/
   ├── js/
   ├── modules/
   ├── uploads/
   ├── index.php
   ├── about.php
   └── contact.php
   ```

---

## STEP 3: CREATE DATABASE

### Using phpMyAdmin

1. **Open phpMyAdmin**
   - WAMP: Left-click WAMP icon → phpMyAdmin
   - XAMPP: Go to http://localhost/phpmyadmin
   - Or both: http://localhost/phpmyadmin

2. **Create Database**
   
   **Method 1: Import SQL File (Recommended)**
   - Click "Import" tab at the top
   - Click "Choose File"
   - Browse to: `ehr-system/database/ehr_database.sql`
   - Click "Go" at the bottom
   - Wait for success message: "Import has been successfully finished"
   - You should see `ehr_system` database in the left sidebar

   **Method 2: Copy-Paste SQL**
   - Click "SQL" tab at the top
   - Open `ehr-system/database/ehr_database.sql` in notepad
   - Copy ALL the content
   - Paste into the SQL box
   - Click "Go"
   - Wait for success messages

3. **Verify Database Creation**
   - Click on `ehr_system` in the left sidebar
   - You should see 4 tables:
     * doctors
     * patients
     * ehr_records
     * login_attempts
   - If you see these tables, database setup is complete! ✅

---

## STEP 4: CONFIGURE DATABASE CONNECTION

1. **Open database configuration file**
   - Navigate to: `ehr-system/config/database.php`
   - Open in any text editor (VS Code, Notepad++, or Notepad)

2. **Check the configuration**
   ```php
   define('DB_HOST', 'localhost');
   define('DB_USER', 'root');
   define('DB_PASS', '');
   define('DB_NAME', 'ehr_system');
   ```

3. **Modify if needed**
   - **DB_HOST**: Usually `localhost` (don't change)
   - **DB_USER**: Usually `root` (don't change)
   - **DB_PASS**: 
     * WAMP default: Empty `''` (don't change)
     * XAMPP default: Empty `''` (don't change)
     * If you set a password during installation, enter it here
   - **DB_NAME**: Must be `ehr_system` (don't change)

4. **Save the file**

---

## STEP 5: TEST THE INSTALLATION

### Test Database Connection

1. **Create test file**
   - Create a new file: `ehr-system/test.php`
   - Add this code:
   ```php
   <?php
   require_once 'config/database.php';
   
   if ($conn) {
       echo "<h2>✅ SUCCESS! Database connected!</h2>";
       echo "<p>Host: " . DB_HOST . "</p>";
       echo "<p>Database: " . DB_NAME . "</p>";
       
       $result = $conn->query("SHOW TABLES");
       echo "<h3>Tables in database:</h3><ul>";
       while ($row = $result->fetch_array()) {
           echo "<li>" . $row[0] . "</li>";
       }
       echo "</ul>";
   } else {
       echo "<h2>❌ FAILED! Could not connect</h2>";
   }
   ?>
   ```

2. **Run the test**
   - Open browser
   - Go to: http://localhost/ehr-system/test.php
   - You should see: "✅ SUCCESS! Database connected!"
   - You should see list of 4 tables

3. **If successful, delete test file**
   - Delete `ehr-system/test.php`

---

## STEP 6: ACCESS THE EHR SYSTEM

### Open the Application

1. **Start WAMP/XAMPP**
   - Make sure Apache and MySQL are running

2. **Open in browser**
   - Type in address bar: http://localhost/ehr-system/
   - Press Enter

3. **You should see the homepage!** 🎉

---

## USING THE EHR SYSTEM

### First Time Setup

**Option 1: Register New Doctor Account**
1. Click "Register" button
2. Fill in the registration form:
   - First Name, Last Name
   - Email, Username
   - Password (must be strong: 8+ chars, uppercase, lowercase, number, special char)
   - Phone (optional)
   - Specialization (select from dropdown)
   - Medical License Number
3. Click "Register"
4. You'll be redirected to login page
5. Login with your username and password

**Option 2: Use Test Account**
- The database includes a test doctor account:
- **Username**: `johndoe`
- **Password**: `Doctor@123`
- Just click "Login" and enter these credentials

### Main Features

After logging in, you can:

1. **Dashboard**
   - View statistics (total patients, EHR records)
   - Quick actions
   - Recent patients list

2. **Add Patient**
   - Click "Add New Patient"
   - Fill in patient information
   - Upload profile picture (optional)
   - Click "Add Patient"

3. **View Patients**
   - See list of all your patients
   - Search patients by name
   - View, Edit, or Delete patients

4. **Add EHR Record**
   - View a patient
   - Click "Add EHR Record"
   - Fill in the comprehensive EHR form:
     * Vital signs (height, weight, blood pressure)
     * Medical history
     * Allergies (checkboxes)
     * Immunization status (radio buttons)
     * Visit date (date picker)
     * Lab results, diagnosis, treatment plan
     * Upload X-rays and medical documents
   - Click "Save EHR Record"

5. **View EHR Records**
   - View all EHR records for a patient
   - Edit or delete records

6. **Profile**
   - Click your name in top-right corner
   - Select "My Profile"
   - View/edit your doctor profile

7. **Logout**
   - Click your name in top-right corner
   - Select "Logout"

---

## COMMON ISSUES & SOLUTIONS

### Issue 1: "Cannot connect to database"

**Symptoms**: Error message about database connection

**Solutions**:
1. **Check if MySQL is running**
   - WAMP: Icon should be GREEN
   - XAMPP: MySQL should show "Running"

2. **Verify database exists**
   - Go to http://localhost/phpmyadmin
   - Check if `ehr_system` database exists in left sidebar
   - If not, import the SQL file again

3. **Check database credentials**
   - Open `config/database.php`
   - Verify DB_USER is `root`
   - Verify DB_PASS is empty `''`
   - Verify DB_NAME is `ehr_system`

---

### Issue 2: "404 Not Found" when opening http://localhost/ehr-system/

**Symptoms**: Page not found error

**Solutions**:
1. **Verify folder location**
   - WAMP: Must be in `C:\wamp64\www\ehr-system\`
   - XAMPP: Must be in `C:\xampp\htdocs\ehr-system\`

2. **Check folder name**
   - Must be exactly `ehr-system` (lowercase, with hyphen)

3. **Verify Apache is running**
   - WAMP: Check if icon is green
   - XAMPP: Check if Apache shows "Running"

4. **Try this URL**
   - http://localhost/ehr-system/index.php

---

### Issue 3: "Port 80 already in use" - Apache won't start

**Symptoms**: Apache fails to start, error about port 80

**Cause**: Another program (Skype, IIS, etc.) is using port 80

**Solution for WAMP**:
1. Left-click WAMP icon in system tray
2. Tools → Use a port other than 80
3. Enter: `8080`
4. Restart all services
5. Access project at: http://localhost:8080/ehr-system/

**Solution for XAMPP**:
1. Open XAMPP Control Panel
2. Click "Config" next to Apache
3. Select "httpd.conf"
4. Find line: `Listen 80`
5. Change to: `Listen 8080`
6. Save file
7. Restart Apache
8. Access project at: http://localhost:8080/ehr-system/

**Alternative**: Close Skype or disable IIS

---

### Issue 4: "Port 3306 already in use" - MySQL won't start

**Symptoms**: MySQL fails to start

**Solution for WAMP**:
1. Left-click WAMP icon
2. MySQL → Settings → Change port
3. Enter: `3307`
4. Restart services
5. Edit `config/database.php`: Change `DB_HOST` to `localhost:3307`

**Solution for XAMPP**:
1. Open XAMPP Control Panel
2. Click "Config" next to MySQL
3. Select "my.ini"
4. Find: `port=3306`
5. Change to: `port=3307`
6. Save and restart MySQL
7. Edit `config/database.php`: Change `DB_HOST` to `localhost:3307`

---

### Issue 5: WAMP icon stays ORANGE or RED

**Orange** (services loading):
- Wait 30-60 seconds
- Should turn green automatically

**Red** (services not running):
1. Restart all services: WAMP icon → Restart All Services
2. Check port conflicts (see Issue 3 and 4)
3. Install Visual C++ Redistributable:
   - Download: https://aka.ms/vs/17/release/vc_redist.x64.exe
   - Install and restart WAMP

---

### Issue 6: Blank white page (nothing displays)

**Symptoms**: Page loads but shows nothing

**Solution**:
1. **Enable error display**
   - Create file: `ehr-system/debug.php`
   - Add:
   ```php
   <?php
   error_reporting(E_ALL);
   ini_set('display_errors', 1);
   ?>
   ```
   - Include at top of problematic page
   - Refresh page to see actual error

2. **Check file paths**
   - Verify `$base_url` is correct in each file

---

### Issue 7: Images not uploading

**Symptoms**: Error when uploading profile pictures or documents

**Solution**:
1. **Check folder permissions**
   - Right-click `ehr-system/uploads` folder
   - Properties → Security
   - Make sure Users have "Write" permission

2. **Check PHP upload limits**
   - WAMP: Left-click icon → PHP → php.ini
   - XAMPP: XAMPP folder → php → php.ini
   - Find: `upload_max_filesize`
   - Change to: `upload_max_filesize = 10M`
   - Find: `post_max_size`
   - Change to: `post_max_size = 10M`
   - Save and restart Apache

---

## ACCESSING FROM OTHER DEVICES (OPTIONAL)

### Access from Phone/Tablet on Same Network

1. **Find your computer's IP address**
   - Open Command Prompt (CMD)
   - Type: `ipconfig`
   - Look for "IPv4 Address" (e.g., 192.168.1.5)

2. **Access from other device**
   - Connect device to same WiFi network
   - Open browser on device
   - Go to: http://YOUR_IP_ADDRESS/ehr-system/
   - Example: http://192.168.1.5/ehr-system/

3. **Enable access in WAMP**
   - WAMP: Left-click icon → Apache → httpd-vhosts.conf
   - Find: `Require local`
   - Change to: `Require all granted`
   - Restart Apache

---

## BACKUP YOUR DATA

### Backup Database

1. **Using phpMyAdmin**
   - Go to http://localhost/phpmyadmin
   - Click `ehr_system` database
   - Click "Export" tab
   - Click "Go"
   - Save the downloaded .sql file

2. **Backup uploaded files**
   - Copy the entire `ehr-system/uploads/` folder
   - Store in a safe location

---

## TEAM COLLABORATION

### Sharing with Team Members

**Method 1: Share entire folder**
1. Zip the `ehr-system` folder
2. Share via Google Drive, Dropbox, etc.
3. Team member extracts to their www/htdocs folder
4. Each person imports SQL file separately
5. Everyone has their own local copy

**Method 2: Use GitHub**
1. Create GitHub repository
2. Push code (DON'T include `config/database.php` with passwords)
3. Team members clone repository
4. Each person configures their own `database.php`
5. Each person imports SQL file

**Note**: Each team member needs their own local database. Don't share database files directly.

---

## USEFUL LINKS

- **WAMP Official**: https://www.wampserver.com/
- **XAMPP Official**: https://www.apachefriends.org/
- **phpMyAdmin Docs**: https://docs.phpmyadmin.net/
- **PHP Manual**: https://www.php.net/manual/en/

---

## CONTACT & SUPPORT

If you encounter issues not covered in this guide:

1. Check the error message carefully
2. Google the exact error message
3. Check WAMP/XAMPP logs:
   - WAMP: Left-click icon → Logs
   - XAMPP: Click "Logs" buttons in control panel
4. Ask your team members
5. Ask your professor or TA

---

## QUICK REFERENCE

| Action | WAMP | XAMPP |
|--------|------|-------|
| Project Location | `C:\wamp64\www\ehr-system\` | `C:\xampp\htdocs\ehr-system\` |
| Start Services | Double-click WAMP icon | Open Control Panel, Start Apache & MySQL |
| phpMyAdmin | http://localhost/phpmyadmin | http://localhost/phpmyadmin |
| Project URL | http://localhost/ehr-system/ | http://localhost/ehr-system/ |
| Config File | `C:\wamp64\bin\apache\apache2.x.x\bin\httpd.conf` | `C:\xampp\apache\conf\httpd.conf` |
| PHP Config | `C:\wamp64\bin\php\php8.x.x\php.ini` | `C:\xampp\php\php.ini` |
| Error Logs | WAMP icon → Logs | XAMPP → Logs button |

---

## FINAL CHECKLIST

Before starting development, verify:

- [ ] WAMP/XAMPP installed and running
- [ ] Apache is green/running
- [ ] MySQL is green/running
- [ ] Database `ehr_system` created with 4 tables
- [ ] Project in correct folder (www or htdocs)
- [ ] http://localhost/ehr-system/ opens successfully
- [ ] Can register/login as doctor
- [ ] Can add patients
- [ ] Can add EHR records
- [ ] Images upload successfully

If all checkboxes are ✅, you're ready to go! 🎉

---

**Good luck with your EHR System project!**
