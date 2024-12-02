
<!DOCTYPE html>
<html lang="en">
<head>
  <title>Edit Webpage</title>
  <link href="style.css" rel="stylesheet">
</head>
<body>
<?php include 'dashboard_nav.php'; ?>


<div class="dashboard-card">
<h2>Edit Webpage Content</h2>
                <p>Here you can edit the content of your school's website.</p>
                <div class="form-group">
                    <label for="page-title">Page Title</label>
                    <input type="text" id="page-title" value="Welcome to Our School" />
                </div>
                <div class="form-group">
                    <label for="page-content">Page Content</label>
                    <textarea id="page-content" rows="10">Welcome to our school's website. We are committed to providing quality education and fostering a supportive learning environment for all our students.</textarea>
                </div>
                <button class="btn btn-primary">Save Changes</button>
</body>
</div>
</html>