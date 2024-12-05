<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard</title>
    <link href="style.css" rel="stylesheet">
</head>
<body>
<?php

include 'connect.php'; 
include 'header.php';
session_start();

$user_role = $_SESSION['user_role'] ?? $_GET['user_role']; 
?>
     
<div id="<?php echo htmlspecialchars($user_role); ?>-dashboard" class="dashboard">
    <div class="container">
    <nav class="dashboard-nav">
    <h3><?php echo ucfirst($user_role); ?> Menu</h3>
    <ul>
      
        <li><a href="edit_webpage.php" data-section="webpage-content">Edit Webpage Content</a></li>
        
        <?php if ($user_role !== 'Editor') : ?>
            <!-- <li><a href="webpagedesign.php" data-section="webpage-design">Modify Website Design</a></li> -->
            <li><a href="user_management.php" data-section="user-management">Manage Users</a></li>
        <?php endif; ?>

        
        <?php if ($user_role === 'SuperAdmin') : ?>
            <li><a href="school_management.php" data-section="school-list">Manage Schools</a></li>
        <?php endif; ?>

       
        <?php if ($user_role === 'Admin') : ?>
            <!-- <li><a href="sitesettings.php" data-section="site-settings">Site Settings</a></li> -->
        <?php endif; ?>

        
        <?php if ($user_role === 'SuperAdmin') : ?>
            <!-- <li><a href="systemsettings.php" data-section="system-settings">System Settings</a></li> -->
        <?php endif; ?>

        
        <li><a href="account.php" data-section="account">Account</a></li>
    </ul>
</nav>


        
    </div>
</div>


</body>
</html>