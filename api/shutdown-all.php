<?php
require_once '../includes/auth-check.php';
require_once '../config/php/config.php';

header('Content-Type: application/json');

// Kiểm tra request method
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
    exit;
}

// Kết nối database
$conn = getDBConnection();

if (!$conn) {
    echo json_encode(['success' => false, 'message' => 'Không thể kết nối database']);
    exit;
}

// Bắt đầu transaction
$conn->begin_transaction();

try {
    // 1. Set all TVs to offline
    $updateTVs = "UPDATE tvs SET status = 'offline', current_content_id = NULL WHERE 1=1";
    $conn->query($updateTVs);
    
    // 2. Delete all media assignments
    $deleteAssignments = "DELETE FROM tv_media_assignments WHERE 1=1";
    $conn->query($deleteAssignments);
    
    // 3. Log activity
    $logStmt = $conn->prepare("INSERT INTO activity_logs (user_id, action, entity_type, description, ip_address) VALUES (?, 'shutdown', 'system', ?, ?)");
    $logDesc = "Tắt toàn bộ hệ thống - Offline tất cả TV và gỡ gán toàn bộ WCB";
    $ip = $_SERVER['REMOTE_ADDR'];
    $logStmt->bind_param("iss", $_SESSION['user_id'], $logDesc, $ip);
    $logStmt->execute();
    
    // Commit transaction
    $conn->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Đã tắt toàn bộ hệ thống thành công',
        'tvs_affected' => $conn->affected_rows
    ]);
    
} catch (Exception $e) {
    $conn->rollback();
    echo json_encode([
        'success' => false,
        'message' => 'Lỗi khi tắt hệ thống: ' . $e->getMessage()
    ]);
}

$conn->close();
