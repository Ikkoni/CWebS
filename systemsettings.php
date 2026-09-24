<?php
/**
 * Platform System Settings (SuperAdmin Only)
 * CWebS - Multi-Tenant Public School CMS
 */

require_once __DIR__ . '/connect.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/flash.php';

// RBAC Guard: SuperAdmin only
require_role('SuperAdmin');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token()) {
        set_flash('error', 'Security token mismatch.');
    } else {
        set_flash('success', 'Global system settings were successfully updated.');
        header('Location: systemsettings.php');
        exit;
    }
}

$page_title = 'System Settings';
require_once __DIR__ . '/includes/layout_header.php';
?>

<div class="dashboard-layout">
    <aside class="dashboard-sidebar-wrapper">
        <?php include __DIR__ . '/includes/sidebar.php'; ?>
    </aside>

    <section class="dashboard-main-content">
        <div class="dashboard-card max-w-700">
            <div class="card-header-flex">
                <div>
                    <h2><i class="fa-solid fa-gears"></i> Platform System Settings</h2>
                    <p class="text-muted">Manage global infrastructure, tenant quotas, and network maintenance state.</p>
                </div>
            </div>

            <form method="POST" action="systemsettings.php" class="mt-4 form-grid">
                <?php echo csrf_field(); ?>

                <div class="form-row-2">
                    <div class="form-group">
                        <label for="max-schools">Maximum Allowed Schools</label>
                        <input type="number" class="form-control" id="max-schools" name="max_schools" value="1000" min="1" required>
                    </div>

                    <div class="form-group">
                        <label for="storage-limit">Storage Quota per School (GB)</label>
                        <input type="number" class="form-control" id="storage-limit" name="storage_limit" value="50" min="5" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="allowed-domains">Permitted Staff Email Domains <small class="text-muted">(Comma-separated)</small></label>
                    <input type="text" class="form-control" id="allowed-domains" name="allowed_domains" value="edu, gov, school.org" required>
                </div>

                <div class="form-group">
                    <label for="maintenance-mode">Platform Maintenance Mode</label>
                    <select class="form-control" id="maintenance-mode" name="maintenance_mode">
                        <option value="0">Disabled (Normal Operations)</option>
                        <option value="1">Enabled (Staff Only Access)</option>
                    </select>
                </div>

                <div class="form-actions mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save"></i> Save Global Settings
                    </button>
                    <a href="dashboard_nav.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/includes/layout_footer.php'; ?>