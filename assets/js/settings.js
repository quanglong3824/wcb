// Settings Page JavaScript

let currentTab = 'general';

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    loadSettings();
    initializeTabs();
});

// Initialize tabs
function initializeTabs() {
    const tabs = document.querySelectorAll('.settings-tab');
    tabs.forEach(tab => {
        tab.addEventListener('click', function() {
            switchTab(this.dataset.tab);
        });
    });
}

// Switch tab
function switchTab(tabName) {
    currentTab = tabName;
    
    // Update tab buttons
    document.querySelectorAll('.settings-tab').forEach(tab => {
        tab.classList.remove('active');
        if (tab.dataset.tab === tabName) {
            tab.classList.add('active');
        }
    });
    
    // Update content sections
    document.querySelectorAll('.settings-section').forEach(section => {
        section.classList.remove('active');
        if (section.id === tabName + '-settings') {
            section.classList.add('active');
        }
    });
}

// Load settings
function loadSettings() {
    fetch('api/get-settings.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateSettings(data.settings);
            }
        })
        .catch(error => {
            console.error('Error loading settings:', error);
        });
}

// Populate settings form
function populateSettings(settings) {
    // General settings
    if (settings.general) {
        document.getElementById('hotelName').value = settings.general.hotelName || '';
        document.getElementById('systemName').value = settings.general.systemName || '';
        document.getElementById('timezone').value = settings.general.timezone || 'Asia/Ho_Chi_Minh';
        document.getElementById('language').value = settings.general.language || 'vi';
    }
    
    // Display settings
    if (settings.display) {
        document.getElementById('autoRefresh').checked = settings.display.autoRefresh || false;
        document.getElementById('refreshInterval').value = settings.display.refreshInterval || 30;
        document.getElementById('defaultTransition').value = settings.display.defaultTransition || 'fade';
        document.getElementById('transitionDuration').value = settings.display.transitionDuration || 1;
    }
    
    // Notification settings
    if (settings.notification) {
        document.getElementById('emailNotifications').checked = settings.notification.emailNotifications || false;
        document.getElementById('notificationEmail').value = settings.notification.notificationEmail || '';
    }
}

// Save general settings
function saveGeneralSettings(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    
    fetch('api/save-settings.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Lưu cài đặt thành công!');
        } else {
            showAlert('error', 'Lỗi: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'Có lỗi xảy ra khi lưu cài đặt');
    });
}

// Save display settings
function saveDisplaySettings(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    
    fetch('api/save-display-settings.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Lưu cài đặt hiển thị thành công!');
        } else {
            showAlert('error', 'Lỗi: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'Có lỗi xảy ra khi lưu cài đặt');
    });
}

// Save notification settings
function saveNotificationSettings(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    
    fetch('api/save-notification-settings.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Lưu cài đặt thông báo thành công!');
        } else {
            showAlert('error', 'Lỗi: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'Có lỗi xảy ra khi lưu cài đặt');
    });
}

// Show alert message
function showAlert(type, message) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert ${type}`;
    alertDiv.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'}"></i>
        <span>${message}</span>
    `;
    
    const container = document.querySelector('.settings-content');
    container.insertBefore(alertDiv, container.firstChild);
    
    setTimeout(() => {
        alertDiv.style.opacity = '0';
        setTimeout(() => alertDiv.remove(), 300);
    }, 3000);
}

// Reset settings
function resetSettings() {
    if (!confirm('Bạn có chắc chắn muốn khôi phục cài đặt mặc định?')) return;
    
    fetch('api/reset-settings.php', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Đã khôi phục cài đặt mặc định!');
            loadSettings();
        } else {
            showAlert('error', 'Lỗi: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'Có lỗi xảy ra');
    });
}

// Test email notification
function testEmailNotification() {
    const email = document.getElementById('notificationEmail').value;
    
    if (!email) {
        alert('Vui lòng nhập email trước');
        return;
    }
    
    fetch('api/test-email.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ email: email })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Email test đã được gửi!');
        } else {
            showAlert('error', 'Lỗi: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'Có lỗi xảy ra khi gửi email');
    });
}
