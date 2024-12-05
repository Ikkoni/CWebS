<?php
include 'connect.php';
include 'dashboard_nav.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Site Settings</title>
    <link href="style.css" rel="stylesheet">
</head>
<body>
     <?php include 'header.php'; ?>
     <div class="dashboard-card">
<h2>Site Settings</h2>
                <p>Configure general settings for your school's website.</p>
                <div class="form-group">
                    <label for="site-name">Site Name</label>
                    <input type="text" id="site-name" value="Springfield Elementary School" />
                </div>
                <div class="form-group">
                    <label for="site-description">Site Description</label>
                    <textarea id="site-description" rows="3">A place where young minds grow and learn.</textarea>
                </div>
                <div class="form-group">
                    <label for="contact-email">Contact Email</label>
                    <input type="email" id="contact-email" value="info@springfield.edu" />
                </div>
                <div class="form-group">
                    <label for="social-media">Social Media Links</label>
                    <input type="text" id="social-media" value="facebook.com/springfield, twitter.com/springfield" />
                </div>
                <button class="btn btn-primary">Save Settings</button>
</body>
</html>