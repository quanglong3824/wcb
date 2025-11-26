<?php
require_once 'includes/auth-check.php';
require_once 'config/php/config.php';

// Xác định base path
$basePath = './';
$pageTitle = 'Cài đặt - Welcome Board System';

// Include header
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<!-- Main Content -->
<main class="main-content">
    <link rel="stylesheet" href="assets/css/settings.css">
    
    <div class="settings-container">
        <!-- Header -->
        <div class="settings-header">
            <h1><i class="fas fa-cog"></i> Cài đặt hệ thống</h1>
            <p>Cấu hình và tùy chỉnh hệ thống Welcome Board</p>
        </div>

        <!-- Settings Tabs -->
        <div class="settings-tabs">
            <button class="settings-tab active" data-tab="general">
                <i class="fas fa-sliders-h"></i> Chung
            </button>
            <button class="settings-tab" data-tab="display">
                <i class="fas fa-desktop"></i> Hiển thị
            </button>
            <button class="settings-tab" data-tab="notification">
                <i class="fas fa-bell"></i> Thông báo
            </button>
            <button class="settings-tab" data-tab="users">
                <i class="fas fa-users"></i> Người dùng
            </button>
        </div>

        <!-- Settings Content -->
        <div class="settings-content">
            <!-- General Settings -->
            <div id="general-settings" class="settings-section active">
                <h2>Cài đặt chung</h2>
                
                <form onsubmit="saveGeneralSettings(event)">
                    <div class="settings-group">
                        <h3>Thông tin cơ bản</h3>
                        
                        <div class="form-group">
                            <label>Tên khách sạn</label>
                            <input type="text" id="hotelName" name="hotelName" 
                                   value="Quang Long Hotel">
                        </div>
                        
                        <div class="form-group">
                            <label>Tên hệ thống</label>
                            <input type="text" id="systemName" name="systemName" 
                                   value="Welcome Board System">
                        </div>
                        
                        <div class="form-group">
                            <label>Múi giờ</label>
                            <select id="timezone" name="timezone">
                                <option value="Asia/Ho_Chi_Minh">Việt Nam (GMT+7)</option>
                                <option value="Asia/Bangkok">Bangkok (GMT+7)</option>
                                <option value="Asia/Singapore">Singapore (GMT+8)</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Ngôn ngữ</label>
                            <select id="language" name="language">
                                <option value="vi">Tiếng Việt</option>
                                <option value="en">English</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="settings-actions">
                        <button type="button" class="btn-reset" onclick="resetSettings()">
                            <i class="fas fa-undo"></i> Khôi phục mặc định
                        </button>
                        <button type="submit" class="btn-save-settings">
                            <i class="fas fa-save"></i> Lưu cài đặt
                        </button>
                    </div>
                </form>
            </div>

            <!-- Display Settings -->
            <div id="display-settings" class="settings-section">
                <h2>Cài đặt hiển thị</h2>
                
                <form onsubmit="saveDisplaySettings(event)">
                    <div class="settings-group">
                        <h3>Tự động làm mới</h3>
                        
                        <div class="toggle-label">
                            <label class="toggle-switch">
                                <input type="checkbox" id="autoRefresh" name="autoRefresh">
                                <span class="toggle-slider"></span>
                            </label>
                            <span>Bật tự động làm mới nội dung</span>
                        </div>
                        
                        <div class="form-group">
                            <label>Thời gian làm mới (giây)</label>
                            <input type="number" id="refreshInterval" name="refreshInterval" 
                                   value="30" min="10" max="300">
                            <small>Khoảng thời gian tự động làm mới nội dung trên TV</small>
                        </div>
                    </div>
                    
                    <div class="settings-group">
                        <h3>Hiệu ứng chuyển cảnh</h3>
                        
                        <div class="form-group">
                            <label>Kiểu chuyển cảnh</label>
                            <select id="defaultTransition" name="defaultTransition">
                                <option value="fade">Fade</option>
                                <option value="slide">Slide</option>
                                <option value="zoom">Zoom</option>
                                <option value="none">Không có</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>Thời gian chuyển cảnh (giây)</label>
                            <input type="number" id="transitionDuration" name="transitionDuration" 
                                   value="1" min="0.5" max="5" step="0.5">
                        </div>
                    </div>
                    
                    <div class="settings-actions">
                        <button type="submit" class="btn-save-settings">
                            <i class="fas fa-save"></i> Lưu cài đặt
                        </button>
                    </div>
                </form>
            </div>

            <!-- Notification Settings -->
            <div id="notification-settings" class="settings-section">
                <h2>Cài đặt thông báo</h2>
                
                <form onsubmit="saveNotificationSettings(event)">
                    <div class="settings-group">
                        <h3>Email thông báo</h3>
                        
                        <div class="toggle-label">
                            <label class="toggle-switch">
                                <input type="checkbox" id="emailNotifications" name="emailNotifications">
                                <span class="toggle-slider"></span>
                            </label>
                            <span>Bật thông báo qua email</span>
                        </div>
                        
                        <div class="form-group">
                            <label>Email nhận thông báo</label>
                            <input type="email" id="notificationEmail" name="notificationEmail" 
                                   placeholder="admin@quanglonghotel.com">
                            <small>Email để nhận thông báo về trạng thái hệ thống</small>
                        </div>
                        
                        <button type="button" class="btn-add-user" onclick="testEmailNotification()">
                            <i class="fas fa-paper-plane"></i> Gửi email test
                        </button>
                    </div>
                    
                    <div class="settings-actions">
                        <button type="submit" class="btn-save-settings">
                            <i class="fas fa-save"></i> Lưu cài đặt
                        </button>
                    </div>
                </form>
            </div>

            <!-- User Management -->
            <div id="users-settings" class="settings-section">
                <h2>Quản lý người dùng</h2>
                
                <button class="btn-add-user">
                    <i class="fas fa-user-plus"></i> Thêm người dùng
                </button>
                
                <table class="users-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Tên đăng nhập</th>
                            <th>Họ tên</th>
                            <th>Email</th>
                            <th>Vai trò</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>1</td>
                            <td>admin</td>
                            <td>Administrator</td>
                            <td>admin@quanglonghotel.com</td>
                            <td><span class="user-role-badge admin">Admin</span></td>
                            <td>
                                <button class="btn-edit-tv" style="padding: 6px 12px;">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</main>

<script src="assets/js/settings.js"></script>

<?php
include 'includes/footer.php';
?>
