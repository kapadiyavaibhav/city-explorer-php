<?php
// admin/logout.php
session_start();

// Unset all of the session variables
$_SESSION = array();

// Destroy the session.
session_destroy();

// Redirect to admin login page or main index
header("location: index.php"); // Assuming 'index.php' is your admin login, adjust if different
exit;
?>
