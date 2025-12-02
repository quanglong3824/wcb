<?php
/**
 * Toggle Test Mode API
 * Bật/tắt chế độ test overlay trên tất cả TV
 */
require_once '../includes/auth-check.php';
require_once '../config/php/config.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$action = isset($input['action']) ? $input['action'] : 'on';

$conn = getDBConnection();

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed']);
    exit;
}

try {
    $settingKey = 'tv_test_mode';
    $settingValue = ($action === 'on') ? '1' : '0';
    $timestamp = time();
    
    // Check if setting exists
    $checkStmt = $conn->prepare("SELECT id FROM system_settings WHERE setting_key = ?");
    $checkStmt->bind_param("s", $settingKey);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        // Update existing
        $updateStmt = $conn->prepare("UPDATE system_settings SET setting_value = ?, updated_at = NOW() WHERE setting_key = ?");
        $updateStmt->bind_param("ss", $settingValue, $settingKey);
        $updateStmt->execute();
    } else {
        // Insert new
        $insertStmt = $conn->prepare("INSERT INTO system_settings (setting_key, setting_value, setting_type, description) VALUES (?, ?, 'boolean', 'Test mode overlay for all TVs')");
        $insertStmt->bind_param("ss", $settingKey, $settingValue);
        $insertStmt->execute();
    }
    
    // Also save timestamp for TV to detect change
    $timestampKey = 'tv_test_mode_timestamp';
    $checkStmt2 = $conn->prepare("SELECT id FROM system_settings WHERE setting_key = ?");
    $checkStmt2->bind_param("s", $timestampKey);
    $checkStmt2->execute();
    $checkResult2 = $checkStmt2->get_result();
    
    $timestampStr = (string)$timestamp;
    if ($checkResult2->num_rows > 0) {
        $updateStmt2 = $conn->prepare("UPDATE system_settings SET setting_value = ?, updated_at = NOW() WHERE setting_key = ?");
        $updateStmt2->bind_param("ss", $timestampStr, $timestampKey);
        $updateStmt2->execute();
    } else {
        $insertStmt2 = $conn->prepare("INSERT INTO system_settings (setting_key, setting_value, setting_type, description) VALUES (?, ?, 'string', 'Test mode change timestamp')");
        $insertStmt2->bind_param("ss", $timestampKey, $timestampStr);
        $insertStmt2->execute();
    }
    
    // Log activity
    $logDesc = ($action === 'on') ? 'Bật chế độ test trên tất cả TV' : 'Tắt chế độ test trên tất cả TV';
    $logStmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description, ip_address) VALUES (?, 'toggle_test_mode', 'system', 0, ?, ?)");
    $ip = $_SERVER['REMOTE_ADDR'];
    $logStmt->bind_param("iss", $_SESSION['user_id'], $logDesc, $ip);
    $logStmt->execute();
    
    echo json_encode([
        'success' => true,
        'message' => $logDesc,
        'test_mode' => $settingValue === '1',
        'timestamp' => $timestamp
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $e->getMessage()
    ]);
}

$conn->close();
