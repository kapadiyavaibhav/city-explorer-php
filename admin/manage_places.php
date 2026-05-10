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
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// 🔠 Function to format text
function formatText($text) {
    return ucfirst(strtolower(trim($text)));
}

$message = '';
$place = null;
$form_mode = 'add';

// ➕ Reset form (Add New)
if (isset($_GET['addnew'])) {
    $place = null;
    $form_mode = 'add';
}

// ✏️ Edit form
if (isset($_POST['edit_id'])) {
    $id = $_POST['edit_id'];
    $result = mysqli_query($conn, "SELECT * FROM historical_place WHERE place_id = '$id'");
    if (mysqli_num_rows($result) > 0) {
        $place = mysqli_fetch_assoc($result);
        $form_mode = 'edit';
    } else {
        $message = "<div class='error'>❌ Place not found.</div>";
    }
}

// ➕ Insert
if (isset($_POST['submit'])) {
    $img_src = $_POST['img_src'];
    $place_name = formatText($_POST['place_name']);
    $description = $_POST['description'];
    $category = formatText($_POST['category']);
    $address = formatText($_POST['address']);

    $sql = "INSERT INTO historical_place (img_src, place_name, description, category, address)
            VALUES ('$img_src', '$place_name', '$description', '$category', '$address')";

    $message = mysqli_query($conn, $sql)
        ? "<div class='success'>✅ Place added successfully.</div>"
        : "<div class='error'>❌ Error: " . mysqli_error($conn) . "</div>";
}

// 🔁 Update
if (isset($_POST['update'])) {
    $id = $_POST['place_id'];
    $img_src = $_POST['img_src'];
    $place_name = formatText($_POST['place_name']);
    $description = $_POST['description'];
    $category = formatText($_POST['category']);
    $address = formatText($_POST['address']);

    $sql = "UPDATE historical_place SET
                img_src='$img_src',
                place_name='$place_name',
                description='$description',
                category='$category',
                address='$address'
            WHERE place_id='$id'";
    $message = mysqli_query($conn, $sql)
        ? "<div class='success'>✅ Place updated successfully.</div>"
        : "<div class='error'>❌ Error: " . mysqli_error($conn) . "</div>";
    $place = null;
    $form_mode = 'add';
}

// 🗑️ Delete
if (isset($_POST['delete'])) {
    $id = $_POST['delete'];
    $message = mysqli_query($conn, "DELETE FROM historical_place WHERE place_id='$id'")
        ? "<div class='success'>✅ Place deleted successfully.</div>"
        : "<div class='error'>❌ Error: " . mysqli_error($conn) . "</div>";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Historical Places</title>
    <style>
        body { font-family: Arial; background: #f4f6f8; padding: 20px; margin: 0; }
        .container { max-width: 960px; margin: auto; background: white; padding: 30px;
            border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        h2, h3 { text-align: center; color: #2c3e50; }
        input, textarea { width: 100%; padding: 10px; margin: 10px 0; border-radius: 5px; border: 1px solid #ccc; }
        input[type="submit"], button { background: #3498db; color: white; border: none; padding: 10px 20px;
            cursor: pointer; font-weight: bold; border-radius: 5px; }
        input[type="submit"]:hover, button:hover { background: #2980b9; }
        .success { color: green; font-weight: bold; text-align: center; }
        .error { color: red; font-weight: bold; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
        th { background-color: #3498db; color: white; }
        .action-btns form { display: inline; }
        .top-right { text-align: right; margin-bottom: 10px; }
    </style>
</head>
<body>
<div class="container">
    <h2>🏛️ Manage Tourist Places</h2>
    <?= $message ?>

    <!-- ➕ Add New Button -->
    <div class="top-right">
        <a href="?addnew=1">
            <button>➕ Add New Place</button>
        </a>
    </div>

    <!-- Unified Form (Add / Update) -->
    <form method="post">
        <?php if ($form_mode === 'edit'): ?>
            <input type="hidden" name="place_id" value="<?= htmlspecialchars($place['place_id']) ?>">
        <?php endif; ?>
        <input type="text" name="img_src" value="<?= $place['img_src'] ?? '' ?>" placeholder="Image Source URL" required>
        <input type="text" name="place_name" value="<?= $place['place_name'] ?? '' ?>" placeholder="Place Name" required>
        <textarea name="description" placeholder="Description" required><?= $place['description'] ?? '' ?></textarea>
        <input type="text" name="category" value="<?= $place['category'] ?? '' ?>" placeholder="Category" required>
        <input type="text" name="address" value="<?= $place['address'] ?? '' ?>" placeholder="Address" required>
        <input type="submit" name="<?= $form_mode === 'edit' ? 'update' : 'submit' ?>" value="<?= $form_mode === 'edit' ? 'Update Place' : 'Add Place' ?>">
    </form>

    <hr>

    <!-- 🔍 Search Bar -->
    <form method="get" style="margin-bottom: 20px;">
        <input type="text" name="search_name" placeholder="Search by Place Name" required>
        <input type="submit" value="Search">
    </form>

    <!-- 📋 Records Table -->
    <h3>📋 All Places</h3>
    <table>
        <tr>
            <th>ID</th><th>Image</th><th>Name</th><th>Description</th><th>Category</th><th>Address</th><th>Actions</th>
        </tr>
        <?php
        $searchQuery = "";
        if (isset($_GET['search_name'])) {
            $search_name = mysqli_real_escape_string($conn, $_GET['search_name']);
            $searchQuery = "WHERE place_name LIKE '%$search_name%'";
        }

        $all = mysqli_query($conn, "SELECT * FROM historical_place $searchQuery ORDER BY place_id ASC");
        if (mysqli_num_rows($all) > 0) {
            while ($row = mysqli_fetch_assoc($all)) {
                echo "<tr>
                    <td>{$row['place_id']}</td>
                    <td>{$row['img_src']}</td>
                    <td>{$row['place_name']}</td>
                    <td>{$row['description']}</td>
                    <td>{$row['category']}</td>
                    <td>{$row['address']}</td>
                    <td class='action-btns'>
                        <form method='post'>
                            <input type='hidden' name='edit_id' value='{$row['place_id']}'>
                            <button type='submit'>✏️ Edit</button>
                        </form>
                        <form method='post'>
                            <input type='hidden' name='delete' value='{$row['place_id']}'>
                            <button type='submit' onclick='return confirm(\"Are you sure?\")'>🗑️ Delete</button>
                        </form>
                    </td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='7' style='text-align:center;'>No records found for <strong>" . htmlspecialchars($_GET['search_name'] ?? '') . "</strong>.</td></tr>";
        }
        ?>
    </table>
</div>
</body>
</html>