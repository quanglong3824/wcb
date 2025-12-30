<?php
/**
 * Debug TV Content API
 * Kiểm tra nội dung được gán cho TV
 */
require_once '../config/php/config.php';

header('Content-Type: application/json');

$tvId = isset($_GET['tv_id']) ? intval($_GET['tv_id']) : 1;

$conn = getDBConnection();

if (!$conn) {
    echo json_encode(['error' => 'Cannot connect to database']);
    exit;
}

// Lấy thông tin TV
$tvStmt = $conn->prepare("SELECT * FROM tvs WHERE id = ?");
$tvStmt->bind_param("i", $tvId);
$tvStmt->execute();
$tv = $tvStmt->get_result()->fetch_assoc();

// Lấy assignments
$assignStmt = $conn->prepare("SELECT tma.*, m.name as media_name, m.type, m.file_path, m.status as media_status FROM tv_media_assignments tma LEFT JOIN media m ON tma.media_id = m.id WHERE tma.tv_id = ?");
$assignStmt->bind_param("i", $tvId);
$assignStmt->execute();
$assignments = $assignStmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Lấy reload signals
$signalStmt = $conn->prepare("SELECT * FROM tv_reload_signals WHERE tv_id = ? ORDER BY created_at DESC LIMIT 5");
$signalStmt->bind_param("i", $tvId);
$signalStmt->execute();
$signals = $signalStmt->get_result()->fetch_all(MYSQLI_ASSOC);

// Lấy system_settings reload signal
$settingKey = 'tv_reload_signal_' . $tvId;
$settingStmt = $conn->prepare("SELECT * FROM system_settings WHERE setting_key = ?");
$settingStmt->bind_param("s", $settingKey);
$settingStmt->execute();
$setting = $settingStmt->get_result()->fetch_assoc();

echo json_encode([
    'tv' => $tv,
    'assignments' => $assignments,
    'reload_signals' => $signals,
    'system_setting' => $setting,
    'current_time' => date('Y-m-d H:i:s')
], JSON_PRETTY_PRINT);

$conn->close();
