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
                <p>Quản lý và cấu hình các màn hình TV trong hệ thống</p>
            </div>
            <button class="btn-add-tv" onclick="openAddTVModal()">
                <i class="fas fa-plus"></i>
                Thêm TV mới
            </button>
        </div>

        <!-- View Toggle -->
        <div class="view-toggle">
            <button class="active" data-view="grid" onclick="toggleView('grid')">
                <i class="fas fa-th"></i> Lưới
            </button>
            <button data-view="table" onclick="toggleView('table')">
                <i class="fas fa-list"></i> Danh sách
            </button>
        </div>

        <!-- TV Container (Grid or Table) -->
        <div id="tvContainer" class="tv-grid-view">
            <!-- TVs will be loaded here dynamically -->
            <div style="grid-column: 1/-1; text-align: center; padding: 60px; color: #999;">
                <i class="fas fa-spinner fa-spin" style="font-size: 3em; display: block; margin-bottom: 20px;"></i>
                <p style="font-size: 1.2em;">Đang tải dữ liệu...</p>
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
