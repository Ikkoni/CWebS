<?php
/**
 * Edit User Record
 * CWebS - Multi-Tenant Public School CMS
 */

require_once __DIR__ . '/connect.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/flash.php';

// RBAC Guard: SuperAdmin and Admin
require_role(['SuperAdmin', 'Admin']);

$currentUser = current_user();

$user_id = isset($_GET['edit_user_id']) ? (int)$_GET['edit_user_id'] : 0;
if ($user_id <= 0) {
    set_flash('error', 'Invalid user identifier.');
    header('Location: user_management.php');
    exit;
}

// Fetch existing user with prepared statement
$stmt = $con->prepare("
    SELECT user_id, user_first_name, user_last_name, user_email, user_password, 
           user_role, user_department, user_position, user_contact_number, 
           user_address, school_id 
    FROM `users` 
    WHERE `user_id` = ? 
    LIMIT 1
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();

if (!$targetUser = $res->fetch_assoc()) {
    $stmt->close();
    set_flash('error', 'User record not found.');
    header('Location: user_management.php');
    exit;
}
$stmt->close();

// Privilege check: Admin cannot edit a SuperAdmin account
if ($currentUser['role'] === 'Admin' && $targetUser['user_role'] === 'SuperAdmin') {
    set_flash('error', 'Admins are not authorized to edit SuperAdmin accounts.');
    header('Location: user_management.php');
    exit;
}

// Fetch available schools for assignment
$schools = [];
$schoolStmt = $con->prepare("SELECT school_id, school_name FROM `school` ORDER BY school_name ASC");
if ($schoolStmt) {
    $schoolStmt->execute();
    $sRes = $schoolStmt->get_result();
    while ($sRow = $sRes->fetch_assoc()) {
        $schools[] = $sRow;
    }
    $schoolStmt->close();
}

$user_first_name = $targetUser['user_first_name'];
$user_last_name = $targetUser['user_last_name'];
$user_email = $targetUser['user_email'];
$user_role = $targetUser['user_role'];
$user_department = $targetUser['user_department'];
$user_position = $targetUser['user_position'];
$user_contact_number = $targetUser['user_contact_number'];
$user_address = $targetUser['user_address'];
$school_id = $targetUser['school_id'];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token()) {
        $errors[] = "Security token mismatch. Please try again.";
    } else {
        $user_first_name = trim($_POST['user_first_name'] ?? '');
        $user_last_name = trim($_POST['user_last_name'] ?? '');
        $user_email = trim($_POST['user_email'] ?? '');
        $new_password = $_POST['user_password'] ?? '';
        $new_role = $_POST['user_role'] ?? $targetUser['user_role'];
        $user_department = trim($_POST['user_department'] ?? '');
        $user_position = trim($_POST['user_position'] ?? '');
        $user_contact_number = trim($_POST['user_contact_number'] ?? '');
        $user_address = trim($_POST['user_address'] ?? '');
        $selected_school = !empty($_POST['school_id']) ? (int)$_POST['school_id'] : null;

        // Admin privilege boundary
        if ($currentUser['role'] === 'Admin' && $new_role === 'SuperAdmin') {
            $errors[] = "Admins cannot promote users to SuperAdmin.";
        } else {
            $user_role = $new_role;
        }

        if (empty($user_first_name) || empty($user_last_name)) {
            $errors[] = "First and Last Name are required.";
        }
        if (empty($user_email) || !filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "A valid Email address is required.";
        }

        if (empty($errors)) {
            // Check for duplicate email excluding current user
            $dupStmt = $con->prepare("SELECT user_id FROM `users` WHERE `user_email` = ? AND `user_id` != ? LIMIT 1");
            if ($dupStmt) {
                $dupStmt->bind_param("si", $user_email, $user_id);
                $dupStmt->execute();
                $dupResult = $dupStmt->get_result();
                if ($dupResult->num_rows > 0) {
                    $errors[] = "This email is already registered to another user.";
                }
                $dupStmt->close();
            }

            if (empty($errors)) {
                $contactInt = (int)preg_replace('/[^0-9]/', '', $user_contact_number);

                // Check if password is being updated
                if (!empty($new_password)) {
                    if (strlen($new_password) < 6) {
                        $errors[] = "New password must be at least 6 characters long.";
                    } else {
                        $hashedPassword = password_hash($new_password, PASSWORD_DEFAULT);
                        $updateStmt = $con->prepare("
                            UPDATE `users` 
                            SET `user_first_name` = ?, `user_last_name` = ?, `user_email` = ?, `user_password` = ?,
                                `user_role` = ?, `user_department` = ?, `user_position` = ?, 
                                `user_contact_number` = ?, `user_address` = ?, `school_id` = ?
                            WHERE `user_id` = ?
                        ");
                        if ($updateStmt) {
                            $updateStmt->bind_param(
                                "sssssssissi",
                                $user_first_name, $user_last_name, $user_email, $hashedPassword,
                                $user_role, $user_department, $user_position,
                                $contactInt, $user_address, $selected_school, $user_id
                            );
                            if ($updateStmt->execute()) {
                                set_flash('success', "User '{$user_first_name} {$user_last_name}' was successfully updated.");
                                header('Location: user_management.php');
                                exit;
                            } else {
                                $errors[] = "Database error: Unable to update user.";
                            }
                            $updateStmt->close();
                        }
                    }
                } else {
                    // Update without modifying password
                    $updateStmt = $con->prepare("
                        UPDATE `users` 
                        SET `user_first_name` = ?, `user_last_name` = ?, `user_email` = ?,
                            `user_role` = ?, `user_department` = ?, `user_position` = ?, 
                            `user_contact_number` = ?, `user_address` = ?, `school_id` = ?
                        WHERE `user_id` = ?
                    ");
                    if ($updateStmt) {
                        $updateStmt->bind_param(
                            "ssssssissi",
                            $user_first_name, $user_last_name, $user_email,
                            $user_role, $user_department, $user_position,
                            $contactInt, $user_address, $selected_school, $user_id
                        );
                        if ($updateStmt->execute()) {
                            set_flash('success', "User '{$user_first_name} {$user_last_name}' was successfully updated.");
                            header('Location: user_management.php');
                            exit;
                        } else {
                            $errors[] = "Database error: Unable to update user.";
                        }
                        $updateStmt->close();
                    }
                }
            }
        }
    }
}

$page_title = 'Edit User';
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
                    <h2><i class="fa-solid fa-user-pen"></i> Edit User #<?php echo (int)$user_id; ?></h2>
                    <p class="text-muted">Modify staff roles, school assignment, or reset password.</p>
                </div>
                <div>
                    <a href="user_management.php" class="btn btn-secondary">
                        <i class="fa-solid fa-arrow-left"></i> Back to Users
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

            <form method="POST" action="edit_user.php?edit_user_id=<?php echo (int)$user_id; ?>" class="mt-4 form-grid">
                <?php echo csrf_field(); ?>

                <div class="form-row-2">
                    <div class="form-group">
                        <label for="user_first_name">First Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="user_first_name" name="user_first_name" value="<?php echo e($user_first_name); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="user_last_name">Last Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="user_last_name" name="user_last_name" value="<?php echo e($user_last_name); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="user_email">Email Address <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="user_email" name="user_email" value="<?php echo e($user_email); ?>" required>
                </div>

                <div class="form-group">
                    <label for="user_password">Reset Password <small class="text-muted">(Leave empty to retain current password)</small></label>
                    <input type="password" class="form-control" id="user_password" name="user_password" placeholder="Leave blank to keep unchanged">
                </div>

                <div class="form-row-3">
                    <div class="form-group">
                        <label for="user_role">Role <span class="text-danger">*</span></label>
                        <select name="user_role" id="user_role" class="form-control" required>
                            <option value="Editor" <?php echo ($user_role === 'Editor') ? 'selected' : ''; ?>>Editor</option>
                            <option value="Admin" <?php echo ($user_role === 'Admin') ? 'selected' : ''; ?>>Admin</option>
                            <?php if ($currentUser['role'] === 'SuperAdmin'): ?>
                                <option value="SuperAdmin" <?php echo ($user_role === 'SuperAdmin') ? 'selected' : ''; ?>>SuperAdmin</option>
                            <?php endif; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="user_department">Department</label>
                        <select name="user_department" id="user_department" class="form-control">
                            <option value="COECSA" <?php echo ($user_department === 'COECSA') ? 'selected' : ''; ?>>COECSA</option>
                            <option value="CITHM" <?php echo ($user_department === 'CITHM') ? 'selected' : ''; ?>>CITHM</option>
                            <option value="CAMS" <?php echo ($user_department === 'CAMS') ? 'selected' : ''; ?>>CAMS</option>
                            <option value="Administration" <?php echo ($user_department === 'Administration') ? 'selected' : ''; ?>>Administration</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="user_position">Position</label>
                        <select name="user_position" id="user_position" class="form-control">
                            <option value="Teacher" <?php echo ($user_position === 'Teacher') ? 'selected' : ''; ?>>Teacher</option>
                            <option value="Principal" <?php echo ($user_position === 'Principal') ? 'selected' : ''; ?>>Principal</option>
                            <option value="IT Personnel" <?php echo ($user_position === 'IT Personnel') ? 'selected' : ''; ?>>IT Personnel</option>
                            <option value="Staff" <?php echo ($user_position === 'Staff') ? 'selected' : ''; ?>>Staff</option>
                        </select>
                    </div>
                </div>

                <div class="form-row-2">
                    <div class="form-group">
                        <label for="school_id">Associated School</label>
                        <select name="school_id" id="school_id" class="form-control">
                            <option value="">-- Unassigned / Platform Wide --</option>
                            <?php foreach ($schools as $s): ?>
                                <option value="<?php echo (int)$s['school_id']; ?>" <?php echo ((int)$school_id === (int)$s['school_id']) ? 'selected' : ''; ?>>
                                    <?php echo e($s['school_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="user_contact_number">Contact Number</label>
                        <input type="text" class="form-control" id="user_contact_number" name="user_contact_number" value="<?php echo e($user_contact_number); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="user_address">Address</label>
                    <input type="text" class="form-control" id="user_address" name="user_address" value="<?php echo e($user_address); ?>">
                </div>

                <div class="form-actions mt-4">
                    <button type="submit" name="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save"></i> Save Changes
                    </button>
                    <a href="user_management.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/includes/layout_footer.php'; ?>
