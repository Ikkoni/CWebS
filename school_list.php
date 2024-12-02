
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Manage School</title>
    <link href="style.css" rel="stylesheet">
</head>
<body>
<?php include 'dashboard_nav.php'; ?>
     <div class="dashboard-card">
<h2>Manage Schools</h2>
                <p>View and manage all schools in the CWebS system.</p>
                <table class="school-table">
                    <thead>
                        <tr>
                            <th>School Name</th>
                            <th>Location</th>
                            <th>Admin Email</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Springfield Elementary</td>
                            <td>Springfield, IL</td>
                            <td>principal@springfield.edu</td>
                            <td>
                                <button class="btn btn-secondary">Edit</button>
                                <button class="btn btn-secondary">Delete</button>
                            </td>
                        </tr>
                        <tr>
                            <td>Riverdale High</td>
                            <td>Riverdale, NY</td>
                            <td>admin@riverdale.edu</td>
                            <td>
                                <button class="btn btn-secondary">Edit</button>
                                <button class="btn btn-secondary">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <button class="btn btn-primary">Add New School</button>
</body>
</div>
</html>