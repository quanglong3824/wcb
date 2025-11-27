// Manage WCB JavaScript

let wcbData = [];

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    loadWCBList();
});

// Load WCB list
function loadWCBList() {
    fetch('api/get-wcb.php')
        .then(response => response.json())
        .then(data => {
            wcbData = data.wcbs || [];
            displayWCBList(wcbData);
        })
        .catch(error => {
            console.error('Error loading WCB:', error);
            displayWCBList([]);
        });
}

// Display WCB list
function displayWCBList(wcbs) {
    const tbody = document.getElementById('wcbTableBody');
    if (!tbody) return;
    
    if (wcbs.length === 0) {
        tbody.innerHTML = `
            <tr>
                <td colspan="7" style="text-align: center; padding: 40px; color: #999;">
                    <i class="fas fa-inbox" style="font-size: 3em; display: block; margin-bottom: 15px;"></i>
                    Chưa có nội dung WCB nào
                </td>
            </tr>
        `;
        return;
    }
    
    tbody.innerHTML = wcbs.map(wcb => {
        // Determine preview source
        const previewSrc = wcb.type === 'image' ? wcb.file_path : (wcb.thumbnail_path || 'assets/img/video-placeholder.png');
        
        return `
        <tr>
            <td>${wcb.id}</td>
            <td>
                <div class="wcb-preview-container" onclick="previewWCB(${wcb.id})" title="Click để xem">
                    ${wcb.type === 'image' ? `
                        <img src="${escapeHtml(previewSrc)}" alt="${escapeHtml(wcb.name)}" class="wcb-preview" 
                             onerror="this.src='assets/img/no-image.png'">
                    ` : `
                        <div class="wcb-preview video-preview">
                            <i class="fas fa-play-circle"></i>
                            <span>Video</span>
                        </div>
                    `}
                </div>
            </td>
            <td>
                <div class="wcb-name-cell">
                    <strong>${escapeHtml(wcb.name)}</strong>
                    ${wcb.description ? `<small>${escapeHtml(wcb.description.substring(0, 50))}${wcb.description.length > 50 ? '...' : ''}</small>` : ''}
                </div>
            </td>
            <td>
                <span class="wcb-type-badge ${wcb.type}">
                    <i class="fas fa-${wcb.type === 'image' ? 'image' : 'video'}"></i>
                    ${wcb.type === 'image' ? 'Hình ảnh' : 'Video'}
                </span>
            </td>
            <td>${wcb.assigned_to_tvs || '<span style="color: #999;">Chưa gán</span>'}</td>
            <td>
                <span class="wcb-status ${wcb.status}">
                    ${wcb.status === 'active' ? 'Đang sử dụng' : 'Không sử dụng'}
                </span>
            </td>
            <td>
                <div class="wcb-actions">
                    <button class="btn-view" onclick="event.stopPropagation(); previewWCB(${wcb.id})" title="Xem">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn-edit" onclick="event.stopPropagation(); editWCBName(${wcb.id})" title="Đổi tên">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn-assign" onclick="event.stopPropagation(); assignWCB(${wcb.id})" title="Gán">
                        <i class="fas fa-tv"></i>
                    </button>
                    <button class="btn-delete-wcb" onclick="event.stopPropagation(); deleteWCB(${wcb.id})" title="Xóa">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
        `;
    }).join('');
}

// Open add WCB modal
function openAddWCBModal() {
    const modal = document.getElementById('wcbModal');
    const form = document.getElementById('wcbForm');
    
    if (modal && form) {
        // Reset form first
        form.reset();
        
        // Clear hidden fields
        document.getElementById('wcbId').value = '';
        document.getElementById('modalTitle').textContent = 'Thêm WCB mới';
        
        // Clear file input explicitly
        const fileInput = form.querySelector('input[type="file"]');
        if (fileInput) {
            fileInput.value = '';
        }
        
        // Show modal
        modal.classList.add('active');
    }
}

// Close modal
function closeModal() {
    const modal = document.getElementById('wcbModal');
    if (modal) {
        modal.classList.remove('active');
        
        // Reset form
        const form = document.getElementById('wcbForm');
        if (form) {
            form.reset();
        }
    }
}

// Show notification
function showNotification(message, type = 'info') {
    // Remove existing notifications
    const existing = document.querySelector('.notification');
    if (existing) {
        existing.remove();
    }
    
    // Create notification
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        <span>${message}</span>
    `;
    
    document.body.appendChild(notification);
    
    // Show notification
    setTimeout(() => {
        notification.classList.add('show');
    }, 10);
    
    // Auto hide after 3 seconds
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// Edit WCB Name - Only allow editing name and description
function editWCBName(id) {
    const wcb = wcbData.find(w => w.id == id);
    if (!wcb) {
        alert('Không tìm thấy WCB!');
        return;
    }
    
    // Create edit name modal
    const modalHTML = `
        <div id="editNameModal" class="edit-name-modal">
            <div class="edit-name-content">
                <div class="edit-name-header">
                    <h2><i class="fas fa-edit"></i> Chỉnh sửa thông tin WCB</h2>
                    <button class="modal-close" onclick="closeEditNameModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="edit-name-body">
                    <div class="wcb-preview-info">
                        ${wcb.type === 'image' ? `
                            <img src="${escapeHtml(wcb.file_path)}" alt="${escapeHtml(wcb.name)}" class="preview-thumb">
                        ` : `
                            <div class="preview-thumb video">
                                <i class="fas fa-video"></i>
                            </div>
                        `}
                        <div class="preview-meta">
                            <span class="type-badge ${wcb.type}">
                                <i class="fas fa-${wcb.type === 'image' ? 'image' : 'video'}"></i>
                                ${wcb.type === 'image' ? 'Hình ảnh' : 'Video'}
                            </span>
                            <span class="file-size">${wcb.file_size_formatted || ''}</span>
                        </div>
                    </div>
                    
                    <form id="editNameForm" onsubmit="saveWCBName(event, ${wcb.id})">
                        <div class="form-group">
                            <label for="editWcbName">Tên WCB *</label>
                            <input type="text" 
                                   id="editWcbName" 
                                   value="${escapeHtml(wcb.name)}" 
                                   required 
                                   placeholder="Nhập tên WCB">
                        </div>
                        
                        <div class="form-group">
                            <label for="editWcbDescription">Mô tả</label>
                            <textarea id="editWcbDescription" 
                                      rows="3" 
                                      placeholder="Nhập mô tả cho WCB...">${escapeHtml(wcb.description || '')}</textarea>
                        </div>
                        
                        <div class="form-note">
                            <i class="fas fa-info-circle"></i>
                            <span>Lưu ý: Chỉ có thể chỉnh sửa tên và mô tả. Không thể thay đổi file.</span>
                        </div>
                        
                        <div class="form-actions">
                            <button type="button" class="btn-cancel" onclick="closeEditNameModal()">
                                <i class="fas fa-times"></i> Hủy
                            </button>
                            <button type="submit" class="btn-save">
                                <i class="fas fa-save"></i> Lưu thay đổi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    document.body.style.overflow = 'hidden';
}

// Close edit name modal
function closeEditNameModal() {
    const modal = document.getElementById('editNameModal');
    if (modal) {
        modal.remove();
    }
    document.body.style.overflow = '';
}

// Save WCB name
function saveWCBName(event, wcbId) {
    event.preventDefault();
    
    const name = document.getElementById('editWcbName').value.trim();
    const description = document.getElementById('editWcbDescription').value.trim();
    
    if (!name) {
        alert('Vui lòng nhập tên WCB!');
        return;
    }
    
    // Show loading
    const submitBtn = event.target.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang lưu...';
    submitBtn.disabled = true;
    
    // Send update request
    fetch('api/update-media-name.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            id: wcbId,
            name: name,
            description: description
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification('Cập nhật thành công!', 'success');
            closeEditNameModal();
            loadWCBList(); // Reload list
        } else {
            showNotification('Lỗi: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Có lỗi xảy ra khi cập nhật!', 'error');
    })
    .finally(() => {
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    });
}

// Save WCB
function saveWCB(event) {
    event.preventDefault();
    event.stopPropagation();
    
    const form = event.target;
    const submitBtn = form.querySelector('button[type="submit"]');
    const fileInput = form.querySelector('input[type="file"]');
    
    // Validate file input for new WCB
    const id = form.querySelector('#wcbId').value;
    if (!id && (!fileInput.files || fileInput.files.length === 0)) {
        alert('Vui lòng chọn file!');
        return false;
    }
    
    // Disable submit button to prevent double submission
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang lưu...';
    
    const formData = new FormData(form);
    const url = id ? 'api/update-wcb.php' : 'api/create-wcb.php';
    
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            closeModal();
            loadWCBList();
            showNotification('Lưu thành công!', 'success');
            
            // Reset form
            form.reset();
        } else {
            showNotification('Lỗi: ' + (data.message || 'Không thể lưu'), 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Có lỗi xảy ra khi lưu: ' + error.message, 'error');
    })
    .finally(() => {
        // Re-enable submit button
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-save"></i> Lưu';
    });
    
    return false;
}

// Delete WCB
function deleteWCB(id) {
    if (!confirm('Bạn có chắc chắn muốn xóa WCB này?')) return;
    
    fetch('api/delete-wcb.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id: id })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadWCBList();
            alert('Xóa thành công!');
        } else {
            alert('Lỗi: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi xóa');
    });
}

// Assign WCB to TV
function assignWCB(id) {
    const wcb = wcbData.find(w => w.id == id);
    if (!wcb) {
        alert('Không tìm thấy WCB!');
        return;
    }
    
    showAssignModal(wcb);
}

// Show assign modal
function showAssignModal(wcb) {
    console.log('Opening assign modal for WCB:', wcb);
    
    // Load TVs and current assignments
    Promise.all([
        fetch('api/get-tvs.php').then(r => r.json()),
        fetch(`api/get-media-assignments.php?media_id=${wcb.id}`).then(r => r.json())
    ])
    .then(([tvsData, assignmentsData]) => {
        console.log('TVs data:', tvsData);
        console.log('Assignments data:', assignmentsData);
        
        const tvs = tvsData.tvs || [];
        const assignments = assignmentsData.assignments || [];
        const assignedTVIds = assignments.map(a => a.tv_id);
        
        console.log('Assigned TV IDs:', assignedTVIds);
        
        const modalHTML = `
            <div id="assignModal" class="assign-modal">
                <div class="assign-modal-content">
                    <div class="assign-modal-header">
                        <h2><i class="fas fa-tv"></i> Gán WCB cho TV</h2>
                        <button class="modal-close" onclick="closeAssignModal()">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                    
                    <div class="assign-modal-body">
                        <div class="assign-wcb-info">
                            <h3>${escapeHtml(wcb.name)}</h3>
                            <p><i class="fas fa-${wcb.type === 'image' ? 'image' : 'video'}"></i> ${wcb.type.toUpperCase()}</p>
                        </div>
                        
                        <div class="assign-current">
                            <h4><i class="fas fa-check-circle"></i> Đang gán cho:</h4>
                            ${assignments.length > 0 ? `
                                <div class="current-assignments">
                                    ${assignments.map(a => `
                                        <div class="assignment-item">
                                            <div class="assignment-info">
                                                <strong>${escapeHtml(a.tv_name)}</strong>
                                                <span>${escapeHtml(a.tv_location)}</span>
                                                ${a.is_default ? '<span class="badge-default">Mặc định</span>' : ''}
                                            </div>
                                            <button class="btn-unassign" onclick="unassignMedia(${wcb.id}, ${a.tv_id}, '${escapeHtml(a.tv_name)}')">
                                                <i class="fas fa-times"></i> Hủy gán
                                            </button>
                                        </div>
                                    `).join('')}
                                </div>
                            ` : '<p class="no-assignments">Chưa gán cho TV nào</p>'}
                        </div>
                        
                        <div class="assign-new">
                            <h4><i class="fas fa-plus-circle"></i> Gán cho TV mới:</h4>
                            <div class="tv-list">
                                ${tvs.map(tv => {
                                    const isAssigned = assignedTVIds.includes(tv.id);
                                    const isOffline = tv.status !== 'online';
                                    const isDisabled = isAssigned || isOffline;
                                    
                                    return `
                                    <label class="tv-checkbox ${isDisabled ? 'disabled' : ''} ${isOffline ? 'offline' : ''}">
                                        <input type="checkbox" 
                                               value="${tv.id}" 
                                               ${isDisabled ? 'disabled' : ''}
                                               ${isAssigned ? 'checked' : ''}
                                               class="tv-select">
                                        <div class="tv-item">
                                            <div class="tv-item-info">
                                                <strong>${escapeHtml(tv.name)}</strong>
                                                <span>${escapeHtml(tv.location)}</span>
                                                ${isOffline ? '<span class="warning-badge"><i class="fas fa-exclamation-triangle"></i> Offline</span>' : ''}
                                            </div>
                                            <span class="tv-status ${tv.status}">${tv.status === 'online' ? 'Online' : 'Offline'}</span>
                                        </div>
                                    </label>
                                `}).join('')}
                            </div>
                            ${tvs.filter(tv => tv.status !== 'online' && !assignedTVIds.includes(tv.id)).length > 0 ? `
                                <div class="offline-warning">
                                    <i class="fas fa-info-circle"></i>
                                    <span>Chỉ có thể gán cho TV đang Online</span>
                                </div>
                            ` : ''}
                        </div>
                        </div>
                    </div>
                    
                    <div class="assign-modal-footer">
                        <button class="btn-cancel" onclick="closeAssignModal()">
                            <i class="fas fa-times"></i> Đóng
                        </button>
                        <button class="btn-assign" onclick="confirmAssign(${wcb.id})">
                            <i class="fas fa-check"></i> Gán cho TV đã chọn
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

// Close assign modal
function closeAssignModal() {
    const modal = document.getElementById('assignModal');
    if (modal) {
        modal.remove();
    }
    document.body.style.overflow = '';
}

// Confirm assign
function confirmAssign(mediaId) {
    const selectedTVs = Array.from(document.querySelectorAll('.tv-select:checked:not(:disabled)'))
        .map(cb => parseInt(cb.value));
    
    console.log('Selected TVs:', selectedTVs);
    
    if (selectedTVs.length === 0) {
        alert('Vui lòng chọn ít nhất 1 TV!');
        return;
    }
    
    const isDefault = 0; // Không dùng default nữa
    
    console.log('Is default:', isDefault);
    
    // Show loading
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang gán...';
    btn.disabled = true;
    
    const payload = {
        media_id: mediaId,
        tv_ids: selectedTVs,
        is_default: isDefault
    };
    
    console.log('Sending payload:', payload);
    
    fetch('api/assign-media.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(payload)
    })
    .then(response => {
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        console.log('Response data:', data);
        
        if (data.success) {
            showNotification(data.message, 'success');
            closeAssignModal();
            loadWCBList(); // Reload to update assigned info
        } else {
            showNotification('Lỗi: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Có lỗi xảy ra khi gán!', 'error');
    })
    .finally(() => {
        btn.innerHTML = originalText;
        btn.disabled = false;
    });
}

// Unassign media from TV
function unassignMedia(mediaId, tvId, tvName) {
    if (!confirm(`Bạn có chắc muốn hủy gán khỏi TV "${tvName}"?`)) {
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
            showNotification(data.message, 'success');
            // Reload modal
            closeAssignModal();
            setTimeout(() => {
                const wcb = wcbData.find(w => w.id == mediaId);
                if (wcb) showAssignModal(wcb);
            }, 500);
        } else {
            showNotification('Lỗi: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('Có lỗi xảy ra khi hủy gán!', 'error');
    });
}

// Preview WCB - Open in new tab
function previewWCB(id) {
    const wcb = wcbData.find(w => w.id == id);
    if (!wcb) {
        alert('Không tìm thấy WCB!');
        return;
    }
    
    // Open file in new tab
    if (wcb.file_path) {
        window.open(wcb.file_path, '_blank');
    } else {
        alert('Không tìm thấy file!');
    }
}

// Search WCB
function searchWCB(query) {
    const filtered = wcbData.filter(wcb => 
        wcb.name.toLowerCase().includes(query.toLowerCase()) ||
        wcb.description.toLowerCase().includes(query.toLowerCase())
    );
    displayWCBList(filtered);
}

// Filter by type
function filterByType(type) {
    if (type === 'all') {
        displayWCBList(wcbData);
    } else {
        const filtered = wcbData.filter(wcb => wcb.type === type);
        displayWCBList(filtered);
    }
}

// Filter by status
function filterByStatus(status) {
    if (status === 'all') {
        displayWCBList(wcbData);
    } else {
        const filtered = wcbData.filter(wcb => wcb.status === status);
        displayWCBList(filtered);
    }
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('wcbModal');
    if (event.target === modal) {
        closeModal();
    }
}


// Handle file select
function handleFileSelect(input) {
    const filePreview = document.getElementById('filePreview');
    const previewImage = document.getElementById('previewImage');
    const fileHint = document.getElementById('fileHint');
    
    if (input.files && input.files[0]) {
        const file = input.files[0];
        const fileSize = (file.size / 1024 / 1024).toFixed(2); // MB
        
        // Update hint
        fileHint.textContent = `Đã chọn: ${file.name} (${fileSize} MB)`;
        fileHint.style.color = '#10b981';
        
        // Show preview for images
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                previewImage.src = e.target.result;
                filePreview.style.display = 'block';
            };
            reader.readAsDataURL(file);
        } else {
            filePreview.style.display = 'none';
        }
    } else {
        fileHint.textContent = 'Chọn file hình ảnh hoặc video';
        fileHint.style.color = '#999';
        filePreview.style.display = 'none';
    }
}


// Escape HTML to prevent XSS
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
