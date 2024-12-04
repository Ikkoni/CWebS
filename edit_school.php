<?php
session_start();

include 'connect.php'; 

$role = $_SESSION['role'] ?? $_GET['role'];
$id = $_GET['editid'];

// Fetch the school's details from the database using the school id
$sql = "SELECT * FROM `school` WHERE id = $id";  // id is the primary key in your table
$result = mysqli_query($con, $sql);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    // Fetch data from the row returned by the query
    $school_name = $row['school_name'];  // This is the column where the name 'LPU' is stored
    $location = $row['location'];  // Assuming 'location' is a valid column
    $admin_email = $row['admin_email'];  // Assuming 'admin_email' is a valid column
} else {
    die('School not found.');
}

if (isset($_POST['submit'])) {
    // Get the updated values from the form
    $school_name = $_POST['school_name'];
    $location = $_POST['location'];
    $admin_email = $_POST['admin_email'];

    // Use prepared statements to update the school data in the database
    $stmt = $con->prepare("UPDATE `school` SET school_name = ?, location = ?, admin_email = ? WHERE id = ?");
    $stmt->bind_param("sssi", $school_name, $location, $admin_email, $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Redirect to the user management page if the update is successful
        header('location:school_management.php');
        exit(); // Stop further execution after redirection
    } else {
        die(mysqli_error($con)); // Handle the error if no rows were affected
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
