
       <?php
// notifications.php

// Include authentication & header
include 'includes/auth.php';
include 'includes/header.php';
include 'includes/navbar.php';
include 'includes/sidebar.php';
include 'config/connection.php';

// Fetch notifications from DB
$query = "SELECT * FROM notifications ORDER BY date_sent DESC";
$result = $conn->query($query);
?>

<div class="content-area">
    <div class="welcome-banner mb-4">
        <h2><i class="fas fa-bell"></i> Notifications</h2>
        <p>Here are the latest notifications sent to parents.</p>
    </div>

    <div class="card card-custom">
        <div class="card-body">
            <h5 class="card-title mb-3">Notification History</h5>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle text-center">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Student ID</th>
                            <th>Parent Contact</th>
                            <th>Message</th>
                            <th>Date Sent</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result->num_rows > 0): ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?= htmlspecialchars($row['notif_id']); ?></td>
                                    <td><?= htmlspecialchars($row['student_id']); ?></td>
                                    <td><?= htmlspecialchars($row['parent_contact']); ?></td>
                                    <td><?= htmlspecialchars($row['message']); ?></td>
                                    <td><?= date("M d, Y h:i A", strtotime($row['date_sent'])); ?></td>
                                    <td>
                                        <?php if ($row['status'] === 'Sent'): ?>
                                            <span class="badge bg-success">Sent</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Failed</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="6" class="text-muted">No notifications yet.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

</div>


