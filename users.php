<?php
/**
 * Users Management Page
 * Quản lý người dùng hệ thống
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
$pageTitle = 'Quản lý người dùng - Welcome Board System';

// Include header
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<!-- Main Content -->
<main class="main-content">
    <link rel="stylesheet" href="assets/css/users.css">
    
    <div class="page-header">
        <div class="page-header-left">
            <h1><i class="fas fa-users"></i> Quản lý người dùng</h1>
            <p>Thêm, sửa, xóa và phân quyền người dùng hệ thống</p>
        </div>
        <div class="page-header-right">
            <button class="btn btn-primary" onclick="openAddUserModal()">
                <i class="fas fa-plus"></i> Thêm người dùng
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon"><i class="fas fa-users"></i></div>
            <div class="stat-info">
                <div class="stat-value" id="totalUsers">-</div>
                <div class="stat-label">Tổng người dùng</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon active"><i class="fas fa-user-check"></i></div>
            <div class="stat-info">
                <div class="stat-value" id="activeUsers">-</div>
                <div class="stat-label">Đang hoạt động</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon admin"><i class="fas fa-user-shield"></i></div>
            <div class="stat-info">
                <div class="stat-value" id="adminUsers">-</div>
                <div class="stat-label">Super Admin</div>
            </div>
        </div>
        <div class="stat-card">
            <div class="stat-icon inactive"><i class="fas fa-user-slash"></i></div>
            <div class="stat-info">
                <div class="stat-value" id="inactiveUsers">-</div>
                <div class="stat-label">Đã khóa</div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card">
        <div class="filters-bar">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Tìm kiếm theo tên, username, email...">
            </div>
            <div class="filter-group">
                <select id="roleFilter">
                    <option value="">Tất cả vai trò</option>
                    <option value="super_admin">Super Admin</option>
                    <option value="content_manager">Content Manager</option>
                </select>
                <select id="statusFilter">
                    <option value="">Tất cả trạng thái</option>
                    <option value="active">Đang hoạt động</option>
                    <option value="inactive">Đã khóa</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="card">
        <div class="card-header">
            <h2><i class="fas fa-list"></i> Danh sách người dùng</h2>
            <button class="btn btn-sm btn-secondary" onclick="loadUsers()">
                <i class="fas fa-sync-alt"></i> Làm mới
            </button>
        </div>
        
        <div class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Người dùng</th>
                        <th>Email</th>
                        <th>Vai trò</th>
                        <th>Trạng thái</th>
                        <th>Đăng nhập cuối</th>
                        <th>Ngày tạo</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody id="usersTableBody">
                    <tr>
                        <td colspan="8" class="loading-cell">
                            <i class="fas fa-spinner fa-spin"></i>
                            <p>Đang tải dữ liệu...</p>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <!-- Pagination -->
        <div class="pagination-container" id="paginationContainer">
            <!-- Pagination will be rendered here -->
        </div>
    </div>
</main>

<!-- Add/Edit User Modal -->
<div class="modal-overlay" id="userModal">
    <div class="modal">
        <div class="modal-header">
            <h3 id="modalTitle"><i class="fas fa-user-plus"></i> Thêm người dùng mới</h3>
            <button class="modal-close" onclick="closeUserModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <form id="userForm" onsubmit="saveUser(event)">
            <input type="hidden" id="userId" name="id" value="">
            
            <div class="modal-body">
                <div class="form-row">
                    <div class="form-group">
                        <label for="username">Tên đăng nhập *</label>
                        <input type="text" id="username" name="username" required 
                               pattern="[a-zA-Z0-9_]+" 
                               title="Chỉ chứa chữ cái, số và dấu gạch dưới">
                    </div>
                    <div class="form-group">
                        <label for="fullName">Họ và tên *</label>
                        <input type="text" id="fullName" name="full_name" required>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="example@email.com">
                </div>
                
                <div class="form-row" id="passwordFields">
                    <div class="form-group">
                        <label for="password">Mật khẩu <span id="passwordRequired">*</span></label>
                        <input type="password" id="password" name="password" minlength="6">
                        <small>Tối thiểu 6 ký tự</small>
                    </div>
                    <div class="form-group">
                        <label for="confirmPassword">Xác nhận mật khẩu <span id="confirmRequired">*</span></label>
                        <input type="password" id="confirmPassword" name="confirm_password" minlength="6">
                    </div>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="role">Vai trò *</label>
                        <select id="role" name="role" required>
                            <option value="content_manager">Content Manager</option>
                            <option value="super_admin">Super Admin</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="status">Trạng thái *</label>
                        <select id="status" name="status" required>
                            <option value="active">Đang hoạt động</option>
                            <option value="inactive">Đã khóa</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeUserModal()">
                    <i class="fas fa-times"></i> Hủy
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Lưu
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal-overlay" id="deleteModal">
    <div class="modal modal-sm">
        <div class="modal-header">
            <h3><i class="fas fa-exclamation-triangle text-danger"></i> Xác nhận xóa</h3>
            <button class="modal-close" onclick="closeDeleteModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="modal-body">
            <p>Bạn có chắc chắn muốn xóa người dùng <strong id="deleteUserName"></strong>?</p>
            <p class="text-muted">Hành động này không thể hoàn tác.</p>
        </div>
        
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">
                <i class="fas fa-times"></i> Hủy
            </button>
            <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                <i class="fas fa-trash"></i> Xóa
            </button>
        </div>
    </div>
</div>

<!-- Reset Password Modal -->
<div class="modal-overlay" id="resetPasswordModal">
    <div class="modal modal-sm">
        <div class="modal-header">
            <h3><i class="fas fa-key"></i> Đặt lại mật khẩu</h3>
            <button class="modal-close" onclick="closeResetPasswordModal()">
                <i class="fas fa-times"></i>
            </button>
        </div>
        
        <div class="modal-body" id="resetPasswordContent">
            <p>Đặt lại mật khẩu cho người dùng <strong id="resetUserName"></strong>?</p>
            <p class="text-muted">Mật khẩu mới sẽ được tạo tự động.</p>
        </div>
        
        <div class="modal-footer" id="resetPasswordFooter">
            <button type="button" class="btn btn-secondary" onclick="closeResetPasswordModal()">
                <i class="fas fa-times"></i> Hủy
            </button>
            <button type="button" class="btn btn-primary" id="confirmResetBtn">
                <i class="fas fa-key"></i> Đặt lại
            </button>
        </div>
    </div>
</div>

<script src="assets/js/users.js"></script>

<?php include 'includes/footer.php'; ?>
