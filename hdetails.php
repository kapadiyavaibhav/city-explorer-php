<?php

// ==== DB CONNECTION ====
$host = 'localhost';
$dbname = 'project';
$user = 'root';
$pass = '';
$conn = mysqli_connect($host, $user, $pass, $dbname);
if (!$conn) { die('Connection failed: ' . mysqli_connect_error()); }

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id === 0) { die('Invalid hospital ID'); }

$res = mysqli_query($conn, "SELECT * FROM hospitals WHERE id = $id");
$hospital = mysqli_fetch_assoc($res);
if (!$hospital) { die('Hospital not found'); }

$hospital['departments'] = $hospital['departments'] ? explode(',', $hospital['departments']) : [];
$hospital['services'] = $hospital['services'] ? explode(',', $hospital['services']) : [];
$hospital['gallery'] = $hospital['gallery'] ? explode(',', $hospital['gallery']) : [];
$hospital['doctors'] = $hospital['doctors'] ? array_map(function($d){
    list($n,$s,$q)=array_pad(explode('|',$d),3,'');
    return ['name'=>$n,'specialization'=>$s,'qualifications'=>$q];
}, explode(',',$hospital['doctors'])) : [];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>City Explorer - Your Ultimate Guide to Urban Adventures</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f6f9fb; margin: 0; padding: 0; color: #222; }
        .containerr { max-width: 980px; margin: 32px auto; background: #fff; border-radius: 18px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); padding: 42px 36px 28px; }
        .headerr { display: flex; align-items: center; border-bottom: 1.5px solid #eee; padding-bottom: 18px; margin-bottom: 0; }
        .hospital-logo { width: 150px; height: 150px; border-radius: 50%; object-fit: cover; margin-right: 28px; border: 3px solid #e0e7ef; background: #fff; }
        .hospital-title { font-size: 2.3rem; color: #2d6ca2; letter-spacing: 1px; margin-bottom: 4px; }
        .hospital-address a { color: #3887ff; text-decoration: none; font-size: 1.08rem; }
        .info-list, .departments-list, .services-list, .beds-list, .doctors-list, .facilities-list, .insurance-list, .gallery-list { list-style: none; padding: 0; margin: 0; }
        .info-list li, .departments-list li, .services-list li, .beds-list li, .facilities-list li, .insurance-list li, .gallery-list li { margin-bottom: 6px; font-size: 1.08rem; }
        /* NAVBAR TABS STYLE */
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
        .section-title { font-size: 1.3rem; color: #1d4d72; margin-bottom: 14px; border-left: 6px solid #4cb6e0; padding-left: 12px; }
        .doctors-list { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 18px; }
        .doctor-card { background: #f8fcff; border-radius: 9px; box-shadow: 0 1px 8px rgba(76,182,224,0.05); padding: 15px 16px; border-left: 4px solid #4cb6e0; }
        .doctor-card .name { font-weight: bold; color: #3578b1; }
        .doctor-card .specialization { color: #777; margin-bottom: 4px; }
        .beds-list { display: flex; gap: 32px; }
        .beds-list li { background: #eaf6fa; padding: 8px 18px; border-radius: 6px; font-weight: bold; color: #2277aa; }
        .facilities-list { display: flex; flex-wrap: wrap; gap: 18px; }
        .facilities-list li { background: #e8f7ee; padding: 8px 14px; border-radius: 6px; font-size: 1.01rem; color: #268b6c; }
        .insurance-list li { display: inline-block; background: #fff3d1; color: #aa8422; border-radius: 5px; padding: 5px 13px; margin-right: 8px; font-size: 1rem; }
        .gallery-list { display: flex; gap: 14px; flex-wrap: wrap; }
        .gallery-list img { width: 170px; height: 110px; object-fit: cover; border-radius: 9px; border: 2px solid #d8e6f3; cursor: pointer; transition: transform 0.2s; }
        .gallery-list img:hover { transform: scale(1.06); }
        .history { font-size: 1.09rem; color: #3d3d3d; background: #f3f6fa; padding: 11px 15px; border-radius: 7px; }
        .emergency-procedures { background: #ffecec; color: #b60f0f; padding: 13px 18px; border-radius: 8px; font-size: 1.07rem; border-left: 6px solid #fa5a5a; margin-bottom: 18px; }
        /* Popup Modal */
        .modal { display: none; position: fixed; z-index: 9999; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background: rgba(36,48,60,0.81); }
        .modal-content { background: #fff; margin: 60px auto; padding: 20px 28px; border-radius: 16px; max-width: 520px; box-shadow: 0 6px 32px rgba(0,0,0,0.18); position: relative; }
        .modal-content img { width: 100%; height: auto; border-radius: 12px; }
        .close { color: #d22; position: absolute; top: 1px; right: 5px; font-size: 2rem; font-weight: bold; cursor: pointer; }
        .tab-section { display: none; }
        .tab-section.active { display: block; }
        @media (max-width: 600px) {
            .navbar-tabs { flex-direction: column; gap: 0; padding: 0 8px;}
            .navbar-tabs button { padding: 12px 0; }
            .containerr { padding: 12px 4px; }
            .headerr { flex-direction: column; align-items: flex-start; }
            .hospital-logo { margin-bottom: 10px; }
            .beds-list, .facilities-list, .gallery-list { flex-direction: column; gap: 8px; }
            .doctors-list { grid-template-columns: 1fr; }
            .modal-content { padding: 8px; }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="containerr">

        <div class="headerr">
            <img src="images/<?= htmlspecialchars($hospital['img']) ?>" alt="Hospital Logo" class="hospital-logo">
            <div>
                <div class="hospital-title"><?= htmlspecialchars($hospital['name']) ?></div>
                <div class="hospital-address">
                    <?= htmlspecialchars($hospital['address']) ?> &#x1F5FA;
                    <a href="https://maps.google.com/?q=<?= urlencode($hospital['name']) ?>,<?= urlencode($hospital['city_name']) ?>" target="_blank">View on Map</a>
                </div>
                <ul class="info-list">
                    <li>📞 <b>Contact:</b> <a href="tel:<?= htmlspecialchars($hospital['contact']) ?>" onclick="event.stopPropagation();"><?= htmlspecialchars($hospital['contact']) ?></a></li>
                    <li>✉️ <b>Email:</b> <a href="mailto:<?= htmlspecialchars($hospital['email']) ?>"><?= htmlspecialchars($hospital['email']) ?></a></li>
                </ul>

               
            </div>
        </div>
 <nav>
                    <div class="navbar-tabs" id="navbarTabs">
                        <button class="active" data-tab="doctorsTab">Doctors</button>
                        <button data-tab="operationalTab">Operational Details</button>
                        <button data-tab="edsTab">Emergency & Departments</button>
                        <button data-tab="facilitiesTab">Facilities & Beds</button>
                        <button data-tab="galleryTab">Photos & History</button>
                    </div>
                </nav>
                <p></p>
        <!-- Doctors Section -->
        <div class="section tab-section active" id="doctorsTab">
            <div class="section-title">Doctors</div>
            <div class="doctors-list">
                <?php foreach($hospital['doctors'] as $doc): ?>
                    <div class="doctor-card">
                        <div class="name"><?= htmlspecialchars($doc['name']) ?></div>
                        <div class="specialization"><?= htmlspecialchars($doc['specialization']) ?></div>
                        <div>🎓 <?= htmlspecialchars($doc['qualifications']) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Operational Details Section -->
        <div class="section tab-section" id="operationalTab">
            <div class="section-title">Operational Details</div>
            <ul class="info-list">
                <li><b>Opening Hours:</b> <?= htmlspecialchars($hospital['opening_hours']) ?></li>
                <li><b>Emergency Department:</b> <?= htmlspecialchars($hospital['emergency']) ?></li>
                <li><b>Visiting Hours:</b> <?= htmlspecialchars($hospital['visiting_hours']) ?></li>
            </ul>
        </div>
        
        <!-- Emergency, Departments, Services Provided Section -->
        <div class="section tab-section" id="edsTab">
            <div class="section-title">Emergency Procedures</div>
            <div class="emergency-procedures">In case of emergency, please proceed to the Emergency Room at the main entrance or call our 24/7 helpline at <?= htmlspecialchars($hospital['emergency_procedures']) ?>. Immediate triage and care are provided upon arrival.</div>
            <div class="section-title" style="margin-top:28px;">Departments & Specialties</div>
            <ul class="departments-list">
                <?php foreach($hospital['departments'] as $dept): ?>
                    <li>• <?= htmlspecialchars($dept) ?></li>
                <?php endforeach; ?>
            </ul>
            <div class="section-title" style="margin-top:28px;">Services Provided</div>
            <ul class="services-list">
                <?php foreach($hospital['services'] as $srv): ?>
                    <li>• <?= htmlspecialchars($srv) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        
        <!-- Facilities, Beds, Insurance Section -->
        <div class="section tab-section" id="facilitiesTab">
            <div class="section-title">Facilities & Amenities</div>
            <ul class="facilities-list">
                <?php if($hospital['laboratory']) echo '<li>Laboratory</li>'; ?>
                <?php if($hospital['diagnostics']) echo '<li>Diagnostics</li>'; ?>
                <?php if($hospital['pharmacy']) echo '<li>Pharmacy</li>'; ?>
                <?php if($hospital['ambulance']) echo '<li>Ambulance Services</li>'; ?>
                <?php if($hospital['wheelchair_accessible']) echo '<li>Wheelchair Accessible</li>'; ?>
            </ul>
            <div class="section-title" style="margin-top:28px;">Beds & Room Types</div>
            <ul class="beds-list">
                <li>General: <?= intval($hospital['beds_general']) ?></li>
                <li>ICU: <?= intval($hospital['beds_icu']) ?></li>
                <li>Private: <?= intval($hospital['beds_private']) ?></li>
            </ul>
            <div class="section-title" style="margin-top:28px;">Insurance Accepted</div>
            <ul class="insurance-list">
                <li><?= htmlspecialchars($hospital['insurance']) ?></li>
            </ul>
        </div>

        <!-- Gallery & History Section -->
        <div class="section tab-section" id="galleryTab">
            <div class="section-title">Photo Gallery</div>
            <ul class="gallery-list">
                <?php foreach($hospital['gallery'] as $img): ?>
                    <li>
                        <img src="images/<?= htmlspecialchars($img) ?>" alt="Gallery Image" class="gallery-img" data-src="images/<?= htmlspecialchars($img) ?>">
                    </li>
                <?php endforeach; ?>
            </ul>
            <div class="section-title" style="margin-top:28px;">Hospital History</div>
            <div class="history"> <?= htmlspecialchars($hospital['history']) ?> </div>
        </div>
        
    </div>
    <?php include 'includes/footer.php'; ?>

    <!-- Modal for Large Photo -->
    <div id="imgModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <img id="modalImg" src="" alt="Large Photo">
        </div>
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
                // Optional: Scroll to navbar if needed
                document.getElementById('navbarTabs')/*.scrollIntoView({behavior:'smooth',- block:'start'});*/
            });
        });

        // Photo popup
        document.querySelectorAll('.gallery-img').forEach(function(img){
            img.addEventListener('click', function(){
                document.getElementById('modalImg').src = this.getAttribute('data-src');
                document.getElementById('imgModal').style.display = 'block';
            });
        });
        function closeModal(){
            document.getElementById('imgModal').style.display='none';
            document.getElementById('modalImg').src='';
        }
        window.onclick = function(event){
            var modal = document.getElementById('imgModal');
            if(event.target == modal){ closeModal(); }
        }
    </script>
</body>
</html>