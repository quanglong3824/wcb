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
$name = isset($data['name']) ? trim($data['name']) : '';
$description = isset($data['description']) ? trim($data['description']) : '';

// Validate
if ($mediaId <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID không hợp lệ']);
    exit;
}

if (empty($name)) {
    echo json_encode(['success' => false, 'message' => 'Tên không được để trống']);
    exit;
}

// Kết nối database
$conn = getDBConnection();

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Không thể kết nối database']);
    exit;
}

// Kiểm tra media có tồn tại không
$checkStmt = $conn->prepare("SELECT id, name FROM media WHERE id = ?");
$checkStmt->bind_param("i", $mediaId);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Media không tồn tại']);
    exit;
}

$oldMedia = $checkResult->fetch_assoc();

// Cập nhật tên và mô tả
$updateStmt = $conn->prepare("UPDATE media SET name = ?, description = ?, updated_at = NOW() WHERE id = ?");
$updateStmt->bind_param("ssi", $name, $description, $mediaId);

if ($updateStmt->execute()) {
    // Ghi log
    try {
        $logStmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description, ip_address) VALUES (?, 'update', 'media', ?, ?, ?)");
        $logDesc = "Cập nhật tên media từ '{$oldMedia['name']}' thành '{$name}'";
        $ip = $_SERVER['REMOTE_ADDR'];
        $logStmt->bind_param("iiss", $_SESSION['user_id'], $mediaId, $logDesc, $ip);
        $logStmt->execute();
    } catch (Exception $e) {
        error_log("Update media name logging error: " . $e->getMessage());
    }
    
    echo json_encode([
        'success' => true,
        'message' => 'Cập nhật thành công',
        'media_id' => $mediaId
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi cập nhật: ' . $conn->error
    ]);
}

$conn->close();
