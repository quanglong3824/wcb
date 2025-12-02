/**
 * Users Management JavaScript
 */

// Global variables
let users = [];
let currentPage = 1;
let itemsPerPage = 10;
let totalPages = 1;
let editingUserId = null;
let deleteUserId = null;
let resetUserId = null;

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadUsers();
    setupEventListeners();
});

// Setup event listeners
function setupEventListeners() {
    // Search input
    const searchInput = document.getElementById('searchInput');
    let searchTimeout;
    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            currentPage = 1;
            loadUsers();
        }, 300);
    });
    
    // Filters
    document.getElementById('roleFilter').addEventListener('change', function() {
        currentPage = 1;
        loadUsers();
    });
    
    document.getElementById('statusFilter').addEventListener('change', function() {
        currentPage = 1;
        loadUsers();
    });
}

// Load users from API
async function loadUsers() {
    const tbody = document.getElementById('usersTableBody');
    tbody.innerHTML = `
        <tr>
            <td colspan="8" class="loading-cell">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Đang tải dữ liệu...</p>
            </td>
        </tr>
    `;
    
    try {
        const search = document.getElementById('searchInput').value;
        const role = document.getElementById('roleFilter').value;
        const status = document.getElementById('statusFilter').value;
        
        const params = new URLSearchParams({
            page: currentPage,
            limit: itemsPerPage,
            search: search,
            role: role,
            status: status
        });
        
        const response = await fetch(`api/users.php?${params}`);
        const data = await response.json();
        
        if (data.success) {
            users = data.users;
            totalPages = data.total_pages;
            
            updateStats(data.stats);
            renderUsers();
            renderPagination();
        } else {
            showError(data.message || 'Không thể tải danh sách người dùng');
        }
    } catch (error) {
        console.error('Error loading users:', error);
        showError('Lỗi kết nối server');
    }
}

// Update statistics
function updateStats(stats) {
    document.getElementById('totalUsers').textContent = stats.total || 0;
    document.getElementById('activeUsers').textContent = stats.active || 0;
    document.getElementById('adminUsers').textContent = stats.admins || 0;
    document.getElementById('inactiveUsers').textContent = stats.inactive || 0;
}

// Get permissions
function getPermissions() {
    return window.userPermissions || {
        canView: true,
        canCreate: false,
        canEdit: false,
        canDelete: false,
        isReadonly: true
    };
}

// Check permission before action
function checkPermission(action) {
    const perms = getPermissions();
    switch(action) {
        case 'create':
            if (!perms.canCreate) {
                showError('Bạn không có quyền tạo mới');
                return false;
            }
            break;
        case 'edit':
            if (!perms.canEdit) {
                showError('Bạn không có quyền chỉnh sửa');
                return false;
            }
            break;
        case 'delete':
            if (!perms.canDelete) {
                showError('Bạn không có quyền xóa');
                return false;
            }
            break;
    }
    return true;
}

// Render users table
function renderUsers() {
    const tbody = document.getElementById('usersTableBody');
    const perms = getPermissions();
    
    if (users.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="8">
                    <div class="empty-state">
                        <i class="fas fa-users-slash"></i>
                        <h3>Không tìm thấy người dùng</h3>
                        <p>Thử thay đổi bộ lọc hoặc thêm người dùng mới</p>
                    </div>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = users.map(user => {
        const initials = getInitials(user.full_name || user.username);
        const roleClass = user.role === 'super_admin' ? 'badge-admin' : (user.role === 'user' ? 'badge-user' : 'badge-manager');
        const roleLabel = user.role === 'super_admin' ? 'Super Admin' : (user.role === 'user' ? 'User' : 'Content Manager');
        const statusClass = user.status === 'active' ? 'badge-active' : 'badge-inactive';
        const statusLabel = user.status === 'active' ? 'Hoạt động' : 'Đã khóa';
        const isCurrentUser = user.id == getCurrentUserId();
        
        // Check permissions for action buttons
        const canEditUser = perms.canEdit && !isCurrentUser;
        const canDeleteUser = perms.canDelete && !isCurrentUser;
        const canResetPwd = perms.canEdit && !isCurrentUser;
        
        return `
            <tr>
                <td>${user.id}</td>
                <td>
                    <div class="user-info">
                        <div class="user-avatar">${initials}</div>
                        <div class="user-details">
                            <span class="user-name">${escapeHtml(user.full_name || 'N/A')}</span>
                            <span class="user-username">@${escapeHtml(user.username)}</span>
                        </div>
                    </div>
                </td>
                <td>${escapeHtml(user.email || '-')}</td>
                <td><span class="badge ${roleClass}"><i class="fas fa-${user.role === 'super_admin' ? 'shield-alt' : (user.role === 'user' ? 'user' : 'user-tie')}"></i> ${roleLabel}</span></td>
                <td><span class="badge ${statusClass}"><i class="fas fa-${user.status === 'active' ? 'check' : 'ban'}"></i> ${statusLabel}</span></td>
                <td>${user.last_login ? formatDate(user.last_login) : '<span style="color: #999">Chưa đăng nhập</span>'}</td>
                <td>${formatDate(user.created_at)}</td>
                <td>
                    <div class="action-buttons">
                        <button class="btn-action edit" onclick="editUser(${user.id})" title="${perms.canEdit ? 'Sửa' : 'Xem chi tiết'}" ${!perms.canEdit ? 'disabled' : ''}>
                            <i class="fas fa-${perms.canEdit ? 'edit' : 'eye'}"></i>
                        </button>
                        <button class="btn-action reset" onclick="resetPassword(${user.id}, '${escapeHtml(user.username)}')" title="Đặt lại mật khẩu" ${!canResetPwd ? 'disabled' : ''}>
                            <i class="fas fa-key"></i>
                        </button>
                        <button class="btn-action delete" onclick="confirmDelete(${user.id}, '${escapeHtml(user.full_name || user.username)}')" title="Xóa" ${!canDeleteUser ? 'disabled' : ''}>
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

// Render pagination
function renderPagination() {
    const container = document.getElementById('paginationContainer');
    
    if (totalPages <= 1) {
        container.innerHTML = '';
        return;
    }
    
    let html = '';
    
    // Previous button
    html += `<button class="pagination-btn" onclick="goToPage(${currentPage - 1})" ${currentPage === 1 ? 'disabled' : ''}>
        <i class="fas fa-chevron-left"></i>
    </button>`;
    
    // Page numbers
    const startPage = Math.max(1, currentPage - 2);
    const endPage = Math.min(totalPages, currentPage + 2);
    
    if (startPage > 1) {
        html += `<button class="pagination-btn" onclick="goToPage(1)">1</button>`;
        if (startPage > 2) {
            html += `<span class="pagination-info">...</span>`;
        }
    }
    
    for (let i = startPage; i <= endPage; i++) {
        html += `<button class="pagination-btn ${i === currentPage ? 'active' : ''}" onclick="goToPage(${i})">${i}</button>`;
    }
    
    if (endPage < totalPages) {
        if (endPage < totalPages - 1) {
            html += `<span class="pagination-info">...</span>`;
        }
        html += `<button class="pagination-btn" onclick="goToPage(${totalPages})">${totalPages}</button>`;
    }
    
    // Next button
    html += `<button class="pagination-btn" onclick="goToPage(${currentPage + 1})" ${currentPage === totalPages ? 'disabled' : ''}>
        <i class="fas fa-chevron-right"></i>
    </button>`;
    
    container.innerHTML = html;
}

// Go to page
function goToPage(page) {
    if (page < 1 || page > totalPages) return;
    currentPage = page;
    loadUsers();
}

// Default permissions by role
const defaultPermissions = {
    'super_admin': {
        'dashboard': ['view', 'create', 'edit', 'delete'],
        'tv_monitor': ['view', 'create', 'edit', 'delete'],
        'tv_manage': ['view', 'create', 'edit', 'delete'],
        'wcb_manage': ['view', 'create', 'edit', 'delete'],
        'upload': ['view', 'create', 'delete'],
        'schedule': ['view', 'create', 'edit', 'delete'],
        'settings': ['view', 'edit'],
        'users': ['view', 'create', 'edit', 'delete'],
        'logs': ['view'],
        'backup': ['view', 'create', 'delete']
    },
    'content_manager': {
        'dashboard': ['view'],
        'tv_monitor': ['view'],
        'tv_manage': ['view', 'create', 'edit'],
        'wcb_manage': ['view', 'create', 'edit', 'delete'],
        'upload': ['view', 'create', 'delete'],
        'schedule': ['view', 'create', 'edit', 'delete'],
        'settings': ['view'],
        'users': ['view'],
        'logs': ['view'],
        'backup': ['view']
    },
    'user': {
        'dashboard': ['view'],
        'tv_monitor': ['view'],
        'tv_manage': ['view'],
        'wcb_manage': ['view'],
        'upload': ['view'],
        'schedule': ['view'],
        'settings': ['view'],
        'users': ['view'],
        'logs': ['view'],
        'backup': ['view']
    }
};

// Toggle custom permissions section based on role
function toggleCustomPermissions() {
    const role = document.getElementById('role').value;
    const section = document.getElementById('customPermissionsSection');
    
    // Show permissions section for non-super_admin roles
    if (role !== 'super_admin') {
        section.style.display = 'block';
    } else {
        section.style.display = 'none';
        document.getElementById('useCustomPermissions').checked = false;
        document.getElementById('permissionsTable').style.display = 'none';
    }
}

// Toggle permissions table
function togglePermissionsTable() {
    const useCustom = document.getElementById('useCustomPermissions').checked;
    const table = document.getElementById('permissionsTable');
    table.style.display = useCustom ? 'block' : 'none';
    
    if (useCustom) {
        applyRoleDefaults();
    }
}

// Select all permissions
function selectAllPermissions() {
    document.querySelectorAll('.perm-table input[type="checkbox"]').forEach(cb => {
        cb.checked = true;
    });
}

// Clear all permissions (except view)
function clearAllPermissions() {
    document.querySelectorAll('.perm-table input[type="checkbox"]').forEach(cb => {
        // Keep view checked
        if (cb.name.includes('[view]')) {
            cb.checked = true;
        } else {
            cb.checked = false;
        }
    });
}

// Apply role defaults
function applyRoleDefaults() {
    const role = document.getElementById('role').value;
    const perms = defaultPermissions[role] || defaultPermissions['user'];
    
    // Reset all checkboxes
    document.querySelectorAll('.perm-table input[type="checkbox"]').forEach(cb => {
        cb.checked = false;
    });
    
    // Apply role permissions
    Object.keys(perms).forEach(module => {
        perms[module].forEach(perm => {
            const checkbox = document.querySelector(`input[name="perm[${module}][${perm}]"]`);
            if (checkbox) {
                checkbox.checked = true;
            }
        });
    });
}

// Load user permissions when editing
async function loadUserPermissions(userId) {
    try {
        const response = await fetch(`api/users.php?action=get_permissions&id=${userId}`);
        const data = await response.json();
        
        if (data.success && data.permissions && Object.keys(data.permissions).length > 0) {
            document.getElementById('useCustomPermissions').checked = true;
            document.getElementById('permissionsTable').style.display = 'block';
            
            // Reset all
            document.querySelectorAll('.perm-table input[type="checkbox"]').forEach(cb => {
                cb.checked = false;
            });
            
            // Apply custom permissions
            Object.keys(data.permissions).forEach(module => {
                data.permissions[module].forEach(perm => {
                    const checkbox = document.querySelector(`input[name="perm[${module}][${perm}]"]`);
                    if (checkbox) {
                        checkbox.checked = true;
                    }
                });
            });
        } else {
            document.getElementById('useCustomPermissions').checked = false;
            document.getElementById('permissionsTable').style.display = 'none';
        }
    } catch (error) {
        console.error('Error loading permissions:', error);
    }
}

// Open add user modal
function openAddUserModal() {
    if (!checkPermission('create')) return;
    
    editingUserId = null;
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-user-plus"></i> Thêm người dùng mới';
    document.getElementById('userForm').reset();
    document.getElementById('userId').value = '';
    document.getElementById('username').disabled = false;
    document.getElementById('password').required = true;
    document.getElementById('confirmPassword').required = true;
    document.getElementById('passwordRequired').style.display = 'inline';
    document.getElementById('confirmRequired').style.display = 'inline';
    
    // Reset permissions section
    document.getElementById('customPermissionsSection').style.display = 'none';
    document.getElementById('useCustomPermissions').checked = false;
    document.getElementById('permissionsTable').style.display = 'none';
    
    document.getElementById('userModal').classList.add('active');
}

// Edit user
async function editUser(id) {
    if (!checkPermission('edit')) return;
    
    editingUserId = id;
    const user = users.find(u => u.id == id);
    
    if (!user) {
        showError('Không tìm thấy người dùng');
        return;
    }
    
    document.getElementById('modalTitle').innerHTML = '<i class="fas fa-user-edit"></i> Sửa người dùng';
    document.getElementById('userId').value = user.id;
    document.getElementById('username').value = user.username;
    document.getElementById('username').disabled = true;
    document.getElementById('fullName').value = user.full_name || '';
    document.getElementById('email').value = user.email || '';
    document.getElementById('role').value = user.role;
    document.getElementById('status').value = user.status;
    document.getElementById('password').value = '';
    document.getElementById('confirmPassword').value = '';
    document.getElementById('password').required = false;
    document.getElementById('confirmPassword').required = false;
    document.getElementById('passwordRequired').style.display = 'none';
    document.getElementById('confirmRequired').style.display = 'none';
    
    // Handle permissions section
    toggleCustomPermissions();
    if (user.role !== 'super_admin') {
        await loadUserPermissions(user.id);
    }
    
    document.getElementById('userModal').classList.add('active');
}

// Close user modal
function closeUserModal() {
    document.getElementById('userModal').classList.remove('active');
    editingUserId = null;
}

// Save user
async function saveUser(event) {
    event.preventDefault();
    
    const form = document.getElementById('userForm');
    const formData = new FormData(form);
    const data = Object.fromEntries(formData.entries());
    
    // Validate passwords match
    if (data.password && data.password !== data.confirm_password) {
        showError('Mật khẩu xác nhận không khớp');
        return;
    }
    
    // Remove confirm_password from data
    delete data.confirm_password;
    
    // If editing and no password, remove password field
    if (editingUserId && !data.password) {
        delete data.password;
    }
    
    // Collect custom permissions if enabled
    const useCustom = document.getElementById('useCustomPermissions').checked;
    if (useCustom && data.role !== 'super_admin') {
        const permissions = {};
        document.querySelectorAll('.perm-table tbody tr').forEach(row => {
            const checkboxes = row.querySelectorAll('input[type="checkbox"]');
            checkboxes.forEach(cb => {
                const match = cb.name.match(/perm\[(\w+)\]\[(\w+)\]/);
                if (match && cb.checked) {
                    const module = match[1];
                    const perm = match[2];
                    if (!permissions[module]) permissions[module] = [];
                    permissions[module].push(perm);
                }
            });
        });
        data.custom_permissions = permissions;
    } else {
        data.custom_permissions = null;
    }
    
    // Remove perm fields from data
    Object.keys(data).forEach(key => {
        if (key.startsWith('perm[') || key === 'use_custom') {
            delete data[key];
        }
    });
    
    try {
        const url = editingUserId ? `api/users.php?id=${editingUserId}` : 'api/users.php';
        const method = editingUserId ? 'PUT' : 'POST';
        
        const response = await fetch(url, {
            method: method,
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            closeUserModal();
            loadUsers();
            showSuccess(result.message || (editingUserId ? 'Cập nhật thành công' : 'Thêm người dùng thành công'));
        } else {
            showError(result.message || 'Có lỗi xảy ra');
        }
    } catch (error) {
        console.error('Error saving user:', error);
        showError('Lỗi kết nối server');
    }
}

// Confirm delete
function confirmDelete(id, name) {
    if (!checkPermission('delete')) return;
    
    deleteUserId = id;
    document.getElementById('deleteUserName').textContent = name;
    document.getElementById('confirmDeleteBtn').onclick = () => deleteUser(id);
    document.getElementById('deleteModal').classList.add('active');
}

// Close delete modal
function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('active');
    deleteUserId = null;
}

// Delete user
async function deleteUser(id) {
    try {
        const response = await fetch(`api/users.php?id=${id}`, {
            method: 'DELETE'
        });
        
        const result = await response.json();
        
        if (result.success) {
            closeDeleteModal();
            loadUsers();
            showSuccess('Xóa người dùng thành công');
        } else {
            showError(result.message || 'Không thể xóa người dùng');
        }
    } catch (error) {
        console.error('Error deleting user:', error);
        showError('Lỗi kết nối server');
    }
}

// Reset password
function resetPassword(id, username) {
    if (!checkPermission('edit')) return;
    
    resetUserId = id;
    document.getElementById('resetUserName').textContent = username;
    document.getElementById('resetPasswordContent').innerHTML = `
        <p>Đặt lại mật khẩu cho người dùng <strong>${escapeHtml(username)}</strong>?</p>
        <p class="text-muted">Mật khẩu mới sẽ được tạo tự động.</p>
    `;
    document.getElementById('resetPasswordFooter').innerHTML = `
        <button type="button" class="btn btn-secondary" onclick="closeResetPasswordModal()">
            <i class="fas fa-times"></i> Hủy
        </button>
        <button type="button" class="btn btn-primary" onclick="doResetPassword(${id})">
            <i class="fas fa-key"></i> Đặt lại
        </button>
    `;
    document.getElementById('resetPasswordModal').classList.add('active');
}

// Close reset password modal
function closeResetPasswordModal() {
    document.getElementById('resetPasswordModal').classList.remove('active');
    resetUserId = null;
}

// Do reset password
async function doResetPassword(id) {
    try {
        const response = await fetch(`api/users.php?action=reset_password&id=${id}`, {
            method: 'POST'
        });
        
        const result = await response.json();
        
        if (result.success) {
            document.getElementById('resetPasswordContent').innerHTML = `
                <p><i class="fas fa-check-circle" style="color: #2ecc71; margin-right: 8px;"></i> Đặt lại mật khẩu thành công!</p>
                <div class="new-password-display">
                    <div class="label">Mật khẩu mới:</div>
                    <div class="password">${result.new_password}</div>
                    <div class="hint">Nhấn để chọn và copy mật khẩu</div>
                </div>
            `;
            document.getElementById('resetPasswordFooter').innerHTML = `
                <button type="button" class="btn btn-primary" onclick="closeResetPasswordModal()">
                    <i class="fas fa-check"></i> Đóng
                </button>
            `;
        } else {
            showError(result.message || 'Không thể đặt lại mật khẩu');
            closeResetPasswordModal();
        }
    } catch (error) {
        console.error('Error resetting password:', error);
        showError('Lỗi kết nối server');
        closeResetPasswordModal();
    }
}

// Helper functions
function getInitials(name) {
    if (!name) return '?';
    const parts = name.split(' ');
    if (parts.length >= 2) {
        return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
    }
    return name.substring(0, 2).toUpperCase();
}

function formatDate(dateStr) {
    if (!dateStr) return '-';
    const date = new Date(dateStr);
    return date.toLocaleDateString('vi-VN', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function getCurrentUserId() {
    // Get from a hidden element or session data
    const userIdEl = document.querySelector('[data-current-user-id]');
    return userIdEl ? userIdEl.dataset.currentUserId : null;
}

function showSuccess(message) {
    showNotification(message, 'success');
}

function showError(message) {
    showNotification(message, 'error');
}

function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existing = document.querySelector('.notification');
    if (existing) existing.remove();
    
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
        <span>${message}</span>
    `;
    
    // Add styles
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 25px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 10px;
        z-index: 9999;
        animation: slideIn 0.3s ease;
        font-size: 0.95em;
        box-shadow: 0 5px 20px rgba(0,0,0,0.3);
        ${type === 'success' ? 'background: linear-gradient(135deg, #2ecc71, #27ae60); color: #fff;' : ''}
        ${type === 'error' ? 'background: linear-gradient(135deg, #e74c3c, #c0392b); color: #fff;' : ''}
        ${type === 'info' ? 'background: linear-gradient(135deg, #3498db, #2980b9); color: #fff;' : ''}
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Add animation styles
const style = document.createElement('style');
style.textContent = `
    @keyframes slideIn {
        from { opacity: 0; transform: translateX(100px); }
        to { opacity: 1; transform: translateX(0); }
    }
    @keyframes slideOut {
        from { opacity: 1; transform: translateX(0); }
        to { opacity: 0; transform: translateX(100px); }
    }
`;
document.head.appendChild(style);
