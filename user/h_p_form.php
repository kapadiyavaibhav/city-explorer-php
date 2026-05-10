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
<h2><?= $editing ? "Edit Historical Place" : "Add Historical Place" ?></h2>
<form method="post" enctype="multipart/form-data" id="hpForm">
    <input type="hidden" name="id" value="<?= $editing ? $row['id'] : '' ?>">
    <label>Place Name</label>
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
    <input type="hidden" name="cat_name" value="HistoricalPlace">
    <label>Contact Number</label>
    <input type="text" name="contact" value="<?= htmlspecialchars($editing && isset($row['contact']) ? $row['contact'] : '') ?>">
   
   <input type="hidden" name="form_type" value="hp">
   
    <label>Email Address</label>
    <input type="email" name="email" value="<?= htmlspecialchars($editing && isset($row['email']) ? $row['email'] : '') ?>">
    <label>Website</label>
    <input type="text" name="website" value="<?= htmlspecialchars($editing && isset($row['website']) ? $row['website'] : '') ?>">
    <label>Established Year</label>
    <input type="text" name="established" value="<?= htmlspecialchars($editing && isset($row['established']) ? $row['established'] : '') ?>">
    <label>Opening Hours</label>
    <input type="text" name="opening_hours" value="<?= htmlspecialchars($editing && isset($row['opening_hours']) ? $row['opening_hours'] : '') ?>">
    <label>Ticket Information</label>
    <input type="text" name="ticket_info" value="<?= htmlspecialchars($editing && isset($row['ticket_info']) ? $row['ticket_info'] : '') ?>">
    <div class="form-section">
        <label>Features <span class="small">(comma-separated)</span></label>
        <input type="text" name="features[]" value="<?= htmlspecialchars($editing && isset($row['features']) ? str_replace(',','&#10;',$row['features']) : '') ?>" placeholder="Heritage,UNESCO Site,Gardens">
    </div>
    <div class="form-section">
        <label>Guides/Contacts (comma-separated)</label>
        <input type="text" name="guides[]" value="<?= htmlspecialchars($editing && isset($row['guides']) ? str_replace(',','&#10;',$row['guides']) : '') ?>" placeholder="Mr. X,Ms. Y">
    </div>
    <div class="form-section">
        <label>Amenities & Features</label>
        <div class="yesno inline"><input type="checkbox" name="hostel" value="Help Desk" <?= $editing && isset($row['hostel']) && $row['hostel']=='Help Desk'?'checked':'' ?>> Help Desk</div>
        <div class="yesno inline"><input type="checkbox" name="transport" value="Parking Area" <?= $editing && isset($row['transport']) && $row['transport']=='Parking Area'?'checked':'' ?>> Parking Area</div>
        <div class="yesno inline"><input type="checkbox" name="library" value="Restrooms & Toilets" <?= $editing && isset($row['library']) && $row['library']=='Restrooms & Toilets'?'checked':'' ?>> Restrooms & Toilets</div>
        <div class="yesno inline"><input type="checkbox" name="sports" value="Drinking Water Stations" <?= $editing && isset($row['sports']) && $row['sports']=='Drinking Water Stations'?'checked':'' ?>> Drinking Water Stations</div>
        <div class="yesno inline"><input type="checkbox" name="cafeteria" value="Seating Areas & Rest Zones" <?= $editing && isset($row['cafeteria']) && $row['cafeteria']=='Seating Areas & Rest Zones'?'checked':'' ?>> Seating Areas & Rest Zones</div>
        <div class="yesno inline"><input type="checkbox" name="disabled_friendly" value="First Aid & Medical Assistance" <?= $editing && isset($row['disabled_friendly']) && $row['disabled_friendly']=='First Aid & Medical Assistance'?'checked':'' ?>> First Aid & Medical Assistance</div>
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