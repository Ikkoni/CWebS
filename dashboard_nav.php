<!DOCTYPE html>
<html lang="en">
<head>
    <title>Document</title>
    <link href="style.css" rel="stylesheet">
</head>
<body>
<?php

include 'connect.php'; 
include 'header.php';
session_start();

$role = $_SESSION['role'] ?? $_GET['role']; 
?>
     

<div id="<?php echo htmlspecialchars($role); ?>-dashboard" class="dashboard">
    <div class="container">
    <nav class="dashboard-nav">
    <h3><?php echo ucfirst($role); ?> Menu</h3>
    <ul>
      
        <li><a href="editwebpage.php" data-section="webpage-content">Edit Webpage Content</a></li>
        
        <?php if ($role !== 'Editor') : ?>
            <li><a href="webpagedesign.php" data-section="webpage-design">Modify Website Design</a></li>
            <li><a href="user_management.php" data-section="user-management">Manage Users</a></li>
        <?php endif; ?>

        
        <?php if ($role === 'SuperAdmin') : ?>
            <li><a href="school_management.php" data-section="school-list">Manage Schools</a></li>
        <?php endif; ?>

       
        <?php if ($role === 'Admin') : ?>
            <li><a href="sitesettings.php" data-section="site-settings">Site Settings</a></li>
        <?php endif; ?>

        
        <?php if ($role === 'SuperAdmin') : ?>
            <li><a href="systemsettings.php" data-section="system-settings">System Settings</a></li>
        <?php endif; ?>

        
        <li><a href="profilesettings.php" data-section="profile-settings">Profile Settings</a></li>
    </ul>
</nav>


        
    </div>
</div>


</body>
</html>