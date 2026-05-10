<?php
$host = 'localhost'; $dbname = 'project'; $user = 'root'; $pass = '';
$conn = mysqli_connect($host, $user, $pass, $dbname);
if (!$conn) { die('Connection failed: ' . mysqli_connect_error()); }
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
if ($id === 0) { die('Invalid Place ID'); }
$res = mysqli_query($conn, "SELECT * FROM h_p WHERE id = $id");
$place = mysqli_fetch_assoc($res);
if (!$place) { die('Place not found'); }
$place['features'] = $place['features'] ? explode(',', $place['features']) : [];
$place['guides'] = $place['guides'] ? explode(',', $place['guides']) : [];
$place['gallery'] = $place['gallery'] ? explode(',', $place['gallery']) : [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($place['name']) ?> - Historical Place Details</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f6f9fb; margin: 0; padding: 0; color: #222; }
        .containerr { max-width: 980px; margin: 32px auto; background: #fff; border-radius: 18px; box-shadow: 0 4px 24px rgba(0,0,0,0.08); padding: 42px 36px 28px; }
        .headerr { display: flex; align-items: center; border-bottom: 1.5px solid #eee; padding-bottom: 18px; margin-bottom: 18px; }
        .place-logo { width: 150px; height: 150px; border-radius: 50%; object-fit: cover; margin-right: 28px; border: 3px solid #e0e7ef; background: #fff; }
        .place-title { font-size: 2.3rem; color: #2d6ca2; letter-spacing: 1px; margin-bottom: 4px; }
        .place-address a { color: #3887ff; text-decoration: none; font-size: 1.08rem; }
        .section { margin-bottom: 28px; }
        .section-title { font-size: 1.4rem; color: #1d4d72; margin-bottom: 14px; border-left: 6px solid #4cb6e0; padding-left: 12px; }
        .info-list, .features-list, .guides-list, .gallery-list { list-style: none; padding: 0; margin: 0; }
        .info-list li, .features-list li, .guides-list li, .gallery-list li { margin-bottom: 6px; font-size: 1.08rem; }
        .features-list { display: flex; flex-wrap: wrap; gap: 18px; }
        .features-list li { background: #e8f7ee; padding: 8px 14px; border-radius: 6px; font-size: 1.01rem; color: #268b6c; }
        .guides-list li { display: inline-block; background: #fff3d1; color: #aa8422; border-radius: 5px; padding: 5px 13px; margin-right: 8px; font-size: 1rem; }
        .gallery-list { display: flex; gap: 14px; }
        .gallery-list img { width: 170px; height: 110px; object-fit: cover; border-radius: 9px; border: 2px solid #d8e6f3; }
        .description { font-size: 1.09rem; color: #3d3d3d; background: #f3f6fa; padding: 11px 15px; border-radius: 7px; }
        @media (max-width: 600px) { .containerr { padding: 12px 4px; } .headerr { flex-direction: column; align-items: flex-start; } .place-logo { margin-bottom: 10px; } .features-list, .gallery-list { flex-direction: column; gap: 8px; } }
    </style>
</head>
<body>
<?php include 'includes/header.php'; ?>
<div class="containerr">
    <div class="headerr">
        <img src="images/<?= htmlspecialchars($place['img']) ?>" alt="Place Logo" class="place-logo">
        <div>
            <div class="place-title"><?= htmlspecialchars($place['name']) ?></div>
            <div class="place-address">
                <?= htmlspecialchars($place['address']) ?> &#x1F5FA;
                <a href="https://maps.google.com/?q=<?= urlencode($place['name']) ?>,<?= urlencode($place['city_name']) ?>" target="_blank">View on Map</a>
            </div>
            <ul class="info-list">
                <li>📞 <b>Contact:</b> <a href="tel:<?= htmlspecialchars($place['contact']) ?>" onclick="event.stopPropagation();"><?= htmlspecialchars($place['contact']) ?></a></li>
                <li>✉️ <b>Email:</b> <a href="mailto:<?= htmlspecialchars($place['email']) ?>"><?= htmlspecialchars($place['email']) ?></a></li>
                <li>🌐 <b>Website:</b> <?php if($place['website']): ?><a href="<?= htmlspecialchars($place['website']) ?>" target="_blank"><?= htmlspecialchars($place['website']) ?></a><?php endif; ?></li>
                <li><b>Established:</b> <?= htmlspecialchars($place['established']) ?></li>
                <li><b>Opening Hours:</b> <?= htmlspecialchars($place['opening_hours']) ?></li>
                <li><b>Ticket Info:</b> <?= htmlspecialchars($place['ticket_info']) ?></li>
            </ul>
        </div>
    </div>
    <div class="section">
        <div class="section-title">Features</div>
        <ul class="features-list">
            <?php foreach($place['features'] as $feature): ?>
                <li><?= htmlspecialchars($feature) ?></li>
            <?php endforeach; ?>
            <?php if($place['hostel']) echo '<li>Help Desk</li>'; ?>
            <?php if($place['transport']) echo '<li>Parking Area</li>'; ?>
            <?php if($place['library']) echo '<li>Restrooms & Toilets</li>'; ?>
            <?php if($place['sports']) echo '<li>Drinking Water Stations</li>'; ?>
            <?php if($place['cafeteria']) echo '<li>Seating Areas & Rest Zones</li>'; ?>
            <?php if($place['disabled_friendly']) echo '<li>First Aid & Medical Assistance</li>'; ?>
        </ul>
    </div>
    <div class="section">
        <div class="section-title">Guides</div>
        <ul class="guides-list">
            <?php foreach($place['guides'] as $guide): ?>
                <li><?= htmlspecialchars($guide) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <div class="section">
        <div class="section-title">Photo Gallery</div>
        <ul class="gallery-list">
            <?php foreach($place['gallery'] as $img): ?>
                <li><img src="images/<?= htmlspecialchars($img) ?>" alt="Gallery Image"></li>
            <?php endforeach; ?>
        </ul>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
</body>
</html>