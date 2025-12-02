<?php
/**
 * Fullscreen All TVs API
 * Gửi lệnh fullscreen đến tất cả 7 TV
 * TV sẽ tự động chuyển sang chế độ toàn màn hình
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
    $tvQuery = "SELECT id, name FROM tvs ORDER BY id ASC";
    $tvResult = $conn->query($tvQuery);
    
    if (!$tvResult || $tvResult->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy TV nào']);
        exit;
    }
    
    $timestamp = time();
    $updatedCount = 0;
    
    while ($tv = $tvResult->fetch_assoc()) {
        $tvId = $tv['id'];
        
        // Lưu tín hiệu fullscreen vào system_settings
        $settingKey = 'tv_fullscreen_signal_' . $tvId;
        
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
            $description = "Fullscreen signal for TV " . $tv['name'];
            $insertStmt->bind_param("sss", $settingKey, $timestampStr, $description);
            $insertStmt->execute();
        }
        
        $updatedCount++;
    }
    
    // Ghi log
    $logDesc = "Gửi lệnh fullscreen đến tất cả $updatedCount TV";
    $logStmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description, ip_address) VALUES (?, 'fullscreen_all', 'tv', 0, ?, ?)");
    $ip = $_SERVER['REMOTE_ADDR'];
    $logStmt->bind_param("iss", $_SESSION['user_id'], $logDesc, $ip);
    $logStmt->execute();
    
    echo json_encode([
        'success' => true,
        'message' => "Đã gửi lệnh fullscreen đến $updatedCount TV",
        'count' => $updatedCount,
        'timestamp' => $timestamp
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $e->getMessage()
    ]);
}

$conn->close();
