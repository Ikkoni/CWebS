<?php
session_start();
include 'connect.php'; 

$role = $_SESSION['role'] ?? $_GET['role'];
$id = $_GET['editid'];

$sql = "SELECT * FROM `school` WHERE id = $id";  
$result = mysqli_query($con, $sql);
if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    
    $school_name = $row['school_name'];  
    $location = $row['location']; 
    $admin_email = $row['admin_email'];  
} else {
    die('School not found.');
}

if (isset($_POST['submit'])) {
    
    $school_name = $_POST['school_name'];
    $location = $_POST['location'];
    $admin_email = $_POST['admin_email'];

    $stmt = $con->prepare("UPDATE `school` SET school_name = ?, location = ?, admin_email = ? WHERE id = ?");
    $stmt->bind_param("sssi", $school_name, $location, $admin_email, $id);
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
    <div class="modal">
        <div class="modal-header">
            <h2>Edit School</h2>
        </div>
        <form method="post">
            <div class="form-group">    
                <label>School Name</label>
                <input type="text" class="form-control" placeholder="Enter school name" name="school_name" value="<?php echo htmlspecialchars($school_name); ?>" autocomplete="off" required>
            </div>
            <div class="form-group">    
                <label>Location</label>
                <input type="text" class="form-control" placeholder="Enter location" name="location" value="<?php echo htmlspecialchars($location); ?>" autocomplete="off" required>
            </div>
            <div class="form-group">    
                <label>Admin Email</label>
                <input type="email" class="form-control" placeholder="Enter admin email" name="admin_email" value="<?php echo htmlspecialchars($admin_email); ?>" autocomplete="off" required>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Update School</button>
            <button type="button" onclick="window.history.back();" class="btn btn-primary">Cancel</button>
        </div>
    </form>
</div>
</div>
</body>
</html>
