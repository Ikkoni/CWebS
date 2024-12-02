
<!DOCTYPE html>
<html lang="en">
<head>
    <title>System Settings</title>
    <link href="style.css" rel="stylesheet">
</head>
<body>
<?php include 'dashboard_nav.php'; ?>

        <div class = dashboard-card>
            <h2>System Settings</h2>
                <p>Manage global settings for the CWebS platform.</p>
                <div class="form-group">
                    <label for="max-schools">Maximum Schools</label>
                    <input type="number" id="max-schools" value="1000" />
                </div>
                <div class="form-group">
                    <label for="storage-limit">Storage Limit per School (GB)</label>
                    <input type="number" id="storage-limit" value="50" />
                </div>
                <div class="form-group">
                    <label for="allowed-domains">Allowed Email Domains</label>
                    <input type="text" id="allowed-domains" value="edu, gov" />
                </div>
                <div class="form-group">
                    <label for="maintenance-mode">Maintenance Mode</label>
                    <select id="maintenance-mode">
                        <option>Off</option>
                        <option>On</option>
                    </select>
                </div>
                <button class="btn btn-primary">Update System Settings</button>
</div>
</body>
</html>