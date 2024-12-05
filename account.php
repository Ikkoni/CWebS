<?php
    //Connect to database
    include 'connect.php';
    include 'dashboard_nav.php'; 
    //Check if an account is not logged in
    if(!isset($_SESSION['email'])) {
        header('location: login.php');
    }

    $id = $_SESSION['id'] ?? $_GET['id'];
    $message = '';

    $select = "SELECT * FROM users WHERE id = '$id'";
    $result = mysqli_query($con, $select);
    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
    }

    if (isset($_POST['submit'])) {
        //Store form values in variables
        $first_name = mysqli_real_escape_string($con, $_POST['first_name']);
        $last_name = mysqli_real_escape_string($con, $_POST['last_name']);
        $email = mysqli_real_escape_string($con, $_POST['email']);
        $password = mysqli_real_escape_string($con, $_POST['password']);
        $confirm_password = mysqli_real_escape_string($con, $_POST['confirm_password']);

        if ($password === $confirm_password){
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $select = "SELECT * FROM users WHERE email = '$email'";
        $result = mysqli_query($con, $select);

        if (mysqli_num_rows($result) > 0) {
            $_SESSION['status'] = "This email is already registered. Please use a different email.";
            header('location:account.php');
            exit;
        }

        else {
            $update = "UPDATE users SET first_name='$first_name', last_name='$last_name', email='$email', password='$hashed_password' WHERE id = '$id'";
            $result = mysqli_query($con, $update);
            $selectUpdate = "SELECT * FROM users WHERE id = '$id'";
            $resultUpdate = mysqli_query($con, $selectUpdate);
            if (mysqli_num_rows($resultUpdate) > 0) {
                $user = mysqli_fetch_assoc($resultUpdate);
                $_SESSION['id'] = $user['id'];
                $_SESSION['first_name'] = $user['first_name'];
                $_SESSION['last_name'] = $user['last_name'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['password'] = $user['password'];
            }
        }
    }
    else{
        $_SESSION['status'] = "Password and Confirm Password does not match";
        header('location:account.php');
        exit;
    }
}
    
    // Pop up window action handling
    if (!empty($message)):
?>
        <script type="text/javascript">
            alert('<?php echo htmlspecialchars($message)?>');
        </script>
<?php endif; ?>

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
                <input type="text" name="first_name" placeholder="First Name" value="<?php echo $user['first_name']?>" autocomplete="off" required>
            </div>
            <div class="form-group">
            <label>Last Name</label>
                <input type="text" name="last_name" placeholder="Last Name" value="<?php echo $user['last_name']?>" autocomplete="off" required><br>
            </div>
            <div class="form-group">
            <label>Email</label>
                <input type="text" name="email" placeholder="Email" value="<?php echo $user['email']?>" autocomplete="off" required><br>
            </div>
            <div class="form-group">
            <label>Password</label>
                <input type="password" name="password" placeholder="Password" autocomplete="off" required><br>
            </div>
            <div class="form-group">
            <label>Confirm Password</label>
                <input type="password" name="confirm_password" placeholder="Confirm Password" autocomplete="off" required><br>
            </div>
  <button class="btn btn-primary"type="submit" name="submit" value="Submit">Update Profile</button>
        </form>
    </main>
</body>
</html>