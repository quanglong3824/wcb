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

$mediaId = isset($data['media_id']) ? intval($data['media_id']) : 0;
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

// Kiểm tra media có tồn tại không
$mediaStmt = $conn->prepare("SELECT id, name, type FROM media WHERE id = ? AND status = 'active'");
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
    // Lấy danh sách TV IDs
    $tvQuery = $excludeRestaurant 
        ? "SELECT id, name FROM tvs WHERE folder != 'restaurant' ORDER BY id ASC"
        : "SELECT id, name FROM tvs ORDER BY id ASC";
    
    $tvResult = $conn->query($tvQuery);
    
    if (!$tvResult || $tvResult->num_rows === 0) {
        throw new Exception('Không tìm thấy TV nào');
    }
    
    $broadcastTVIds = [];
    $tvNames = [];
    while ($row = $tvResult->fetch_assoc()) {
        $broadcastTVIds[] = (int)$row['id'];
        $tvNames[$row['id']] = $row['name'];
    }
    
    $tvIdsStr = implode(',', $broadcastTVIds);
    
    // 1. Bật tất cả TV (set online, tắt pause)
    $updateTVs = "UPDATE tvs SET status = 'online', is_paused = 0 WHERE id IN ($tvIdsStr)";
    $conn->query($updateTVs);
    
    // 2. Xóa tất cả assignments cũ của các TV này
    $deleteOld = "DELETE FROM tv_media_assignments WHERE tv_id IN ($tvIdsStr)";
    $conn->query($deleteOld);
    
    // 3. Cập nhật default_content_id và current_content_id trước (tránh trigger conflict)
    foreach ($broadcastTVIds as $tvId) {
        $updateTV = $conn->prepare("UPDATE tvs SET default_content_id = ?, current_content_id = ? WHERE id = ?");
        $updateTV->bind_param("iii", $mediaId, $mediaId, $tvId);
        $updateTV->execute();
    }
    
    // 4. Gán media mới cho tất cả TV (sau khi update tvs)
    $assignedTVs = [];
    foreach ($broadcastTVIds as $tvId) {
        // Thêm assignment (không set is_default để tránh trigger)
        $insertStmt = $conn->prepare("INSERT INTO tv_media_assignments (tv_id, media_id, is_default, assigned_by, assigned_at) VALUES (?, ?, 0, ?, NOW())");
        $insertStmt->bind_param("iii", $tvId, $mediaId, $_SESSION['user_id']);
        $insertStmt->execute();
        
        $assignedTVs[] = $tvNames[$tvId];
    }
    
    // 5. Cập nhật is_default sau khi insert (tránh trigger conflict)
    $updateDefault = "UPDATE tv_media_assignments SET is_default = 1 WHERE tv_id IN ($tvIdsStr) AND media_id = ?";
    $stmt = $conn->prepare($updateDefault);
    $stmt->bind_param("i", $mediaId);
    $stmt->execute();
    
    // 6. Ghi log
    $logStmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description, ip_address) VALUES (?, 'video_broadcast', 'media', ?, ?, ?)");
    $logDesc = "Video Broadcast - Gán '{$media['name']}' cho " . implode(', ', $assignedTVs);
    $ip = $_SERVER['REMOTE_ADDR'];
    $logStmt->bind_param("iiss", $_SESSION['user_id'], $mediaId, $logDesc, $ip);
    $logStmt->execute();
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => "Đã phát '{$media['name']}' trên " . count($assignedTVs) . " TV!",
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
