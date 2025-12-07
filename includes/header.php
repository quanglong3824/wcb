<?php
// Xác định base path
$basePath = isset($basePath) ? $basePath : '../';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Welcome Board System'; ?></title>
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Main CSS -->
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/css/style.css">
    
    <!-- Mobile Responsive CSS -->
    <link rel="stylesheet" href="<?php echo $basePath; ?>assets/css/mobile-responsive.css">
</head>
<body>
    <!-- Top Info Bar -->
    <div class="top-info-bar">
        <div class="info-bar-content">
            <div class="info-bar-left">
                <!-- Hamburger Menu Button -->
                <button class="hamburger-menu" id="hamburgerMenu" aria-label="Menu">
                    <span></span><span></span><span></span>
                </button>
                <span><i class="fas fa-hotel"></i> Aurora Hotel Plaza - Welcome Board System</span>
            </div>
            <div class="info-bar-right">
                <a href="#" class="info-link"><i class="fas fa-question-circle"></i> <span>Hỗ trợ</span></a>
                <span class="info-divider">|</span>
                <a href="#" class="info-link"><i class="fas fa-book"></i> <span>Tài liệu</span></a>
                <span class="info-divider">|</span>
                <span class="info-version">v1.0</span>
            </div>
        </div>
    </div>
    
    <div class="app-container">

