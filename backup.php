<?php
/**
 * Backup Management Page
 */
require_once 'includes/auth-check.php';
require_once 'config/php/config.php';

// Only super_admin
if ($_SESSION['user_role'] !== 'super_admin') {
    header('Location: index.php');
    exit;
}

$basePath = './';
$pageTitle = 'Backup & Restore - Welcome Board System';

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<main class="main-content">
    <link rel="stylesheet" href="assets/css/backup.css">
    
    <div class="page-header">
        <div class="page-header-left">
            <h1><i class="fas fa-database"></i> Backup & Restore</h1>
            <p>Quản lý sao lưu và khôi phục dữ liệu hệ thống</p>
        </div>
        <div class="page-header-right">
            <button class="btn btn-primary" onclick="createBackup()" id="createBackupBtn">
                <i class="fas fa-plus"></i> Tạo Backup
            </button>
        </div>
    </div>

    <!-- Info Cards -->
    <div class="info-cards">
        <div class="info-card">
            <div class="info-icon"><i class="fas fa-hdd"></i></div>
            <div class="info-content">
                <h3>Backup Database</h3>
                <p>Sao lưu toàn bộ cơ sở dữ liệu MySQL bao gồm users, media, schedules, logs...</p>
            </div>
        </div>
        <div class="info-card warning">
            <div class="info-icon"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="info-content">
                <h3>Lưu ý quan trọng</h3>
                <p>Khôi phục backup sẽ ghi đè toàn bộ dữ liệu hiện tại. Hãy cân nhắc kỹ trước khi thực hiện.</p>
            </div>
        </div>
    </div>

    <!-- Backups List -->
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-list"></i> Danh sách Backup</h2>
            <button class="btn btn-sm btn-secondary" onclick="loadBackups()">
                <i class="fas fa-sync-alt"></i> Làm mới
            </button>
        </div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Tên file</th>
                        <th>Kích thước</th>
                        <th>Ngày tạo</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody id="backupsTableBody">
                    <tr>
                        <td colspan="4" class="loading-cell">
                            <i class="fas fa-spinner fa-spin"></i>
                            <p>Đang tải...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</main>

<!-- Restore Confirmation Modal -->
<div class="modal-overlay" id="restoreModal">
    <div class="modal modal-sm">
        <div class="modal-header">
            <h3><i class="fas fa-exclamation-triangle text-warning"></i> Xác nhận khôi phục</h3>
            <button class="modal-close" onclick="closeRestoreModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <div class="modal-body">
            <p><strong>Cảnh báo:</strong> Khôi phục backup sẽ ghi đè toàn bộ dữ liệu hiện tại!</p>
            <p>Bạn có chắc chắn muốn khôi phục từ file: <strong id="restoreFilename"></strong>?</p>
        </div>
        <div class="modal-footer">
            <button class="btn btn-secondary" onclick="closeRestoreModal()">Hủy</button>
            <button class="btn btn-warning" id="confirmRestoreBtn">
                <i class="fas fa-undo"></i> Khôi phục
            </button>
        </div>
    </div>
</div>

<script src="assets/js/backup.js"></script>

<?php include 'includes/footer.php'; ?>
