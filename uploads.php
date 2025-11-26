<?php
session_start();
require_once 'config/php/config.php';

// Kiểm tra đăng nhập
if (!isLoggedIn()) {
    redirect('auth/login.php');
}

// Xác định base path
$basePath = './';
$pageTitle = 'Quản lý Upload - Welcome Board System';

// Include header
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<!-- Main Content -->
<main class="main-content">
    <link rel="stylesheet" href="assets/css/uploads.css">
    
    <div class="upload-container">
        <!-- Header -->
        <div class="upload-header">
            <h1><i class="fas fa-cloud-upload-alt"></i> Quản lý Upload</h1>
            <p>Tải lên và quản lý nội dung WCB (Welcome Board)</p>
        </div>

        <!-- Upload Zone -->
        <div class="upload-zone">
            <div id="dropzone" class="dropzone">
                <div class="dropzone-icon">
                    <i class="fas fa-cloud-upload-alt"></i>
                </div>
                <div class="dropzone-text">
                    <h3>Kéo thả file vào đây hoặc click để chọn</h3>
                    <p>Hỗ trợ nhiều file cùng lúc</p>
                    <button type="button" class="upload-btn">
                        <i class="fas fa-folder-open"></i>
                        Chọn file
                    </button>
                    <p class="file-types">
                        Định dạng: JPG, PNG, GIF, MP4, WEBM (Tối đa 50MB)
                    </p>
                </div>
                <input type="file" id="fileInput" multiple accept="image/*,video/*">
            </div>
            
            <!-- Upload Progress -->
            <div class="upload-progress" id="uploadProgress">
                <!-- Progress items will be added here dynamically -->
            </div>
        </div>

        <!-- File Gallery -->
        <div class="file-gallery">
            <div class="gallery-header">
                <h2><i class="fas fa-images"></i> Thư viện nội dung</h2>
                <div class="gallery-filters">
                    <button class="filter-btn active" onclick="filterFiles('all')">
                        <i class="fas fa-th"></i> Tất cả
                    </button>
                    <button class="filter-btn" onclick="filterFiles('image')">
                        <i class="fas fa-image"></i> Hình ảnh
                    </button>
                    <button class="filter-btn" onclick="filterFiles('video')">
                        <i class="fas fa-video"></i> Video
                    </button>
                </div>
            </div>
            
            <div class="file-grid" id="fileGallery">
                <!-- Files will be loaded here dynamically -->
                <div class="empty-state">
                    <i class="fas fa-folder-open"></i>
                    <p>Đang tải dữ liệu...</p>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="assets/js/uploads.js"></script>

<?php
include 'includes/footer.php';
?>
