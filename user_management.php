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
            <th scope = "col">Password</th>
            <th scope = "col">Role</th>
            <th scope = "col">Action</th>
            </tr>
            </thead>
        <tbody>
    <?php

    $sql = "SELECT * FROM `users`";
    $result = mysqli_query($con,$sql);
    if ($result) {
       while( $row = mysqli_fetch_assoc($result)){
        $id = $row['id'];
        $first_name = $row['first_name'];
        $last_name = $row['last_name'];
        $email = $row['email'];
        $password = $row['password'];
        $role = $row['role'];
        echo '<tr>
            <th scope = "row">'.$id.'</th>
            <td>'.$first_name.'</td>
            <td>'.$last_name.'</td>
            <td>'.$email.'</td>
            <td>'.$password.'</td>
            <td>'.$role.'</td>
            <td>
        <a href="edit_user.php? editid='.$id.'"><button class="btn btn-secondary"  >Edit</button></a>
        <a href="delete_user.php? deleteid='.$id.'"><button class="btn btn-secondary" >Delete</button></a>
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