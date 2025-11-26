-- =====================================================
-- Database: auroraho_wcb
-- Welcome Board System - Quang Long Hotel
-- Hệ thống quản lý nội dung hiển thị trên màn hình TV
-- Version: 2.0 - Updated with full UI integration
-- =====================================================

-- Tạo database
CREATE DATABASE IF NOT EXISTS `auroraho_wcb` 
DEFAULT CHARACTER SET utf8mb4 
COLLATE utf8mb4_unicode_ci;

USE `auroraho_wcb`;

-- =====================================================
-- Bảng: users
-- Quản lý tài khoản người dùng và phân quyền
-- =====================================================
CREATE TABLE `users` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(50) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `full_name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(100) DEFAULT NULL,
  `role` ENUM('super_admin', 'content_manager') NOT NULL DEFAULT 'content_manager',
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `last_login` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_username` (`username`),
  INDEX `idx_role` (`role`),
  INDEX `idx_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Bảng: tvs
-- Quản lý thông tin các màn hình TV (7 màn hình)
-- =====================================================
CREATE TABLE `tvs` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `location` VARCHAR(100) NOT NULL,
  `folder` VARCHAR(100) NOT NULL COMMENT 'Tên thư mục chứa file hiển thị',
  `display_url` VARCHAR(500) DEFAULT NULL COMMENT 'URL để trình chiếu trên TV',
  `status` ENUM('online', 'offline') NOT NULL DEFAULT 'offline',
  `current_content_id` INT(11) DEFAULT NULL COMMENT 'ID nội dung đang chiếu',
  `default_content_id` INT(11) DEFAULT NULL COMMENT 'ID nội dung mặc định',
  `ip_address` VARCHAR(45) DEFAULT NULL COMMENT 'Địa chỉ IP của TV',
  `description` TEXT DEFAULT NULL,
  `last_heartbeat` DATETIME DEFAULT NULL COMMENT 'Lần gửi heartbeat cuối',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_folder` (`folder`),
  INDEX `idx_status` (`status`),
  INDEX `idx_folder` (`folder`),
  INDEX `idx_location` (`location`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Bảng: media
-- Quản lý thư viện nội dung (hình ảnh, video)
-- =====================================================
CREATE TABLE `media` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(200) NOT NULL,
  `type` ENUM('image', 'video') NOT NULL,
  `file_name` VARCHAR(255) NOT NULL,
  `file_path` VARCHAR(500) NOT NULL,
  `file_size` BIGINT DEFAULT NULL COMMENT 'Kích thước file (bytes)',
  `mime_type` VARCHAR(100) DEFAULT NULL,
  `thumbnail_path` VARCHAR(500) DEFAULT NULL,
  `duration` INT DEFAULT NULL COMMENT 'Thời lượng video (giây)',
  `width` INT DEFAULT NULL COMMENT 'Chiều rộng (px)',
  `height` INT DEFAULT NULL COMMENT 'Chiều cao (px)',
  `description` TEXT DEFAULT NULL,
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `uploaded_by` INT(11) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_type` (`type`),
  INDEX `idx_status` (`status`),
  INDEX `idx_uploaded_by` (`uploaded_by`),
  INDEX `idx_created_at` (`created_at`),
  FULLTEXT INDEX `idx_search` (`name`, `description`),
  FOREIGN KEY (`uploaded_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Bảng: schedules
-- Quản lý lịch chiếu nội dung trên các TV
-- =====================================================
CREATE TABLE `schedules` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `tv_id` INT(11) NOT NULL,
  `media_id` INT(11) NOT NULL,
  `schedule_date` DATE NOT NULL,
  `start_time` TIME NOT NULL,
  `end_time` TIME NOT NULL,
  `repeat_type` ENUM('none', 'daily', 'weekly', 'monthly') NOT NULL DEFAULT 'none',
  `repeat_until` DATE DEFAULT NULL COMMENT 'Ngày kết thúc lặp lại',
  `priority` INT DEFAULT 0 COMMENT 'Độ ưu tiên (số càng cao càng ưu tiên)',
  `status` ENUM('active', 'pending', 'completed', 'cancelled') NOT NULL DEFAULT 'pending',
  `note` TEXT DEFAULT NULL,
  `created_by` INT(11) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_tv_id` (`tv_id`),
  INDEX `idx_media_id` (`media_id`),
  INDEX `idx_schedule_date` (`schedule_date`),
  INDEX `idx_status` (`status`),
  INDEX `idx_datetime` (`schedule_date`, `start_time`, `end_time`),
  INDEX `idx_active_schedules` (`tv_id`, `status`, `schedule_date`, `start_time`, `end_time`),
  FOREIGN KEY (`tv_id`) REFERENCES `tvs`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`media_id`) REFERENCES `media`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Bảng: tv_media_assignments
-- Gán nội dung mặc định cho các TV
-- =====================================================
CREATE TABLE `tv_media_assignments` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `tv_id` INT(11) NOT NULL,
  `media_id` INT(11) NOT NULL,
  `is_default` TINYINT(1) DEFAULT 0 COMMENT '1 = nội dung mặc định',
  `display_order` INT DEFAULT 0 COMMENT 'Thứ tự hiển thị',
  `assigned_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `assigned_by` INT(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_tv_media` (`tv_id`, `media_id`),
  INDEX `idx_tv_id` (`tv_id`),
  INDEX `idx_media_id` (`media_id`),
  INDEX `idx_is_default` (`is_default`),
  FOREIGN KEY (`tv_id`) REFERENCES `tvs`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`media_id`) REFERENCES `media`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`assigned_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Bảng: activity_logs
-- Ghi lại các hoạt động trong hệ thống
-- =====================================================
CREATE TABLE `activity_logs` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `user_id` INT(11) DEFAULT NULL,
  `action` VARCHAR(100) NOT NULL COMMENT 'Loại hành động: login, upload, schedule, etc.',
  `entity_type` VARCHAR(50) DEFAULT NULL COMMENT 'Loại đối tượng: tv, media, schedule',
  `entity_id` INT(11) DEFAULT NULL COMMENT 'ID của đối tượng',
  `description` TEXT DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `user_agent` VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_user_id` (`user_id`),
  INDEX `idx_action` (`action`),
  INDEX `idx_entity` (`entity_type`, `entity_id`),
  INDEX `idx_created_at` (`created_at`),
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Bảng: system_settings
-- Cấu hình hệ thống
-- =====================================================
CREATE TABLE `system_settings` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `setting_key` VARCHAR(100) NOT NULL UNIQUE,
  `setting_value` TEXT DEFAULT NULL,
  `setting_type` ENUM('string', 'number', 'boolean', 'json') NOT NULL DEFAULT 'string',
  `description` TEXT DEFAULT NULL,
  `is_public` TINYINT(1) DEFAULT 0 COMMENT '1 = có thể xem công khai',
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_setting_key` (`setting_key`),
  INDEX `idx_setting_key` (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Bảng: tv_heartbeats
-- Lưu lịch sử heartbeat của các TV
-- =====================================================
CREATE TABLE `tv_heartbeats` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `tv_id` INT(11) NOT NULL,
  `status` ENUM('online', 'offline') NOT NULL,
  `current_media_id` INT(11) DEFAULT NULL,
  `user_agent` VARCHAR(255) DEFAULT NULL,
  `ip_address` VARCHAR(45) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_tv_id` (`tv_id`),
  INDEX `idx_created_at` (`created_at`),
  FOREIGN KEY (`tv_id`) REFERENCES `tvs`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- DỮ LIỆU MẪU (Sample Data)
-- =====================================================

-- Thêm tài khoản người dùng
-- Password mặc định: admin123 (đã mã hóa bằng password_hash)
INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `email`, `role`, `status`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin@quanglonghotel.com', 'super_admin', 'active'),
(2, 'manager', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Content Manager', 'manager@quanglonghotel.com', 'content_manager', 'active');

-- Thêm 7 màn hình TV
INSERT INTO `tvs` (`id`, `name`, `location`, `folder`, `display_url`, `status`, `description`) VALUES
(1, 'TV Basement', 'Tầng hầm', 'basement', 'basement/index.php', 'online', 'TV tại khu vực tầng hầm - Hiển thị thông tin chào mừng'),
(2, 'TV Chrysan', 'Phòng Chrysan', 'chrysan', 'chrysan/index.php', 'online', 'TV tại phòng hội nghị Chrysan'),
(3, 'TV Jasmine', 'Phòng Jasmine', 'jasmine', 'jasmine/index.php', 'online', 'TV tại phòng hội nghị Jasmine'),
(4, 'TV Lotus', 'Phòng Lotus', 'lotus', 'lotus/index.php', 'online', 'TV tại phòng hội nghị Lotus'),
(5, 'TV Restaurant', 'Nhà hàng', 'restaurant', 'restaurant/index.php', 'online', 'TV tại nhà hàng - Hiển thị menu và khuyến mãi'),
(6, 'TV FO 1', 'Lễ tân 1', 'fo/tv1', 'fo/tv1/index.php', 'online', 'TV tại quầy lễ tân số 1'),
(7, 'TV FO 2', 'Lễ tân 2', 'fo/tv2', 'fo/tv2/index.php', 'online', 'TV tại quầy lễ tân số 2');

-- Thêm nội dung mẫu
INSERT INTO `media` (`id`, `name`, `type`, `file_name`, `file_path`, `file_size`, `mime_type`, `description`, `status`, `uploaded_by`) VALUES
(1, 'Welcome Banner 01', 'image', 'welcome_banner_01.jpg', 'uploads/welcome_banner_01.jpg', 524288, 'image/jpeg', 'Banner chào mừng khách hàng phiên bản 1', 'active', 1),
(2, 'Hotel Promo Video', 'video', 'hotel_promo.mp4', 'uploads/hotel_promo.mp4', 10485760, 'video/mp4', 'Video quảng cáo giới thiệu khách sạn', 'active', 1),
(3, 'Menu Display', 'image', 'menu_display.jpg', 'uploads/menu_display.jpg', 786432, 'image/jpeg', 'Thực đơn nhà hàng - Cập nhật tháng hiện tại', 'active', 1),
(4, 'Welcome Banner 02', 'image', 'welcome_banner_02.jpg', 'uploads/welcome_banner_02.jpg', 614400, 'image/jpeg', 'Banner chào mừng phiên bản 2 - Thiết kế mới', 'active', 1),
(5, 'Lotus Room Display', 'image', 'lotus_display.jpg', 'uploads/lotus_display.jpg', 921600, 'image/jpeg', 'Hình ảnh giới thiệu phòng Lotus', 'active', 1),
(6, 'Welcome Video', 'video', 'welcome_video.mp4', 'uploads/welcome_video.mp4', 15728640, 'video/mp4', 'Video chào mừng khách đến khách sạn', 'active', 1),
(7, 'Chrysan Room Info', 'image', 'chrysan_info.jpg', 'uploads/chrysan_info.jpg', 716800, 'image/jpeg', 'Thông tin phòng hội nghị Chrysan', 'active', 1),
(8, 'Jasmine Room Info', 'image', 'jasmine_info.jpg', 'uploads/jasmine_info.jpg', 819200, 'image/jpeg', 'Thông tin phòng hội nghị Jasmine', 'active', 1);

-- Gán nội dung mặc định cho các TV
INSERT INTO `tv_media_assignments` (`tv_id`, `media_id`, `is_default`, `display_order`, `assigned_by`) VALUES
(1, 1, 1, 1, 1), -- TV Basement -> Welcome Banner 01 (default)
(2, 7, 1, 1, 1), -- TV Chrysan -> Chrysan Room Info (default)
(2, 2, 0, 2, 1), -- TV Chrysan -> Hotel Promo Video
(3, 8, 1, 1, 1), -- TV Jasmine -> Jasmine Room Info (default)
(3, 2, 0, 2, 1), -- TV Jasmine -> Hotel Promo Video
(4, 5, 1, 1, 1), -- TV Lotus -> Lotus Room Display (default)
(4, 2, 0, 2, 1), -- TV Lotus -> Hotel Promo Video
(5, 3, 1, 1, 1), -- TV Restaurant -> Menu Display (default)
(6, 6, 1, 1, 1), -- TV FO 1 -> Welcome Video (default)
(6, 1, 0, 2, 1), -- TV FO 1 -> Welcome Banner 01
(7, 4, 1, 1, 1), -- TV FO 2 -> Welcome Banner 02 (default)
(7, 6, 0, 2, 1); -- TV FO 2 -> Welcome Video

-- Cập nhật default_content_id cho các TV
UPDATE `tvs` SET `default_content_id` = 1, `current_content_id` = 1 WHERE `id` = 1;
UPDATE `tvs` SET `default_content_id` = 7, `current_content_id` = 7 WHERE `id` = 2;
UPDATE `tvs` SET `default_content_id` = 8, `current_content_id` = 8 WHERE `id` = 3;
UPDATE `tvs` SET `default_content_id` = 5, `current_content_id` = 5 WHERE `id` = 4;
UPDATE `tvs` SET `default_content_id` = 3, `current_content_id` = 3 WHERE `id` = 5;
UPDATE `tvs` SET `default_content_id` = 6, `current_content_id` = 6 WHERE `id` = 6;
UPDATE `tvs` SET `default_content_id` = 4, `current_content_id` = 4 WHERE `id` = 7;

-- Thêm lịch chiếu mẫu
INSERT INTO `schedules` (`tv_id`, `media_id`, `schedule_date`, `start_time`, `end_time`, `repeat_type`, `status`, `priority`, `note`, `created_by`) VALUES
(1, 1, CURDATE(), '08:00:00', '22:00:00', 'daily', 'active', 1, 'Lịch chiếu hàng ngày cho TV Basement', 1),
(2, 7, CURDATE(), '08:00:00', '18:00:00', 'daily', 'active', 1, 'Hiển thị thông tin phòng Chrysan', 1),
(3, 8, CURDATE(), '08:00:00', '18:00:00', 'daily', 'active', 1, 'Hiển thị thông tin phòng Jasmine', 1),
(4, 5, CURDATE(), '08:00:00', '18:00:00', 'daily', 'active', 1, 'Hiển thị thông tin phòng Lotus', 1),
(5, 3, CURDATE(), '11:00:00', '22:00:00', 'daily', 'active', 1, 'Hiển thị menu nhà hàng', 1),
(6, 6, CURDATE(), '06:00:00', '23:00:00', 'daily', 'active', 1, 'Video chào mừng tại lễ tân 1', 1),
(7, 4, CURDATE(), '06:00:00', '23:00:00', 'daily', 'active', 1, 'Banner chào mừng tại lễ tân 2', 1);

-- Thêm cấu hình hệ thống
INSERT INTO `system_settings` (`setting_key`, `setting_value`, `setting_type`, `description`, `is_public`) VALUES
('site_name', 'Quang Long Hotel - Welcome Board System', 'string', 'Tên hệ thống', 1),
('hotel_name', 'Quang Long Hotel', 'string', 'Tên khách sạn', 1),
('heartbeat_interval', '60', 'number', 'Khoảng thời gian gửi heartbeat (giây)', 0),
('offline_threshold', '300', 'number', 'Thời gian tối đa không nhận heartbeat để đánh dấu offline (giây)', 0),
('max_upload_size', '52428800', 'number', 'Kích thước file tối đa cho phép upload (bytes) - 50MB', 0),
('allowed_image_types', 'jpg,jpeg,png,gif,webp', 'string', 'Các định dạng hình ảnh được phép', 0),
('allowed_video_types', 'mp4,webm,avi,mov', 'string', 'Các định dạng video được phép', 0),
('auto_refresh_interval', '30', 'number', 'Khoảng thời gian tự động làm mới màn hình TV (giây)', 1),
('default_transition', 'fade', 'string', 'Hiệu ứng chuyển cảnh mặc định', 1),
('transition_duration', '1', 'number', 'Thời gian chuyển cảnh (giây)', 1),
('enable_logging', 'true', 'boolean', 'Bật/tắt ghi log hoạt động', 0),
('timezone', 'Asia/Ho_Chi_Minh', 'string', 'Múi giờ hệ thống', 0),
('language', 'vi', 'string', 'Ngôn ngữ mặc định', 1);

-- Thêm log hoạt động mẫu
INSERT INTO `activity_logs` (`user_id`, `action`, `entity_type`, `entity_id`, `description`, `ip_address`) VALUES
(1, 'login', NULL, NULL, 'Admin đăng nhập hệ thống', '192.168.1.100'),
(1, 'upload', 'media', 1, 'Upload file: Welcome Banner 01', '192.168.1.100'),
(1, 'upload', 'media', 2, 'Upload file: Hotel Promo Video', '192.168.1.100'),
(1, 'create', 'schedule', 1, 'Tạo lịch chiếu cho TV Basement', '192.168.1.100'),
(1, 'assign', 'tv_media', 1, 'Gán nội dung mặc định cho TV Basement', '192.168.1.100'),
(2, 'login', NULL, NULL, 'Content Manager đăng nhập hệ thống', '192.168.1.101'),
(2, 'upload', 'media', 3, 'Upload file: Menu Display', '192.168.1.101');

-- =====================================================
-- VIEWS (Các view hữu ích)
-- =====================================================

-- View: Thông tin TV với nội dung hiện tại
CREATE OR REPLACE VIEW `view_tv_status` AS
SELECT 
    t.id,
    t.name,
    t.location,
    t.folder,
    t.display_url,
    t.status,
    t.ip_address,
    t.last_heartbeat,
    t.description,
    m.id AS current_content_id,
    m.name AS current_content_name,
    m.type AS current_content_type,
    m.file_path AS current_content_path,
    dm.id AS default_content_id,
    dm.name AS default_content_name,
    dm.type AS default_content_type,
    dm.file_path AS default_content_path,
    TIMESTAMPDIFF(SECOND, t.last_heartbeat, NOW()) AS seconds_since_heartbeat,
    CASE 
        WHEN TIMESTAMPDIFF(SECOND, t.last_heartbeat, NOW()) > 300 THEN 'offline'
        ELSE t.status
    END AS actual_status
FROM `tvs` t
LEFT JOIN `media` m ON t.current_content_id = m.id
LEFT JOIN `media` dm ON t.default_content_id = dm.id;

-- View: Lịch chiếu đang hoạt động
CREATE OR REPLACE VIEW `view_active_schedules` AS
SELECT 
    s.id,
    s.schedule_date,
    s.start_time,
    s.end_time,
    s.repeat_type,
    s.repeat_until,
    s.priority,
    s.status,
    s.note,
    t.id AS tv_id,
    t.name AS tv_name,
    t.location AS tv_location,
    t.folder AS tv_folder,
    m.id AS media_id,
    m.name AS media_name,
    m.type AS media_type,
    m.file_path AS media_path,
    m.thumbnail_path AS media_thumbnail,
    u.full_name AS created_by_name,
    s.created_at
FROM `schedules` s
INNER JOIN `tvs` t ON s.tv_id = t.id
INNER JOIN `media` m ON s.media_id = m.id
LEFT JOIN `users` u ON s.created_by = u.id
WHERE s.status IN ('active', 'pending')
ORDER BY s.schedule_date DESC, s.start_time DESC;

-- View: Thống kê media
CREATE OR REPLACE VIEW `view_media_stats` AS
SELECT 
    m.id,
    m.name,
    m.type,
    m.file_name,
    m.file_path,
    m.file_size,
    m.status,
    m.created_at,
    COUNT(DISTINCT tma.tv_id) AS assigned_tv_count,
    COUNT(DISTINCT s.id) AS schedule_count,
    GROUP_CONCAT(DISTINCT t.name SEPARATOR ', ') AS assigned_to_tvs,
    u.full_name AS uploaded_by_name
FROM `media` m
LEFT JOIN `tv_media_assignments` tma ON m.id = tma.media_id
LEFT JOIN `tvs` t ON tma.tv_id = t.id
LEFT JOIN `schedules` s ON m.id = s.media_id AND s.status IN ('active', 'pending')
LEFT JOIN `users` u ON m.uploaded_by = u.id
GROUP BY m.id;

-- View: Dashboard statistics
CREATE OR REPLACE VIEW `view_dashboard_stats` AS
SELECT 
    (SELECT COUNT(*) FROM tvs) AS total_tvs,
    (SELECT COUNT(*) FROM tvs WHERE status = 'online') AS online_tvs,
    (SELECT COUNT(*) FROM tvs WHERE status = 'offline') AS offline_tvs,
    (SELECT COUNT(*) FROM media WHERE status = 'active') AS total_media,
    (SELECT COUNT(*) FROM media WHERE type = 'image' AND status = 'active') AS total_images,
    (SELECT COUNT(*) FROM media WHERE type = 'video' AND status = 'active') AS total_videos,
    (SELECT COUNT(*) FROM schedules WHERE status = 'active') AS active_schedules,
    (SELECT COUNT(*) FROM schedules WHERE status = 'pending') AS pending_schedules,
    (SELECT COUNT(*) FROM schedules WHERE schedule_date = CURDATE() AND status IN ('active', 'pending')) AS today_schedules;

-- =====================================================
-- STORED PROCEDURES
-- =====================================================

-- Procedure: Cập nhật trạng thái TV dựa trên heartbeat
DELIMITER $$
CREATE PROCEDURE `sp_update_tv_status`()
BEGIN
    DECLARE offline_threshold INT DEFAULT 300;
    
    -- Lấy ngưỡng offline từ settings
    SELECT CAST(setting_value AS UNSIGNED) INTO offline_threshold
    FROM system_settings 
    WHERE setting_key = 'offline_threshold'
    LIMIT 1;
    
    -- Cập nhật trạng thái offline cho các TV không gửi heartbeat
    UPDATE tvs 
    SET status = 'offline'
    WHERE TIMESTAMPDIFF(SECOND, last_heartbeat, NOW()) > offline_threshold
    AND status = 'online'
    AND last_heartbeat IS NOT NULL;
    
    -- Cập nhật trạng thái online cho các TV vừa gửi heartbeat
    UPDATE tvs 
    SET status = 'online'
    WHERE TIMESTAMPDIFF(SECOND, last_heartbeat, NOW()) <= offline_threshold
    AND status = 'offline'
    AND last_heartbeat IS NOT NULL;
END$$
DELIMITER ;

-- Procedure: Lấy nội dung cần hiển thị cho TV
DELIMITER $$
CREATE PROCEDURE `sp_get_tv_content`(IN tv_id_param INT)
BEGIN
    DECLARE current_media_id INT DEFAULT NULL;
    DECLARE default_media_id INT DEFAULT NULL;
    
    -- Kiểm tra lịch chiếu hiện tại (ưu tiên theo priority)
    SELECT s.media_id INTO current_media_id
    FROM schedules s
    WHERE s.tv_id = tv_id_param
    AND s.status = 'active'
    AND s.schedule_date = CURDATE()
    AND CURTIME() BETWEEN s.start_time AND s.end_time
    ORDER BY s.priority DESC, s.id DESC
    LIMIT 1;
    
    -- Nếu không có lịch chiếu, lấy nội dung mặc định
    IF current_media_id IS NULL THEN
        SELECT default_content_id INTO default_media_id
        FROM tvs
        WHERE id = tv_id_param;
        
        SET current_media_id = default_media_id;
    END IF;
    
    -- Trả về thông tin media
    SELECT 
        m.id,
        m.name,
        m.type,
        m.file_path,
        m.thumbnail_path,
        m.duration,
        m.width,
        m.height,
        'scheduled' AS source_type
    FROM media m
    WHERE m.id = current_media_id
    AND m.status = 'active';
    
    -- Cập nhật current_content_id cho TV
    UPDATE tvs 
    SET current_content_id = current_media_id,
        last_heartbeat = NOW()
    WHERE id = tv_id_param;
END$$
DELIMITER ;

-- Procedure: Cập nhật trạng thái lịch chiếu
DELIMITER $$
CREATE PROCEDURE `sp_update_schedule_status`()
BEGIN
    -- Đánh dấu completed cho các lịch đã qua (không lặp lại)
    UPDATE schedules
    SET status = 'completed'
    WHERE status = 'active'
    AND (
        (schedule_date < CURDATE())
        OR (schedule_date = CURDATE() AND end_time < CURTIME())
    )
    AND repeat_type = 'none';
    
    -- Kích hoạt các lịch pending đã đến giờ
    UPDATE schedules
    SET status = 'active'
    WHERE status = 'pending'
    AND schedule_date <= CURDATE()
    AND (repeat_until IS NULL OR repeat_until >= CURDATE());
    
    -- Hủy các lịch lặp lại đã hết hạn
    UPDATE schedules
    SET status = 'completed'
    WHERE status IN ('active', 'pending')
    AND repeat_type != 'none'
    AND repeat_until IS NOT NULL
    AND repeat_until < CURDATE();
END$$
DELIMITER ;

-- Procedure: Ghi heartbeat cho TV
DELIMITER $$
CREATE PROCEDURE `sp_record_heartbeat`(
    IN tv_id_param INT,
    IN status_param VARCHAR(10),
    IN media_id_param INT,
    IN ip_param VARCHAR(45),
    IN user_agent_param VARCHAR(255)
)
BEGIN
    -- Cập nhật thông tin TV
    UPDATE tvs
    SET status = status_param,
        current_content_id = media_id_param,
        last_heartbeat = NOW(),
        ip_address = ip_param
    WHERE id = tv_id_param;
    
    -- Ghi log heartbeat
    INSERT INTO tv_heartbeats (tv_id, status, current_media_id, ip_address, user_agent)
    VALUES (tv_id_param, status_param, media_id_param, ip_param, user_agent_param);
END$$
DELIMITER ;

-- Procedure: Lấy thống kê dashboard
DELIMITER $$
CREATE PROCEDURE `sp_get_dashboard_stats`()
BEGIN
    SELECT * FROM view_dashboard_stats;
    
    -- Lấy hoạt động gần đây
    SELECT 
        al.id,
        al.action,
        al.description,
        al.created_at,
        u.full_name AS user_name,
        u.username
    FROM activity_logs al
    LEFT JOIN users u ON al.user_id = u.id
    ORDER BY al.created_at DESC
    LIMIT 10;
END$$
DELIMITER ;

-- =====================================================
-- TRIGGERS
-- =====================================================

-- Trigger: Ghi log khi thêm media mới
DELIMITER $$
CREATE TRIGGER `trg_media_after_insert`
AFTER INSERT ON `media`
FOR EACH ROW
BEGIN
    INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description)
    VALUES (NEW.uploaded_by, 'upload', 'media', NEW.id, CONCAT('Upload media: ', NEW.name));
END$$
DELIMITER ;

-- Trigger: Ghi log khi xóa media
DELIMITER $$
CREATE TRIGGER `trg_media_before_delete`
BEFORE DELETE ON `media`
FOR EACH ROW
BEGIN
    INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description)
    VALUES (NULL, 'delete', 'media', OLD.id, CONCAT('Delete media: ', OLD.name));
END$$
DELIMITER ;

-- Trigger: Ghi log khi tạo lịch chiếu
DELIMITER $$
CREATE TRIGGER `trg_schedule_after_insert`
AFTER INSERT ON `schedules`
FOR EACH ROW
BEGIN
    DECLARE tv_name_var VARCHAR(100);
    DECLARE media_name_var VARCHAR(200);
    
    SELECT name INTO tv_name_var FROM tvs WHERE id = NEW.tv_id;
    SELECT name INTO media_name_var FROM media WHERE id = NEW.media_id;
    
    INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description)
    VALUES (NEW.created_by, 'create', 'schedule', NEW.id, 
            CONCAT('Tạo lịch chiếu "', media_name_var, '" cho ', tv_name_var));
END$$
DELIMITER ;

-- Trigger: Ghi log khi cập nhật lịch chiếu
DELIMITER $$
CREATE TRIGGER `trg_schedule_after_update`
AFTER UPDATE ON `schedules`
FOR EACH ROW
BEGIN
    DECLARE tv_name_var VARCHAR(100);
    DECLARE media_name_var VARCHAR(200);
    DECLARE change_desc VARCHAR(500);
    
    SELECT name INTO tv_name_var FROM tvs WHERE id = NEW.tv_id;
    SELECT name INTO media_name_var FROM media WHERE id = NEW.media_id;
    
    IF OLD.status != NEW.status THEN
        SET change_desc = CONCAT('Cập nhật trạng thái lịch chiếu "', media_name_var, '" cho ', tv_name_var, ' từ ', OLD.status, ' sang ', NEW.status);
    ELSE
        SET change_desc = CONCAT('Cập nhật lịch chiếu "', media_name_var, '" cho ', tv_name_var);
    END IF;
    
    INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description)
    VALUES (NULL, 'update', 'schedule', NEW.id, change_desc);
END$$
DELIMITER ;

-- Trigger: Ghi log khi xóa lịch chiếu
DELIMITER $$
CREATE TRIGGER `trg_schedule_before_delete`
BEFORE DELETE ON `schedules`
FOR EACH ROW
BEGIN
    DECLARE tv_name_var VARCHAR(100);
    DECLARE media_name_var VARCHAR(200);
    
    SELECT name INTO tv_name_var FROM tvs WHERE id = OLD.tv_id;
    SELECT name INTO media_name_var FROM media WHERE id = OLD.media_id;
    
    INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description)
    VALUES (NULL, 'delete', 'schedule', OLD.id, 
            CONCAT('Xóa lịch chiếu "', media_name_var, '" cho ', tv_name_var));
END$$
DELIMITER ;

-- Trigger: Cập nhật TV khi gán nội dung mặc định
DELIMITER $$
CREATE TRIGGER `trg_tv_media_after_insert`
AFTER INSERT ON `tv_media_assignments`
FOR EACH ROW
BEGIN
    -- Nếu là nội dung mặc định, cập nhật TV
    IF NEW.is_default = 1 THEN
        -- Bỏ default của các assignment khác cho TV này
        UPDATE tv_media_assignments
        SET is_default = 0
        WHERE tv_id = NEW.tv_id
        AND id != NEW.id;
        
        -- Cập nhật default_content_id cho TV
        UPDATE tvs
        SET default_content_id = NEW.media_id
        WHERE id = NEW.tv_id;
    END IF;
END$$
DELIMITER ;

-- =====================================================
-- EVENTS (Tự động chạy định kỳ)
-- =====================================================

-- Bật event scheduler
SET GLOBAL event_scheduler = ON;

-- Event: Cập nhật trạng thái TV mỗi phút
CREATE EVENT IF NOT EXISTS `evt_update_tv_status`
ON SCHEDULE EVERY 1 MINUTE
DO CALL sp_update_tv_status();

-- Event: Cập nhật trạng thái lịch chiếu mỗi phút
CREATE EVENT IF NOT EXISTS `evt_update_schedule_status`
ON SCHEDULE EVERY 1 MINUTE
DO CALL sp_update_schedule_status();

-- Event: Xóa log cũ hơn 90 ngày (chạy hàng ngày lúc 2h sáng)
CREATE EVENT IF NOT EXISTS `evt_cleanup_old_logs`
ON SCHEDULE EVERY 1 DAY
STARTS (TIMESTAMP(CURRENT_DATE) + INTERVAL 2 HOUR)
DO DELETE FROM activity_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY);

-- Event: Xóa heartbeat cũ hơn 7 ngày (chạy hàng ngày lúc 3h sáng)
CREATE EVENT IF NOT EXISTS `evt_cleanup_old_heartbeats`
ON SCHEDULE EVERY 1 DAY
STARTS (TIMESTAMP(CURRENT_DATE) + INTERVAL 3 HOUR)
DO DELETE FROM tv_heartbeats WHERE created_at < DATE_SUB(NOW(), INTERVAL 7 DAY);

-- =====================================================
-- INDEXES BỔ SUNG (Tối ưu hiệu suất)
-- =====================================================

-- Composite index cho tìm kiếm lịch chiếu theo thời gian
CREATE INDEX idx_schedule_time_range ON schedules(tv_id, status, schedule_date, start_time, end_time);

-- Index cho tìm kiếm theo repeat_type
CREATE INDEX idx_schedule_repeat ON schedules(repeat_type, repeat_until, status);

-- Index cho activity logs theo thời gian và action
CREATE INDEX idx_logs_time_action ON activity_logs(created_at DESC, action);

-- Index cho media theo upload time
CREATE INDEX idx_media_upload_time ON media(uploaded_by, created_at DESC);

-- =====================================================
-- FUNCTIONS (Hàm tiện ích)
-- =====================================================

-- Function: Lấy tên TV theo ID
DELIMITER $$
CREATE FUNCTION `fn_get_tv_name`(tv_id_param INT)
RETURNS VARCHAR(100)
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE tv_name_result VARCHAR(100);
    SELECT name INTO tv_name_result FROM tvs WHERE id = tv_id_param;
    RETURN IFNULL(tv_name_result, 'Unknown TV');
END$$
DELIMITER ;

-- Function: Lấy tên media theo ID
DELIMITER $$
CREATE FUNCTION `fn_get_media_name`(media_id_param INT)
RETURNS VARCHAR(200)
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE media_name_result VARCHAR(200);
    SELECT name INTO media_name_result FROM media WHERE id = media_id_param;
    RETURN IFNULL(media_name_result, 'Unknown Media');
END$$
DELIMITER ;

-- Function: Kiểm tra TV có online không
DELIMITER $$
CREATE FUNCTION `fn_is_tv_online`(tv_id_param INT)
RETURNS BOOLEAN
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE is_online BOOLEAN DEFAULT FALSE;
    DECLARE last_beat DATETIME;
    DECLARE offline_threshold INT DEFAULT 300;
    
    SELECT last_heartbeat INTO last_beat FROM tvs WHERE id = tv_id_param;
    
    IF last_beat IS NOT NULL AND TIMESTAMPDIFF(SECOND, last_beat, NOW()) <= offline_threshold THEN
        SET is_online = TRUE;
    END IF;
    
    RETURN is_online;
END$$
DELIMITER ;

-- Function: Đếm số lịch chiếu active của TV
DELIMITER $$
CREATE FUNCTION `fn_count_active_schedules`(tv_id_param INT)
RETURNS INT
DETERMINISTIC
READS SQL DATA
BEGIN
    DECLARE schedule_count INT DEFAULT 0;
    
    SELECT COUNT(*) INTO schedule_count
    FROM schedules
    WHERE tv_id = tv_id_param
    AND status = 'active';
    
    RETURN schedule_count;
END$$
DELIMITER ;

-- =====================================================
-- PERMISSIONS & SECURITY
-- =====================================================

-- Tạo user cho ứng dụng (nếu cần)
-- CREATE USER IF NOT EXISTS 'wcb_app'@'localhost' IDENTIFIED BY 'secure_password_here';
-- GRANT SELECT, INSERT, UPDATE, DELETE ON auroraho_wcb.* TO 'wcb_app'@'localhost';
-- FLUSH PRIVILEGES;

-- =====================================================
-- INITIAL DATA VERIFICATION
-- =====================================================

-- Kiểm tra dữ liệu đã import
SELECT 'Database created successfully!' AS status;
SELECT CONCAT('Total tables: ', COUNT(*)) AS info FROM information_schema.tables WHERE table_schema = 'auroraho_wcb';
SELECT CONCAT('Total users: ', COUNT(*)) AS info FROM users;
SELECT CONCAT('Total TVs: ', COUNT(*)) AS info FROM tvs;
SELECT CONCAT('Total media: ', COUNT(*)) AS info FROM media;
SELECT CONCAT('Total schedules: ', COUNT(*)) AS info FROM schedules;
SELECT CONCAT('Total settings: ', COUNT(*)) AS info FROM system_settings;

-- =====================================================
-- NOTES & DOCUMENTATION
-- =====================================================

/*
HƯỚNG DẪN SỬ DỤNG:

1. IMPORT DATABASE:
   - Tạo database: CREATE DATABASE auroraho_wcb;
   - Import file này: mysql -u root -p auroraho_wcb < database.sql
   - Hoặc dùng phpMyAdmin để import

2. TÀI KHOẢN MẶC ĐỊNH:
   - Super Admin: admin / admin123
   - Content Manager: manager / admin123
   ⚠️ ĐỔI MẬT KHẨU NGAY SAU KHI ĐĂNG NHẬP!

3. CẤU TRÚC 7 TV:
   - TV Basement (Tầng hầm)
   - TV Chrysan (Phòng Chrysan)
   - TV Jasmine (Phòng Jasmine)
   - TV Lotus (Phòng Lotus)
   - TV Restaurant (Nhà hàng)
   - TV FO 1 (Lễ tân 1)
   - TV FO 2 (Lễ tân 2)

4. TÍNH NĂNG TỰ ĐỘNG:
   - Cập nhật trạng thái TV mỗi phút
   - Cập nhật trạng thái lịch chiếu mỗi phút
   - Xóa log cũ hơn 90 ngày
   - Xóa heartbeat cũ hơn 7 ngày

5. STORED PROCEDURES:
   - sp_update_tv_status(): Cập nhật trạng thái TV
   - sp_get_tv_content(tv_id): Lấy nội dung cho TV
   - sp_update_schedule_status(): Cập nhật trạng thái lịch
   - sp_record_heartbeat(): Ghi heartbeat
   - sp_get_dashboard_stats(): Lấy thống kê dashboard

6. VIEWS:
   - view_tv_status: Trạng thái TV với nội dung
   - view_active_schedules: Lịch chiếu đang hoạt động
   - view_media_stats: Thống kê media
   - view_dashboard_stats: Thống kê tổng quan

7. BẢO MẬT:
   - Sử dụng prepared statements
   - Password được hash bằng bcrypt
   - Session security enabled
   - Activity logging enabled

8. BACKUP:
   - Nên backup database hàng ngày
   - Lưu trữ backup ít nhất 30 ngày
   - Test restore định kỳ

9. MONITORING:
   - Kiểm tra event scheduler: SHOW EVENTS;
   - Kiểm tra triggers: SHOW TRIGGERS;
   - Kiểm tra procedures: SHOW PROCEDURE STATUS WHERE Db = 'auroraho_wcb';

10. TROUBLESHOOTING:
    - Nếu events không chạy: SET GLOBAL event_scheduler = ON;
    - Nếu lỗi timezone: SET time_zone = '+07:00';
    - Kiểm tra logs: SELECT * FROM activity_logs ORDER BY created_at DESC LIMIT 50;

Liên hệ hỗ trợ: admin@quanglonghotel.com
*/

-- =====================================================
-- END OF DATABASE SCRIPT
-- =====================================================
