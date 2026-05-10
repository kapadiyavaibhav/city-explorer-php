<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$host = 'localhost';
$dbname = 'project';
$user = 'root';
$pass = '';
$conn = mysqli_connect($host, $user, $pass, $dbname);
if (!$conn) { die('Connection failed: ' . mysqli_connect_error()); }

function arr2str($arr) { return is_array($arr) ? implode(',', $arr) : $arr; }

$msg = '';

if (isset($_POST['save'])) {
    $fields = [
        'name','img','address','city_name','cat_name','contact','email',
        'opening_hours','emergency','visiting_hours','departments','services','doctors',
        'beds_general','beds_icu','beds_private',
        'laboratory','diagnostics','pharmacy','ambulance','wheelchair_accessible',
        'insurance','history','gallery','emergency_procedures','user_email'
    ];
    $data = [];

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
                    } else {
                        $data[$f] = '';
                    }
                } else {
                    $data[$f] = '';
                }
            } else {
                $data[$f] = '';
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
            $galleryImages = $newGalleryImages;
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

    $fieldstr = implode(',', $fields);
    $values = implode("','", array_map(fn($v)=>mysqli_real_escape_string($conn,$v), array_values($data)));
    $sql = "INSERT INTO t_hospital ($fieldstr) VALUES ('$values')";
    mysqli_query($conn, $sql);
    $msg = "Record added successfully.";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Your Hospital</title>
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
        .btn { padding: 5px 15px; border-radius: 5px; border: 0; background: #2680c2; color: #fff; cursor: pointer; }
        .btn-add { background: #3bbf72; }
        .small { font-size: 0.95em; color: #888; }
        .inline { display: inline-block; margin-right: 10px; }
        #header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #eaf2fb;
            padding: 12px 35px;
            border-radius: 10px;
            margin-bottom: 34px;
            box-shadow: 0 2px 8px #dde7f4;
        }
        #user-email {
            font-size: 1.1em;
            color: #2365ab;
            font-weight: bold;
        }
        #logout-btn {
            background: #d03030;
            color: #fff;
            padding: 6px 18px;
            border-radius: 7px;
            border: none;
            cursor: pointer;
            font-size: 1em;
            font-weight: bold;
            text-decoration: none;
        }
    </style>
</head>
<body>
<div class="container">
    <div id="header-bar">
        <span id="user-email"><?= htmlspecialchars($_SESSION['email']) ?></span>
        <div class="navigation">
        <a class="btn" href="../index.php">Home</a>
        <a class="btn btn-danger" href="logout.php">Logout</a>
        </div>
    </div>

    <h2>Add Your Hospital</h2>

    <form method="post" enctype="multipart/form-data" id="hospitalForm">
        <input type="hidden" name="id" value="">
        <label>Hospital Name</label>
        <input type="text" name="name" required value="">

        <label>Logo/Image File</label>
        <input type="file" name="img" accept="image/*">

        <label>Address</label>
        <input type="text" name="address" value="">

        <label>Contact Number</label>
        <input type="text" name="contact" value="">

        <label>Email Address</label>
        <input type="email" name="email" value="">

        <label>Opening Hours</label>
        <input type="text" name="opening_hours" value="">

        <label>Emergency Department Availability</label>
        <input type="text" name="emergency" value="">

        <label>Visiting Hours</label>
        <input type="text" name="visiting_hours" value="">

        <div class="form-section">
            <label>Departments & Specialties <span class="small">(comma-separated)</span></label>
            <input type="text" name="departments[]" value="" placeholder="Cardiology,Orthopedics,Pediatrics">
        </div>

        <div class="form-section">
            <label>Services Provided <span class="small">(comma-separated)</span></label>
            <input type="text" name="services[]" value="" placeholder="Outpatient,Surgery,Wellness">
        </div>

        <div class="form-section">
            <label>Doctors</label>
            <div id="doctors">
                <div class="doc-block">
                    <input type="text" name="doc_name[]" placeholder="Name">
                    <input type="text" name="doc_spec[]" placeholder="Specialization">
                    <input type="text" name="doc_qual[]" placeholder="Qualifications">
                </div>
                <div id="doctor-controls">
                    <button type="button" onclick="addDoc()">Add Doctor</button>
                    <button type="button" onclick="removeDoc()">Remove Doctor</button>
                </div>
            </div>
        </div>

        <div class="form-section">
            <label>Beds & Room Types</label>
            <input type="number" name="beds_general" min="0" placeholder="General Beds" value=""> General
            <input type="number" name="beds_icu" min="0" placeholder="ICU Beds" value=""> ICU
            <input type="number" name="beds_private" min="0" placeholder="Private Beds" value=""> Private
        </div>

        <div class="form-section">
            <label>Facilities & Amenities</label>
            <div class="yesno inline"><input type="checkbox" name="laboratory" value="Laboratory"> Laboratory</div>
            <div class="yesno inline"><input type="checkbox" name="diagnostics" value="Diagnostics"> Diagnostics</div>
            <div class="yesno inline"><input type="checkbox" name="pharmacy" value="Pharmacy"> Pharmacy</div>
            <div class="yesno inline"><input type="checkbox" name="ambulance" value="Ambulance"> Ambulance</div>
            <div class="yesno inline"><input type="checkbox" name="wheelchair_accessible" value="Wheelchair Accessible"> Wheelchair Accessible</div>
        </div>

        <div class="form-section">
            <label>Insurance Accepted</label>
            <label><input type="radio" name="insurance" value="Yes"> Yes</label>
            <label><input type="radio" name="insurance" value="No"> No</label>
        </div>

        <div class="form-section">
            <label>Hospital History</label>
            <textarea name="history" rows="2"></textarea>
        </div>

        <div class="form-section">
            <label>Photo Gallery <span class="small">(You can upload multiple images)</span></label>
            <input type="file" name="gallery[]" accept="image/*" multiple>
        </div>

        <div class="form-section">
            <label>Emergency Procedures</label>
            <textarea name="emergency_procedures" rows="2"></textarea>
        </div>

        <label>City Name</label>
        <input type="text" name="city_name" value="">

        <input type="hidden" name="cat_name" value="Hospital">
        <input type="hidden" name="user_email" value="<?= htmlspecialchars($_SESSION['email']) ?>">

        <div class="actions">
            <button class="btn btn-add" type="submit" name="save">Add</button>
            <?php if (!empty($msg)): ?>
                <div class="msg-success"><?= $msg ?></div>
            <?php endif; ?>
        </div>
    </form>
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