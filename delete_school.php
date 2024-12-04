<?php
    include 'connect.php';
    if(isset($_GET['deleteid'])) {
        $id = $_GET['deleteid'];

        $sql = "DELETE from `school` where id= $id";
        $result = mysqli_query($con,$sql);
        if($result){
            header('location:school_management.php');
        }else{
            die(mysqli_error($con));
        }
    }
?>