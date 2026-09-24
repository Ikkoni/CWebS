<?php
/**
 * Dashboard Overview & Role Hub
 * CWebS - Multi-Tenant Public School CMS
 */

require_once __DIR__ . '/connect.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/flash.php';

// Route Guard: Authentication required
require_login();

$user = current_user();
$user_role = $user['role'];
$page_title = ucfirst($user_role) . ' Dashboard';

// Fetch summary metrics for dashboard cards using parameterized queries
$totalSchools = 0;
$totalUsers = 0;

if ($user_role === 'SuperAdmin') {
    $stmt = $con->prepare("SELECT COUNT(*) AS total FROM `school`");
    if ($stmt) {
        $stmt->execute();
        $res = $stmt->get_result();
        if ($row = $res->fetch_assoc()) {
            $totalSchools = (int)$row['total'];
        }
        $stmt->close();
    }

    $stmtUsers = $con->prepare("SELECT COUNT(*) AS total FROM `users`");
    if ($stmtUsers) {
        $stmtUsers->execute();
        $resUsers = $stmtUsers->get_result();
        if ($rowUsers = $resUsers->fetch_assoc()) {
            $totalUsers = (int)$rowUsers['total'];
        }
        $stmtUsers->close();
    }
} elseif ($user_role === 'Admin') {
    // School-specific user count if assigned to a school, else total users
    if (!empty($user['school_id'])) {
        $stmtUsers = $con->prepare("SELECT COUNT(*) AS total FROM `users` WHERE school_id = ?");
        if ($stmtUsers) {
            $stmtUsers->bind_param("i", $user['school_id']);
            $stmtUsers->execute();
            $resUsers = $stmtUsers->get_result();
            if ($rowUsers = $resUsers->fetch_assoc()) {
                $totalUsers = (int)$rowUsers['total'];
            }
            $stmtUsers->close();
        }
    } else {
        $stmtUsers = $con->prepare("SELECT COUNT(*) AS total FROM `users`");
        if ($stmtUsers) {
            $stmtUsers->execute();
            $resUsers = $stmtUsers->get_result();
            if ($rowUsers = $resUsers->fetch_assoc()) {
                $totalUsers = (int)$rowUsers['total'];
            }
            $stmtUsers->close();
        }
    }
}

require_once __DIR__ . '/includes/layout_header.php';
?>

<div class="dashboard-layout">
    <aside class="dashboard-sidebar-wrapper">
        <?php include __DIR__ . '/includes/sidebar.php'; ?>
    </aside>

    <section class="dashboard-main-content">
        <div class="dashboard-welcome-banner">
            <div class="welcome-text">
                <h2>Welcome back, <?php echo e($user['first_name'] ?: 'Administrator'); ?>!</h2>
                <p>You are logged into the <strong><?php echo e(APP_NAME); ?></strong> platform as 
                   <span class="badge badge-<?php echo strtolower($user_role); ?>"><?php echo e($user_role); ?></span>
                   <?php if (!empty($user['department'])): ?>
                       &bull; Department: <strong><?php echo e($user['department']); ?></strong>
                   <?php endif; ?>
                </p>
            </div>
            <div class="welcome-actions">
                <a href="edit_webpage.php" class="btn btn-primary"><i class="fa-solid fa-pen-to-square"></i> Open CMS Editor</a>
            </div>
        </div>

        <div class="metrics-grid">
            <?php if ($user_role === 'SuperAdmin'): ?>
                <div class="metric-card">
                    <div class="metric-icon metric-icon-purple"><i class="fa-solid fa-school"></i></div>
                    <div class="metric-info">
                        <div class="metric-number"><?php echo $totalSchools; ?></div>
                        <div class="metric-label">Registered Schools</div>
                    </div>
                    <a href="school_management.php" class="metric-link">Manage schools &rarr;</a>
                </div>
            <?php endif; ?>

            <?php if ($user_role === 'SuperAdmin' || $user_role === 'Admin'): ?>
                <div class="metric-card">
                    <div class="metric-icon metric-icon-green"><i class="fa-solid fa-users"></i></div>
                    <div class="metric-info">
                        <div class="metric-number"><?php echo $totalUsers; ?></div>
                        <div class="metric-label">System Users</div>
                    </div>
                    <a href="user_management.php" class="metric-link">Manage users &rarr;</a>
                </div>
            <?php endif; ?>

            <div class="metric-card">
                <div class="metric-icon metric-icon-blue"><i class="fa-solid fa-globe"></i></div>
                <div class="metric-info">
                    <div class="metric-number"><i class="fa-solid fa-circle-check text-success"></i> Active</div>
                    <div class="metric-label">School CMS Status</div>
                </div>
                <a href="edit_webpage.php" class="metric-link">Preview site &rarr;</a>
            </div>

            <div class="metric-card">
                <div class="metric-icon metric-icon-orange"><i class="fa-solid fa-shield-halved"></i></div>
                <div class="metric-info">
                    <div class="metric-number"><?php echo e($user['position'] ?: 'Staff'); ?></div>
                    <div class="metric-label">Account Role</div>
                </div>
                <a href="account.php" class="metric-link">Edit profile &rarr;</a>
            </div>
        </div>

        <div class="dashboard-card mt-4">
            <div class="card-header-flex">
                <h3><i class="fa-solid fa-bolt"></i> Quick Actions</h3>
            </div>
            <div class="quick-actions-grid">
                <a href="edit_webpage.php" class="action-card">
                    <div class="action-icon"><i class="fa-solid fa-palette"></i></div>
                    <h4>Customize School Webpage</h4>
                    <p>Edit colors, layout sections, banners, and typography for your school website.</p>
                </a>

                <?php if ($user_role === 'SuperAdmin' || $user_role === 'Admin'): ?>
                    <a href="user_management.php" class="action-card">
                        <div class="action-icon"><i class="fa-solid fa-user-plus"></i></div>
                        <h4>Manage Staff &amp; Roles</h4>
                        <p>Provision and configure administrator, teacher, and editor access permissions.</p>
                    </a>
                <?php endif; ?>

                <?php if ($user_role === 'SuperAdmin'): ?>
                    <a href="school_management.php" class="action-card">
                        <div class="action-icon"><i class="fa-solid fa-building-columns"></i></div>
                        <h4>Manage Public Schools</h4>
                        <p>Register new educational institutions, manage addresses, and school contacts.</p>
                    </a>
                    <a href="systemsettings.php" class="action-card">
                        <div class="action-icon"><i class="fa-solid fa-sliders"></i></div>
                        <h4>System Configuration</h4>
                        <p>Adjust platform-wide quota limits, domains, and maintenance mode status.</p>
                    </a>
                <?php endif; ?>

                <?php if ($user_role === 'Admin'): ?>
                    <a href="sitesettings.php" class="action-card">
                        <div class="action-icon"><i class="fa-solid fa-sliders"></i></div>
                        <h4>School Site Settings</h4>
                        <p>Configure institution branding, public contact email, and social media handles.</p>
                    </a>
                <?php endif; ?>

                <a href="account.php" class="action-card">
                    <div class="action-icon"><i class="fa-solid fa-id-badge"></i></div>
                    <h4>Profile &amp; Credentials</h4>
                    <p>Update personal contact info, change account password, and manage security settings.</p>
                </a>
            </div>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/includes/layout_footer.php'; ?>