<?php
/**
 * User Management Directory
 * CWebS - Multi-Tenant Public School CMS
 */

require_once __DIR__ . '/connect.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/flash.php';

// RBAC Guard: SuperAdmin and Admin only
require_role(['SuperAdmin', 'Admin']);

$currentUser = current_user();
$page_title = 'User Management';
require_once __DIR__ . '/includes/layout_header.php';

// Fetch users with their associated school name
$users = [];
$query = "
    SELECT u.user_id, u.user_first_name, u.user_last_name, u.user_email, 
           u.user_role, u.user_department, u.user_position, u.user_contact_number, 
           u.user_address, u.school_id, s.school_name
    FROM `users` u
    LEFT JOIN `school` s ON u.school_id = s.school_id
";

// If Admin has an assigned school, restrict view to that school's users
if ($currentUser['role'] === 'Admin' && !empty($currentUser['school_id'])) {
    $query .= " WHERE u.school_id = ? ORDER BY u.user_id ASC";
    $stmt = $con->prepare($query);
    if ($stmt) {
        $stmt->bind_param("i", $currentUser['school_id']);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        $stmt->close();
    }
} else {
    $query .= " ORDER BY u.user_id ASC";
    $stmt = $con->prepare($query);
    if ($stmt) {
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
        $stmt->close();
    }
}
?>

<div class="dashboard-layout">
    <aside class="dashboard-sidebar-wrapper">
        <?php include __DIR__ . '/includes/sidebar.php'; ?>
    </aside>

    <section class="dashboard-main-content">
        <div class="dashboard-card">
            <div class="card-header-flex">
                <div>
                    <h2><i class="fa-solid fa-users-gear"></i> User Management</h2>
                    <p class="text-muted">Provision, edit, and configure user accounts, roles, and departmental permissions.</p>
                </div>
                <div>
                    <a href="add_user.php" class="btn btn-primary">
                        <i class="fa-solid fa-user-plus"></i> Add New User
                    </a>
                </div>
            </div>

            <div class="table-responsive mt-4">
                <table class="data-table user-table">
                    <thead>
                        <tr>
                            <th style="width: 50px;">ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Department</th>
                            <th>Position</th>
                            <th>School</th>
                            <th style="width: 170px;" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($users)): ?>
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">
                                    <i class="fa-solid fa-users-slash"></i> No user accounts found. Click "Add New User" to create one.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($users as $u): ?>
                                <tr>
                                    <td><strong>#<?php echo e($u['user_id']); ?></strong></td>
                                    <td>
                                        <div class="font-medium"><?php echo e(trim($u['user_first_name'] . ' ' . $u['user_last_name'])); ?></div>
                                        <?php if (!empty($u['user_address'])): ?>
                                            <div class="text-muted text-xs"><?php echo e($u['user_address']); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="mailto:<?php echo e($u['user_email']); ?>" class="text-link"><?php echo e($u['user_email']); ?></a>
                                    </td>
                                    <td>
                                        <span class="badge badge-<?php echo strtolower($u['user_role']); ?>">
                                            <?php echo e($u['user_role']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo e($u['user_department'] ?: '&mdash;'); ?></td>
                                    <td><?php echo e($u['user_position'] ?: '&mdash;'); ?></td>
                                    <td><?php echo e($u['school_name'] ?: 'Platform-wide'); ?></td>
                                    <td class="text-center">
                                        <div class="action-buttons">
                                            <a href="edit_user.php?edit_user_id=<?php echo (int)$u['user_id']; ?>" class="btn btn-sm btn-secondary" title="Edit User">
                                                <i class="fa-solid fa-pen"></i> Edit
                                            </a>

                                            <?php if ((int)$u['user_id'] !== (int)$currentUser['id']): ?>
                                                <form method="POST" action="delete_user.php" class="d-inline" onsubmit="return confirm('Are you sure you want to delete <?php echo e(addslashes($u['user_first_name'])); ?>?');">
                                                    <?php echo csrf_field(); ?>
                                                    <input type="hidden" name="user_id" value="<?php echo (int)$u['user_id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete User">
                                                        <i class="fa-solid fa-trash"></i>
                                                    </button>
                                                </form>
                                            <?php else: ?>
                                                <span class="btn btn-sm btn-disabled" title="You cannot delete yourself">
                                                    <i class="fa-solid fa-lock"></i>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/includes/layout_footer.php'; ?>