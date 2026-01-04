-- Aurora Hotel WCB Database Backup
-- Generated: 2026-01-04 10:29:07
-- Database: auroraho_wcb
-- --------------------------------------------------------

SET FOREIGN_KEY_CHECKS=0;
SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';
SET AUTOCOMMIT = 0;
START TRANSACTION;

-- --------------------------------------------------------
-- Table structure for `activity_logs`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `activity_logs`;
CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(100) NOT NULL COMMENT 'Loại hành động: login, upload, schedule, etc.',
  `entity_type` varchar(50) DEFAULT NULL COMMENT 'Loại đối tượng: tv, media, schedule',
  `entity_id` int(11) DEFAULT NULL COMMENT 'ID của đối tượng',
  `description` text DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_action` (`action`),
  KEY `idx_entity` (`entity_type`,`entity_id`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_logs_time_action` (`created_at`,`action`),
  CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=873 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `activity_logs`

INSERT INTO `activity_logs` VALUES("22",NULL,"logout",NULL,NULL,"User logged out","::1",NULL,"2025-11-26 20:43:03");
INSERT INTO `activity_logs` VALUES("23",NULL,"login",NULL,NULL,"User logged in","::1",NULL,"2025-11-26 20:43:09");
INSERT INTO `activity_logs` VALUES("24",NULL,"logout",NULL,NULL,"User logged out","::1",NULL,"2025-11-26 20:46:19");
INSERT INTO `activity_logs` VALUES("25",NULL,"login",NULL,NULL,"User logged in","::1",NULL,"2025-11-26 20:49:04");
INSERT INTO `activity_logs` VALUES("26",NULL,"upload","media","19","Upload media: Cao đẳng Hòa Bình Xuân Lộc - Orchild tầng 5",NULL,NULL,"2025-11-26 20:56:28");
INSERT INTO `activity_logs` VALUES("27",NULL,"upload","media","19","Upload file: Cao đẳng Hòa Bình Xuân Lộc - Orchild tầng 5","::1",NULL,"2025-11-26 20:56:28");
INSERT INTO `activity_logs` VALUES("28",NULL,"upload","media","20","Upload media: Abbott - Tầng 5 - Lotus",NULL,NULL,"2025-11-26 20:56:28");
INSERT INTO `activity_logs` VALUES("29",NULL,"upload","media","20","Upload file: Abbott - Tầng 5 - Lotus","::1",NULL,"2025-11-26 20:56:28");
INSERT INTO `activity_logs` VALUES("30",NULL,"upload","media","21","Upload media: VietCab tầng 6",NULL,NULL,"2025-11-26 20:56:28");
INSERT INTO `activity_logs` VALUES("31",NULL,"upload","media","21","Upload file: VietCab tầng 6","::1",NULL,"2025-11-26 20:56:28");
INSERT INTO `activity_logs` VALUES("32",NULL,"delete","media","21","Delete media: VietCab tầng 6",NULL,NULL,"2025-11-26 21:01:29");
INSERT INTO `activity_logs` VALUES("33",NULL,"upload","media","22","Upload media: VietCab tầng 6",NULL,NULL,"2025-11-26 21:01:37");
INSERT INTO `activity_logs` VALUES("34",NULL,"upload","media","22","Upload file: VietCab tầng 6","::1",NULL,"2025-11-26 21:01:37");
INSERT INTO `activity_logs` VALUES("35",NULL,"delete","media","22","Delete media: VietCab tầng 6",NULL,NULL,"2025-11-26 21:03:10");
INSERT INTO `activity_logs` VALUES("36",NULL,"delete","media","22","Xóa media: VietCab tầng 6","::1",NULL,"2025-11-26 21:03:10");
INSERT INTO `activity_logs` VALUES("37",NULL,"upload","media","23","Upload media: VietCab tầng 6",NULL,NULL,"2025-11-26 21:03:16");
INSERT INTO `activity_logs` VALUES("38",NULL,"upload","media","23","Upload file: VietCab tầng 6","::1",NULL,"2025-11-26 21:03:16");
INSERT INTO `activity_logs` VALUES("39",NULL,"delete","media","23","Đánh dấu xóa media: VietCab tầng 6","::1",NULL,"2025-11-26 21:06:15");
INSERT INTO `activity_logs` VALUES("40",NULL,"delete","media","23","Đánh dấu xóa media: VietCab tầng 6","::1",NULL,"2025-11-26 21:07:02");
INSERT INTO `activity_logs` VALUES("41",NULL,"delete","media","19","Đánh dấu xóa media: Cao đẳng Hòa Bình Xuân Lộc - Orchild tầng 5","::1",NULL,"2025-11-26 21:07:05");
INSERT INTO `activity_logs` VALUES("42",NULL,"delete","media","20","Đánh dấu xóa media: Abbott - Tầng 5 - Lotus","::1",NULL,"2025-11-26 21:07:07");
INSERT INTO `activity_logs` VALUES("43",NULL,"delete","media","19","Delete media: Cao đẳng Hòa Bình Xuân Lộc - Orchild tầng 5",NULL,NULL,"2025-11-26 21:07:19");
INSERT INTO `activity_logs` VALUES("44",NULL,"delete","media","20","Delete media: Abbott - Tầng 5 - Lotus",NULL,NULL,"2025-11-26 21:07:19");
INSERT INTO `activity_logs` VALUES("45",NULL,"delete","media","23","Delete media: VietCab tầng 6",NULL,NULL,"2025-11-26 21:07:19");
INSERT INTO `activity_logs` VALUES("46",NULL,"upload","media","24","Upload media: Abbott - Tầng 5 - Lotus",NULL,NULL,"2025-11-26 21:07:30");
INSERT INTO `activity_logs` VALUES("47",NULL,"upload","media","25","Upload media: Cao đẳng Hòa Bình Xuân Lộc - Orchild tầng 5",NULL,NULL,"2025-11-26 21:07:30");
INSERT INTO `activity_logs` VALUES("48",NULL,"upload","media","26","Upload media: VietCab tầng 6",NULL,NULL,"2025-11-26 21:07:30");
INSERT INTO `activity_logs` VALUES("49",NULL,"update","media","24","Cập nhật tên media từ \'Abbott - Tầng 5 - Lotus\' thành \'Abbort - Tầng 5 - Lotus\'","::1",NULL,"2025-11-26 21:21:20");
INSERT INTO `activity_logs` VALUES("50",NULL,"update","tv","1","Cập nhật thông tin TV: TV Basement","::1",NULL,"2025-11-26 21:21:37");
INSERT INTO `activity_logs` VALUES("51",NULL,"assign","media","24","Gán media \'Abbort - Tầng 5 - Lotus\' cho TV Basement","::1",NULL,"2025-11-26 21:21:45");
INSERT INTO `activity_logs` VALUES("52",NULL,"assign","media","25","Gán media \'Cao đẳng Hòa Bình Xuân Lộc - Orchild tầng 5\' cho TV Basement","::1",NULL,"2025-11-26 21:25:23");
INSERT INTO `activity_logs` VALUES("53",NULL,"unassign","media","25","Hủy gán media \'Cao đẳng Hòa Bình Xuân Lộc - Orchild tầng 5\' khỏi TV \'TV Basement\'","::1",NULL,"2025-11-26 21:25:57");
INSERT INTO `activity_logs` VALUES("54",NULL,"assign","media","25","Gán media \'Cao đẳng Hòa Bình Xuân Lộc - Orchild tầng 5\' cho TV Basement","::1",NULL,"2025-11-26 21:26:42");
INSERT INTO `activity_logs` VALUES("55",NULL,"unassign","media","24","Hủy gán media \'Abbort - Tầng 5 - Lotus\' khỏi TV \'TV Basement\'","::1",NULL,"2025-11-26 21:38:17");
INSERT INTO `activity_logs` VALUES("56",NULL,"unassign","media","25","Hủy gán media \'Cao đẳng Hòa Bình Xuân Lộc - Orchild tầng 5\' khỏi TV \'TV Basement\'","::1",NULL,"2025-11-26 21:38:25");
INSERT INTO `activity_logs` VALUES("57",NULL,"login",NULL,NULL,"User logged in","::1",NULL,"2025-11-27 07:37:29");
INSERT INTO `activity_logs` VALUES("58",NULL,"update","tv","1","Cập nhật thông tin TV: TV Basement","::1",NULL,"2025-11-27 07:41:33");
INSERT INTO `activity_logs` VALUES("59",NULL,"update","tv","1","Cập nhật thông tin TV: TV Basement","::1",NULL,"2025-11-27 07:43:36");
INSERT INTO `activity_logs` VALUES("60",NULL,"assign","media","24","Gán media \'Abbort - Tầng 5 - Lotus\' cho TV Basement","::1",NULL,"2025-11-27 07:43:41");
INSERT INTO `activity_logs` VALUES("61",NULL,"update","tv","2","Cập nhật thông tin TV: TV Chrysan","::1",NULL,"2025-11-27 07:43:47");
INSERT INTO `activity_logs` VALUES("62",NULL,"assign","media","24","Gán media \'Abbort - Tầng 5 - Lotus\' cho TV Chrysan","::1",NULL,"2025-11-27 07:44:06");
INSERT INTO `activity_logs` VALUES("63",NULL,"assign","media","25","Gán media \'Cao đẳng Hòa Bình Xuân Lộc - Orchild tầng 5\' cho TV Basement","::1",NULL,"2025-11-27 07:56:15");
INSERT INTO `activity_logs` VALUES("64",NULL,"unassign","media","24","Hủy gán media \'Abbort - Tầng 5 - Lotus\' khỏi TV \'TV Basement\'","::1",NULL,"2025-11-27 08:03:05");
INSERT INTO `activity_logs` VALUES("65",NULL,"unassign","media","24","Hủy gán media \'Abbort - Tầng 5 - Lotus\' khỏi TV \'TV Chrysan\'","::1",NULL,"2025-11-27 08:03:08");
INSERT INTO `activity_logs` VALUES("66",NULL,"unassign","media","25","Hủy gán media \'Cao đẳng Hòa Bình Xuân Lộc - Orchild tầng 5\' khỏi TV \'TV Basement\'","::1",NULL,"2025-11-27 08:03:53");
INSERT INTO `activity_logs` VALUES("67",NULL,"shutdown","system",NULL,"Tắt toàn bộ hệ thống - Offline tất cả TV và gỡ gán toàn bộ WCB","::1",NULL,"2025-11-27 08:04:50");
INSERT INTO `activity_logs` VALUES("68",NULL,"update","tv","3","Cập nhật thông tin TV: TV Jasmine","::1",NULL,"2025-11-27 08:06:53");
INSERT INTO `activity_logs` VALUES("69",NULL,"assign","media","24","Gán media \'Abbort - Tầng 5 - Lotus\' cho TV Jasmine","::1",NULL,"2025-11-27 08:06:58");
INSERT INTO `activity_logs` VALUES("70",NULL,"update","tv","3","Cập nhật thông tin TV: TV Jasmine","::1",NULL,"2025-11-27 08:08:07");
INSERT INTO `activity_logs` VALUES("71",NULL,"update","tv","3","Cập nhật thông tin TV: TV Jasmine","::1",NULL,"2025-11-27 08:08:23");
INSERT INTO `activity_logs` VALUES("72",NULL,"shutdown","system",NULL,"Tắt toàn bộ hệ thống - Offline tất cả TV và gỡ gán toàn bộ WCB","::1",NULL,"2025-11-27 08:10:43");
INSERT INTO `activity_logs` VALUES("73",NULL,"orchid_mode","media","25","Áp dụng chế độ Orchid - Gán \'Cao đẳng Hòa Bình Xuân Lộc - Orchild tầng 5\' cho TV Basement, TV Chrysan, TV Jasmine, TV Lotus, TV FO 1, TV FO 2 và bật tất cả TV","::1",NULL,"2025-11-27 08:11:55");
INSERT INTO `activity_logs` VALUES("74",NULL,"shutdown","system",NULL,"Tắt toàn bộ hệ thống - Offline tất cả TV và gỡ gán toàn bộ WCB","::1",NULL,"2025-11-27 08:15:05");
INSERT INTO `activity_logs` VALUES("75",NULL,"reload","tv","1","Ép tải lại TV \'TV Basement\'","::1",NULL,"2025-11-27 08:19:30");
INSERT INTO `activity_logs` VALUES("76",NULL,"reload","tv","1","Ép tải lại TV \'TV Basement\'","::1",NULL,"2025-11-27 08:19:42");
INSERT INTO `activity_logs` VALUES("77",NULL,"assign","media","25","Gán media \'Cao đẳng Hòa Bình Xuân Lộc - Orchild tầng 5\' cho TV Basement","::1",NULL,"2025-11-27 08:19:49");
INSERT INTO `activity_logs` VALUES("78",NULL,"reload","tv","1","Ép tải lại TV \'TV Basement\'","::1",NULL,"2025-11-27 08:19:53");
INSERT INTO `activity_logs` VALUES("79",NULL,"unassign","media","25","Hủy gán media \'Cao đẳng Hòa Bình Xuân Lộc - Orchild tầng 5\' khỏi TV \'TV Basement\'","::1",NULL,"2025-11-27 08:20:47");
INSERT INTO `activity_logs` VALUES("80",NULL,"reload","tv","1","Ép tải lại TV \'TV Basement\'","::1",NULL,"2025-11-27 08:20:51");
INSERT INTO `activity_logs` VALUES("81",NULL,"assign","media","25","Gán media \'Cao đẳng Hòa Bình Xuân Lộc - Orchild tầng 5\' cho TV Basement","::1",NULL,"2025-11-27 08:23:27");
INSERT INTO `activity_logs` VALUES("82",NULL,"reload","tv","1","Ép tải lại TV \'TV Basement\'","::1",NULL,"2025-11-27 08:23:31");
INSERT INTO `activity_logs` VALUES("83",NULL,"reload","tv","1","Ép tải lại TV \'TV Basement\'","::1",NULL,"2025-11-27 08:24:05");
INSERT INTO `activity_logs` VALUES("84",NULL,"orchid_mode","media","25","Áp dụng chế độ Orchid - Gán \'Cao đẳng Hòa Bình Xuân Lộc - Orchild tầng 5\' cho TV Basement, TV Chrysan, TV Jasmine, TV Lotus, TV FO 1, TV FO 2 và bật tất cả TV","::1",NULL,"2025-11-27 08:26:18");
INSERT INTO `activity_logs` VALUES("85",NULL,"shutdown","system",NULL,"Tắt toàn bộ hệ thống - Offline tất cả TV và gỡ gán toàn bộ WCB","::1",NULL,"2025-11-27 08:26:41");
INSERT INTO `activity_logs` VALUES("86",NULL,"toggle_status","tv","1","Bật TV \'TV Basement\'","::1",NULL,"2025-11-27 08:29:48");
INSERT INTO `activity_logs` VALUES("87",NULL,"toggle_status","tv","1","Tắt TV \'TV Basement\'","::1",NULL,"2025-11-27 08:29:50");
INSERT INTO `activity_logs` VALUES("88",NULL,"toggle_status","tv","1","Bật TV \'TV Basement\'","::1",NULL,"2025-11-27 08:29:52");
INSERT INTO `activity_logs` VALUES("89",NULL,"logout",NULL,NULL,"User logged out","::1",NULL,"2025-11-27 08:32:34");
INSERT INTO `activity_logs` VALUES("90",NULL,"login",NULL,NULL,"User logged in","::1",NULL,"2025-11-27 08:32:41");
INSERT INTO `activity_logs` VALUES("91",NULL,"toggle_status","tv","1","Tắt TV \'TV Basement\'","::1",NULL,"2025-11-27 08:32:50");
INSERT INTO `activity_logs` VALUES("92",NULL,"logout",NULL,NULL,"User logged out","::1",NULL,"2025-11-27 08:36:02");
INSERT INTO `activity_logs` VALUES("93",NULL,"login",NULL,NULL,"User logged in","::1",NULL,"2025-11-27 08:36:07");
INSERT INTO `activity_logs` VALUES("94",NULL,"reload","tv","1","Ép tải lại TV \'TV Basement\'","::1",NULL,"2025-11-27 08:38:48");
INSERT INTO `activity_logs` VALUES("95",NULL,"toggle_status","tv","1","Bật TV \'TV Basement\'","::1",NULL,"2025-11-27 08:44:00");
INSERT INTO `activity_logs` VALUES("96",NULL,"assign","media","25","Gán media \'Cao đẳng Hòa Bình Xuân Lộc - Orchild tầng 5\' cho TV Basement","::1",NULL,"2025-11-27 08:44:54");
INSERT INTO `activity_logs` VALUES("97",NULL,"reload","tv","1","Ép tải lại TV \'TV Basement\'","::1",NULL,"2025-11-27 08:45:03");
INSERT INTO `activity_logs` VALUES("98",NULL,"toggle_status","tv","1","Tắt TV \'TV Basement\'","::1",NULL,"2025-11-27 08:45:11");
INSERT INTO `activity_logs` VALUES("99",NULL,"shutdown","system",NULL,"Tắt toàn bộ hệ thống - Offline tất cả TV và gỡ gán toàn bộ WCB","::1",NULL,"2025-11-27 08:45:31");
INSERT INTO `activity_logs` VALUES("100",NULL,"toggle_status","tv","6","Bật TV \'TV FO 1\'","::1",NULL,"2025-11-27 08:47:55");
INSERT INTO `activity_logs` VALUES("101",NULL,"reload","tv","6","Ép tải lại TV \'TV FO 1\'","::1",NULL,"2025-11-27 08:48:01");
INSERT INTO `activity_logs` VALUES("102",NULL,"assign","media","26","Gán media \'VietCab tầng 6\' cho TV FO 1","::1",NULL,"2025-11-27 08:48:04");
INSERT INTO `activity_logs` VALUES("103",NULL,"unassign","media","26","Hủy gán media \'VietCab tầng 6\' khỏi TV \'TV FO 1\'","::1",NULL,"2025-11-27 08:48:16");
INSERT INTO `activity_logs` VALUES("104",NULL,"reload","tv","6","Ép tải lại TV \'TV FO 1\'","::1",NULL,"2025-11-27 08:48:27");
INSERT INTO `activity_logs` VALUES("105",NULL,"toggle_status","tv","6","Tắt TV \'TV FO 1\'","::1",NULL,"2025-11-27 08:48:31");
INSERT INTO `activity_logs` VALUES("106",NULL,"toggle_status","tv","2","Bật TV \'TV Chrysan\'","::1",NULL,"2025-11-27 08:54:58");
INSERT INTO `activity_logs` VALUES("107",NULL,"assign","media","25","Gán media \'Cao đẳng Hòa Bình Xuân Lộc - Orchild tầng 5\' cho TV Chrysan","::1",NULL,"2025-11-27 08:55:02");
INSERT INTO `activity_logs` VALUES("108",NULL,"unassign","media","25","Hủy gán media \'Cao đẳng Hòa Bình Xuân Lộc - Orchild tầng 5\' khỏi TV \'TV Chrysan\'","::1",NULL,"2025-11-27 08:55:10");
INSERT INTO `activity_logs` VALUES("109",NULL,"assign","media","24","Gán media \'Abbort - Tầng 5 - Lotus\' cho TV Basement","::1",NULL,"2025-11-27 08:55:33");
INSERT INTO `activity_logs` VALUES("110",NULL,"assign","media","26","Gán media \'VietCab tầng 6\' cho TV Basement","::1",NULL,"2025-11-27 08:55:33");
INSERT INTO `activity_logs` VALUES("111",NULL,"assign","media","25","Gán media \'Cao đẳng Hòa Bình Xuân Lộc - Orchild tầng 5\' cho TV Basement","::1",NULL,"2025-11-27 08:55:33");
INSERT INTO `activity_logs` VALUES("112",NULL,"toggle_status","tv","1","Bật TV \'TV Basement\'","::1",NULL,"2025-11-27 08:55:39");
INSERT INTO `activity_logs` VALUES("113",NULL,"unassign","media","24","Hủy gán media \'Abbort - Tầng 5 - Lotus\' khỏi TV \'TV Basement\'","::1",NULL,"2025-11-27 08:56:07");
INSERT INTO `activity_logs` VALUES("114",NULL,"unassign","media","25","Hủy gán media \'Cao đẳng Hòa Bình Xuân Lộc - Orchild tầng 5\' khỏi TV \'TV Basement\'","::1",NULL,"2025-11-27 08:56:10");
INSERT INTO `activity_logs` VALUES("115",NULL,"unassign","media","26","Hủy gán media \'VietCab tầng 6\' khỏi TV \'TV Basement\'","::1",NULL,"2025-11-27 08:56:11");
INSERT INTO `activity_logs` VALUES("116",NULL,"toggle_status","tv","1","Tắt TV \'TV Basement\'","::1",NULL,"2025-11-27 08:56:14");
INSERT INTO `activity_logs` VALUES("117",NULL,"toggle_status","tv","2","Tắt TV \'TV Chrysan\'","::1",NULL,"2025-11-27 08:58:02");
INSERT INTO `activity_logs` VALUES("118",NULL,"toggle_status","tv","1","Bật TV \'TV Basement\'","::1",NULL,"2025-11-27 09:03:19");
INSERT INTO `activity_logs` VALUES("119",NULL,"toggle_status","tv","1","Tắt TV \'TV Basement\'","::1",NULL,"2025-11-27 09:03:26");
INSERT INTO `activity_logs` VALUES("120",NULL,"orchid_mode","media","26","Áp dụng chế độ Orchid - Gán \'VietCab tầng 6\' cho TV Basement, TV Chrysan, TV Jasmine, TV Lotus, TV FO 1, TV FO 2 và bật tất cả TV","::1",NULL,"2025-11-27 09:04:05");
INSERT INTO `activity_logs` VALUES("121",NULL,"shutdown","system",NULL,"Tắt toàn bộ hệ thống - Offline tất cả TV và gỡ gán toàn bộ WCB","::1",NULL,"2025-11-27 09:04:20");
INSERT INTO `activity_logs` VALUES("122",NULL,"toggle_status","tv","6","Bật TV \'TV FO 1\'","::1",NULL,"2025-11-27 09:08:26");
INSERT INTO `activity_logs` VALUES("123",NULL,"toggle_status","tv","6","Tắt TV \'TV FO 1\'","::1",NULL,"2025-11-27 09:08:29");
INSERT INTO `activity_logs` VALUES("124",NULL,"toggle_status","tv","2","Bật TV \'TV Chrysan\'","::1",NULL,"2025-11-27 09:08:52");
INSERT INTO `activity_logs` VALUES("125",NULL,"toggle_status","tv","2","Tắt TV \'TV Chrysan\'","::1",NULL,"2025-11-27 09:09:37");
INSERT INTO `activity_logs` VALUES("126",NULL,"toggle_status","tv","1","Bật TV \'TV Basement\'","::1",NULL,"2025-11-27 09:14:44");
INSERT INTO `activity_logs` VALUES("127",NULL,"toggle_status","tv","1","Tắt TV \'TV Basement\'","::1",NULL,"2025-11-27 09:15:30");
INSERT INTO `activity_logs` VALUES("128",NULL,"login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 08:23:59");
INSERT INTO `activity_logs` VALUES("129",NULL,"delete","media","26","Đánh dấu xóa media: VietCab tầng 6","::1",NULL,"2025-12-01 08:24:22");
INSERT INTO `activity_logs` VALUES("130",NULL,"delete","media","25","Đánh dấu xóa media: Cao đẳng Hòa Bình Xuân Lộc - Orchild tầng 5","::1",NULL,"2025-12-01 08:24:24");
INSERT INTO `activity_logs` VALUES("131",NULL,"delete","media","24","Đánh dấu xóa media: Abbort - Tầng 5 - Lotus","::1",NULL,"2025-12-01 08:24:27");
INSERT INTO `activity_logs` VALUES("132",NULL,"login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 08:39:45");
INSERT INTO `activity_logs` VALUES("133",NULL,"toggle_status","tv","1","Bật TV \'TV Basement\'","::1",NULL,"2025-12-01 08:42:27");
INSERT INTO `activity_logs` VALUES("134",NULL,"logout",NULL,NULL,"User logged out","::1",NULL,"2025-12-01 08:44:39");
INSERT INTO `activity_logs` VALUES("135",NULL,"login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 08:44:46");
INSERT INTO `activity_logs` VALUES("136",NULL,"toggle_status","tv","6","Bật TV \'TV FO 1\'","::1",NULL,"2025-12-01 08:47:04");
INSERT INTO `activity_logs` VALUES("137",NULL,"toggle_status","tv","6","Tắt TV \'TV FO 1\'","::1",NULL,"2025-12-01 08:47:17");
INSERT INTO `activity_logs` VALUES("138",NULL,"login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 08:54:01");
INSERT INTO `activity_logs` VALUES("139",NULL,"login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 08:56:01");
INSERT INTO `activity_logs` VALUES("140",NULL,"logout",NULL,NULL,"User logged out","::1",NULL,"2025-12-01 08:59:03");
INSERT INTO `activity_logs` VALUES("141",NULL,"login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 08:59:17");
INSERT INTO `activity_logs` VALUES("142",NULL,"toggle_status","tv","1","Tắt TV \'TV Basement\'","::1",NULL,"2025-12-01 08:59:25");
INSERT INTO `activity_logs` VALUES("143",NULL,"logout",NULL,NULL,"User logged out","::1",NULL,"2025-12-01 08:59:53");
INSERT INTO `activity_logs` VALUES("144",NULL,"login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 09:26:25");
INSERT INTO `activity_logs` VALUES("145",NULL,"login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 09:34:43");
INSERT INTO `activity_logs` VALUES("146",NULL,"login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 09:59:15");
INSERT INTO `activity_logs` VALUES("147",NULL,"logout",NULL,NULL,"User logged out","::1",NULL,"2025-12-01 10:00:10");
INSERT INTO `activity_logs` VALUES("148",NULL,"login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 10:04:15");
INSERT INTO `activity_logs` VALUES("149",NULL,"login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 10:06:33");
INSERT INTO `activity_logs` VALUES("150",NULL,"login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 10:07:57");
INSERT INTO `activity_logs` VALUES("151",NULL,"login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 10:08:46");
INSERT INTO `activity_logs` VALUES("152",NULL,"login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 10:19:13");
INSERT INTO `activity_logs` VALUES("153",NULL,"login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 10:19:44");
INSERT INTO `activity_logs` VALUES("154",NULL,"logout",NULL,NULL,"User logged out","::1",NULL,"2025-12-01 10:19:52");
INSERT INTO `activity_logs` VALUES("155",NULL,"login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 10:19:57");
INSERT INTO `activity_logs` VALUES("156",NULL,"login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 10:20:40");
INSERT INTO `activity_logs` VALUES("157",NULL,"toggle_status","tv","2","Bật TV \'TV Chrysan\'","::1",NULL,"2025-12-01 10:23:55");
INSERT INTO `activity_logs` VALUES("158",NULL,"toggle_status","tv","2","Tắt TV \'TV Chrysan\'","::1",NULL,"2025-12-01 10:23:56");
INSERT INTO `activity_logs` VALUES("159",NULL,"toggle_status","tv","1","Bật TV \'TV Basement\'","::1",NULL,"2025-12-01 10:24:03");
INSERT INTO `activity_logs` VALUES("160",NULL,"toggle_status","tv","1","Tắt TV \'TV Basement\'","::1",NULL,"2025-12-01 10:24:04");
INSERT INTO `activity_logs` VALUES("161",NULL,"toggle_status","tv","1","Bật TV \'TV Basement\'","::1",NULL,"2025-12-01 10:30:49");
INSERT INTO `activity_logs` VALUES("162",NULL,"toggle_status","tv","2","Bật TV \'TV Chrysan\'","::1",NULL,"2025-12-01 10:30:51");
INSERT INTO `activity_logs` VALUES("163",NULL,"toggle_status","tv","3","Bật TV \'TV Jasmine\'","::1",NULL,"2025-12-01 10:30:52");
INSERT INTO `activity_logs` VALUES("164",NULL,"reload","tv","3","Ép tải lại TV \'TV Jasmine\'","::1",NULL,"2025-12-01 10:31:03");
INSERT INTO `activity_logs` VALUES("165",NULL,"reload","tv","3","Ép tải lại TV \'TV Jasmine\'","::1",NULL,"2025-12-01 10:31:14");
INSERT INTO `activity_logs` VALUES("166",NULL,"shutdown","system",NULL,"Tắt toàn bộ hệ thống - Offline tất cả TV và gỡ gán toàn bộ WCB","::1",NULL,"2025-12-01 10:33:23");
INSERT INTO `activity_logs` VALUES("167",NULL,"create_backup","backup","0","Created backup: backup_2025-12-01_104505.sql.gz","::1",NULL,"2025-12-01 10:45:05");
INSERT INTO `activity_logs` VALUES("168",NULL,"create_user","user","10","Tạo người dùng: quanglong","::1",NULL,"2025-12-01 11:02:01");
INSERT INTO `activity_logs` VALUES("169",NULL,"logout",NULL,NULL,"User logged out","::1",NULL,"2025-12-01 11:02:09");
INSERT INTO `activity_logs` VALUES("170","10","login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 11:02:20");
INSERT INTO `activity_logs` VALUES("171","10","create_user","user","11","Tạo người dùng: salemanager","::1",NULL,"2025-12-01 11:03:22");
INSERT INTO `activity_logs` VALUES("172","10","logout",NULL,NULL,"User logged out","::1",NULL,"2025-12-01 11:03:26");
INSERT INTO `activity_logs` VALUES("173",NULL,"login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 11:03:29");
INSERT INTO `activity_logs` VALUES("174",NULL,"logout",NULL,NULL,"User logged out","::1",NULL,"2025-12-01 11:04:03");
INSERT INTO `activity_logs` VALUES("175",NULL,"login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 11:04:10");
INSERT INTO `activity_logs` VALUES("176",NULL,"logout",NULL,NULL,"User logged out","::1",NULL,"2025-12-01 11:06:42");
INSERT INTO `activity_logs` VALUES("177",NULL,"login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 11:06:56");
INSERT INTO `activity_logs` VALUES("178",NULL,"logout",NULL,NULL,"User logged out","::1",NULL,"2025-12-01 11:08:05");
INSERT INTO `activity_logs` VALUES("179",NULL,"login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 11:08:09");
INSERT INTO `activity_logs` VALUES("180",NULL,"update_user","user","11","Cập nhật người dùng: salemanager","::1",NULL,"2025-12-01 11:09:15");
INSERT INTO `activity_logs` VALUES("181",NULL,"logout",NULL,NULL,"User logged out","::1",NULL,"2025-12-01 11:09:19");
INSERT INTO `activity_logs` VALUES("182","10","login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 11:09:43");
INSERT INTO `activity_logs` VALUES("183","10","update_user","user","11","Cập nhật người dùng: salemanager","::1",NULL,"2025-12-01 11:09:50");
INSERT INTO `activity_logs` VALUES("184","10","create_backup","backup","0","Created backup: backup_2025-12-01_111147.sql.gz","::1",NULL,"2025-12-01 11:11:47");
INSERT INTO `activity_logs` VALUES("185","10","delete_backup","backup","0","Deleted backup: backup_2025-12-01_104505.sql.gz","::1",NULL,"2025-12-01 11:11:50");
INSERT INTO `activity_logs` VALUES("186","10","reset_password","user","11","Đặt lại mật khẩu cho: salemanager","::1",NULL,"2025-12-01 11:13:11");
INSERT INTO `activity_logs` VALUES("187","10","logout",NULL,NULL,"User logged out","::1",NULL,"2025-12-01 11:13:25");
INSERT INTO `activity_logs` VALUES("188",NULL,"login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 11:13:32");
INSERT INTO `activity_logs` VALUES("189",NULL,"change_password",NULL,NULL,"Changed password","::1",NULL,"2025-12-01 11:13:59");
INSERT INTO `activity_logs` VALUES("190",NULL,"logout",NULL,NULL,"User logged out","::1",NULL,"2025-12-01 11:16:33");
INSERT INTO `activity_logs` VALUES("191",NULL,"login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 11:16:36");
INSERT INTO `activity_logs` VALUES("192",NULL,"update_user","user","11","Cập nhật người dùng: salemanager","::1",NULL,"2025-12-01 11:16:57");
INSERT INTO `activity_logs` VALUES("193",NULL,"logout",NULL,NULL,"User logged out","::1",NULL,"2025-12-01 11:16:59");
INSERT INTO `activity_logs` VALUES("194",NULL,"login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 11:17:05");
INSERT INTO `activity_logs` VALUES("195",NULL,"logout",NULL,NULL,"User logged out","::1",NULL,"2025-12-01 11:26:07");
INSERT INTO `activity_logs` VALUES("196",NULL,"login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 11:26:11");
INSERT INTO `activity_logs` VALUES("197",NULL,"logout",NULL,NULL,"User logged out","::1",NULL,"2025-12-01 11:29:50");
INSERT INTO `activity_logs` VALUES("198",NULL,"login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 11:29:56");
INSERT INTO `activity_logs` VALUES("199",NULL,"logout",NULL,NULL,"User logged out","::1",NULL,"2025-12-01 11:39:25");
INSERT INTO `activity_logs` VALUES("200",NULL,"login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 11:39:29");
INSERT INTO `activity_logs` VALUES("201",NULL,"login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 14:24:27");
INSERT INTO `activity_logs` VALUES("202",NULL,"toggle_status","tv","1","Tắt TV \'TV Basement\'","::1",NULL,"2025-12-01 14:24:49");
INSERT INTO `activity_logs` VALUES("203","10","login",NULL,NULL,"User logged in","115.74.225.100",NULL,"2025-12-02 07:17:53");
INSERT INTO `activity_logs` VALUES("204","10","create_backup","backup","0","Created database backup: backup_db_2025-12-02_071817.sql.gz","115.74.225.100",NULL,"2025-12-02 07:18:17");
INSERT INTO `activity_logs` VALUES("205","10","delete_backup","backup","0","Deleted backup: backup_db_2025-12-02_071817.sql.gz","115.74.225.100",NULL,"2025-12-02 07:18:22");
INSERT INTO `activity_logs` VALUES("206","10","delete_backup","backup","0","Deleted backup: backup_db_2025-12-01_142530.sql.gz","115.74.225.100",NULL,"2025-12-02 07:18:23");
INSERT INTO `activity_logs` VALUES("207","10","create_backup","backup","0","Created database backup: backup_db_2025-12-02_071834.sql.gz","115.74.225.100",NULL,"2025-12-02 07:18:34");
INSERT INTO `activity_logs` VALUES("208","10","toggle_status","tv","1","Bật TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-02 07:25:10");
INSERT INTO `activity_logs` VALUES("209","10","assign","media","29","Gán media \'nestle 3:12:25 chrysan\' cho TV Basement","115.74.225.100",NULL,"2025-12-02 07:25:28");
INSERT INTO `activity_logs` VALUES("210","10","reload","tv","1","Ép tải lại TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-02 07:25:37");
INSERT INTO `activity_logs` VALUES("211","10","assign","media","28","Gán media \'bgd-29:11:25 lotus\' cho TV Basement","115.74.225.100",NULL,"2025-12-02 07:25:55");
INSERT INTO `activity_logs` VALUES("212","10","assign","media","27","Gán media \'bgd-29:11:25 lotus 2\' cho TV Basement","115.74.225.100",NULL,"2025-12-02 07:26:08");
INSERT INTO `activity_logs` VALUES("213","10","toggle_status","tv","2","Bật TV \'TV Chrysan\'","115.74.225.100",NULL,"2025-12-02 07:26:12");
INSERT INTO `activity_logs` VALUES("214","10","assign","media","29","Gán media \'nestle 3:12:25 chrysan\' cho TV Chrysan","115.74.225.100",NULL,"2025-12-02 07:26:15");
INSERT INTO `activity_logs` VALUES("215","10","toggle_status","tv","1","Tắt TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-02 07:27:12");
INSERT INTO `activity_logs` VALUES("216","10","toggle_status","tv","1","Tắt TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-02 07:27:15");
INSERT INTO `activity_logs` VALUES("217","10","reload","tv","1","Ép tải lại TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-02 07:27:21");
INSERT INTO `activity_logs` VALUES("218","10","toggle_status","tv","1","Bật TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-02 07:28:45");
INSERT INTO `activity_logs` VALUES("219","10","shutdown","system",NULL,"Tắt toàn bộ hệ thống - Offline tất cả TV và gỡ gán toàn bộ WCB","115.74.225.100",NULL,"2025-12-02 07:28:52");
INSERT INTO `activity_logs` VALUES("220","10","reload","tv","1","Ép tải lại TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-02 07:31:39");
INSERT INTO `activity_logs` VALUES("221","10","toggle_status","tv","1","Bật TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-02 07:31:42");
INSERT INTO `activity_logs` VALUES("222","10","reload","tv","1","Ép tải lại TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-02 07:31:48");
INSERT INTO `activity_logs` VALUES("223","10","assign","media","29","Gán media \'nestle 3:12:25 chrysan\' cho TV Basement","115.74.225.100",NULL,"2025-12-02 07:31:53");
INSERT INTO `activity_logs` VALUES("224","10","reload","tv","1","Ép tải lại TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-02 07:31:56");
INSERT INTO `activity_logs` VALUES("225","10","toggle_status","tv","1","Tắt TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-02 07:36:31");
INSERT INTO `activity_logs` VALUES("226","10","reload","tv","1","Ép tải lại TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-02 07:36:35");
INSERT INTO `activity_logs` VALUES("227","10","toggle_status","tv","1","Bật TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-02 07:38:02");
INSERT INTO `activity_logs` VALUES("228","10","reload","tv","1","Ép tải lại TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-02 07:41:27");
INSERT INTO `activity_logs` VALUES("229","10","reload","tv","1","Ép tải lại TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-02 07:41:36");
INSERT INTO `activity_logs` VALUES("230","10","reload","tv","1","Ép tải lại TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-02 07:41:43");
INSERT INTO `activity_logs` VALUES("231","10","toggle_status","tv","1","Tắt TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-02 07:41:46");
INSERT INTO `activity_logs` VALUES("232","10","reload","tv","1","Ép tải lại TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-02 07:41:51");
INSERT INTO `activity_logs` VALUES("233","10","unassign","media","29","Hủy gán media \'nestle 3:12:25 chrysan\' khỏi TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-02 07:42:05");
INSERT INTO `activity_logs` VALUES("234","10","toggle_status","tv","1","Bật TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-02 07:42:10");
INSERT INTO `activity_logs` VALUES("235","10","reload","tv","1","Ép tải lại TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-02 07:42:13");
INSERT INTO `activity_logs` VALUES("236","10","assign","media","28","Gán media \'bgd-29:11:25 lotus\' cho TV Basement","115.74.225.100",NULL,"2025-12-02 07:43:15");
INSERT INTO `activity_logs` VALUES("237","10","assign","media","27","Gán media \'bgd-29:11:25 lotus 2\' cho TV Basement","115.74.225.100",NULL,"2025-12-02 07:43:15");
INSERT INTO `activity_logs` VALUES("238","10","assign","media","29","Gán media \'nestle 3:12:25 chrysan\' cho TV Basement","115.74.225.100",NULL,"2025-12-02 07:43:15");
INSERT INTO `activity_logs` VALUES("239","10","reload","tv","1","Ép tải lại TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-02 07:43:18");
INSERT INTO `activity_logs` VALUES("240","10","assign","media","29","Gán media \'nestle 3:12:25 chrysan\' cho TV Chrysan","115.74.225.100",NULL,"2025-12-02 07:43:40");
INSERT INTO `activity_logs` VALUES("241","10","reload","tv","2","Ép tải lại TV \'TV Chrysan\'","115.74.225.100",NULL,"2025-12-02 07:43:49");
INSERT INTO `activity_logs` VALUES("242","10","toggle_status","tv","4","Bật TV \'TV Lotus\'","115.74.225.100",NULL,"2025-12-02 07:43:52");
INSERT INTO `activity_logs` VALUES("243","10","assign","media","28","Gán media \'bgd-29:11:25 lotus\' cho TV Lotus","115.74.225.100",NULL,"2025-12-02 07:44:07");
INSERT INTO `activity_logs` VALUES("244","10","reload","tv","4","Ép tải lại TV \'TV Lotus\'","115.74.225.100",NULL,"2025-12-02 07:44:10");
INSERT INTO `activity_logs` VALUES("245","10","toggle_status","tv","1","Tắt TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-02 07:44:45");
INSERT INTO `activity_logs` VALUES("246","10","toggle_status","tv","2","Tắt TV \'TV Chrysan\'","115.74.225.100",NULL,"2025-12-02 07:44:46");
INSERT INTO `activity_logs` VALUES("247","10","toggle_status","tv","4","Tắt TV \'TV Lotus\'","115.74.225.100",NULL,"2025-12-02 07:44:48");
INSERT INTO `activity_logs` VALUES("248","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","115.74.225.100",NULL,"2025-12-02 07:44:56");
INSERT INTO `activity_logs` VALUES("249","10","toggle_status","tv","2","Tắt TV \'TV Chrysan\'","115.74.225.100",NULL,"2025-12-02 07:45:05");
INSERT INTO `activity_logs` VALUES("250","10","reload","tv","1","Ép tải lại TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-02 07:45:08");
INSERT INTO `activity_logs` VALUES("251","10","toggle_status","tv","1","Tắt TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-02 07:45:25");
INSERT INTO `activity_logs` VALUES("252","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","115.74.225.100",NULL,"2025-12-02 07:45:28");
INSERT INTO `activity_logs` VALUES("253","10","toggle_status","tv","4","Tắt TV \'TV Lotus\'","115.74.225.100",NULL,"2025-12-02 07:45:32");
INSERT INTO `activity_logs` VALUES("254","10","toggle_status","tv","2","Tắt TV \'TV Chrysan\'","115.74.225.100",NULL,"2025-12-02 07:45:34");
INSERT INTO `activity_logs` VALUES("255","10","toggle_status","tv","1","Tắt TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-02 07:45:37");
INSERT INTO `activity_logs` VALUES("256","10","reload","tv","1","Ép tải lại TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-02 07:45:43");
INSERT INTO `activity_logs` VALUES("257","10","unassign","media","28","Hủy gán media \'bgd-29:11:25 lotus\' khỏi TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-02 07:47:02");
INSERT INTO `activity_logs` VALUES("258","10","unassign","media","27","Hủy gán media \'bgd-29:11:25 lotus 2\' khỏi TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-02 07:47:07");
INSERT INTO `activity_logs` VALUES("259","10","unassign","media","29","Hủy gán media \'nestle 3:12:25 chrysan\' khỏi TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-02 07:47:11");
INSERT INTO `activity_logs` VALUES("260","10","reload","tv","1","Ép tải lại TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-02 07:47:17");
INSERT INTO `activity_logs` VALUES("261","10","assign","media","29","Gán media \'nestle 3:12:25 chrysan\' cho TV Basement","115.74.225.100",NULL,"2025-12-02 07:48:30");
INSERT INTO `activity_logs` VALUES("262","10","reload","tv","1","Ép tải lại TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-02 07:48:33");
INSERT INTO `activity_logs` VALUES("263","10","toggle_status","tv","2","Tắt TV \'TV Chrysan\'","115.74.225.100",NULL,"2025-12-02 07:48:46");
INSERT INTO `activity_logs` VALUES("264","10","toggle_status","tv","4","Tắt TV \'TV Lotus\'","115.74.225.100",NULL,"2025-12-02 07:48:50");
INSERT INTO `activity_logs` VALUES("265","10","reload","tv","2","Ép tải lại TV \'TV Chrysan\'","115.74.225.100",NULL,"2025-12-02 07:48:55");
INSERT INTO `activity_logs` VALUES("266","10","unassign","media","29","Hủy gán media \'nestle 3:12:25 chrysan\' khỏi TV \'TV Chrysan\'","115.74.225.100",NULL,"2025-12-02 07:49:15");
INSERT INTO `activity_logs` VALUES("267","10","reload","tv","2","Ép tải lại TV \'TV Chrysan\'","115.74.225.100",NULL,"2025-12-02 07:49:21");
INSERT INTO `activity_logs` VALUES("268","10","shutdown","system",NULL,"Tắt toàn bộ hệ thống - Offline tất cả TV và gỡ gán toàn bộ WCB","115.74.225.100",NULL,"2025-12-02 07:50:20");
INSERT INTO `activity_logs` VALUES("269","10","reload","tv","1","Ép tải lại TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-02 07:50:23");
INSERT INTO `activity_logs` VALUES("270","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","115.74.225.100",NULL,"2025-12-02 07:50:25");
INSERT INTO `activity_logs` VALUES("271","10","shutdown","system",NULL,"Tắt toàn bộ hệ thống - Offline tất cả TV và gỡ gán toàn bộ WCB","115.74.225.100",NULL,"2025-12-02 07:54:07");
INSERT INTO `activity_logs` VALUES("272","10","assign","media","29","Gán media \'nestle 3:12:25 chrysan\' cho TV Basement","115.74.225.100",NULL,"2025-12-02 07:54:17");
INSERT INTO `activity_logs` VALUES("273","10","resume","tv","1","Tiếp tục chiếu TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-02 07:54:21");
INSERT INTO `activity_logs` VALUES("274","10","reload","tv","1","Ép tải lại TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-02 07:54:25");
INSERT INTO `activity_logs` VALUES("275","10","toggle_status","tv","1","Tắt TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-02 07:54:27");
INSERT INTO `activity_logs` VALUES("276","10","reload","tv","1","Ép tải lại TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-02 07:54:30");
INSERT INTO `activity_logs` VALUES("277","10","toggle_status","tv","1","Bật TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-02 07:54:41");
INSERT INTO `activity_logs` VALUES("278","10","resume","tv","1","Tiếp tục chiếu TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-02 07:54:45");
INSERT INTO `activity_logs` VALUES("279","10","unassign","media","29","Hủy gán media \'nestle 3:12:25 chrysan\' khỏi TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-02 07:54:54");
INSERT INTO `activity_logs` VALUES("280","10","reload","tv","1","Ép tải lại TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-02 07:55:07");
INSERT INTO `activity_logs` VALUES("281","10","toggle_status","tv","2","Tắt TV \'TV Chrysan\'","115.74.225.100",NULL,"2025-12-02 07:55:14");
INSERT INTO `activity_logs` VALUES("282","10","orchid_mode","media","29","Áp dụng chế độ Orchid - Gán \'nestle 3:12:25 chrysan\' cho TV Basement, TV Chrysan, TV Jasmine, TV Lotus, TV FO 1, TV FO 2 và bật tất cả TV","115.74.225.100",NULL,"2025-12-02 07:55:30");
INSERT INTO `activity_logs` VALUES("283","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","115.74.225.100",NULL,"2025-12-02 07:55:35");
INSERT INTO `activity_logs` VALUES("284","10","shutdown","system",NULL,"Tắt toàn bộ hệ thống - Offline tất cả TV và gỡ gán toàn bộ WCB","115.74.225.100",NULL,"2025-12-02 07:55:41");
INSERT INTO `activity_logs` VALUES("285","10","create_user","user","12","Tạo người dùng: tv","115.74.225.100",NULL,"2025-12-02 07:59:31");
INSERT INTO `activity_logs` VALUES("286",NULL,"login",NULL,NULL,"User logged in","123.31.116.145",NULL,"2025-12-02 08:00:16");
INSERT INTO `activity_logs` VALUES("287","10","logout",NULL,NULL,"User logged out","115.74.225.100",NULL,"2025-12-02 08:00:31");
INSERT INTO `activity_logs` VALUES("288",NULL,"login",NULL,NULL,"User logged in","115.74.225.100",NULL,"2025-12-02 08:00:39");
INSERT INTO `activity_logs` VALUES("289","10","login",NULL,NULL,"User logged in","115.74.225.100",NULL,"2025-12-02 08:00:56");
INSERT INTO `activity_logs` VALUES("290","10","logout",NULL,NULL,"User logged out","115.74.225.100",NULL,"2025-12-02 08:01:01");
INSERT INTO `activity_logs` VALUES("291",NULL,"login",NULL,NULL,"User logged in","115.74.225.100",NULL,"2025-12-02 08:01:08");
INSERT INTO `activity_logs` VALUES("292","10","login",NULL,NULL,"User logged in","123.31.116.145",NULL,"2025-12-02 08:02:09");
INSERT INTO `activity_logs` VALUES("293","10","login",NULL,NULL,"User logged in","115.74.225.100",NULL,"2025-12-02 08:02:45");
INSERT INTO `activity_logs` VALUES("294","10","reload","tv","2","Ép tải lại TV \'TV Chrysan\'","115.74.225.100",NULL,"2025-12-02 08:05:32");
INSERT INTO `activity_logs` VALUES("295","10","assign","media","29","Gán media \'nestle 3:12:25 chrysan\' cho TV Chrysan","115.74.225.100",NULL,"2025-12-02 08:05:40");
INSERT INTO `activity_logs` VALUES("296","10","reload","tv","2","Ép tải lại TV \'TV Chrysan\'","115.74.225.100",NULL,"2025-12-02 08:05:43");
INSERT INTO `activity_logs` VALUES("297","10","reload","tv","2","Ép tải lại TV \'TV Chrysan\'","115.74.225.100",NULL,"2025-12-02 08:06:01");
INSERT INTO `activity_logs` VALUES("298","10","assign","media","27","Gán media \'bgd-29:11:25 lotus 2\' cho TV Chrysan","115.74.225.100",NULL,"2025-12-02 08:06:22");
INSERT INTO `activity_logs` VALUES("299","10","assign","media","28","Gán media \'bgd-29:11:25 lotus\' cho TV Chrysan","115.74.225.100",NULL,"2025-12-02 08:06:22");
INSERT INTO `activity_logs` VALUES("300","10","reload","tv","2","Ép tải lại TV \'TV Chrysan\'","115.74.225.100",NULL,"2025-12-02 08:06:29");
INSERT INTO `activity_logs` VALUES("301","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","115.74.225.100",NULL,"2025-12-02 08:07:25");
INSERT INTO `activity_logs` VALUES("302","10","shutdown","system",NULL,"Tắt toàn bộ hệ thống - Offline tất cả TV và gỡ gán toàn bộ WCB","115.74.225.100",NULL,"2025-12-02 08:07:38");
INSERT INTO `activity_logs` VALUES("303","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","115.74.225.100",NULL,"2025-12-02 08:07:42");
INSERT INTO `activity_logs` VALUES("304","10","assign","media","29","Gán media \'nestle 3:12:25 chrysan\' cho TV Chrysan","115.74.225.100",NULL,"2025-12-02 08:13:17");
INSERT INTO `activity_logs` VALUES("305","10","reload","tv","2","Ép tải lại TV \'TV Chrysan\'","115.74.225.100",NULL,"2025-12-02 08:13:20");
INSERT INTO `activity_logs` VALUES("306","10","assign","media","28","Gán media \'bgd-29:11:25 lotus\' cho TV Jasmine","123.31.116.145",NULL,"2025-12-02 08:18:46");
INSERT INTO `activity_logs` VALUES("307","10","reload","tv","3","Ép tải lại TV \'TV Jasmine\'","123.31.116.145",NULL,"2025-12-02 08:18:49");
INSERT INTO `activity_logs` VALUES("308","10","assign","media","28","Gán media \'bgd-29:11:25 lotus\' cho TV Lotus","123.31.116.145",NULL,"2025-12-02 08:19:11");
INSERT INTO `activity_logs` VALUES("309","10","reload","tv","4","Ép tải lại TV \'TV Lotus\'","123.31.116.145",NULL,"2025-12-02 08:19:14");
INSERT INTO `activity_logs` VALUES("310","10","reload","tv","3","Ép tải lại TV \'TV Jasmine\'","123.31.116.145",NULL,"2025-12-02 08:19:23");
INSERT INTO `activity_logs` VALUES("311","10","assign","media","27","Gán media \'bgd-29:11:25 lotus 2\' cho TV Jasmine","123.31.116.145",NULL,"2025-12-02 08:20:06");
INSERT INTO `activity_logs` VALUES("312","10","unassign","media","28","Hủy gán media \'bgd-29:11:25 lotus\' khỏi TV \'TV Jasmine\'","123.31.116.145",NULL,"2025-12-02 08:20:11");
INSERT INTO `activity_logs` VALUES("313","10","reload","tv","3","Ép tải lại TV \'TV Jasmine\'","123.31.116.145",NULL,"2025-12-02 08:20:16");
INSERT INTO `activity_logs` VALUES("314","10","assign","media","29","Gán media \'nestle 3:12:25 chrysan\' cho TV Jasmine","123.31.116.145",NULL,"2025-12-02 08:20:26");
INSERT INTO `activity_logs` VALUES("315","10","reload","tv","3","Ép tải lại TV \'TV Jasmine\'","123.31.116.145",NULL,"2025-12-02 08:20:29");
INSERT INTO `activity_logs` VALUES("316","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","115.74.225.100",NULL,"2025-12-02 08:23:51");
INSERT INTO `activity_logs` VALUES("317","10","unassign","media","27","Hủy gán media \'bgd-29:11:25 lotus 2\' khỏi TV \'TV Jasmine\'","115.74.225.100",NULL,"2025-12-02 08:24:08");
INSERT INTO `activity_logs` VALUES("318","10","toggle_status","tv","3","Tắt TV \'TV Jasmine\'","115.74.225.100",NULL,"2025-12-02 08:24:14");
INSERT INTO `activity_logs` VALUES("319","10","reload","tv","3","Ép tải lại TV \'TV Jasmine\'","115.74.225.100",NULL,"2025-12-02 08:24:18");
INSERT INTO `activity_logs` VALUES("320","10","shutdown","system",NULL,"Tắt toàn bộ hệ thống - Offline tất cả TV và gỡ gán toàn bộ WCB","115.74.225.100",NULL,"2025-12-02 08:26:05");
INSERT INTO `activity_logs` VALUES("321","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","115.74.225.100",NULL,"2025-12-02 08:26:09");
INSERT INTO `activity_logs` VALUES("322","10","shutdown","system",NULL,"Tắt toàn bộ hệ thống - Offline tất cả TV và gỡ gán toàn bộ WCB","115.74.225.100",NULL,"2025-12-02 08:26:39");
INSERT INTO `activity_logs` VALUES("323","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","115.74.225.100",NULL,"2025-12-02 08:26:42");
INSERT INTO `activity_logs` VALUES("324","10","create_user","user","13","Tạo người dùng: fo","115.74.225.100",NULL,"2025-12-02 08:27:24");
INSERT INTO `activity_logs` VALUES("325","10","logout",NULL,NULL,"User logged out","115.74.225.100",NULL,"2025-12-02 08:27:26");
INSERT INTO `activity_logs` VALUES("326",NULL,"login",NULL,NULL,"User logged in","115.74.225.100",NULL,"2025-12-02 08:27:31");
INSERT INTO `activity_logs` VALUES("327",NULL,"logout",NULL,NULL,"User logged out","115.74.225.100",NULL,"2025-12-02 08:27:53");
INSERT INTO `activity_logs` VALUES("328","10","login",NULL,NULL,"User logged in","115.74.225.100",NULL,"2025-12-02 08:28:01");
INSERT INTO `activity_logs` VALUES("329","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","115.74.225.100",NULL,"2025-12-02 08:29:08");
INSERT INTO `activity_logs` VALUES("330","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","115.74.225.100",NULL,"2025-12-02 08:29:16");
INSERT INTO `activity_logs` VALUES("331","10","fullscreen_all","tv","0","Gửi lệnh fullscreen đến tất cả 7 TV","115.74.225.100",NULL,"2025-12-02 08:29:16");
INSERT INTO `activity_logs` VALUES("332","10","assign","media","28","Gán media \'bgd-29:11:25 lotus\' cho TV Basement","115.74.225.100",NULL,"2025-12-02 08:30:44");
INSERT INTO `activity_logs` VALUES("333","10","assign","media","27","Gán media \'bgd-29:11:25 lotus 2\' cho TV Basement","115.74.225.100",NULL,"2025-12-02 08:30:44");
INSERT INTO `activity_logs` VALUES("334","10","assign","media","29","Gán media \'nestle 3:12:25 chrysan\' cho TV Basement","115.74.225.100",NULL,"2025-12-02 08:30:44");
INSERT INTO `activity_logs` VALUES("335","10","assign","media","27","Gán media \'bgd-29:11:25 lotus 2\' cho TV Chrysan","115.74.225.100",NULL,"2025-12-02 08:30:48");
INSERT INTO `activity_logs` VALUES("336","10","assign","media","28","Gán media \'bgd-29:11:25 lotus\' cho TV Chrysan","115.74.225.100",NULL,"2025-12-02 08:30:48");
INSERT INTO `activity_logs` VALUES("337","10","assign","media","29","Gán media \'nestle 3:12:25 chrysan\' cho TV Chrysan","115.74.225.100",NULL,"2025-12-02 08:30:48");
INSERT INTO `activity_logs` VALUES("338","10","assign","media","29","Gán media \'nestle 3:12:25 chrysan\' cho TV Jasmine","115.74.225.100",NULL,"2025-12-02 08:30:51");
INSERT INTO `activity_logs` VALUES("339","10","assign","media","27","Gán media \'bgd-29:11:25 lotus 2\' cho TV Jasmine","115.74.225.100",NULL,"2025-12-02 08:30:51");
INSERT INTO `activity_logs` VALUES("340","10","assign","media","28","Gán media \'bgd-29:11:25 lotus\' cho TV Jasmine","115.74.225.100",NULL,"2025-12-02 08:30:51");
INSERT INTO `activity_logs` VALUES("341","10","assign","media","28","Gán media \'bgd-29:11:25 lotus\' cho TV Lotus","115.74.225.100",NULL,"2025-12-02 08:30:56");
INSERT INTO `activity_logs` VALUES("342","10","assign","media","27","Gán media \'bgd-29:11:25 lotus 2\' cho TV Lotus","115.74.225.100",NULL,"2025-12-02 08:30:56");
INSERT INTO `activity_logs` VALUES("343","10","assign","media","29","Gán media \'nestle 3:12:25 chrysan\' cho TV Lotus","115.74.225.100",NULL,"2025-12-02 08:30:56");
INSERT INTO `activity_logs` VALUES("344","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","115.74.225.100",NULL,"2025-12-02 08:31:12");
INSERT INTO `activity_logs` VALUES("345","10","login",NULL,NULL,"User logged in","115.74.225.100",NULL,"2025-12-02 09:22:08");
INSERT INTO `activity_logs` VALUES("346","10","toggle_test_mode","system","0","Bật chế độ test trên tất cả TV","115.74.225.100",NULL,"2025-12-02 09:22:15");
INSERT INTO `activity_logs` VALUES("347","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","115.74.225.100",NULL,"2025-12-02 09:22:20");
INSERT INTO `activity_logs` VALUES("348","10","toggle_test_mode","system","0","Bật chế độ test trên tất cả TV","115.74.225.100",NULL,"2025-12-02 09:25:47");
INSERT INTO `activity_logs` VALUES("349","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","115.74.225.100",NULL,"2025-12-02 09:26:16");
INSERT INTO `activity_logs` VALUES("350","10","assign","media","29","Gán media \'nestle 3:12:25 chrysan\' cho TV Restaurant","115.74.225.100",NULL,"2025-12-02 09:26:54");
INSERT INTO `activity_logs` VALUES("351","10","assign","media","27","Gán media \'bgd-29:11:25 lotus 2\' cho TV Restaurant","115.74.225.100",NULL,"2025-12-02 09:26:54");
INSERT INTO `activity_logs` VALUES("352","10","assign","media","28","Gán media \'bgd-29:11:25 lotus\' cho TV Restaurant","115.74.225.100",NULL,"2025-12-02 09:26:54");
INSERT INTO `activity_logs` VALUES("353","10","toggle_status","tv","5","Bật TV \'TV Restaurant\'","115.74.225.100",NULL,"2025-12-02 09:26:55");
INSERT INTO `activity_logs` VALUES("354","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","115.74.225.100",NULL,"2025-12-02 09:27:06");
INSERT INTO `activity_logs` VALUES("355","10","toggle_test_mode","system","0","Bật chế độ test trên tất cả TV","115.74.225.100",NULL,"2025-12-02 09:27:30");
INSERT INTO `activity_logs` VALUES("356","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","115.74.225.100",NULL,"2025-12-02 09:27:35");
INSERT INTO `activity_logs` VALUES("357","10","toggle_test_mode","system","0","Tắt chế độ test trên tất cả TV","115.74.225.100",NULL,"2025-12-02 09:28:15");
INSERT INTO `activity_logs` VALUES("358","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","115.74.225.100",NULL,"2025-12-02 09:28:19");
INSERT INTO `activity_logs` VALUES("359","10","toggle_test_mode","system","0","Bật chế độ test trên tất cả TV","113.161.147.9",NULL,"2025-12-02 09:48:38");
INSERT INTO `activity_logs` VALUES("360","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","113.161.147.9",NULL,"2025-12-02 09:48:41");
INSERT INTO `activity_logs` VALUES("361","10","resume","tv","1","Tiếp tục chiếu TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-02 10:00:30");
INSERT INTO `activity_logs` VALUES("362","10","toggle_test_mode","system","0","Bật chế độ test trên tất cả TV","115.74.225.100",NULL,"2025-12-02 10:00:46");
INSERT INTO `activity_logs` VALUES("363","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","115.74.225.100",NULL,"2025-12-02 10:00:49");
INSERT INTO `activity_logs` VALUES("364","10","fullscreen_all","tv","0","Gửi lệnh fullscreen đến tất cả 7 TV","115.74.225.100",NULL,"2025-12-02 10:00:49");
INSERT INTO `activity_logs` VALUES("365","10","create_backup","backup","0","Created full backup: backup_full_2025-12-02_100213.zip (4 files)","115.74.225.100",NULL,"2025-12-02 10:02:13");
INSERT INTO `activity_logs` VALUES("366","10","logout",NULL,NULL,"User logged out","115.74.225.100",NULL,"2025-12-02 10:03:20");
INSERT INTO `activity_logs` VALUES("367",NULL,"login",NULL,NULL,"User logged in","115.74.225.100",NULL,"2025-12-02 10:03:26");
INSERT INTO `activity_logs` VALUES("368",NULL,"logout",NULL,NULL,"User logged out","115.74.225.100",NULL,"2025-12-02 10:03:43");
INSERT INTO `activity_logs` VALUES("369","10","login",NULL,NULL,"User logged in","115.74.225.100",NULL,"2025-12-02 10:03:50");
INSERT INTO `activity_logs` VALUES("370","10","create_user","user","14","Tạo người dùng: admin","115.74.225.100",NULL,"2025-12-02 10:43:49");
INSERT INTO `activity_logs` VALUES("371","14","login",NULL,NULL,"User logged in","115.74.225.100",NULL,"2025-12-02 10:43:54");
INSERT INTO `activity_logs` VALUES("372","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","115.74.225.100",NULL,"2025-12-02 10:50:17");
INSERT INTO `activity_logs` VALUES("373","10","login",NULL,NULL,"User logged in","118.69.64.122",NULL,"2025-12-02 10:52:51");
INSERT INTO `activity_logs` VALUES("374","10","login",NULL,NULL,"User logged in","115.74.225.100",NULL,"2025-12-02 11:02:10");
INSERT INTO `activity_logs` VALUES("375","10","create_backup","backup","0","Created media backup: backup_media_2025-12-02_110318.zip (3 files)","115.74.225.100",NULL,"2025-12-02 11:03:18");
INSERT INTO `activity_logs` VALUES("376","10","toggle_status","tv","1","Tắt TV \'TV Basement\'","118.69.64.122",NULL,"2025-12-02 11:09:36");
INSERT INTO `activity_logs` VALUES("377","10","unassign","media","28","Hủy gán media \'bgd-29:11:25 lotus\' khỏi TV \'TV Basement\'","125.235.120.65",NULL,"2025-12-02 11:09:45");
INSERT INTO `activity_logs` VALUES("378","10","unassign","media","27","Hủy gán media \'bgd-29:11:25 lotus 2\' khỏi TV \'TV Basement\'","125.235.120.65",NULL,"2025-12-02 11:09:50");
INSERT INTO `activity_logs` VALUES("379","10","unassign","media","29","Hủy gán media \'nestle 3:12:25 chrysan\' khỏi TV \'TV Basement\'","125.235.120.65",NULL,"2025-12-02 11:09:55");
INSERT INTO `activity_logs` VALUES("380","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","125.235.120.65",NULL,"2025-12-02 11:10:06");
INSERT INTO `activity_logs` VALUES("381","10","toggle_test_mode","system","0","Bật chế độ test trên tất cả TV","125.235.120.65",NULL,"2025-12-02 11:10:12");
INSERT INTO `activity_logs` VALUES("382","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","125.235.120.65",NULL,"2025-12-02 11:10:15");
INSERT INTO `activity_logs` VALUES("383","10","toggle_test_mode","system","0","Tắt chế độ test trên tất cả TV","125.235.120.65",NULL,"2025-12-02 11:10:21");
INSERT INTO `activity_logs` VALUES("384","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","125.235.120.65",NULL,"2025-12-02 11:10:24");
INSERT INTO `activity_logs` VALUES("385","10","shutdown","system",NULL,"Tắt toàn bộ hệ thống - Offline tất cả TV và gỡ gán toàn bộ WCB","125.235.120.65",NULL,"2025-12-02 11:10:30");
INSERT INTO `activity_logs` VALUES("386","10","toggle_status","tv","1","Tắt TV \'TV Basement\'","125.235.120.65",NULL,"2025-12-02 11:10:46");
INSERT INTO `activity_logs` VALUES("387","10","orchid_mode","media","29","Áp dụng chế độ Orchid - Gán \'nestle 3:12:25 chrysan\' cho TV Basement, TV Chrysan, TV Jasmine, TV Lotus, TV FO 1, TV FO 2 và bật tất cả TV","125.235.120.65",NULL,"2025-12-02 11:10:56");
INSERT INTO `activity_logs` VALUES("388","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","125.235.120.65",NULL,"2025-12-02 11:11:00");
INSERT INTO `activity_logs` VALUES("389","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","125.235.120.65",NULL,"2025-12-02 11:11:08");
INSERT INTO `activity_logs` VALUES("390","10","fullscreen_all","tv","0","Gửi lệnh fullscreen đến tất cả 7 TV","125.235.120.65",NULL,"2025-12-02 11:11:08");
INSERT INTO `activity_logs` VALUES("391","10","shutdown","system",NULL,"Tắt toàn bộ hệ thống - Offline tất cả TV và gỡ gán toàn bộ WCB","125.235.120.65",NULL,"2025-12-02 11:11:14");
INSERT INTO `activity_logs` VALUES("392","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","125.235.120.65",NULL,"2025-12-02 11:11:26");
INSERT INTO `activity_logs` VALUES("393","10","orchid_mode","media","28","Áp dụng chế độ Orchid - Gán \'bgd-29:11:25 lotus\' cho TV Basement, TV Chrysan, TV Jasmine, TV Lotus, TV FO 1, TV FO 2 và bật tất cả TV","125.235.120.65",NULL,"2025-12-02 11:15:05");
INSERT INTO `activity_logs` VALUES("394","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","125.235.120.65",NULL,"2025-12-02 11:15:26");
INSERT INTO `activity_logs` VALUES("395","10","shutdown","system",NULL,"Tắt toàn bộ hệ thống - Offline tất cả TV và gỡ gán toàn bộ WCB","125.235.120.65",NULL,"2025-12-02 11:16:35");
INSERT INTO `activity_logs` VALUES("396","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","125.235.120.65",NULL,"2025-12-02 11:16:57");
INSERT INTO `activity_logs` VALUES("397","10","assign","media","28","Gán media \'bgd-29:11:25 lotus\' cho TV Chrysan","125.235.120.65",NULL,"2025-12-02 11:17:07");
INSERT INTO `activity_logs` VALUES("398","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","125.235.120.65",NULL,"2025-12-02 11:17:31");
INSERT INTO `activity_logs` VALUES("399","10","assign","media","27","Gán media \'bgd-29:11:25 lotus 2\' cho TV Chrysan","125.235.120.65",NULL,"2025-12-02 11:17:50");
INSERT INTO `activity_logs` VALUES("400","10","assign","media","29","Gán media \'nestle 3:12:25 chrysan\' cho TV Chrysan","125.235.120.65",NULL,"2025-12-02 11:17:50");
INSERT INTO `activity_logs` VALUES("401","10","orchid_mode","media","29","Áp dụng chế độ Orchid - Gán \'nestle 3:12:25 chrysan\' cho TV Basement, TV Chrysan, TV Jasmine, TV Lotus, TV FO 1, TV FO 2 và bật tất cả TV","125.235.120.65",NULL,"2025-12-02 11:18:40");
INSERT INTO `activity_logs` VALUES("402","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","125.235.120.65",NULL,"2025-12-02 11:18:46");
INSERT INTO `activity_logs` VALUES("403","10","delete","media","27","Đánh dấu xóa media: bgd-29:11:25 lotus 2","125.235.120.65",NULL,"2025-12-02 11:20:12");
INSERT INTO `activity_logs` VALUES("404","10","delete","media","28","Đánh dấu xóa media: bgd-29:11:25 lotus","125.235.120.65",NULL,"2025-12-02 11:20:15");
INSERT INTO `activity_logs` VALUES("405","10","shutdown","system",NULL,"Tắt toàn bộ hệ thống - Offline tất cả TV và gỡ gán toàn bộ WCB","125.235.120.65",NULL,"2025-12-02 11:21:27");
INSERT INTO `activity_logs` VALUES("406","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","125.235.120.65",NULL,"2025-12-02 11:21:31");
INSERT INTO `activity_logs` VALUES("407","10","toggle_test_mode","system","0","Bật chế độ test trên tất cả TV","125.235.120.65",NULL,"2025-12-02 11:21:33");
INSERT INTO `activity_logs` VALUES("408","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","125.235.120.65",NULL,"2025-12-02 11:21:36");
INSERT INTO `activity_logs` VALUES("409","10","delete","media","29","Đánh dấu xóa media: nestle 3:12:25 chrysan","125.235.120.65",NULL,"2025-12-02 11:21:57");
INSERT INTO `activity_logs` VALUES("410","10","orchid_mode","media","32","Áp dụng chế độ Orchid - Gán \'nestle 3:12:25 chrysan\' cho TV Basement, TV Chrysan, TV Jasmine, TV Lotus, TV FO 1, TV FO 2 và bật tất cả TV","125.235.120.65",NULL,"2025-12-02 11:26:34");
INSERT INTO `activity_logs` VALUES("411","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","125.235.120.65",NULL,"2025-12-02 11:28:39");
INSERT INTO `activity_logs` VALUES("412","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","125.235.120.65",NULL,"2025-12-02 11:28:50");
INSERT INTO `activity_logs` VALUES("413","10","assign","media","30","Gán media \'bgd-29:11:25 lotus 2\' cho TV Basement","125.235.120.65",NULL,"2025-12-02 11:30:56");
INSERT INTO `activity_logs` VALUES("414","10","assign","media","31","Gán media \'bgd-29:11:25 lotus\' cho TV Basement","125.235.120.65",NULL,"2025-12-02 11:30:56");
INSERT INTO `activity_logs` VALUES("415","10","assign","media","31","Gán media \'bgd-29:11:25 lotus\' cho TV Chrysan","125.235.120.65",NULL,"2025-12-02 11:31:00");
INSERT INTO `activity_logs` VALUES("416","10","assign","media","30","Gán media \'bgd-29:11:25 lotus 2\' cho TV Chrysan","125.235.120.65",NULL,"2025-12-02 11:31:00");
INSERT INTO `activity_logs` VALUES("417","10","assign","media","31","Gán media \'bgd-29:11:25 lotus\' cho TV Jasmine","125.235.120.65",NULL,"2025-12-02 11:31:04");
INSERT INTO `activity_logs` VALUES("418","10","assign","media","30","Gán media \'bgd-29:11:25 lotus 2\' cho TV Jasmine","125.235.120.65",NULL,"2025-12-02 11:31:04");
INSERT INTO `activity_logs` VALUES("419","10","assign","media","30","Gán media \'bgd-29:11:25 lotus 2\' cho TV Lotus","125.235.120.65",NULL,"2025-12-02 11:31:08");
INSERT INTO `activity_logs` VALUES("420","10","assign","media","31","Gán media \'bgd-29:11:25 lotus\' cho TV Lotus","125.235.120.65",NULL,"2025-12-02 11:31:08");
INSERT INTO `activity_logs` VALUES("421","10","assign","media","30","Gán media \'bgd-29:11:25 lotus 2\' cho TV FO 1","125.235.120.65",NULL,"2025-12-02 11:31:14");
INSERT INTO `activity_logs` VALUES("422","10","assign","media","31","Gán media \'bgd-29:11:25 lotus\' cho TV FO 1","125.235.120.65",NULL,"2025-12-02 11:31:14");
INSERT INTO `activity_logs` VALUES("423","10","toggle_test_mode","system","0","Bật chế độ test trên tất cả TV","125.235.120.65",NULL,"2025-12-02 11:33:05");
INSERT INTO `activity_logs` VALUES("424","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","125.235.120.65",NULL,"2025-12-02 11:33:09");
INSERT INTO `activity_logs` VALUES("425","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","115.74.225.100",NULL,"2025-12-02 11:43:53");
INSERT INTO `activity_logs` VALUES("426","10","toggle_test_mode","system","0","Bật chế độ test trên tất cả TV","115.74.225.100",NULL,"2025-12-02 11:43:58");
INSERT INTO `activity_logs` VALUES("427","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","115.74.225.100",NULL,"2025-12-02 11:44:01");
INSERT INTO `activity_logs` VALUES("428","14","login",NULL,NULL,"User logged in","115.74.225.100",NULL,"2025-12-02 12:12:01");
INSERT INTO `activity_logs` VALUES("429","10","login",NULL,NULL,"User logged in","115.74.225.100",NULL,"2025-12-02 12:16:55");
INSERT INTO `activity_logs` VALUES("430","10","login",NULL,NULL,"User logged in","118.69.64.122",NULL,"2025-12-02 13:15:26");
INSERT INTO `activity_logs` VALUES("431","10","shutdown","system",NULL,"Tắt toàn bộ hệ thống - Offline tất cả TV và gỡ gán toàn bộ WCB","125.235.120.59",NULL,"2025-12-02 13:17:18");
INSERT INTO `activity_logs` VALUES("432","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","125.235.120.59",NULL,"2025-12-02 13:17:20");
INSERT INTO `activity_logs` VALUES("433","10","toggle_test_mode","system","0","Bật chế độ test trên tất cả TV","125.235.120.59",NULL,"2025-12-02 13:17:26");
INSERT INTO `activity_logs` VALUES("434","10","toggle_test_mode","system","0","Bật chế độ test trên tất cả TV","125.235.120.59",NULL,"2025-12-02 13:17:32");
INSERT INTO `activity_logs` VALUES("435","10","toggle_test_mode","system","0","Tắt chế độ test trên tất cả TV","125.235.120.59",NULL,"2025-12-02 13:17:38");
INSERT INTO `activity_logs` VALUES("436","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","125.235.120.59",NULL,"2025-12-02 13:17:41");
INSERT INTO `activity_logs` VALUES("437","10","assign","media","32","Gán media \'nestle 3:12:25 chrysan\' cho TV Basement","125.235.120.59",NULL,"2025-12-02 13:18:10");
INSERT INTO `activity_logs` VALUES("438","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","125.235.120.59",NULL,"2025-12-02 13:18:29");
INSERT INTO `activity_logs` VALUES("439","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","125.235.120.59",NULL,"2025-12-02 13:18:48");
INSERT INTO `activity_logs` VALUES("440","10","fullscreen_all","tv","0","Gửi lệnh fullscreen đến tất cả 7 TV","125.235.120.59",NULL,"2025-12-02 13:18:48");
INSERT INTO `activity_logs` VALUES("441","10","orchid_mode","media","32","Áp dụng chế độ Orchid - Gán \'nestle 3:12:25 chrysan\' cho TV Basement, TV Chrysan, TV Jasmine, TV Lotus, TV FO 1, TV FO 2 và bật tất cả TV","125.235.120.59",NULL,"2025-12-02 13:19:16");
INSERT INTO `activity_logs` VALUES("442","10","orchid_mode","media","31","Áp dụng chế độ Orchid - Gán \'bgd-29:11:25 lotus\' cho TV Basement, TV Chrysan, TV Jasmine, TV Lotus, TV FO 1, TV FO 2 và bật tất cả TV","125.235.120.59",NULL,"2025-12-02 13:19:26");
INSERT INTO `activity_logs` VALUES("443","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","125.235.120.59",NULL,"2025-12-02 13:19:29");
INSERT INTO `activity_logs` VALUES("444","10","orchid_mode","media","32","Áp dụng chế độ Orchid - Gán \'nestle 3:12:25 chrysan\' cho TV Basement, TV Chrysan, TV Jasmine, TV Lotus, TV FO 1, TV FO 2 và bật tất cả TV","125.235.120.59",NULL,"2025-12-02 13:19:40");
INSERT INTO `activity_logs` VALUES("445","10","assign","media","31","Gán media \'bgd-29:11:25 lotus\' cho TV Basement","125.235.120.59",NULL,"2025-12-02 13:19:46");
INSERT INTO `activity_logs` VALUES("446","10","assign","media","30","Gán media \'bgd-29:11:25 lotus 2\' cho TV Basement","125.235.120.59",NULL,"2025-12-02 13:19:46");
INSERT INTO `activity_logs` VALUES("447","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","125.235.120.59",NULL,"2025-12-02 13:19:50");
INSERT INTO `activity_logs` VALUES("448","10","create_backup","backup","0","Created full backup: backup_full_2025-12-02_132117.zip (4 files)","125.235.120.59",NULL,"2025-12-02 13:21:17");
INSERT INTO `activity_logs` VALUES("449","10","create_backup","backup","0","Created database backup: backup_db_2025-12-02_132119.sql.gz","125.235.120.59",NULL,"2025-12-02 13:21:19");
INSERT INTO `activity_logs` VALUES("450","10","logout",NULL,NULL,"User logged out","125.235.120.59",NULL,"2025-12-02 13:21:37");
INSERT INTO `activity_logs` VALUES("451","10","login",NULL,NULL,"User logged in","125.235.120.59",NULL,"2025-12-02 13:21:41");
INSERT INTO `activity_logs` VALUES("452","10","toggle_test_mode","system","0","Bật chế độ test trên tất cả TV","125.235.120.59",NULL,"2025-12-02 13:21:58");
INSERT INTO `activity_logs` VALUES("453","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","125.235.120.59",NULL,"2025-12-02 13:22:00");
INSERT INTO `activity_logs` VALUES("454","10","orchid_mode","media","30","Áp dụng chế độ Orchid - Gán \'bgd-29:11:25 lotus 2\' cho TV Basement, TV Chrysan, TV Jasmine, TV Lotus, TV FO 1, TV FO 2 và bật tất cả TV","125.235.120.59",NULL,"2025-12-02 13:23:21");
INSERT INTO `activity_logs` VALUES("455","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","125.235.120.59",NULL,"2025-12-02 13:23:25");
INSERT INTO `activity_logs` VALUES("456","10","upload","media","33","Upload file: Ảnh màn hình 2025-12-02 lúc 13.23.58","125.235.120.59",NULL,"2025-12-02 13:24:05");
INSERT INTO `activity_logs` VALUES("457","10","delete","media","33","Đánh dấu xóa media: Ảnh màn hình 2025-12-02 lúc 13.23.58","125.235.120.59",NULL,"2025-12-02 13:24:28");
INSERT INTO `activity_logs` VALUES("458","10","login",NULL,NULL,"User logged in","115.74.225.100",NULL,"2025-12-02 14:00:43");
INSERT INTO `activity_logs` VALUES("459","10","shutdown","system",NULL,"Tắt toàn bộ hệ thống - Offline tất cả TV và gỡ gán toàn bộ WCB","115.74.225.100",NULL,"2025-12-02 14:00:52");
INSERT INTO `activity_logs` VALUES("460","10","delete","media","32","Đánh dấu xóa media: nestle 3:12:25 chrysan","115.74.225.100",NULL,"2025-12-02 14:00:56");
INSERT INTO `activity_logs` VALUES("461","10","delete","media","31","Đánh dấu xóa media: bgd-29:11:25 lotus","115.74.225.100",NULL,"2025-12-02 14:00:58");
INSERT INTO `activity_logs` VALUES("462","10","delete","media","30","Đánh dấu xóa media: bgd-29:11:25 lotus 2","115.74.225.100",NULL,"2025-12-02 14:01:01");
INSERT INTO `activity_logs` VALUES("463","10","shutdown","system",NULL,"Tắt toàn bộ hệ thống - Offline tất cả TV và gỡ gán toàn bộ WCB","115.74.225.100",NULL,"2025-12-02 14:01:49");
INSERT INTO `activity_logs` VALUES("464","10","upload","media","34","Upload file: nestle 3:12:25 chrysan","115.74.225.100",NULL,"2025-12-02 14:02:55");
INSERT INTO `activity_logs` VALUES("465","10","assign","media","34","Gán media \'nestle 3:12:25 chrysan\' cho TV Basement","115.74.225.100",NULL,"2025-12-02 14:03:11");
INSERT INTO `activity_logs` VALUES("466","10","assign","media","34","Gán media \'nestle 3:12:25 chrysan\' cho TV Chrysan","115.74.225.100",NULL,"2025-12-02 14:03:14");
INSERT INTO `activity_logs` VALUES("467","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","115.74.225.100",NULL,"2025-12-02 14:03:16");
INSERT INTO `activity_logs` VALUES("468","10","toggle_test_mode","system","0","Bật chế độ test trên tất cả TV","115.74.225.100",NULL,"2025-12-02 14:04:04");
INSERT INTO `activity_logs` VALUES("469","10","toggle_test_mode","system","0","Tắt chế độ test trên tất cả TV","115.74.225.100",NULL,"2025-12-02 14:04:06");
INSERT INTO `activity_logs` VALUES("470","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","115.74.225.100",NULL,"2025-12-02 14:04:08");
INSERT INTO `activity_logs` VALUES("471",NULL,"login",NULL,NULL,"User logged in","115.74.225.100",NULL,"2025-12-02 14:14:06");
INSERT INTO `activity_logs` VALUES("472","10","login",NULL,NULL,"User logged in","115.74.225.100",NULL,"2025-12-02 14:18:56");
INSERT INTO `activity_logs` VALUES("473","14","login",NULL,NULL,"User logged in","113.161.147.9",NULL,"2025-12-02 15:36:26");
INSERT INTO `activity_logs` VALUES("474","14","login",NULL,NULL,"User logged in","115.74.225.100",NULL,"2025-12-02 15:47:26");
INSERT INTO `activity_logs` VALUES("475","10","login",NULL,NULL,"User logged in","118.71.92.27",NULL,"2025-12-02 21:57:59");
INSERT INTO `activity_logs` VALUES("476","14","login",NULL,NULL,"User logged in","123.31.116.145",NULL,"2025-12-03 08:10:25");
INSERT INTO `activity_logs` VALUES("477","14","fullscreen_all","tv","0","Gửi lệnh fullscreen đến tất cả 7 TV","123.31.116.145",NULL,"2025-12-03 08:10:40");
INSERT INTO `activity_logs` VALUES("478","14","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","123.31.116.145",NULL,"2025-12-03 08:10:40");
INSERT INTO `activity_logs` VALUES("479","10","login",NULL,NULL,"User logged in","118.71.92.27",NULL,"2025-12-03 09:27:04");
INSERT INTO `activity_logs` VALUES("480","10","login",NULL,NULL,"User logged in","115.74.225.100",NULL,"2025-12-03 13:25:44");
INSERT INTO `activity_logs` VALUES("481","10","toggle_status","tv","6","Tắt TV \'TV FO 1\'","115.74.225.100",NULL,"2025-12-03 13:32:11");
INSERT INTO `activity_logs` VALUES("482","10","toggle_status","tv","4","Tắt TV \'TV Lotus\'","115.74.225.100",NULL,"2025-12-03 13:32:14");
INSERT INTO `activity_logs` VALUES("483","10","toggle_status","tv","3","Tắt TV \'TV Jasmine\'","115.74.225.100",NULL,"2025-12-03 13:32:15");
INSERT INTO `activity_logs` VALUES("484","10","login",NULL,NULL,"User logged in","118.69.64.122",NULL,"2025-12-03 20:16:32");
INSERT INTO `activity_logs` VALUES("485","10","shutdown","system",NULL,"Tắt toàn bộ hệ thống - Offline tất cả TV và gỡ gán toàn bộ WCB","118.69.64.122",NULL,"2025-12-03 20:16:55");
INSERT INTO `activity_logs` VALUES("486","10","toggle_status","tv","1","Bật TV \'TV Basement\'","118.69.64.122",NULL,"2025-12-03 20:17:01");
INSERT INTO `activity_logs` VALUES("487","10","toggle_status","tv","1","Tắt TV \'TV Basement\'","118.69.64.122",NULL,"2025-12-03 20:17:02");
INSERT INTO `activity_logs` VALUES("488","10","toggle_status","tv","4","Bật TV \'TV Lotus\'","118.69.64.122",NULL,"2025-12-03 20:17:24");
INSERT INTO `activity_logs` VALUES("489","10","assign","media","36","Gán media \'grimaud lotus 6:12 8h-17h\' cho TV Lotus","118.69.64.122",NULL,"2025-12-03 20:17:28");
INSERT INTO `activity_logs` VALUES("490","10","toggle_status","tv","1","Bật TV \'TV Basement\'","118.69.64.122",NULL,"2025-12-03 20:17:30");
INSERT INTO `activity_logs` VALUES("491","10","assign","media","36","Gán media \'grimaud lotus 6:12 8h-17h\' cho TV Basement","118.69.64.122",NULL,"2025-12-03 20:17:32");
INSERT INTO `activity_logs` VALUES("492","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","118.69.64.122",NULL,"2025-12-03 20:17:36");
INSERT INTO `activity_logs` VALUES("493","10","assign","media","35","Gán media \'schaeffler chrysan 8-9:12 8h-17h\' cho TV Chrysan","118.69.64.122",NULL,"2025-12-03 20:17:49");
INSERT INTO `activity_logs` VALUES("494","10","toggle_status","tv","2","Bật TV \'TV Chrysan\'","118.69.64.122",NULL,"2025-12-03 20:17:50");
INSERT INTO `activity_logs` VALUES("495","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","118.69.64.122",NULL,"2025-12-03 20:17:53");
INSERT INTO `activity_logs` VALUES("496","10","login",NULL,NULL,"User logged in","42.119.231.41",NULL,"2025-12-04 18:12:34");
INSERT INTO `activity_logs` VALUES("497","10","upload","media","37","Upload file: sở nội vụ 8-9:12 lotus","42.119.231.41",NULL,"2025-12-04 18:15:28");
INSERT INTO `activity_logs` VALUES("498","10","upload","media","38","Upload file: sở nội vụ 10-11:12 lotus","42.119.231.41",NULL,"2025-12-04 18:19:51");
INSERT INTO `activity_logs` VALUES("499","10","shutdown","system",NULL,"Tắt toàn bộ hệ thống - Offline tất cả TV và gỡ gán toàn bộ WCB","42.119.231.41",NULL,"2025-12-04 18:25:53");
INSERT INTO `activity_logs` VALUES("500","14","login",NULL,NULL,"User logged in","123.31.116.145",NULL,"2025-12-05 13:42:03");
INSERT INTO `activity_logs` VALUES("501","14","resume","tv","4","Tiếp tục chiếu TV \'TV Lotus\'","123.31.116.145",NULL,"2025-12-05 13:42:46");
INSERT INTO `activity_logs` VALUES("502","14","assign","media","36","Gán media \'grimaud lotus 6:12 8h-17h\' cho TV Lotus","123.31.116.145",NULL,"2025-12-05 13:43:24");
INSERT INTO `activity_logs` VALUES("503","14","resume","tv","4","Tiếp tục chiếu TV \'TV Lotus\'","123.31.116.145",NULL,"2025-12-05 13:43:28");
INSERT INTO `activity_logs` VALUES("504","14","assign","media","35","Gán media \'schaeffler chrysan 8-9:12 8h-17h\' cho TV Chrysan","123.31.116.145",NULL,"2025-12-05 13:44:55");
INSERT INTO `activity_logs` VALUES("505","14","resume","tv","2","Tiếp tục chiếu TV \'TV Chrysan\'","123.31.116.145",NULL,"2025-12-05 13:44:59");
INSERT INTO `activity_logs` VALUES("506","14","resume","tv","4","Tiếp tục chiếu TV \'TV Lotus\'","123.31.116.145",NULL,"2025-12-05 13:45:50");
INSERT INTO `activity_logs` VALUES("507","14","resume","tv","4","Tiếp tục chiếu TV \'TV Lotus\'","123.31.116.145",NULL,"2025-12-05 13:45:56");
INSERT INTO `activity_logs` VALUES("508","14","fullscreen_all","tv","0","Gửi lệnh fullscreen đến tất cả 7 TV","123.31.116.145",NULL,"2025-12-05 13:46:02");
INSERT INTO `activity_logs` VALUES("509","14","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","123.31.116.145",NULL,"2025-12-05 13:46:02");
INSERT INTO `activity_logs` VALUES("510","14","login",NULL,NULL,"User logged in","115.74.225.100",NULL,"2025-12-05 14:26:16");
INSERT INTO `activity_logs` VALUES("511","14","assign","media","36","Gán media \'grimaud lotus 6:12 8h-17h\' cho TV Basement","115.74.225.100",NULL,"2025-12-05 14:27:07");
INSERT INTO `activity_logs` VALUES("512","14","resume","tv","1","Tiếp tục chiếu TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-05 14:27:15");
INSERT INTO `activity_logs` VALUES("513","14","shutdown","system",NULL,"Tắt toàn bộ hệ thống - Offline tất cả TV và gỡ gán toàn bộ WCB","115.74.225.100",NULL,"2025-12-05 14:27:25");
INSERT INTO `activity_logs` VALUES("514","14","assign","media","36","Gán media \'grimaud lotus 6:12 8h-17h\' cho TV Basement","115.74.225.100",NULL,"2025-12-05 14:27:35");
INSERT INTO `activity_logs` VALUES("515","14","resume","tv","1","Tiếp tục chiếu TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-05 14:27:38");
INSERT INTO `activity_logs` VALUES("516","14","resume","tv","2","Tiếp tục chiếu TV \'TV Chrysan\'","115.74.225.100",NULL,"2025-12-05 14:27:41");
INSERT INTO `activity_logs` VALUES("517","14","assign","media","35","Gán media \'schaeffler chrysan 8-9:12 8h-17h\' cho TV Chrysan","115.74.225.100",NULL,"2025-12-05 14:27:49");
INSERT INTO `activity_logs` VALUES("518","14","assign","media","36","Gán media \'grimaud lotus 6:12 8h-17h\' cho TV Lotus","115.74.225.100",NULL,"2025-12-05 14:27:54");
INSERT INTO `activity_logs` VALUES("519","14","fullscreen_all","tv","0","Gửi lệnh fullscreen đến tất cả 7 TV","115.74.225.100",NULL,"2025-12-05 14:28:01");
INSERT INTO `activity_logs` VALUES("520","14","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","115.74.225.100",NULL,"2025-12-05 14:28:01");
INSERT INTO `activity_logs` VALUES("521","14","delete","media","34","Đánh dấu xóa media: nestle 3:12:25 chrysan","115.74.225.100",NULL,"2025-12-05 14:29:30");
INSERT INTO `activity_logs` VALUES("522","10","login",NULL,NULL,"User logged in","42.119.231.41",NULL,"2025-12-05 22:59:15");
INSERT INTO `activity_logs` VALUES("523","10","login",NULL,NULL,"User logged in","42.119.231.41",NULL,"2025-12-06 07:37:14");
INSERT INTO `activity_logs` VALUES("524","10","login",NULL,NULL,"User logged in","42.119.231.41",NULL,"2025-12-06 07:38:32");
INSERT INTO `activity_logs` VALUES("525","14","login",NULL,NULL,"User logged in","118.69.64.122",NULL,"2025-12-06 10:27:02");
INSERT INTO `activity_logs` VALUES("526","14","login",NULL,NULL,"User logged in","123.31.116.145",NULL,"2025-12-06 13:39:16");
INSERT INTO `activity_logs` VALUES("527","14","logout",NULL,NULL,"User logged out","123.31.116.145",NULL,"2025-12-06 13:46:28");
INSERT INTO `activity_logs` VALUES("528","10","login",NULL,NULL,"User logged in","118.69.64.122",NULL,"2025-12-07 07:33:53");
INSERT INTO `activity_logs` VALUES("529","10","login",NULL,NULL,"User logged in","115.74.225.100",NULL,"2025-12-07 08:07:42");
INSERT INTO `activity_logs` VALUES("530","10","toggle_test_mode","system","0","Bật chế độ test trên tất cả TV","115.74.225.100",NULL,"2025-12-07 08:08:07");
INSERT INTO `activity_logs` VALUES("531","10","toggle_test_mode","system","0","Tắt chế độ test trên tất cả TV","115.74.225.100",NULL,"2025-12-07 08:09:00");
INSERT INTO `activity_logs` VALUES("532","10","toggle_test_mode","system","0","Bật chế độ test trên tất cả TV","115.74.225.100",NULL,"2025-12-07 08:14:15");
INSERT INTO `activity_logs` VALUES("533","10","toggle_test_mode","system","0","Tắt chế độ test trên tất cả TV","115.74.225.100",NULL,"2025-12-07 08:14:47");
INSERT INTO `activity_logs` VALUES("534","10","toggle_test_mode","system","0","Bật chế độ test trên tất cả TV","115.74.225.100",NULL,"2025-12-07 08:14:50");
INSERT INTO `activity_logs` VALUES("535","10","toggle_status","tv","1","Tắt TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-07 08:17:03");
INSERT INTO `activity_logs` VALUES("536","10","shutdown","system",NULL,"Tắt toàn bộ hệ thống - Offline tất cả TV và gỡ gán toàn bộ WCB","115.74.225.100",NULL,"2025-12-07 08:17:07");
INSERT INTO `activity_logs` VALUES("537","10","toggle_status","tv","1","Bật TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-07 08:17:12");
INSERT INTO `activity_logs` VALUES("538","10","toggle_status","tv","2","Bật TV \'TV Chrysan\'","115.74.225.100",NULL,"2025-12-07 08:17:13");
INSERT INTO `activity_logs` VALUES("539","10","toggle_status","tv","4","Bật TV \'TV Lotus\'","115.74.225.100",NULL,"2025-12-07 08:17:15");
INSERT INTO `activity_logs` VALUES("540","10","assign","media","35","Gán media \'schaeffler chrysan 8-9:12 8h-17h\' cho TV Basement","115.74.225.100",NULL,"2025-12-07 08:17:32");
INSERT INTO `activity_logs` VALUES("541","10","assign","media","37","Gán media \'sở nội vụ 8-9:12 lotus\' cho TV Basement","115.74.225.100",NULL,"2025-12-07 08:17:32");
INSERT INTO `activity_logs` VALUES("542","10","assign","media","35","Gán media \'schaeffler chrysan 8-9:12 8h-17h\' cho TV Chrysan","115.74.225.100",NULL,"2025-12-07 08:17:37");
INSERT INTO `activity_logs` VALUES("543","10","assign","media","37","Gán media \'sở nội vụ 8-9:12 lotus\' cho TV Lotus","115.74.225.100",NULL,"2025-12-07 08:17:42");
INSERT INTO `activity_logs` VALUES("544","10","login",NULL,NULL,"User logged in","113.161.147.9",NULL,"2025-12-07 08:50:06");
INSERT INTO `activity_logs` VALUES("545","10","login",NULL,NULL,"User logged in","115.74.225.100",NULL,"2025-12-07 08:58:16");
INSERT INTO `activity_logs` VALUES("546","10","delete_user","user","13","Xóa người dùng: fo","115.74.225.100",NULL,"2025-12-07 08:58:29");
INSERT INTO `activity_logs` VALUES("547","10","delete_user","user","12","Xóa người dùng: tv","115.74.225.100",NULL,"2025-12-07 08:58:31");
INSERT INTO `activity_logs` VALUES("548","10","delete_user","user","11","Xóa người dùng: salemanager","115.74.225.100",NULL,"2025-12-07 08:58:34");
INSERT INTO `activity_logs` VALUES("549","10","create_user","user","15","Tạo người dùng: anhvang99","115.74.225.100",NULL,"2025-12-07 08:59:19");
INSERT INTO `activity_logs` VALUES("550","10","login",NULL,NULL,"User logged in","118.69.64.122",NULL,"2025-12-07 11:18:02");
INSERT INTO `activity_logs` VALUES("551","10","login",NULL,NULL,"User logged in","115.74.225.100",NULL,"2025-12-07 11:21:23");
INSERT INTO `activity_logs` VALUES("552","10","login",NULL,NULL,"User logged in","125.235.213.142",NULL,"2025-12-08 06:01:46");
INSERT INTO `activity_logs` VALUES("553","10","toggle_test_mode","system","0","Bật chế độ test trên tất cả TV","125.235.213.142",NULL,"2025-12-08 06:01:53");
INSERT INTO `activity_logs` VALUES("554","10","toggle_test_mode","system","0","Tắt chế độ test trên tất cả TV","125.235.213.142",NULL,"2025-12-08 06:01:55");
INSERT INTO `activity_logs` VALUES("555","14","login",NULL,NULL,"User logged in","115.74.225.100",NULL,"2025-12-08 08:38:18");
INSERT INTO `activity_logs` VALUES("556","14","assign","media","35","Gán media \'schaeffler chrysan 8-9:12 8h-17h\' cho TV Jasmine","115.74.225.100",NULL,"2025-12-08 08:38:37");
INSERT INTO `activity_logs` VALUES("557","14","resume","tv","2","Tiếp tục chiếu TV \'TV Chrysan\'","115.74.225.100",NULL,"2025-12-08 08:38:40");
INSERT INTO `activity_logs` VALUES("558","14","resume","tv","3","Tiếp tục chiếu TV \'TV Jasmine\'","115.74.225.100",NULL,"2025-12-08 08:38:46");
INSERT INTO `activity_logs` VALUES("559","10","login",NULL,NULL,"User logged in","115.74.225.100",NULL,"2025-12-08 09:04:26");
INSERT INTO `activity_logs` VALUES("560","14","login",NULL,NULL,"User logged in","115.74.225.100",NULL,"2025-12-08 10:40:28");
INSERT INTO `activity_logs` VALUES("561","14","resume","tv","2","Tiếp tục chiếu TV \'TV Chrysan\'","115.74.225.100",NULL,"2025-12-08 10:40:36");
INSERT INTO `activity_logs` VALUES("562","14","resume","tv","3","Tiếp tục chiếu TV \'TV Jasmine\'","115.74.225.100",NULL,"2025-12-08 10:40:47");
INSERT INTO `activity_logs` VALUES("563","10","login",NULL,NULL,"User logged in","115.74.225.100",NULL,"2025-12-08 10:49:01");
INSERT INTO `activity_logs` VALUES("564","10","upload","media","39","Upload file: schl 8-9.12 chysan","115.74.225.100",NULL,"2025-12-08 10:50:35");
INSERT INTO `activity_logs` VALUES("565","10","unassign","media","35","Hủy gán media \'schaeffler chrysan 8-9:12 8h-17h\' khỏi TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-08 10:50:53");
INSERT INTO `activity_logs` VALUES("566","10","assign","media","39","Gán media \'schl 8-9.12 chysan\' cho TV Basement","115.74.225.100",NULL,"2025-12-08 10:51:02");
INSERT INTO `activity_logs` VALUES("567","10","unassign","media","35","Hủy gán media \'schaeffler chrysan 8-9:12 8h-17h\' khỏi TV \'TV Chrysan\'","115.74.225.100",NULL,"2025-12-08 10:51:11");
INSERT INTO `activity_logs` VALUES("568","10","assign","media","39","Gán media \'schl 8-9.12 chysan\' cho TV Chrysan","115.74.225.100",NULL,"2025-12-08 10:51:15");
INSERT INTO `activity_logs` VALUES("569","10","unassign","media","35","Hủy gán media \'schaeffler chrysan 8-9:12 8h-17h\' khỏi TV \'TV Jasmine\'","115.74.225.100",NULL,"2025-12-08 10:51:22");
INSERT INTO `activity_logs` VALUES("570","10","assign","media","39","Gán media \'schl 8-9.12 chysan\' cho TV Jasmine","115.74.225.100",NULL,"2025-12-08 10:51:35");
INSERT INTO `activity_logs` VALUES("571","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","115.74.225.100",NULL,"2025-12-08 10:51:40");
INSERT INTO `activity_logs` VALUES("572","10","login",NULL,NULL,"User logged in","115.74.225.100",NULL,"2025-12-08 10:54:48");
INSERT INTO `activity_logs` VALUES("573","10","login",NULL,NULL,"User logged in","125.235.213.147",NULL,"2025-12-08 13:44:13");
INSERT INTO `activity_logs` VALUES("574","14","login",NULL,NULL,"User logged in","115.74.225.100",NULL,"2025-12-09 16:17:54");
INSERT INTO `activity_logs` VALUES("575","14","upload","media","40","Upload file: File_WCB_1011","115.74.225.100",NULL,"2025-12-09 16:18:04");
INSERT INTO `activity_logs` VALUES("576","14","delete","media","36","Đánh dấu xóa media: grimaud lotus 6:12 8h-17h","115.74.225.100",NULL,"2025-12-09 16:21:04");
INSERT INTO `activity_logs` VALUES("577","14","delete","media","35","Đánh dấu xóa media: schaeffler chrysan 8-9:12 8h-17h","115.74.225.100",NULL,"2025-12-09 16:21:36");
INSERT INTO `activity_logs` VALUES("578","14","login",NULL,NULL,"User logged in","103.199.33.217",NULL,"2025-12-09 18:14:21");
INSERT INTO `activity_logs` VALUES("579","14","unassign","media","39","Hủy gán media \'schl 8-9.12 chysan\' khỏi TV \'TV Basement\'","103.199.33.217",NULL,"2025-12-09 18:14:39");
INSERT INTO `activity_logs` VALUES("580","14","unassign","media","37","Hủy gán media \'sở nội vụ 8-9:12 lotus\' khỏi TV \'TV Basement\'","103.199.33.217",NULL,"2025-12-09 18:14:43");
INSERT INTO `activity_logs` VALUES("581","14","assign","media","40","Gán media \'File_WCB_1011\' cho TV Basement","103.199.33.217",NULL,"2025-12-09 18:14:53");
INSERT INTO `activity_logs` VALUES("582","14","resume","tv","1","Tiếp tục chiếu TV \'TV Basement\'","103.199.33.217",NULL,"2025-12-09 18:14:59");
INSERT INTO `activity_logs` VALUES("583","14","unassign","media","37","Hủy gán media \'sở nội vụ 8-9:12 lotus\' khỏi TV \'TV Lotus\'","103.199.33.217",NULL,"2025-12-09 18:15:23");
INSERT INTO `activity_logs` VALUES("584","14","assign","media","40","Gán media \'File_WCB_1011\' cho TV Lotus","103.199.33.217",NULL,"2025-12-09 18:15:26");
INSERT INTO `activity_logs` VALUES("585","14","resume","tv","4","Tiếp tục chiếu TV \'TV Lotus\'","103.199.33.217",NULL,"2025-12-09 18:15:32");
INSERT INTO `activity_logs` VALUES("586","14","login",NULL,NULL,"User logged in","103.199.56.2",NULL,"2025-12-10 06:50:41");
INSERT INTO `activity_logs` VALUES("587","14","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","42.115.165.208",NULL,"2025-12-10 06:50:57");
INSERT INTO `activity_logs` VALUES("588","14","fullscreen_all","tv","0","Gửi lệnh fullscreen đến tất cả 7 TV","42.115.165.208",NULL,"2025-12-10 06:50:57");
INSERT INTO `activity_logs` VALUES("589","14","resume","tv","4","Tiếp tục chiếu TV \'TV Lotus\'","42.115.165.208",NULL,"2025-12-10 06:51:18");
INSERT INTO `activity_logs` VALUES("590","14","resume","tv","1","Tiếp tục chiếu TV \'TV Basement\'","42.115.165.208",NULL,"2025-12-10 06:51:36");
INSERT INTO `activity_logs` VALUES("591","10","login",NULL,NULL,"User logged in","1.53.74.173",NULL,"2025-12-10 06:58:22");
INSERT INTO `activity_logs` VALUES("592","10","toggle_status","tv","2","Tắt TV \'TV Chrysan\'","1.53.74.173",NULL,"2025-12-10 06:58:37");
INSERT INTO `activity_logs` VALUES("593","10","toggle_status","tv","3","Tắt TV \'TV Jasmine\'","1.53.74.173",NULL,"2025-12-10 06:58:47");
INSERT INTO `activity_logs` VALUES("594","14","login",NULL,NULL,"User logged in","123.31.116.145",NULL,"2025-12-10 07:55:53");
INSERT INTO `activity_logs` VALUES("595","14","resume","tv","4","Tiếp tục chiếu TV \'TV Lotus\'","123.31.116.145",NULL,"2025-12-10 07:56:08");
INSERT INTO `activity_logs` VALUES("596","14","fullscreen_all","tv","0","Gửi lệnh fullscreen đến tất cả 7 TV","123.31.116.145",NULL,"2025-12-10 07:56:13");
INSERT INTO `activity_logs` VALUES("597","14","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","123.31.116.145",NULL,"2025-12-10 07:56:13");
INSERT INTO `activity_logs` VALUES("598","14","login",NULL,NULL,"User logged in","115.74.225.100",NULL,"2025-12-10 08:13:26");
INSERT INTO `activity_logs` VALUES("599","14","login",NULL,NULL,"User logged in","123.31.116.145",NULL,"2025-12-10 09:34:58");
INSERT INTO `activity_logs` VALUES("600","14","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","123.31.116.145",NULL,"2025-12-10 09:35:40");
INSERT INTO `activity_logs` VALUES("601","14","fullscreen_all","tv","0","Gửi lệnh fullscreen đến tất cả 7 TV","123.31.116.145",NULL,"2025-12-10 09:35:40");
INSERT INTO `activity_logs` VALUES("602","14","login",NULL,NULL,"User logged in","115.74.225.100",NULL,"2025-12-10 15:31:59");
INSERT INTO `activity_logs` VALUES("603","14","login",NULL,NULL,"User logged in","115.74.225.100",NULL,"2025-12-11 08:01:41");
INSERT INTO `activity_logs` VALUES("604","10","login",NULL,NULL,"User logged in","103.199.33.168",NULL,"2025-12-12 17:37:36");
INSERT INTO `activity_logs` VALUES("605","14","login",NULL,NULL,"User logged in","115.74.225.100",NULL,"2025-12-19 06:06:38");
INSERT INTO `activity_logs` VALUES("606","14","upload","media","41","Upload file: nestle","115.74.225.100",NULL,"2025-12-19 06:08:48");
INSERT INTO `activity_logs` VALUES("607","14","upload","media","42","Upload file: japfa","115.74.225.100",NULL,"2025-12-19 06:08:57");
INSERT INTO `activity_logs` VALUES("608","14","assign","media","41","Gán media \'nestle\' cho TV Basement","115.74.225.100",NULL,"2025-12-19 06:09:09");
INSERT INTO `activity_logs` VALUES("609","14","unassign","media","40","Hủy gán media \'File_WCB_1011\' khỏi TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-19 06:09:29");
INSERT INTO `activity_logs` VALUES("610","14","toggle_status","tv","2","Bật TV \'TV Chrysan\'","115.74.225.100",NULL,"2025-12-19 06:09:45");
INSERT INTO `activity_logs` VALUES("611","14","assign","media","41","Gán media \'nestle\' cho TV Chrysan","115.74.225.100",NULL,"2025-12-19 06:09:52");
INSERT INTO `activity_logs` VALUES("612","14","unassign","media","39","Hủy gán media \'schl 8-9.12 chysan\' khỏi TV \'TV Chrysan\'","115.74.225.100",NULL,"2025-12-19 06:10:01");
INSERT INTO `activity_logs` VALUES("613","14","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","115.74.225.100",NULL,"2025-12-19 06:10:26");
INSERT INTO `activity_logs` VALUES("614","14","resume","tv","2","Tiếp tục chiếu TV \'TV Chrysan\'","115.74.225.100",NULL,"2025-12-19 06:10:48");
INSERT INTO `activity_logs` VALUES("615","14","assign","media","42","Gán media \'japfa\' cho TV Restaurant","115.74.225.100",NULL,"2025-12-19 06:11:05");
INSERT INTO `activity_logs` VALUES("616","14","toggle_status","tv","4","Tắt TV \'TV Lotus\'","115.74.225.100",NULL,"2025-12-19 06:12:07");
INSERT INTO `activity_logs` VALUES("617","14","delete_user","user","15","Xóa người dùng: anhvang99","115.74.225.100",NULL,"2025-12-19 06:15:28");
INSERT INTO `activity_logs` VALUES("618","14","login",NULL,NULL,"User logged in","42.112.78.128",NULL,"2025-12-19 07:24:11");
INSERT INTO `activity_logs` VALUES("619","14","reset_password","user","10","Đặt lại mật khẩu cho: quanglong","42.112.78.128",NULL,"2025-12-19 07:24:57");
INSERT INTO `activity_logs` VALUES("620","14","logout",NULL,NULL,"User logged out","42.112.78.128",NULL,"2025-12-19 07:25:01");
INSERT INTO `activity_logs` VALUES("621","10","login",NULL,NULL,"User logged in","42.112.78.128",NULL,"2025-12-19 07:25:08");
INSERT INTO `activity_logs` VALUES("622","10","change_password",NULL,NULL,"Changed password","42.112.78.128",NULL,"2025-12-19 07:25:30");
INSERT INTO `activity_logs` VALUES("623","14","login",NULL,NULL,"User logged in","115.74.225.100",NULL,"2025-12-19 12:06:41");
INSERT INTO `activity_logs` VALUES("624","14","toggle_status","tv","5","Tắt TV \'TV Restaurant\'","115.74.225.100",NULL,"2025-12-19 12:06:49");
INSERT INTO `activity_logs` VALUES("625","14","toggle_status","tv","4","Bật TV \'TV Lotus\'","115.74.225.100",NULL,"2025-12-19 12:06:52");
INSERT INTO `activity_logs` VALUES("626","14","assign","media","42","Gán media \'japfa\' cho TV Lotus","115.74.225.100",NULL,"2025-12-19 12:07:04");
INSERT INTO `activity_logs` VALUES("627","14","unassign","media","40","Hủy gán media \'File_WCB_1011\' khỏi TV \'TV Lotus\'","115.74.225.100",NULL,"2025-12-19 12:07:10");
INSERT INTO `activity_logs` VALUES("628","10","login",NULL,NULL,"User logged in","115.74.225.100",NULL,"2025-12-19 13:37:37");
INSERT INTO `activity_logs` VALUES("629","10","toggle_status","tv","1","Tắt TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-19 13:39:22");
INSERT INTO `activity_logs` VALUES("630","10","toggle_status","tv","2","Tắt TV \'TV Chrysan\'","115.74.225.100",NULL,"2025-12-19 13:39:55");
INSERT INTO `activity_logs` VALUES("631","10","unassign","media","41","Hủy gán media \'nestle\' khỏi TV \'TV Chrysan\'","115.74.225.100",NULL,"2025-12-19 13:40:06");
INSERT INTO `activity_logs` VALUES("632","10","assign","media","41","Gán media \'nestle\' cho TV Chrysan","115.74.225.100",NULL,"2025-12-19 13:40:30");
INSERT INTO `activity_logs` VALUES("633","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","115.74.225.100",NULL,"2025-12-19 13:41:14");
INSERT INTO `activity_logs` VALUES("634","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","115.74.225.100",NULL,"2025-12-19 13:41:57");
INSERT INTO `activity_logs` VALUES("635","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","115.74.225.100",NULL,"2025-12-19 13:43:07");
INSERT INTO `activity_logs` VALUES("636","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","115.74.225.100",NULL,"2025-12-19 13:43:23");
INSERT INTO `activity_logs` VALUES("637","10","login",NULL,NULL,"User logged in","222.253.191.65",NULL,"2025-12-19 13:54:24");
INSERT INTO `activity_logs` VALUES("638","10","resume","tv","4","Tiếp tục chiếu TV \'TV Lotus\'","222.253.191.65",NULL,"2025-12-19 13:54:38");
INSERT INTO `activity_logs` VALUES("639","10","upload","media","43","Upload file: Giới thiệu CoinVlog (Video 5s)","222.253.191.65",NULL,"2025-12-19 13:57:02");
INSERT INTO `activity_logs` VALUES("640","10","login",NULL,NULL,"User logged in","103.199.57.91",NULL,"2025-12-19 15:15:34");
INSERT INTO `activity_logs` VALUES("641","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","103.199.57.91",NULL,"2025-12-19 15:15:41");
INSERT INTO `activity_logs` VALUES("642","10","login",NULL,NULL,"User logged in","222.253.191.65",NULL,"2025-12-19 17:07:28");
INSERT INTO `activity_logs` VALUES("643","10","assign","media","42","Gán media \'japfa\' cho TV Basement","222.253.191.65",NULL,"2025-12-19 17:07:37");
INSERT INTO `activity_logs` VALUES("644","10","unassign","media","41","Hủy gán media \'nestle\' khỏi TV \'TV Basement\'","222.253.191.65",NULL,"2025-12-19 17:07:41");
INSERT INTO `activity_logs` VALUES("645","10","fullscreen_all","tv","0","Gửi lệnh fullscreen đến tất cả 7 TV","222.253.191.65",NULL,"2025-12-19 17:07:49");
INSERT INTO `activity_logs` VALUES("646","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","222.253.191.65",NULL,"2025-12-19 17:07:49");
INSERT INTO `activity_logs` VALUES("647","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","222.253.191.65",NULL,"2025-12-19 17:07:55");
INSERT INTO `activity_logs` VALUES("648","10","login",NULL,NULL,"User logged in","222.253.191.65",NULL,"2025-12-19 19:15:27");
INSERT INTO `activity_logs` VALUES("649","10","login",NULL,NULL,"User logged in","118.69.64.122",NULL,"2025-12-21 20:43:11");
INSERT INTO `activity_logs` VALUES("650","10","toggle_test_mode","system","0","Bật chế độ test trên tất cả TV","118.69.64.122",NULL,"2025-12-21 20:43:16");
INSERT INTO `activity_logs` VALUES("651","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","118.69.64.122",NULL,"2025-12-21 20:43:19");
INSERT INTO `activity_logs` VALUES("652","10","login",NULL,NULL,"User logged in","1.52.39.161",NULL,"2025-12-22 08:07:43");
INSERT INTO `activity_logs` VALUES("653","10","toggle_test_mode","system","0","Bật chế độ test trên tất cả TV","1.52.39.161",NULL,"2025-12-22 08:07:52");
INSERT INTO `activity_logs` VALUES("654","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","1.52.39.161",NULL,"2025-12-22 08:07:56");
INSERT INTO `activity_logs` VALUES("655","14","login",NULL,NULL,"User logged in","115.74.225.100",NULL,"2025-12-22 08:44:43");
INSERT INTO `activity_logs` VALUES("656","14","upload","media","44","Upload file: AURORA HOTEL PLAZA","115.74.225.100",NULL,"2025-12-22 08:45:09");
INSERT INTO `activity_logs` VALUES("657","14","unassign","media","42","Hủy gán media \'japfa\' khỏi TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-22 08:45:22");
INSERT INTO `activity_logs` VALUES("658","14","assign","media","44","Gán media \'AURORA HOTEL PLAZA\' cho TV Basement","115.74.225.100",NULL,"2025-12-22 08:45:31");
INSERT INTO `activity_logs` VALUES("659","14","resume","tv","1","Tiếp tục chiếu TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-22 08:45:40");
INSERT INTO `activity_logs` VALUES("660","14","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","115.74.225.100",NULL,"2025-12-22 08:45:53");
INSERT INTO `activity_logs` VALUES("661","14","login",NULL,NULL,"User logged in","115.74.225.100",NULL,"2025-12-22 08:49:19");
INSERT INTO `activity_logs` VALUES("662","14","toggle_status","tv","6","Bật TV \'TV FO 1\'","115.74.225.100",NULL,"2025-12-22 08:49:31");
INSERT INTO `activity_logs` VALUES("663","14","assign","media","44","Gán media \'AURORA HOTEL PLAZA\' cho TV FO 1","115.74.225.100",NULL,"2025-12-22 08:49:36");
INSERT INTO `activity_logs` VALUES("664","14","resume","tv","1","Tiếp tục chiếu TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-22 08:56:15");
INSERT INTO `activity_logs` VALUES("665","14","resume","tv","6","Tiếp tục chiếu TV \'TV FO 1\'","115.74.225.100",NULL,"2025-12-22 08:56:20");
INSERT INTO `activity_logs` VALUES("666","14","fullscreen_all","tv","0","Gửi lệnh fullscreen đến tất cả 7 TV","115.74.225.100",NULL,"2025-12-22 08:56:49");
INSERT INTO `activity_logs` VALUES("667","14","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","115.74.225.100",NULL,"2025-12-22 08:56:49");
INSERT INTO `activity_logs` VALUES("668","14","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","115.74.225.100",NULL,"2025-12-22 08:57:02");
INSERT INTO `activity_logs` VALUES("669","14","toggle_status","tv","1","Tắt TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-22 08:58:32");
INSERT INTO `activity_logs` VALUES("670","14","toggle_status","tv","1","Bật TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-22 08:58:34");
INSERT INTO `activity_logs` VALUES("671","14","assign","media","43","Gán media \'Giới thiệu CoinVlog (Video 5s)\' cho TV Basement","115.74.225.100",NULL,"2025-12-22 08:59:00");
INSERT INTO `activity_logs` VALUES("672","14","resume","tv","1","Tiếp tục chiếu TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-22 08:59:04");
INSERT INTO `activity_logs` VALUES("673","14","assign","media","42","Gán media \'japfa\' cho TV Basement","115.74.225.100",NULL,"2025-12-22 08:59:14");
INSERT INTO `activity_logs` VALUES("674","14","resume","tv","1","Tiếp tục chiếu TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-22 08:59:18");
INSERT INTO `activity_logs` VALUES("675","14","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","115.74.225.100",NULL,"2025-12-22 09:00:28");
INSERT INTO `activity_logs` VALUES("676","14","unassign","media","43","Hủy gán media \'Giới thiệu CoinVlog (Video 5s)\' khỏi TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-22 09:00:56");
INSERT INTO `activity_logs` VALUES("677","14","unassign","media","44","Hủy gán media \'AURORA HOTEL PLAZA\' khỏi TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-22 09:01:59");
INSERT INTO `activity_logs` VALUES("678","14","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","115.74.225.100",NULL,"2025-12-22 09:02:15");
INSERT INTO `activity_logs` VALUES("679","14","login",NULL,NULL,"User logged in","115.74.225.100",NULL,"2025-12-22 10:55:22");
INSERT INTO `activity_logs` VALUES("680","14","fullscreen_all","tv","0","Gửi lệnh fullscreen đến tất cả 7 TV","115.74.225.100",NULL,"2025-12-22 10:55:31");
INSERT INTO `activity_logs` VALUES("681","14","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","115.74.225.100",NULL,"2025-12-22 10:55:31");
INSERT INTO `activity_logs` VALUES("682","14","login",NULL,NULL,"User logged in","115.74.225.100",NULL,"2025-12-22 11:26:15");
INSERT INTO `activity_logs` VALUES("683","14","upload","media","45","Upload file: BUFFET NOEL","115.74.225.100",NULL,"2025-12-22 11:29:07");
INSERT INTO `activity_logs` VALUES("684","14","assign","media","45","Gán media \'BUFFET NOEL\' cho TV Basement","115.74.225.100",NULL,"2025-12-22 11:29:15");
INSERT INTO `activity_logs` VALUES("685","14","unassign","media","42","Hủy gán media \'japfa\' khỏi TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-22 11:29:20");
INSERT INTO `activity_logs` VALUES("686","14","assign","media","45","Gán media \'BUFFET NOEL\' cho TV Restaurant","115.74.225.100",NULL,"2025-12-22 11:29:40");
INSERT INTO `activity_logs` VALUES("687","14","toggle_status","tv","5","Bật TV \'TV Restaurant\'","115.74.225.100",NULL,"2025-12-22 11:29:43");
INSERT INTO `activity_logs` VALUES("688","14","unassign","media","42","Hủy gán media \'japfa\' khỏi TV \'TV Restaurant\'","115.74.225.100",NULL,"2025-12-22 11:29:48");
INSERT INTO `activity_logs` VALUES("689","14","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","115.74.225.100",NULL,"2025-12-22 11:29:54");
INSERT INTO `activity_logs` VALUES("690","14","delete","media","43","Đánh dấu xóa media: Giới thiệu CoinVlog (Video 5s)","115.74.225.100",NULL,"2025-12-22 11:49:04");
INSERT INTO `activity_logs` VALUES("691","10","login",NULL,NULL,"User logged in","222.253.191.65",NULL,"2025-12-22 14:01:22");
INSERT INTO `activity_logs` VALUES("692","10","orchid_mode","media","45","Áp dụng chế độ Orchid - Gán \'BUFFET NOEL\' cho TV Basement, TV Chrysan, TV Jasmine, TV Lotus, TV FO 1, TV FO 2 và bật tất cả TV","222.253.191.65",NULL,"2025-12-22 14:01:38");
INSERT INTO `activity_logs` VALUES("693","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","222.253.191.65",NULL,"2025-12-22 14:01:43");
INSERT INTO `activity_logs` VALUES("694","10","orchid_mode","media","44","Áp dụng chế độ Orchid - Gán \'AURORA HOTEL PLAZA\' cho TV Basement, TV Chrysan, TV Jasmine, TV Lotus, TV FO 1, TV FO 2 và bật tất cả TV","222.253.191.65",NULL,"2025-12-22 14:28:15");
INSERT INTO `activity_logs` VALUES("695","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","222.253.191.65",NULL,"2025-12-22 14:30:03");
INSERT INTO `activity_logs` VALUES("696","10","login",NULL,NULL,"User logged in","115.74.225.100",NULL,"2025-12-23 15:39:41");
INSERT INTO `activity_logs` VALUES("697","10","login",NULL,NULL,"User logged in","118.69.64.122",NULL,"2025-12-24 14:17:24");
INSERT INTO `activity_logs` VALUES("698","10","shutdown","system",NULL,"Tắt toàn bộ hệ thống - Offline tất cả TV và gỡ gán toàn bộ WCB","118.69.64.122",NULL,"2025-12-24 14:17:32");
INSERT INTO `activity_logs` VALUES("699","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","118.69.64.122",NULL,"2025-12-24 14:17:36");
INSERT INTO `activity_logs` VALUES("700","10","toggle_test_mode","system","0","Bật chế độ test trên tất cả TV","118.69.64.122",NULL,"2025-12-24 14:17:40");
INSERT INTO `activity_logs` VALUES("701","10","toggle_test_mode","system","0","Tắt chế độ test trên tất cả TV","118.69.64.122",NULL,"2025-12-24 14:17:44");
INSERT INTO `activity_logs` VALUES("702","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","118.69.64.122",NULL,"2025-12-24 14:17:46");
INSERT INTO `activity_logs` VALUES("703","10","assign","media","45","Gán media \'BUFFET NOEL\' cho TV Basement","118.69.64.122",NULL,"2025-12-24 14:17:55");
INSERT INTO `activity_logs` VALUES("704","10","assign","media","45","Gán media \'BUFFET NOEL\' cho TV Restaurant","118.69.64.122",NULL,"2025-12-24 14:18:06");
INSERT INTO `activity_logs` VALUES("705","10","toggle_status","tv","5","Bật TV \'TV Restaurant\'","118.69.64.122",NULL,"2025-12-24 14:18:08");
INSERT INTO `activity_logs` VALUES("706","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","118.69.64.122",NULL,"2025-12-24 14:18:17");
INSERT INTO `activity_logs` VALUES("707","10","toggle_status","tv","5","Tắt TV \'TV Restaurant\'","222.253.191.65",NULL,"2025-12-24 14:28:40");
INSERT INTO `activity_logs` VALUES("708","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","222.253.191.65",NULL,"2025-12-24 14:28:45");
INSERT INTO `activity_logs` VALUES("709","14","login",NULL,NULL,"User logged in","115.74.225.100",NULL,"2025-12-27 14:29:54");
INSERT INTO `activity_logs` VALUES("710","14","delete","media","42","Đánh dấu xóa media: japfa","115.74.225.100",NULL,"2025-12-27 14:30:16");
INSERT INTO `activity_logs` VALUES("711","14","delete","media","37","Đánh dấu xóa media: sở nội vụ 8-9:12 lotus","115.74.225.100",NULL,"2025-12-27 14:30:23");
INSERT INTO `activity_logs` VALUES("712","14","upload","media","46","Upload file: 28-12 BẾN THÀNH TOURIST","115.74.225.100",NULL,"2025-12-27 14:33:23");
INSERT INTO `activity_logs` VALUES("713","14","unassign","media","45","Hủy gán media \'BUFFET NOEL\' khỏi TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-27 14:33:40");
INSERT INTO `activity_logs` VALUES("714","14","assign","media","46","Gán media \'28-12 BẾN THÀNH TOURIST\' cho TV Basement","115.74.225.100",NULL,"2025-12-27 14:33:44");
INSERT INTO `activity_logs` VALUES("715","14","unassign","media","45","Hủy gán media \'BUFFET NOEL\' khỏi TV \'TV Restaurant\'","115.74.225.100",NULL,"2025-12-27 14:33:53");
INSERT INTO `activity_logs` VALUES("716","14","toggle_status","tv","4","Bật TV \'TV Lotus\'","115.74.225.100",NULL,"2025-12-27 14:34:00");
INSERT INTO `activity_logs` VALUES("717","14","assign","media","46","Gán media \'28-12 BẾN THÀNH TOURIST\' cho TV Lotus","115.74.225.100",NULL,"2025-12-27 14:34:09");
INSERT INTO `activity_logs` VALUES("718","14","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","115.74.225.100",NULL,"2025-12-27 14:34:16");
INSERT INTO `activity_logs` VALUES("719","10","login",NULL,NULL,"User logged in","222.253.191.65",NULL,"2025-12-28 08:09:34");
INSERT INTO `activity_logs` VALUES("720","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","222.253.191.65",NULL,"2025-12-28 08:09:49");
INSERT INTO `activity_logs` VALUES("721","10","login",NULL,NULL,"User logged in","222.253.191.65",NULL,"2025-12-29 10:56:24");
INSERT INTO `activity_logs` VALUES("722","10","unassign","media","46","Hủy gán media \'28-12 BẾN THÀNH TOURIST\' khỏi TV \'TV Basement\'","222.253.191.65",NULL,"2025-12-29 10:57:00");
INSERT INTO `activity_logs` VALUES("723","10","assign","media","44","Gán media \'AURORA HOTEL PLAZA\' cho TV Basement","222.253.191.65",NULL,"2025-12-29 10:57:02");
INSERT INTO `activity_logs` VALUES("724","10","login",NULL,NULL,"User logged in","118.69.64.122",NULL,"2025-12-29 10:58:25");
INSERT INTO `activity_logs` VALUES("725","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","118.69.64.122",NULL,"2025-12-29 10:58:30");
INSERT INTO `activity_logs` VALUES("726","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","118.69.64.122",NULL,"2025-12-29 11:06:36");
INSERT INTO `activity_logs` VALUES("727","10","assign","media","45","Gán media \'BUFFET NOEL\' (image) cho TV Basement","118.69.64.122",NULL,"2025-12-29 11:06:54");
INSERT INTO `activity_logs` VALUES("728","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","118.69.64.122",NULL,"2025-12-29 11:06:58");
INSERT INTO `activity_logs` VALUES("729","10","unassign","media","44","Hủy gán media \'AURORA HOTEL PLAZA\' khỏi TV \'TV Basement\'","118.69.64.122",NULL,"2025-12-29 11:07:10");
INSERT INTO `activity_logs` VALUES("730","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","118.69.64.122",NULL,"2025-12-29 11:07:18");
INSERT INTO `activity_logs` VALUES("731","10","unassign","media","45","Hủy gán media \'BUFFET NOEL\' khỏi TV \'TV Basement\'","118.69.64.122",NULL,"2025-12-29 11:07:27");
INSERT INTO `activity_logs` VALUES("732","10","assign","media","44","Gán media \'AURORA HOTEL PLAZA\' (video) cho TV Basement","118.69.64.122",NULL,"2025-12-29 11:07:31");
INSERT INTO `activity_logs` VALUES("733","10","unassign","media","44","Hủy gán media \'AURORA HOTEL PLAZA\' khỏi TV \'TV Basement\'","118.69.64.122",NULL,"2025-12-29 11:13:31");
INSERT INTO `activity_logs` VALUES("734","10","shutdown","system",NULL,"Tắt toàn bộ hệ thống - Offline tất cả TV và gỡ gán toàn bộ WCB","118.69.64.122",NULL,"2025-12-29 11:13:36");
INSERT INTO `activity_logs` VALUES("735","10","assign","media","44","Gán media \'AURORA HOTEL PLAZA\' (video) cho TV Basement | Tự động resume: TV Basement","118.69.64.122",NULL,"2025-12-29 11:13:53");
INSERT INTO `activity_logs` VALUES("736","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","118.69.64.122",NULL,"2025-12-29 11:14:04");
INSERT INTO `activity_logs` VALUES("737","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","118.69.64.122",NULL,"2025-12-29 11:16:46");
INSERT INTO `activity_logs` VALUES("738","10","toggle_test_mode","system","0","Bật chế độ test trên tất cả TV","118.69.64.122",NULL,"2025-12-29 11:16:53");
INSERT INTO `activity_logs` VALUES("739","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","118.69.64.122",NULL,"2025-12-29 11:16:58");
INSERT INTO `activity_logs` VALUES("740","10","create_backup","backup","0","Created database backup: backup_db_2025-12-29_111824.sql.gz","118.69.64.122",NULL,"2025-12-29 11:18:24");
INSERT INTO `activity_logs` VALUES("741","10","login",NULL,NULL,"User logged in","222.253.191.65",NULL,"2025-12-29 13:26:11");
INSERT INTO `activity_logs` VALUES("742","10","shutdown","system",NULL,"Tắt toàn bộ hệ thống - Offline tất cả TV và gỡ gán toàn bộ WCB","222.253.191.65",NULL,"2025-12-29 13:26:17");
INSERT INTO `activity_logs` VALUES("743","10","assign","media","44","Gán media \'AURORA HOTEL PLAZA\' (video) cho TV Basement | Tự động resume: TV Basement","222.253.191.65",NULL,"2025-12-29 13:26:24");
INSERT INTO `activity_logs` VALUES("744","10","login",NULL,NULL,"User logged in","118.69.64.122",NULL,"2025-12-29 13:27:58");
INSERT INTO `activity_logs` VALUES("745","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","118.69.64.122",NULL,"2025-12-29 13:28:03");
INSERT INTO `activity_logs` VALUES("746","10","unassign","media","44","Hủy gán media \'AURORA HOTEL PLAZA\' khỏi TV \'TV Basement\'","118.69.64.122",NULL,"2025-12-29 13:28:49");
INSERT INTO `activity_logs` VALUES("747","10","assign","media","46","Gán media \'28-12 BẾN THÀNH TOURIST\' (image) cho TV Basement","118.69.64.122",NULL,"2025-12-29 13:28:51");
INSERT INTO `activity_logs` VALUES("748","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","118.69.64.122",NULL,"2025-12-29 13:28:53");
INSERT INTO `activity_logs` VALUES("749","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","118.69.64.122",NULL,"2025-12-29 13:29:01");
INSERT INTO `activity_logs` VALUES("750","10","toggle_test_mode","system","0","Bật chế độ test trên tất cả TV","118.69.64.122",NULL,"2025-12-29 13:30:30");
INSERT INTO `activity_logs` VALUES("751","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","118.69.64.122",NULL,"2025-12-29 13:30:32");
INSERT INTO `activity_logs` VALUES("752","10","unassign","media","46","Hủy gán media \'28-12 BẾN THÀNH TOURIST\' khỏi TV \'TV Basement\'","222.253.191.65",NULL,"2025-12-29 13:37:55");
INSERT INTO `activity_logs` VALUES("753","10","assign","media","45","Gán media \'BUFFET NOEL\' (image) cho TV Basement","222.253.191.65",NULL,"2025-12-29 13:37:59");
INSERT INTO `activity_logs` VALUES("754","10","toggle_test_mode","system","0","Tắt chế độ test trên tất cả TV","222.253.191.65",NULL,"2025-12-29 13:38:10");
INSERT INTO `activity_logs` VALUES("755","14","login",NULL,NULL,"User logged in","115.74.225.100",NULL,"2025-12-29 16:24:38");
INSERT INTO `activity_logs` VALUES("756","14","upload","media","47","Upload file: 30-12","115.74.225.100",NULL,"2025-12-29 16:27:12");
INSERT INTO `activity_logs` VALUES("757","14","unassign","media","45","Hủy gán media \'BUFFET NOEL\' khỏi TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-29 16:27:44");
INSERT INTO `activity_logs` VALUES("758","14","assign","media","47","Gán media \'30-12\' (image) cho TV Basement","115.74.225.100",NULL,"2025-12-29 16:38:24");
INSERT INTO `activity_logs` VALUES("759","14","toggle_status","tv","5","Bật TV \'TV Restaurant\'","115.74.225.100",NULL,"2025-12-29 16:38:37");
INSERT INTO `activity_logs` VALUES("760","14","assign","media","47","Gán media \'30-12\' (image) cho TV Restaurant","115.74.225.100",NULL,"2025-12-29 16:39:00");
INSERT INTO `activity_logs` VALUES("761","14","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","115.74.225.100",NULL,"2025-12-29 16:39:09");
INSERT INTO `activity_logs` VALUES("762","14","login",NULL,NULL,"User logged in","115.74.225.100",NULL,"2025-12-29 16:40:24");
INSERT INTO `activity_logs` VALUES("763","10","login",NULL,NULL,"User logged in","1.53.196.173",NULL,"2025-12-30 06:20:21");
INSERT INTO `activity_logs` VALUES("764","10","login",NULL,NULL,"User logged in","222.253.191.65",NULL,"2025-12-30 11:17:18");
INSERT INTO `activity_logs` VALUES("765","10","login",NULL,NULL,"User logged in","222.253.191.65",NULL,"2025-12-30 13:22:34");
INSERT INTO `activity_logs` VALUES("766","10","login",NULL,NULL,"User logged in","222.253.191.65",NULL,"2025-12-30 15:20:40");
INSERT INTO `activity_logs` VALUES("767","10","upload","media","48","Upload file: seAH","222.253.191.65",NULL,"2025-12-30 15:20:50");
INSERT INTO `activity_logs` VALUES("768","10","shutdown","system",NULL,"Tắt toàn bộ hệ thống - Offline tất cả TV và gỡ gán toàn bộ WCB","222.253.191.65",NULL,"2025-12-30 15:21:00");
INSERT INTO `activity_logs` VALUES("769","10","toggle_status","tv","1","Bật TV \'TV Basement\'","222.253.191.65",NULL,"2025-12-30 15:21:17");
INSERT INTO `activity_logs` VALUES("770","10","assign","media","48","Gán media \'seAH\' cho TV Basement","222.253.191.65",NULL,"2025-12-30 15:21:21");
INSERT INTO `activity_logs` VALUES("771","10","toggle_status","tv","5","Bật TV \'TV Restaurant\'","222.253.191.65",NULL,"2025-12-30 15:21:26");
INSERT INTO `activity_logs` VALUES("772","10","assign","media","48","Gán media \'seAH\' cho TV Restaurant","222.253.191.65",NULL,"2025-12-30 15:21:29");
INSERT INTO `activity_logs` VALUES("773","10","login",NULL,NULL,"User logged in","115.74.225.100",NULL,"2025-12-30 15:54:17");
INSERT INTO `activity_logs` VALUES("774","10","video_broadcast","media","44","Áp dụng chế độ Video Broadcast - Gán \'AURORA HOTEL PLAZA\' (Video) cho TV Basement, TV Chrysan, TV FO 1, TV FO 2, TV Jasmine, TV Lotus","115.74.225.100",NULL,"2025-12-30 15:54:35");
INSERT INTO `activity_logs` VALUES("775","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","115.74.225.100",NULL,"2025-12-30 15:54:41");
INSERT INTO `activity_logs` VALUES("776","10","toggle_status","tv","1","Tắt TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-30 15:55:41");
INSERT INTO `activity_logs` VALUES("777","10","toggle_status","tv","1","Bật TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-30 15:55:42");
INSERT INTO `activity_logs` VALUES("778","10","unassign","media","44","Hủy gán media \'AURORA HOTEL PLAZA\' khỏi TV \'TV Basement\'","115.74.225.100",NULL,"2025-12-30 15:55:56");
INSERT INTO `activity_logs` VALUES("779","10","assign","media","48","Gán media \'seAH\' cho TV Basement","115.74.225.100",NULL,"2025-12-30 15:55:59");
INSERT INTO `activity_logs` VALUES("780","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","115.74.225.100",NULL,"2025-12-30 16:00:29");
INSERT INTO `activity_logs` VALUES("781","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","115.74.225.100",NULL,"2025-12-30 16:00:52");
INSERT INTO `activity_logs` VALUES("782","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","115.74.225.100",NULL,"2025-12-30 16:05:56");
INSERT INTO `activity_logs` VALUES("783","10","login",NULL,NULL,"User logged in","222.253.191.65",NULL,"2026-01-01 14:17:17");
INSERT INTO `activity_logs` VALUES("784","10","shutdown","system",NULL,"Tắt toàn bộ hệ thống - Offline tất cả TV và gỡ gán toàn bộ WCB","222.253.191.65",NULL,"2026-01-01 14:17:24");
INSERT INTO `activity_logs` VALUES("785","10","delete","media","44","Đánh dấu xóa media: AURORA HOTEL PLAZA","222.253.191.65",NULL,"2026-01-01 14:17:31");
INSERT INTO `activity_logs` VALUES("786","10","delete","media","48","Đánh dấu xóa media: seAH","222.253.191.65",NULL,"2026-01-01 14:17:38");
INSERT INTO `activity_logs` VALUES("787","10","delete","media","48","Đánh dấu xóa media: seAH","222.253.191.65",NULL,"2026-01-01 14:17:40");
INSERT INTO `activity_logs` VALUES("788","10","delete","media","47","Đánh dấu xóa media: 30-12","222.253.191.65",NULL,"2026-01-01 14:17:41");
INSERT INTO `activity_logs` VALUES("789","10","logout",NULL,NULL,"User logged out","222.253.191.65",NULL,"2026-01-01 14:17:49");
INSERT INTO `activity_logs` VALUES("790","10","login",NULL,NULL,"User logged in","222.253.191.65",NULL,"2026-01-01 14:17:54");
INSERT INTO `activity_logs` VALUES("791","10","delete","media","46","Đánh dấu xóa media: 28-12 BẾN THÀNH TOURIST","222.253.191.65",NULL,"2026-01-01 14:18:06");
INSERT INTO `activity_logs` VALUES("792","10","delete","media","45","Đánh dấu xóa media: BUFFET NOEL","222.253.191.65",NULL,"2026-01-01 14:18:09");
INSERT INTO `activity_logs` VALUES("793","10","delete","media","45","Đánh dấu xóa media: BUFFET NOEL","222.253.191.65",NULL,"2026-01-01 14:18:10");
INSERT INTO `activity_logs` VALUES("794","10","delete","media","41","Đánh dấu xóa media: nestle","222.253.191.65",NULL,"2026-01-01 14:18:13");
INSERT INTO `activity_logs` VALUES("795","10","delete","media","40","Đánh dấu xóa media: File_WCB_1011","222.253.191.65",NULL,"2026-01-01 14:18:15");
INSERT INTO `activity_logs` VALUES("796","10","delete","media","39","Đánh dấu xóa media: schl 8-9.12 chysan","222.253.191.65",NULL,"2026-01-01 14:18:18");
INSERT INTO `activity_logs` VALUES("797","10","delete","media","38","Đánh dấu xóa media: sở nội vụ 10-11:12 lotus","222.253.191.65",NULL,"2026-01-01 14:18:20");
INSERT INTO `activity_logs` VALUES("798","10","toggle_status","tv","1","Bật TV \'TV Basement\'","222.253.191.65",NULL,"2026-01-01 14:19:32");
INSERT INTO `activity_logs` VALUES("799","10","assign","media","49","Gán media \'AURORA HOTEL PLAZA - Nguyen Minh Hieu (1080p, h264)\' cho TV Basement","222.253.191.65",NULL,"2026-01-01 14:19:36");
INSERT INTO `activity_logs` VALUES("800","10","video_broadcast","media","49","Video Broadcast - Gán \'AURORA HOTEL PLAZA - Nguyen Minh Hieu (1080p, h264)\' cho TV Basement, TV Chrysan, TV Jasmine, TV Lotus, TV Restaurant, TV FO 1, TV FO 2","222.253.191.65",NULL,"2026-01-01 14:25:00");
INSERT INTO `activity_logs` VALUES("801","10","reload_all","tv","0","Ép tải lại TẤT CẢ 7 TV","222.253.191.65",NULL,"2026-01-01 14:25:00");
INSERT INTO `activity_logs` VALUES("802","10","reload_all","tv","0","Ép tải lại TẤT CẢ 8 TV","222.253.191.65",NULL,"2026-01-01 14:33:23");
INSERT INTO `activity_logs` VALUES("803","10","reload_all","tv","0","Ép tải lại TẤT CẢ 8 TV","222.253.191.65",NULL,"2026-01-01 14:36:15");
INSERT INTO `activity_logs` VALUES("804","10","login",NULL,NULL,"User logged in","118.69.64.122",NULL,"2026-01-01 14:38:01");
INSERT INTO `activity_logs` VALUES("805","10","reload_all","tv","0","Ép tải lại TẤT CẢ 8 TV","118.69.64.122",NULL,"2026-01-01 14:38:06");
INSERT INTO `activity_logs` VALUES("806","10","login",NULL,NULL,"User logged in","118.69.64.122",NULL,"2026-01-01 14:44:34");
INSERT INTO `activity_logs` VALUES("807","10","video_broadcast","media","49","Video Broadcast - Gán \'AURORA HOTEL PLAZA - Nguyen Minh Hieu (1080p, h264)\' cho TV Basement, TV Chrysan, TV Jasmine, TV Lotus, TV Restaurant, TV FO 1, TV FO 2, TV Greeting","118.69.64.122",NULL,"2026-01-01 14:44:49");
INSERT INTO `activity_logs` VALUES("808","10","reload_all","tv","0","Ép tải lại TẤT CẢ 8 TV","118.69.64.122",NULL,"2026-01-01 14:44:49");
INSERT INTO `activity_logs` VALUES("809","10","login",NULL,NULL,"User logged in","222.253.191.65",NULL,"2026-01-01 15:27:07");
INSERT INTO `activity_logs` VALUES("810","10","reload_all","tv","0","Ép tải lại TẤT CẢ 8 TV","222.253.191.65",NULL,"2026-01-01 15:27:20");
INSERT INTO `activity_logs` VALUES("811","10","reload_all","tv","0","Ép tải lại TẤT CẢ 8 TV","222.253.191.65",NULL,"2026-01-01 15:34:24");
INSERT INTO `activity_logs` VALUES("812","14","login",NULL,NULL,"User logged in","115.74.225.100",NULL,"2026-01-02 14:44:57");
INSERT INTO `activity_logs` VALUES("813","14","reload_all","tv","0","Ép tải lại TẤT CẢ 8 TV","115.74.225.100",NULL,"2026-01-02 14:45:45");
INSERT INTO `activity_logs` VALUES("814","14","fullscreen_all","tv","0","Gửi lệnh fullscreen đến tất cả 8 TV","115.74.225.100",NULL,"2026-01-02 14:45:45");
INSERT INTO `activity_logs` VALUES("815","14","reload_all","tv","0","Ép tải lại TẤT CẢ 8 TV","222.253.191.65",NULL,"2026-01-02 14:48:10");
INSERT INTO `activity_logs` VALUES("816","14","reload_all","tv","0","Ép tải lại TẤT CẢ 8 TV","222.253.191.65",NULL,"2026-01-02 14:50:52");
INSERT INTO `activity_logs` VALUES("817","14","fullscreen_all","tv","0","Gửi lệnh fullscreen đến tất cả 8 TV","222.253.191.65",NULL,"2026-01-02 14:50:52");
INSERT INTO `activity_logs` VALUES("818","14","login",NULL,NULL,"User logged in","115.74.225.100",NULL,"2026-01-03 12:26:05");
INSERT INTO `activity_logs` VALUES("819","10","login",NULL,NULL,"User logged in","222.253.191.65",NULL,"2026-01-03 15:57:26");
INSERT INTO `activity_logs` VALUES("820","10","reload_all","tv","0","Ép tải lại TẤT CẢ 8 TV","222.253.191.65",NULL,"2026-01-03 15:57:36");
INSERT INTO `activity_logs` VALUES("821","10","video_broadcast","media","49","Video Broadcast - Gán \'AURORA HOTEL PLAZA - Nguyen Minh Hieu (1080p, h264)\' cho TV Basement, TV Chrysan, TV Jasmine, TV Lotus, TV Restaurant, TV FO 1, TV FO 2, TV Greeting","222.253.191.65",NULL,"2026-01-03 16:00:57");
INSERT INTO `activity_logs` VALUES("822","10","reload_all","tv","0","Ép tải lại TẤT CẢ 8 TV","222.253.191.65",NULL,"2026-01-03 16:00:57");
INSERT INTO `activity_logs` VALUES("823","10","reload_all","tv","0","Ép tải lại TẤT CẢ 8 TV","222.253.191.65",NULL,"2026-01-03 16:04:21");
INSERT INTO `activity_logs` VALUES("824","10","video_broadcast","media","49","Video Broadcast - Gán \'AURORA HOTEL PLAZA - Nguyen Minh Hieu (1080p, h264)\' cho TV Basement, TV Chrysan, TV Jasmine, TV Lotus, TV Restaurant, TV FO 1, TV FO 2, TV Greeting","222.253.191.65",NULL,"2026-01-03 16:04:43");
INSERT INTO `activity_logs` VALUES("825","10","reload_all","tv","0","Ép tải lại TẤT CẢ 8 TV","222.253.191.65",NULL,"2026-01-03 16:04:43");
INSERT INTO `activity_logs` VALUES("826","10","reload_all","tv","0","Ép tải lại TẤT CẢ 8 TV","222.253.191.65",NULL,"2026-01-03 16:05:03");
INSERT INTO `activity_logs` VALUES("827","10","orchid_mode","media","49","Áp dụng chế độ Orchid - Gán \'AURORA HOTEL PLAZA - Nguyen Minh Hieu (1080p, h264)\' cho TV Basement, TV Chrysan, TV Jasmine, TV Lotus, TV FO 1, TV FO 2 và bật tất cả TV","222.253.191.65",NULL,"2026-01-03 16:05:12");
INSERT INTO `activity_logs` VALUES("828","10","login",NULL,NULL,"User logged in","222.253.191.65",NULL,"2026-01-03 18:51:57");
INSERT INTO `activity_logs` VALUES("829","10","update_settings","setting","0","Cập nhật 8 settings","222.253.191.65",NULL,"2026-01-03 18:59:49");
INSERT INTO `activity_logs` VALUES("830","10","reload_all","tv","0","Ép tải lại TẤT CẢ 8 TV","222.253.191.65",NULL,"2026-01-03 18:59:51");
INSERT INTO `activity_logs` VALUES("831","10","login",NULL,NULL,"User logged in","222.253.191.65",NULL,"2026-01-03 20:15:39");
INSERT INTO `activity_logs` VALUES("832","10","update_settings","setting","0","Cập nhật 8 settings","222.253.191.65",NULL,"2026-01-03 20:16:05");
INSERT INTO `activity_logs` VALUES("833","10","reload_all","tv","0","Ép tải lại TẤT CẢ 8 TV","222.253.191.65",NULL,"2026-01-03 20:16:07");
INSERT INTO `activity_logs` VALUES("834","10","update_settings","setting","0","Cập nhật 8 settings","222.253.191.65",NULL,"2026-01-03 20:20:35");
INSERT INTO `activity_logs` VALUES("835","10","reload_all","tv","0","Ép tải lại TẤT CẢ 8 TV","222.253.191.65",NULL,"2026-01-03 20:20:36");
INSERT INTO `activity_logs` VALUES("836","10","login",NULL,NULL,"User logged in","118.69.64.122",NULL,"2026-01-03 20:39:20");
INSERT INTO `activity_logs` VALUES("837","10","reload_all","tv","0","Ép tải lại TẤT CẢ 8 TV","118.69.64.122",NULL,"2026-01-03 20:39:28");
INSERT INTO `activity_logs` VALUES("838","10","login",NULL,NULL,"User logged in","118.69.64.122",NULL,"2026-01-03 20:48:58");
INSERT INTO `activity_logs` VALUES("839","10","reload_all","tv","0","Ép tải lại TẤT CẢ 8 TV","118.69.64.122",NULL,"2026-01-03 20:49:04");
INSERT INTO `activity_logs` VALUES("840","10","reload_all","tv","0","Ép tải lại TẤT CẢ 8 TV","118.69.64.122",NULL,"2026-01-03 20:51:22");
INSERT INTO `activity_logs` VALUES("841","10","reload_all","tv","0","Ép tải lại TẤT CẢ 8 TV","118.69.64.122",NULL,"2026-01-03 20:57:23");
INSERT INTO `activity_logs` VALUES("842","10","fullscreen_all","tv","0","Gửi lệnh fullscreen đến tất cả 8 TV","118.69.64.122",NULL,"2026-01-03 20:57:24");
INSERT INTO `activity_logs` VALUES("843","10","reload_all","tv","0","Ép tải lại TẤT CẢ 8 TV","118.69.64.122",NULL,"2026-01-03 20:57:24");
INSERT INTO `activity_logs` VALUES("844","10","login",NULL,NULL,"User logged in","118.69.64.122",NULL,"2026-01-03 21:01:03");
INSERT INTO `activity_logs` VALUES("845","10","reload_all","tv","0","Ép tải lại TẤT CẢ 8 TV","118.69.64.122",NULL,"2026-01-03 21:05:54");
INSERT INTO `activity_logs` VALUES("846","10","login",NULL,NULL,"User logged in","118.69.64.122",NULL,"2026-01-03 21:10:54");
INSERT INTO `activity_logs` VALUES("847","10","upload","media","50","Upload file: 7.12 chysab","118.69.64.122",NULL,"2026-01-03 21:13:52");
INSERT INTO `activity_logs` VALUES("848","10","shutdown","system",NULL,"Tắt toàn bộ hệ thống - Offline tất cả TV và gỡ gán toàn bộ WCB","118.69.64.122",NULL,"2026-01-03 21:14:06");
INSERT INTO `activity_logs` VALUES("849","10","toggle_status","tv","1","Bật TV \'TV Basement\'","118.69.64.122",NULL,"2026-01-03 21:14:37");
INSERT INTO `activity_logs` VALUES("850","10","toggle_status","tv","2","Tắt TV \'TV Chrysan\'","118.69.64.122",NULL,"2026-01-03 21:14:39");
INSERT INTO `activity_logs` VALUES("851","10","toggle_status","tv","4","Tắt TV \'TV Lotus\'","118.69.64.122",NULL,"2026-01-03 21:14:42");
INSERT INTO `activity_logs` VALUES("852","10","assign","media","50","Gán media \'7.12 chysab\' cho TV Basement","118.69.64.122",NULL,"2026-01-03 21:14:46");
INSERT INTO `activity_logs` VALUES("853","10","shutdown","system",NULL,"Tắt toàn bộ hệ thống - Offline tất cả TV và gỡ gán toàn bộ WCB","118.69.64.122",NULL,"2026-01-03 21:15:08");
INSERT INTO `activity_logs` VALUES("854","10","reload_all","tv","0","Ép tải lại TẤT CẢ 8 TV","118.69.64.122",NULL,"2026-01-03 21:15:16");
INSERT INTO `activity_logs` VALUES("855","10","assign","media","50","Gán media \'7.12 chysab\' cho TV Basement","118.69.64.122",NULL,"2026-01-03 21:15:22");
INSERT INTO `activity_logs` VALUES("856","10","video_broadcast","media","49","Video Broadcast - Gán \'AURORA HOTEL PLAZA - Nguyen Minh Hieu (1080p, h264)\' cho TV Basement, TV Chrysan, TV Jasmine, TV Lotus, TV FO 1, TV FO 2, TV Greeting","118.69.64.122",NULL,"2026-01-03 21:15:27");
INSERT INTO `activity_logs` VALUES("857","10","reload_all","tv","0","Ép tải lại TẤT CẢ 8 TV","118.69.64.122",NULL,"2026-01-03 21:15:28");
INSERT INTO `activity_logs` VALUES("858","14","login",NULL,NULL,"User logged in","222.253.191.65",NULL,"2026-01-04 08:53:09");
INSERT INTO `activity_logs` VALUES("859","14","login",NULL,NULL,"User logged in","222.253.191.65",NULL,"2026-01-04 08:54:56");
INSERT INTO `activity_logs` VALUES("860","14","login",NULL,NULL,"User logged in","115.74.225.100",NULL,"2026-01-04 09:49:30");
INSERT INTO `activity_logs` VALUES("861","10","login",NULL,NULL,"User logged in","1.53.196.173",NULL,"2026-01-04 09:52:01");
INSERT INTO `activity_logs` VALUES("862","10","login",NULL,NULL,"User logged in","1.53.196.173",NULL,"2026-01-04 09:52:40");
INSERT INTO `activity_logs` VALUES("863","10","video_broadcast","media","49","Video Broadcast - Gán \'AURORA HOTEL PLAZA - Nguyen Minh Hieu (1080p, h264)\' cho TV Basement, TV Chrysan, TV Jasmine, TV Lotus, TV Restaurant, TV FO 1, TV FO 2, TV Greeting","1.53.196.173",NULL,"2026-01-04 09:54:17");
INSERT INTO `activity_logs` VALUES("864","10","reload_all","tv","0","Ép tải lại TẤT CẢ 8 TV","1.53.196.173",NULL,"2026-01-04 09:54:17");
INSERT INTO `activity_logs` VALUES("865","14","logout",NULL,NULL,"User logged out","115.74.225.100",NULL,"2026-01-04 09:56:19");
INSERT INTO `activity_logs` VALUES("866","14","login",NULL,NULL,"User logged in","115.74.225.100",NULL,"2026-01-04 09:56:21");
INSERT INTO `activity_logs` VALUES("867","14","login",NULL,NULL,"User logged in","115.74.225.100",NULL,"2026-01-04 09:56:53");
INSERT INTO `activity_logs` VALUES("868","10","login",NULL,NULL,"User logged in","103.199.57.184",NULL,"2026-01-04 10:16:57");
INSERT INTO `activity_logs` VALUES("869","14","login",NULL,NULL,"User logged in","115.74.225.100",NULL,"2026-01-04 10:18:25");
INSERT INTO `activity_logs` VALUES("870","10","create_user","user","16","Tạo người dùng: IT","1.53.196.173",NULL,"2026-01-04 10:27:35");
INSERT INTO `activity_logs` VALUES("871","10","logout",NULL,NULL,"User logged out","1.53.196.173",NULL,"2026-01-04 10:27:37");
INSERT INTO `activity_logs` VALUES("872","16","login",NULL,NULL,"User logged in","1.53.196.173",NULL,"2026-01-04 10:27:43");

-- --------------------------------------------------------
-- Table structure for `media`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `media`;
CREATE TABLE `media` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_type` (`type`),
  KEY `idx_status` (`status`),
  KEY `idx_uploaded_by` (`uploaded_by`),
  KEY `idx_created_at` (`created_at`),
  KEY `idx_media_upload_time` (`uploaded_by`,`created_at`),
  FULLTEXT KEY `idx_search` (`name`,`description`),
  CONSTRAINT `media_ibfk_1` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `media`

INSERT INTO `media` VALUES("24","Abbort - Tầng 5 - Lotus","image","692709a29862c_1764166050_0.jpg","uploads/692709a29862c_1764166050_0.jpg","291759","image/jpeg",NULL,NULL,"1920","1080","","inactive",NULL,"2025-11-26 21:07:30","2025-12-01 08:24:27");
INSERT INTO `media` VALUES("25","Cao đẳng Hòa Bình Xuân Lộc - Orchild tầng 5","image","692709a2a026e_1764166050_1.jpg","uploads/692709a2a026e_1764166050_1.jpg","323821","image/jpeg",NULL,NULL,"1920","1080",NULL,"inactive",NULL,"2025-11-26 21:07:30","2025-12-01 08:24:24");
INSERT INTO `media` VALUES("26","VietCab tầng 6","image","692709a2a1ddd_1764166050_2.jpg","uploads/692709a2a1ddd_1764166050_2.jpg","505784","image/jpeg",NULL,NULL,"2560","1440",NULL,"inactive",NULL,"2025-11-26 21:07:30","2025-12-01 08:24:22");
INSERT INTO `media` VALUES("27","bgd-29:11:25 lotus 2","image","692e31c9ba22d_1764635081_0.jpg","uploads/692e31c9ba22d_1764635081_0.jpg","260601","image/jpeg",NULL,NULL,"1920","1080","WCB BGD&DT Sảnh Lotus ngày 29/11/2025","inactive","10","2025-12-02 07:24:41","2025-12-02 11:20:12");
INSERT INTO `media` VALUES("28","bgd-29:11:25 lotus","image","692e31c9ba7d9_1764635081_1.jpg","uploads/692e31c9ba7d9_1764635081_1.jpg","902099","image/jpeg",NULL,NULL,"2560","1452","WCB BGD&DT Sảnh Lotus ngày 29/11/2025","inactive","10","2025-12-02 07:24:41","2025-12-02 11:20:15");
INSERT INTO `media` VALUES("29","nestle 3:12:25 chrysan","image","692e31c9badc8_1764635081_2.jpg","uploads/692e31c9badc8_1764635081_2.jpg","123665","image/jpeg",NULL,NULL,"1920","1080","WCB NESTLE Sảnh Chrysan ngày 2/12/2025","inactive","10","2025-12-02 07:24:41","2025-12-02 11:21:57");
INSERT INTO `media` VALUES("30","bgd-29:11:25 lotus 2","image","692e6976e870e_1764649334_0.jpg","uploads/692e6976e870e_1764649334_0.jpg","260601","image/jpeg",NULL,NULL,"1920","1080",NULL,"inactive","10","2025-12-02 11:22:14","2025-12-02 14:01:01");
INSERT INTO `media` VALUES("31","bgd-29:11:25 lotus","image","692e6976e8e4a_1764649334_1.jpg","uploads/692e6976e8e4a_1764649334_1.jpg","902099","image/jpeg",NULL,NULL,"2560","1452",NULL,"inactive","10","2025-12-02 11:22:14","2025-12-02 14:00:58");
INSERT INTO `media` VALUES("32","nestle 3:12:25 chrysan","image","692e6976e95c3_1764649334_2.jpg","uploads/692e6976e95c3_1764649334_2.jpg","123665","image/jpeg",NULL,NULL,"1920","1080",NULL,"inactive","10","2025-12-02 11:22:14","2025-12-02 14:00:56");
INSERT INTO `media` VALUES("33","Ảnh màn hình 2025-12-02 lúc 13.23.58","image","692e8605de048_1764656645.png","uploads/692e8605de048_1764656645.png","3842915","image/png",NULL,NULL,"3360","2100","","inactive","10","2025-12-02 13:24:05","2025-12-02 13:24:28");
INSERT INTO `media` VALUES("34","nestle 3:12:25 chrysan","image","692e8f1fb1e61_1764658975.jpg","uploads/692e8f1fb1e61_1764658975.jpg","123665","image/jpeg",NULL,NULL,"1920","1080","","inactive","10","2025-12-02 14:02:55","2025-12-05 14:29:30");
INSERT INTO `media` VALUES("35","schaeffler chrysan 8-9:12 8h-17h","image","692fda49e8598_1764743753_0.jpg","uploads/692fda49e8598_1764743753_0.jpg","138160","image/jpeg",NULL,NULL,"1920","1080",NULL,"inactive","10","2025-12-03 13:35:53","2025-12-09 16:21:36");
INSERT INTO `media` VALUES("36","grimaud lotus 6:12 8h-17h","image","692fda49e9651_1764743753_1.jpg","uploads/692fda49e9651_1764743753_1.jpg","254415","image/jpeg",NULL,NULL,"2560","1440",NULL,"inactive","10","2025-12-03 13:35:53","2025-12-09 16:21:04");
INSERT INTO `media` VALUES("37","sở nội vụ 8-9:12 lotus","image","69316d5002c59_1764846928.jpg","uploads/69316d5002c59_1764846928.jpg","463123","image/jpeg",NULL,NULL,"1920","1080","","inactive","10","2025-12-04 18:15:28","2025-12-27 14:30:23");
INSERT INTO `media` VALUES("38","sở nội vụ 10-11:12 lotus","image","69316e57c17e2_1764847191.jpg","uploads/69316e57c17e2_1764847191.jpg","459839","image/jpeg",NULL,NULL,"1920","1080","","inactive","10","2025-12-04 18:19:51","2026-01-01 14:18:20");
INSERT INTO `media` VALUES("39","schl 8-9.12 chysan","image","69364b0b75a01_1765165835.jpg","uploads/69364b0b75a01_1765165835.jpg","142486","image/jpeg",NULL,NULL,"1920","1080","","inactive","10","2025-12-08 10:50:35","2026-01-01 14:18:18");
INSERT INTO `media` VALUES("40","File_WCB_1011","image","6937e94c1d373_1765271884.jpg","uploads/6937e94c1d373_1765271884.jpg","459839","image/jpeg",NULL,NULL,"1920","1080","","inactive","14","2025-12-09 16:18:04","2026-01-01 14:18:15");
INSERT INTO `media` VALUES("41","nestle","image","69448980039bf_1766099328.jpg","uploads/69448980039bf_1766099328.jpg","345861","image/jpeg",NULL,NULL,"1920","1080","","inactive","14","2025-12-19 06:08:48","2026-01-01 14:18:13");
INSERT INTO `media` VALUES("42","japfa","image","69448989b8ce1_1766099337.jpg","uploads/69448989b8ce1_1766099337.jpg","123000","image/jpeg",NULL,NULL,"1920","1080","","inactive","14","2025-12-19 06:08:57","2025-12-27 14:30:16");
INSERT INTO `media` VALUES("43","Giới thiệu CoinVlog (Video 5s)","video","6944f73e5609c_1766127422.mp4","uploads/6944f73e5609c_1766127422.mp4","1114978","video/mp4",NULL,NULL,NULL,NULL,"","inactive","10","2025-12-19 13:57:02","2025-12-22 11:49:04");
INSERT INTO `media` VALUES("44","AURORA HOTEL PLAZA","video","6948a2a299baf_1766367906.mp4","uploads/6948a2a299baf_1766367906.mp4","36960680","video/mp4",NULL,NULL,NULL,NULL,"","inactive","14","2025-12-22 08:45:09","2026-01-01 14:17:31");
INSERT INTO `media` VALUES("45","BUFFET NOEL","image","6948c91392888_1766377747.jpg","uploads/6948c91392888_1766377747.jpg","433237","image/jpeg",NULL,NULL,"1920","1080","","inactive","14","2025-12-22 11:29:07","2026-01-01 14:18:10");
INSERT INTO `media` VALUES("46","28-12 BẾN THÀNH TOURIST","image","694f8bc36cb24_1766820803.jpg","uploads/694f8bc36cb24_1766820803.jpg","217143","image/jpeg",NULL,NULL,"1920","1080","","inactive","14","2025-12-27 14:33:23","2026-01-01 14:18:06");
INSERT INTO `media` VALUES("47","30-12","image","6952497066cdc_1767000432.jpg","uploads/6952497066cdc_1767000432.jpg","205631","image/jpeg",NULL,NULL,"2560","1387","","inactive","14","2025-12-29 16:27:12","2026-01-01 14:17:41");
INSERT INTO `media` VALUES("48","seAH","image","69538b62ac8a2_1767082850.jpg","uploads/69538b62ac8a2_1767082850.jpg","205689","image/jpeg",NULL,NULL,"2560","1387","","inactive","10","2025-12-30 15:20:50","2026-01-01 14:17:40");
INSERT INTO `media` VALUES("49","AURORA HOTEL PLAZA - Nguyen Minh Hieu (1080p, h264)","video","69561fceab3f9_1767251918.mp4","uploads/69561fceab3f9_1767251918.mp4","37913114","video/mp4",NULL,NULL,NULL,NULL,"","active","10","2026-01-01 14:18:41","2026-01-01 14:18:41");
INSERT INTO `media` VALUES("50","7.12 chysab","image","695924204afbc_1767449632.jpg","uploads/695924204afbc_1767449632.jpg","123506","image/jpeg",NULL,NULL,"1920","1080","","active","10","2026-01-03 21:13:52","2026-01-03 21:13:52");

-- --------------------------------------------------------
-- Table structure for `notifications`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL COMMENT 'NULL means broadcast to all users',
  `type` enum('info','success','warning','error','tv_offline','schedule','system') DEFAULT 'info',
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `link` varchar(500) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `read_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_user_id` (`user_id`),
  KEY `idx_is_read` (`is_read`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `notifications`

INSERT INTO `notifications` VALUES("1",NULL,"info","Chào mừng đến WCB System","Hệ thống Welcome Board đã sẵn sàng hoạt động.","/index.php","0",NULL,"2025-12-01 07:35:45");
INSERT INTO `notifications` VALUES("2",NULL,"success","Cập nhật hệ thống","Phiên bản mới đã được cài đặt thành công.",NULL,"0",NULL,"2025-12-01 07:35:45");

-- --------------------------------------------------------
-- Table structure for `schedules`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `schedules`;
CREATE TABLE `schedules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_tv_id` (`tv_id`),
  KEY `idx_media_id` (`media_id`),
  KEY `idx_schedule_date` (`schedule_date`),
  KEY `idx_status` (`status`),
  KEY `idx_datetime` (`schedule_date`,`start_time`,`end_time`),
  KEY `idx_active_schedules` (`tv_id`,`status`,`schedule_date`,`start_time`,`end_time`),
  KEY `created_by` (`created_by`),
  KEY `idx_schedule_time_range` (`tv_id`,`status`,`schedule_date`,`start_time`,`end_time`),
  KEY `idx_schedule_repeat` (`repeat_type`,`repeat_until`,`status`),
  CONSTRAINT `schedules_ibfk_1` FOREIGN KEY (`tv_id`) REFERENCES `tvs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `schedules_ibfk_2` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`) ON DELETE CASCADE,
  CONSTRAINT `schedules_ibfk_3` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `security_keys`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `security_keys`;
CREATE TABLE `security_keys` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `key_name` varchar(50) NOT NULL,
  `key_value` varchar(255) NOT NULL,
  `previous_key` varchar(255) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp(),
  `updated_at` datetime NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `expires_at` datetime DEFAULT NULL,
  `auto_rotate` tinyint(1) DEFAULT 1,
  `rotate_days` int(11) DEFAULT 30,
  `is_active` tinyint(1) DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `key_name` (`key_name`),
  KEY `idx_key_name` (`key_name`),
  KEY `idx_expires` (`expires_at`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `security_keys`

INSERT INTO `security_keys` VALUES("1","secret_key","94dd7e3e40e05c07e17e96b90641b7c56aaa46678d53cff83a3879ec0d8a937f",NULL,"2025-12-02 10:27:48","2025-12-02 10:27:48","2026-01-01 03:27:48","1","30","1");
INSERT INTO `security_keys` VALUES("2","encryption_key","1d4341a9a987db697783dcc35142f065f07b0a9be20eb8bf8aff35032fedab7e",NULL,"2025-12-02 10:27:48","2025-12-02 10:27:48","2026-01-31 03:27:48","1","60","1");
INSERT INTO `security_keys` VALUES("3","api_key","cb9e58de46e472322571ff76b6e0eae2b9f07d50812c1a2a366a8466236c2c0c",NULL,"2025-12-02 10:27:48","2025-12-02 10:27:48","2025-12-09 03:27:48","1","7","1");
INSERT INTO `security_keys` VALUES("4","media_key","8c63011d61a612c5606dfec6dd94db0a51478c48bb35981fd94c18375d353dab",NULL,"2025-12-02 10:27:48","2025-12-02 10:27:48","2026-03-02 03:27:48","1","90","1");

-- --------------------------------------------------------
-- Table structure for `system_settings`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `system_settings`;
CREATE TABLE `system_settings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` enum('string','number','boolean','json') NOT NULL DEFAULT 'string',
  `description` text DEFAULT NULL,
  `is_public` tinyint(1) DEFAULT 0 COMMENT '1 = có thể xem công khai',
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `setting_key` (`setting_key`),
  UNIQUE KEY `unique_setting_key` (`setting_key`),
  KEY `idx_setting_key` (`setting_key`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `system_settings`

INSERT INTO `system_settings` VALUES("1","site_name","Aurora Hotel Plaza - Welcome Board System","string","Tên hệ thống","1","2025-11-27 08:35:46");
INSERT INTO `system_settings` VALUES("2","hotel_name","Quang Long Hotel","string","Tên khách sạn","1","2025-11-26 19:56:54");
INSERT INTO `system_settings` VALUES("3","heartbeat_interval","60","number","Khoảng thời gian gửi heartbeat (giây)","0","2025-11-26 19:56:54");
INSERT INTO `system_settings` VALUES("4","offline_threshold","300","number","Thời gian tối đa không nhận heartbeat để đánh dấu offline (giây)","0","2025-11-26 19:56:54");
INSERT INTO `system_settings` VALUES("5","max_upload_size","52428800","number","Kích thước file tối đa cho phép upload (bytes) - 50MB","0","2025-11-26 19:56:54");
INSERT INTO `system_settings` VALUES("6","allowed_image_types","jpg,jpeg,png,gif,webp","string","Các định dạng hình ảnh được phép","0","2025-11-26 19:56:54");
INSERT INTO `system_settings` VALUES("7","allowed_video_types","mp4,webm,avi,mov","string","Các định dạng video được phép","0","2025-11-26 19:56:54");
INSERT INTO `system_settings` VALUES("8","auto_refresh_interval","30","number","Khoảng thời gian tự động làm mới màn hình TV (giây)","1","2025-11-26 19:56:54");
INSERT INTO `system_settings` VALUES("9","default_transition","fade","string","Hiệu ứng chuyển cảnh mặc định","1","2025-11-26 19:56:54");
INSERT INTO `system_settings` VALUES("10","transition_duration","1","number","Thời gian chuyển cảnh (giây)","1","2025-11-26 19:56:54");
INSERT INTO `system_settings` VALUES("11","enable_logging","true","boolean","Bật/tắt ghi log hoạt động","0","2025-11-26 19:56:54");
INSERT INTO `system_settings` VALUES("12","timezone","Asia/Ho_Chi_Minh","string","Múi giờ hệ thống","0","2025-11-26 19:56:54");
INSERT INTO `system_settings` VALUES("13","language","vi","string","Ngôn ngữ mặc định","1","2025-11-26 19:56:54");
INSERT INTO `system_settings` VALUES("14","tv_reload_signal_1","1767495257","string","Reload signal for TV TV Basement","0","2026-01-04 09:54:17");
INSERT INTO `system_settings` VALUES("15","tv_reload_signal_6","1767495257","string","Reload signal for TV TV FO 1","0","2026-01-04 09:54:17");
INSERT INTO `system_settings` VALUES("16","tv_reload_signal_3","1767495257","string","Reload signal for TV TV Jasmine","0","2026-01-04 09:54:17");
INSERT INTO `system_settings` VALUES("17","tv_reload_signal_2","1767495257","string","Reload signal for TV TV Chrysan","0","2026-01-04 09:54:17");
INSERT INTO `system_settings` VALUES("18","tv_reload_signal_4","1767495257","string","Reload signal for TV TV Lotus","0","2026-01-04 09:54:17");
INSERT INTO `system_settings` VALUES("19","tv_reload_signal_5","1767495257","string","Reload signal for TV TV Restaurant","0","2026-01-04 09:54:17");
INSERT INTO `system_settings` VALUES("20","tv_reload_signal_7","1767495257","string","Reload signal for TV TV FO 2","0","2026-01-04 09:54:17");
INSERT INTO `system_settings` VALUES("21","tv_fullscreen_signal_1","1767448644","string","Fullscreen signal for TV TV Basement","0","2026-01-03 20:57:24");
INSERT INTO `system_settings` VALUES("22","tv_fullscreen_signal_2","1767448644","string","Fullscreen signal for TV TV Chrysan","0","2026-01-03 20:57:24");
INSERT INTO `system_settings` VALUES("23","tv_fullscreen_signal_3","1767448644","string","Fullscreen signal for TV TV Jasmine","0","2026-01-03 20:57:24");
INSERT INTO `system_settings` VALUES("24","tv_fullscreen_signal_4","1767448644","string","Fullscreen signal for TV TV Lotus","0","2026-01-03 20:57:24");
INSERT INTO `system_settings` VALUES("25","tv_fullscreen_signal_5","1767448644","string","Fullscreen signal for TV TV Restaurant","0","2026-01-03 20:57:24");
INSERT INTO `system_settings` VALUES("26","tv_fullscreen_signal_6","1767448644","string","Fullscreen signal for TV TV FO 1","0","2026-01-03 20:57:24");
INSERT INTO `system_settings` VALUES("27","tv_fullscreen_signal_7","1767448644","string","Fullscreen signal for TV TV FO 2","0","2026-01-03 20:57:24");
INSERT INTO `system_settings` VALUES("28","tv_test_mode","0","boolean","Test mode overlay for all TVs","0","2025-12-29 13:38:10");
INSERT INTO `system_settings` VALUES("29","tv_test_mode_timestamp","1766990290","string","Test mode change timestamp","0","2025-12-29 13:38:10");
INSERT INTO `system_settings` VALUES("30","tv_reload_signal_8","1767495257","string","Reload signal for TV TV Greeting","0","2026-01-04 09:54:17");
INSERT INTO `system_settings` VALUES("31","tv_fullscreen_signal_8","1767448644","string","Fullscreen signal for TV TV Greeting","0","2026-01-03 20:57:24");
INSERT INTO `system_settings` VALUES("32","auto_refresh","1","string",NULL,"0","2026-01-03 20:16:05");
INSERT INTO `system_settings` VALUES("33","refresh_interval","30","string",NULL,"0","2026-01-03 18:59:49");
INSERT INTO `system_settings` VALUES("34","auto_reload_enabled","1","string",NULL,"0","2026-01-03 20:16:05");
INSERT INTO `system_settings` VALUES("35","auto_reload_mode","smart","string",NULL,"0","2026-01-03 20:20:35");
INSERT INTO `system_settings` VALUES("36","auto_reload_interval","1","string",NULL,"0","2026-01-03 20:20:35");
INSERT INTO `system_settings` VALUES("37","auto_reload_threshold","20","string",NULL,"0","2026-01-03 20:16:05");

-- --------------------------------------------------------
-- Table structure for `tv_heartbeats`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `tv_heartbeats`;
CREATE TABLE `tv_heartbeats` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tv_id` int(11) NOT NULL,
  `status` enum('online','offline') NOT NULL,
  `current_media_id` int(11) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `idx_tv_id` (`tv_id`),
  KEY `idx_created_at` (`created_at`),
  CONSTRAINT `tv_heartbeats_ibfk_1` FOREIGN KEY (`tv_id`) REFERENCES `tvs` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- Table structure for `tv_media_assignments`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `tv_media_assignments`;
CREATE TABLE `tv_media_assignments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tv_id` int(11) NOT NULL,
  `media_id` int(11) NOT NULL,
  `is_default` tinyint(1) DEFAULT 0 COMMENT '1 = nội dung mặc định',
  `display_order` int(11) DEFAULT 0 COMMENT 'Thứ tự hiển thị',
  `assigned_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `assigned_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_tv_media` (`tv_id`,`media_id`),
  KEY `idx_tv_id` (`tv_id`),
  KEY `idx_media_id` (`media_id`),
  KEY `idx_is_default` (`is_default`),
  KEY `assigned_by` (`assigned_by`),
  CONSTRAINT `tv_media_assignments_ibfk_1` FOREIGN KEY (`tv_id`) REFERENCES `tvs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tv_media_assignments_ibfk_2` FOREIGN KEY (`media_id`) REFERENCES `media` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tv_media_assignments_ibfk_3` FOREIGN KEY (`assigned_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=281 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `tv_media_assignments`

INSERT INTO `tv_media_assignments` VALUES("273","1","49","1","0","2026-01-04 09:54:17","10");
INSERT INTO `tv_media_assignments` VALUES("274","2","49","1","0","2026-01-04 09:54:17","10");
INSERT INTO `tv_media_assignments` VALUES("275","3","49","1","0","2026-01-04 09:54:17","10");
INSERT INTO `tv_media_assignments` VALUES("276","4","49","1","0","2026-01-04 09:54:17","10");
INSERT INTO `tv_media_assignments` VALUES("277","5","49","1","0","2026-01-04 09:54:17","10");
INSERT INTO `tv_media_assignments` VALUES("278","6","49","1","0","2026-01-04 09:54:17","10");
INSERT INTO `tv_media_assignments` VALUES("279","7","49","1","0","2026-01-04 09:54:17","10");
INSERT INTO `tv_media_assignments` VALUES("280","8","49","1","0","2026-01-04 09:54:17","10");

-- --------------------------------------------------------
-- Table structure for `tv_reload_signals`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `tv_reload_signals`;
CREATE TABLE `tv_reload_signals` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `tv_id` int(11) NOT NULL,
  `signal_type` enum('reload','shutdown','change_content') DEFAULT 'reload',
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`payload`)),
  `processed` tinyint(1) DEFAULT 0,
  `processed_at` datetime DEFAULT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `created_by` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_tv_id` (`tv_id`),
  KEY `idx_processed` (`processed`),
  KEY `created_by` (`created_by`),
  CONSTRAINT `tv_reload_signals_ibfk_1` FOREIGN KEY (`tv_id`) REFERENCES `tvs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tv_reload_signals_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=941 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `tv_reload_signals`

INSERT INTO `tv_reload_signals` VALUES("1","1","reload",NULL,"1","2025-12-02 07:44:58","2025-12-02 07:44:56",NULL);
INSERT INTO `tv_reload_signals` VALUES("2","2","reload",NULL,"1","2025-12-02 07:44:57","2025-12-02 07:44:56",NULL);
INSERT INTO `tv_reload_signals` VALUES("3","3","reload",NULL,"1","2025-12-02 08:10:44","2025-12-02 07:44:56",NULL);
INSERT INTO `tv_reload_signals` VALUES("4","4","reload",NULL,"1","2025-12-02 07:44:58","2025-12-02 07:44:56",NULL);
INSERT INTO `tv_reload_signals` VALUES("5","5","reload",NULL,"1","2025-12-02 09:27:44","2025-12-02 07:44:56",NULL);
INSERT INTO `tv_reload_signals` VALUES("6","6","reload",NULL,"1","2025-12-02 14:06:09","2025-12-02 07:44:56",NULL);
INSERT INTO `tv_reload_signals` VALUES("7","7","reload",NULL,"0",NULL,"2025-12-02 07:44:56",NULL);
INSERT INTO `tv_reload_signals` VALUES("8","1","reload",NULL,"1","2025-12-02 07:45:30","2025-12-02 07:45:28",NULL);
INSERT INTO `tv_reload_signals` VALUES("9","2","reload",NULL,"1","2025-12-02 07:45:29","2025-12-02 07:45:28",NULL);
INSERT INTO `tv_reload_signals` VALUES("10","3","reload",NULL,"1","2025-12-02 08:10:43","2025-12-02 07:45:28",NULL);
INSERT INTO `tv_reload_signals` VALUES("11","4","reload",NULL,"1","2025-12-02 07:45:29","2025-12-02 07:45:28",NULL);
INSERT INTO `tv_reload_signals` VALUES("12","5","reload",NULL,"1","2025-12-02 09:27:43","2025-12-02 07:45:28",NULL);
INSERT INTO `tv_reload_signals` VALUES("13","6","reload",NULL,"1","2025-12-02 14:06:08","2025-12-02 07:45:28",NULL);
INSERT INTO `tv_reload_signals` VALUES("14","7","reload",NULL,"0",NULL,"2025-12-02 07:45:28",NULL);
INSERT INTO `tv_reload_signals` VALUES("15","1","reload",NULL,"1","2025-12-02 07:50:26","2025-12-02 07:50:25",NULL);
INSERT INTO `tv_reload_signals` VALUES("16","2","reload",NULL,"1","2025-12-02 07:50:27","2025-12-02 07:50:25",NULL);
INSERT INTO `tv_reload_signals` VALUES("17","3","reload",NULL,"1","2025-12-02 08:10:42","2025-12-02 07:50:25",NULL);
INSERT INTO `tv_reload_signals` VALUES("18","4","reload",NULL,"1","2025-12-02 07:50:26","2025-12-02 07:50:25",NULL);
INSERT INTO `tv_reload_signals` VALUES("19","5","reload",NULL,"1","2025-12-02 09:27:43","2025-12-02 07:50:25",NULL);
INSERT INTO `tv_reload_signals` VALUES("20","6","reload",NULL,"1","2025-12-02 14:06:08","2025-12-02 07:50:25",NULL);
INSERT INTO `tv_reload_signals` VALUES("21","7","reload",NULL,"0",NULL,"2025-12-02 07:50:25",NULL);
INSERT INTO `tv_reload_signals` VALUES("22","1","reload",NULL,"1","2025-12-02 07:55:37","2025-12-02 07:55:35",NULL);
INSERT INTO `tv_reload_signals` VALUES("23","2","reload",NULL,"1","2025-12-02 07:55:35","2025-12-02 07:55:35",NULL);
INSERT INTO `tv_reload_signals` VALUES("24","3","reload",NULL,"1","2025-12-02 08:10:41","2025-12-02 07:55:35",NULL);
INSERT INTO `tv_reload_signals` VALUES("25","4","reload",NULL,"1","2025-12-02 07:55:36","2025-12-02 07:55:35",NULL);
INSERT INTO `tv_reload_signals` VALUES("26","5","reload",NULL,"1","2025-12-02 09:27:42","2025-12-02 07:55:35",NULL);
INSERT INTO `tv_reload_signals` VALUES("27","6","reload",NULL,"1","2025-12-02 14:06:07","2025-12-02 07:55:35",NULL);
INSERT INTO `tv_reload_signals` VALUES("28","7","reload",NULL,"0",NULL,"2025-12-02 07:55:35",NULL);
INSERT INTO `tv_reload_signals` VALUES("29","1","reload",NULL,"1","2025-12-02 08:07:27","2025-12-02 08:07:25",NULL);
INSERT INTO `tv_reload_signals` VALUES("30","2","reload",NULL,"1","2025-12-02 08:07:27","2025-12-02 08:07:25",NULL);
INSERT INTO `tv_reload_signals` VALUES("31","3","reload",NULL,"1","2025-12-02 08:10:40","2025-12-02 08:07:25",NULL);
INSERT INTO `tv_reload_signals` VALUES("32","4","reload",NULL,"1","2025-12-02 08:07:27","2025-12-02 08:07:25",NULL);
INSERT INTO `tv_reload_signals` VALUES("33","5","reload",NULL,"1","2025-12-02 09:27:41","2025-12-02 08:07:25",NULL);
INSERT INTO `tv_reload_signals` VALUES("34","6","reload",NULL,"1","2025-12-02 14:06:06","2025-12-02 08:07:25",NULL);
INSERT INTO `tv_reload_signals` VALUES("35","7","reload",NULL,"0",NULL,"2025-12-02 08:07:25",NULL);
INSERT INTO `tv_reload_signals` VALUES("36","1","reload",NULL,"1","2025-12-02 08:07:43","2025-12-02 08:07:42",NULL);
INSERT INTO `tv_reload_signals` VALUES("37","2","reload",NULL,"1","2025-12-02 08:07:42","2025-12-02 08:07:42",NULL);
INSERT INTO `tv_reload_signals` VALUES("38","3","reload",NULL,"1","2025-12-02 08:10:39","2025-12-02 08:07:42",NULL);
INSERT INTO `tv_reload_signals` VALUES("39","4","reload",NULL,"1","2025-12-02 08:07:43","2025-12-02 08:07:42",NULL);
INSERT INTO `tv_reload_signals` VALUES("40","5","reload",NULL,"1","2025-12-02 09:27:41","2025-12-02 08:07:42",NULL);
INSERT INTO `tv_reload_signals` VALUES("41","6","reload",NULL,"1","2025-12-02 14:06:06","2025-12-02 08:07:42",NULL);
INSERT INTO `tv_reload_signals` VALUES("42","7","reload",NULL,"0",NULL,"2025-12-02 08:07:42",NULL);
INSERT INTO `tv_reload_signals` VALUES("43","1","reload",NULL,"1","2025-12-02 08:23:52","2025-12-02 08:23:51",NULL);
INSERT INTO `tv_reload_signals` VALUES("44","2","reload",NULL,"1","2025-12-02 08:23:53","2025-12-02 08:23:51",NULL);
INSERT INTO `tv_reload_signals` VALUES("45","3","reload",NULL,"1","2025-12-02 08:23:51","2025-12-02 08:23:51",NULL);
INSERT INTO `tv_reload_signals` VALUES("46","4","reload",NULL,"1","2025-12-02 08:23:52","2025-12-02 08:23:51",NULL);
INSERT INTO `tv_reload_signals` VALUES("47","5","reload",NULL,"1","2025-12-02 09:27:40","2025-12-02 08:23:51",NULL);
INSERT INTO `tv_reload_signals` VALUES("48","6","reload",NULL,"1","2025-12-02 14:06:05","2025-12-02 08:23:51",NULL);
INSERT INTO `tv_reload_signals` VALUES("49","7","reload",NULL,"0",NULL,"2025-12-02 08:23:51",NULL);
INSERT INTO `tv_reload_signals` VALUES("50","1","reload",NULL,"1","2025-12-02 08:26:11","2025-12-02 08:26:09",NULL);
INSERT INTO `tv_reload_signals` VALUES("51","2","reload",NULL,"1","2025-12-02 08:26:12","2025-12-02 08:26:09",NULL);
INSERT INTO `tv_reload_signals` VALUES("52","3","reload",NULL,"1","2025-12-02 08:26:10","2025-12-02 08:26:09",NULL);
INSERT INTO `tv_reload_signals` VALUES("53","4","reload",NULL,"1","2025-12-02 08:26:10","2025-12-02 08:26:09",NULL);
INSERT INTO `tv_reload_signals` VALUES("54","5","reload",NULL,"1","2025-12-02 09:27:39","2025-12-02 08:26:09",NULL);
INSERT INTO `tv_reload_signals` VALUES("55","6","reload",NULL,"1","2025-12-02 14:06:04","2025-12-02 08:26:09",NULL);
INSERT INTO `tv_reload_signals` VALUES("56","7","reload",NULL,"0",NULL,"2025-12-02 08:26:09",NULL);
INSERT INTO `tv_reload_signals` VALUES("57","1","reload",NULL,"1","2025-12-02 08:26:43","2025-12-02 08:26:42",NULL);
INSERT INTO `tv_reload_signals` VALUES("58","2","reload",NULL,"1","2025-12-02 08:26:42","2025-12-02 08:26:42",NULL);
INSERT INTO `tv_reload_signals` VALUES("59","3","reload",NULL,"1","2025-12-02 08:26:44","2025-12-02 08:26:42",NULL);
INSERT INTO `tv_reload_signals` VALUES("60","4","reload",NULL,"1","2025-12-02 08:26:44","2025-12-02 08:26:42",NULL);
INSERT INTO `tv_reload_signals` VALUES("61","5","reload",NULL,"1","2025-12-02 09:27:00","2025-12-02 08:26:42",NULL);
INSERT INTO `tv_reload_signals` VALUES("62","6","reload",NULL,"1","2025-12-02 14:06:04","2025-12-02 08:26:42",NULL);
INSERT INTO `tv_reload_signals` VALUES("63","7","reload",NULL,"0",NULL,"2025-12-02 08:26:42",NULL);
INSERT INTO `tv_reload_signals` VALUES("64","1","reload",NULL,"1","2025-12-02 08:29:11","2025-12-02 08:29:08",NULL);
INSERT INTO `tv_reload_signals` VALUES("65","2","reload",NULL,"1","2025-12-02 08:29:09","2025-12-02 08:29:08",NULL);
INSERT INTO `tv_reload_signals` VALUES("66","3","reload",NULL,"1","2025-12-02 08:29:10","2025-12-02 08:29:08",NULL);
INSERT INTO `tv_reload_signals` VALUES("67","4","reload",NULL,"1","2025-12-02 08:29:09","2025-12-02 08:29:08",NULL);
INSERT INTO `tv_reload_signals` VALUES("68","5","reload",NULL,"1","2025-12-02 09:26:59","2025-12-02 08:29:08",NULL);
INSERT INTO `tv_reload_signals` VALUES("69","6","reload",NULL,"1","2025-12-02 14:06:03","2025-12-02 08:29:08",NULL);
INSERT INTO `tv_reload_signals` VALUES("70","7","reload",NULL,"1","2025-12-02 10:48:01","2025-12-02 08:29:08",NULL);
INSERT INTO `tv_reload_signals` VALUES("71","1","reload",NULL,"1","2025-12-02 08:29:17","2025-12-02 08:29:16",NULL);
INSERT INTO `tv_reload_signals` VALUES("72","2","reload",NULL,"1","2025-12-02 08:29:17","2025-12-02 08:29:16",NULL);
INSERT INTO `tv_reload_signals` VALUES("73","3","reload",NULL,"1","2025-12-02 08:29:16","2025-12-02 08:29:16",NULL);
INSERT INTO `tv_reload_signals` VALUES("74","4","reload",NULL,"1","2025-12-02 08:29:16","2025-12-02 08:29:16",NULL);
INSERT INTO `tv_reload_signals` VALUES("75","5","reload",NULL,"1","2025-12-02 09:26:59","2025-12-02 08:29:16",NULL);
INSERT INTO `tv_reload_signals` VALUES("76","6","reload",NULL,"1","2025-12-02 14:06:02","2025-12-02 08:29:16",NULL);
INSERT INTO `tv_reload_signals` VALUES("77","7","reload",NULL,"1","2025-12-02 10:48:00","2025-12-02 08:29:16",NULL);
INSERT INTO `tv_reload_signals` VALUES("78","1","reload",NULL,"1","2025-12-02 08:31:14","2025-12-02 08:31:12",NULL);
INSERT INTO `tv_reload_signals` VALUES("79","2","reload",NULL,"1","2025-12-02 08:31:14","2025-12-02 08:31:12",NULL);
INSERT INTO `tv_reload_signals` VALUES("80","3","reload",NULL,"1","2025-12-02 08:31:14","2025-12-02 08:31:12",NULL);
INSERT INTO `tv_reload_signals` VALUES("81","4","reload",NULL,"1","2025-12-02 08:31:14","2025-12-02 08:31:12",NULL);
INSERT INTO `tv_reload_signals` VALUES("82","5","reload",NULL,"1","2025-12-02 09:26:58","2025-12-02 08:31:12",NULL);
INSERT INTO `tv_reload_signals` VALUES("83","6","reload",NULL,"1","2025-12-02 14:06:02","2025-12-02 08:31:12",NULL);
INSERT INTO `tv_reload_signals` VALUES("84","7","reload",NULL,"1","2025-12-02 10:48:00","2025-12-02 08:31:12",NULL);
INSERT INTO `tv_reload_signals` VALUES("85","1","reload",NULL,"1","2025-12-02 09:22:21","2025-12-02 09:22:20",NULL);
INSERT INTO `tv_reload_signals` VALUES("86","2","reload",NULL,"1","2025-12-02 09:22:21","2025-12-02 09:22:20",NULL);
INSERT INTO `tv_reload_signals` VALUES("87","3","reload",NULL,"1","2025-12-02 09:22:22","2025-12-02 09:22:20",NULL);
INSERT INTO `tv_reload_signals` VALUES("88","4","reload",NULL,"1","2025-12-02 09:22:21","2025-12-02 09:22:20",NULL);
INSERT INTO `tv_reload_signals` VALUES("89","5","reload",NULL,"1","2025-12-02 09:26:58","2025-12-02 09:22:20",NULL);
INSERT INTO `tv_reload_signals` VALUES("90","6","reload",NULL,"1","2025-12-02 14:06:01","2025-12-02 09:22:20",NULL);
INSERT INTO `tv_reload_signals` VALUES("91","7","reload",NULL,"1","2025-12-02 10:47:59","2025-12-02 09:22:20",NULL);
INSERT INTO `tv_reload_signals` VALUES("92","1","reload",NULL,"1","2025-12-02 09:26:19","2025-12-02 09:26:16",NULL);
INSERT INTO `tv_reload_signals` VALUES("93","2","reload",NULL,"1","2025-12-02 09:26:17","2025-12-02 09:26:16",NULL);
INSERT INTO `tv_reload_signals` VALUES("94","3","reload",NULL,"1","2025-12-02 09:26:17","2025-12-02 09:26:16",NULL);
INSERT INTO `tv_reload_signals` VALUES("95","4","reload",NULL,"1","2025-12-02 09:26:17","2025-12-02 09:26:16",NULL);
INSERT INTO `tv_reload_signals` VALUES("96","5","reload",NULL,"1","2025-12-02 09:26:57","2025-12-02 09:26:16",NULL);
INSERT INTO `tv_reload_signals` VALUES("97","6","reload",NULL,"1","2025-12-02 14:06:00","2025-12-02 09:26:16",NULL);
INSERT INTO `tv_reload_signals` VALUES("98","7","reload",NULL,"1","2025-12-02 10:47:58","2025-12-02 09:26:16",NULL);
INSERT INTO `tv_reload_signals` VALUES("99","1","reload",NULL,"1","2025-12-02 09:27:07","2025-12-02 09:27:06",NULL);
INSERT INTO `tv_reload_signals` VALUES("100","2","reload",NULL,"1","2025-12-02 09:27:08","2025-12-02 09:27:06",NULL);
INSERT INTO `tv_reload_signals` VALUES("101","3","reload",NULL,"1","2025-12-02 09:27:08","2025-12-02 09:27:06",NULL);
INSERT INTO `tv_reload_signals` VALUES("102","4","reload",NULL,"1","2025-12-02 09:27:08","2025-12-02 09:27:06",NULL);
INSERT INTO `tv_reload_signals` VALUES("103","5","reload",NULL,"1","2025-12-02 09:27:39","2025-12-02 09:27:06",NULL);
INSERT INTO `tv_reload_signals` VALUES("104","6","reload",NULL,"1","2025-12-02 14:06:00","2025-12-02 09:27:06",NULL);
INSERT INTO `tv_reload_signals` VALUES("105","7","reload",NULL,"1","2025-12-02 10:47:58","2025-12-02 09:27:06",NULL);
INSERT INTO `tv_reload_signals` VALUES("106","1","reload",NULL,"1","2025-12-02 09:27:37","2025-12-02 09:27:35",NULL);
INSERT INTO `tv_reload_signals` VALUES("107","2","reload",NULL,"1","2025-12-02 09:27:36","2025-12-02 09:27:35",NULL);
INSERT INTO `tv_reload_signals` VALUES("108","3","reload",NULL,"1","2025-12-02 09:27:37","2025-12-02 09:27:35",NULL);
INSERT INTO `tv_reload_signals` VALUES("109","4","reload",NULL,"1","2025-12-02 09:27:36","2025-12-02 09:27:35",NULL);
INSERT INTO `tv_reload_signals` VALUES("110","5","reload",NULL,"1","2025-12-02 09:27:38","2025-12-02 09:27:35",NULL);
INSERT INTO `tv_reload_signals` VALUES("111","6","reload",NULL,"1","2025-12-02 14:05:59","2025-12-02 09:27:35",NULL);
INSERT INTO `tv_reload_signals` VALUES("112","7","reload",NULL,"1","2025-12-02 10:47:57","2025-12-02 09:27:35",NULL);
INSERT INTO `tv_reload_signals` VALUES("113","1","reload",NULL,"1","2025-12-02 09:28:26","2025-12-02 09:28:19",NULL);
INSERT INTO `tv_reload_signals` VALUES("114","2","reload",NULL,"1","2025-12-02 09:28:22","2025-12-02 09:28:19",NULL);
INSERT INTO `tv_reload_signals` VALUES("115","3","reload",NULL,"1","2025-12-02 09:28:23","2025-12-02 09:28:19",NULL);
INSERT INTO `tv_reload_signals` VALUES("116","4","reload",NULL,"1","2025-12-02 09:28:22","2025-12-02 09:28:19",NULL);
INSERT INTO `tv_reload_signals` VALUES("117","5","reload",NULL,"1","2025-12-02 09:28:22","2025-12-02 09:28:19",NULL);
INSERT INTO `tv_reload_signals` VALUES("118","6","reload",NULL,"1","2025-12-02 14:05:58","2025-12-02 09:28:19",NULL);
INSERT INTO `tv_reload_signals` VALUES("119","7","reload",NULL,"1","2025-12-02 10:47:56","2025-12-02 09:28:19",NULL);
INSERT INTO `tv_reload_signals` VALUES("120","1","reload",NULL,"1","2025-12-02 09:48:42","2025-12-02 09:48:41",NULL);
INSERT INTO `tv_reload_signals` VALUES("121","2","reload",NULL,"1","2025-12-02 09:48:44","2025-12-02 09:48:41",NULL);
INSERT INTO `tv_reload_signals` VALUES("122","3","reload",NULL,"1","2025-12-02 09:48:45","2025-12-02 09:48:41",NULL);
INSERT INTO `tv_reload_signals` VALUES("123","4","reload",NULL,"1","2025-12-02 09:48:43","2025-12-02 09:48:41",NULL);
INSERT INTO `tv_reload_signals` VALUES("124","5","reload",NULL,"1","2025-12-02 09:48:46","2025-12-02 09:48:41",NULL);
INSERT INTO `tv_reload_signals` VALUES("125","6","reload",NULL,"1","2025-12-02 14:05:58","2025-12-02 09:48:41",NULL);
INSERT INTO `tv_reload_signals` VALUES("126","7","reload",NULL,"1","2025-12-02 10:47:56","2025-12-02 09:48:41",NULL);
INSERT INTO `tv_reload_signals` VALUES("127","1","reload",NULL,"1","2025-12-02 10:00:51","2025-12-02 10:00:49",NULL);
INSERT INTO `tv_reload_signals` VALUES("128","2","reload",NULL,"1","2025-12-02 10:00:51","2025-12-02 10:00:49",NULL);
INSERT INTO `tv_reload_signals` VALUES("129","3","reload",NULL,"1","2025-12-02 10:00:52","2025-12-02 10:00:49",NULL);
INSERT INTO `tv_reload_signals` VALUES("130","4","reload",NULL,"1","2025-12-02 10:00:50","2025-12-02 10:00:49",NULL);
INSERT INTO `tv_reload_signals` VALUES("131","5","reload",NULL,"1","2025-12-02 10:01:01","2025-12-02 10:00:49",NULL);
INSERT INTO `tv_reload_signals` VALUES("132","6","reload",NULL,"1","2025-12-02 14:05:57","2025-12-02 10:00:49",NULL);
INSERT INTO `tv_reload_signals` VALUES("133","7","reload",NULL,"1","2025-12-02 10:47:55","2025-12-02 10:00:49",NULL);
INSERT INTO `tv_reload_signals` VALUES("134","1","reload",NULL,"1","2025-12-02 11:07:00","2025-12-02 10:50:17",NULL);
INSERT INTO `tv_reload_signals` VALUES("135","2","reload",NULL,"1","2025-12-02 11:12:23","2025-12-02 10:50:17",NULL);
INSERT INTO `tv_reload_signals` VALUES("136","3","reload",NULL,"1","2025-12-02 11:13:38","2025-12-02 10:50:17",NULL);
INSERT INTO `tv_reload_signals` VALUES("137","4","reload",NULL,"1","2025-12-02 11:14:13","2025-12-02 10:50:17",NULL);
INSERT INTO `tv_reload_signals` VALUES("138","5","reload",NULL,"1","2025-12-18 11:40:50","2025-12-02 10:50:17",NULL);
INSERT INTO `tv_reload_signals` VALUES("139","6","reload",NULL,"1","2025-12-02 14:05:57","2025-12-02 10:50:17",NULL);
INSERT INTO `tv_reload_signals` VALUES("140","7","reload",NULL,"0",NULL,"2025-12-02 10:50:17",NULL);
INSERT INTO `tv_reload_signals` VALUES("141","1","reload",NULL,"1","2025-12-02 11:10:07","2025-12-02 11:10:06",NULL);
INSERT INTO `tv_reload_signals` VALUES("142","2","reload",NULL,"1","2025-12-02 11:12:22","2025-12-02 11:10:06",NULL);
INSERT INTO `tv_reload_signals` VALUES("143","3","reload",NULL,"1","2025-12-02 11:13:36","2025-12-02 11:10:06",NULL);
INSERT INTO `tv_reload_signals` VALUES("144","4","reload",NULL,"1","2025-12-02 11:14:12","2025-12-02 11:10:06",NULL);
INSERT INTO `tv_reload_signals` VALUES("145","5","reload",NULL,"1","2025-12-18 11:40:48","2025-12-02 11:10:06",NULL);
INSERT INTO `tv_reload_signals` VALUES("146","6","reload",NULL,"1","2025-12-02 14:05:56","2025-12-02 11:10:06",NULL);
INSERT INTO `tv_reload_signals` VALUES("147","7","reload",NULL,"0",NULL,"2025-12-02 11:10:06",NULL);
INSERT INTO `tv_reload_signals` VALUES("148","1","reload",NULL,"1","2025-12-02 11:10:17","2025-12-02 11:10:15",NULL);
INSERT INTO `tv_reload_signals` VALUES("149","2","reload",NULL,"1","2025-12-02 11:12:22","2025-12-02 11:10:15",NULL);
INSERT INTO `tv_reload_signals` VALUES("150","3","reload",NULL,"1","2025-12-02 11:13:35","2025-12-02 11:10:15",NULL);
INSERT INTO `tv_reload_signals` VALUES("151","4","reload",NULL,"1","2025-12-02 11:14:11","2025-12-02 11:10:15",NULL);
INSERT INTO `tv_reload_signals` VALUES("152","5","reload",NULL,"1","2025-12-18 11:40:48","2025-12-02 11:10:15",NULL);
INSERT INTO `tv_reload_signals` VALUES("153","6","reload",NULL,"1","2025-12-02 14:05:55","2025-12-02 11:10:15",NULL);
INSERT INTO `tv_reload_signals` VALUES("154","7","reload",NULL,"0",NULL,"2025-12-02 11:10:15",NULL);
INSERT INTO `tv_reload_signals` VALUES("155","1","reload",NULL,"1","2025-12-02 11:10:26","2025-12-02 11:10:24",NULL);
INSERT INTO `tv_reload_signals` VALUES("156","2","reload",NULL,"1","2025-12-02 11:12:21","2025-12-02 11:10:24",NULL);
INSERT INTO `tv_reload_signals` VALUES("157","3","reload",NULL,"1","2025-12-02 11:13:34","2025-12-02 11:10:24",NULL);
INSERT INTO `tv_reload_signals` VALUES("158","4","reload",NULL,"1","2025-12-02 11:14:11","2025-12-02 11:10:24",NULL);
INSERT INTO `tv_reload_signals` VALUES("159","5","reload",NULL,"1","2025-12-18 11:40:47","2025-12-02 11:10:24",NULL);
INSERT INTO `tv_reload_signals` VALUES("160","6","reload",NULL,"1","2025-12-02 14:05:55","2025-12-02 11:10:24",NULL);
INSERT INTO `tv_reload_signals` VALUES("161","7","reload",NULL,"0",NULL,"2025-12-02 11:10:24",NULL);
INSERT INTO `tv_reload_signals` VALUES("162","1","reload",NULL,"1","2025-12-02 11:11:01","2025-12-02 11:11:00",NULL);
INSERT INTO `tv_reload_signals` VALUES("163","2","reload",NULL,"1","2025-12-02 11:12:20","2025-12-02 11:11:00",NULL);
INSERT INTO `tv_reload_signals` VALUES("164","3","reload",NULL,"1","2025-12-02 11:13:33","2025-12-02 11:11:00",NULL);
INSERT INTO `tv_reload_signals` VALUES("165","4","reload",NULL,"1","2025-12-02 11:14:10","2025-12-02 11:11:00",NULL);
INSERT INTO `tv_reload_signals` VALUES("166","5","reload",NULL,"1","2025-12-18 11:40:47","2025-12-02 11:11:00",NULL);
INSERT INTO `tv_reload_signals` VALUES("167","6","reload",NULL,"1","2025-12-02 14:05:54","2025-12-02 11:11:00",NULL);
INSERT INTO `tv_reload_signals` VALUES("168","7","reload",NULL,"0",NULL,"2025-12-02 11:11:00",NULL);
INSERT INTO `tv_reload_signals` VALUES("169","1","reload",NULL,"1","2025-12-02 11:11:09","2025-12-02 11:11:08",NULL);
INSERT INTO `tv_reload_signals` VALUES("170","2","reload",NULL,"1","2025-12-02 11:12:19","2025-12-02 11:11:08",NULL);
INSERT INTO `tv_reload_signals` VALUES("171","3","reload",NULL,"1","2025-12-02 11:13:32","2025-12-02 11:11:08",NULL);
INSERT INTO `tv_reload_signals` VALUES("172","4","reload",NULL,"1","2025-12-02 11:14:09","2025-12-02 11:11:08",NULL);
INSERT INTO `tv_reload_signals` VALUES("173","5","reload",NULL,"1","2025-12-18 11:40:46","2025-12-02 11:11:08",NULL);
INSERT INTO `tv_reload_signals` VALUES("174","6","reload",NULL,"1","2025-12-02 14:05:53","2025-12-02 11:11:08",NULL);
INSERT INTO `tv_reload_signals` VALUES("175","7","reload",NULL,"0",NULL,"2025-12-02 11:11:08",NULL);
INSERT INTO `tv_reload_signals` VALUES("176","1","reload",NULL,"1","2025-12-02 11:11:28","2025-12-02 11:11:26",NULL);
INSERT INTO `tv_reload_signals` VALUES("177","2","reload",NULL,"1","2025-12-02 11:12:19","2025-12-02 11:11:26",NULL);
INSERT INTO `tv_reload_signals` VALUES("178","3","reload",NULL,"1","2025-12-02 11:13:31","2025-12-02 11:11:26",NULL);
INSERT INTO `tv_reload_signals` VALUES("179","4","reload",NULL,"1","2025-12-02 11:14:08","2025-12-02 11:11:26",NULL);
INSERT INTO `tv_reload_signals` VALUES("180","5","reload",NULL,"1","2025-12-18 11:40:45","2025-12-02 11:11:26",NULL);
INSERT INTO `tv_reload_signals` VALUES("181","6","reload",NULL,"1","2025-12-02 14:05:53","2025-12-02 11:11:26",NULL);
INSERT INTO `tv_reload_signals` VALUES("182","7","reload",NULL,"0",NULL,"2025-12-02 11:11:26",NULL);
INSERT INTO `tv_reload_signals` VALUES("183","1","reload",NULL,"1","2025-12-02 11:15:27","2025-12-02 11:15:26",NULL);
INSERT INTO `tv_reload_signals` VALUES("184","2","reload",NULL,"1","2025-12-02 11:15:53","2025-12-02 11:15:26",NULL);
INSERT INTO `tv_reload_signals` VALUES("185","3","reload",NULL,"1","2025-12-02 11:15:28","2025-12-02 11:15:26",NULL);
INSERT INTO `tv_reload_signals` VALUES("186","4","reload",NULL,"1","2025-12-02 11:15:37","2025-12-02 11:15:26",NULL);
INSERT INTO `tv_reload_signals` VALUES("187","5","reload",NULL,"1","2025-12-18 11:40:45","2025-12-02 11:15:26",NULL);
INSERT INTO `tv_reload_signals` VALUES("188","6","reload",NULL,"1","2025-12-02 14:05:52","2025-12-02 11:15:26",NULL);
INSERT INTO `tv_reload_signals` VALUES("189","7","reload",NULL,"0",NULL,"2025-12-02 11:15:26",NULL);
INSERT INTO `tv_reload_signals` VALUES("190","1","reload",NULL,"1","2025-12-02 11:16:58","2025-12-02 11:16:57",NULL);
INSERT INTO `tv_reload_signals` VALUES("191","2","reload",NULL,"1","2025-12-02 11:16:59","2025-12-02 11:16:57",NULL);
INSERT INTO `tv_reload_signals` VALUES("192","3","reload",NULL,"1","2025-12-02 11:16:58","2025-12-02 11:16:57",NULL);
INSERT INTO `tv_reload_signals` VALUES("193","4","reload",NULL,"1","2025-12-02 11:16:58","2025-12-02 11:16:57",NULL);
INSERT INTO `tv_reload_signals` VALUES("194","5","reload",NULL,"1","2025-12-18 11:40:44","2025-12-02 11:16:57",NULL);
INSERT INTO `tv_reload_signals` VALUES("195","6","reload",NULL,"1","2025-12-02 14:05:50","2025-12-02 11:16:57",NULL);
INSERT INTO `tv_reload_signals` VALUES("196","7","reload",NULL,"0",NULL,"2025-12-02 11:16:57",NULL);
INSERT INTO `tv_reload_signals` VALUES("197","1","reload",NULL,"1","2025-12-02 11:17:32","2025-12-02 11:17:31",NULL);
INSERT INTO `tv_reload_signals` VALUES("198","2","reload",NULL,"1","2025-12-02 11:17:31","2025-12-02 11:17:31",NULL);
INSERT INTO `tv_reload_signals` VALUES("199","3","reload",NULL,"1","2025-12-02 11:17:32","2025-12-02 11:17:31",NULL);
INSERT INTO `tv_reload_signals` VALUES("200","4","reload",NULL,"1","2025-12-02 11:17:32","2025-12-02 11:17:31",NULL);
INSERT INTO `tv_reload_signals` VALUES("201","5","reload",NULL,"1","2025-12-18 11:40:44","2025-12-02 11:17:31",NULL);
INSERT INTO `tv_reload_signals` VALUES("202","6","reload",NULL,"1","2025-12-02 14:05:49","2025-12-02 11:17:31",NULL);
INSERT INTO `tv_reload_signals` VALUES("203","7","reload",NULL,"0",NULL,"2025-12-02 11:17:31",NULL);
INSERT INTO `tv_reload_signals` VALUES("204","1","reload",NULL,"1","2025-12-02 11:18:48","2025-12-02 11:18:46",NULL);
INSERT INTO `tv_reload_signals` VALUES("205","2","reload",NULL,"1","2025-12-02 11:18:48","2025-12-02 11:18:46",NULL);
INSERT INTO `tv_reload_signals` VALUES("206","3","reload",NULL,"1","2025-12-02 11:18:48","2025-12-02 11:18:46",NULL);
INSERT INTO `tv_reload_signals` VALUES("207","4","reload",NULL,"1","2025-12-02 11:18:48","2025-12-02 11:18:46",NULL);
INSERT INTO `tv_reload_signals` VALUES("208","5","reload",NULL,"1","2025-12-18 11:40:43","2025-12-02 11:18:46",NULL);
INSERT INTO `tv_reload_signals` VALUES("209","6","reload",NULL,"1","2025-12-02 14:05:49","2025-12-02 11:18:46",NULL);
INSERT INTO `tv_reload_signals` VALUES("210","7","reload",NULL,"0",NULL,"2025-12-02 11:18:46",NULL);
INSERT INTO `tv_reload_signals` VALUES("211","1","reload",NULL,"1","2025-12-02 11:21:32","2025-12-02 11:21:31",NULL);
INSERT INTO `tv_reload_signals` VALUES("212","2","reload",NULL,"1","2025-12-02 11:21:34","2025-12-02 11:21:31",NULL);
INSERT INTO `tv_reload_signals` VALUES("213","3","reload",NULL,"1","2025-12-02 11:21:31","2025-12-02 11:21:31",NULL);
INSERT INTO `tv_reload_signals` VALUES("214","4","reload",NULL,"1","2025-12-02 11:21:31","2025-12-02 11:21:31",NULL);
INSERT INTO `tv_reload_signals` VALUES("215","5","reload",NULL,"1","2025-12-18 11:40:43","2025-12-02 11:21:31",NULL);
INSERT INTO `tv_reload_signals` VALUES("216","6","reload",NULL,"1","2025-12-02 14:05:48","2025-12-02 11:21:31",NULL);
INSERT INTO `tv_reload_signals` VALUES("217","7","reload",NULL,"0",NULL,"2025-12-02 11:21:31",NULL);
INSERT INTO `tv_reload_signals` VALUES("218","1","reload",NULL,"1","2025-12-02 11:21:38","2025-12-02 11:21:36",NULL);
INSERT INTO `tv_reload_signals` VALUES("219","2","reload",NULL,"1","2025-12-02 11:21:38","2025-12-02 11:21:36",NULL);
INSERT INTO `tv_reload_signals` VALUES("220","3","reload",NULL,"1","2025-12-02 11:21:37","2025-12-02 11:21:36",NULL);
INSERT INTO `tv_reload_signals` VALUES("221","4","reload",NULL,"1","2025-12-02 11:21:37","2025-12-02 11:21:36",NULL);
INSERT INTO `tv_reload_signals` VALUES("222","5","reload",NULL,"1","2025-12-18 11:40:42","2025-12-02 11:21:36",NULL);
INSERT INTO `tv_reload_signals` VALUES("223","6","reload",NULL,"1","2025-12-02 14:05:47","2025-12-02 11:21:36",NULL);
INSERT INTO `tv_reload_signals` VALUES("224","7","reload",NULL,"0",NULL,"2025-12-02 11:21:36",NULL);
INSERT INTO `tv_reload_signals` VALUES("225","1","reload",NULL,"1","2025-12-02 11:28:39","2025-12-02 11:28:39",NULL);
INSERT INTO `tv_reload_signals` VALUES("226","2","reload",NULL,"1","2025-12-02 11:28:39","2025-12-02 11:28:39",NULL);
INSERT INTO `tv_reload_signals` VALUES("227","3","reload",NULL,"1","2025-12-02 11:28:42","2025-12-02 11:28:39",NULL);
INSERT INTO `tv_reload_signals` VALUES("228","4","reload",NULL,"1","2025-12-02 11:28:42","2025-12-02 11:28:39",NULL);
INSERT INTO `tv_reload_signals` VALUES("229","5","reload",NULL,"1","2025-12-18 11:40:42","2025-12-02 11:28:39",NULL);
INSERT INTO `tv_reload_signals` VALUES("230","6","reload",NULL,"1","2025-12-02 14:05:47","2025-12-02 11:28:39",NULL);
INSERT INTO `tv_reload_signals` VALUES("231","7","reload",NULL,"0",NULL,"2025-12-02 11:28:39",NULL);
INSERT INTO `tv_reload_signals` VALUES("232","1","reload",NULL,"1","2025-12-02 11:28:52","2025-12-02 11:28:50",NULL);
INSERT INTO `tv_reload_signals` VALUES("233","2","reload",NULL,"1","2025-12-02 11:28:50","2025-12-02 11:28:50",NULL);
INSERT INTO `tv_reload_signals` VALUES("234","3","reload",NULL,"1","2025-12-02 11:28:51","2025-12-02 11:28:50",NULL);
INSERT INTO `tv_reload_signals` VALUES("235","4","reload",NULL,"1","2025-12-02 11:28:51","2025-12-02 11:28:50",NULL);
INSERT INTO `tv_reload_signals` VALUES("236","5","reload",NULL,"1","2025-12-18 11:40:41","2025-12-02 11:28:50",NULL);
INSERT INTO `tv_reload_signals` VALUES("237","6","reload",NULL,"1","2025-12-02 14:05:46","2025-12-02 11:28:50",NULL);
INSERT INTO `tv_reload_signals` VALUES("238","7","reload",NULL,"0",NULL,"2025-12-02 11:28:50",NULL);
INSERT INTO `tv_reload_signals` VALUES("239","1","reload",NULL,"1","2025-12-02 11:33:11","2025-12-02 11:33:09",NULL);
INSERT INTO `tv_reload_signals` VALUES("240","2","reload",NULL,"1","2025-12-02 11:33:11","2025-12-02 11:33:09",NULL);
INSERT INTO `tv_reload_signals` VALUES("241","3","reload",NULL,"1","2025-12-02 11:33:11","2025-12-02 11:33:09",NULL);
INSERT INTO `tv_reload_signals` VALUES("242","4","reload",NULL,"1","2025-12-02 11:33:11","2025-12-02 11:33:09",NULL);
INSERT INTO `tv_reload_signals` VALUES("243","5","reload",NULL,"1","2025-12-18 11:40:40","2025-12-02 11:33:09",NULL);
INSERT INTO `tv_reload_signals` VALUES("244","6","reload",NULL,"1","2025-12-02 14:05:45","2025-12-02 11:33:09",NULL);
INSERT INTO `tv_reload_signals` VALUES("245","7","reload",NULL,"0",NULL,"2025-12-02 11:33:09",NULL);
INSERT INTO `tv_reload_signals` VALUES("246","1","reload",NULL,"1","2025-12-02 11:43:53","2025-12-02 11:43:53",NULL);
INSERT INTO `tv_reload_signals` VALUES("247","2","reload",NULL,"1","2025-12-02 11:43:53","2025-12-02 11:43:53",NULL);
INSERT INTO `tv_reload_signals` VALUES("248","3","reload",NULL,"1","2025-12-02 11:43:53","2025-12-02 11:43:53",NULL);
INSERT INTO `tv_reload_signals` VALUES("249","4","reload",NULL,"1","2025-12-02 11:43:55","2025-12-02 11:43:53",NULL);
INSERT INTO `tv_reload_signals` VALUES("250","5","reload",NULL,"1","2025-12-18 11:40:40","2025-12-02 11:43:53",NULL);
INSERT INTO `tv_reload_signals` VALUES("251","6","reload",NULL,"1","2025-12-02 14:05:45","2025-12-02 11:43:53",NULL);
INSERT INTO `tv_reload_signals` VALUES("252","7","reload",NULL,"0",NULL,"2025-12-02 11:43:53",NULL);
INSERT INTO `tv_reload_signals` VALUES("253","1","reload",NULL,"1","2025-12-02 11:44:04","2025-12-02 11:44:01",NULL);
INSERT INTO `tv_reload_signals` VALUES("254","2","reload",NULL,"1","2025-12-02 11:44:04","2025-12-02 11:44:01",NULL);
INSERT INTO `tv_reload_signals` VALUES("255","3","reload",NULL,"1","2025-12-02 11:44:08","2025-12-02 11:44:01",NULL);
INSERT INTO `tv_reload_signals` VALUES("256","4","reload",NULL,"1","2025-12-02 11:44:02","2025-12-02 11:44:01",NULL);
INSERT INTO `tv_reload_signals` VALUES("257","5","reload",NULL,"1","2025-12-18 11:40:40","2025-12-02 11:44:01",NULL);
INSERT INTO `tv_reload_signals` VALUES("258","6","reload",NULL,"1","2025-12-02 14:05:44","2025-12-02 11:44:01",NULL);
INSERT INTO `tv_reload_signals` VALUES("259","7","reload",NULL,"0",NULL,"2025-12-02 11:44:01",NULL);
INSERT INTO `tv_reload_signals` VALUES("260","1","reload",NULL,"1","2025-12-02 13:17:21","2025-12-02 13:17:20",NULL);
INSERT INTO `tv_reload_signals` VALUES("261","2","reload",NULL,"1","2025-12-02 13:17:22","2025-12-02 13:17:20",NULL);
INSERT INTO `tv_reload_signals` VALUES("262","3","reload",NULL,"1","2025-12-02 13:17:22","2025-12-02 13:17:20",NULL);
INSERT INTO `tv_reload_signals` VALUES("263","4","reload",NULL,"1","2025-12-02 13:17:21","2025-12-02 13:17:20",NULL);
INSERT INTO `tv_reload_signals` VALUES("264","5","reload",NULL,"1","2025-12-18 11:40:39","2025-12-02 13:17:20",NULL);
INSERT INTO `tv_reload_signals` VALUES("265","6","reload",NULL,"1","2025-12-02 14:05:43","2025-12-02 13:17:20",NULL);
INSERT INTO `tv_reload_signals` VALUES("266","7","reload",NULL,"0",NULL,"2025-12-02 13:17:20",NULL);
INSERT INTO `tv_reload_signals` VALUES("267","1","reload",NULL,"1","2025-12-02 13:17:41","2025-12-02 13:17:41",NULL);
INSERT INTO `tv_reload_signals` VALUES("268","2","reload",NULL,"1","2025-12-02 13:17:43","2025-12-02 13:17:41",NULL);
INSERT INTO `tv_reload_signals` VALUES("269","3","reload",NULL,"1","2025-12-02 13:17:42","2025-12-02 13:17:41",NULL);
INSERT INTO `tv_reload_signals` VALUES("270","4","reload",NULL,"1","2025-12-02 13:17:42","2025-12-02 13:17:41",NULL);
INSERT INTO `tv_reload_signals` VALUES("271","5","reload",NULL,"1","2025-12-18 11:40:38","2025-12-02 13:17:41",NULL);
INSERT INTO `tv_reload_signals` VALUES("272","6","reload",NULL,"1","2025-12-02 14:05:43","2025-12-02 13:17:41",NULL);
INSERT INTO `tv_reload_signals` VALUES("273","7","reload",NULL,"0",NULL,"2025-12-02 13:17:41",NULL);
INSERT INTO `tv_reload_signals` VALUES("274","1","reload",NULL,"1","2025-12-02 13:18:31","2025-12-02 13:18:29",NULL);
INSERT INTO `tv_reload_signals` VALUES("275","2","reload",NULL,"1","2025-12-02 13:18:33","2025-12-02 13:18:29",NULL);
INSERT INTO `tv_reload_signals` VALUES("276","3","reload",NULL,"1","2025-12-02 13:18:32","2025-12-02 13:18:29",NULL);
INSERT INTO `tv_reload_signals` VALUES("277","4","reload",NULL,"1","2025-12-02 13:18:31","2025-12-02 13:18:29",NULL);
INSERT INTO `tv_reload_signals` VALUES("278","5","reload",NULL,"1","2025-12-18 11:40:36","2025-12-02 13:18:29",NULL);
INSERT INTO `tv_reload_signals` VALUES("279","6","reload",NULL,"1","2025-12-02 14:05:42","2025-12-02 13:18:29",NULL);
INSERT INTO `tv_reload_signals` VALUES("280","7","reload",NULL,"0",NULL,"2025-12-02 13:18:29",NULL);
INSERT INTO `tv_reload_signals` VALUES("281","1","reload",NULL,"1","2025-12-02 13:18:51","2025-12-02 13:18:48",NULL);
INSERT INTO `tv_reload_signals` VALUES("282","2","reload",NULL,"1","2025-12-02 13:18:49","2025-12-02 13:18:48",NULL);
INSERT INTO `tv_reload_signals` VALUES("283","3","reload",NULL,"1","2025-12-02 13:18:52","2025-12-02 13:18:48",NULL);
INSERT INTO `tv_reload_signals` VALUES("284","4","reload",NULL,"1","2025-12-02 13:18:51","2025-12-02 13:18:48",NULL);
INSERT INTO `tv_reload_signals` VALUES("285","5","reload",NULL,"1","2025-12-18 11:40:36","2025-12-02 13:18:48",NULL);
INSERT INTO `tv_reload_signals` VALUES("286","6","reload",NULL,"1","2025-12-02 14:05:41","2025-12-02 13:18:48",NULL);
INSERT INTO `tv_reload_signals` VALUES("287","7","reload",NULL,"0",NULL,"2025-12-02 13:18:48",NULL);
INSERT INTO `tv_reload_signals` VALUES("288","1","reload",NULL,"1","2025-12-02 13:19:32","2025-12-02 13:19:29",NULL);
INSERT INTO `tv_reload_signals` VALUES("289","2","reload",NULL,"1","2025-12-02 13:19:32","2025-12-02 13:19:29",NULL);
INSERT INTO `tv_reload_signals` VALUES("290","3","reload",NULL,"1","2025-12-02 13:19:32","2025-12-02 13:19:29",NULL);
INSERT INTO `tv_reload_signals` VALUES("291","4","reload",NULL,"1","2025-12-02 13:19:31","2025-12-02 13:19:29",NULL);
INSERT INTO `tv_reload_signals` VALUES("292","5","reload",NULL,"1","2025-12-18 11:40:35","2025-12-02 13:19:29",NULL);
INSERT INTO `tv_reload_signals` VALUES("293","6","reload",NULL,"1","2025-12-02 14:05:41","2025-12-02 13:19:29",NULL);
INSERT INTO `tv_reload_signals` VALUES("294","7","reload",NULL,"0",NULL,"2025-12-02 13:19:29",NULL);
INSERT INTO `tv_reload_signals` VALUES("295","1","reload",NULL,"1","2025-12-02 13:19:52","2025-12-02 13:19:50",NULL);
INSERT INTO `tv_reload_signals` VALUES("296","2","reload",NULL,"1","2025-12-02 13:19:52","2025-12-02 13:19:50",NULL);
INSERT INTO `tv_reload_signals` VALUES("297","3","reload",NULL,"1","2025-12-02 13:19:51","2025-12-02 13:19:50",NULL);
INSERT INTO `tv_reload_signals` VALUES("298","4","reload",NULL,"1","2025-12-02 13:19:50","2025-12-02 13:19:50",NULL);
INSERT INTO `tv_reload_signals` VALUES("299","5","reload",NULL,"1","2025-12-18 11:40:35","2025-12-02 13:19:50",NULL);
INSERT INTO `tv_reload_signals` VALUES("300","6","reload",NULL,"1","2025-12-02 14:05:40","2025-12-02 13:19:50",NULL);
INSERT INTO `tv_reload_signals` VALUES("301","7","reload",NULL,"0",NULL,"2025-12-02 13:19:50",NULL);
INSERT INTO `tv_reload_signals` VALUES("302","1","reload",NULL,"1","2025-12-02 13:22:03","2025-12-02 13:22:00",NULL);
INSERT INTO `tv_reload_signals` VALUES("303","2","reload",NULL,"1","2025-12-02 13:22:03","2025-12-02 13:22:00",NULL);
INSERT INTO `tv_reload_signals` VALUES("304","3","reload",NULL,"1","2025-12-02 13:22:01","2025-12-02 13:22:00",NULL);
INSERT INTO `tv_reload_signals` VALUES("305","4","reload",NULL,"1","2025-12-02 13:22:01","2025-12-02 13:22:00",NULL);
INSERT INTO `tv_reload_signals` VALUES("306","5","reload",NULL,"1","2025-12-18 11:40:34","2025-12-02 13:22:00",NULL);
INSERT INTO `tv_reload_signals` VALUES("307","6","reload",NULL,"1","2025-12-02 14:05:40","2025-12-02 13:22:00",NULL);
INSERT INTO `tv_reload_signals` VALUES("308","7","reload",NULL,"0",NULL,"2025-12-02 13:22:00",NULL);
INSERT INTO `tv_reload_signals` VALUES("309","1","reload",NULL,"1","2025-12-02 13:23:28","2025-12-02 13:23:25",NULL);
INSERT INTO `tv_reload_signals` VALUES("310","2","reload",NULL,"1","2025-12-02 13:23:29","2025-12-02 13:23:25",NULL);
INSERT INTO `tv_reload_signals` VALUES("311","3","reload",NULL,"1","2025-12-02 13:23:26","2025-12-02 13:23:25",NULL);
INSERT INTO `tv_reload_signals` VALUES("312","4","reload",NULL,"1","2025-12-02 13:23:26","2025-12-02 13:23:25",NULL);
INSERT INTO `tv_reload_signals` VALUES("313","5","reload",NULL,"1","2025-12-18 11:40:32","2025-12-02 13:23:25",NULL);
INSERT INTO `tv_reload_signals` VALUES("314","6","reload",NULL,"1","2025-12-02 14:05:39","2025-12-02 13:23:25",NULL);
INSERT INTO `tv_reload_signals` VALUES("315","7","reload",NULL,"0",NULL,"2025-12-02 13:23:25",NULL);
INSERT INTO `tv_reload_signals` VALUES("316","1","reload",NULL,"1","2025-12-02 14:03:18","2025-12-02 14:03:16",NULL);
INSERT INTO `tv_reload_signals` VALUES("317","2","reload",NULL,"1","2025-12-02 14:03:19","2025-12-02 14:03:16",NULL);
INSERT INTO `tv_reload_signals` VALUES("318","3","reload",NULL,"1","2025-12-02 15:13:53","2025-12-02 14:03:16",NULL);
INSERT INTO `tv_reload_signals` VALUES("319","4","reload",NULL,"1","2025-12-02 15:27:14","2025-12-02 14:03:16",NULL);
INSERT INTO `tv_reload_signals` VALUES("320","5","reload",NULL,"1","2025-12-18 11:40:32","2025-12-02 14:03:16",NULL);
INSERT INTO `tv_reload_signals` VALUES("321","6","reload",NULL,"1","2025-12-02 14:05:38","2025-12-02 14:03:16",NULL);
INSERT INTO `tv_reload_signals` VALUES("322","7","reload",NULL,"0",NULL,"2025-12-02 14:03:16",NULL);
INSERT INTO `tv_reload_signals` VALUES("323","1","reload",NULL,"1","2025-12-02 14:04:09","2025-12-02 14:04:08",NULL);
INSERT INTO `tv_reload_signals` VALUES("324","2","reload",NULL,"1","2025-12-02 14:04:10","2025-12-02 14:04:08",NULL);
INSERT INTO `tv_reload_signals` VALUES("325","3","reload",NULL,"1","2025-12-02 15:13:51","2025-12-02 14:04:08",NULL);
INSERT INTO `tv_reload_signals` VALUES("326","4","reload",NULL,"1","2025-12-02 15:27:14","2025-12-02 14:04:08",NULL);
INSERT INTO `tv_reload_signals` VALUES("327","5","reload",NULL,"1","2025-12-18 11:40:31","2025-12-02 14:04:08",NULL);
INSERT INTO `tv_reload_signals` VALUES("328","6","reload",NULL,"1","2025-12-02 14:05:38","2025-12-02 14:04:08",NULL);
INSERT INTO `tv_reload_signals` VALUES("329","7","reload",NULL,"0",NULL,"2025-12-02 14:04:08",NULL);
INSERT INTO `tv_reload_signals` VALUES("330","1","reload",NULL,"1","2025-12-05 14:17:50","2025-12-03 08:10:40",NULL);
INSERT INTO `tv_reload_signals` VALUES("331","2","reload",NULL,"1","2025-12-03 08:10:43","2025-12-03 08:10:40",NULL);
INSERT INTO `tv_reload_signals` VALUES("332","3","reload",NULL,"1","2025-12-08 08:39:00","2025-12-03 08:10:40",NULL);
INSERT INTO `tv_reload_signals` VALUES("333","4","reload",NULL,"1","2025-12-03 20:22:46","2025-12-03 08:10:40",NULL);
INSERT INTO `tv_reload_signals` VALUES("334","5","reload",NULL,"1","2025-12-18 11:40:31","2025-12-03 08:10:40",NULL);
INSERT INTO `tv_reload_signals` VALUES("335","6","reload",NULL,"1","2025-12-03 09:27:31","2025-12-03 08:10:40",NULL);
INSERT INTO `tv_reload_signals` VALUES("336","7","reload",NULL,"0",NULL,"2025-12-03 08:10:40",NULL);
INSERT INTO `tv_reload_signals` VALUES("337","1","reload",NULL,"1","2025-12-05 14:17:49","2025-12-03 20:17:36",NULL);
INSERT INTO `tv_reload_signals` VALUES("338","2","reload",NULL,"1","2025-12-03 20:22:16","2025-12-03 20:17:36",NULL);
INSERT INTO `tv_reload_signals` VALUES("339","3","reload",NULL,"1","2025-12-08 08:38:59","2025-12-03 20:17:36",NULL);
INSERT INTO `tv_reload_signals` VALUES("340","4","reload",NULL,"1","2025-12-03 20:22:44","2025-12-03 20:17:36",NULL);
INSERT INTO `tv_reload_signals` VALUES("341","5","reload",NULL,"1","2025-12-18 11:40:30","2025-12-03 20:17:36",NULL);
INSERT INTO `tv_reload_signals` VALUES("342","6","reload",NULL,"1","2025-12-22 08:50:45","2025-12-03 20:17:36",NULL);
INSERT INTO `tv_reload_signals` VALUES("343","7","reload",NULL,"0",NULL,"2025-12-03 20:17:36",NULL);
INSERT INTO `tv_reload_signals` VALUES("344","1","reload",NULL,"1","2025-12-05 14:17:48","2025-12-03 20:17:53",NULL);
INSERT INTO `tv_reload_signals` VALUES("345","2","reload",NULL,"1","2025-12-03 20:22:14","2025-12-03 20:17:53",NULL);
INSERT INTO `tv_reload_signals` VALUES("346","3","reload",NULL,"1","2025-12-08 08:38:59","2025-12-03 20:17:53",NULL);
INSERT INTO `tv_reload_signals` VALUES("347","4","reload",NULL,"1","2025-12-03 20:22:44","2025-12-03 20:17:53",NULL);
INSERT INTO `tv_reload_signals` VALUES("348","5","reload",NULL,"1","2025-12-18 11:40:30","2025-12-03 20:17:53",NULL);
INSERT INTO `tv_reload_signals` VALUES("349","6","reload",NULL,"1","2025-12-22 08:50:44","2025-12-03 20:17:53",NULL);
INSERT INTO `tv_reload_signals` VALUES("350","7","reload",NULL,"0",NULL,"2025-12-03 20:17:53",NULL);
INSERT INTO `tv_reload_signals` VALUES("351","1","reload",NULL,"1","2025-12-05 14:17:48","2025-12-05 13:46:02",NULL);
INSERT INTO `tv_reload_signals` VALUES("352","2","reload",NULL,"1","2025-12-05 13:46:04","2025-12-05 13:46:02",NULL);
INSERT INTO `tv_reload_signals` VALUES("353","3","reload",NULL,"1","2025-12-08 08:38:58","2025-12-05 13:46:02",NULL);
INSERT INTO `tv_reload_signals` VALUES("354","4","reload",NULL,"1","2025-12-05 13:46:02","2025-12-05 13:46:02",NULL);
INSERT INTO `tv_reload_signals` VALUES("355","5","reload",NULL,"1","2025-12-18 11:40:30","2025-12-05 13:46:02",NULL);
INSERT INTO `tv_reload_signals` VALUES("356","6","reload",NULL,"1","2025-12-22 08:50:44","2025-12-05 13:46:02",NULL);
INSERT INTO `tv_reload_signals` VALUES("357","7","reload",NULL,"0",NULL,"2025-12-05 13:46:02",NULL);
INSERT INTO `tv_reload_signals` VALUES("358","1","reload",NULL,"1","2025-12-05 14:28:04","2025-12-05 14:28:01",NULL);
INSERT INTO `tv_reload_signals` VALUES("359","2","reload",NULL,"1","2025-12-05 14:28:03","2025-12-05 14:28:01",NULL);
INSERT INTO `tv_reload_signals` VALUES("360","3","reload",NULL,"1","2025-12-08 08:38:58","2025-12-05 14:28:01",NULL);
INSERT INTO `tv_reload_signals` VALUES("361","4","reload",NULL,"1","2025-12-05 14:28:02","2025-12-05 14:28:01",NULL);
INSERT INTO `tv_reload_signals` VALUES("362","5","reload",NULL,"1","2025-12-18 11:40:30","2025-12-05 14:28:01",NULL);
INSERT INTO `tv_reload_signals` VALUES("363","6","reload",NULL,"1","2025-12-22 08:50:44","2025-12-05 14:28:01",NULL);
INSERT INTO `tv_reload_signals` VALUES("364","7","reload",NULL,"0",NULL,"2025-12-05 14:28:01",NULL);
INSERT INTO `tv_reload_signals` VALUES("365","1","reload",NULL,"1","2025-12-08 10:51:43","2025-12-08 10:51:40",NULL);
INSERT INTO `tv_reload_signals` VALUES("366","2","reload",NULL,"1","2025-12-08 10:51:43","2025-12-08 10:51:40",NULL);
INSERT INTO `tv_reload_signals` VALUES("367","3","reload",NULL,"1","2025-12-08 10:51:40","2025-12-08 10:51:40",NULL);
INSERT INTO `tv_reload_signals` VALUES("368","4","reload",NULL,"1","2025-12-08 10:51:41","2025-12-08 10:51:40",NULL);
INSERT INTO `tv_reload_signals` VALUES("369","5","reload",NULL,"1","2025-12-18 11:40:28","2025-12-08 10:51:40",NULL);
INSERT INTO `tv_reload_signals` VALUES("370","6","reload",NULL,"1","2025-12-22 08:50:44","2025-12-08 10:51:40",NULL);
INSERT INTO `tv_reload_signals` VALUES("371","7","reload",NULL,"0",NULL,"2025-12-08 10:51:40",NULL);
INSERT INTO `tv_reload_signals` VALUES("372","1","reload",NULL,"1","2025-12-10 07:31:54","2025-12-10 06:50:57",NULL);
INSERT INTO `tv_reload_signals` VALUES("373","2","reload",NULL,"1","2025-12-19 06:56:28","2025-12-10 06:50:57",NULL);
INSERT INTO `tv_reload_signals` VALUES("374","3","reload",NULL,"1","2025-12-22 14:02:22","2025-12-10 06:50:57",NULL);
INSERT INTO `tv_reload_signals` VALUES("375","4","reload",NULL,"1","2025-12-10 07:28:32","2025-12-10 06:50:57",NULL);
INSERT INTO `tv_reload_signals` VALUES("376","5","reload",NULL,"1","2025-12-18 11:40:28","2025-12-10 06:50:57",NULL);
INSERT INTO `tv_reload_signals` VALUES("377","6","reload",NULL,"1","2025-12-22 08:50:43","2025-12-10 06:50:57",NULL);
INSERT INTO `tv_reload_signals` VALUES("378","7","reload",NULL,"0",NULL,"2025-12-10 06:50:57",NULL);
INSERT INTO `tv_reload_signals` VALUES("379","1","reload",NULL,"1","2025-12-10 07:56:16","2025-12-10 07:56:13",NULL);
INSERT INTO `tv_reload_signals` VALUES("380","2","reload",NULL,"1","2025-12-19 06:56:28","2025-12-10 07:56:13",NULL);
INSERT INTO `tv_reload_signals` VALUES("381","3","reload",NULL,"1","2025-12-22 14:02:21","2025-12-10 07:56:13",NULL);
INSERT INTO `tv_reload_signals` VALUES("382","4","reload",NULL,"1","2025-12-10 07:56:14","2025-12-10 07:56:13",NULL);
INSERT INTO `tv_reload_signals` VALUES("383","5","reload",NULL,"1","2025-12-18 11:40:27","2025-12-10 07:56:13",NULL);
INSERT INTO `tv_reload_signals` VALUES("384","6","reload",NULL,"1","2025-12-22 08:50:43","2025-12-10 07:56:13",NULL);
INSERT INTO `tv_reload_signals` VALUES("385","7","reload",NULL,"0",NULL,"2025-12-10 07:56:13",NULL);
INSERT INTO `tv_reload_signals` VALUES("386","1","reload",NULL,"1","2025-12-10 09:35:42","2025-12-10 09:35:40",NULL);
INSERT INTO `tv_reload_signals` VALUES("387","2","reload",NULL,"1","2025-12-19 06:56:27","2025-12-10 09:35:40",NULL);
INSERT INTO `tv_reload_signals` VALUES("388","3","reload",NULL,"1","2025-12-22 14:02:21","2025-12-10 09:35:40",NULL);
INSERT INTO `tv_reload_signals` VALUES("389","4","reload",NULL,"1","2025-12-10 09:35:43","2025-12-10 09:35:40",NULL);
INSERT INTO `tv_reload_signals` VALUES("390","5","reload",NULL,"1","2025-12-18 11:40:27","2025-12-10 09:35:40",NULL);
INSERT INTO `tv_reload_signals` VALUES("391","6","reload",NULL,"1","2025-12-22 08:50:43","2025-12-10 09:35:40",NULL);
INSERT INTO `tv_reload_signals` VALUES("392","7","reload",NULL,"0",NULL,"2025-12-10 09:35:40",NULL);
INSERT INTO `tv_reload_signals` VALUES("393","1","reload",NULL,"1","2025-12-19 06:53:45","2025-12-19 06:10:26",NULL);
INSERT INTO `tv_reload_signals` VALUES("394","2","reload",NULL,"1","2025-12-19 06:56:27","2025-12-19 06:10:26",NULL);
INSERT INTO `tv_reload_signals` VALUES("395","3","reload",NULL,"1","2025-12-22 14:02:21","2025-12-19 06:10:26",NULL);
INSERT INTO `tv_reload_signals` VALUES("396","4","reload",NULL,"1","2025-12-19 13:23:49","2025-12-19 06:10:26",NULL);
INSERT INTO `tv_reload_signals` VALUES("397","5","reload",NULL,"1","2025-12-24 14:24:45","2025-12-19 06:10:26",NULL);
INSERT INTO `tv_reload_signals` VALUES("398","6","reload",NULL,"1","2025-12-22 08:50:43","2025-12-19 06:10:26",NULL);
INSERT INTO `tv_reload_signals` VALUES("399","7","reload",NULL,"0",NULL,"2025-12-19 06:10:26",NULL);
INSERT INTO `tv_reload_signals` VALUES("400","1","reload",NULL,"1","2025-12-19 13:41:17","2025-12-19 13:41:14",NULL);
INSERT INTO `tv_reload_signals` VALUES("401","2","reload",NULL,"1","2025-12-19 13:41:17","2025-12-19 13:41:14",NULL);
INSERT INTO `tv_reload_signals` VALUES("402","3","reload",NULL,"1","2025-12-22 14:02:20","2025-12-19 13:41:14",NULL);
INSERT INTO `tv_reload_signals` VALUES("403","4","reload",NULL,"1","2025-12-19 13:41:17","2025-12-19 13:41:14",NULL);
INSERT INTO `tv_reload_signals` VALUES("404","5","reload",NULL,"1","2025-12-24 14:24:45","2025-12-19 13:41:14",NULL);
INSERT INTO `tv_reload_signals` VALUES("405","6","reload",NULL,"1","2025-12-22 08:50:42","2025-12-19 13:41:14",NULL);
INSERT INTO `tv_reload_signals` VALUES("406","7","reload",NULL,"0",NULL,"2025-12-19 13:41:14",NULL);
INSERT INTO `tv_reload_signals` VALUES("407","1","reload",NULL,"1","2025-12-19 13:41:58","2025-12-19 13:41:57",NULL);
INSERT INTO `tv_reload_signals` VALUES("408","2","reload",NULL,"1","2025-12-19 13:41:58","2025-12-19 13:41:57",NULL);
INSERT INTO `tv_reload_signals` VALUES("409","3","reload",NULL,"1","2025-12-22 14:02:20","2025-12-19 13:41:57",NULL);
INSERT INTO `tv_reload_signals` VALUES("410","4","reload",NULL,"1","2025-12-19 13:41:58","2025-12-19 13:41:57",NULL);
INSERT INTO `tv_reload_signals` VALUES("411","5","reload",NULL,"1","2025-12-24 14:24:45","2025-12-19 13:41:57",NULL);
INSERT INTO `tv_reload_signals` VALUES("412","6","reload",NULL,"1","2025-12-22 08:50:42","2025-12-19 13:41:57",NULL);
INSERT INTO `tv_reload_signals` VALUES("413","7","reload",NULL,"0",NULL,"2025-12-19 13:41:57",NULL);
INSERT INTO `tv_reload_signals` VALUES("414","1","reload",NULL,"1","2025-12-19 13:43:08","2025-12-19 13:43:07",NULL);
INSERT INTO `tv_reload_signals` VALUES("415","2","reload",NULL,"1","2025-12-19 13:43:08","2025-12-19 13:43:07",NULL);
INSERT INTO `tv_reload_signals` VALUES("416","3","reload",NULL,"1","2025-12-22 14:02:20","2025-12-19 13:43:07",NULL);
INSERT INTO `tv_reload_signals` VALUES("417","4","reload",NULL,"1","2025-12-19 13:43:08","2025-12-19 13:43:07",NULL);
INSERT INTO `tv_reload_signals` VALUES("418","5","reload",NULL,"1","2025-12-24 14:24:45","2025-12-19 13:43:07",NULL);
INSERT INTO `tv_reload_signals` VALUES("419","6","reload",NULL,"1","2025-12-22 08:50:41","2025-12-19 13:43:07",NULL);
INSERT INTO `tv_reload_signals` VALUES("420","7","reload",NULL,"0",NULL,"2025-12-19 13:43:07",NULL);
INSERT INTO `tv_reload_signals` VALUES("421","1","reload",NULL,"1","2025-12-19 13:43:24","2025-12-19 13:43:23",NULL);
INSERT INTO `tv_reload_signals` VALUES("422","2","reload",NULL,"1","2025-12-19 13:43:23","2025-12-19 13:43:23",NULL);
INSERT INTO `tv_reload_signals` VALUES("423","3","reload",NULL,"1","2025-12-22 14:02:19","2025-12-19 13:43:23",NULL);
INSERT INTO `tv_reload_signals` VALUES("424","4","reload",NULL,"1","2025-12-19 13:43:24","2025-12-19 13:43:23",NULL);
INSERT INTO `tv_reload_signals` VALUES("425","5","reload",NULL,"1","2025-12-24 14:24:44","2025-12-19 13:43:23",NULL);
INSERT INTO `tv_reload_signals` VALUES("426","6","reload",NULL,"1","2025-12-22 08:50:41","2025-12-19 13:43:23",NULL);
INSERT INTO `tv_reload_signals` VALUES("427","7","reload",NULL,"0",NULL,"2025-12-19 13:43:23",NULL);
INSERT INTO `tv_reload_signals` VALUES("428","1","reload",NULL,"1","2025-12-19 15:15:43","2025-12-19 15:15:41",NULL);
INSERT INTO `tv_reload_signals` VALUES("429","2","reload",NULL,"1","2025-12-19 15:15:44","2025-12-19 15:15:41",NULL);
INSERT INTO `tv_reload_signals` VALUES("430","3","reload",NULL,"1","2025-12-22 14:02:19","2025-12-19 15:15:41",NULL);
INSERT INTO `tv_reload_signals` VALUES("431","4","reload",NULL,"1","2025-12-19 15:15:44","2025-12-19 15:15:41",NULL);
INSERT INTO `tv_reload_signals` VALUES("432","5","reload",NULL,"1","2025-12-24 14:24:44","2025-12-19 15:15:41",NULL);
INSERT INTO `tv_reload_signals` VALUES("433","6","reload",NULL,"1","2025-12-22 08:50:41","2025-12-19 15:15:41",NULL);
INSERT INTO `tv_reload_signals` VALUES("434","7","reload",NULL,"0",NULL,"2025-12-19 15:15:41",NULL);
INSERT INTO `tv_reload_signals` VALUES("435","1","reload",NULL,"1","2025-12-19 17:07:52","2025-12-19 17:07:49",NULL);
INSERT INTO `tv_reload_signals` VALUES("436","2","reload",NULL,"1","2025-12-19 17:07:51","2025-12-19 17:07:49",NULL);
INSERT INTO `tv_reload_signals` VALUES("437","3","reload",NULL,"1","2025-12-22 14:02:18","2025-12-19 17:07:49",NULL);
INSERT INTO `tv_reload_signals` VALUES("438","4","reload",NULL,"1","2025-12-19 17:07:51","2025-12-19 17:07:49",NULL);
INSERT INTO `tv_reload_signals` VALUES("439","5","reload",NULL,"1","2025-12-24 14:24:43","2025-12-19 17:07:49",NULL);
INSERT INTO `tv_reload_signals` VALUES("440","6","reload",NULL,"1","2025-12-22 08:50:41","2025-12-19 17:07:49",NULL);
INSERT INTO `tv_reload_signals` VALUES("441","7","reload",NULL,"0",NULL,"2025-12-19 17:07:49",NULL);
INSERT INTO `tv_reload_signals` VALUES("442","1","reload",NULL,"1","2025-12-19 17:07:58","2025-12-19 17:07:55",NULL);
INSERT INTO `tv_reload_signals` VALUES("443","2","reload",NULL,"1","2025-12-19 17:07:57","2025-12-19 17:07:55",NULL);
INSERT INTO `tv_reload_signals` VALUES("444","3","reload",NULL,"1","2025-12-22 14:02:18","2025-12-19 17:07:55",NULL);
INSERT INTO `tv_reload_signals` VALUES("445","4","reload",NULL,"1","2025-12-19 17:07:56","2025-12-19 17:07:55",NULL);
INSERT INTO `tv_reload_signals` VALUES("446","5","reload",NULL,"1","2025-12-24 14:24:43","2025-12-19 17:07:55",NULL);
INSERT INTO `tv_reload_signals` VALUES("447","6","reload",NULL,"1","2025-12-22 08:50:40","2025-12-19 17:07:55",NULL);
INSERT INTO `tv_reload_signals` VALUES("448","7","reload",NULL,"0",NULL,"2025-12-19 17:07:55",NULL);
INSERT INTO `tv_reload_signals` VALUES("449","1","reload",NULL,"1","2025-12-21 20:43:21","2025-12-21 20:43:19",NULL);
INSERT INTO `tv_reload_signals` VALUES("450","2","reload",NULL,"1","2025-12-22 14:04:04","2025-12-21 20:43:19",NULL);
INSERT INTO `tv_reload_signals` VALUES("451","3","reload",NULL,"1","2025-12-22 14:02:17","2025-12-21 20:43:19",NULL);
INSERT INTO `tv_reload_signals` VALUES("452","4","reload",NULL,"1","2025-12-22 14:01:07","2025-12-21 20:43:19",NULL);
INSERT INTO `tv_reload_signals` VALUES("453","5","reload",NULL,"1","2025-12-24 14:24:42","2025-12-21 20:43:19",NULL);
INSERT INTO `tv_reload_signals` VALUES("454","6","reload",NULL,"1","2025-12-22 08:50:40","2025-12-21 20:43:19",NULL);
INSERT INTO `tv_reload_signals` VALUES("455","7","reload",NULL,"0",NULL,"2025-12-21 20:43:19",NULL);
INSERT INTO `tv_reload_signals` VALUES("456","1","reload",NULL,"1","2025-12-22 08:10:04","2025-12-22 08:07:56",NULL);
INSERT INTO `tv_reload_signals` VALUES("457","2","reload",NULL,"1","2025-12-22 14:04:03","2025-12-22 08:07:56",NULL);
INSERT INTO `tv_reload_signals` VALUES("458","3","reload",NULL,"1","2025-12-22 14:02:17","2025-12-22 08:07:56",NULL);
INSERT INTO `tv_reload_signals` VALUES("459","4","reload",NULL,"1","2025-12-22 14:01:06","2025-12-22 08:07:56",NULL);
INSERT INTO `tv_reload_signals` VALUES("460","5","reload",NULL,"1","2025-12-24 14:24:42","2025-12-22 08:07:56",NULL);
INSERT INTO `tv_reload_signals` VALUES("461","6","reload",NULL,"1","2025-12-22 08:50:39","2025-12-22 08:07:56",NULL);
INSERT INTO `tv_reload_signals` VALUES("462","7","reload",NULL,"0",NULL,"2025-12-22 08:07:56",NULL);
INSERT INTO `tv_reload_signals` VALUES("463","1","reload",NULL,"1","2025-12-22 08:45:53","2025-12-22 08:45:53",NULL);
INSERT INTO `tv_reload_signals` VALUES("464","2","reload",NULL,"1","2025-12-22 14:04:03","2025-12-22 08:45:53",NULL);
INSERT INTO `tv_reload_signals` VALUES("465","3","reload",NULL,"1","2025-12-22 14:02:16","2025-12-22 08:45:53",NULL);
INSERT INTO `tv_reload_signals` VALUES("466","4","reload",NULL,"1","2025-12-22 14:01:06","2025-12-22 08:45:53",NULL);
INSERT INTO `tv_reload_signals` VALUES("467","5","reload",NULL,"1","2025-12-24 14:24:41","2025-12-22 08:45:53",NULL);
INSERT INTO `tv_reload_signals` VALUES("468","6","reload",NULL,"1","2025-12-22 08:50:39","2025-12-22 08:45:53",NULL);
INSERT INTO `tv_reload_signals` VALUES("469","7","reload",NULL,"0",NULL,"2025-12-22 08:45:53",NULL);
INSERT INTO `tv_reload_signals` VALUES("470","1","reload",NULL,"1","2025-12-22 08:56:51","2025-12-22 08:56:49",NULL);
INSERT INTO `tv_reload_signals` VALUES("471","2","reload",NULL,"1","2025-12-22 14:04:03","2025-12-22 08:56:49",NULL);
INSERT INTO `tv_reload_signals` VALUES("472","3","reload",NULL,"1","2025-12-22 14:02:16","2025-12-22 08:56:49",NULL);
INSERT INTO `tv_reload_signals` VALUES("473","4","reload",NULL,"1","2025-12-22 14:01:05","2025-12-22 08:56:49",NULL);
INSERT INTO `tv_reload_signals` VALUES("474","5","reload",NULL,"1","2025-12-24 14:24:41","2025-12-22 08:56:49",NULL);
INSERT INTO `tv_reload_signals` VALUES("475","6","reload",NULL,"1","2025-12-22 08:56:52","2025-12-22 08:56:49",NULL);
INSERT INTO `tv_reload_signals` VALUES("476","7","reload",NULL,"0",NULL,"2025-12-22 08:56:49",NULL);
INSERT INTO `tv_reload_signals` VALUES("477","1","reload",NULL,"1","2025-12-22 08:57:02","2025-12-22 08:57:02",NULL);
INSERT INTO `tv_reload_signals` VALUES("478","2","reload",NULL,"1","2025-12-22 14:04:03","2025-12-22 08:57:02",NULL);
INSERT INTO `tv_reload_signals` VALUES("479","3","reload",NULL,"1","2025-12-22 14:02:14","2025-12-22 08:57:02",NULL);
INSERT INTO `tv_reload_signals` VALUES("480","4","reload",NULL,"1","2025-12-22 14:01:05","2025-12-22 08:57:02",NULL);
INSERT INTO `tv_reload_signals` VALUES("481","5","reload",NULL,"1","2025-12-24 14:24:40","2025-12-22 08:57:02",NULL);
INSERT INTO `tv_reload_signals` VALUES("482","6","reload",NULL,"1","2025-12-22 08:57:04","2025-12-22 08:57:02",NULL);
INSERT INTO `tv_reload_signals` VALUES("483","7","reload",NULL,"0",NULL,"2025-12-22 08:57:02",NULL);
INSERT INTO `tv_reload_signals` VALUES("484","1","reload",NULL,"1","2025-12-22 09:00:31","2025-12-22 09:00:28",NULL);
INSERT INTO `tv_reload_signals` VALUES("485","2","reload",NULL,"1","2025-12-22 14:04:02","2025-12-22 09:00:28",NULL);
INSERT INTO `tv_reload_signals` VALUES("486","3","reload",NULL,"1","2025-12-22 14:02:14","2025-12-22 09:00:28",NULL);
INSERT INTO `tv_reload_signals` VALUES("487","4","reload",NULL,"1","2025-12-22 14:01:05","2025-12-22 09:00:28",NULL);
INSERT INTO `tv_reload_signals` VALUES("488","5","reload",NULL,"1","2025-12-24 14:24:40","2025-12-22 09:00:28",NULL);
INSERT INTO `tv_reload_signals` VALUES("489","6","reload",NULL,"1","2025-12-22 09:00:31","2025-12-22 09:00:28",NULL);
INSERT INTO `tv_reload_signals` VALUES("490","7","reload",NULL,"0",NULL,"2025-12-22 09:00:28",NULL);
INSERT INTO `tv_reload_signals` VALUES("491","1","reload",NULL,"1","2025-12-22 09:02:17","2025-12-22 09:02:15",NULL);
INSERT INTO `tv_reload_signals` VALUES("492","2","reload",NULL,"1","2025-12-22 14:04:02","2025-12-22 09:02:15",NULL);
INSERT INTO `tv_reload_signals` VALUES("493","3","reload",NULL,"1","2025-12-22 14:02:14","2025-12-22 09:02:15",NULL);
INSERT INTO `tv_reload_signals` VALUES("494","4","reload",NULL,"1","2025-12-22 14:01:05","2025-12-22 09:02:15",NULL);
INSERT INTO `tv_reload_signals` VALUES("495","5","reload",NULL,"1","2025-12-24 14:24:39","2025-12-22 09:02:15",NULL);
INSERT INTO `tv_reload_signals` VALUES("496","6","reload",NULL,"1","2025-12-22 09:02:28","2025-12-22 09:02:15",NULL);
INSERT INTO `tv_reload_signals` VALUES("497","7","reload",NULL,"0",NULL,"2025-12-22 09:02:15",NULL);
INSERT INTO `tv_reload_signals` VALUES("498","1","reload",NULL,"1","2025-12-22 10:55:33","2025-12-22 10:55:31",NULL);
INSERT INTO `tv_reload_signals` VALUES("499","2","reload",NULL,"1","2025-12-22 14:04:01","2025-12-22 10:55:31",NULL);
INSERT INTO `tv_reload_signals` VALUES("500","3","reload",NULL,"1","2025-12-22 14:02:14","2025-12-22 10:55:31",NULL);
INSERT INTO `tv_reload_signals` VALUES("501","4","reload",NULL,"1","2025-12-22 14:01:04","2025-12-22 10:55:31",NULL);
INSERT INTO `tv_reload_signals` VALUES("502","5","reload",NULL,"1","2025-12-24 14:24:39","2025-12-22 10:55:31",NULL);
INSERT INTO `tv_reload_signals` VALUES("503","6","reload",NULL,"1","2025-12-22 11:08:21","2025-12-22 10:55:31",NULL);
INSERT INTO `tv_reload_signals` VALUES("504","7","reload",NULL,"0",NULL,"2025-12-22 10:55:31",NULL);
INSERT INTO `tv_reload_signals` VALUES("505","1","reload",NULL,"1","2025-12-22 11:30:03","2025-12-22 11:29:54",NULL);
INSERT INTO `tv_reload_signals` VALUES("506","2","reload",NULL,"1","2025-12-22 14:04:01","2025-12-22 11:29:54",NULL);
INSERT INTO `tv_reload_signals` VALUES("507","3","reload",NULL,"1","2025-12-22 14:02:13","2025-12-22 11:29:54",NULL);
INSERT INTO `tv_reload_signals` VALUES("508","4","reload",NULL,"1","2025-12-22 14:01:04","2025-12-22 11:29:54",NULL);
INSERT INTO `tv_reload_signals` VALUES("509","5","reload",NULL,"1","2025-12-24 14:24:38","2025-12-22 11:29:54",NULL);
INSERT INTO `tv_reload_signals` VALUES("510","6","reload",NULL,"1","2025-12-22 11:30:09","2025-12-22 11:29:54",NULL);
INSERT INTO `tv_reload_signals` VALUES("511","7","reload",NULL,"0",NULL,"2025-12-22 11:29:54",NULL);
INSERT INTO `tv_reload_signals` VALUES("512","1","reload",NULL,"1","2025-12-22 14:01:45","2025-12-22 14:01:43",NULL);
INSERT INTO `tv_reload_signals` VALUES("513","2","reload",NULL,"1","2025-12-22 14:04:00","2025-12-22 14:01:43",NULL);
INSERT INTO `tv_reload_signals` VALUES("514","3","reload",NULL,"1","2025-12-22 14:02:13","2025-12-22 14:01:43",NULL);
INSERT INTO `tv_reload_signals` VALUES("515","4","reload",NULL,"1","2025-12-22 14:01:44","2025-12-22 14:01:43",NULL);
INSERT INTO `tv_reload_signals` VALUES("516","5","reload",NULL,"1","2025-12-24 14:24:38","2025-12-22 14:01:43",NULL);
INSERT INTO `tv_reload_signals` VALUES("517","6","reload",NULL,"1","2025-12-23 08:10:56","2025-12-22 14:01:43",NULL);
INSERT INTO `tv_reload_signals` VALUES("518","7","reload",NULL,"0",NULL,"2025-12-22 14:01:43",NULL);
INSERT INTO `tv_reload_signals` VALUES("519","1","reload",NULL,"1","2025-12-22 14:30:04","2025-12-22 14:30:03",NULL);
INSERT INTO `tv_reload_signals` VALUES("520","2","reload",NULL,"1","2025-12-22 14:30:04","2025-12-22 14:30:03",NULL);
INSERT INTO `tv_reload_signals` VALUES("521","3","reload",NULL,"1","2025-12-22 14:30:04","2025-12-22 14:30:03",NULL);
INSERT INTO `tv_reload_signals` VALUES("522","4","reload",NULL,"1","2025-12-22 14:30:03","2025-12-22 14:30:03",NULL);
INSERT INTO `tv_reload_signals` VALUES("523","5","reload",NULL,"1","2025-12-24 14:24:37","2025-12-22 14:30:03",NULL);
INSERT INTO `tv_reload_signals` VALUES("524","6","reload",NULL,"1","2025-12-23 08:10:56","2025-12-22 14:30:03",NULL);
INSERT INTO `tv_reload_signals` VALUES("525","7","reload",NULL,"0",NULL,"2025-12-22 14:30:03",NULL);
INSERT INTO `tv_reload_signals` VALUES("526","1","reload",NULL,"1","2025-12-24 14:17:37","2025-12-24 14:17:36",NULL);
INSERT INTO `tv_reload_signals` VALUES("527","2","reload",NULL,"1","2025-12-30 16:06:12","2025-12-24 14:17:36",NULL);
INSERT INTO `tv_reload_signals` VALUES("528","3","reload",NULL,"1","2025-12-30 15:56:54","2025-12-24 14:17:36",NULL);
INSERT INTO `tv_reload_signals` VALUES("529","4","reload",NULL,"1","2025-12-27 14:59:12","2025-12-24 14:17:36",NULL);
INSERT INTO `tv_reload_signals` VALUES("530","5","reload",NULL,"1","2025-12-24 14:24:37","2025-12-24 14:17:36",NULL);
INSERT INTO `tv_reload_signals` VALUES("531","6","reload",NULL,"0",NULL,"2025-12-24 14:17:36",NULL);
INSERT INTO `tv_reload_signals` VALUES("532","7","reload",NULL,"0",NULL,"2025-12-24 14:17:36",NULL);
INSERT INTO `tv_reload_signals` VALUES("533","1","reload",NULL,"1","2025-12-24 14:17:48","2025-12-24 14:17:46",NULL);
INSERT INTO `tv_reload_signals` VALUES("534","2","reload",NULL,"1","2025-12-30 16:06:11","2025-12-24 14:17:46",NULL);
INSERT INTO `tv_reload_signals` VALUES("535","3","reload",NULL,"1","2025-12-30 15:56:53","2025-12-24 14:17:46",NULL);
INSERT INTO `tv_reload_signals` VALUES("536","4","reload",NULL,"1","2025-12-27 14:59:11","2025-12-24 14:17:46",NULL);
INSERT INTO `tv_reload_signals` VALUES("537","5","reload",NULL,"1","2025-12-24 14:24:37","2025-12-24 14:17:46",NULL);
INSERT INTO `tv_reload_signals` VALUES("538","6","reload",NULL,"0",NULL,"2025-12-24 14:17:46",NULL);
INSERT INTO `tv_reload_signals` VALUES("539","7","reload",NULL,"0",NULL,"2025-12-24 14:17:46",NULL);
INSERT INTO `tv_reload_signals` VALUES("540","1","reload",NULL,"1","2025-12-24 14:18:18","2025-12-24 14:18:17",NULL);
INSERT INTO `tv_reload_signals` VALUES("541","2","reload",NULL,"1","2025-12-30 16:06:11","2025-12-24 14:18:17",NULL);
INSERT INTO `tv_reload_signals` VALUES("542","3","reload",NULL,"1","2025-12-30 15:56:53","2025-12-24 14:18:17",NULL);
INSERT INTO `tv_reload_signals` VALUES("543","4","reload",NULL,"1","2025-12-27 14:59:11","2025-12-24 14:18:17",NULL);
INSERT INTO `tv_reload_signals` VALUES("544","5","reload",NULL,"1","2025-12-24 14:24:37","2025-12-24 14:18:17",NULL);
INSERT INTO `tv_reload_signals` VALUES("545","6","reload",NULL,"0",NULL,"2025-12-24 14:18:17",NULL);
INSERT INTO `tv_reload_signals` VALUES("546","7","reload",NULL,"0",NULL,"2025-12-24 14:18:17",NULL);
INSERT INTO `tv_reload_signals` VALUES("547","1","reload",NULL,"1","2025-12-24 14:28:45","2025-12-24 14:28:45",NULL);
INSERT INTO `tv_reload_signals` VALUES("548","2","reload",NULL,"1","2025-12-30 16:06:10","2025-12-24 14:28:45",NULL);
INSERT INTO `tv_reload_signals` VALUES("549","3","reload",NULL,"1","2025-12-30 15:56:52","2025-12-24 14:28:45",NULL);
INSERT INTO `tv_reload_signals` VALUES("550","4","reload",NULL,"1","2025-12-27 14:59:11","2025-12-24 14:28:45",NULL);
INSERT INTO `tv_reload_signals` VALUES("551","5","reload",NULL,"1","2025-12-24 14:28:46","2025-12-24 14:28:45",NULL);
INSERT INTO `tv_reload_signals` VALUES("552","6","reload",NULL,"0",NULL,"2025-12-24 14:28:45",NULL);
INSERT INTO `tv_reload_signals` VALUES("553","7","reload",NULL,"0",NULL,"2025-12-24 14:28:45",NULL);
INSERT INTO `tv_reload_signals` VALUES("554","1","reload",NULL,"1","2025-12-27 14:44:06","2025-12-27 14:34:16",NULL);
INSERT INTO `tv_reload_signals` VALUES("555","2","reload",NULL,"1","2025-12-30 16:06:10","2025-12-27 14:34:16",NULL);
INSERT INTO `tv_reload_signals` VALUES("556","3","reload",NULL,"1","2025-12-30 15:56:52","2025-12-27 14:34:16",NULL);
INSERT INTO `tv_reload_signals` VALUES("557","4","reload",NULL,"1","2025-12-27 14:59:11","2025-12-27 14:34:16",NULL);
INSERT INTO `tv_reload_signals` VALUES("558","5","reload",NULL,"1","2025-12-30 15:39:12","2025-12-27 14:34:16",NULL);
INSERT INTO `tv_reload_signals` VALUES("559","6","reload",NULL,"0",NULL,"2025-12-27 14:34:16",NULL);
INSERT INTO `tv_reload_signals` VALUES("560","7","reload",NULL,"0",NULL,"2025-12-27 14:34:16",NULL);
INSERT INTO `tv_reload_signals` VALUES("561","1","reload",NULL,"1","2025-12-28 08:10:56","2025-12-28 08:09:49",NULL);
INSERT INTO `tv_reload_signals` VALUES("562","2","reload",NULL,"1","2025-12-30 16:06:10","2025-12-28 08:09:49",NULL);
INSERT INTO `tv_reload_signals` VALUES("563","3","reload",NULL,"1","2025-12-30 15:56:52","2025-12-28 08:09:49",NULL);
INSERT INTO `tv_reload_signals` VALUES("564","4","reload",NULL,"1","2025-12-28 08:09:52","2025-12-28 08:09:49",NULL);
INSERT INTO `tv_reload_signals` VALUES("565","5","reload",NULL,"1","2025-12-30 15:39:12","2025-12-28 08:09:49",NULL);
INSERT INTO `tv_reload_signals` VALUES("566","6","reload",NULL,"0",NULL,"2025-12-28 08:09:49",NULL);
INSERT INTO `tv_reload_signals` VALUES("567","7","reload",NULL,"0",NULL,"2025-12-28 08:09:49",NULL);
INSERT INTO `tv_reload_signals` VALUES("568","1","reload",NULL,"1","2025-12-29 10:58:33","2025-12-29 10:58:30",NULL);
INSERT INTO `tv_reload_signals` VALUES("569","2","reload",NULL,"1","2025-12-30 16:06:10","2025-12-29 10:58:30",NULL);
INSERT INTO `tv_reload_signals` VALUES("570","3","reload",NULL,"1","2025-12-30 15:56:51","2025-12-29 10:58:30",NULL);
INSERT INTO `tv_reload_signals` VALUES("571","4","reload",NULL,"1","2025-12-30 15:38:22","2025-12-29 10:58:30",NULL);
INSERT INTO `tv_reload_signals` VALUES("572","5","reload",NULL,"1","2025-12-30 15:39:11","2025-12-29 10:58:30",NULL);
INSERT INTO `tv_reload_signals` VALUES("573","6","reload",NULL,"0",NULL,"2025-12-29 10:58:30",NULL);
INSERT INTO `tv_reload_signals` VALUES("574","7","reload",NULL,"0",NULL,"2025-12-29 10:58:30",NULL);
INSERT INTO `tv_reload_signals` VALUES("575","1","reload",NULL,"1","2025-12-29 11:06:39","2025-12-29 11:06:36",NULL);
INSERT INTO `tv_reload_signals` VALUES("576","2","reload",NULL,"1","2025-12-30 16:06:09","2025-12-29 11:06:36",NULL);
INSERT INTO `tv_reload_signals` VALUES("577","3","reload",NULL,"1","2025-12-30 15:56:50","2025-12-29 11:06:36",NULL);
INSERT INTO `tv_reload_signals` VALUES("578","4","reload",NULL,"1","2025-12-30 15:38:22","2025-12-29 11:06:36",NULL);
INSERT INTO `tv_reload_signals` VALUES("579","5","reload",NULL,"1","2025-12-30 15:39:11","2025-12-29 11:06:36",NULL);
INSERT INTO `tv_reload_signals` VALUES("580","6","reload",NULL,"0",NULL,"2025-12-29 11:06:36",NULL);
INSERT INTO `tv_reload_signals` VALUES("581","7","reload",NULL,"0",NULL,"2025-12-29 11:06:36",NULL);
INSERT INTO `tv_reload_signals` VALUES("582","1","reload",NULL,"1","2025-12-29 11:07:00","2025-12-29 11:06:58",NULL);
INSERT INTO `tv_reload_signals` VALUES("583","2","reload",NULL,"1","2025-12-30 16:06:09","2025-12-29 11:06:58",NULL);
INSERT INTO `tv_reload_signals` VALUES("584","3","reload",NULL,"1","2025-12-30 15:56:50","2025-12-29 11:06:58",NULL);
INSERT INTO `tv_reload_signals` VALUES("585","4","reload",NULL,"1","2025-12-30 15:38:21","2025-12-29 11:06:58",NULL);
INSERT INTO `tv_reload_signals` VALUES("586","5","reload",NULL,"1","2025-12-30 15:39:10","2025-12-29 11:06:58",NULL);
INSERT INTO `tv_reload_signals` VALUES("587","6","reload",NULL,"0",NULL,"2025-12-29 11:06:58",NULL);
INSERT INTO `tv_reload_signals` VALUES("588","7","reload",NULL,"0",NULL,"2025-12-29 11:06:58",NULL);
INSERT INTO `tv_reload_signals` VALUES("589","1","reload",NULL,"1","2025-12-29 11:07:19","2025-12-29 11:07:18",NULL);
INSERT INTO `tv_reload_signals` VALUES("590","2","reload",NULL,"1","2025-12-30 16:06:08","2025-12-29 11:07:18",NULL);
INSERT INTO `tv_reload_signals` VALUES("591","3","reload",NULL,"1","2025-12-30 15:56:49","2025-12-29 11:07:18",NULL);
INSERT INTO `tv_reload_signals` VALUES("592","4","reload",NULL,"1","2025-12-30 15:38:21","2025-12-29 11:07:18",NULL);
INSERT INTO `tv_reload_signals` VALUES("593","5","reload",NULL,"1","2025-12-30 15:39:10","2025-12-29 11:07:18",NULL);
INSERT INTO `tv_reload_signals` VALUES("594","6","reload",NULL,"0",NULL,"2025-12-29 11:07:18",NULL);
INSERT INTO `tv_reload_signals` VALUES("595","7","reload",NULL,"0",NULL,"2025-12-29 11:07:18",NULL);
INSERT INTO `tv_reload_signals` VALUES("596","1","reload",NULL,"1","2025-12-29 13:26:28","2025-12-29 11:14:04",NULL);
INSERT INTO `tv_reload_signals` VALUES("597","2","reload",NULL,"1","2025-12-30 16:06:07","2025-12-29 11:14:04",NULL);
INSERT INTO `tv_reload_signals` VALUES("598","3","reload",NULL,"1","2025-12-30 15:56:49","2025-12-29 11:14:04",NULL);
INSERT INTO `tv_reload_signals` VALUES("599","4","reload",NULL,"1","2025-12-30 15:38:20","2025-12-29 11:14:04",NULL);
INSERT INTO `tv_reload_signals` VALUES("600","5","reload",NULL,"1","2025-12-30 15:39:09","2025-12-29 11:14:04",NULL);
INSERT INTO `tv_reload_signals` VALUES("601","6","reload",NULL,"0",NULL,"2025-12-29 11:14:04",NULL);
INSERT INTO `tv_reload_signals` VALUES("602","7","reload",NULL,"0",NULL,"2025-12-29 11:14:04",NULL);
INSERT INTO `tv_reload_signals` VALUES("603","1","reload",NULL,"1","2025-12-29 13:26:27","2025-12-29 11:16:46",NULL);
INSERT INTO `tv_reload_signals` VALUES("604","2","reload",NULL,"1","2025-12-30 16:06:07","2025-12-29 11:16:46",NULL);
INSERT INTO `tv_reload_signals` VALUES("605","3","reload",NULL,"1","2025-12-30 15:56:48","2025-12-29 11:16:46",NULL);
INSERT INTO `tv_reload_signals` VALUES("606","4","reload",NULL,"1","2025-12-30 15:38:20","2025-12-29 11:16:46",NULL);
INSERT INTO `tv_reload_signals` VALUES("607","5","reload",NULL,"1","2025-12-30 15:39:09","2025-12-29 11:16:46",NULL);
INSERT INTO `tv_reload_signals` VALUES("608","6","reload",NULL,"0",NULL,"2025-12-29 11:16:46",NULL);
INSERT INTO `tv_reload_signals` VALUES("609","7","reload",NULL,"0",NULL,"2025-12-29 11:16:46",NULL);
INSERT INTO `tv_reload_signals` VALUES("610","1","reload",NULL,"1","2025-12-29 13:26:27","2025-12-29 11:16:58",NULL);
INSERT INTO `tv_reload_signals` VALUES("611","2","reload",NULL,"1","2025-12-30 16:06:06","2025-12-29 11:16:58",NULL);
INSERT INTO `tv_reload_signals` VALUES("612","3","reload",NULL,"1","2025-12-30 15:56:48","2025-12-29 11:16:58",NULL);
INSERT INTO `tv_reload_signals` VALUES("613","4","reload",NULL,"1","2025-12-30 15:38:20","2025-12-29 11:16:58",NULL);
INSERT INTO `tv_reload_signals` VALUES("614","5","reload",NULL,"1","2025-12-30 15:39:08","2025-12-29 11:16:58",NULL);
INSERT INTO `tv_reload_signals` VALUES("615","6","reload",NULL,"0",NULL,"2025-12-29 11:16:58",NULL);
INSERT INTO `tv_reload_signals` VALUES("616","7","reload",NULL,"0",NULL,"2025-12-29 11:16:58",NULL);
INSERT INTO `tv_reload_signals` VALUES("617","1","reload",NULL,"1","2025-12-29 13:30:19","2025-12-29 13:28:03",NULL);
INSERT INTO `tv_reload_signals` VALUES("618","2","reload",NULL,"1","2025-12-30 16:06:06","2025-12-29 13:28:03",NULL);
INSERT INTO `tv_reload_signals` VALUES("619","3","reload",NULL,"1","2025-12-30 15:56:47","2025-12-29 13:28:03",NULL);
INSERT INTO `tv_reload_signals` VALUES("620","4","reload",NULL,"1","2025-12-30 15:38:20","2025-12-29 13:28:03",NULL);
INSERT INTO `tv_reload_signals` VALUES("621","5","reload",NULL,"1","2025-12-30 15:39:08","2025-12-29 13:28:03",NULL);
INSERT INTO `tv_reload_signals` VALUES("622","6","reload",NULL,"0",NULL,"2025-12-29 13:28:03",NULL);
INSERT INTO `tv_reload_signals` VALUES("623","7","reload",NULL,"0",NULL,"2025-12-29 13:28:03",NULL);
INSERT INTO `tv_reload_signals` VALUES("624","1","reload",NULL,"1","2025-12-29 13:30:18","2025-12-29 13:28:53",NULL);
INSERT INTO `tv_reload_signals` VALUES("625","2","reload",NULL,"1","2025-12-30 16:06:05","2025-12-29 13:28:53",NULL);
INSERT INTO `tv_reload_signals` VALUES("626","3","reload",NULL,"1","2025-12-30 15:56:47","2025-12-29 13:28:53",NULL);
INSERT INTO `tv_reload_signals` VALUES("627","4","reload",NULL,"1","2025-12-30 15:38:19","2025-12-29 13:28:53",NULL);
INSERT INTO `tv_reload_signals` VALUES("628","5","reload",NULL,"1","2025-12-30 15:39:07","2025-12-29 13:28:53",NULL);
INSERT INTO `tv_reload_signals` VALUES("629","6","reload",NULL,"0",NULL,"2025-12-29 13:28:53",NULL);
INSERT INTO `tv_reload_signals` VALUES("630","7","reload",NULL,"0",NULL,"2025-12-29 13:28:53",NULL);
INSERT INTO `tv_reload_signals` VALUES("631","1","reload",NULL,"1","2025-12-29 13:30:18","2025-12-29 13:29:01",NULL);
INSERT INTO `tv_reload_signals` VALUES("632","2","reload",NULL,"1","2025-12-30 16:06:05","2025-12-29 13:29:01",NULL);
INSERT INTO `tv_reload_signals` VALUES("633","3","reload",NULL,"1","2025-12-30 15:56:46","2025-12-29 13:29:01",NULL);
INSERT INTO `tv_reload_signals` VALUES("634","4","reload",NULL,"1","2025-12-30 15:38:19","2025-12-29 13:29:01",NULL);
INSERT INTO `tv_reload_signals` VALUES("635","5","reload",NULL,"1","2025-12-30 15:39:07","2025-12-29 13:29:01",NULL);
INSERT INTO `tv_reload_signals` VALUES("636","6","reload",NULL,"0",NULL,"2025-12-29 13:29:01",NULL);
INSERT INTO `tv_reload_signals` VALUES("637","7","reload",NULL,"0",NULL,"2025-12-29 13:29:01",NULL);
INSERT INTO `tv_reload_signals` VALUES("638","1","reload",NULL,"1","2025-12-29 13:30:35","2025-12-29 13:30:32",NULL);
INSERT INTO `tv_reload_signals` VALUES("639","2","reload",NULL,"1","2025-12-30 16:06:05","2025-12-29 13:30:32",NULL);
INSERT INTO `tv_reload_signals` VALUES("640","3","reload",NULL,"1","2025-12-30 15:56:46","2025-12-29 13:30:32",NULL);
INSERT INTO `tv_reload_signals` VALUES("641","4","reload",NULL,"1","2025-12-30 15:38:18","2025-12-29 13:30:32",NULL);
INSERT INTO `tv_reload_signals` VALUES("642","5","reload",NULL,"1","2025-12-30 15:39:06","2025-12-29 13:30:32",NULL);
INSERT INTO `tv_reload_signals` VALUES("643","6","reload",NULL,"0",NULL,"2025-12-29 13:30:32",NULL);
INSERT INTO `tv_reload_signals` VALUES("644","7","reload",NULL,"0",NULL,"2025-12-29 13:30:32",NULL);
INSERT INTO `tv_reload_signals` VALUES("645","1","reload",NULL,"1","2025-12-29 16:41:09","2025-12-29 16:39:09",NULL);
INSERT INTO `tv_reload_signals` VALUES("646","2","reload",NULL,"1","2025-12-30 16:06:05","2025-12-29 16:39:09",NULL);
INSERT INTO `tv_reload_signals` VALUES("647","3","reload",NULL,"1","2025-12-30 15:56:44","2025-12-29 16:39:09",NULL);
INSERT INTO `tv_reload_signals` VALUES("648","4","reload",NULL,"1","2025-12-30 15:38:18","2025-12-29 16:39:09",NULL);
INSERT INTO `tv_reload_signals` VALUES("649","5","reload",NULL,"1","2025-12-30 15:39:06","2025-12-29 16:39:09",NULL);
INSERT INTO `tv_reload_signals` VALUES("650","6","reload",NULL,"0",NULL,"2025-12-29 16:39:09",NULL);
INSERT INTO `tv_reload_signals` VALUES("651","7","reload",NULL,"0",NULL,"2025-12-29 16:39:09",NULL);
INSERT INTO `tv_reload_signals` VALUES("652","1","reload",NULL,"1","2025-12-30 15:54:42","2025-12-30 15:54:41",NULL);
INSERT INTO `tv_reload_signals` VALUES("653","2","reload",NULL,"1","2025-12-30 16:06:04","2025-12-30 15:54:41",NULL);
INSERT INTO `tv_reload_signals` VALUES("654","3","reload",NULL,"1","2025-12-30 15:56:44","2025-12-30 15:54:41",NULL);
INSERT INTO `tv_reload_signals` VALUES("655","4","reload",NULL,"1","2025-12-30 15:54:42","2025-12-30 15:54:41",NULL);
INSERT INTO `tv_reload_signals` VALUES("656","5","reload",NULL,"1","2025-12-30 15:54:44","2025-12-30 15:54:41",NULL);
INSERT INTO `tv_reload_signals` VALUES("657","6","reload",NULL,"0",NULL,"2025-12-30 15:54:41",NULL);
INSERT INTO `tv_reload_signals` VALUES("658","7","reload",NULL,"0",NULL,"2025-12-30 15:54:41",NULL);
INSERT INTO `tv_reload_signals` VALUES("659","1","reload",NULL,"1","2025-12-30 16:00:32","2025-12-30 16:00:29",NULL);
INSERT INTO `tv_reload_signals` VALUES("660","2","reload",NULL,"1","2025-12-30 16:06:04","2025-12-30 16:00:29",NULL);
INSERT INTO `tv_reload_signals` VALUES("661","3","reload",NULL,"1","2026-01-01 14:58:15","2025-12-30 16:00:29",NULL);
INSERT INTO `tv_reload_signals` VALUES("662","4","reload",NULL,"1","2025-12-30 16:00:32","2025-12-30 16:00:29",NULL);
INSERT INTO `tv_reload_signals` VALUES("663","5","reload",NULL,"1","2025-12-30 16:00:30","2025-12-30 16:00:29",NULL);
INSERT INTO `tv_reload_signals` VALUES("664","6","reload",NULL,"0",NULL,"2025-12-30 16:00:29",NULL);
INSERT INTO `tv_reload_signals` VALUES("665","7","reload",NULL,"0",NULL,"2025-12-30 16:00:29",NULL);
INSERT INTO `tv_reload_signals` VALUES("666","1","reload",NULL,"1","2025-12-30 16:00:53","2025-12-30 16:00:52",NULL);
INSERT INTO `tv_reload_signals` VALUES("667","2","reload",NULL,"1","2025-12-30 16:06:03","2025-12-30 16:00:52",NULL);
INSERT INTO `tv_reload_signals` VALUES("668","3","reload",NULL,"1","2026-01-01 14:58:15","2025-12-30 16:00:52",NULL);
INSERT INTO `tv_reload_signals` VALUES("669","4","reload",NULL,"1","2025-12-30 16:00:53","2025-12-30 16:00:52",NULL);
INSERT INTO `tv_reload_signals` VALUES("670","5","reload",NULL,"1","2025-12-30 16:00:52","2025-12-30 16:00:52",NULL);
INSERT INTO `tv_reload_signals` VALUES("671","6","reload",NULL,"0",NULL,"2025-12-30 16:00:52",NULL);
INSERT INTO `tv_reload_signals` VALUES("672","7","reload",NULL,"0",NULL,"2025-12-30 16:00:52",NULL);
INSERT INTO `tv_reload_signals` VALUES("673","1","reload",NULL,"1","2025-12-30 16:05:59","2025-12-30 16:05:56",NULL);
INSERT INTO `tv_reload_signals` VALUES("674","2","reload",NULL,"1","2025-12-30 16:06:03","2025-12-30 16:05:56",NULL);
INSERT INTO `tv_reload_signals` VALUES("675","3","reload",NULL,"1","2026-01-01 14:58:13","2025-12-30 16:05:56",NULL);
INSERT INTO `tv_reload_signals` VALUES("676","4","reload",NULL,"1","2025-12-30 16:05:57","2025-12-30 16:05:56",NULL);
INSERT INTO `tv_reload_signals` VALUES("677","5","reload",NULL,"1","2025-12-30 16:05:57","2025-12-30 16:05:56",NULL);
INSERT INTO `tv_reload_signals` VALUES("678","6","reload",NULL,"0",NULL,"2025-12-30 16:05:56",NULL);
INSERT INTO `tv_reload_signals` VALUES("679","7","reload",NULL,"0",NULL,"2025-12-30 16:05:56",NULL);
INSERT INTO `tv_reload_signals` VALUES("680","1","reload",NULL,"1","2026-01-01 14:25:03","2026-01-01 14:25:00",NULL);
INSERT INTO `tv_reload_signals` VALUES("681","2","reload",NULL,"1","2026-01-01 14:57:55","2026-01-01 14:25:00",NULL);
INSERT INTO `tv_reload_signals` VALUES("682","3","reload",NULL,"1","2026-01-01 14:58:13","2026-01-01 14:25:00",NULL);
INSERT INTO `tv_reload_signals` VALUES("683","4","reload",NULL,"1","2026-01-01 14:58:17","2026-01-01 14:25:00",NULL);
INSERT INTO `tv_reload_signals` VALUES("684","5","reload",NULL,"1","2026-01-02 14:38:51","2026-01-01 14:25:00",NULL);
INSERT INTO `tv_reload_signals` VALUES("685","6","reload",NULL,"0",NULL,"2026-01-01 14:25:00",NULL);
INSERT INTO `tv_reload_signals` VALUES("686","7","reload",NULL,"0",NULL,"2026-01-01 14:25:00",NULL);
INSERT INTO `tv_reload_signals` VALUES("687","1","reload",NULL,"1","2026-01-01 14:25:03","2026-01-01 14:25:00",NULL);
INSERT INTO `tv_reload_signals` VALUES("688","2","reload",NULL,"1","2026-01-01 14:57:57","2026-01-01 14:25:00",NULL);
INSERT INTO `tv_reload_signals` VALUES("689","3","reload",NULL,"1","2026-01-01 14:58:12","2026-01-01 14:25:00",NULL);
INSERT INTO `tv_reload_signals` VALUES("690","4","reload",NULL,"1","2026-01-01 14:58:17","2026-01-01 14:25:00",NULL);
INSERT INTO `tv_reload_signals` VALUES("691","5","reload",NULL,"1","2026-01-02 14:38:52","2026-01-01 14:25:00",NULL);
INSERT INTO `tv_reload_signals` VALUES("692","6","reload",NULL,"0",NULL,"2026-01-01 14:25:00",NULL);
INSERT INTO `tv_reload_signals` VALUES("693","7","reload",NULL,"0",NULL,"2026-01-01 14:25:00",NULL);
INSERT INTO `tv_reload_signals` VALUES("694","1","reload",NULL,"1","2026-01-01 14:33:24","2026-01-01 14:33:23",NULL);
INSERT INTO `tv_reload_signals` VALUES("695","2","reload",NULL,"1","2026-01-01 14:57:55","2026-01-01 14:33:23",NULL);
INSERT INTO `tv_reload_signals` VALUES("696","3","reload",NULL,"1","2026-01-01 14:58:12","2026-01-01 14:33:23",NULL);
INSERT INTO `tv_reload_signals` VALUES("697","4","reload",NULL,"1","2026-01-01 14:58:15","2026-01-01 14:33:23",NULL);
INSERT INTO `tv_reload_signals` VALUES("698","5","reload",NULL,"1","2026-01-02 14:38:51","2026-01-01 14:33:23",NULL);
INSERT INTO `tv_reload_signals` VALUES("699","6","reload",NULL,"0",NULL,"2026-01-01 14:33:23",NULL);
INSERT INTO `tv_reload_signals` VALUES("700","7","reload",NULL,"0",NULL,"2026-01-01 14:33:23",NULL);
INSERT INTO `tv_reload_signals` VALUES("701","8","reload",NULL,"1","2026-01-01 14:44:13","2026-01-01 14:33:23",NULL);
INSERT INTO `tv_reload_signals` VALUES("702","1","reload",NULL,"1","2026-01-01 14:36:17","2026-01-01 14:36:15",NULL);
INSERT INTO `tv_reload_signals` VALUES("703","2","reload",NULL,"1","2026-01-01 14:57:55","2026-01-01 14:36:15",NULL);
INSERT INTO `tv_reload_signals` VALUES("704","3","reload",NULL,"1","2026-01-01 14:58:11","2026-01-01 14:36:15",NULL);
INSERT INTO `tv_reload_signals` VALUES("705","4","reload",NULL,"1","2026-01-01 14:58:15","2026-01-01 14:36:15",NULL);
INSERT INTO `tv_reload_signals` VALUES("706","5","reload",NULL,"1","2026-01-02 14:38:50","2026-01-01 14:36:15",NULL);
INSERT INTO `tv_reload_signals` VALUES("707","6","reload",NULL,"0",NULL,"2026-01-01 14:36:15",NULL);
INSERT INTO `tv_reload_signals` VALUES("708","7","reload",NULL,"0",NULL,"2026-01-01 14:36:15",NULL);
INSERT INTO `tv_reload_signals` VALUES("709","8","reload",NULL,"1","2026-01-01 14:44:12","2026-01-01 14:36:15",NULL);
INSERT INTO `tv_reload_signals` VALUES("710","1","reload",NULL,"1","2026-01-01 14:38:08","2026-01-01 14:38:06",NULL);
INSERT INTO `tv_reload_signals` VALUES("711","2","reload",NULL,"1","2026-01-01 14:57:55","2026-01-01 14:38:06",NULL);
INSERT INTO `tv_reload_signals` VALUES("712","3","reload",NULL,"1","2026-01-01 14:58:11","2026-01-01 14:38:06",NULL);
INSERT INTO `tv_reload_signals` VALUES("713","4","reload",NULL,"1","2026-01-01 14:58:14","2026-01-01 14:38:06",NULL);
INSERT INTO `tv_reload_signals` VALUES("714","5","reload",NULL,"1","2026-01-02 14:38:50","2026-01-01 14:38:06",NULL);
INSERT INTO `tv_reload_signals` VALUES("715","6","reload",NULL,"0",NULL,"2026-01-01 14:38:06",NULL);
INSERT INTO `tv_reload_signals` VALUES("716","7","reload",NULL,"0",NULL,"2026-01-01 14:38:06",NULL);
INSERT INTO `tv_reload_signals` VALUES("717","8","reload",NULL,"1","2026-01-01 14:44:12","2026-01-01 14:38:06",NULL);
INSERT INTO `tv_reload_signals` VALUES("718","1","reload",NULL,"1","2026-01-01 14:44:49","2026-01-01 14:44:49",NULL);
INSERT INTO `tv_reload_signals` VALUES("719","2","reload",NULL,"1","2026-01-01 14:57:53","2026-01-01 14:44:49",NULL);
INSERT INTO `tv_reload_signals` VALUES("720","3","reload",NULL,"1","2026-01-01 14:58:10","2026-01-01 14:44:49",NULL);
INSERT INTO `tv_reload_signals` VALUES("721","4","reload",NULL,"1","2026-01-01 14:58:13","2026-01-01 14:44:49",NULL);
INSERT INTO `tv_reload_signals` VALUES("722","5","reload",NULL,"1","2026-01-02 14:38:48","2026-01-01 14:44:49",NULL);
INSERT INTO `tv_reload_signals` VALUES("723","6","reload",NULL,"0",NULL,"2026-01-01 14:44:49",NULL);
INSERT INTO `tv_reload_signals` VALUES("724","7","reload",NULL,"0",NULL,"2026-01-01 14:44:49",NULL);
INSERT INTO `tv_reload_signals` VALUES("725","8","reload",NULL,"1","2026-01-01 14:46:07","2026-01-01 14:44:49",NULL);
INSERT INTO `tv_reload_signals` VALUES("726","1","reload",NULL,"1","2026-01-01 14:44:50","2026-01-01 14:44:49",NULL);
INSERT INTO `tv_reload_signals` VALUES("727","2","reload",NULL,"1","2026-01-01 14:57:53","2026-01-01 14:44:49",NULL);
INSERT INTO `tv_reload_signals` VALUES("728","3","reload",NULL,"1","2026-01-01 14:58:10","2026-01-01 14:44:49",NULL);
INSERT INTO `tv_reload_signals` VALUES("729","4","reload",NULL,"1","2026-01-01 14:58:13","2026-01-01 14:44:49",NULL);
INSERT INTO `tv_reload_signals` VALUES("730","5","reload",NULL,"1","2026-01-02 14:38:48","2026-01-01 14:44:49",NULL);
INSERT INTO `tv_reload_signals` VALUES("731","6","reload",NULL,"0",NULL,"2026-01-01 14:44:49",NULL);
INSERT INTO `tv_reload_signals` VALUES("732","7","reload",NULL,"0",NULL,"2026-01-01 14:44:49",NULL);
INSERT INTO `tv_reload_signals` VALUES("733","8","reload",NULL,"1","2026-01-01 14:46:07","2026-01-01 14:44:49",NULL);
INSERT INTO `tv_reload_signals` VALUES("734","1","reload",NULL,"1","2026-01-01 15:27:22","2026-01-01 15:27:20",NULL);
INSERT INTO `tv_reload_signals` VALUES("735","2","reload",NULL,"1","2026-01-01 15:27:23","2026-01-01 15:27:20",NULL);
INSERT INTO `tv_reload_signals` VALUES("736","3","reload",NULL,"1","2026-01-01 15:27:24","2026-01-01 15:27:20",NULL);
INSERT INTO `tv_reload_signals` VALUES("737","4","reload",NULL,"1","2026-01-01 15:27:22","2026-01-01 15:27:20",NULL);
INSERT INTO `tv_reload_signals` VALUES("738","5","reload",NULL,"1","2026-01-02 14:38:45","2026-01-01 15:27:20",NULL);
INSERT INTO `tv_reload_signals` VALUES("739","6","reload",NULL,"0",NULL,"2026-01-01 15:27:20",NULL);
INSERT INTO `tv_reload_signals` VALUES("740","7","reload",NULL,"0",NULL,"2026-01-01 15:27:20",NULL);
INSERT INTO `tv_reload_signals` VALUES("741","8","reload",NULL,"1","2026-01-01 15:27:20","2026-01-01 15:27:20",NULL);
INSERT INTO `tv_reload_signals` VALUES("742","1","reload",NULL,"1","2026-01-01 15:34:27","2026-01-01 15:34:24",NULL);
INSERT INTO `tv_reload_signals` VALUES("743","2","reload",NULL,"1","2026-01-01 15:34:24","2026-01-01 15:34:24",NULL);
INSERT INTO `tv_reload_signals` VALUES("744","3","reload",NULL,"1","2026-01-01 15:36:33","2026-01-01 15:34:24",NULL);
INSERT INTO `tv_reload_signals` VALUES("745","4","reload",NULL,"1","2026-01-01 15:34:28","2026-01-01 15:34:24",NULL);
INSERT INTO `tv_reload_signals` VALUES("746","5","reload",NULL,"1","2026-01-02 14:38:45","2026-01-01 15:34:24",NULL);
INSERT INTO `tv_reload_signals` VALUES("747","6","reload",NULL,"0",NULL,"2026-01-01 15:34:24",NULL);
INSERT INTO `tv_reload_signals` VALUES("748","7","reload",NULL,"0",NULL,"2026-01-01 15:34:24",NULL);
INSERT INTO `tv_reload_signals` VALUES("749","8","reload",NULL,"1","2026-01-01 15:34:26","2026-01-01 15:34:24",NULL);
INSERT INTO `tv_reload_signals` VALUES("750","1","reload",NULL,"1","2026-01-03 20:38:21","2026-01-02 14:45:45",NULL);
INSERT INTO `tv_reload_signals` VALUES("751","2","reload",NULL,"1","2026-01-02 14:45:45","2026-01-02 14:45:45",NULL);
INSERT INTO `tv_reload_signals` VALUES("752","3","reload",NULL,"1","2026-01-02 14:45:47","2026-01-02 14:45:45",NULL);
INSERT INTO `tv_reload_signals` VALUES("753","4","reload",NULL,"1","2026-01-02 14:45:45","2026-01-02 14:45:45",NULL);
INSERT INTO `tv_reload_signals` VALUES("754","5","reload",NULL,"1","2026-01-02 14:45:48","2026-01-02 14:45:45",NULL);
INSERT INTO `tv_reload_signals` VALUES("755","6","reload",NULL,"0",NULL,"2026-01-02 14:45:45",NULL);
INSERT INTO `tv_reload_signals` VALUES("756","7","reload",NULL,"0",NULL,"2026-01-02 14:45:45",NULL);
INSERT INTO `tv_reload_signals` VALUES("757","8","reload",NULL,"1","2026-01-02 15:46:23","2026-01-02 14:45:45",NULL);
INSERT INTO `tv_reload_signals` VALUES("758","1","reload",NULL,"1","2026-01-03 20:38:20","2026-01-02 14:48:10",NULL);
INSERT INTO `tv_reload_signals` VALUES("759","2","reload",NULL,"1","2026-01-02 14:48:11","2026-01-02 14:48:10",NULL);
INSERT INTO `tv_reload_signals` VALUES("760","3","reload",NULL,"1","2026-01-02 14:48:12","2026-01-02 14:48:10",NULL);
INSERT INTO `tv_reload_signals` VALUES("761","4","reload",NULL,"1","2026-01-02 14:48:11","2026-01-02 14:48:10",NULL);
INSERT INTO `tv_reload_signals` VALUES("762","5","reload",NULL,"1","2026-01-02 14:48:11","2026-01-02 14:48:10",NULL);
INSERT INTO `tv_reload_signals` VALUES("763","6","reload",NULL,"0",NULL,"2026-01-02 14:48:10",NULL);
INSERT INTO `tv_reload_signals` VALUES("764","7","reload",NULL,"0",NULL,"2026-01-02 14:48:10",NULL);
INSERT INTO `tv_reload_signals` VALUES("765","8","reload",NULL,"1","2026-01-02 15:46:19","2026-01-02 14:48:10",NULL);
INSERT INTO `tv_reload_signals` VALUES("766","1","reload",NULL,"1","2026-01-03 20:38:20","2026-01-02 14:50:52",NULL);
INSERT INTO `tv_reload_signals` VALUES("767","2","reload",NULL,"1","2026-01-02 14:50:52","2026-01-02 14:50:52",NULL);
INSERT INTO `tv_reload_signals` VALUES("768","3","reload",NULL,"1","2026-01-02 14:50:56","2026-01-02 14:50:52",NULL);
INSERT INTO `tv_reload_signals` VALUES("769","4","reload",NULL,"1","2026-01-02 14:50:52","2026-01-02 14:50:52",NULL);
INSERT INTO `tv_reload_signals` VALUES("770","5","reload",NULL,"1","2026-01-02 14:50:56","2026-01-02 14:50:52",NULL);
INSERT INTO `tv_reload_signals` VALUES("771","6","reload",NULL,"0",NULL,"2026-01-02 14:50:52",NULL);
INSERT INTO `tv_reload_signals` VALUES("772","7","reload",NULL,"0",NULL,"2026-01-02 14:50:52",NULL);
INSERT INTO `tv_reload_signals` VALUES("773","8","reload",NULL,"1","2026-01-02 15:46:19","2026-01-02 14:50:52",NULL);
INSERT INTO `tv_reload_signals` VALUES("774","1","reload",NULL,"1","2026-01-03 20:38:19","2026-01-03 15:57:36",NULL);
INSERT INTO `tv_reload_signals` VALUES("775","2","reload",NULL,"1","2026-01-03 15:57:38","2026-01-03 15:57:36",NULL);
INSERT INTO `tv_reload_signals` VALUES("776","3","reload",NULL,"1","2026-01-03 15:57:38","2026-01-03 15:57:36",NULL);
INSERT INTO `tv_reload_signals` VALUES("777","4","reload",NULL,"1","2026-01-03 15:57:38","2026-01-03 15:57:36",NULL);
INSERT INTO `tv_reload_signals` VALUES("778","5","reload",NULL,"1","2026-01-03 16:02:25","2026-01-03 15:57:36",NULL);
INSERT INTO `tv_reload_signals` VALUES("779","6","reload",NULL,"0",NULL,"2026-01-03 15:57:36",NULL);
INSERT INTO `tv_reload_signals` VALUES("780","7","reload",NULL,"0",NULL,"2026-01-03 15:57:36",NULL);
INSERT INTO `tv_reload_signals` VALUES("781","8","reload",NULL,"1","2026-01-03 21:19:02","2026-01-03 15:57:36",NULL);
INSERT INTO `tv_reload_signals` VALUES("782","1","reload",NULL,"1","2026-01-03 20:38:18","2026-01-03 16:00:57",NULL);
INSERT INTO `tv_reload_signals` VALUES("783","2","reload",NULL,"1","2026-01-03 16:00:58","2026-01-03 16:00:57",NULL);
INSERT INTO `tv_reload_signals` VALUES("784","3","reload",NULL,"1","2026-01-03 16:00:58","2026-01-03 16:00:57",NULL);
INSERT INTO `tv_reload_signals` VALUES("785","4","reload",NULL,"1","2026-01-03 16:00:58","2026-01-03 16:00:57",NULL);
INSERT INTO `tv_reload_signals` VALUES("786","5","reload",NULL,"1","2026-01-03 16:02:23","2026-01-03 16:00:57",NULL);
INSERT INTO `tv_reload_signals` VALUES("787","6","reload",NULL,"0",NULL,"2026-01-03 16:00:57",NULL);
INSERT INTO `tv_reload_signals` VALUES("788","7","reload",NULL,"0",NULL,"2026-01-03 16:00:57",NULL);
INSERT INTO `tv_reload_signals` VALUES("789","8","reload",NULL,"1","2026-01-03 21:19:01","2026-01-03 16:00:57",NULL);
INSERT INTO `tv_reload_signals` VALUES("790","1","reload",NULL,"1","2026-01-03 20:38:19","2026-01-03 16:00:57",NULL);
INSERT INTO `tv_reload_signals` VALUES("791","2","reload",NULL,"1","2026-01-03 16:00:58","2026-01-03 16:00:57",NULL);
INSERT INTO `tv_reload_signals` VALUES("792","3","reload",NULL,"1","2026-01-03 16:01:03","2026-01-03 16:00:57",NULL);
INSERT INTO `tv_reload_signals` VALUES("793","4","reload",NULL,"1","2026-01-03 16:00:58","2026-01-03 16:00:57",NULL);
INSERT INTO `tv_reload_signals` VALUES("794","5","reload",NULL,"1","2026-01-03 16:02:23","2026-01-03 16:00:57",NULL);
INSERT INTO `tv_reload_signals` VALUES("795","6","reload",NULL,"0",NULL,"2026-01-03 16:00:57",NULL);
INSERT INTO `tv_reload_signals` VALUES("796","7","reload",NULL,"0",NULL,"2026-01-03 16:00:57",NULL);
INSERT INTO `tv_reload_signals` VALUES("797","8","reload",NULL,"1","2026-01-03 21:19:01","2026-01-03 16:00:57",NULL);
INSERT INTO `tv_reload_signals` VALUES("798","1","reload",NULL,"1","2026-01-03 20:38:18","2026-01-03 16:04:21",NULL);
INSERT INTO `tv_reload_signals` VALUES("799","2","reload",NULL,"1","2026-01-03 16:04:24","2026-01-03 16:04:21",NULL);
INSERT INTO `tv_reload_signals` VALUES("800","3","reload",NULL,"1","2026-01-03 16:04:22","2026-01-03 16:04:21",NULL);
INSERT INTO `tv_reload_signals` VALUES("801","4","reload",NULL,"1","2026-01-03 16:04:24","2026-01-03 16:04:21",NULL);
INSERT INTO `tv_reload_signals` VALUES("802","5","reload",NULL,"1","2026-01-03 16:04:21","2026-01-03 16:04:21",NULL);
INSERT INTO `tv_reload_signals` VALUES("803","6","reload",NULL,"0",NULL,"2026-01-03 16:04:21",NULL);
INSERT INTO `tv_reload_signals` VALUES("804","7","reload",NULL,"0",NULL,"2026-01-03 16:04:21",NULL);
INSERT INTO `tv_reload_signals` VALUES("805","8","reload",NULL,"1","2026-01-03 21:19:00","2026-01-03 16:04:21",NULL);
INSERT INTO `tv_reload_signals` VALUES("806","1","reload",NULL,"1","2026-01-03 20:38:17","2026-01-03 16:04:43",NULL);
INSERT INTO `tv_reload_signals` VALUES("807","2","reload",NULL,"1","2026-01-03 16:04:44","2026-01-03 16:04:43",NULL);
INSERT INTO `tv_reload_signals` VALUES("808","3","reload",NULL,"1","2026-01-03 16:04:47","2026-01-03 16:04:43",NULL);
INSERT INTO `tv_reload_signals` VALUES("809","4","reload",NULL,"1","2026-01-03 16:04:44","2026-01-03 16:04:43",NULL);
INSERT INTO `tv_reload_signals` VALUES("810","5","reload",NULL,"1","2026-01-03 16:04:46","2026-01-03 16:04:43",NULL);
INSERT INTO `tv_reload_signals` VALUES("811","6","reload",NULL,"0",NULL,"2026-01-03 16:04:43",NULL);
INSERT INTO `tv_reload_signals` VALUES("812","7","reload",NULL,"0",NULL,"2026-01-03 16:04:43",NULL);
INSERT INTO `tv_reload_signals` VALUES("813","8","reload",NULL,"1","2026-01-03 21:19:00","2026-01-03 16:04:43",NULL);
INSERT INTO `tv_reload_signals` VALUES("814","1","reload",NULL,"1","2026-01-03 20:38:17","2026-01-03 16:04:43",NULL);
INSERT INTO `tv_reload_signals` VALUES("815","2","reload",NULL,"1","2026-01-03 16:04:44","2026-01-03 16:04:43",NULL);
INSERT INTO `tv_reload_signals` VALUES("816","3","reload",NULL,"1","2026-01-03 16:04:52","2026-01-03 16:04:43",NULL);
INSERT INTO `tv_reload_signals` VALUES("817","4","reload",NULL,"1","2026-01-03 16:04:44","2026-01-03 16:04:43",NULL);
INSERT INTO `tv_reload_signals` VALUES("818","5","reload",NULL,"1","2026-01-03 16:04:51","2026-01-03 16:04:43",NULL);
INSERT INTO `tv_reload_signals` VALUES("819","6","reload",NULL,"0",NULL,"2026-01-03 16:04:43",NULL);
INSERT INTO `tv_reload_signals` VALUES("820","7","reload",NULL,"0",NULL,"2026-01-03 16:04:43",NULL);
INSERT INTO `tv_reload_signals` VALUES("821","8","reload",NULL,"1","2026-01-03 21:18:59","2026-01-03 16:04:43",NULL);
INSERT INTO `tv_reload_signals` VALUES("822","1","reload",NULL,"1","2026-01-03 20:38:16","2026-01-03 16:05:03",NULL);
INSERT INTO `tv_reload_signals` VALUES("823","2","reload",NULL,"1","2026-01-03 16:05:05","2026-01-03 16:05:03",NULL);
INSERT INTO `tv_reload_signals` VALUES("824","3","reload",NULL,"1","2026-01-03 16:05:07","2026-01-03 16:05:03",NULL);
INSERT INTO `tv_reload_signals` VALUES("825","4","reload",NULL,"1","2026-01-03 16:05:05","2026-01-03 16:05:03",NULL);
INSERT INTO `tv_reload_signals` VALUES("826","5","reload",NULL,"1","2026-01-03 16:05:06","2026-01-03 16:05:03",NULL);
INSERT INTO `tv_reload_signals` VALUES("827","6","reload",NULL,"0",NULL,"2026-01-03 16:05:03",NULL);
INSERT INTO `tv_reload_signals` VALUES("828","7","reload",NULL,"0",NULL,"2026-01-03 16:05:03",NULL);
INSERT INTO `tv_reload_signals` VALUES("829","8","reload",NULL,"1","2026-01-03 21:18:59","2026-01-03 16:05:03",NULL);
INSERT INTO `tv_reload_signals` VALUES("830","1","reload",NULL,"1","2026-01-03 20:38:16","2026-01-03 18:59:51",NULL);
INSERT INTO `tv_reload_signals` VALUES("831","2","reload",NULL,"1","2026-01-03 18:59:53","2026-01-03 18:59:51",NULL);
INSERT INTO `tv_reload_signals` VALUES("832","3","reload",NULL,"1","2026-01-04 08:16:48","2026-01-03 18:59:51",NULL);
INSERT INTO `tv_reload_signals` VALUES("833","4","reload",NULL,"1","2026-01-03 18:59:53","2026-01-03 18:59:51",NULL);
INSERT INTO `tv_reload_signals` VALUES("834","5","reload",NULL,"0",NULL,"2026-01-03 18:59:51",NULL);
INSERT INTO `tv_reload_signals` VALUES("835","6","reload",NULL,"0",NULL,"2026-01-03 18:59:51",NULL);
INSERT INTO `tv_reload_signals` VALUES("836","7","reload",NULL,"0",NULL,"2026-01-03 18:59:51",NULL);
INSERT INTO `tv_reload_signals` VALUES("837","8","reload",NULL,"1","2026-01-03 21:18:59","2026-01-03 18:59:51",NULL);
INSERT INTO `tv_reload_signals` VALUES("838","1","reload",NULL,"1","2026-01-03 20:38:15","2026-01-03 20:16:07",NULL);
INSERT INTO `tv_reload_signals` VALUES("839","2","reload",NULL,"1","2026-01-03 20:16:07","2026-01-03 20:16:07",NULL);
INSERT INTO `tv_reload_signals` VALUES("840","3","reload",NULL,"1","2026-01-04 08:16:48","2026-01-03 20:16:07",NULL);
INSERT INTO `tv_reload_signals` VALUES("841","4","reload",NULL,"1","2026-01-03 20:16:09","2026-01-03 20:16:07",NULL);
INSERT INTO `tv_reload_signals` VALUES("842","5","reload",NULL,"0",NULL,"2026-01-03 20:16:07",NULL);
INSERT INTO `tv_reload_signals` VALUES("843","6","reload",NULL,"0",NULL,"2026-01-03 20:16:07",NULL);
INSERT INTO `tv_reload_signals` VALUES("844","7","reload",NULL,"0",NULL,"2026-01-03 20:16:07",NULL);
INSERT INTO `tv_reload_signals` VALUES("845","8","reload",NULL,"1","2026-01-03 21:18:59","2026-01-03 20:16:07",NULL);
INSERT INTO `tv_reload_signals` VALUES("846","1","reload",NULL,"1","2026-01-03 20:38:15","2026-01-03 20:20:36",NULL);
INSERT INTO `tv_reload_signals` VALUES("847","2","reload",NULL,"1","2026-01-03 20:20:37","2026-01-03 20:20:36",NULL);
INSERT INTO `tv_reload_signals` VALUES("848","3","reload",NULL,"1","2026-01-04 08:16:47","2026-01-03 20:20:36",NULL);
INSERT INTO `tv_reload_signals` VALUES("849","4","reload",NULL,"1","2026-01-03 20:20:38","2026-01-03 20:20:36",NULL);
INSERT INTO `tv_reload_signals` VALUES("850","5","reload",NULL,"0",NULL,"2026-01-03 20:20:36",NULL);
INSERT INTO `tv_reload_signals` VALUES("851","6","reload",NULL,"0",NULL,"2026-01-03 20:20:36",NULL);
INSERT INTO `tv_reload_signals` VALUES("852","7","reload",NULL,"0",NULL,"2026-01-03 20:20:36",NULL);
INSERT INTO `tv_reload_signals` VALUES("853","8","reload",NULL,"1","2026-01-03 21:18:58","2026-01-03 20:20:36",NULL);
INSERT INTO `tv_reload_signals` VALUES("854","1","reload",NULL,"1","2026-01-03 20:39:30","2026-01-03 20:39:28",NULL);
INSERT INTO `tv_reload_signals` VALUES("855","2","reload",NULL,"1","2026-01-03 20:39:29","2026-01-03 20:39:28",NULL);
INSERT INTO `tv_reload_signals` VALUES("856","3","reload",NULL,"1","2026-01-04 08:16:47","2026-01-03 20:39:28",NULL);
INSERT INTO `tv_reload_signals` VALUES("857","4","reload",NULL,"1","2026-01-03 20:39:29","2026-01-03 20:39:28",NULL);
INSERT INTO `tv_reload_signals` VALUES("858","5","reload",NULL,"0",NULL,"2026-01-03 20:39:28",NULL);
INSERT INTO `tv_reload_signals` VALUES("859","6","reload",NULL,"0",NULL,"2026-01-03 20:39:28",NULL);
INSERT INTO `tv_reload_signals` VALUES("860","7","reload",NULL,"0",NULL,"2026-01-03 20:39:28",NULL);
INSERT INTO `tv_reload_signals` VALUES("861","8","reload",NULL,"1","2026-01-03 21:18:58","2026-01-03 20:39:28",NULL);
INSERT INTO `tv_reload_signals` VALUES("862","1","reload",NULL,"1","2026-01-03 20:49:06","2026-01-03 20:49:04",NULL);
INSERT INTO `tv_reload_signals` VALUES("863","2","reload",NULL,"1","2026-01-03 20:49:05","2026-01-03 20:49:04",NULL);
INSERT INTO `tv_reload_signals` VALUES("864","3","reload",NULL,"1","2026-01-04 08:16:46","2026-01-03 20:49:04",NULL);
INSERT INTO `tv_reload_signals` VALUES("865","4","reload",NULL,"1","2026-01-03 20:49:05","2026-01-03 20:49:04",NULL);
INSERT INTO `tv_reload_signals` VALUES("866","5","reload",NULL,"0",NULL,"2026-01-03 20:49:04",NULL);
INSERT INTO `tv_reload_signals` VALUES("867","6","reload",NULL,"0",NULL,"2026-01-03 20:49:04",NULL);
INSERT INTO `tv_reload_signals` VALUES("868","7","reload",NULL,"0",NULL,"2026-01-03 20:49:04",NULL);
INSERT INTO `tv_reload_signals` VALUES("869","8","reload",NULL,"1","2026-01-03 21:18:57","2026-01-03 20:49:04",NULL);
INSERT INTO `tv_reload_signals` VALUES("870","1","reload",NULL,"1","2026-01-03 20:51:24","2026-01-03 20:51:22",NULL);
INSERT INTO `tv_reload_signals` VALUES("871","2","reload",NULL,"1","2026-01-03 20:51:24","2026-01-03 20:51:22",NULL);
INSERT INTO `tv_reload_signals` VALUES("872","3","reload",NULL,"1","2026-01-04 08:16:46","2026-01-03 20:51:22",NULL);
INSERT INTO `tv_reload_signals` VALUES("873","4","reload",NULL,"1","2026-01-03 20:51:24","2026-01-03 20:51:22",NULL);
INSERT INTO `tv_reload_signals` VALUES("874","5","reload",NULL,"0",NULL,"2026-01-03 20:51:22",NULL);
INSERT INTO `tv_reload_signals` VALUES("875","6","reload",NULL,"0",NULL,"2026-01-03 20:51:22",NULL);
INSERT INTO `tv_reload_signals` VALUES("876","7","reload",NULL,"0",NULL,"2026-01-03 20:51:22",NULL);
INSERT INTO `tv_reload_signals` VALUES("877","8","reload",NULL,"1","2026-01-03 21:18:57","2026-01-03 20:51:22",NULL);
INSERT INTO `tv_reload_signals` VALUES("878","1","reload",NULL,"1","2026-01-03 20:57:24","2026-01-03 20:57:23",NULL);
INSERT INTO `tv_reload_signals` VALUES("879","2","reload",NULL,"1","2026-01-03 20:57:23","2026-01-03 20:57:23",NULL);
INSERT INTO `tv_reload_signals` VALUES("880","3","reload",NULL,"1","2026-01-04 08:16:45","2026-01-03 20:57:23",NULL);
INSERT INTO `tv_reload_signals` VALUES("881","4","reload",NULL,"1","2026-01-03 20:57:23","2026-01-03 20:57:23",NULL);
INSERT INTO `tv_reload_signals` VALUES("882","5","reload",NULL,"0",NULL,"2026-01-03 20:57:23",NULL);
INSERT INTO `tv_reload_signals` VALUES("883","6","reload",NULL,"0",NULL,"2026-01-03 20:57:23",NULL);
INSERT INTO `tv_reload_signals` VALUES("884","7","reload",NULL,"0",NULL,"2026-01-03 20:57:23",NULL);
INSERT INTO `tv_reload_signals` VALUES("885","8","reload",NULL,"1","2026-01-03 21:18:56","2026-01-03 20:57:23",NULL);
INSERT INTO `tv_reload_signals` VALUES("886","1","reload",NULL,"1","2026-01-03 20:57:25","2026-01-03 20:57:24",NULL);
INSERT INTO `tv_reload_signals` VALUES("887","2","reload",NULL,"1","2026-01-03 20:57:24","2026-01-03 20:57:24",NULL);
INSERT INTO `tv_reload_signals` VALUES("888","3","reload",NULL,"1","2026-01-04 08:16:45","2026-01-03 20:57:24",NULL);
INSERT INTO `tv_reload_signals` VALUES("889","4","reload",NULL,"1","2026-01-03 20:57:24","2026-01-03 20:57:24",NULL);
INSERT INTO `tv_reload_signals` VALUES("890","5","reload",NULL,"0",NULL,"2026-01-03 20:57:24",NULL);
INSERT INTO `tv_reload_signals` VALUES("891","6","reload",NULL,"0",NULL,"2026-01-03 20:57:24",NULL);
INSERT INTO `tv_reload_signals` VALUES("892","7","reload",NULL,"0",NULL,"2026-01-03 20:57:24",NULL);
INSERT INTO `tv_reload_signals` VALUES("893","8","reload",NULL,"1","2026-01-03 21:18:56","2026-01-03 20:57:24",NULL);
INSERT INTO `tv_reload_signals` VALUES("894","1","reload",NULL,"1","2026-01-03 21:05:55","2026-01-03 21:05:54",NULL);
INSERT INTO `tv_reload_signals` VALUES("895","2","reload",NULL,"1","2026-01-03 21:05:56","2026-01-03 21:05:54",NULL);
INSERT INTO `tv_reload_signals` VALUES("896","3","reload",NULL,"1","2026-01-04 08:16:44","2026-01-03 21:05:54",NULL);
INSERT INTO `tv_reload_signals` VALUES("897","4","reload",NULL,"1","2026-01-03 21:05:56","2026-01-03 21:05:54",NULL);
INSERT INTO `tv_reload_signals` VALUES("898","5","reload",NULL,"0",NULL,"2026-01-03 21:05:54",NULL);
INSERT INTO `tv_reload_signals` VALUES("899","6","reload",NULL,"0",NULL,"2026-01-03 21:05:54",NULL);
INSERT INTO `tv_reload_signals` VALUES("900","7","reload",NULL,"0",NULL,"2026-01-03 21:05:54",NULL);
INSERT INTO `tv_reload_signals` VALUES("901","8","reload",NULL,"1","2026-01-03 21:18:56","2026-01-03 21:05:54",NULL);
INSERT INTO `tv_reload_signals` VALUES("902","1","reload",NULL,"1","2026-01-03 21:15:16","2026-01-03 21:15:16",NULL);
INSERT INTO `tv_reload_signals` VALUES("903","2","reload",NULL,"1","2026-01-03 21:15:17","2026-01-03 21:15:16",NULL);
INSERT INTO `tv_reload_signals` VALUES("904","3","reload",NULL,"1","2026-01-04 08:16:44","2026-01-03 21:15:16",NULL);
INSERT INTO `tv_reload_signals` VALUES("905","4","reload",NULL,"1","2026-01-03 21:15:17","2026-01-03 21:15:16",NULL);
INSERT INTO `tv_reload_signals` VALUES("906","5","reload",NULL,"0",NULL,"2026-01-03 21:15:16",NULL);
INSERT INTO `tv_reload_signals` VALUES("907","6","reload",NULL,"0",NULL,"2026-01-03 21:15:16",NULL);
INSERT INTO `tv_reload_signals` VALUES("908","7","reload",NULL,"0",NULL,"2026-01-03 21:15:16",NULL);
INSERT INTO `tv_reload_signals` VALUES("909","8","reload",NULL,"1","2026-01-03 21:18:56","2026-01-03 21:15:16",NULL);
INSERT INTO `tv_reload_signals` VALUES("910","1","reload",NULL,"1","2026-01-03 21:15:29","2026-01-03 21:15:27",NULL);
INSERT INTO `tv_reload_signals` VALUES("911","2","reload",NULL,"1","2026-01-03 21:15:29","2026-01-03 21:15:27",NULL);
INSERT INTO `tv_reload_signals` VALUES("912","3","reload",NULL,"1","2026-01-04 08:16:42","2026-01-03 21:15:27",NULL);
INSERT INTO `tv_reload_signals` VALUES("913","4","reload",NULL,"1","2026-01-03 21:15:28","2026-01-03 21:15:27",NULL);
INSERT INTO `tv_reload_signals` VALUES("914","6","reload",NULL,"0",NULL,"2026-01-03 21:15:27",NULL);
INSERT INTO `tv_reload_signals` VALUES("915","7","reload",NULL,"0",NULL,"2026-01-03 21:15:27",NULL);
INSERT INTO `tv_reload_signals` VALUES("916","8","reload",NULL,"1","2026-01-03 21:18:55","2026-01-03 21:15:27",NULL);
INSERT INTO `tv_reload_signals` VALUES("917","1","reload",NULL,"1","2026-01-03 21:15:29","2026-01-03 21:15:28",NULL);
INSERT INTO `tv_reload_signals` VALUES("918","2","reload",NULL,"1","2026-01-03 21:15:28","2026-01-03 21:15:28",NULL);
INSERT INTO `tv_reload_signals` VALUES("919","3","reload",NULL,"1","2026-01-04 08:16:42","2026-01-03 21:15:28",NULL);
INSERT INTO `tv_reload_signals` VALUES("920","4","reload",NULL,"1","2026-01-03 21:15:28","2026-01-03 21:15:28",NULL);
INSERT INTO `tv_reload_signals` VALUES("921","5","reload",NULL,"0",NULL,"2026-01-03 21:15:28",NULL);
INSERT INTO `tv_reload_signals` VALUES("922","6","reload",NULL,"0",NULL,"2026-01-03 21:15:28",NULL);
INSERT INTO `tv_reload_signals` VALUES("923","7","reload",NULL,"0",NULL,"2026-01-03 21:15:28",NULL);
INSERT INTO `tv_reload_signals` VALUES("924","8","reload",NULL,"1","2026-01-03 21:18:55","2026-01-03 21:15:28",NULL);
INSERT INTO `tv_reload_signals` VALUES("925","1","reload",NULL,"1","2026-01-04 09:54:19","2026-01-04 09:54:17",NULL);
INSERT INTO `tv_reload_signals` VALUES("926","2","reload",NULL,"1","2026-01-04 09:54:17","2026-01-04 09:54:17",NULL);
INSERT INTO `tv_reload_signals` VALUES("927","3","reload",NULL,"1","2026-01-04 09:54:19","2026-01-04 09:54:17",NULL);
INSERT INTO `tv_reload_signals` VALUES("928","4","reload",NULL,"1","2026-01-04 09:54:17","2026-01-04 09:54:17",NULL);
INSERT INTO `tv_reload_signals` VALUES("929","5","reload",NULL,"0",NULL,"2026-01-04 09:54:17",NULL);
INSERT INTO `tv_reload_signals` VALUES("930","6","reload",NULL,"0",NULL,"2026-01-04 09:54:17",NULL);
INSERT INTO `tv_reload_signals` VALUES("931","7","reload",NULL,"0",NULL,"2026-01-04 09:54:17",NULL);
INSERT INTO `tv_reload_signals` VALUES("932","8","reload",NULL,"1","2026-01-04 09:54:18","2026-01-04 09:54:17",NULL);
INSERT INTO `tv_reload_signals` VALUES("933","1","reload",NULL,"1","2026-01-04 09:54:19","2026-01-04 09:54:17",NULL);
INSERT INTO `tv_reload_signals` VALUES("934","2","reload",NULL,"1","2026-01-04 09:54:19","2026-01-04 09:54:17",NULL);
INSERT INTO `tv_reload_signals` VALUES("935","3","reload",NULL,"1","2026-01-04 09:54:19","2026-01-04 09:54:17",NULL);
INSERT INTO `tv_reload_signals` VALUES("936","4","reload",NULL,"1","2026-01-04 09:54:19","2026-01-04 09:54:17",NULL);
INSERT INTO `tv_reload_signals` VALUES("937","5","reload",NULL,"0",NULL,"2026-01-04 09:54:17",NULL);
INSERT INTO `tv_reload_signals` VALUES("938","6","reload",NULL,"0",NULL,"2026-01-04 09:54:17",NULL);
INSERT INTO `tv_reload_signals` VALUES("939","7","reload",NULL,"0",NULL,"2026-01-04 09:54:17",NULL);
INSERT INTO `tv_reload_signals` VALUES("940","8","reload",NULL,"1","2026-01-04 09:54:19","2026-01-04 09:54:17",NULL);

-- --------------------------------------------------------
-- Table structure for `tv_video_progress`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `tv_video_progress`;
CREATE TABLE `tv_video_progress` (
  `tv_id` int(11) NOT NULL,
  `content_id` int(11) DEFAULT NULL,
  `current_time` decimal(10,2) DEFAULT 0.00,
  `duration` decimal(10,2) DEFAULT 0.00,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`tv_id`),
  KEY `content_id` (`content_id`),
  KEY `idx_updated_at` (`updated_at`),
  CONSTRAINT `tv_video_progress_ibfk_1` FOREIGN KEY (`tv_id`) REFERENCES `tvs` (`id`) ON DELETE CASCADE,
  CONSTRAINT `tv_video_progress_ibfk_2` FOREIGN KEY (`content_id`) REFERENCES `media` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for `tvs`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `tvs`;
CREATE TABLE `tvs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `is_paused` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_folder` (`folder`),
  KEY `idx_status` (`status`),
  KEY `idx_folder` (`folder`),
  KEY `idx_location` (`location`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `tvs`

INSERT INTO `tvs` VALUES("1","TV Basement","Tầng hầm","basement","basement/index.php","online","49","49","","TV tại khu vực tầng hầm - Hiển thị thông tin chào mừng","2026-01-04 10:29:05","2025-11-26 19:56:54","2026-01-04 10:29:05","0");
INSERT INTO `tvs` VALUES("2","TV Chrysan","Phòng Chrysan","chrysan","chrysan/index.php","online","49","49","","TV tại phòng hội nghị Chrysan","2026-01-04 10:29:06","2025-11-26 19:56:54","2026-01-04 10:29:06","0");
INSERT INTO `tvs` VALUES("3","TV Jasmine","Phòng Jasmine","jasmine","jasmine/index.php","online","49","49","","Phòng họp Jasmine","2026-01-04 10:21:08","2025-11-26 19:56:54","2026-01-04 10:21:08","0");
INSERT INTO `tvs` VALUES("4","TV Lotus","Phòng Lotus","lotus","lotus/index.php","online","49","49","","TV tại phòng hội nghị Lotus","2026-01-04 10:29:06","2025-11-26 19:56:54","2026-01-04 10:29:06","0");
INSERT INTO `tvs` VALUES("5","TV Restaurant","Nhà hàng","restaurant","restaurant/index.php","online","49","49","","TV tại nhà hàng - Hiển thị menu và khuyến mãi","2026-01-03 16:16:23","2025-11-26 19:56:54","2026-01-04 09:54:17","0");
INSERT INTO `tvs` VALUES("6","TV FO 1","Lễ tân 1","fo/tv1","fo/tv1/index.php","online","49","49","","TV tại quầy lễ tân số 1","2025-12-23 08:10:57","2025-11-26 19:56:54","2026-01-03 21:15:27","0");
INSERT INTO `tvs` VALUES("7","TV FO 2","Lễ tân 2","fo/tv2","fo/tv2/index.php","online","49","49","","TV tại quầy lễ tân số 2","2025-12-02 10:48:01","2025-11-26 19:56:54","2026-01-03 21:15:27","0");
INSERT INTO `tvs` VALUES("8","TV Greeting","Phòng chào đoàn - Aurora Hotel Plaza","greeting",NULL,"online","49","49",NULL,NULL,"2026-01-04 10:29:06","2026-01-01 14:32:33","2026-01-04 10:29:06","0");

-- --------------------------------------------------------
-- Table structure for `user_permissions`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `user_permissions`;
CREATE TABLE `user_permissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `module` varchar(50) NOT NULL,
  `can_view` tinyint(1) DEFAULT 1,
  `can_create` tinyint(1) DEFAULT 0,
  `can_edit` tinyint(1) DEFAULT 0,
  `can_delete` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `user_module` (`user_id`,`module`),
  CONSTRAINT `user_permissions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for `users`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` enum('super_admin','content_manager') NOT NULL DEFAULT 'content_manager',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  KEY `idx_username` (`username`),
  KEY `idx_role` (`role`),
  KEY `idx_status` (`status`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `users`

INSERT INTO `users` VALUES("10","quanglong","$2y$10$zJtLJlSjRCIysPjqvaCPMOuxbIKvDyS5d1UAOzjhiyxm0JehYeaky","Quang Long","longdev.08@gmail.com","super_admin","active","2026-01-04 10:16:57","2025-12-01 11:02:01","2026-01-04 10:16:57");
INSERT INTO `users` VALUES("14","admin","$2y$10$pb91DPtYRzBJJ0u2ZPfmveDvDw8HD.DKUUHyjCyO4nadFeCll46zC","Bùi Thanh Tú","it@aurorahotelplaza.com","super_admin","active","2026-01-04 10:18:25","2025-12-02 10:43:49","2026-01-04 10:18:25");
INSERT INTO `users` VALUES("16","IT","$2y$10$kGM81lfQFe8/0T/X7nD/2.zn7B9pfUGXyhd9/ovleXhxoTWTrNWV6","IT APH","it01@aurorahotelplaza.com","super_admin","active","2026-01-04 10:27:43","2026-01-04 10:27:35","2026-01-04 10:27:43");

SET FOREIGN_KEY_CHECKS=1;
COMMIT;
