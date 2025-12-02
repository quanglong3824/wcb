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
    // Determine detailed status
    let statusClass, statusText, statusIcon;
    
    if (tv.status === 'offline') {
        statusClass = 'standby';
        statusText = 'Standby';
        statusIcon = 'fas fa-moon';
    } else if (tv.status === 'online' && tv.current_content_id) {
        statusClass = 'playing';
        statusText = 'Playing';
        statusIcon = 'fas fa-play-circle';
    } else {
        statusClass = 'online';
        statusText = 'Online';
        statusIcon = 'fas fa-check-circle';
    }
    
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
                    <img src="assets/img/logo-dark-ui.png" 
                         alt="Logo" 
                         class="empty-logo"
                         onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                    <div class="empty-fallback">
                        <i class="fas fa-tv"></i>
                        <p>Chưa gán WCB</p>
                    </div>
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
                <div class="tv-status-toggle">
                    <label class="toggle-switch" title="Bật/Tắt TV">
                        <input type="checkbox" 
                               ${tv.status === 'online' ? 'checked' : ''} 
                               onchange="toggleTVStatus(${tv.id}, this.checked, '${escapeHtml(tv.name)}')">
                        <span class="toggle-slider"></span>
                    </label>
                    <span class="tv-status-text ${statusClass}">
                        <i class="${statusIcon}"></i> ${statusText}
                    </span>
                </div>
            </div>
            
            ${previewHTML}
            
            <div class="tv-card-body">
                ${tv.assigned_media && tv.assigned_media.length > 0 ? `
                    <div class="tv-wcb-count">
                        <i class="fas fa-images"></i>
                        <span>${tv.assigned_media_count} WCB</span>
                    </div>
                ` : `
                    <div class="tv-wcb-count empty">
                        <i class="fas fa-inbox"></i>
                        <span>Chưa gán WCB</span>
                    </div>
                `}
            </div>
            
            <div class="tv-card-actions">
                <button class="btn-tv-action btn-view" onclick="viewTV(${tv.id})">
                    <i class="fas fa-eye"></i> Xem
                </button>
                <button class="btn-tv-action btn-reload" onclick="forceReloadTV(${tv.id}, '${escapeHtml(tv.name)}')" title="Ép tải lại TV từ xa">
                    <i class="fas fa-sync-alt"></i> Tải lại
                </button>
                <button class="btn-tv-action btn-pause ${tv.is_paused ? 'active' : ''}" onclick="togglePauseTV(${tv.id}, '${escapeHtml(tv.name)}')" title="${tv.is_paused ? 'Tiếp tục chiếu' : 'Tạm dừng - Chế độ chờ'}">
                    <i class="fas fa-${tv.is_paused ? 'play' : 'pause'}"></i> ${tv.is_paused ? 'Tiếp tục' : 'Tạm dừng'}
                </button>
                <button class="btn-tv-action btn-assign" onclick="assignWCBToTV(${tv.id})">
                    <i class="fas fa-plus-circle"></i> Gán WCB
                </button>
                <button class="btn-tv-action btn-edit" onclick="editTV(${tv.id})">
                    <i class="fas fa-edit"></i> Sửa
                </button>
            </div>
        </div>
    `;
}

// View TV (open in new blank tab)
function viewTV(tvId) {
    const tv = allTVs.find(t => t.id == tvId);
    if (!tv || !tv.folder) {
        alert('Không tìm thấy thông tin TV!');
        return;
    }
    
    // Open TV display in new blank tab
    const url = tv.folder + '/index.php';
    window.open(url, '_blank');
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

// Force Fullscreen All TVs - Gửi lệnh fullscreen và reload đến tất cả TV
function forceFullscreenAllTVs() {
    if (!confirm('Bạn có chắc muốn ép TẤT CẢ 7 TV vào chế độ toàn màn hình?\n\nTV sẽ tự động chuyển sang fullscreen và hiển thị WCB full màn hình.')) {
        return;
    }
    
    const btn = document.querySelector('.btn-fullscreen-all');
    if (!btn) {
        console.error('Button not found');
        return;
    }
    
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang gửi...';
    btn.disabled = true;
    
    // Gửi cả lệnh fullscreen và reload
    Promise.all([
        fetch('api/fullscreen-all-tvs.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' }
        }),
        fetch('api/reload-all-tvs.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' }
        })
    ])
    .then(responses => Promise.all(responses.map(r => r.json())))
    .then(results => {
        const fullscreenResult = results[0];
        const reloadResult = results[1];
        
        if (fullscreenResult.success || reloadResult.success) {
            showMessage('Đã gửi lệnh fullscreen và reload đến tất cả TV!', 'success');
            
            // Countdown
            let countdown = 5;
            const countdownInterval = setInterval(() => {
                btn.innerHTML = `<i class="fas fa-clock"></i> ${countdown}s`;
                countdown--;
                
                if (countdown < 0) {
                    clearInterval(countdownInterval);
                    btn.innerHTML = originalHTML;
                    btn.disabled = false;
                }
            }, 1000);
        } else {
            showMessage('Lỗi: ' + (fullscreenResult.message || reloadResult.message), 'error');
            btn.innerHTML = originalHTML;
            btn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Có lỗi xảy ra: ' + error.message, 'error');
        btn.innerHTML = originalHTML;
        btn.disabled = false;
    });
}

// Refresh Data using AJAX - No page reload, smooth update
function refreshDataAjax() {
    const btn = document.querySelector('.btn-refresh-data');
    if (!btn) return;
    
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang tải...';
    btn.disabled = true;
    
    // Fade out current cards slightly
    const cards = document.querySelectorAll('.tv-card');
    cards.forEach(card => {
        card.style.opacity = '0.6';
        card.style.transition = 'opacity 0.3s';
    });
    
    // Fetch new data
    fetch('api/get-tvs.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                allTVs = data.tvs;
                
                // Smooth update - fade in new content
                displayTVs(allTVs);
                
                // Animate new cards
                const newCards = document.querySelectorAll('.tv-card');
                newCards.forEach((card, index) => {
                    card.style.opacity = '0';
                    card.style.transform = 'translateY(10px)';
                    card.style.transition = 'opacity 0.3s, transform 0.3s';
                    
                    setTimeout(() => {
                        card.style.opacity = '1';
                        card.style.transform = 'translateY(0)';
                    }, index * 50);
                });
                
                showMessage('Đã cập nhật dữ liệu!', 'success');
            } else {
                showMessage('Lỗi: ' + (data.message || 'Không thể tải dữ liệu'), 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('Có lỗi xảy ra khi tải dữ liệu!', 'error');
        })
        .finally(() => {
            btn.innerHTML = originalHTML;
            btn.disabled = false;
        });
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

// ============================================
// SPECIAL MODES
// ============================================

// Open Orchid Mode - Assign 1 WCB to 6 TVs (exclude Restaurant)
function openOrchidMode() {
    // Load all WCBs
    fetch('api/get-wcb.php')
        .then(r => r.json())
        .then(data => {
            const wcbs = data.wcbs || [];
            
            if (wcbs.length === 0) {
                alert('Chưa có WCB nào trong hệ thống!');
                return;
            }
            
            const modalHTML = `
                <div id="orchidModal" class="assign-modal">
                    <div class="assign-modal-content">
                        <div class="assign-modal-header">
                            <h2><i class="fas fa-layer-group"></i> Chế độ Orchid</h2>
                            <button class="modal-close" onclick="closeOrchidModal()">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        
                        <div class="assign-modal-body">
                            <div class="orchid-info">
                                <div class="info-box info-purple">
                                    <i class="fas fa-info-circle"></i>
                                    <div>
                                        <strong>Chế độ Orchid</strong>
                                        <p>Gán 1 WCB cho 6 TV: Basement, Chrysan, Jasmine, Lotus, FO 1, FO 2</p>
                                        <p style="color: #ef4444; margin-top: 5px;">
                                            <i class="fas fa-exclamation-triangle"></i> 
                                            TV Restaurant sẽ không bị ảnh hưởng
                                        </p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="wcb-selection-orchid">
                                <h4><i class="fas fa-image"></i> Chọn WCB để gán:</h4>
                                <div class="wcb-selection-grid">
                                    ${wcbs.filter(w => w.status === 'active').map(wcb => `
                                        <label class="wcb-radio">
                                            <input type="radio" name="orchid-wcb" value="${wcb.id}" class="wcb-radio-input">
                                            <div class="wcb-select-item">
                                                <div class="wcb-select-preview">
                                                    ${wcb.type === 'image' ? `
                                                        <img src="${escapeHtml(wcb.file_path)}" alt="${escapeHtml(wcb.name)}" 
                                                             onerror="this.src='assets/img/no-image.png'">
                                                    ` : `
                                                        <div class="video-preview-grid">
                                                            <i class="fas fa-play-circle"></i>
                                                        </div>
                                                    `}
                                                </div>
                                                <div class="wcb-select-info">
                                                    <strong>${escapeHtml(wcb.name)}</strong>
                                                    <span><i class="fas fa-${wcb.type === 'image' ? 'image' : 'video'}"></i> ${wcb.type === 'image' ? 'Hình ảnh' : 'Video'}</span>
                                                </div>
                                            </div>
                                        </label>
                                    `).join('')}
                                </div>
                            </div>
                        </div>
                        
                        <div class="assign-modal-footer">
                            <button class="btn-cancel" onclick="closeOrchidModal()">
                                <i class="fas fa-times"></i> Hủy
                            </button>
                            <button class="btn-assign btn-orchid-confirm" onclick="confirmOrchidMode()">
                                <i class="fas fa-check"></i> Áp dụng chế độ Orchid
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            document.body.insertAdjacentHTML('beforeend', modalHTML);
            document.body.style.overflow = 'hidden';
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Có lỗi khi tải dữ liệu!');
        });
}

// Close Orchid Modal
function closeOrchidModal() {
    const modal = document.getElementById('orchidModal');
    if (modal) {
        modal.remove();
    }
    document.body.style.overflow = '';
}

// Confirm Orchid Mode
function confirmOrchidMode() {
    const selectedWCB = document.querySelector('input[name="orchid-wcb"]:checked');
    
    if (!selectedWCB) {
        alert('Vui lòng chọn 1 WCB!');
        return;
    }
    
    const mediaId = parseInt(selectedWCB.value);
    
    if (!confirm('Bạn có chắc muốn áp dụng chế độ Orchid?\n\nWCB này sẽ được gán cho 6 TV (trừ Restaurant).')) {
        return;
    }
    
    // Show loading
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang áp dụng...';
    btn.disabled = true;
    
    // Call Orchid Mode API - Bật TV và gán WCB
    fetch('api/orchid-mode.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            media_id: mediaId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage('Đã áp dụng chế độ Orchid thành công!', 'success');
            closeOrchidModal();
            loadTVs();
        } else {
            showMessage('Lỗi: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Có lỗi xảy ra!', 'error');
    })
    .finally(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}

// Toggle TV Status - Quick on/off switch
function toggleTVStatus(tvId, isOnline, tvName) {
    const newStatus = isOnline ? 'online' : 'offline';
    
    // Show loading
    showMessage(`Đang ${isOnline ? 'bật' : 'tắt'} "${tvName}"...`, 'info');
    
    fetch('api/toggle-tv-status.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            tv_id: tvId,
            status: newStatus
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(`Đã ${isOnline ? 'bật' : 'tắt'} "${tvName}" thành công!`, 'success');
            // Reload TV list to update UI
            setTimeout(() => loadTVs(), 500);
        } else {
            showMessage('Lỗi: ' + data.message, 'error');
            // Revert toggle
            loadTVs();
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Có lỗi xảy ra!', 'error');
        // Revert toggle
        loadTVs();
    });
}

// Force Reload TV - Send reload signal to TV
function forceReloadTV(tvId, tvName) {
    if (!confirm(`Bạn có chắc muốn ép tải lại "${tvName}"?\n\nTV sẽ tự động refresh để cập nhật nội dung mới.`)) {
        return;
    }
    
    // Show loading on button
    const btn = event.target.closest('.btn-reload');
    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang tải...';
    btn.disabled = true;
    
    // Send reload signal
    fetch('api/reload-tv.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            tv_id: tvId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(`Đã gửi lệnh tải lại cho "${tvName}"!`, 'success');
            
            // Show countdown
            let countdown = 3;
            const countdownInterval = setInterval(() => {
                btn.innerHTML = `<i class="fas fa-clock"></i> ${countdown}s`;
                countdown--;
                
                if (countdown < 0) {
                    clearInterval(countdownInterval);
                    btn.innerHTML = originalHTML;
                    btn.disabled = false;
                }
            }, 1000);
        } else {
            showMessage('Lỗi: ' + data.message, 'error');
            btn.innerHTML = originalHTML;
            btn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Có lỗi xảy ra khi gửi lệnh tải lại!', 'error');
        btn.innerHTML = originalHTML;
        btn.disabled = false;
    });
}

// Toggle Pause TV - Đưa TV về chế độ chờ mà không gỡ WCB
function togglePauseTV(tvId, tvName) {
    const tv = allTVs.find(t => t.id == tvId);
    if (!tv) {
        alert('Không tìm thấy TV!');
        return;
    }
    
    const isPaused = tv.is_paused;
    const action = isPaused ? 'tiếp tục chiếu' : 'tạm dừng';
    
    if (!confirm(`Bạn có chắc muốn ${action} "${tvName}"?\n\n${isPaused ? 'TV sẽ tiếp tục hiển thị WCB đã gán.' : 'TV sẽ chuyển sang chế độ chờ (hiển thị logo).\nWCB vẫn được giữ nguyên.'}`)) {
        return;
    }
    
    // Show loading
    showMessage(`Đang ${action} "${tvName}"...`, 'info');
    
    fetch('api/toggle-pause-tv.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            tv_id: tvId,
            pause: !isPaused
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(`Đã ${action} "${tvName}" thành công!`, 'success');
            // Reload TV list to update UI
            setTimeout(() => loadTVs(), 500);
        } else {
            showMessage('Lỗi: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Có lỗi xảy ra!', 'error');
    });
}

// Shutdown All TVs - Set offline and unassign all WCBs
function shutdownAllTVs() {
    if (!confirm('⚠️ CẢNH BÁO ⚠️\n\nBạn có chắc muốn TẮT TOÀN BỘ hệ thống?\n\n- Tất cả 7 TV sẽ chuyển sang OFFLINE\n- Tất cả WCB sẽ bị GỠ GÁN\n\nHành động này không thể hoàn tác!')) {
        return;
    }
    
    // Double confirm
    if (!confirm('Xác nhận lần cuối: TẮT TOÀN BỘ TV và GỠ GÁN tất cả WCB?')) {
        return;
    }
    
    // Show loading overlay
    const loadingHTML = `
        <div id="shutdownLoading" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.8); z-index: 9999; display: flex; align-items: center; justify-content: center; flex-direction: column; color: white;">
            <i class="fas fa-power-off fa-3x" style="color: #ef4444; margin-bottom: 20px; animation: pulse 1s infinite;"></i>
            <h2>Đang tắt toàn bộ hệ thống...</h2>
            <p style="margin-top: 10px; opacity: 0.8;">Vui lòng đợi...</p>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', loadingHTML);
    
    // Call API to shutdown all
    fetch('api/shutdown-all.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        const loading = document.getElementById('shutdownLoading');
        if (loading) loading.remove();
        
        if (data.success) {
            showMessage('Đã tắt toàn bộ hệ thống thành công!', 'success');
            loadTVs();
        } else {
            showMessage('Lỗi: ' + data.message, 'error');
        }
    })
    .catch(error => {
        const loading = document.getElementById('shutdownLoading');
        if (loading) loading.remove();
        
        console.error('Error:', error);
        showMessage('Có lỗi xảy ra khi tắt hệ thống!', 'error');
    });
}

// Assign WCB to TV - Open modal to select WCB
function assignWCBToTV(tvId) {
    const tv = allTVs.find(t => t.id == tvId);
    if (!tv) {
        alert('Không tìm thấy TV!');
        return;
    }
    
    // Kiểm tra TV có online không
    if (tv.status !== 'online') {
        if (!confirm(`TV "${tv.name}" đang OFFLINE!\n\nBạn có chắc muốn gán WCB cho TV offline?\n\nLưu ý: TV sẽ không hiển thị nội dung cho đến khi được bật lại.`)) {
            return;
        }
    }
    
    // Load all WCBs and current assignments
    Promise.all([
        fetch('api/get-wcb.php').then(r => r.json()),
        fetch(`api/get-media-assignments.php?tv_id=${tvId}`).then(r => r.json())
    ])
    .then(([wcbData, assignmentsData]) => {
        const wcbs = wcbData.wcbs || [];
        const assignments = assignmentsData.assignments || [];
        const assignedMediaIds = assignments.map(a => a.media_id);
        
        const modalHTML = `
            <div id="assignWCBModal" class="assign-modal">
                <div class="assign-modal-content">
                    <div class="assign-modal-header">
                        <h2><i class="fas fa-plus-circle"></i> Gán WCB cho ${escapeHtml(tv.name)}</h2>
                        <button class="modal-close" onclick="closeAssignWCBModal()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <div class="assign-modal-body">
                        <div class="assign-tv-info">
                            <div class="tv-info-box">
                                <i class="fas fa-tv"></i>
                                <div>
                                    <h3>${escapeHtml(tv.name)}</h3>
                                    <p>${escapeHtml(tv.location)}</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="assign-current">
                            <h4><i class="fas fa-check-circle"></i> WCB đã gán:</h4>
                            ${assignments.length > 0 ? `
                                <div class="current-assignments">
                                    ${assignments.map(a => `
                                        <div class="assignment-item">
                                            <div class="assignment-preview">
                                                ${a.media_type === 'image' ? `
                                                    <img src="${escapeHtml(a.media_file_path)}" alt="${escapeHtml(a.media_name)}" 
                                                         onerror="this.src='assets/img/no-image.png'">
                                                ` : `
                                                    <div class="video-preview-small">
                                                        <i class="fas fa-video"></i>
                                                    </div>
                                                `}
                                            </div>
                                            <div class="assignment-info">
                                                <strong>${escapeHtml(a.media_name)}</strong>
                                                <span><i class="fas fa-${a.media_type === 'image' ? 'image' : 'video'}"></i> ${a.media_type === 'image' ? 'Hình ảnh' : 'Video'}</span>
                                                ${a.is_default ? '<span class="badge-default">Mặc định</span>' : ''}
                                            </div>
                                            <button class="btn-unassign" onclick="unassignMediaFromTV(${a.media_id}, ${tvId}, '${escapeHtml(a.media_name)}')">
                                                <i class="fas fa-times"></i> Hủy
                                            </button>
                                        </div>
                                    `).join('')}
                                </div>
                            ` : '<p class="no-assignments">Chưa gán WCB nào</p>'}
                        </div>
                        
                        <div class="assign-new">
                            <h4><i class="fas fa-plus-circle"></i> Chọn WCB để gán:</h4>
                            <div class="wcb-selection-grid">
                                ${wcbs.filter(w => w.status === 'active').map(wcb => `
                                    <label class="wcb-checkbox ${assignedMediaIds.includes(wcb.id) ? 'disabled' : ''}">
                                        <input type="checkbox" 
                                               value="${wcb.id}" 
                                               ${assignedMediaIds.includes(wcb.id) ? 'disabled checked' : ''}
                                               class="wcb-select">
                                        <div class="wcb-select-item">
                                            <div class="wcb-select-preview">
                                                ${wcb.type === 'image' ? `
                                                    <img src="${escapeHtml(wcb.file_path)}" alt="${escapeHtml(wcb.name)}" 
                                                         onerror="this.src='assets/img/no-image.png'">
                                                ` : `
                                                    <div class="video-preview-grid">
                                                        <i class="fas fa-play-circle"></i>
                                                    </div>
                                                `}
                                            </div>
                                            <div class="wcb-select-info">
                                                <strong>${escapeHtml(wcb.name)}</strong>
                                                <span><i class="fas fa-${wcb.type === 'image' ? 'image' : 'video'}"></i> ${wcb.type === 'image' ? 'Hình ảnh' : 'Video'}</span>
                                            </div>
                                        </div>
                                    </label>
                                `).join('')}
                            </div>
                        </div>
                    </div>
                    
                    <div class="assign-modal-footer">
                        <button class="btn-cancel" onclick="closeAssignWCBModal()">
                            <i class="fas fa-times"></i> Đóng
                        </button>
                        <button class="btn-assign" onclick="confirmAssignWCBToTV(${tvId})">
                            <i class="fas fa-check"></i> Gán WCB đã chọn
                        </button>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        document.body.style.overflow = 'hidden';
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi khi tải dữ liệu!');
    });
}

// Close assign WCB modal
function closeAssignWCBModal() {
    const modal = document.getElementById('assignWCBModal');
    if (modal) {
        modal.remove();
    }
    document.body.style.overflow = '';
}

// Confirm assign WCB to TV
function confirmAssignWCBToTV(tvId) {
    const selectedWCBs = Array.from(document.querySelectorAll('.wcb-select:checked:not(:disabled)'))
        .map(cb => parseInt(cb.value));
    
    if (selectedWCBs.length === 0) {
        alert('Vui lòng chọn ít nhất 1 WCB!');
        return;
    }
    
    // Show loading
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang gán...';
    btn.disabled = true;
    
    // Assign each WCB to this TV
    const promises = selectedWCBs.map(mediaId => {
        return fetch('api/assign-media.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                media_id: mediaId,
                tv_ids: [tvId],
                is_default: 0
            })
        }).then(r => r.json());
    });
    
    Promise.all(promises)
        .then(results => {
            const allSuccess = results.every(r => r.success);
            
            if (allSuccess) {
                showMessage('Gán WCB thành công!', 'success');
                closeAssignWCBModal();
                loadTVs(); // Reload to update
            } else {
                showMessage('Có lỗi xảy ra khi gán một số WCB!', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showMessage('Có lỗi xảy ra khi gán!', 'error');
        })
        .finally(() => {
            btn.innerHTML = originalText;
            btn.disabled = false;
        });
}

// Force Reload All TVs - Send reload signal to all 7 TVs
// Dùng cho TV cũ Samsung/Sony không tự động reload
function forceReloadAllTVs() {
    if (!confirm('Bạn có chắc muốn ép tải lại TẤT CẢ 7 TV?\n\nTất cả TV sẽ tự động refresh để cập nhật nội dung mới.\n\nĐây là tính năng dành cho TV cũ (Samsung/Sony) không tự động reload.')) {
        return;
    }
    
    // Show loading overlay
    const loadingHTML = `
        <div id="reloadAllLoading" style="position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.8); z-index: 9999; display: flex; align-items: center; justify-content: center; flex-direction: column; color: white;">
            <i class="fas fa-sync-alt fa-3x fa-spin" style="color: #3b82f6; margin-bottom: 20px;"></i>
            <h2>Đang gửi lệnh reload cho tất cả TV...</h2>
            <p style="margin-top: 10px; opacity: 0.8;">Vui lòng đợi...</p>
        </div>
    `;
    document.body.insertAdjacentHTML('beforeend', loadingHTML);
    
    // Send reload signal to all TVs
    fetch('api/reload-all-tvs.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        const loading = document.getElementById('reloadAllLoading');
        if (loading) loading.remove();
        
        if (data.success) {
            showMessage(`Đã gửi lệnh tải lại cho ${data.tv_count} TV thành công!`, 'success');
            
            // Show countdown for TV reload
            let countdown = 10;
            const countdownMsg = document.createElement('div');
            countdownMsg.className = 'alert-message alert-info';
            countdownMsg.id = 'reloadCountdown';
            countdownMsg.innerHTML = `
                <i class="fas fa-clock"></i>
                <span>TV sẽ reload trong <strong>${countdown}</strong> giây...</span>
            `;
            const container = document.querySelector('.tv-container');
            container.insertBefore(countdownMsg, container.firstChild);
            
            const countdownInterval = setInterval(() => {
                countdown--;
                const countdownEl = document.getElementById('reloadCountdown');
                if (countdownEl) {
                    countdownEl.querySelector('strong').textContent = countdown;
                }
                
                if (countdown <= 0) {
                    clearInterval(countdownInterval);
                    if (countdownEl) countdownEl.remove();
                    loadTVs(); // Refresh TV list
                }
            }, 1000);
        } else {
            showMessage('Lỗi: ' + data.message, 'error');
        }
    })
    .catch(error => {
        const loading = document.getElementById('reloadAllLoading');
        if (loading) loading.remove();
        
        console.error('Error:', error);
        showMessage('Có lỗi xảy ra khi gửi lệnh tải lại!', 'error');
    });
}

// Unassign media from TV
function unassignMediaFromTV(mediaId, tvId, mediaName) {
    if (!confirm(`Bạn có chắc muốn hủy gán "${mediaName}" khỏi TV này?`)) {
        return;
    }
    
    fetch('api/unassign-media.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            media_id: mediaId,
            tv_id: tvId
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage('Hủy gán thành công!', 'success');
            // Reload modal
            closeAssignWCBModal();
            setTimeout(() => {
                assignWCBToTV(tvId);
            }, 500);
        } else {
            showMessage('Lỗi: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Có lỗi xảy ra khi hủy gán!', 'error');
    });
}
