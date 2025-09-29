<?php
// includes/footer.php
?>
<!-- Bootstrap 5 JS Bundle - ONLY INCLUDE ONCE -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<!-- Remove the duplicate Bootstrap bundle below -->

<script>
// Wait for DOM to be fully loaded
document.addEventListener('DOMContentLoaded', function() {
    // Initialize all Bootstrap dropdowns
    var dropdownElementList = [].slice.call(document.querySelectorAll('.dropdown-toggle'));
    var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
        return new bootstrap.Dropdown(dropdownToggleEl);
    });

    // Initialize all Bootstrap tooltips (if any)
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Sidebar Toggle Functionality
    const sidebarToggle = document.getElementById('sidebarToggle');
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const content = document.querySelector('.content-area');
            
            if (window.innerWidth >= 768) {
                // Desktop behavior
                sidebar.classList.toggle('sidebar-collapsed');
                if (content) {
                    content.classList.toggle('expanded');
                }
            } else {
                // Mobile behavior
                sidebar.classList.toggle('sidebar-mobile-open');
            }
        });
    }

    // Close sidebar when clicking on a link in mobile view
    document.querySelectorAll('.sidebar .nav-link').forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth < 768) {
                const sidebar = document.getElementById('sidebar');
                if (sidebar) {
                    sidebar.classList.remove('sidebar-mobile-open');
                }
            }
        });
    });

    // Close mobile sidebar when clicking outside
    document.addEventListener('click', function(e) {
        const sidebar = document.getElementById('sidebar');
        const toggleBtn = document.getElementById('sidebarToggle');
        
        if (window.innerWidth < 768 && 
            sidebar && 
            toggleBtn &&
            !sidebar.contains(e.target) && 
            !toggleBtn.contains(e.target) &&
            sidebar.classList.contains('sidebar-mobile-open')) {
            sidebar.classList.remove('sidebar-mobile-open');
        }
    });

    // Smooth page transitions
    const links = document.querySelectorAll('.sidebar .nav-link:not(.text-warning)');
    links.forEach(link => {
        link.addEventListener('click', function(e) {
            if (this.getAttribute('href') && !this.getAttribute('href').startsWith('#')) {
                // Add loading state
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Loading...';
                
                setTimeout(() => {
                    window.location.href = this.getAttribute('href');
                }, 300);
                
                // Prevent default only for smooth transition
                e.preventDefault();
            }
        });
    });

    // Simple animation for cards on page load
    const animElements = document.querySelectorAll('.welcome-banner, .card-custom');
    animElements.forEach((element, index) => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(40px)';
        
        setTimeout(() => {
            element.style.transition = 'opacity 0.8s, transform 0.5s';
            element.style.opacity = '1';
            element.style.transform = 'translateY(0)';
        }, 100 + (index * 100));
    });

    // Chart.js Implementation - Only run if charts exist on page
    initializeCharts();
});

// Initialize Charts function
function initializeCharts() {
    // Line Chart - Attendance Overview
    const ctx = document.getElementById('attendanceChart');
    if (ctx) {
        const attendanceChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
                datasets: [{
                    label: 'Present',
                    data: [120, 115, 118, 122, 119, 0, 0],
                    borderColor: '#4CAF50',
                    backgroundColor: 'rgba(76, 175, 80, 0.1)',
                    tension: 0.4,
                    fill: true
                }, {
                    label: 'Absent',
                    data: [5, 8, 7, 3, 6, 0, 0],
                    borderColor: '#f44336',
                    backgroundColor: 'rgba(244, 67, 54, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'top',
                    },
                    title: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Pie Chart - Today's Status
    const pieCtx = document.getElementById('attendancePieChart');
    if (pieCtx) {
        const attendancePieChart = new Chart(pieCtx, {
            type: 'doughnut',
            data: {
                labels: ['Present', 'Absent'],
                datasets: [{
                    data: [<?php echo $present_today ?? 0; ?>, <?php echo $absent_today ?? 0; ?>],
                    backgroundColor: ['#4CAF50', '#f44336'],
                    borderWidth: 2,
                    borderColor: '#fff'
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                },
                cutout: '60%'
            }
        });
    }
}

// Global function to handle dropdowns (if needed elsewhere)
function initializeDropdowns() {
    // This ensures dropdowns work even if they're dynamically added
    document.querySelectorAll('.dropdown-toggle').forEach(function(dropdownToggle) {
        var dropdown = new bootstrap.Dropdown(dropdownToggle);
    });
}

// Re-initialize when navigating (for SPA-like behavior)
if (window.history.pushState) {
    window.addEventListener('popstate', function() {
        initializeDropdowns();
    });
}
</script>

</body>
</html>