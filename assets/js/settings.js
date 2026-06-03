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
    // Show loading indicator
    const container = document.querySelector('.settings-content');
    const loadingDiv = document.createElement('div');
    loadingDiv.className = 'loading-overlay';
    loadingDiv.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang tải cài đặt...';
    loadingDiv.style.cssText = 'position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);z-index:999;';
    
    fetch('api/settings.php')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                populateSettings(data.grouped || {});
            } else {
                showAlert('error', 'Không thể tải cài đặt: ' + (data.message || 'Unknown error'));
            }
        })
        .catch(error => {
            console.error('Error loading settings:', error);
            showAlert('error', 'Lỗi kết nối khi tải cài đặt');
        })
        .finally(() => {
            if (loadingDiv.parentNode) {
                loadingDiv.remove();
            }
        });
}

// Populate settings form
function populateSettings(grouped) {
    // General settings
    if (grouped.general) {
        const gen = grouped.general;
        if (gen.hotel_name) document.getElementById('hotelName').value = gen.hotel_name;
        if (gen.system_name) document.getElementById('systemName').value = gen.system_name;
        if (gen.timezone) document.getElementById('timezone').value = gen.timezone;
        if (gen.language) document.getElementById('language').value = gen.language;
    }
    
    // Display settings (including auto reload)
    if (grouped.display || grouped.auto_reload) {
        const display = grouped.display || {};
        const autoReload = grouped.auto_reload || {};
        
        // Basic display settings
        if (display.auto_refresh !== undefined) {
            document.getElementById('autoRefresh').checked = display.auto_refresh === '1' || display.auto_refresh === true;
        }
        if (display.refresh_interval) {
            document.getElementById('refreshInterval').value = display.refresh_interval;
        }
        if (display.default_transition) {
            document.getElementById('defaultTransition').value = display.default_transition;
        }
        if (display.transition_duration) {
            document.getElementById('transitionDuration').value = display.transition_duration;
        }
        
        // Auto reload settings
        if (autoReload.auto_reload_enabled !== undefined) {
            document.getElementById('autoReloadEnabled').checked = autoReload.auto_reload_enabled === '1' || autoReload.auto_reload_enabled === true;
        }
        if (autoReload.auto_reload_mode) {
            document.getElementById('autoReloadMode').value = autoReload.auto_reload_mode;
        }
        if (autoReload.auto_reload_interval) {
            document.getElementById('autoReloadInterval').value = autoReload.auto_reload_interval;
        }
        if (autoReload.auto_reload_threshold) {
            document.getElementById('autoReloadThreshold').value = autoReload.auto_reload_threshold;
        }
        if (autoReload.simple_reload_enabled !== undefined) {
            document.getElementById('simpleReloadEnabled').checked = autoReload.simple_reload_enabled === '1' || autoReload.simple_reload_enabled === true;
        }
        if (autoReload.simple_reload_interval) {
            document.getElementById('simpleReloadInterval').value = autoReload.simple_reload_interval;
        }
    }
    
    // Notification settings
    if (grouped.notification) {
        const notif = grouped.notification;
        if (notif.email_notifications !== undefined) {
            document.getElementById('emailNotifications').checked = notif.email_notifications === '1' || notif.email_notifications === true;
        }
        if (notif.notification_email) {
            document.getElementById('notificationEmail').value = notif.notification_email;
        }
    }
    
    console.log('[Settings] Settings loaded successfully');
}

// Save general settings
function saveGeneralSettings(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const submitBtn = event.target.querySelector('button[type="submit"]');
    const originalHTML = submitBtn.innerHTML;
    
    // Show loading
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang lưu...';
    submitBtn.disabled = true;
    
    // Convert to JSON
    const settings = {
        hotel_name: formData.get('hotelName'),
        system_name: formData.get('systemName'),
        timezone: formData.get('timezone'),
        language: formData.get('language')
    };
    
    fetch('api/settings.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(settings)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Lưu cài đặt chung thành công!');
        } else {
            showAlert('error', 'Lỗi: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'Có lỗi xảy ra khi lưu cài đặt');
    })
    .finally(() => {
        submitBtn.innerHTML = originalHTML;
        submitBtn.disabled = false;
    });
}

// Save display settings
function saveDisplaySettings(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const submitBtn = event.target.querySelector('button[type="submit"]');
    const originalHTML = submitBtn.innerHTML;
    
    // Show loading
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang lưu...';
    submitBtn.disabled = true;
    
    // Convert to JSON for easier handling
    const settings = {
        auto_refresh: formData.get('autoRefresh') === 'on' ? '1' : '0',
        refresh_interval: formData.get('refreshInterval'),
        default_transition: formData.get('defaultTransition'),
        transition_duration: formData.get('transitionDuration'),
        // Auto reload settings
        auto_reload_enabled: formData.get('autoReloadEnabled') === 'on' ? '1' : '0',
        auto_reload_mode: formData.get('autoReloadMode'),
        auto_reload_interval: formData.get('autoReloadInterval'),
        auto_reload_threshold: formData.get('autoReloadThreshold'),
        simple_reload_enabled: formData.get('simpleReloadEnabled') === 'on' ? '1' : '0',
        simple_reload_interval: formData.get('simpleReloadInterval')
    };
    
    fetch('api/settings.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(settings)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Lưu cài đặt hiển thị thành công! Các TV sẽ áp dụng cài đặt mới sau khi reload.');
            
            // Trigger reload all TVs to apply new settings
            setTimeout(() => {
                if (confirm('Bạn có muốn reload tất cả TV để áp dụng cài đặt mới ngay không?')) {
                    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang reload TVs...';
                    submitBtn.disabled = true;
                    
                    fetch('api/reload-all-tvs.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' }
                    })
                    .then(r => r.json())
                    .then(result => {
                        if (result.success) {
                            showAlert('success', 'Đã gửi lệnh reload đến tất cả TV!');
                        }
                    })
                    .finally(() => {
                        submitBtn.innerHTML = originalHTML;
                        submitBtn.disabled = false;
                    });
                } else {
                    submitBtn.innerHTML = originalHTML;
                    submitBtn.disabled = false;
                }
            }, 1000);
        } else {
            showAlert('error', 'Lỗi: ' + (data.message || 'Unknown error'));
            submitBtn.innerHTML = originalHTML;
            submitBtn.disabled = false;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'Có lỗi xảy ra khi lưu cài đặt');
        submitBtn.innerHTML = originalHTML;
        submitBtn.disabled = false;
    });
}

// Save notification settings
function saveNotificationSettings(event) {
    event.preventDefault();
    
    const formData = new FormData(event.target);
    const submitBtn = event.target.querySelector('button[type="submit"]');
    const originalHTML = submitBtn.innerHTML;
    
    // Show loading
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang lưu...';
    submitBtn.disabled = true;
    
    // Convert to JSON
    const settings = {
        email_notifications: formData.get('emailNotifications') === 'on' ? '1' : '0',
        notification_email: formData.get('notificationEmail')
    };
    
    fetch('api/settings.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(settings)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showAlert('success', 'Lưu cài đặt thông báo thành công!');
        } else {
            showAlert('error', 'Lỗi: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'Có lỗi xảy ra khi lưu cài đặt');
    })
    .finally(() => {
        submitBtn.innerHTML = originalHTML;
        submitBtn.disabled = false;
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
