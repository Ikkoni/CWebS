<?php
include 'connect.php';
include 'dashboard_nav.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>User Management</title>
    <link href="style.css" rel="stylesheet">
</head>
<body>
        
     <div class="dashboard-card">
     <?php
        if(isset($_SESSION['success']) && $_SESSION['success'] !=''){
            echo '<h2>'.$_SESSION['success'].'</h2>';
            unset($_SESSION['success']);
        }

        if(isset($_SESSION['status']) && $_SESSION['status'] !=''){
            echo '<h2> '.$_SESSION['status'].'</h2>';
            unset($_SESSION['status']);
        }
     ?>
     <h2>Manage Users</h2>
     <p>Add, edit, or remove user accounts for your school's website.</p>
    <table class = "user-table"> 
        <thead>
            <tr>
            <th scope = "col">ID</th>
            <th scope = "col">First Name</th>
            <th scope = "col">Last Name</th>
            <th scope = "col">Email</th>
            <th scope = "col">Role</th>
            <th scope = "col">Department</th>
            <th scope = "col">Position</th>
            <th scope = "col">Address</th>
            <th scope = "col">Action</th>
            </tr>
            </thead>
        <tbody>
    <?php

    $sql = "SELECT * FROM `users`";
    $result = mysqli_query($con,$sql);
    if ($result) {
       while( $row = mysqli_fetch_assoc($result)){
        $user_id = $row['user_id'];
        $user_first_name = $row['user_first_name'];
        $user_last_name = $row['user_last_name'];
        $user_email = $row['user_email'];
        $user_role = $row['user_role'];
        $user_department = $row['user_department'];
        $user_position = $row['user_position'];
        $user_contact_number = $row['user_contact_number'];
        $user_address = $row['user_address'];
        echo '<tr>
            <th scope = "row">'.$user_id.'</th>
            <td>'.$user_first_name.'</td>
            <td>'.$user_last_name.'</td>
            <td>'.$user_email.'</td>
            <td>'.$user_role.'</td>
            <td>'.$user_department.'</td>
            <td>'.$user_position.'</td>
            <td>'.$user_address.'</td>
            <td>
        <a href="edit_user.php? edit_user_id='.$user_id.'"><button class="btn btn-secondary"  >Edit</button></a>
        <a href="delete_user.php? delete_user_id='.$user_id.'"><button class="btn btn-secondary" >Delete</button></a>
            </td>
            </tr>';
       }
    }

    ?>  
    </table>
    <a href="add_user.php" class = "text-light"><button class = "btn btn-primary my-5 mx-5">Add New user</button></a>

    </body>
    </div>
</html>