<?php

// categories.php - City Explorer Category Page with your SVGs

require_once 'includes/admin_header.php'; // Includes header, starts session, checks login

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Categories - City Explorer</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
   
<!-- Inline CSS for this page only -->
    <style>
        :root {
            --primary-color: #007bff;
            --secondary-color: #6c757d;
            --tertiary-color: #28a745;
            --danger-color: #dc3545;
            --dark-color: #343a40;
            --light-color: #f8f9fa;
            --white-color: #ffffff;
            --heading-color: #222;
            --font-poppins: 'Poppins', sans-serif;
            --spacing-md: 20px;
            --spacing-lg: 40px;
            --spacing-xl: 80px;
            --border-radius: 8px;
            --shadow-light: 0 4px 8px rgba(0,0,0,0.08);
            --shadow-medium: 0 8px 16px rgba(0,0,0,0.13);
        }
        body {
            margin: 0; padding: 0;
            background: var(--light-color);
            font-family: 'Open Sans', Arial, sans-serif;
            color: #222;
        }
        .category-hero {
            background: var(--primary-color);
            color: var(--white-color);
            text-align: center;
            padding: var(--spacing-xl) 0 var(--spacing-lg);
        }
        .category-hero h2 {
            font-family: var(--font-poppins);
            font-size: 2em;
            margin-bottom: 10px;
        }
        .category-hero p {
          color: black;
            font-size: 1.15em;
            opacity: 0.96;
        }
        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 0 var(--spacing-md);
        }
        .categories-list {
            padding: var(--spacing-xl) 0 var(--spacing-lg);
        }
        .category-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: var(--spacing-lg);
            justify-content: center;
            max-width: 900px;
            margin: 0 auto;
            transition: max-width 0.15s;
        }
        .category-card {
            background: var(--white-color);
            border-radius: var(--border-radius);
            box-shadow: var(--shadow-light);
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: var(--spacing-lg) var(--spacing-md);
            text-decoration: none;
            color: var(--heading-color);
            transition: transform 0.18s, box-shadow 0.18s, max-width 0.18s;
            min-height: 180px;
            width: 100%;
            box-sizing: border-box;
        }
        .category-card:hover {
            transform: translateY(-5px) scale(1.04);
            box-shadow: var(--shadow-medium);
            color: var(--primary-color);
        }
        .category-icon {
            width: 64px;
            height: 64px;
            margin-bottom: var(--spacing-md);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .category-icon img {
            width: 64px;
            height: 64px;
            object-fit: contain;
            display: block;
        }
        .category-title {
            font-family: var(--font-poppins);
            font-weight: 600;
            font-size: 1.18em;
            text-align: center;
            margin-top: var(--spacing-md);
            letter-spacing: 0.5px;
        }
        /* SVG styling for icons */
        .category-icon svg {
            width: 100%;
            height: 100%;
            display: block;
        }
        /* SEARCH BAR CENTERING */
        .category-search-wrap {
            width: 100%;
            display: flex;
            justify-content: center;
            margin-bottom: 38px;
        }
        #categorySearchBar {
            width: 100%;
            max-width: 340px;
            padding: 11px 18px;
            font-size: 1.07em;
            border-radius: 8px;
            border: 1.5px solid #b4d1e5;
            box-shadow: 0 2px 10px #cde6ff28;
            outline: none;
            margin: 0 auto;
            margin-top: 10px;
            margin-bottom: 10px;
            background: #fff;
            color: #222;
        }
        /* Single card adjustment */
        .category-grid.single-card {
            max-width: 370px;
            grid-template-columns: 1fr !important;
            justify-content: center;
        }
        .category-grid.single-card .category-card {
            max-width: 360px;
            margin-left: auto;
            margin-right: auto;
        }
        @media (max-width: 700px) {
            .category-hero {
                padding: var(--spacing-lg) 0 var(--spacing-md);
            }
            .category-search-wrap { margin-bottom: 18px; }
            .category-grid {
                grid-template-columns: 1fr;
                gap: var(--spacing-md);
                max-width: 100% !important;
            }
        }
    </style>
    </head>

<body>
   
     <?php  
        include 'includes/conf.php';
        $sql3="SELECT * FROM category WHERE 1";
        $result3=mysqli_query($conn,$sql3);
     ?>
   
    <section class="categories-list">
      
        <div class="category-search-wrap">
            <input type="text" id="categorySearchBar" placeholder="Search by Category Name..." onkeyup="filterCategoryCards()">
        </div>
        <div class="container">
            <div class="category-grid" id="categoryGrid">

<?php
if(mysqli_num_rows($result3)) {
    while($fetch=mysqli_fetch_assoc($result3)) {
?>
                <a href="manage_<?php echo $fetch['cat_image'];?>.php" class="category-card">
                    <div class="category-icon">
                        <img src="icons/<?php echo $fetch['cat_image']; ?>.svg" alt="<?php echo $fetch['cat_name']; ?>">
                    </div>
                    <div class="category-title"><?php echo $fetch['cat_name'];?></div>
                </a>
<?php 
    }   
}
?>
            </div>
        </div>
    </section>

    <script>
    function filterCategoryCards() {
        var input = document.getElementById("categorySearchBar").value.toUpperCase();
        var grid = document.getElementById("categoryGrid");
        var cards = grid.getElementsByClassName("category-card");
        let visibleCount = 0;
        for (var i = 0; i < cards.length; i++) {
            var titleElem = cards[i].querySelector(".category-title");
            if (titleElem) {
                var txtValue = titleElem.textContent || titleElem.innerText;
                if (txtValue.toUpperCase().indexOf(input) > -1) {
                    cards[i].style.display = "";
                    visibleCount++;
                } else {
                    cards[i].style.display = "none";
                }
            }
        }
        // If only one card is visible, center and resize it
        if (visibleCount === 1) {
            grid.classList.add('single-card');
        } else {
            grid.classList.remove('single-card');
        }
    }
    </script>

    <?php include 'includes/admin_footer.php'; ?>
</body>
</html>
