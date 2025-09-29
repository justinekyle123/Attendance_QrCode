<?php
// includes/header.php
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Attendance System - Dashboard</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- SweetAlert2 CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

<!-- SweetAlert2 JS -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.js"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary-color: #4CAF50;
            --secondary-color: #a7d7a7;
            --dark-green: #2E7D32;
            --light-green: #e8f5e9;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa;
            overflow-x: hidden;
            padding-top: 70px; /* Added for fixed navbar */
        }

        /*------NOTIF AREA----------------*/
.table thead th {
    background: var(--light-green);
    color: #333;
    font-weight: 600;
}

.table tbody tr:hover {
    background: #f1f8f1;
    transition: 0.3s ease;
}

.badge {
    padding: 6px 12px;
    font-size: 0.85rem;
}
        
/*----------SIDE BAR AREA--------------*/

        .sidebar {
    background: linear-gradient(135deg, var(--primary-color), var(--dark-green));
    color: white;
    height: calc(100vh - 70px);
    box-shadow: 3px 0 10px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
    position: fixed;
    top: 70px;
    left: 0;
    width: 250px;
    z-index: 1000;
    overflow-y: auto;
}

.sidebar-content {
    padding-bottom: 20px;
}

.sidebar .nav-link {
    color: rgba(255,255,255,0.8);
    padding: 12px 20px;
    margin: 5px 15px;
    border-radius: 8px;
    transition: all 0.3s ease;
    position: relative;
    white-space: nowrap;
}

.sidebar .nav-link:hover {
    background: rgba(255,255,255,0.15);
    color: white;
    transform: translateX(5px);
}

.sidebar .nav-link.active {
    background: rgba(255,255,255,0.2);
    color: white;
    transform: translateX(5px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}

.sidebar .nav-link.active::before {
    content: '';
    position: absolute;
    left: -15px;
    top: 50%;
    transform: translateY(-50%);
    width: 4px;
    height: 70%;
    background: white;
    border-radius: 2px;
}

.sidebar .nav-link i {
    width: 25px;
    text-align: center;
}

/* Ensure the sidebar can scroll if content is too long */
.sidebar {
    overflow-y: auto;
}

/* Custom scrollbar for sidebar */
.sidebar::-webkit-scrollbar {
    width: 6px;
}

.sidebar::-webkit-scrollbar-track {
    background: rgba(255,255,255,0.1);
    border-radius: 3px;
}

.sidebar::-webkit-scrollbar-thumb {
    background: rgba(255,255,255,0.3);
    border-radius: 3px;
}

.sidebar::-webkit-scrollbar-thumb:hover {
    background: rgba(255,255,255,0.5);
}
        .navbar-custom {
            background: white;
            box-shadow: 0 2px 15px rgba(0,0,0,0.1);
            border-bottom: 3px solid var(--secondary-color);
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1030;
            height: 70px;
        }
        
        .content-area {
            margin-left: 250px;
            transition: all 0.3s ease;
            padding: 20px;
            min-height: calc(100vh - 70px);
        }
        
        .sidebar-collapsed {
            margin-left: -250px;
        }
        
        .sidebar-collapsed + .content-area {
            margin-left: 0;
        }
        
        .card-custom {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border-left: 4px solid var(--primary-color);
        }
        
        .card-custom:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }
        
        .stat-card {
            background: linear-gradient(135deg, #fff, var(--light-green));
        }
        
        .welcome-banner {
            background: linear-gradient(135deg, var(--primary-color), var(--dark-green));
            color: white;
            border-radius: 15px;
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .btn-custom {
            background: linear-gradient(135deg, var(--primary-color), var(--dark-green));
            color: white;
            border: none;
            border-radius: 25px;
            padding: 10px 25px;
            transition: all 0.3s ease;
        }
        
        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(76, 175, 80, 0.3);
            color: white;
        }
        
        /* Mobile responsiveness */
        @media (max-width: 768px) {
            body {
                padding-top: 70px;
            }
            
            .sidebar {
                width: 250px;
                transform: translateX(-100%);
            }
            
            .sidebar-mobile-open {
                transform: translateX(0);
            }
            
            .content-area {
                margin-left: 0;
                padding: 15px;
            }
            
            .sidebar-collapsed + .content-area {
                margin-left: 0;
            }
        }
        
        .chart-container {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        /* Scrollbar styling for sidebar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: rgba(255,255,255,0.1);
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.3);
            border-radius: 3px;
        }
        
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: rgba(255,255,255,0.5);
        }
    </style>
</head>
<body>