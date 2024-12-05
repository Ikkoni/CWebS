<?php
include 'connect.php';
include 'dashboard_nav.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Website Design</title>
    <link href="style.css" rel="stylesheet">
</head>
<body>
     <div class="dashboard-card">
    <h2>Modify Website Design</h2>
                <p>Customize the look and feel of your school's website.</p>
                <div class="form-group">
                    <label for="color-scheme">Color Scheme</label>
                    <select id="color-scheme">
                        <option>Blue and White</option>
                        <option>Green and Gold</option>
                        <option>Red and Gray</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="font-style">Font Style</label>
                    <select id="font-style">
                        <option>Modern Sans-Serif</option>
                        <option>Classic Serif</option>
                        <option>Playful Handwritten</option>
                    </select>
                </div>
                <h3>Templates</h3>
                <div class="template-grid">
                    <div class="template-card">
                        <img src="/placeholder.svg?height=150&width=200" alt="Modern Template" />
                        <h4>Modern</h4>
                        <p>A clean, minimalist design for a contemporary look.</p>
                        <button class="btn btn-secondary">Preview</button>
                        <button class="btn btn-primary">Select</button>
                    </div>
                    <div class="template-card">
                        <img src="/placeholder.svg?height=150&width=200" alt="Classic Template" />
                        <h4>Classic</h4>
                        <p>A traditional design that exudes academic excellence.</p>
                        <button class="btn btn-secondary">Preview</button>
                        <button class="btn btn-primary">Select</button>
                    </div>
                    <div class="template-card">
                        <img src="/placeholder.svg?height=150&width=200" alt="Vibrant Template" />
                        <h4>Vibrant</h4>
                        <p>An energetic design to showcase school spirit.</p>
                        <button class="btn btn-secondary">Preview</button>
                        <button class="btn btn-primary">Select</button>
                    </div>
                </div>
                <button class="btn btn-primary mt-4">Apply Changes</button>
</body>
</div>
</html>