<?php
// This ensures that variables like $site_name and $tagline are available.
// In the index.php, these would be defined before including this header.
// If this file were ever accessed directly, these defaults would prevent errors.
if (!isset($site_name)) {
    $site_name = "City Explorer";
}
if (!isset($tagline)) {
    $tagline = "Uncover the Soul of Every City";
}
?>
<header class="main-header">
    <div class="container header-content">
        <div class="logo">
            <a href="index.php">
                <span class="icon">&#x1F9ED;</span> <?php echo htmlspecialchars($site_name); ?>
            </a>
            <p class="tagline"><?php echo htmlspecialchars($tagline); ?></p>
        </div>
        <nav class="main-nav">
            <ul>
                <li><a href="index.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'index.php') ? 'active' : ''; ?>">Home</a></li>
                <li><a href="index.php#scrolltocity" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'destinations.php') ? 'active' : ''; ?>">Cities</a></li>
                 <li><a href="index.php#scrolltoplaces" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'blog.php') ? 'active' : ''; ?>">Places</a></li>
                <li><a href="index.php#scrolltoguide" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'guides.php') ? 'active' : ''; ?>">Guides</a></li>
                <li><a href="about.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'about.php') ? 'active' : ''; ?>">About Us</a></li>
                <li><a href="contact.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'contact.php') ? 'active' : ''; ?>">Contact Us</a></li>
                 
                 <li><a href="user/profile.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'contact.php') ; ?>">Profile</a></li>
                              
                
        
                </ul>
        </nav>
        <button class="menu-toggle" aria-label="Toggle navigation">&#9776;</button>
    </div>
</header>

