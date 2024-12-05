<?php
include 'connect.php';
include 'dashboard_nav.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>School Management</title>
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
     <h2>Manage Schools</h2>
     <p></p>
    <table class = "school-table"> 
        <thead>
            <tr>
            <th scope = "row">ID</th>
            <th scope = "col">School Name</th>
            <th scope = "col">Location</th>
            <th scope = "col">Admin Email</th>
            <th scope = "col">Action</th>
            </tr>
            </thead>
        <tbody>
    <?php
    $sql = "SELECT * FROM `school`";
    $result = mysqli_query($con,$sql);
    if ($result) {
       while( $row = mysqli_fetch_assoc($result)){
        $id = $row['id'];
        $school_name = $row['school_name'];
        $location = $row['location'];
        $admin_email = $row['admin_email'];
        echo '<tr>
            <th scope = "row">'.$id.'</th>
            <td>'.$school_name.'</td>
            <td>'.$location.'</td>
            <td>'.$admin_email.'</td>
            <td>
        <a href="edit_school.php? editid='.$id.'"><button class="btn btn-secondary"  >Edit</button></a>
        <a href="delete_school.php? deleteid='.$id.'"><button class="btn btn-secondary" >Delete</button></a>
            </td>
            </tr>';
       }
    }

    ?>  
    </table>
    <a href="add_school.php"><button class = "btn btn-primary">Add New School</button></a>

    </body>
    </div>
</html>