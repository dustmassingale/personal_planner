-- schema.sql
-- Run once via: php db/bootstrap.php
-- Or execute directly: sqlite3 db/app.db < db/schema.sql
--
-- NOTE: "values" is a reserved keyword in SQL — table renamed to "user_values"

CREATE TABLE IF NOT EXISTS user_values (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    name        TEXT    NOT NULL,
    description TEXT,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS roles (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    name        TEXT NOT NULL,
    description TEXT,
    impact      TEXT,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS goals (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    value_id    INTEGER REFERENCES user_values(id) ON DELETE SET NULL,
    category    TEXT    DEFAULT 'Other',
    description TEXT    NOT NULL,
    status      TEXT    DEFAULT 'active' CHECK(status IN ('active','paused','complete')),
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS weekly_plan (
    id          INTEGER PRIMARY KEY AUTOINCREMENT,
    week_start  DATE    NOT NULL,
    day         TEXT    NOT NULL CHECK(day IN ('Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday')),
    social      TEXT,
    spiritual   TEXT,
    physical    TEXT,
    learning    TEXT,
    created_at  DATETIME DEFAULT CURRENT_TIMESTAMP
);
