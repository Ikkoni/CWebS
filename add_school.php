<?php
/**
 * Add New School Record
 * CWebS - Multi-Tenant Public School CMS
 */

require_once __DIR__ . '/connect.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/flash.php';

// RBAC Guard: Only SuperAdmin can add schools
require_role('SuperAdmin');

$school_name = '';
$school_address = '';
$school_contact_number = '';
$school_email = '';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token()) {
        $errors[] = "Security token validation failed. Please try again.";
    } else {
        $school_name = trim($_POST['school_name'] ?? '');
        $school_address = trim($_POST['school_address'] ?? '');
        $school_contact_number = trim($_POST['school_contact_number'] ?? '');
        $school_email = trim($_POST['school_email'] ?? '');

        if (empty($school_name)) {
            $errors[] = "School Name is required.";
        }
        if (empty($school_address)) {
            $errors[] = "School Address is required.";
        }
        if (empty($school_email) || !filter_var($school_email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "A valid School Email address is required.";
        }

        if (empty($errors)) {
            // Check for duplicate school name or school email with prepared statement
            $dupStmt = $con->prepare("SELECT school_id FROM `school` WHERE `school_name` = ? OR `school_email` = ? LIMIT 1");
            if ($dupStmt) {
                $dupStmt->bind_param("ss", $school_name, $school_email);
                $dupStmt->execute();
                $dupResult = $dupStmt->get_result();

                if ($dupResult->num_rows > 0) {
                    $errors[] = "A school with this name or email address is already registered.";
                }
                $dupStmt->close();
            }

            if (empty($errors)) {
                // Prepared statement insertion
                $insertStmt = $con->prepare("INSERT INTO `school` (`school_name`, `school_address`, `school_contact_number`, `school_email`) VALUES (?, ?, ?, ?)");
                if ($insertStmt) {
                    // Contact number is stored as int in schema, sanitize to integer
                    $contactInt = (int)preg_replace('/[^0-9]/', '', $school_contact_number);
                    $insertStmt->bind_param("ssis", $school_name, $school_address, $contactInt, $school_email);

                    if ($insertStmt->execute()) {
                        set_flash('success', "School '{$school_name}' was successfully registered.");
                        header('Location: school_management.php');
                        exit;
                    } else {
                        $errors[] = "Database error: Unable to create school record.";
                    }
                    $insertStmt->close();
                } else {
                    $errors[] = "Failed to prepare database statement.";
                }
            }
        }
    }
}

$page_title = 'Add School';
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
                    <h2><i class="fa-solid fa-plus-circle"></i> Register New School</h2>
                    <p class="text-muted">Fill out the official information for the public school institution.</p>
                </div>
                <div>
                    <a href="school_management.php" class="btn btn-secondary">
                        <i class="fa-solid fa-arrow-left"></i> Back to Schools
                    </a>
                </div>
            </div>

            <?php if (!empty($errors)): ?>
                <div class="alert alert-danger mt-3" role="alert">
                    <span class="alert-icon">✕</span>
                    <div class="alert-content">
                        <ul class="mb-0">
                            <?php foreach ($errors as $err): ?>
                                <li><?php echo e($err); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

            <form method="POST" action="add_school.php" class="mt-4 form-grid">
                <?php echo csrf_field(); ?>

                <div class="form-group">
                    <label for="school_name">School Name <span class="text-danger">*</span></label>
                    <input 
                        type="text" 
                        class="form-control" 
                        id="school_name" 
                        name="school_name" 
                        placeholder="e.g. Springfield High School" 
                        value="<?php echo e($school_name); ?>" 
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="school_address">Campus Address <span class="text-danger">*</span></label>
                    <input 
                        type="text" 
                        class="form-control" 
                        id="school_address" 
                        name="school_address" 
                        placeholder="e.g. 742 Evergreen Terrace, General Trias" 
                        value="<?php echo e($school_address); ?>" 
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="school_contact_number">Contact Number</label>
                    <input 
                        type="text" 
                        class="form-control" 
                        id="school_contact_number" 
                        name="school_contact_number" 
                        placeholder="e.g. 4772312" 
                        value="<?php echo e($school_contact_number); ?>"
                    >
                </div>

                <div class="form-group">
                    <label for="school_email">Official School Email <span class="text-danger">*</span></label>
                    <input 
                        type="email" 
                        class="form-control" 
                        id="school_email" 
                        name="school_email" 
                        placeholder="e.g. admin@springfield.edu" 
                        value="<?php echo e($school_email); ?>" 
                        required
                    >
                </div>

                <div class="form-actions mt-4">
                    <button type="submit" name="submit" class="btn btn-primary">
                        <i class="fa-solid fa-check"></i> Register School
                    </button>
                    <a href="school_management.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/includes/layout_footer.php'; ?>
