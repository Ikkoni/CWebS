<?php

$con = new mysqli('localhost', 'root', '', 'accounts');

if(!$con){
   die(mysqli_error($con));
}
?>