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
$tvIds = isset($data['tv_ids']) ? $data['tv_ids'] : [];
$isDefault = isset($data['is_default']) ? intval($data['is_default']) : 0;

// Validate
if ($mediaId <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID media không hợp lệ']);
    exit;
}

if (empty($tvIds) || !is_array($tvIds)) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng chọn ít nhất 1 TV']);
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
    $assignedCount = 0;
    $skippedCount = 0;
    $assignedTVs = [];
    
    foreach ($tvIds as $tvId) {
        $tvId = intval($tvId);
        
        // Kiểm tra TV có tồn tại không
        $tvStmt = $conn->prepare("SELECT id, name FROM tvs WHERE id = ?");
        $tvStmt->bind_param("i", $tvId);
        $tvStmt->execute();
        $tvResult = $tvStmt->get_result();
        
        if ($tvResult->num_rows === 0) {
            $skippedCount++;
            continue;
        }
        
        $tv = $tvResult->fetch_assoc();
        
        // Kiểm tra đã gán chưa
        $checkStmt = $conn->prepare("SELECT id FROM tv_media_assignments WHERE tv_id = ? AND media_id = ?");
        $checkStmt->bind_param("ii", $tvId, $mediaId);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();
        
        if ($checkResult->num_rows > 0) {
            // Đã gán rồi, cập nhật is_default nếu cần
            if ($isDefault) {
                // Cập nhật trực tiếp trong tvs table (tránh trigger conflict)
                $updateTvStmt = $conn->prepare("UPDATE tvs SET default_content_id = ? WHERE id = ?");
                $updateTvStmt->bind_param("ii", $mediaId, $tvId);
                $updateTvStmt->execute();
                
                // Cập nhật is_default trong assignments (tránh trigger)
                $conn->query("UPDATE tv_media_assignments SET is_default = 0 WHERE tv_id = {$tvId} AND media_id != {$mediaId}");
                $conn->query("UPDATE tv_media_assignments SET is_default = 1 WHERE tv_id = {$tvId} AND media_id = {$mediaId}");
            }
            $skippedCount++;
            continue;
        }
        
        // Thêm assignment mới
        $insertStmt = $conn->prepare("INSERT INTO tv_media_assignments (tv_id, media_id, is_default, assigned_by, assigned_at) VALUES (?, ?, ?, ?, NOW())");
        $insertStmt->bind_param("iiii", $tvId, $mediaId, $isDefault, $_SESSION['user_id']);
        
        if ($insertStmt->execute()) {
            $assignedCount++;
            $assignedTVs[] = $tv['name'];
            
            // Trigger sẽ tự động xử lý is_default và cập nhật tvs.default_content_id
        }
    }
    
    // Ghi log
    if ($assignedCount > 0) {
        $logStmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description, ip_address) VALUES (?, 'assign', 'media', ?, ?, ?)");
        $logDesc = "Gán media '{$media['name']}' cho " . implode(', ', $assignedTVs);
        $ip = $_SERVER['REMOTE_ADDR'];
        $logStmt->bind_param("iiss", $_SESSION['user_id'], $mediaId, $logDesc, $ip);
        $logStmt->execute();
    }
    
    // Commit transaction
    $conn->commit();
    
    $message = "Gán thành công cho {$assignedCount} TV";
    if ($skippedCount > 0) {
        $message .= " ({$skippedCount} TV đã được gán trước đó)";
    }
    
    echo json_encode([
        'success' => true,
        'message' => $message,
        'assigned' => $assignedCount,
        'skipped' => $skippedCount,
        'tvs' => $assignedTVs
    ]);
    
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi gán: ' . $e->getMessage()
    ]);
}

$conn->close();
