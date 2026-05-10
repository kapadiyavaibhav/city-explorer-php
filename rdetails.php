<?php

// ==== DB CONNECTION ====
$host = 'localhost';
$dbname = 'project';
$user = 'root';
$pass = '';
$conn = mysqli_connect($host, $user, $pass, $dbname);
if (!$conn) { die('Connection failed: ' . mysqli_connect_error()); }

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id === 0) { die('Invalid restaurant ID'); }

$res = mysqli_query($conn, "SELECT * FROM restaurants WHERE id = $id");
$restaurant = mysqli_fetch_assoc($res);
if (!$restaurant) { die('Restaurant not found'); }

// Parse comma-separated or JSON fields if needed
$restaurant['cuisines'] = $restaurant['cuisines'] ? explode(',', $restaurant['cuisines']) : [];
$restaurant['services'] = $restaurant['services'] ? explode(',', $restaurant['services']) : [];
$restaurant['gallery'] = $restaurant['gallery'] ? explode(',', $restaurant['gallery']) : [];
$restaurant['menu'] = $restaurant['menu'] ? explode(',', $restaurant['menu']) : [];
$restaurant['chefs'] = $restaurant['chefs'] ? array_map(function($c){
    list($n,$s,$exp)=array_pad(explode('|',$c),3,'');
    return ['name'=>$n,'specialization'=>$s,'experience'=>$exp];
}, explode(',',$restaurant['chefs'])) : [];

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($restaurant['name']) ?> - Restaurant Details</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f6f9fb; margin: 0; padding: 0; color: #222; }
        .containerr { max-width: 980px; margin: 32px auto; background: #fff; border-radius: 18px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); padding: 42px 36px 28px; }
        .headerr { display: flex; align-items: center; border-bottom: 1.5px solid #eee; padding-bottom: 18px; margin-bottom: 0; }
        .restaurant-logo { width: 150px; height: 150px; border-radius: 50%; object-fit: cover; margin-right: 28px; border: 3px solid #e0e7ef; background: #fff; }
        .restaurant-title { font-size: 2.3rem; color: #c85a2e; letter-spacing: 1px; margin-bottom: 4px; }
        .restaurant-address a { color: #e67d22; text-decoration: none; font-size: 1.08rem; }
        .info-list, .cuisines-list, .services-list, .chefs-list, .menu-list, .amenities-list, .gallery-list, .payment-list { list-style: none; padding: 0; margin: 0; }
        .info-list li, .cuisines-list li, .services-list li, .menu-list li, .amenities-list li, .payment-list li, .gallery-list li { margin-bottom: 6px; font-size: 1.08rem; }
        .chefs-list { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 18px; }
        .chef-card { background: #fff6f0; border-radius: 9px; box-shadow: 0 1px 8px rgba(230,125,34,0.05); padding: 15px 16px; border-left: 4px solid #e67d22; }
        .chef-card .name { font-weight: bold; color: #e67d22; }
        .chef-card .specialization { color: #777; margin-bottom: 4px; }
        .amenities-list { display: flex; flex-wrap: wrap; gap: 18px; }
        .amenities-list li { background: #e8f7ee; padding: 8px 14px; border-radius: 6px; font-size: 1.01rem; color: #268b6c; }
        .payment-list li { display: inline-block; background: #fff3d1; color: #aa8422; border-radius: 5px; padding: 5px 13px; margin-right: 8px; font-size: 1rem; }
        .gallery-list { display: flex; gap: 14px; flex-wrap: wrap;}
        .gallery-list img { width: 170px; height: 110px; object-fit: cover; border-radius: 9px; border: 2px solid #d8e6f3; cursor: pointer; transition: box-shadow 0.2s; }
        .gallery-list img:hover { box-shadow: 0 2px 10px rgba(44,162,211,0.23);}
        .description { font-size: 1.09rem; color: #3d3d3d; background: #f3f6fa; padding: 11px 15px; border-radius: 7px; }
        .special-offers { background: #ecf8ff; color: #0b4176; padding: 13px 18px; border-radius: 8px; font-size: 1.07rem; border-left: 6px solid #4cb6e0; }

        /* Navbar tabs style (same as PG/College/School details) */
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
            color: #c85a2e;
            font-weight: 600;
            font-size: 1.08rem;
            padding: 18px 0 12px 0;
            cursor: pointer;
            border-bottom: 2px solid transparent;
            transition: color 0.2s, border-bottom 0.2s;
            outline: none;
        }
        .navbar-tabs button.active {
            color: #e67d22;
            border-bottom: 2px solid #e67d22;
        }
        .tab-section { display: none; }
        .tab-section.active { display: block; }
        .section { margin-bottom: 28px; }
        .section-title {
            font-size: 1.4rem;
            color: #7d2d19;
            margin-bottom: 14px;
            border-left: 6px solid #e67d22;
            padding-left: 12px;
        }
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
            .restaurant-logo { margin-bottom: 10px; }
            .amenities-list, .gallery-list { flex-direction: column; gap: 8px; }
            .chefs-list { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
    <?php include 'includes/header.php'; ?>
    <div class="containerr">
        <div class="headerr">
            <img src="images/<?= htmlspecialchars($restaurant['img']) ?>" alt="Restaurant Logo" class="restaurant-logo">
            <div>
                <div class="restaurant-title"><?= htmlspecialchars($restaurant['name']) ?></div>
                <div class="restaurant-address">
                    <?= htmlspecialchars($restaurant['address']) ?> &#x1F4CD;
                    <a href="https://maps.google.com/?q=<?= urlencode($restaurant['name']) ?>,<?= urlencode($restaurant['city_name']) ?>" target="_blank">View on Map</a>
                </div>
                <ul class="info-list">
                    <li>📞 <b>Contact:</b> <a href="tel:<?= htmlspecialchars($restaurant['contact']) ?>" onclick="event.stopPropagation();"><?= htmlspecialchars($restaurant['contact']) ?></a></li>
                    <li>✉️ <b>Email:</b> <a href="mailto:<?= htmlspecialchars($restaurant['email']) ?>"><?= htmlspecialchars($restaurant['email']) ?></a></li>
                    <li>🌐 <b>Website:</b> <?php if($restaurant['website']): ?><a href="<?= htmlspecialchars($restaurant['website']) ?>" target="_blank"><?= htmlspecialchars($restaurant['website']) ?></a><?php endif; ?></li>
                </ul>
            </div>
        </div>

        <!-- NAVBAR TABS -->
        <nav>
            <div class="navbar-tabs" id="navbarTabs">
                <button data-tab="operationalTab">Operational Details</button>
                <button class="active" data-tab="chefsTab">Chefs</button>
                <button data-tab="offersTab">Special Offers & Cuisines & Menu Highlights</button>
                <button data-tab="servicesTab">Services & Payment Options & Amenities & Features</button>
                <button data-tab="descTab">Description & Gallery</button>
            </div>
        </nav>
        <p></p>
        <!-- TAB SECTIONS -->
        <div class="section tab-section active" id="chefsTab">
            <div class="section-title">Chefs</div>
            <div class="chefs-list">
                <?php foreach($restaurant['chefs'] as $chef): ?>
                    <div class="chef-card">
                        <div class="name">Name: <?= htmlspecialchars($chef['name']) ?></div>
                        <div class="specialization">Specialist: <?= htmlspecialchars($chef['specialization']) ?></div>
                        <div>👨‍🍳 Experience: <?= htmlspecialchars($chef['experience']) ?> years</div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="section tab-section" id="operationalTab">
            <div class="section-title">Operational Details</div>
            <ul class="info-list">
                <li><b>Opening Hours:</b> <?= htmlspecialchars($restaurant['opening_hours']) ?></li>
                <li><b>Closed On:</b> <?= htmlspecialchars($restaurant['closed_on']) ?></li>
                <li><b>Reservation Required:</b> <?= htmlspecialchars($restaurant['reservation_required']) ?></li>
            </ul>
        </div>

        <div class="section tab-section" id="offersTab">
            <div class="section-title">Special Offers</div>
            <div class="special-offers"><?= htmlspecialchars($restaurant['special_offers']) ?></div>
            <div class="section-title" style="margin-top:22px;">Cuisines</div>
            <ul class="cuisines-list">
                <?php foreach($restaurant['cuisines'] as $cuisine): ?>
                    <li>• <?= htmlspecialchars($cuisine) ?></li>
                <?php endforeach; ?>
            </ul>
            <div class="section-title" style="margin-top:22px;">Menu Highlights</div>
            <ul class="menu-list">
                <?php foreach($restaurant['menu'] as $menu): ?>
                    <li>• <?= htmlspecialchars($menu) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="section tab-section" id="servicesTab">
            <div class="section-title">Services</div>
            <ul class="services-list">
                <?php foreach($restaurant['services'] as $srv): ?>
                    <li>• <?= htmlspecialchars($srv) ?></li>
                <?php endforeach; ?>
            </ul>
            <div class="section-title" style="margin-top:22px;">Amenities & Features</div>
            <ul class="amenities-list">
                <?php if($restaurant['wifi']) echo '<li>Free Wi-Fi</li>'; ?>
                <?php if($restaurant['parking']) echo '<li>Parking Available</li>'; ?>
                <?php if($restaurant['outdoor_seating']) echo '<li>Outdoor Seating</li>'; ?>
                <?php if($restaurant['live_music']) echo '<li>Live Music</li>'; ?>
                <?php if($restaurant['bar']) echo '<li>Bar</li>'; ?>
                <?php if($restaurant['kids_friendly']) echo '<li>Kids Friendly</li>'; ?>
                <?php if($restaurant['pet_friendly']) echo '<li>Pet Friendly</li>'; ?>
                <?php if($restaurant['wheelchair_accessible']) echo '<li>Wheelchair Accessible</li>'; ?>
            </ul>
            <div class="section-title" style="margin-top:22px;">Payment Options</div>
            <ul class="payment-list">
                <?php foreach(explode(',', $restaurant['payment_options']) as $pay): ?>
                    <li><?= htmlspecialchars($pay) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>

        <div class="section tab-section" id="descTab">
            <div class="section-title">Description</div>
            <div class="description"><?= htmlspecialchars($restaurant['description']) ?></div>
            <div class="section-title" style="margin-top:22px;">Photo Gallery</div>
            <ul class="gallery-list">
                <?php foreach($restaurant['gallery'] as $img): ?>
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