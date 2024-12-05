<?php
    include 'connect.php'; 

    session_start();
    if(isset($_POST['submit'])){
        $user_first_name =$_POST['user_first_name'];
        $user_last_name =$_POST['user_last_name'];
        $user_email =$_POST['user_email'];
        $user_password =$_POST['user_password'];
        $user_confirm_password = $_POST['user_confirm_password'];
        $user_role = $_POST['user_role'];
        $user_department = $_POST['user_department'];
        $user_position = $_POST['user_position'];
        $user_contact_number = $_POST['user_contact_number'];
        $user_address = $_POST['user_address'];

        $email_duplicate_checker = "SELECT * FROM users WHERE user_email = '$user_email'";
        $result = mysqli_query($con, $email_duplicate_checker);

        // If the email already exists
        if (mysqli_num_rows($result) > 0) {
            $_SESSION['status'] = "This email is already registered. Please use a different email.";
            header('location:user_management.php');
            exit;
    }

        if ($user_password === $user_confirm_password){
        $hashed_password = password_hash($user_password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO `users` (user_first_name,user_last_name,user_email,user_password,user_role,user_department, user_position, user_contact_number, user_address )
        VALUES('$user_first_name', '$user_last_name','$user_email','$hashed_password', '$user_role', '$user_department','$user_position', '$user_contact_number' , '$user_address' )";
        $result = mysqli_query($con,$sql);
            if($result){
                $_SESSION['success'] = " $role Successfully added";
                header('location:user_management.php');
            }else{
                $_SESSION['status'] = "User failed to add";
                header('location:add_user.php');
            }
        }else{
            $_SESSION['status'] = "Password and Confirm Password does not match";
            header('location:user_management.php');
        }
        
        

    }
?>
<!DOCTYPE html>
<html>
<head>
	<title>Add user</title>
    <link href="style.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body> 
        <div  class="modal-overlay">
            <div class="modal">
                <div class="modal-header">
                    <h2>Add User</h2>
        </div>
            <form method = "post">
        <div class = "form-group">    
        <label>First Name</label>
        <input type="text" class = "form-control" placeholder = "Enter your first name" name = "user_first_name" autocomplete = "off" required="">
        </div>
        <div class = "form-group">    
            <label>Last Name</label>
            <input type="text" class = "form-control" placeholder = "Enter your last name" name = "user_last_name" autocomplete = "off" required="" >
        </div>
        <div class = "form-group">    
            <label>Email</label>
            <input type="email" class = "form-control" placeholder = "Enter your email" name = "user_email" autocomplete = "off" required="">
        </div>
    
    <div class="form-group">
        <label>Password</label>
    <div style="position: relative;">
        <input  type="password" class="form-control" id="password" placeholder="Enter your password" name="user_password" autocomplete="off" required>
        <button type="button" id="togglePassword" style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); background: none; border: none; cursor: pointer;"> <i class="fa-regular fa-eye-slash"></i></button>
    </div>
    </div>
    
    <div class="form-group">
        <label>Confirm Password</label>
    <div style="position: relative;">
        <input type="password" class="form-control" id="confirm_password" placeholder="Confirm your password" name="user_confirm_password" autocomplete="off" required>
        <button type="button" id="toggleConfirmPassword" style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); background: none; border: none; cursor: pointer;"> <i class="fa-regular fa-eye-slash"></i></button>
    </div>
    </div>

        <script>
        const togglePasswordVisibility = (fieldId, buttonId) => {
            const field = document.getElementById(fieldId);
            const button = document.getElementById(buttonId);
            const icon = button.querySelector('i');
            const text = button.querySelector('span');

            button.addEventListener('click', () => {
                const isPasswordHidden = field.type === 'user_password';
                field.type = isPasswordHidden ? 'text' : 'user_password';
                icon.classList.toggle('fa-eye-slash');
                icon.classList.toggle('fa-eye');
                text.textContent = isPasswordHidden ? 'Hide Password' : 'Show Password';
            });
        };

        togglePasswordVisibility('user_password', 'togglePassword');
        togglePasswordVisibility('user_confirm_password', 'toggleConfirmPassword');
    </script>   
    <div>
    <div class="form-group">
                <label>Role</label>
                <select name="user_role" class="form-control">
                <option value="Admin">Admin</option>
                <option value="SuperAdmin">SuperAdmin</option>
                <option value="Editor">Editor</option>
            </select>
            </div>

            <div class="form-group">
                <label>Department</label>
                <select name="user_department" class="form-control">
                <option value="COECSA">COECSA</option>
                <option value="CITHM">CITHM</option>
                <option value="CAMS">CAMS</option>
            </select>
            </div>

            <div class="form-group">
                <label>Position</label>
                <select name="user_position" class="form-control">
                <option value="Teacher">Teacher</option>
                <option value="Principal">Principal</option>
                <option value="IT Personnel">IT Personnel</option>
            </select>
            </div>

            <!-- <div class = "form-group">    
            <label>Contact Number</label>
            <input type="text" class = "form-control" placeholder = "Enter your contact number" name = "user_contact_number" autocomplete = "off" required="">
            </div> -->

            <div class = "form-group">    
            <label>Address</label>
            <input type="text" class = "form-control" placeholder = "Enter your address" name = "user_address" autocomplete = "off" required="">
            </div>

            <button type = "submit" name = "submit" class="btn btn-primary"> Add user </button>
            <button type= "button" onclick="window.history.back();" class="btn btn-primary">Cancel</button>
        </div>
        </form>
</div>
        
</div>
</body>
</html>
