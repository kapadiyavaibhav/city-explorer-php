<?php
// admin/manage_users.php
require_once 'includes/admin_header.php'; // Includes header, starts session, checks login

$message = '';
$message_type = '';

// Handle Add/Edit User
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_user'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password']; // Raw password if new/changing
    $role = $_POST['role'];
    $user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;

    if (empty($username) || empty($email) || empty($role)) {
        $message = "Username, Email, and Role are required.";
        $message_type = "error";
    } elseif ($user_id === 0 && empty($password)) { // Password required for new users
        $message = "Password is required for new users.";
        $message_type = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Invalid email format.";
        $message_type = "error";
    } else {
        $password_hash = null;
        if (!empty($password)) {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
        }

        if ($user_id > 0) {
            // Update existing user
            if ($password_hash) {
                // Update with new password
                $stmt = mysqli_prepare($link, "UPDATE users SET username = ?, email = ?, password_hash = ?, role = ? WHERE user_id = ?");
                mysqli_stmt_bind_param($stmt, "ssssi", $username, $email, $password_hash, $role, $user_id);
            } else {
                // Update without changing password
                $stmt = mysqli_prepare($link, "UPDATE users SET username = ?, email = ?, role = ? WHERE user_id = ?");
                mysqli_stmt_bind_param($stmt, "sssi", $username, $email, $role, $user_id);
            }
            
            if (mysqli_stmt_execute($stmt)) {
                $message = "User updated successfully!";
                $message_type = "success";
            } else {
                if (mysqli_errno($link) == 1062) { // Duplicate entry error code (username/email)
                    $message = "Error: Username or Email already exists.";
                } else {
                    $message = "Error updating user: " . mysqli_error($link);
                }
                $message_type = "error";
            }
            mysqli_stmt_close($stmt);
        } else {
            // Add new user
            $stmt = mysqli_prepare($link, "INSERT INTO users (username, email, password_hash, role) VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($stmt, "ssss", $username, $email, $password_hash, $role);
            if (mysqli_stmt_execute($stmt)) {
                $message = "User added successfully!";
                $message_type = "success";
            } else {
                if (mysqli_errno($link) == 1062) { // Duplicate entry error code (username/email)
                    $message = "Error: Username or Email already exists.";
                } else {
                    $message = "Error adding user: " . mysqli_error($link);
                }
                $message_type = "error";
            }
            mysqli_stmt_close($stmt);
        }
    }
}

// Handle Delete User
if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
    $user_id = intval($_GET['id']);
    
    // Prevent deleting the currently logged-in admin user
    if (isset($_SESSION['admin_user_id']) && $_SESSION['admin_user_id'] == $user_id) {
        $message = "You cannot delete your own admin account while logged in.";
        $message_type = "error";
    } else {
        $stmt = mysqli_prepare($link, "DELETE FROM users WHERE user_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $user_id);
        if (mysqli_stmt_execute($stmt)) {
            $message = "User deleted successfully!";
            $message_type = "success";
        } else {
            $message = "Error deleting user: " . mysqli_error($link);
            $message_type = "error";
        }
        mysqli_stmt_close($stmt);
    }
}

// Fetch user for editing
$edit_user = null;
if (isset($_GET['action']) && $_GET['action'] == 'edit' && isset($_GET['id'])) {
    $user_id = intval($_GET['id']);
    $stmt = mysqli_prepare($link, "SELECT user_id, username, email, role FROM users WHERE user_id = ?");
    mysqli_stmt_bind_param($stmt, "i", $user_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $edit_user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    if (!$edit_user) {
        $message = "User not found for editing.";
        $message_type = "error";
    }
}

// Fetch all users for display
$users = [];
$sql = "SELECT user_id, username, email, role, created_at FROM users ORDER BY username ASC";
$result = mysqli_query($link, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $users[] = $row;
    }
    mysqli_free_result($result);
} else {
    $message .= " Error fetching users: " . mysqli_error($link);
    $message_type = "error";
}

?>

            <div class="admin-content-inner">
                <?php if ($message): ?>
                    <div class="admin-message <?php echo $message_type; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <h3><?php echo $edit_user ? 'Edit User' : 'Add New User'; ?></h3>
                <form action="manage_users.php" method="POST" class="admin-form">
                    <?php if ($edit_user): ?>
                        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($edit_user['user_id']); ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label for="username">Username:</label>
                        <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($edit_user['username'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($edit_user['email'] ?? ''); ?>" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Password: <?php echo $edit_user ? '(Leave blank to keep current password)' : ''; ?></label>
                        <input type="password" id="password" name="password">
                    </div>
                    <div class="form-group">
                        <label for="role">Role:</label>
                        <select id="role" name="role" required>
                            <option value="user" <?php echo (isset($edit_user['role']) && $edit_user['role'] == 'user') ? 'selected' : ''; ?>>User</option>
                            <option value="admin" <?php echo (isset($edit_user['role']) && $edit_user['role'] == 'admin') ? 'selected' : ''; ?>>Admin</option>
                        </select>
                    </div>
                    <button type="submit" name="submit_user" class="btn-submit">
                        <?php echo $edit_user ? 'Update User' : 'Add User'; ?>
                    </button>
                </form>

                <h3 class="mt-40">Existing Users</h3>
                <?php if (empty($users)): ?>
                    <p>No users found. Start by adding one above!</p>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Registered On</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($users as $user): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($user['user_id']); ?></td>
                                        <td><?php echo htmlspecialchars($user['username']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><?php echo htmlspecialchars(ucfirst($user['role'])); ?></td>
                                        <td><?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($user['created_at']))); ?></td>
                                        <td class="action-buttons">
                                            <a href="manage_users.php?action=edit&id=<?php echo htmlspecialchars($user['user_id']); ?>" class="btn-edit"><i class="fas fa-edit"></i> Edit</a>
                                            <a href="manage_users.php?action=delete&id=<?php echo htmlspecialchars($user['user_id']); ?>" class="btn-delete" onclick="return confirm('Are you sure you want to delete this user? This action cannot be undone.');"><i class="fas fa-trash-alt"></i> Delete</a>
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
