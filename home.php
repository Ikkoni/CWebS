<?php
/**
 * Landing Page
 * CWebS - Custom Website Builder and CMS for Public Schools
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/flash.php';

$user = current_user();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CWebS: Custom Website Builder and CMS for Public Schools</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>
    <header class="header">
        <div class="container header-content">
            <a href="home.php" class="logo">
                <span class="logo-icon">🏫</span>
                <span class="logo-title"><?php echo e(APP_NAME); ?></span>
            </a>
            <nav class="nav-links">
                <?php if (is_logged_in()): ?>
                    <div class="user-meta">
                        <span class="user-name"><i class="fa-regular fa-user"></i> <?php echo e($user['full_name'] ?: $user['email']); ?></span>
                        <span class="badge badge-<?php echo strtolower($user['role']); ?>"><?php echo e($user['role']); ?></span>
                    </div>
                    <a href="dashboard_nav.php" class="btn btn-primary"><i class="fa-solid fa-table-columns"></i> Go to Dashboard</a>
                    <a href="logout.php" class="btn btn-secondary"><i class="fa-solid fa-right-from-bracket"></i> Log Out</a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary"><i class="fa-solid fa-right-to-bracket"></i> Login</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <div class="flash-container container mt-4">
        <?php echo render_flashes(); ?>
    </div>

    <div id="index">
        <section class="hero">
            <div class="wave-container">
                <svg class="wave" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320" preserveAspectRatio="none">
                    <path fill-opacity="1" d="M0,64L48,80C96,96,192,128,288,128C384,128,480,96,576,90.7C672,85,768,107,864,122.7C960,139,1056,149,1152,138.7C1248,128,1344,96,1392,80L1440,64L1440,320L1392,320C1344,320,1248,320,1152,320C1056,320,960,320,864,320C768,320,672,320,576,320C480,320,384,320,288,320C192,320,96,320,48,320L0,320Z"></path>
                </svg>
            </div>
            <div class="container hero-content">
                <h1 class="hero-title">Welcome to <?php echo e(APP_NAME); ?></h1>
                <p class="hero-description">Your simple website builder and content management solution for public schools. Empower your educators and staff to publish, customize, and communicate with ease.</p>
                <div class="hero-buttons">
                    <?php if (is_logged_in()): ?>
                        <a href="dashboard_nav.php" class="btn btn-primary btn-lg"><i class="fa-solid fa-gauge"></i> Open Dashboard</a>
                        <a href="edit_webpage.php" class="btn btn-secondary btn-lg"><i class="fa-solid fa-pen-to-square"></i> Open CMS Editor</a>
                    <?php else: ?>
                        <a href="login.php" class="btn btn-primary btn-lg"><i class="fa-solid fa-arrow-right"></i> Get Started</a>
                        <button class="btn btn-secondary btn-lg" id="learnMoreBtn"><i class="fa-solid fa-circle-info"></i> Learn More</button>
                    <?php endif; ?>
                </div>
            </div>
        </section>

        <section class="features">
            <div class="container">
                <div class="section-title text-center">
                    <h2>Everything Your School Needs</h2>
                    <p class="text-muted">Designed specifically to meet public education web management requirements.</p>
                </div>
                <div class="features-grid">
                    <div class="feature-card">
                        <div class="feature-icon">🚀</div>
                        <h3>Easy Setup</h3>
                        <p>Get your school website up and running in minutes with our intuitive setup process and zero coding needed.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">🎨</div>
                        <h3>Customizable Templates</h3>
                        <p>Choose from clean, accessible templates designed specifically for primary and secondary public schools.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">📝</div>
                        <h3>Content Management</h3>
                        <p>Empower teachers and editors to update department notices, schedules, and bulletins with role isolation.</p>
                    </div>
                    <div class="feature-card">
                        <div class="feature-icon">🛡️</div>
                        <h3>Role-Based Security</h3>
                        <p>Granular RBAC controls ensuring SuperAdmins, Principals, and Content Editors only access their authorized scope.</p>
                    </div>
                </div>
            </div>
        </section>
    </div>

    <footer class="footer">
        <div class="container footer-content">
            <p>&copy; <?php echo date('Y'); ?> <?php echo e(APP_NAME); ?> &mdash; Custom Website Builder and CMS for Public Schools.</p>
        </div>
    </footer>

    <script>
        const learnMoreBtn = document.getElementById("learnMoreBtn");
        if (learnMoreBtn) {
            learnMoreBtn.addEventListener("click", () => {
                document.querySelector(".features").scrollIntoView({ behavior: "smooth" });
            });
        }
    </script>
</body>
</html>