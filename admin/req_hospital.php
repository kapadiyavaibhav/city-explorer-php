<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once 'includes/admin_header.php';

$host = 'localhost';
$dbname = 'project';
$user = 'root';
$pass = '';
$conn = mysqli_connect($host, $user, $pass, $dbname);
if (!$conn) { die('Connection failed: ' . mysqli_connect_error()); }

function arr2str($arr) { return is_array($arr) ? implode(',', $arr) : $arr; }
function str2arr($str) { return $str ? explode(',', $str) : []; }

$table_pending = "t_hospital";
$table_final   = "hospitals";

$editing = false;
$edit_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
$msg = "";

/* ================== APPROVE ================== */
if (isset($_GET['approve'])) {
    $id = intval($_GET['approve']);
    $res = mysqli_query($conn, "SELECT * FROM $table_pending WHERE id=$id");
    if ($row = mysqli_fetch_assoc($res)) {
       // remove 'id' field before insert (let AUTO_INCREMENT handle it)
unset($row['id']);

$fields = array_keys($row);
$values = array_map(fn($v)=>mysqli_real_escape_string($conn,$v), array_values($row));

$sql = "INSERT INTO $table_final (".implode(",",$fields).") VALUES ('".implode("','",$values)."')";
mysqli_query($conn, $sql);

        mysqli_query($conn, "DELETE FROM $table_pending WHERE id=$id");
        $msg = "Record approved successfully and moved!";
    }
}

/* ================== REJECT ================== */
if (isset($_GET['reject'])) {
    $id = intval($_GET['reject']);
    mysqli_query($conn, "DELETE FROM $table_pending WHERE id=$id");
    $msg = "Record rejected and deleted!";
}

/* ================== EDIT / ADD ================== */
if (isset($_POST['save'])) {
    $id = intval($_POST['id'] ?? 0);
    $fields = [
        'name','img','address','city_name','cat_name','contact','email',
        'opening_hours','emergency','visiting_hours','departments','services','doctors',
        'beds_general','beds_icu','beds_private',
        'laboratory','diagnostics','pharmacy','ambulance','wheelchair_accessible',
        'insurance','history','gallery','emergency_procedures','user_email'
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
                    } else {
                        $data[$f] = $old_img;
                    }
                } else {
                    $data[$f] = $old_img;
                }
            } else {
                $data[$f] = $old_img;
            }
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
        else if (in_array($f, ['departments','services']))
            $data[$f] = arr2str($_POST[$f] ?? []);
        else if ($f === 'doctors')
            $data[$f] = arr2str(array_map(function($n,$s,$q){return "$n|$s|$q";},
                $_POST['doc_name']??[],$_POST['doc_spec']??[],$_POST['doc_qual']??[]));
        else if (strpos($f,'beds_')===0)
            $data[$f] = intval($_POST[$f]??0);
        else if (in_array($f, ['laboratory','diagnostics','pharmacy','ambulance','wheelchair_accessible']))
            $data[$f] = $_POST[$f] ?? '';
        else
            $data[$f] = mysqli_real_escape_string($conn, $_POST[$f]??'');
    }

    if ($id) {
        $sets = [];
        foreach ($fields as $f) $sets[] = "$f='{$data[$f]}'";
        $sql = "UPDATE $table_pending SET ".implode(',',$sets)." WHERE id=$id";
        mysqli_query($conn, $sql);
        $msg = "Pending record edited successfully.";
        $edit_id = 0;
        $editing = false;
        unset($row);
    } else {
        $fieldstr = implode(',', $fields);
        $values = implode("','", array_map(fn($v)=>mysqli_real_escape_string($conn,$v), array_values($data)));
        $sql = "INSERT INTO $table_pending ($fieldstr) VALUES ('$values')";
        mysqli_query($conn, $sql);
        $msg = "New pending record added successfully.";
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
    <title>Pending Hospitals</title>
    <style>
        body { background: #f8fafc; font-family: Segoe UI,Arial; }
        .container { max-width: 1020px; margin: 32px auto; background: #fff; padding: 36px 40px; border-radius: 14px; box-shadow: 0 3px 16px #d2e5f9; }
        h2 { color: #2365ab; }
        .msg-success { background:#e1fbe3; color:#2d853a; padding:9px 14px; border-radius:6px; margin:12px 0 0 0; font-weight:bold; }
        .btn { padding: 5px 15px; border-radius: 5px; border: 0; background: #2680c2; color: #fff; cursor: pointer; text-decoration:none; }
        .btn-danger { background: #d03030; }
        .btn-add { background: #3bbf72; }
       
       
        form input[type=text], form input[type=email], form textarea {
            width: 98%; padding: 7px; border: 1px solid #b4d1e5; border-radius: 5px; margin-bottom: 7px;
        }
        form select, form input[type=number] { padding: 5px; border-radius: 4px; }
        label { font-weight:bold; margin-top:7px; display:block; }
        .form-section { margin-bottom: 18px; }
        .yesno { margin-bottom: 5px; }
        .actions { margin-top: 16px; }
        .actions button { margin-right: 8px; }
        table { border-collapse: collapse; width: 100%; margin-top: 24px; background: #f7fafc; }
        th, td { border: 1px solid #dde7f4; padding: 8px 6px; }
        th { background: #eaf2fb; color: #245f8e; }
        tr:nth-child(even) { background: #f4f9fd; }
        .btn { padding: 5px 15px; border-radius: 5px; border: 0; background: #2680c2; color: #fff; cursor: pointer; }
        
        .small { font-size: 0.95em; color: #888; }
        .inline { display: inline-block; margin-right: 10px; }
        #searchBar { margin-bottom: 15px; padding: 8px; width: 300px; border-radius: 6px; border: 1px solid #b4d1e5; }
        .gallery-img-block { display: inline-flex; align-items: center; margin-right: 16px; margin-bottom: 5px;}
        .gallery-img { max-height: 70px; border-radius: 5px; border:1px solid #eee;}
        .gallery-remove-btn { background: #e74c3c; color: #fff; border: none; border-radius: 3px; padding:3px 8px; margin-left:6px; cursor:pointer; font-size: 0.9em;}
        .logo-img-block { margin-top:5px; display:inline-flex; align-items:center; }
    </style>
</head>
<body>

<div class="container">
    <h2><?= $editing ? "Edit Pending Hospital" : "Add Pending Hospital" ?></h2>

    <!-- ================== FULL FORM (same as your original) ================== -->
    <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="id" value="<?= $editing ? $row['id'] : '' ?>">

        <label>Hospital Name</label>
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

        <label>Contact Number</label>
        <input type="text" name="contact" value="<?= htmlspecialchars($editing?$row['contact']:'') ?>">

        <label>Email Address</label>
        <input type="email" name="email" value="<?= htmlspecialchars($editing?$row['email']:'') ?>">

        <label>Opening Hours</label>
        <input type="text" name="opening_hours" value="<?= htmlspecialchars($editing?$row['opening_hours']:'') ?>">

        <label>Emergency Department Availability</label>
        <input type="text" name="emergency" value="<?= htmlspecialchars($editing?$row['emergency']:'') ?>">

        <label>Visiting Hours</label>
        <input type="text" name="visiting_hours" value="<?= htmlspecialchars($editing?$row['visiting_hours']:'') ?>">

        <div class="form-section">
            <label>Departments & Specialties</label>
            <input type="text" name="departments[]" value="<?= htmlspecialchars($editing?$row['departments']:'') ?>" placeholder="Cardiology,Orthopedics,Pediatrics">
        </div>

        <div class="form-section">
            <label>Services Provided</label>
            <input type="text" name="services[]" value="<?= htmlspecialchars($editing?$row['services']:'') ?>" placeholder="Outpatient,Surgery,Wellness">
        </div>

        <div class="form-section">
            <label>Doctors</label>
            <div id="doctors">
                <?php
                $doctors = $editing ? str2arr($row['doctors']) : ['||'];
                foreach ($doctors as $i=>$doc) {
                    list($n,$s,$q) = array_pad(explode('|',$doc),3,'');
                ?>
                    <div class="doc-block">
                        <input type="text" name="doc_name[]" placeholder="Name" value="<?= htmlspecialchars($n) ?>">
                        <input type="text" name="doc_spec[]" placeholder="Specialization" value="<?= htmlspecialchars($s) ?>">
                        <input type="text" name="doc_qual[]" placeholder="Qualifications" value="<?= htmlspecialchars($q) ?>">
                    </div>
                <?php } ?>
                <div id="doctor-controls">
                    <button type="button" onclick="addDoc()">Add Doctor</button>
                    <button type="button" onclick="removeDoc()">Remove Doctor</button>
                </div>
            </div>
        </div>

        <div class="form-section">
            <label>Beds & Room Types</label>
            <input type="number" name="beds_general" min="0" placeholder="General Beds" value="<?= $editing?$row['beds_general']:'' ?>"> General
            <input type="number" name="beds_icu" min="0" placeholder="ICU Beds" value="<?= $editing?$row['beds_icu']:'' ?>"> ICU
            <input type="number" name="beds_private" min="0" placeholder="Private Beds" value="<?= $editing?$row['beds_private']:'' ?>"> Private
        </div>

        <div class="form-section">
            <label>Facilities & Amenities</label>
            <div><input type="checkbox" name="laboratory" value="Laboratory" <?= $editing && $row['laboratory']=='Laboratory'?'checked':'' ?>> Laboratory</div>
            <div><input type="checkbox" name="diagnostics" value="Diagnostics" <?= $editing && $row['diagnostics']=='Diagnostics'?'checked':'' ?>> Diagnostics</div>
            <div><input type="checkbox" name="pharmacy" value="Pharmacy" <?= $editing && $row['pharmacy']=='Pharmacy'?'checked':'' ?>> Pharmacy</div>
            <div><input type="checkbox" name="ambulance" value="Ambulance" <?= $editing && $row['ambulance']=='Ambulance'?'checked':'' ?>> Ambulance</div>
            <div><input type="checkbox" name="wheelchair_accessible" value="Wheelchair Accessible" <?= $editing && $row['wheelchair_accessible']=='Wheelchair Accessible'?'checked':'' ?>> Wheelchair Accessible</div>
        </div>

        <div class="form-section">
            <label>Insurance Accepted</label>
            <label><input type="radio" name="insurance" value="Yes" <?= $editing&&$row['insurance']=='Yes'?'checked':'' ?>> Yes</label>
            <label><input type="radio" name="insurance" value="No" <?= $editing&&$row['insurance']=='No'?'checked':'' ?>> No</label>
        </div>

        <div class="form-section">
            <label>Hospital History</label>
            <textarea name="history" rows="2"><?= htmlspecialchars($editing?$row['history']??'':'') ?></textarea>
        </div>

        <div class="form-section">
            <label>Photo Gallery</label>
            <input type="file" name="gallery[]" accept="image/*" multiple>
            <?php if ($editing && !empty($row['gallery'])):
                $galleryImgs = str2arr($row['gallery']);
                foreach ($galleryImgs as $img):
                    if ($img): ?>
                        <img src="uploads/<?= htmlspecialchars($img) ?>" style="max-height:70px;margin:5px;border:1px solid #ccc;">
                    <?php endif;
                endforeach;
            endif; ?>
        </div>

        <div class="form-section">
            <label>Emergency Procedures</label>
            <textarea name="emergency_procedures" rows="2"><?= htmlspecialchars($editing?$row['emergency_procedures']??'':'') ?></textarea>
        </div>

        <label>City Name</label>
        <input type="text" name="city_name" value="<?= htmlspecialchars($editing?$row['city_name']??'':'') ?>">

        <input type="hidden" name="cat_name" value="Hospital">
        <input type="hidden" name="user_email" value="">

        <div class="actions">
            <button class="btn" type="submit" name="save"><?= $editing ? 'Update' : 'Add' ?></button>
            <?php if (!empty($msg)): ?>
              <div class="msg-success"><?= $msg ?></div>
            <?php endif; ?>
        </div>
    </form>

    <h2>All Pending Hospitals</h2>
    <table>
        <tr>
            <th>ID</th><th>Name</th><th>City</th><th>Category</th><th>Contact</th><th>Email</th><th>Beds</th><th>Actions</th>
        </tr>
        <?php foreach($rows as $r): ?>
            <tr>
                <td><?= $r['id'] ?></td>
                <td><?= htmlspecialchars($r['name']) ?></td>
                <td><?= htmlspecialchars($r['city_name']) ?></td>
                <td><?= htmlspecialchars($r['cat_name']) ?></td>
                <td><?= htmlspecialchars($r['contact']) ?></td>
                <td><?= htmlspecialchars($r['email']) ?></td>
                <td><?= $r['beds_general']+$r['beds_icu']+$r['beds_private'] ?></td>
                <td>
                    <a class="btn btn-add" href="?approve=<?= $r['id'] ?>">Approve</a>
                    <a class="btn" href="?edit=<?= $r['id'] ?>">Edit</a>
                    <a class="btn btn-danger" href="?reject=<?= $r['id'] ?>" onclick="return confirm('Reject this record?')">Reject</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<script>
function addDoc() {
    var d = document.createElement('div');
    d.className = 'doc-block';
    d.innerHTML = `
        <input type="text" name="doc_name[]" placeholder="Name">
        <input type="text" name="doc_spec[]" placeholder="Specialization">
        <input type="text" name="doc_qual[]" placeholder="Qualifications">
    `;
    document.getElementById('doctors').insertBefore(d, document.getElementById('doctor-controls'));
}
function removeDoc() {
    var doctorBlocks = document.querySelectorAll('#doctors .doc-block');
    if (doctorBlocks.length > 1) {
        doctorBlocks[doctorBlocks.length - 1].remove();
    } else {
        alert('At least one doctor record is required!');
    }
}
</script>

</body>
</html>
