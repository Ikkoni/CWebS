<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>School Website</title>
    <link href="editwebpage.css" rel="stylesheet">
</head>
<body>
    <!-- CMS Toolbar -->
    <div id="cms-toolbar">
        <div>
            <button contenteditable="false" onclick="saveEdits()">Save Edits</button>
            <button contenteditable="false" onclick="clearEdits()">Clear Edits</button>
        </div>
        <div>
            <label for="header-color-picker">Header Color:</label>
            <input type="color" id="header-color-picker" onchange="changeHeaderColor(this.value)">
            <label for="header-text-color-picker">Header Text Color:</label>
            <input type="color" id="header-text-color-picker" onchange="changeHeaderTextColor(this.value)">
            <label for="footer-color-picker">Footer Color:</label>
            <input type="color" id="footer-color-picker" onchange="changeFooterColor(this.value)">
            <label for="background-color-picker">Background Color:</label>
            <input type="color" id="background-color-picker" onchange="changeBackground(this.value)">
            <label for="nav-color-picker">Navigation Bar Color:</label>
            <input type="color" id="nav-color-picker" onchange="changeNavColor(this.value)">
            <label for="column-color-picker">Column Color:</label>
            <input type="color" id="column-color-picker" onchange="changeColumnColor(this.value)">
            <label for="font-selector">Font:</label>
            <select id="font-selector" onchange="changeFont(this.value)">
                <option value="Arial">Arial</option>
                <option value="Georgia">Georgia</option>
                <option value="Verdana">Verdana</option>
                <option value="Times New Roman">Times New Roman</option>
            </select>
            <label for="text-color-picker">Text Color:</label>
            <input type="color" id="text-color-picker" onchange="changeTextColor(this.value)">
        </div>
    </div>

    <div>
        <button id="start-editing" onclick="toggleEditing(true)">Start Editing</button>
        <button id="finish-editing" class="hidden" onclick="toggleEditing(false)">Finish Editing</button>
        <button id = "back" onclick="window.history.back();" class="btn btn-primary">Return to homepage</button>
    </div>

    <!-- Header Section -->
    <header>
        <div class="logo-placeholder" onclick="uploadLogo()">
            <span>Upload Logo</span>
            <input type="file" id="logo-upload" style="display:none;" accept="image/*" onchange="showLogo(this)">
        </div>
        <h1 contenteditable="false">School Name</h1>
    </header>

    <!-- Navigation Bar -->
    <nav>
        <ul>
            <li><a href="#" onclick="loadPage('Home')">Home</a></li>
            <li><a href="#" onclick="loadPage('About Us')">About Us</a></li>
            <li><a href="#" onclick="loadPage('Admissions')">Admissions</a></li>
            <li><a href="#" onclick="loadPage('Academics')">Academics</a></li>
            <li><a href="#" onclick="loadPage('Contact')">Contact</a></li>
        </ul>
    </nav>

    <!-- Main Content -->
    <div id="content" class="main-content container">
        <div class="content-section">
            <h2 contenteditable="false">Welcome to Our School</h2>
            <p contenteditable="false">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque fringilla lorem at dolor suscipit, ac vehicula est fermentum.</p>
        </div>
        <div class="content-section">
            <h3 contenteditable="false">Announcements</h3>
            <p contenteditable="false">Stay updated with the latest school announcements.</p>
        </div>
        <div class="content-section">
            <h3 contenteditable="false">Upcoming Events</h3>
            <p contenteditable="false">Don't miss our upcoming events and activities!</p>
        </div>
    </div>

    <!-- Footer -->
    <footer>
        <p contenteditable="false">&copy; 2024 School Name. <a href="#" contenteditable="false">Privacy Policy</a></p>
    </footer>

    <!-- JavaScript -->
    <script>
        const content = document.getElementById('content');
        const cmsToolbar = document.getElementById('cms-toolbar');
        const startEditingButton = document.getElementById('start-editing');
        const finishEditingButton = document.getElementById('finish-editing');
        const backButton = document.getElementById('back');

         function toggleEditing(enable) {
            if (enable) {
                document.querySelectorAll('*').forEach(element => {
                    // Exclude elements in the CMS toolbar, buttons, and scripts
                    if (
                        element.id !== 'cms-toolbar' &&
                        !element.closest('#cms-toolbar') &&
                        element.tagName !== 'BUTTON' &&
                        element.tagName !== 'SCRIPT'
                    ) {
                        element.contentEditable = true;
                    }
                });
            } else {
                document.querySelectorAll('[contenteditable]').forEach(element => {
                    element.removeAttribute('contenteditable');
                });
            }

            back.classList.toggle('hidden', enable);
            startEditingButton.classList.toggle('hidden', enable);
            finishEditingButton.classList.toggle('hidden', !enable);
            cmsToolbar.style.display = enable ? 'flex' : 'none';
        }


        function loadPage(pageName) {
            const editingEnabled = cmsToolbar.style.display === 'flex'; // Check if editing mode is enabled
            const editable = editingEnabled ? 'contenteditable="true"' : '';

            let pageContent = '';

            switch (pageName) {
                case 'About Us':
                    pageContent = `
                        <div class="content-section">
                            <h2 ${editable}>About Us</h2>
                            <p ${editable}>Learn more about our school, our mission, and our values.</p>
                        </div>
                        <div class="content-section">
                            <h3 ${editable}>Our History</h3>
                            <p ${editable}>Founded in 1950, our school has a long-standing tradition of excellence.</p>
                        </div>`;
                    break;

                case 'Admissions':
                    pageContent = `
                        <div class="content-section">
                            <h2 ${editable}>Admissions</h2>
                            <p ${editable}>Find out how to join our school and become part of our community.</p>
                        </div>
                        <div class="content-section">
                            <h3 ${editable}>Application Process</h3>
                            <p ${editable}>Follow the steps to apply for admission to our programs.</p>
                        </div>`;
                    break;

                case 'Academics':
                    pageContent = `
                        <div class="content-section">
                            <h2 ${editable}>Academics</h2>
                            <p ${editable}>Discover our academic programs and offerings.</p>
                        </div>
                        <div class="content-section">
                            <h3 ${editable}>Curriculum</h3>
                            <p ${editable}>Explore our comprehensive curriculum designed to foster growth and learning.</p>
                        </div>`;
                    break;

                case 'Contact':
                    pageContent = `
                        <div class="content-section">
                            <h2 ${editable}>Contact</h2>
                            <p ${editable}>Get in touch with us for more information or assistance.</p>
                        </div>
                        <div class="content-section">
                            <h3 ${editable}>Location</h3>
                            <p ${editable}>Visit us at 123 School Street, Hometown.</p>
                        </div>`;
                    break;

                default:
                    pageContent = `
                        <div class="content-section">
                            <h2 ${editable}>Welcome to Our School</h2>
                            <p ${editable}>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque fringilla lorem at dolor suscipit, ac vehicula est fermentum.</p>
                        </div>
                        <div class="content-section">
                            <h3 ${editable}>Announcements</h3>
                            <p ${editable}>Stay updated with the latest school announcements.</p>
                        </div>
                        <div class="content-section">
                            <h3 ${editable}>Upcoming Events</h3>
                            <p ${editable}>Don't miss our upcoming events and activities!</p>
                        </div>`;
                    break;
            }

            content.innerHTML = pageContent;
        }

        function saveEdits() {
            // Temporarily hide editing elements
            const elementsToHide = [cmsToolbar, startEditingButton, finishEditingButton, back];
            elementsToHide.forEach(el => el.style.display = 'none');

            // Remove all contenteditable attributes
            document.querySelectorAll('[contenteditable]').forEach(element => {
                element.removeAttribute('contenteditable');
            });

            // Get the current HTML content
            const htmlContent = document.documentElement.outerHTML;

            // Restore the visibility of the editing elements
            elementsToHide.forEach(el => el.style.display = '');

            // Save the HTML content as a file
            const blob = new Blob([htmlContent], { type: 'text/html' });
            const link = document.createElement('a');
            link.href = URL.createObjectURL(blob);
            link.download = 'edited-website.html';
            link.click();
        }

        function changeHeaderTextColor(color) {
            document.querySelector('header').style.color = color;
        }

        function changeNavColor(color) {
            document.querySelector('nav').style.backgroundColor = color;
        }

        function clearEdits() {
            if (confirm('Are you sure you want to clear all edits?')) {
                location.reload();
            }
        }

        function changeBackground(color) {
            document.body.style.backgroundColor = color;
        }

        function changeHeaderColor(color) {
            document.querySelector('header').style.backgroundColor = color;
        }

        function changeFooterColor(color) {
            document.querySelector('footer').style.backgroundColor = color;
        }

        function changeColumnColor(color) {
            const columns = document.querySelectorAll('.content-section');
            columns.forEach(column => column.style.backgroundColor = color);
        }

        function changeFont(font) {
            document.body.style.fontFamily = font;
        }

        function changeTextColor(color) {
            document.body.style.color = color;
        }

        function uploadLogo() {
            document.getElementById('logo-upload').click();
        }

        function showLogo(input) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const logoPlaceholder = document.querySelector('.logo-placeholder');
                    logoPlaceholder.innerHTML = `<img src="${e.target.result}" alt="Logo">`;
                };
                reader.readAsDataURL(input.files[0]);
            }
        }



    </script>
</body>
</html>
