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
        'opening_hours','closed_on','reservation_required','special_offers',
        'cuisines','menu','services','chefs',
        'wifi','parking','outdoor_seating','live_music','bar','kids_friendly','pet_friendly','wheelchair_accessible',
        'payment_options','description','gallery','user_email'
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
    $fieldstr = implode(',', $fields);
    $values = implode("','", array_map(fn($v)=>mysqli_real_escape_string($conn,$v), array_values($data)));
    $sql = "INSERT INTO t_restaurants ($fieldstr) VALUES ('$values')";
    mysqli_query($conn, $sql);
    $msg = "Record added successfully.";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Restaurant</title>
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
        .btn { padding: 5px 15px; border-radius: 5px; border: 0; background: #e67d22; color: #fff; cursor: pointer; }
        .btn-add { background: #3bbf72; }
        .small { font-size: 0.95em; color: #888; }
        .inline { display: inline-block; margin-right: 10px; }
        #header-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fbeeea;
            padding: 12px 35px;
            border-radius: 10px;
            margin-bottom: 34px;
            box-shadow: 0 2px 8px #eeded1;
        }
        #user-email {
            font-size: 1.1em;
            color: #ab5123;
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
    <h2>Add Restaurant</h2>
    <form method="post" enctype="multipart/form-data" id="restaurantForm">
        <input type="hidden" name="id" value="">

        <input type="hidden" name="user_email" value="<?= htmlspecialchars($_SESSION['email']) ?>">

        <label>Restaurant Name</label>
        <input type="text" name="name" required value="">

        <label>Logo/Image File</label>
        <input type="file" name="img" accept="image/*">

        <label>Address</label>
        <input type="text" name="address" value="">

        <input type="hidden" name="cat_name" value="Restaurant">

        <label>Contact Number</label>
        <input type="text" name="contact" value="">

        <label>Email Address</label>
        <input type="email" name="email" value="">

        <label>Website</label>
        <input type="text" name="website" value="">

        <label>Opening Hours</label>
        <input type="text" name="opening_hours" value="">

        <label>Closed On</label>
        <input type="text" name="closed_on" value="">

        <label>Reservation Required (Yes/No)</label>
        <input type="text" name="reservation_required" value="">

        <label>Special Offers</label>
        <input type="text" name="special_offers" value="">

        <div class="form-section">
            <label>Cuisines <span class="small">(comma-separated)</span></label>
            <input type="text" name="cuisines[]" value="" placeholder="Indian,Chinese,Italian">
        </div>

        <div class="form-section">
            <label>Menu Highlights <span class="small">(comma-separated)</span></label>
            <input type="text" name="menu[]" value="" placeholder="Paneer Makhani,Manchurian,Pasta">
        </div>

        <div class="form-section">
            <label>Services <span class="small">(comma-separated)</span></label>
            <input type="text" name="services[]" value="" placeholder="Dine-in,Takeaway,Delivery">
        </div>

        <div class="form-section">
            <label>Chefs</label>
            <div id="chefs">
                <div class="chef-block">
                    <input type="text" name="chef_name[]" placeholder="Name">
                    <input type="text" name="chef_spec[]" placeholder="Specialization">
                    <input type="text" name="chef_exp[]" placeholder="Experience (years)">
                </div>
                <div id="chef-controls">
                    <button type="button" onclick="addChef()">Add Chef</button>
                    <button type="button" onclick="removeChef()">Remove Chef</button>
                </div>
            </div>
        </div>

        <div class="form-section">
            <label>Amenities & Features</label>
            <div class="yesno inline"><input type="checkbox" name="wifi" value="Wi-Fi"> Free Wi-Fi</div>
            <div class="yesno inline"><input type="checkbox" name="parking" value="Parking"> Parking Available</div>
            <div class="yesno inline"><input type="checkbox" name="outdoor_seating" value="Outdoor Seating"> Outdoor Seating</div>
            <div class="yesno inline"><input type="checkbox" name="live_music" value="Live Music"> Live Music</div>
            <div class="yesno inline"><input type="checkbox" name="bar" value="Bar"> Bar</div>
            <div class="yesno inline"><input type="checkbox" name="kids_friendly" value="Kids Friendly"> Kids Friendly</div>
            <div class="yesno inline"><input type="checkbox" name="pet_friendly" value="Pet Friendly"> Pet Friendly</div>
            <div class="yesno inline"><input type="checkbox" name="wheelchair_accessible" value="Wheelchair Accessible"> Wheelchair Accessible</div>
        </div>

        <div class="form-section">
            <label>Payment Options <span class="small">(comma-separated)</span></label>
            <input type="text" name="payment_options" value="" placeholder="Cash,Credit Card,UPI">
        </div>

        <div class="form-section">
            <label>Description</label>
            <textarea name="description" rows="2"></textarea>
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
function addChef() {
    var d = document.createElement('div');
    d.className = 'chef-block';
    d.innerHTML = `
        <input type="text" name="chef_name[]" placeholder="Name">
        <input type="text" name="chef_spec[]" placeholder="Specialization">
        <input type="text" name="chef_exp[]" placeholder="Experience (years)">
    `;
    document.getElementById('chefs').insertBefore(d, document.getElementById('chef-controls'));
}

function removeChef() {
    var chefBlocks = document.querySelectorAll('#chefs .chef-block');
    if (chefBlocks.length > 1) {
        chefBlocks[chefBlocks.length - 1].remove();
    } else {
        alert('At least one chef record is required!');
    }
}
</script>

</body>
</html>