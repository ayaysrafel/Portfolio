<?php
// Database settings (XAMPP defaults). Change these when you host the site online.
const DB_HOST = 'localhost';
const DB_NAME = 'portfolio_db';
const DB_USER = 'portfolio_user';
const DB_PASS = 'your-password-here';

// Password for php/admin.php (where you read your messages). CHANGE THIS.
const ADMIN_PASSWORD = 'your-password-here';

function db(): PDO {
    return new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
}
