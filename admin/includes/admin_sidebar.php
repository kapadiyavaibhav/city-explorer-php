
<?php
// admin/includes/admin_sidebar.php

// Function to check if a link is active
function is_admin_active($page_name) {
    // Get the current script name without the .php extension
    $current_page = basename($_SERVER['PHP_SELF'], '.php');
    return ($current_page === $page_name) ? 'active' : '';
}
?>
<nav class="admin-side-nav">
    <ul>
        <li>
            <a href="dashboard.php" class="<?php echo is_admin_active('dashboard'); ?>">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
        </li>
        <li>
            <a href="manage_cities.php" class="<?php echo is_admin_active('manage_cities'); ?>">
                <i class="fas fa-building"></i> Manage Cities
            </a>
        </li>
        <li>
            <a href="manage_categories.php" class="<?php echo is_admin_active('manage_categories'); ?>">
                <i class="fas fa-tags"></i> Manage Categories
            </a>
        </li>
        <li>
            <a href="manage_places.php" class="<?php echo is_admin_active('manage_places'); ?>">
                <i class="fas fa-map-marker-alt"></i> Manage Places
            </a>
        </li>
          <li>
            <a href="record.php" class="<?php echo is_admin_active('record'); ?>">
                <i class="fas fa-rocket"></i> Manage Records
            </a>
        </li>
        <li>
            <a href="requests.php" class="<?php echo is_admin_active('manage_reviews'); ?>">
                <i class="fas fa-hourglass"> </i> Manage Request
            </a>
        </li>
        <li>
            <a href="review.php" class="<?php echo is_admin_active('settings'); ?>">
               <i class="fas fa-comments"></i> Review
            </a>
        </li>
        <li>
            <a href="logout.php">
                <i class="fas fa-sign-out-alt"></i> Logout
            </a>
        </li>
    </ul>
</nav>
