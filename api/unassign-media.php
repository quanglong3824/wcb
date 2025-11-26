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
$tvId = isset($data['tv_id']) ? intval($data['tv_id']) : 0;

// Validate
if ($mediaId <= 0 || $tvId <= 0) {
    echo json_encode(['success' => false, 'message' => 'Tham số không hợp lệ']);
    exit;
}

// Kết nối database
$conn = getDBConnection();

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Không thể kết nối database']);
    exit;
}

// Lấy thông tin trước khi xóa
$infoStmt = $conn->prepare("SELECT 
    tma.is_default,
    t.name as tv_name,
    m.name as media_name
    FROM tv_media_assignments tma
    INNER JOIN tvs t ON tma.tv_id = t.id
    INNER JOIN media m ON tma.media_id = m.id
    WHERE tma.tv_id = ? AND tma.media_id = ?");
$infoStmt->bind_param("ii", $tvId, $mediaId);
$infoStmt->execute();
$infoResult = $infoStmt->get_result();

if ($infoResult->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Không tìm thấy assignment']);
    exit;
}

$info = $infoResult->fetch_assoc();

// Xóa assignment
$deleteStmt = $conn->prepare("DELETE FROM tv_media_assignments WHERE tv_id = ? AND media_id = ?");
$deleteStmt->bind_param("ii", $tvId, $mediaId);

if ($deleteStmt->execute()) {
    // Nếu là default, cập nhật TV
    if ($info['is_default']) {
        $conn->query("UPDATE tvs SET default_content_id = NULL WHERE id = {$tvId}");
    }
    
    // Ghi log
    try {
        $logStmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description, ip_address) VALUES (?, 'unassign', 'media', ?, ?, ?)");
        $logDesc = "Hủy gán media '{$info['media_name']}' khỏi TV '{$info['tv_name']}'";
        $ip = $_SERVER['REMOTE_ADDR'];
        $logStmt->bind_param("iiss", $_SESSION['user_id'], $mediaId, $logDesc, $ip);
        $logStmt->execute();
    } catch (Exception $e) {
        error_log("Unassign logging error: " . $e->getMessage());
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Hủy gán thành công'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi hủy gán: ' . $conn->error
    ]);
}

$conn->close();
