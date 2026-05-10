<?php
// This is the main index file for the City Explorer website.
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
        }

        .destination-card {
            transition: all 0.3s;
        }

        .destination-card.hide {
            display: none !important;
        }
        .destination-grid {
    display: flex;
    flex-wrap: wrap;
    justify-content: center; /* This centers the items */
    gap: 20px;
}

.destination-card {
    width: 280px;
}

    </style>
</head>

<body>

    <?php include 'includes/header.php'; ?>
    <?php include 'includes/conf.php';

    $sql = "SELECT * FROM city";
    $result = mysqli_query($conn, $sql);
    ?>

    <section class="featured-destinations">
        <div class="container">
            <h2>Where Will You Explore Next?</h2>

            <!-- Live Search Bar (No form submit) -->
            <div class="search-bar-city">
                <input type="text" placeholder="Search by city name..." onkeyup="filterCities(this)">
            </div>

            <div class="destination-grid">
                <?php
                if (mysqli_num_rows($result)) {
                    while ($fetch = mysqli_fetch_assoc($result)) {
                ?>
                        <div class="destination-card">
                            <img src="images/<?php echo $fetch['img_src']; ?>">
                            <h3><?php echo $fetch['city_name']; ?></h3>
                            <p><?php echo $fetch['description']; ?></p>
                            <a href="category.php?city=<?= $fetch['city_name'] ?>" class="btn btn-secondary">Explore <?php echo $fetch['city_name']; ?></a>
                        </div>
                <?php
                    }
                } else {
                    echo '<p style="text-align:center; font-size:1.2em; color:#555; margin-top:40px;">No cities found.</p>';
                }
                ?>
            </div>
        </div>
    </section>

    <?php include 'includes/footer.php'; ?>

    <script>
        function filterCities(input) {
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

</body>
</html>
