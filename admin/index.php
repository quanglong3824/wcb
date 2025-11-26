<?php
// Include auth check
require_once '../includes/auth-check.php';

// Include config
require_once '../config/php/config.php';

// Xác định base path
$basePath = '../';

// Set page title
$pageTitle = 'Admin Panel - Welcome Board System';

// Include header
include '../includes/header.php';

// Include sidebar
include '../includes/sidebar.php';

// Xác định trang hiện tại
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>

<!-- Main Content -->
<main class="main-content">
    <?php
    // Load nội dung trang tương ứng
    switch ($page) {
        case 'tv':
            include 'pages/tv-management.php';
            break;
        
        case 'schedule':
            include 'pages/schedule-management.php';
            break;
        
        case 'settings':
            include 'pages/settings.php';
            break;
        
        default:
            // Dashboard mặc định
            ?>
            <div class="page-header">
                <h1>Admin Panel</h1>
                <p>Quản lý hệ thống Welcome Board</p>
            </div>

            <div class="dashboard-grid">
                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-card-icon blue">
                            <i class="fas fa-tv"></i>
                        </div>
                    </div>
                    <div class="stat-card-value">12</div>
                    <div class="stat-card-label">Tổng số TV</div>
                </div>

                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-card-icon green">
                            <i class="fas fa-check-circle"></i>
                        </div>
                    </div>
                    <div class="stat-card-value">10</div>
                    <div class="stat-card-label">TV đang hoạt động</div>
                </div>

                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-card-icon orange">
                            <i class="fas fa-images"></i>
                        </div>
                    </div>
                    <div class="stat-card-value">45</div>
                    <div class="stat-card-label">Nội dung WCB</div>
                </div>

                <div class="stat-card">
                    <div class="stat-card-header">
                        <div class="stat-card-icon purple">
                            <i class="fas fa-calendar-check"></i>
                        </div>
                    </div>
                    <div class="stat-card-value">8</div>
                    <div class="stat-card-label">Lịch chiếu hôm nay</div>
                </div>
            </div>
            <?php
            break;
    }
    ?>
</main>

<?php
// Include footer
include '../includes/footer.php';
?>
