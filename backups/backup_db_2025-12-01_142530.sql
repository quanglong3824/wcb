-- Aurora Hotel WCB Database Backup
-- Generated: 2025-12-01 14:25:30
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
) ENGINE=InnoDB AUTO_INCREMENT=203 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
INSERT INTO `activity_logs` VALUES("148","7","login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 10:04:15");
INSERT INTO `activity_logs` VALUES("149","7","login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 10:06:33");
INSERT INTO `activity_logs` VALUES("150","7","login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 10:07:57");
INSERT INTO `activity_logs` VALUES("151","7","login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 10:08:46");
INSERT INTO `activity_logs` VALUES("152","7","login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 10:19:13");
INSERT INTO `activity_logs` VALUES("153","7","login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 10:19:44");
INSERT INTO `activity_logs` VALUES("154","7","logout",NULL,NULL,"User logged out","::1",NULL,"2025-12-01 10:19:52");
INSERT INTO `activity_logs` VALUES("155","7","login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 10:19:57");
INSERT INTO `activity_logs` VALUES("156","7","login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 10:20:40");
INSERT INTO `activity_logs` VALUES("157","7","toggle_status","tv","2","Bật TV \'TV Chrysan\'","::1",NULL,"2025-12-01 10:23:55");
INSERT INTO `activity_logs` VALUES("158","7","toggle_status","tv","2","Tắt TV \'TV Chrysan\'","::1",NULL,"2025-12-01 10:23:56");
INSERT INTO `activity_logs` VALUES("159","7","toggle_status","tv","1","Bật TV \'TV Basement\'","::1",NULL,"2025-12-01 10:24:03");
INSERT INTO `activity_logs` VALUES("160","7","toggle_status","tv","1","Tắt TV \'TV Basement\'","::1",NULL,"2025-12-01 10:24:04");
INSERT INTO `activity_logs` VALUES("161","7","toggle_status","tv","1","Bật TV \'TV Basement\'","::1",NULL,"2025-12-01 10:30:49");
INSERT INTO `activity_logs` VALUES("162","7","toggle_status","tv","2","Bật TV \'TV Chrysan\'","::1",NULL,"2025-12-01 10:30:51");
INSERT INTO `activity_logs` VALUES("163","7","toggle_status","tv","3","Bật TV \'TV Jasmine\'","::1",NULL,"2025-12-01 10:30:52");
INSERT INTO `activity_logs` VALUES("164","7","reload","tv","3","Ép tải lại TV \'TV Jasmine\'","::1",NULL,"2025-12-01 10:31:03");
INSERT INTO `activity_logs` VALUES("165","7","reload","tv","3","Ép tải lại TV \'TV Jasmine\'","::1",NULL,"2025-12-01 10:31:14");
INSERT INTO `activity_logs` VALUES("166","7","shutdown","system",NULL,"Tắt toàn bộ hệ thống - Offline tất cả TV và gỡ gán toàn bộ WCB","::1",NULL,"2025-12-01 10:33:23");
INSERT INTO `activity_logs` VALUES("167","7","create_backup","backup","0","Created backup: backup_2025-12-01_104505.sql.gz","::1",NULL,"2025-12-01 10:45:05");
INSERT INTO `activity_logs` VALUES("168","7","create_user","user","10","Tạo người dùng: quanglong","::1",NULL,"2025-12-01 11:02:01");
INSERT INTO `activity_logs` VALUES("169","7","logout",NULL,NULL,"User logged out","::1",NULL,"2025-12-01 11:02:09");
INSERT INTO `activity_logs` VALUES("170","10","login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 11:02:20");
INSERT INTO `activity_logs` VALUES("171","10","create_user","user","11","Tạo người dùng: salemanager","::1",NULL,"2025-12-01 11:03:22");
INSERT INTO `activity_logs` VALUES("172","10","logout",NULL,NULL,"User logged out","::1",NULL,"2025-12-01 11:03:26");
INSERT INTO `activity_logs` VALUES("173","11","login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 11:03:29");
INSERT INTO `activity_logs` VALUES("174","11","logout",NULL,NULL,"User logged out","::1",NULL,"2025-12-01 11:04:03");
INSERT INTO `activity_logs` VALUES("175","7","login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 11:04:10");
INSERT INTO `activity_logs` VALUES("176","7","logout",NULL,NULL,"User logged out","::1",NULL,"2025-12-01 11:06:42");
INSERT INTO `activity_logs` VALUES("177","11","login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 11:06:56");
INSERT INTO `activity_logs` VALUES("178","11","logout",NULL,NULL,"User logged out","::1",NULL,"2025-12-01 11:08:05");
INSERT INTO `activity_logs` VALUES("179","7","login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 11:08:09");
INSERT INTO `activity_logs` VALUES("180","7","update_user","user","11","Cập nhật người dùng: salemanager","::1",NULL,"2025-12-01 11:09:15");
INSERT INTO `activity_logs` VALUES("181","7","logout",NULL,NULL,"User logged out","::1",NULL,"2025-12-01 11:09:19");
INSERT INTO `activity_logs` VALUES("182","10","login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 11:09:43");
INSERT INTO `activity_logs` VALUES("183","10","update_user","user","11","Cập nhật người dùng: salemanager","::1",NULL,"2025-12-01 11:09:50");
INSERT INTO `activity_logs` VALUES("184","10","create_backup","backup","0","Created backup: backup_2025-12-01_111147.sql.gz","::1",NULL,"2025-12-01 11:11:47");
INSERT INTO `activity_logs` VALUES("185","10","delete_backup","backup","0","Deleted backup: backup_2025-12-01_104505.sql.gz","::1",NULL,"2025-12-01 11:11:50");
INSERT INTO `activity_logs` VALUES("186","10","reset_password","user","11","Đặt lại mật khẩu cho: salemanager","::1",NULL,"2025-12-01 11:13:11");
INSERT INTO `activity_logs` VALUES("187","10","logout",NULL,NULL,"User logged out","::1",NULL,"2025-12-01 11:13:25");
INSERT INTO `activity_logs` VALUES("188","11","login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 11:13:32");
INSERT INTO `activity_logs` VALUES("189","11","change_password",NULL,NULL,"Changed password","::1",NULL,"2025-12-01 11:13:59");
INSERT INTO `activity_logs` VALUES("190","11","logout",NULL,NULL,"User logged out","::1",NULL,"2025-12-01 11:16:33");
INSERT INTO `activity_logs` VALUES("191","7","login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 11:16:36");
INSERT INTO `activity_logs` VALUES("192","7","update_user","user","11","Cập nhật người dùng: salemanager","::1",NULL,"2025-12-01 11:16:57");
INSERT INTO `activity_logs` VALUES("193","7","logout",NULL,NULL,"User logged out","::1",NULL,"2025-12-01 11:16:59");
INSERT INTO `activity_logs` VALUES("194","11","login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 11:17:05");
INSERT INTO `activity_logs` VALUES("195","11","logout",NULL,NULL,"User logged out","::1",NULL,"2025-12-01 11:26:07");
INSERT INTO `activity_logs` VALUES("196","7","login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 11:26:11");
INSERT INTO `activity_logs` VALUES("197","7","logout",NULL,NULL,"User logged out","::1",NULL,"2025-12-01 11:29:50");
INSERT INTO `activity_logs` VALUES("198","11","login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 11:29:56");
INSERT INTO `activity_logs` VALUES("199","11","logout",NULL,NULL,"User logged out","::1",NULL,"2025-12-01 11:39:25");
INSERT INTO `activity_logs` VALUES("200","7","login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 11:39:29");
INSERT INTO `activity_logs` VALUES("201","7","login",NULL,NULL,"User logged in","::1",NULL,"2025-12-01 14:24:27");
INSERT INTO `activity_logs` VALUES("202","7","toggle_status","tv","1","Tắt TV \'TV Basement\'","::1",NULL,"2025-12-01 14:24:49");

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
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `media`

INSERT INTO `media` VALUES("24","Abbort - Tầng 5 - Lotus","image","692709a29862c_1764166050_0.jpg","uploads/692709a29862c_1764166050_0.jpg","291759","image/jpeg",NULL,NULL,"1920","1080","","inactive",NULL,"2025-11-26 21:07:30","2025-12-01 08:24:27");
INSERT INTO `media` VALUES("25","Cao đẳng Hòa Bình Xuân Lộc - Orchild tầng 5","image","692709a2a026e_1764166050_1.jpg","uploads/692709a2a026e_1764166050_1.jpg","323821","image/jpeg",NULL,NULL,"1920","1080",NULL,"inactive",NULL,"2025-11-26 21:07:30","2025-12-01 08:24:24");
INSERT INTO `media` VALUES("26","VietCab tầng 6","image","692709a2a1ddd_1764166050_2.jpg","uploads/692709a2a1ddd_1764166050_2.jpg","505784","image/jpeg",NULL,NULL,"2560","1440",NULL,"inactive",NULL,"2025-11-26 21:07:30","2025-12-01 08:24:22");

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
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
INSERT INTO `system_settings` VALUES("14","tv_reload_signal_1","1764207903","string","Reload signal for TV TV Basement","0","2025-11-27 08:45:03");
INSERT INTO `system_settings` VALUES("15","tv_reload_signal_6","1764208106","string","Reload signal for TV TV FO 1","0","2025-11-27 08:48:27");
INSERT INTO `system_settings` VALUES("16","tv_reload_signal_3","1764559874","string","Reload signal for TV TV Jasmine","0","2025-12-01 10:31:14");

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
) ENGINE=InnoDB AUTO_INCREMENT=54 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_folder` (`folder`),
  KEY `idx_status` (`status`),
  KEY `idx_folder` (`folder`),
  KEY `idx_location` (`location`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `tvs`

INSERT INTO `tvs` VALUES("1","TV Basement","Tầng hầm","basement","basement/index.php","offline",NULL,"26","","TV tại khu vực tầng hầm - Hiển thị thông tin chào mừng","2025-12-01 11:12:19","2025-11-26 19:56:54","2025-12-01 14:24:49");
INSERT INTO `tvs` VALUES("2","TV Chrysan","Phòng Chrysan","chrysan","chrysan/index.php","offline",NULL,"26","","TV tại phòng hội nghị Chrysan","2025-11-27 08:55:03","2025-11-26 19:56:54","2025-12-01 10:33:23");
INSERT INTO `tvs` VALUES("3","TV Jasmine","Phòng Jasmine","jasmine","jasmine/index.php","offline",NULL,"26","","Phòng họp Jasmine","2025-12-01 10:32:16","2025-11-26 19:56:54","2025-12-01 10:33:23");
INSERT INTO `tvs` VALUES("4","TV Lotus","Phòng Lotus","lotus","lotus/index.php","offline",NULL,"26","","TV tại phòng hội nghị Lotus",NULL,"2025-11-26 19:56:54","2025-11-27 09:04:20");
INSERT INTO `tvs` VALUES("5","TV Restaurant","Nhà hàng","restaurant","restaurant/index.php","offline",NULL,"3","","TV tại nhà hàng - Hiển thị menu và khuyến mãi",NULL,"2025-11-26 19:56:54","2025-11-27 08:04:50");
INSERT INTO `tvs` VALUES("6","TV FO 1","Lễ tân 1","fo/tv1","fo/tv1/index.php","offline",NULL,"26","","TV tại quầy lễ tân số 1","2025-12-01 08:47:13","2025-11-26 19:56:54","2025-12-01 08:47:17");
INSERT INTO `tvs` VALUES("7","TV FO 2","Lễ tân 2","fo/tv2","fo/tv2/index.php","offline",NULL,"26","","TV tại quầy lễ tân số 2",NULL,"2025-11-26 19:56:54","2025-11-27 09:04:20");

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

-- Dumping data for `user_permissions`

INSERT INTO `user_permissions` VALUES("5","11","dashboard","1","0","0","0","2025-12-01 11:16:57","2025-12-01 11:16:57");
INSERT INTO `user_permissions` VALUES("6","11","tv_monitor","1","0","0","0","2025-12-01 11:16:57","2025-12-01 11:16:57");
INSERT INTO `user_permissions` VALUES("7","11","tv_manage","1","0","0","0","2025-12-01 11:16:57","2025-12-01 11:16:57");
INSERT INTO `user_permissions` VALUES("8","11","wcb_manage","1","0","0","0","2025-12-01 11:16:57","2025-12-01 11:16:57");
INSERT INTO `user_permissions` VALUES("9","11","upload","1","0","0","0","2025-12-01 11:16:57","2025-12-01 11:16:57");
INSERT INTO `user_permissions` VALUES("10","11","schedule","1","0","0","0","2025-12-01 11:16:57","2025-12-01 11:16:57");
INSERT INTO `user_permissions` VALUES("11","11","settings","1","0","0","0","2025-12-01 11:16:57","2025-12-01 11:16:57");
INSERT INTO `user_permissions` VALUES("12","11","users","1","0","0","0","2025-12-01 11:16:57","2025-12-01 11:16:57");
INSERT INTO `user_permissions` VALUES("13","11","logs","1","0","0","0","2025-12-01 11:16:57","2025-12-01 11:16:57");
INSERT INTO `user_permissions` VALUES("14","11","backup","1","0","0","0","2025-12-01 11:16:57","2025-12-01 11:16:57");

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
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Dumping data for `users`

INSERT INTO `users` VALUES("7","admin","$2y$10$XlkDOWr/mBe84.2KiS3kG.SSCS.JRIAXnDpMgDQWWwrdtQLZtBG7q","Administrator","admin@quanglonghotel.com","super_admin","active","2025-12-01 14:24:27","2025-12-01 10:04:08","2025-12-01 14:24:27");
INSERT INTO `users` VALUES("10","quanglong","$2y$10$CGRsXXcwJrcWT47RGM/lDe.JO9A0jYnsP/9ISf2eai7//ZkOCl2s2","Quang Long","longdev.08@gmail.com","super_admin","active","2025-12-01 11:09:43","2025-12-01 11:02:01","2025-12-01 11:09:43");
INSERT INTO `users` VALUES("11","salemanager","$2y$10$iL3tWD65lnh/cwCIW.TfdOe2UFPLrdK2W73BNxoG3f6xON9.WQIia","Phòng Kinh Doanh","sales@aurorahotelplaza.com","content_manager","active","2025-12-01 11:29:56","2025-12-01 11:03:22","2025-12-01 11:29:56");

SET FOREIGN_KEY_CHECKS=1;
COMMIT;
