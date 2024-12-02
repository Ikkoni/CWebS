<?php
    include 'connect.php';
    if(isset($_GET['deleteid'])) {
        $id = $_GET['deleteid'];

        $sql = "DELETE from `users` where id= $id";
        $result = mysqli_query($con,$sql);
        if($result){
            header('location:dashboard_nav.php');
        }else{
            die(mysqli_error($con));
        }
    }
?>