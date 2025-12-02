<?php
/**
 * No Permission Page Component
 * Hiển thị khi user không có quyền chỉnh sửa (readonly mode)
 */

// Get module name for display
$moduleNames = [
    'dashboard' => 'Dashboard',
    'tv_monitor' => 'Giám sát TV',
    'tv_manage' => 'Quản lý TV',
    'wcb_manage' => 'Quản lý WCB',
    'upload' => 'Upload',
    'schedule' => 'Lịch chiếu',
    'settings' => 'Cài đặt',
    'users' => 'Quản lý Users',
    'logs' => 'Activity Logs',
    'backup' => 'Backup'
];

$moduleName = isset($moduleNames[$currentModule]) ? $moduleNames[$currentModule] : 'Trang này';
$userRole = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : 'user';
$roleName = getRoleName($userRole);
?>

<div class="no-permission-container">
    <div class="no-permission-card">
        <div class="no-permission-icon">
            <i class="fas fa-lock"></i>
        </div>
        <div class="no-permission-code">403</div>
        <h1>Không có quyền truy cập</h1>
        <p class="no-permission-message">
            Bạn đang đăng nhập với vai trò <strong><?php echo $roleName; ?></strong> 
            và chỉ có quyền <strong>xem</strong> trang <strong><?php echo $moduleName; ?></strong>.
        </p>
        <p class="no-permission-hint">
            Liên hệ <strong>Super Admin</strong> để được cấp quyền chỉnh sửa.
        </p>
        
        <div class="no-permission-info">
            <h3><i class="fas fa-info-circle"></i> Quyền hiện tại của bạn:</h3>
            <ul>
                <?php if (hasPermission($currentModule, PERM_VIEW)): ?>
                <li><i class="fas fa-check text-success"></i> Xem nội dung</li>
                <?php endif; ?>
                <?php if (!hasPermission($currentModule, PERM_CREATE)): ?>
                <li><i class="fas fa-times text-danger"></i> Tạo mới</li>
                <?php endif; ?>
                <?php if (!hasPermission($currentModule, PERM_EDIT)): ?>
                <li><i class="fas fa-times text-danger"></i> Chỉnh sửa</li>
                <?php endif; ?>
                <?php if (!hasPermission($currentModule, PERM_DELETE)): ?>
                <li><i class="fas fa-times text-danger"></i> Xóa</li>
                <?php endif; ?>
            </ul>
        </div>
        
        <div class="no-permission-actions">
            <a href="index.php" class="btn btn-primary">
                <i class="fas fa-home"></i> Về Dashboard
            </a>
            <a href="javascript:history.back()" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>
</div>

<style>
.no-permission-container {
    display: flex;
    align-items: center;
    justify-content: center;
    min-height: calc(100vh - 150px);
    padding: 40px 20px;
}

.no-permission-card {
    background: white;
    border: 3px solid #d4af37;
    max-width: 550px;
    width: 100%;
    padding: 50px 40px;
    text-align: center;
}

.no-permission-icon {
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    border: 3px solid #f59e0b;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 20px;
}

.no-permission-icon i {
    font-size: 3em;
    color: #92400e;
}

.no-permission-code {
    font-size: 4em;
    font-weight: 700;
    color: #d4af37;
    line-height: 1;
    margin-bottom: 10px;
}

.no-permission-card h1 {
    font-size: 1.5em;
    color: #1a1a1a;
    margin-bottom: 20px;
}

.no-permission-message {
    color: #666;
    font-size: 1em;
    line-height: 1.6;
    margin-bottom: 10px;
}

.no-permission-hint {
    color: #999;
    font-size: 0.9em;
    margin-bottom: 30px;
}

.no-permission-info {
    background: #f9fafb;
    border: 2px solid #e5e7eb;
    padding: 20px;
    margin-bottom: 30px;
    text-align: left;
}

.no-permission-info h3 {
    font-size: 0.95em;
    color: #1a1a1a;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.no-permission-info ul {
    list-style: none;
    padding: 0;
    margin: 0;
}

.no-permission-info li {
    padding: 8px 0;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 0.9em;
    color: #666;
}

.no-permission-info li:last-child {
    border-bottom: none;
}

.no-permission-info .text-success {
    color: #10b981;
}

.no-permission-info .text-danger {
    color: #ef4444;
}

.no-permission-actions {
    display: flex;
    gap: 15px;
    justify-content: center;
}

.no-permission-actions .btn {
    padding: 12px 25px;
    border: 2px solid;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s;
}

.no-permission-actions .btn-primary {
    background: #d4af37;
    border-color: #d4af37;
    color: #1a1a1a;
}

.no-permission-actions .btn-primary:hover {
    background: transparent;
    color: #d4af37;
}

.no-permission-actions .btn-secondary {
    background: white;
    border-color: #e5e7eb;
    color: #666;
}

.no-permission-actions .btn-secondary:hover {
    border-color: #d4af37;
    color: #d4af37;
}
</style>
