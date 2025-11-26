/**
 * Authentication JavaScript
 */

// Form validation and submission
document.addEventListener('DOMContentLoaded', function() {
    const loginForm = document.getElementById('loginForm');
    
    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            const username = document.getElementById('username').value.trim();
            const password = document.getElementById('password').value;
            const submitBtn = loginForm.querySelector('.btn-login');
            
            // Validate
            if (!username || !password) {
                e.preventDefault();
                showError('Vui lòng nhập đầy đủ thông tin!');
                return false;
            }
            
            // Show loading state
            submitBtn.classList.add('loading');
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang đăng nhập...';
        });
    }
});

// Show error message
function showError(message) {
    const loginBody = document.querySelector('.login-body');
    const existingAlert = loginBody.querySelector('.alert');
    
    if (existingAlert) {
        existingAlert.remove();
    }
    
    const alert = document.createElement('div');
    alert.className = 'alert alert-danger';
    alert.innerHTML = `
        <i class="fas fa-exclamation-circle"></i>
        <span>${message}</span>
    `;
    
    loginBody.insertBefore(alert, loginBody.firstChild);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        alert.remove();
    }, 5000);
}

// Auto-hide messages and clean URL
document.addEventListener('DOMContentLoaded', function() {
    const messages = document.querySelectorAll('.message');
    
    if (messages.length > 0) {
        // Auto hide after 5 seconds
        messages.forEach(message => {
            setTimeout(() => {
                message.style.opacity = '0';
                message.style.transition = 'opacity 0.5s';
            }, 5000);
        });
        
        // Clean URL immediately to prevent showing message on refresh
        setTimeout(() => {
            cleanURL();
        }, 100);
    }
});

// Clean URL parameters
function cleanURL() {
    if (window.location.search) {
        const url = window.location.pathname;
        window.history.replaceState({}, document.title, url);
    }
}

// Toggle password visibility
function togglePassword() {
    const passwordInput = document.getElementById('password');
    const toggleIcon = document.querySelector('.toggle-password i');
    
    if (passwordInput.type === 'password') {
        passwordInput.type = 'text';
        toggleIcon.classList.remove('fa-eye');
        toggleIcon.classList.add('fa-eye-slash');
    } else {
        passwordInput.type = 'password';
        toggleIcon.classList.remove('fa-eye-slash');
        toggleIcon.classList.add('fa-eye');
    }
}
