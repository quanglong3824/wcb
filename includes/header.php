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
</head>
<body>
    <!-- Top Info Bar -->
    <div class="top-info-bar">
        <div class="info-bar-content">
            <div class="info-bar-left">
                <span><i class="fas fa-hotel"></i> Quang Long Hotel - Welcome Board System</span>
            </div>
            <div class="info-bar-right">
                <a href="#" class="info-link"><i class="fas fa-question-circle"></i> Hỗ trợ</a>
                <span class="info-divider">|</span>
                <a href="#" class="info-link"><i class="fas fa-book"></i> Tài liệu</a>
                <span class="info-divider">|</span>
                <span class="info-version">v1.0</span>
            </div>
        </div>
    </div>
    
    <div class="app-container">
