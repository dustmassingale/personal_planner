# Personal Improvement Devotional — Setup Guide

## Requirements
- PHP 8.0+ with PDO and SQLite extensions enabled
- Apache or Nginx with mod_rewrite
- No Composer, no npm — fully dependency-free

---

## File Structure

```
/
├── index.php                  # SPA shell (only page the browser loads)
├── .htaccess                  # Security rules + routing
│
├── assets/
│   └── js/
│       ├── state.js           # Global AppState object
│       ├── api.js             # fetch() wrapper for all API calls
│       ├── modal.js           # Reusable modal + escHtml() helper
│       └── router.js          # Hash-based SPA router (loaded last)
│
├── components/
│   ├── values.js              # US1 — Define & Refine Personal Values
│   ├── roles.js               # US2 — Identify Roles & Responsibilities
│   ├── goals.js               # US3 — Goals Aligned With Values
│   ├── weekly_plan.js         # US4 — Guided Weekly Planning
│   └── export.js              # US5 — Export to .ics Calendar
│
├── api/
│   ├── _helpers.php           # Shared: headers, sanitize(), jsonOut()
│   ├── values.php             # GET / POST / DELETE user_values
│   ├── roles.php              # GET / POST / DELETE roles
│   ├── goals.php              # GET / POST / DELETE goals
│   ├── weekly_plan.php        # GET / POST / DELETE weekly_plan
│   └── export.php             # GET → downloads .ics file
│
└── db/
    ├── connection.php         # PDO bootstrap (included by API files)
    ├── schema.sql             # Table definitions
    ├── bootstrap.php          # One-time DB init script
    └── app.db                 # SQLite database (auto-created, git-ignore this)
```

---

## First-Time Setup

### 1. Create the database
```bash
php db/bootstrap.php
```
This creates `db/app.db` and applies `schema.sql`.

### 2. Set directory permissions (Linux/Mac)
```bash
chmod 775 db/
chmod 664 db/app.db
```
The web server user (e.g. `www-data`) must be able to write to `db/`.

### 3. Configure Apache virtual host
```apache
<VirtualHost *:80>
    DocumentRoot /var/www/devotional
    DirectoryIndex index.php
    <Directory /var/www/devotional>
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```
Enable mod_rewrite: `sudo a2enmod rewrite && sudo systemctl restart apache2`

### 4. Protect the db/ directory (Nginx alternative to .htaccess)
```nginx
location ~ ^/(db|api/_) {
    deny all;
    return 403;
}
```

---

## Key Design Decisions vs. Copilot's Original

| Issue | Copilot | Fixed |
|---|---|---|
| SQL reserved word | `CREATE TABLE values` → crashes | Renamed to `user_values` |
| XSS | Raw innerHTML from DB | `escHtml()` on all output |
| Missing JSON headers | Not sent | Set in `_helpers.php` |
| CSRF | None | `X-Requested-With` check |
| Missing API files | Only `values.php` | All 5 endpoints provided |
| `prompt()` UX | Used everywhere | Replaced with modal forms |
| No `week_start` | Never set | Computed from current Monday |
| No `.ics` generation | Dead link | Full RFC 5545 export |
| `schema.sql` never runs | No bootstrap | `db/bootstrap.php` provided |
| Race condition in router | Loaded before components | Router loaded last |
| No error handling | Silent failures | Try/catch + UI error messages |
| No DELETE support | Read + create only | DELETE on all endpoints |
| `value_id` linkage | Not implemented | Goals → Values join + dropdown |

---

## Adding Features

### Next logical steps per the user story plan:
- **US1.1 Values Wizard** — multi-step modal for guided value discovery
- **Goal status toggle** — click badge to cycle active → paused → complete
- **Weekly plan templates** — pre-fill common patterns per value set
- **Memory / reflection journal** — `memory.json` or `journal` table
- **Authentication** — single-user PIN or PHP session before multi-user support
