<?php
// dashboard.php - Main dashboard page
include 'includes/auth.php';

// Set current page for active link highlighting
$current_page = 'dashboard.php';

// Fetch dashboard statistics
$total_students = 0;
$present_today = 0;
$absent_today = 0;
$total_teachers = 0;

// Get total students
$sql_students = "SELECT COUNT(*) as total FROM students WHERE status = 'active'";
$result = $conn->query($sql_students);
if ($result) {
    $row = $result->fetch_assoc();
    $total_students = $row['total'];
}

// Get total teachers
$sql_teachers = "SELECT COUNT(*) as total FROM users WHERE role = 'teacher' AND status = 'active'";
$result = $conn->query($sql_teachers);
if ($result) {
    $row = $result->fetch_assoc();
    $total_teachers = $row['total'];
}

// Get today's attendance
$today = date('Y-m-d');
$sql_present = "SELECT COUNT(DISTINCT student_id) as present FROM attendance WHERE date = ? AND status = 'present'";
$stmt = $conn->prepare($sql_present);
$stmt->bind_param("s", $today);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $present_today = $row['present'];
}
$stmt->close();

$absent_today = $total_students - $present_today;
?>

<?php include 'includes/header.php'; ?>
<?php include 'includes/navbar.php'; ?>

        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>

        <!-- Main Content -->
        <div class="content-area">
            <!-- Welcome Banner -->
            <div class="welcome-banner">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2>Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>! 👋</h2>
                        <p class="mb-0">Here's what's happening with your attendance system today.</p>
                    </div>
                    <div class="col-md-4 text-end">
                        <div class="bg-white bg-opacity-25 p-3 rounded d-inline-block">
                            <i class="fas fa-calendar-day me-2"></i>
                            <?php echo date('l, F j, Y'); ?>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Statistics Cards -->
            <div class="row mb-4">
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card card-custom stat-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title text-muted">Total Students</h6>
                                    <h3 class="text-primary"><?php echo $total_students; ?></h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-users fa-2x text-primary"></i>
                                </div>
                            </div>
                            <small class="text-muted">Active enrolled students</small>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card card-custom stat-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title text-muted">Present Today</h6>
                                    <h3 class="text-success"><?php echo $present_today; ?></h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-user-check fa-2x text-success"></i>
                                </div>
                            </div>
                            <small class="text-muted">Students present today</small>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card card-custom stat-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title text-muted">Absent Today</h6>
                                    <h3 class="text-danger"><?php echo $absent_today; ?></h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-user-times fa-2x text-danger"></i>
                                </div>
                            </div>
                            <small class="text-muted">Students absent today</small>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 col-sm-6 mb-3">
                    <div class="card card-custom stat-card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <h6 class="card-title text-muted">Total Teachers</h6>
                                    <h3 class="text-info"><?php echo $total_teachers; ?></h3>
                                </div>
                                <div class="align-self-center">
                                    <i class="fas fa-chalkboard-teacher fa-2x text-info"></i>
                                </div>
                            </div>
                            <small class="text-muted">Active teaching staff</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="row">
                <div class="col-lg-8 mb-4">
                    <div class="chart-container">
                        <h5 class="mb-3">Attendance Overview (Last 7 Days)</h5>
                        <canvas id="attendanceChart" height="250"></canvas>
                    </div>
                </div>
                
                <div class="col-lg-4 mb-4">
                    <div class="chart-container">
                        <h5 class="mb-3">Today's Status</h5>
                        <canvas id="attendancePieChart" height="250"></canvas>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            <?php if ($_SESSION['role'] === 'admin'): ?>
            <div class="row">
                <div class="col-12">
                    <div class="chart-container">
                        <h5 class="mb-3">Quick Actions</h5>
                        <div class="row">
                            <div class="col-md-3 col-6 mb-3">
                                <a href="generate_qr.php" class="btn btn-custom w-100 py-3">
                                    <i class="fas fa-qrcode fa-2x mb-2"></i><br>
                                    Generate QR
                                </a>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <a href="export.php" class="btn btn-custom w-100 py-3">
                                    <i class="fas fa-file-export fa-2x mb-2"></i><br>
                                    Export Report
                                </a>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <a href="students.php?action=add" class="btn btn-custom w-100 py-3">
                                    <i class="fas fa-user-plus fa-2x mb-2"></i><br>
                                    Add Student
                                </a>
                            </div>
                            <div class="col-md-3 col-6 mb-3">
                                <a href="settings.php" class="btn btn-custom w-100 py-3">
                                    <i class="fas fa-cog fa-2x mb-2"></i><br>
                                    Settings
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

<?php include 'includes/footer.php'; ?>