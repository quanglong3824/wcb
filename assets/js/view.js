// View Page - TV Monitor JavaScript

let refreshInterval;

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    loadTVMonitors();
    startAutoRefresh();
});

// Load TV monitors
function loadTVMonitors() {
    showLoading();
    
    fetch('api/get-tvs.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                displayTVMonitors(data.tvs || []);
                updateStats(data);
            } else {
                console.error('Error:', data.message);
                displayTVMonitors([]);
            }
            hideLoading();
        })
        .catch(error => {
            console.error('Error loading TV monitors:', error);
            hideLoading();
            displayTVMonitors([]);
        });
}

// Display TV monitors
function displayTVMonitors(tvs) {
    const grid = document.getElementById('tvGrid');
    if (!grid) return;
    
    if (tvs.length === 0) {
        grid.innerHTML = `
            <div class="empty-state" style="grid-column: 1/-1;">
                <i class="fas fa-tv"></i>
                <p>Không có TV nào được cấu hình</p>
                <small>Vui lòng import database để tạo dữ liệu mẫu</small>
            </div>
        `;
        return;
    }
    
    grid.innerHTML = tvs.map(tv => `
        <div class="tv-monitor" data-tv-id="${tv.id}" data-location="${tv.location}" data-status="${tv.actual_status}">
            <div class="tv-screen ${tv.current_content_path ? '' : 'no-content'}">
                ${getTVScreenContent(tv)}
                <div class="tv-status-badge ${tv.actual_status}">
                    <i class="fas fa-circle"></i>
                    ${tv.is_online ? 'Hoạt động' : 'Offline'}
                </div>
            </div>
            <div class="tv-info">
                <div class="tv-name">
                    <i class="fas fa-tv"></i>
                    ${tv.name}
                </div>
                <div class="tv-location">
                    <i class="fas fa-map-marker-alt"></i>
                    ${tv.location}
                </div>
                <div class="tv-current-content">
                    <strong>Đang chiếu:</strong> 
                    ${tv.current_content_name || tv.default_content_name || 'Không có nội dung'}
                </div>
                ${tv.last_heartbeat ? `
                <div class="tv-last-seen">
                    <i class="fas fa-clock"></i>
                    <small>Cập nhật: ${tv.last_seen}</small>
                </div>
                ` : ''}
                <div class="tv-actions">
                    <button class="btn-edit" onclick="openEditModal(${tv.id})" title="Chỉnh sửa TV">
                        <i class="fas fa-edit"></i> Sửa
                    </button>
                    ${tv.display_url ? `
                    <button class="btn-view" onclick="viewFullscreen('${tv.display_url}')" title="Xem toàn màn hình">
                        <i class="fas fa-expand"></i> Xem
                    </button>
                    ` : ''}
                </div>
            </div>
        </div>
    `).join('');
}

// Get TV screen content HTML
function getTVScreenContent(tv) {
    // Ưu tiên current content, nếu không có thì dùng default
    const contentPath = tv.current_content_path || tv.default_content_path;
    const contentType = tv.current_content_type || tv.default_content_type;
    const contentName = tv.current_content_name || tv.default_content_name;
    
    if (!contentPath) {
        return `
            <i class="fas fa-tv"></i>
            <div class="no-content-text">Không trình chiếu</div>
        `;
    }
    
    if (contentType === 'image') {
        return `
            <img src="${contentPath}" alt="${contentName}" onerror="this.parentElement.innerHTML='<i class=\\'fas fa-image\\'></i><div class=\\'no-content-text\\'>Không tải được hình</div>'">
            <div class="content-overlay">
                <i class="fas fa-image"></i>
                <span>${contentName}</span>
            </div>
        `;
    } else if (contentType === 'video') {
        return `
            <video src="${contentPath}" autoplay muted loop onerror="this.parentElement.innerHTML='<i class=\\'fas fa-video\\'></i><div class=\\'no-content-text\\'>Không tải được video</div>'"></video>
            <div class="content-overlay">
                <i class="fas fa-video"></i>
                <span>${contentName}</span>
            </div>
        `;
    } else {
        return `
            <i class="fas fa-tv"></i>
            <div class="no-content-text">Không trình chiếu</div>
        `;
    }
}

// Update statistics
function updateStats(data) {
    // Có thể thêm hiển thị thống kê ở header nếu cần
    console.log(`Total TVs: ${data.total}, Online: ${data.online_count}, Offline: ${data.offline_count}`);
}

// Control TV
function controlTV(tvId) {
    window.location.href = `tv.php?id=${tvId}`;
}

// View fullscreen
function viewFullscreen(displayUrl) {
    // Open TV display in new window
    window.open(displayUrl, '_blank', 'fullscreen=yes,width=' + screen.width + ',height=' + screen.height);
}

// Refresh monitors
function refreshMonitors() {
    const btn = document.querySelector('.refresh-btn i');
    if (btn) {
        btn.classList.add('fa-spin');
    }
    
    loadTVMonitors();
    
    setTimeout(() => {
        if (btn) {
            btn.classList.remove('fa-spin');
        }
    }, 1000);
}

// Start auto refresh
function startAutoRefresh() {
    // Refresh every 30 seconds
    refreshInterval = setInterval(() => {
        loadTVMonitors();
    }, 30000);
}

// Stop auto refresh
function stopAutoRefresh() {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
}

// Filter by location
function filterByLocation(location) {
    const monitors = document.querySelectorAll('.tv-monitor');
    
    monitors.forEach(monitor => {
        const tvLocation = monitor.getAttribute('data-location');
        
        if (location === 'all' || tvLocation.includes(location)) {
            monitor.style.display = 'block';
        } else {
            monitor.style.display = 'none';
        }
    });
}

// Filter by status
function filterByStatus(status) {
    const monitors = document.querySelectorAll('.tv-monitor');
    
    monitors.forEach(monitor => {
        const tvStatus = monitor.getAttribute('data-status');
        
        if (status === 'all' || tvStatus === status) {
            monitor.style.display = 'block';
        } else {
            monitor.style.display = 'none';
        }
    });
}

// Show loading overlay
function showLoading() {
    const overlay = document.createElement('div');
    overlay.className = 'loading-overlay';
    overlay.id = 'loadingOverlay';
    overlay.innerHTML = '<div class="loading-spinner"></div>';
    document.body.appendChild(overlay);
}

// Hide loading overlay
function hideLoading() {
    const overlay = document.getElementById('loadingOverlay');
    if (overlay) {
        overlay.remove();
    }
}

// Redirect to TV management page for editing
function openEditModal(tvId) {
    window.location.href = `tv.php#edit-${tvId}`;
}

// Cleanup on page unload
window.addEventListener('beforeunload', () => {
    stopAutoRefresh();
});
