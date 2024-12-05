<?php
    include 'connect.php';
    if(isset($_GET['deleteschool_id'])) {
        $school_id = $_GET['deleteschool_id'];

        $sql = "DELETE from `school` where school_id= $school_id";
        $result = mysqli_query($con,$sql);
        if($result){
            header('location:school_management.php');
        }else{
            die(mysqli_error($con));
        }
    }
?>