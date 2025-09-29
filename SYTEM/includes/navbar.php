<?php
// includes/navbar.php
// Get current page for active link highlighting
$current_page = basename($_SERVER['PHP_SELF']);
?>
<nav class="navbar navbar-expand-lg navbar-custom">
    <div class="container-fluid">
        <!-- Sidebar Toggle Button -->
        <button class="btn btn-outline-secondary me-3" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        
        <!-- Page Title -->
        <span class="navbar-brand mb-0 h1">
            <i class="fas fa-qrcode me-2"></i>QR Attendance System
        </span>
        
        <!-- Mobile Toggle Button -->
        <button class="navbar-toggler d-md-none" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMobile">
            <i class="fas fa-ellipsis-v"></i>
        </button>
        
        <!-- User Info and Logout -->
        <div class="collapse navbar-collapse" id="navbarMobile">
            <div class="navbar-nav ms-auto align-items-center">
                <div class="nav-item dropdown">
                    <div class="d-flex align-items-center">
                        <div class="me-3 text-end d-none d-md-block">
                            <div class="fw-bold text-dark"><?php echo htmlspecialchars($_SESSION['username']); ?></div>
                            <small class="text-muted text-capitalize"><?php echo htmlspecialchars($_SESSION['role']); ?></small>
                        </div>
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="userDropdown" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i>
                            <span class="d-none d-sm-inline"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Profile</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-cog me-2"></i>Settings</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="logout.php"><i class="fas fa-sign-out-alt me-2"></i>Logout</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>