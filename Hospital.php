<?php
// Sample data array updated for your hospital card fields.

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
      box-shadow: 0 4px 18px rgba(0, 162, 255, 0.14);
      border: 2px solid #20aaff;
      background: #f8fcff;
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
    .hospital-title {
      font-size: 1.18em;
      font-weight: 700;
      color: #222;
      margin-bottom: 7px;
    }
    .hospital-address-row {
      display: flex;
      align-items: center;
      margin-bottom: 6px;
      color: #444;
      font-size: 0.98em;
    }
    .hospital-address {
      display: inline-block;
    }
    .hospital-map-link {
      color: #2196f3;
      text-decoration: underline;
      margin-left: 10px;
      font-weight: 600;
      font-size: 0.98em;
      white-space: nowrap;
      /* Avoids link wrapping to new line */
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
    /* Prevent pointer events for the View on Map link and phone links */
    .hospital-map-link, .contact-list a {
      pointer-events: auto;
      cursor: pointer;
    }
    /* overlays */
    .overlay-link {
      position: absolute;
      inset: 0;
      z-index: 1;
    }
    .hospital-map-link, .contact-list a {
      position: relative;
      z-index: 2;
    }
    /* Prevents overlay capturing clicks on these */
    .hospital-map-link, .contact-list a {
      pointer-events: auto;
    }
    /* Make sure overlay doesn't block excluded elements */
    .listing-card *:not(.hospital-map-link):not(.contact-list a) {
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
    /* Search bar styling */
    #searchBar {
      margin-bottom: 20px;
      padding: 10px 14px;
      width: 100%;
      max-width: 380px;
      border-radius: 7px;
      border: 1.5px solid #b4d1e5;
      font-size: 1em;
      display: block;
      margin-left: auto;
      margin-right: auto;
    }
  </style>
</head>
<body>
  <?php include 'includes/header.php'; 
  $city = $_REQUEST['city'];
  $type = $_REQUEST['type'];
  ?>
    <?php include 'includes/conf.php';
	$sql="SELECT * FROM hospitals WHERE city_name='$city' AND cat_name='$type'";
	$records=mysqli_query($conn,$sql);
  ?>
  

   <section class="categories-list">
      <h2>Explore Top Hospitals in Your City</h2>
      <!-- LIVE SEARCH BAR -->
      <input type="text" id="searchBar" placeholder="Search by Hospital Name..." onkeyup="filterHospitalsByName()">

  <div class="listing-container" id="listingContainer">
    <?php foreach($records as $r): ?>
      <div class="listing-card-wrapper">
        <div class="listing-card" onclick="window.location.href='hdetails.php?id=<?php echo $r['id']; ?>'">
          <!-- Transparent overlay link to make whole card clickable except links/buttons -->
          <a href="hdetails.php?id=<?= $r['id'];?>" class="overlay-link" tabindex="-1" aria-label="More details"></a>
          <div class="listing-left">
            <div class="listing-image">
              <img src="images/<?php echo $r['img']; ?>" alt="Logo of <?php echo htmlspecialchars($r['name']); ?>">
            </div>
          </div>
          <div class="listing-center">
            <div class="hospital-title"><?php echo htmlspecialchars($r['name']); ?></div>
            <div class="hospital-address-row">
              <span class="hospital-address"><?php echo htmlspecialchars($r['address']); ?></span>
              <a class="hospital-map-link" href="https://maps.google.com/?q=<?php echo urlencode($r['name']); ?>" target="_blank" onclick="event.stopPropagation();">View on Map</a>
            </div>
            <ul class="contact-list">
              <li>
                <span class="contact-label">Contact No:</span>
                <a href="tel:<?php echo $r['contact']; ?>" onclick="event.stopPropagation();"><?php echo $r['contact']; ?></a>
              </li>
            </ul>
          </div>
          <div class="listing-right">
            <div class="field-row">
              <span class="field-label">Opening Hours:</span>
              <?php echo htmlspecialchars($r['opening_hours']); ?>
            </div>
            <div class="field-row">
              <span class="field-label">Emergency Dept.:</span>
              <?php echo htmlspecialchars($r['emergency']); ?>
            </div>
            <div class="field-row">
              <span class="field-label">Visiting Hours:</span>
              <?php echo htmlspecialchars($r['visiting_hours']); ?>
            </div>
            <div class="field-row">
              <span class="field-label">Specialties:</span>
              <?php echo htmlspecialchars($r['departments']); ?>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <script>
    function filterHospitalsByName() {
      var input = document.getElementById("searchBar").value.toUpperCase();
      var container = document.getElementById("listingContainer");
      var cards = container.getElementsByClassName("listing-card-wrapper");
      for (var i = 0; i < cards.length; i++) {
        var titleEl = cards[i].querySelector(".hospital-title");
        if (titleEl) {
          var txtValue = titleEl.textContent || titleEl.innerText;
          if (txtValue.toUpperCase().indexOf(input) > -1) {
            cards[i].style.display = "";
          } else {
            cards[i].style.display = "none";
          }
        }
      }
    }
  </script>
<?php include 'includes/footer.php'; ?>
</body>
</html>