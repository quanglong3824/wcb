-- Migration: Thêm hỗ trợ audio và slideshow
-- Date: 2026-01-28

-- Cập nhật bảng media để hỗ trợ audio
ALTER TABLE `media` 
MODIFY COLUMN `type` ENUM('image', 'video', 'audio', 'slideshow') NOT NULL;

-- Tạo bảng mới cho slideshow configurations
CREATE TABLE IF NOT EXISTS `slideshows` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(200) NOT NULL,
  `description` TEXT DEFAULT NULL,
  `transition_duration` INT(11) NOT NULL DEFAULT 5 COMMENT 'Thời gian hiển thị mỗi ảnh (giây)',
  `total_duration` INT(11) NOT NULL DEFAULT 0 COMMENT 'Tổng thời gian trình chiếu (giây)',
  `audio_id` INT(11) DEFAULT NULL COMMENT 'ID của file nhạc nền',
  `fade_out_duration` INT(11) NOT NULL DEFAULT 3 COMMENT 'Thời gian fade out nhạc (giây)',
  `status` ENUM('active', 'inactive') NOT NULL DEFAULT 'active',
  `created_by` INT(11) DEFAULT NULL,
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  `updated_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  KEY `idx_status` (`status`),
  KEY `idx_created_by` (`created_by`),
  KEY `fk_audio_id` (`audio_id`),
  CONSTRAINT `slideshows_ibfk_1` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `slideshows_ibfk_2` FOREIGN KEY (`audio_id`) REFERENCES `media` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo bảng liên kết giữa slideshow và images
CREATE TABLE IF NOT EXISTS `slideshow_images` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `slideshow_id` INT(11) NOT NULL,
  `media_id` INT(11) NOT NULL COMMENT 'ID của image',
  `display_order` INT(11) NOT NULL DEFAULT 0 COMMENT 'Thứ tự hiển thị',
  `custom_duration` INT(11) DEFAULT NULL COMMENT 'Thời gian hiển thị riêng (giây), NULL = dùng mặc định',
  `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP(),
  PRIMARY KEY (`id`),
  KEY `idx_slideshow_id` (`slideshow_id`),
  KEY `idx_media_id` (`media_id`),
  KEY `idx_display_order` (`slideshow_id`, `display_order`),
  CONSTRAINT `slideshow_images_ibfk_1` FOREIGN KEY (`slideshow_id`) REFERENCES `slideshows` (`id`) ON DELETE CASCADE,
  CONSTRAINT `slideshow_images_ibfk_2` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Thêm index cho media type audio
ALTER TABLE `media` ADD INDEX `idx_media_type_audio` (`type`);
