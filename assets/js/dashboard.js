/**
 * Dashboard JavaScript
 * Load và hiển thị thống kê dashboard
 */

document.addEventListener('DOMContentLoaded', function() {
    loadDashboardData();
    
    // Auto refresh every 30 seconds
    setInterval(loadDashboardData, 30000);
});

function loadDashboardData() {
    fetch('api/get-dashboard-stats.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateStats(data.stats);
                updateRecentActivities(data.recent_activities);
                updateTVStatus(data.tv_details);
            } else {
                console.error('Error loading dashboard data:', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

function updateStats(stats) {
    const statsGrid = document.getElementById('statsGrid');
    
    statsGrid.innerHTML = `
        <div class="stat-card stat-blue">
            <div class="stat-card-header">
                <div class="stat-card-icon">
                    <i class="fas fa-tv"></i>
                </div>
            </div>
            <div class="stat-card-value">${stats.tv.total_tvs}</div>
            <div class="stat-card-label">Tổng số TV</div>
            <div class="stat-card-footer">
                <span class="stat-detail"><i class="fas fa-check-circle"></i> ${stats.tv.online_tvs} Online</span>
                <span class="stat-detail"><i class="fas fa-times-circle"></i> ${stats.tv.offline_tvs} Offline</span>
            </div>
        </div>
        
        <div class="stat-card stat-green">
            <div class="stat-card-header">
                <div class="stat-card-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
            </div>
            <div class="stat-card-value">${stats.tv.online_tvs}</div>
            <div class="stat-card-label">TV đang Online</div>
            <div class="stat-card-footer">
                <span class="stat-detail">${Math.round((stats.tv.online_tvs / stats.tv.total_tvs) * 100)}% hoạt động</span>
            </div>
        </div>
        
        <div class="stat-card stat-orange">
            <div class="stat-card-header">
                <div class="stat-card-icon">
                    <i class="fas fa-images"></i>
                </div>
            </div>
            <div class="stat-card-value">${stats.media.total_media}</div>
            <div class="stat-card-label">Nội dung WCB</div>
            <div class="stat-card-footer">
                <span class="stat-detail"><i class="fas fa-image"></i> ${stats.media.total_images} ảnh</span>
                <span class="stat-detail"><i class="fas fa-video"></i> ${stats.media.total_videos} video</span>
            </div>
        </div>
        
        <div class="stat-card stat-purple">
            <div class="stat-card-header">
                <div class="stat-card-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
            </div>
            <div class="stat-card-value">${stats.schedule.today_schedules}</div>
            <div class="stat-card-label">Lịch chiếu hôm nay</div>
            <div class="stat-card-footer">
                <span class="stat-detail">${stats.schedule.active_schedules} lịch đang active</span>
            </div>
        </div>
    `;
}

function updateRecentActivities(activities) {
    const tbody = document.getElementById('activityTableBody');
    
    if (activities.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="4" style="text-align: center; padding: 40px; color: #999;">
                    <i class="fas fa-inbox" style="font-size: 3em; display: block; margin-bottom: 15px; opacity: 0.3;"></i>
                    <p>Chưa có hoạt động nào</p>
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = activities.map(activity => {
        const actionBadge = getActionBadge(activity.action);
        
        return `
            <tr>
                <td>
                    <div style="font-size: 0.9em; color: #6b7280;">${activity.time_ago}</div>
                    <div style="font-size: 0.8em; color: #9ca3af;">${activity.created_at_formatted}</div>
                </td>
                <td>
                    <strong>${escapeHtml(activity.user_name || 'System')}</strong>
                    <div style="font-size: 0.85em; color: #9ca3af;">@${escapeHtml(activity.username || 'system')}</div>
                </td>
                <td>
                    ${actionBadge}
                    <div style="margin-top: 5px; font-size: 0.9em; color: #6b7280;">${escapeHtml(activity.description)}</div>
                </td>
                <td>
                    <span class="badge badge-success">
                        <i class="fas fa-check"></i> Thành công
                    </span>
                </td>
            </tr>
        `;
    }).join('');
}

function updateTVStatus(tvDetails) {
    const grid = document.getElementById('tvStatusGrid');
    
    if (tvDetails.length === 0) {
        grid.innerHTML = `
            <div style="text-align: center; padding: 40px; color: #999;">
                <i class="fas fa-tv" style="font-size: 3em; opacity: 0.3;"></i>
                <p style="margin-top: 15px;">Chưa có TV nào</p>
            </div>
        `;
        return;
    }
    
    grid.innerHTML = tvDetails.map(tv => `
        <div class="tv-status-item ${tv.status}">
            <div class="tv-status-header">
                <i class="fas fa-tv"></i>
                <span class="tv-status-badge ${tv.status}">${tv.status === 'online' ? 'Online' : 'Offline'}</span>
            </div>
            <h4>${escapeHtml(tv.name)}</h4>
            <p class="tv-location">${escapeHtml(tv.location)}</p>
            ${tv.current_content_name ? `
                <div class="tv-current-content">
                    <i class="fas fa-${tv.current_content_type === 'image' ? 'image' : 'video'}"></i>
                    <span>${escapeHtml(tv.current_content_name)}</span>
                </div>
            ` : '<div class="tv-no-content">Chưa có nội dung</div>'}
            <div class="tv-last-seen">
                <i class="fas fa-clock"></i> ${tv.last_heartbeat_formatted}
            </div>
        </div>
    `).join('');
}

function getActionBadge(action) {
    const badges = {
        'login': '<span class="action-badge badge-blue"><i class="fas fa-sign-in-alt"></i> Đăng nhập</span>',
        'upload': '<span class="action-badge badge-green"><i class="fas fa-upload"></i> Upload</span>',
        'assign': '<span class="action-badge badge-purple"><i class="fas fa-link"></i> Gán WCB</span>',
        'unassign': '<span class="action-badge badge-orange"><i class="fas fa-unlink"></i> Hủy gán</span>',
        'reload': '<span class="action-badge badge-cyan"><i class="fas fa-sync-alt"></i> Tải lại</span>',
        'toggle_status': '<span class="action-badge badge-yellow"><i class="fas fa-power-off"></i> Đổi trạng thái</span>',
        'orchid_mode': '<span class="action-badge badge-purple"><i class="fas fa-layer-group"></i> Orchid Mode</span>',
        'shutdown': '<span class="action-badge badge-red"><i class="fas fa-power-off"></i> Tắt hệ thống</span>',
        'create': '<span class="action-badge badge-green"><i class="fas fa-plus"></i> Tạo mới</span>',
        'update': '<span class="action-badge badge-blue"><i class="fas fa-edit"></i> Cập nhật</span>',
        'delete': '<span class="action-badge badge-red"><i class="fas fa-trash"></i> Xóa</span>'
    };
    
    return badges[action] || `<span class="action-badge badge-gray"><i class="fas fa-circle"></i> ${action}</span>`;
}

function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
