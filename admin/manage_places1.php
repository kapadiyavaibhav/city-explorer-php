<?php
// admin/manage_places.php
require_once 'includes/admin_header.php'; // Includes header, starts session, checks login

$message = '';
$message_type = '';

// Handle Add/Edit Place
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_place'])) {
    $place_name = trim($_POST['place_name']);
    $description = trim($_POST['description']);
    $address = trim($_POST['address']);
    $image_url = trim($_POST['image_url']);
    $city_id = intval($_POST['city_id']); // Get selected city_id
    $category_id = isset($_POST['category_id']) && $_POST['category_id'] !== '' ? intval($_POST['category_id']) : NULL; // Get selected category_id, handle optional

    $place_id = isset($_POST['place_id']) ? intval($_POST['place_id']) : 0;

    // Validate inputs
    if (empty($place_name) || empty($address) || $city_id <= 0) {
        $message = "Place Name, Address, and City are required.";
        $message_type = "error";
    } else {
        if ($place_id > 0) {
            // Update existing place
            $stmt = mysqli_prepare($link, "UPDATE places SET place_name = ?, description = ?, address = ?, image_url = ?, city_id = ?, category_id = ? WHERE place_id = ?");
            // Use 's' for string, 'i' for integer, 'i' for category_id which can be NULL so it behaves as an integer or NULL
            // For nullable integers, you might consider custom bind if strict types are an issue, but 'i' usually works.
            mysqli_stmt_bind_param($stmt, "ssssiii", $place_name, $description, $address, $image_url, $city_id, $category_id, $place_id);
            if (mysqli_stmt_execute($stmt)) {
                $message = "Place updated successfully!";
                $message_type = "success";
            } else {
                $message = "Error updating place: " . mysqli_error($link);
                $message_type = "error";
            }
            mysqli_stmt_close($stmt);
        } else {
            // Add new place
            $stmt = mysqli_prepare($link, "INSERT INTO places (place_name, description, address, image_url, city_id, category_id) VALUES (?, ?, ?, ?, ?, ?)");
            // Use 's' for string, 'i' for integer, 'i' for category_id which can be NULL so it behaves as an integer or NULL
            mysqli_stmt_bind_param($stmt, "ssssii", $place_name, $description, $address, $image_url, $city_id, $category_id);
            if (mysqli_stmt_execute($stmt)) {
                $message = "Place added successfully!";
                $message_type = "success";
            } else {
                $message = "Error adding place: " . mysqli_error($link);
                $message_type = "error";
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// Handle Delete Place
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $place_id = intval($_GET['id']);
    $stmt = mysqli_prepare($link, "DELETE FROM places WHERE place_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $place_id);
    if (mysqli_stmt_execute($stmt)) {
        $message = "Place deleted successfully!";
        $message_type = "success";
    } else {
        $message = "Error deleting place: " . mysqli_error($link);
        $message_type = "error";
    }
    mysqli_stmt_close($stmt);
}

// Fetch place for editing
$edit_place = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $place_id = intval($_GET['id']);
    $stmt = mysqli_prepare($link, "SELECT place_id, place_name, description, address, image_url, city_id, category_id FROM places WHERE place_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $place_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $edit_place = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    if (!$edit_place) {
        $message = "Place not found for editing.";
        $message_type = "error";
    }
}

// Fetch all cities for dropdown
$cities = [];
$sql_cities = "SELECT city_id, city_name FROM cities ORDER BY city_name ASC";
$result_cities = mysqli_query($link, $sql_cities);
if ($result_cities) {
    while ($row = mysqli_fetch_assoc($result_cities)) {
        $cities[] = $row;
    }
    mysqli_free_result($result_cities);
} else {
    $message .= " Error fetching cities: " . mysqli_error($link);
    $message_type = "error";
}

// Fetch all categories for dropdown
$categories_list = []; // Renamed to avoid conflict with a potential $categories variable elsewhere
$sql_categories = "SELECT category_id, category_name FROM categories ORDER BY category_name ASC";
$result_categories = mysqli_query($link, $sql_categories);
if ($result_categories) {
    while ($row = mysqli_fetch_assoc($result_categories)) {
        $categories_list[] = $row;
    }
    mysqli_free_result($result_categories);
} else {
    $message .= " Error fetching categories: " . mysqli_error($link);
    $message_type = "error";
}


// Fetch all places for display (joining with cities and categories for display names)
$places = [];
$sql = "SELECT p.place_id, p.place_name, p.address, p.image_url,
               c.city_name, cat.category_name
        FROM places p
        LEFT JOIN cities c ON p.city_id = c.city_id
        LEFT JOIN categories cat ON p.category_id = cat.category_id
        ORDER BY p.place_name ASC";
$result = mysqli_query($link, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $places[] = $row;
    }
    mysqli_free_result($result);
} else {
    $message .= " Error fetching places: " . mysqli_error($link);
    $message_type = "error";
}

?>

            <div class="admin-content-inner">
                <?php if ($message): ?>
                    <div class="admin-message <?php echo $message_type; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <h3><?php echo $edit_place ? 'Edit Place' : 'Add New Place'; ?></h3>
                <form action="manage_places.php" method="POST" class="admin-form">
                    <?php if ($edit_place): ?>
                        <input type="hidden" name="place_id" value="<?php echo htmlspecialchars($edit_place['place_id']); ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="place_name">Place Name:</label>
                        <input type="text" id="place_name" name="place_name" value="<?php echo htmlspecialchars($edit_place['place_name'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="description">Description:</label>
                        <textarea id="description" name="description" rows="4"><?php echo htmlspecialchars($edit_place['description'] ?? ''); ?></textarea>
                    </div>
                    <div class="form-group">
                        <label for="address">Address:</label>
                        <input type="text" id="address" name="address" value="<?php echo htmlspecialchars($edit_place['address'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="image_url">Image URL:</label>
                        <input type="text" id="image_url" name="image_url" value="<?php echo htmlspecialchars($edit_place['image_url'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label for="city_id">City:</label>
                        <select id="city_id" name="city_id" required>
                            <option value="">-- Select City --</option>
                            <?php foreach ($cities as $city): ?>
                                <option value="<?php echo htmlspecialchars($city['city_id']); ?>"
                                    <?php echo (isset($edit_place['city_id']) && $edit_place['city_id'] == $city['city_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($city['city_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="category_id">Category:</label>
                        <select id="category_id" name="category_id">
                            <option value="">-- Select Category (Optional) --</option>
                            <?php foreach ($categories_list as $category): // Using categories_list ?>
                                <option value="<?php echo htmlspecialchars($category['category_id']); ?>"
                                    <?php echo (isset($edit_place['category_id']) && $edit_place['category_id'] == $category['category_id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($category['category_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <button type="submit" name="submit_place" class="btn-submit">
                        <?php echo $edit_place ? 'Update Place' : 'Add Place'; ?>
                    </button>
                </form>

                <h3 class="mt-40">Existing Places</h3>
                <?php if (empty($places)): ?>
                    <p>No places found. Start by adding one above!</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Place Name</th>
                                    <th>City</th>
                                    <th>Category</th>
                                    <th>Address</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($places as $place): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($place['place_id']); ?></td>
                                        <td><?php echo htmlspecialchars($place['place_name']); ?></td>
                                        <td><?php echo htmlspecialchars($place['city_name'] ?? 'N/A'); ?></td>
                                        <td><?php echo htmlspecialchars($place['category_name'] ?? 'Uncategorized'); ?></td>
                                        <td><?php echo htmlspecialchars(substr($place['address'], 0, 50)) . (strlen($place['address']) > 50 ? '...' : ''); ?></td>
                                        <td class="action-buttons">
                                            <a href="manage_places.php?action=edit&id=<?php echo htmlspecialchars($place['place_id']); ?>" class="btn-edit"><i class="fas fa-edit"></i> Edit</a>
                                            <a href="manage_places.php?action=delete&id=<?php echo htmlspecialchars($place['place_id']); ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this place? This action cannot be undone.');"><i class="fas fa-trash-alt"></i> Delete</a>
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
