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

$tvId = isset($data['tv_id']) ? intval($data['tv_id']) : 0;
$status = isset($data['status']) ? $data['status'] : '';

// Validate
if ($tvId <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID TV không hợp lệ']);
    exit;
}

if (!in_array($status, ['online', 'offline'])) {
    echo json_encode(['success' => false, 'message' => 'Trạng thái không hợp lệ']);
    exit;
}

// Kết nối database
$conn = getDBConnection();

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Không thể kết nối database']);
    exit;
}

// Kiểm tra TV có tồn tại không
$tvStmt = $conn->prepare("SELECT id, name FROM tvs WHERE id = ?");
$tvStmt->bind_param("i", $tvId);
$tvStmt->execute();
$tvResult = $tvStmt->get_result();

if ($tvResult->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'TV không tồn tại']);
    exit;
}

$tv = $tvResult->fetch_assoc();

try {
    // Cập nhật trạng thái TV
    $updateStmt = $conn->prepare("UPDATE tvs SET status = ? WHERE id = ?");
    $updateStmt->bind_param("si", $status, $tvId);
    $updateStmt->execute();
    
    // Ghi log
    $logStmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description, ip_address) VALUES (?, 'toggle_status', 'tv', ?, ?, ?)");
    $logDesc = ($status === 'online' ? 'Bật' : 'Tắt') . " TV '{$tv['name']}'";
    $ip = $_SERVER['REMOTE_ADDR'];
    $logStmt->bind_param("iiss", $_SESSION['user_id'], $tvId, $logDesc, $ip);
    $logStmt->execute();
    
    echo json_encode([
        'success' => true,
        'message' => 'Đã cập nhật trạng thái TV',
        'tv_name' => $tv['name'],
        'status' => $status
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi: ' . $e->getMessage()
    ]);
}

$conn->close();
