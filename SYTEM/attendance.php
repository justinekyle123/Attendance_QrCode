<?php
// attendance.php
include 'includes/auth.php';
include 'includes/header.php';
include 'includes/navbar.php';
include 'includes/sidebar.php';
include 'config/connection.php';

// Debug: Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// First, let's check what tables actually exist in your database
$debug_tables = $conn->query("SHOW TABLES");
echo "<!-- Debug: Available tables: ";
$tables = [];
while ($row = $debug_tables->fetch_array()) {
    $tables[] = $row[0];
    echo $row[0] . ", ";
}
echo " -->";

// Check if required tables exist
$required_tables = ['attendance', 'students', 'classes'];
$missing_tables = array_diff($required_tables, $tables);

if (!empty($missing_tables)) {
    die("<div class='content-area'><div class='alert alert-danger'>Missing tables: " . implode(', ', $missing_tables) . ". Please run the database setup.</div></div>");
}

// Let's also check the structure of the tables
$attendance_columns = $conn->query("DESCRIBE attendance");
echo "<!-- Debug: Attendance table columns: ";
while ($row = $attendance_columns->fetch_assoc()) {
    echo $row['Field'] . " (" . $row['Type'] . "), ";
}
echo " -->";

// Modified query with error handling
$query = "SELECT a.attendance_id, a.date, a.time_in, a.status, 
                 s.name,last_name, 
                 c.class_name
          FROM attendance a
          JOIN students s ON a.student_id = s.student_id
          JOIN classes c ON a.class_id = c.class_id
          ORDER BY a.date DESC, a.time_in DESC 
          LIMIT 100"; // Added LIMIT for safety

$stmt = $conn->prepare($query);

// Check if prepare was successful
if ($stmt === false) {
    die("<div class='content-area'><div class='alert alert-danger'>Prepare failed: " . $conn->error . "</div></div>");
}

// Execute the statement
if (!$stmt->execute()) {
    die("<div class='content-area'><div class='alert alert-danger'>Execute failed: " . $stmt->error . "</div></div>");
}

// Get the result set
$result = $stmt->get_result();
?>

<div class="content-area">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="welcome-banner flex-grow-1">
            <h2><i class="fas fa-calendar-check me-2"></i> Attendance Records</h2>
            <p class="mb-0">View and manage all attendance logs here.</p>
        </div>
        <button class="btn btn-custom ms-3">
            <i class="fas fa-download me-2"></i>Export
        </button>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card card-custom stat-card">
                <div class="card-body text-center">
                    <h3 class="text-primary" id="totalRecords">0</h3>
                    <p class="text-muted mb-0">Total Records</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card card-custom stat-card">
                <div class="card-body text-center">
                    <h3 class="text-success" id="presentCount">0</h3>
                    <p class="text-muted mb-0">Present Today</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card card-custom stat-card">
                <div class="card-body text-center">
                    <h3 class="text-warning" id="lateCount">0</h3>
                    <p class="text-muted mb-0">Late Today</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card card-custom stat-card">
                <div class="card-body text-center">
                    <h3 class="text-danger" id="absentCount">0</h3>
                    <p class="text-muted mb-0">Absent Today</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card card-custom">
        <div class="card-header bg-transparent">
            <h5 class="card-title mb-0"><i class="fas fa-history me-2"></i>Attendance History</h5>
        </div>
        <div class="card-body">
            <!-- Filter Options -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <input type="date" class="form-control" id="dateFilter" value="<?php echo date('Y-m-d'); ?>">
                </div>
                <div class="col-md-3">
                    <select class="form-control" id="classFilter">
                        <option value="">All Classes</option>
                        <?php
                        $classes = $conn->query("SELECT class_id, class_name FROM classes ORDER BY class_name");
                        while ($class = $classes->fetch_assoc()) {
                            echo "<option value='{$class['class_id']}'>{$class['class_name']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-control" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="present">Present</option>
                        <option value="late">Late</option>
                        <option value="absent">Absent</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <button class="btn btn-custom w-100" onclick="filterAttendance()">
                        <i class="fas fa-filter me-2"></i>Filter
                    </button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Student Name</th>
                            <th>Class</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php 
                            $totalRecords = 0;
                            $presentCount = 0;
                            $lateCount = 0;
                            $absentCount = 0;
                            $today = date('Y-m-d');
                            ?>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <?php 
                                $totalRecords++;
                                if ($row['date'] == $today) {
                                    if ($row['status'] == 'Present') $presentCount++;
                                    if ($row['status'] == 'Late') $lateCount++;
                                    if ($row['status'] == 'Absent') $absentCount++;
                                }
                                ?>
                                <tr>
                                    <td><?= date("M d, Y", strtotime($row['date'])); ?></td>
                                    <td><?= $row['time_in'] ? date("h:i A", strtotime($row['time_in'])) : 'N/A'; ?></td>
                                    <td><?= htmlspecialchars($row['name'] . ' ' . $row['last_name']); ?></td>
                                    <td><?= htmlspecialchars($row['class_name']); ?></td>
                                    <td>
                                        <?php if ($row['status'] == 'Present'): ?>
                                            <span class="badge bg-success"><i class="fas fa-check me-1"></i>Present</span>
                                        <?php elseif ($row['status'] == 'Late'): ?>
                                            <span class="badge bg-warning text-dark"><i class="fas fa-clock me-1"></i>Late</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger"><i class="fas fa-times me-1"></i>Absent</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="viewAttendance(<?= $row['attendance_id']; ?>)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-warning" onclick="editAttendance(<?= $row['attendance_id']; ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                    No attendance records found.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <nav>
                <ul class="pagination justify-content-center">
                    <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
                    <li class="page-item active"><a class="page-link" href="#">1</a></li>
                    <li class="page-item"><a class="page-link" href="#">2</a></li>
                    <li class="page-item"><a class="page-link" href="#">3</a></li>
                    <li class="page-item"><a class="page-link" href="#">Next</a></li>
                </ul>
            </nav>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script>
// Update statistics
document.getElementById('totalRecords').textContent = '<?php echo $totalRecords; ?>';
document.getElementById('presentCount').textContent = '<?php echo $presentCount; ?>';
document.getElementById('lateCount').textContent = '<?php echo $lateCount; ?>';
document.getElementById('absentCount').textContent = '<?php echo $absentCount; ?>';

function filterAttendance() {
    const date = document.getElementById('dateFilter').value;
    const classId = document.getElementById('classFilter').value;
    const status = document.getElementById('statusFilter').value;
    
    // Show loading
    alert('Filter functionality would be implemented here.\nDate: ' + date + '\nClass: ' + classId + '\nStatus: ' + status);
}

function viewAttendance(id) {
    alert('View attendance record: ' + id);
}

function editAttendance(id) {
    alert('Edit attendance record: ' + id);
}
</script>

<?php 
// Close statement and connection
if (isset($stmt)) {
    $stmt->close();
}
$conn->close();

?>

