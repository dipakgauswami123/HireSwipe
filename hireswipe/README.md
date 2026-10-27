# HireSwipe (frontend only)

PHP, HTML and CSS with dummy data. No database.

Run from this folder: `php -S localhost:8000`, then open http://localhost:8000

Demo logins (password `Hire@123`):
- Employee: `priya.mehta@example.com` or `9824010001`
- Employer: `hr@kirantextiles.example` or `9898100001`

```
index.php               login (landing page)
register.php            choose employee or employer
register-employee.php   employee form
register-employer.php   employer form
employee-home.php       employee sees 16 company requests, swipe right to confirm or left to reject
job-search.php          employee browses open jobs and applies
applied-jobs.php        jobs the employee applied to
employer-home.php       employer sees 24 dummy employees
interview.php           four tabs: scheduled today, scheduled, not scheduled, done (employer and employee)
profile.php             details, edit details and notification settings (employer and employee)
logout.php
includes/               helpers.php, data.php (dummy records), cards.php, head.php, foot.php, brand.php
assets/css/style.css
assets/js/app.js        image preview
```
