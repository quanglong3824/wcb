-- Migration: Add notifications table
-- Date: 2024-01-20

-- Create notifications table
CREATE TABLE IF NOT EXISTS `notifications` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT NULL COMMENT 'NULL means broadcast to all users',
    `type` ENUM('info', 'success', 'warning', 'error', 'tv_offline', 'schedule', 'system') DEFAULT 'info',
    `title` VARCHAR(255) NOT NULL,
    `message` TEXT NOT NULL,
    `link` VARCHAR(500) NULL,
    `is_read` TINYINT(1) DEFAULT 0,
    `read_at` DATETIME NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_user_id` (`user_id`),
    INDEX `idx_is_read` (`is_read`),
    INDEX `idx_created_at` (`created_at`),
    FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Create tv_reload_signals table for remote TV control
CREATE TABLE IF NOT EXISTS `tv_reload_signals` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tv_id` INT NOT NULL,
    `signal_type` ENUM('reload', 'shutdown', 'change_content') DEFAULT 'reload',
    `payload` JSON NULL,
    `processed` TINYINT(1) DEFAULT 0,
    `processed_at` DATETIME NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `created_by` INT NULL,
    INDEX `idx_tv_id` (`tv_id`),
    INDEX `idx_processed` (`processed`),
    FOREIGN KEY (`tv_id`) REFERENCES `tvs`(`id`) ON DELETE CASCADE,
    FOREIGN KEY (`created_by`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Add display_order to tv_media_assignments if not exists
-- ALTER TABLE `tv_media_assignments` ADD COLUMN IF NOT EXISTS `display_order` INT DEFAULT 0;

-- Insert sample notifications
INSERT INTO `notifications` (`user_id`, `type`, `title`, `message`, `link`) VALUES
(NULL, 'info', 'Chào mừng đến WCB System', 'Hệ thống Welcome Board đã sẵn sàng hoạt động.', '/index.php'),
(NULL, 'success', 'Cập nhật hệ thống', 'Phiên bản mới đã được cài đặt thành công.', NULL);
