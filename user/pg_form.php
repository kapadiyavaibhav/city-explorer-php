<?php
if (!isset($editing)) { $editing = false; $row = []; }
//function arr2str($arr) { return is_array($arr)?implode(',',$arr):$arr; }
//function str2arr($str) { return $str?explode(',',$str):[]; }
?>
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
<h2><?= $editing ? "Edit P.G." : "Add P.G." ?></h2>
<form method="post" enctype="multipart/form-data" id="pgForm">
    <input type="hidden" name="id" value="<?= $editing ? $row['id'] : '' ?>">
    <label>P.G. Name</label>
    <input type="text" name="name" required value="<?= htmlspecialchars($editing?$row['name']:'') ?>">
    <label>Logo/Image File</label>
    <input type="file" name="img" accept="image/*">
    <?php if ($editing && !empty($row['img'])): ?>
        <div class="logo-img-block">
            <img src="../images/<?= htmlspecialchars($row['img']) ?>" alt="Logo" style="max-height:80px;">
        </div>
    <?php endif; ?>
    <label>Address</label>
    <input type="text" name="address" value="<?= htmlspecialchars($editing?$row['address']:'') ?>">
    <input type="hidden" name="cat_name" value="PG">
    <label>Contact Number</label>
    <input type="text" name="contact" value="<?= htmlspecialchars($editing?$row['contact']:'') ?>">

<input type="hidden" name="form_type" value="pg">

    <label>Email Address</label>
    <input type="email" name="email" value="<?= htmlspecialchars($editing?$row['email']:'') ?>">
    <label>Website</label>
    <input type="text" name="website" value="<?= htmlspecialchars($editing?$row['website']:'') ?>">
    <label>Established Year</label>
    <input type="text" name="established" value="<?= htmlspecialchars($editing?$row['established']:'') ?>">
    <label>Total Rooms</label>
    <input type="text" name="rooms" value="<?= htmlspecialchars($editing?$row['rooms']:'') ?>">
    <label>Room Types</label>
    <?php
        $selected_room_types = $editing ? explode(',', $row['room_types']) : [];
        function checked_roomtype($val, $arr) { return in_array($val, $arr) ? 'checked' : ''; }
    ?>
    <div class="yesno inline"><input type="checkbox" name="room_types[]" value="Single" <?= checked_roomtype('Single', $selected_room_types) ?>> Single</div>
    <div class="yesno inline"><input type="checkbox" name="room_types[]" value="Double" <?= checked_roomtype('Double', $selected_room_types) ?>> Double</div>
    <div class="yesno inline"><input type="checkbox" name="room_types[]" value="3 Sharing" <?= checked_roomtype('3 Sharing', $selected_room_types) ?>> 3 Sharing</div>
    <div class="yesno inline"><input type="checkbox" name="room_types[]" value="AC" <?= checked_roomtype('AC', $selected_room_types) ?>> AC</div>
    <div class="yesno inline"><input type="checkbox" name="room_types[]" value="Non AC" <?= checked_roomtype('Non AC', $selected_room_types) ?>> Non AC</div>
    <div class="form-section">
        <label>Facilities <span class="small">(comma-separated)</span></label>
        <input type="text" name="facilities[]" value="<?= htmlspecialchars($editing?str_replace(',','&#10;',$row['facilities']??''):'') ?>" placeholder="Wi-Fi,Mess,Power Backup">
    </div>
    <div class="form-section">
        <label>Faculty/Staff</label>
        <div id="faculty">
            <?php
            $faculty = $editing ? str2arr($row['faculty']) : ['||'];
            foreach ($faculty as $i=>$fac) {
                list($n,$d,$e) = array_pad(explode('|',$fac),3,'');
            ?>
                <div class="faculty-block">
                    <input type="text" name="faculty_name[]" placeholder="Name" value="<?= htmlspecialchars($n) ?>">
                    <input type="text" name="faculty_dept[]" placeholder="Role" value="<?= htmlspecialchars($d) ?>">
                    <input type="text" name="faculty_exp[]" placeholder="Experience (years)" value="<?= htmlspecialchars($e) ?>">
                </div>
            <?php } ?>
            <div id="faculty-controls">
                <button type="button" onclick="addFaculty()">Add Staff</button>
                <button type="button" onclick="removeFaculty()">Remove Staff</button>
            </div>
        </div>
    </div>
    <div class="form-section">
        <label>Admission Process</label>
        <textarea name="admission_process" rows="2"><?= htmlspecialchars($editing?$row['admission_process']:'') ?></textarea>
    </div>
    <div class="form-section">
        <label>Scholarships/Discounts (comma-separated)</label>
        <input type="text" name="scholarships[]" value="<?= htmlspecialchars($editing?str_replace(',','&#10;',$row['scholarships']??''):'') ?>" placeholder="Discount for Early Payment,Referral Bonus">
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
<script>
function addFaculty() {
    var d = document.createElement('div');
    d.className = 'faculty-block';
    d.innerHTML = `
        <input type="text" name="faculty_name[]" placeholder="Name">
        <input type="text" name="faculty_dept[]" placeholder="Role">
        <input type="text" name="faculty_exp[]" placeholder="Experience (years)">
    `;
    document.getElementById('faculty').insertBefore(d, document.getElementById('faculty-controls'));
}
function removeFaculty() {
    var facultyBlocks = document.querySelectorAll('#faculty .faculty-block');
    if (facultyBlocks.length > 1) {
        facultyBlocks[facultyBlocks.length - 1].remove();
    } else {
        alert('At least one staff record is required!');
    }
}
</script>