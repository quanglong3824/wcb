/**
 * Profile Page JavaScript
 */

// Form validation
document.addEventListener('DOMContentLoaded', function() {
    // Change password form validation
    const passwordForm = document.querySelector('form[action="api/change-password.php"]');
    
    if (passwordForm) {
        passwordForm.addEventListener('submit', function(e) {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            // Check if passwords match
            if (newPassword !== confirmPassword) {
                e.preventDefault();
                showError('Mật khẩu mới không khớp!');
                return false;
            }
            
            // Check password length
            if (newPassword.length < 6) {
                e.preventDefault();
                showError('Mật khẩu phải có ít nhất 6 ký tự!');
                return false;
            }
            
            // Confirm password change
            if (!confirm('Bạn có chắc muốn đổi mật khẩu?')) {
                e.preventDefault();
                return false;
            }
        });
    }
    
    // Profile update form
    const profileForm = document.querySelector('form[action="api/update-profile.php"]');
    
    if (profileForm) {
        profileForm.addEventListener('submit', function(e) {
            const fullName = document.getElementById('full_name').value.trim();
            
            if (!fullName) {
                e.preventDefault();
                showError('Vui lòng nhập họ và tên!');
                return false;
            }
        });
    }
});

// Show error message
function showError(message) {
    const container = document.querySelector('.profile-container');
    const existingAlert = container.querySelector('.alert-danger');
    
    if (existingAlert) {
        existingAlert.remove();
    }
    
    const alert = document.createElement('div');
    alert.className = 'alert alert-danger';
    alert.innerHTML = `
        <i class="fas fa-exclamation-circle"></i>
        <span>${message}</span>
    `;
    
    container.insertBefore(alert, container.firstChild);
    
    // Scroll to top
    window.scrollTo({ top: 0, behavior: 'smooth' });
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        alert.style.opacity = '0';
        alert.style.transition = 'opacity 0.3s';
        setTimeout(() => alert.remove(), 300);
    }, 5000);
}

// Auto-hide alerts after 5 seconds
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert');
    alerts.forEach(alert => {
        setTimeout(() => {
            alert.style.opacity = '0';
            alert.style.transition = 'opacity 0.3s';
            setTimeout(() => alert.remove(), 300);
        }, 5000);
    });
});

// Password strength indicator (optional enhancement)
document.addEventListener('DOMContentLoaded', function() {
    const newPasswordInput = document.getElementById('new_password');
    
    if (newPasswordInput) {
        newPasswordInput.addEventListener('input', function() {
            const password = this.value;
            const strength = calculatePasswordStrength(password);
            // You can add visual feedback here
        });
    }
});

function calculatePasswordStrength(password) {
    let strength = 0;
    
    if (password.length >= 6) strength++;
    if (password.length >= 10) strength++;
    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) strength++;
    if (/\d/.test(password)) strength++;
    if (/[^a-zA-Z0-9]/.test(password)) strength++;
    
    return strength; // 0-5
}
