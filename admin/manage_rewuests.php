<?php
// manage_requests.php
// Single giant file that merges category-specific manage forms (exact fields from your DB schema)
// Uses core PHP (mysqli). Assumes includes/conf.php provides $conn (mysqli) and admin header/footer exist.

require_once "includes/admin_header.php";
require_once "includes/conf.php";

/**
 * Mapping between category name (as in category.cat_name) and temp/main tables.
 * These names come from your SQL dump.
 */
$tables = [
    "College"         => ["temp" => "t_colleges",    "main" => "colleges"],
    "Hospital"        => ["temp" => "t_hospital",    "main" => "hospitals"],
    "HistoricalPlace" => ["temp" => "t_h_p",         "main" => "h_p"],
    "PG"              => ["temp" => "t_pgs",        "main" => "pgs"],
    "Restaurant"      => ["temp" => "t_restaurants", "main" => "restaurants"],
    "School"          => ["temp" => "t_schools",     "main" => "schools"],
];

// Helper: sanitize POST values for update building
function esc($conn, $v) {
    return $conn->real_escape_string($v);
}

// Handle Approve / Reject / Update actions
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'], $_POST['id'], $_POST['category'])) {
    $id = intval($_POST['id']);
    $cat = $_POST['category'];

    if (isset($tables[$cat])) {
        $temp = $tables[$cat]['temp'];
        $main = $tables[$cat]['main'];

        if ($_POST['action'] === 'approve') {
            // Insert into main table from temp, then delete temp
            // Use explicit queries but trusting table column order from your SQL dump (they match)
            $id_safe = intval($id);
            $sqlInsert = "INSERT INTO `$main` SELECT * FROM `$temp` WHERE id=$id_safe";
            if ($conn->query($sqlInsert)) {
                $conn->query("DELETE FROM `$temp` WHERE id=$id_safe");
                $flash = "Record approved and moved to $main.";
            } else {
                $flash = "Error approving record: " . $conn->error;
            }
        } elseif ($_POST['action'] === 'reject') {
            $id_safe = intval($id);
            if ($conn->query("DELETE FROM `$temp` WHERE id=$id_safe")) {
                $flash = "Record rejected and deleted.";
            } else {
                $flash = "Error rejecting record: " . $conn->error;
            }
        } elseif ($_POST['action'] === 'update') {
            // Build update query from posted fields (excluding control fields)
            $sets = [];
            foreach ($_POST as $k => $v) {
                if (in_array($k, ['action','id','category'])) continue;
                // skip file inputs if any - none here
                $safeVal = esc($conn, $v);
                $sets[] = "`$k` = '$safeVal'";
            }
            if (!empty($sets)) {
                $sql = "UPDATE `$temp` SET " . implode(", ", $sets) . " WHERE id=" . intval($id);
                if ($conn->query($sql)) {
                    $flash = "Record updated in temporary table.";
                } else {
                    $flash = "Error updating record: " . $conn->error;
                }
            } else {
                $flash = "No fields to update.";
            }
        }
    } else {
        $flash = "Unknown category selected.";
    }

    // redirect back to GET to avoid form resubmission (preserve category parameter)
    $redirect_cat = isset($_POST['category']) ? rawurlencode($_POST['category']) : '';
    header("Location: manage_requests.php" . ($redirect_cat ? "?category=$redirect_cat" : ""));
    exit;
}

// Selected category (from dropdown)
$selectedCategory = isset($_GET['category']) ? $_GET['category'] : "";

// Fetch categories from DB (category table)
$categories = [];
$resCats = $conn->query("SELECT cat_name FROM category ORDER BY cat_name ASC");
if ($resCats) {
    while ($r = $resCats->fetch_assoc()) $categories[] = $r['cat_name'];
}

// Fetch pending requests from selected temp table
$requests = [];
if ($selectedCategory && isset($tables[$selectedCategory])) {
    $tempTable = $tables[$selectedCategory]['temp'];
    $res = $conn->query("SELECT * FROM `$tempTable` ORDER BY id DESC");
    if ($res) {
        while ($r = $res->fetch_assoc()) $requests[] = $r;
    }
}

/*
  We will produce modals that include full forms for each category exactly according to the DB columns.
  For each category modal we will include all columns from its temp table except `id` and `created_at` (if present).
  The forms below follow the fields seen in your SQL dump.
*/
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<title>Manage Requests - Admin</title>
<meta name="viewport" content="width=device-width,initial-scale=1" />
<style>
/* Basic styling to follow structure from your manage_* files (kept simple & clean) */
body { font-family: Arial, Helvetica, sans-serif; background: #f7f9fc; color: #222; margin:0; padding:20px; }
.container { max-width:1200px; margin:0 auto; background:#fff; padding:20px; border-radius:8px; box-shadow:0 6px 18px rgba(0,0,0,0.06); }
h1 { margin:0 0 16px 0; font-size:22px; }
.selector { margin-bottom:16px; }
select { padding:8px 10px; font-size:14px; border-radius:4px; border:1px solid #ccc; }
table.requests { width:100%; border-collapse: collapse; margin-top:12px; }
table.requests th, table.requests td { border:1px solid #e1e6ef; padding:10px; text-align:left; vertical-align:top; }
table.requests th { background:#f4f6fb; }
.action-btn { padding:6px 10px; border:none; border-radius:4px; cursor:pointer; font-size:13px; margin-right:6px; }
.btn-approve { background:#28a745; color:#fff; }
.btn-reject { background:#dc3545; color:#fff; }
.btn-view { background:#007bff; color:#fff; }
.flash { padding:10px; margin-bottom:12px; background:#e9f7ef; color:#114b2b; border-radius:4px; }

/* Modal styles */
.modal { display:none; position:fixed; z-index:2000; left:0; top:0; width:100%; height:100%; overflow:auto; background:rgba(0,0,0,0.65); }
.modal-inner { background:#fff; margin:40px auto; padding:20px; border-radius:6px; width:90%; max-width:900px; box-shadow:0 10px 40px rgba(0,0,0,0.3); position:relative; }
.modal-close { position:absolute; right:12px; top:8px; cursor:pointer; font-size:22px; color:#666; }
.form-row { display:flex; gap:12px; margin-bottom:10px; }
.form-col { flex:1; }
label { display:block; font-weight:600; margin-bottom:6px; font-size:13px; color:#333; }
input[type="text"], input[type="email"], input[type="number"], textarea, select { width:100%; padding:8px 10px; border:1px solid #ccc; border-radius:4px; font-size:14px; box-sizing:border-box; }
textarea { min-height:80px; resize:vertical; }
.modal-actions { margin-top:14px; text-align:right; }
.small-muted { font-size:12px; color:#666; margin-top:6px; }
@media (max-width:700px) {
    .form-row { flex-direction:column; }
}
</style>

<script>
function openModal(id){ var el=document.getElementById('modal-'+id); if(el) el.style.display='block'; }
function closeModal(id){ var el=document.getElementById('modal-'+id); if(el) el.style.display='none'; }
function closeAllModals(){ var els=document.getElementsByClassName('modal'); for(var i=0;i<els.length;i++) els[i].style.display='none'; }
window.onclick = function(event){ if(event.target.classList && event.target.classList.contains('modal')) { event.target.style.display='none'; } }
</script>
</head>
<body>
<div class="container">
    <h1>Manage Requests</h1>

    <?php if (!empty($flash)): ?>
        <div class="flash"><?php echo htmlspecialchars($flash); ?></div>
    <?php endif; ?>

    <div class="selector">
        <form method="get" id="catForm">
            <label for="category">Select Category</label>
            <select name="category" id="category" onchange="document.getElementById('catForm').submit();">
                <option value="">-- Choose Category --</option>
                <?php foreach ($categories as $c): ?>
                    <option value="<?php echo htmlspecialchars($c); ?>" <?php echo ($c === $selectedCategory) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($c); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <?php if (!$selectedCategory): ?>
        <p class="small-muted">Please select a category to view pending requests.</p>
    <?php else: ?>

        <h3>Pending requests for <?php echo htmlspecialchars($selectedCategory); ?></h3>

        <?php if (empty($requests)): ?>
            <p class="small-muted">No pending requests for this category.</p>
        <?php else: ?>

            <table class="requests">
                <thead>
                    <tr>
                        <th style="width:70px">ID</th>
                        <th style="width:220px">Name / Title</th>
                        <th>City</th>
                        <th>Contact / Email</th>
                        <th style="width:200px">Submitted By</th>
                        <th style="width:220px">Actions</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($requests as $row): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td><?php
                            // common name fields differ by category; try common keys
                            $displayName = $row['name'] ?? $row['place_name'] ?? $row['title'] ?? '';
                            echo htmlspecialchars($displayName);
                        ?></td>
                        <td><?php echo htmlspecialchars($row['city_name'] ?? $row['address'] ?? ''); ?></td>
                        <td>
                            <?php echo htmlspecialchars($row['contact'] ?? ''); ?><br>
                            <?php echo htmlspecialchars($row['email'] ?? ''); ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['user_email'] ?? ''); ?></td>
                        <td>
                            <button class="action-btn btn-view" type="button" onclick="openModal(<?php echo $row['id']; ?>)">View / Edit</button>

                            <!-- Approve form -->
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                <input type="hidden" name="category" value="<?php echo htmlspecialchars($selectedCategory); ?>">
                                <button class="action-btn btn-approve" name="action" value="approve" type="submit">Approve</button>
                            </form>

                            <!-- Reject form -->
                            <form method="post" style="display:inline;">
                                <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">
                                <input type="hidden" name="category" value="<?php echo htmlspecialchars($selectedCategory); ?>">
                                <button class="action-btn btn-reject" name="action" value="reject" type="submit" onclick="return confirm('Reject and delete this request?')">Reject</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Generate modals for each record (category-specific forms) -->
            <?php foreach ($requests as $row):
                $rid = intval($row['id']);
                // Prepare commonly used values
                $cat = $selectedCategory;
            ?>
            <div id="modal-<?php echo $rid; ?>" class="modal" aria-hidden="true">
                <div class="modal-inner" role="dialog" aria-modal="true">
                    <span class="modal-close" onclick="closeModal(<?php echo $rid; ?>)">&times;</span>

                    <h2>Edit <?php echo htmlspecialchars($cat); ?> Request (ID: <?php echo $rid; ?>)</h2>

                    <!-- Category-specific form markup below. Each form posts action=update/approve/reject -->
                    <?php if ($cat === 'Hospital'): ?>
                        <!-- Hospital form (fields from t_hospital) -->
                        <form method="post">
                            <input type="hidden" name="id" value="<?php echo $rid; ?>">
                            <input type="hidden" name="category" value="Hospital">

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Hospital Name</label>
                                    <input type="text" name="name" value="<?php echo htmlspecialchars($row['name'] ?? ''); ?>">
                                </div>
                                <div class="form-col">
                                    <label>Image (filename)</label>
                                    <input type="text" name="img" value="<?php echo htmlspecialchars($row['img'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Address</label>
                                    <input type="text" name="address" value="<?php echo htmlspecialchars($row['address'] ?? ''); ?>">
                                </div>
                                <div class="form-col">
                                    <label>City Name</label>
                                    <input type="text" name="city_name" value="<?php echo htmlspecialchars($row['city_name'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Contact</label>
                                    <input type="text" name="contact" value="<?php echo htmlspecialchars($row['contact'] ?? ''); ?>">
                                </div>
                                <div class="form-col">
                                    <label>Email</label>
                                    <input type="email" name="email" value="<?php echo htmlspecialchars($row['email'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Opening Hours</label>
                                    <input type="text" name="opening_hours" value="<?php echo htmlspecialchars($row['opening_hours'] ?? ''); ?>">
                                </div>
                                <div class="form-col">
                                    <label>Emergency</label>
                                    <input type="text" name="emergency" value="<?php echo htmlspecialchars($row['emergency'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Visiting Hours</label>
                                    <input type="text" name="visiting_hours" value="<?php echo htmlspecialchars($row['visiting_hours'] ?? ''); ?>">
                                </div>
                                <div class="form-col">
                                    <label>Departments</label>
                                    <input type="text" name="departments" value="<?php echo htmlspecialchars($row['departments'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Services</label>
                                    <textarea name="services"><?php echo htmlspecialchars($row['services'] ?? ''); ?></textarea>
                                </div>
                                <div class="form-col">
                                    <label>Doctors</label>
                                    <textarea name="doctors"><?php echo htmlspecialchars($row['doctors'] ?? ''); ?></textarea>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Beds (General)</label>
                                    <input type="number" name="beds_general" value="<?php echo htmlspecialchars($row['beds_general'] ?? ''); ?>">
                                </div>
                                <div class="form-col">
                                    <label>Beds (ICU)</label>
                                    <input type="number" name="beds_icu" value="<?php echo htmlspecialchars($row['beds_icu'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Beds (Private)</label>
                                    <input type="number" name="beds_private" value="<?php echo htmlspecialchars($row['beds_private'] ?? ''); ?>">
                                </div>
                                <div class="form-col">
                                    <label>Laboratory</label>
                                    <input type="text" name="laboratory" value="<?php echo htmlspecialchars($row['laboratory'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Diagnostics</label>
                                    <input type="text" name="diagnostics" value="<?php echo htmlspecialchars($row['diagnostics'] ?? ''); ?>">
                                </div>
                                <div class="form-col">
                                    <label>Pharmacy</label>
                                    <input type="text" name="pharmacy" value="<?php echo htmlspecialchars($row['pharmacy'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Ambulance</label>
                                    <input type="text" name="ambulance" value="<?php echo htmlspecialchars($row['ambulance'] ?? ''); ?>">
                                </div>
                                <div class="form-col">
                                    <label>Wheelchair Accessible</label>
                                    <input type="text" name="wheelchair_accessible" value="<?php echo htmlspecialchars($row['wheelchair_accessible'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Insurance</label>
                                    <input type="text" name="insurance" value="<?php echo htmlspecialchars($row['insurance'] ?? ''); ?>">
                                </div>
                                <div class="form-col">
                                    <label>History</label>
                                    <textarea name="history"><?php echo htmlspecialchars($row['history'] ?? ''); ?></textarea>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Gallery (filenames comma separated)</label>
                                    <input type="text" name="gallery" value="<?php echo htmlspecialchars($row['gallery'] ?? ''); ?>">
                                </div>
                                <div class="form-col">
                                    <label>Emergency Procedures</label>
                                    <textarea name="emergency_procedures"><?php echo htmlspecialchars($row['emergency_procedures'] ?? ''); ?></textarea>
                                </div>
                            </div>

                            <div class="modal-actions">
                                <button class="action-btn" type="submit" name="action" value="update">Save Changes</button>
                                <button class="action-btn btn-approve" type="submit" name="action" value="approve">Approve</button>
                                <button class="action-btn btn-reject" type="submit" name="action" value="reject" onclick="return confirm('Reject and delete this request?')">Reject</button>
                            </div>
                        </form>

                    <?php elseif ($cat === 'School'): ?>
                        <!-- School form (fields from t_schools) -->
                        <form method="post">
                            <input type="hidden" name="id" value="<?php echo $rid; ?>">
                            <input type="hidden" name="category" value="School">

                            <div class="form-row">
                                <div class="form-col">
                                    <label>School Name</label>
                                    <input type="text" name="name" value="<?php echo htmlspecialchars($row['name'] ?? ''); ?>">
                                </div>
                                <div class="form-col">
                                    <label>Image (filename)</label>
                                    <input type="text" name="img" value="<?php echo htmlspecialchars($row['img'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Address</label>
                                    <input type="text" name="address" value="<?php echo htmlspecialchars($row['address'] ?? ''); ?>">
                                </div>
                                <div class="form-col">
                                    <label>City Name</label>
                                    <input type="text" name="city_name" value="<?php echo htmlspecialchars($row['city_name'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Contact</label>
                                    <input type="text" name="contact" value="<?php echo htmlspecialchars($row['contact'] ?? ''); ?>">
                                </div>
                                <div class="form-col">
                                    <label>Email</label>
                                    <input type="email" name="email" value="<?php echo htmlspecialchars($row['email'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Website</label>
                                    <input type="text" name="website" value="<?php echo htmlspecialchars($row['website'] ?? ''); ?>">
                                </div>
                                <div class="form-col">
                                    <label>Established</label>
                                    <input type="text" name="established" value="<?php echo htmlspecialchars($row['established'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Grades</label>
                                    <input type="text" name="grades" value="<?php echo htmlspecialchars($row['grades'] ?? ''); ?>">
                                </div>
                                <div class="form-col">
                                    <label>Departments</label>
                                    <textarea name="departments"><?php echo htmlspecialchars($row['departments'] ?? ''); ?></textarea>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Facilities</label>
                                    <textarea name="facilities"><?php echo htmlspecialchars($row['facilities'] ?? ''); ?></textarea>
                                </div>
                                <div class="form-col">
                                    <label>Faculty</label>
                                    <textarea name="faculty"><?php echo htmlspecialchars($row['faculty'] ?? ''); ?></textarea>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Admission Process</label>
                                    <textarea name="admission_process"><?php echo htmlspecialchars($row['admission_process'] ?? ''); ?></textarea>
                                </div>
                                <div class="form-col">
                                    <label>Gallery (filenames)</label>
                                    <input type="text" name="gallery" value="<?php echo htmlspecialchars($row['gallery'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Scholarships</label>
                                    <input type="text" name="scholarships" value="<?php echo htmlspecialchars($row['scholarships'] ?? ''); ?>">
                                </div>
                                <div class="form-col">
                                    <label>Hostel</label>
                                    <input type="text" name="hostel" value="<?php echo htmlspecialchars($row['hostel'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Transport</label>
                                    <input type="text" name="transport" value="<?php echo htmlspecialchars($row['transport'] ?? ''); ?>">
                                </div>
                                <div class="form-col">
                                    <label>Library</label>
                                    <input type="text" name="library" value="<?php echo htmlspecialchars($row['library'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Sports</label>
                                    <input type="text" name="sports" value="<?php echo htmlspecialchars($row['sports'] ?? ''); ?>">
                                </div>
                                <div class="form-col">
                                    <label>Cafeteria</label>
                                    <input type="text" name="cafeteria" value="<?php echo htmlspecialchars($row['cafeteria'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Disabled Friendly</label>
                                    <input type="text" name="disabled_friendly" value="<?php echo htmlspecialchars($row['disabled_friendly'] ?? ''); ?>">
                                </div>
                                <div class="form-col">
                                    <label>Created At</label>
                                    <input type="text" name="created_at" value="<?php echo htmlspecialchars($row['created_at'] ?? ''); ?>" readonly>
                                </div>
                            </div>

                            <div class="modal-actions">
                                <button class="action-btn" type="submit" name="action" value="update">Save Changes</button>
                                <button class="action-btn btn-approve" type="submit" name="action" value="approve">Approve</button>
                                <button class="action-btn btn-reject" type="submit" name="action" value="reject" onclick="return confirm('Reject and delete this request?')">Reject</button>
                            </div>
                        </form>

                    <?php elseif ($cat === 'College'): ?>
                        <!-- College form (fields from t_colleges) -->
                        <form method="post">
                            <input type="hidden" name="id" value="<?php echo $rid; ?>">
                            <input type="hidden" name="category" value="College">

                            <div class="form-row">
                                <div class="form-col">
                                    <label>College Name</label>
                                    <input type="text" name="name" value="<?php echo htmlspecialchars($row['name'] ?? ''); ?>">
                                </div>
                                <div class="form-col">
                                    <label>Image (filename)</label>
                                    <input type="text" name="img" value="<?php echo htmlspecialchars($row['img'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Address</label>
                                    <input type="text" name="address" value="<?php echo htmlspecialchars($row['address'] ?? ''); ?>">
                                </div>
                                <div class="form-col">
                                    <label>City Name</label>
                                    <input type="text" name="city_name" value="<?php echo htmlspecialchars($row['city_name'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Contact</label>
                                    <input type="text" name="contact" value="<?php echo htmlspecialchars($row['contact'] ?? ''); ?>">
                                </div>
                                <div class="form-col">
                                    <label>Email</label>
                                    <input type="email" name="email" value="<?php echo htmlspecialchars($row['email'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Website</label>
                                    <input type="text" name="website" value="<?php echo htmlspecialchars($row['website'] ?? ''); ?>">
                                </div>
                                <div class="form-col">
                                    <label>Established</label>
                                    <input type="text" name="established" value="<?php echo htmlspecialchars($row['established'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Courses</label>
                                    <textarea name="courses"><?php echo htmlspecialchars($row['courses'] ?? ''); ?></textarea>
                                </div>
                                <div class="form-col">
                                    <label>Departments</label>
                                    <textarea name="departments"><?php echo htmlspecialchars($row['departments'] ?? ''); ?></textarea>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Facilities</label>
                                    <textarea name="facilities"><?php echo htmlspecialchars($row['facilities'] ?? ''); ?></textarea>
                                </div>
                                <div class="form-col">
                                    <label>Faculty</label>
                                    <textarea name="faculty"><?php echo htmlspecialchars($row['faculty'] ?? ''); ?></textarea>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Admission Process</label>
                                    <textarea name="admission_process"><?php echo htmlspecialchars($row['admission_process'] ?? ''); ?></textarea>
                                </div>
                                <div class="form-col">
                                    <label>Gallery (filenames)</label>
                                    <input type="text" name="gallery" value="<?php echo htmlspecialchars($row['gallery'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Scholarships</label>
                                    <input type="text" name="scholarships" value="<?php echo htmlspecialchars($row['scholarships'] ?? ''); ?>">
                                </div>
                                <div class="form-col">
                                    <label>Hostel</label>
                                    <input type="text" name="hostel" value="<?php echo htmlspecialchars($row['hostel'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Transport</label>
                                    <input type="text" name="transport" value="<?php echo htmlspecialchars($row['transport'] ?? ''); ?>">
                                </div>
                                <div class="form-col">
                                    <label>Library</label>
                                    <input type="text" name="library" value="<?php echo htmlspecialchars($row['library'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="modal-actions">
                                <button class="action-btn" type="submit" name="action" value="update">Save Changes</button>
                                <button class="action-btn btn-approve" type="submit" name="action" value="approve">Approve</button>
                                <button class="action-btn btn-reject" type="submit" name="action" value="reject" onclick="return confirm('Reject and delete this request?')">Reject</button>
                            </div>
                        </form>

                    <?php elseif ($cat === 'Restaurant'): ?>
                        <!-- Restaurant form (fields from t_restaurants) -->
                        <form method="post">
                            <input type="hidden" name="id" value="<?php echo $rid; ?>">
                            <input type="hidden" name="category" value="Restaurant">

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Restaurant Name</label>
                                    <input type="text" name="name" value="<?php echo htmlspecialchars($row['name'] ?? ''); ?>">
                                </div>
                                <div class="form-col">
                                    <label>Image (filename)</label>
                                    <input type="text" name="img" value="<?php echo htmlspecialchars($row['img'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Address</label>
                                    <input type="text" name="address" value="<?php echo htmlspecialchars($row['address'] ?? ''); ?>">
                                </div>
                                <div class="form-col">
                                    <label>City Name</label>
                                    <input type="text" name="city_name" value="<?php echo htmlspecialchars($row['city_name'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Contact</label>
                                    <input type="text" name="contact" value="<?php echo htmlspecialchars($row['contact'] ?? ''); ?>">
                                </div>
                                <div class="form-col">
                                    <label>Email</label>
                                    <input type="email" name="email" value="<?php echo htmlspecialchars($row['email'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Website</label>
                                    <input type="text" name="website" value="<?php echo htmlspecialchars($row['website'] ?? ''); ?>">
                                </div>
                                <div class="form-col">
                                    <label>Opening Hours</label>
                                    <input type="text" name="opening_hours" value="<?php echo htmlspecialchars($row['opening_hours'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Closed On</label>
                                    <input type="text" name="closed_on" value="<?php echo htmlspecialchars($row['closed_on'] ?? ''); ?>">
                                </div>
                                <div class="form-col">
                                    <label>Reservation Required</label>
                                    <input type="text" name="reservation_required" value="<?php echo htmlspecialchars($row['reservation_required'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Special Offers</label>
                                    <input type="text" name="special_offers" value="<?php echo htmlspecialchars($row['special_offers'] ?? ''); ?>">
                                </div>
                                <div class="form-col">
                                    <label>Cuisines</label>
                                    <textarea name="cuisines"><?php echo htmlspecialchars($row['cuisines'] ?? ''); ?></textarea>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Menu</label>
                                    <textarea name="menu"><?php echo htmlspecialchars($row['menu'] ?? ''); ?></textarea>
                                </div>
                                <div class="form-col">
                                    <label>Services</label>
                                    <textarea name="services"><?php echo htmlspecialchars($row['services'] ?? ''); ?></textarea>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Chefs</label>
                                    <input type="text" name="chefs" value="<?php echo htmlspecialchars($row['chefs'] ?? ''); ?>">
                                </div>
                                <div class="form-col">
                                    <label>Wi-Fi</label>
                                    <input type="text" name="wifi" value="<?php echo htmlspecialchars($row['wifi'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Parking</label>
                                    <input type="text" name="parking" value="<?php echo htmlspecialchars($row['parking'] ?? ''); ?>">
                                </div>
                                <div class="form-col">
                                    <label>Outdoor Seating</label>
                                    <input type="text" name="outdoor_seating" value="<?php echo htmlspecialchars($row['outdoor_seating'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Live Music</label>
                                    <input type="text" name="live_music" value="<?php echo htmlspecialchars($row['live_music'] ?? ''); ?>">
                                </div>
                                <div class="form-col">
                                    <label>Bar</label>
                                    <input type="text" name="bar" value="<?php echo htmlspecialchars($row['bar'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Kids Friendly</label>
                                    <input type="text" name="kids_friendly" value="<?php echo htmlspecialchars($row['kids_friendly'] ?? ''); ?>">
                                </div>
                                <div class="form-col">
                                    <label>Pet Friendly</label>
                                    <input type="text" name="pet_friendly" value="<?php echo htmlspecialchars($row['pet_friendly'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Wheelchair Accessible</label>
                                    <input type="text" name="wheelchair_accessible" value="<?php echo htmlspecialchars($row['wheelchair_accessible'] ?? ''); ?>">
                                </div>
                                <div class="form-col">
                                    <label>Payment Options</label>
                                    <input type="text" name="payment_options" value="<?php echo htmlspecialchars($row['payment_options'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Description</label>
                                    <textarea name="description"><?php echo htmlspecialchars($row['description'] ?? ''); ?></textarea>
                                </div>
                                <div class="form-col">
                                    <label>Gallery (filenames)</label>
                                    <input type="text" name="gallery" value="<?php echo htmlspecialchars($row['gallery'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="modal-actions">
                                <button class="action-btn" type="submit" name="action" value="update">Save Changes</button>
                                <button class="action-btn btn-approve" type="submit" name="action" value="approve">Approve</button>
                                <button class="action-btn btn-reject" type="submit" name="action" value="reject" onclick="return confirm('Reject and delete this request?')">Reject</button>
                            </div>
                        </form>

                    <?php elseif ($cat === 'PG'): ?>
                        <!-- PG form (fields from t_pgs) -->
                        <form method="post">
                            <input type="hidden" name="id" value="<?php echo $rid; ?>">
                            <input type="hidden" name="category" value="PG">

                            <div class="form-row">
                                <div class="form-col">
                                    <label>PG Name</label>
                                    <input type="text" name="name" value="<?php echo htmlspecialchars($row['name'] ?? ''); ?>">
                                </div>
                                <div class="form-col">
                                    <label>Image</label>
                                    <input type="text" name="img" value="<?php echo htmlspecialchars($row['img'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Address</label>
                                    <input type="text" name="address" value="<?php echo htmlspecialchars($row['address'] ?? ''); ?>">
                                </div>
                                <div class="form-col">
                                    <label>City Name</label>
                                    <input type="text" name="city_name" value="<?php echo htmlspecialchars($row['city_name'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Contact</label>
                                    <input type="text" name="contact" value="<?php echo htmlspecialchars($row['contact'] ?? ''); ?>">
                                </div>
                                <div class="form-col">
                                    <label>Email</label>
                                    <input type="email" name="email" value="<?php echo htmlspecialchars($row['email'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Website</label>
                                    <input type="text" name="website" value="<?php echo htmlspecialchars($row['website'] ?? ''); ?>">
                                </div>
                                <div class="form-col">
                                    <label>Established</label>
                                    <input type="text" name="established" value="<?php echo htmlspecialchars($row['established'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Rooms</label>
                                    <input type="text" name="rooms" value="<?php echo htmlspecialchars($row['rooms'] ?? ''); ?>">
                                </div>
                                <div class="form-col">
                                    <label>Room Types</label>
                                    <textarea name="room_types"><?php echo htmlspecialchars($row['room_types'] ?? ''); ?></textarea>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Facilities</label>
                                    <textarea name="facilities"><?php echo htmlspecialchars($row['facilities'] ?? ''); ?></textarea>
                                </div>
                                <div class="form-col">
                                    <label>Faculty/Owner</label>
                                    <input type="text" name="faculty" value="<?php echo htmlspecialchars($row['faculty'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Admission Process</label>
                                    <textarea name="admission_process"><?php echo htmlspecialchars($row['admission_process'] ?? ''); ?></textarea>
                                </div>
                                <div class="form-col">
                                    <label>Gallery</label>
                                    <input type="text" name="gallery" value="<?php echo htmlspecialchars($row['gallery'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Scholorships</label>
                                    <input type="text" name="scholarships" value="<?php echo htmlspecialchars($row['scholarships'] ?? ''); ?>">
                                </div>
                                <div class="form-col">
                                    <label>Hostel</label>
                                    <input type="text" name="hostel" value="<?php echo htmlspecialchars($row['hostel'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="modal-actions">
                                <button class="action-btn" type="submit" name="action" value="update">Save Changes</button>
                                <button class="action-btn btn-approve" type="submit" name="action" value="approve">Approve</button>
                                <button class="action-btn btn-reject" type="submit" name="action" value="reject" onclick="return confirm('Reject and delete this request?')">Reject</button>
                            </div>
                        </form>

                    <?php elseif ($cat === 'HistoricalPlace'): ?>
                        <!-- Historical Place form (fields from t_h_p) -->
                        <form method="post">
                            <input type="hidden" name="id" value="<?php echo $rid; ?>">
                            <input type="hidden" name="category" value="HistoricalPlace">

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Place Name</label>
                                    <input type="text" name="name" value="<?php echo htmlspecialchars($row['name'] ?? ''); ?>">
                                </div>
                                <div class="form-col">
                                    <label>Image</label>
                                    <input type="text" name="img" value="<?php echo htmlspecialchars($row['img'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Address</label>
                                    <input type="text" name="address" value="<?php echo htmlspecialchars($row['address'] ?? ''); ?>">
                                </div>
                                <div class="form-col">
                                    <label>City Name</label>
                                    <input type="text" name="city_name" value="<?php echo htmlspecialchars($row['city_name'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Contact</label>
                                    <input type="text" name="contact" value="<?php echo htmlspecialchars($row['contact'] ?? ''); ?>">
                                </div>
                                <div class="form-col">
                                    <label>Email</label>
                                    <input type="email" name="email" value="<?php echo htmlspecialchars($row['email'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Website</label>
                                    <input type="text" name="website" value="<?php echo htmlspecialchars($row['website'] ?? ''); ?>">
                                </div>
                                <div class="form-col">
                                    <label>Established</label>
                                    <input type="text" name="established" value="<?php echo htmlspecialchars($row['established'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Opening Hours</label>
                                    <input type="text" name="opening_hours" value="<?php echo htmlspecialchars($row['opening_hours'] ?? ''); ?>">
                                </div>
                                <div class="form-col">
                                    <label>Ticket Info</label>
                                    <input type="text" name="ticket_info" value="<?php echo htmlspecialchars($row['ticket_info'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Features</label>
                                    <textarea name="features"><?php echo htmlspecialchars($row['features'] ?? ''); ?></textarea>
                                </div>
                                <div class="form-col">
                                    <label>Guides</label>
                                    <textarea name="guides"><?php echo htmlspecialchars($row['guides'] ?? ''); ?></textarea>
                                </div>
                            </div>

                            <div class="form-row">
                                <div class="form-col">
                                    <label>Gallery</label>
                                    <input type="text" name="gallery" value="<?php echo htmlspecialchars($row['gallery'] ?? ''); ?>">
                                </div>
                                <div class="form-col">
                                    <label>Hostel</label>
                                    <input type="text" name="hostel" value="<?php echo htmlspecialchars($row['hostel'] ?? ''); ?>">
                                </div>
                            </div>

                            <div class="modal-actions">
                                <button class="action-btn" type="submit" name="action" value="update">Save Changes</button>
                                <button class="action-btn btn-approve" type="submit" name="action" value="approve">Approve</button>
                                <button class="action-btn btn-reject" type="submit" name="action" value="reject" onclick="return confirm('Reject and delete this request?')">Reject</button>
                            </div>
                        </form>

                    <?php else: ?>
                        <!-- Generic fallback: list all columns in simple inputs -->
                        <form method="post">
                            <input type="hidden" name="id" value="<?php echo $rid; ?>">
                            <input type="hidden" name="category" value="<?php echo htmlspecialchars($cat); ?>">
                            <?php foreach ($row as $col => $val):
                                if (in_array($col, ['id','created_at'])) continue;
                            ?>
                                <div class="form-row">
                                    <div class="form-col">
                                        <label><?php echo htmlspecialchars(ucfirst(str_replace('_',' ',$col))); ?></label>
                                        <input type="text" name="<?php echo htmlspecialchars($col); ?>" value="<?php echo htmlspecialchars($val); ?>">
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <div class="modal-actions">
                                <button class="action-btn" type="submit" name="action" value="update">Save Changes</button>
                                <button class="action-btn btn-approve" type="submit" name="action" value="approve">Approve</button>
                                <button class="action-btn btn-reject" type="submit" name="action" value="reject" onclick="return confirm('Reject and delete this request?')">Reject</button>
                            </div>
                        </form>
                    <?php endif; ?>

                </div>
            </div>
            <?php endforeach; ?>

        <?php endif; ?>
    <?php endif; ?>

</div> <!-- container -->

<?php
require_once "includes/admin_footer.php";
?>
</body>
</html>
