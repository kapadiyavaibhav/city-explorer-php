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
<h2><?= $editing ? "Edit School" : "Add School" ?></h2>
<form method="post" enctype="multipart/form-data" id="schoolForm">
    <input type="hidden" name="id" value="<?= $editing ? $row['id'] : '' ?>">
    <label>School Name</label>
    <input type="text" name="name" required value="<?= htmlspecialchars($editing && isset($row['name']) ? $row['name'] : '') ?>">
    <label>Logo/Image File</label>
    <input type="file" name="img" accept="image/*">
    <?php if ($editing && !empty($row['img'])): ?>
        <div class="logo-img-block">
            <img src="../images/<?= htmlspecialchars($row['img']) ?>" alt="Logo" style="max-height:80px;">
        </div>
    <?php endif; ?>
    <label>Address</label>
    <input type="text" name="address" value="<?= htmlspecialchars($editing && isset($row['address']) ? $row['address'] : '') ?>">
    <input type="hidden" name="cat_name" value="School">
    <input type="hidden" name="form_type" value="school">
    <label>Contact Number</label>
    <input type="text" name="contact" value="<?= htmlspecialchars($editing && isset($row['contact']) ? $row['contact'] : '') ?>">
    <label>Email Address</label>
    <input type="email" name="email" value="<?= htmlspecialchars($editing && isset($row['email']) ? $row['email'] : '') ?>">
    <label>Website</label>
    <input type="text" name="website" value="<?= htmlspecialchars($editing && isset($row['website']) ? $row['website'] : '') ?>">
    <label>Established Year</label>
    <input type="text" name="established" value="<?= htmlspecialchars($editing && isset($row['established']) ? $row['established'] : '') ?>">
    <label>Grades (e.g. Nursery-12)</label>
    <input type="text" name="grades" value="<?= htmlspecialchars($editing && isset($row['grades']) ? $row['grades'] : '') ?>">
    <div class="form-section">
        <label>Departments <span class="small">(comma-separated)</span></label>
        <input type="text" name="departments[]" value="<?= htmlspecialchars($editing && isset($row['departments']) ? str_replace(',','&#10;',$row['departments']) : '') ?>" placeholder="Mathematics,Physics,English">
    </div>
    <div class="form-section">
        <label>Facilities <span class="small">(comma-separated)</span></label>
        <input type="text" name="facilities[]" value="<?= htmlspecialchars($editing && isset($row['facilities']) ? str_replace(',','&#10;',$row['facilities']) : '') ?>" placeholder="Auditorium,Medical,Playground">
    </div>
    <div class="form-section">
        <label>Faculty</label>
        <div id="faculty">
            <?php
            $faculty = $editing && isset($row['faculty']) ? str2arr($row['faculty']) : ['||'];
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
        <textarea name="admission_process" rows="2"><?= htmlspecialchars($editing && isset($row['admission_process']) ? $row['admission_process'] : '') ?></textarea>
    </div>
    <div class="form-section">
        <label>Scholarships (comma-separated)</label>
        <input type="text" name="scholarships[]" value="<?= htmlspecialchars($editing && isset($row['scholarships']) ? str_replace(',','&#10;',$row['scholarships']) : '') ?>" placeholder="Merit-based,SC/ST,State">
    </div>
    <div class="form-section">
        <label>Amenities & Features</label>
        <div class="yesno inline"><input type="checkbox" name="hostel" value="Hostel" <?= $editing && isset($row['hostel']) && $row['hostel']=='Hostel'?'checked':'' ?>> Hostel</div>
        <div class="yesno inline"><input type="checkbox" name="transport" value="Transport" <?= $editing && isset($row['transport']) && $row['transport']=='Transport'?'checked':'' ?>> Transport</div>
        <div class="yesno inline"><input type="checkbox" name="library" value="Library" <?= $editing && isset($row['library']) && $row['library']=='Library'?'checked':'' ?>> Library</div>
        <div class="yesno inline"><input type="checkbox" name="sports" value="Sports" <?= $editing && isset($row['sports']) && $row['sports']=='Sports'?'checked':'' ?>> Sports</div>
        <div class="yesno inline"><input type="checkbox" name="cafeteria" value="Cafeteria" <?= $editing && isset($row['cafeteria']) && $row['cafeteria']=='Cafeteria'?'checked':'' ?>> Cafeteria</div>
        <div class="yesno inline"><input type="checkbox" name="disabled_friendly" value="Disabled Friendly" <?= $editing && isset($row['disabled_friendly']) && $row['disabled_friendly']=='Disabled Friendly'?'checked':'' ?>> Disabled Friendly</div>
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
    <input type="text" name="city_name" value="<?= htmlspecialchars($editing && isset($row['city_name']) ? $row['city_name'] : '') ?>">
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