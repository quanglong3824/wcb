<?php
require_once 'includes/auth-check.php';
require_once 'config/php/config.php';

// Xác định base path
$basePath = './';
$pageTitle = 'Quản lý Slideshow - Welcome Board System';

// Include header
include 'includes/header.php';
include 'includes/sidebar.php';
?>

<!-- Main Content -->
<main class="main-content">
    <link rel="stylesheet" href="assets/css/slideshow.css">

    <div class="slideshow-container">
        <!-- Header -->
        <div class="slideshow-header">
            <div>
                <h1><i class="fas fa-images"></i> Quản lý Slideshow</h1>
                <p>Tạo và quản lý slideshow với nhạc nền cho TV</p>
            </div>
            <div class="header-actions">
                <button class="btn-primary" onclick="openCreateModal()">
                    <i class="fas fa-plus"></i> Tạo Slideshow Mới
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div class="slideshow-controls">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" id="searchInput" placeholder="Tìm kiếm slideshow..." onkeyup="filterSlideshows()">
            </div>

            <div class="filter-group">
                <select id="statusFilter" onchange="filterSlideshows()">
                    <option value="all">Tất cả trạng thái</option>
                    <option value="active">Active</option>
                    <option value="inactive">Inactive</option>
                </select>

                <button class="btn-refresh" onclick="loadSlideshows()" title="Làm mới">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
        </div>

        <!-- Slideshows Grid -->
        <div id="slideshowGrid" class="slideshow-grid">
            <!-- Slideshows will be loaded here -->
            <div class="empty-state" style="grid-column: 1/-1;">
                <i class="fas fa-spinner fa-spin"></i>
                <p>Đang tải dữ liệu...</p>
            </div>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    <div id="slideshowModal" class="modal">
        <div class="modal-content modal-large">
            <div class="modal-header">
                <h2><i class="fas fa-images"></i> <span id="modalTitle">Tạo Slideshow Mới</span></h2>
                <button class="modal-close" onclick="closeModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="modal-body">
                <form id="slideshowForm">
                    <input type="hidden" id="slideshowId" name="slideshow_id">

                    <div class="form-row">
                        <div class="form-group flex-2">
                            <label for="slideshowName">Tên Slideshow *</label>
                            <input type="text" id="slideshowName" name="name" placeholder="Vd: Slideshow Chào Mừng"
                                required>
                        </div>

                        <div class="form-group">
                            <label for="transitionDuration">Thời gian mỗi ảnh (giây) *</label>
                            <input type="number" id="transitionDuration" name="transition_duration" value="5" min="1"
                                max="60" required>
                        </div>

                        <div class="form-group">
                            <label for="fadeOutDuration">Fade Out nhạc (giây)</label>
                            <input type="number" id="fadeOutDuration" name="fade_out_duration" value="3" min="0"
                                max="10">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="slideshowDescription">Mô tả</label>
                        <textarea id="slideshowDescription" name="description" rows="2"
                            placeholder="Nhập mô tả cho slideshow..."></textarea>
                    </div>

                    <!-- Audio Selection -->
                    <div class="form-group">
                        <label>Nhạc nền</label>
                        <div class="audio-upload-section">
                            <div class="upload-area" id="audioUploadArea">
                                <i class="fas fa-music"></i>
                                <p>Kéo thả file nhạc hoặc click để chọn</p>
                                <small>Hỗ trợ: MP3, WAV, OGG, M4A (tối đa 50MB)</small>
                                <input type="file" id="audioFileInput" accept="audio/*" style="display: none;">
                            </div>
                            <div id="selectedAudio" class="selected-audio" style="display: none;">
                                <div class="audio-info">
                                    <i class="fas fa-music"></i>
                                    <div>
                                        <p class="audio-name"></p>
                                        <small class="audio-duration"></small>
                                    </div>
                                </div>
                                <button type="button" class="btn-remove" onclick="removeAudio()">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <input type="hidden" id="audioId" name="audio_id">
                        </div>
                    </div>

                    <!-- Image Selection -->
                    <div class="form-group">
                        <label>Chọn ảnh *</label>
                        <div class="image-selection-section">
                            <button type="button" class="btn-select-images" onclick="openImageSelector()">
                                <i class="fas fa-images"></i> Chọn từ thư viện
                            </button>
                            <div id="selectedImages" class="selected-images">
                                <!-- Selected images will appear here -->
                            </div>
                        </div>
                    </div>

                    <!-- Duration Info -->
                    <div class="duration-info">
                        <div class="info-item">
                            <i class="fas fa-images"></i>
                            <span>Số lượng ảnh: <strong id="imageCount">0</strong></span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-clock"></i>
                            <span>Tổng thời gian: <strong id="totalDuration">0:00</strong></span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-music"></i>
                            <span>Thời lượng nhạc: <strong id="audioDuration">--:--</strong></span>
                        </div>
                    </div>

                    <div class="form-actions">
                        <button type="button" class="btn-cancel" onclick="closeModal()">
                            <i class="fas fa-times"></i> Hủy
                        </button>
                        <button type="submit" class="btn-save">
                            <i class="fas fa-save"></i> Lưu Slideshow
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Image Selector Modal -->
    <div id="imageSelectorModal" class="modal">
        <div class="modal-content modal-large">
            <div class="modal-header">
                <h2><i class="fas fa-images"></i> Chọn ảnh từ thư viện</h2>
                <button class="modal-close" onclick="closeImageSelector()">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            <div class="modal-body">
                <div class="image-library-grid" id="imageLibraryGrid">
                    <!-- Images will be loaded here -->
                    <div class="loading-state">
                        <i class="fas fa-spinner fa-spin"></i>
                        <p>Đang tải ảnh...</p>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="button" class="btn-cancel" onclick="closeImageSelector()">
                        <i class="fas fa-times"></i> Hủy
                    </button>
                    <button type="button" class="btn-primary" onclick="confirmImageSelection()">
                        <i class="fas fa-check"></i> Xác nhận (<span id="selectedImageCount">0</span> ảnh)
                    </button>
                </div>
            </div>
        </div>
    </div>
</main>

<script src="assets/js/slideshow.js"></script>

<?php
include 'includes/footer.php';
?>