<?php
/**
 * Global includes and initialization
 */

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Load configuration
if (!defined('DB_HOST')) {
    require_once __DIR__ . '/../config/config.php';
}

// Load database
require_once __DIR__ . '/../functions/Database.php';

// Initialize database connection
if (!isset($db)) {
    $db = new Database();
    $db->connect();
}

// Load all helper classes
require_once __DIR__ . '/../functions/Auth.php';
require_once __DIR__ . '/../functions/DomainManager.php';
require_once __DIR__ . '/../functions/BlogManager.php';
require_once __DIR__ . '/../functions/PaymentManager.php';
require_once __DIR__ . '/../functions/NotificationManager.php';
require_once __DIR__ . '/../functions/OfferManager.php';
require_once __DIR__ . '/../functions/Security.php';
require_once __DIR__ . '/../functions/FileUpload.php';
require_once __DIR__ . '/../functions/SearchManager.php';
require_once __DIR__ . '/../functions/EmailManager.php';
require_once __DIR__ . '/../functions/helpers.php';

// Initialize core managers
if (!isset($auth)) {
    $auth = new Auth($db);
}

if (!isset($security)) {
    $security = new Security($db);
}

// Error handling
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Set security headers
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');

// CSRF Token generation
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
