<?php
// includes/sidebar.php
$current_role = $_SESSION['role'];
$current_page = basename($_SERVER['PHP_SELF']);

// Function to check if link is active
function isActive($page, $current) {
    return $page === $current ? 'active' : '';
}
?>

<!-- Sidebar -->
<nav id="sidebar" class="sidebar">
    <div class="sidebar-content">
        <!-- User Profile in Sidebar -->
        <div class="sidebar-header text-center py-4">
            <div class="sidebar-user">
                <div class="mb-3">
                    <i class="fas fa-user-circle fa-3x text-white"></i>
                </div>
                <h6 class="mb-1"><?php echo htmlspecialchars($_SESSION['username']); ?></h6>
                <small class="text-white-50 text-capitalize"><?php echo htmlspecialchars($_SESSION['role']); ?></small>
            </div>
        </div>

        <!-- Navigation Menu -->
        <ul class="nav flex-column">
            <!-- Common Links for All Users -->
            <li class="nav-item">
                <a class="nav-link <?php echo isActive('dashboard.php', $current_page); ?>" href="dashboard.php">
                    <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo isActive('attendance.php', $current_page); ?>" href="attendance.php">
                    <i class="fas fa-calendar-check me-2"></i>Attendance
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo isActive('classes.php', $current_page); ?>" href="classes.php">
                    <i class="fas fa-book me-2"></i>Classes & Subjects
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo isActive('students.php', $current_page); ?>" href="students.php">
                    <i class="fas fa-users me-2"></i>Students
                </a>
            </li>
            
            <!-- Admin Only Links -->
            <?php if ($current_role === 'admin'): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo isActive('users.php', $current_page); ?>" href="users.php">
                    <i class="fas fa-user-tie me-2"></i>User Management
                </a>
            </li>
            <?php endif; ?>
            
            <!-- Common Links Continued -->
            <li class="nav-item">
                <a class="nav-link <?php echo isActive('sessions.php', $current_page); ?>" href="sessions.php">
                    <i class="fas fa-clock me-2"></i>Class Sessions
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo isActive('reports.php', $current_page); ?>" href="reports.php">
                    <i class="fas fa-chart-bar me-2"></i>Reports & Analytics
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo isActive('logs.php', $current_page); ?>" href="logs.php">
                    <i class="fas fa-history me-2"></i>System Logs
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo isActive('notifications.php', $current_page); ?>" href="notifications.php">
                    <i class="fas fa-bell me-2"></i>Notifications
                    <span class="badge bg-warning float-end mt-1" id="notificationCount">0</span>
                </a>
            </li>
            
            <!-- Admin Only Links (Extended) -->
            <?php if ($current_role === 'admin'): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo isActive('qr_generator.php', $current_page); ?>" href="qr_generator.php">
                    <i class="fas fa-qrcode me-2"></i>QR Code Generator
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo isActive('settings.php', $current_page); ?>" href="settings.php">
                    <i class="fas fa-cog me-2"></i>System Settings
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo isActive('backup.php', $current_page); ?>" href="backup.php">
                    <i class="fas fa-database me-2"></i>Backup & Restore
                </a>
            </li>
            <?php endif; ?>
            
            <!-- Teacher Only Links -->
            <?php if ($current_role === 'teacher'): ?>
            <li class="nav-item">
                <a class="nav-link <?php echo isActive('my_classes.php', $current_page); ?>" href="my_classes.php">
                    <i class="fas fa-chalkboard me-2"></i>My Classes
                </a>
            </li>
            
            <li class="nav-item">
                <a class="nav-link <?php echo isActive('my_reports.php', $current_page); ?>" href="my_reports.php">
                    <i class="fas fa-chart-pie me-2"></i>My Reports
                </a>
            </li>
            <?php endif; ?>
            
            <!-- Common Bottom Links -->
            <li class="nav-item">
                <a class="nav-link <?php echo isActive('profile.php', $current_page); ?>" href="profile.php">
                    <i class="fas fa-user me-2"></i>My Profile
                </a>
            </li>
            
            <li class="nav-item mt-3">
                <a class="nav-link text-warning" href="logout.php">
                    <i class="fas fa-sign-out-alt me-2"></i>Logout
                </a>
            </li>
        </ul>
    </div>
</nav>