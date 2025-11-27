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

$mediaId = isset($data['media_id']) ? intval($data['media_id']) : 0;

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
$mediaStmt = $conn->prepare("SELECT id, name FROM media WHERE id = ? AND status = 'active'");
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
    // TV IDs cho Orchid mode: 1=Basement, 2=Chrysan, 3=Jasmine, 4=Lotus, 6=FO1, 7=FO2 (exclude 5=Restaurant)
    $orchidTVIds = [1, 2, 3, 4, 6, 7];
    
    // 1. Bật tất cả TV (set online)
    $tvIdsStr = implode(',', $orchidTVIds);
    $updateTVs = "UPDATE tvs SET status = 'online' WHERE id IN ($tvIdsStr)";
    $conn->query($updateTVs);
    
    // 2. Xóa tất cả assignments cũ của các TV này
    $deleteOld = "DELETE FROM tv_media_assignments WHERE tv_id IN ($tvIdsStr)";
    $conn->query($deleteOld);
    
    // 3. Cập nhật default_content_id và current_content_id trước (tránh trigger conflict)
    foreach ($orchidTVIds as $tvId) {
        $updateTV = $conn->prepare("UPDATE tvs SET default_content_id = ?, current_content_id = ? WHERE id = ?");
        $updateTV->bind_param("iii", $mediaId, $mediaId, $tvId);
        $updateTV->execute();
    }
    
    // 4. Gán WCB mới cho tất cả TV (sau khi update tvs)
    $assignedTVs = [];
    foreach ($orchidTVIds as $tvId) {
        // Lấy tên TV
        $tvStmt = $conn->prepare("SELECT name FROM tvs WHERE id = ?");
        $tvStmt->bind_param("i", $tvId);
        $tvStmt->execute();
        $tvResult = $tvStmt->get_result();
        $tv = $tvResult->fetch_assoc();
        
        if ($tv) {
            // Thêm assignment (không set is_default để tránh trigger)
            $insertStmt = $conn->prepare("INSERT INTO tv_media_assignments (tv_id, media_id, is_default, assigned_by, assigned_at) VALUES (?, ?, 0, ?, NOW())");
            $insertStmt->bind_param("iii", $tvId, $mediaId, $_SESSION['user_id']);
            $insertStmt->execute();
            
            $assignedTVs[] = $tv['name'];
        }
    }
    
    // 5. Cập nhật is_default sau khi insert (tránh trigger conflict)
    $updateDefault = "UPDATE tv_media_assignments SET is_default = 1 WHERE tv_id IN ($tvIdsStr) AND media_id = ?";
    $stmt = $conn->prepare($updateDefault);
    $stmt->bind_param("i", $mediaId);
    $stmt->execute();
    
    // 6. Ghi log
    $logStmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description, ip_address) VALUES (?, 'orchid_mode', 'media', ?, ?, ?)");
    $logDesc = "Áp dụng chế độ Orchid - Gán '{$media['name']}' cho " . implode(', ', $assignedTVs) . " và bật tất cả TV";
    $ip = $_SERVER['REMOTE_ADDR'];
    $logStmt->bind_param("iiss", $_SESSION['user_id'], $mediaId, $logDesc, $ip);
    $logStmt->execute();
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Đã áp dụng chế độ Orchid thành công! Tất cả TV đã được bật và gán WCB.',
        'tvs_affected' => count($assignedTVs),
        'tvs' => $assignedTVs
    ]);
    
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi áp dụng chế độ Orchid: ' . $e->getMessage()
    ]);
}

$conn->close();
