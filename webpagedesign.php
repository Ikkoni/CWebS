<?php
/**
 * Website Design & Theme Selector
 * CWebS - Multi-Tenant Public School CMS
 */

require_once __DIR__ . '/connect.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/flash.php';

// RBAC Guard: SuperAdmin and Admin
require_role(['SuperAdmin', 'Admin']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token()) {
        set_flash('error', 'Security token mismatch.');
    } else {
        set_flash('success', 'Design theme updated successfully.');
        header('Location: webpagedesign.php');
        exit;
    }
}

$page_title = 'Website Design';
require_once __DIR__ . '/includes/layout_header.php';
?>

<div class="dashboard-layout">
    <aside class="dashboard-sidebar-wrapper">
        <?php include __DIR__ . '/includes/sidebar.php'; ?>
    </aside>

    <section class="dashboard-main-content">
        <div class="dashboard-card">
            <div class="card-header-flex">
                <div>
                    <h2><i class="fa-solid fa-palette"></i> School Website Theme &amp; Styling</h2>
                    <p class="text-muted">Choose your school's color scheme, font family, and pre-built layout theme.</p>
                </div>
                <div>
                    <a href="edit_webpage.php" class="btn btn-secondary">
                        <i class="fa-solid fa-pen-to-square"></i> Live Visual Editor
                    </a>
                </div>
            </div>

            <form method="POST" action="webpagedesign.php" class="mt-4">
                <?php echo csrf_field(); ?>

                <div class="form-row-2">
                    <div class="form-group">
                        <label for="color-scheme">Primary Color Palette</label>
                        <select id="color-scheme" name="color_scheme" class="form-control">
                            <option value="blue">Classic Navy &amp; Academic Gold</option>
                            <option value="forest">Forest Green &amp; Slate White</option>
                            <option value="crimson">Crimson Red &amp; Charcoal</option>
                            <option value="indigo" selected>Indigo Modern &amp; Emerald Accent</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="font-style">Typography Heading &amp; Body</label>
                        <select id="font-style" name="font_style" class="form-control">
                            <option value="inter" selected>Inter / Sans-Serif (Modern &amp; Readable)</option>
                            <option value="merriweather">Merriweather / Serif (Traditional &amp; Classic)</option>
                            <option value="roboto">Roboto / Clean Geometric</option>
                        </select>
                    </div>
                </div>

                <h3 class="mt-4 mb-2">Preset School Templates</h3>
                <div class="template-grid">
                    <div class="template-card selected">
                        <div class="template-badge">Active</div>
                        <div class="template-preview-box template-preview-modern">
                            <div class="preview-bar"></div>
                            <div class="preview-content-lines"></div>
                        </div>
                        <h4>Modern Academic</h4>
                        <p>Clean card-based presentation ideal for large district and secondary schools.</p>
                        <button type="submit" name="theme" value="modern" class="btn btn-sm btn-primary">Selected</button>
                    </div>

                    <div class="template-card">
                        <div class="template-preview-box template-preview-classic">
                            <div class="preview-bar preview-bar-gold"></div>
                            <div class="preview-content-lines"></div>
                        </div>
                        <h4>Classical Heritage</h4>
                        <p>Traditional scholarly aesthetic featuring formal crests and editorial typography.</p>
                        <button type="submit" name="theme" value="classic" class="btn btn-sm btn-secondary">Apply Theme</button>
                    </div>

                    <div class="template-card">
                        <div class="template-preview-box template-preview-vibrant">
                            <div class="preview-bar preview-bar-green"></div>
                            <div class="preview-content-lines"></div>
                        </div>
                        <h4>Vibrant Campus</h4>
                        <p>Dynamic, media-rich layouts highlighting student athletics and extracurriculars.</p>
                        <button type="submit" name="theme" value="vibrant" class="btn btn-sm btn-secondary">Apply Theme</button>
                    </div>
                </div>

                <div class="form-actions mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fa-solid fa-check"></i> Apply Design Settings
                    </button>
                    <a href="dashboard_nav.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/includes/layout_footer.php'; ?>