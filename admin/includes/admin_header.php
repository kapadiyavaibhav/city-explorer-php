<?php
// admin/includes/admin_header.php

// Include database connection (if not already included by a higher-level file)
require_once 'db_config.php';

// Basic session check for admin
// You would typically have more robust authentication here (e.g., checking user role)
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Redirect to admin login page if not logged in
    header("location: index.php"); // Assuming index.php is your admin login page
    exit;
}

// Determine page title
$page_title = basename($_SERVER['PHP_SELF'], '.php'); // Gets the current file name without extension
$page_title = str_replace('_', ' ', $page_title); // Replace underscores with spaces
$page_title = ucwords($page_title); // Capitalize first letter of each word

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>City Explorer Admin - <?php echo htmlspecialchars($page_title); ?></title>
    
    <link rel="stylesheet" href="css/admin.css"> 
    
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <div class="admin-wrapper">
        <header class="admin-main-header">
            <div class="admin-header-left">
                <button class="menu-toggle" aria-label="Toggle navigation">
                    <i class="fas fa-bars"></i>
                </button>
                <a href="dashboard.php" class="admin-logo">
                    <i class="fas fa-city"></i> City Explorer Admin
                </a>
            </div>
            <div class="admin-header-right">
                <nav class="admin-top-nav">
                    <ul>
                        <li><a href="#"><i class="fas fa-bell"></i> Notifications</a></li>
                        <li>
                            <a href="#">
                                <i class="fas fa-user-circle"></i> <?php echo htmlspecialchars($_SESSION['admin_username'] ?? 'Admin'); ?>
                            </a>
                        </li>
                        <li><a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a></li>
                    </ul>
                </nav>
            </div>
        </header>

        <aside class="admin-sidebar">
            <?php include 'admin_sidebar.php'; ?>
        </aside>

        <main class="admin-content">
            <h1 class="admin-page-title"><?php echo htmlspecialchars($page_title); ?></h1>
            <div class="admin-content-inner">
