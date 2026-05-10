<?php
// admin/includes/db_config.php

// Database credentials
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root'); // YOUR DATABASE USERNAME
define('DB_PASSWORD', ''); // YOUR DATABASE PASSWORD
define('DB_NAME', 'project'); // YOUR DATABASE NAME

// Attempt to connect to MySQL database
$link = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check connection
if($link === false){
    die("ERROR: Could not connect. " . mysqli_connect_error());
}

// Optional: Set charset to UTF-8
mysqli_set_charset($link, "utf8mb4");

// Start session if not already started (important for login management)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// You might also want to add functions for authentication, e.g., check_admin_login() here or in functions.php
?>
