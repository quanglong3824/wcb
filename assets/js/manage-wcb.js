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
    
    tbody.innerHTML = wcbs.map(wcb => `
        <tr>
            <td>${wcb.id}</td>
            <td>
                <img src="${wcb.thumbnail}" alt="${wcb.name}" class="wcb-preview" 
                     onclick="previewWCB('${wcb.id}')">
            </td>
            <td>${wcb.name}</td>
            <td>${wcb.type === 'image' ? 'Hình ảnh' : 'Video'}</td>
            <td>${wcb.assignedTo || 'Chưa gán'}</td>
            <td>
                <span class="wcb-status ${wcb.status}">
                    ${wcb.status === 'active' ? 'Đang sử dụng' : 'Không sử dụng'}
                </span>
            </td>
            <td>
                <div class="wcb-actions">
                    <button class="btn-edit" onclick="editWCB('${wcb.id}')">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn-assign" onclick="assignWCB('${wcb.id}')">
                        <i class="fas fa-tv"></i>
                    </button>
                    <button class="btn-delete-wcb" onclick="deleteWCB('${wcb.id}')">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </td>
        </tr>
    `).join('');
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

// Edit WCB
function editWCB(id) {
    const wcb = wcbData.find(w => w.id == id);
    if (!wcb) return;
    
    const modal = document.getElementById('wcbModal');
    const form = document.getElementById('wcbForm');
    
    if (modal && form) {
        // Reset form first
        form.reset();
        
        // Fill form with data
        document.getElementById('modalTitle').textContent = 'Chỉnh sửa WCB';
        document.getElementById('wcbId').value = wcb.id;
        document.getElementById('wcbName').value = wcb.name;
        document.getElementById('wcbType').value = wcb.type;
        document.getElementById('wcbDescription').value = wcb.description || '';
        
        // Update file input hint
        const fileRequired = document.getElementById('fileRequired');
        const fileHint = document.getElementById('fileHint');
        if (fileRequired) fileRequired.style.display = 'none';
        if (fileHint) {
            fileHint.textContent = 'Chọn file mới nếu muốn thay đổi';
            fileHint.style.color = '#999';
        }
        
        // Clear file input
        const fileInput = form.querySelector('input[type="file"]');
        if (fileInput) {
            fileInput.value = '';
        }
        
        // Hide preview
        const filePreview = document.getElementById('filePreview');
        if (filePreview) {
            filePreview.style.display = 'none';
        }
        
        // Show modal
        modal.classList.add('active');
    }
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
    // Open assign modal or redirect to assignment page
    window.location.href = `assign-wcb.php?wcb=${id}`;
}

// Preview WCB
function previewWCB(id) {
    const wcb = wcbData.find(w => w.id == id);
    if (!wcb) return;
    
    // Open preview in modal or new window
    window.open(wcb.url, '_blank');
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
