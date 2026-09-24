# CWebS: Custom Website Builder and CMS for Public Schools

[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-777BB4?logo=php&logoColor=white)](https://www.php.net/)
[![Database](https://img.shields.io/badge/MySQL-MariaDB-4479A1?logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Security](https://img.shields.io/badge/Security-Hardened-10B981?logo=shield&logoColor=white)](#security-standards--hardening)
[![Architecture](https://img.shields.io/badge/RBAC-Multi--Tenant-4F46E5)](#role-based-access-control-rbac)

**CWebS** (**C**ustom **Web**site Builder and CMS for Public **S**chools) is a multi-tenant content management platform engineered specifically for public educational institutions and school districts. It empowers district administrators, school principals, faculty, and content editors to manage institution records, provision staff permissions, and customize public-facing school portals through an accessible live visual CMS.

---

## Table of Contents
- [Problem Domain & Mission](#problem-domain--mission)
- [Architecture & Tech Stack](#architecture--tech-stack)
- [Role-Based Access Control (RBAC)](#role-based-access-control-rbac)
- [Security Standards & Hardening](#security-standards--hardening)
- [Directory Structure](#directory-structure)
- [Local Development Setup](#local-development-setup)
- [Default Seed Accounts](#default-seed-accounts)
- [Database Schema](#database-schema)

---

## Problem Domain & Mission

Public schools often lack the dedicated IT engineering staff and budget required to deploy, maintain, and secure disparate web applications. CWebS solves this by offering:
1. **Centralized District Multi-Tenancy**: A single unified database serving multiple independent school profiles.
2. **Granular Separation of Concerns**: Strict tier boundaries between central district overseers (`SuperAdmin`), school-level administrators (`Admin`), and faculty publishers (`Editor`).
3. **Low-Friction Visual Customization**: An intuitive, zero-code live web canvas editor for school announcements, banners, color schemes, and academic calendars.

---

## Architecture & Tech Stack

* **Backend Engine**: Pure modern PHP 8.x (no bloated framework overhead; procedural & object-oriented database access).
* **Database**: MySQL 5.7+ / MariaDB 10.4+ (`utf8mb4` encoding with relational foreign key cascading).
* **Session Layer**: Hardened PHP native sessions configured with strict cookie flags (`HttpOnly`, `SameSite=Lax`, conditional `Secure`).
* **Frontend**: Responsive CSS3 (Custom Properties / Flexbox / Grid) with FontAwesome 6 icons and Inter typography.
* **Configuration Layer**: Isolated environment loader supporting `.env` parsing with safe fallbacks.

---

## Role-Based Access Control (RBAC)

CWebS enforces a hierarchical permission model across all endpoints via `require_role()` guards:

```
                  ┌────────────────────────────────────────┐
                  │              SuperAdmin                │
                  │   District-wide master administrator   │
                  └───────────────────┬────────────────────┘
                                      │
                   ┌──────────────────┴──────────────────┐
                   ▼                                     ▼
        ┌─────────────────────┐               ┌─────────────────────┐
        │        Admin        │               │       Editor        │
        │ School-level admin  │               │ Faculty / Publisher │
        └──────────┬──────────┘               └─────────────────────┘
                   │
                   ▼
        ┌─────────────────────┐
        │  School Management  │
        │  Faculty & Teachers │
        └─────────────────────┘
```

| Privilege / Route | SuperAdmin | Admin | Editor |
| :--- | :---: | :---: | :---: |
| **Manage Schools** (`school_management.php`, `add_school.php`, `edit_school.php`, `delete_school.php`) |  Full Access |  No Access |  No Access |
| **System Settings** (`systemsettings.php`) |  Full Access |  No Access |  No Access |
| **Manage Users** (`user_management.php`, `add_user.php`, `edit_user.php`, `delete_user.php`) |  All Users |  School-Scoped |  No Access |
| **Site Settings & Themes** (`sitesettings.php`, `webpagedesign.php`) |  Full Access |  Assigned School |  No Access |
| **Visual CMS Editor** (`edit_webpage.php`) |  Full Access |  Full Access |  Content Only |
| **Personal Profile** (`account.php`) |  Self-Service |  Self-Service |  Self-Service |

---

## Security Standards & Hardening

The codebase has undergone a principal security audit and refactor adhering to OWASP Top 10 recommendations:

1. **Prepared Parameterized SQL Queries**:
   - **Zero raw string interpolation**: All CRUD modules utilize parameterized `mysqli::prepare()` statements with bound parameters (`$stmt->bind_param(...)`).
   - Completely eliminates SQL injection vectors across authentication, profile updates, and record deletion.

2. **Session Security & Fixation Defense**:
   - `session_regenerate_id(true)` is executed immediately upon successful authentication in `login.php` to prevent session fixation attacks.
   - Session cookies are strictly locked via `session_set_cookie_params()`:
     * `httponly = true` (mitigates document cookie extraction via XSS).
     * `samesite = 'Lax'` (mitigates Cross-Site Request Forgery across navigation).
     * `secure = true` (automatically enabled over HTTPS).

3. **Cross-Site Scripting (XSS) Prevention**:
   - All dynamic output rendered into HTML attributes and document text is sanitized through `htmlspecialchars($val, ENT_QUOTES, 'UTF-8')` via the global `e()` utility helper.

4. **CSRF Mitigation**:
   - State-changing actions (record creations, edits, password updates, and deletions) are guarded with cryptographically secure, session-bound CSRF tokens verified via `hash_equals()`.
   - Deletions are triggered via standard `POST` forms with confirmation dialogs rather than naked `GET` URLs.

5. **Insecure Direct Object Reference (IDOR) Hardening**:
   - Self-service endpoints such as `account.php` strictly bind to `$_SESSION['user_id']`, rejecting arbitrary `?user_id=` query parameters.
   - Self-deletion is explicitly blocked in `delete_user.php`.

6. **Password Security**:
   - Passwords are securely hashed using PHP's native `password_hash($password, PASSWORD_DEFAULT)` (Bcrypt) and verified using `password_verify()`.
   - Cleartext or hashed passwords are never stored in session memory.

---

## Directory Structure

```text
CWebS/
├── .env.example              # Environment variables template
├── .env                      # Local environment configuration (Git-ignored)
├── .gitignore                # Git ignore patterns
├── README.md                 # Project documentation
├── accounts.sql              # MySQL database schema and seed data
├── connect.php               # Database connection factory & charset configuration
│
├── includes/                 # Core framework modules & helpers
│   ├── config.php            # Environment loader & configuration registry
│   ├── auth.php              # Session lifecycle, RBAC guards, CSRF & XSS helpers
│   ├── flash.php             # Session alert & notification banner helper
│   ├── layout_header.php     # Unified semantic HTML layout header & topbar
│   ├── layout_footer.php     # Unified semantic HTML layout footer
│   └── sidebar.php           # Role-filtered responsive navigation sidebar
│
├── home.php                  # Public landing page with hero and feature showcase
├── login.php                 # Hardened authentication gateway
├── logout.php                # Session destruction & cookie invalidation gateway
├── dashboard_nav.php         # Main role dashboard hub & activity metrics
├── account.php               # Self-service profile & password management
│
├── school_management.php     # [SuperAdmin] Public schools directory
├── add_school.php            # [SuperAdmin] School registration form
├── edit_school.php           # [SuperAdmin] School updater
├── delete_school.php         # [SuperAdmin] School deletion gateway (CSRF protected)
│
├── user_management.php       # [SuperAdmin/Admin] Staff accounts directory
├── add_user.php              # [SuperAdmin/Admin] User provisioning form
├── edit_user.php             # [SuperAdmin/Admin] User details & password reset
├── delete_user.php           # [SuperAdmin/Admin] User deletion gateway
│
├── edit_webpage.php          # Visual live-canvas school webpage CMS editor
├── webpagedesign.php         # Theme, color scheme, and template selector
├── sitesettings.php          # School institution public metadata settings
├── systemsettings.php        # Platform-wide tenant quota and maintenance settings
└── style.css                 # Responsive stylesheet (Flexbox, Grid, DataTables)
```

---

## Local Development Setup

### Prerequisites
- **PHP**: Version 8.0 or newer (CLI or Web Server).
- **MySQL / MariaDB**: MySQL 5.7+ or MariaDB 10.4+ (e.g. via XAMPP, Laragon, Docker, or native service).

### Step 1: Clone Repository & Create Environment Config
```bash
git clone https://github.com/Ikkoni/CWebS.git
cd CWebS

# Copy the environment template
copy .env.example .env     # Windows (cmd/PowerShell)
# cp .env.example .env     # Linux/macOS
```

Open `.env` in your editor and configure your database credentials:
```ini
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=accounts
DB_USER=root
DB_PASS=
```

### Step 2: Import Database Schema & Seed Data
Create the database and import `accounts.sql` using MySQL CLI or phpMyAdmin:

```bash
# Using MySQL CLI
mysql -u root -p -e "CREATE DATABASE IF NOT EXISTS accounts CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;"
mysql -u root -p accounts < accounts.sql
```

### Step 3: Start Local Development Server
You can run CWebS directly using PHP's built-in development web server:

```bash
php -S localhost:8000
```

Open your browser and navigate to:
```
http://localhost:8000/home.php
```

*(Alternatively, place the project folder into your web root e.g. `C:\xampp\htdocs\CWebS` and access `http://localhost/CWebS/home.php`)*.

---

## Default Seed Accounts

The initial database import (`accounts.sql`) provisions the following default accounts:

| Role | Email | Password | Scope |
| :--- | :--- | :--- | :--- |
| **SuperAdmin** | `superadmin@gmail.com` | `123` | District-wide access |
| **Admin** | `admin@gmail.com` | `123` | School-level administration |

> **Note**: To reset or create a known password for any test account, run:
> ```bash
> php -r "echo password_hash('Admin123!', PASSWORD_DEFAULT) . PHP_EOL;"
> ```
> And update the `user_password` hash in the `users` table.

---

## Database Schema

```sql
school (
  school_id INT PRIMARY KEY AUTO_INCREMENT,
  school_name VARCHAR(220) NOT NULL,
  school_address VARCHAR(220) NOT NULL,
  school_contact_number INT NOT NULL,
  school_email VARCHAR(200) UNIQUE NOT NULL
);

users (
  user_id INT PRIMARY KEY AUTO_INCREMENT,
  user_first_name VARCHAR(200) NOT NULL,
  user_last_name VARCHAR(225) NOT NULL,
  user_email VARCHAR(100) UNIQUE NOT NULL,
  user_password VARCHAR(225) NOT NULL,
  user_role VARCHAR(220) NOT NULL,
  user_department VARCHAR(200) NOT NULL,
  user_position VARCHAR(220) NOT NULL,
  user_contact_number INT NOT NULL,
  user_address VARCHAR(220) NOT NULL,
  school_id INT NULL,
  FOREIGN KEY (school_id) REFERENCES school(school_id) ON DELETE CASCADE
);
```

---

## License

This software is developed for public educational institutions and is distributed under the MIT License.
