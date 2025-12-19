# DATABASE CONFIGURATION EXPLANATION

## What is this file?
`database.php` is the **bridge** between your PHP code and MySQL database. Every PHP file that needs to access the database will include this file.

---

## How it works - Step by Step

### Step 1: Define Database Credentials
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'ehr_system');
```

**What this does**:
- `DB_HOST`: Where is your MySQL server? Usually 'localhost' (same computer)
- `DB_USER`: MySQL username (XAMPP default is 'root')
- `DB_PASS`: MySQL password (XAMPP default is empty '')
- `DB_NAME`: Which database to use (must match our SQL file: 'ehr_system')

**Why use `define()`?**
- Creates CONSTANTS that cannot be changed accidentally
- Can be used anywhere in the code after this file is included

---

### Step 2: Create Connection
```php
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
```

**What this does**:
- Creates a new MySQL connection object called `$conn`
- Uses the credentials we defined above
- `mysqli` = MySQL Improved (newer, more secure than old mysql)

**The `$conn` variable**:
- This is your "phone line" to the database
- You'll use it in every database query
- Example: `$conn->query("SELECT * FROM doctors")`

---

### Step 3: Check Connection
```php
if ($conn->connect_error) {
    die("Database Connection Failed: " . $conn->connect_error);
}
```

**What this does**:
- Checks if connection was successful
- If it FAILED: Stop execution (`die()`) and show error message
- If it WORKED: Continue to next line

**Why this is important**:
- Without a working connection, nothing will work
- Error message helps you debug (e.g., wrong password, database doesn't exist)

---

### Step 4: Set Character Set
```php
$conn->set_charset(DB_CHARSET);
```

**What this does**:
- Sets encoding to UTF-8 (utf8mb4)
- Allows proper storage of special characters (é, ñ, ü) and emojis

**Why this matters**:
- Without this, patient names like "José García" might show as "Jos� Garc�a"
- utf8mb4 supports all languages and emojis

---

### Step 5: Timezone
```php
date_default_timezone_set('Europe/Berlin');
```

**What this does**:
- Sets the timezone for PHP date/time functions
- All timestamps will use this timezone

**Why you need this**:
- When you save `created_at` timestamp, it uses correct local time
- Prevents time confusion (e.g., record created at "wrong" hour)

---

## How to use this file in other PHP files

In ANY PHP file that needs database access, add this at the top:

```php
<?php
require_once '../config/database.php';

// Now you can use $conn to query database
$result = $conn->query("SELECT * FROM doctors");
?>
```

**Important**: The path `../config/database.php` depends on where your file is located:
- If file is in `modules/auth/`, use: `../../config/database.php`
- If file is in root directory, use: `config/database.php`

---

## Setup Instructions (IMPORTANT!)

### For WAMP/XAMPP Users:
1. Make sure WAMP or XAMPP is running
2. **XAMPP**: Start Apache and MySQL in XAMPP Control Panel
   **WAMP**: Wait for WAMP icon to turn GREEN in system tray
3. Open phpMyAdmin: http://localhost/phpmyadmin
4. Run the `ehr_database.sql` file to create database
5. The default settings in this file should work:
   - Host: localhost
   - User: root
   - Password: (empty)
6. **File Location**: 
   - XAMPP: `C:\xampp\htdocs\ehr-system`
   - WAMP: `C:\wamp64\www\ehr-system` (or `C:\wamp\www\ehr-system`)
7. **Access URL**: http://localhost/ehr-system/

### If you get "Connection Failed" error:

**Check these things**:
1. Is MySQL running in WAMP/XAMPP?
2. Is the database name correct? (`ehr_system`)
3. Did you run the SQL file to create the database?
4. Is your MySQL password different? (change `DB_PASS` if needed)
5. Check phpMyAdmin access at http://localhost/phpmyadmin

---

## Security Notes

### Current setup (for development):
- ✅ Uses mysqli (secure)
- ✅ Constants prevent accidental changes
- ✅ Error messages help debugging

### For production (real hospital use):
- ❌ Don't use 'root' user (create specific user with limited permissions)
- ❌ Don't leave password empty (set strong password)
- ❌ Don't display error messages to users (log them instead)
- ❌ Don't commit this file to public GitHub (use .env file)

**But for your university project, the current setup is PERFECT!**

---

## Testing the Connection

Want to test if it works?

1. Create a test file: `test_connection.php` in root folder (ehr-system directory)
2. Add this code:
```php
<?php
require_once 'config/database.php';

if ($conn) {
    echo "✅ Database connected successfully!<br>";
    echo "Database name: " . DB_NAME . "<br>";
    echo "Host: " . DB_HOST . "<br>";
    echo "User: " . DB_USER . "<br>";
    
    // Test if our database exists
    $result = $conn->query("SHOW TABLES");
    if ($result) {
        echo "<br>Tables in database:<br>";
        while ($row = $result->fetch_array()) {
            echo "- " . $row[0] . "<br>";
        }
    }
} else {
    echo "❌ Connection failed!";
}
?>
```
3. Open in browser: http://localhost/ehr-system/test_connection.php
4. You should see: "✅ Database connected successfully!" and a list of tables

**Expected Output:**
```
✅ Database connected successfully!
Database name: ehr_system
Host: localhost
User: root

Tables in database:
- doctors
- patients
- ehr_records
- login_attempts
```

---

## Common Errors and Solutions

### Error: "Unknown database 'ehr_system'"
**Solution**: You didn't run the SQL file yet. Go to phpMyAdmin and run `ehr_database.sql`

### Error: "Access denied for user 'root'@'localhost'"
**Solution**: Your MySQL has a password. Add it to `DB_PASS` constant

### Error: "Call to undefined function mysqli_connect()"
**Solution**: PHP mysqli extension is not enabled. Enable it in php.ini

---

## What's Next?

After this config file, we'll create:
1. **Session management** (keep users logged in)
2. **Registration page** (uses this $conn to INSERT into doctors table)
3. **Login page** (uses this $conn to SELECT from doctors table)
4. **Dashboard** (uses this $conn to get patient data)

Every single page will start with:
```php
require_once 'config/database.php';
```

This is the **foundation** of your entire EHR system!
