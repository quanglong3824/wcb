// Upload Page JavaScript

let selectedFiles = [];

// Initialize upload functionality
document.addEventListener('DOMContentLoaded', function() {
    initDropzone();
    loadFileGallery();
});

// Initialize dropzone
function initDropzone() {
    const dropzone = document.getElementById('dropzone');
    const fileInput = document.getElementById('fileInput');
    
    if (!dropzone || !fileInput) return;
    
    // Click to select files
    dropzone.addEventListener('click', () => fileInput.click());
    
    // Drag and drop events
    dropzone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropzone.classList.add('dragover');
    });
    
    dropzone.addEventListener('dragleave', () => {
        dropzone.classList.remove('dragover');
    });
    
    dropzone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropzone.classList.remove('dragover');
        handleFiles(e.dataTransfer.files);
    });
    
    // File input change
    fileInput.addEventListener('change', (e) => {
        handleFiles(e.target.files);
    });
}

// Handle selected files
function handleFiles(files) {
    selectedFiles = Array.from(files);
    
    if (selectedFiles.length === 0) return;
    
    // Show progress section
    const progressSection = document.querySelector('.upload-progress');
    if (progressSection) {
        progressSection.classList.add('active');
        progressSection.innerHTML = '';
    }
    
    // Upload each file
    selectedFiles.forEach((file, index) => {
        uploadFile(file, index);
    });
}

// Upload single file
function uploadFile(file, index) {
    const formData = new FormData();
    formData.append('file', file);
    
    // Create progress item
    const progressItem = createProgressItem(file.name, index);
    document.querySelector('.upload-progress').appendChild(progressItem);
    
    // Simulate upload (replace with actual AJAX call)
    const xhr = new XMLHttpRequest();
    
    xhr.upload.addEventListener('progress', (e) => {
        if (e.lengthComputable) {
            const percentComplete = (e.loaded / e.total) * 100;
            updateProgress(index, percentComplete);
        }
    });
    
    xhr.addEventListener('load', () => {
        if (xhr.status === 200) {
            updateProgress(index, 100, 'Hoàn thành');
            setTimeout(() => {
                loadFileGallery();
            }, 1000);
        } else {
            updateProgress(index, 0, 'Lỗi');
        }
    });
    
    xhr.addEventListener('error', () => {
        updateProgress(index, 0, 'Lỗi kết nối');
    });
    
    xhr.open('POST', 'api/upload.php');
    xhr.send(formData);
}

// Create progress item HTML
function createProgressItem(filename, index) {
    const div = document.createElement('div');
    div.className = 'progress-item';
    div.id = `progress-${index}`;
    div.innerHTML = `
        <div class="progress-header">
            <span class="progress-filename">${filename}</span>
            <span class="progress-status">Đang tải...</span>
        </div>
        <div class="progress-bar-container">
            <div class="progress-bar" style="width: 0%"></div>
        </div>
    `;
    return div;
}

// Update progress
function updateProgress(index, percent, status = null) {
    const progressItem = document.getElementById(`progress-${index}`);
    if (!progressItem) return;
    
    const progressBar = progressItem.querySelector('.progress-bar');
    const progressStatus = progressItem.querySelector('.progress-status');
    
    progressBar.style.width = percent + '%';
    
    if (status) {
        progressStatus.textContent = status;
    } else {
        progressStatus.textContent = Math.round(percent) + '%';
    }
}

// Load file gallery
function loadFileGallery() {
    fetch('api/get-files.php')
        .then(response => response.json())
        .then(data => {
            displayFileGallery(data.files || []);
        })
        .catch(error => {
            console.error('Error loading files:', error);
            displayFileGallery([]);
        });
}

// Display file gallery
function displayFileGallery(files) {
    const gallery = document.getElementById('fileGallery');
    if (!gallery) return;
    
    if (files.length === 0) {
        gallery.innerHTML = `
            <div class="empty-state">
                <i class="fas fa-folder-open"></i>
                <p>Chưa có file nào được tải lên</p>
            </div>
        `;
        return;
    }
    
    gallery.innerHTML = files.map(file => `
        <div class="file-item" data-id="${file.id}">
            <div class="file-preview">
                ${getFilePreview(file)}
            </div>
            <div class="file-info">
                <div class="file-name" title="${file.name}">${file.name}</div>
                <div class="file-meta">
                    <span>${formatFileSize(file.size)}</span>
                    <span>${file.date}</span>
                </div>
                <div class="file-actions">
                    <button class="btn-download" onclick="downloadFile('${file.id}')">
                        <i class="fas fa-download"></i>
                    </button>
                    <button class="btn-delete" onclick="deleteFile('${file.id}')">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    `).join('');
}

// Get file preview HTML
function getFilePreview(file) {
    if (file.type.startsWith('image/')) {
        return `<img src="${file.url}" alt="${file.name}">`;
    } else if (file.type.startsWith('video/')) {
        return `<video src="${file.url}" muted></video>`;
    } else {
        return `<i class="fas fa-file"></i>`;
    }
}

// Format file size
function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
}

// Delete file
function deleteFile(fileId) {
    if (!confirm('Bạn có chắc chắn muốn xóa file này?')) return;
    
    fetch('api/delete-file.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ id: fileId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            loadFileGallery();
        } else {
            alert('Lỗi: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Có lỗi xảy ra khi xóa file');
    });
}

// Download file
function downloadFile(fileId) {
    window.location.href = `api/download-file.php?id=${fileId}`;
}

// Filter files
function filterFiles(type) {
    const buttons = document.querySelectorAll('.filter-btn');
    buttons.forEach(btn => btn.classList.remove('active'));
    event.target.classList.add('active');
    
    // Implement filter logic here
    console.log('Filter by:', type);
}
