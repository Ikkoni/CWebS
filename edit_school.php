<?php
/**
 * Edit School Record
 * CWebS - Multi-Tenant Public School CMS
 */

require_once __DIR__ . '/connect.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/flash.php';

// RBAC Guard: Only SuperAdmin can edit schools
require_role('SuperAdmin');

$school_id = isset($_GET['editschool_id']) ? (int)$_GET['editschool_id'] : 0;
if ($school_id <= 0) {
    set_flash('error', 'Invalid school identifier.');
    header('Location: school_management.php');
    exit;
}

// Fetch existing school details with prepared statement
$stmt = $con->prepare("SELECT school_id, school_name, school_address, school_contact_number, school_email FROM `school` WHERE `school_id` = ? LIMIT 1");
$stmt->bind_param("i", $school_id);
$stmt->execute();
$result = $stmt->get_result();

if (!$school = $result->fetch_assoc()) {
    $stmt->close();
    set_flash('error', 'School record not found.');
    header('Location: school_management.php');
    exit;
}
$stmt->close();

$school_name = $school['school_name'];
$school_address = $school['school_address'];
$school_contact_number = $school['school_contact_number'];
$school_email = $school['school_email'];
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
            // Check for duplicate email excluding current school
            $dupStmt = $con->prepare("SELECT school_id FROM `school` WHERE `school_email` = ? AND `school_id` != ? LIMIT 1");
            if ($dupStmt) {
                $dupStmt->bind_param("si", $school_email, $school_id);
                $dupStmt->execute();
                $dupResult = $dupStmt->get_result();
                if ($dupResult->num_rows > 0) {
                    $errors[] = "This email is already in use by another school.";
                }
                $dupStmt->close();
            }

            if (empty($errors)) {
                $contactInt = (int)preg_replace('/[^0-9]/', '', $school_contact_number);
                $updateStmt = $con->prepare("UPDATE `school` SET `school_name` = ?, `school_address` = ?, `school_contact_number` = ?, `school_email` = ? WHERE `school_id` = ?");
                if ($updateStmt) {
                    $updateStmt->bind_param("ssisi", $school_name, $school_address, $contactInt, $school_email, $school_id);
                    if ($updateStmt->execute()) {
                        set_flash('success', "School '{$school_name}' was successfully updated.");
                        header('Location: school_management.php');
                        exit;
                    } else {
                        $errors[] = "Database error: Unable to update school.";
                    }
                    $updateStmt->close();
                } else {
                    $errors[] = "Failed to prepare database update statement.";
                }
            }
        }
    }
}

$page_title = 'Edit School';
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
                    <h2><i class="fa-solid fa-pen-to-square"></i> Edit School #<?php echo (int)$school_id; ?></h2>
                    <p class="text-muted">Update contact details, campus address, or school identity.</p>
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

            <form method="POST" action="edit_school.php?editschool_id=<?php echo (int)$school_id; ?>" class="mt-4 form-grid">
                <?php echo csrf_field(); ?>

                <div class="form-group">
                    <label for="school_name">School Name <span class="text-danger">*</span></label>
                    <input 
                        type="text" 
                        class="form-control" 
                        id="school_name" 
                        name="school_name" 
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
                        value="<?php echo e($school_email); ?>" 
                        required
                    >
                </div>

                <div class="form-actions mt-4">
                    <button type="submit" name="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save"></i> Save Changes
                    </button>
                    <a href="school_management.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/includes/layout_footer.php'; ?>