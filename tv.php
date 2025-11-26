<?php
session_start();
require_once 'config/php/config.php';

// Kiểm tra đăng nhập
if (!isLoggedIn()) {
    redirect('auth/login.php');
}

// Xác định base path
$basePath = './';
$pageTitle = 'Quản lý TV - Welcome Board System';

// Include header
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<!-- Main Content -->
<main class="main-content">
    <link rel="stylesheet" href="assets/css/tv.css">
    
    <div class="tv-container">
        <!-- Header -->
        <div class="tv-header">
            <div>
                <h1><i class="fas fa-tv"></i> Quản lý TV</h1>
                <p>Quản lý 7 màn hình TV tại các vị trí trong khách sạn</p>
            </div>
        </div>

        <!-- Search and Filter -->
        <div class="tv-controls">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" 
                       id="searchInput" 
                       placeholder="Tìm kiếm theo tên hoặc vị trí..." 
                       onkeyup="filterTVs()">
            </div>
            
            <div class="filter-group">
                <select id="statusFilter" onchange="filterTVs()">
                    <option value="all">Tất cả trạng thái</option>
                    <option value="online">Online</option>
                    <option value="offline">Offline</option>
                </select>
                
                <select id="contentFilter" onchange="filterTVs()">
                    <option value="all">Tất cả nội dung</option>
                    <option value="playing">Đang trình chiếu</option>
                    <option value="idle">Không trình chiếu</option>
                </select>
                
                <button class="btn-refresh" onclick="refreshTVs()" title="Làm mới">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
        </div>

        <!-- TV Grid -->
        <div id="tvGrid" class="tv-grid">
            <!-- TVs will be loaded here dynamically -->
            <div class="empty-state" style="grid-column: 1/-1;">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Đang tải dữ liệu...</p>
            </div>
        </div>
    </div>

    <!-- TV Modal -->
    <div id="tvModal" class="tv-modal">
        <div class="tv-modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Thêm TV mới</h2>
                <button class="modal-close" onclick="closeTVModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="tvForm" onsubmit="saveTV(event)">
                <input type="hidden" id="tvId" name="tvId">
                
                <div class="form-group">
                    <label>Tên TV *</label>
                    <input type="text" id="tvName" name="tvName" 
                           placeholder="Ví dụ: TV Basement" required>
                </div>
                
                <div class="form-group">
                    <label>Vị trí *</label>
                    <input type="text" id="tvLocation" name="tvLocation" 
                           placeholder="Ví dụ: Tầng hầm" required>
                </div>
                
                <div class="form-group">
                    <label>Địa chỉ IP</label>
                    <input type="text" id="tvIpAddress" name="tvIpAddress" 
                           placeholder="Ví dụ: 192.168.1.100">
                    <small>Địa chỉ IP của thiết bị TV (tùy chọn)</small>
                </div>
                
                <div class="form-group">
                    <label>Mô tả</label>
                    <textarea id="tvDescription" name="tvDescription" 
                              placeholder="Nhập mô tả cho TV..."></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeTVModal()">
                        Hủy
                    </button>
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> Lưu
                    </button>
                </div>
            </form>
        </div>
    </div>
</main>

<script src="assets/js/tv.js"></script>

<?php
include 'includes/footer.php';
?>
