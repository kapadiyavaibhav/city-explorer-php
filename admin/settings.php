<?php
// admin/settings.php
require_once 'includes/admin_header.php'; // Includes header, starts session, checks login

// Initialize messages
$message = '';
$message_type = '';

// Function to get a setting value from the database
function get_setting($link, $setting_name) {
    $stmt = mysqli_prepare($link, "SELECT setting_value FROM settings WHERE setting_name = ?");
    mysqli_stmt_bind_param($stmt, "s", $setting_name);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $setting_value);
    mysqli_stmt_fetch($stmt);
    mysqli_stmt_close($stmt);
    return $setting_value ?? ''; // Return value or empty string if not found
}

// Function to set/update a setting value in the database
function set_setting($link, $setting_name, $setting_value) {
    // Check if the setting already exists
    $check_stmt = mysqli_prepare($link, "SELECT COUNT(*) FROM settings WHERE setting_name = ?");
    mysqli_stmt_bind_param($check_stmt, "s", $setting_name);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_bind_result($check_stmt, $count);
    mysqli_stmt_fetch($check_stmt);
    mysqli_stmt_close($check_stmt);

    if ($count > 0) {
        // Update existing setting
        $stmt = mysqli_prepare($link, "UPDATE settings SET setting_value = ? WHERE setting_name = ?");
        mysqli_stmt_bind_param($stmt, "ss", $setting_value, $setting_name);
    } else {
        // Insert new setting
        $stmt = mysqli_prepare($link, "INSERT INTO settings (setting_name, setting_value) VALUES (?, ?)");
        mysqli_stmt_bind_param($stmt, "ss", $setting_name, $setting_value);
    }

    $success = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    return $success;
}

// List of expected settings (to sanitize and save)
$setting_keys = [
    'site_name',
    'admin_email',
    'default_place_image_url',
    'facebook_url',
    'twitter_url',
    'instagram_url',
    'items_per_page'
];

// Handle form submission for settings
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    $errors = [];

    // Validate and sanitize inputs
    $site_name = trim($_POST['site_name'] ?? '');
    if (empty($site_name)) {
        $errors[] = "Site Name cannot be empty.";
    }

    $admin_email = trim($_POST['admin_email'] ?? '');
    if (!filter_var($admin_email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Invalid Admin Email format.";
    }

    $default_place_image_url = trim($_POST['default_place_image_url'] ?? '');
    if (!empty($default_place_image_url) && !filter_var($default_place_image_url, FILTER_VALIDATE_URL)) {
        $errors[] = "Default Place Image URL is not a valid URL.";
    }

    $facebook_url = trim($_POST['facebook_url'] ?? '');
    if (!empty($facebook_url) && !filter_var($facebook_url, FILTER_VALIDATE_URL)) {
        $errors[] = "Facebook URL is not a valid URL.";
    }
    
    $twitter_url = trim($_POST['twitter_url'] ?? '');
    if (!empty($twitter_url) && !filter_var($twitter_url, FILTER_VALIDATE_URL)) {
        $errors[] = "Twitter URL is not a valid URL.";
    }

    $instagram_url = trim($_POST['instagram_url'] ?? '');
    if (!empty($instagram_url) && !filter_var($instagram_url, FILTER_VALIDATE_URL)) {
        $errors[] = "Instagram URL is not a valid URL.";
    }

    $items_per_page = intval($_POST['items_per_page'] ?? 0);
    if ($items_per_page <= 0) {
        $errors[] = "Items Per Page must be a positive number.";
    }

    if (!empty($errors)) {
        $message = "Please correct the following errors:<br>" . implode("<br>", $errors);
        $message_type = "error";
    } else {
        // Save each setting
        $success_count = 0;
        foreach ($setting_keys as $key) {
            $value = $_POST[$key] ?? ''; // Use raw POST value for saving
            // Re-assign validated/sanitized values for specific fields
            if ($key === 'site_name') $value = $site_name;
            if ($key === 'admin_email') $value = $admin_email;
            if ($key === 'default_place_image_url') $value = $default_place_image_url;
            if ($key === 'facebook_url') $value = $facebook_url;
            if ($key === 'twitter_url') $value = $twitter_url;
            if ($key === 'instagram_url') $value = $instagram_url;
            if ($key === 'items_per_page') $value = $items_per_page;


            if (set_setting($link, $key, $value)) {
                $success_count++;
            } else {
                // Log or handle individual setting save errors
                error_log("Failed to save setting: " . $key . " - " . mysqli_error($link));
            }
        }

        if ($success_count === count($setting_keys)) {
            $message = "All settings saved successfully!";
            $message_type = "success";
        } else {
            $message = "Some settings could not be saved. Please check logs for details.";
            $message_type = "warning";
        }
    }
}

// Fetch current settings to pre-fill the form
$current_settings = [];
foreach ($setting_keys as $key) {
    $current_settings[$key] = get_setting($link, $key);
}

// Provide default values if settings are not found in DB
if (empty($current_settings['site_name'])) $current_settings['site_name'] = 'City Explorer';
if (empty($current_settings['admin_email'])) $current_settings['admin_email'] = 'admin@example.com';
if (empty($current_settings['default_place_image_url'])) $current_settings['default_place_image_url'] = 'https://via.placeholder.com/400x300.png?text=No+Image';
if (empty($current_settings['items_per_page'])) $current_settings['items_per_page'] = 10;

?>

            <div class="admin-content-inner">
                <?php if ($message): ?>
                    <div class="admin-message <?php echo $message_type; ?>">
                        <?php echo $message; ?>
                    </div>
                <?php endif; ?>

                <h3>Site Settings</h3>
                <p>Manage various global settings for your City Explorer website.</p>

                <form action="settings.php" method="POST" class="admin-form">
                    <h4>General Settings</h4>
                    <div class="form-group">
                        <label for="site_name">Website Name / Title:</label>
                        <input type="text" id="site_name" name="site_name" value="<?php echo htmlspecialchars($current_settings['site_name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="admin_email">Admin Contact Email:</label>
                        <input type="email" id="admin_email" name="admin_email" value="<?php echo htmlspecialchars($current_settings['admin_email']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="default_place_image_url">Default Place Image URL:</label>
                        <input type="url" id="default_place_image_url" name="default_place_image_url" value="<?php echo htmlspecialchars($current_settings['default_place_image_url']); ?>" placeholder="e.g., https://example.com/default.jpg">
                        <small>This image will be used if a place doesn't have a specific image.</small>
                    </div>
                    <div class="form-group">
                        <label for="items_per_page">Items Per Page (for listings):</label>
                        <input type="number" id="items_per_page" name="items_per_page" value="<?php echo htmlspecialchars($current_settings['items_per_page']); ?>" min="1" required>
                        <small>Number of items (e.g., places, cities) to display per page on the frontend.</small>
                    </div>

                    <h4 class="mt-40">Social Media Links</h4>
                    <div class="form-group">
                        <label for="facebook_url">Facebook URL:</label>
                        <input type="url" id="facebook_url" name="facebook_url" value="<?php echo htmlspecialchars($current_settings['facebook_url']); ?>" placeholder="e.g., https://facebook.com/yourpage">
                    </div>
                    <div class="form-group">
                        <label for="twitter_url">Twitter URL:</label>
                        <input type="url" id="twitter_url" name="twitter_url" value="<?php echo htmlspecialchars($current_settings['twitter_url']); ?>" placeholder="e.g., https://twitter.com/yourhandle">
                    </div>
                    <div class="form-group">
                        <label for="instagram_url">Instagram URL:</label>
                        <input type="url" id="instagram_url" name="instagram_url" value="<?php echo htmlspecialchars($current_settings['instagram_url']); ?>" placeholder="e.g., https://instagram.com/yourprofile">
                    </div>
                    
                    <button type="submit" name="save_settings" class="btn-submit">Save Settings</button>
                </form>

            </div>

<?php
require_once 'includes/admin_footer.php'; // Includes footer, closes main tags
?>
