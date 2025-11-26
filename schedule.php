<?php
require_once 'includes/auth-check.php';
require_once 'config/php/config.php';

// Xác định base path
$basePath = './';
$pageTitle = 'Quản lý lịch chiếu - Welcome Board System';

// Include header
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<!-- Main Content -->
<main class="main-content">
    <link rel="stylesheet" href="assets/css/schedule.css">
    
    <div class="schedule-container">
        <!-- Header -->
        <div class="schedule-header">
            <div>
                <h1><i class="fas fa-calendar-alt"></i> Quản lý lịch chiếu</h1>
                <p>Lên lịch hiển thị nội dung cho các màn hình TV</p>
            </div>
            <button class="btn-add-schedule" onclick="openAddScheduleModal()">
                <i class="fas fa-plus"></i>
                Thêm lịch chiếu
            </button>
        </div>

        <!-- Filters -->
        <div class="schedule-filters">
            <label><i class="fas fa-filter"></i> Lọc theo:</label>
            
            <select onchange="filterByTV(this.value)">
                <option value="all">Tất cả TV</option>
                <option value="1">TV Basement</option>
                <option value="2">TV Chrysan</option>
                <option value="3">TV Jasmine</option>
                <option value="4">TV Lotus</option>
                <option value="5">TV Restaurant</option>
                <option value="6">TV FO 1</option>
                <option value="7">TV FO 2</option>
            </select>
            
            <select onchange="filterByScheduleStatus(this.value)">
                <option value="all">Tất cả trạng thái</option>
                <option value="active">Đang chạy</option>
                <option value="pending">Chờ chạy</option>
                <option value="completed">Đã hoàn thành</option>
            </select>
        </div>

        <!-- Schedule List -->
        <div class="schedule-list">
            <table class="schedule-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Thời gian</th>
                        <th>TV</th>
                        <th>Nội dung</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody id="scheduleTableBody">
                    <tr>
                        <td colspan="6" style="text-align: center; padding: 40px;">
                            <i class="fas fa-spinner fa-spin" style="font-size: 2em; color: #d4af37;"></i>
                            <p style="margin-top: 15px; color: #999;">Đang tải dữ liệu...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Schedule Modal -->
    <div id="scheduleModal" class="schedule-modal">
        <div class="schedule-modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Thêm lịch chiếu mới</h2>
                <button class="modal-close" onclick="closeScheduleModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <form id="scheduleForm" onsubmit="saveSchedule(event)">
                <input type="hidden" id="scheduleId" name="scheduleId">
                
                <div class="form-group">
                    <label>Chọn TV *</label>
                    <select id="scheduleTv" name="scheduleTv" required>
                        <option value="">-- Chọn TV --</option>
                        <option value="1">TV Basement</option>
                        <option value="2">TV Chrysan</option>
                        <option value="3">TV Jasmine</option>
                        <option value="4">TV Lotus</option>
                        <option value="5">TV Restaurant</option>
                        <option value="6">TV FO 1</option>
                        <option value="7">TV FO 2</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Chọn nội dung *</label>
                    <select id="scheduleContent" name="scheduleContent" required>
                        <option value="">-- Chọn nội dung --</option>
                        <option value="1">Welcome Banner 01</option>
                        <option value="2">Hotel Promo Video</option>
                        <option value="3">Menu Display</option>
                    </select>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Ngày chiếu *</label>
                        <input type="date" id="scheduleDate" name="scheduleDate" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Lặp lại</label>
                        <select id="scheduleRepeat" name="scheduleRepeat">
                            <option value="none">Không lặp</option>
                            <option value="daily">Hàng ngày</option>
                            <option value="weekly">Hàng tuần</option>
                            <option value="monthly">Hàng tháng</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label>Giờ bắt đầu *</label>
                        <input type="time" id="scheduleStartTime" name="scheduleStartTime" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Giờ kết thúc *</label>
                        <input type="time" id="scheduleEndTime" name="scheduleEndTime" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label>Ghi chú</label>
                    <textarea id="scheduleNote" name="scheduleNote" 
                              placeholder="Nhập ghi chú cho lịch chiếu..."></textarea>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeScheduleModal()">
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

<script src="assets/js/schedule.js"></script>

<?php
include 'includes/footer.php';
?>
