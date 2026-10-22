# StudentHub — Campus Engagement & Events Platform

**Student:** Shivam Shah | **Roll No:** 25IT093  
**Subject:** Web Technology Lab | **Practicals:** 7–15

## Tech Stack
- **Frontend:** HTML5, CSS3, Vanilla JavaScript
- **Backend:** PHP (MySQLi)
- **Database:** MySQL (`studenthub`)
- **Server:** XAMPP (Apache + MySQL)

---

## Practical Summary

| # | Practical | Files |
|---|-----------|-------|
| P7 | PHP Form Processing (POST, sanitize, JSON store) | `php/process_register.php`, `php/process_contact.php` |
| P8 | MySQL Schema Design | `studenthub.sql` |
| P9 | Secure User Registration (MySQLi + password_hash) | `php/process_register.php`, `php/db.php` |
| P10 | Secure Login/Logout + Session Timeout | `php/login.php`, `php/logout.php`, `php/auth.php`, `pages/dashboard.php`, `pages/profile.php` |
| P11 | Student Management CRUD (search, filter, feedback) | `pages/admin/students.php` |
| P12 | Event Management CRUD + Poster Image Upload | `pages/admin/events.php`, `uploads/events/` |
| P13 | PHP JSON API + AJAX/Fetch CRUD | `php/api/events.php`, `php/api/students.php`, `pages/ajax_manager.php` |
| P14 | Admin Dashboard (RBAC + dynamic stats) | `pages/admin/index.php` |
| P15 | Final Integration (nav links, session, validation) | All pages updated |

---

## Project Structure
```
STUDENTHUB/
├── css/              # Stylesheets (style.css, style2-11.css)
├── data/             # Static JSON data files
├── images/           # Logo, favicon, assets
├── js/script.js      # Frontend JS (validation, events fetch, FAQ)
├── pages/            # Frontend HTML/PHP pages
│   ├── admin/        # Admin-only PHP pages (RBAC protected)
│   │   ├── index.php       # Admin dashboard
│   │   ├── students.php    # Student CRUD
│   │   └── events.php      # Event CRUD + image upload
│   ├── dashboard.php       # Student dashboard (session-protected)
│   ├── profile.php         # User profile + password change
│   └── ajax_manager.php    # AJAX/Fetch API demo (P13)
├── php/              # Backend PHP modules
│   ├── db.php              # MySQLi connection
│   ├── auth.php            # Session guard (30-min timeout)
│   ├── auth_admin.php      # Admin role guard
│   ├── login.php           # Login processor
│   ├── logout.php          # Session destroy
│   ├── process_register.php
│   ├── process_contact.php
│   └── api/
│       ├── events.php      # REST JSON API for events
│       └── students.php    # REST JSON API for students
├── uploads/events/   # Event poster images (gitignored)
└── studenthub.sql    # MySQL schema + seed data
```

## Setup
1. Import `studenthub.sql` into phpMyAdmin
2. Start Apache + MySQL in XAMPP
3. Visit `http://localhost/STUDENTHUB/pages/`
4. Admin login: `admin@studenthub.edu` / `password`