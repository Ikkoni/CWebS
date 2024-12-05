<?php
    session_start();

    include 'connect.php'; 

    $user_role = $_SESSION['user_role'] ?? $_GET['user_role'];

    $user_id = $_GET['edit_user_id'];

    // Fetch the user's details from the database
    $sql = "SELECT * FROM `users` WHERE user_id = $user_id";
    $result = mysqli_query($con, $sql);
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $user_first_name = $row['user_first_name'];
        $user_last_name = $row['user_last_name'];
        $user_email = $row['user_email'];
        $user_password = $row['user_password'];
        $user_role = $row['user_role'];
        $user_department = $row['user_department'];
        $user_position = $row['user_position'];
        $user_contact_number = $row['user_contact_number'];
        $user_address = $row['user_address'];

    } else {
        die('User not found.');
    }

    if (isset($_POST['submit'])) {
        $user_first_name = $_POST['user_first_name'];
        $user_last_name = $_POST['user_last_name'];
        $user_email = $_POST['user_email'];
        $user_password = $_POST['user_password'];
        $user_role = $_POST['user_role'];
        $user_department = $_POST['user_department'];
        $user_position = $_POST['user_position'];
        $user_contact_number = $_POST['user_contact_number'];
        $user_address = $_POST['user_address'];


        // Hash the password before saving it
        $hashed_password = password_hash($user_password, PASSWORD_DEFAULT);

        // Using a prepared statement to prevent SQL injection
        $stmt = $con->prepare("UPDATE `users` SET user_first_name = ?, user_last_name = ?, user_email = ?, user_password = ? , user_role = ? , user_department = ? , user_position = ? , user_contact_number = ? , user_address = ? WHERE user_id = ?");
        $stmt->bind_param("sssssssisi", $user_first_name, $user_last_name, $user_email, $hashed_password, $user_role, $user_department,  $user_position,  $user_contact_number,  $user_address, $user_id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            header('location:user_management.php');
        } else {
            die(mysqli_error($con));
        }
    }
?>
<!DOCTYPE html>
<html>
<head>
    <title>Edit User</title>
    <link href="style.css" rel="stylesheet">
</head>
<body>
    
<div  class="modal-overlay">
        <div class="modal">
            <h2>Edit user info</h2>
        <div class="modal-header">
        </div>
            <form method = "post">
            <div class="form-group">
                <label>First Name</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($user_first_name); ?>" name="user_first_name" autocomplete="off" required>
            </div>

            <div class="form-group">
                <label>Last Name</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($user_last_name); ?>" name="user_last_name" autocomplete="off" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" class="form-control" value="<?php echo htmlspecialchars($user_email); ?>" name="user_email" autocomplete="off" required>
            </div>


            <div class="form-group">
                <label>Password</label>
                <input type="password" class="form-control" name="user_password" placeholder = "If you don't want to change, input the current"autocomplete="off" required>
            </div>

            <div class="form-group">
                <label>Role</label>
                <select name="user_role" class="form-control">
                <option value="Admin" <?php echo ($user_role == 'Admin') ? 'selected' : ''; ?>>Admin</option>
                <option value="SuperAdmin" <?php echo ($user_role == 'SuperAdmin') ? 'selected' : ''; ?>>Super Admin</option>
                <option value="Editor" <?php echo ($user_role == 'Editor') ? 'selected' : ''; ?>>Editor</option>
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
            <input type="text" class = "form-control" value="<?php echo htmlspecialchars($user_contact_number); ?>" name = "user_contact_number" autocomplete = "off" required="">
            </div> -->

            <div class = "form-group">    
            <label>Address</label>
            <input type="text" class = "form-control" value="<?php echo htmlspecialchars($user_address); ?>" name = "user_address" autocomplete = "off" required="">
            </div>

            <div>
                <button type="submit" name="submit" class="btn btn-primary">Edit</button>
                <button type="button" onclick="window.history.back();" class="btn btn-primary">Cancel</button>
            </div>
        </form>
</div>
        
</div>
</body>
</html>
