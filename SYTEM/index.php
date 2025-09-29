<?php
session_start();
include 'config/connection.php';

// Add a default admin user if no users exist (password: admin123)
$checkUsersSQL = "SELECT COUNT(*) as count FROM users";
$result = $conn->query($checkUsersSQL);
$row = $result->fetch_assoc();

if ($row['count'] == 0) {
    $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
    $insertAdminSQL = "INSERT INTO users (username, password_hash, role, email) 
                      VALUES ('admin', ?, 'admin', 'admin@school.edu')";
    $stmt = $conn->prepare($insertAdminSQL);
    $stmt->bind_param("s", $hashedPassword);
    $stmt->execute();
    $stmt->close();
}

$error_message = '';

// Process login form
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (!empty($email) && !empty($password)) {
        // Prepare statement to prevent SQL injection
        $sql = "SELECT user_id, username, password_hash, role, email FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
            
            // Verify password
            if (password_verify($password, $user['password_hash'])) {
                // Set session variables
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['loggedin'] = true;
                
                // Redirect to dashboard
                header("Location: dashboard.php");
                exit();
            } else {
                $error_message = "Invalid email or password!";
            }
        } else {
            $error_message = "Invalid email or password!";
        }
        
        $stmt->close();
    } else {
        $error_message = "Please enter both email and password!";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Attendance System - Login</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: linear-gradient(135deg, #a7d7a7 0%, #4CAF50 50%, #2196F3 100%);
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 20px;
        }
        
        .login-container {
            background-color: white;
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.2);
            overflow: hidden;
            width: 100%;
            max-width: 900px;
            min-height: 550px;
        }
        
        .left-panel {
            background: linear-gradient(135deg, #4CAF50, #2E7D32);
            color: white;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            height: 100%;
        }
        
        .right-panel {
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            height: 100%;
        }
        
        .logo-wrapper {
            text-align: center;
            margin-bottom: 30px;
        }
        
        .logo-placeholder {
            width: 120px;
            height: 120px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            color: white;
            font-size: 40px;
            border: 4px solid rgba(255, 255, 255, 0.3);
        }
        
        .system-title {
            font-size: 28px;
            font-weight: 700;
            margin-bottom: 10px;
        }
        
        .system-subtitle {
            font-size: 16px;
            opacity: 0.9;
            margin-bottom: 30px;
        }
        
        .feature-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .feature-list li {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
        }
        
        .feature-icon {
            width: 30px;
            height: 30px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            font-size: 14px;
        }
        
        .form-group {
            position: relative;
            margin-bottom: 25px;
        }
        
        .form-control {
            border-radius: 50px;
            padding: 15px 20px 15px 50px;
            border: 2px solid #e9ecef;
            font-size: 16px;
            transition: all 0.3s ease;
            background-color: #f8f9fa;
        }
        
        .form-control:focus {
            border-color: #4CAF50;
            box-shadow: 0 0 0 0.3rem rgba(76, 175, 80, 0.15);
            background-color: white;
        }
        
        .input-icon {
            position: absolute;
            left: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #4CAF50;
            font-size: 18px;
            z-index: 10;
        }
        
        .btn-login {
            background: linear-gradient(135deg, #4CAF50, #45a049);
            border: none;
            border-radius: 50px;
            padding: 15px;
            color: white;
            font-weight: 600;
            font-size: 16px;
            width: 100%;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.3);
            margin-bottom: 15px;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(76, 175, 80, 0.4);
            background: linear-gradient(135deg, #45a049, #4CAF50);
        }
        
        .btn-clear {
            background: transparent;
            border: 2px solid #6c757d;
            border-radius: 50px;
            padding: 13px;
            color: #6c757d;
            font-weight: 600;
            font-size: 16px;
            width: 100%;
            transition: all 0.3s ease;
            margin-bottom: 15px;
        }
        
        .btn-clear:hover {
            background: #6c757d;
            color: white;
            transform: translateY(-2px);
        }
        
        .alert {
            border-radius: 15px;
            margin-bottom: 25px;
            border: none;
            padding: 15px 20px;
            background: linear-gradient(135deg, #ff6b6b, #ee5a52);
            color: white;
            box-shadow: 0 5px 15px rgba(255, 107, 107, 0.3);
        }
        
        .forgot-password {
            text-align: center;
            margin-top: 15px;
        }
        
        .forgot-password a {
            color: #4CAF50;
            text-decoration: none;
            font-weight: 500;
            transition: color 0.3s ease;
        }
        
        .forgot-password a:hover {
            color: #2E7D32;
            text-decoration: underline;
        }
        
        .welcome-text {
            font-size: 24px;
            font-weight: 600;
            margin-bottom: 10px;
            color: #2c3e50;
        }
        
        .instruction-text {
            color: #6c757d;
            margin-bottom: 30px;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .left-panel {
                padding: 30px;
            }
            
            .right-panel {
                padding: 30px;
            }
            
            .login-container {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="row g-0">
            <!-- Left Panel -->
            <div class="col-lg-6">
                <div class="left-panel">
                    <div class="logo-wrapper">
                        <div class="logo-placeholder">
                            <i class="fas fa-qrcode"></i>
                        </div>
                        <h1 class="system-title">QR Attendance</h1>
                        <p class="system-subtitle">Digital Attendance Management System</p>
                    </div>
                    
                    <ul class="feature-list">
                        <li>
                            <div class="feature-icon">
                                <i class="fas fa-clock"></i>
                            </div>
                            <div>
                                <strong>Real-time Tracking</strong>
                                <div class="small">Monitor attendance instantly</div>
                            </div>
                        </li>
                        <li>
                            <div class="feature-icon">
                                <i class="fas fa-chart-bar"></i>
                            </div>
                            <div>
                                <strong>Analytics & Reports</strong>
                                <div class="small">Generate detailed reports</div>
                            </div>
                        </li>
                        <li>
                            <div class="feature-icon">
                                <i class="fas fa-mobile-alt"></i>
                            </div>
                            <div>
                                <strong>Mobile Friendly</strong>
                                <div class="small">Works on all devices</div>
                            </div>
                        </li>
                        <li>
                            <div class="feature-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div>
                                <strong>Secure & Reliable</strong>
                                <div class="small">Enterprise-grade security</div>
                            </div>
                        </li>
                    </ul>
                    
                    
                </div>
            </div>
            
            <!-- Right Panel -->
            <div class="col-lg-6">
                <div class="right-panel">
                    <h2 class="welcome-text">Welcome Back!</h2>
                    <p class="instruction-text">Please sign in to your account</p>
                    
                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="">
                        <div class="form-group">
                            <i class="fas fa-envelope input-icon"></i>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Enter your email" required autofocus>
                        </div>
                        
                        <div class="form-group">
                            <i class="fas fa-lock input-icon"></i>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
                        </div>
                        
                        <div class="d-grid gap-2 mb-2">
                            <button type="submit" class="btn btn-login">
                                <i class="fas fa-sign-in-alt me-2"></i>Sign In
                            </button>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="reset" class="btn btn-clear">
                                <i class="fas fa-eraser me-2"></i>Clear
                            </button>
                        </div>
                        
                        <div class="forgot-password">
                            <a href="#">
                                <i class="fas fa-key me-1"></i>Forgot your password?
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Add focus effects dynamically
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.form-control');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.querySelector('.input-icon').style.color = '#4CAF50';
                    this.parentElement.querySelector('.input-icon').style.transform = 'translateY(-50%) scale(1.1)';
                });
                
                input.addEventListener('blur', function() {
                    this.parentElement.querySelector('.input-icon').style.color = '#6c757d';
                    this.parentElement.querySelector('.input-icon').style.transform = 'translateY(-50%) scale(1)';
                });
            });
        });
    </script>
</body>
</html>