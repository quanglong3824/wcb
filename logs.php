<?php
/**
 * Activity Logs Page
 * Xem lịch sử hoạt động hệ thống
 */
require_once 'includes/auth-check.php';
require_once 'config/php/config.php';

// Chỉ super_admin mới được truy cập
if ($_SESSION['user_role'] !== 'super_admin') {
    header('Location: index.php');
    exit;
}

// Xác định base path
$basePath = './';
$pageTitle = 'Activity Logs - Welcome Board System';

// Include header
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<!-- Main Content -->
<main class="main-content">
    <link rel="stylesheet" href="assets/css/logs.css">
    
    <div class="page-header">
        <div class="page-header-left">
            <h1><i class="fas fa-history"></i> Activity Logs</h1>
            <p>Theo dõi lịch sử hoạt động của hệ thống</p>
        </div>
        <div class="page-header-right">
            <button class="btn btn-secondary" onclick="exportLogs()">
                <i class="fas fa-download"></i> Export CSV
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="card">
        <div class="filters-bar">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Tìm kiếm theo mô tả...">
            </div>
            <div class="filter-group">
                <select id="actionFilter">
                    <option value="">Tất cả hành động</option>
                    <option value="login">Đăng nhập</option>
                    <option value="logout">Đăng xuất</option>
                    <option value="upload">Upload</option>
                    <option value="assign">Gán media</option>
                    <option value="unassign">Bỏ gán</option>
                    <option value="delete">Xóa</option>
                    <option value="update">Cập nhật</option>
                    <option value="create_user">Tạo user</option>
                    <option value="password_reset">Reset mật khẩu</option>
                </select>
                <select id="userFilter">
                    <option value="">Tất cả người dùng</option>
                </select>
                <input type="date" id="dateFrom" placeholder="Từ ngày">
                <input type="date" id="dateTo" placeholder="Đến ngày">
            </div>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-list"></i> Lịch sử hoạt động</h2>
            <button class="btn btn-sm btn-secondary" onclick="loadLogs()">
                <i class="fas fa-sync-alt"></i> Làm mới
            </button>
        </div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Thời gian</th>
                        <th>Người dùng</th>
                        <th>Hành động</th>
                        <th>Đối tượng</th>
                        <th>Mô tả</th>
                        <th>IP</th>
                    </tr>
                </thead>
                <tbody id="logsTableBody">
                    <tr>
                        <td colspan="7" class="loading-cell">
                            <i class="fas fa-spinner fa-spin"></i>
                            <p>Đang tải dữ liệu...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="pagination-container" id="paginationContainer">
        </div>
    </div>
</main>

<script src="assets/js/logs.js"></script>

<?php include 'includes/footer.php'; ?>
