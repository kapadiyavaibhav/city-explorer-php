<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$host = 'localhost'; $dbname = 'project'; $user = 'root'; $pass = '';
$conn = mysqli_connect($host, $user, $pass, $dbname);
if (!$conn) { die('Connection failed: ' . mysqli_connect_error()); }

function arr2str($arr) { return is_array($arr)?implode(',',$arr):$arr; }

$msg = '';

if (isset($_POST['save'])) {
    $fields = [
        'name','img','address','city_name','cat_name','contact','email','website',
        'established','grades',
        'departments','facilities','faculty','admission_process',
        'gallery','scholarships',
        'hostel','transport','library','sports','cafeteria','disabled_friendly','user_email'
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
        elseif (in_array($f, ['departments','facilities','scholarships']))
            $data[$f] = arr2str($_POST[$f] ?? []);
        elseif ($f === 'faculty')
            $data[$f] = arr2str(array_map(function($n,$d,$e){return "$n|$d|$e";},
                $_POST['faculty_name']??[],$_POST['faculty_dept']??[],$_POST['faculty_exp']??[]));
        elseif (in_array($f, ['hostel','transport','library','sports','cafeteria','disabled_friendly']))
            $data[$f] = isset($_POST[$f]) ? $_POST[$f] : '';
        else
            $data[$f] = mysqli_real_escape_string($conn, $_POST[$f]??'');
    }
    $fieldstr = implode(',', $fields);
    $values = implode("','", array_map(fn($v)=>mysqli_real_escape_string($conn,$v), array_values($data)));
    $sql = "INSERT INTO t_schools ($fieldstr) VALUES ('$values')";
    mysqli_query($conn, $sql);
    $msg = "Record added successfully.";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add School</title>
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
        </div>    </div>
    <h2>Add School</h2>
    <form method="post" enctype="multipart/form-data" id="schoolForm">
        <input type="hidden" name="id" value="">
        <input type="hidden" name="user_email" value="<?= htmlspecialchars($_SESSION['email']) ?>">
        <label>School Name</label>
        <input type="text" name="name" required value="">
        <label>Logo/Image File</label>
        <input type="file" name="img" accept="image/*">
        <label>Address</label>
        <input type="text" name="address" value="">
        <input type="hidden" name="cat_name" value="School">
        <label>Contact Number</label>
        <input type="text" name="contact" value="">
        <label>Email Address</label>
        <input type="email" name="email" value="">
        <label>Website</label>
        <input type="text" name="website" value="">
        <label>Established Year</label>
        <input type="text" name="established" value="">
        <label>Grades (e.g. Nursery-12)</label>
        <input type="text" name="grades" value="">

        <div class="form-section">
            <label>Departments <span class="small">(comma-separated)</span></label>
            <input type="text" name="departments[]" value="" placeholder="Mathematics,Physics,English">
        </div>
        <div class="form-section">
            <label>Facilities <span class="small">(comma-separated)</span></label>
            <input type="text" name="facilities[]" value="" placeholder="Auditorium,Medical,Playground">
        </div>
        <div class="form-section">
            <label>Faculty</label>
            <div id="faculty">
                <div class="faculty-block">
                    <input type="text" name="faculty_name[]" placeholder="Name">
                    <input type="text" name="faculty_dept[]" placeholder="Department">
                    <input type="text" name="faculty_exp[]" placeholder="Experience (years)">
                </div>
                <div id="faculty-controls">
                    <button type="button" onclick="addFaculty()">Add Faculty</button>
                    <button type="button" onclick="removeFaculty()">Remove Faculty</button>
                </div>
            </div>
        </div>
        <div class="form-section">
            <label>Admission Process</label>
            <textarea name="admission_process" rows="2"></textarea>
        </div>
        <div class="form-section">
            <label>Scholarships (comma-separated)</label>
            <input type="text" name="scholarships[]" value="" placeholder="Merit-based,SC/ST,State">
        </div>
        <div class="form-section">
            <label>Amenities & Features</label>
            <div class="yesno inline"><input type="checkbox" name="hostel" value="Hostel"> Hostel</div>
            <div class="yesno inline"><input type="checkbox" name="transport" value="Transport"> Transport</div>
            <div class="yesno inline"><input type="checkbox" name="library" value="Library"> Library</div>
            <div class="yesno inline"><input type="checkbox" name="sports" value="Sports"> Sports</div>
            <div class="yesno inline"><input type="checkbox" name="cafeteria" value="Cafeteria"> Cafeteria</div>
            <div class="yesno inline"><input type="checkbox" name="disabled_friendly" value="Disabled Friendly"> Disabled Friendly</div>
        </div>
        <div class="form-section">
            <label>Photo Gallery <span class="small">(You can upload multiple images)</span></label>
            <input type="file" name="gallery[]" accept="image/*" multiple>
        </div>
        <label>City Name</label>
        <input type="text" name="city_name" value="">
        <div class="actions">
            <button class="btn btn-add" type="submit" name="save">Add</button>
            <?php if (!empty($msg)): ?>
              <div class="msg-success"><?= $msg ?></div>
            <?php endif; ?>
        </div>
    </form>
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
</script>
</body>
</html>