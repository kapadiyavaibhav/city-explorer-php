<?php
if (!isset($editing)) { $editing = false; $row = []; }
//function arr2str($arr) { return is_array($arr)?implode(',',$arr):$arr; }
//function str2arr($str) { return $str?explode(',',$str):[]; }

?>
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
    table { border-collapse: collapse; width: 100%; margin-top: 24px; background: #f7fafc; }
    th, td { border: 1px solid #eeded1; padding: 8px 6px; }
    th { background: #fbeeea; color: #8e5524; }
    tr:nth-child(even) { background: #fdf4f4; }
    .btn { padding: 5px 15px; border-radius: 5px; border: 0; background: #e67d22; color: #fff; cursor: pointer; }
    .btn-danger { background: #d03030; }
    .btn-add { background: #3bbf72; }
    .small { font-size: 0.95em; color: #888; }
    .inline { display: inline-block; margin-right: 10px; }
    #searchBar { margin-bottom: 15px; padding: 8px; width: 300px; border-radius: 6px; border: 1px solid #e5cbb4; }
    .gallery-img-block { display: inline-flex; align-items: center; margin-right: 16px; margin-bottom: 5px;}
    .gallery-img { max-height: 70px; border-radius: 5px; border:1px solid #eee;}
    .gallery-remove-btn { background: #e74c3c; color: #fff; border: none; border-radius: 3px; padding:3px 8px; margin-left:6px; cursor:pointer; font-size: 0.9em;}
    .logo-img-block { margin-top:5px; display:inline-flex; align-items:center; }
</style>
<h2><?= $editing ? "Edit Restaurant" : "Add Restaurant" ?></h2>
<form method="post" enctype="multipart/form-data" id="restaurantForm">
    <input type="hidden" name="id" value="<?= $editing ? $row['id'] : '' ?>">
    <label>Restaurant Name</label>
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
    <input type="hidden" name="cat_name" value="Restaurant">
    <label>Contact Number</label>
    <input type="text" name="contact" value="<?= htmlspecialchars($editing?$row['contact']:'') ?>">

<input type="hidden" name="form_type" value="restaurant">


    <label>Email Address</label>
    <input type="email" name="email" value="<?= htmlspecialchars($editing?$row['email']:'') ?>">
    <label>Website</label>
    <input type="text" name="website" value="<?= htmlspecialchars($editing?$row['website']:'') ?>">
    <label>Opening Hours</label>
    <input type="text" name="opening_hours" value="<?= htmlspecialchars($editing?$row['opening_hours']:'') ?>">
    <label>Closed On</label>
    <input type="text" name="closed_on" value="<?= htmlspecialchars($editing?$row['closed_on']:'') ?>">
    <label>Reservation Required (Yes/No)</label>
    <input type="text" name="reservation_required" value="<?= htmlspecialchars($editing?$row['reservation_required']:'') ?>">
    <label>Special Offers</label>
    <input type="text" name="special_offers" value="<?= htmlspecialchars($editing?$row['special_offers']:'') ?>">
    <div class="form-section">
        <label>Cuisines <span class="small">(comma-separated)</span></label>
        <input type="text" name="cuisines[]" value="<?= htmlspecialchars($editing?str_replace(',','&#10;',$row['cuisines']??''):'') ?>" placeholder="Indian,Chinese,Italian">
    </div>
    <div class="form-section">
        <label>Menu Highlights <span class="small">(comma-separated)</span></label>
        <input type="text" name="menu[]" value="<?= htmlspecialchars($editing?str_replace(',','&#10;',$row['menu']??''):'') ?>" placeholder="Paneer Makhani,Manchurian,Pasta">
    </div>
    <div class="form-section">
        <label>Services <span class="small">(comma-separated)</span></label>
        <input type="text" name="services[]" value="<?= htmlspecialchars($editing?str_replace(',','&#10;',$row['services']??''):'') ?>" placeholder="Dine-in,Takeaway,Delivery">
    </div>
    <div class="form-section">
        <label>Chefs</label>
        <div id="chefs">
            <?php
            $chefs = $editing ? str2arr($row['chefs']) : ['||'];
            foreach ($chefs as $i=>$chef) {
                list($n,$s,$e) = array_pad(explode('|',$chef),3,'');
            ?>
                <div class="chef-block">
                    <input type="text" name="chef_name[]" placeholder="Name" value="<?= htmlspecialchars($n) ?>">
                    <input type="text" name="chef_spec[]" placeholder="Specialization" value="<?= htmlspecialchars($s) ?>">
                    <input type="text" name="chef_exp[]" placeholder="Experience (years)" value="<?= htmlspecialchars($e) ?>">
                </div>
            <?php } ?>
            <div id="chef-controls">
                <button type="button" onclick="addChef()">Add Chef</button>
                <button type="button" onclick="removeChef()">Remove Chef</button>
            </div>
        </div>
    </div>
    <div class="form-section">
        <label>Amenities & Features</label>
        <div class="yesno inline"><input type="checkbox" name="wifi" value="Wi-Fi" <?= $editing && $row['wifi']=='Wi-Fi'?'checked':'' ?>> Free Wi-Fi</div>
        <div class="yesno inline"><input type="checkbox" name="parking" value="Parking" <?= $editing && $row['parking']=='Parking'?'checked':'' ?>> Parking Available</div>
        <div class="yesno inline"><input type="checkbox" name="outdoor_seating" value="Outdoor Seating" <?= $editing && $row['outdoor_seating']=='Outdoor Seating'?'checked':'' ?>> Outdoor Seating</div>
        <div class="yesno inline"><input type="checkbox" name="live_music" value="Live Music" <?= $editing && $row['live_music']=='Live Music'?'checked':'' ?>> Live Music</div>
        <div class="yesno inline"><input type="checkbox" name="bar" value="Bar" <?= $editing && $row['bar']=='Bar'?'checked':'' ?>> Bar</div>
        <div class="yesno inline"><input type="checkbox" name="kids_friendly" value="Kids Friendly" <?= $editing && $row['kids_friendly']=='Kids Friendly'?'checked':'' ?>> Kids Friendly</div>
        <div class="yesno inline"><input type="checkbox" name="pet_friendly" value="Pet Friendly" <?= $editing && $row['pet_friendly']=='Pet Friendly'?'checked':'' ?>> Pet Friendly</div>
        <div class="yesno inline"><input type="checkbox" name="wheelchair_accessible" value="Wheelchair Accessible" <?= $editing && $row['wheelchair_accessible']=='Wheelchair Accessible'?'checked':'' ?>> Wheelchair Accessible</div>
    </div>
    <div class="form-section">
        <label>Payment Options <span class="small">(comma-separated)</span></label>
        <input type="text" name="payment_options" value="<?= htmlspecialchars($editing?$row['payment_options']:'') ?>" placeholder="Cash,Credit Card,UPI">
    </div>
    <div class="form-section">
        <label>Description</label>
        <textarea name="description" rows="2"><?= htmlspecialchars($editing?$row['description']:'') ?></textarea>
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