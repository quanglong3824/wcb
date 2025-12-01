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
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #fff;
        }
        
        .error-container {
            text-align: center;
            padding: 40px;
            max-width: 600px;
        }
        
        .error-code {
            font-size: 150px;
            font-weight: 700;
            background: linear-gradient(135deg, #d4af37 0%, #f4d03f 50%, #d4af37 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            line-height: 1;
            margin-bottom: 20px;
            text-shadow: 0 0 40px rgba(212, 175, 55, 0.3);
        }
        
        .error-icon {
            font-size: 80px;
            color: #d4af37;
            margin-bottom: 30px;
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
        
        .error-title {
            font-size: 32px;
            margin-bottom: 15px;
            color: #fff;
        }
        
        .error-message {
            font-size: 18px;
            color: rgba(255, 255, 255, 0.7);
            margin-bottom: 40px;
            line-height: 1.6;
        }
        
        .error-actions {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 15px 30px;
            border-radius: 50px;
            text-decoration: none;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #d4af37 0%, #f4d03f 100%);
            color: #1a1a2e;
        }
        
        .btn-primary:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(212, 175, 55, 0.4);
        }
        
        .btn-secondary {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }
        
        .btn-secondary:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-3px);
        }
        
        .suggestions {
            margin-top: 50px;
            padding-top: 30px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .suggestions h3 {
            font-size: 18px;
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 20px;
        }
        
        .suggestions-list {
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        
        .suggestion-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 30px;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            font-size: 14px;
            transition: all 0.3s ease;
        }
        
        .suggestion-link:hover {
            background: rgba(212, 175, 55, 0.2);
            color: #d4af37;
        }
        
        .suggestion-link i {
            font-size: 16px;
        }
    </style>
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
