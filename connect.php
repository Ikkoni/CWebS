<?php
/**
 * Database Connection Provider
 * CWebS - Multi-Tenant Public School CMS
 */

require_once __DIR__ . '/includes/config.php';

// Disable default mysqli error reporting that leaks sensitive stack traces
mysqli_report(MYSQLI_REPORT_OFF);

$con = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME, DB_PORT);

if ($con->connect_errno) {
    if (APP_DEBUG) {
        die("Database Connection Error (" . $con->connect_errno . "): " . htmlspecialchars($con->connect_error));
    } else {
        error_log("Database Connection Error (" . $con->connect_errno . "): " . $con->connect_error);
        die("Service temporarily unavailable. Please try again later.");
    }
}

// Ensure correct UTF-8 character encoding
$con->set_charset("utf8mb4");