<?php
/**
 * Authentication, Session Security & Access Control Guard
 * CWebS - Multi-Tenant Public School CMS
 */

require_once __DIR__ . '/config.php';

/**
 * Initialize session with strict, hardened cookie flags
 */
function start_secure_session() {
    if (session_status() === PHP_SESSION_NONE) {
        $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ||
                    (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ||
                    (bool)env('SESSION_SECURE', false);

        session_set_cookie_params([
            'lifetime' => (int)env('SESSION_LIFETIME', 7200),
            'path'     => '/',
            'domain'   => '',
            'secure'   => $isSecure,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);

        session_start();
    }
}

// Auto-boot secure session whenever auth.php is loaded
start_secure_session();

/**
 * Check if a user is currently authenticated
 *
 * @return bool
 */
function is_logged_in() {
    return !empty($_SESSION['user_id']) && !empty($_SESSION['user_email']);
}

/**
 * Retrieve current authenticated user data
 *
 * @return array|null
 */
function current_user() {
    if (!is_logged_in()) {
        return null;
    }

    return [
        'id'             => $_SESSION['user_id'] ?? null,
        'first_name'     => $_SESSION['user_first_name'] ?? '',
        'last_name'      => $_SESSION['user_last_name'] ?? '',
        'full_name'      => trim(($_SESSION['user_first_name'] ?? '') . ' ' . ($_SESSION['user_last_name'] ?? '')),
        'email'          => $_SESSION['user_email'] ?? '',
        'role'           => $_SESSION['user_role'] ?? 'Editor',
        'department'     => $_SESSION['user_department'] ?? '',
        'position'       => $_SESSION['user_position'] ?? '',
        'contact_number' => $_SESSION['user_contact_number'] ?? '',
        'address'        => $_SESSION['user_address'] ?? '',
        'school_id'      => $_SESSION['school_id'] ?? null
    ];
}

/**
 * Route guard: Require user to be logged in
 */
function require_login() {
    if (!is_logged_in()) {
        require_once __DIR__ . '/flash.php';
        set_flash('error', 'Please log in to access this page.');
        header('Location: login.php');
        exit;
    }
}

/**
 * Route guard: Require specific Role-Based Access Control (RBAC) permissions
 *
 * @param string|array $roles Single role string or array of allowed roles
 */
function require_role($roles) {
    require_login();

    $userRole = $_SESSION['user_role'] ?? '';
    $allowed = is_array($roles) ? $roles : [$roles];

    if (!in_array($userRole, $allowed, true)) {
        require_once __DIR__ . '/flash.php';
        set_flash('error', 'Access denied: You do not have permission to access that resource.');
        header('Location: dashboard_nav.php');
        exit;
    }
}

/**
 * Generate or retrieve CSRF token
 *
 * @return string
 */
function csrf_token() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Output hidden CSRF token form input
 *
 * @return string HTML
 */
function csrf_field() {
    $token = htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8');
    return '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

/**
 * Verify CSRF token from POST request
 *
 * @param string|null $token
 * @return bool
 */
function verify_csrf_token($token = null) {
    if ($token === null) {
        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    }

    if (empty($_SESSION['csrf_token']) || empty($token)) {
        return false;
    }

    return hash_equals($_SESSION['csrf_token'], $token);
}

/**
 * XSS escaping helper
 *
 * @param mixed $string
 * @return string
 */
function e($string) {
    return htmlspecialchars((string)$string, ENT_QUOTES, 'UTF-8');
}
