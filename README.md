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

## License

This project is for educational purposes.
