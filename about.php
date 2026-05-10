<?php
// Define variables specific to this page, if any
$page_title = "About City Explorer";
$site_name = "City Explorer"; // Inherited from common settings or defined here if not global

// In a real application, you might define these in a separate config file
// and include it on every page: require_once 'config.php';

// Include the header file
include 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($page_title); ?> - <?php echo htmlspecialchars($site_name); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Open+Sans:wght@400;600;700&family=Poppins:wght@600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/style.css">
    <link rel="icon" href="images/favicon.ico" type="image/x-icon">
</head>
<body>

    <?php // include 'includes/header.php'; // This is already handled at the top of the file ?>

    <main>
        <section class="page-hero about-hero">
            <div class="container">
                <h1>About City Explorer</h1>
                <p class="tagline">Our passion for urban discovery, delivered to you.</p>
            </div>
        </section>

        <section class="about-intro content-section">
            <div class="container">
                <h2>Our Story</h2>
                <p><strong>City Explorer</strong> was founded on a simple idea: that every city holds a universe of unique experiences waiting to be discovered. Born from a shared love for travel and local culture, our team set out to create a platform that goes beyond typical tourist guides, helping adventurers like you uncover the true soul of urban landscapes.</p>
                <p>We believe that the best way to experience a city is through the eyes of its residents. That's why we meticulously research, explore, and collaborate with local experts to bring you authentic recommendations, hidden gems, and practical advice that transforms a visit into an unforgettable adventure.</p>
            </div>
        </section>

        <section class="about-mission content-section bg-light-grey">
            <div class="container">
                <h2>Our Mission</h2>
                <div class="mission-grid">
                    <div class="mission-item">
                        <img src="icons/explore.svg" alt="Explore Icon" class="mission-icon">
                        <h3>Inspire Discovery</h3>
                        <p>To ignite curiosity and empower travelers to delve deeper into cities, encouraging exploration beyond the well-trodden paths.</p>
                    </div>
                    <div class="mission-item">
                        <img src="icons/guide.svg" alt="Guide Icon" class="mission-icon">
                        <h3>Provide Authentic Insights</h3>
                        <p>To deliver high-quality, reliable, and genuinely local recommendations for dining, activities, and cultural experiences.</p>
                    </div>
                    <div class="mission-item">
                        <img src="icons/community.svg" alt="Community Icon" class="mission-icon">
                        <h3>Foster Connection</h3>
                        <p>To build a vibrant community of city lovers who share knowledge, experiences, and a passion for urban adventures.</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="about-team content-section">
            <div class="container">
                <h2>Meet the Team</h2>
                <div class="team-grid">
                    <div class="team-member">
                        <img src="images/member-1.jpg" alt="Team Member Nirag Thakar" class="team-photo">
                        <h3>Nirag Thakar</h3>
                        <p class="role">Chief Explorer & Founder</p>
                        <p>Nirag's obsession with maps and hidden alleyways led to the birth of City Explorer. He's always planning the next big urban adventure.</p>
                    </div>
                   
                    <div class="team-member">
                        <img src="images/member-2.jpg" alt="Team Member Vaibhav Kapadiya" class="team-photo">
                        <h3>Vaibhav Kapadiya</h3>
                        <p class="role">Community Manager</p>
                        <p>Vaibhav thrives on connecting people. He's the heart of our explorer community, fostering shared knowledge and travel tips.</p>
                    </div>
                     <div class="team-member">
                        <img src="images/member-3.jpg" alt="Team Member ChatGPT" class="team-photo">
                        <h3>ChatGPT</h3>
                        <p class="role">Content & Research Lead</p>
                        <p>A meticulous researcher and storyteller, ChatGPT ensures every guide is packed with accurate, inspiring, and actionable information.</p>
                    </div>
                </div>
            </div>
        </section>

        
    </main>

    <?php include 'includes/footer.php'; ?>

</body>
</html>
