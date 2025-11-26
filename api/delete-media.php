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

$mediaId = isset($data['id']) ? intval($data['id']) : 0;

// Validate
if ($mediaId <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID không hợp lệ']);
    exit;
}

// Kết nối database
$conn = getDBConnection();

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Không thể kết nối database']);
    exit;
}

// Lấy thông tin media
$stmt = $conn->prepare("SELECT id, name, file_path, thumbnail_path FROM media WHERE id = ?");
$stmt->bind_param("i", $mediaId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Media không tồn tại']);
    exit;
}

$media = $result->fetch_assoc();

// Kiểm tra xem media có đang được sử dụng không
$checkStmt = $conn->prepare("SELECT COUNT(*) as count FROM tv_media_assignments WHERE media_id = ?");
$checkStmt->bind_param("i", $mediaId);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();
$assignmentCount = $checkResult->fetch_assoc()['count'];

if ($assignmentCount > 0) {
    echo json_encode([
        'success' => false, 
        'message' => "Media này đang được gán cho {$assignmentCount} TV. Vui lòng hủy gán trước khi xóa."
    ]);
    exit;
}

// Kiểm tra xem media có trong lịch chiếu không
$scheduleStmt = $conn->prepare("SELECT COUNT(*) as count FROM schedules WHERE media_id = ? AND status IN ('active', 'pending')");
$scheduleStmt->bind_param("i", $mediaId);
$scheduleStmt->execute();
$scheduleResult = $scheduleStmt->get_result();
$scheduleCount = $scheduleResult->fetch_assoc()['count'];

if ($scheduleCount > 0) {
    echo json_encode([
        'success' => false, 
        'message' => "Media này đang có {$scheduleCount} lịch chiếu. Vui lòng xóa lịch chiếu trước."
    ]);
    exit;
}

// Soft delete - Đánh dấu inactive thay vì xóa hẳn
$updateStmt = $conn->prepare("UPDATE media SET status = 'inactive', updated_at = NOW() WHERE id = ?");
$updateStmt->bind_param("i", $mediaId);

if ($updateStmt->execute()) {
    // Ghi log
    try {
        $logStmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description, ip_address) VALUES (?, 'delete', 'media', ?, ?, ?)");
        $logDesc = "Đánh dấu xóa media: " . $media['name'];
        $ip = $_SERVER['REMOTE_ADDR'];
        $logStmt->bind_param("iiss", $_SESSION['user_id'], $mediaId, $logDesc, $ip);
        $logStmt->execute();
    } catch (Exception $e) {
        error_log("Delete media logging error: " . $e->getMessage());
    }
    
    echo json_encode([
        'success' => true, 
        'message' => 'Xóa media thành công! (File được đánh dấu inactive)',
        'soft_delete' => true
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Lỗi khi xóa media: ' . $conn->error
    ]);
}

$conn->close();
