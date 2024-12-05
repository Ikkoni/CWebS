<?php
// Include the database connection
include 'connect.php'; 
// Start the session to store login data
session_start();

// Check if the form is submitted
if (isset($_POST['login'])) {
    $user_email = $_POST['user_email'];
    $user_password = $_POST['user_password'];

    // Query to select the user with the provided email
    $sql = "SELECT * FROM users WHERE user_email = '$user_email'";
    $result = mysqli_query($con, $sql);

    // If a user is found
    if (mysqli_num_rows($result) > 0) {
        // Fetch the user data
        $user = mysqli_fetch_assoc($result);

        if (password_verify($user_password, $user['user_password'])) {
            // If successful, store user data in session and redirect
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['user_first_name'] = $user['user_first_name'];
            $_SESSION['user_last_name'] = $user['user_last_name'];
            $_SESSION['user_email'] = $user['user_email'];
            $_SESSION['user_role'] = $user['user_role'];
            $_SESSION['user_department'] = $user['user_department'];
            $_SESSION['user_position'] = $user['user_position'];
            $_SESSION['user_contact_number'] = $user['user_contact_number'];
            $_SESSION['user_address'] = $user['user_address'];
        if ($_SESSION['user_role'] == 'SuperAdmin') {
            header('Location: dashboard_nav.php'); // Redirect to admin dashboard
        } else if($_SESSION['user_role'] == 'Admin') {
            header('Location: dashboard_nav.php'); // Redirect to user dashboard
        }else if($_SESSION['user_role'] == 'Editor') {
            header('Location: dashboard_nav.php'); // Redirect to user dashboard
        }
            exit;
        } else {
            // Incorrect password
            $error = "Incorrect password";
        }
    } else {
        // No user found with this email
        $error = "No user exists with this email";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link href="style.css" rel="stylesheet">
</head>
<body>
    <div  class="modal-overlay">
        <div class="modal-add-school">
    <div class="modal-header">
        <h2>Login</h2>
    </div>
        <form method="post">
            <div class="form-group">
                <label>Email</label>
                <input type="email" class="form-control" name="user_email" placeholder="Enter your email"  autocomplete="off" required >
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" class="form-control" name="user_password"  placeholder="Enter your password" autocomplete="off" required>
            </div>
            <button type="submit" name="login" class="btn btn-primary">Login</button>
            <button type="button" onclick="window.history.back();" class="btn btn-primary">Cancel</button>
        </form>

        <?php 
        // Display error message if login fails
        if (isset($error)) {
            echo "<div class='alert alert-danger mt-3'>$error</div>";
        }
        ?>
    
    </div>
    </div>
</body>
</html>
