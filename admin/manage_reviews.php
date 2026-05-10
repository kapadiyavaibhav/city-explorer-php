<?php
// admin/manage_reviews.php
require_once 'includes/admin_header.php'; // Includes header, starts session, checks login

$message = '';
$message_type = '';

// Handle Review Actions (Approve/Reject/Delete)
if (isset($_GET['action']) && isset($_GET['id'])) {
    $review_id = intval($_GET['id']);
    $action = $_GET['action'];

    if ($action === 'approve' || $action === 'reject') {
        $status = ($action === 'approve') ? 'approved' : 'rejected';
        $stmt = mysqli_prepare($link, "UPDATE reviews SET status = ? WHERE review_id = ?");
        mysqli_stmt_bind_param($stmt, "si", $status, $review_id);
        if (mysqli_stmt_execute($stmt)) {
            $message = "Review status updated to " . htmlspecialchars($status) . " successfully!";
            $message_type = "success";
        } else {
            $message = "Error updating review status: " . mysqli_error($link);
            $message_type = "error";
        }
        mysqli_stmt_close($stmt);
    } elseif ($action === 'delete') {
        $stmt = mysqli_prepare($link, "DELETE FROM reviews WHERE review_id = ?");
        mysqli_stmt_bind_param($stmt, "i", $review_id);
        if (mysqli_stmt_execute($stmt)) {
            $message = "Review deleted successfully!";
            $message_type = "success";
        } else {
            $message = "Error deleting review: " . mysqli_error($link);
            $message_type = "error";
        }
        mysqli_stmt_close($stmt);
    }
}

// Fetch all reviews
$reviews = [];
$sql = "SELECT r.review_id, r.rating, r.comment, r.status, r.created_at,
               u.username, p.place_name, c.city_name
        FROM reviews r
        LEFT JOIN users u ON r.user_id = u.user_id
        LEFT JOIN places p ON r.place_id = p.place_id
        LEFT JOIN cities c ON p.city_id = c.city_id
        ORDER BY r.created_at DESC";
$result = mysqli_query($link, $sql);
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $reviews[] = $row;
    }
    mysqli_free_result($result);
} else {
    $message .= " Error fetching reviews: " . mysqli_error($link);
    $message_type = "error";
}

?>

<!-- Inline CSS -->
<style>
body {
    font-family: "Segoe UI", Tahoma, sans-serif;
    background: #f4f6f9;
    margin: 0;
    padding: 0;
}
.admin-content-inner {
    background: #fff;
    padding: 25px;
    margin: 20px auto;
    border-radius: 12px;
    max-width: 1200px;
    box-shadow: 0 4px 10px rgba(0,0,0,0.08);
}
h3 {
    margin-bottom: 20px;
    font-size: 22px;
    color: #222;
    border-left: 4px solid #007bff;
    padding-left: 10px;
}
.admin-message {
    padding: 12px 16px;
    border-radius: 8px;
    margin-bottom: 20px;
    font-weight: 500;
}
.admin-message.success {
    background: #e8f9f0;
    border: 1px solid #28a745;
    color: #1e7e34;
}
.admin-message.error {
    background: #fdeaea;
    border: 1px solid #dc3545;
    color: #a71d2a;
}
.table-responsive {
    overflow-x: auto;
}
.admin-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 14px;
}
.admin-table thead {
    background: #f0f2f5;
}
.admin-table th, 
.admin-table td {
    padding: 12px 14px;
    border-bottom: 1px solid #eee;
    text-align: left;
}
.admin-table th {
    font-weight: 600;
    color: #333;
}
.admin-table tbody tr:hover {
    background: #fafafa;
    transition: 0.2s;
}
.review-status-badge {
    display: inline-block;
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 600;
    text-transform: capitalize;
}
.status-approved {
    background: #e6f7e6;
    color: #28a745;
}
.status-rejected {
    background: #fdeaea;
    color: #dc3545;
}
.status-pending {
    background: #fff3cd;
    color: #856404;
}
.action-buttons a {
    display: inline-block;
    padding: 6px 12px;
    margin: 2px;
    border-radius: 6px;
    font-size: 13px;
    font-weight: 500;
    text-decoration: none;
    transition: 0.2s;
}
.btn-primary {
    background: #007bff;
    color: #fff;
}
.btn-primary:hover {
    background: #0056b3;
}
.btn-warning {
    background: #ffc107;
    color: #212529;
}
.btn-warning:hover {
    background: #e0a800;
}
.btn-delete {
    background: #dc3545;
    color: #fff;
}
.btn-delete:hover {
    background: #c82333;
}
</style>

<div class="admin-content-inner">
    <?php if ($message): ?>
        <div class="admin-message <?php echo $message_type; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <h3>Manage Reviews</h3>
    <?php if (empty($reviews)): ?>
        <p>No reviews found.</p>
    <?php else: ?>
        <div class="table-responsive">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>User</th>
                        <th>Place</th>
                        <th>City</th>
                        <th>Rating</th>
                        <th>Comment</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reviews as $review): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($review['review_id']); ?></td>
                            <td><?php echo htmlspecialchars($review['username'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($review['place_name'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($review['city_name'] ?? 'N/A'); ?></td>
                            <td><?php echo htmlspecialchars($review['rating']); ?>/5</td>
                            <td><?php echo htmlspecialchars(substr($review['comment'], 0, 70)) . (strlen($review['comment']) > 70 ? '...' : ''); ?></td>
                            <td>
                                <span class="review-status-badge status-<?php echo htmlspecialchars($review['status']); ?>">
                                    <?php echo htmlspecialchars(ucfirst($review['status'])); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars(date('Y-m-d H:i', strtotime($review['created_at']))); ?></td>
                            <td class="action-buttons">
                                <?php if ($review['status'] === 'pending' || $review['status'] === 'rejected'): ?>
                                    <a href="manage_reviews.php?action=approve&id=<?php echo htmlspecialchars($review['review_id']); ?>" class="btn-primary btn-sm"><i class="fas fa-check"></i> Approve</a>
                                <?php endif; ?>
                                <?php if ($review['status'] === 'pending' || $review['status'] === 'approved'): ?>
                                    <a href="manage_reviews.php?action=reject&id=<?php echo htmlspecialchars($review['review_id']); ?>" class="btn-warning btn-sm"><i class="fas fa-times"></i> Reject</a>
                                <?php endif; ?>
                                <a href="manage_reviews.php?action=delete&id=<?php echo htmlspecialchars($review['review_id']); ?>" class="btn-delete btn-sm" onclick="return confirm('Are you sure you want to delete this review?');"><i class="fas fa-trash-alt"></i> Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<?php
require_once 'includes/admin_footer.php';
?>
