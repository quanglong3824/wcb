<?php
/**
 * Check Fullscreen Signal API
 * Kiểm tra tín hiệu fullscreen cho TV
 */
require_once '../config/php/config.php';

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$tvId = isset($_GET['tv_id']) ? intval($_GET['tv_id']) : 0;

if ($tvId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid TV ID']);
    exit;
}

$conn = getDBConnection();

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

// Lấy timestamp fullscreen signal từ system_settings
$settingKey = 'tv_fullscreen_signal_' . $tvId;

$stmt = $conn->prepare("SELECT setting_value FROM system_settings WHERE setting_key = ?");
$stmt->bind_param("s", $settingKey);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $timestamp = intval($row['setting_value']);
    
    echo json_encode([
        'success' => true,
        'timestamp' => $timestamp,
        'tv_id' => $tvId
    ]);
} else {
    echo json_encode([
        'success' => true,
        'timestamp' => 0,
        'tv_id' => $tvId
    ]);
}

$conn->close();
