-- =====================================================
-- Database: auroraho_wcb
-- Welcome Board System - Aurora Hotel
-- Hệ thống quản lý nội dung hiển thị trên màn hình TV
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
  INDEX `idx_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================
-- Bảng: tvs
-- Quản lý thông tin các màn hình TV
-- =====================================================
CREATE TABLE `tvs` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(100) NOT NULL,
  `location` VARCHAR(100) NOT NULL,
  `folder` VARCHAR(100) NOT NULL,
  `display_url` VARCHAR(500) DEFAULT NULL COMMENT 'URL để trình chiếu trên TV',
  `status` ENUM('online', 'offline') NOT NULL DEFAULT 'offline',
  `current_content_id` INT(11) DEFAULT NULL,
  `default_content_id` INT(11) DEFAULT NULL,
  `description` TEXT DEFAULT NULL,
  `last_heartbeat` DATETIME DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_status` (`status`),
  INDEX `idx_folder` (`folder`)
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
  `thumbnail_path` VARCHAR(500) DEFAULT NULL,
  `duration` INT DEFAULT NULL COMMENT 'Thời lượng video (giây)',
  `description` TEXT DEFAULT NULL,
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `uploaded_by` INT(11) DEFAULT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_type` (`type`),
  INDEX `idx_status` (`status`),
  INDEX `idx_uploaded_by` (`uploaded_by`),
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
  `assigned_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `assigned_by` INT(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_tv_media` (`tv_id`, `media_id`),
  INDEX `idx_tv_id` (`tv_id`),
  INDEX `idx_media_id` (`media_id`),
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
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_setting_key` (`setting_key`)
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
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  INDEX `idx_tv_id` (`tv_id`),
  INDEX `idx_created_at` (`created_at`),
  FOREIGN KEY (`tv_id`) REFERENCES `tvs`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =====================================================
-- DỮ LIỆU MẪU (Sample Data)
-- =====================================================

-- Thêm tài khoản Super Admin mặc định
-- Username: admin, Password: admin123 (đã mã hóa bằng password_hash)
INSERT INTO `users` (`username`, `password`, `full_name`, `email`, `role`, `status`) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Administrator', 'admin@auroraho.com', 'super_admin', 'active'),
('manager', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Content Manager', 'manager@auroraho.com', 'content_manager', 'active');

-- Thêm 7 màn hình TV
INSERT INTO `tvs` (`id`, `name`, `location`, `folder`, `display_url`, `status`, `description`) VALUES
(1, 'TV Basement', 'Tầng hầm', 'basement', 'basement/index.php', 'online', 'TV tại khu vực tầng hầm'),
(2, 'TV Chrysan', 'Phòng Chrysan', 'chrysan', 'chrysan/index.php', 'online', 'TV tại phòng Chrysan'),
(3, 'TV Jasmine', 'Phòng Jasmine', 'jasmine', 'jasmine/index.php', 'online', 'TV tại phòng Jasmine'),
(4, 'TV Lotus', 'Phòng Lotus', 'lotus', 'lotus/index.php', 'online', 'TV tại phòng Lotus'),
(5, 'TV Restaurant', 'Nhà hàng', 'restaurant', 'restaurant/index.php', 'online', 'TV tại nhà hàng'),
(6, 'TV FO 1', 'Lễ tân 1', 'fo/tv1', 'fo/tv1/index.php', 'online', 'TV tại quầy lễ tân 1'),
(7, 'TV FO 2', 'Lễ tân 2', 'fo/tv2', 'fo/tv2/index.php', 'online', 'TV tại quầy lễ tân 2');

-- Thêm nội dung mẫu
INSERT INTO `media` (`id`, `name`, `type`, `file_name`, `file_path`, `description`, `status`, `uploaded_by`) VALUES
(1, 'Welcome Banner 01', 'image', 'welcome_banner_01.jpg', 'uploads/welcome_banner_01.jpg', 'Banner chào mừng khách hàng', 'active', 1),
(2, 'Hotel Promo Video', 'video', 'hotel_promo.mp4', 'uploads/hotel_promo.mp4', 'Video quảng cáo khách sạn', 'active', 1),
(3, 'Menu Display', 'image', 'menu_display.jpg', 'uploads/menu_display.jpg', 'Thực đơn nhà hàng', 'active', 1),
(4, 'Welcome Banner 02', 'image', 'welcome_banner_02.jpg', 'uploads/welcome_banner_02.jpg', 'Banner chào mừng phiên bản 2', 'active', 1),
(5, 'Lotus Display', 'image', 'lotus_display.jpg', 'uploads/lotus_display.jpg', 'Hình ảnh phòng Lotus', 'active', 1),
(6, 'Welcome Video', 'video', 'welcome_video.mp4', 'uploads/welcome_video.mp4', 'Video chào mừng', 'active', 1);

-- Gán nội dung mặc định cho các TV
INSERT INTO `tv_media_assignments` (`tv_id`, `media_id`, `is_default`, `assigned_by`) VALUES
(1, 1, 1, 1), -- TV Basement -> Welcome Banner 01
(2, 2, 1, 1), -- TV Chrysan -> Hotel Promo Video
(3, 4, 1, 1), -- TV Jasmine -> Welcome Banner 02
(4, 5, 1, 1), -- TV Lotus -> Lotus Display
(5, 3, 1, 1), -- TV Restaurant -> Menu Display
(6, 6, 1, 1), -- TV FO 1 -> Welcome Video
(7, 1, 1, 1); -- TV FO 2 -> Welcome Banner 01

-- Cập nhật default_content_id cho các TV
UPDATE `tvs` SET `default_content_id` = 1 WHERE `id` = 1;
UPDATE `tvs` SET `default_content_id` = 2 WHERE `id` = 2;
UPDATE `tvs` SET `default_content_id` = 4 WHERE `id` = 3;
UPDATE `tvs` SET `default_content_id` = 5 WHERE `id` = 4;
UPDATE `tvs` SET `default_content_id` = 3 WHERE `id` = 5;
UPDATE `tvs` SET `default_content_id` = 6 WHERE `id` = 6;
UPDATE `tvs` SET `default_content_id` = 1 WHERE `id` = 7;

-- Thêm lịch chiếu mẫu
INSERT INTO `schedules` (`tv_id`, `media_id`, `schedule_date`, `start_time`, `end_time`, `repeat_type`, `status`, `created_by`) VALUES
(1, 1, '2024-01-20', '08:00:00', '18:00:00', 'daily', 'active', 1),
(2, 2, '2024-01-20', '09:00:00', '17:00:00', 'daily', 'active', 1),
(5, 3, '2024-01-20', '11:00:00', '22:00:00', 'daily', 'active', 1),
(3, 1, '2024-01-21', '08:00:00', '20:00:00', 'none', 'pending', 1);

-- Thêm cấu hình hệ thống
INSERT INTO `system_settings` (`setting_key`, `setting_value`, `setting_type`, `description`) VALUES
('site_name', 'Aurora Hotel - Welcome Board System', 'string', 'Tên hệ thống'),
('heartbeat_interval', '60', 'number', 'Khoảng thời gian gửi heartbeat (giây)'),
('offline_threshold', '300', 'number', 'Thời gian tối đa không nhận heartbeat để đánh dấu offline (giây)'),
('max_upload_size', '52428800', 'number', 'Kích thước file tối đa cho phép upload (bytes) - 50MB'),
('allowed_image_types', 'jpg,jpeg,png,gif', 'string', 'Các định dạng hình ảnh được phép'),
('allowed_video_types', 'mp4,avi,mov,wmv', 'string', 'Các định dạng video được phép'),
('auto_refresh_interval', '30', 'number', 'Khoảng thời gian tự động làm mới màn hình TV (giây)'),
('enable_logging', 'true', 'boolean', 'Bật/tắt ghi log hoạt động'),
('timezone', 'Asia/Ho_Chi_Minh', 'string', 'Múi giờ hệ thống');

-- Thêm log hoạt động mẫu
INSERT INTO `activity_logs` (`user_id`, `action`, `entity_type`, `entity_id`, `description`, `ip_address`) VALUES
(1, 'login', NULL, NULL, 'Admin đăng nhập hệ thống', '192.168.1.100'),
(1, 'upload', 'media', 1, 'Upload file: Welcome Banner 01', '192.168.1.100'),
(1, 'create', 'schedule', 1, 'Tạo lịch chiếu cho TV Basement', '192.168.1.100'),
(2, 'login', NULL, NULL, 'Content Manager đăng nhập hệ thống', '192.168.1.101');

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
    t.last_heartbeat,
    m.name AS current_content_name,
    m.type AS current_content_type,
    m.file_path AS current_content_path,
    dm.name AS default_content_name,
    dm.file_path AS default_content_path,
    TIMESTAMPDIFF(SECOND, t.last_heartbeat, NOW()) AS seconds_since_heartbeat
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
    s.status,
    t.id AS tv_id,
    t.name AS tv_name,
    t.location AS tv_location,
    m.id AS media_id,
    m.name AS media_name,
    m.type AS media_type,
    m.file_path AS media_path,
    u.full_name AS created_by_name
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
    m.status,
    m.created_at,
    COUNT(DISTINCT tma.tv_id) AS assigned_tv_count,
    COUNT(DISTINCT s.id) AS schedule_count,
    u.full_name AS uploaded_by_name
FROM `media` m
LEFT JOIN `tv_media_assignments` tma ON m.id = tma.media_id
LEFT JOIN `schedules` s ON m.id = s.media_id
LEFT JOIN `users` u ON m.uploaded_by = u.id
GROUP BY m.id;

-- =====================================================
-- STORED PROCEDURES
-- =====================================================

-- Procedure: Cập nhật trạng thái TV dựa trên heartbeat
DELIMITER $$
CREATE PROCEDURE `sp_update_tv_status`()
BEGIN
    DECLARE offline_threshold INT;
    
    -- Lấy ngưỡng offline từ settings
    SELECT CAST(setting_value AS UNSIGNED) INTO offline_threshold
    FROM system_settings 
    WHERE setting_key = 'offline_threshold';
    
    -- Cập nhật trạng thái offline cho các TV không gửi heartbeat
    UPDATE tvs 
    SET status = 'offline'
    WHERE TIMESTAMPDIFF(SECOND, last_heartbeat, NOW()) > offline_threshold
    AND status = 'online';
    
    -- Cập nhật trạng thái online cho các TV vừa gửi heartbeat
    UPDATE tvs 
    SET status = 'online'
    WHERE TIMESTAMPDIFF(SECOND, last_heartbeat, NOW()) <= offline_threshold
    AND status = 'offline';
END$$
DELIMITER ;

-- Procedure: Lấy nội dung cần hiển thị cho TV
DELIMITER $$
CREATE PROCEDURE `sp_get_tv_content`(IN tv_id_param INT)
BEGIN
    DECLARE current_media_id INT;
    DECLARE default_media_id INT;
    
    -- Kiểm tra lịch chiếu hiện tại
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
        m.duration
    FROM media m
    WHERE m.id = current_media_id
    AND m.status = 'active';
END$$
DELIMITER ;

-- Procedure: Cập nhật trạng thái lịch chiếu
DELIMITER $$
CREATE PROCEDURE `sp_update_schedule_status`()
BEGIN
    -- Đánh dấu completed cho các lịch đã qua
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
    AND schedule_date <= CURDATE();
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
CREATE INDEX idx_schedule_active ON schedules(tv_id, status, schedule_date, start_time, end_time);

-- Index cho tìm kiếm media theo tên
CREATE FULLTEXT INDEX idx_media_name_fulltext ON media(name, description);

-- =====================================================
-- HOÀN TẤT
-- =====================================================

-- Hiển thị thông báo
SELECT 'Database auroraho_wcb đã được tạo thành công!' AS message;
SELECT CONCAT('Tổng số bảng: ', COUNT(*)) AS total_tables 
FROM information_schema.tables 
WHERE table_schema = 'auroraho_wcb';

