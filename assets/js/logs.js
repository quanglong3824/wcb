/**
 * Activity Logs JavaScript
 */

let logs = [];
let currentPage = 1;
let itemsPerPage = 20;
let totalPages = 1;

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadLogs();
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
            loadLogs();
        }, 300);
    });
    
    // Filters
    document.getElementById('actionFilter').addEventListener('change', function() {
        currentPage = 1;
        loadLogs();
    });
    
    document.getElementById('userFilter').addEventListener('change', function() {
        currentPage = 1;
        loadLogs();
    });
    
    document.getElementById('dateFrom').addEventListener('change', function() {
        currentPage = 1;
        loadLogs();
    });
    
    document.getElementById('dateTo').addEventListener('change', function() {
        currentPage = 1;
        loadLogs();
    });
}

// Load users for filter dropdown
async function loadUsers() {
    try {
        const response = await fetch('api/logs.php?get_users=1');
        const data = await response.json();
        
        if (data.success) {
            const select = document.getElementById('userFilter');
            data.users.forEach(user => {
                const option = document.createElement('option');
                option.value = user.id;
                option.textContent = user.full_name || user.username;
                select.appendChild(option);
            });
        }
    } catch (error) {
        console.error('Error loading users:', error);
    }
}

// Load logs from API
async function loadLogs() {
    const tbody = document.getElementById('logsTableBody');
    tbody.innerHTML = `
        <tr>
            <td colspan="7" class="loading-cell">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Đang tải dữ liệu...</p>
            </td>
        </tr>
    `;
    
    try {
        const search = document.getElementById('searchInput').value;
        const action = document.getElementById('actionFilter').value;
        const userId = document.getElementById('userFilter').value;
        const dateFrom = document.getElementById('dateFrom').value;
        const dateTo = document.getElementById('dateTo').value;
        
        const params = new URLSearchParams({
            page: currentPage,
            limit: itemsPerPage,
            search: search,
            action: action,
            user_id: userId,
            date_from: dateFrom,
            date_to: dateTo
        });
        
        const response = await fetch(`api/logs.php?${params}`);
        const data = await response.json();
        
        if (data.success) {
            logs = data.logs;
            totalPages = data.total_pages;
            renderLogs();
            renderPagination();
        } else {
            showError(data.message || 'Không thể tải logs');
        }
    } catch (error) {
        console.error('Error loading logs:', error);
        showError('Lỗi kết nối server');
    }
}

// Render logs table
function renderLogs() {
    const tbody = document.getElementById('logsTableBody');
    
    if (logs.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7">
                    <div class="empty-state">
                        <i class="fas fa-clipboard-list"></i>
                        <h3>Không có dữ liệu</h3>
                        <p>Chưa có hoạt động nào được ghi nhận</p>
                    </div>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = logs.map(log => {
        const actionClass = getActionClass(log.action);
        const actionLabel = getActionLabel(log.action);
        const entityIcon = getEntityIcon(log.entity_type);
        
        return `
            <tr>
                <td>${log.id}</td>
                <td>${formatDate(log.created_at)}</td>
                <td>${escapeHtml(log.user_name || log.username || 'System')}</td>
                <td><span class="action-badge ${actionClass}"><i class="fas fa-${getActionIcon(log.action)}"></i> ${actionLabel}</span></td>
                <td>
                    <span class="entity-type">
                        <i class="fas fa-${entityIcon}"></i>
                        ${log.entity_type || '-'}
                        ${log.entity_id ? `#${log.entity_id}` : ''}
                    </span>
                </td>
                <td><span class="log-description" title="${escapeHtml(log.description || '')}">${escapeHtml(log.description || '-')}</span></td>
                <td><span class="ip-address">${log.ip_address || '-'}</span></td>
            </tr>
        `;
    }).join('');
}

// Get action class for styling
function getActionClass(action) {
    const classes = {
        'login': 'login',
        'logout': 'logout',
        'upload': 'upload',
        'assign': 'assign',
        'unassign': 'unassign',
        'delete': 'delete',
        'update': 'update',
        'create_user': 'create_user',
        'update_user': 'update',
        'delete_user': 'delete',
        'password_reset': 'password_reset',
        'reset_password': 'password_reset'
    };
    return classes[action] || 'default';
}

// Get action label
function getActionLabel(action) {
    const labels = {
        'login': 'Đăng nhập',
        'logout': 'Đăng xuất',
        'upload': 'Upload',
        'assign': 'Gán media',
        'unassign': 'Bỏ gán',
        'delete': 'Xóa',
        'update': 'Cập nhật',
        'create_user': 'Tạo user',
        'update_user': 'Sửa user',
        'delete_user': 'Xóa user',
        'password_reset': 'Reset MK',
        'reset_password': 'Reset MK'
    };
    return labels[action] || action;
}

// Get action icon
function getActionIcon(action) {
    const icons = {
        'login': 'sign-in-alt',
        'logout': 'sign-out-alt',
        'upload': 'upload',
        'assign': 'link',
        'unassign': 'unlink',
        'delete': 'trash',
        'update': 'edit',
        'create_user': 'user-plus',
        'update_user': 'user-edit',
        'delete_user': 'user-minus',
        'password_reset': 'key',
        'reset_password': 'key'
    };
    return icons[action] || 'circle';
}

// Get entity icon
function getEntityIcon(entityType) {
    const icons = {
        'user': 'user',
        'media': 'image',
        'tv': 'tv',
        'schedule': 'calendar',
        'setting': 'cog'
    };
    return icons[entityType] || 'file';
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
    loadLogs();
}

// Export logs to CSV
async function exportLogs() {
    try {
        const search = document.getElementById('searchInput').value;
        const action = document.getElementById('actionFilter').value;
        const userId = document.getElementById('userFilter').value;
        const dateFrom = document.getElementById('dateFrom').value;
        const dateTo = document.getElementById('dateTo').value;
        
        const params = new URLSearchParams({
            export: 'csv',
            search: search,
            action: action,
            user_id: userId,
            date_from: dateFrom,
            date_to: dateTo
        });
        
        window.location.href = `api/logs.php?${params}`;
    } catch (error) {
        console.error('Error exporting logs:', error);
        showError('Lỗi khi export');
    }
}

// Helper functions
function formatDate(dateStr) {
    if (!dateStr) return '-';
    const date = new Date(dateStr);
    return date.toLocaleDateString('vi-VN', {
        day: '2-digit',
        month: '2-digit',
        year: 'numeric',
        hour: '2-digit',
        minute: '2-digit',
        second: '2-digit'
    });
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showError(message) {
    showNotification(message, 'error');
}

function showNotification(message, type = 'info') {
    const existing = document.querySelector('.notification');
    if (existing) existing.remove();
    
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
        <span>${message}</span>
    `;
    
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
        ${type === 'error' ? 'background: linear-gradient(135deg, #e74c3c, #c0392b); color: #fff;' : ''}
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}
