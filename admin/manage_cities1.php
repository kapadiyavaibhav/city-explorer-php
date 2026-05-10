<?php
// admin/manage_cities.php
require_once 'includes/admin_header.php'; // Includes header, starts session, checks login

$message = '';
$message_type = '';

// Handle Add/Edit City
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_city'])) {
    $city_name = trim($_POST['city_name']);
    $description = trim($_POST['description']);
    $image_url = trim($_POST['image_url']);
    $city_id = isset($_POST['city_id']) ? intval($_POST['city_id']) : 0;

    if (empty($city_name)) {
        $message = "City name cannot be empty.";
        $message_type = "error";
    } else {
        if ($city_id > 0) {
            // Update existing city
            $stmt = mysqli_prepare($link, "UPDATE cities SET city_name = ?, description = ?, image_url = ? WHERE city_id = ?");
            mysqli_stmt_bind_param($stmt, "sssi", $city_name, $description, $image_url, $city_id);
            if (mysqli_stmt_execute($stmt)) {
                $message = "City updated successfully!";
                $message_type = "success";
            } else {
                if (mysqli_errno($link) == 1062) { // Duplicate entry error code
                    $message = "Error: City name already exists.";
                } else {
                    $message = "Error updating city: " . mysqli_error($link);
                }
                $message_type = "error";
            }
            mysqli_stmt_close($stmt);
        } else {
            // Add new city
            $stmt = mysqli_prepare($link, "INSERT INTO cities (city_name, description, image_url) VALUES (?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "sss", $city_name, $description, $image_url);
            if (mysqli_stmt_execute($stmt)) {
                $message = "City added successfully!";
                $message_type = "success";
            } else {
                if (mysqli_errno($link) == 1062) { // Duplicate entry error code
                    $message = "Error: City name already exists.";
                } else {
                    $message = "Error adding city: " . mysqli_error($link);
                }
                $message_type = "error";
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// Handle Delete City
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $city_id = intval($_GET['id']);
    
    // Check if city is used by any places (due to ON DELETE RESTRICT on places table)
    $check_stmt = mysqli_prepare($link, "SELECT COUNT(*) FROM places WHERE city_id = ?");
    mysqli_stmt_bind_param($check_stmt, "i", $city_id);
    mysqli_stmt_execute($check_stmt);
    mysqli_stmt_bind_result($check_stmt, $count_places);
    mysqli_stmt_fetch($check_stmt);
    mysqli_stmt_close($check_stmt);

    if ($count_places > 0) {
        $message = "Cannot delete city: It is currently linked to " . $count_places . " places. Please delete or reassign all places in this city first.";
        $message_type = "error";
    } else {
        $stmt = mysqli_prepare($link, "DELETE FROM cities WHERE city_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $city_id);
        if (mysqli_stmt_execute($stmt)) {
            $message = "City deleted successfully!";
            $message_type = "success";
        } else {
            $message = "Error deleting city: " . mysqli_error($link);
            $message_type = "error";
        }
        mysqli_stmt_close($stmt);
    }
}

// Fetch city for editing
$edit_city = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $city_id = intval($_GET['id']);
    $stmt = mysqli_prepare($link, "SELECT city_id, city_name, description, image_url FROM cities WHERE city_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $city_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $edit_city = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    if (!$edit_city) {
        $message = "City not found for editing.";
        $message_type = "error";
    }
}

// Fetch all cities for display
$cities = [];
$sql = "SELECT city_id, city_name, description, image_url FROM cities ORDER BY city_name ASC";
$result = mysqli_query($link, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $cities[] = $row;
    }
    mysqli_free_result($result);
} else {
    $message = "Error fetching cities: " . mysqli_error($link);
    $message_type = "error";
}

?>

            <div class="admin-content-inner">
                <?php if ($message): ?>
                    <div class="admin-message <?php echo $message_type; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <h3><?php echo $edit_city ? 'Edit City' : 'Add New City'; ?></h3>
                <form action="manage_cities.php" method="POST" class="admin-form">
                    <?php if ($edit_city): ?>
                        <input type="hidden" name="city_id" value="<?php echo htmlspecialchars($edit_city['city_id']); ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="city_name">City Name:</label>
                        <input type="text" id="city_name" name="city_name" value="<?php echo htmlspecialchars($edit_city['city_name'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description:</label>
                        <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($edit_city['description'] ?? ''); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="image_url">Image URL:</label>
                        <input type="text" id="image_url" name="image_url" value="<?php echo htmlspecialchars($edit_city['image_url'] ?? ''); ?>">
                    </div>
                    <button type="submit" name="submit_city" class="btn-submit">
                        <?php echo $edit_city ? 'Update City' : 'Add City'; ?>
                    </button>
                </form>

                <h3 class="mt-40">Existing Cities</h3>
                <?php if (empty($cities)): ?>
                    <p>No cities found. Start by adding one above!</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>City Name</th>
                                    <th>Description</th>
                                    <th>Image URL</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($cities as $city): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($city['city_id']); ?></td>
                                        <td><?php echo htmlspecialchars($city['city_name']); ?></td>
                                        <td><?php echo htmlspecialchars(substr($city['description'], 0, 100)) . (strlen($city['description']) > 100 ? '...' : ''); ?></td>
                                        <td><?php echo htmlspecialchars(substr($city['image_url'], 0, 50)) . (strlen($city['image_url']) > 50 ? '...' : ''); ?></td>
                                        <td class="action-buttons">
                                            <a href="manage_cities.php?action=edit&id=<?php echo htmlspecialchars($city['city_id']); ?>" class="btn-edit"><i class="fas fa-edit"></i> Edit</a>
                                            <a href="manage_cities.php?action=delete&id=<?php echo htmlspecialchars($city['city_id']); ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this city? This will fail if places are linked to it.');"><i class="fas fa-trash-alt"></i> Delete</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>
            </div>

<?php
require_once 'includes/admin_footer.php'; // Includes footer, closes main tags
?>
