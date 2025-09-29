<?php
// students.php
include 'includes/auth.php';
include 'includes/header.php';
include 'includes/navbar.php';
include 'includes/sidebar.php';
include 'config/connection.php';

// Handle form submissions
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_student'])) {
        // Add new student
        $name = trim($_POST['name']);
        $course = trim($_POST['course']);
        $year = $_POST['year'];
        $gender = $_POST['gender'];
        $address = trim($_POST['address']);
        $contact_no = trim($_POST['contact_no']);
        $parent_contact = trim($_POST['parent_contact']);
        
        // Generate QR code (simple version - you can enhance this)
        $qr_code = 'STU' . uniqid();
        
        $sql = "INSERT INTO students (name, course, year, gender, address, contact_no, parent_contact, qr_code) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssisssss", $name, $course, $year, $gender, $address, $contact_no, $parent_contact, $qr_code);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Student added successfully!";
        } else {
            $_SESSION['error'] = "Error adding student: " . $stmt->error;
        }
        $stmt->close();
        
        header("Location: students.php");
        exit();
    }
    
    if (isset($_POST['update_student'])) {
        // Update student
        $student_id = $_POST['student_id'];
        $name = trim($_POST['name']);
        $course = trim($_POST['course']);
        $year = $_POST['year'];
        $gender = $_POST['gender'];
        $address = trim($_POST['address']);
        $contact_no = trim($_POST['contact_no']);
        $parent_contact = trim($_POST['parent_contact']);
        
        $sql = "UPDATE students SET name=?, course=?, year=?, gender=?, address=?, contact_no=?, parent_contact=? 
                WHERE student_id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssissssi", $name, $course, $year, $gender, $address, $contact_no, $parent_contact, $student_id);
        
        if ($stmt->execute()) {
            $_SESSION['success'] = "Student updated successfully!";
        } else {
            $_SESSION['error'] = "Error updating student: " . $stmt->error;
        }
        $stmt->close();
        
        header("Location: students.php");
        exit();
    }
}

// Handle delete action
if (isset($_GET['delete'])) {
    $student_id = $_GET['delete'];
    
    $sql = "DELETE FROM students WHERE student_id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $student_id);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Student deleted successfully!";
    } else {
        $_SESSION['error'] = "Error deleting student: " . $stmt->error;
    }
    $stmt->close();
    
    header("Location: students.php");
    exit();
}

// Fetch all students
$sql = "SELECT * FROM students ORDER BY name ASC";
$result = $conn->query($sql);

// Count students by course for statistics
$course_stats = [];
$stats_sql = "SELECT course, COUNT(*) as count FROM students GROUP BY course";
$stats_result = $conn->query($stats_sql);
while ($row = $stats_result->fetch_assoc()) {
    $course_stats[$row['course']] = $row['count'];
}
?>

<div class="content-area">
    <!-- Header Section -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div class="welcome-banner flex-grow-1">
            <h2><i class="fas fa-users me-2"></i> Student Management</h2>
            <p class="mb-0">Manage student records and information</p>
        </div>
        <button class="btn btn-custom ms-3" data-bs-toggle="modal" data-bs-target="#addStudentModal">
            <i class="fas fa-user-plus me-2"></i>Add New Student
        </button>
    </div>

    <!-- Display Messages -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i><?php echo $_SESSION['success']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?php echo $_SESSION['error']; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card card-custom stat-card">
                <div class="card-body text-center">
                    <h3 class="text-primary"><?php echo $result->num_rows; ?></h3>
                    <p class="text-muted mb-0">Total Students</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card card-custom stat-card">
                <div class="card-body text-center">
                    <h3 class="text-info"><?php echo isset($course_stats['BSIT']) ? $course_stats['BSIT'] : 0; ?></h3>
                    <p class="text-muted mb-0">BSIT Students</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card card-custom stat-card">
                <div class="card-body text-center">
                    <h3 class="text-success"><?php echo isset($course_stats['BSCS']) ? $course_stats['BSCS'] : 0; ?></h3>
                    <p class="text-muted mb-0">BSCS Students</p>
                </div>
            </div>
        </div>
        <div class="col-md-3 col-sm-6 mb-3">
            <div class="card card-custom stat-card">
                <div class="card-body text-center">
                    <h3 class="text-warning"><?php echo isset($course_stats['BSIS']) ? $course_stats['BSIS'] : 0; ?></h3>
                    <p class="text-muted mb-0">BSIS Students</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Students Table -->
    <div class="card card-custom">
        <div class="card-header bg-transparent">
            <h5 class="card-title mb-0"><i class="fas fa-list me-2"></i>All Students</h5>
        </div>
        <div class="card-body">
            <!-- Search and Filter -->
            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="form-control" placeholder="Search students..." id="searchInput">
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-control" id="courseFilter">
                        <option value="">All Courses</option>
                        <option value="BSIT">BSIT</option>
                        <option value="BSCS">BSCS</option>
                        <option value="BSIS">BSIS</option>
                        <option value="BSCE">BSCE</option>
                        <option value="BSME">BSME</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-control" id="yearFilter">
                        <option value="">All Years</option>
                        <option value="1">1st Year</option>
                        <option value="2">2nd Year</option>
                        <option value="3">3rd Year</option>
                        <option value="4">4th Year</option>
                    </select>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle" id="studentsTable">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Course</th>
                            <th>Year</th>
                            <th>Gender</th>
                            <th>Contact No</th>
                            <th>Parent Contact</th>
                            <th>QR Code</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if ($result && $result->num_rows > 0): ?>
                            <?php while ($student = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $student['student_id']; ?></td>
                                    <td><?php echo htmlspecialchars($student['name']); ?></td>
                                    <td>
                                        <span class="badge bg-primary"><?php echo htmlspecialchars($student['course']); ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary">Year <?php echo $student['year_level']; ?></span>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?php echo $student['gender'] == 'Male' ? 'info' : 'warning'; ?>">
                                            <?php echo $student['gender']; ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($student['contact_no']); ?></td>
                                    <td><?php echo htmlspecialchars($student['parent_contact']); ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-success" onclick="viewQRCode('<?php echo $student['qr_code']; ?>', '<?php echo htmlspecialchars($student['name']); ?>')">
                                            <i class="fas fa-qrcode me-1"></i>View QR
                                        </button>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-outline-primary" onclick="editStudent(<?php echo $student['student_id']; ?>)">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteStudent(<?php echo $student['student_id']; ?>, '<?php echo htmlspecialchars($student['name']); ?>')">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="fas fa-users fa-3x mb-3 d-block"></i>
                                    No students found. <a href="#" data-bs-toggle="modal" data-bs-target="#addStudentModal">Add the first student</a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add Student Modal -->
<div class="modal fade" id="addStudentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>Add New Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Full Name *</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Course *</label>
                            <select class="form-control" name="course" required>
                                <option value="">Select Course</option>
                                <option value="BSIT">BS Information Technology</option>
                                <option value="BSCS">BS Computer Science</option>
                                <option value="BSIS">BS Information Systems</option>
                                <option value="BSCE">BS Computer Engineering</option>
                                <option value="BSME">BS Mechanical Engineering</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Year Level *</label>
                            <select class="form-control" name="year" required>
                                <option value="">Select Year</option>
                                <option value="1">1st Year</option>
                                <option value="2">2nd Year</option>
                                <option value="3">3rd Year</option>
                                <option value="4">4th Year</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Gender *</label>
                            <select class="form-control" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" name="address" rows="2"></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contact Number</label>
                            <input type="tel" class="form-control" name="contact_no">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Parent's Contact Number</label>
                            <input type="tel" class="form-control" name="parent_contact">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="add_student" class="btn btn-custom">Add Student</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Student Modal -->
<div class="modal fade" id="editStudentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="">
                <input type="hidden" name="student_id" id="edit_student_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Full Name *</label>
                            <input type="text" class="form-control" name="name" id="edit_name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Course *</label>
                            <select class="form-control" name="course" id="edit_course" required>
                                <option value="">Select Course</option>
                                <option value="BSIT">BS Information Technology</option>
                                <option value="BSCS">BS Computer Science</option>
                                <option value="BSIS">BS Information Systems</option>
                                <option value="BSCE">BS Computer Engineering</option>
                                <option value="BSME">BS Mechanical Engineering</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Year Level *</label>
                            <select class="form-control" name="year" id="edit_year" required>
                                <option value="">Select Year</option>
                                <option value="1">1st Year</option>
                                <option value="2">2nd Year</option>
                                <option value="3">3rd Year</option>
                                <option value="4">4th Year</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Gender *</label>
                            <select class="form-control" name="gender" id="edit_gender" required>
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                            </select>
                        </div>
                        <div class="col-12 mb-3">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" name="address" id="edit_address" rows="2"></textarea>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Contact Number</label>
                            <input type="tel" class="form-control" name="contact_no" id="edit_contact_no">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Parent's Contact Number</label>
                            <input type="tel" class="form-control" name="parent_contact" id="edit_parent_contact">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" name="update_student" class="btn btn-custom">Update Student</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- QR Code Modal -->
<div class="modal fade" id="qrCodeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="qrCodeTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <div id="qrcode" class="mb-3"></div>
                <p class="text-muted" id="qrCodeText"></p>
                <button class="btn btn-custom" onclick="printQRCode()">
                    <i class="fas fa-print me-2"></i>Print QR Code
                </button>
            </div>
        </div>
    </div>
</div>

<script>
// Search and Filter functionality
document.getElementById('searchInput').addEventListener('input', filterStudents);
document.getElementById('courseFilter').addEventListener('change', filterStudents);
document.getElementById('yearFilter').addEventListener('change', filterStudents);

function filterStudents() {
    const search = document.getElementById('searchInput').value.toLowerCase();
    const course = document.getElementById('courseFilter').value;
    const year = document.getElementById('yearFilter').value;
    
    const rows = document.querySelectorAll('#studentsTable tbody tr');
    
    rows.forEach(row => {
        const name = row.cells[1].textContent.toLowerCase();
        const studentCourse = row.cells[2].textContent.trim();
        const studentYear = row.cells[3].textContent.includes('1') ? '1' : 
                           row.cells[3].textContent.includes('2') ? '2' :
                           row.cells[3].textContent.includes('3') ? '3' : '4';
        
        const matchesSearch = name.includes(search);
        const matchesCourse = !course || studentCourse === course;
        const matchesYear = !year || studentYear === year;
        
        row.style.display = (matchesSearch && matchesCourse && matchesYear) ? '' : 'none';
    });
}

// Edit Student
function editStudent(studentId) {
    // In a real application, you would fetch student data via AJAX
    // For now, we'll redirect to an edit page or show modal with existing data
    alert('Edit functionality for student ID: ' + studentId + '\nThis would fetch student data and populate the edit form.');
    
    // You can implement AJAX to fetch student data and populate the edit modal
    // For now, we'll just show the edit modal
    const editModal = new bootstrap.Modal(document.getElementById('editStudentModal'));
    editModal.show();
}

// Delete Student with confirmation
function deleteStudent(studentId, studentName) {
    if (confirm(`Are you sure you want to delete student: ${studentName}?`)) {
        window.location.href = `students.php?delete=${studentId}`;
    }
}

// View QR Code
function viewQRCode(qrCode, studentName) {
    document.getElementById('qrCodeTitle').textContent = `QR Code - ${studentName}`;
    document.getElementById('qrCodeText').textContent = `QR Code: ${qrCode}`;
    
    // Generate QR code (you can use a QR code library here)
    const qrContainer = document.getElementById('qrcode');
    qrContainer.innerHTML = `
        <div class="border p-3 bg-light d-inline-block">
            <div style="font-family: monospace; font-size: 12px; letter-spacing: 2px;">
                ${qrCode}
            </div>
            <div class="mt-2">
                <i class="fas fa-qrcode fa-3x text-primary"></i>
            </div>
        </div>
    `;
    
    const qrModal = new bootstrap.Modal(document.getElementById('qrCodeModal'));
    qrModal.show();
}

// Print QR Code
function printQRCode() {
    const printContent = document.getElementById('qrcode').innerHTML;
    const studentName = document.getElementById('qrCodeTitle').textContent;
    
    const printWindow = window.open('', '_blank');
    printWindow.document.write(`
        <html>
            <head>
                <title>QR Code - ${studentName}</title>
                <style>
                    body { font-family: Arial, sans-serif; text-align: center; padding: 20px; }
                    .qrcode { border: 2px solid #333; padding: 20px; display: inline-block; margin: 20px; }
                </style>
            </head>
            <body>
                <h2>${studentName}</h2>
                <div class="qrcode">${printContent}</div>
                <p>Generated on: ${new Date().toLocaleDateString()}</p>
            </body>
        </html>
    `);
    printWindow.document.close();
    printWindow.print();
}
</script>

<script>
    function deleteStudent(studentId, studentName) {
    Swal.fire({
        icon: 'warning',
        title: 'Are you sure?',
        text: "You are about to delete " + studentName,
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            // Ajax call to the server
            $.ajax({
                type: "POST",
                url: 'delete_student.php', // Create this file to handle the deletion
                data: { student_id: studentId },
                success: function (response) {
                    // Show success message
                    Swal.fire('Deleted!', 'Student has been deleted.', 'success');
                    // Optionally, refresh the student list
                    // fetchStudents();
                },
                error: function () {
                    Swal.fire('Error!', 'There was an issue deleting the student.', 'error');
                }
            });
        }
    });
}
</script>

<?php 
$conn->close();
include 'includes/footer.php'; 
?>