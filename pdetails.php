<?php
$host = 'localhost'; $dbname = 'project'; $user = 'root'; $pass = '';
$conn = mysqli_connect($host, $user, $pass, $dbname);
if (!$conn) { die('Connection failed: ' . mysqli_connect_error()); }
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id === 0) { die('Invalid P.G. ID'); }
$res = mysqli_query($conn, "SELECT * FROM pgs WHERE id = $id");
$pg = mysqli_fetch_assoc($res);
if (!$pg) { die('P.G. not found'); }
$pg['room_types'] = $pg['room_types'] ? explode(',', $pg['room_types']) : [];
$pg['facilities'] = $pg['facilities'] ? explode(',', $pg['facilities']) : [];
$pg['gallery'] = $pg['gallery'] ? explode(',', $pg['gallery']) : [];
$pg['faculty'] = $pg['faculty'] ? array_map(function($f){
    list($n,$d,$e)=array_pad(explode('|',$f),3,'');
    return ['name'=>$n,'dept'=>$d,'exp'=>$e];
}, explode(',',$pg['faculty'])) : [];
$pg['scholarships'] = $pg['scholarships'] ? explode(',', $pg['scholarships']) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pg['name']) ?> - P.G. Details</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f6f9fb; margin: 0; padding: 0; color: #222; }
        .containerr { max-width: 980px; margin: 32px auto; background: #fff; border-radius: 18px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); padding: 42px 36px 28px; }
        .headerr { display: flex; align-items: center; border-bottom: 1.5px solid #eee; padding-bottom: 18px; margin-bottom: 0; }
        .pg-logo { width: 150px; height: 150px; border-radius: 50%; object-fit: cover; margin-right: 28px; border: 3px solid #e0e7ef; background: #fff; }
        .pg-title { font-size: 2.3rem; color: #2d6ca2; letter-spacing: 1px; margin-bottom: 4px; }
        .pg-address a { color: #3887ff; text-decoration: none; font-size: 1.08rem; }
        .info-list, .room-types-list, .facilities-list, .faculty-list, .scholarships-list, .gallery-list { list-style: none; padding: 0; margin: 0; }
        .info-list li, .room-types-list li, .facilities-list li, .scholarships-list li, .gallery-list li { margin-bottom: 6px; font-size: 1.08rem; }
        .faculty-list { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 18px; }
        .faculty-card { background: #f8fcff; border-radius: 9px; box-shadow: 0 1px 8px rgba(76,182,224,0.05); padding: 15px 16px; border-left: 4px solid #4cb6e0; }
        .faculty-card .name { font-weight: bold; color: #3578b1; }
        .faculty-card .department { color: #777; margin-bottom: 4px; }
        .facilities-list { display: flex; flex-wrap: wrap; gap: 18px; }
        .facilities-list li { background: #e8f7ee; padding: 8px 14px; border-radius: 6px; font-size: 1.01rem; color: #268b6c; }
        .scholarships-list li { display: inline-block; background: #fff3d1; color: #aa8422; border-radius: 5px; padding: 5px 13px; margin-right: 8px; font-size: 1rem; }
        .gallery-list { display: flex; gap: 14px; }
        .gallery-list img { width: 170px; height: 110px; object-fit: cover; border-radius: 9px; border: 2px solid #d8e6f3; cursor: pointer; transition: box-shadow 0.2s; }
        .gallery-list img:hover { box-shadow: 0 2px 10px rgba(44,162,211,0.23);}
        .description { font-size: 1.09rem; color: #3d3d3d; background: #f3f6fa; padding: 11px 15px; border-radius: 7px; }
        /* NAVBAR TABS STYLE (copied from hospital page) */
        .navbar-tabs {
            display: flex;
            gap: 34px;
            font-size: 1.08rem;
            margin: 0;
            padding: 0 0 0 2px;
            border-bottom: 1px solid #e3e8ef;
            background: none;
        }
        .navbar-tabs button {
            background: none;
            border: none;
            color: #2d6ca2;
            font-weight: 600;
            font-size: 1.08rem;
            padding: 18px 0 12px 0;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            transition: color 0.2s, border-bottom 0.2s;
            outline: none;
        }
        .navbar-tabs button.active {
            color: #3887ff;
            border-bottom: 2px solid #3887ff;
        }
        .section { margin-bottom: 28px; }
        .section-title { font-size: 1.4rem; color: #1d4d72; margin-bottom: 14px; border-left: 6px solid #4cb6e0; padding-left: 12px; }
        .tab-section { display: none; }
        .tab-section.active { display: block; }
        /* Gallery popup overlay */
        .gallery-popup-overlay {
            display: none;
            position: fixed; top:0; left:0; width:100vw; height:100vh; background:rgba(30,34,40,0.85); z-index:1000;
            align-items: center; justify-content: center;
        }
        .gallery-popup-overlay.active { display: flex;}
        .gallery-popup-img {
            max-width: 92vw;
            max-height: 82vh;
            border-radius: 12px;
            box-shadow: 0 8px 40px rgba(0,0,0,0.20);
            background: #fff;
        }
        .gallery-popup-close {
            position: absolute; top: 36px; right: 46px;
            font-size: 2.2rem; color: #fff; background: none; border: none; cursor:pointer; z-index:1100;
            font-family: Arial, sans-serif;
            line-height: 1;
        }
        @media (max-width: 600px) {
            .navbar-tabs { flex-direction: column; gap: 0; padding: 0 8px;}
            .navbar-tabs button { padding: 12px 0; }
            .containerr { padding: 12px 4px; }
            .headerr { flex-direction: column; align-items: flex-start; }
            .pg-logo { margin-bottom: 10px; }
            .facilities-list, .gallery-list { flex-direction: column; gap: 8px; }
            .faculty-list { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>
<div class="containerr">
    <div class="headerr">
        <img src="images/<?= htmlspecialchars($pg['img']) ?>" alt="P.G. Logo" class="pg-logo">
        <div>
            <div class="pg-title"><?= htmlspecialchars($pg['name']) ?></div>
            <div class="pg-address">
                <?= htmlspecialchars($pg['address']) ?> &#x1F5FA;
                <a href="https://maps.google.com/?q=<?= urlencode($pg['name']) ?>,<?= urlencode($pg['city_name']) ?>" target="_blank">View on Map</a>
            </div>
            <ul class="info-list">
                <li>📞 <b>Contact:</b> <a href="tel:<?= htmlspecialchars($pg['contact']) ?>" onclick="event.stopPropagation();"><?= htmlspecialchars($pg['contact']) ?></a></li>
                <li>✉️ <b>Email:</b> <a href="mailto:<?= htmlspecialchars($pg['email']) ?>"><?= htmlspecialchars($pg['email']) ?></a></li>
                <li>🌐 <b>Website:</b> <?php if($pg['website']): ?><a href="<?= htmlspecialchars($pg['website']) ?>" target="_blank"><?= htmlspecialchars($pg['website']) ?></a><?php endif; ?></li>
                <li><b>Established:</b> <?= htmlspecialchars($pg['established']) ?></li>
                <li><b>Total Rooms:</b> <?= htmlspecialchars($pg['rooms']) ?></li>
            </ul>
        </div>
    </div>
    <!-- NAVBAR TABS -->
    <nav>
        <div class="navbar-tabs" id="navbarTabs">
            <button class="active" data-tab="staffTab">Staff</button>
            <button data-tab="roomTab">Room Types & Facilities</button>
            <button data-tab="admissionTab">Admission & Scholarships</button>
            <button data-tab="galleryTab">Gallery</button>
        </div>
    </nav>
    <p></p>
    <!-- TAB SECTIONS -->
    <div class="section tab-section active" id="staffTab">
        <div class="section-title">Staff</div>
        <div class="faculty-list">
            <?php foreach($pg['faculty'] as $fac): ?>
                <div class="faculty-card">
                    <div class="name"><?= htmlspecialchars($fac['name']) ?></div>
                    <div class="department"><?= htmlspecialchars($fac['dept']) ?></div>
                    <div><?= htmlspecialchars($fac['exp']) ?></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="section tab-section" id="roomTab">
        <div class="section-title">Room Types</div>
        <ul class="room-types-list">
            <?php foreach($pg['room_types'] as $type): ?>
                <li>• <?= htmlspecialchars($type) ?></li>
            <?php endforeach; ?>
        </ul>
        <div class="section-title" style="margin-top:22px;">Facilities</div>
        <ul class="facilities-list">
            <?php foreach($pg['facilities'] as $fac): ?>
                <li><?= htmlspecialchars($fac) ?></li>
            <?php endforeach; ?>
            <?php if($pg['hostel']) echo '<li>Food</li>'; ?>
            <?php if($pg['transport']) echo '<li>Kitchen</li>'; ?>
            <?php if($pg['library']) echo '<li>Library</li>'; ?>
            <?php if($pg['sports']) echo '<li>Sports</li>'; ?>
            <?php if($pg['cafeteria']) echo '<li>Cafeteria</li>'; ?>
            <?php if($pg['disabled_friendly']) echo '<li>Disabled Friendly</li>'; ?>
        </ul>
    </div>
    <div class="section tab-section" id="admissionTab">
        <div class="section-title">Admission Process</div>
        <div class="description"><?= htmlspecialchars($pg['admission_process']) ?></div>
        <div class="section-title" style="margin-top:22px;">Scholarships/Discounts</div>
        <ul class="scholarships-list">
            <?php foreach($pg['scholarships'] as $sch): ?>
                <li><?= htmlspecialchars($sch) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="section tab-section" id="galleryTab">
        <div class="section-title">Photo Gallery</div>
        <ul class="gallery-list">
            <?php foreach($pg['gallery'] as $img): ?>
                <li>
                    <img src="images/<?= htmlspecialchars($img) ?>" alt="Gallery Image" class="gallery-img" data-src="images/<?= htmlspecialchars($img) ?>">
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<?php include 'includes/footer.php'; ?>

<!-- Modal for Large Photo -->
<div id="imgModal" class="gallery-popup-overlay">
    <button class="gallery-popup-close" onclick="closeModal()">&times;</button>
    <img src="" alt="Large Photo" class="gallery-popup-img" id="modalImg">
</div>
<script>
    // TAB FUNCTIONALITY
    document.querySelectorAll('.navbar-tabs button').forEach(function(btn){
        btn.addEventListener('click', function(){
            document.querySelectorAll('.navbar-tabs button').forEach(function(b){ b.classList.remove('active'); });
            btn.classList.add('active');
            var tabId = btn.getAttribute('data-tab');
            document.querySelectorAll('.tab-section').forEach(function(sec){
                sec.classList.remove('active');
            });
            document.getElementById(tabId).classList.add('active');
        });
    });

    // Photo popup
    document.querySelectorAll('.gallery-img').forEach(function(img){
        img.addEventListener('click', function(){
            document.getElementById('modalImg').src = this.getAttribute('data-src');
            document.getElementById('imgModal').classList.add('active');
        });
    });
    function closeModal(){
        document.getElementById('imgModal').classList.remove('active');
        document.getElementById('modalImg').src='';
    }
    document.getElementById('imgModal').onclick = function(event){
        if(event.target === this){ closeModal(); }
    }
    // Optional: close on ESC
    document.addEventListener('keydown', function(e){
        if(e.key==='Escape') {
            closeModal();
        }
    });
</script>
</body>
</html>