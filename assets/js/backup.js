/**
 * Backup Management JavaScript
 */

let restoreFilename = null;

document.addEventListener('DOMContentLoaded', function() {
    loadBackups();
});

// Load backups list
async function loadBackups() {
    const tbody = document.getElementById('backupsTableBody');
    tbody.innerHTML = `
        <tr>
            <td colspan="4" class="loading-cell">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Đang tải...</p>
            </td>
        </tr>
    `;
    
    try {
        const response = await fetch('api/backup.php?action=list');
        const data = await response.json();
        
        if (data.success) {
            renderBackups(data.backups);
        } else {
            showError(data.message || 'Không thể tải danh sách backup');
        }
    } catch (error) {
        console.error('Error:', error);
        showError('Lỗi kết nối server');
    }
}

// Render backups table
function renderBackups(backups) {
    const tbody = document.getElementById('backupsTableBody');
    
    if (backups.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="4">
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
    
    tbody.innerHTML = backups.map(backup => `
        <tr>
            <td>
                <i class="fas fa-file-archive" style="color: #d4af37; margin-right: 10px;"></i>
                ${escapeHtml(backup.filename)}
            </td>
            <td>${backup.size_formatted}</td>
            <td>${backup.created_at_formatted}</td>
            <td>
                <div class="action-buttons">
                    <button class="btn-action download" onclick="downloadBackup('${escapeHtml(backup.filename)}')" title="Tải xuống">
                        <i class="fas fa-download"></i>
                    </button>
                    <button class="btn-action restore" onclick="confirmRestore('${escapeHtml(backup.filename)}')" title="Khôi phục">
                        <i class="fas fa-undo"></i>
                    </button>
                    <button class="btn-action delete" onclick="deleteBackup('${escapeHtml(backup.filename)}')" title="Xóa">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
}

// Create new backup
async function createBackup() {
    const btn = document.getElementById('createBackupBtn');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang tạo...';
    
    try {
        const response = await fetch('api/backup.php?action=create', {
            method: 'POST'
        });
        const data = await response.json();
        
        if (data.success) {
            showSuccess(data.message);
            loadBackups();
        } else {
            showError(data.message || 'Không thể tạo backup');
        }
    } catch (error) {
        console.error('Error:', error);
        showError('Lỗi kết nối server');
    } finally {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-plus"></i> Tạo Backup';
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
    
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
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
        box-shadow: 0 5px 20px rgba(0,0,0,0.3);
        ${type === 'success' ? 'background: linear-gradient(135deg, #2ecc71, #27ae60); color: #fff;' : ''}
        ${type === 'error' ? 'background: linear-gradient(135deg, #e74c3c, #c0392b); color: #fff;' : ''}
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.remove();
    }, 3000);
}
