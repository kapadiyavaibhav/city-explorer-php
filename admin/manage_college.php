<?php
require_once 'includes/admin_header.php';
$host = 'localhost'; $dbname = 'project'; $user = 'root'; $pass = '';
$conn = mysqli_connect($host, $user, $pass, $dbname);
if (!$conn) { die('Connection failed: ' . mysqli_connect_error()); }
function arr2str($arr) { return is_array($arr) ? implode(',', $arr) : $arr; }
function str2arr($str) { return $str ? explode(',', $str) : []; }
$editing = false;
$edit_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
$msg = '';

if (isset($_POST['remove_gallery_img']) && isset($_POST['id'])) {
    $id = intval($_POST['id']);
    $remove_img = $_POST['remove_gallery_img'];
    $res = mysqli_query($conn, "SELECT gallery FROM colleges WHERE id=$id");
    $row = mysqli_fetch_assoc($res);
    $gallery = str2arr($row['gallery']);
    $gallery = array_filter($gallery, fn($img) => $img !== $remove_img);
    $gallery_str = arr2str($gallery);
    mysqli_query($conn, "UPDATE colleges SET gallery='$gallery_str' WHERE id=$id");
    $filePath = __DIR__ . '../images/' . $remove_img;
    if (file_exists($filePath)) unlink($filePath);
    $msg = "Gallery image removed successfully.";
    $edit_id = $id;
}

if (isset($_POST['save'])) {
    $id = intval($_POST['id'] ?? 0);
    $fields = [
        'name','img','address','city_name','cat_name','contact','email','website',
        'established',
        'courses','departments','facilities','faculty','admission_process',
        'gallery','scholarships',
        'hostel','transport','library','sports','cafeteria','disabled_friendly'
    ];
    $data = [];
    $old_img = '';
    $old_gallery = [];

    if ($id) {
        $fetch = mysqli_query($conn, "SELECT img,gallery FROM colleges WHERE id=$id");
        $old_data = mysqli_fetch_assoc($fetch);
        $old_img = $old_data['img'] ?? '';
        $old_gallery = str2arr($old_data['gallery'] ?? '');
    }
    foreach ($fields as $f) {
        if ($f === 'img') {
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
            if ($id) {
                $galleryImages = array_merge($old_gallery, $newGalleryImages);
            } else {
                $galleryImages = $newGalleryImages;
            }
            $data[$f] = arr2str($galleryImages);
        }
        elseif (in_array($f, ['courses','departments','facilities','scholarships']))
            $data[$f] = arr2str($_POST[$f] ?? []);
        elseif ($f === 'faculty')
            $data[$f] = arr2str(array_map(function($n,$d,$e){return "$n|$d|$e";},
                $_POST['faculty_name']??[],$_POST['faculty_dept']??[],$_POST['faculty_exp']??[]));
        elseif (in_array($f, ['hostel','transport','library','sports','cafeteria','disabled_friendly']))
            $data[$f] = isset($_POST[$f]) ? $_POST[$f] : '';
        else
            $data[$f] = mysqli_real_escape_string($conn, $_POST[$f]??'');
    }
    if ($id) {
        $sets = [];
        foreach ($fields as $f) $sets[] = "$f='{$data[$f]}'";
        $sql = "UPDATE colleges SET ".implode(',',$sets)." WHERE id=$id";
        mysqli_query($conn, $sql);
        $msg = "Record edited successfully.";
        $edit_id = 0;
        $editing = false;
        unset($row);
    } else {
        $fieldstr = implode(',', $fields);
        $values = implode("','", array_map(fn($v)=>mysqli_real_escape_string($conn,$v), array_values($data)));
        $sql = "INSERT INTO colleges ($fieldstr) VALUES ('$values')";
        mysqli_query($conn, $sql);
        $msg = "Record added successfully.";
    }
    header('Location: '.$_SERVER['PHP_SELF']);
    exit;
}

if (isset($_GET['del'])) {
    $id = intval($_GET['del']);
    $res = mysqli_query($conn, "SELECT img,gallery FROM colleges WHERE id=$id");
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
    mysqli_query($conn, "DELETE FROM colleges WHERE id=$id");
    $msg = "Record deleted successfully.";
    header('Location: '.$_SERVER['PHP_SELF']);
    exit;
}

if ($edit_id) {
    $res = mysqli_query($conn, "SELECT * FROM colleges WHERE id=$edit_id");
    $row = mysqli_fetch_assoc($res);
    if ($row) $editing = true;
}

$res = mysqli_query($conn, "SELECT * FROM colleges ORDER BY id DESC");
$rows = [];
while($r = mysqli_fetch_assoc($res)) $rows[] = $r;
?>
<!DOCTYPE html>
<html>
<head>
    <title>College Admin</title>
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
    <h2><?= $editing ? "Edit College" : "Add College" ?></h2>
    <form method="post" enctype="multipart/form-data" id="collegeForm">
        <input type="hidden" name="id" value="<?= $editing ? $row['id'] : '' ?>">
        <label>College Name</label>
        <input type="text" name="name" required value="<?= htmlspecialchars($editing?$row['name']:'') ?>">

<input type="hidden" name="user_email" value="">

        <label>Logo/Image File</label>
        <input type="file" name="img" accept="image/*">
        <?php if ($editing && !empty($row['img'])): ?>
            <div class="logo-img-block">
                <img src="../images/<?= htmlspecialchars($row['img']) ?>" alt="Logo" style="max-height:80px;">
            </div>
        <?php endif; ?>
        <label>Address</label>
        <input type="text" name="address" value="<?= htmlspecialchars($editing?$row['address']:'') ?>">
        <input type="hidden" name="cat_name" value="College">
        <label>Contact Number</label>
        <input type="text" name="contact" value="<?= htmlspecialchars($editing?$row['contact']:'') ?>">
        <label>Email Address</label>
        <input type="email" name="email" value="<?= htmlspecialchars($editing?$row['email']:'') ?>">
        <label>Website</label>
        <input type="text" name="website" value="<?= htmlspecialchars($editing?$row['website']:'') ?>">
        <label>Established Year</label>
        <input type="text" name="established" value="<?= htmlspecialchars($editing?$row['established']:'') ?>">
        <div class="form-section">
            <label>Courses Offered <span class="small">(comma-separated)</span></label>
            <input type="text" name="courses[]" value="<?= htmlspecialchars($editing?str_replace(',','',$row['courses']??''):'') ?>" placeholder="B.Tech,MBA,B.Sc">
        </div>
        <div class="form-section">
            <label>Departments <span class="small">(comma-separated)</span></label>
            <input type="text" name="departments[]" value="<?= htmlspecialchars($editing?str_replace(',','',$row['departments']??''):'') ?>" placeholder="Computer Science,Mechanical,Physics">
        </div>
        <div class="form-section">
            <label>Facilities <span class="small">(comma-separated)</span></label>
            <input type="text" name="facilities[]" value="<?= htmlspecialchars($editing?str_replace(',','',$row['facilities']??''):'') ?>" placeholder="Auditorium,Medical,ATM">
        </div>
        <div class="form-section">
            <label>Faculty</label>
            <div id="faculty">
                <?php
                $faculty = $editing ? str2arr($row['faculty']) : ['||'];
                foreach ($faculty as $i=>$fac) {
                    list($n,$d,$e) = array_pad(explode('|',$fac),3,'');
                ?>
                    <div class="faculty-block">
                        <input type="text" name="faculty_name[]" placeholder="Name" value="<?= htmlspecialchars($n) ?>">
                        <input type="text" name="faculty_dept[]" placeholder="Department" value="<?= htmlspecialchars($d) ?>">
                        <input type="text" name="faculty_exp[]" placeholder="Experience (years)" value="<?= htmlspecialchars($e) ?>">
                    </div>
                <?php } ?>
                <div id="faculty-controls">
                    <button type="button" onclick="addFaculty()">Add Faculty</button>
                    <button type="button" onclick="removeFaculty()">Remove Faculty</button>
                </div>
            </div>
        </div>
        <div class="form-section">
            <label>Admission Process</label>
            <textarea name="admission_process" rows="2"><?= htmlspecialchars($editing?$row['admission_process']:'') ?></textarea>
        </div>
        <div class="form-section">
            <label>Scholarships (comma-separated)</label>
            <input type="text" name="scholarships[]" value="<?= htmlspecialchars($editing?str_replace(',','&#10;',$row['scholarships']??''):'') ?>" placeholder="Merit-based,SC/ST,State">
        </div>
        <div class="form-section">
            <label>Amenities & Features</label>
            <div class="yesno inline"><input type="checkbox" name="hostel" value="Hostel" <?= $editing && $row['hostel']=='Hostel'?'checked':'' ?>> Hostel</div>
            <div class="yesno inline"><input type="checkbox" name="transport" value="Transport" <?= $editing && $row['transport']=='Transport'?'checked':'' ?>> Transport</div>
            <div class="yesno inline"><input type="checkbox" name="library" value="Library" <?= $editing && $row['library']=='Library'?'checked':'' ?>> Library</div>
            <div class="yesno inline"><input type="checkbox" name="sports" value="Sports" <?= $editing && $row['sports']=='Sports'?'checked':'' ?>> Sports</div>
            <div class="yesno inline"><input type="checkbox" name="cafeteria" value="Cafeteria" <?= $editing && $row['cafeteria']=='Cafeteria'?'checked':'' ?>> Cafeteria</div>
            <div class="yesno inline"><input type="checkbox" name="disabled_friendly" value="Disabled Friendly" <?= $editing && $row['disabled_friendly']=='Disabled Friendly'?'checked':'' ?>> Disabled Friendly</div>
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
                        <img class="gallery-img" src="../images/<?= htmlspecialchars($img) ?>" alt="Gallery">
                    </span>
                    <?php
                    endif;
                endforeach;
            endif; ?>
            </div>
        </div>
        <label>City Name</label>
        <input type="text" name="city_name" value="<?= htmlspecialchars($editing?$row['city_name']:'') ?>">
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
    <h2>All Colleges</h2>
    <input type="text" id="searchBar" placeholder="Search by College Name..." onkeyup="filterTable()">
    <table id="collegeTable">
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
                    <a class="btn" href="?edit=<?= $r['id'] ?>">Edit</a>
                    <a class="btn btn-danger" href="?del=<?= $r['id'] ?>" onclick="return confirm('Delete this record?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>
<script>
function addFaculty() {
    var d = document.createElement('div');
    d.className = 'faculty-block';
    d.innerHTML = `
        <input type="text" name="faculty_name[]" placeholder="Name">
        <input type="text" name="faculty_dept[]" placeholder="Department">
        <input type="text" name="faculty_exp[]" placeholder="Experience (years)">
    `;
    document.getElementById('faculty').insertBefore(d, document.getElementById('faculty-controls'));
}
function removeFaculty() {
    var facultyBlocks = document.querySelectorAll('#faculty .faculty-block');
    if (facultyBlocks.length > 1) {
        facultyBlocks[facultyBlocks.length - 1].remove();
    } else {
        alert('At least one faculty record is required!');
    }
}
function filterTable() {
    var input = document.getElementById("searchBar").value.toUpperCase();
    var table = document.getElementById("collegeTable");
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