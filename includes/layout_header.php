<?php
/**
 * Master Layout Header
 * CWebS - Multi-Tenant Public School CMS
 */

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/flash.php';

$page_title = $page_title ?? 'CWebS';
$user = current_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo e($page_title); ?> - <?php echo e(APP_NAME); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <header class="header">
        <div class="container header-content">
            <a href="<?php echo is_logged_in() ? 'dashboard_nav.php' : 'home.php'; ?>" class="logo">
                <span class="logo-icon">🏫</span>
                <span class="logo-title"><?php echo e(APP_NAME); ?></span>
            </a>

            <nav class="nav-links">
                <?php if (is_logged_in()): ?>
                    <div class="user-meta">
                        <span class="user-name"><i class="fa-regular fa-user"></i> <?php echo e($user['full_name'] ?: $user['email']); ?></span>
                        <span class="badge badge-<?php echo strtolower($user['role']); ?>"><?php echo e($user['role']); ?></span>
                    </div>
                    <a href="dashboard_nav.php" class="btn btn-sm btn-secondary"><i class="fa-solid fa-table-columns"></i> Dashboard</a>
                    <a href="account.php" class="btn btn-sm btn-secondary"><i class="fa-solid fa-gear"></i> Account</a>
                    <a href="logout.php" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-right-from-bracket"></i> Log Out</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary"><i class="fa-solid fa-arrow-right-to-bracket"></i> Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="main-wrapper">
        <div class="container">
            <?php echo render_flashes(); ?>
