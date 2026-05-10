<?php
require_once 'includes/admin_header.php';
$host = 'localhost'; $dbname = 'project'; $user = 'root'; $pass = '';
$conn = mysqli_connect($host, $user, $pass, $dbname);
if (!$conn) { die('Connection failed: ' . mysqli_connect_error()); }

function arr2str($arr) { return is_array($arr)?implode(',',$arr):$arr; }
function str2arr($str) { return $str?explode(',',$str):[]; }

$table_pending = "t_restaurants";
$table_final   = "restaurants";

$editing = false;
$edit_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
$msg = "";

/* ================== APPROVE ================== */
if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);
    $res = mysqli_query($conn, "SELECT * FROM $table_pending WHERE id=$id");
    if ($row = mysqli_fetch_assoc($res)) {
        unset($row['id']); // auto increment id
        $fields = array_keys($row);
        $values = array_map(fn($v)=>mysqli_real_escape_string($conn,$v), array_values($row));
        $sql = "INSERT INTO $table_final (".implode(",",$fields).") VALUES ('".implode("','",$values)."')";
        mysqli_query($conn, $sql);
        mysqli_query($conn, "DELETE FROM $table_pending WHERE id=$id");
        $msg = "Restaurant approved and moved to final!";
    }
}

/* ================== REJECT ================== */
if (isset($_GET['reject'])) {
    $id = intval($_GET['reject']);
    mysqli_query($conn, "DELETE FROM $table_pending WHERE id=$id");
    $msg = "Restaurant rejected and deleted!";
}

/* ================== SAVE (ADD/EDIT) ================== */
if (isset($_POST['save'])) {
    $id = intval($_POST['id'] ?? 0);
    $fields = [
        'name','img','address','city_name','cat_name','contact','email','website',
        'opening_hours','closed_on','reservation_required','special_offers',
        'cuisines','menu','services','chefs',
        'wifi','parking','outdoor_seating','live_music','bar','kids_friendly','pet_friendly','wheelchair_accessible',
        'payment_options','description','gallery','user_email'
    ];
    $data = [];

    $old_img = '';
    $old_gallery = [];
    if ($id) {
        $fetch = mysqli_query($conn, "SELECT img,gallery FROM $table_pending WHERE id=$id");
        $old_data = mysqli_fetch_assoc($fetch);
        $old_img = $old_data['img'] ?? '';
        $old_gallery = str2arr($old_data['gallery'] ?? '');
    }

    foreach ($fields as $f) {
        if ($f === 'img') {
            if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/uploads/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
                $filename = uniqid() . '_' . basename($_FILES['img']['name']);
                $targetFile = $uploadDir . $filename;
                $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
                $allowed = ['jpg','jpeg','png','gif','webp'];
                if (in_array($fileType, $allowed)) {
                    if (move_uploaded_file($_FILES['img']['tmp_name'], $targetFile)) {
                        $data[$f] = $filename;
                        if ($id && $old_img && $old_img !== $filename) {
                            $oldFile = $uploadDir . $old_img;
                            if (file_exists($oldFile)) unlink($oldFile);
                        }
                    } else { $data[$f] = $old_img; }
                } else { $data[$f] = $old_img; }
            } else { $data[$f] = $old_img; }
        }
        else if ($f === 'gallery') {
            $newGalleryImages = [];
            $uploadDir = __DIR__ . '/uploads/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
            if (isset($_FILES['gallery']['name']) && is_array($_FILES['gallery']['name'])) {
                foreach ($_FILES['gallery']['name'] as $i => $imgName) {
                    if (!empty($imgName) && $_FILES['gallery']['error'][$i] === UPLOAD_ERR_OK) {
                        $fileType = strtolower(pathinfo($imgName, PATHINFO_EXTENSION));
                        $allowed = ['jpg','jpeg','png','gif','webp'];
                        if (in_array($fileType, $allowed)) {
                            $filename = uniqid() . '_' . basename($imgName);
                            $targetFile = $uploadDir . $filename;
                            if (move_uploaded_file($_FILES['gallery']['tmp_name'][$i], $targetFile)) {
                                $newGalleryImages[] = $filename;
                            }
                        }
                    }
                }
            }
            $galleryImages = $id ? array_merge($old_gallery, $newGalleryImages) : $newGalleryImages;
            $data[$f] = arr2str($galleryImages);
        }
        elseif (in_array($f, ['cuisines','menu','services']))
            $data[$f] = arr2str($_POST[$f] ?? []);
        elseif ($f === 'chefs')
            $data[$f] = arr2str(array_map(function($n,$s,$e){return "$n|$s|$e";},
                $_POST['chef_name']??[],$_POST['chef_spec']??[],$_POST['chef_exp']??[]));
        elseif (in_array($f, ['wifi','parking','outdoor_seating','live_music','bar','kids_friendly','pet_friendly','wheelchair_accessible']))
            $data[$f] = isset($_POST[$f]) ? $_POST[$f] : '';
        else
            $data[$f] = mysqli_real_escape_string($conn, $_POST[$f]??'');
    }

    if ($id) {
        $sets = [];
        foreach ($fields as $f) $sets[] = "$f='{$data[$f]}'";
        $sql = "UPDATE $table_pending SET ".implode(',',$sets)." WHERE id=$id";
        mysqli_query($conn, $sql);
        $msg = "Pending restaurant updated!";
        $edit_id = 0; $editing = false; unset($row);
    } else {
        $fieldstr = implode(',', $fields);
        $values = implode("','", array_map(fn($v)=>mysqli_real_escape_string($conn,$v), array_values($data)));
        $sql = "INSERT INTO $table_pending ($fieldstr) VALUES ('$values')";
        mysqli_query($conn, $sql);
        $msg = "New pending restaurant added!";
    }
}

/* ================== LOAD EDIT RECORD ================== */
if ($edit_id) {
    $res = mysqli_query($conn, "SELECT * FROM $table_pending WHERE id=$edit_id");
    $row = mysqli_fetch_assoc($res);
    if ($row) $editing = true;
}

/* ================== FETCH ALL PENDING ================== */
$res = mysqli_query($conn, "SELECT * FROM $table_pending ORDER BY id DESC");
$rows = [];
while($r = mysqli_fetch_assoc($res)) $rows[] = $r;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Pending Restaurants</title>
    <style>
      body { background: #f8fafc; font-family: Segoe UI,Arial; }
        .container { max-width: 1020px; margin: 32px auto; background: #fff; padding: 36px 40px; border-radius: 14px; box-shadow: 0 3px 16px #d2e5f9; }
        h2 { color: #ab5123; }
        .msg-success { background:#e1fbe3; color:#2d853a; padding:9px 14px; border-radius:6px; margin:12px 0 0 0; font-weight:bold; }
        form input[type=text], form input[type=email], form textarea {
            width: 98%; padding: 7px; border: 1px solid #e5cbb4; border-radius: 5px; margin-bottom: 7px;
        }
        form select, form input[type=number] { padding: 5px; border-radius: 4px; }
        label { font-weight:bold; margin-top:7px; display:block; }
        .form-section { margin-bottom: 18px; }
        .yesno { margin-bottom: 5px; }
        .actions { margin-top: 16px; }
        .actions button { margin-right: 8px; }
        table { border-collapse: collapse; width: 100%; margin-top: 24px; background: #f7fafc; }
        th, td { border: 1px solid #eeded1; padding: 8px 6px; }
        th { background: #fbeeea; color: #8e5524; }
        tr:nth-child(even) { background: #fdf4f4; }
        .btn { padding: 5px 15px; border-radius: 5px; border: 0; background: #e67d22; color: #fff; cursor: pointer; }
        .btn-danger { background: #d03030; }
        .btn-add { background: #3bbf72; }
        .small { font-size: 0.95em; color: #888; }
        .inline { display: inline-block; margin-right: 10px; }
        #searchBar { margin-bottom: 15px; padding: 8px; width: 300px; border-radius: 6px; border: 1px solid #e5cbb4; }
        .gallery-img-block { display: inline-flex; align-items: center; margin-right: 16px; margin-bottom: 5px;}
        .gallery-img { max-height: 70px; border-radius: 5px; border:1px solid #eee;}
        .gallery-remove-btn { background: #e74c3c; color: #fff; border: none; border-radius: 3px; padding:3px 8px; margin-left:6px; cursor:pointer; font-size: 0.9em;}
        .logo-img-block { margin-top:5px; display:inline-flex; align-items:center; }
    </style>
</head>
<body>
<div class="container">
    <h2><?= $editing ? "Edit Pending Restaurant" : "Add Pending Restaurant" ?></h2>

 <form method="post" enctype="multipart/form-data" id="restaurantForm">
        <input type="hidden" name="id" value="<?= $editing ? $row['id'] : '' ?>">

        <label>Restaurant Name</label>
        <input type="text" name="name" required value="<?= htmlspecialchars($editing?$row['name']:'') ?>">

        <label>Logo/Image File</label>
        <input type="file" name="img" accept="image/*">
        <?php if ($editing && !empty($row['img'])): ?>
            <div class="logo-img-block">
                <img src="uploads/<?= htmlspecialchars($row['img']) ?>" alt="Logo" style="max-height:80px;">
            </div>
        <?php endif; ?>

        <label>Address</label>
        <input type="text" name="address" value="<?= htmlspecialchars($editing?$row['address']:'') ?>">

        <input type="hidden" name="cat_name" value="Restaurant">

        <label>Contact Number</label>
        <input type="text" name="contact" value="<?= htmlspecialchars($editing?$row['contact']:'') ?>">

        <label>Email Address</label>
        <input type="email" name="email" value="<?= htmlspecialchars($editing?$row['email']:'') ?>">

        <label>Website</label>
        <input type="text" name="website" value="<?= htmlspecialchars($editing?$row['website']:'') ?>">

        <label>Opening Hours</label>
        <input type="text" name="opening_hours" value="<?= htmlspecialchars($editing?$row['opening_hours']:'') ?>">

        <label>Closed On</label>
        <input type="text" name="closed_on" value="<?= htmlspecialchars($editing?$row['closed_on']:'') ?>">

        <label>Reservation Required (Yes/No)</label>
        <input type="text" name="reservation_required" value="<?= htmlspecialchars($editing?$row['reservation_required']:'') ?>">

        <label>Special Offers</label>
        <input type="text" name="special_offers" value="<?= htmlspecialchars($editing?$row['special_offers']:'') ?>">

        <div class="form-section">
            <label>Cuisines <span class="small">(comma-separated)</span></label>
            <input type="text" name="cuisines[]" value="<?= htmlspecialchars($editing?str_replace(',','&#10;',$row['cuisines']??''):'') ?>" placeholder="Indian,Chinese,Italian">
        </div>

        <div class="form-section">
            <label>Menu Highlights <span class="small">(comma-separated)</span></label>
            <input type="text" name="menu[]" value="<?= htmlspecialchars($editing?str_replace(',','&#10;',$row['menu']??''):'') ?>" placeholder="Paneer Makhani,Manchurian,Pasta">
        </div>

        <div class="form-section">
            <label>Services <span class="small">(comma-separated)</span></label>
            <input type="text" name="services[]" value="<?= htmlspecialchars($editing?str_replace(',','&#10;',$row['services']??''):'') ?>" placeholder="Dine-in,Takeaway,Delivery">
        </div>

        <div class="form-section">
            <label>Chefs</label>
            <div id="chefs">
                <?php
                $chefs = $editing ? str2arr($row['chefs']) : ['||'];
                foreach ($chefs as $i=>$chef) {
                    list($n,$s,$e) = array_pad(explode('|',$chef),3,'');
                ?>
                    <div class="chef-block">
                        <input type="text" name="chef_name[]" placeholder="Name" value="<?= htmlspecialchars($n) ?>">
                        <input type="text" name="chef_spec[]" placeholder="Specialization" value="<?= htmlspecialchars($s) ?>">
                        <input type="text" name="chef_exp[]" placeholder="Experience (years)" value="<?= htmlspecialchars($e) ?>">
                    </div>
                <?php } ?>
                <div id="chef-controls">
                    <button type="button" onclick="addChef()">Add Chef</button>
                    <button type="button" onclick="removeChef()">Remove Chef</button>
                </div>
            </div>
        </div>

        <div class="form-section">
            <label>Amenities & Features</label>
            <div class="yesno inline"><input type="checkbox" name="wifi" value="Wi-Fi" <?= $editing && $row['wifi']=='Wi-Fi'?'checked':'' ?>> Free Wi-Fi</div>
            <div class="yesno inline"><input type="checkbox" name="parking" value="Parking" <?= $editing && $row['parking']=='Parking'?'checked':'' ?>> Parking Available</div>
            <div class="yesno inline"><input type="checkbox" name="outdoor_seating" value="Outdoor Seating" <?= $editing && $row['outdoor_seating']=='Outdoor Seating'?'checked':'' ?>> Outdoor Seating</div>
            <div class="yesno inline"><input type="checkbox" name="live_music" value="Live Music" <?= $editing && $row['live_music']=='Live Music'?'checked':'' ?>> Live Music</div>
            <div class="yesno inline"><input type="checkbox" name="bar" value="Bar" <?= $editing && $row['bar']=='Bar'?'checked':'' ?>> Bar</div>
            <div class="yesno inline"><input type="checkbox" name="kids_friendly" value="Kids Friendly" <?= $editing && $row['kids_friendly']=='Kids Friendly'?'checked':'' ?>> Kids Friendly</div>
            <div class="yesno inline"><input type="checkbox" name="pet_friendly" value="Pet Friendly" <?= $editing && $row['pet_friendly']=='Pet Friendly'?'checked':'' ?>> Pet Friendly</div>
            <div class="yesno inline"><input type="checkbox" name="wheelchair_accessible" value="Wheelchair Accessible" <?= $editing && $row['wheelchair_accessible']=='Wheelchair Accessible'?'checked':'' ?>> Wheelchair Accessible</div>
        </div>

        <div class="form-section">
            <label>Payment Options <span class="small">(comma-separated)</span></label>
            <input type="text" name="payment_options" value="<?= htmlspecialchars($editing?$row['payment_options']:'') ?>" placeholder="Cash,Credit Card,UPI">
        </div>

        <div class="form-section">
            <label>Description</label>
            <textarea name="description" rows="2"><?= htmlspecialchars($editing?$row['description']:'') ?></textarea>
        </div>

        <div class="form-section">
            <label>Photo Gallery <span class="small">(You can upload multiple images)</span></label>
            <input type="file" name="gallery[]" accept="image/*" multiple>
            <div>
            <?php if ($editing && !empty($row['gallery'])):
                $galleryImgs = str2arr($row['gallery']);
                foreach ($galleryImgs as $img):
                    if ($img): ?>
                    <span class="gallery-img-block">
                        <img class="gallery-img" src="uploads/<?= htmlspecialchars($img) ?>" alt="Gallery">
                    </span>
                    <?php
                    endif;
                endforeach;
            endif; ?>
            </div>
        </div>
        <label>City Name</label>
        <input type="text" name="city_name" value="<?= htmlspecialchars($editing?$row['city_name']:'') ?>">
        <input type="hidden" name="user_email" value="">
        
        <div class="actions">
            <button class="btn" type="submit" name="save"><?= $editing ? 'Update' : 'Add' ?></button>
            <?php if (!empty($msg)): ?>
              <div class="msg-success"><?= $msg ?></div>
            <?php endif; ?>
        </div>
    </form>
    <?php
    if ($editing && !empty($row['gallery'])):
        $galleryImgs = str2arr($row['gallery']);
        echo '<div style="margin-bottom:18px;">';
        foreach ($galleryImgs as $img):
            if ($img): ?>
            <span class="gallery-img-block">
                <form method="post" style="display:inline;">
                    <input type="hidden" name="id" value="<?= $row['id'] ?>">
                    <input type="hidden" name="remove_gallery_img" value="<?= htmlspecialchars($img) ?>">
                    <button type="submit" class="gallery-remove-btn" onclick="return confirm('Remove this image?')">Remove</button>
                </form>
            </span>
            <?php
            endif;
        endforeach;
        echo '</div>';
    endif;
    ?>

   
    <h2>All Pending Restaurants</h2>
    <table>
        <tr>
            <th>ID</th><th>Name</th><th>City</th><th>Category</th><th>Contact</th><th>Email</th><th>Actions</th>
        </tr>
        <?php foreach($rows as $r): ?>
            <tr>
                <td><?= $r['id'] ?></td>
                <td><?= htmlspecialchars($r['name']) ?></td>
                <td><?= htmlspecialchars($r['city_name']) ?></td>
                <td><?= htmlspecialchars($r['cat_name']) ?></td>
                <td><?= htmlspecialchars($r['contact']) ?></td>
                <td><?= htmlspecialchars($r['email']) ?></td>
                <td>
                    <a class="btn btn-add" href="?approve=<?= $r['id'] ?>">Approve</a>
                    <a class="btn" href="?edit=<?= $r['id'] ?>">Edit</a>
                    <a class="btn btn-danger" href="?reject=<?= $r['id'] ?>" onclick="return confirm('Reject this record?')">Reject</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
</body>
</html>
