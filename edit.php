<?php
    session_start();

    include 'connect.php'; 

    $role = $_SESSION['role'] ?? $_GET['role'];

    $id = $_GET['editid'];

    // Fetch the user's details from the database
    $sql = "SELECT * FROM `users` WHERE id = $id";
    $result = mysqli_query($con, $sql);
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $first_name = $row['first_name'];
        $last_name = $row['last_name'];
        $email = $row['email'];
        $password = $row['password'];
        $role = $row['role'];
    } else {
        die('User not found.');
    }

    if (isset($_POST['submit'])) {
        $first_name = $_POST['first_name'];
        $last_name = $_POST['last_name'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $update_role = $_POST['update_role'];

        // Hash the password before saving it
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Using a prepared statement to prevent SQL injection
        $stmt = $con->prepare("UPDATE `users` SET first_name = ?, last_name = ?, email = ?, password = ? , role = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $first_name, $last_name, $email, $hashed_password, $update_role, $id);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            header('location:dashboard_nav.php');
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
        <div class="modal-header">
        </div>
            <form method = "post">
            <div class="form-group">
                <label>First Name</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($first_name); ?>" name="first_name" autocomplete="off" required>
            </div>

            <div class="form-group">
                <label>Last Name</label>
                <input type="text" class="form-control" value="<?php echo htmlspecialchars($last_name); ?>" name="last_name" autocomplete="off" required>
            </div>

            <div class="form-group">
                <label>Email</label>
                <input type="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" name="email" autocomplete="off" required>
            </div>


            <div class="form-group">
                <label>Password</label>
                <input type="password" class="form-control" name="password" placeholder = "If you don't want to change, input the current"autocomplete="off" required>
            </div>

            <div class="form-group">
                <label>Role</label>
                <select name="update_role" class="form-control">
                <option value="Admin" <?php echo ($role == 'Admin') ? 'selected' : ''; ?>>Admin</option>
                <option value="SuperAdmin" <?php echo ($role == 'SuperAdmin') ? 'selected' : ''; ?>>Super Admin</option>
                <option value="Editor" <?php echo ($role == 'Editor') ? 'selected' : ''; ?>>Editor</option>
            </select>
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
