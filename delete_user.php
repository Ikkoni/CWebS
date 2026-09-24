<?php
/**
 * Delete User Action Gateway
 * CWebS - Multi-Tenant Public School CMS
 */

require_once __DIR__ . '/connect.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/flash.php';

// RBAC Guard: SuperAdmin and Admin
require_role(['SuperAdmin', 'Admin']);

$currentUser = current_user();

$user_id = 0;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token()) {
        set_flash('error', 'Security token mismatch. User deletion aborted.');
        header('Location: user_management.php');
        exit;
    }
    $user_id = (int)($_POST['user_id'] ?? 0);
} elseif (isset($_GET['delete_user_id'])) {
    $user_id = (int)$_GET['delete_user_id'];
}

if ($user_id <= 0) {
    set_flash('error', 'Invalid user identifier.');
    header('Location: user_management.php');
    exit;
}

// Prevent self-deletion
if ($user_id === (int)$currentUser['id']) {
    set_flash('error', 'Self-deletion is prohibited. You cannot delete your own account.');
    header('Location: user_management.php');
    exit;
}

// Fetch user details to verify target existence and role
$stmt = $con->prepare("SELECT user_first_name, user_last_name, user_role, school_id FROM `users` WHERE `user_id` = ? LIMIT 1");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();

if (!$target = $res->fetch_assoc()) {
    $stmt->close();
    set_flash('error', 'User not found.');
    header('Location: user_management.php');
    exit;
}
$stmt->close();

// Privilege Guard: Admin cannot delete a SuperAdmin
if ($currentUser['role'] === 'Admin' && $target['user_role'] === 'SuperAdmin') {
    set_flash('error', 'Admins are not authorized to delete SuperAdmin accounts.');
    header('Location: user_management.php');
    exit;
}

// Parameterized prepared DELETE statement
$delStmt = $con->prepare("DELETE FROM `users` WHERE `user_id` = ?");
if ($delStmt) {
    $delStmt->bind_param("i", $user_id);
    if ($delStmt->execute()) {
        $name = trim($target['user_first_name'] . ' ' . $target['user_last_name']);
        set_flash('success', "User '{$name}' was successfully deleted.");
    } else {
        set_flash('error', "Database error: Failed to delete user.");
    }
    $delStmt->close();
}

header('Location: user_management.php');
exit;