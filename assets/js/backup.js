/**
 * Backup Management JavaScript
 */

let restoreFilename = null;

document.addEventListener('DOMContentLoaded', function() {
    loadBackups();
    loadStats();
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!e.target.closest('.btn-group')) {
            const dropdown = document.getElementById('backupDropdown');
            if (dropdown) dropdown.classList.remove('show');
        }
    });
});

// Toggle backup dropdown menu
function toggleBackupMenu() {
    const dropdown = document.getElementById('backupDropdown');
    dropdown.classList.toggle('show');
}

// Load storage stats
async function loadStats() {
    try {
        const response = await fetch('api/backup.php?action=get_stats');
        const data = await response.json();
        
        if (data.success) {
            document.getElementById('dbSize').textContent = data.stats.database.size_formatted;
            document.getElementById('mediaSize').textContent = data.stats.uploads.size_formatted;
            document.getElementById('mediaCount').textContent = data.stats.uploads.file_count + ' files';
            document.getElementById('wcbSize').textContent = data.stats.wcb_content.size_formatted;
            document.getElementById('wcbCount').textContent = data.stats.wcb_content.file_count + ' files';
            document.getElementById('totalSize').textContent = data.stats.total.size_formatted;
        }
    } catch (error) {
        console.error('Error loading stats:', error);
    }
}

// Load backups list
async function loadBackups() {
    const tbody = document.getElementById('backupsTableBody');
    tbody.innerHTML = `
        <tr>
            <td colspan="5" class="loading-cell">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Đang tải...</p>
            </td>
        </tr>
    `;
    
    try {
        const response = await fetch('api/backup.php?action=list');
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const text = await response.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error('Invalid JSON response:', text);
            throw new Error('Server trả về dữ liệu không hợp lệ');
        }
        
        if (data.success) {
            renderBackups(data.backups);
        } else {
            showError(data.message || 'Không thể tải danh sách backup');
        }
    } catch (error) {
        console.error('Error:', error);
        showError(error.message || 'Lỗi kết nối server');
    }
}

// Render backups table
function renderBackups(backups) {
    const tbody = document.getElementById('backupsTableBody');
    
    if (backups.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="5">
                    <div class="empty-state">
                        <i class="fas fa-database"></i>
                        <h3>Chưa có backup nào</h3>
                        <p>Nhấn "Tạo Backup" để tạo bản sao lưu đầu tiên</p>
                    </div>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = backups.map(backup => {
        const canRestore = backup.type === 'database' || backup.filename.endsWith('.sql.gz');
        return `
            <tr>
                <td>
                    <span class="backup-type backup-type-${backup.type || 'database'}">
                        <i class="${backup.icon || 'fas fa-database'}"></i>
                        ${backup.type_label || 'Database'}
                    </span>
                </td>
                <td>
                    <span class="backup-filename">${escapeHtml(backup.filename)}</span>
                </td>
                <td>${backup.size_formatted}</td>
                <td>${backup.created_at_formatted}</td>
                <td>
                    <div class="action-buttons">
                        <button class="btn-action download" onclick="downloadBackup('${escapeHtml(backup.filename)}')" title="Tải xuống">
                            <i class="fas fa-download"></i>
                        </button>
                        ${canRestore ? `
                        <button class="btn-action restore" onclick="confirmRestore('${escapeHtml(backup.filename)}')" title="Khôi phục">
                            <i class="fas fa-undo"></i>
                        </button>
                        ` : ''}
                        <button class="btn-action delete" onclick="deleteBackup('${escapeHtml(backup.filename)}')" title="Xóa">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }).join('');
}

// Create new backup
async function createBackup(type = 'database') {
    // Close dropdown
    const dropdown = document.getElementById('backupDropdown');
    if (dropdown) dropdown.classList.remove('show');
    
    // Get type labels
    const typeLabels = {
        'database': 'Database',
        'media': 'Media Files',
        'wcb': 'WCB Content',
        'full': 'Full System'
    };
    
    showNotification(`Đang tạo ${typeLabels[type]} backup...`, 'info');
    
    try {
        const response = await fetch(`api/backup.php?action=create&type=${type}`, {
            method: 'POST'
        });
        
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        
        const text = await response.text();
        let data;
        try {
            data = JSON.parse(text);
        } catch (e) {
            console.error('Invalid JSON response:', text);
            throw new Error('Server trả về dữ liệu không hợp lệ');
        }
        
        if (data.success) {
            showSuccess(data.message);
            loadBackups();
            loadStats();
        } else {
            showError(data.message || 'Không thể tạo backup');
        }
    } catch (error) {
        console.error('Error:', error);
        showError(error.message || 'Lỗi kết nối server');
    }
}

// Download backup
function downloadBackup(filename) {
    window.location.href = `api/backup.php?action=download&filename=${encodeURIComponent(filename)}`;
}

// Confirm restore
function confirmRestore(filename) {
    restoreFilename = filename;
    document.getElementById('restoreFilename').textContent = filename;
    document.getElementById('confirmRestoreBtn').onclick = () => doRestore(filename);
    document.getElementById('restoreModal').classList.add('active');
}

// Close restore modal
function closeRestoreModal() {
    document.getElementById('restoreModal').classList.remove('active');
    restoreFilename = null;
}

// Do restore
async function doRestore(filename) {
    const btn = document.getElementById('confirmRestoreBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang khôi phục...';
    
    try {
        const response = await fetch(`api/backup.php?action=restore&filename=${encodeURIComponent(filename)}`, {
            method: 'POST'
        });
        const data = await response.json();
        
        if (data.success) {
            showSuccess(data.message);
            closeRestoreModal();
        } else {
            showError(data.message || 'Không thể khôi phục');
        }
    } catch (error) {
        console.error('Error:', error);
        showError('Lỗi kết nối server');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-undo"></i> Khôi phục';
    }
}

// Delete backup
async function deleteBackup(filename) {
    if (!confirm(`Bạn có chắc chắn muốn xóa backup "${filename}"?`)) {
        return;
    }
    
    try {
        const response = await fetch(`api/backup.php?action=delete&filename=${encodeURIComponent(filename)}`, {
            method: 'DELETE'
        });
        const data = await response.json();
        
        if (data.success) {
            showSuccess(data.message);
            loadBackups();
        } else {
            showError(data.message || 'Không thể xóa backup');
        }
    } catch (error) {
        console.error('Error:', error);
        showError('Lỗi kết nối server');
    }
}

// Helper functions
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

function showSuccess(message) {
    showNotification(message, 'success');
}

function showError(message) {
    showNotification(message, 'error');
}

function showNotification(message, type) {
    const existing = document.querySelector('.notification');
    if (existing) existing.remove();
    
    const icons = {
        'success': 'check-circle',
        'error': 'exclamation-circle',
        'info': 'spinner fa-spin'
    };
    
    const colors = {
        'success': 'background: linear-gradient(135deg, #2ecc71, #27ae60); color: #fff;',
        'error': 'background: linear-gradient(135deg, #e74c3c, #c0392b); color: #fff;',
        'info': 'background: linear-gradient(135deg, #d4af37, #b8941f); color: #fff;'
    };
    
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${icons[type] || 'info-circle'}"></i>
        <span>${message}</span>
    `;
    
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 15px 25px;
        display: flex;
        align-items: center;
        gap: 10px;
        z-index: 9999;
        animation: slideIn 0.3s ease;
        box-shadow: 0 5px 20px rgba(0,0,0,0.3);
        ${colors[type] || colors['info']}
    `;
    
    document.body.appendChild(notification);
    
    // Don't auto-remove info notifications (they will be replaced)
    if (type !== 'info') {
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
}
