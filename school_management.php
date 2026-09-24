<?php
/**
 * School Management (List & Directory)
 * CWebS - Multi-Tenant Public School CMS
 */

require_once __DIR__ . '/connect.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/flash.php';

// RBAC Guard: Only SuperAdmin can access school administration
require_role('SuperAdmin');

$page_title = 'Manage Schools';
require_once __DIR__ . '/includes/layout_header.php';

// Fetch all schools with prepared statement
$schools = [];
$stmt = $con->prepare("SELECT school_id, school_name, school_address, school_contact_number, school_email FROM `school` ORDER BY school_id ASC");
if ($stmt) {
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $schools[] = $row;
    }
    $stmt->close();
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
                    <h2><i class="fa-solid fa-school"></i> Manage Schools</h2>
                    <p class="text-muted">Register, edit, or configure institutions participating in the CWebS network.</p>
                </div>
                <div>
                    <a href="add_school.php" class="btn btn-primary">
                        <i class="fa-solid fa-plus"></i> Add New School
                    </a>
                </div>
            </div>

            <div class="table-responsive mt-4">
                <table class="data-table school-table">
                    <thead>
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th>School Name</th>
                            <th>Address</th>
                            <th>Contact Number</th>
                            <th>Official Email</th>
                            <th style="width: 170px;" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($schools)): ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fa-solid fa-folder-open"></i> No schools registered yet. Click "Add New School" to create one.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($schools as $school): ?>
                                <tr>
                                    <td><strong>#<?php echo e($school['school_id']); ?></strong></td>
                                    <td>
                                        <div class="font-medium"><?php echo e($school['school_name']); ?></div>
                                    </td>
                                    <td><?php echo e($school['school_address']); ?></td>
                                    <td><?php echo e($school['school_contact_number']); ?></td>
                                    <td><a href="mailto:<?php echo e($school['school_email']); ?>" class="text-link"><?php echo e($school['school_email']); ?></a></td>
                                    <td class="text-center">
                                        <div class="action-buttons">
                                            <a href="edit_school.php?editschool_id=<?php echo (int)$school['school_id']; ?>" class="btn btn-sm btn-secondary" title="Edit School">
                                                <i class="fa-solid fa-pen"></i> Edit
                                            </a>
                                            <form method="POST" action="delete_school.php" class="d-inline" onsubmit="return confirm('Are you sure you want to delete <?php echo e(addslashes($school['school_name'])); ?>? This will cascade delete associated accounts.');">
                                                <?php echo csrf_field(); ?>
                                                <input type="hidden" name="school_id" value="<?php echo (int)$school['school_id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete School">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </form>
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