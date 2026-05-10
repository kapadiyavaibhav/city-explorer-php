<?php
session_start();

// If not logged in, save record_type in session and go to login
if (!isset($_SESSION['user_id'])) {
    if (isset($_POST['record_type'])) {
        $_SESSION['record_type'] = $_POST['record_type'];
    }
    header("Location: login.php");
    exit();
}

$type = "";

// Prefer POST if available, else take from session
if (isset($_POST['record_type'])) {
    $type = $_POST['record_type'];
    $_SESSION['record_type'] = $type; // store latest
} elseif (isset($_SESSION['record_type'])) {
    $type = $_SESSION['record_type'];
    unset($_SESSION['record_type']); // clear after use
}

if (!empty($type)) {
    // Normalize category name
    $type = strtolower($type);

    // Replace spaces with underscores (for file names like add_historical_place.php)
    $fileName = "add_" . str_replace(" ", "_", $type) . ".php";

    // Redirect to file if it exists, else back to contact page
    if (file_exists($fileName)) {
        header("Location: " . $fileName);
    } else {
        header("Location: contactus.php");
    }
} else {
    header("Location: profile.php");
}
exit;
?>
