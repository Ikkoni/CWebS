<?php
/**
 * Delete School Action Gateway
 * CWebS - Multi-Tenant Public School CMS
 */

require_once __DIR__ . '/connect.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/flash.php';

// RBAC Guard: Only SuperAdmin can delete schools
require_role('SuperAdmin');

// Support both POST (standard secure form) and GET (legacy with CSRF/auth verification)
$school_id = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token()) {
        set_flash('error', 'Security token mismatch. School deletion aborted.');
        header('Location: school_management.php');
        exit;
    }
    $school_id = (int)($_POST['school_id'] ?? 0);
} elseif (isset($_GET['deleteschool_id'])) {
    $school_id = (int)$_GET['deleteschool_id'];
}

if ($school_id <= 0) {
    set_flash('error', 'Invalid school identifier.');
    header('Location: school_management.php');
    exit;
}

// Check school exists and fetch name for flash message
$checkStmt = $con->prepare("SELECT school_name FROM `school` WHERE `school_id` = ? LIMIT 1");
$checkStmt->bind_param("i", $school_id);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($row = $checkResult->fetch_assoc()) {
    $school_name = $row['school_name'];
    $checkStmt->close();

    // Parameterized prepared DELETE statement
    $delStmt = $con->prepare("DELETE FROM `school` WHERE `school_id` = ?");
    if ($delStmt) {
        $delStmt->bind_param("i", $school_id);
        if ($delStmt->execute()) {
            set_flash('success', "School '{$school_name}' and all associated records were successfully deleted.");
        } else {
            set_flash('error', "Database error: Failed to delete school record.");
        }
        $delStmt->close();
    }
} else {
    $checkStmt->close();
    set_flash('error', 'School record not found.');
}

header('Location: school_management.php');
exit;