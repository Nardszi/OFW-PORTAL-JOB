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

## Requirements

- PHP 7.4+
- MySQL 5.7+
- Apache (XAMPP / WAMP / any web server with mod_rewrite enabled)

## Setup

1. Clone the repository into your web server's root directory (e.g., `htdocs` for XAMPP).
2. Import the database schema from `db/ofw_management (3).sql` into your MySQL server.
3. Update the database credentials in `config/database.php`:

```php
$host = "your_host";
$user = "your_username";
$pass = "your_password";
$db_name = "your_database_name";
```

4. Make sure the `uploads/` directory is writable by the web server.
5. Visit `http://localhost/` in your browser.

## Default Roles

- **Admin** – Manages users, jobs, benefits, news, and applications.
- **OFW** – Can apply for jobs and benefits, view news, and manage their profile.

## OTP Email Setup

The OTP system uses the [Brevo](https://www.brevo.com) (formerly Sendinblue) API to send verification emails. Follow these steps to get it working:

### Option 1: Brevo API (Recommended)

1. Go to [https://www.brevo.com](https://www.brevo.com) and create a free account.
2. Navigate to `SMTP & API` → `API Keys` → click `Generate a new API key`.
3. Copy the key and open `auth/send_otp.php`.
4. Replace `YOUR_BREVO_API_KEY` on line 27 with your actual key:

```php
$apiKey = 'your-actual-api-key-here';
```

5. Also update the sender email in the same file to match your verified Brevo sender:

```php
'sender' => ['name' => 'OFW Management', 'email' => 'your@email.com'],
```

6. Do the same in `manage_applications.php` and `view_benefit_applications.php`.

### Option 2: Gmail SMTP (PHPMailer)

1. Install PHPMailer via Composer:

```bash
composer require phpmailer/phpmailer
```

2. Go to your Google account → `Security` → `2-Step Verification` → `App Passwords` and generate a password.
3. In `auth/send_otp.php`, comment out the Brevo block and uncomment the Gmail SMTP block.
4. Replace the credentials:

```php
$mail->Username = 'your@gmail.com';
$mail->Password = 'your-app-password';
```

## License

This project is for educational purposes.
