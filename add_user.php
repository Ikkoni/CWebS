<?php
/**
 * Add User Gateway
 * CWebS - Multi-Tenant Public School CMS
 */

require_once __DIR__ . '/connect.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/flash.php';

// RBAC Guard: SuperAdmin and Admin
require_role(['SuperAdmin', 'Admin']);

$currentUser = current_user();

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

$user_first_name = '';
$user_last_name = '';
$user_email = '';
$user_role = 'Editor';
$user_department = 'COECSA';
$user_position = 'Teacher';
$user_contact_number = '';
$user_address = '';
$school_id = ($currentUser['role'] === 'Admin') ? ($currentUser['school_id'] ?? null) : null;
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf_token()) {
        $errors[] = "Security token mismatch. Please try again.";
    } else {
        $user_first_name = trim($_POST['user_first_name'] ?? '');
        $user_last_name = trim($_POST['user_last_name'] ?? '');
        $user_email = trim($_POST['user_email'] ?? '');
        $user_password = $_POST['user_password'] ?? '';
        $user_confirm_password = $_POST['user_confirm_password'] ?? '';
        $user_role = $_POST['user_role'] ?? 'Editor';
        $user_department = trim($_POST['user_department'] ?? '');
        $user_position = trim($_POST['user_position'] ?? '');
        $user_contact_number = trim($_POST['user_contact_number'] ?? '');
        $user_address = trim($_POST['user_address'] ?? '');
        $selected_school = !empty($_POST['school_id']) ? (int)$_POST['school_id'] : null;

        // Role privilege boundary: Admin cannot create SuperAdmin
        if ($currentUser['role'] === 'Admin' && $user_role === 'SuperAdmin') {
            $errors[] = "Admins cannot provision SuperAdmin accounts.";
        }

        // Validate basic inputs
        if (empty($user_first_name) || empty($user_last_name)) {
            $errors[] = "First and Last Name are required.";
        }
        if (empty($user_email) || !filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "A valid Email address is required.";
        }
        if (empty($user_password)) {
            $errors[] = "Password is required.";
        } elseif (strlen($user_password) < 6) {
            $errors[] = "Password must be at least 6 characters long.";
        }
        if ($user_password !== $user_confirm_password) {
            $errors[] = "Password and Confirm Password do not match.";
        }

        if (empty($errors)) {
            // Check for duplicate email with prepared statement
            $dupStmt = $con->prepare("SELECT user_id FROM `users` WHERE `user_email` = ? LIMIT 1");
            if ($dupStmt) {
                $dupStmt->bind_param("s", $user_email);
                $dupStmt->execute();
                $dupResult = $dupStmt->get_result();
                if ($dupResult->num_rows > 0) {
                    $errors[] = "An account with this email address already exists.";
                }
                $dupStmt->close();
            }

            if (empty($errors)) {
                $hashedPassword = password_hash($user_password, PASSWORD_DEFAULT);
                $contactInt = (int)preg_replace('/[^0-9]/', '', $user_contact_number);

                $insertStmt = $con->prepare("
                    INSERT INTO `users` 
                    (`user_first_name`, `user_last_name`, `user_email`, `user_password`, `user_role`, `user_department`, `user_position`, `user_contact_number`, `user_address`, `school_id`)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
                ");

                if ($insertStmt) {
                    $insertStmt->bind_param(
                        "sssssssisi",
                        $user_first_name,
                        $user_last_name,
                        $user_email,
                        $hashedPassword,
                        $user_role,
                        $user_department,
                        $user_position,
                        $contactInt,
                        $user_address,
                        $selected_school
                    );

                    if ($insertStmt->execute()) {
                        set_flash('success', "User '{$user_first_name} {$user_last_name}' ({$user_role}) created successfully.");
                        header('Location: user_management.php');
                        exit;
                    } else {
                        $errors[] = "Database error: Unable to create user record.";
                    }
                    $insertStmt->close();
                } else {
                    $errors[] = "Failed to prepare database statement.";
                }
            }
        }
    }
}

$page_title = 'Add User';
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
                    <h2><i class="fa-solid fa-user-plus"></i> Provision New User</h2>
                    <p class="text-muted">Create an account and assign educational department roles.</p>
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

            <form method="POST" action="add_user.php" class="mt-4 form-grid">
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
                    <input type="email" class="form-control" id="user_email" name="user_email" placeholder="user@school.edu" value="<?php echo e($user_email); ?>" required>
                </div>

                <div class="form-row-2">
                    <div class="form-group">
                        <label for="user_password">Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="user_password" name="user_password" placeholder="At least 6 characters" required>
                    </div>

                    <div class="form-group">
                        <label for="user_confirm_password">Confirm Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="user_confirm_password" name="user_confirm_password" required>
                    </div>
                </div>

                <div class="form-row-3">
                    <div class="form-group">
                        <label for="user_role">Role <span class="text-danger">*</span></label>
                        <select name="user_role" id="user_role" class="form-control" required>
                            <option value="Editor" <?php echo ($user_role === 'Editor') ? 'selected' : ''; ?>>Editor (Content Only)</option>
                            <option value="Admin" <?php echo ($user_role === 'Admin') ? 'selected' : ''; ?>>Admin (School Admin)</option>
                            <?php if ($currentUser['role'] === 'SuperAdmin'): ?>
                                <option value="SuperAdmin" <?php echo ($user_role === 'SuperAdmin') ? 'selected' : ''; ?>>SuperAdmin (Full Platform)</option>
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
                        <i class="fa-solid fa-check"></i> Create User
                    </button>
                    <a href="user_management.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/includes/layout_footer.php'; ?>
