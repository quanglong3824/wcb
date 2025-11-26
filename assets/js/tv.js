/**
 * TV Management JavaScript
 */

// Global variable to store all TVs
let allTVs = [];

// Load TVs on page load
document.addEventListener('DOMContentLoaded', function() {
    loadTVs();
});

// Load TVs from API
function loadTVs() {
    fetch('api/get-tvs.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                allTVs = data.tvs; // Store all TVs
                displayTVs(allTVs);
            } else {
                showError(data.message || 'Không thể tải dữ liệu TV');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('Lỗi kết nối. Vui lòng thử lại.');
        });
}

// Display TVs in grid
function displayTVs(tvs) {
    const grid = document.getElementById('tvGrid');
    
    if (!tvs || tvs.length === 0) {
        grid.innerHTML = `
            <div class="empty-state" style="grid-column: 1/-1;">
                <i class="fas fa-tv"></i>
                <p>Chưa có TV nào trong hệ thống</p>
                <p style="font-size: 0.9em;">Vui lòng import database để thêm 7 TV mặc định</p>
            </div>
        `;
        return;
    }
    
    grid.innerHTML = tvs.map(tv => createTVCard(tv)).join('');
}

// Create TV card HTML
function createTVCard(tv) {
    const statusClass = tv.status === 'online' ? 'online' : 'offline';
    const statusText = tv.status === 'online' ? 'Online' : 'Offline';
    
    // Determine content type icon
    let contentIcon = 'fas fa-film';
    let contentTypeText = '';
    
    if (tv.current_content_type === 'image') {
        contentIcon = 'fas fa-image';
        contentTypeText = 'Hình ảnh';
    } else if (tv.current_content_type === 'video') {
        contentIcon = 'fas fa-video';
        contentTypeText = 'Video';
    }
    
    // Build playing content HTML
    let playingContentHTML = '';
    if (tv.current_content_id && tv.current_content_name) {
        playingContentHTML = `
            <div class="tv-playing">
                <div class="tv-playing-header">
                    <i class="fas fa-play-circle"></i>
                    Đang trình chiếu
                </div>
                <div class="tv-playing-content">
                    <div class="tv-playing-icon">
                        <i class="${contentIcon}"></i>
                    </div>
                    <div class="tv-playing-info">
                        <div class="tv-playing-name">${escapeHtml(tv.current_content_name)}</div>
                        <div class="tv-playing-type">
                            <i class="${contentIcon}"></i>
                            ${contentTypeText}
                        </div>
                    </div>
                </div>
            </div>
        `;
    } else {
        playingContentHTML = `
            <div class="tv-playing">
                <div class="tv-no-content">
                    <i class="fas fa-ban"></i>
                    Không trình chiếu
                </div>
            </div>
        `;
    }
    
    return `
        <div class="tv-card">
            <div class="tv-card-header">
                <div class="tv-info">
                    <div class="tv-name">
                        <i class="fas fa-tv"></i>
                        ${escapeHtml(tv.name)}
                    </div>
                    <div class="tv-location">
                        <i class="fas fa-map-marker-alt"></i>
                        ${escapeHtml(tv.location)}
                    </div>
                </div>
                <span class="tv-status-badge ${statusClass}">
                    ${statusText}
                </span>
            </div>
            
            <div class="tv-card-body">
                <div class="tv-detail-row">
                    <span class="tv-detail-label">Folder:</span>
                    <span class="tv-detail-value">${escapeHtml(tv.folder)}</span>
                </div>
                
                ${tv.description ? `
                    <div class="tv-content-preview">
                        <p><strong>Mô tả:</strong> ${escapeHtml(tv.description)}</p>
                    </div>
                ` : ''}
            </div>
            
            ${playingContentHTML}
            
            <div class="tv-card-actions">
                <button class="btn-tv-action btn-view" onclick="viewTV(${tv.id})">
                    <i class="fas fa-eye"></i> Xem
                </button>
                <button class="btn-tv-action btn-edit" onclick="editTV(${tv.id})">
                    <i class="fas fa-edit"></i> Sửa
                </button>
            </div>
        </div>
    `;
}

// View TV (open in new tab)
function viewTV(tvId) {
    // TODO: Get TV folder from data
    const tvFolders = {
        1: 'basement',
        2: 'chrysan',
        3: 'jasmine',
        4: 'lotus',
        5: 'restaurant',
        6: 'fo/tv1',
        7: 'fo/tv2'
    };
    
    const folder = tvFolders[tvId];
    if (folder) {
        window.open(folder + '/index.php', '_blank');
    }
}

// Edit TV
function editTV(tvId) {
    alert('Chức năng chỉnh sửa TV đang được phát triển.\nTV ID: ' + tvId);
    // TODO: Implement edit functionality
}

// Show error message
function showError(message) {
    const grid = document.getElementById('tvGrid');
    grid.innerHTML = `
        <div class="empty-state" style="grid-column: 1/-1;">
            <i class="fas fa-exclamation-triangle" style="color: #ef4444;"></i>
            <p style="color: #ef4444;">${escapeHtml(message)}</p>
            <button class="btn btn-primary" onclick="loadTVs()" style="margin-top: 15px;">
                <i class="fas fa-sync-alt"></i> Thử lại
            </button>
        </div>
    `;
}

// Escape HTML to prevent XSS
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Filter TVs based on search and filters
function filterTVs() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const statusFilter = document.getElementById('statusFilter').value;
    const contentFilter = document.getElementById('contentFilter').value;
    
    let filteredTVs = allTVs.filter(tv => {
        // Search filter
        const matchesSearch = !searchTerm || 
            tv.name.toLowerCase().includes(searchTerm) ||
            tv.location.toLowerCase().includes(searchTerm) ||
            (tv.folder && tv.folder.toLowerCase().includes(searchTerm));
        
        // Status filter
        const matchesStatus = statusFilter === 'all' || tv.status === statusFilter;
        
        // Content filter
        let matchesContent = true;
        if (contentFilter === 'playing') {
            matchesContent = tv.current_content_id !== null;
        } else if (contentFilter === 'idle') {
            matchesContent = tv.current_content_id === null;
        }
        
        return matchesSearch && matchesStatus && matchesContent;
    });
    
    displayTVs(filteredTVs);
    
    // Show filter result count
    if (searchTerm || statusFilter !== 'all' || contentFilter !== 'all') {
        showFilterResult(filteredTVs.length, allTVs.length);
    }
}

// Show filter result count
function showFilterResult(filtered, total) {
    const grid = document.getElementById('tvGrid');
    
    if (filtered === 0) {
        grid.innerHTML = `
            <div class="empty-state" style="grid-column: 1/-1;">
                <i class="fas fa-search"></i>
                <p>Không tìm thấy TV nào phù hợp</p>
                <button class="btn btn-secondary" onclick="clearFilters()" style="margin-top: 15px;">
                    <i class="fas fa-times"></i> Xóa bộ lọc
                </button>
            </div>
        `;
    }
}

// Clear all filters
function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('statusFilter').value = 'all';
    document.getElementById('contentFilter').value = 'all';
    displayTVs(allTVs);
}

// Refresh TVs
function refreshTVs() {
    // Show loading state
    const grid = document.getElementById('tvGrid');
    grid.innerHTML = `
        <div class="empty-state" style="grid-column: 1/-1;">
            <i class="fas fa-spinner fa-spin"></i>
            <p>Đang làm mới...</p>
        </div>
    `;
    
    // Clear filters
    clearFilters();
    
    // Reload data
    loadTVs();
}
