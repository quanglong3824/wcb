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
            console.log('TV data received:', data);
            if (data.success) {
                allTVs = data.tvs; // Store all TVs
                console.log('Stored TVs:', allTVs);
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
    
    // Build single large preview with assigned media
    let previewHTML = '';
    
    if (tv.assigned_media && tv.assigned_media.length > 0) {
        // Show first assigned media (default or first one)
        const firstMedia = tv.assigned_media[0];
        const mediaCount = tv.assigned_media.length;
        
        previewHTML = `
            <div class="tv-preview-large" onclick="showTVMediaModal(${tv.id})">
                ${firstMedia.type === 'image' ? `
                    <img src="${escapeHtml(firstMedia.file_path)}" alt="${escapeHtml(firstMedia.name)}" 
                         onerror="this.src='assets/img/no-image.png'">
                ` : `
                    <div class="video-preview-large">
                        <i class="fas fa-play-circle"></i>
                        <span>Video</span>
                    </div>
                `}
                <div class="preview-overlay">
                    <div class="preview-info">
                        <span class="preview-name">${escapeHtml(firstMedia.name)}</span>
                        ${firstMedia.is_default ? '<span class="preview-badge">Mặc định</span>' : ''}
                    </div>
                    ${mediaCount > 1 ? `
                        <div class="preview-count">
                            <i class="fas fa-images"></i> +${mediaCount - 1} WCB khác
                        </div>
                    ` : ''}
                </div>
            </div>
        `;
    } else {
        previewHTML = `
            <div class="tv-preview-large empty">
                <div class="tv-preview-placeholder">
                    <i class="fas fa-tv"></i>
                    <p>Chưa gán WCB</p>
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
            
            ${previewHTML}
            
            <div class="tv-card-body">
                <div class="tv-detail-row">
                    <span class="tv-detail-label">Folder:</span>
                    <span class="tv-detail-value">${escapeHtml(tv.folder)}</span>
                </div>
                
                ${tv.description ? `
                    <div class="tv-detail-row">
                        <span class="tv-detail-label">Mô tả:</span>
                        <span class="tv-detail-value">${escapeHtml(tv.description)}</span>
                    </div>
                ` : ''}
                
                ${tv.assigned_media && tv.assigned_media.length > 0 ? `
                    <div class="tv-detail-row">
                        <span class="tv-detail-label">WCB đã gán:</span>
                        <span class="tv-detail-value">${tv.assigned_media_count} file</span>
                    </div>
                ` : ''}
            </div>
            
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

// View TV (open in new tab with fullscreen)
function viewTV(tvId) {
    const tv = allTVs.find(t => t.id == tvId);
    if (!tv || !tv.folder) {
        alert('Không tìm thấy thông tin TV!');
        return;
    }
    
    // Open TV display in new window
    const url = tv.folder + '/index.php';
    const newWindow = window.open(url, '_blank', 'width=1920,height=1080');
    
    // Try to make it fullscreen after a short delay
    if (newWindow) {
        setTimeout(() => {
            try {
                // Request fullscreen on the new window's document
                if (newWindow.document.documentElement.requestFullscreen) {
                    newWindow.document.documentElement.requestFullscreen();
                } else if (newWindow.document.documentElement.webkitRequestFullscreen) {
                    newWindow.document.documentElement.webkitRequestFullscreen();
                } else if (newWindow.document.documentElement.mozRequestFullScreen) {
                    newWindow.document.documentElement.mozRequestFullScreen();
                } else if (newWindow.document.documentElement.msRequestFullscreen) {
                    newWindow.document.documentElement.msRequestFullscreen();
                }
            } catch (e) {
                console.log('Fullscreen request failed:', e);
                // Fallback: maximize window
                newWindow.moveTo(0, 0);
                newWindow.resizeTo(screen.width, screen.height);
            }
        }, 1000);
    }
}

// Edit TV - Open modal with TV data
function editTV(tvId) {
    const tv = allTVs.find(t => t.id == tvId);
    if (!tv) {
        alert('Không tìm thấy thông tin TV!');
        return;
    }
    
    // Fill form with TV data
    document.getElementById('editTvId').value = tv.id;
    document.getElementById('editTvName').value = tv.name;
    document.getElementById('editTvLocation').value = tv.location;
    document.getElementById('editTvFolder').value = tv.folder;
    document.getElementById('editTvIpAddress').value = tv.ip_address || '';
    document.getElementById('editTvStatus').value = tv.status;
    document.getElementById('editTvDescription').value = tv.description || '';
    
    // Show modal
    const modal = document.getElementById('editTVModal');
    modal.classList.add('active');
    
    // Prevent body scroll
    document.body.style.overflow = 'hidden';
}

// Close edit modal
function closeEditModal() {
    const modal = document.getElementById('editTVModal');
    modal.classList.remove('active');
    
    // Restore body scroll
    document.body.style.overflow = '';
}

// Save TV
function saveTV(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    
    // Show loading
    const submitBtn = event.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang lưu...';
    submitBtn.disabled = true;
    
    fetch('api/update-tv.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            showMessage('Cập nhật thông tin TV thành công!', 'success');
            
            // Close modal
            closeEditModal();
            
            // Reload TV list
            loadTVs();
        } else {
            showMessage(data.message || 'Có lỗi xảy ra khi cập nhật!', 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Có lỗi xảy ra khi cập nhật!', 'error');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// Show message
function showMessage(message, type) {
    // Remove existing messages
    const existingMsg = document.querySelector('.alert-message');
    if (existingMsg) {
        existingMsg.remove();
    }
    
    // Create message element
    const msgDiv = document.createElement('div');
    msgDiv.className = `alert-message alert-${type}`;
    msgDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        <span>${message}</span>
    `;
    
    // Insert at top of container
    const container = document.querySelector('.tv-container');
    container.insertBefore(msgDiv, container.firstChild);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        msgDiv.style.animation = 'fadeOut 0.3s';
        setTimeout(() => msgDiv.remove(), 300);
    }, 5000);
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('editTVModal');
    if (event.target === modal) {
        closeEditModal();
    }
});

// Close modal with ESC key
document.addEventListener('keydown', function(event) {
    if (event.key === 'Escape') {
        closeEditModal();
    }
});

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


// Show TV media modal
function showTVMediaModal(tvId) {
    console.log('Opening modal for TV ID:', tvId);
    console.log('All TVs:', allTVs);
    
    const tv = allTVs.find(t => t.id == tvId);
    console.log('Found TV:', tv);
    
    if (!tv) {
        alert('Không tìm thấy TV!');
        return;
    }
    
    console.log('TV assigned media:', tv.assigned_media);
    
    const modalHTML = `
        <div id="tvMediaModal" class="tv-media-modal">
            <div class="tv-media-modal-content">
                <div class="tv-media-modal-header">
                    <div class="modal-header-info">
                        <h2><i class="fas fa-tv"></i> ${escapeHtml(tv.name)}</h2>
                        <p>${escapeHtml(tv.location)}</p>
                    </div>
                    <button class="modal-close" onclick="closeTVMediaModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="tv-media-modal-body">
                    ${tv.assigned_media && tv.assigned_media.length > 0 ? `
                        <div class="media-grid">
                            ${tv.assigned_media.map(media => `
                                <div class="media-grid-item ${media.is_default ? 'is-default' : ''}" 
                                     onclick="viewMediaInNewTab('${escapeHtml(media.file_path)}')">
                                    <div class="media-grid-thumb">
                                        ${media.type === 'image' ? `
                                            <img src="${escapeHtml(media.file_path)}" alt="${escapeHtml(media.name)}" 
                                                 onerror="this.src='assets/img/no-image.png'">
                                        ` : `
                                            <div class="video-thumb-grid">
                                                <i class="fas fa-play-circle"></i>
                                            </div>
                                        `}
                                        ${media.is_default ? '<span class="default-badge-grid">Mặc định</span>' : ''}
                                    </div>
                                    <div class="media-grid-info">
                                        <div class="media-grid-name" title="${escapeHtml(media.name)}">
                                            ${escapeHtml(media.name)}
                                        </div>
                                        <div class="media-grid-type">
                                            <i class="fas fa-${media.type === 'image' ? 'image' : 'video'}"></i>
                                            ${media.type === 'image' ? 'Hình ảnh' : 'Video'}
                                        </div>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    ` : `
                        <div class="no-media-message">
                            <i class="fas fa-inbox"></i>
                            <p>TV này chưa có WCB nào được gán</p>
                        </div>
                    `}
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    document.body.style.overflow = 'hidden';
}

// Close TV media modal
function closeTVMediaModal() {
    const modal = document.getElementById('tvMediaModal');
    if (modal) {
        modal.remove();
    }
    document.body.style.overflow = '';
}

// View media in new tab
function viewMediaInNewTab(filePath) {
    if (filePath) {
        window.open(filePath, '_blank');
    }
}
