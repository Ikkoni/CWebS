<?php
/**
 * Sign Out / Session Destruction Gateway
 * CWebS - Multi-Tenant Public School CMS
 */

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/flash.php';

// Clear session variables
$_SESSION = [];

// Invalidate session cookie in browser
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

// Destroy session on server
session_destroy();

// Start clean session for flash notification
start_secure_session();
set_flash('info', 'You have been signed out safely.');

header("Location: home.php");
exit;