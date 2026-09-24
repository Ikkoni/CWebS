<?php
/**
 * Account Profile & Security Settings
 * CWebS - Multi-Tenant Public School CMS
 */

require_once __DIR__ . '/connect.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/flash.php';

// Route Guard: Authentication required
require_login();

// Protect against IDOR: Always bind strictly to the logged-in session user ID
$user_id = (int)$_SESSION['user_id'];

// Fetch latest user details with prepared statement
$stmt = $con->prepare("
    SELECT u.user_id, u.user_first_name, u.user_last_name, u.user_email, 
           u.user_role, u.user_department, u.user_position, u.user_contact_number, 
           u.user_address, u.school_id, s.school_name 
    FROM `users` u
    LEFT JOIN `school` s ON u.school_id = s.school_id
    WHERE u.user_id = ? 
    LIMIT 1
");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$res = $stmt->get_result();

if (!$user = $res->fetch_assoc()) {
    $stmt->close();
    set_flash('error', 'Unable to retrieve user profile.');
    header('Location: logout.php');
    exit;
}
$stmt->close();

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
        $user_contact_number = trim($_POST['user_contact_number'] ?? '');
        $user_address = trim($_POST['user_address'] ?? '');

        if (empty($user_first_name) || empty($user_last_name)) {
            $errors[] = "First and Last Name are required.";
        }
        if (empty($user_email) || !filter_var($user_email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = "A valid Email address is required.";
        }

        // Email uniqueness check (excluding current user)
        $dupStmt = $con->prepare("SELECT user_id FROM `users` WHERE `user_email` = ? AND `user_id` != ? LIMIT 1");
        if ($dupStmt) {
            $dupStmt->bind_param("si", $user_email, $user_id);
            $dupStmt->execute();
            $dupRes = $dupStmt->get_result();
            if ($dupRes->num_rows > 0) {
                $errors[] = "This email is already in use by another account.";
            }
            $dupStmt->close();
        }

        // Optional password change
        $updatePassword = false;
        $hashed_password = null;
        if (!empty($user_password) || !empty($user_confirm_password)) {
            if ($user_password !== $user_confirm_password) {
                $errors[] = "New password and confirmation password do not match.";
            } elseif (strlen($user_password) < 6) {
                $errors[] = "New password must be at least 6 characters long.";
            } else {
                $updatePassword = true;
                $hashed_password = password_hash($user_password, PASSWORD_DEFAULT);
            }
        }

        if (empty($errors)) {
            $contactInt = (int)preg_replace('/[^0-9]/', '', $user_contact_number);

            if ($updatePassword) {
                $updateStmt = $con->prepare("
                    UPDATE `users` 
                    SET `user_first_name` = ?, `user_last_name` = ?, `user_email` = ?, 
                        `user_password` = ?, `user_contact_number` = ?, `user_address` = ?
                    WHERE `user_id` = ?
                ");
                $updateStmt->bind_param("ssssisi", $user_first_name, $user_last_name, $user_email, $hashed_password, $contactInt, $user_address, $user_id);
            } else {
                $updateStmt = $con->prepare("
                    UPDATE `users` 
                    SET `user_first_name` = ?, `user_last_name` = ?, `user_email` = ?, 
                        `user_contact_number` = ?, `user_address` = ?
                    WHERE `user_id` = ?
                ");
                $updateStmt->bind_param("sssisi", $user_first_name, $user_last_name, $user_email, $contactInt, $user_address, $user_id);
            }

            if ($updateStmt && $updateStmt->execute()) {
                // Refresh session values (NEVER store passwords in session!)
                $_SESSION['user_first_name'] = $user_first_name;
                $_SESSION['user_last_name'] = $user_last_name;
                $_SESSION['user_email'] = $user_email;
                $_SESSION['user_contact_number'] = $contactInt;
                $_SESSION['user_address'] = $user_address;

                set_flash('success', 'Your account settings were successfully updated.');
                header('Location: account.php');
                exit;
            } else {
                $errors[] = "Database error: Unable to update account.";
            }
            if ($updateStmt) {
                $updateStmt->close();
            }
        }
    }
}

$page_title = 'My Account';
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
                    <h2><i class="fa-solid fa-user-gear"></i> Account Settings</h2>
                    <p class="text-muted">Manage your personal profile information, security credentials, and preferences.</p>
                </div>
                <div>
                    <span class="badge badge-<?php echo strtolower($user['user_role']); ?>"><?php echo e($user['user_role']); ?></span>
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

            <form method="POST" action="account.php" class="mt-4 form-grid">
                <?php echo csrf_field(); ?>

                <div class="form-row-2">
                    <div class="form-group">
                        <label for="user_first_name">First Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="user_first_name" name="user_first_name" value="<?php echo e($user['user_first_name']); ?>" required>
                    </div>

                    <div class="form-group">
                        <label for="user_last_name">Last Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="user_last_name" name="user_last_name" value="<?php echo e($user['user_last_name']); ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label for="user_email">Email Address <span class="text-danger">*</span></label>
                    <input type="email" class="form-control" id="user_email" name="user_email" value="<?php echo e($user['user_email']); ?>" required>
                </div>

                <div class="form-row-2">
                    <div class="form-group">
                        <label for="user_department">Department</label>
                        <input type="text" class="form-control" id="user_department" value="<?php echo e($user['user_department'] ?: 'N/A'); ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label for="user_position">Position</label>
                        <input type="text" class="form-control" id="user_position" value="<?php echo e($user['user_position'] ?: 'N/A'); ?>" disabled>
                    </div>
                </div>

                <div class="form-row-2">
                    <div class="form-group">
                        <label for="school_name">Assigned School</label>
                        <input type="text" class="form-control" id="school_name" value="<?php echo e($user['school_name'] ?: 'Platform-wide / District'); ?>" disabled>
                    </div>

                    <div class="form-group">
                        <label for="user_contact_number">Contact Number</label>
                        <input type="text" class="form-control" id="user_contact_number" name="user_contact_number" value="<?php echo e($user['user_contact_number'] ?: ''); ?>">
                    </div>
                </div>

                <div class="form-group">
                    <label for="user_address">Address</label>
                    <input type="text" class="form-control" id="user_address" name="user_address" value="<?php echo e($user['user_address']); ?>">
                </div>

                <hr class="form-divider">

                <div class="form-section-title">
                    <h4><i class="fa-solid fa-lock"></i> Change Password</h4>
                    <p class="text-muted text-sm">Leave these fields blank if you do not wish to change your password.</p>
                </div>

                <div class="form-row-2">
                    <div class="form-group">
                        <label for="user_password">New Password</label>
                        <input type="password" class="form-control" id="user_password" name="user_password" placeholder="New password (min 6 characters)">
                    </div>

                    <div class="form-group">
                        <label for="user_confirm_password">Confirm New Password</label>
                        <input type="password" class="form-control" id="user_confirm_password" name="user_confirm_password" placeholder="Confirm new password">
                    </div>
                </div>

                <div class="form-actions mt-4">
                    <button type="submit" name="submit" class="btn btn-primary">
                        <i class="fa-solid fa-save"></i> Update Profile
                    </button>
                    <a href="dashboard_nav.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/includes/layout_footer.php'; ?>