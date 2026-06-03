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
$mediaStmt = $conn->prepare("SELECT id, name FROM media WHERE id IN ($placeholders) AND status = 'active'");
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
    // TV IDs cho Orchid mode: 1=Basement, 2=Chrysan, 3=Jasmine, 4=Lotus, 6=FO1, 7=FO2 (exclude 5=Restaurant)
    $orchidTVIds = [1, 2, 3, 4, 6, 7];
    
    // 1. Bật tất cả TV (set online)
    $tvIdsStr = implode(',', $orchidTVIds);
    $updateTVs = "UPDATE tvs SET status = 'online' WHERE id IN ($tvIdsStr)";
    $conn->query($updateTVs);
    
    // 2. Xóa tất cả assignments cũ của các TV này
    $deleteOld = "DELETE FROM tv_media_assignments WHERE tv_id IN ($tvIdsStr)";
    $conn->query($deleteOld);
    
    // 3. Cập nhật default_content_id và current_content_id (dùng ID đầu tiên)
    $firstMediaId = $validMediaIds[0];
    foreach ($orchidTVIds as $tvId) {
        $updateTV = $conn->prepare("UPDATE tvs SET default_content_id = ?, current_content_id = ? WHERE id = ?");
        $updateTV->bind_param("iii", $firstMediaId, $firstMediaId, $tvId);
        $updateTV->execute();
    }
    
    // 4. Gán danh sách WCB mới cho tất cả TV
    $assignedTVs = [];
    foreach ($orchidTVIds as $tvId) {
        // Lấy tên TV
        $tvStmt = $conn->prepare("SELECT name FROM tvs WHERE id = ?");
        $tvStmt->bind_param("i", $tvId);
        $tvStmt->execute();
        $tvResult = $tvStmt->get_result();
        $tv = $tvResult->fetch_assoc();
        
        if ($tv) {
            foreach ($validMediaIds as $index => $mId) {
                // Thêm assignment (không set is_default ngay để tránh trigger loop)
                $insertStmt = $conn->prepare("INSERT INTO tv_media_assignments (tv_id, media_id, is_default, assigned_by, assigned_at) VALUES (?, ?, 0, ?, NOW())");
                $insertStmt->bind_param("iii", $tvId, $mId, $_SESSION['user_id']);
                $insertStmt->execute();
            }
            $assignedTVs[] = $tv['name'];
        }
    }
    
    // 5. Cập nhật is_default cho media đầu tiên
    $updateDefault = "UPDATE tv_media_assignments SET is_default = 1 WHERE tv_id IN ($tvIdsStr) AND media_id = ?";
    $stmt = $conn->prepare($updateDefault);
    $stmt->bind_param("i", $firstMediaId);
    $stmt->execute();
    
    // 6. Ghi log
    $logStmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description, ip_address) VALUES (?, 'orchid_mode', 'media', ?, ?, ?)");
    $logDesc = "Áp dụng chế độ Orchid - Gán [" . $mediaNamesStr . "] cho " . implode(', ', array_unique($assignedTVs)) . " và bật tất cả TV";
    $ip = $_SERVER['REMOTE_ADDR'];
    $logStmt->bind_param("iiss", $_SESSION['user_id'], $firstMediaId, $logDesc, $ip);
    $logStmt->execute();
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Đã áp dụng chế độ Orchid thành công! Tất cả TV đã được bật và gán chuỗi WCB.',
        'tvs_affected' => count(array_unique($assignedTVs)),
        'tvs' => array_unique($assignedTVs)
    ]);
    
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi áp dụng chế độ Orchid: ' . $e->getMessage()
    ]);
}

$conn->close();
