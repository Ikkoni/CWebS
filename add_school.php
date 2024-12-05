<?php
    include 'connect.php'; 

    session_start();
    if(isset($_POST['submit'])){
        $school_name =$_POST['school_name'];
        $school_address =$_POST['school_address'];
        $school_contact_number =$_POST['school_contact_number'];
        $school_email =$_POST['school_email'];

        $email_duplicate_checker = "SELECT * FROM school WHERE school_name = '$school_name'";
        $result = mysqli_query($con, $email_duplicate_checker);

        // If the email already exists
        if (mysqli_num_rows($result) > 0) {
            $_SESSION['status'] = "This school is already registered.";
            header('location:school_management.php');
            exit;
    }
        $sql = "INSERT INTO `school` (school_name,school_address,school_contact_number,school_email)
        VALUES('$school_name', '$school_address','$school_contact_number', '$school_email')";
        $result = mysqli_query($con,$sql);
            if($result){
                $_SESSION['success'] = "School successfully added";
                header('location:school_management.php');
            }else{
                $_SESSION['status'] = "User failed to add";
                header('location:add.php');
            }
        }
        
?>
<!DOCTYPE html>
<html>
<head>
	<title>Add School</title>
    <link href="style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body> 
        <div  class="modal-overlay">
            <div class="modal-add-school">
                <div class="modal-header">
                    <h2>Add School</h2>
        </div>
            <form method = "post">
        <div class = "form-group">    
        <label>School Name</label>
        <input type="text" class = "form-control" placeholder = "Enter your name" name = "school_name" autocomplete = "off" required="">
        </div>
        <div class = "form-group">    
            <label>School Address</label>
            <input type="text" class = "form-control" placeholder = "Enter your school address" name = "school_address" autocomplete = "off" required="" >
        </div>
        <div class = "form-group">    
            <label>Contact Number</label>
            <input type="text" class = "form-control" placeholder = "Enter your contact" name = "school_contact_number" autocomplete = "off" required="">
        </div>
        <div class = "form-group">    
            <label>School Email</label>
            <input type="email" class = "form-control" placeholder = "Enter your email" name = "school_email" autocomplete = "off" required="">
        </div>  
            <button type = "submit" name = "submit" class="btn btn-primary"> Add school </button>
            <button type= "button" onclick="window.history.back();" class="btn btn-primary">Cancel</button>
        </div>
        </form>
</div>
        
</div>
</body>
</html>
