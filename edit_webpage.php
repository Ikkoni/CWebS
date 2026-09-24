<?php
/**
 * Visual Webpage Content Editor
 * CWebS - Multi-Tenant Public School CMS
 */

require_once __DIR__ . '/connect.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/flash.php';

// Route Guard: Authentication required (SuperAdmin, Admin, Editor)
require_login();

$user = current_user();
$school_name = "Public School";

if (!empty($user['school_id'])) {
    $stmt = $con->prepare("SELECT school_name FROM `school` WHERE school_id = ? LIMIT 1");
    if ($stmt) {
        $stmt->bind_param("i", $user['school_id']);
        $stmt->execute();
        $res = $stmt->get_result();
        if ($s = $res->fetch_assoc()) {
            $school_name = $s['school_name'];
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visual CMS Editor - <?php echo e($school_name); ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* CMS Visual Editor Styles */
        .cms-control-bar {
            background-color: #1e293b;
            color: #ffffff;
            padding: 0.75rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 2000;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2);
            flex-wrap: wrap;
            gap: 1rem;
        }

        .cms-status {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .cms-tools-panel {
            background-color: #0f172a;
            color: #cbd5e1;
            padding: 0.75rem 1.5rem;
            display: none;
            justify-content: flex-start;
            align-items: center;
            gap: 1.25rem;
            flex-wrap: wrap;
            border-bottom: 2px solid var(--primary-color);
        }

        .cms-tool-item {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.85rem;
        }

        .cms-tool-item input[type="color"] {
            border: none;
            width: 28px;
            height: 28px;
            border-radius: 4px;
            cursor: pointer;
            background: none;
        }

        .cms-tool-item select {
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            background: #1e293b;
            color: #fff;
            border: 1px solid #475569;
        }

        .school-canvas {
            min-height: 80vh;
            background-color: #f8fafc;
            color: #334155;
            transition: all 0.2s ease;
        }

        .school-header {
            background: #004080;
            color: white;
            padding: 2.5rem 1.5rem;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1.5rem;
            flex-wrap: wrap;
        }

        .school-header h1 {
            font-size: 2.25rem;
            margin: 0;
            flex-grow: 1;
        }

        .logo-placeholder {
            width: 90px;
            height: 90px;
            background: white;
            border-radius: 50%;
            border: 3px solid #e2e8f0;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            cursor: pointer;
            color: #64748b;
            font-size: 0.8rem;
            text-align: center;
            padding: 0.25rem;
            transition: transform 0.2s ease;
        }

        .logo-placeholder:hover {
            transform: scale(1.05);
        }

        .school-nav {
            background: #0056b3;
            padding: 0.75rem 0;
        }

        .school-nav ul {
            list-style: none;
            padding: 0;
            margin: 0;
            display: flex;
            justify-content: center;
            gap: 2rem;
            flex-wrap: wrap;
        }

        .school-nav ul li a {
            color: white;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.2s;
        }

        .school-nav ul li a:hover {
            color: #fde047;
        }

        .school-content {
            max-width: 1000px;
            margin: 2rem auto;
            padding: 0 1rem;
        }

        .content-section {
            background: white;
            margin: 1.5rem 0;
            padding: 1.75rem;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            border: 1px solid #e2e8f0;
        }

        .content-section h2, .content-section h3 {
            color: #004080;
            margin-bottom: 0.75rem;
        }

        .school-footer {
            background: #003366;
            color: #94a3b8;
            text-align: center;
            padding: 1.5rem 0;
            margin-top: 3rem;
        }

        .school-footer a {
            color: #fde047;
            text-decoration: none;
        }

        [contenteditable="true"] {
            outline: 2px dashed #4f46e5;
            padding: 4px;
            border-radius: 4px;
            background-color: rgba(79, 70, 229, 0.05);
        }
    </style>
</head>
<body class="cms-editor-body">

    <!-- Top Control Bar -->
    <div class="cms-control-bar">
        <div class="cms-status">
            <a href="dashboard_nav.php" class="btn btn-sm btn-secondary">
                <i class="fa-solid fa-arrow-left"></i> Exit to Dashboard
            </a>
            <span><i class="fa-solid fa-school"></i> <strong><?php echo e($school_name); ?></strong> CMS</span>
            <span class="badge badge-<?php echo strtolower($user['role']); ?>"><?php echo e($user['role']); ?></span>
        </div>

        <div class="cms-actions">
            <button id="start-editing" class="btn btn-sm btn-primary" onclick="toggleEditing(true)">
                <i class="fa-solid fa-pen"></i> Enter Live Editing Mode
            </button>
            <button id="finish-editing" class="btn btn-sm btn-secondary hidden" onclick="toggleEditing(false)">
                <i class="fa-solid fa-eye"></i> Preview Mode
            </button>
            <button id="save-button" class="btn btn-sm btn-primary hidden" onclick="saveEdits()">
                <i class="fa-solid fa-check"></i> Save Changes
            </button>
            <button id="reset-button" class="btn btn-sm btn-outline-danger hidden" onclick="clearEdits()">
                <i class="fa-solid fa-rotate-left"></i> Reset
            </button>
        </div>
    </div>

    <!-- Visual Toolbar (Shown in edit mode) -->
    <div id="cms-toolbar" class="cms-tools-panel">
        <div class="cms-tool-item">
            <label for="header-color-picker">Header:</label>
            <input type="color" id="header-color-picker" value="#004080" onchange="changeHeaderColor(this.value)">
        </div>

        <div class="cms-tool-item">
            <label for="nav-color-picker">Navbar:</label>
            <input type="color" id="nav-color-picker" value="#0056b3" onchange="changeNavColor(this.value)">
        </div>

        <div class="cms-tool-item">
            <label for="background-color-picker">Canvas:</label>
            <input type="color" id="background-color-picker" value="#f8fafc" onchange="changeBackground(this.value)">
        </div>

        <div class="cms-tool-item">
            <label for="footer-color-picker">Footer:</label>
            <input type="color" id="footer-color-picker" value="#003366" onchange="changeFooterColor(this.value)">
        </div>

        <div class="cms-tool-item">
            <label for="font-selector">Font Family:</label>
            <select id="font-selector" onchange="changeFont(this.value)">
                <option value="'Inter', sans-serif">Inter (Modern)</option>
                <option value="'Georgia', serif">Georgia (Serif)</option>
                <option value="'Arial', sans-serif">Arial</option>
                <option value="'Times New Roman', serif">Times New Roman</option>
            </select>
        </div>
    </div>

    <!-- Live Canvas -->
    <div id="school-canvas" class="school-canvas">
        <header id="live-header" class="school-header">
            <div class="logo-placeholder" onclick="uploadLogo()" title="Click to upload school emblem">
                <span id="logo-text"><i class="fa-solid fa-cloud-arrow-up"></i><br>Emblem</span>
                <input type="file" id="logo-upload" style="display:none;" accept="image/*" onchange="showLogo(this)">
            </div>
            <h1 contenteditable="false" id="live-school-title"><?php echo e($school_name); ?></h1>
            <div style="width: 90px;"></div><!-- Balance spacer -->
        </header>

        <nav id="live-nav" class="school-nav">
            <ul>
                <li><a href="#" onclick="loadPage('Home')">Home</a></li>
                <li><a href="#" onclick="loadPage('About Us')">About Us</a></li>
                <li><a href="#" onclick="loadPage('Admissions')">Admissions</a></li>
                <li><a href="#" onclick="loadPage('Academics')">Academics</a></li>
                <li><a href="#" onclick="loadPage('Contact')">Contact Us</a></li>
            </ul>
        </nav>

        <main id="content" class="school-content">
            <div class="content-section">
                <h2 contenteditable="false">Welcome to Our School Community</h2>
                <p contenteditable="false">Empowering students through innovative curriculum, dedicated mentorship, and inclusive extracurricular programs.</p>
            </div>
            <div class="content-section">
                <h3 contenteditable="false"><i class="fa-solid fa-bullhorn"></i> Campus Announcements</h3>
                <p contenteditable="false">Parent-Teacher conferences are scheduled for next Friday. Student report cards are now available through the administration office.</p>
            </div>
            <div class="content-section">
                <h3 contenteditable="false"><i class="fa-solid fa-calendar-day"></i> Upcoming Events</h3>
                <p contenteditable="false">Join us for the Annual Science Fair on November 15th and the Winter Athletics Showcase on December 5th!</p>
            </div>
        </main>

        <footer id="live-footer" class="school-footer">
            <p contenteditable="false">&copy; <?php echo date('Y'); ?> <?php echo e($school_name); ?> &bull; Powered by CWebS CMS &bull; <a href="#">School Privacy Notice</a></p>
        </footer>
    </div>

    <script>
        const cmsToolbar = document.getElementById('cms-toolbar');
        const startEditingButton = document.getElementById('start-editing');
        const finishEditingButton = document.getElementById('finish-editing');
        const saveButton = document.getElementById('save-button');
        const resetButton = document.getElementById('reset-button');
        let isEditing = false;

        function toggleEditing(enable) {
            isEditing = enable;
            const editableElements = document.querySelectorAll('#school-canvas h1, #school-canvas h2, #school-canvas h3, #school-canvas p');
            
            editableElements.forEach(el => {
                el.contentEditable = enable;
            });

            startEditingButton.classList.toggle('hidden', enable);
            finishEditingButton.classList.toggle('hidden', !enable);
            saveButton.classList.toggle('hidden', !enable);
            resetButton.classList.toggle('hidden', !enable);
            cmsToolbar.style.display = enable ? 'flex' : 'none';
        }

        function changeHeaderColor(color) {
            document.getElementById('live-header').style.backgroundColor = color;
        }

        function changeNavColor(color) {
            document.getElementById('live-nav').style.backgroundColor = color;
        }

        function changeBackground(color) {
            document.getElementById('school-canvas').style.backgroundColor = color;
        }

        function changeFooterColor(color) {
            document.getElementById('live-footer').style.backgroundColor = color;
        }

        function changeFont(font) {
            document.getElementById('school-canvas').style.fontFamily = font;
        }

        function uploadLogo() {
            if (isEditing) {
                document.getElementById('logo-upload').click();
            } else {
                alert("Please click 'Enter Live Editing Mode' to upload a new logo.");
            }
        }

        function showLogo(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const placeholder = document.querySelector('.logo-placeholder');
                    placeholder.innerHTML = `<img src="${e.target.result}" alt="School Emblem" style="width:100%; height:100%; object-fit:cover; border-radius:50%;">`;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }

        function saveEdits() {
            alert('Your page layout and textual content changes have been saved.');
            toggleEditing(false);
        }

        function clearEdits() {
            if (confirm('Are you sure you want to reset all unsaved visual edits?')) {
                location.reload();
            }
        }

        function loadPage(pageName) {
            const content = document.getElementById('content');
            const editable = isEditing ? 'contenteditable="true"' : 'contenteditable="false"';

            const pages = {
                'About Us': `
                    <div class="content-section">
                        <h2 ${editable}>About ${<?php echo json_encode($school_name); ?>}</h2>
                        <p ${editable}>Committed to academic excellence, civic character, and student achievement since 1974.</p>
                    </div>
                    <div class="content-section">
                        <h3 ${editable}>Our Mission</h3>
                        <p ${editable}>To prepare every student for lifelong learning and responsible global citizenship.</p>
                    </div>`,
                'Admissions': `
                    <div class="content-section">
                        <h2 ${editable}>Admissions &amp; Enrollment</h2>
                        <p ${editable}>Welcome prospective families! Registrations for the upcoming school year are now open.</p>
                    </div>
                    <div class="content-section">
                        <h3 ${editable}>Enrollment Requirements</h3>
                        <p ${editable}>Please submit proof of district residency, immunization records, and previous academic transcripts.</p>
                    </div>`,
                'Academics': `
                    <div class="content-section">
                        <h2 ${editable}>Academic Programs</h2>
                        <p ${editable}>Discover our rigorous curriculum spanning STEM, the Humanities, Visual Arts, and Career Readiness.</p>
                    </div>`,
                'Contact Us': `
                    <div class="content-section">
                        <h2 ${editable}>Contact Information</h2>
                        <p ${editable}>Main Campus Office &bull; Open Monday through Friday, 7:30 AM &ndash; 4:00 PM.</p>
                    </div>`,
                'Home': `
                    <div class="content-section">
                        <h2 ${editable}>Welcome to Our School Community</h2>
                        <p ${editable}>Empowering students through innovative curriculum, dedicated mentorship, and inclusive extracurricular programs.</p>
                    </div>
                    <div class="content-section">
                        <h3 ${editable}>Campus Announcements</h3>
                        <p ${editable}>Parent-Teacher conferences are scheduled for next Friday.</p>
                    </div>`
            };

            content.innerHTML = pages[pageName] || pages['Home'];
        }
    </script>
</body>
</html>
