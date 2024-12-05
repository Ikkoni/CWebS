<?php
    include 'connect.php';
    if(isset($_GET['delete_user_id'])) {
        $user_id = $_GET['delete_user_id'];

        $sql = "DELETE from `users` where user_id= $user_id";
        $result = mysqli_query($con,$sql);
        if($result){
            header('location:user_management.php');
        }else{
            die(mysqli_error($con));
        }
    }
?>