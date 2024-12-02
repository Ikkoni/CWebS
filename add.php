<?php
    include 'connect.php'; 

    session_start();
    if(isset($_POST['submit'])){
        $first_name =$_POST['first_name'];
        $last_name =$_POST['last_name'];
        $email =$_POST['email'];
        $password =$_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        $role = $_POST['role'];

        $email_duplicate_checker = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($con, $email_duplicate_checker);

        // If the email already exists
        if (mysqli_num_rows($result) > 0) {
            $_SESSION['status'] = "This email is already registered. Please use a different email.";
            header('location:superadmin_display.php');
            exit;
    }

        if ($password === $confirm_password){
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO `users` (first_name,last_name,email,password,role )
        VALUES('$first_name', '$last_name','$email','$hashed_password', '$role')";
        $result = mysqli_query($con,$sql);
            if($result){
                $_SESSION['success'] = "User successfully added";
                header('location:dashboard_nav.php');
            }else{
                $_SESSION['status'] = "User failed to add";
                header('location:add.php');
            }
        }else{
            $_SESSION['status'] = "Password and Confirm Password does not match";
            header('location:superadmin_display.php');
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
        <input type="text" class = "form-control" placeholder = "Enter your name" name = "first_name" autocomplete = "off" required="">
        </div>
        <div class = "form-group">    
            <label>Last Name</label>
            <input type="text" class = "form-control" placeholder = "Enter your name" name = "last_name" autocomplete = "off" required="" >
        </div>
        <div class = "form-group">    
            <label>Email</label>
            <input type="email" class = "form-control" placeholder = "Enter your email" name = "email" autocomplete = "off" required="">
        </div>
    
    <div class="form-group">
        <label>Password</label>
    <div style="position: relative;">
        <input  type="password" class="form-control" id="password" placeholder="Enter your password" name="password" autocomplete="off" required>
        <button type="button" id="togglePassword" style="position: absolute; top: 50%; right: 10px; transform: translateY(-50%); background: none; border: none; cursor: pointer;"> <i class="fa-regular fa-eye-slash"></i></button>
    </div>
    </div>
    
    <div class="form-group">
        <label>Confirm Password</label>
    <div style="position: relative;">
        <input type="password" class="form-control" id="confirm_password" placeholder="Confirm your password" name="confirm_password" autocomplete="off" required>
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
                const isPasswordHidden = field.type === 'password';
                field.type = isPasswordHidden ? 'text' : 'password';
                icon.classList.toggle('fa-eye-slash');
                icon.classList.toggle('fa-eye');
                text.textContent = isPasswordHidden ? 'Hide Password' : 'Show Password';
            });
        };

        togglePasswordVisibility('password', 'togglePassword');
        togglePasswordVisibility('confirm_password', 'toggleConfirmPassword');
    </script>   
    <div>
    <div class="form-group">
                <label>Role</label>
                <select name="role" class="form-control">
                <option value="Admin">Admin</option>
                <option value="SuperAdmin">SuperAdmin</option>
                <option value="Editor">Editor</option>
            </select>
            </div>
            <button type = "submit" name = "submit" class="btn btn-primary"> Add user </button>
            <button type= "button" onclick="window.history.back();" class="btn btn-primary">Cancel</button>
        </div>
        </form>
</div>
        
</div>
</body>
</html>
