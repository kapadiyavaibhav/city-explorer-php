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

$editing = false;
$edit_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
$msg = '';

// Handle gallery image removal (separate POST, outside main form)
if (isset($_POST['remove_gallery_img']) && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $remove_img = $_POST['remove_gallery_img'];
    $res = mysqli_query($conn, "SELECT gallery FROM hospitals WHERE id=$id");
    $row = mysqli_fetch_assoc($res);
    $gallery = str2arr($row['gallery']);
    $gallery = array_filter($gallery, fn($img) => $img !== $remove_img);
    $gallery_str = arr2str($gallery);
    mysqli_query($conn, "UPDATE hospitals SET gallery='$gallery_str' WHERE id=$id");
    $filePath = __DIR__ . '../images/' . $remove_img;
    if (file_exists($filePath)) unlink($filePath);
    $msg = "Gallery image removed successfully.";
    $edit_id = $id;
}

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

    // Fetch old images for edit
    $old_img = '';
    $old_gallery = [];
    if ($id) {
        $fetch = mysqli_query($conn, "SELECT img,gallery FROM hospitals WHERE id=$id");
        $old_data = mysqli_fetch_assoc($fetch);
        $old_img = $old_data['img'] ?? '';
        $old_gallery = str2arr($old_data['gallery'] ?? '');
    }

    foreach ($fields as $f) {
        if ($f === 'img') {
            // Keep existing logo image if none selected
            if (isset($_FILES['img']) && $_FILES['img']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../images/';
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
            // ---- FIXED: Merge new uploads with old gallery images ----
            $newGalleryImages = [];
            $uploadDir = __DIR__ . '/../images/';
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
            // Merge old gallery images with new uploads
            if ($id) {
                $galleryImages = array_merge($old_gallery, $newGalleryImages);
            } else {
                $galleryImages = $newGalleryImages;
            }
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
        $sql = "UPDATE hospitals SET ".implode(',',$sets)." WHERE id=$id";
        mysqli_query($conn, $sql);
        $msg = "Record edited successfully.";
        $edit_id = 0;     // <-- Reset edit mode
        $editing = false; // <-- Reset edit mode
        unset($row);      // <-- Remove edit data
    } else {
        $fieldstr = implode(',', $fields);
        $values = implode("','", array_map(fn($v)=>mysqli_real_escape_string($conn,$v), array_values($data)));
        $sql = "INSERT INTO hospitals ($fieldstr) VALUES ('$values')";
        mysqli_query($conn, $sql);
        $msg = "Record added successfully.";
    }
}

if (isset($_GET['del'])) {
    $id = intval($_GET['del']);
    $res = mysqli_query($conn, "SELECT img,gallery FROM hospitals WHERE id=$id");
    $row = mysqli_fetch_assoc($res);
    if ($row) {
        if (!empty($row['img'])) {
            $filePath = __DIR__ . '/../images/' . $row['img'];
            if (file_exists($filePath)) unlink($filePath);
        }
        if (!empty($row['gallery'])) {
            foreach (str2arr($row['gallery']) as $gimg) {
                if ($gimg) {
                    $filePath = __DIR__ . '/../images/' . $gimg;
                    if (file_exists($filePath)) unlink($filePath);
                }
            }
        }
    }
    mysqli_query($conn, "DELETE FROM hospitals WHERE id=$id");
    $msg = "Record deleted successfully.";
}

if ($edit_id) {
    $res = mysqli_query($conn, "SELECT * FROM hospitals WHERE id=$edit_id");
    $row = mysqli_fetch_assoc($res);
    if ($row) $editing = true;
}

$res = mysqli_query($conn, "SELECT * FROM hospitals ORDER BY id DESC");
$rows = [];
while($r = mysqli_fetch_assoc($res)) $rows[] = $r;
?>
<!DOCTYPE html>
<html>
<head>
    <title>Hospital Admin</title>
    <style>
        body { background: #f8fafc; font-family: Segoe UI,Arial; }
        .container { max-width: 1020px; margin: 32px auto; background: #fff; padding: 36px 40px; border-radius: 14px; box-shadow: 0 3px 16px #d2e5f9; }
        h2 { color: #2365ab; }
        .msg-success { background:#e1fbe3; color:#2d853a; padding:9px 14px; border-radius:6px; margin:12px 0 0 0; font-weight:bold; }
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
        .btn-danger { background: #d03030; }
        .btn-add { background: #3bbf72; }
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
    <h2><?= $editing ? "Edit Hospital" : "Add Hospital" ?></h2>
    <form method="post" enctype="multipart/form-data" id="hospitalForm">
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
            <label>Departments & Specialties <span class="small">(comma-separated)</span></label>
            <input type="text" name="departments[]" value="<?= htmlspecialchars($editing?str_replace(',','&#10;',$row['departments']??''):'') ?>" placeholder="Cardiology,Orthopedics,Pediatrics">
        </div>

        <div class="form-section">
            <label>Services Provided <span class="small">(comma-separated)</span></label>
            <input type="text" name="services[]" value="<?= htmlspecialchars($editing?str_replace(',','&#10;',$row['services']??''):'') ?>" placeholder="Outpatient,Surgery,Wellness">
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
            <div class="yesno inline"><input type="checkbox" name="laboratory" value="Laboratory" <?= $editing && $row['laboratory']=='Laboratory'?'checked':'' ?>> Laboratory</div>
            <div class="yesno inline"><input type="checkbox" name="diagnostics" value="Diagnostics" <?= $editing && $row['diagnostics']=='Diagnostics'?'checked':'' ?>> Diagnostics</div>
            <div class="yesno inline"><input type="checkbox" name="pharmacy" value="Pharmacy" <?= $editing && $row['pharmacy']=='Pharmacy'?'checked':'' ?>> Pharmacy</div>
            <div class="yesno inline"><input type="checkbox" name="ambulance" value="Ambulance" <?= $editing && $row['ambulance']=='Ambulance'?'checked':'' ?>> Ambulance</div>
            <div class="yesno inline"><input type="checkbox" name="wheelchair_accessible" value="Wheelchair Accessible" <?= $editing && $row['wheelchair_accessible']=='Wheelchair Accessible'?'checked':'' ?>> Wheelchair Accessible</div>
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

    <!-- Gallery remove buttons OUTSIDE the main form, right next to images -->
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

    <h2>All Hospitals</h2>
    <input type="text" id="searchBar" placeholder="Search by Hospital Name..." onkeyup="filterTable()">

    <table id="hospitalTable">
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
                    <a class="btn" href="?edit=<?= $r['id'] ?>">Edit</a>
                    <a class="btn btn-danger" href="?del=<?= $r['id'] ?>" onclick="return confirm('Delete this record?')">Delete</a>
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

function filterTable() {
    var input = document.getElementById("searchBar").value.toUpperCase();
    var table = document.getElementById("hospitalTable");
    var tr = table.getElementsByTagName("tr");
    for (var i = 1; i < tr.length; i++) {
        var td = tr[i].getElementsByTagName("td")[1];
        if (td) {
            var txt = td.textContent || td.innerText;
            tr[i].style.display = txt.toUpperCase().indexOf(input) > -1 ? "" : "none";
        }
    }
}
</script>

</body>
</html>