<?php
session_start();

// Check if system is installed
$lockFile = dirname(__DIR__) . '/.installed';
if (!file_exists($lockFile)) {
    header('Location: ../install.php');
    exit;
}

// Nếu đã đăng nhập, redirect về trang chủ
if (isset($_SESSION['user_id'])) {
    header('Location: ../index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head></head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - Welcome Board System</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Auth CSS -->
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h1>Welcome Board System</h1>
            <p>Quang Long Hotel Management</p>
        </div>
        
        <div class="login-body">
            <!-- Fixed height message area -->
            <div class="message-area">
                <?php if (isset($_GET['error'])): ?>
                    <p class="message message-error">
                        <i class="fas fa-exclamation-circle"></i>
                        <?php 
                            switch ($_GET['error']) {
                                case 'invalid':
                                    echo 'Tên đăng nhập hoặc mật khẩu không đúng!';
                                    break;
                                case 'empty':
                                    echo 'Vui lòng nhập đầy đủ thông tin!';
                                    break;
                                case 'connection':
                                    echo 'Không thể kết nối database!';
                                    break;
                                default:
                                    echo 'Đã xảy ra lỗi. Vui lòng thử lại!';
                            }
                        ?>
                    </p>
                <?php elseif (isset($_GET['logout'])): ?>
                    <p class="message message-success">
                        <i class="fas fa-check-circle"></i>
                        Đăng xuất thành công!
                    </p>
                <?php elseif (isset($_GET['session_expired'])): ?>
                    <p class="message message-info">
                        <i class="fas fa-info-circle"></i>
                        Phiên đăng nhập đã hết hạn. Vui lòng đăng nhập lại.
                    </p>
                <?php elseif (isset($_GET['password_reset'])): ?>
                    <p class="message message-success">
                        <i class="fas fa-check-circle"></i>
                        Đặt lại mật khẩu thành công! Vui lòng đăng nhập với mật khẩu mới.
                    </p>
                <?php endif; ?>
            </div>
            
            <form id="loginForm" action="process-login.php" method="POST">
                <div class="form-group">
                    <label for="username">Tên đăng nhập</label>
                    <div class="input-group">
                        <i class="fas fa-user"></i>
                        <input type="text" 
                               id="username" 
                               name="username" 
                               placeholder="Nhập tên đăng nhập"
                               required 
                               autofocus>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="password">Mật khẩu</label>
                    <div class="input-group">
                        <i class="fas fa-lock"></i>
                        <input type="password" 
                               id="password" 
                               name="password" 
                               placeholder="Nhập mật khẩu"
                               required>
                    </div>
                </div>
                
                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember" id="remember">
                        <span>Ghi nhớ đăng nhập</span>
                    </label>
                    <a href="forgot-password.php" class="forgot-password">Quên mật khẩu?</a>
                </div>
                
                <button type="submit" class="btn-login">
                    <i class="fas fa-sign-in-alt"></i>
                    Đăng nhập
                </button>
            </form>
        </div>
        
        <div class="login-footer">
            <p>
                Tài khoản mặc định: <strong>admin</strong> / <strong>admin123</strong><br>
                <a href="../check-database.php">Kiểm tra kết nối database</a> | 
                <a href="../install.php">Cài đặt lại hệ thống</a>
            </p>
        </div>
    </div>
    
    <!-- Auth JavaScript -->
    <script src="../assets/js/auth.js"></script>
</body>
</html>
