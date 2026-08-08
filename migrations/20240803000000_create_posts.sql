-- SQLite sample migration: posts table
-- MySQL: use the matching *.mysql.sql file (same timestamp prefix).
CREATE TABLE IF NOT EXISTS posts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title TEXT NOT NULL,
    content TEXT,
    created_at TEXT,
    updated_at TEXT
);

INSERT INTO posts (title, content, created_at, updated_at) VALUES
    ('Hello Flight', 'Your first post from the skeleton migration.', datetime('now'), datetime('now')),
    ('Build something great', 'Inject SimplePdo, use ActiveRecord, render with Twig.', datetime('now'), datetime('now'));
