<?php
/**
 * Reload All TVs API
 * Gửi tín hiệu reload cho tất cả 7 TV cùng lúc
 * Dùng cho trường hợp TV cũ (Samsung/Sony) không tự động reload
 */
require_once '../includes/auth-check.php';
require_once '../config/php/config.php';

header('Content-Type: application/json');

// Kiểm tra request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Kết nối database
$conn = getDBConnection();

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Không thể kết nối database']);
    exit;
}

try {
    // Lấy danh sách tất cả TV
    $tvStmt = $conn->prepare("SELECT id, name FROM tvs ORDER BY id");
    $tvStmt->execute();
    $tvResult = $tvStmt->get_result();
    
    $timestamp = time();
    $reloadedTVs = [];
    $errors = [];
    
    while ($tv = $tvResult->fetch_assoc()) {
        $tvId = $tv['id'];
        $settingKey = 'tv_reload_signal_' . $tvId;
        
        // Kiểm tra xem setting đã tồn tại chưa
        $checkStmt = $conn->prepare("SELECT id FROM system_settings WHERE setting_key = ?");
        $checkStmt->bind_param("s", $settingKey);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        
        if ($checkResult->num_rows > 0) {
            // Update existing
            $updateStmt = $conn->prepare("UPDATE system_settings SET setting_value = ?, updated_at = NOW() WHERE setting_key = ?");
            $timestampStr = (string)$timestamp;
            $updateStmt->bind_param("ss", $timestampStr, $settingKey);
            $updateStmt->execute();
        } else {
            // Insert new
            $insertStmt = $conn->prepare("INSERT INTO system_settings (setting_key, setting_value, setting_type, description) VALUES (?, ?, 'string', ?)");
            $timestampStr = (string)$timestamp;
            $description = "Reload signal for TV " . $tv['name'];
            $insertStmt->bind_param("sss", $settingKey, $timestampStr, $description);
            $insertStmt->execute();
        }
        
        // Thêm vào bảng tv_reload_signals (nếu có)
        $signalStmt = $conn->prepare("INSERT INTO tv_reload_signals (tv_id, created_at, processed) VALUES (?, NOW(), 0) ON DUPLICATE KEY UPDATE created_at = NOW(), processed = 0");
        if ($signalStmt) {
            $signalStmt->bind_param("i", $tvId);
            $signalStmt->execute();
        }
        
        $reloadedTVs[] = $tv['name'];
    }
    
    // Ghi log
    $logStmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description, ip_address) VALUES (?, 'reload_all', 'tv', 0, ?, ?)");
    $logDesc = "Ép tải lại TẤT CẢ " . count($reloadedTVs) . " TV";
    $ip = $_SERVER['REMOTE_ADDR'];
    $logStmt->bind_param("iss", $_SESSION['user_id'], $logDesc, $ip);
    $logStmt->execute();
    
    echo json_encode([
        'success' => true,
        'message' => 'Đã gửi lệnh tải lại cho tất cả TV',
        'tv_count' => count($reloadedTVs),
        'tvs' => $reloadedTVs,
        'timestamp' => $timestamp
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $e->getMessage()
    ]);
}

$conn->close();
