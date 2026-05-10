<?php
// expects $editing and $row set if editing
if (!isset($editing)) { $editing = false; $row = []; }
//function arr2str($arr) { return is_array($arr) ? implode(',', $arr) : $arr; }
//function str2arr($str) { return $str ? explode(',', $str) : []; }
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
<h2><?= $editing ? "Edit Hospital" : "Add Hospital" ?></h2>
<form method="post" enctype="multipart/form-data" id="hospitalForm">
    <input type="hidden" name="id" value="<?= $editing ? $row['id'] : '' ?>">
    <label>Hospital Name</label>
    <input type="text" name="name" required value="<?= htmlspecialchars($editing?$row['name']:'') ?>">

<input type="hidden" name="form_type" value="hospital">


    <label>Logo/Image File</label>
    <input type="file" name="img" accept="../images/*">
    <?php if ($editing && !empty($row['img'])): ?>
        <div class="logo-img-block">
            <img src="../images/<?= htmlspecialchars($row['img']) ?>" alt="Logo" style="max-height:80px;">
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
                    <img class="gallery-img" src="../images/<?= htmlspecialchars($img) ?>" alt="Gallery">
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