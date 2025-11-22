// Smooth scrolling cho navigation
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Kích hoạt board
function activateBoard(boardId) {
    if (confirm('Bạn có chắc muốn kích hoạt Welcome Board này?\n\nLưu ý: Tối đa 3 board có thể hiển thị cùng lúc.')) {
        const formData = new FormData();
        formData.append('action', 'activate');
        formData.append('board_id', boardId);
        
        // Hiển thị loading ngay lập tức
        showNotification('⏳ Đang kích hoạt...', 'success');
        
        fetch('admin_actions.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification('✅ ' + data.message + ' - Màn hình sẽ cập nhật trong 3 giây!', 'success');
                
                // Thông báo cho display.php cập nhật ngay
                notifyDisplayUpdate();
                
                setTimeout(() => {
                    window.location.href = 'index.php?admin_success=1';
                }, 1500);
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('Có lỗi xảy ra: ' + error.message, 'error');
        });
    }
}

// Thông báo cho display.php cập nhật ngay (trigger refresh)
function notifyDisplayUpdate() {
    // Gửi signal qua localStorage để display.php nhận được
    localStorage.setItem('wcb_update_trigger', Date.now().toString());
    
    // Gửi thêm qua file trigger
    fetch('admin_actions.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded'},
        body: 'action=trigger_update'
    });
}

// Tự động scroll đến board được gợi ý
document.addEventListener('DOMContentLoaded', function() {
    const suggestion = document.querySelector('.smart-suggestion');
    if (suggestion) {
        suggestion.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }
});

// Tắt board
function deactivateBoard(boardId) {
    if (confirm('Bạn có chắc muốn tắt hiển thị Welcome Board này?')) {
        const formData = new FormData();
        formData.append('action', 'deactivate');
        formData.append('board_id', boardId);
        
        fetch('admin_actions.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                setTimeout(() => {
                    window.location.href = 'index.php?admin_success=1';
                }, 1000);
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('Có lỗi xảy ra: ' + error.message, 'error');
        });
    }
}

// Xóa board
function deleteBoard(boardId) {
    if (confirm('⚠️ CẢNH BÁO: Bạn có chắc muốn xóa Welcome Board này?\n\nHành động này không thể hoàn tác!')) {
        const formData = new FormData();
        formData.append('action', 'delete');
        formData.append('board_id', boardId);
        
        fetch('admin_actions.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                setTimeout(() => {
                    window.location.href = 'index.php?admin_success=1';
                }, 1000);
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            showNotification('Có lỗi xảy ra: ' + error.message, 'error');
        });
    }
}

// Xem trước ảnh
function previewImage(imagePath) {
    const modal = document.getElementById('imageModal');
    const modalImg = document.getElementById('modalImage');
    
    modal.style.display = 'block';
    modalImg.src = imagePath;
}

// Đóng modal
function closeModal() {
    document.getElementById('imageModal').style.display = 'none';
}

// Hiển thị thông báo
function showNotification(message, type) {
    // Tạo element thông báo
    const notification = document.createElement('div');
    notification.className = `notification ${type}`;
    notification.innerHTML = `
        <span>${message}</span>
        <button onclick="this.parentElement.remove()">×</button>
    `;
    
    // Thêm CSS cho notification nếu chưa có
    if (!document.querySelector('#notification-styles')) {
        const style = document.createElement('style');
        style.id = 'notification-styles';
        style.textContent = `
            .notification {
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 15px 20px;
                border-radius: 10px;
                color: white;
                font-weight: 600;
                z-index: 10000;
                animation: slideIn 0.3s ease-out;
                max-width: 400px;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            }
            
            .notification.success {
                background: #28a745;
            }
            
            .notification.error {
                background: #dc3545;
            }
            
            .notification button {
                background: none;
                border: none;
                color: white;
                font-size: 18px;
                font-weight: bold;
                cursor: pointer;
                float: right;
                margin-left: 10px;
            }
            
            @keyframes slideIn {
                from {
                    transform: translateX(100%);
                    opacity: 0;
                }
                to {
                    transform: translateX(0);
                    opacity: 1;
                }
            }
        `;
        document.head.appendChild(style);
    }
    
    // Thêm vào body
    document.body.appendChild(notification);
    
    // Tự động xóa sau 5 giây
    setTimeout(() => {
        if (notification.parentElement) {
            notification.remove();
        }
    }, 5000);
}

// Validation cho form upload
document.addEventListener('DOMContentLoaded', function() {
    const uploadForm = document.querySelector('.upload-form');
    const fileInput = document.querySelector('#welcome_image');
    
    if (uploadForm && fileInput) {
        uploadForm.addEventListener('submit', function(e) {
            const file = fileInput.files[0];
            
            if (file) {
                // Kiểm tra kích thước file (max 10MB)
                if (file.size > 10 * 1024 * 1024) {
                    e.preventDefault();
                    showNotification('File quá lớn! Vui lòng chọn file nhỏ hơn 10MB.', 'error');
                    return;
                }
                
                // Kiểm tra định dạng file
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png'];
                if (!allowedTypes.includes(file.type)) {
                    e.preventDefault();
                    showNotification('Chỉ chấp nhận file JPG, PNG!', 'error');
                    return;
                }
                
                // Hiển thị loading
                const submitBtn = uploadForm.querySelector('button[type="submit"]');
                const originalText = submitBtn.innerHTML;
                submitBtn.innerHTML = '⏳ Đang upload...';
                submitBtn.disabled = true;
                
                // Khôi phục nút sau 10 giây (phòng trường hợp lỗi)
                setTimeout(() => {
                    submitBtn.innerHTML = originalText;
                    submitBtn.disabled = false;
                }, 10000);
            }
        });
        
        // Preview ảnh khi chọn file
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Tạo preview nếu chưa có
                    let preview = document.querySelector('#image-preview');
                    if (!preview) {
                        preview = document.createElement('div');
                        preview.id = 'image-preview';
                        preview.innerHTML = `
                            <h4>🖼️ Xem trước:</h4>
                            <img id="preview-img" style="max-width: 300px; max-height: 200px; border-radius: 8px; margin-top: 10px;">
                        `;
                        fileInput.parentElement.appendChild(preview);
                    }
                    
                    document.querySelector('#preview-img').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    }
});

// Keyboard shortcuts
document.addEventListener('keydown', function(e) {
    // Ctrl + R: Refresh trang
    if (e.ctrlKey && e.key === 'r') {
        e.preventDefault();
        location.reload();
    }
    
    // Escape: Đóng modal
    if (e.key === 'Escape') {
        closeModal();
    }
});

// Auto refresh admin panel mỗi 60 giây
if (window.location.pathname.includes('index.php') || window.location.pathname === '/') {
    setInterval(() => {
        // Chỉ refresh phần admin list, không refresh toàn trang
        const adminPanel = document.querySelector('.admin-controls');
        if (adminPanel && !document.querySelector('.modal').style.display === 'block') {
            // Có thể thêm AJAX refresh ở đây nếu cần
        }
    }, 60000);
}