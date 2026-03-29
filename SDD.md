# System Design Document (SDD)

## Overview
This document describes the internal technical architecture of the **Personal Improvement Devotional** app. It is intended for developers and covers data models, API design, component architecture, technology stack, and deployment strategy. For product features and user requirements, refer to `PRD.md`. For user interface flows, refer to `UX_SPEC.md`.

---

## Table of Contents
1. [Technology Stack](#1-technology-stack)
2. [Data Models](#2-data-models)
3. [File & Component Architecture](#3-file--component-architecture)
4. [API Design](#4-api-design)
5. [Data Storage](#5-data-storage)
6. [Deployment Strategy](#6-deployment-strategy)
7. [Security Considerations](#7-security-considerations)

---

## 1. Technology Stack

| Layer | Technology |
|---|---|
| **Server** | PHP 8 |
| **Database** | SQLite3 via PDO |
| **Frontend Framework** | W3.CSS |
| **Markup** | HTML5 |
| **Styling** | CSS3 |
| **Scripting** | JavaScript (ES6+) |
| **Deployment** | FTP via `deploy.bat` |

> **Note:** SQLite3 via PDO requires the `pdo_sqlite` extension to be enabled in the server's `php.ini`. Local environment configuration may be required.

---

## 2. Data Models

The following data models represent the core entities of the application. These will map to SQLite3 tables.

### 2.1 Values
```json
{
  "id": 1,
  "name": "Integrity",
  "description": "Act with honesty and transparency in all situations."
}
```
| Field | Type | Description |
|---|---|---|
| `id` | INTEGER | Primary key, auto-increment |
| `name` | TEXT | Name of the value |
| `description` | TEXT | Description of the value |

### 2.2 Roles
```json
{
  "id": 1,
  "name": "Parent",
  "description": "Support and nurture children.",
  "impact": "Provide emotional and physical support to children."
}
```
| Field | Type | Description |
|---|---|---|
| `id` | INTEGER | Primary key, auto-increment |
| `name` | TEXT | Name of the role |
| `description` | TEXT | Description of the role |
| `impact` | TEXT | How this role impacts others |

### 2.3 Goals
```json
{
  "id": 1,
  "value_id": 1,
  "category": "Spiritual",
  "description": "Spend 10 minutes daily in prayer.",
  "status": "Active"
}
```
| Field | Type | Description |
|---|---|---|
| `id` | INTEGER | Primary key, auto-increment |
| `value_id` | INTEGER | Foreign key linking to a Value |
| `category` | TEXT | One of: Social, Spiritual, Physical, Learning/Mental |
| `description` | TEXT | Description of the goal |
| `status` | TEXT | One of: Active, Completed, Paused |

### 2.4 Weekly Plan
```json
{
  "id": 1,
  "week_start": "2026-03-28",
  "day": "Monday",
  "social": "Call a friend",
  "spiritual": "Morning prayer",
  "physical": "30-min workout",
  "learning": "Read 10 pages"
}
```
| Field | Type | Description |
|---|---|---|
| `id` | INTEGER | Primary key, auto-increment |
| `week_start` | DATE | The start date (Sunday) of the week |
| `day` | TEXT | Day of the week |
| `social` | TEXT | Planned social activity |
| `spiritual` | TEXT | Planned spiritual activity |
| `physical` | TEXT | Planned physical activity |
| `learning` | TEXT | Planned learning/mental activity |

---

## 3. File & Component Architecture

Each component will have its own dedicated file. The project structure is as follows:

```
/personal_planner
│
├── index.php                  # App entry point and routing
├── deploy.bat                 # FTP deployment script
├── .gitignore                 # Git ignore rules
│
├── /api                       # PHP API endpoint files
│   ├── values.php
│   ├── goals.php
│   ├── roles.php
│   └── weekly_plan.php
│
├── /components                # Reusable HTML/PHP UI components
│   ├── header.php
│   ├── footer.php
│   ├── nav.php
│   └── wizard.php
│
├── /pages                     # Individual page views
│   ├── values.php
│   ├── goals.php
│   ├── weekly_plan.php
│   ├── roles.php
│   └── export.php
│
├── /db                        # Database connection and initialization
│   ├── connection.php
│   └── schema.sql
│
├── /memory                    # Local runtime data store (not committed)
│   └── memory.json
│
└── /assets                    # Static assets
    ├── /css
    └── /js

### 3.1 Separation of Markup and Behavior

- **Goal:** Keep HTML markup and JavaScript behavior separate to improve maintainability, testability, and reusability.
- **Pattern:** Store static HTML fragments in `components/partials/*.html`. Place all interactive behavior in `assets/js/*.js` (or `components/*.js` for component-specific logic).
- **Event-driven:** Prefer custom events and data-attributes for communication between markup and scripts rather than inline `onclick` handlers. Example: partials emit or listen for a `showAddRole` event; script attaches listeners on DOMContentLoaded.
- **Benefits:** Easier unit testing of JS, simpler visual edits to markup, clearer separation of concerns for future refactors or server-side rendering.

```

---

## 4. API Design

All API endpoints are PHP files located in the `/api` directory. They accept and return **JSON**. HTTP methods follow REST conventions.

| Endpoint | Method | Description |
|---|---|---|
| `/api/values.php` | GET | Retrieve all values |
| `/api/values.php` | POST | Create a new value |
| `/api/values.php` | PUT | Update an existing value |
| `/api/values.php` | DELETE | Delete a value |
| `/api/goals.php` | GET | Retrieve all goals |
| `/api/goals.php` | POST | Create a new goal |
| `/api/goals.php` | PUT | Update an existing goal |
| `/api/goals.php` | DELETE | Delete a goal |
| `/api/roles.php` | GET | Retrieve all roles |
| `/api/roles.php` | POST | Create a new role |
| `/api/roles.php` | PUT | Update an existing role |
| `/api/roles.php` | DELETE | Delete a role |
| `/api/weekly_plan.php` | GET | Retrieve weekly plan |
| `/api/weekly_plan.php` | POST | Save a weekly plan |
| `/api/weekly_plan.php` | PUT | Update a weekly plan entry |

---

## 5. Data Storage

### Primary Storage: SQLite3
- The primary data store is a **SQLite3** database accessed via **PHP PDO**.
- The database schema is defined in `/db/schema.sql`.
- The database file will be stored on the server and excluded from version control via `.gitignore`.

### Secondary Storage: memory.json
- `memory/memory.json` serves as a **local runtime cache** and development scratchpad.
- It is **excluded from Git** via `.gitignore` and should never be committed.
- It mirrors the structure of the SQLite3 database for local testing without a live server.

---

## 6. Deployment Strategy

- Deployment is handled via **FTP** using a `deploy.bat` script.
- The script will upload all relevant files to the production server, excluding files listed in `.gitignore`.
- The `memory/memory.json` file and the SQLite3 database file will **not** be deployed via the script — they are managed on the server directly.

**Deployment Checklist:**
- [ ] Confirm `php.ini` has `pdo_sqlite` enabled on the server
- [ ] Confirm `/db/schema.sql` has been run on the server
- [ ] Confirm `memory/memory.json` is excluded from deployment
- [ ] Confirm FTP credentials are set in `deploy.bat` (not committed to Git)

---

## 7. Security Considerations

- **FTP Credentials**: Store FTP credentials only in `deploy.bat` locally. This file should be added to `.gitignore`.
- **SQLite Database File**: The `.db` file must not be publicly accessible. Store it outside the web root or protect it with server rules.
- **Input Validation**: All API endpoints must validate and sanitize user input before writing to the database.
- **PDO Prepared Statements**: All database queries must use PDO prepared statements to prevent SQL injection.