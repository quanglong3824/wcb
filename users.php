<?php
/**
 * Users Management Page
 * Quản lý người dùng hệ thống
 */
require_once 'includes/auth-check.php';
require_once 'config/php/config.php';
require_once 'includes/permissions.php';

// Xác định base path
$basePath = './';
$pageTitle = 'Quản lý người dùng - Welcome Board System';
$currentModule = 'users';

// Kiểm tra quyền xem
$canView = hasPermission('users', PERM_VIEW);
$canCreate = hasPermission('users', PERM_CREATE);
$canEdit = hasPermission('users', PERM_EDIT);
$canDelete = hasPermission('users', PERM_DELETE);
$isReadonly = isReadOnly('users');

// Include header
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<!-- Main Content -->
<main class="main-content">
    <link rel="stylesheet" href="assets/css/users.css">
    <style>
        /* Override for modal display */
        #userModal.active,
        #deleteModal.active,
        #resetPasswordModal.active {
            display: flex !important;
            position: fixed !important;
            top: 0 !important;
            left: 0 !important;
            right: 0 !important;
            bottom: 0 !important;
            z-index: 999999 !important;
            background: rgba(0, 0, 0, 0.5) !important;
            align-items: center !important;
            justify-content: center !important;
        }
        #userModal .modal,
        #deleteModal .modal,
        #resetPasswordModal .modal {
            display: block !important;
            position: relative !important;
            background: white !important;
            border: 3px solid #d4af37 !important;
            max-width: 600px !important;
            width: 100% !important;
            max-height: 90vh !important;
            overflow-y: auto !important;
        }
        #deleteModal .modal,
        #resetPasswordModal .modal {
            max-width: 450px !important;
        }
    </style>
    
    <?php include 'includes/permission-bar.php'; ?>
    
    <div class="page-header">
        <div class="page-header-left">
            <h1><i class="fas fa-users"></i> Quản lý người dùng</h1>
            <p>Thêm, sửa, xóa và phân quyền người dùng hệ thống</p>
        </div>
        <div class="page-header-right">
            <?php if ($canCreate): ?>
            <button class="btn btn-primary" onclick="openAddUserModal()">
                <i class="fas fa-plus"></i> Thêm người dùng
            </button>
            <?php else: ?>
            <button class="btn btn-primary" disabled title="Bạn không có quyền thêm người dùng">
                <i class="fas fa-plus"></i> Thêm người dùng
            </button>
            <?php endif; ?>
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
                        <select id="role" name="role" required onchange="toggleCustomPermissions()">
                            <option value="user">User (Chỉ xem)</option>
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
                
                <!-- Custom Permissions Section -->
                <div id="customPermissionsSection" class="permissions-section" style="display: none;">
                    <div class="permissions-header">
                        <h4><i class="fas fa-shield-alt"></i> Phân quyền chi tiết</h4>
                        <label class="use-custom-toggle">
                            <input type="checkbox" id="useCustomPermissions" name="use_custom" onchange="togglePermissionsTable()">
                            <span>Tùy chỉnh quyền</span>
                        </label>
                    </div>
                    
                    <div id="permissionsTable" class="permissions-table" style="display: none;">
                        <div class="permissions-note">
                            <i class="fas fa-info-circle"></i>
                            Chọn các quyền cho từng chức năng. Nếu không tùy chỉnh, sẽ sử dụng quyền mặc định của vai trò.
                        </div>
                        
                        <table class="perm-table">
                            <thead>
                                <tr>
                                    <th>Chức năng</th>
                                    <th><i class="fas fa-eye"></i> Xem</th>
                                    <th><i class="fas fa-plus"></i> Tạo</th>
                                    <th><i class="fas fa-edit"></i> Sửa</th>
                                    <th><i class="fas fa-trash"></i> Xóa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                global $ALL_MODULES;
                                foreach ($ALL_MODULES as $moduleKey => $moduleInfo): 
                                    if ($moduleKey === 'profile') continue; // Skip profile
                                ?>
                                <tr>
                                    <td>
                                        <i class="<?php echo $moduleInfo['icon']; ?>"></i>
                                        <?php echo $moduleInfo['name']; ?>
                                    </td>
                                    <td><input type="checkbox" name="perm[<?php echo $moduleKey; ?>][view]" value="1" checked></td>
                                    <td><input type="checkbox" name="perm[<?php echo $moduleKey; ?>][create]" value="1"></td>
                                    <td><input type="checkbox" name="perm[<?php echo $moduleKey; ?>][edit]" value="1"></td>
                                    <td><input type="checkbox" name="perm[<?php echo $moduleKey; ?>][delete]" value="1"></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                        
                        <div class="permissions-actions">
                            <button type="button" class="btn-sm" onclick="selectAllPermissions()">
                                <i class="fas fa-check-double"></i> Chọn tất cả
                            </button>
                            <button type="button" class="btn-sm" onclick="clearAllPermissions()">
                                <i class="fas fa-times"></i> Bỏ chọn tất cả
                            </button>
                            <button type="button" class="btn-sm" onclick="applyRoleDefaults()">
                                <i class="fas fa-undo"></i> Theo vai trò
                            </button>
                        </div>
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

<!-- Current user ID and Permissions for JS -->
<div data-current-user-id="<?php echo $_SESSION['user_id']; ?>" style="display:none;"></div>
<script>
    // Pass permissions to JavaScript
    window.userPermissions = {
        canView: <?php echo $canView ? 'true' : 'false'; ?>,
        canCreate: <?php echo $canCreate ? 'true' : 'false'; ?>,
        canEdit: <?php echo $canEdit ? 'true' : 'false'; ?>,
        canDelete: <?php echo $canDelete ? 'true' : 'false'; ?>,
        isReadonly: <?php echo $isReadonly ? 'true' : 'false'; ?>
    };
</script>

<script src="assets/js/users.js"></script>

<?php include 'includes/footer.php'; ?>
