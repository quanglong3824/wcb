<?php
session_start();
require_once '../config/php/config.php';

header('Content-Type: application/json');

// Kiểm tra đăng nhập
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

// Kiểm tra request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Lấy dữ liệu từ form
$tvId = isset($_POST['tvId']) ? intval($_POST['tvId']) : 0;
$tvName = isset($_POST['tvName']) ? trim($_POST['tvName']) : '';
$tvLocation = isset($_POST['tvLocation']) ? trim($_POST['tvLocation']) : '';
$tvFolder = isset($_POST['tvFolder']) ? trim($_POST['tvFolder']) : '';
$tvIpAddress = isset($_POST['tvIpAddress']) ? trim($_POST['tvIpAddress']) : null;
$tvStatus = isset($_POST['tvStatus']) ? $_POST['tvStatus'] : 'offline';
$tvDescription = isset($_POST['tvDescription']) ? trim($_POST['tvDescription']) : null;

// Validate
if ($tvId <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID TV không hợp lệ']);
    exit;
}

if (empty($tvName) || empty($tvLocation) || empty($tvFolder)) {
    echo json_encode(['success' => false, 'message' => 'Vui lòng điền đầy đủ thông tin bắt buộc']);
    exit;
}

// Validate folder name (cho phép chữ thường, số, gạch ngang, gạch dưới, và dấu /)
if (!preg_match('/^[a-z0-9_\-\/]+$/', $tvFolder)) {
    echo json_encode(['success' => false, 'message' => 'Tên thư mục chỉ được chứa chữ thường, số, gạch ngang, gạch dưới và dấu /']);
    exit;
}

// Validate status
if (!in_array($tvStatus, ['online', 'offline'])) {
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
$checkStmt = $conn->prepare("SELECT id FROM tvs WHERE id = ?");
$checkStmt->bind_param("i", $tvId);
$checkStmt->execute();
$checkResult = $checkStmt->get_result();

if ($checkResult->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'TV không tồn tại']);
    exit;
}

// Kiểm tra folder có bị trùng không (trừ TV hiện tại)
$folderCheckStmt = $conn->prepare("SELECT id FROM tvs WHERE folder = ? AND id != ?");
$folderCheckStmt->bind_param("si", $tvFolder, $tvId);
$folderCheckStmt->execute();
$folderCheckResult = $folderCheckStmt->get_result();

if ($folderCheckResult->num_rows > 0) {
    echo json_encode(['success' => false, 'message' => 'Tên thư mục đã được sử dụng bởi TV khác']);
    exit;
}

// Tạo display_url từ folder
$displayUrl = $tvFolder . '/index.php';

// Cập nhật thông tin TV
$stmt = $conn->prepare("UPDATE tvs SET 
    name = ?, 
    location = ?, 
    folder = ?, 
    display_url = ?,
    ip_address = ?, 
    status = ?, 
    description = ?,
    updated_at = NOW() 
    WHERE id = ?");

$stmt->bind_param("sssssssi", 
    $tvName, 
    $tvLocation, 
    $tvFolder, 
    $displayUrl,
    $tvIpAddress, 
    $tvStatus, 
    $tvDescription, 
    $tvId
);

if ($stmt->execute()) {
    // Ghi log (bỏ qua lỗi nếu có)
    try {
        $logStmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, entity_type, entity_id, description, ip_address) VALUES (?, 'update', 'tv', ?, ?, ?)");
        $logDesc = "Cập nhật thông tin TV: " . $tvName;
        $ip = $_SERVER['REMOTE_ADDR'];
        $logStmt->bind_param("iiss", $_SESSION['user_id'], $tvId, $logDesc, $ip);
        $logStmt->execute();
    } catch (Exception $e) {
        error_log("TV update logging error: " . $e->getMessage());
    }
    
    echo json_encode([
        'success' => true, 
        'message' => 'Cập nhật thông tin TV thành công',
        'tv_id' => $tvId
    ]);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Lỗi khi cập nhật: ' . $conn->error
    ]);
}

$stmt->close();
$conn->close();
