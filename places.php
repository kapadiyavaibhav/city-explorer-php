<?php
session_start();
include 'includes/conf.php';
include 'includes/header.php';

// Handle search query
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';
$sql1 = "SELECT * FROM historical_place";
if (!empty($searchQuery)) {
  $safeQuery = mysqli_real_escape_string($conn, $searchQuery);
  $sql1 .= " WHERE place_name LIKE '%$safeQuery%'";
}
$result1 = mysqli_query($conn, $sql1);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Historical Places</title>
  <link rel="stylesheet" href="css/style.css" />
  <style>
    /* Search bar styling (matches index.php city search bar) */
    .search-bar-city {
      display: flex;
      justify-content: center;
      align-items: center;
      margin: 30px 0 40px 0;
      gap: 10px;
    }
    .search-bar-city input[type="text"] {
      padding: 10px 12px;
      border-radius: 5px;
      border: 1px solid #ccc;
      font-size: 1em;
      width: 240px;
      transition: border-color 0.3s;
    }
    .search-bar-city input[type="text"]:focus {
      border-color: #007bff;
      outline: none;
    }
    .destination-card {
      transition: all 0.3s;
    }
    .destination-card.hide {
      display: none !important;
    }
    /* Optional: Blog post grid styling, unchanged */
    .blog-post-grid {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 20px;
    }
    .blog-post-card {
      width: 300px;
    }
    /* Popup styles unchanged */
    .popup {
      position: fixed;
      top: 50%;
      left: 50%;
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
    .popup p {
      margin-top: 15px;
      line-height: 1.6;
      font-size: 16px;
    }
  </style>
</head>
<body>

<section class="latest-blog-posts">
  <div class="container">
    <h2>Latest Discoveries & Travel Stories</h2>

    <!-- 🚀 New: Live Search Bar (matches city search, no form submit) -->
    <div class="search-bar-city">
      <input type="text" placeholder="Search places..." onkeyup="filterPlaces(this)" id="searchInput" value="<?php echo htmlspecialchars($searchQuery); ?>" />
    </div>

    <div class="blog-post-grid" id="placesGrid">
      <?php
      if (mysqli_num_rows($result1)) {
        while ($fetch = mysqli_fetch_assoc($result1)) {
          $placeName = htmlspecialchars($fetch['place_name'] ?? 'Unknown Place', ENT_QUOTES);
          $description = htmlspecialchars($fetch['description'] ?? 'No description available.', ENT_QUOTES);
          $imgSrc = "images/" . htmlspecialchars($fetch['img_src'] ?? 'default.jpg', ENT_QUOTES);
          $location = htmlspecialchars($fetch['address'] ?? 'Unknown', ENT_QUOTES);
          $category = htmlspecialchars($fetch['category'] ?? 'Uncategorized', ENT_QUOTES);
          $mapSearchUrl = "https://www.google.com/maps/search/?api=1&query=" . urlencode($placeName);
          $altText = "Image of " . $placeName;
      ?>
      <div class="blog-post-card destination-card">
        <img src="<?php echo $imgSrc; ?>" alt="<?php echo $altText; ?>" />
        <h3><?php echo $placeName; ?></h3>
        <p><?php echo (strlen($description) > 30) ? substr($description, 0, 30) . '..' : $description; ?></p>
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
        }
      } else {
        echo "<p style='text-align:center;'>No historical places found matching your search.</p>";
      }
      ?>
    </div>
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

<!-- 🧠 JavaScript for Popup and Live Search -->
<script>
window.addEventListener("DOMContentLoaded", () => {
  // --- Popup JS (Unchanged) ---
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

// --- Live Client-Side Search for Places (matches city search) ---
function filterPlaces(input) {
  var filter = input.value.toUpperCase();
  var cards = document.getElementsByClassName('destination-card');
  for (var i = 0; i < cards.length; i++) {
    var title = cards[i].getElementsByTagName("h3")[0];
    var txt = title.textContent || title.innerText;
    if (txt.toUpperCase().indexOf(filter) > -1) {
      cards[i].classList.remove('hide');
    } else {
      cards[i].classList.add('hide');
    }
  }
}
</script>

<?php include 'includes/footer.php'; ?>
</body>
</html>