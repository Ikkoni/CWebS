<?php
    //Connect to database
    include 'connect.php';
    include 'dashboard_nav.php'; 
    //Check if an account is not logged in
    if(!isset($_SESSION['user_email'])) {
        header('location: login.php');
    }

    $user_id = $_SESSION['user_id'] ?? $_GET['user_id'];
    $message = '';

    $select = "SELECT * FROM users WHERE user_id = '$user_id'";
    $result = mysqli_query($con, $select);
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
    }

    if (isset($_POST['submit'])) {
        //Store form values in variables
        $user_first_name = mysqli_real_escape_string($con, $_POST['user_first_name']);
        $user_last_name = mysqli_real_escape_string($con, $_POST['user_last_name']);
        $user_email = mysqli_real_escape_string($con, $_POST['user_email']);
        $user_password = mysqli_real_escape_string($con, $_POST['user_password']);
        $user_confirm_password = mysqli_real_escape_string($con, $_POST['user_confirm_password']);

        if ($user_password === $user_confirm_password){
        $hashed_password = password_hash($user_password, PASSWORD_DEFAULT);
        
        $select = "SELECT * FROM users WHERE user_email = '$user_email'";
        $result = mysqli_query($con, $select);

        if (mysqli_num_rows($result) > 0) {
            $_SESSION['status'] = "This email is already registered. Please use a different email.";
            header('location:account.php');
            exit;
        }

        else {
            $update = "UPDATE users SET user_first_name='$user_first_name', user_last_name='$user_last_name', user_email='$user_email', user_password='$hashed_password' WHERE user_id = '$user_id'";
            $result = mysqli_query($con, $update);
            $selectUpdate = "SELECT * FROM users WHERE user_id = '$user_id'";
            $resultUpdate = mysqli_query($con, $selectUpdate);
            if (mysqli_num_rows($resultUpdate) > 0) {
                $user = mysqli_fetch_assoc($resultUpdate);
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['user_first_name'] = $user['user_first_name'];
                $_SESSION['user_last_name'] = $user['user_last_name'];
                $_SESSION['user_email'] = $user['user_email'];
                $_SESSION['user_password'] = $user['user_password'];
            }
        }
    }
    else{
        $_SESSION['status'] = "Password and Confirm Password does not match";
        header('location:account.php');
        exit;
    }
}
    
    ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Details</title>
    <link rel="stylesheet" href="updateForm.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <script>
        function toggleMenu() {
            const menuBar = document.getElementById('menu-bar');
            menuBar.classList.toggle('active');
        }
    </script>
</head>
<body>
    <header>
    </header>
    <main>
    <div class = dashboard-card>
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
        <form method="post">
            <div class = "profile-settings">
            <h2>Account</h2>
            <p>Update your personal information and account settings</p><br>    
            <div class="form-group">
                <label>First Name</label>
                <input type="text" name="user_first_name"  value="<?php echo $user['user_first_name']?>" autocomplete="off" required>
            </div>
            <div class="form-group">
            <label>Last Name</label>
                <input type="text" name="user_last_name" value="<?php echo $user['user_last_name']?>" autocomplete="off" required><br>
            </div>
            <div class="form-group">
            <label>Email</label>
                <input type="text" name="user_email" placeholder="Email" value="<?php echo $user['user_email']?>" autocomplete="off" required><br>
            </div>
            <div class="form-group">
            <label>Password</label>
                <input type="password" name="user_password" placeholder="Password" autocomplete="off" required><br>
            </div>
            <div class="form-group">
            <label>Confirm Password</label>
                <input type="password" name="user_confirm_password" placeholder="Confirm Password" autocomplete="off" required><br>
            </div>
  <button class="btn btn-primary"type="submit" name="submit" value="Submit">Update Profile</button>
        </form>
    </main>
</body>
</html>