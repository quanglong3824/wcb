<?php
/**
 * Video Broadcast Mode API
 * Gán 1 video cho tất cả TV (hoặc TV được chọn) và phát dạng slideshow
 * Tương tự chế độ Orchid nhưng dành cho video
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

$mediaId = isset($data['media_id']) ? intval($data['media_id']) : 0;
$tvIds = isset($data['tv_ids']) ? $data['tv_ids'] : []; // Nếu rỗng = tất cả TV
$excludeRestaurant = isset($data['exclude_restaurant']) ? (bool)$data['exclude_restaurant'] : true;

// Validate
if ($mediaId <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID media không hợp lệ']);
    exit;
}

// Kết nối database
$conn = getDBConnection();

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Không thể kết nối database']);
    exit;
}

// Kiểm tra media có tồn tại và là video không
$mediaStmt = $conn->prepare("SELECT id, name, type, file_path, thumbnail_path FROM media WHERE id = ? AND status = 'active'");
$mediaStmt->bind_param("i", $mediaId);
$mediaStmt->execute();
$mediaResult = $mediaStmt->get_result();

if ($mediaResult->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Media không tồn tại hoặc đã bị xóa']);
    exit;
}

$media = $mediaResult->fetch_assoc();

// Bắt đầu transaction
$conn->begin_transaction();

try {
    // Xác định danh sách TV
    if (empty($tvIds)) {
        // Lấy tất cả TV
        $tvQuery = "SELECT id FROM tvs";
        if ($excludeRestaurant) {
            // Exclude Restaurant (thường là ID 5)
            $tvQuery .= " WHERE folder != 'restaurant'";
        }
        $tvResult = $conn->query($tvQuery);
        $tvIds = [];
        while ($row = $tvResult->fetch_assoc()) {
            $tvIds[] = $row['id'];
        }
    }
    
    if (empty($tvIds)) {
        throw new Exception('Không có TV nào để gán');
    }
    
    $tvIdsStr = implode(',', array_map('intval', $tvIds));
    
    // 1. Bật tất cả TV được chọn (set online, tắt pause)
    $updateTVs = "UPDATE tvs SET status = 'online', is_paused = 0 WHERE id IN ($tvIdsStr)";
    $conn->query($updateTVs);
    
    // 2. Xóa tất cả assignments cũ của các TV này
    $deleteOld = "DELETE FROM tv_media_assignments WHERE tv_id IN ($tvIdsStr)";
    $conn->query($deleteOld);
    
    // 3. Cập nhật default_content_id và current_content_id
    foreach ($tvIds as $tvId) {
        $updateTV = $conn->prepare("UPDATE tvs SET default_content_id = ?, current_content_id = ? WHERE id = ?");
        $updateTV->bind_param("iii", $mediaId, $mediaId, $tvId);
        $updateTV->execute();
    }
    
    // 4. Gán media mới cho tất cả TV
    $assignedTVs = [];
    foreach ($tvIds as $tvId) {
        $tvId = intval($tvId);
        
        // Lấy tên TV
        $tvStmt = $conn->prepare("SELECT name FROM tvs WHERE id = ?");
        $tvStmt->bind_param("i", $tvId);
        $tvStmt->execute();
        $tvResult = $tvStmt->get_result();
        $tv = $tvResult->fetch_assoc();
        
        if ($tv) {
            // Thêm assignment
            $insertStmt = $conn->prepare("INSERT INTO tv_media_assignments (tv_id, media_id, is_default, assigned_by, assigned_at) VALUES (?, ?, 0, ?, NOW())");
            $insertStmt->bind_param("iii", $tvId, $mediaId, $_SESSION['user_id']);
            $insertStmt->execute();
            
            $assignedTVs[] = $tv['name'];
        }
    }
    
    // 5. Cập nhật is_default sau khi insert
    $updateDefault = "UPDATE tv_media_assignments SET is_default = 1 WHERE tv_id IN ($tvIdsStr) AND media_id = ?";
    $stmt = $conn->prepare($updateDefault);
    $stmt->bind_param("i", $mediaId);
    $stmt->execute();
    
    // 6. Gửi tín hiệu reload cho tất cả TV
    $timestamp = time();
    foreach ($tvIds as $tvId) {
        $settingKey = 'tv_reload_signal_' . $tvId;
        
        $checkStmt = $conn->prepare("SELECT id FROM system_settings WHERE setting_key = ?");
        $checkStmt->bind_param("s", $settingKey);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        
        if ($checkResult->num_rows > 0) {
            $updateStmt = $conn->prepare("UPDATE system_settings SET setting_value = ?, updated_at = NOW() WHERE setting_key = ?");
            $timestampStr = (string)$timestamp;
            $updateStmt->bind_param("ss", $timestampStr, $settingKey);
            $updateStmt->execute();
        } else {
            $insertStmt = $conn->prepare("INSERT INTO system_settings (setting_key, setting_value, setting_type, description) VALUES (?, ?, 'string', ?)");
            $timestampStr = (string)$timestamp;
            $description = "Reload signal for TV ID " . $tvId;
            $insertStmt->bind_param("sss", $settingKey, $timestampStr, $description);
            $insertStmt->execute();
        }
    }
    
    // 7. Ghi log
    $logStmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description, ip_address) VALUES (?, 'video_broadcast', 'media', ?, ?, ?)");
    $mediaType = $media['type'] === 'video' ? 'Video' : 'Media';
    $logDesc = "Áp dụng chế độ Video Broadcast - Gán '{$media['name']}' ($mediaType) cho " . implode(', ', $assignedTVs);
    $ip = $_SERVER['REMOTE_ADDR'];
    $logStmt->bind_param("iiss", $_SESSION['user_id'], $mediaId, $logDesc, $ip);
    $logStmt->execute();
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => "Đã phát '{$media['name']}' trên " . count($assignedTVs) . " TV!",
        'tvs_affected' => count($assignedTVs),
        'tvs' => $assignedTVs,
        'media' => [
            'id' => $media['id'],
            'name' => $media['name'],
            'type' => $media['type']
        ]
    ]);
    
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $e->getMessage()
    ]);
}

$conn->close();
