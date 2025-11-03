# GreenGrow – Urban Gardening & Plant Care Guide

**Deadline-ready build:** Core features implemented in plain PHP/MySQL/HTML/CSS/JS.

## Quick Start (Localhost)

1. Create a MySQL database named `greengrow` (phpMyAdmin recommended).
2. Import `schema.sql` into `greengrow`.
3. Copy this folder to your PHP server root (e.g., `htdocs/greengrow` or `www/greengrow`).  
4. Edit `config.php` with your DB credentials, `SITE_URL`, and admin email.
5. Visit `2025-08-24`: `http://localhost/greengrow/index.php`

### Admin Access
The account whose **email matches `ADMIN_EMAIL`** in `config.php` automatically gains admin rights after logging in. Register using that email and log in.

### Email Reminders
- Edit `FROM_EMAIL` in `config.php`.
- Set a daily cron:  
  `php /absolute/path/to/greengrow/cron_send_reminders.php`  
  On shared hosting, use the provider's cron UI.
- The script sends reminders due **today**, marks them sent, and schedules the next one using each plant's saved frequency (daily/weekly).

### Images
Upload plant images in **Admin → Manage Plants**. Files are stored in `/uploads` and the filename saved in DB.

## Features Implemented (Core)
- Homepage with mission + **Featured Plant of the Day**.
- Category navigation (Indoor, Outdoor, Herbs, Succulents).
- Category-wise plant listing and **detailed plant pages** with:
  - Name, scientific name
  - Watering schedule
  - Sunlight
  - Soil type
  - Common pests & remedies
- User Registration & Login (password hashing, sessions).
- **Personalized Plant Care Reminders**: add plants to profile, choose daily/weekly frequency, upcoming reminders list.
- **Admin Panel**: add/edit/delete plants (with image upload), manage users, view reminders.

## Nice-to-haves (stubs you can extend)
- Blog, comments, social sharing (not required by core spec).

## Security Notes
- Minimal CSRF protections. For production, add CSRF tokens and stronger validation.
- Uses PHP `mail()`; configure SMTP (e.g., sendmail/ssmtp) if localhost doesn't deliver.

## Database Tables (per spec)
- `users` → ID, Name, Email, Password
- `plants` → ID, Name, Scientific_Name, Category, Watering_Schedule, Sunlight, Soil_Type, Pest_Info, Image
- `user_plants` → ID, User_id, Plant_id, Added_Date, Frequency
- `reminders` → ID, User_id, Plant_id, Reminder_Date, Sent

---

© 2025 GreenGrow


## Community Contributions (This build)
- Any logged-in user can **Add a Plant** and edit **their own** plants from **My Contributions**.
- Each plant has a **Public** toggle. Only public plants appear in listings/featured sections.
- Owner or Admin can edit a plant; everyone can **view** public plant details.

### Migration
In phpMyAdmin, run `schema_migration_community.sql` after importing `schema.sql`.


## Anonymous Contributions + Moderation
- `/contribute_public.php` lets **anyone** submit a plant without logging in.
- Submissions are saved as **pending** and **not public**.
- Admin reviews in **Admin → Moderate**; Approve (makes public) or Reject.
- Simple math **CAPTCHA** prevents spam bots.

### Migration
After previous migrations, run `schema_migration_anonymous.sql` in phpMyAdmin.
