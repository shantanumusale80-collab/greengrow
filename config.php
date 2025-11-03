<?php
// ===== GreenGrow Configuration =====
session_start();

// Update these to match your environment
define('DB_HOST', 'localhost');
define('DB_NAME', 'greengrow');
define('DB_USER', 'root');
define('DB_PASS', '');

// App settings
define('SITE_NAME', 'GreenGrow');
define('SITE_URL', 'http://localhost/greengrow'); // no trailing slash
define('ADMIN_EMAIL', 'admin@example.com'); // The account with this email will have admin access
define('FROM_EMAIL', 'no-reply@greengrow.local'); // For outgoing reminder emails

// Connect with PDO
try {
    $pdo = new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset=utf8mb4', DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}

// Simple helpers
function isLoggedIn() { return isset($_SESSION['user']); }
function currentUser() { return $_SESSION['user'] ?? null; }
function isAdmin() {
    return isLoggedIn() && isset($_SESSION['user']['Email']) && $_SESSION['user']['Email'] === ADMIN_EMAIL;
}
function redirect($path) {
    header('Location: ' . SITE_URL . '/' . ltrim($path, '/'));
    exit;
}
function e($s) { return htmlspecialchars($s ?? '', ENT_QUOTES, 'UTF-8'); }
?>
