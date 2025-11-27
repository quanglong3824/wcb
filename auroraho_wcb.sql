-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Máy chủ: 127.0.0.1
-- Thời gian đã tạo: Th10 27, 2025 lúc 03:18 AM
-- Phiên bản máy phục vụ: 10.4.32-MariaDB
-- Phiên bản PHP: 8.2.12

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Cơ sở dữ liệu: `auroraho_wcb`
--

DELIMITER $$
--
-- Thủ tục
--
CREATE DEFINER=`auroraho_longdev`@`localhost` PROCEDURE `sp_get_dashboard_stats` ()   BEGIN
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

CREATE DEFINER=`auroraho_longdev`@`localhost` PROCEDURE `sp_get_tv_content` (IN `tv_id_param` INT)   BEGIN
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

CREATE DEFINER=`auroraho_longdev`@`localhost` PROCEDURE `sp_record_heartbeat` (IN `tv_id_param` INT, IN `status_param` VARCHAR(10), IN `media_id_param` INT, IN `ip_param` VARCHAR(45), IN `user_agent_param` VARCHAR(255))   BEGIN
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

CREATE DEFINER=`auroraho_longdev`@`localhost` PROCEDURE `sp_update_schedule_status` ()   BEGIN
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

CREATE DEFINER=`auroraho_longdev`@`localhost` PROCEDURE `sp_update_tv_status` ()   BEGIN
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

--
-- Các hàm
--
CREATE DEFINER=`auroraho_longdev`@`localhost` FUNCTION `fn_count_active_schedules` (`tv_id_param` INT) RETURNS INT(11) DETERMINISTIC READS SQL DATA BEGIN
    DECLARE schedule_count INT DEFAULT 0;
    
    SELECT COUNT(*) INTO schedule_count
    FROM schedules
    WHERE tv_id = tv_id_param
    AND status = 'active';
    
    RETURN schedule_count;
END$$

CREATE DEFINER=`auroraho_longdev`@`localhost` FUNCTION `fn_get_media_name` (`media_id_param` INT) RETURNS VARCHAR(200) CHARSET utf8mb4 COLLATE utf8mb4_general_ci DETERMINISTIC READS SQL DATA BEGIN
    DECLARE media_name_result VARCHAR(200);
    SELECT name INTO media_name_result FROM media WHERE id = media_id_param;
    RETURN IFNULL(media_name_result, 'Unknown Media');
END$$

CREATE DEFINER=`auroraho_longdev`@`localhost` FUNCTION `fn_get_tv_name` (`tv_id_param` INT) RETURNS VARCHAR(100) CHARSET utf8mb4 COLLATE utf8mb4_general_ci DETERMINISTIC READS SQL DATA BEGIN
    DECLARE tv_name_result VARCHAR(100);
    SELECT name INTO tv_name_result FROM tvs WHERE id = tv_id_param;
    RETURN IFNULL(tv_name_result, 'Unknown TV');
END$$

CREATE DEFINER=`auroraho_longdev`@`localhost` FUNCTION `fn_is_tv_online` (`tv_id_param` INT) RETURNS TINYINT(1) DETERMINISTIC READS SQL DATA BEGIN
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

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL COMMENT 'Loại hành động: login, upload, schedule, etc.',
  `entity_type` varchar(50) DEFAULT NULL COMMENT 'Loại đối tượng: tv, media, schedule',
  `entity_id` int(11) DEFAULT NULL COMMENT 'ID của đối tượng',
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `entity_type`, `entity_id`, `description`, `ip_address`, `user_agent`, `created_at`) VALUES
(22, 3, 'logout', NULL, NULL, 'User logged out', '::1', NULL, '2025-11-26 13:43:03'),
(23, 3, 'login', NULL, NULL, 'User logged in', '::1', NULL, '2025-11-26 13:43:09'),
(24, 3, 'logout', NULL, NULL, 'User logged out', '::1', NULL, '2025-11-26 13:46:19'),
(25, 3, 'login', NULL, NULL, 'User logged in', '::1', NULL, '2025-11-26 13:49:04'),
(26, 3, 'upload', 'media', 19, 'Upload media: Cao đẳng Hòa Bình Xuân Lộc - Orchild tầng 5', NULL, NULL, '2025-11-26 13:56:28'),
(27, 3, 'upload', 'media', 19, 'Upload file: Cao đẳng Hòa Bình Xuân Lộc - Orchild tầng 5', '::1', NULL, '2025-11-26 13:56:28'),
(28, 3, 'upload', 'media', 20, 'Upload media: Abbott - Tầng 5 - Lotus', NULL, NULL, '2025-11-26 13:56:28'),
(29, 3, 'upload', 'media', 20, 'Upload file: Abbott - Tầng 5 - Lotus', '::1', NULL, '2025-11-26 13:56:28'),
(30, 3, 'upload', 'media', 21, 'Upload media: VietCab tầng 6', NULL, NULL, '2025-11-26 13:56:28'),
(31, 3, 'upload', 'media', 21, 'Upload file: VietCab tầng 6', '::1', NULL, '2025-11-26 13:56:28'),
(32, NULL, 'delete', 'media', 21, 'Delete media: VietCab tầng 6', NULL, NULL, '2025-11-26 14:01:29'),
(33, 3, 'upload', 'media', 22, 'Upload media: VietCab tầng 6', NULL, NULL, '2025-11-26 14:01:37'),
(34, 3, 'upload', 'media', 22, 'Upload file: VietCab tầng 6', '::1', NULL, '2025-11-26 14:01:37'),
(35, NULL, 'delete', 'media', 22, 'Delete media: VietCab tầng 6', NULL, NULL, '2025-11-26 14:03:10'),
(36, 3, 'delete', 'media', 22, 'Xóa media: VietCab tầng 6', '::1', NULL, '2025-11-26 14:03:10'),
(37, 3, 'upload', 'media', 23, 'Upload media: VietCab tầng 6', NULL, NULL, '2025-11-26 14:03:16'),
(38, 3, 'upload', 'media', 23, 'Upload file: VietCab tầng 6', '::1', NULL, '2025-11-26 14:03:16'),
(39, 3, 'delete', 'media', 23, 'Đánh dấu xóa media: VietCab tầng 6', '::1', NULL, '2025-11-26 14:06:15'),
(40, 3, 'delete', 'media', 23, 'Đánh dấu xóa media: VietCab tầng 6', '::1', NULL, '2025-11-26 14:07:02'),
(41, 3, 'delete', 'media', 19, 'Đánh dấu xóa media: Cao đẳng Hòa Bình Xuân Lộc - Orchild tầng 5', '::1', NULL, '2025-11-26 14:07:05'),
(42, 3, 'delete', 'media', 20, 'Đánh dấu xóa media: Abbott - Tầng 5 - Lotus', '::1', NULL, '2025-11-26 14:07:07'),
(43, NULL, 'delete', 'media', 19, 'Delete media: Cao đẳng Hòa Bình Xuân Lộc - Orchild tầng 5', NULL, NULL, '2025-11-26 14:07:19'),
(44, NULL, 'delete', 'media', 20, 'Delete media: Abbott - Tầng 5 - Lotus', NULL, NULL, '2025-11-26 14:07:19'),
(45, NULL, 'delete', 'media', 23, 'Delete media: VietCab tầng 6', NULL, NULL, '2025-11-26 14:07:19'),
(46, 3, 'upload', 'media', 24, 'Upload media: Abbott - Tầng 5 - Lotus', NULL, NULL, '2025-11-26 14:07:30'),
(47, 3, 'upload', 'media', 25, 'Upload media: Cao đẳng Hòa Bình Xuân Lộc - Orchild tầng 5', NULL, NULL, '2025-11-26 14:07:30'),
(48, 3, 'upload', 'media', 26, 'Upload media: VietCab tầng 6', NULL, NULL, '2025-11-26 14:07:30'),
(49, 3, 'update', 'media', 24, 'Cập nhật tên media từ \'Abbott - Tầng 5 - Lotus\' thành \'Abbort - Tầng 5 - Lotus\'', '::1', NULL, '2025-11-26 14:21:20'),
(50, 3, 'update', 'tv', 1, 'Cập nhật thông tin TV: TV Basement', '::1', NULL, '2025-11-26 14:21:37'),
(51, 3, 'assign', 'media', 24, 'Gán media \'Abbort - Tầng 5 - Lotus\' cho TV Basement', '::1', NULL, '2025-11-26 14:21:45'),
(52, 3, 'assign', 'media', 25, 'Gán media \'Cao đẳng Hòa Bình Xuân Lộc - Orchild tầng 5\' cho TV Basement', '::1', NULL, '2025-11-26 14:25:23'),
(53, 3, 'unassign', 'media', 25, 'Hủy gán media \'Cao đẳng Hòa Bình Xuân Lộc - Orchild tầng 5\' khỏi TV \'TV Basement\'', '::1', NULL, '2025-11-26 14:25:57'),
(54, 3, 'assign', 'media', 25, 'Gán media \'Cao đẳng Hòa Bình Xuân Lộc - Orchild tầng 5\' cho TV Basement', '::1', NULL, '2025-11-26 14:26:42'),
(55, 3, 'unassign', 'media', 24, 'Hủy gán media \'Abbort - Tầng 5 - Lotus\' khỏi TV \'TV Basement\'', '::1', NULL, '2025-11-26 14:38:17'),
(56, 3, 'unassign', 'media', 25, 'Hủy gán media \'Cao đẳng Hòa Bình Xuân Lộc - Orchild tầng 5\' khỏi TV \'TV Basement\'', '::1', NULL, '2025-11-26 14:38:25'),
(57, 3, 'login', NULL, NULL, 'User logged in', '::1', NULL, '2025-11-27 00:37:29'),
(58, 3, 'update', 'tv', 1, 'Cập nhật thông tin TV: TV Basement', '::1', NULL, '2025-11-27 00:41:33'),
(59, 3, 'update', 'tv', 1, 'Cập nhật thông tin TV: TV Basement', '::1', NULL, '2025-11-27 00:43:36'),
(60, 3, 'assign', 'media', 24, 'Gán media \'Abbort - Tầng 5 - Lotus\' cho TV Basement', '::1', NULL, '2025-11-27 00:43:41'),
(61, 3, 'update', 'tv', 2, 'Cập nhật thông tin TV: TV Chrysan', '::1', NULL, '2025-11-27 00:43:47'),
(62, 3, 'assign', 'media', 24, 'Gán media \'Abbort - Tầng 5 - Lotus\' cho TV Chrysan', '::1', NULL, '2025-11-27 00:44:06'),
(63, 3, 'assign', 'media', 25, 'Gán media \'Cao đẳng Hòa Bình Xuân Lộc - Orchild tầng 5\' cho TV Basement', '::1', NULL, '2025-11-27 00:56:15'),
(64, 3, 'unassign', 'media', 24, 'Hủy gán media \'Abbort - Tầng 5 - Lotus\' khỏi TV \'TV Basement\'', '::1', NULL, '2025-11-27 01:03:05'),
(65, 3, 'unassign', 'media', 24, 'Hủy gán media \'Abbort - Tầng 5 - Lotus\' khỏi TV \'TV Chrysan\'', '::1', NULL, '2025-11-27 01:03:08'),
(66, 3, 'unassign', 'media', 25, 'Hủy gán media \'Cao đẳng Hòa Bình Xuân Lộc - Orchild tầng 5\' khỏi TV \'TV Basement\'', '::1', NULL, '2025-11-27 01:03:53'),
(67, 3, 'shutdown', 'system', NULL, 'Tắt toàn bộ hệ thống - Offline tất cả TV và gỡ gán toàn bộ WCB', '::1', NULL, '2025-11-27 01:04:50'),
(68, 3, 'update', 'tv', 3, 'Cập nhật thông tin TV: TV Jasmine', '::1', NULL, '2025-11-27 01:06:53'),
(69, 3, 'assign', 'media', 24, 'Gán media \'Abbort - Tầng 5 - Lotus\' cho TV Jasmine', '::1', NULL, '2025-11-27 01:06:58'),
(70, 3, 'update', 'tv', 3, 'Cập nhật thông tin TV: TV Jasmine', '::1', NULL, '2025-11-27 01:08:07'),
(71, 3, 'update', 'tv', 3, 'Cập nhật thông tin TV: TV Jasmine', '::1', NULL, '2025-11-27 01:08:23'),
(72, 3, 'shutdown', 'system', NULL, 'Tắt toàn bộ hệ thống - Offline tất cả TV và gỡ gán toàn bộ WCB', '::1', NULL, '2025-11-27 01:10:43'),
(73, 3, 'orchid_mode', 'media', 25, 'Áp dụng chế độ Orchid - Gán \'Cao đẳng Hòa Bình Xuân Lộc - Orchild tầng 5\' cho TV Basement, TV Chrysan, TV Jasmine, TV Lotus, TV FO 1, TV FO 2 và bật tất cả TV', '::1', NULL, '2025-11-27 01:11:55'),
(74, 3, 'shutdown', 'system', NULL, 'Tắt toàn bộ hệ thống - Offline tất cả TV và gỡ gán toàn bộ WCB', '::1', NULL, '2025-11-27 01:15:05'),
(75, 3, 'reload', 'tv', 1, 'Ép tải lại TV \'TV Basement\'', '::1', NULL, '2025-11-27 01:19:30'),
(76, 3, 'reload', 'tv', 1, 'Ép tải lại TV \'TV Basement\'', '::1', NULL, '2025-11-27 01:19:42'),
(77, 3, 'assign', 'media', 25, 'Gán media \'Cao đẳng Hòa Bình Xuân Lộc - Orchild tầng 5\' cho TV Basement', '::1', NULL, '2025-11-27 01:19:49'),
(78, 3, 'reload', 'tv', 1, 'Ép tải lại TV \'TV Basement\'', '::1', NULL, '2025-11-27 01:19:53'),
(79, 3, 'unassign', 'media', 25, 'Hủy gán media \'Cao đẳng Hòa Bình Xuân Lộc - Orchild tầng 5\' khỏi TV \'TV Basement\'', '::1', NULL, '2025-11-27 01:20:47'),
(80, 3, 'reload', 'tv', 1, 'Ép tải lại TV \'TV Basement\'', '::1', NULL, '2025-11-27 01:20:51'),
(81, 3, 'assign', 'media', 25, 'Gán media \'Cao đẳng Hòa Bình Xuân Lộc - Orchild tầng 5\' cho TV Basement', '::1', NULL, '2025-11-27 01:23:27'),
(82, 3, 'reload', 'tv', 1, 'Ép tải lại TV \'TV Basement\'', '::1', NULL, '2025-11-27 01:23:31'),
(83, 3, 'reload', 'tv', 1, 'Ép tải lại TV \'TV Basement\'', '::1', NULL, '2025-11-27 01:24:05'),
(84, 3, 'orchid_mode', 'media', 25, 'Áp dụng chế độ Orchid - Gán \'Cao đẳng Hòa Bình Xuân Lộc - Orchild tầng 5\' cho TV Basement, TV Chrysan, TV Jasmine, TV Lotus, TV FO 1, TV FO 2 và bật tất cả TV', '::1', NULL, '2025-11-27 01:26:18'),
(85, 3, 'shutdown', 'system', NULL, 'Tắt toàn bộ hệ thống - Offline tất cả TV và gỡ gán toàn bộ WCB', '::1', NULL, '2025-11-27 01:26:41'),
(86, 3, 'toggle_status', 'tv', 1, 'Bật TV \'TV Basement\'', '::1', NULL, '2025-11-27 01:29:48'),
(87, 3, 'toggle_status', 'tv', 1, 'Tắt TV \'TV Basement\'', '::1', NULL, '2025-11-27 01:29:50'),
(88, 3, 'toggle_status', 'tv', 1, 'Bật TV \'TV Basement\'', '::1', NULL, '2025-11-27 01:29:52'),
(89, 3, 'logout', NULL, NULL, 'User logged out', '::1', NULL, '2025-11-27 01:32:34'),
(90, 3, 'login', NULL, NULL, 'User logged in', '::1', NULL, '2025-11-27 01:32:41'),
(91, 3, 'toggle_status', 'tv', 1, 'Tắt TV \'TV Basement\'', '::1', NULL, '2025-11-27 01:32:50'),
(92, 3, 'logout', NULL, NULL, 'User logged out', '::1', NULL, '2025-11-27 01:36:02'),
(93, 3, 'login', NULL, NULL, 'User logged in', '::1', NULL, '2025-11-27 01:36:07'),
(94, 3, 'reload', 'tv', 1, 'Ép tải lại TV \'TV Basement\'', '::1', NULL, '2025-11-27 01:38:48'),
(95, 3, 'toggle_status', 'tv', 1, 'Bật TV \'TV Basement\'', '::1', NULL, '2025-11-27 01:44:00'),
(96, 3, 'assign', 'media', 25, 'Gán media \'Cao đẳng Hòa Bình Xuân Lộc - Orchild tầng 5\' cho TV Basement', '::1', NULL, '2025-11-27 01:44:54'),
(97, 3, 'reload', 'tv', 1, 'Ép tải lại TV \'TV Basement\'', '::1', NULL, '2025-11-27 01:45:03'),
(98, 3, 'toggle_status', 'tv', 1, 'Tắt TV \'TV Basement\'', '::1', NULL, '2025-11-27 01:45:11'),
(99, 3, 'shutdown', 'system', NULL, 'Tắt toàn bộ hệ thống - Offline tất cả TV và gỡ gán toàn bộ WCB', '::1', NULL, '2025-11-27 01:45:31'),
(100, 3, 'toggle_status', 'tv', 6, 'Bật TV \'TV FO 1\'', '::1', NULL, '2025-11-27 01:47:55'),
(101, 3, 'reload', 'tv', 6, 'Ép tải lại TV \'TV FO 1\'', '::1', NULL, '2025-11-27 01:48:01'),
(102, 3, 'assign', 'media', 26, 'Gán media \'VietCab tầng 6\' cho TV FO 1', '::1', NULL, '2025-11-27 01:48:04'),
(103, 3, 'unassign', 'media', 26, 'Hủy gán media \'VietCab tầng 6\' khỏi TV \'TV FO 1\'', '::1', NULL, '2025-11-27 01:48:16'),
(104, 3, 'reload', 'tv', 6, 'Ép tải lại TV \'TV FO 1\'', '::1', NULL, '2025-11-27 01:48:27'),
(105, 3, 'toggle_status', 'tv', 6, 'Tắt TV \'TV FO 1\'', '::1', NULL, '2025-11-27 01:48:31'),
(106, 3, 'toggle_status', 'tv', 2, 'Bật TV \'TV Chrysan\'', '::1', NULL, '2025-11-27 01:54:58'),
(107, 3, 'assign', 'media', 25, 'Gán media \'Cao đẳng Hòa Bình Xuân Lộc - Orchild tầng 5\' cho TV Chrysan', '::1', NULL, '2025-11-27 01:55:02'),
(108, 3, 'unassign', 'media', 25, 'Hủy gán media \'Cao đẳng Hòa Bình Xuân Lộc - Orchild tầng 5\' khỏi TV \'TV Chrysan\'', '::1', NULL, '2025-11-27 01:55:10'),
(109, 3, 'assign', 'media', 24, 'Gán media \'Abbort - Tầng 5 - Lotus\' cho TV Basement', '::1', NULL, '2025-11-27 01:55:33'),
(110, 3, 'assign', 'media', 26, 'Gán media \'VietCab tầng 6\' cho TV Basement', '::1', NULL, '2025-11-27 01:55:33'),
(111, 3, 'assign', 'media', 25, 'Gán media \'Cao đẳng Hòa Bình Xuân Lộc - Orchild tầng 5\' cho TV Basement', '::1', NULL, '2025-11-27 01:55:33'),
(112, 3, 'toggle_status', 'tv', 1, 'Bật TV \'TV Basement\'', '::1', NULL, '2025-11-27 01:55:39'),
(113, 3, 'unassign', 'media', 24, 'Hủy gán media \'Abbort - Tầng 5 - Lotus\' khỏi TV \'TV Basement\'', '::1', NULL, '2025-11-27 01:56:07'),
(114, 3, 'unassign', 'media', 25, 'Hủy gán media \'Cao đẳng Hòa Bình Xuân Lộc - Orchild tầng 5\' khỏi TV \'TV Basement\'', '::1', NULL, '2025-11-27 01:56:10'),
(115, 3, 'unassign', 'media', 26, 'Hủy gán media \'VietCab tầng 6\' khỏi TV \'TV Basement\'', '::1', NULL, '2025-11-27 01:56:11'),
(116, 3, 'toggle_status', 'tv', 1, 'Tắt TV \'TV Basement\'', '::1', NULL, '2025-11-27 01:56:14'),
(117, 3, 'toggle_status', 'tv', 2, 'Tắt TV \'TV Chrysan\'', '::1', NULL, '2025-11-27 01:58:02'),
(118, 3, 'toggle_status', 'tv', 1, 'Bật TV \'TV Basement\'', '::1', NULL, '2025-11-27 02:03:19'),
(119, 3, 'toggle_status', 'tv', 1, 'Tắt TV \'TV Basement\'', '::1', NULL, '2025-11-27 02:03:26'),
(120, 3, 'orchid_mode', 'media', 26, 'Áp dụng chế độ Orchid - Gán \'VietCab tầng 6\' cho TV Basement, TV Chrysan, TV Jasmine, TV Lotus, TV FO 1, TV FO 2 và bật tất cả TV', '::1', NULL, '2025-11-27 02:04:05'),
(121, 3, 'shutdown', 'system', NULL, 'Tắt toàn bộ hệ thống - Offline tất cả TV và gỡ gán toàn bộ WCB', '::1', NULL, '2025-11-27 02:04:20'),
(122, 3, 'toggle_status', 'tv', 6, 'Bật TV \'TV FO 1\'', '::1', NULL, '2025-11-27 02:08:26'),
(123, 3, 'toggle_status', 'tv', 6, 'Tắt TV \'TV FO 1\'', '::1', NULL, '2025-11-27 02:08:29'),
(124, 3, 'toggle_status', 'tv', 2, 'Bật TV \'TV Chrysan\'', '::1', NULL, '2025-11-27 02:08:52'),
(125, 3, 'toggle_status', 'tv', 2, 'Tắt TV \'TV Chrysan\'', '::1', NULL, '2025-11-27 02:09:37'),
(126, 3, 'toggle_status', 'tv', 1, 'Bật TV \'TV Basement\'', '::1', NULL, '2025-11-27 02:14:44'),
(127, 3, 'toggle_status', 'tv', 1, 'Tắt TV \'TV Basement\'', '::1', NULL, '2025-11-27 02:15:30');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `media`
--

CREATE TABLE `media` (
  `id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `type` enum('image','video') NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` bigint(20) DEFAULT NULL COMMENT 'Kích thước file (bytes)',
  `mime_type` varchar(100) DEFAULT NULL,
  `thumbnail_path` varchar(500) DEFAULT NULL,
  `duration` int(11) DEFAULT NULL COMMENT 'Thời lượng video (giây)',
  `width` int(11) DEFAULT NULL COMMENT 'Chiều rộng (px)',
  `height` int(11) DEFAULT NULL COMMENT 'Chiều cao (px)',
  `description` text DEFAULT NULL,
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `uploaded_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `media`
--

INSERT INTO `media` (`id`, `name`, `type`, `file_name`, `file_path`, `file_size`, `mime_type`, `thumbnail_path`, `duration`, `width`, `height`, `description`, `status`, `uploaded_by`, `created_at`, `updated_at`) VALUES
(24, 'Abbort - Tầng 5 - Lotus', 'image', '692709a29862c_1764166050_0.jpg', 'uploads/692709a29862c_1764166050_0.jpg', 291759, 'image/jpeg', NULL, NULL, 1920, 1080, '', 'active', 3, '2025-11-26 14:07:30', '2025-11-26 14:21:20'),
(25, 'Cao đẳng Hòa Bình Xuân Lộc - Orchild tầng 5', 'image', '692709a2a026e_1764166050_1.jpg', 'uploads/692709a2a026e_1764166050_1.jpg', 323821, 'image/jpeg', NULL, NULL, 1920, 1080, NULL, 'active', 3, '2025-11-26 14:07:30', '2025-11-26 14:07:30'),
(26, 'VietCab tầng 6', 'image', '692709a2a1ddd_1764166050_2.jpg', 'uploads/692709a2a1ddd_1764166050_2.jpg', 505784, 'image/jpeg', NULL, NULL, 2560, 1440, NULL, 'active', 3, '2025-11-26 14:07:30', '2025-11-26 14:07:30');

--
-- Bẫy `media`
--
DELIMITER $$
CREATE TRIGGER `trg_media_after_insert` AFTER INSERT ON `media` FOR EACH ROW BEGIN
    INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description)
    VALUES (NEW.uploaded_by, 'upload', 'media', NEW.id, CONCAT('Upload media: ', NEW.name));
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_media_before_delete` BEFORE DELETE ON `media` FOR EACH ROW BEGIN
    INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description)
    VALUES (NULL, 'delete', 'media', OLD.id, CONCAT('Delete media: ', OLD.name));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `schedules`
--

CREATE TABLE `schedules` (
  `id` int(11) NOT NULL,
  `tv_id` int(11) NOT NULL,
  `media_id` int(11) NOT NULL,
  `schedule_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `repeat_type` enum('none','daily','weekly','monthly') NOT NULL DEFAULT 'none',
  `repeat_until` date DEFAULT NULL COMMENT 'Ngày kết thúc lặp lại',
  `priority` int(11) DEFAULT 0 COMMENT 'Độ ưu tiên (số càng cao càng ưu tiên)',
  `status` enum('active','pending','completed','cancelled') NOT NULL DEFAULT 'pending',
  `note` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Bẫy `schedules`
--
DELIMITER $$
CREATE TRIGGER `trg_schedule_after_insert` AFTER INSERT ON `schedules` FOR EACH ROW BEGIN
    DECLARE tv_name_var VARCHAR(100);
    DECLARE media_name_var VARCHAR(200);
    
    SELECT name INTO tv_name_var FROM tvs WHERE id = NEW.tv_id;
    SELECT name INTO media_name_var FROM media WHERE id = NEW.media_id;
    
    INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description)
    VALUES (NEW.created_by, 'create', 'schedule', NEW.id, 
            CONCAT('Tạo lịch chiếu "', media_name_var, '" cho ', tv_name_var));
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_schedule_after_update` AFTER UPDATE ON `schedules` FOR EACH ROW BEGIN
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
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_schedule_before_delete` BEFORE DELETE ON `schedules` FOR EACH ROW BEGIN
    DECLARE tv_name_var VARCHAR(100);
    DECLARE media_name_var VARCHAR(200);
    
    SELECT name INTO tv_name_var FROM tvs WHERE id = OLD.tv_id;
    SELECT name INTO media_name_var FROM media WHERE id = OLD.media_id;
    
    INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description)
    VALUES (NULL, 'delete', 'schedule', OLD.id, 
            CONCAT('Xóa lịch chiếu "', media_name_var, '" cho ', tv_name_var));
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `system_settings`
--

CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('string','number','boolean','json') NOT NULL DEFAULT 'string',
  `description` text DEFAULT NULL,
  `is_public` tinyint(1) DEFAULT 0 COMMENT '1 = có thể xem công khai',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `system_settings`
--

INSERT INTO `system_settings` (`id`, `setting_key`, `setting_value`, `setting_type`, `description`, `is_public`, `updated_at`) VALUES
(1, 'site_name', 'Aurora Hotel Plaza - Welcome Board System', 'string', 'Tên hệ thống', 1, '2025-11-27 01:35:46'),
(2, 'hotel_name', 'Quang Long Hotel', 'string', 'Tên khách sạn', 1, '2025-11-26 12:56:54'),
(3, 'heartbeat_interval', '60', 'number', 'Khoảng thời gian gửi heartbeat (giây)', 0, '2025-11-26 12:56:54'),
(4, 'offline_threshold', '300', 'number', 'Thời gian tối đa không nhận heartbeat để đánh dấu offline (giây)', 0, '2025-11-26 12:56:54'),
(5, 'max_upload_size', '52428800', 'number', 'Kích thước file tối đa cho phép upload (bytes) - 50MB', 0, '2025-11-26 12:56:54'),
(6, 'allowed_image_types', 'jpg,jpeg,png,gif,webp', 'string', 'Các định dạng hình ảnh được phép', 0, '2025-11-26 12:56:54'),
(7, 'allowed_video_types', 'mp4,webm,avi,mov', 'string', 'Các định dạng video được phép', 0, '2025-11-26 12:56:54'),
(8, 'auto_refresh_interval', '30', 'number', 'Khoảng thời gian tự động làm mới màn hình TV (giây)', 1, '2025-11-26 12:56:54'),
(9, 'default_transition', 'fade', 'string', 'Hiệu ứng chuyển cảnh mặc định', 1, '2025-11-26 12:56:54'),
(10, 'transition_duration', '1', 'number', 'Thời gian chuyển cảnh (giây)', 1, '2025-11-26 12:56:54'),
(11, 'enable_logging', 'true', 'boolean', 'Bật/tắt ghi log hoạt động', 0, '2025-11-26 12:56:54'),
(12, 'timezone', 'Asia/Ho_Chi_Minh', 'string', 'Múi giờ hệ thống', 0, '2025-11-26 12:56:54'),
(13, 'language', 'vi', 'string', 'Ngôn ngữ mặc định', 1, '2025-11-26 12:56:54'),
(14, 'tv_reload_signal_1', '1764207903', 'string', 'Reload signal for TV TV Basement', 0, '2025-11-27 01:45:03'),
(15, 'tv_reload_signal_6', '1764208106', 'string', 'Reload signal for TV TV FO 1', 0, '2025-11-27 01:48:27');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tvs`
--

CREATE TABLE `tvs` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `location` varchar(100) NOT NULL,
  `folder` varchar(100) NOT NULL COMMENT 'Tên thư mục chứa file hiển thị',
  `display_url` varchar(500) DEFAULT NULL COMMENT 'URL để trình chiếu trên TV',
  `status` enum('online','offline') NOT NULL DEFAULT 'offline',
  `current_content_id` int(11) DEFAULT NULL COMMENT 'ID nội dung đang chiếu',
  `default_content_id` int(11) DEFAULT NULL COMMENT 'ID nội dung mặc định',
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'Địa chỉ IP của TV',
  `description` text DEFAULT NULL,
  `last_heartbeat` datetime DEFAULT NULL COMMENT 'Lần gửi heartbeat cuối',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `tvs`
--

INSERT INTO `tvs` (`id`, `name`, `location`, `folder`, `display_url`, `status`, `current_content_id`, `default_content_id`, `ip_address`, `description`, `last_heartbeat`, `created_at`, `updated_at`) VALUES
(1, 'TV Basement', 'Tầng hầm', 'basement', 'basement/index.php', 'offline', NULL, 26, '', 'TV tại khu vực tầng hầm - Hiển thị thông tin chào mừng', '2025-11-27 08:55:35', '2025-11-26 12:56:54', '2025-11-27 02:15:30'),
(2, 'TV Chrysan', 'Phòng Chrysan', 'chrysan', 'chrysan/index.php', 'offline', NULL, 26, '', 'TV tại phòng hội nghị Chrysan', '2025-11-27 08:55:03', '2025-11-26 12:56:54', '2025-11-27 02:09:37'),
(3, 'TV Jasmine', 'Phòng Jasmine', 'jasmine', 'jasmine/index.php', 'offline', NULL, 26, '', 'Phòng họp Jasmine', '2025-11-27 08:07:02', '2025-11-26 12:56:54', '2025-11-27 02:04:20'),
(4, 'TV Lotus', 'Phòng Lotus', 'lotus', 'lotus/index.php', 'offline', NULL, 26, '', 'TV tại phòng hội nghị Lotus', NULL, '2025-11-26 12:56:54', '2025-11-27 02:04:20'),
(5, 'TV Restaurant', 'Nhà hàng', 'restaurant', 'restaurant/index.php', 'offline', NULL, 3, '', 'TV tại nhà hàng - Hiển thị menu và khuyến mãi', NULL, '2025-11-26 12:56:54', '2025-11-27 01:04:50'),
(6, 'TV FO 1', 'Lễ tân 1', 'fo/tv1', 'fo/tv1/index.php', 'offline', NULL, 26, '', 'TV tại quầy lễ tân số 1', '2025-11-27 08:48:05', '2025-11-26 12:56:54', '2025-11-27 02:08:29'),
(7, 'TV FO 2', 'Lễ tân 2', 'fo/tv2', 'fo/tv2/index.php', 'offline', NULL, 26, '', 'TV tại quầy lễ tân số 2', NULL, '2025-11-26 12:56:54', '2025-11-27 02:04:20');

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tv_heartbeats`
--

CREATE TABLE `tv_heartbeats` (
  `id` int(11) NOT NULL,
  `tv_id` int(11) NOT NULL,
  `status` enum('online','offline') NOT NULL,
  `current_media_id` int(11) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `tv_media_assignments`
--

CREATE TABLE `tv_media_assignments` (
  `id` int(11) NOT NULL,
  `tv_id` int(11) NOT NULL,
  `media_id` int(11) NOT NULL,
  `is_default` tinyint(1) DEFAULT 0 COMMENT '1 = nội dung mặc định',
  `display_order` int(11) DEFAULT 0 COMMENT 'Thứ tự hiển thị',
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `assigned_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Bẫy `tv_media_assignments`
--
DELIMITER $$
CREATE TRIGGER `trg_tv_media_after_insert` AFTER INSERT ON `tv_media_assignments` FOR EACH ROW BEGIN
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
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Cấu trúc bảng cho bảng `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` enum('super_admin','content_manager') NOT NULL DEFAULT 'content_manager',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Đang đổ dữ liệu cho bảng `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `full_name`, `email`, `role`, `status`, `last_login`, `created_at`, `updated_at`) VALUES
(3, 'admin', '$2y$10$pZXTBTjkcZEijm4VdDHIrO4g9p5ALDcfPYrOGYgeHeMQ5Uq2L9rWe', 'Administrator', 'admin@quanglonghotel.com', 'super_admin', 'active', '2025-11-27 08:36:07', '2025-11-26 13:42:37', '2025-11-27 01:36:07');

-- --------------------------------------------------------

--
-- Cấu trúc đóng vai cho view `view_active_schedules`
-- (See below for the actual view)
--
CREATE TABLE `view_active_schedules` (
`id` int(11)
,`schedule_date` date
,`start_time` time
,`end_time` time
,`repeat_type` enum('none','daily','weekly','monthly')
,`repeat_until` date
,`priority` int(11)
,`status` enum('active','pending','completed','cancelled')
,`note` text
,`tv_id` int(11)
,`tv_name` varchar(100)
,`tv_location` varchar(100)
,`tv_folder` varchar(100)
,`media_id` int(11)
,`media_name` varchar(200)
,`media_type` enum('image','video')
,`media_path` varchar(500)
,`media_thumbnail` varchar(500)
,`created_by_name` varchar(100)
,`created_at` timestamp
);

-- --------------------------------------------------------

--
-- Cấu trúc đóng vai cho view `view_dashboard_stats`
-- (See below for the actual view)
--
CREATE TABLE `view_dashboard_stats` (
`total_tvs` bigint(21)
,`online_tvs` bigint(21)
,`offline_tvs` bigint(21)
,`total_media` bigint(21)
,`total_images` bigint(21)
,`total_videos` bigint(21)
,`active_schedules` bigint(21)
,`pending_schedules` bigint(21)
,`today_schedules` bigint(21)
);

-- --------------------------------------------------------

--
-- Cấu trúc đóng vai cho view `view_media_stats`
-- (See below for the actual view)
--
CREATE TABLE `view_media_stats` (
`id` int(11)
,`name` varchar(200)
,`type` enum('image','video')
,`file_name` varchar(255)
,`file_path` varchar(500)
,`file_size` bigint(20)
,`status` enum('active','inactive')
,`created_at` timestamp
,`assigned_tv_count` bigint(21)
,`schedule_count` bigint(21)
,`assigned_to_tvs` mediumtext
,`uploaded_by_name` varchar(100)
);

-- --------------------------------------------------------

--
-- Cấu trúc đóng vai cho view `view_tv_status`
-- (See below for the actual view)
--
CREATE TABLE `view_tv_status` (
`id` int(11)
,`name` varchar(100)
,`location` varchar(100)
,`folder` varchar(100)
,`display_url` varchar(500)
,`status` enum('online','offline')
,`ip_address` varchar(45)
,`last_heartbeat` datetime
,`description` text
,`current_content_id` int(11)
,`current_content_name` varchar(200)
,`current_content_type` enum('image','video')
,`current_content_path` varchar(500)
,`default_content_id` int(11)
,`default_content_name` varchar(200)
,`default_content_type` enum('image','video')
,`default_content_path` varchar(500)
,`seconds_since_heartbeat` bigint(21)
,`actual_status` varchar(7)
);

-- --------------------------------------------------------

--
-- Cấu trúc cho view `view_active_schedules`
--
DROP TABLE IF EXISTS `view_active_schedules`;

CREATE ALGORITHM=UNDEFINED DEFINER=`auroraho_longdev`@`localhost` SQL SECURITY DEFINER VIEW `view_active_schedules`  AS SELECT `s`.`id` AS `id`, `s`.`schedule_date` AS `schedule_date`, `s`.`start_time` AS `start_time`, `s`.`end_time` AS `end_time`, `s`.`repeat_type` AS `repeat_type`, `s`.`repeat_until` AS `repeat_until`, `s`.`priority` AS `priority`, `s`.`status` AS `status`, `s`.`note` AS `note`, `t`.`id` AS `tv_id`, `t`.`name` AS `tv_name`, `t`.`location` AS `tv_location`, `t`.`folder` AS `tv_folder`, `m`.`id` AS `media_id`, `m`.`name` AS `media_name`, `m`.`type` AS `media_type`, `m`.`file_path` AS `media_path`, `m`.`thumbnail_path` AS `media_thumbnail`, `u`.`full_name` AS `created_by_name`, `s`.`created_at` AS `created_at` FROM (((`schedules` `s` join `tvs` `t` on(`s`.`tv_id` = `t`.`id`)) join `media` `m` on(`s`.`media_id` = `m`.`id`)) left join `users` `u` on(`s`.`created_by` = `u`.`id`)) WHERE `s`.`status` in ('active','pending') ORDER BY `s`.`schedule_date` DESC, `s`.`start_time` DESC ;

-- --------------------------------------------------------

--
-- Cấu trúc cho view `view_dashboard_stats`
--
DROP TABLE IF EXISTS `view_dashboard_stats`;

CREATE ALGORITHM=UNDEFINED DEFINER=`auroraho_longdev`@`localhost` SQL SECURITY DEFINER VIEW `view_dashboard_stats`  AS SELECT (select count(0) from `tvs`) AS `total_tvs`, (select count(0) from `tvs` where `tvs`.`status` = 'online') AS `online_tvs`, (select count(0) from `tvs` where `tvs`.`status` = 'offline') AS `offline_tvs`, (select count(0) from `media` where `media`.`status` = 'active') AS `total_media`, (select count(0) from `media` where `media`.`type` = 'image' and `media`.`status` = 'active') AS `total_images`, (select count(0) from `media` where `media`.`type` = 'video' and `media`.`status` = 'active') AS `total_videos`, (select count(0) from `schedules` where `schedules`.`status` = 'active') AS `active_schedules`, (select count(0) from `schedules` where `schedules`.`status` = 'pending') AS `pending_schedules`, (select count(0) from `schedules` where `schedules`.`schedule_date` = curdate() and `schedules`.`status` in ('active','pending')) AS `today_schedules` ;

-- --------------------------------------------------------

--
-- Cấu trúc cho view `view_media_stats`
--
DROP TABLE IF EXISTS `view_media_stats`;

CREATE ALGORITHM=UNDEFINED DEFINER=`auroraho_longdev`@`localhost` SQL SECURITY DEFINER VIEW `view_media_stats`  AS SELECT `m`.`id` AS `id`, `m`.`name` AS `name`, `m`.`type` AS `type`, `m`.`file_name` AS `file_name`, `m`.`file_path` AS `file_path`, `m`.`file_size` AS `file_size`, `m`.`status` AS `status`, `m`.`created_at` AS `created_at`, count(distinct `tma`.`tv_id`) AS `assigned_tv_count`, count(distinct `s`.`id`) AS `schedule_count`, group_concat(distinct `t`.`name` separator ', ') AS `assigned_to_tvs`, `u`.`full_name` AS `uploaded_by_name` FROM ((((`media` `m` left join `tv_media_assignments` `tma` on(`m`.`id` = `tma`.`media_id`)) left join `tvs` `t` on(`tma`.`tv_id` = `t`.`id`)) left join `schedules` `s` on(`m`.`id` = `s`.`media_id` and `s`.`status` in ('active','pending'))) left join `users` `u` on(`m`.`uploaded_by` = `u`.`id`)) GROUP BY `m`.`id` ;

-- --------------------------------------------------------

--
-- Cấu trúc cho view `view_tv_status`
--
DROP TABLE IF EXISTS `view_tv_status`;

CREATE ALGORITHM=UNDEFINED DEFINER=`auroraho_longdev`@`localhost` SQL SECURITY DEFINER VIEW `view_tv_status`  AS SELECT `t`.`id` AS `id`, `t`.`name` AS `name`, `t`.`location` AS `location`, `t`.`folder` AS `folder`, `t`.`display_url` AS `display_url`, `t`.`status` AS `status`, `t`.`ip_address` AS `ip_address`, `t`.`last_heartbeat` AS `last_heartbeat`, `t`.`description` AS `description`, `m`.`id` AS `current_content_id`, `m`.`name` AS `current_content_name`, `m`.`type` AS `current_content_type`, `m`.`file_path` AS `current_content_path`, `dm`.`id` AS `default_content_id`, `dm`.`name` AS `default_content_name`, `dm`.`type` AS `default_content_type`, `dm`.`file_path` AS `default_content_path`, timestampdiff(SECOND,`t`.`last_heartbeat`,current_timestamp()) AS `seconds_since_heartbeat`, CASE WHEN timestampdiff(SECOND,`t`.`last_heartbeat`,current_timestamp()) > 300 THEN 'offline' ELSE `t`.`status` END AS `actual_status` FROM ((`tvs` `t` left join `media` `m` on(`t`.`current_content_id` = `m`.`id`)) left join `media` `dm` on(`t`.`default_content_id` = `dm`.`id`)) ;

--
-- Chỉ mục cho các bảng đã đổ
--

--
-- Chỉ mục cho bảng `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_action` (`action`),
  ADD KEY `idx_entity` (`entity_type`,`entity_id`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_logs_time_action` (`created_at`,`action`);

--
-- Chỉ mục cho bảng `media`
--
ALTER TABLE `media`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_type` (`type`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_uploaded_by` (`uploaded_by`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_media_upload_time` (`uploaded_by`,`created_at`);
ALTER TABLE `media` ADD FULLTEXT KEY `idx_search` (`name`,`description`);

--
-- Chỉ mục cho bảng `schedules`
--
ALTER TABLE `schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tv_id` (`tv_id`),
  ADD KEY `idx_media_id` (`media_id`),
  ADD KEY `idx_schedule_date` (`schedule_date`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_datetime` (`schedule_date`,`start_time`,`end_time`),
  ADD KEY `idx_active_schedules` (`tv_id`,`status`,`schedule_date`,`start_time`,`end_time`),
  ADD KEY `created_by` (`created_by`),
  ADD KEY `idx_schedule_time_range` (`tv_id`,`status`,`schedule_date`,`start_time`,`end_time`),
  ADD KEY `idx_schedule_repeat` (`repeat_type`,`repeat_until`,`status`);

--
-- Chỉ mục cho bảng `system_settings`
--
ALTER TABLE `system_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD UNIQUE KEY `unique_setting_key` (`setting_key`),
  ADD KEY `idx_setting_key` (`setting_key`);

--
-- Chỉ mục cho bảng `tvs`
--
ALTER TABLE `tvs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_folder` (`folder`),
  ADD KEY `idx_status` (`status`),
  ADD KEY `idx_folder` (`folder`),
  ADD KEY `idx_location` (`location`);

--
-- Chỉ mục cho bảng `tv_heartbeats`
--
ALTER TABLE `tv_heartbeats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_tv_id` (`tv_id`),
  ADD KEY `idx_created_at` (`created_at`);

--
-- Chỉ mục cho bảng `tv_media_assignments`
--
ALTER TABLE `tv_media_assignments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_tv_media` (`tv_id`,`media_id`),
  ADD KEY `idx_tv_id` (`tv_id`),
  ADD KEY `idx_media_id` (`media_id`),
  ADD KEY `idx_is_default` (`is_default`),
  ADD KEY `assigned_by` (`assigned_by`);

--
-- Chỉ mục cho bảng `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD KEY `idx_username` (`username`),
  ADD KEY `idx_role` (`role`),
  ADD KEY `idx_status` (`status`);

--
-- AUTO_INCREMENT cho các bảng đã đổ
--

--
-- AUTO_INCREMENT cho bảng `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=128;

--
-- AUTO_INCREMENT cho bảng `media`
--
ALTER TABLE `media`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT cho bảng `schedules`
--
ALTER TABLE `schedules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `system_settings`
--
ALTER TABLE `system_settings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT cho bảng `tvs`
--
ALTER TABLE `tvs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT cho bảng `tv_heartbeats`
--
ALTER TABLE `tv_heartbeats`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT cho bảng `tv_media_assignments`
--
ALTER TABLE `tv_media_assignments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT cho bảng `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Các ràng buộc cho các bảng đã đổ
--

--
-- Các ràng buộc cho bảng `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `media`
--
ALTER TABLE `media`
  ADD CONSTRAINT `media_ibfk_1` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `schedules`
--
ALTER TABLE `schedules`
  ADD CONSTRAINT `schedules_ibfk_1` FOREIGN KEY (`tv_id`) REFERENCES `tvs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedules_ibfk_2` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `schedules_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Các ràng buộc cho bảng `tv_heartbeats`
--
ALTER TABLE `tv_heartbeats`
  ADD CONSTRAINT `tv_heartbeats_ibfk_1` FOREIGN KEY (`tv_id`) REFERENCES `tvs` (`id`) ON DELETE CASCADE;

--
-- Các ràng buộc cho bảng `tv_media_assignments`
--
ALTER TABLE `tv_media_assignments`
  ADD CONSTRAINT `tv_media_assignments_ibfk_1` FOREIGN KEY (`tv_id`) REFERENCES `tvs` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tv_media_assignments_ibfk_2` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `tv_media_assignments_ibfk_3` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

DELIMITER $$
--
-- Sự kiện
--
CREATE DEFINER=`auroraho_longdev`@`localhost` EVENT `evt_update_tv_status` ON SCHEDULE EVERY 1 MINUTE STARTS '2025-11-26 04:56:54' ON COMPLETION NOT PRESERVE ENABLE DO CALL sp_update_tv_status()$$

CREATE DEFINER=`auroraho_longdev`@`localhost` EVENT `evt_update_schedule_status` ON SCHEDULE EVERY 1 MINUTE STARTS '2025-11-26 04:56:54' ON COMPLETION NOT PRESERVE ENABLE DO CALL sp_update_schedule_status()$$

CREATE DEFINER=`auroraho_longdev`@`localhost` EVENT `evt_cleanup_old_logs` ON SCHEDULE EVERY 1 DAY STARTS '2025-11-26 02:00:00' ON COMPLETION NOT PRESERVE ENABLE DO DELETE FROM activity_logs WHERE created_at < DATE_SUB(NOW(), INTERVAL 90 DAY)$$

CREATE DEFINER=`auroraho_longdev`@`localhost` EVENT `evt_cleanup_old_heartbeats` ON SCHEDULE EVERY 1 DAY STARTS '2025-11-26 03:00:00' ON COMPLETION NOT PRESERVE ENABLE DO DELETE FROM tv_heartbeats WHERE created_at < DATE_SUB(NOW(), INTERVAL 7 DAY)$$

DELIMITER ;
SET FOREIGN_KEY_CHECKS=1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
