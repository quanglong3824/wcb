<?php
require_once '../includes/auth-check.php';
require_once '../config/php/config.php';

header('Content-Type: application/json');

// Kiểm tra request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Lấy dữ liệu
$input = file_get_contents('php://input');
$data = json_decode($input, true);

$tvId = isset($data['tv_id']) ? intval($data['tv_id']) : 0;

// Validate
if ($tvId <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID TV không hợp lệ']);
    exit;
}

// Kết nối database
$conn = getDBConnection();

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Không thể kết nối database']);
    exit;
}

// Kiểm tra TV có tồn tại không
$tvStmt = $conn->prepare("SELECT id, name FROM tvs WHERE id = ?");
$tvStmt->bind_param("i", $tvId);
$tvStmt->execute();
$tvResult = $tvStmt->get_result();

if ($tvResult->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'TV không tồn tại']);
    exit;
}

$tv = $tvResult->fetch_assoc();
$timestamp = time();
$settingKey = 'tv_reload_signal_' . $tvId;

try {
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
    
    // Ghi log
    $logStmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description, ip_address) VALUES (?, 'reload', 'tv', ?, ?, ?)");
    $logDesc = "Ép tải lại TV '{$tv['name']}'";
    $ip = $_SERVER['REMOTE_ADDR'];
    $logStmt->bind_param("iiss", $_SESSION['user_id'], $tvId, $logDesc, $ip);
    $logStmt->execute();
    
    echo json_encode([
        'success' => true,
        'message' => 'Đã gửi lệnh tải lại cho TV',
        'tv_name' => $tv['name'],
        'timestamp' => $timestamp
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $e->getMessage()
    ]);
}

$conn->close();
