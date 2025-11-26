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
    
    fetch('api/get-tv-status.php')
        .then(response => response.json())
        .then(data => {
            displayTVMonitors(data.tvs || []);
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
            </div>
        `;
        return;
    }
    
    grid.innerHTML = tvs.map(tv => `
        <div class="tv-monitor" data-tv-id="${tv.id}">
            <div class="tv-screen ${tv.currentContent ? '' : 'no-content'}">
                ${getTVScreenContent(tv)}
                <div class="tv-status-badge ${tv.status}">
                    <i class="fas fa-circle"></i>
                    ${tv.status === 'online' ? 'Hoạt động' : 'Offline'}
                </div>
            </div>
            <div class="tv-info">
                <div class="tv-name">${tv.name}</div>
                <div class="tv-location">
                    <i class="fas fa-map-marker-alt"></i>
                    ${tv.location}
                </div>
                <div class="tv-current-content">
                    <strong>Đang chiếu:</strong> 
                    ${tv.currentContent || 'Không có nội dung'}
                </div>
                <div class="tv-actions">
                    <button class="btn-control" onclick="controlTV('${tv.id}')">
                        <i class="fas fa-cog"></i> Điều khiển
                    </button>
                    <button class="btn-view" onclick="viewFullscreen('${tv.id}')">
                        <i class="fas fa-expand"></i> Xem
                    </button>
                </div>
            </div>
        </div>
    `).join('');
}

// Get TV screen content HTML
function getTVScreenContent(tv) {
    if (!tv.currentContent) {
        return `
            <i class="fas fa-tv"></i>
            <div class="no-content-text">Không trình chiếu</div>
        `;
    }
    
    if (tv.contentType === 'image') {
        return `<img src="${tv.contentUrl}" alt="${tv.currentContent}">`;
    } else if (tv.contentType === 'video') {
        return `<video src="${tv.contentUrl}" autoplay muted loop></video>`;
    } else {
        return `
            <i class="fas fa-tv"></i>
            <div class="no-content-text">Không trình chiếu</div>
        `;
    }
}

// Control TV
function controlTV(tvId) {
    window.location.href = `admin/index.php?page=tv&id=${tvId}`;
}

// View fullscreen
function viewFullscreen(tvId) {
    // Open TV display in new window
    window.open(`display.php?tv=${tvId}`, '_blank', 'fullscreen=yes');
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
        const tvLocation = monitor.querySelector('.tv-location').textContent.trim();
        
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
        const badge = monitor.querySelector('.tv-status-badge');
        
        if (status === 'all' || badge.classList.contains(status)) {
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

// Cleanup on page unload
window.addEventListener('beforeunload', () => {
    stopAutoRefresh();
});
