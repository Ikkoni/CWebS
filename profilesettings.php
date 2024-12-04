<!DOCTYPE html>
<html lang="en">
<head>
    <title>Profile Settings</title>
    <link href="style.css" rel="stylesheet">
</head>
<body>
<?php include 'dashboard_nav.php'; ?>

     <div class="dashboard-card">
            <h2>Profile Settings</h2>
                <p>Update your personal information and account settings.</p>
                <div class="form-group">
                    <label for="user-name">Name</label>
                    <input type="text" id="user-name" value="John Doe" />
                </div>
                <div class="form-group">
                    <label for="user-email">Email</label>
                    <input type="email" id="user-email" value="john@example.com" />
                </div>
                <div class="form-group">
                    <label for="user-password">New Password</label>
                    <input type="password" id="user-password" />
                </div>
                <div class="form-group">
                    <label for="user-password-confirm">Confirm New Password</label>
                    <input type="password" id="user-password-confirm" />
                </div>
                <!-- <div class="form-group">
                    <label for="user-avatar">Profile Picture</label>
                    <input type="file" id="user-avatar" />
                </div> -->
                <button class="btn btn-primary">Update Profile</button>
</body>
</div>
</html>