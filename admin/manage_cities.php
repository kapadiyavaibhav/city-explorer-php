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
if (mysqli_connect_errno()) {
    die("Connection failed: " . mysqli_connect_error());
}

$message = '';
$city = null;
$form_mode = 'insert'; // default form mode
$btn_label = 'Add City';

if (isset($_POST['insert'])) {
    $message = insertCity($conn, $_POST);
} elseif (isset($_POST['update'])) {
    $message = updateCity($conn, $_POST);
} elseif (isset($_POST['search'])) {
    $city_name = $_POST['city_name'];
    list($city, $message) = searchCity($conn, $city_name);
    if ($city) {
        $form_mode = 'update';
        $btn_label = 'Update City';
    }
} elseif (isset($_POST['edit_id'])) {
    $city_id = $_POST['edit_id'];
    list($city, $message) = searchCity($conn, $city_id);
    if ($city) {
        $form_mode = 'update';
        $btn_label = 'Update City';
    }
} elseif (isset($_POST['delete'])) {
    $city_id = $_POST['delete'];
    $message = deleteCity($conn, $city_id);
}

function insertCity($conn, $data) {
    $img_src = $data['img_src'];
    $city_name = $data['city_name'];
    $description = $data['description'];
    $check = mysqli_query($conn, "SELECT city_name FROM city WHERE city_name='$city_name'");
    if (mysqli_num_rows($check) > 0) {
        return "<div class='error'>❌ City name already exists.</div>";
    }
    $sql = "INSERT INTO city (img_src, city_name, description) VALUES ('$img_src', '$city_name', '$description')";
    return mysqli_query($conn, $sql)
        ? "<div class='success'>✅ City added successfully!</div>"
        : "<div class='error'>❌ Error: " . mysqli_error($conn) . "</div>";
}

function searchCity($conn, $name) {
    $result = mysqli_query($conn, "SELECT * FROM city WHERE city_name='$name' OR city_id='$name'");
    return mysqli_num_rows($result) > 0
        ? [mysqli_fetch_assoc($result), ""]
        : [null, "<div class='error'>❌ City not found.</div>"];
}

function updateCity($conn, $data) {
    $sql = "UPDATE city SET img_src='{$data['img_src']}', city_name='{$data['city_name']}', description='{$data['description']}' WHERE city_id='{$data['city_id']}'";
    return mysqli_query($conn, $sql)
        ? "<div class='success'>✅ City updated successfully.</div>"
        : "<div class='error'>❌ Error: " . mysqli_error($conn) . "</div>";
}

function deleteCity($conn, $city_id) {
    return mysqli_query($conn, "DELETE FROM city WHERE city_id='$city_id'")
        ? "<div class='success'>✅ City deleted successfully.</div>"
        : "<div class='error'>❌ Error: " . mysqli_error($conn) . "</div>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Cities</title>
    <style>
        body { font-family: Arial; background: #f4f6f8; padding: 20px; margin: 0; }
        .container { max-width: 900px; margin: auto; background: white; padding: 30px;
            border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.1); }
        h2, h3 { text-align: center; color: #2c3e50; }
        input, textarea {
            width: 100%; padding: 12px; margin: 10px 0 20px 0; border-radius: 6px;
            border: 1px solid #ccc; font-size: 16px;
        }
        input[type="submit"], button {
            background-color: #3498db; color: white; border: none; padding: 10px 15px;
            border-radius: 5px; cursor: pointer; font-weight: bold;
        }
        input[type="submit"]:hover, button:hover { background-color: #2980b9; }
        .btn-group { display: flex; gap: 10px; justify-content: flex-start; }
        .success, .error { font-weight: bold; text-align: center; }
        .success { color: green; }
        .error { color: red; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ccc; text-align: left; }
        th { background-color: #3498db; color: white; }
        .action-btns form { display: inline; }
        .top-btn { float: right; background: #2ecc71; }
        .top-btn:hover { background: #27ae60; }
    </style>
</head>
<body>

<div class="container">
    <h2>🌆 Manage Cities</h2>
    <?= $message ?>

    <form method="post">
        <?php if ($form_mode == 'update'): ?>
            <input type="hidden" name="city_id" value="<?= htmlspecialchars($city['city_id']) ?>">
        <?php endif; ?>
        <input type="text" name="img_src" placeholder="Image Source (URL)" value="<?= $city['img_src'] ?? '' ?>" required>
        <input type="text" name="city_name" placeholder="City Name" value="<?= $city['city_name'] ?? '' ?>" required>
        <textarea name="description" placeholder="City Description" rows="4" required><?= $city['description'] ?? '' ?></textarea>
        <input type="submit" name="<?= $form_mode ?>" value="<?= $btn_label ?>">
        <?php if ($form_mode == 'update'): ?>
            <button class="top-btn" onclick="window.location.href='manage_city.php'">➕ Add New</button>
        <?php endif; ?>
    </form>

    <form method="post">
        <input type="text" name="city_name" placeholder="Search by City Name or ID" required>
        <input type="submit" name="search" value="Search">
    </form>

    <hr>
    <h3>📋 All City Records</h3>
    <table>
        <tr>
            <th>City ID</th>
            <th>Image Source</th>
            <th>City Name</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
        <?php
        $all = mysqli_query($conn, "SELECT * FROM city ORDER BY city_id ASC");
        if (mysqli_num_rows($all) > 0) {
            while ($row = mysqli_fetch_assoc($all)) {
                echo "<tr>
                    <td>" . htmlspecialchars($row['city_id']) . "</td>
                    <td>" . htmlspecialchars($row['img_src']) . "</td>
                    <td>" . htmlspecialchars($row['city_name']) . "</td>
                    <td>" . htmlspecialchars($row['description']) . "</td>
                    <td class='action-btns'>
                        <form method='post'>
                            <input type='hidden' name='edit_id' value='" . $row['city_id'] . "'>
                            <button type='submit'>✏️ Edit</button>
                        </form>
                        <form method='post' onsubmit='return confirm(\"Are you sure you want to delete this city?\")'>
                            <input type='hidden' name='delete' value='" . $row['city_id'] . "'>
                            <button type='submit'>🗑️ Delete</button>
                        </form>
                    </td>
                </tr>";
            }
        } else {
            echo "<tr><td colspan='5' style='text-align:center;'>No records found.</td></tr>";
        }
        ?>
    </table>
</div>

</body>
</html>