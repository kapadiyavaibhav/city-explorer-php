<?php
// Ensure $site_name and $current_year are available, or set defaults.
if (!isset($site_name)) {
    $site_name = "City Explorer";
}
if (!isset($current_year)) {
    $current_year = date("Y"); // Fallback to current year if not defined elsewhere
}
?>
<footer class="main-footer" style="margin-bottom: -80px;">
    <div class="container footer-content">
        <div class="footer-about">
            <h3><?php echo htmlspecialchars($site_name); ?></h3>
            <p>Your trusted companion for discovering, experiencing, and understanding the world's most captivating cities.</p>
        </div>
        <div class="footer-links">
            <h4>Quick Links</h4>
            <ul>
                <li><a href="index.php">Home</a></li>
                <li><a href="index.php#scrolltocity">Cities</a></li>
                
                <li><a href="index.php#scrolltoplaces">Places</a></li>
                <li><a href="index.php#scrolltoguide">Guides</a></li>
                <li><a href="about.php">About Us</a></li>
                <li><a href="contact.php">Contact</a></li>
            </ul>
        </div>
        <div class="footer-legal">
            <h4>Legal</h4>
            <ul>
                <li><a href="privacy-policy.php">Privacy Policy</a></li>
                <li><a href="terms-of-service.php">Terms of Service</a></li>
            </ul>
        </div>
        <div class="footer-social">
            <h4>Connect With Us</h4>
            <div class="social-icons">
                <a href="#" aria-label="Facebook"><img src="icons/facebook.svg" alt="Facebook"></a>
                <a href="https://www.instagram.com/nirag.thakar?igsh=dGRhaTJ5bmZ3Nmt1" aria-label="Instagram"><img src="icons/instagram.svg" alt="Instagram"></a>
                
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="container">
            <p>&copy; <?php echo htmlspecialchars($current_year); ?> <?php echo htmlspecialchars($site_name); ?>. All rights reserved.</p>
        </div>
    </div>
</footer>
