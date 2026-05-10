
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>City Explorer</title>
    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
        }
        header {
            background: #080808ff;
            color: white;
            padding: 15px;
            text-align: center;
            font-size: 24px;
            font-weight: bold;
        }
        footer {
            background: #030303ff;
            color: white;
            text-align: center;
            padding: 10px;
            position: fixed;
            bottom: 0;
            width: 100%;
            font-size: 14px;
        }
        main {
            padding: 20px;
            margin-bottom: 60px; /* space for footer */
        }
    </style>
</head>
<body>
<header>
    
                <span class="icon">&#x1F9ED;</span>City Explorer
    
</header>
<main>

<?php
include "db_connect.php";
session_start();
if (!isset($_SESSION['user_id'])) { header("Location: login.php"); exit(); }
$user_email = $_SESSION['email'];

// Utility functions (declare ONCE)
function arr2str($arr) { return is_array($arr) ? implode(',', $arr) : $arr; }
function str2arr($str) { return $str ? explode(',', $str) : []; }
function val($row, $key, $default = '') { return isset($row[$key]) ? $row[$key] : $default; }

// Table config
$table_map = [
    'hospitals'   => 'Hospital',
    'restaurants' => 'Restaurant',
    'pgs'         => 'PG',
    'h_p'         => 'HistoricalPlace',
    'schools'     => 'School',
    'colleges'    => 'College'
];

// Collect all user records
$all_records = [];
foreach ($table_map as $table => $cat_name) {
    $sql = "SELECT *, '$table' AS table_src FROM $table WHERE user_email = '".mysqli_real_escape_string($conn, $user_email)."'";
    $res = mysqli_query($conn, $sql);
    while ($row = mysqli_fetch_assoc($res)) {
        $row['table_src'] = $table;
        $row['cat_name'] = $cat_name;
        $all_records[] = $row;
    }
}

// Find which record to edit
$edit_id = isset($_GET['edit']) ? intval($_GET['edit']) : 0;
$edit_table = isset($_GET['table']) ? $_GET['table'] : '';
$editing = false;
$edit_row = [];
if ($edit_id && $edit_table && isset($table_map[$edit_table])) {
    $res = mysqli_query($conn, "SELECT * FROM $edit_table WHERE id=$edit_id AND user_email='" . mysqli_real_escape_string($conn, $user_email) . "'");
    if ($res && mysqli_num_rows($res) > 0) {
        $edit_row = mysqli_fetch_assoc($res);
        $editing = true;
    }
}

// Handle delete
if (isset($_GET['del']) && isset($_GET['del_table']) && isset($table_map[$_GET['del_table']])) {
    $del_id = intval($_GET['del']);
    $del_table = $_GET['del_table'];
    $img_q = mysqli_query($conn, "SELECT img, gallery FROM $del_table WHERE id=$del_id");
    $img_r = mysqli_fetch_assoc($img_q);
    if ($img_r) {
        if (!empty($img_r['img'])) { $img_path = __DIR__ . '/../images/' . $img_r['img']; if (file_exists($img_path)) unlink($img_path); }
        if (!empty($img_r['gallery'])) { foreach (str2arr($img_r['gallery']) as $gimg) { $gimg_path = __DIR__ . '/../images/' . $gimg; if (file_exists($gimg_path)) unlink($gimg_path); } }
    }
    mysqli_query($conn, "DELETE FROM $del_table WHERE id=$del_id AND user_email='" . mysqli_real_escape_string($conn, $user_email) . "'");
    header("Location: profile.php");
    exit();
}
?>
<?php
// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save'])) {
    if ($_POST['form_type'] === 'hospital') {
        // ================= HOSPITAL ADD/UPDATE =================
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        $contact = mysqli_real_escape_string($conn, $_POST['contact']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $opening_hours = mysqli_real_escape_string($conn, $_POST['opening_hours']);
        $emergency = mysqli_real_escape_string($conn, $_POST['emergency']);
        $visiting_hours = mysqli_real_escape_string($conn, $_POST['visiting_hours']);
        $departments = isset($_POST['departments']) ? arr2str($_POST['departments']) : '';
        $services = isset($_POST['services']) ? arr2str($_POST['services']) : '';
        $history = mysqli_real_escape_string($conn, $_POST['history']);
        $emergency_procedures = mysqli_real_escape_string($conn, $_POST['emergency_procedures']);
        $city_name = mysqli_real_escape_string($conn, $_POST['city_name']);
        $cat_name = 'Hospital';
        $insurance = $_POST['insurance'] ?? '';
        $beds_general = intval($_POST['beds_general'] ?? 0);
        $beds_icu = intval($_POST['beds_icu'] ?? 0);
        $beds_private = intval($_POST['beds_private'] ?? 0);
        $laboratory = $_POST['laboratory'] ?? '';
        $diagnostics = $_POST['diagnostics'] ?? '';
        $pharmacy = $_POST['pharmacy'] ?? '';
        $ambulance = $_POST['ambulance'] ?? '';
        $wheelchair_accessible = $_POST['wheelchair_accessible'] ?? '';

        // Doctors
        $doctors_arr = [];
        if (isset($_POST['doc_name'])) {
            foreach ($_POST['doc_name'] as $i => $n) {
                $spec = $_POST['doc_spec'][$i] ?? '';
                $qual = $_POST['doc_qual'][$i] ?? '';
                $doctors_arr[] = "$n|$spec|$qual";
            }
        }
        $doctors = arr2str($doctors_arr);

        // Image upload
        $img = '';
        if (!empty($_FILES['img']['name'])) {
            $img = time().'_'.basename($_FILES['img']['name']);
            move_uploaded_file($_FILES['img']['tmp_name'], '../images/'.$img);
        } elseif ($id && !empty($edit_row['img'])) {
            $img = $edit_row['img'];
        }

        // Gallery upload
        $gallery_imgs = [];
        if (!empty($_FILES['gallery']['name'][0])) {
            foreach ($_FILES['gallery']['tmp_name'] as $k => $tmp_name) {
                if ($_FILES['gallery']['name'][$k]) {
                    $gimg = time().'_'.$k.'_'.basename($_FILES['gallery']['name'][$k]);
                    move_uploaded_file($tmp_name, '../images/'.$gimg);
                    $gallery_imgs[] = $gimg;
                }
            }
        } elseif ($id && !empty($edit_row['gallery'])) {
            $gallery_imgs = str2arr($edit_row['gallery']);
        }
        $gallery = arr2str($gallery_imgs);

        if ($id) {
            $sql = "UPDATE hospitals SET name='$name', address='$address', contact='$contact', email='$email',
                opening_hours='$opening_hours', emergency='$emergency', visiting_hours='$visiting_hours',
                departments='$departments', services='$services', doctors='$doctors',
                beds_general='$beds_general', beds_icu='$beds_icu', beds_private='$beds_private',
                laboratory='$laboratory', diagnostics='$diagnostics', pharmacy='$pharmacy', ambulance='$ambulance',
                wheelchair_accessible='$wheelchair_accessible', insurance='$insurance',
                history='$history', emergency_procedures='$emergency_procedures',
                city_name='$city_name', cat_name='$cat_name', user_email='$user_email',
                img='$img', gallery='$gallery'
                WHERE id=$id AND user_email='$user_email'";
            mysqli_query($conn, $sql);
        } else {
            $sql = "INSERT INTO hospitals (name,address,contact,email,opening_hours,emergency,visiting_hours,
                departments,services,doctors,beds_general,beds_icu,beds_private,laboratory,diagnostics,
                pharmacy,ambulance,wheelchair_accessible,insurance,history,emergency_procedures,
                city_name,cat_name,user_email,img,gallery)
                VALUES ('$name','$address','$contact','$email','$opening_hours','$emergency',
                '$visiting_hours','$departments','$services','$doctors','$beds_general','$beds_icu',
                '$beds_private','$laboratory','$diagnostics','$pharmacy','$ambulance',
                '$wheelchair_accessible','$insurance','$history','$emergency_procedures',
                '$city_name','$cat_name','$user_email','$img','$gallery')";
            mysqli_query($conn, $sql);
        }
        header("Location: profile.php");
        exit();

    } elseif ($_POST['form_type'] === 'restaurant') {
        // ================= RESTAURANT UPDATE =================
        $id = intval($_POST['id']);
        $name = mysqli_real_escape_string($conn, $_POST['name']);
        $address = mysqli_real_escape_string($conn, $_POST['address']);
        $contact = mysqli_real_escape_string($conn, $_POST['contact']);
        $email = mysqli_real_escape_string($conn, $_POST['email']);
        $website = mysqli_real_escape_string($conn, $_POST['website']);
        $opening_hours = mysqli_real_escape_string($conn, $_POST['opening_hours']);
        $closed_on = mysqli_real_escape_string($conn, $_POST['closed_on']);
        $reservation_required = mysqli_real_escape_string($conn, $_POST['reservation_required']);
        $special_offers = mysqli_real_escape_string($conn, $_POST['special_offers']);
        $description = mysqli_real_escape_string($conn, $_POST['description']);
        $city_name = mysqli_real_escape_string($conn, $_POST['city_name']);
        $payment_options = mysqli_real_escape_string($conn, $_POST['payment_options']);
        $cat_name = 'Restaurant';

        // Features
        $wifi = $_POST['wifi'] ?? '';
        $parking = $_POST['parking'] ?? '';
        $outdoor_seating = $_POST['outdoor_seating'] ?? '';
        $live_music = $_POST['live_music'] ?? '';
        $bar = $_POST['bar'] ?? '';
        $kids_friendly = $_POST['kids_friendly'] ?? '';
        $pet_friendly = $_POST['pet_friendly'] ?? '';
        $wheelchair_accessible = $_POST['wheelchair_accessible'] ?? '';

        $cuisines = arr2str($_POST['cuisines'] ?? []);
        $menu = arr2str($_POST['menu'] ?? []);
        $services = arr2str($_POST['services'] ?? []);

        // Chefs
        $chefs_arr = [];
        if (isset($_POST['chef_name'])) {
            foreach ($_POST['chef_name'] as $i=>$n) {
                $spec = $_POST['chef_spec'][$i] ?? '';
                $exp = $_POST['chef_exp'][$i] ?? '';
                $chefs_arr[] = "$n|$spec|$exp";
            }
        }
        $chefs = arr2str($chefs_arr);

        // Get existing images
        $res = mysqli_query($conn, "SELECT img, gallery FROM restaurants WHERE id=$id AND user_email='" . mysqli_real_escape_string($conn, $user_email) . "'");
        $edit_row = mysqli_fetch_assoc($res);

        // Main image
        $img = $edit_row['img'] ?? '';
        if (!empty($_FILES['img']['name'])) {
            $img = time().'_'.basename($_FILES['img']['name']);
            move_uploaded_file($_FILES['img']['tmp_name'], __DIR__.'../../images/'.$img);
        }

        // Gallery
        $gallery_imgs = isset($edit_row['gallery']) ? str2arr($edit_row['gallery']) : [];
        if (!empty($_FILES['gallery']['name'][0])) {
            foreach ($_FILES['gallery']['tmp_name'] as $k => $tmp_name) {
                if ($_FILES['gallery']['name'][$k]) {
                    $gimg = time().'_'.$k.'_'.basename($_FILES['gallery']['name'][$k]);
                    move_uploaded_file($tmp_name, __DIR__.'../../images/'.$gimg);
                    $gallery_imgs[] = $gimg;
                }
            }
        }
        if (isset($_POST['remove_gallery_img'])) {
            $remove_img = $_POST['remove_gallery_img'];
            $gallery_imgs = array_filter($gallery_imgs, fn($im) => $im !== $remove_img);
            $img_path = __DIR__ . '../../images/' . $remove_img;
            if (file_exists($img_path)) unlink($img_path);
        }
        $gallery = arr2str($gallery_imgs);

        // Update
        $sql = "UPDATE restaurants SET name='$name', address='$address', cat_name='$cat_name', contact='$contact',
            email='$email', website='$website', opening_hours='$opening_hours', closed_on='$closed_on',
            reservation_required='$reservation_required', special_offers='$special_offers', cuisines='$cuisines',
            menu='$menu', services='$services', chefs='$chefs', wifi='$wifi', parking='$parking',
            outdoor_seating='$outdoor_seating', live_music='$live_music', bar='$bar',
            kids_friendly='$kids_friendly', pet_friendly='$pet_friendly', wheelchair_accessible='$wheelchair_accessible',
            payment_options='$payment_options', description='$description', gallery='$gallery',
            img='$img', city_name='$city_name'
            WHERE id=$id AND user_email='" . mysqli_real_escape_string($conn, $user_email) . "'";
        mysqli_query($conn, $sql);

        header("Location: profile.php");
        exit();
    }elseif ($_POST['form_type'] === 'pg') 
    
    {
        // PG EDIT LOGIC
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save']) && isset($_POST['id']) && $_POST['id']) {
    $id = intval($_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $cat_name = 'PG';
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $website = mysqli_real_escape_string($conn, $_POST['website']);
    $established = mysqli_real_escape_string($conn, $_POST['established']);
    $rooms = mysqli_real_escape_string($conn, $_POST['rooms']);
    $city_name = mysqli_real_escape_string($conn, $_POST['city_name']);
    $admission_process = mysqli_real_escape_string($conn, $_POST['admission_process']);
    $payment_options = isset($_POST['payment_options']) ? mysqli_real_escape_string($conn, $_POST['payment_options']) : '';

    // Room types (checkbox group)
    $room_types = isset($_POST['room_types']) ? arr2str($_POST['room_types']) : '';

    // Facilities (comma-separated values)
    $facilities = isset($_POST['facilities']) ? arr2str($_POST['facilities']) : '';

    // Scholarships/Discounts
    $scholarships = isset($_POST['scholarships']) ? arr2str($_POST['scholarships']) : '';

    // Amenities & Features (checkboxes)
    $hostel = isset($_POST['hostel']) ? $_POST['hostel'] : '';
    $transport = isset($_POST['transport']) ? $_POST['transport'] : '';
    $library = isset($_POST['library']) ? $_POST['library'] : '';
    $sports = isset($_POST['sports']) ? $_POST['sports'] : '';
    $cafeteria = isset($_POST['cafeteria']) ? $_POST['cafeteria'] : '';
    $disabled_friendly = isset($_POST['disabled_friendly']) ? $_POST['disabled_friendly'] : '';

    // Faculty/Staff
    $faculty_arr = [];
    if (isset($_POST['faculty_name']) && is_array($_POST['faculty_name'])) {
        foreach ($_POST['faculty_name'] as $i=>$n) {
            $dept = $_POST['faculty_dept'][$i] ?? '';
            $exp = $_POST['faculty_exp'][$i] ?? '';
            $faculty_arr[] = "$n|$dept|$exp";
        }
    }
    $faculty = arr2str($faculty_arr);

    // Get existing row for images/gallery
    $res = mysqli_query($conn, "SELECT img, gallery FROM pgs WHERE id=$id AND user_email='" . mysqli_real_escape_string($conn, $user_email) . "'");
    $edit_row = mysqli_fetch_assoc($res);

    // Handle main image upload
    $img = $edit_row['img'] ?? '';
    if (!empty($_FILES['img']['name'])) {
        $img = time().'_'.basename($_FILES['img']['name']);
        move_uploaded_file($_FILES['img']['tmp_name'], __DIR__.'../../images/'.$img);
    }

    // Handle gallery upload
    $gallery_imgs = isset($edit_row['gallery']) ? str2arr($edit_row['gallery']) : [];
    if (!empty($_FILES['gallery']['name'][0])) {
        foreach ($_FILES['gallery']['tmp_name'] as $k => $tmp_name) {
            if ($_FILES['gallery']['name'][$k]) {
                $gimg = time().'_'.$k.'_'.basename($_FILES['gallery']['name'][$k]);
                move_uploaded_file($tmp_name, __DIR__.'../../images/'.$gimg);
                $gallery_imgs[] = $gimg;
            }
        }
    }
    // Handle removal of gallery images
    if (isset($_POST['remove_gallery_img'])) {
        $remove_img = $_POST['remove_gallery_img'];
        $gallery_imgs = array_filter($gallery_imgs, function($img) use ($remove_img) {
            return $img !== $remove_img;
        });
        $img_path = __DIR__ . '../../images/' . $remove_img;
        if (file_exists($img_path)) unlink($img_path);
    }
    $gallery = arr2str($gallery_imgs);

    // Update query
    $sql = "UPDATE pgs SET 
        name='$name', address='$address', cat_name='$cat_name', contact='$contact', email='$email', website='$website', established='$established',
        rooms='$rooms', room_types='$room_types', facilities='$facilities', faculty='$faculty', admission_process='$admission_process',
        scholarships='$scholarships', hostel='$hostel', transport='$transport', library='$library', sports='$sports', cafeteria='$cafeteria',
        disabled_friendly='$disabled_friendly', gallery='$gallery', img='$img', city_name='$city_name'
        WHERE id=$id AND user_email='" . mysqli_real_escape_string($conn, $user_email) . "'";
    mysqli_query($conn, $sql);

    // Redirect after update
    header("Location: profile.php");
    exit();
}
}elseif ($_POST['form_type'] === 'hp') {
    // HISTORICAL PLACE EDIT LOGIC
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save']) && isset($_POST['id']) && $_POST['id']) {
    $id = intval($_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $cat_name = "HistoricalPlace";
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $website = mysqli_real_escape_string($conn, $_POST['website']);
    $established = mysqli_real_escape_string($conn, $_POST['established']);
    $opening_hours = mysqli_real_escape_string($conn, $_POST['opening_hours']);
    $ticket_info = mysqli_real_escape_string($conn, $_POST['ticket_info']);
    $city_name = mysqli_real_escape_string($conn, $_POST['city_name']);

    // Features and Guides
    $features = isset($_POST['features']) ? arr2str($_POST['features']) : '';
    $guides = isset($_POST['guides']) ? arr2str($_POST['guides']) : '';

    // Amenities & Features (checkboxes)
    $hostel = isset($_POST['hostel']) ? $_POST['hostel'] : '';
    $transport = isset($_POST['transport']) ? $_POST['transport'] : '';
    $library = isset($_POST['library']) ? $_POST['library'] : '';
    $sports = isset($_POST['sports']) ? $_POST['sports'] : '';
    $cafeteria = isset($_POST['cafeteria']) ? $_POST['cafeteria'] : '';
    $disabled_friendly = isset($_POST['disabled_friendly']) ? $_POST['disabled_friendly'] : '';

    // Get existing row for images/gallery
    $res = mysqli_query($conn, "SELECT img, gallery FROM h_p WHERE id=$id AND user_email='" . mysqli_real_escape_string($conn, $user_email) . "'");
    $edit_row = mysqli_fetch_assoc($res);

    // Handle main image upload
    $img = $edit_row['img'] ?? '';
    if (!empty($_FILES['img']['name'])) {
        $img = time().'_'.basename($_FILES['img']['name']);
        move_uploaded_file($_FILES['img']['tmp_name'], __DIR__.'../../images/'.$img);
    }

    // Handle gallery upload
    $gallery_imgs = isset($edit_row['gallery']) ? str2arr($edit_row['gallery']) : [];
    if (!empty($_FILES['gallery']['name'][0])) {
        foreach ($_FILES['gallery']['tmp_name'] as $k => $tmp_name) {
            if ($_FILES['gallery']['name'][$k]) {
                $gimg = time().'_'.$k.'_'.basename($_FILES['gallery']['name'][$k]);
                move_uploaded_file($tmp_name, __DIR__.'../../images/'.$gimg);
                $gallery_imgs[] = $gimg;
            }
        }
    }
    // Handle removal of gallery images
    if (isset($_POST['remove_gallery_img'])) {
        $remove_img = $_POST['remove_gallery_img'];
        $gallery_imgs = array_filter($gallery_imgs, function($img) use ($remove_img) {
            return $img !== $remove_img;
        });
        $img_path = __DIR__ . '../../images/' . $remove_img;
        if (file_exists($img_path)) unlink($img_path);
    }
    $gallery = arr2str($gallery_imgs);

    // Update query
    $sql = "UPDATE h_p SET 
        name='$name', address='$address', cat_name='$cat_name', contact='$contact', email='$email', website='$website', established='$established',
        opening_hours='$opening_hours', ticket_info='$ticket_info', features='$features', guides='$guides',
        hostel='$hostel', transport='$transport', library='$library', sports='$sports', cafeteria='$cafeteria',
        disabled_friendly='$disabled_friendly', gallery='$gallery', img='$img', city_name='$city_name'
        WHERE id=$id AND user_email='" . mysqli_real_escape_string($conn, $user_email) . "'";
    mysqli_query($conn, $sql);

    // Redirect after update
    header("Location: profile.php");
    exit();
}
}elseif ($_POST['form_type'] === 'school'){

    // SCHOOL EDIT LOGIC
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save']) && isset($_POST['id']) && $_POST['id']) {
    $id = intval($_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $cat_name = "School";
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $website = mysqli_real_escape_string($conn, $_POST['website']);
    $established = mysqli_real_escape_string($conn, $_POST['established']);
    $grades = mysqli_real_escape_string($conn, $_POST['grades']);
    $city_name = mysqli_real_escape_string($conn, $_POST['city_name']);
    $admission_process = mysqli_real_escape_string($conn, $_POST['admission_process']);

    // Departments & Facilities (comma separated)
    $departments = isset($_POST['departments']) ? arr2str($_POST['departments']) : '';
    $facilities = isset($_POST['facilities']) ? arr2str($_POST['facilities']) : '';
    $scholarships = isset($_POST['scholarships']) ? arr2str($_POST['scholarships']) : '';

    // Amenities & Features (checkboxes)
    $hostel = isset($_POST['hostel']) ? $_POST['hostel'] : '';
    $transport = isset($_POST['transport']) ? $_POST['transport'] : '';
    $library = isset($_POST['library']) ? $_POST['library'] : '';
    $sports = isset($_POST['sports']) ? $_POST['sports'] : '';
    $cafeteria = isset($_POST['cafeteria']) ? $_POST['cafeteria'] : '';
    $disabled_friendly = isset($_POST['disabled_friendly']) ? $_POST['disabled_friendly'] : '';

    // Faculty array
    $faculty_arr = [];
    if (isset($_POST['faculty_name']) && is_array($_POST['faculty_name'])) {
        foreach ($_POST['faculty_name'] as $i => $n) {
            $dept = $_POST['faculty_dept'][$i] ?? '';
            $exp = $_POST['faculty_exp'][$i] ?? '';
            $faculty_arr[] = "$n|$dept|$exp";
        }
    }
    $faculty = arr2str($faculty_arr);

    // Get existing row for images/gallery
    $res = mysqli_query($conn, "SELECT img, gallery FROM schools WHERE id=$id AND user_email='" . mysqli_real_escape_string($conn, $user_email) . "'");
    $edit_row = mysqli_fetch_assoc($res);

    // Handle main image upload
    $img = $edit_row['img'] ?? '';
    if (!empty($_FILES['img']['name'])) {
        $img = time().'_'.basename($_FILES['img']['name']);
        move_uploaded_file($_FILES['img']['tmp_name'], __DIR__.'../../images/'.$img);
    }

    // Handle gallery upload
    $gallery_imgs = isset($edit_row['gallery']) ? str2arr($edit_row['gallery']) : [];
    if (!empty($_FILES['gallery']['name'][0])) {
        foreach ($_FILES['gallery']['tmp_name'] as $k => $tmp_name) {
            if ($_FILES['gallery']['name'][$k]) {
                $gimg = time().'_'.$k.'_'.basename($_FILES['gallery']['name'][$k]);
                move_uploaded_file($tmp_name, __DIR__.'../../images/'.$gimg);
                $gallery_imgs[] = $gimg;
            }
        }
    }
    // Handle removal of gallery images
    if (isset($_POST['remove_gallery_img'])) {
        $remove_img = $_POST['remove_gallery_img'];
        $gallery_imgs = array_filter($gallery_imgs, function($img) use ($remove_img) {
            return $img !== $remove_img;
        });
        $img_path = __DIR__ . '../../images/' . $remove_img;
        if (file_exists($img_path)) unlink($img_path);
    }
    $gallery = arr2str($gallery_imgs);

    // Update query
    $sql = "UPDATE schools SET 
        name='$name', address='$address', cat_name='$cat_name', contact='$contact', email='$email', website='$website', established='$established',
        grades='$grades', departments='$departments', facilities='$facilities', faculty='$faculty', admission_process='$admission_process',
        scholarships='$scholarships', hostel='$hostel', transport='$transport', library='$library', sports='$sports', cafeteria='$cafeteria',
        disabled_friendly='$disabled_friendly', gallery='$gallery', img='$img', city_name='$city_name'
        WHERE id=$id AND user_email='" . mysqli_real_escape_string($conn, $user_email) . "'";
    mysqli_query($conn, $sql);

    // Redirect after update
    header("Location: profile.php");
    exit();
}
}elseif ($_POST['form_type'] === 'college'){

    $id = intval($_POST['id']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $city_name = mysqli_real_escape_string($conn, $_POST['city_name']);
    $cat_name = "College";
    $contact = mysqli_real_escape_string($conn, $_POST['contact']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $website = mysqli_real_escape_string($conn, $_POST['website']);
    $established = mysqli_real_escape_string($conn, $_POST['established']);
    $admission_process = mysqli_real_escape_string($conn, $_POST['admission_process']);
    $user_email = $_SESSION['email'];

    // Convert array inputs to comma-separated strings
    $courses = isset($_POST['courses']) ? arr2str($_POST['courses']) : '';
    $departments = isset($_POST['departments']) ? arr2str($_POST['departments']) : '';
    $facilities = isset($_POST['facilities']) ? arr2str($_POST['facilities']) : '';
    $scholarships = isset($_POST['scholarships']) ? arr2str($_POST['scholarships']) : '';

    // Faculty format: name|dept|exp,name|dept|exp
    $facultyArr = [];
    if (!empty($_POST['faculty_name'])) {
        foreach ($_POST['faculty_name'] as $i => $fname) {
            $fd = trim($_POST['faculty_dept'][$i] ?? '');
            $fe = trim($_POST['faculty_exp'][$i] ?? '');
            if ($fname || $fd || $fe) {
                $facultyArr[] = $fname . "|" . $fd . "|" . $fe;
            }
        }
    }
    $faculty = implode(",", $facultyArr);

    // Checkboxes
    $hostel = isset($_POST['hostel']) ? $_POST['hostel'] : '';
    $transport = isset($_POST['transport']) ? $_POST['transport'] : '';
    $library = isset($_POST['library']) ? $_POST['library'] : '';
    $sports = isset($_POST['sports']) ? $_POST['sports'] : '';
    $cafeteria = isset($_POST['cafeteria']) ? $_POST['cafeteria'] : '';
    $disabled_friendly = isset($_POST['disabled_friendly']) ? $_POST['disabled_friendly'] : '';

    // Upload Logo
    $img = $editing && !empty($row['img']) ? $row['img'] : '';
    if (!empty($_FILES['img']['name'])) {
        $imgName = time() . "_" . basename($_FILES['img']['name']);
        $target = "../images/" . $imgName;
        if (move_uploaded_file($_FILES['img']['tmp_name'], $target)) {
            $img = $imgName;
        }
    }

    // Upload Gallery
    $gallery = $editing && !empty($row['gallery']) ? str2arr($row['gallery']) : [];
    if (!empty($_FILES['gallery']['name'][0])) {
        foreach ($_FILES['gallery']['name'] as $key => $gname) {
            if ($gname) {
                $gimgName = time() . "_" . basename($gname);
                $target = "../images/" . $gimgName;
                if (move_uploaded_file($_FILES['gallery']['tmp_name'][$key], $target)) {
                    $gallery[] = $gimgName;
                }
            }
        }
    }
    $galleryStr = arr2str($gallery);

    // Insert or Update
    if ($id > 0) {
        $sql = "UPDATE colleges SET 
            name='$name',
            img='$img',
            address='$address',
            city_name='$city_name',
            cat_name='$cat_name',
            contact='$contact',
            email='$email',
            website='$website',
            established='$established',
            courses='$courses',
            departments='$departments',
            facilities='$facilities',
            faculty='$faculty',
            admission_process='$admission_process',
            gallery='$galleryStr',
            scholarships='$scholarships',
            hostel='$hostel',
            transport='$transport',
            library='$library',
            sports='$sports',
            cafeteria='$cafeteria',
            disabled_friendly='$disabled_friendly',
            user_email='$user_email'
            WHERE id=$id";
        mysqli_query($conn, $sql);
        $msg = "College updated successfully!";
    } else {
        $sql = "INSERT INTO colleges 
            (name,img,address,city_name,cat_name,contact,email,website,established,
             courses,departments,facilities,faculty,admission_process,gallery,
             scholarships,hostel,transport,library,sports,cafeteria,disabled_friendly,user_email)
            VALUES 
            ('$name','$img','$address','$city_name','$cat_name','$contact','$email','$website','$established',
             '$courses','$departments','$facilities','$faculty','$admission_process','$galleryStr',
             '$scholarships','$hostel','$transport','$library','$sports','$cafeteria','$disabled_friendly','$user_email')";
        mysqli_query($conn, $sql);
        $msg = "College added successfully!";
    }
    header("Location: profile.php");
    exit();
}
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profile Page</title>
    <style>
        body { background: #f8fafc; font-family: Segoe UI,Arial; }
        .container { max-width: 1100px; margin: 32px auto; background: #fff; padding: 30px 35px; border-radius: 14px; box-shadow: 0 3px 16px #d2e5f9; }
        h2 { color: #2365ab; margin-bottom: 15px; }
        table { border-collapse: collapse; width: 100%; margin-top: 15px; background: #f7fafc; }
        th, td { border: 1px solid #dde7f4; padding: 8px 6px; }
        th { background: #eaf2fb; color: #245f8e; }
        tr:nth-child(even) { background: #f4f9fd; }
        #searchBar { margin-bottom: 15px; padding: 8px; width: 300px; border-radius: 6px; border: 1px solid #b4d1e5; }
        .msg { color:#888; margin-top:15px; }
        a.logout { display:inline-block; margin-top:20px; background:#d03030; color:#fff; padding:8px 16px; border-radius:6px; text-decoration:none; }
        #logout-btn { background: #d03030; color: #fff; padding: 6px 18px; border-radius: 7px; border: none; cursor: pointer; font-size: 1em; font-weight: bold; text-decoration: none;}
        #header-bar { display: flex; justify-content: space-between; align-items: center; background: #eaf2fb; padding: 12px 35px; border-radius: 10px; margin-bottom: 34px; box-shadow: 0 2px 8px #dde7f4; }
        .btn { padding: 5px 15px; border-radius: 5px; border: 0; background: #2680c2; color: #fff; cursor: pointer; }
        .btn-danger { background: #d03030; }
        .form-container { display: none; margin-top:30px; }
        .form-container.active { display: block; }
        /* --- Additional CSS below from all forms --- */
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
        .btn-add { background: #3bbf72; }
        .small { font-size: 0.95em; color: #888; }
        .inline { display: inline-block; margin-right: 10px; }
        .gallery-img-block { display: inline-flex; align-items: center; margin-right: 16px; margin-bottom: 5px;}
        .gallery-img { max-height: 70px; border-radius: 5px; border:1px solid #eee;}
        .gallery-remove-btn { background: #e74c3c; color: #fff; border: none; border-radius: 3px; padding:3px 8px; margin-left:6px; cursor:pointer; font-size: 0.9em;}
        .logo-img-block { margin-top:5px; display:inline-flex; align-items:center; }
    </style>
</head>
<body>
<div class="container">
    <div id="header-bar">
        <span id="user-email"><b>Logged in as: </b><?= htmlspecialchars($_SESSION['email']) ?></span>
        <div class="nav">
        <a class="btn" href="../index.php">Home</a>
        <a id="logout-btn" href="logout1.php">Logout</a>
    </div>
    </div>
    <h2>Profile Records</h2>
    <?php if (count($all_records) > 0): ?>
        <input type="text" id="searchBar" placeholder="Search by Name..." onkeyup="filterTable()">
        <table id="profileTable">
            <tr>
                <th>Name</th>
                <th>Category</th>
                <th>City</th>
                <th>Actions</th>
            </tr>
            <?php foreach ($all_records as $row): ?>
                <tr>
                    <td><?= htmlspecialchars(val($row, 'name')) ?></td>
                    <td><?= htmlspecialchars(val($row, 'cat_name')) ?></td>
                    <td><?= htmlspecialchars(val($row, 'city_name')) ?></td>
                    <td>
                        <a class="btn" href="profile.php?edit=<?= $row['id'] ?>&table=<?= $row['table_src'] ?>">Edit</a>
                        <a class="btn btn-danger" href="profile.php?del=<?= $row['id'] ?>&del_table=<?= $row['table_src'] ?>" onclick="return confirm('Delete this record?')">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    <?php else: ?>
        <p class="msg">No records found for your account.</p>
    <?php endif; ?>

    <!-- 6 FULL forms below - only the active one is shown -->
    <div id="hospitalForm_container" class="form-container <?= ($editing && $edit_table=='hospitals')?'active':'' ?>">
        <?php if ($editing && $edit_table=='hospitals') {
            $row = $edit_row;
            $editing = true;
            include 'hospital_form.php';
        } ?>
    </div>
    <div id="restaurantForm_container" class="form-container <?= ($editing && $edit_table=='restaurants')?'active':'' ?>">
        <?php if ($editing && $edit_table=='restaurants') {
            $row = $edit_row;
            $editing = true;
            include 'restaurant_form.php';
        } ?>
    </div>
    <div id="pgForm_container" class="form-container <?= ($editing && $edit_table=='pgs')?'active':'' ?>">
        <?php if ($editing && $edit_table=='pgs') {
            $row = $edit_row;
            $editing = true;
            include 'pg_form.php';
        } ?>
    </div>
    <div id="hpForm_container" class="form-container <?= ($editing && $edit_table=='h_p')?'active':'' ?>">
        <?php if ($editing && $edit_table=='h_p') {
            $row = $edit_row;
            $editing = true;
            include 'h_p_form.php';
        } ?>
    </div>
    <div id="schoolForm_container" class="form-container <?= ($editing && $edit_table=='schools')?'active':'' ?>">
        <?php if ($editing && $edit_table=='schools') {
            $row = $edit_row;
            $editing = true;
            include 'school_form.php';
        } ?>
    </div>
    <div id="collegeForm_container" class="form-container <?= ($editing && $edit_table=='colleges')?'active':'' ?>">
        <?php if ($editing && $edit_table=='colleges') {
            $row = $edit_row;
            $editing = true;
            include 'college_form.php';
        } ?>
    </div>
</div>
<script>
function filterTable() {
    var input = document.getElementById("searchBar").value.toUpperCase();
    var table = document.getElementById("profileTable");
    var tr = table.getElementsByTagName("tr");
    for (var i = 1; i < tr.length; i++) {
        var td = tr[i].getElementsByTagName("td")[0];
        if (td) {
            var txt = td.textContent || td.innerText;
            tr[i].style.display = txt.toUpperCase().indexOf(input) > -1 ? "" : "none";
        }
    }
}
window.onload = function() {
    <?php if ($editing && $edit_table): ?>
        document.getElementById('profileTable').style.display = 'none';
        document.querySelector('.form-container.active').style.display = 'block';
    <?php else: ?>
        var containers = document.getElementsByClassName('form-container');
        for (var i = 0; i < containers.length; i++) containers[i].classList.remove('active');
        document.getElementById('profileTable').style.display = '';
    <?php endif; ?>
};
</script>
</body>
</html>
</main>
</body>
</html>
