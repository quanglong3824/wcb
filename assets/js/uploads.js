/**
 * Uploads Page JavaScript
 */

let allFiles = [];

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initializeUpload();
    loadFiles();
});

// Initialize upload functionality
function initializeUpload() {
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('fileInput');
    const uploadBtn = document.querySelector('.upload-btn');
    
    // Click to select files - ONLY on button
    uploadBtn.addEventListener('click', (e) => {
        e.preventDefault();
        e.stopPropagation();
        fileInput.click();
    });
    
    // Remove dropzone click to prevent double trigger
    // Only allow drag & drop, not click on dropzone
    
    // File input change
    fileInput.addEventListener('change', handleFiles);
    
    // Drag and drop
    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzone.classList.add('dragover');
    });
    
    dropzone.addEventListener('dragleave', () => {
        dropzone.classList.remove('dragover');
    });
    
    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        e.stopPropagation();
        dropzone.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleFiles({ target: { files } });
        }
    });
}

// Handle selected files
function handleFiles(e) {
    const files = Array.from(e.target.files);
    
    if (files.length === 0) return;
    
    // Prevent multiple calls
    if (window.isUploading) {
        console.log('Upload already in progress, skipping...');
        return;
    }
    
    window.isUploading = true;
    
    // Always show confirmation modal (for both single and multiple files)
    showUploadConfirmModal(files);
    
    // Reset input after a short delay
    setTimeout(() => {
        e.target.value = '';
    }, 500);
}

// Show upload confirmation modal
function showUploadConfirmModal(files) {
    // Validate files first
    const validFiles = [];
    const invalidFiles = [];
    
    files.forEach(file => {
        const maxSize = 50 * 1024 * 1024;
        const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'video/mp4', 'video/webm', 'video/avi', 'video/mov', 'video/quicktime'];
        
        if (file.size > maxSize) {
            invalidFiles.push({ file, reason: 'Quá lớn (tối đa 50MB)' });
        } else if (!validTypes.includes(file.type)) {
            invalidFiles.push({ file, reason: 'Định dạng không hỗ trợ' });
        } else {
            validFiles.push(file);
        }
    });
    
    if (validFiles.length === 0) {
        showMessage('Không có file hợp lệ để upload!', 'error');
        window.isUploading = false;
        return;
    }
    
    // Calculate total size
    const totalSize = validFiles.reduce((sum, f) => sum + f.size, 0);
    
    // Create modal HTML
    const modalHTML = `
        <div id="uploadConfirmModal" class="upload-confirm-modal">
            <div class="upload-confirm-content">
                <div class="upload-confirm-header">
                    <h2><i class="fas fa-cloud-upload-alt"></i> Xác nhận upload</h2>
                    <button class="modal-close" onclick="closeUploadConfirmModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="upload-confirm-body">
                    <div class="upload-summary">
                        <div class="summary-item">
                            <i class="fas fa-file"></i>
                            <span><strong>${validFiles.length}</strong> file hợp lệ</span>
                        </div>
                        <div class="summary-item">
                            <i class="fas fa-hdd"></i>
                            <span><strong>${formatFileSize(totalSize)}</strong> tổng dung lượng</span>
                        </div>
                        ${invalidFiles.length > 0 ? `
                            <div class="summary-item error">
                                <i class="fas fa-exclamation-triangle"></i>
                                <span><strong>${invalidFiles.length}</strong> file không hợp lệ (sẽ bỏ qua)</span>
                            </div>
                        ` : ''}
                    </div>
                    
                    ${invalidFiles.length > 0 ? `
                        <div class="invalid-files-list">
                            <h4><i class="fas fa-times-circle"></i> File không hợp lệ:</h4>
                            ${invalidFiles.map(item => `
                                <div class="invalid-file-item">
                                    <span class="file-name">${escapeHtml(item.file.name)}</span>
                                    <span class="file-reason">${item.reason}</span>
                                </div>
                            `).join('')}
                        </div>
                    ` : ''}
                    
                    <div class="upload-preview-list">
                        <h4><i class="fas fa-images"></i> File sẽ được upload:</h4>
                        <div class="preview-grid">
                            ${validFiles.map((file, index) => `
                                <div class="preview-item">
                                    <div class="preview-thumbnail">
                                        ${file.type.startsWith('image/') ? 
                                            `<img src="${URL.createObjectURL(file)}" alt="${file.name}">` : 
                                            `<div class="video-thumb"><i class="fas fa-video"></i></div>`
                                        }
                                    </div>
                                    <div class="preview-info">
                                        <div class="preview-name" title="${escapeHtml(file.name)}">${escapeHtml(file.name)}</div>
                                        <div class="preview-meta">
                                            <span><i class="fas fa-hdd"></i> ${formatFileSize(file.size)}</span>
                                            <span><i class="fas fa-${file.type.startsWith('image/') ? 'image' : 'video'}"></i> ${file.type.split('/')[1].toUpperCase()}</span>
                                        </div>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>
                
                <div class="upload-confirm-footer">
                    <button class="btn-cancel" onclick="closeUploadConfirmModal()">
                        <i class="fas fa-times"></i> Hủy
                    </button>
                    <button class="btn-confirm-upload" onclick="confirmUpload()">
                        <i class="fas fa-cloud-upload-alt"></i> Xác nhận upload ${validFiles.length} file
                    </button>
                </div>
            </div>
        </div>
    `;
    
    // Add modal to page
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Store files in global variable
    window.pendingUploadFiles = validFiles;
    
    // Prevent body scroll
    document.body.style.overflow = 'hidden';
}

// Close upload confirm modal
function closeUploadConfirmModal() {
    const modal = document.getElementById('uploadConfirmModal');
    if (modal) {
        modal.remove();
    }
    document.body.style.overflow = '';
    window.pendingUploadFiles = null;
    window.isUploading = false;
}

// Confirm upload
function confirmUpload() {
    const files = window.pendingUploadFiles;
    if (!files || files.length === 0) {
        closeUploadConfirmModal();
        return;
    }
    
    // Close confirm modal
    closeUploadConfirmModal();
    
    // If multiple files, show batch upload modal for metadata
    if (files.length > 1) {
        showBatchUploadModal(files);
    } else {
        // Single file upload directly
        uploadFile(files[0]);
    }
}

// Show batch upload modal
function showBatchUploadModal(files) {
    // Create modal HTML
    const modalHTML = `
        <div id="batchUploadModal" class="batch-modal">
            <div class="batch-modal-content">
                <div class="batch-modal-header">
                    <h2><i class="fas fa-cloud-upload-alt"></i> Upload ${files.length} files</h2>
                    <button class="modal-close" onclick="closeBatchModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="batch-modal-body">
                    <p class="batch-info">Nhập thông tin cho từng file (tùy chọn):</p>
                    <div class="batch-files-list" id="batchFilesList">
                        ${files.map((file, index) => `
                            <div class="batch-file-item">
                                <div class="batch-file-preview">
                                    ${file.type.startsWith('image/') ? 
                                        `<img src="${URL.createObjectURL(file)}" alt="${file.name}">` : 
                                        `<i class="fas fa-video"></i>`
                                    }
                                </div>
                                <div class="batch-file-info">
                                    <div class="batch-file-original">${escapeHtml(file.name)}</div>
                                    <input type="text" 
                                           class="batch-file-name" 
                                           placeholder="Tên hiển thị (tùy chọn)" 
                                           value="${escapeHtml(file.name.replace(/\.[^/.]+$/, ''))}">
                                    <textarea class="batch-file-desc" 
                                              placeholder="Mô tả (tùy chọn)" 
                                              rows="2"></textarea>
                                </div>
                            </div>
                        `).join('')}
                    </div>
                </div>
                <div class="batch-modal-footer">
                    <button class="btn-cancel" onclick="closeBatchModal()">Hủy</button>
                    <button class="btn-upload-batch" onclick="processBatchUpload()">
                        <i class="fas fa-cloud-upload-alt"></i> Upload ${files.length} files
                    </button>
                </div>
            </div>
        </div>
    `;
    
    // Add modal to page
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    
    // Store files in global variable
    window.batchFiles = files;
    
    // Prevent body scroll
    document.body.style.overflow = 'hidden';
}

// Close batch modal
function closeBatchModal() {
    const modal = document.getElementById('batchUploadModal');
    if (modal) {
        modal.remove();
    }
    document.body.style.overflow = '';
    window.batchFiles = null;
    window.isUploading = false;
}

// Process batch upload
function processBatchUpload() {
    const files = window.batchFiles;
    if (!files) return;
    
    // Get custom names and descriptions
    const fileNames = [];
    const fileDescriptions = [];
    
    document.querySelectorAll('.batch-file-item').forEach((item, index) => {
        const name = item.querySelector('.batch-file-name').value.trim();
        const desc = item.querySelector('.batch-file-desc').value.trim();
        fileNames.push(name);
        fileDescriptions.push(desc);
    });
    
    // Close modal
    closeBatchModal();
    
    // Create progress container
    const progressId = 'batch-progress-' + Date.now();
    const progressHTML = `
        <div class="progress-item" id="${progressId}">
            <div class="progress-info">
                <i class="fas fa-cloud-upload-alt"></i>
                <div class="progress-details">
                    <div class="progress-name">Đang upload ${files.length} files...</div>
                    <div class="progress-size">Tổng: ${formatFileSize(files.reduce((sum, f) => sum + f.size, 0))}</div>
                </div>
            </div>
            <div class="progress-bar-container">
                <div class="progress-bar" style="width: 0%"></div>
            </div>
            <div class="progress-status">Đang xử lý...</div>
        </div>
    `;
    
    const progressContainer = document.getElementById('uploadProgress');
    progressContainer.insertAdjacentHTML('beforeend', progressHTML);
    
    // Prepare form data
    const formData = new FormData();
    
    // Add files with correct array format
    files.forEach((file) => {
        formData.append('files[]', file);
    });
    
    // Add metadata
    formData.append('fileNames', fileNames.join(';'));
    formData.append('fileDescriptions', fileDescriptions.join(';'));
    
    // Debug log
    console.log('Uploading files:', files.length);
    console.log('File names:', fileNames.join(';'));
    console.log('File descriptions:', fileDescriptions.join(';'));
    
    // Upload with progress
    const xhr = new XMLHttpRequest();
    
    xhr.upload.addEventListener('progress', (e) => {
        if (e.lengthComputable) {
            const percent = (e.loaded / e.total) * 100;
            const progressBar = document.querySelector(`#${progressId} .progress-bar`);
            if (progressBar) {
                progressBar.style.width = percent + '%';
            }
        }
    });
    
    xhr.addEventListener('load', () => {
        const progressItem = document.getElementById(progressId);
        
        if (xhr.status === 200) {
            try {
                // Log raw response for debugging
                console.log('Raw response:', xhr.responseText);
                
                const response = JSON.parse(xhr.responseText);
                console.log('Parsed response:', response);
                
                if (response.success) {
                    progressItem.querySelector('.progress-status').innerHTML = 
                        `<i class="fas fa-check-circle" style="color: #10b981;"></i> Upload thành công ${response.uploaded}/${response.total} files`;
                    progressItem.classList.add('success');
                    
                    showMessage(response.message, 'success');
                    
                    // Reload file gallery
                    setTimeout(() => {
                        loadFiles();
                        progressItem.remove();
                        window.isUploading = false;
                    }, 2000);
                } else {
                    progressItem.querySelector('.progress-status').innerHTML = 
                        `<i class="fas fa-exclamation-circle" style="color: #f59e0b;"></i> ${response.message}`;
                    progressItem.classList.add('error');
                    
                    if (response.errors && response.errors.length > 0) {
                        console.error('Upload errors:', response.errors);
                        showMessage('Một số file upload thất bại. Xem console để biết chi tiết.', 'error');
                    }
                    
                    window.isUploading = false;
                }
            } catch (e) {
                console.error('Parse error:', e);
                console.error('Response text:', xhr.responseText);
                progressItem.querySelector('.progress-status').innerHTML = 
                    '<i class="fas fa-times-circle" style="color: #ef4444;"></i> Lỗi parse response - Xem console';
                progressItem.classList.add('error');
                showMessage('Lỗi: ' + e.message + '. Kiểm tra console để xem chi tiết.', 'error');
                window.isUploading = false;
            }
        } else {
            console.error('HTTP Error:', xhr.status, xhr.responseText);
            progressItem.querySelector('.progress-status').innerHTML = 
                `<i class="fas fa-times-circle" style="color: #ef4444;"></i> Lỗi HTTP ${xhr.status}`;
            progressItem.classList.add('error');
            window.isUploading = false;
        }
    });
    
    xhr.addEventListener('error', () => {
        const progressItem = document.getElementById(progressId);
        progressItem.querySelector('.progress-status').innerHTML = 
            '<i class="fas fa-times-circle" style="color: #ef4444;"></i> Lỗi kết nối';
        progressItem.classList.add('error');
        window.isUploading = false;
    });
    
    xhr.open('POST', 'api/upload-batch.php');
    xhr.send(formData);
}

// Upload single file
function uploadFile(file) {
    // Validate file size (50MB)
    const maxSize = 50 * 1024 * 1024;
    if (file.size > maxSize) {
        showMessage('File "' + file.name + '" quá lớn (tối đa 50MB)', 'error');
        return;
    }
    
    // Validate file type
    const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp', 'video/mp4', 'video/webm', 'video/avi', 'video/mov', 'video/quicktime'];
    if (!validTypes.includes(file.type)) {
        showMessage('Định dạng file "' + file.name + '" không được hỗ trợ', 'error');
        return;
    }
    
    // Create progress item
    const progressId = 'progress-' + Date.now();
    const progressHTML = `
        <div class="progress-item" id="${progressId}">
            <div class="progress-info">
                <i class="fas fa-${file.type.startsWith('image/') ? 'image' : 'video'}"></i>
                <div class="progress-details">
                    <div class="progress-name">${escapeHtml(file.name)}</div>
                    <div class="progress-size">${formatFileSize(file.size)}</div>
                </div>
            </div>
            <div class="progress-bar-container">
                <div class="progress-bar" style="width: 0%"></div>
            </div>
            <div class="progress-status">Đang upload...</div>
        </div>
    `;
    
    const progressContainer = document.getElementById('uploadProgress');
    progressContainer.insertAdjacentHTML('beforeend', progressHTML);
    
    // Prepare form data
    const formData = new FormData();
    formData.append('file', file);
    formData.append('fileName', file.name.replace(/\.[^/.]+$/, '')); // Remove extension
    
    // Upload with progress
    const xhr = new XMLHttpRequest();
    
    xhr.upload.addEventListener('progress', (e) => {
        if (e.lengthComputable) {
            const percent = (e.loaded / e.total) * 100;
            const progressBar = document.querySelector(`#${progressId} .progress-bar`);
            if (progressBar) {
                progressBar.style.width = percent + '%';
            }
        }
    });
    
    xhr.addEventListener('load', () => {
        const progressItem = document.getElementById(progressId);
        
        if (xhr.status === 200) {
            try {
                const response = JSON.parse(xhr.responseText);
                
                if (response.success) {
                    // Update progress item
                    progressItem.querySelector('.progress-status').innerHTML = '<i class="fas fa-check-circle" style="color: #10b981;"></i> Hoàn thành';
                    progressItem.classList.add('success');
                    
                    // Show success message
                    showMessage('Upload thành công: ' + file.name, 'success');
                    
                    // Reload file gallery
                    setTimeout(() => {
                        loadFiles();
                        progressItem.remove();
                        window.isUploading = false;
                    }, 2000);
                } else {
                    progressItem.querySelector('.progress-status').innerHTML = '<i class="fas fa-times-circle" style="color: #ef4444;"></i> Lỗi: ' + response.message;
                    progressItem.classList.add('error');
                    window.isUploading = false;
                }
            } catch (e) {
                progressItem.querySelector('.progress-status').innerHTML = '<i class="fas fa-times-circle" style="color: #ef4444;"></i> Lỗi parse response';
                progressItem.classList.add('error');
                window.isUploading = false;
            }
        } else {
            progressItem.querySelector('.progress-status').innerHTML = '<i class="fas fa-times-circle" style="color: #ef4444;"></i> Lỗi upload';
            progressItem.classList.add('error');
            window.isUploading = false;
        }
    });
    
    xhr.addEventListener('error', () => {
        const progressItem = document.getElementById(progressId);
        progressItem.querySelector('.progress-status').innerHTML = '<i class="fas fa-times-circle" style="color: #ef4444;"></i> Lỗi kết nối';
        progressItem.classList.add('error');
        window.isUploading = false;
    });
    
    xhr.open('POST', 'api/upload.php');
    xhr.send(formData);
}

// Load files from database
function loadFiles() {
    fetch('api/get-wcb.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                allFiles = data.wcbs || [];
                displayFiles(allFiles);
            } else {
                showError(data.message || 'Không thể tải dữ liệu');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showError('Lỗi kết nối. Vui lòng thử lại.');
        });
}

// Display files in gallery
function displayFiles(files) {
    const gallery = document.getElementById('fileGallery');
    
    if (!files || files.length === 0) {
        gallery.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-folder-open"></i>
                <p>Chưa có file nào</p>
                <small>Upload file để bắt đầu</small>
            </div>
        `;
        return;
    }
    
    gallery.innerHTML = files.map(file => createFileCard(file)).join('');
}

// Create file card HTML
function createFileCard(file) {
    const isImage = file.type === 'image';
    const isVideo = file.type === 'video';
    
    return `
        <div class="file-card" data-type="${file.type}">
            <div class="file-preview">
                ${isImage ? `
                    <img src="${escapeHtml(file.file_path)}" alt="${escapeHtml(file.name)}" onerror="this.src='assets/img/no-image.png'">
                ` : ''}
                ${isVideo ? `
                    <video src="${escapeHtml(file.file_path)}" muted></video>
                    <div class="video-overlay">
                        <i class="fas fa-play-circle"></i>
                    </div>
                ` : ''}
                <div class="file-type-badge">
                    <i class="fas fa-${isImage ? 'image' : 'video'}"></i>
                    ${file.type.toUpperCase()}
                </div>
            </div>
            <div class="file-info">
                <div class="file-name" title="${escapeHtml(file.name)}">${escapeHtml(file.name)}</div>
                <div class="file-meta">
                    <span><i class="fas fa-hdd"></i> ${file.file_size_formatted}</span>
                    ${file.width && file.height ? `<span><i class="fas fa-expand"></i> ${file.width}x${file.height}</span>` : ''}
                </div>
                ${file.assigned_to_tvs ? `
                    <div class="file-assigned">
                        <i class="fas fa-tv"></i> ${escapeHtml(file.assigned_to_tvs)}
                    </div>
                ` : ''}
                <div class="file-actions">
                    <button class="btn-file-action" onclick="viewFile(${file.id})" title="Xem">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn-file-action" onclick="deleteFile(${file.id}, '${escapeHtml(file.name)}')" title="Xóa">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `;
}

// View file
function viewFile(fileId) {
    const file = allFiles.find(f => f.id == fileId);
    if (file) {
        window.open(file.file_path, '_blank');
    }
}

// Delete file
function deleteFile(fileId, fileName) {
    // Show confirmation modal
    showDeleteConfirmModal(fileId, fileName);
}

// Show delete confirmation modal
function showDeleteConfirmModal(fileId, fileName) {
    const file = allFiles.find(f => f.id == fileId);
    if (!file) {
        showMessage('Không tìm thấy file!', 'error');
        return;
    }
    
    const modalHTML = `
        <div id="deleteConfirmModal" class="delete-confirm-modal">
            <div class="delete-confirm-content">
                <div class="delete-confirm-header">
                    <h2><i class="fas fa-exclamation-triangle"></i> Xác nhận xóa</h2>
                    <button class="modal-close" onclick="closeDeleteConfirmModal()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                
                <div class="delete-confirm-body">
                    <div class="delete-warning">
                        <i class="fas fa-exclamation-circle"></i>
                        <p>Bạn có chắc chắn muốn xóa file này?</p>
                    </div>
                    
                    <div class="delete-file-info">
                        <div class="delete-file-preview">
                            ${file.type === 'image' ? 
                                `<img src="${escapeHtml(file.file_path)}" alt="${escapeHtml(file.name)}">` : 
                                `<div class="video-icon"><i class="fas fa-video"></i></div>`
                            }
                        </div>
                        <div class="delete-file-details">
                            <h3>${escapeHtml(file.name)}</h3>
                            <p><i class="fas fa-${file.type === 'image' ? 'image' : 'video'}"></i> ${file.type.toUpperCase()}</p>
                            <p><i class="fas fa-hdd"></i> ${file.file_size_formatted}</p>
                            ${file.assigned_to_tvs ? `
                                <p class="assigned-warning">
                                    <i class="fas fa-tv"></i> Đang gán cho: ${escapeHtml(file.assigned_to_tvs)}
                                </p>
                            ` : ''}
                        </div>
                    </div>
                    
                    <div class="delete-note">
                        <i class="fas fa-info-circle"></i>
                        <p><strong>Lưu ý:</strong> Hành động này không thể hoàn tác. File sẽ bị xóa vĩnh viễn khỏi hệ thống.</p>
                    </div>
                </div>
                
                <div class="delete-confirm-footer">
                    <button class="btn-cancel" onclick="closeDeleteConfirmModal()">
                        <i class="fas fa-times"></i> Hủy
                    </button>
                    <button class="btn-delete-confirm" onclick="confirmDelete(${fileId})">
                        <i class="fas fa-trash"></i> Xóa vĩnh viễn
                    </button>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', modalHTML);
    document.body.style.overflow = 'hidden';
}

// Close delete confirm modal
function closeDeleteConfirmModal() {
    const modal = document.getElementById('deleteConfirmModal');
    if (modal) {
        modal.remove();
    }
    document.body.style.overflow = '';
}

// Confirm delete
function confirmDelete(fileId) {
    // Close modal
    closeDeleteConfirmModal();
    
    // Show loading message
    showMessage('Đang xóa...', 'info');
    
    // Send delete request
    fetch('api/delete-media.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id: fileId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showMessage(data.message, 'success');
            
            // Reload file gallery
            setTimeout(() => {
                loadFiles();
            }, 1000);
        } else {
            showMessage('Lỗi: ' + data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showMessage('Có lỗi xảy ra khi xóa file!', 'error');
    });
}

// Filter files
function filterFiles(type) {
    // Update active button
    document.querySelectorAll('.filter-btn').forEach(btn => {
        btn.classList.remove('active');
    });
    event.target.closest('.filter-btn').classList.add('active');
    
    // Filter files
    if (type === 'all') {
        displayFiles(allFiles);
    } else {
        const filtered = allFiles.filter(f => f.type === type);
        displayFiles(filtered);
    }
}

// Show message
function showMessage(message, type) {
    const msgDiv = document.createElement('div');
    msgDiv.className = `alert-message alert-${type}`;
    msgDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
        <span>${message}</span>
    `;
    
    const container = document.querySelector('.upload-container');
    container.insertBefore(msgDiv, container.firstChild);
    
    setTimeout(() => {
        msgDiv.style.animation = 'fadeOut 0.3s';
        setTimeout(() => msgDiv.remove(), 300);
    }, 5000);
}

// Show error
function showError(message) {
    const gallery = document.getElementById('fileGallery');
    gallery.innerHTML = `
        <div class="empty-state">
            <i class="fas fa-exclamation-triangle" style="color: #ef4444;"></i>
            <p style="color: #ef4444;">${escapeHtml(message)}</p>
            <button class="btn btn-primary" onclick="loadFiles()" style="margin-top: 15px;">
                <i class="fas fa-sync-alt"></i> Thử lại
            </button>
        </div>
    `;
}

// Format file size
function formatFileSize(bytes) {
    if (bytes >= 1073741824) {
        return (bytes / 1073741824).toFixed(2) + ' GB';
    } else if (bytes >= 1048576) {
        return (bytes / 1048576).toFixed(2) + ' MB';
    } else if (bytes >= 1024) {
        return (bytes / 1024).toFixed(2) + ' KB';
    } else {
        return bytes + ' bytes';
    }
}

// Escape HTML
function escapeHtml(text) {
    if (!text) return '';
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
