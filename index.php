<?php
// Authentication check - Must be first
require_once 'includes/auth-check.php';

// Xác định base path
$basePath = './';

// Set page title
$pageTitle = 'Dashboard - Welcome Board System';

// Include header
include 'includes/header.php';

// Include sidebar
include 'includes/sidebar.php';

// Stats sẽ được load động qua JavaScript
?>

<!-- Main Content -->
<main class="main-content">
    <link rel="stylesheet" href="assets/css/dashboard.css">
    <div class="page-header">
        <h1><i class="fas fa-chart-line"></i> Dashboard</h1>
        <p>Chào mừng đến với hệ thống quản lý Welcome Board - Aurora Hotel</p>
    </div>

    <!-- Statistics Cards -->
    <div class="dashboard-grid" id="statsGrid">
        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-card-icon">
                    <i class="fas fa-tv"></i>
                </div>
            </div>
            <div class="stat-card-value"><i class="fas fa-spinner fa-spin"></i></div>
            <div class="stat-card-label">Tổng số TV</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-card-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="stat-card-value"><i class="fas fa-spinner fa-spin"></i></div>
            <div class="stat-card-label">TV đang Online</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-card-icon">
                    <i class="fas fa-images"></i>
                </div>
            </div>
            <div class="stat-card-value"><i class="fas fa-spinner fa-spin"></i></div>
            <div class="stat-card-label">Nội dung WCB</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-card-header">
                <div class="stat-card-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
            </div>
            <div class="stat-card-value"><i class="fas fa-spinner fa-spin"></i></div>
            <div class="stat-card-label">Lịch chiếu hôm nay</div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-bolt"></i> Thao tác nhanh</h2>
        </div>
        
        <div class="dashboard-grid">
            <div class="stat-card">
                <h3 class="mb-3"><i class="fas fa-tv text-gold"></i> Quản lý TV</h3>
                <p class="text-muted mb-3">Giám sát và điều khiển các màn hình TV</p>
                <a href="tv.php" class="btn btn-primary">
                    Xem chi tiết <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <div class="stat-card">
                <h3 class="mb-3"><i class="fas fa-image text-gold"></i> Quản lý WCB</h3>
                <p class="text-muted mb-3">Tải lên và quản lý nội dung hiển thị</p>
                <a href="manage-wcb.php" class="btn btn-primary">
                    Xem chi tiết <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <div class="stat-card">
                <h3 class="mb-3"><i class="fas fa-calendar-alt text-gold"></i> Lịch chiếu</h3>
                <p class="text-muted mb-3">Lên lịch hiển thị nội dung tự động</p>
                <a href="schedule.php" class="btn btn-primary">
                    Xem chi tiết <i class="fas fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
    
    <!-- Recent Activity -->
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-history"></i> Hoạt động gần đây</h2>
            <a href="#" class="btn btn-sm btn-secondary">Xem tất cả</a>
        </div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Thời gian</th>
                        <th>Người dùng</th>
                        <th>Hành động</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody id="activityTableBody">
                    <tr>
                        <td colspan="4" style="text-align: center; padding: 40px; color: #999;">
                            <i class="fas fa-spinner fa-spin" style="font-size: 3em; display: block; margin-bottom: 15px;"></i>
                            <p>Đang tải dữ liệu...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- TV Status Overview -->
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-tv"></i> Trạng thái TV</h2>
        </div>
        
        <div id="tvStatusGrid" class="tv-status-grid">
            <div style="text-align: center; padding: 40px; color: #999;">
                <i class="fas fa-spinner fa-spin" style="font-size: 3em;"></i>
                <p style="margin-top: 15px;">Đang tải...</p>
            </div>
        </div>
    </div>
</main>

<script src="assets/js/dashboard.js"></script>

<?php
// Include footer
include 'includes/footer.php';
?>
