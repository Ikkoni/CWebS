<?php
/**
 * Role-Based Dashboard Navigation Sidebar Component
 * CWebS - Multi-Tenant Public School CMS
 */

require_once __DIR__ . '/auth.php';

$user = current_user();
$user_role = $user['role'] ?? 'Editor';
$current_script = basename($_SERVER['PHP_SELF']);
?>
<nav class="dashboard-sidebar" aria-label="Dashboard Navigation">
    <div class="sidebar-header">
        <div class="sidebar-title"><?php echo e($user_role); ?> Portal</div>
        <div class="sidebar-subtitle"><?php echo e($user['department'] ?: 'School Administration'); ?></div>
    </div>

    <ul class="sidebar-menu">
        <li class="menu-item <?php echo ($current_script === 'dashboard_nav.php') ? 'active' : ''; ?>">
            <a href="dashboard_nav.php"><i class="fa-solid fa-gauge-high"></i> Overview</a>
        </li>

        <li class="menu-item <?php echo ($current_script === 'edit_webpage.php') ? 'active' : ''; ?>">
            <a href="edit_webpage.php"><i class="fa-solid fa-pen-to-square"></i> Edit Webpage</a>
        </li>

        <?php if ($user_role !== 'Editor'): ?>
            <li class="menu-item <?php echo in_array($current_script, ['user_management.php', 'add_user.php', 'edit_user.php']) ? 'active' : ''; ?>">
                <a href="user_management.php"><i class="fa-solid fa-users"></i> Manage Users</a>
            </li>
            <li class="menu-item <?php echo ($current_script === 'webpagedesign.php') ? 'active' : ''; ?>">
                <a href="webpagedesign.php"><i class="fa-solid fa-palette"></i> Website Design</a>
            </li>
        <?php endif; ?>

        <?php if ($user_role === 'SuperAdmin'): ?>
            <li class="menu-item <?php echo in_array($current_script, ['school_management.php', 'add_school.php', 'edit_school.php']) ? 'active' : ''; ?>">
                <a href="school_management.php"><i class="fa-solid fa-school"></i> Manage Schools</a>
            </li>
            <li class="menu-item <?php echo ($current_script === 'systemsettings.php') ? 'active' : ''; ?>">
                <a href="systemsettings.php"><i class="fa-solid fa-sliders"></i> System Settings</a>
            </li>
        <?php endif; ?>

        <?php if ($user_role === 'Admin'): ?>
            <li class="menu-item <?php echo ($current_script === 'sitesettings.php') ? 'active' : ''; ?>">
                <a href="sitesettings.php"><i class="fa-solid fa-sliders"></i> Site Settings</a>
            </li>
        <?php endif; ?>

        <li class="menu-separator"></li>

        <li class="menu-item <?php echo ($current_script === 'account.php') ? 'active' : ''; ?>">
            <a href="account.php"><i class="fa-solid fa-user-gear"></i> My Account</a>
        </li>

        <li class="menu-item menu-item-danger">
            <a href="logout.php"><i class="fa-solid fa-right-from-bracket"></i> Sign Out</a>
        </li>
    </ul>
</nav>
