<?php
// admin/dashboard.php
require_once 'includes/admin_header.php'; // Includes header, starts session, checks login
?>

            <h2>Welcome to Your Admin Dashboard!</h2>
            <p>Here you can manage all aspects of your City Explorer website.</p>

            <div class="dashboard-stats-grid">
                <div class="stat-card">
                    <h3>Total Cities</h3>
                    <p class="stat-number">
                        <?php
                        // Example: Fetch total cities from database
                        $total_cities = 0;
                        $sql_cities = "SELECT COUNT(*) AS total FROM city";
                        if ($result = mysqli_query($link, $sql_cities)) {
                            $row = mysqli_fetch_assoc($result);
                            $total_cities = $row['total'];
                            mysqli_free_result($result);
                        }
                        echo $total_cities;
                        ?>
                    </p>
                    <a href="manage_cities.php" class="stat-link">View All Cities <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="stat-card">
                    <h3>Total Places</h3>
                    <p class="stat-number">
                        <?php
                        // Example: Fetch total places
                        $total_places = 0;
                        $sql_places = "SELECT COUNT(*) AS total FROM historical_place";
                        if ($result = mysqli_query($link, $sql_places)) {
                            $row = mysqli_fetch_assoc($result);
                            $total_places = $row['total'];
                            mysqli_free_result($result);
                        }
                        echo $total_places;
                        ?>
                    </p>
                    <a href="manage_places.php" class="stat-link">View All Places <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="stat-card">
                    <h3>Total Categories</h3>
                    <p class="stat-number">
                        <?php
                        // Example: Fetch total categories
                        $total_categories = 0;
                        $sql_categories = "SELECT COUNT(*) AS total FROM category";
                        if ($result = mysqli_query($link, $sql_categories)) {
                            $row = mysqli_fetch_assoc($result);
                            $total_categories = $row['total'];
                            mysqli_free_result($result);
                        }
                        echo $total_categories;
                        ?>
                    </p>
                    <a href="manage_categories.php" class="stat-link">View All Categories <i class="fas fa-arrow-right"></i></a>
                </div>
                 <div class="stat-card">
                    <h3>Total Admins</h3>
                    <p class="stat-number">
                        <?php
                        // Example: Fetch total users
                        $total_users = 0;
                        $sql_users = "SELECT COUNT(*) AS total FROM users";
                        if ($result = mysqli_query($link, $sql_users)) {
                            $row = mysqli_fetch_assoc($result);
                            $total_users = $row['total'];
                            mysqli_free_result($result);
                        }
                        echo $total_users;
                        ?>
                    </p>
                    <a href="manage_users.php" class="stat-link">View All Users <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="stat-card">
                    <h3>Manage Records</h3>
                    <p class="stat-number">
                       <?php
        $total_records = 0;

        // List all your tables here
        $tables = ['colleges', 'hospitals', 'h_p', 'pgs', 'restaurants','schools'];

        foreach ($tables as $table) {
            $sql = "SELECT COUNT(*) AS total FROM $table";
            if ($result = mysqli_query($link, $sql)) {
                $row = mysqli_fetch_assoc($result);
                $total_records += $row['total']; // add count of each table
                mysqli_free_result($result);
            }
        }

        echo $total_records;
        ?>
                    </p>
                    <a href="record.php" class="stat-link">View All Records <i class="fas fa-arrow-right"></i></a>
                </div>

 <div class="stat-card">
                    <h3>Requested  Records</h3>
                    <p class="stat-number">
                       <?php
        $total_records = 0;

        // List all your tables here
        $tables = ['t_colleges', 't_hospital', 't_h_p', 't_pgs', 't_restaurants','t_schools'];

        foreach ($tables as $table) {
            $sql = "SELECT COUNT(*) AS total FROM $table";
            if ($result = mysqli_query($link, $sql)) {
                $row = mysqli_fetch_assoc($result);
                $total_records += $row['total']; // add count of each table
                mysqli_free_result($result);
            }
        }

        echo $total_records;
        ?>
                    </p>
                    <a href="requests.php" class="stat-link">View All Records <i class="fas fa-arrow-right"></i></a>
                </div>
    <div class="stat-card">
                    <h3>Total Reviews</h3>
                    <p class="stat-number">
                        <?php
                        // Example: Fetch total places
                        $total_places = 0;
                        $sql_places = "SELECT COUNT(*) AS total FROM feedback";
                        if ($result = mysqli_query($link, $sql_places)) {
                            $row = mysqli_fetch_assoc($result);
                            $total_places = $row['total'];
                            mysqli_free_result($result);
                        }
                        echo $total_places;
                        ?>
                    </p>
                    <a href="review.php" class="stat-link">View All Places <i class="fas fa-arrow-right"></i></a>
                </div>
            </div>
           

            <?php
require_once 'includes/admin_footer.php'; // Includes footer, closes main tags
?>
