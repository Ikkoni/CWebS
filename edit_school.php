<?php
session_start();
include 'connect.php'; 

$user_role = $_SESSION['user_role'] ?? $_GET['user_role '];
$school_id = $_GET['editschool_id'];

$sql = "SELECT * FROM `school` WHERE school_id = $school_id";  
$result = mysqli_query($con, $sql);
if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    
    $school_name =$row['school_name'];
    $school_address =$row['school_address'];
    $school_contact_number =$row['school_contact_number'];
    $school_email =$row['school_email'];  
} else {
    die('School not found.');
}

if (isset($_POST['submit'])) {
    
    $school_name =$_POST['school_name'];
    $school_address =$_POST['school_address'];
    $school_contact_number =$_POST['school_contact_number'];
    $school_email =$_POST['school_email'];

    $stmt = $con->prepare("UPDATE `school` SET school_name = ?, school_address = ?, school_contact_number = ?, school_email = ? WHERE school_id = ?");
    $stmt->bind_param("sssi", $school_name, $school_address,$school_contact_number ,$school_email, $school_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        
        header('location:school_management.php');
        exit(); 
    } else {
        die(mysqli_error($con)); 
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit School</title>
    <link href="style.css" rel="stylesheet">
</head>
<body>
<div class="modal-overlay">
    <div class="modal-add-school">
        <div class="modal-header">
            <h2>Edit School</h2>
        
        <form method="post">
        </div>
        <div class = "form-group">    
        <label>School Name</label>
        <input type="text" class = "form-control" placeholder = "Enter your name" name = "school_name" autocomplete = "off" required="">
        </div>
        <div class = "form-group">    
            <label>School Address</label>
            <input type="text" class = "form-control" placeholder = "Enter your address" name = "school_address" autocomplete = "off" required="" >
        </div>
        <div class = "form-group">    
            <label>Contact Number</label>
            <input type="text" class = "form-control" placeholder = "Enter your contact number" name = "school_contact_number" autocomplete = "off" required="">
        </div>
        <div class = "form-group">    
            <label>School Email</label>
            <input type="email" class = "form-control" placeholder = "Enter your email" name = "school_email" autocomplete = "off" required="">
        </div> 
        <div class = "form-group">
            <button type="submit" name="submit" class="btn btn-primary">Update School</button>
            <button type="button" onclick="window.history.back();" class="btn btn-primary">Cancel</button>
        </div>
        </form>
</div>
</div>      
</div>
</body>
</html>