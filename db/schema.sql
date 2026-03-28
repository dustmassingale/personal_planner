-- SQLite schema for Personal Planner
CREATE TABLE IF NOT EXISTS values (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    description TEXT,
    selected INTEGER DEFAULT 0
);

CREATE TABLE IF NOT EXISTS weekly_plan (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    week_start DATE NOT NULL,
    day TEXT NOT NULL,
    social TEXT,
    spiritual TEXT,
    physical TEXT,
    learning TEXT
);
CREATE TABLE IF NOT EXISTS values_table (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    description TEXT
);

CREATE TABLE IF NOT EXISTS roles_table (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name TEXT NOT NULL,
    description TEXT,
    impact TEXT
);

CREATE TABLE IF NOT EXISTS goals_table (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    value_id INTEGER,
    category TEXT,
    description TEXT,
    status TEXT DEFAULT 'Active',
    FOREIGN KEY (value_id) REFERENCES values_table(id)
);

CREATE TABLE IF NOT EXISTS weekly_plan_table (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    week_start DATE,
    day TEXT,
    social TEXT,
    spiritual TEXT,
    physical TEXT,
    learning TEXT
);