<?php
/**
 * Uniform Flash Messaging & Alert Helper
 * CWebS - Multi-Tenant Public School CMS
 */

require_once __DIR__ . '/auth.php';

/**
 * Set a flash message
 *
 * @param string $type 'success', 'error', 'warning', 'info'
 * @param string $message
 */
function set_flash($type, $message) {
    if (!isset($_SESSION['flash'])) {
        $_SESSION['flash'] = [];
    }
    // Normalize type names
    if ($type === 'status' || $type === 'danger') {
        $type = 'error';
    }
    $_SESSION['flash'][$type][] = $message;
}

/**
 * Check if there are any pending flash messages
 *
 * @return bool
 */
function has_flashes() {
    return !empty($_SESSION['flash']) || !empty($_SESSION['success']) || !empty($_SESSION['status']);
}

/**
 * Render and clear all pending flash messages
 *
 * @return string HTML
 */
function render_flashes() {
    $html = '';

    // Handle legacy session variables for backward compatibility
    if (!empty($_SESSION['success'])) {
        set_flash('success', $_SESSION['success']);
        unset($_SESSION['success']);
    }

    if (!empty($_SESSION['status'])) {
        // Status messages in the old codebase were usually errors
        set_flash('error', $_SESSION['status']);
        unset($_SESSION['status']);
    }

    if (empty($_SESSION['flash'])) {
        return '';
    }

    $icons = [
        'success' => '✓',
        'error'   => '✕',
        'warning' => '⚠',
        'info'    => 'ℹ'
    ];

    foreach ($_SESSION['flash'] as $type => $messages) {
        $alertClass = 'alert alert-' . ($type === 'error' ? 'danger' : $type);
        $icon = $icons[$type] ?? 'ℹ';

        foreach ($messages as $msg) {
            $safeMsg = htmlspecialchars($msg, ENT_QUOTES, 'UTF-8');
            $html .= '<div class="' . $alertClass . ' alert-dismissible" role="alert">';
            $html .= '<span class="alert-icon">' . $icon . '</span>';
            $html .= '<div class="alert-content">' . $safeMsg . '</div>';
            $html .= '<button type="button" class="alert-close" onclick="this.parentElement.remove();" aria-label="Close">&times;</button>';
            $html .= '</div>';
        }
    }

    // Clear flash array after rendering
    unset($_SESSION['flash']);

    return $html;
}
