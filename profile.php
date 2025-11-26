<?php
session_start();
require_once 'config/php/config.php';

// Kiểm tra đăng nhập
if (!isLoggedIn()) {
    redirect('auth/login.php');
}

// Lấy thông tin user từ database
$conn = getDBConnection();
$user = null;

if ($conn) {
    $stmt = $conn->prepare("SELECT id, username, full_name, email, role, created_at, last_login FROM users WHERE id = ?");
    $stmt->bind_param("i", $_SESSION['user_id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
    $stmt->close();
}

// Xác định base path
$basePath = './';
$pageTitle = 'Thông tin cá nhân - Welcome Board System';

// Include header
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<!-- Main Content -->
<main class="main-content">
    <link rel="stylesheet" href="assets/css/profile.css">
    
    <div class="profile-container">
        <!-- Header -->
        <div class="profile-header">
            <h1><i class="fas fa-user-circle"></i> Thông tin cá nhân</h1>
            <p>Quản lý thông tin tài khoản và bảo mật</p>
        </div>

        <!-- Success/Error Messages -->
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success">
                <i class="fas fa-check-circle"></i>
                <span>
                    <?php
                    switch ($_GET['success']) {
                        case 'profile_updated':
                            echo 'Cập nhật thông tin thành công!';
                            break;
                        case 'password_changed':
                            echo 'Đổi mật khẩu thành công!';
                            break;
                        default:
                            echo 'Thao tác thành công!';
                    }
                    ?>
                </span>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <i class="fas fa-exclamation-circle"></i>
                <span>
                    <?php
                    switch ($_GET['error']) {
                        case 'wrong_password':
                            echo 'Mật khẩu hiện tại không đúng!';
                            break;
                        case 'password_mismatch':
                            echo 'Mật khẩu mới không khớp!';
                            break;
                        case 'update_failed':
                            echo 'Cập nhật thất bại. Vui lòng thử lại!';
                            break;
                        default:
                            echo 'Đã xảy ra lỗi. Vui lòng thử lại!';
                    }
                    ?>
                </span>
            </div>
        <?php endif; ?>

        <div class="profile-grid">
            <!-- Profile Info Card -->
            <div class="card profile-info-card">
                <div class="card-header">
                    <h2><i class="fas fa-user"></i> Thông tin tài khoản</h2>
                </div>
                
                <?php if ($user): ?>
                <div class="profile-avatar">
                    <div class="avatar-circle">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="avatar-info">
                        <h3><?php echo htmlspecialchars($user['full_name'] ?? 'Người dùng'); ?></h3>
                        <span class="role-badge <?php echo $user['role'] ?? 'content_manager'; ?>">
                            <?php echo ($user['role'] ?? '') === 'super_admin' ? 'Super Admin' : 'Content Manager'; ?>
                        </span>
                    </div>
                </div>

                <div class="info-list">
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-user"></i>
                            Tên đăng nhập
                        </div>
                        <div class="info-value"><?php echo htmlspecialchars($user['username'] ?? 'N/A'); ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-envelope"></i>
                            Email
                        </div>
                        <div class="info-value"><?php echo htmlspecialchars($user['email'] ?? 'Chưa cập nhật'); ?></div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-calendar"></i>
                            Ngày tạo
                        </div>
                        <div class="info-value">
                            <?php 
                            if (isset($user['created_at']) && $user['created_at']) {
                                echo date('d/m/Y H:i', strtotime($user['created_at']));
                            } else {
                                echo 'N/A';
                            }
                            ?>
                        </div>
                    </div>
                    
                    <div class="info-item">
                        <div class="info-label">
                            <i class="fas fa-clock"></i>
                            Đăng nhập lần cuối
                        </div>
                        <div class="info-value">
                            <?php 
                            if (isset($user['last_login']) && $user['last_login']) {
                                echo date('d/m/Y H:i', strtotime($user['last_login']));
                            } else {
                                echo 'Chưa có';
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <?php else: ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <span>Không thể tải thông tin người dùng. Vui lòng đăng nhập lại.</span>
                </div>
                <?php endif; ?>
            </div>

            <!-- Update Profile Form -->
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-edit"></i> Cập nhật thông tin</h2>
                </div>
                
                <?php if ($user): ?>
                <form action="api/update-profile.php" method="POST" class="profile-form">
                    <div class="form-group">
                        <label for="full_name">Họ và tên *</label>
                        <input type="text" 
                               id="full_name" 
                               name="full_name" 
                               value="<?php echo htmlspecialchars($user['full_name'] ?? ''); ?>"
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" 
                               id="email" 
                               name="email" 
                               value="<?php echo htmlspecialchars($user['email'] ?? ''); ?>"
                               placeholder="your@email.com">
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Lưu thay đổi
                        </button>
                    </div>
                </form>
                <?php else: ?>
                <div class="alert alert-warning">
                    <i class="fas fa-database"></i>
                    <span>Vui lòng import database để sử dụng chức năng này.</span>
                </div>
                <?php endif; ?>
            </div>

            <!-- Change Password Form -->
            <div class="card">
                <div class="card-header">
                    <h2><i class="fas fa-lock"></i> Đổi mật khẩu</h2>
                </div>
                
                <?php if ($user): ?>
                <form action="api/change-password.php" method="POST" class="profile-form">
                    <div class="form-group">
                        <label for="current_password">Mật khẩu hiện tại *</label>
                        <input type="password" 
                               id="current_password" 
                               name="current_password" 
                               required>
                    </div>
                    
                    <div class="form-group">
                        <label for="new_password">Mật khẩu mới *</label>
                        <input type="password" 
                               id="new_password" 
                               name="new_password" 
                               minlength="6"
                               required>
                        <small>Tối thiểu 6 ký tự</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Xác nhận mật khẩu mới *</label>
                        <input type="password" 
                               id="confirm_password" 
                               name="confirm_password" 
                               minlength="6"
                               required>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-key"></i> Đổi mật khẩu
                        </button>
                    </div>
                </form>
                <?php else: ?>
                <div class="alert alert-warning">
                    <i class="fas fa-database"></i>
                    <span>Vui lòng import database để sử dụng chức năng này.</span>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</main>

<script src="assets/js/profile.js"></script>

<?php
include 'includes/footer.php';
?>
