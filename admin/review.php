<?php
// admin_feedback.php
require_once 'includes/admin_header.php'; // header + session check

// Database connection
$host = 'localhost';
$dbname = 'project';   // change if needed
$user = 'root';        // change if needed
$pass = '';
$conn = mysqli_connect($host, $user, $pass, $dbname);

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Delete feedback if requested
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    mysqli_query($conn, "DELETE FROM feedback WHERE id = $delete_id");
    header("Location: admin_feedback.php?msg=deleted");
    exit;
}

// Fetch all feedback
$result = mysqli_query($conn, "SELECT * FROM feedback ORDER BY id DESC");
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Feedback - Admin Panel</title>
    <link rel="stylesheet" href="assets/admin.css"> <!-- optional -->
    <style>
        table { width: 100%; border-collapse: collapse; margin-top:20px; }
        th, td { border: 1px solid #ccc; padding: 10px; text-align: left; }
        th { background: #f5f5f5; }
        .delete-btn { color: red; text-decoration: none; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1>User Feedback</h1>
        <?php if (isset($_GET['msg']) && $_GET['msg'] == 'deleted'): ?>
            <p style="color:green;">Feedback deleted successfully.</p>
        <?php endif; ?>

        <table>
            <tr>
                <th>ID</th>
                <th>Email</th>
                <th>Message</th>
                
            </tr>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
                <td><?php echo $row['id']; ?></td>
                <td><?php echo htmlspecialchars($row['email']); ?></td>
                <td><?php echo nl2br(htmlspecialchars($row['message'])); ?></td>
              
            </tr>
            <?php endwhile; ?>
        </table>
    </div>
</body>
</html>
