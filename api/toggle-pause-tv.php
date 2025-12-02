<?php
/**
 * Toggle Pause TV API
 * Tạm dừng/Tiếp tục chiếu TV mà không gỡ WCB
 * Khi tạm dừng: TV hiển thị logo (chế độ chờ)
 * Khi tiếp tục: TV hiển thị WCB đã gán
 */
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
$pause = isset($data['pause']) ? (bool)$data['pause'] : true;

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
$tvStmt = $conn->prepare("SELECT id, name, is_paused FROM tvs WHERE id = ?");
$tvStmt->bind_param("i", $tvId);
$tvStmt->execute();
$tvResult = $tvStmt->get_result();

if ($tvResult->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'TV không tồn tại']);
    exit;
}

$tv = $tvResult->fetch_assoc();

try {
    // Cập nhật trạng thái pause
    $isPaused = $pause ? 1 : 0;
    $updateStmt = $conn->prepare("UPDATE tvs SET is_paused = ? WHERE id = ?");
    $updateStmt->bind_param("ii", $isPaused, $tvId);
    $updateStmt->execute();
    
    // Gửi tín hiệu reload cho TV
    $timestamp = time();
    $settingKey = 'tv_reload_signal_' . $tvId;
    
    $checkStmt = $conn->prepare("SELECT id FROM system_settings WHERE setting_key = ?");
    $checkStmt->bind_param("s", $settingKey);
    $checkStmt->execute();
    $checkResult = $checkStmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        $updateSignal = $conn->prepare("UPDATE system_settings SET setting_value = ?, updated_at = NOW() WHERE setting_key = ?");
        $timestampStr = (string)$timestamp;
        $updateSignal->bind_param("ss", $timestampStr, $settingKey);
        $updateSignal->execute();
    } else {
        $insertSignal = $conn->prepare("INSERT INTO system_settings (setting_key, setting_value, setting_type, description) VALUES (?, ?, 'string', ?)");
        $timestampStr = (string)$timestamp;
        $description = "Reload signal for TV " . $tv['name'];
        $insertSignal->bind_param("sss", $settingKey, $timestampStr, $description);
        $insertSignal->execute();
    }
    
    // Ghi log
    $action = $pause ? 'pause' : 'resume';
    $logDesc = $pause ? "Tạm dừng TV '{$tv['name']}' - Chế độ chờ" : "Tiếp tục chiếu TV '{$tv['name']}'";
    $logStmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description, ip_address) VALUES (?, ?, 'tv', ?, ?, ?)");
    $ip = $_SERVER['REMOTE_ADDR'];
    $logStmt->bind_param("isiss", $_SESSION['user_id'], $action, $tvId, $logDesc, $ip);
    $logStmt->execute();
    
    echo json_encode([
        'success' => true,
        'message' => $pause ? 'Đã tạm dừng TV' : 'Đã tiếp tục chiếu TV',
        'tv_name' => $tv['name'],
        'is_paused' => $pause
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $e->getMessage()
    ]);
}

$conn->close();
