<?php
/**
 * School Site Settings
 * CWebS - Multi-Tenant Public School CMS
 */

require_once __DIR__ . '/connect.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/flash.php';

// RBAC Guard: SuperAdmin and Admin
require_role(['SuperAdmin', 'Admin']);

$user = current_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token()) {
        set_flash('error', 'Security token mismatch.');
    } else {
        // Mock save for settings demonstration
        set_flash('success', 'Site settings were successfully saved.');
        header('Location: sitesettings.php');
        exit;
    }
}

$page_title = 'Site Settings';
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
                    <h2><i class="fa-solid fa-sliders"></i> School Site Settings</h2>
                    <p class="text-muted">Configure public identity, announcements, and contact details for this school portal.</p>
                </div>
            </div>

            <form method="POST" action="sitesettings.php" class="mt-4 form-grid">
                <?php echo csrf_field(); ?>

                <div class="form-group">
                    <label for="site-name">School Website Title</label>
                    <input type="text" class="form-control" id="site-name" name="site_name" value="Springfield Public School" required>
                </div>

                <div class="form-group">
                    <label for="site-description">School Mission / Meta Description</label>
                    <textarea class="form-control" id="site-description" name="site_description" rows="3">A nurturing educational environment where young minds grow, learn, and lead.</textarea>
                </div>

                <div class="form-row-2">
                    <div class="form-group">
                        <label for="contact-email">Public Inquiries Email</label>
                        <input type="email" class="form-control" id="contact-email" name="contact_email" value="info@springfield.edu" required>
                    </div>

                    <div class="form-group">
                        <label for="contact-phone">Public Telephone</label>
                        <input type="text" class="form-control" id="contact-phone" name="contact_phone" value="(555) 019-2834">
                    </div>
                </div>

                <div class="form-group">
                    <label for="social-media">Social Media Links <small class="text-muted">(Comma-separated)</small></label>
                    <input type="text" class="form-control" id="social-media" name="social_media" value="facebook.com/springfield, x.com/springfield">
                </div>

                <div class="form-actions mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save"></i> Save Site Settings
                    </button>
                    <a href="dashboard_nav.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/includes/layout_footer.php'; ?>