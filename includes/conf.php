<?php
$host = "localhost";       // Replace with your database host
$username = "root";        // Your database username
$password = "";            // Your database password
$database = "project"; // Name of your database

// Create connection
$conn = new mysqli($host, $username, $password, $database);

/* Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
echo "Connected successfully!";
?>*/