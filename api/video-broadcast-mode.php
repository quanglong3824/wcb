<?php
/**
 * Video Broadcast Mode API
 * Gán 1 video/hình ảnh cho tất cả TV (hoặc trừ Restaurant)
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

$mediaIds = isset($data['media_ids']) && is_array($data['media_ids']) ? $data['media_ids'] : [];
// Tương thích ngược nếu chỉ truyền 1 ID
if (empty($mediaIds) && isset($data['media_id'])) {
    $mediaIds = [intval($data['media_id'])];
}

// Lọc các ID hợp lệ
$validMediaIds = [];
foreach ($mediaIds as $id) {
    if (intval($id) > 0) {
        $validMediaIds[] = intval($id);
    }
}

// Validate
if (empty($validMediaIds)) {
    echo json_encode(['success' => false, 'message' => 'Không có ID media hợp lệ được chọn']);
    exit;
}

// Kết nối database
$conn = getDBConnection();

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Không thể kết nối database']);
    exit;
}

// Kiểm tra media có tồn tại không
$placeholders = implode(',', array_fill(0, count($validMediaIds), '?'));
$mediaStmt = $conn->prepare("SELECT id, name, type FROM media WHERE id IN ($placeholders) AND status = 'active'");
$types = str_repeat('i', count($validMediaIds));
$mediaStmt->bind_param($types, ...$validMediaIds);
$mediaStmt->execute();
$mediaResult = $mediaStmt->get_result();

if ($mediaResult->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Các Media không tồn tại hoặc đã bị xóa']);
    exit;
}

$mediaNames = [];
while ($row = $mediaResult->fetch_assoc()) {
    $mediaNames[] = $row['name'];
}
$mediaNamesStr = implode(', ', $mediaNames);

// Bắt đầu transaction
$conn->begin_transaction();

try {
    $excludeRestaurant = isset($data['exclude_restaurant']) ? (bool)$data['exclude_restaurant'] : true;
    
    // Lấy danh sách TV IDs
    $tvQuery = $excludeRestaurant 
        ? "SELECT id, name FROM tvs WHERE folder != 'restaurant' ORDER BY id ASC"
        : "SELECT id, name FROM tvs ORDER BY id ASC";
        
    $tvResult = $conn->query($tvQuery);
    
    $tvIds = [];
    $assignedTVs = [];
    
    while ($tv = $tvResult->fetch_assoc()) {
        $tvIds[] = $tv['id'];
        $assignedTVs[] = $tv['name'];
    }
    
    if (empty($tvIds)) {
        throw new Exception("Không có TV nào trong hệ thống");
    }
    
    $tvIdsStr = implode(',', $tvIds);
    
    // 1. Bật tất cả TV đã chọn
    $updateTVs = "UPDATE tvs SET status = 'online' WHERE id IN ($tvIdsStr)";
    $conn->query($updateTVs);
    
    // 2. Xóa tất cả assignments cũ của các TV này
    $deleteOld = "DELETE FROM tv_media_assignments WHERE tv_id IN ($tvIdsStr)";
    $conn->query($deleteOld);
    
    $firstMediaId = $validMediaIds[0];
    
    // 3. Cập nhật default_content_id và current_content_id
    foreach ($tvIds as $tvId) {
        $updateTV = $conn->prepare("UPDATE tvs SET default_content_id = ?, current_content_id = ? WHERE id = ?");
        $updateTV->bind_param("iii", $firstMediaId, $firstMediaId, $tvId);
        $updateTV->execute();
    }
    
    // 4. Gán danh sách WCB mới cho tất cả TV
    foreach ($tvIds as $tvId) {
        foreach ($validMediaIds as $index => $mId) {
            $insertStmt = $conn->prepare("INSERT INTO tv_media_assignments (tv_id, media_id, is_default, assigned_by, assigned_at) VALUES (?, ?, 0, ?, NOW())");
            $insertStmt->bind_param("iii", $tvId, $mId, $_SESSION['user_id']);
            $insertStmt->execute();
        }
    }
    
    // 5. Cập nhật is_default cho media đầu tiên
    $updateDefault = "UPDATE tv_media_assignments SET is_default = 1 WHERE media_id = ?";
    $stmt = $conn->prepare($updateDefault);
    $stmt->bind_param("i", $firstMediaId);
    $stmt->execute();
    
    // 6. Gửi tín hiệu reload cho tất cả TV
    $timestamp = time();
    foreach ($tvIds as $tvId) {
        $settingKey = 'tv_reload_signal_' . $tvId;
        $timestampStr = (string)$timestamp;
        
        // Kiểm tra setting đã tồn tại chưa
        $checkResult = $conn->query("SELECT id FROM system_settings WHERE setting_key = '$settingKey' LIMIT 1");
        
        if ($checkResult && $checkResult->num_rows > 0) {
            $conn->query("UPDATE system_settings SET setting_value = '$timestampStr', updated_at = NOW() WHERE setting_key = '$settingKey'");
        } else {
            $desc = "Reload signal for TV ID $tvId";
            $conn->query("INSERT INTO system_settings (setting_key, setting_value, setting_type, description) VALUES ('$settingKey', '$timestampStr', 'string', '$desc')");
        }
        
        // Thêm vào bảng tv_reload_signals
        $conn->query("INSERT INTO tv_reload_signals (tv_id, created_at, processed) VALUES ($tvId, NOW(), 0)");
    }
    
    // 7. Ghi log
    $logStmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description, ip_address) VALUES (?, 'video_broadcast', 'media', ?, ?, ?)");
    $logDesc = "Video Broadcast - Gán [" . $mediaNamesStr . "] cho " . implode(', ', $assignedTVs);
    $ip = $_SERVER['REMOTE_ADDR'];
    $logStmt->bind_param("iiss", $_SESSION['user_id'], $firstMediaId, $logDesc, $ip);
    $logStmt->execute();
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => "Đã phát [" . $mediaNamesStr . "] trên " . count($assignedTVs) . " TV!",
        'tvs_affected' => count($assignedTVs),
        'tvs' => $assignedTVs
    ]);
    
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $e->getMessage()
    ]);
}

$conn->close();
