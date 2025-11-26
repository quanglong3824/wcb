<?php
session_start();
require_once '../config/php/config.php';

header('Content-Type: application/json');

// Kiểm tra đăng nhập
if (!isLoggedIn()) {
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

// Lấy dữ liệu 7 TV từ database với thông tin nội dung đang chiếu
$conn = getDBConnection();

if ($conn) {
    // Sử dụng view để lấy thông tin TV kèm nội dung
    $query = "SELECT 
                t.id,
                t.name,
                t.location,
                t.folder,
                t.status,
                t.description,
                t.current_content_id,
                t.default_content_id,
                m.name as current_content_name,
                m.type as current_content_type,
                m.file_path as current_content_path
              FROM tvs t
              LEFT JOIN media m ON t.current_content_id = m.id
              ORDER BY t.id ASC";
    
    $result = $conn->query($query);
    $tvs = [];
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $tvs[] = $row;
        }
    }
    
    echo json_encode([
        'success' => true,
        'tvs' => $tvs
    ]);
} else {
    echo json_encode([
        'success' => false,
        'tvs' => [],
        'message' => 'Không thể kết nối database'
    ]);
}
