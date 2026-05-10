<?php
// contactus.php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "project";

$conn = mysqli_connect($host, $user, $pass, $dbname);
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Handle feedback form submission
if (isset($_POST['submit_feedback'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    $sql = "INSERT INTO feedback (email, message) VALUES ('$email', '$message')";
    if (mysqli_query($conn, $sql)) {
        $success = "Feedback submitted successfully!";
    } else {
        $error = "Error: " . mysqli_error($conn);
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    

    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="css/style.css">

    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <title>Contact Us</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .containerr { max-width: 800px; margin: auto; }
        h2 { margin-top: 40px; color: #333; }
        form { margin: 20px 0; }
        input, textarea, select { width: 100%; padding: 8px; margin: 8px 0; }
        button { padding: 10px 15px; background: #007BFF; color: white; border: none; cursor: pointer; }
        button:hover { background: #0056b3; }
        .msg { margin: 10px 0; padding: 10px; background: #f0f0f0; }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <?php include 'includes/conf.php';
	$sql="SELECT * FROM category WHERE 1";
	$result=mysqli_query($conn,$sql);
    ?>
<div class="containerr">
    <!-- First Section -->
    <h2>Contact Us</h2>
    <p>If you have any feedback, please fill out the form below:</p>
    <?php if (!empty($success)) echo "<div class='msg'>$success</div>"; ?>
    <?php if (!empty($error)) echo "<div class='msg'>$error</div>"; ?>
    <form method="post" action="">
        <label>Email:</label>
        <input type="email" name="email" required>
        <label>Message:</label>
        <textarea name="message" rows="5" required></textarea>
        <button type="submit" name="submit_feedback">Submit</button>
    </form>
    <!-- Second Section -->
    <h2>You can add your Bussiness details</h2>
    <form method="post" action="user/redirect.php">
        <label>If you want to add your Record you can do this from here<br><br>choose category:</label>
        <select name="record_type" required>
            <?php

if(mysqli_num_rows($result))
{
	while($fetch=mysqli_fetch_assoc($result))
{
?>
            
            <option value="<?php echo $fetch['cat_name'];?>"><?php echo $fetch['cat_name'];?></option>
            
            
            <?php 
}   }
?>

        </select>
        <button type="submit" name="add_record">Add</button>
    </form>
</div>

<?php include 'includes/footer.php'; ?>

</body>
</html>
