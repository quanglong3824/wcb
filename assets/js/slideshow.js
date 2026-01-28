/**
 * Slideshow Management JavaScript
 * Quản lý tạo, sửa, xóa slideshow và xử lý upload audio
 */

// Global variables
let selectedImages = [];
let selectedAudio = null;
let editingSlideshowId = null;
let allImages = [];
let lastSelectedId = null; // Track last interaction for shift-click

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadSlideshows();
    initAudioUpload();
    initFormHandlers();
});

/**
 * Load slideshows list
 */
async function loadSlideshows() {
    const grid = document.getElementById('slideshowGrid');
    const statusFilter = document.getElementById('statusFilter').value;
    
    try {
        grid.innerHTML = `
            <div class="loading-state" style="grid-column: 1/-1;">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Đang tải dữ liệu...</p>
            </div>
        `;
        
        const response = await fetch(`api/slideshows.php?action=list&status=${statusFilter}`);
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.message || 'Lỗi khi tải danh sách slideshow');
        }
        
        if (data.slideshows.length === 0) {
            grid.innerHTML = `
                <div class="empty-state" style="grid-column: 1/-1;">
                    <i class="fas fa-images"></i>
                    <p>Chưa có slideshow nào</p>
                    <button class="btn-primary" onclick="openCreateModal()">
                        <i class="fas fa-plus"></i> Tạo Slideshow Đầu Tiên
                    </button>
                </div>
            `;
            return;
        }
        
        grid.innerHTML = data.slideshows.map(slideshow => createSlideshowCard(slideshow)).join('');
        
    } catch (error) {
        console.error('Error loading slideshows:', error);
        grid.innerHTML = `
            <div class="empty-state" style="grid-column: 1/-1;">
                <i class="fas fa-exclamation-triangle"></i>
                <p>Lỗi: ${error.message}</p>
            </div>
        `;
    }
}

/**
 * Create slideshow card HTML
 */
function createSlideshowCard(slideshow) {
    const duration = formatDuration(slideshow.total_duration);
    const audioDuration = slideshow.audio_duration ? formatDuration(slideshow.audio_duration) : '--:--';
    
    return `
        <div class="slideshow-card" onclick="editSlideshow(${slideshow.id})">
            <div class="slideshow-card-header">
                <div class="slideshow-card-title">
                    <h3>${escapeHtml(slideshow.name)}</h3>
                    <p>${slideshow.description || 'Không có mô tả'}</p>
                </div>
                <div class="slideshow-card-actions" onclick="event.stopPropagation()">
                    <button class="btn-icon" onclick="assignToTV(${slideshow.id})" title="Gán cho TV">
                        <i class="fas fa-tv"></i>
                    </button>
                    <button class="btn-icon" onclick="deleteSlideshow(${slideshow.id})" title="Xóa">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
            
            <div class="slideshow-preview">
                <div class="no-preview">
                    <i class="fas fa-images"></i>
                    <p>${slideshow.image_count} ảnh</p>
                </div>
            </div>
            
            <div class="slideshow-info">
                <div class="info-item">
                    <i class="fas fa-images"></i>
                    <span><strong>${slideshow.image_count}</strong> ảnh</span>
                </div>
                <div class="info-item">
                    <i class="fas fa-clock"></i>
                    <span><strong>${duration}</strong></span>
                </div>
                <div class="info-item">
                    <i class="fas fa-music"></i>
                    <span title="${slideshow.audio_name || 'Không có nhạc'}">
                        <strong>${slideshow.audio_name ? '♪ ' + audioDuration : 'Không có'}</strong>
                    </span>
                </div>
            </div>
        </div>
    `;
}

/**
 * Open create modal
 */
function openCreateModal() {
    editingSlideshowId = null;
    selectedImages = [];
    selectedAudio = null;
    
    document.getElementById('modalTitle').textContent = 'Tạo Slideshow Mới';
    document.getElementById('slideshowForm').reset();
    document.getElementById('slideshowId').value = '';
    document.getElementById('audioId').value = '';
    document.getElementById('selectedImages').innerHTML = '';
    document.getElementById('selectedAudio').style.display = 'none';
    document.getElementById('audioUploadArea').style.display = 'block';
    
    updateDurationInfo();
    openModal();
}

/**
 * Edit slideshow
 */
async function editSlideshow(id) {
    try {
        const response = await fetch(`api/slideshows.php?action=get&id=${id}`);
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.message || 'Lỗi khi tải thông tin slideshow');
        }
        
        const slideshow = data.slideshow;
        editingSlideshowId = id;
        selectedImages = slideshow.images.map(img => ({
            id: img.media_id,
            name: img.name,
            file_path: img.file_path,
            slideshow_image_id: img.id
        }));
        
        // Fill form
        document.getElementById('modalTitle').textContent = 'Chỉnh sửa Slideshow';
        document.getElementById('slideshowId').value = slideshow.id;
        document.getElementById('slideshowName').value = slideshow.name;
        document.getElementById('slideshowDescription').value = slideshow.description || '';
        document.getElementById('transitionDuration').value = slideshow.transition_duration;
        document.getElementById('fadeOutDuration').value = slideshow.fade_out_duration;
        
        // Set audio
        if (slideshow.audio_id) {
            selectedAudio = {
                id: slideshow.audio_id,
                name: slideshow.audio_name,
                file_path: slideshow.audio_path,
                duration: slideshow.audio_duration
            };
            document.getElementById('audioId').value = slideshow.audio_id;
            showSelectedAudio();
        } else {
            selectedAudio = null;
            document.getElementById('audioId').value = '';
            document.getElementById('selectedAudio').style.display = 'none';
            document.getElementById('audioUploadArea').style.display = 'block';
        }
        
        // Display images
        displaySelectedImages();
        updateDurationInfo();
        openModal();
        
    } catch (error) {
        console.error('Error loading slideshow:', error);
        alert('Lỗi: ' + error.message);
    }
}

/**
 * Delete slideshow
 */
async function deleteSlideshow(id) {
    if (!confirm('Bạn có chắc chắn muốn xóa slideshow này?')) {
        return;
    }
    
    try {
        const formData = new FormData();
        formData.append('slideshow_id', id);
        
        const response = await fetch('api/slideshows.php?action=delete', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.message || 'Lỗi khi xóa slideshow');
        }
        
        alert('Xóa slideshow thành công!');
        loadSlideshows();
        
    } catch (error) {
        console.error('Error deleting slideshow:', error);
        alert('Lỗi: ' + error.message);
    }
}

/**
 * Initialize audio upload
 */
function initAudioUpload() {
    const uploadArea = document.getElementById('audioUploadArea');
    const fileInput = document.getElementById('audioFileInput');
    
    // Click to select file
    uploadArea.addEventListener('click', () => {
        fileInput.click();
    });
    
    // Drag and drop
    uploadArea.addEventListener('dragover', (e) => {
        e.preventDefault();
        uploadArea.style.borderColor = '#a78bfa';
        uploadArea.style.background = 'rgba(167, 139, 250, 0.1)';
    });
    
    uploadArea.addEventListener('dragleave', () => {
        uploadArea.style.borderColor = 'rgba(167, 139, 250, 0.3)';
        uploadArea.style.background = 'transparent';
    });
    
    uploadArea.addEventListener('drop', async (e) => {
        e.preventDefault();
        uploadArea.style.borderColor = 'rgba(167, 139, 250, 0.3)';
        uploadArea.style.background = 'transparent';
        
        const file = e.dataTransfer.files[0];
        if (file && file.type.startsWith('audio/')) {
            await uploadAudio(file);
        } else {
            alert('Vui lòng chọn file audio hợp lệ');
        }
    });
    
    // File input change
    fileInput.addEventListener('change', async (e) => {
        const file = e.target.files[0];
        if (file) {
            await uploadAudio(file);
        }
    });
}

/**
 * Upload audio file
 */
async function uploadAudio(file) {
    try {
        const formData = new FormData();
        formData.append('audio', file);
        formData.append('name', file.name.replace(/\.[^/.]+$/, ''));
        
        // Show loading
        const uploadArea = document.getElementById('audioUploadArea');
        uploadArea.innerHTML = `
            <i class="fas fa-spinner fa-spin"></i>
            <p>Đang upload...</p>
        `;
        
        const response = await fetch('api/upload-audio.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.message || 'Lỗi khi upload audio');
        }
        
        selectedAudio = data.media;
        document.getElementById('audioId').value = data.media.id;
        showSelectedAudio();
        updateDurationInfo();
        
    } catch (error) {
        console.error('Error uploading audio:', error);
        alert('Lỗi: ' + error.message);
        
        // Reset upload area
        const uploadArea = document.getElementById('audioUploadArea');
        uploadArea.innerHTML = `
            <i class="fas fa-music"></i>
            <p>Kéo thả file nhạc hoặc click để chọn</p>
            <small>Hỗ trợ: MP3, WAV, OGG, M4A (tối đa 50MB)</small>
        `;
    }
}

/**
 * Show selected audio
 */
function showSelectedAudio() {
    const selectedAudioDiv = document.getElementById('selectedAudio');
    const uploadArea = document.getElementById('audioUploadArea');
    
    if (selectedAudio) {
        const duration = selectedAudio.duration ? formatDuration(selectedAudio.duration) : 'Không xác định';
        
        selectedAudioDiv.querySelector('.audio-name').textContent = selectedAudio.name;
        selectedAudioDiv.querySelector('.audio-duration').textContent = `Thời lượng: ${duration}`;
        selectedAudioDiv.style.display = 'flex';
        uploadArea.style.display = 'none';
    }
}

/**
 * Remove audio
 */
function removeAudio() {
    selectedAudio = null;
    document.getElementById('audioId').value = '';
    document.getElementById('selectedAudio').style.display = 'none';
    document.getElementById('audioUploadArea').style.display = 'block';
    
    // Reset upload area
    const uploadArea = document.getElementById('audioUploadArea');
    uploadArea.innerHTML = `
        <i class="fas fa-music"></i>
        <p>Kéo thả file nhạc hoặc click để chọn</p>
        <small>Hỗ trợ: MP3, WAV, OGG, M4A (tối đa 50MB)</small>
    `;
    
    updateDurationInfo();
}

/**
 * Open image selector
 */
async function openImageSelector() {
    const modal = document.getElementById('imageSelectorModal');
    const grid = document.getElementById('imageLibraryGrid');
    
    modal.classList.add('active');
    
    // Prevent body scroll when modal is open
    document.body.style.overflow = 'hidden';
    
    try {
        // Load images from media library - sửa endpoint
        const response = await fetch('api/media.php?type=image&status=active&limit=100');
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.message || 'Lỗi khi tải danh sách ảnh');
        }
        
        allImages = data.media || [];
        
        if (allImages.length === 0) {
            grid.innerHTML = `
                <div class="loading-state">
                    <i class="fas fa-images"></i>
                    <p>Chưa có ảnh nào trong thư viện</p>
                </div>
            `;
            return;
        }
        
        grid.innerHTML = allImages.map(img => {
            const isSelected = selectedImages.some(si => si.id === img.id);
            return `
                <div class="library-image-item ${isSelected ? 'selected' : ''}" 
                     data-id="${img.id}" 
                     onclick="toggleImageSelection(${img.id}, event)">
                    <img src="${img.file_path}" alt="${escapeHtml(img.name)}">
                    <div class="check-icon">
                        <i class="fas fa-check"></i>
                    </div>
                </div>
            `;
        }).join('');
        
        updateSelectedImageCount();
        
    } catch (error) {
        console.error('Error loading images:', error);
        grid.innerHTML = `
            <div class="loading-state">
                <i class="fas fa-exclamation-triangle"></i>
                <p>Lỗi: ${error.message}</p>
            </div>
        `;
    }
}

/**
 * Toggle image selection with Shift Click support
 */
function toggleImageSelection(imageId, event) {
    // Check if shift key is pressed and we have a last selected id
    if (event && event.shiftKey && lastSelectedId !== null && lastSelectedId !== imageId) {
        const currentIndex = allImages.findIndex(img => img.id === imageId);
        const lastIndex = allImages.findIndex(img => img.id === lastSelectedId);
        
        if (currentIndex !== -1 && lastIndex !== -1) {
            const start = Math.min(currentIndex, lastIndex);
            const end = Math.max(currentIndex, lastIndex);
            
            // Loop through range
            for (let i = start; i <= end; i++) {
                const img = allImages[i];
                const isSelected = selectedImages.some(si => si.id === img.id);
                
                // Select if not already selected
                if (!isSelected) {
                    selectedImages.push({
                        id: img.id,
                        name: img.name,
                        file_path: img.file_path
                    });
                    
                    const item = document.querySelector(`.library-image-item[data-id="${img.id}"]`);
                    if (item) item.classList.add('selected');
                }
            }
        }
    } else {
        // Normal click behavior
        const item = document.querySelector(`.library-image-item[data-id="${imageId}"]`);
        const index = selectedImages.findIndex(img => img.id === imageId);
        
        if (index > -1) {
            // Deselect
            selectedImages.splice(index, 1);
            item.classList.remove('selected');
        } else {
            // Select
            const image = allImages.find(img => img.id === imageId);
            if (image) {
                selectedImages.push({
                    id: image.id,
                    name: image.name,
                    file_path: image.file_path
                });
                item.classList.add('selected');
            }
        }
    }
    
    // Update last selected id
    lastSelectedId = imageId;
    
    updateSelectedImageCount();
}

/**
 * Update selected image count
 */
function updateSelectedImageCount() {
    document.getElementById('selectedImageCount').textContent = selectedImages.length;
}

/**
 * Confirm image selection
 */
function confirmImageSelection() {
    displaySelectedImages();
    updateDurationInfo();
    closeImageSelector();
}

/**
 * Close image selector
 */
function closeImageSelector() {
    document.getElementById('imageSelectorModal').classList.remove('active');
    // Restore body scroll
    document.body.style.overflow = '';
}

/**
 * Display selected images
 */
function displaySelectedImages() {
    const container = document.getElementById('selectedImages');
    
    if (selectedImages.length === 0) {
        container.innerHTML = '';
        return;
    }
    
    container.innerHTML = selectedImages.map((img, index) => `
        <div class="selected-image-item" draggable="true" data-id="${img.id}" data-index="${index}">
            <img src="${img.file_path}" alt="${escapeHtml(img.name)}">
            <button class="remove-btn" onclick="removeImage(${index})" type="button">
                <i class="fas fa-times"></i>
            </button>
            <div class="order-number">${index + 1}</div>
        </div>
    `).join('');
    
    initDragAndDrop();
}

/**
 * Remove image from selection
 */
function removeImage(index) {
    selectedImages.splice(index, 1);
    displaySelectedImages();
    updateDurationInfo();
}

/**
 * Initialize drag and drop for reordering
 */
function initDragAndDrop() {
    const items = document.querySelectorAll('.selected-image-item');
    let draggedItem = null;
    
    items.forEach(item => {
        item.addEventListener('dragstart', function(e) {
            draggedItem = this;
            setTimeout(() => this.style.opacity = '0.5', 0);
        });
        
        item.addEventListener('dragend', function() {
            setTimeout(() => this.style.opacity = '1', 0);
            draggedItem = null;
        });
        
        item.addEventListener('dragover', function(e) {
            e.preventDefault();
        });
        
        item.addEventListener('drop', function(e) {
            e.preventDefault();
            if (this !== draggedItem) {
                const fromIndex = parseInt(draggedItem.dataset.index);
                const toIndex = parseInt(this.dataset.index);
                
                // Swap items
                const temp = selectedImages[fromIndex];
                selectedImages[fromIndex] = selectedImages[toIndex];
                selectedImages[toIndex] = temp;
                
                displaySelectedImages();
            }
        });
    });
}

/**
 * Update duration info
 */
function updateDurationInfo() {
    const transitionDuration = parseInt(document.getElementById('transitionDuration').value) || 5;
    const imageCount = selectedImages.length;
    const totalDuration = imageCount * transitionDuration;
    
    document.getElementById('imageCount').textContent = imageCount;
    document.getElementById('totalDuration').textContent = formatDuration(totalDuration);
    
    if (selectedAudio && selectedAudio.duration) {
        document.getElementById('audioDuration').textContent = formatDuration(selectedAudio.duration);
    } else {
        document.getElementById('audioDuration').textContent = '--:--';
    }
}

/**
 * Initialize form handlers
 */
function initFormHandlers() {
    const form = document.getElementById('slideshowForm');
    const transitionDuration = document.getElementById('transitionDuration');
    
    // Update duration when transition time changes
    transitionDuration.addEventListener('change', updateDurationInfo);
    
    // Form submission
    form.addEventListener('submit', handleFormSubmit);
}

/**
 * Handle form submit
 */
async function handleFormSubmit(e) {
    e.preventDefault();
    
    if (selectedImages.length === 0) {
        alert('Vui lòng chọn ít nhất 1 ảnh!');
        return;
    }
    
    const formData = new FormData(e.target);
    
    // Add image IDs
    formData.append('image_ids', JSON.stringify(selectedImages.map(img => img.id)));
    
    try {
        const action = editingSlideshowId ? 'update' : 'create';
        const response = await fetch(`api/slideshows.php?action=${action}`, {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (!data.success) {
            throw new Error(data.message || `Lỗi khi ${action === 'create' ? 'tạo' : 'cập nhật'} slideshow`);
        }
        
        alert(`${action === 'create' ? 'Tạo' : 'Cập nhật'} slideshow thành công!`);
        closeModal();
        loadSlideshows();
        
    } catch (error) {
        console.error('Error saving slideshow:', error);
        alert('Lỗi: ' + error.message);
    }
}

/**
 * Filter slideshows
 */
function filterSlideshows() {
    loadSlideshows();
}

/**
 * Assign slideshow to TV
 */
function assignToTV(slideshowId) {
    // TODO: Implement assign to TV functionality
    alert('Tính năng gán slideshow cho TV sẽ được triển khai sau');
}

/**
 * Open modal
 */
function openModal() {
    document.getElementById('slideshowModal').classList.add('active');
    // Prevent body scroll
    document.body.style.overflow = 'hidden';
}

/**
 * Close modal
 */
function closeModal() {
    document.getElementById('slideshowModal').classList.remove('active');
    // Restore body scroll
    document.body.style.overflow = '';
}

/**
 * Format duration to MM:SS
 */
function formatDuration(seconds) {
    if (!seconds || seconds <= 0) return '0:00';
    
    const mins = Math.floor(seconds / 60);
    const secs = seconds % 60;
    return `${mins}:${secs.toString().padStart(2, '0')}`;
}

/**
 * Escape HTML
 */
function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}
