<?php
require_once 'includes/auth-check.php';
require_once 'config/php/config.php';

// Xác định base path
$basePath = './';
$pageTitle = 'Giám sát TV - Welcome Board System';

// Include header
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<!-- Main Content -->
<main class="main-content">
    <style>
        .coming-soon-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: calc(100vh - 200px);
            text-align: center;
            padding: 40px 20px;
        }
        
        .coming-soon-icon {
            font-size: 8em;
            color: #d4af37;
            margin-bottom: 30px;
            animation: pulse 2s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
                opacity: 1;
            }
            50% {
                transform: scale(1.05);
                opacity: 0.8;
            }
        }
        
        .coming-soon-title {
            font-size: 3em;
            font-weight: 700;
            color: #1a1a1a;
            margin-bottom: 20px;
            background: linear-gradient(135deg, #d4af37 0%, #f4d03f 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .coming-soon-subtitle {
            font-size: 1.3em;
            color: #666;
            margin-bottom: 40px;
            max-width: 600px;
        }
        
        .coming-soon-features {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 30px;
            max-width: 900px;
            margin: 40px 0;
        }
        
        .feature-card {
            background: white;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
        }
        
        .feature-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(212, 175, 55, 0.2);
        }
        
        .feature-icon {
            font-size: 3em;
            color: #d4af37;
            margin-bottom: 15px;
        }
        
        .feature-title {
            font-size: 1.2em;
            font-weight: 600;
            color: #1a1a1a;
            margin-bottom: 10px;
        }
        
        .feature-desc {
            color: #666;
            font-size: 0.95em;
            line-height: 1.6;
        }
        
        .coming-soon-actions {
            display: flex;
            gap: 15px;
            margin-top: 40px;
            flex-wrap: wrap;
            justify-content: center;
        }
        
        .btn-action {
            padding: 15px 30px;
            font-size: 1.1em;
            font-weight: 600;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
            text-decoration: none;
        }
        
        .btn-primary-action {
            background: linear-gradient(135deg, #d4af37 0%, #f4d03f 100%);
            color: white;
        }
        
        .btn-primary-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(212, 175, 55, 0.4);
        }
        
        .btn-secondary-action {
            background: #f3f4f6;
            color: #1a1a1a;
        }
        
        .btn-secondary-action:hover {
            background: #e5e7eb;
        }
        
        .progress-bar {
            width: 100%;
            max-width: 500px;
            height: 8px;
            background: #e5e7eb;
            border-radius: 10px;
            overflow: hidden;
            margin: 30px 0;
        }
        
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #d4af37 0%, #f4d03f 100%);
            width: 65%;
            animation: progressAnimation 2s ease-in-out;
        }
        
        @keyframes progressAnimation {
            from {
                width: 0%;
            }
            to {
                width: 65%;
            }
        }
        
        .progress-text {
            color: #666;
            font-size: 0.9em;
            margin-top: 10px;
        }
        
        @media (max-width: 768px) {
            .coming-soon-title {
                font-size: 2em;
            }
            
            .coming-soon-subtitle {
                font-size: 1.1em;
            }
            
            .coming-soon-features {
                grid-template-columns: 1fr;
            }
        }
    </style>
    
    <div class="coming-soon-container">
        <div class="coming-soon-icon">
            <i class="fas fa-desktop"></i>
        </div>
        
        <h1 class="coming-soon-title">Giám sát TV</h1>
        <p class="coming-soon-subtitle">
            Tính năng giám sát trực tiếp các màn hình TV đang được phát triển. 
            Sẽ sớm ra mắt với nhiều tính năng mạnh mẽ!
        </p>
        
        <div class="progress-bar">
            <div class="progress-fill"></div>
        </div>
        <p class="progress-text">Đang phát triển: 65% hoàn thành</p>
        
        <div class="coming-soon-features">
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-eye"></i>
                </div>
                <h3 class="feature-title">Giám sát Real-time</h3>
                <p class="feature-desc">
                    Theo dõi trạng thái và nội dung đang chiếu trên tất cả các TV theo thời gian thực
                </p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-chart-line"></i>
                </div>
                <h3 class="feature-title">Thống kê chi tiết</h3>
                <p class="feature-desc">
                    Xem báo cáo và thống kê về thời gian hoạt động, nội dung được chiếu nhiều nhất
                </p>
            </div>
            
            <div class="feature-card">
                <div class="feature-icon">
                    <i class="fas fa-bell"></i>
                </div>
                <h3 class="feature-title">Cảnh báo tự động</h3>
                <p class="feature-desc">
                    Nhận thông báo ngay khi có TV offline hoặc gặp sự cố trong quá trình phát
                </p>
            </div>
        </div>
        
        <div class="coming-soon-actions">
            <a href="tv.php" class="btn-action btn-primary-action">
                <i class="fas fa-tv"></i>
                Quản lý TV
            </a>
            <a href="index.php" class="btn-action btn-secondary-action">
                <i class="fas fa-home"></i>
                Về Dashboard
            </a>
        </div>
    </div>
</main>

<?php
include 'includes/footer.php';
?>
