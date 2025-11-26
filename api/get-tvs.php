<?php
session_start();
require_once '../config/php/config.php';

header('Content-Type: application/json');

// Kiểm tra đăng nhập
if (!isLoggedIn()) {
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

// Lấy dữ liệu 7 TV từ database với thông tin nội dung đang chiếu
$conn = getDBConnection();

if ($conn) {
    // Sử dụng view để lấy thông tin TV kèm nội dung chi tiết
    $query = "SELECT 
                t.id,
                t.name,
                t.location,
                t.folder,
                t.display_url,
                t.status,
                t.ip_address,
                t.description,
                t.last_heartbeat,
                t.current_content_id,
                t.default_content_id,
                m.name as current_content_name,
                m.type as current_content_type,
                m.file_path as current_content_path,
                m.thumbnail_path as current_content_thumbnail,
                dm.name as default_content_name,
                dm.type as default_content_type,
                dm.file_path as default_content_path,
                TIMESTAMPDIFF(SECOND, t.last_heartbeat, NOW()) as seconds_since_heartbeat,
                CASE 
                    WHEN t.last_heartbeat IS NULL THEN 'offline'
                    WHEN TIMESTAMPDIFF(SECOND, t.last_heartbeat, NOW()) > 300 THEN 'offline'
                    ELSE t.status
                END as actual_status
              FROM tvs t
              LEFT JOIN media m ON t.current_content_id = m.id
              LEFT JOIN media dm ON t.default_content_id = dm.id
              ORDER BY t.id ASC";
    
    $result = $conn->query($query);
    $tvs = [];
    
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            // Format dữ liệu
            $row['is_online'] = ($row['actual_status'] === 'online');
            $row['last_seen'] = $row['last_heartbeat'] ? date('d/m/Y H:i:s', strtotime($row['last_heartbeat'])) : 'Chưa kết nối';
            
            // Thêm URL đầy đủ cho display
            if ($row['display_url']) {
                $row['full_display_url'] = BASE_URL . $row['display_url'];
            }
            
            $tvs[] = $row;
        }
        
        echo json_encode([
            'success' => true,
            'tvs' => $tvs,
            'total' => count($tvs),
            'online_count' => count(array_filter($tvs, function($tv) { return $tv['is_online']; })),
            'offline_count' => count(array_filter($tvs, function($tv) { return !$tv['is_online']; }))
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'tvs' => [],
            'message' => 'Lỗi truy vấn database: ' . $conn->error
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'tvs' => [],
        'message' => 'Không thể kết nối database'
    ]);
}
