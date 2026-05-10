<?php
include 'includes/header.php'; 
$city = $_REQUEST['city'] ?? '';
$type = $_REQUEST['type'] ?? '';
include 'includes/conf.php';
$sql = "SELECT * FROM restaurants WHERE city_name='$city'" . ($type ? " AND cat_name='$type'" : "");
$records = mysqli_query($conn, $sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>City Explorer - Restaurants</title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
    <style>
    body { background: #f5f5f5; font-family: 'Open Sans', Arial, sans-serif; }
    .listing-container {
      max-width: 950px;
      margin: 40px auto;
      background: #fff;
      border-radius: 18px;
      box-shadow: 0 4px 16px rgba(0,0,0,0.09);
      padding: 30px 16px;
    }
    .listing-card-wrapper {
      margin-bottom: 32px;
      position: relative;
    }
    .listing-card {
      display: flex;
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.07);
      padding: 18px 16px;
      align-items: stretch;
      position: relative;
      transition: box-shadow 0.18s, border 0.18s;
      border: 2px solid transparent;
      min-height: 150px;
      cursor: pointer;
    }
    .listing-card:hover {
      box-shadow: 0 4px 18px rgba(230, 125, 34, 0.14);
      border: 2px solid #e67d22;
      background: #fff9f5;
    }
    .listing-left {
      display: flex;
      align-items: center;
      justify-content: center;
      width: 130px;
      flex-shrink: 0;
      margin-right: 24px;
    }
    .listing-image img {
      width: 150px;
      height: 150px;
      object-fit: contain;
      border-radius: 8px;
      background: #eee;
      border: 1.5px solid #e2e2e2;
      display: block;
      margin: 0 auto;
    }
    .listing-center {
      flex: 1.2;
      display: flex;
      flex-direction: column;
      justify-content: center;
      margin-right: 24px;
    }
    .restaurant-title {
      font-size: 1.18em;
      font-weight: 700;
      color: #c85a2e;
      margin-bottom: 7px;
    }
    .restaurant-address-row {
      display: flex;
      align-items: center;
      margin-bottom: 6px;
      color: #444;
      font-size: 0.98em;
    }
    .restaurant-address {
      display: inline-block;
    }
    .restaurant-map-link {
      color: #e67d22;
      text-decoration: underline;
      margin-left: 10px;
      font-weight: 600;
      font-size: 0.98em;
      white-space: nowrap;
    }
    .contact-list {
      list-style: none;
      padding: 0;
      margin: 0 0 4px 0;
      font-size: 0.98em;
      color: #444;
    }
    .contact-list li {
      margin-bottom: 3px;
    }
    .contact-label {
      color: black;
      font-weight: 600;
      margin-right: 4px;
    }
    .listing-right {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      min-width: 220px;
    }
    .field-row {
      margin-bottom: 7px;
      font-size: 0.98em;
      color: #444;
    }
    .field-label {
      color: black;
      font-weight: 600;
      margin-right: 6px;
    }
    .overlay-link {
      position: absolute;
      inset: 0;
      z-index: 1;
    }
    .restaurant-map-link, .contact-list a {
      pointer-events: auto;
      cursor: pointer;
      position: relative;
      z-index: 2;
    }
    .listing-card *:not(.restaurant-map-link):not(.contact-list a) {
      z-index: 2;
    }
    @media (max-width: 950px) {
      .listing-center { margin-right: 10px; }
      .listing-right { min-width: 180px; }
    }
    @media (max-width: 700px) {
      .listing-container { padding: 12px 2px; }
      .listing-card { flex-direction: column; align-items: stretch; }
      .listing-left { width: 100%; margin-right: 0; justify-content: flex-start; margin-bottom: 12px;}
      .listing-image img { width: 80px; height: 80px; }
      .listing-center { margin-right: 0; margin-bottom: 12px; }
      .listing-right { min-width: 0; }
    }
    </style>
</head>
<body>
<section class="categories-list">
    <h2>Top-Rated Restaurants Just for You</h2>
    <!-- Search Bar Start -->
    <div style="text-align:center; margin-bottom:14px;">
      <input type="text" id="searchBar" placeholder="Search by Name..." style="padding:7px 11px; width:250px; border-radius:6px; border:1px solid #b4d1e5;">
    </div>
    <script>
    function filterTableByName() {
        var input = document.getElementById("searchBar").value.toUpperCase();
        var cards = document.querySelectorAll(".listing-card-wrapper");
        if (cards.length) {
            cards.forEach(function(card) {
                var nameElem = card.querySelector(".restaurant-title");
                if (!nameElem) return;
                var txt = nameElem.textContent || nameElem.innerText;
                card.style.display = txt.toUpperCase().indexOf(input) > -1 ? "" : "none";
            });
        }
    }
    document.getElementById("searchBar").onkeyup = filterTableByName;
    </script>
    <!-- Search Bar End -->
    <div class="listing-container">
        <?php foreach($records as $r): ?>
            <div class="listing-card-wrapper">
                <div class="listing-card" onclick="window.location.href='rdetails.php?id=<?php echo $r['id']; ?>'">
                    <a href="rdetails.php?id=<?= $r['id']; ?>" class="overlay-link" tabindex="-1" aria-label="More details"></a>
                    <div class="listing-left">
                        <div class="listing-image">
                            <img src="images/<?php echo $r['img']; ?>" alt="Logo of <?php echo htmlspecialchars($r['name']); ?>">
                        </div>
                    </div>
                    <div class="listing-center">
                        <div class="restaurant-title"><?php echo htmlspecialchars($r['name']); ?></div>
                        <div class="restaurant-address-row">
                            <span class="restaurant-address"><?php echo htmlspecialchars($r['address']); ?></span>
                            <a class="restaurant-map-link" href="https://maps.google.com/?q=<?php echo urlencode($r['name']); ?>,<?php echo htmlspecialchars($r['city_name']); ?>" target="_blank" onclick="event.stopPropagation();">View on Map</a>
                        </div>
                        <ul class="contact-list">
                            <li>
                                <span class="contact-label">Contact No:</span>
                                <a href="tel:<?php echo $r['contact']; ?>" onclick="event.stopPropagation();"><?php echo $r['contact']; ?></a>
                            </li>
                            <?php if($r['website']): ?>
                            <li>
                                <span class="contact-label">Website:</span>
                                <a href="<?php echo htmlspecialchars($r['website']); ?>" target="_blank" onclick="event.stopPropagation();"><?php echo htmlspecialchars($r['website']); ?></a>
                            </li>
                            <?php endif; ?>
                        </ul>
                    </div>
                    <div class="listing-right">
                        <div class="field-row">
                            <span class="field-label">Opening Hours:</span>
                            <?php echo htmlspecialchars($r['opening_hours']); ?>
                        </div>
                        <div class="field-row">
                            <span class="field-label">Cuisines:</span>
                            <?php echo htmlspecialchars($r['cuisines']); ?>
                        </div>
                        <div class="field-row">
                            <span class="field-label">Menu Highlights:</span>
                            <?php echo htmlspecialchars($r['menu']); ?>
                        </div>
                        <div class="field-row">
                            <span class="field-label">Special Offers:</span>
                            <?php echo htmlspecialchars($r['special_offers']); ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</section>
<?php include 'includes/footer.php'; ?>
</body>
</html>