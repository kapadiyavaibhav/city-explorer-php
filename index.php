<?php
// This is the main index file for the City Explorer website.
// It includes common components like the header and footer to maintain consistency.
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>City Explorer - Your Ultimate Guide to Urban Adventures</title>

  <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="css/style.css">
  <link rel="icon" href="images/favicon.ico" type="image/x-icon">

  <style>
    /* Popup styles */
    .popup {
      position: fixed;
      top: 50%; left: 50%;
      transform: translate(-50%, -50%);
      background: white;
      padding: 25px;
      box-shadow: 0 0 20px rgba(0,0,0,0.5);
      z-index: 1000;
      display: none;
      width: 600px;
      max-width: 95%;
      height: 90vh;
      overflow-y: scroll;
      border-radius: 25px;
      scrollbar-width: none;
    }
    .popup::-webkit-scrollbar { display: none; }
    .popup-overlay {
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      background: rgba(0,0,0,0.6);
      z-index: 999;
      display: none;
    }
    .popup-close {
      float: right;
      font-weight: bold;
      font-size: 22px;
      background: red;
      color: white;
      padding: 1px 10px;
      border-radius: 50%;
      cursor: pointer;
    }
    .popup p { margin-top: 15px; line-height: 1.6; font-size: 16px; }
  </style>
</head>
<body>

<?php include 'includes/header.php'; ?>
<?php 
include 'includes/conf.php';
$sql="SELECT * FROM city WHERE 1";
$result=mysqli_query($conn,$sql);
$sql1="SELECT * FROM historical_place WHERE 1";
$result1=mysqli_query($conn,$sql1);
?>

<section class="hero">
  <div class="hero-content" style="padding-top: 150px;">
    <h1>Explore your City with us</h1>
    <p>Discover hidden gems, local favorites, and essential travel tips for cities around the globe.</p>
    <a href="#scrolltocity" class="btn btn-primary">Start Exploring..!</a>
  </div>
</section>

<section class="featured-destinations">
  <div class="container">
    <section id="scrolltocity" style="padding-top: 20px;">
      <h2>Where Will You Explore Next?</h2>
      <div class="destination-grid">
        <?php
        $cnt=0;
        if(mysqli_num_rows($result)) {
          while($fetch=mysqli_fetch_assoc($result)) {
            if($cnt<3) {
        ?>
        <div class="destination-card">
          <img src="images/<?php echo $fetch['img_src'];?>">
          <h3><?php echo $fetch['city_name'];?></h3>
          <p><?php echo $fetch['description'];?></p>
          <a href="category.php?city=<?=$fetch['city_name']?>" class="btn btn-secondary">
            Explore <?php echo $fetch['city_name'];?>
          </a>
        </div>
        <?php 
          $cnt++;
        } } }
        ?>
      </div>
      <div class="text-center">
        <a href="cities.php" class="btn btn-secondary mt-4">View All Cities</a>
      </div>
    </section>
  </div>
</section>

<section class="latest-blog-posts">
  <div class="container">
    <section id="scrolltoplaces" style="padding-top: 30px;">
      <h2>Latest Discoveries & Travel Stories</h2>
      <div class="blog-post-grid">
        <?php
        $cnt1=0;
        if(mysqli_num_rows($result1)) {
          while($fetch=mysqli_fetch_assoc($result1)) {
            if($cnt1<=2) {
              $placeName = htmlspecialchars($fetch['place_name'], ENT_QUOTES);
              $description = htmlspecialchars($fetch['description'], ENT_QUOTES);
              $imgSrc = "images/" . htmlspecialchars($fetch['img_src'], ENT_QUOTES);
              $location = htmlspecialchars($fetch['address'], ENT_QUOTES);
              $category = htmlspecialchars($fetch['category'], ENT_QUOTES);
              $mapSearchUrl = "https://www.google.com/maps/search/?api=1&query=" . urlencode($placeName);
        ?>
        <div class="blog-post-card destination-card">
          <img src="<?php echo $imgSrc; ?>" alt="Image of <?php echo $placeName; ?>">
          <h3><?php echo $placeName; ?></h3>
          <p>
            <?php echo (strlen($description) > 30) ? substr($description, 0, 30) . '..' : $description; ?>
          </p>
          <button class="btn btn-tertiary readMoreBtn"
            data-title="<?php echo $placeName; ?>"
            data-description="<?php echo $description; ?>"
            data-img="<?php echo $imgSrc; ?>"
            data-location="<?php echo $location; ?>"
            data-category="<?php echo $category; ?>"
            data-map="<?php echo $mapSearchUrl; ?>">
            Read More
          </button>
        </div>
        <?php 
            $cnt1++;
          } } }
        ?>
      </div>
      <div class="text-center">
        <a href="places.php" class="btn btn-secondary mt-4">View All Stories</a>
      </div>
    </section>
  </div>
</section>

<!-- 📦 Popup Overlay -->
<div class="popup-overlay" id="popupOverlay"></div>
<div class="popup" id="popupBox">
  <span class="popup-close" id="popupClose">&times;</span>
  <img id="popupImage" src="" alt="" style="width: 100%; max-height: 350px; object-fit: cover; border-radius: 10px;">
  <h2 id="popupTitle" style="margin-top: 10px;"></h2>
  <p id="popupDesc" style="max-height: 300px; overflow-y: auto;"></p>
  <p><strong>Location:</strong> <span id="popupLocation"></span></p>
  <p><strong>Category:</strong> <span id="popupCategory"></span></p>
  <a id="popupMap" href="#" target="_blank" style="color: #007bff; text-decoration: underline;">🔗 View on Map</a>
</div>

<section class="why-choose-us">
  <section id="scrolltoguide" style="padding-top: 50px;">
    <div class="container">
      <h2>Why Explore with Us?</h2>
      <div class="features-grid">
        <div class="feature-item">
          <img src="icons/diamond.svg" alt="Diamond Icon" class="feature-icon"> 
          <h3>Discover Hidden Gems</h3>
          <p>Go beyond the tourist traps. We uncover the authentic experiences and local secrets only true explorers find.</p>
        </div>
        <div class="feature-item">
          <img src="icons/map.svg" alt="Map Icon" class="feature-icon"> 
          <h3>Expertly Curated Guides</h3>
          <p>From in-depth itineraries to dining recommendations, our guides are crafted by travel enthusiasts for effortless exploration.</p>
        </div>
        <div class="feature-item">
          <img src="icons/community.svg" alt="Community Icon" class="feature-icon"> 
          <h3>Community & Insights</h3>
          <p>Connect with fellow adventurers, share your stories, and get real-time tips from our vibrant community.</p>
        </div>
        <div class="feature-item">
          <img src="icons/gps.svg" alt="GPS Icon" class="feature-icon"> 
          <h3>Easy Planning & Navigation</h3>
          <p>Our intuitive tools make planning your urban adventure simple, so you can focus on the journey, not the logistics.</p>
        </div>
      </div>
    </div>
  </section>
</section>

<?php include 'includes/footer.php'; ?>

<!-- 🧠 JS for Popup -->
<script>
window.addEventListener("DOMContentLoaded", () => {
  const buttons = document.querySelectorAll(".readMoreBtn");
  const popup = document.getElementById("popupBox");
  const overlay = document.getElementById("popupOverlay");
  const closeBtn = document.getElementById("popupClose");

  const popupTitle = document.getElementById("popupTitle");
  const popupDesc = document.getElementById("popupDesc");
  const popupLocation = document.getElementById("popupLocation");
  const popupCategory = document.getElementById("popupCategory");
  const popupMap = document.getElementById("popupMap");

  buttons.forEach(btn => {
    btn.addEventListener("click", () => {
      document.getElementById("popupImage").src = btn.getAttribute("data-img");
      popupTitle.textContent = btn.getAttribute("data-title");
      popupDesc.textContent = btn.getAttribute("data-description");
      popupLocation.textContent = btn.getAttribute("data-location");
      popupCategory.textContent = btn.getAttribute("data-category");
      popupMap.href = btn.getAttribute("data-map");

      popup.style.display = "block";
      overlay.style.display = "block";
    });
  });

  closeBtn.addEventListener("click", () => {
    popup.style.display = "none";
    overlay.style.display = "none";
  });

  overlay.addEventListener("click", () => {
    popup.style.display = "none";
    overlay.style.display = "none";
  });
});
</script>
</body>
</html>
