<?php
session_start();
require_once 'config/php/config.php';

// Kiểm tra đăng nhập
if (!isLoggedIn()) {
    redirect('auth/login.php');
}

// Xác định base path
$basePath = './';
$pageTitle = 'Quản lý WCB - Welcome Board System';

// Include header
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<!-- Main Content -->
<main class="main-content">
    <link rel="stylesheet" href="assets/css/manage-wcb.css">
    
    <div class="wcb-container">
        <!-- Header -->
        <div class="wcb-header">
            <div>
                <h1><i class="fas fa-image"></i> Quản lý WCB</h1>
                <p>Quản lý nội dung Welcome Board cho các màn hình TV</p>
            </div>
            <button class="btn-add-wcb" onclick="openAddWCBModal()">
                <i class="fas fa-plus"></i>
                Thêm WCB mới
            </button>
        </div>

        <!-- Search and Filter -->
        <div class="wcb-controls">
            <div class="search-box">
                <input type="text" placeholder="Tìm kiếm WCB..." 
                       onkeyup="searchWCB(this.value)">
                <i class="fas fa-search"></i>
            </div>
            
            <select onchange="filterByType(this.value)">
                <option value="all">Tất cả loại</option>
                <option value="image">Hình ảnh</option>
                <option value="video">Video</option>
            </select>
            
            <select onchange="filterByStatus(this.value)">
                <option value="all">Tất cả trạng thái</option>
                <option value="active">Đang sử dụng</option>
                <option value="inactive">Không sử dụng</option>
            </select>
        </div>

        <!-- WCB Table -->
        <div class="wcb-table-container">
            <table class="wcb-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Preview</th>
                        <th>Tên</th>
                        <th>Loại</th>
                        <th>Gán cho TV</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody id="wcbTableBody">
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 40px;">
                            <i class="fas fa-spinner fa-spin" style="font-size: 2em; color: #667eea;"></i>
                            <p style="margin-top: 15px; color: #999;">Đang tải dữ liệu...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- WCB Modal -->
    <div id="wcbModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Thêm WCB mới</h2>
                <button class="modal-close" onclick="closeModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="wcbForm" onsubmit="saveWCB(event)">
                <input type="hidden" id="wcbId" name="wcbId">
                
                <div class="form-group">
                    <label>Tên WCB *</label>
                    <input type="text" id="wcbName" name="wcbName" required>
                </div>
                
                <div class="form-group">
                    <label>Loại *</label>
                    <select id="wcbType" name="wcbType" required>
                        <option value="image">Hình ảnh</option>
                        <option value="video">Video</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>File *</label>
                    <input type="file" name="wcbFile" accept="image/*,video/*">
                    <small style="color: #999;">Chọn file mới nếu muốn thay đổi</small>
                </div>
                
                <div class="form-group">
                    <label>Mô tả</label>
                    <textarea id="wcbDescription" name="wcbDescription" 
                              placeholder="Nhập mô tả cho WCB..."></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeModal()">
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

<script src="assets/js/manage-wcb.js"></script>

<?php
include 'includes/footer.php';
?>
