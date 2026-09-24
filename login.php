<?php
/**
 * Authentication / Login Gateway
 * CWebS - Multi-Tenant Public School CMS
 */

require_once __DIR__ . '/connect.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/flash.php';

// If already logged in, redirect straight to dashboard
if (is_logged_in()) {
    header('Location: dashboard_nav.php');
    exit;
}

$error = '';
$user_email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_email = trim($_POST['user_email'] ?? '');
    $user_password = $_POST['user_password'] ?? '';

    // Verify CSRF token
    if (!verify_csrf_token()) {
        $error = "Session expired or invalid security token. Please try again.";
    } elseif (empty($user_email) || empty($user_password)) {
        $error = "Please enter both email and password.";
    } else {
        // Parameterized prepared query to prevent SQL injection
        $stmt = $con->prepare("SELECT user_id, user_first_name, user_last_name, user_email, user_password, user_role, user_department, user_position, user_contact_number, user_address, school_id FROM `users` WHERE `user_email` = ? LIMIT 1");
        if ($stmt) {
            $stmt->bind_param("s", $user_email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($user = $result->fetch_assoc()) {
                if (password_verify($user_password, $user['user_password'])) {
                    // Session Fixation Protection: regenerate session ID on privilege change
                    session_regenerate_id(true);

                    // Re-seed CSRF token for the new session
                    unset($_SESSION['csrf_token']);

                    // Set user session data (excluding password)
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['user_first_name'] = $user['user_first_name'];
                    $_SESSION['user_last_name'] = $user['user_last_name'];
                    $_SESSION['user_email'] = $user['user_email'];
                    $_SESSION['user_role'] = $user['user_role'];
                    $_SESSION['user_department'] = $user['user_department'];
                    $_SESSION['user_position'] = $user['user_position'];
                    $_SESSION['user_contact_number'] = $user['user_contact_number'];
                    $_SESSION['user_address'] = $user['user_address'];
                    $_SESSION['school_id'] = $user['school_id'];

                    set_flash('success', "Welcome back, " . ($user['user_first_name'] ?: 'User') . "!");
                    header('Location: dashboard_nav.php');
                    exit;
                } else {
                    // Generic error message to prevent user enumeration
                    $error = "Invalid email or password.";
                }
            } else {
                $error = "Invalid email or password.";
            }
            $stmt->close();
        } else {
            $error = "Internal system error. Please try again later.";
        }
    }
}

$page_title = 'Login';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo e(APP_NAME); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="auth-page">
    <header class="header">
        <div class="container header-content">
            <a href="home.php" class="logo">
                <span>🏫</span>
                <span><?php echo e(APP_NAME); ?></span>
            </a>
            <nav class="nav-links">
                <a href="home.php" class="btn btn-sm btn-secondary"><i class="fa-solid fa-arrow-left"></i> Return Home</a>
            </nav>
        </div>
    </header>

    <main class="auth-main">
        <div class="auth-card">
            <div class="auth-header">
                <div class="auth-icon"><i class="fa-solid fa-school-flag"></i></div>
                <h2>Sign in to CWebS</h2>
                <p>Public School Website &amp; Content Management Portal</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger" role="alert">
                    <span class="alert-icon">✕</span>
                    <div class="alert-content"><?php echo e($error); ?></div>
                </div>
            <?php endif; ?>

            <?php echo render_flashes(); ?>

            <form method="POST" action="login.php" class="auth-form" autocomplete="on">
                <?php echo csrf_field(); ?>

                <div class="form-group">
                    <label for="user_email"><i class="fa-regular fa-envelope"></i> School Email Address</label>
                    <input 
                        type="email" 
                        class="form-control" 
                        id="user_email" 
                        name="user_email" 
                        placeholder="e.g. admin@school.edu" 
                        value="<?php echo e($user_email); ?>" 
                        required 
                        autofocus
                    >
                </div>

                <div class="form-group">
                    <label for="user_password"><i class="fa-solid fa-lock"></i> Password</label>
                    <div class="password-wrapper">
                        <input 
                            type="password" 
                            class="form-control" 
                            id="user_password" 
                            name="user_password" 
                            placeholder="Enter your account password" 
                            required
                        >
                        <button type="button" class="btn-toggle-pw" onclick="togglePasswordVisibility('user_password', this)" aria-label="Toggle password view">
                            <i class="fa-regular fa-eye-slash"></i>
                        </button>
                    </div>
                </div>

                <div class="form-actions mt-4">
                    <button type="submit" name="login" class="btn btn-primary btn-block">
                        <i class="fa-solid fa-right-to-bracket"></i> Sign In to Portal
                    </button>
                </div>
            </form>

            <div class="auth-footer">
                <p class="text-muted text-sm">Need help or role provisioning? Contact your district SuperAdmin.</p>
            </div>
        </div>
    </main>

    <footer class="footer">
        <div class="container footer-content">
            <p>&copy; <?php echo date('Y'); ?> <?php echo e(APP_NAME); ?> &mdash; Public Schools CMS. All rights reserved.</p>
        </div>
    </footer>

    <script>
        function togglePasswordVisibility(fieldId, btn) {
            const field = document.getElementById(fieldId);
            const icon = btn.querySelector('i');
            if (field.type === 'password') {
                field.type = 'text';
                icon.className = 'fa-regular fa-eye';
            } else {
                field.type = 'password';
                icon.className = 'fa-regular fa-eye-slash';
            }
        }
    </script>
</body>
</html>
