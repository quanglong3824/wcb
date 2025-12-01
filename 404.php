<?php
/**
 * 404 Not Found Page
 * Trang hiển thị khi không tìm thấy nội dung
 */
session_start();

// Xác định base path
$basePath = './';
$pageTitle = '404 - Không tìm thấy trang';
$isLoggedIn = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle; ?> - Welcome Board System</title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Page CSS -->
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/css/404.css">
</head>
<body>
    <div class="error-container">
        <div class="error-icon">
            <i class="fas fa-ghost"></i>
        </div>
        
        <div class="error-code">404</div>
        
        <h1 class="error-title">Oops! Trang không tồn tại</h1>
        
        <p class="error-message">
            Trang bạn đang tìm kiếm có thể đã bị xóa, đổi tên hoặc tạm thời không khả dụng.
            Vui lòng kiểm tra lại đường dẫn hoặc quay về trang chủ.
        </p>
        
        <div class="error-actions">
            <?php if ($isLoggedIn): ?>
                <a href="<?php echo $basePath; ?>index.php" class="btn btn-primary">
                    <i class="fas fa-home"></i>
                    Về Dashboard
                </a>
            <?php else: ?>
                <a href="<?php echo $basePath; ?>auth/login.php" class="btn btn-primary">
                    <i class="fas fa-sign-in-alt"></i>
                    Đăng nhập
                </a>
            <?php endif; ?>
            
            <a href="javascript:history.back()" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i>
                Quay lại
            </a>
        </div>
        
        <?php if ($isLoggedIn): ?>
        <div class="suggestions">
            <h3>Có thể bạn muốn truy cập:</h3>
            <div class="suggestions-list">
                <a href="<?php echo $basePath; ?>tv.php" class="suggestion-link">
                    <i class="fas fa-tv"></i> Quản lý TV
                </a>
                <a href="<?php echo $basePath; ?>manage-wcb.php" class="suggestion-link">
                    <i class="fas fa-image"></i> Quản lý WCB
                </a>
                <a href="<?php echo $basePath; ?>schedule.php" class="suggestion-link">
                    <i class="fas fa-calendar"></i> Lịch chiếu
                </a>
                <a href="<?php echo $basePath; ?>uploads.php" class="suggestion-link">
                    <i class="fas fa-upload"></i> Upload
                </a>
                <a href="<?php echo $basePath; ?>settings.php" class="suggestion-link">
                    <i class="fas fa-cog"></i> Cài đặt
                </a>
            </div>
        </div>
        <?php endif; ?>
    </div>
</body>
</html>
