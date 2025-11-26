<?php
session_start();
require_once 'config/php/config.php';

// Kiểm tra đăng nhập
if (!isLoggedIn()) {
    redirect('auth/login.php');
}

// Xác định base path
$basePath = './';
$pageTitle = 'Giám sát TV - Welcome Board System';

// Include header
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<!-- Main Content -->
<main class="main-content">
    <link rel="stylesheet" href="assets/css/view.css">
    
    <div class="view-container">
        <!-- Header -->
        <div class="view-header">
            <div>
                <h1><i class="fas fa-desktop"></i> Giám sát TV</h1>
                <p>Theo dõi trạng thái và nội dung đang chiếu trên các màn hình</p>
            </div>
            <button class="refresh-btn" onclick="refreshMonitors()">
                <i class="fas fa-sync-alt"></i>
                Làm mới
            </button>
        </div>

        <!-- Filter Section -->
        <div class="filter-section">
            <label><i class="fas fa-filter"></i> Lọc theo:</label>
            
            <select onchange="filterByLocation(this.value)">
                <option value="all">Tất cả vị trí</option>
                <option value="Tầng hầm">Tầng hầm</option>
                <option value="Tầng 1">Tầng 1</option>
                <option value="Tầng 2">Tầng 2</option>
                <option value="Nhà hàng">Nhà hàng</option>
            </select>
            
            <select onchange="filterByStatus(this.value)">
                <option value="all">Tất cả trạng thái</option>
                <option value="online">Đang hoạt động</option>
                <option value="offline">Offline</option>
            </select>
        </div>

        <!-- TV Grid -->
        <div class="tv-grid" id="tvGrid">
            <!-- TV monitors will be loaded here dynamically -->
            <div class="empty-state" style="grid-column: 1/-1;">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Đang tải dữ liệu...</p>
            </div>
        </div>
    </div>
</main>

<script src="assets/js/view.js"></script>

<?php
include 'includes/footer.php';
?>
