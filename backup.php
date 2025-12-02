<?php
/**
 * Backup Management Page
 */
require_once 'includes/auth-check.php';
require_once 'config/php/config.php';
require_once 'includes/permissions.php';

$basePath = './';
$pageTitle = 'Backup & Restore - Welcome Board System';
$currentModule = 'backup';

$canCreate = hasPermission('backup', PERM_CREATE);
$canDelete = hasPermission('backup', PERM_DELETE);
$isReadonly = isReadOnly('backup');

include 'includes/header.php';
include 'includes/sidebar.php';
?>

<main class="main-content">
    <link rel="stylesheet" href="assets/css/backup.css">
    
    <?php include 'includes/permission-bar.php'; ?>
    
    <div class="page-header">
        <div class="page-header-left">
            <h1><i class="fas fa-database"></i> Backup & Restore</h1>
            <p>Quản lý sao lưu và khôi phục dữ liệu hệ thống</p>
        </div>
        <div class="page-header-right">
            <div class="btn-group">
                <button class="btn btn-primary dropdown-toggle" onclick="toggleBackupMenu()">
                    <i class="fas fa-plus"></i> Tạo Backup <i class="fas fa-caret-down"></i>
                </button>
                <div class="dropdown-menu" id="backupDropdown">
                    <a href="#" onclick="createBackup('database'); return false;">
                        <i class="fas fa-database"></i> Database Only
                    </a>
                    <a href="#" onclick="createBackup('media'); return false;">
                        <i class="fas fa-images"></i> Media Files
                    </a>
                    <a href="#" onclick="createBackup('wcb'); return false;">
                        <i class="fas fa-tv"></i> WCB Content
                    </a>
                    <div class="dropdown-divider"></div>
                    <a href="#" onclick="createBackup('full'); return false;" class="highlight">
                        <i class="fas fa-archive"></i> Full Backup
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Storage Stats -->
    <div class="stats-grid" id="storageStats">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-database"></i></div>
            <div class="stat-info">
                <div class="stat-value" id="dbSize">-</div>
                <div class="stat-label">Database</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-images"></i></div>
            <div class="stat-info">
                <div class="stat-value" id="mediaSize">-</div>
                <div class="stat-label">Media Files</div>
                <div class="stat-sub" id="mediaCount">- files</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-tv"></i></div>
            <div class="stat-info">
                <div class="stat-value" id="wcbSize">-</div>
                <div class="stat-label">WCB Content</div>
                <div class="stat-sub" id="wcbCount">- files</div>
            </div>
        </div>
        <div class="stat-card highlight">
            <div class="stat-icon"><i class="fas fa-hdd"></i></div>
            <div class="stat-info">
                <div class="stat-value" id="totalSize">-</div>
                <div class="stat-label">Tổng dung lượng</div>
            </div>
        </div>
    </div>

    <!-- Info Cards -->
    <div class="info-cards">
        <div class="info-card">
            <div class="info-icon"><i class="fas fa-info-circle"></i></div>
            <div class="info-content">
                <h3>Các loại Backup</h3>
                <p><strong>Database:</strong> Cơ sở dữ liệu | <strong>Media:</strong> Ảnh/Video uploads | <strong>WCB:</strong> Nội dung Welcome Board | <strong>Full:</strong> Tất cả</p>
            </div>
        </div>
        <div class="info-card warning">
            <div class="info-icon"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="info-content">
                <h3>Lưu ý quan trọng</h3>
                <p>Khôi phục backup sẽ ghi đè dữ liệu hiện tại. Chỉ database backup mới có thể restore tự động.</p>
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
                        <th>Loại</th>
                        <th>Tên file</th>
                        <th>Kích thước</th>
                        <th>Ngày tạo</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody id="backupsTableBody">
                    <tr>
                        <td colspan="5" class="loading-cell">
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
