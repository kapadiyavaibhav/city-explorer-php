<?php
// admin/dashboard.php
require_once 'includes/admin_header.php'; // Includes header, starts session, checks login
?>
<?php
//session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit;
}

$conn = mysqli_connect("localhost", "root", "", "project");
if (!$conn) die("Connection failed: " . mysqli_connect_error());

$message = '';
$search = '';
$category = null;
$form_mode = 'insert';
$btn_label = 'Add Category';

if (isset($_POST['insert'])) {
    $img = $_POST['cat_image'];
    $name = $_POST['cat_name'];
    $exists = mysqli_query($conn, "SELECT * FROM category WHERE cat_name='$name'");
    if (mysqli_num_rows($exists) > 0) {
        $message = "<div class='error'>❌ Already exists.</div>";
    } else {
        $sql = "INSERT INTO category (cat_image, cat_name) VALUES ('$img', '$name')";
        $message = mysqli_query($conn, $sql)
            ? "<div class='success'>✅ Category added!</div>"
            : "<div class='error'>❌ " . mysqli_error($conn) . "</div>";
    }
} elseif (isset($_POST['edit_id'])) {
    $id = $_POST['edit_id'];
    $result = mysqli_query($conn, "SELECT * FROM category WHERE cat_id='$id'");
    if (mysqli_num_rows($result) > 0) {
        $category = mysqli_fetch_assoc($result);
        $form_mode = 'update';
        $btn_label = 'Update Category';
    }
} elseif (isset($_POST['update'])) {
    $id = $_POST['cat_id'];
    $sql = "UPDATE category SET cat_image='{$_POST['cat_image']}', cat_name='{$_POST['cat_name']}' WHERE cat_id='$id'";
    $message = mysqli_query($conn, $sql)
        ? "<div class='success'>✅ Updated successfully!</div>"
        : "<div class='error'>❌ " . mysqli_error($conn) . "</div>";
} elseif (isset($_POST['delete'])) {
    $id = $_POST['delete'];
    $message = mysqli_query($conn, "DELETE FROM category WHERE cat_id='$id'")
        ? "<div class='success'>✅ Deleted successfully!</div>"
        : "<div class='error'>❌ " . mysqli_error($conn) . "</div>";
} elseif (isset($_POST['search'])) {
    $search = $_POST['search'];
}

$result = mysqli_query($conn, "SELECT * FROM category WHERE cat_name LIKE '%$search%' ORDER BY cat_id ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Categories</title>
    <style>
        body { font-family: Arial; background: #eef2f3; padding: 20px; margin: 0; }
        .container { max-width: 800px; margin: auto; background: white; padding: 30px; border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        h2, h3 { text-align: center; color: #333; }
        input, button, textarea {
            width: 100%; padding: 10px; margin: 10px 0 20px 0;
            border-radius: 5px; border: 1px solid #ccc; font-size: 16px;
        }
        input[type="submit"], button {
            background-color: #3498db; color: white; border: none;
            cursor: pointer; font-weight: bold;
        }
        input[type="submit"]:hover, button:hover { background-color: #2980b9; }
        .btn-right { float: right; background: #3498db; padding: 10px; margin-top: -10px;
            border: none; color: white; border-radius: 5px; }
        .btn-right:hover { background: #2980b9; }
        .success, .error { text-align: center; font-weight: bold; margin: 10px 0; }
        .success { color: green; }
        .error { color: red; }
        table { width: 100%; border-collapse: collapse; margin-top: 30px; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: center; }
        th { background-color: #3498db; color: white; }
        .action-btns form { display: inline; }
    </style>
</head>
<body>
<div class="container">
    <h2>📦 Manage Categories</h2>
    <?= $message ?>

    <!-- Unified Form -->
    <form method="post">
        <?php if ($form_mode == 'update'): ?>
            <input type="hidden" name="cat_id" value="<?= htmlspecialchars($category['cat_id']) ?>">
        <?php endif; ?>
        <input type="text" name="cat_image" placeholder="Image Path" value="<?= $category['cat_image'] ?? '' ?>" required>
        <input type="text" name="cat_name" placeholder="Category Name" value="<?= $category['cat_name'] ?? '' ?>" required>
        <input type="submit" name="<?= $form_mode ?>" value="<?= $btn_label ?>">
        <?php if ($form_mode == 'update'): ?>
            <button class="btn-right" onclick="window.location.href='managecategory.php'">➕ Add New</button>
        <?php endif; ?>
    </form>

    <!-- Search -->
    <form method="post">
        <input type="text" name="search" placeholder="Search by Name..." value="<?= htmlspecialchars($search) ?>">
        <input type="submit" name="search_btn" value="Search">
    </form>

    <!-- Data Table -->
    <h3>📋 All Categories</h3>
    <table>
        <tr>
            <th>ID</th>
            <th>Image</th>
            <th>Name</th>
            <th>Actions</th>
        </tr>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['cat_id']) ?></td>
                    <td><?= htmlspecialchars($row['cat_image']) ?></td>
                    <td><?= htmlspecialchars($row['cat_name']) ?></td>
                    <td class="action-btns">
                        <form method="post">
                            <input type="hidden" name="edit_id" value="<?= $row['cat_id'] ?>">
                            <button type="submit">✏️ Edit</button>
                        </form>
                        <form method="post" onsubmit="return confirm('Delete this category?')">
                            <input type="hidden" name="delete" value="<?= $row['cat_id'] ?>">
                            <button type="submit">🗑️ Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="4">No categories found.</td></tr>
        <?php endif; ?>
    </table>
</div>
</body>
</html>