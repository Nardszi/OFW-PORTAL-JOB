# OFW Management System

A web-based management system for Overseas Filipino Workers (OFWs) built with PHP and MySQL.

## Features

- OFW registration and profile management
- Job listings and applications
- Benefits and assistance programs with country-based eligibility
- News and announcements
- Case intake management
- Admin dashboard with user management
- Notifications system
- Activity logs

---

## Requirements

- PHP 7.4+
- MySQL 5.7+
- Apache with `mod_rewrite` enabled (XAMPP or WAMP recommended)
- cURL enabled in PHP (`extension=curl` in `php.ini`)

---

## Installation

### Step 1 — Clone the Repository

```bash
git clone https://github.com/Nardszi/OFW-PORTAL-JOB.git
```

Place the cloned folder inside your web server root:
- XAMPP: `C:/xampp/htdocs/`
- WAMP: `C:/wamp64/www/`

---

### Step 2 — Import the Database

1. Open your browser and go to `http://localhost/phpmyadmin`
2. Create a new database (e.g., `ofwmanagement`)
3. Click the database → `Import` tab
4. Select the file `db/ofw_management (3).sql` and click `Go`

---

### Step 3 — Configure the Database

Open `config/database.php` and update with your credentials:

```php
$host = "localhost";         // usually localhost for XAMPP
$user = "root";              // your MySQL username
$pass = "";                  // your MySQL password (blank for XAMPP default)
$db_name = "ofwmanagement";  // the database name you created
```

---

### Step 4 — Set Up the Uploads Folder

Make sure the `uploads/` folder exists and is writable. In XAMPP this is automatic, but if needed run:

```bash
chmod 775 uploads/
```

---

### Step 5 — Enable mod_rewrite (Apache)

The `.htaccess` file handles URL rewriting. Make sure `mod_rewrite` is enabled:

1. Open `C:/xampp/apache/conf/httpd.conf`
2. Find and uncomment this line (remove the `#`):

```
LoadModule rewrite_module modules/mod_rewrite.so
```

3. Also find `AllowOverride None` and change it to `AllowOverride All`
4. Restart Apache

---

### Step 6 — Visit the App

Open your browser and go to:

```
http://localhost/OFW-PORTAL-JOB/
```

---

## Default Roles

| Role  | Access |
|-------|--------|
| Admin | Manage users, jobs, benefits, news, applications |
| OFW   | Apply for jobs and benefits, view news, manage profile |

To create an admin account, register normally then manually update the `role` column in the `users` table to `admin` via phpMyAdmin.

---

## OTP Email Setup

The registration form sends a 6-digit OTP to verify the user's email. Two options are available:

### Option 1 — Brevo API (Recommended, no SMTP needed)

1. Sign up for free at [https://www.brevo.com](https://www.brevo.com)
2. Go to `SMTP & API` → `API Keys` → `Generate a new API key`
3. Open `auth/send_otp.php` and replace `YOUR_BREVO_API_KEY`:

```php
$apiKey = 'your-brevo-api-key-here';
```

4. Update the sender email to your verified Brevo sender address:

```php
'sender' => ['name' => 'OFW Management', 'email' => 'your@email.com'],
```

5. Do the same replacement in:
   - `manage_applications.php` (around line 174)
   - `view_benefit_applications.php` (around line 175)

---

### Option 2 — Gmail SMTP via PHPMailer

1. Install PHPMailer:

```bash
composer require phpmailer/phpmailer
```

2. Go to your Google account → `Security` → `2-Step Verification` → `App Passwords` → generate a password
3. In `auth/send_otp.php`, comment out the Brevo block and uncomment the Gmail SMTP block
4. Fill in your credentials:

```php
$mail->Username = 'your@gmail.com';
$mail->Password = 'your-google-app-password';
```

---

## License

This project is for educational purposes.
